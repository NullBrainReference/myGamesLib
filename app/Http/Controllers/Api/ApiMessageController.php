<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiMessageController extends Controller
{
    /**
     * Get recent conversation contacts merged with the friends list.
     */
    public function conversations(Request $request): JsonResponse
    {
        $authUser = $request->user();

        // Merge recent conversation partners with accepted friends list
        $contacts = $authUser->conversations()->merge($authUser->friends)->unique('id')->values();

        // Attach last message snippet for each contact
        $contactsWithLastMsg = $contacts->map(function ($contact) use ($authUser) {
            $lastMessage = Message::where(function ($q) use ($authUser, $contact) {
                $q->where('sender_id', $authUser->id)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($authUser, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $authUser->id);
            })->latest()->first();

            return [
                'id'           => $contact->id,
                'name'         => $contact->name,
                'email'        => $contact->email,
                'role'         => $contact->role,
                'last_message' => $lastMessage ? [
                    'body'       => $lastMessage->body,
                    'created_at' => $lastMessage->created_at->toIso8601String(),
                    'sender_id'  => $lastMessage->sender_id,
                ] : null,
            ];
        });

        return response()->json([
            'success'       => true,
            'conversations' => $contactsWithLastMsg,
        ]);
    }

    /**
     * Get two-way chat messages between auth user and target user.
     */
    public function messages(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        $messages = Message::where(function ($q) use ($authUser, $user) {
            $q->where('sender_id', $authUser->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authUser, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $authUser->id);
        })->orderBy('created_at', 'asc')->get();

        // Mark unread incoming messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success'  => true,
            'contact'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to a user.
     */
    public function send(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $user->id,
            'body'        => $validated['body'],
        ]);

        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ], 201);
    }
}