<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->get();
        return view('admin.dashboard', compact('customers'));
    }

    public function create()
    {
        $agents = User::where('role', 'agent')->get();
        return view('admin.customers.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|regex:/^254[0-9]{9}$/|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'agent_id' => 'nullable|exists:users,id',
            'initial_reading' => 'nullable|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'water_units' => 0,
            'balance' => 0,
            'agent_id' => $request->agent_id,
        ]);

        // Create initial meter reading if provided
        if ($request->filled('initial_reading')) {
            MeterReading::create([
                'customer_id' => $user->id,
                'agent_id' => $request->agent_id,
                'previous_reading' => 0,
                'current_reading' => $request->initial_reading,
                'units' => $request->initial_reading,
                'status' => 'approved',
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Customer created successfully.');
    }

    public function edit(User $user)
    {
        if ($user->role !== 'customer') {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid customer.');
        }
        $agents = User::where('role', 'agent')->get();
        return view('admin.customers.edit', compact('user', 'agents'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'customer') {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid customer.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|regex:/^254[0-9]{9}$/|unique:users,phone,' . $user->id,
            'agent_id' => 'nullable|exists:users,id',
            'water_units' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'agent_id', 'water_units', 'balance']));
        return redirect()->route('admin.dashboard')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'customer') {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid customer.');
        }
        $user->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Customer deleted successfully.');
    }

    public function updateUsage(Request $request, User $user)
    {
        if ($user->role !== 'customer') {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid customer.');
        }

        $request->validate(['water_units' => 'required|numeric|min:0']);
        $user->update([
            'water_units' => $request->water_units,
            'balance' => $request->water_units * 150,
        ]);

        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $twilio->messages->create($user->phone, [
                'from' => env('TWILIO_NUMBER'),
                'body' => "Your water usage updated: $request->water_units units. Balance due: {$user->balance} KES.",
            ]);
        } catch (Exception $e) {
            \Log::error('Twilio SMS Error:', ['message' => $e->getMessage()]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Usage updated.');
    }

    public function agents()
    {
        $agents = User::where('role', 'agent')->get();
        return view('admin.agents', compact('agents'));
    }

    public function createAgent()
    {
        return view('admin.agents.create');
    }

    public function storeAgent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|regex:/^254[0-9]{9}$/|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'water_units' => 0,
            'balance' => 0,
        ]);

        return redirect()->route('admin.agents')->with('success', 'Agent created successfully.');
    }

    public function editAgent(User $user)
    {
        if ($user->role !== 'agent') {
            return redirect()->route('admin.agents')->with('error', 'Invalid agent.');
        }
        return view('admin.agents.edit', compact('user'));
    }

    public function updateAgent(Request $request, User $user)
    {
        if ($user->role !== 'agent') {
            return redirect()->route('admin.agents')->with('error', 'Invalid agent.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|regex:/^254[0-9]{9}$/|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('admin.agents')->with('success', 'Agent updated successfully.');
    }

    public function destroyAgent(User $user)
    {
        if ($user->role !== 'agent') {
            return redirect()->route('admin.agents')->with('error', 'Invalid agent.');
        }
        $user->delete();
        return redirect()->route('admin.agents')->with('success', 'Agent deleted successfully.');
    }

    public function meterReadings()
    {
        $readings = MeterReading::with(['agent', 'customer'])->orderBy('created_at', 'desc')->get();
        return view('admin.meter_readings', compact('readings'));
    }

    public function updateMeterReading(Request $request, MeterReading $reading)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($request->status === 'approved') {
            $customer = $reading->customer;
            $units = $reading->units;
            $balance = $units * 150;

            $customer->update([
                'water_units' => $units,
                'balance' => $balance,
            ]);

            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
                $twilio->messages->create($customer->phone, [
                    'from' => env('TWILIO_NUMBER'),
                    'body' => "Your water usage updated: $units units. Balance due: $balance KES.",
                ]);
            } catch (Exception $e) {
                \Log::error('Twilio SMS Error:', ['message' => $e->getMessage()]);
            }
        }

        $reading->update(['status' => $request->status]);
        return redirect()->route('admin.meter_readings')->with('success', 'Meter reading updated.');
    }

    public function viewCustomerProfile(User $user)
    {
        if ($user->role !== 'customer') {
            abort(404);
        }

        return view('admin.customers.profile', [
            'customer' => $user,
            'meterReadings' => $user->meterReadings()->latest()->take(5)->get(),
            'payments' => $user->payments()->latest()->take(5)->get()
        ]);
    }

    public function updateCustomerProfile(Request $request, User $user)
    {
        if ($user->role !== 'customer') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.customers.profile', $user)
            ->with('success', 'Customer profile updated successfully.');
    }

    public function toggleCustomerStatus(User $user)
    {
        if ($user->role !== 'customer') {
            abort(404);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'unblocked' : 'blocked';
        return redirect()->back()
            ->with('success', "Customer account has been {$status}.");
    }

    public function deleteCustomer(User $user)
    {
        if ($user->role !== 'customer') {
            abort(404);
        }

        // Delete related records
        $user->meterReadings()->delete();
        $user->payments()->delete();
        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Customer account has been deleted successfully.');
    }

    public function usageHistory()
    {
        $readings = MeterReading::with(['customer', 'agent'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($reading) {
                return $reading->created_at->format('F Y');
            });

        return view('admin.usage_history', compact('readings'));
    }

    public function customerPayments()
    {
        $customers = User::where('role', 'customer')
            ->with(['payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function($customer) {
                $customer->last_payment = $customer->payments->first();
                $customer->payment_status = $customer->last_payment ? $customer->last_payment->status : 'no_payment';
                return $customer;
            });

        return view('admin.customer_payments', compact('customers'));
    }
}
