<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('chat', ['users' => $users]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        auth()->login($user, remember: true);

        // Regenerate session untuk security
        $request->session()->regenerate();

        return redirect()->route('chat.index');
    }

    public function getMessages()
    {
        $messages = Message::with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'body' => 'required|string|max:500',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $message = Message::create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $message->load('user');

        broadcast(new ChatMessageSent($message));

        return response()->json($message);
    }
}

