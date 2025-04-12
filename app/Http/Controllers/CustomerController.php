<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use App\Jobs\ProcessPayment;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)->latest()->get();
        return view('dashboard', compact('user', 'payments'));
    }

    public function pay(Request $request)
    {
        $user = Auth::user();
        $units = $user->water_units;
        $amount = (int) ($units * 150);

        if ($amount <= 0) {
            return redirect()->route('dashboard')->with('error', 'No payment required.');
        }

        try {
            $client = new Client();
            $response = $client->request('GET', env('MPESA_ENV') === 'sandbox' ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
                'auth' => [env('MPESA_CONSUMER_KEY'), env('MPESA_CONSUMER_SECRET')]
            ]);

            $token = json_decode($response->getBody())->access_token;

            $timestamp = now()->format('YmdHis');
            $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);

            $payload = [
                'BusinessShortCode' => env('MPESA_SHORTCODE'),
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (string) $amount,
                'PartyA' => $user->phone,
                'PartyB' => env('MPESA_SHORTCODE'),
                'PhoneNumber' => $user->phone,
                'CallBackURL' => env('MPESA_CALLBACK_URL'),
                'AccountReference' => 'WaterPayment' . $user->id,
                'TransactionDesc' => "Payment for $units water units",
            ];

            Log::info('STK Push Payload:', $payload);

            $stkResponse = $client->request('POST', env('MPESA_ENV') === 'sandbox' ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($stkResponse->getBody(), true);
            Log::info('STK Push Response:', $result);

            if ($result['ResponseCode'] == '0') {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'units_paid' => $units,
                    'mpesa_transaction_id' => $result['CheckoutRequestID'],
                    'status' => 'pending',
                ]);

                // Dispatch payment processing job
                ProcessPayment::dispatch($payment, $user);

                return redirect()->route('dashboard')->with('success', 'Payment request sent to your phone.');
            } else {
                throw new Exception('STK Push failed: ' . $result['ResponseDescription']);
            }
        } catch (Exception $e) {
            Log::error('STK Push Error:', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        Log::info('MPesa Callback Received:', $request->all());

        try {
            $data = $request->all();
            $stkCallback = $data['Body']['stkCallback'];
            $checkoutRequestID = $stkCallback['CheckoutRequestID'];
            $resultCode = $stkCallback['ResultCode'];

            $payment = Payment::where('mpesa_transaction_id', $checkoutRequestID)->first();

            if ($payment) {
                // Handle cancelled payments first (ResultCode 1032)
                if ($resultCode == 1032) {
                    $payment->update([
                        'status' => 'failed',
                        'failure_reason' => 'Payment cancelled by user'
                    ]);
                    Log::info('Payment Cancelled:', ['payment_id' => $payment->id]);
                    return response()->json(['message' => 'Payment cancelled']);
                }

                // Handle successful payments (ResultCode 0)
                if ($resultCode == 0) {
                    // Check if we have a valid M-Pesa receipt number
                    if (isset($stkCallback['CallbackMetadata']['Item'])) {
                        $mpesaReceipt = collect($stkCallback['CallbackMetadata']['Item'])
                            ->where('Name', 'MpesaReceiptNumber')
                            ->first()['Value'] ?? null;

                        if ($mpesaReceipt) {
                            $payment->update([
                                'status' => 'completed',
                                'mpesa_transaction_id' => $mpesaReceipt,
                                'failure_reason' => null
                            ]);

                            $user = $payment->user;
                            $user->update([
                                'water_units' => 0,
                                'balance' => 0,
                            ]);

                            Log::info('Payment Completed:', ['payment_id' => $payment->id]);
                            return response()->json(['message' => 'Payment completed']);
                        }
                    }
                }

                // Handle all other failure cases
                $failureReason = $stkCallback['ResultDesc'] ?? 'Payment failed';
                
                // Specific handling for common M-Pesa result codes
                switch ($resultCode) {
                    case 2001:
                        $failureReason = 'Insufficient funds';
                        break;
                    case 2002:
                        $failureReason = 'Less than minimum transaction value';
                        break;
                    case 2003:
                        $failureReason = 'More than maximum transaction value';
                        break;
                    case 2004:
                        $failureReason = 'Would exceed daily transfer limit';
                        break;
                    case 2005:
                        $failureReason = 'Would exceed minimum balance';
                        break;
                    case 2006:
                        $failureReason = 'Unresolved primary party';
                        break;
                    case 2007:
                        $failureReason = 'Unresolved receiver party';
                        break;
                    case 2008:
                        $failureReason = 'Would exceed maximum balance';
                        break;
                    case 2010:
                        $failureReason = 'Invalid transaction';
                        break;
                    case 2011:
                        $failureReason = 'Invalid amount';
                        break;
                    case 2012:
                        $failureReason = 'Invalid receiver party';
                        break;
                    case 2013:
                        $failureReason = 'Invalid initiator information';
                        break;
                    case 2014:
                        $failureReason = 'Invalid security credential';
                        break;
                    case 2015:
                        $failureReason = 'Invalid service';
                        break;
                    case 2016:
                        $failureReason = 'Invalid transaction reference';
                        break;
                }

                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $failureReason
                ]);

                Log::info('Payment Failed:', [
                    'payment_id' => $payment->id,
                    'result_code' => $resultCode,
                    'reason' => $failureReason
                ]);
            }

            return response()->json(['message' => 'Callback processed']);
        } catch (Exception $e) {
            Log::error('Callback Error:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Callback failed'], 500);
        }
    }

    public function paymentStatus()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($payment) {
                return $payment->status;
            });

        return view('customer.payment_status', compact('user', 'payments'));
    }
}