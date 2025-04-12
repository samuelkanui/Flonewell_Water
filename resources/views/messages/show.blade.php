@extends('layouts.' . (auth()->user()->role === 'admin' ? 'admin' : (auth()->user()->role === 'agent' ? 'agent' : 'app')))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-white">Message</h1>
        <div class="flex space-x-4">
            <a href="{{ route('messages.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Back to Inbox
            </a>
            <a href="{{ route('messages.create') }}?reply_to={{ $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Reply
            </a>
        </div>
    </div>
    
    <div class="bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-700">
        <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-700">
            <div>
                @if($message->sender_id === auth()->id())
                    <h2 class="text-xl font-semibold text-white">To: {{ $message->receiver->name }}</h2>
                    <p class="text-sm text-gray-400">{{ ucfirst($message->receiver->role) }}</p>
                @else
                    <h2 class="text-xl font-semibold text-white">From: {{ $message->sender->name }}</h2>
                    <p class="text-sm text-gray-400">{{ ucfirst($message->sender->role) }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">{{ $message->created_at->format('F j, Y g:i A') }}</p>
                @if(!is_null($message->read_at) && $message->sender_id === auth()->id())
                    <p class="text-xs text-green-400">Read {{ $message->read_at->diffForHumans() }}</p>
                @endif
            </div>
        </div>
        
        <div class="prose prose-lg prose-invert max-w-none">
            <p class="text-white whitespace-pre-line">{{ $message->message }}</p>
        </div>
    </div>
</div>
@endsection 