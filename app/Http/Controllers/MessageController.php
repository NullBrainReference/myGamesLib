<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(User $user = null)
    {
        $authUser = Auth::user();

        // Get recent conversation partners, merged with friends list
        $conversations = $authUser->conversations()->merge($authUser->friends)->unique('id');

        $messages = collect();

        if ($user) {
            // Fetch two-way conversation between auth user and selected user
            $messages = Message::where(function ($q) use ($authUser, $user) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($authUser, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authUser->id);
            })->orderBy('created_at', 'asc')->get();

            // Mark unread messages as read
            Message::where('sender_id', $user->id)
                ->where('receiver_id', $authUser->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('messages.index', compact('conversations', 'user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.index', $user->id);
    }
}
