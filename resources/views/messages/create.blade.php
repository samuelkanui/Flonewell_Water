@extends('layouts.' . (auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'agent' ? 'agent' : 'app')))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-3xl font-bold text-white mb-6">New Message</h1>
    
    <div class="bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-700">
        <form action="{{ route('messages.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Recipient -->
            <div>
                <label for="receiver_id" class="block text-sm font-medium text-gray-300 mb-1">Send To</label>
                <select name="receiver_id" id="receiver_id" 
                        class="block w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('receiver_id') border-red-500 @enderror" 
                        required>
                    <option value="">Select recipient</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
                @error('receiver_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                <textarea name="message" id="message" rows="6"
                          class="block w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('message') border-red-500 @enderror" 
                          placeholder="Type your message here..." 
                          required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('messages.index') }}" 
                   class="inline-flex items-center px-6 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 