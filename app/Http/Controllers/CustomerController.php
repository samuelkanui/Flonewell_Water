<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

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
                Payment::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'units_paid' => $units,
                    'mpesa_transaction_id' => $result['CheckoutRequestID'],
                    'status' => 'pending',
                ]);

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
                if ($resultCode == 0) {
                    $mpesaReceipt = collect($stkCallback['CallbackMetadata']['Item'])
                        ->where('Name', 'MpesaReceiptNumber')
                        ->first()['Value'] ?? $checkoutRequestID;

                    $payment->update([
                        'status' => 'completed',
                        'mpesa_transaction_id' => $mpesaReceipt,
                    ]);

                    $user = $payment->user;
                    $user->update([
                        'water_units' => 0,
                        'balance' => 0,
                    ]);

                    Log::info('Payment Completed:', ['payment_id' => $payment->id]);
                } else {
                    $payment->update(['status' => 'failed']);
                    Log::info('Payment Failed:', ['payment_id' => $payment->id, 'result_code' => $resultCode]);
                }
            }

            return response()->json(['message' => 'Callback processed']);
        } catch (Exception $e) {
            Log::error('Callback Error:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Callback failed'], 500);
        }
    }
}