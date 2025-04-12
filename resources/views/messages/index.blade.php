@extends('layouts.' . (auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'agent' ? 'agent' : 'app')))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">Messages</h1>
        <a href="{{ route('messages.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            New Message
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Inbox -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <div class="bg-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Inbox</h2>
                @if($unreadCount > 0)
                    <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs">{{ $unreadCount }} new</span>
                @endif
            </div>
            <div class="divide-y divide-gray-700">
                @forelse($receivedMessages as $message)
                    <a href="{{ route('messages.show', $message) }}" class="block px-6 py-4 hover:bg-gray-700 transition-colors {{ is_null($message->read_at) ? 'bg-gray-700' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-white">From: {{ $message->sender->name }}</p>
                                <p class="text-sm text-gray-300 mt-1 truncate">{{ \Illuminate\Support\Str::limit($message->message, 50) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                                @if(is_null($message->read_at))
                                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-4 text-gray-400">
                        <p>No messages received.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sent -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <div class="bg-gray-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Sent</h2>
            </div>
            <div class="divide-y divide-gray-700">
                @forelse($sentMessages as $message)
                    <a href="{{ route('messages.show', $message) }}" class="block px-6 py-4 hover:bg-gray-700 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-white">To: {{ $message->receiver->name }}</p>
                                <p class="text-sm text-gray-300 mt-1 truncate">{{ \Illuminate\Support\Str::limit($message->message, 50) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                                @if(!is_null($message->read_at))
                                    <span class="text-xs text-gray-400">Read</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-4 text-gray-400">
                        <p>No sent messages.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 