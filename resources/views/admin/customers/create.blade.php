@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Page Heading -->
        <h1 class="text-3xl font-bold text-white mb-6">Create New Customer</h1>

        <!-- Form Section -->
        <div class="bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-700">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <!-- Grid Layout for Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('name') border-red-500 @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="Enter customer name" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" id="email" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="Enter customer email" 
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Phone</label>
                        <input type="text" name="phone" id="phone" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('phone') border-red-500 @enderror" 
                               value="{{ old('phone') }}" 
                               placeholder="254XXXXXXXXX" 
                               required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agent Field -->
                    <div>
                        <label for="agent_id" class="block text-sm font-medium text-gray-300 mb-1">Assign Agent</label>
                        <select name="agent_id" id="agent_id" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('agent_id') border-red-500 @enderror">
                            <option value="">Select an agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('agent_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" id="password" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('password') border-red-500 @enderror" 
                               placeholder="Enter password" 
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white" 
                               placeholder="Confirm password" 
                               required>
                    </div>

                    <!-- Initial Meter Reading Field -->
                    <div>
                        <label for="initial_reading" class="block text-sm font-medium text-gray-300 mb-1">Initial Meter Reading (Optional)</label>
                        <input type="number" name="initial_reading" id="initial_reading" 
                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('initial_reading') border-red-500 @enderror" 
                               value="{{ old('initial_reading', 0) }}" 
                               placeholder="Enter initial meter reading" 
                               min="0" step="0.01">
                        @error('initial_reading')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-400">Leave as 0 for new meters, or enter the current reading for existing meters</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection