@extends('layouts.customer')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 bg-gray-800 rounded-xl shadow-lg border border-gray-700">
        <!-- Heading -->
        <div>
            <h1 class="text-3xl font-bold text-blue-400 mb-2 tracking-tight">Welcome, {{ $user->name }}!</h1>
            <p class="text-lg text-gray-300">Your Water Usage Overview</p>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-900 border-l-4 border-green-500 text-green-100 px-4 py-3 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.01]">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-900 border-l-4 border-red-500 text-red-100 px-4 py-3 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.01]">
                {{ session('error') }}
            </div>
        @endif

        <!-- Current Usage Section -->
        <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
            <h2 class="text-2xl font-bold text-white mb-4">Current Usage</h2>
            <div class="flex flex-col md:flex-row justify-between space-y-6 md:space-y-0 md:space-x-6">
                <div class="text-center p-4 bg-gray-600 rounded-lg flex-1">
                    <p class="text-gray-300 mb-1">Units</p>
                    <p class="text-3xl font-bold text-white">{{ $user->water_units }}</p>
                </div>
                <div class="text-center p-4 bg-gray-600 rounded-lg flex-1">
                    <p class="text-gray-300 mb-1">Balance</p>
                    <p class="text-3xl font-bold text-white">KES {{ number_format($user->balance, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
            <h2 class="text-2xl font-bold text-white mb-4">Make a Payment</h2>
            <form action="{{ route('customer.pay') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Phone Number (starts with 254)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full px-3 py-2 bg-gray-600 text-white border border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('phone')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-300 mb-1">Amount (KES)</label>
                    <input type="number" name="amount" id="amount" min="1" step="1" value="{{ old('amount', $user->balance > 0 ? $user->balance : 150) }}" class="mt-1 block w-full px-3 py-2 bg-gray-600 text-white border border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('amount')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        Pay with M-Pesa
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Payments -->
        <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
            <h2 class="text-2xl font-bold text-white mb-4">Recent Payments</h2>
            @if ($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-600">
                        <thead class="bg-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-700 divide-y divide-gray-600">
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">KES {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status === 'completed' ? 'bg-green-900 text-green-100' : 'bg-yellow-900 text-yellow-100' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-300">No recent payments.</p>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#payments-table').DataTable({
                "order": [[5, "desc"]],
                "paging": true,
                "searching": true,
                "info": true,
                "responsive": true
            });
        });
    </script>
@endsection