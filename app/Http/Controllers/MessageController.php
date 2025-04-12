<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $receivedMessages = $user->receivedMessages()->with('sender')->latest()->get();
        $sentMessages = $user->sentMessages()->with('receiver')->latest()->get();
        
        $unreadCount = $user->unreadMessages()->count();
        
        return view('messages.index', compact('receivedMessages', 'sentMessages', 'unreadCount'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get users based on current user's role
        if ($user->isAdmin()) {
            // Admin can message all users
            $users = User::where('id', '!=', $user->id)->get();
        } elseif ($user->isAgent()) {
            // Agent can message admin only
            $users = User::where('role', 'admin')->get();
        } else {
            // Customer can message admin only
            $users = User::where('role', 'admin')->get();
        }
        
        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        
        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        // Check if the user is authorized to view this message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return redirect()->route('messages.index')->with('error', 'You do not have permission to view this message.');
        }
        
        // Mark as read if the user is the receiver
        if ($message->receiver_id === Auth::id() && is_null($message->read_at)) {
            $message->markAsRead();
        }
        
        return view('messages.show', compact('message'));
    }

    public function markAsRead(Message $message)
    {
        // Check if the user is the receiver
        if ($message->receiver_id !== Auth::id()) {
            return redirect()->route('messages.index')->with('error', 'You do not have permission to mark this message as read.');
        }
        
        $message->markAsRead();
        
        return redirect()->back()->with('success', 'Message marked as read.');
    }
}
