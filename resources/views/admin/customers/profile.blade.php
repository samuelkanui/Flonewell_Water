<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-white">Customer Profile</h2>
                <div class="flex items-center space-x-4">
                    <!-- Status Toggle Button -->
                    <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium {{ $customer->is_active ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                            <span class="mr-2">
                                @if($customer->is_active)
                                    <i class="fas fa-ban"></i>
                                @else
                                    <i class="fas fa-check"></i>
                                @endif
                            </span>
                            {{ $customer->is_active ? 'Block Account' : 'Unblock Account' }}
                        </button>
                    </form>

                    <!-- Delete Account Button -->
                    <form action="{{ route('admin.customers.delete', $customer) }}" method="POST" 
                        onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-md text-sm font-medium">
                            <span class="mr-2">
                                <i class="fas fa-trash"></i>
                            </span>
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="mt-6 bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
                <div class="p-6">
                    <form action="{{ route('admin.customers.profile.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}"
                                    class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}"
                                    class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-300">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                                    class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-300">Address</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $customer->address) }}"
                                    class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                                <span class="mr-2">
                                    <i class="fas fa-save"></i>
                                </span>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Meter Readings -->
                <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
                    <div class="px-6 py-4 bg-blue-600">
                        <h3 class="text-lg font-semibold text-white">Recent Meter Readings</h3>
                    </div>
                    <div class="p-6">
                        @if($meterReadings->count() > 0)
                            <div class="space-y-4">
                                @foreach($meterReadings as $reading)
                                    <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                                        <div>
                                            <p class="text-sm font-medium text-white">Reading: {{ $reading->reading }}</p>
                                            <p class="text-sm text-gray-400">{{ $reading->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium {{ $reading->status === 'approved' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }} rounded-full">
                                            {{ ucfirst($reading->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-sm">No recent meter readings</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
                    <div class="px-6 py-4 bg-blue-600">
                        <h3 class="text-lg font-semibold text-white">Recent Payments</h3>
                    </div>
                    <div class="p-6">
                        @if($payments->count() > 0)
                            <div class="space-y-4">
                                @foreach($payments as $payment)
                                    <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                                        <div>
                                            <p class="text-sm font-medium text-white">Amount: KES {{ number_format($payment->amount, 2) }}</p>
                                            <p class="text-sm text-gray-400">{{ $payment->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium {{ $payment->status === 'completed' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }} rounded-full">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-sm">No recent payments</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</x-admin-layout> 