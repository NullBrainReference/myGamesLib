<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFriendController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success'           => true,
            'friends'           => $user->friends->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'email' => $f->email]),
            'incoming_requests' => $user->pendingFriendsReceived->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'email' => $f->email]),
            'sent_requests'     => $user->pendingFriendsSent->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'email' => $f->email]),
        ]);
    }

    public function sendRequest(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        if ($authUser->id === $user->id) {
            return response()->json(['message' => 'You cannot send a friend request to yourself.'], 422);
        }

        if (!$authUser->isFriendsWith($user) && !$authUser->hasPendingRequestTo($user)) {
            $authUser->pendingFriendsSent()->attach($user->id, ['status' => 'pending']);
        }

        return response()->json(['success' => true, 'message' => 'Friend request sent!']);
    }

    public function acceptRequest(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        if ($authUser->hasPendingRequestFrom($user)) {
            $authUser->pendingFriendsReceived()->updateExistingPivot($user->id, ['status' => 'accepted']);
        }

        return response()->json(['success' => true, 'message' => 'Friend request accepted!']);
    }

    public function removeFriend(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        $authUser->friendsOfThisUser()->detach($user->id);
        $authUser->thisUserFriendOf()->detach($user->id);
        $authUser->pendingFriendsSent()->detach($user->id);
        $authUser->pendingFriendsReceived()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Friend removed.']);
    }
}