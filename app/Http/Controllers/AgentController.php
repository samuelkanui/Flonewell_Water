<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class AgentController extends Controller
{
    public function dashboard()
    {
        try {
            $agent = Auth::user();
            
            // Get total customers count
            $customers = $agent->customers()->count();
            
            // Get pending readings count
            $pendingReadings = MeterReading::where('agent_id', $agent->id)
                ->where('status', 'pending')
                ->count();
            
            // Get recent readings with customer info
            $recentReadings = MeterReading::with('customer')
                ->where('agent_id', $agent->id)
                ->latest()
                ->take(5)
                ->get();
            
            return view('agent.dashboard', compact('customers', 'pendingReadings', 'recentReadings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function customers()
    {
        try {
            $customers = Auth::user()
                ->customers()
                ->with(['latestMeterReading', 'meterReadings' => function($query) {
                    $query->latest()->take(1);
                }])
                ->get();

            return view('agent.customers', compact('customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading customers: ' . $e->getMessage());
        }
    }

    public function meterReadings()
    {
        try {
            $customers = Auth::user()->customers()->get();
            return view('agent.meter_readings', compact('customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading meter readings: ' . $e->getMessage());
        }
    }

    public function getCustomerReadings(User $customer)
    {
        try {
            // Verify that the customer belongs to the agent
            if ($customer->agent_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $readings = $customer->meterReadings()
                ->with('customer')
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->id,
                        'customer_id' => $reading->customer_id,
                        'customer_name' => $reading->customer->name,
                        'previous_reading' => $reading->previous_reading,
                        'current_reading' => $reading->current_reading,
                        'units' => $reading->units,
                        'status' => $reading->status,
                        'created_at' => $reading->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json($readings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching readings: ' . $e->getMessage()], 500);
        }
    }

    public function submitReading(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:users,id',
                'previous_reading' => 'required|numeric|min:0',
                'current_reading' => 'required|numeric|min:0|gt:previous_reading',
            ]);

            $customer = User::findOrFail($request->customer_id);

            // Verify that the customer belongs to the agent
            if ($customer->agent_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Calculate units used
            $units = $request->current_reading - $request->previous_reading;

            // Create new meter reading
            $reading = MeterReading::create([
                'customer_id' => $customer->id,
                'agent_id' => Auth::id(),
                'previous_reading' => $request->previous_reading,
                'current_reading' => $request->current_reading,
                'units' => $units,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Meter reading submitted successfully and is pending admin approval',
                'reading' => $reading->load('customer')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error submitting reading: ' . $e->getMessage()], 500);
        }
    }
}