<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $friends = $user->friends;
        $incomingRequests = $user->pendingFriendsReceived()->with('profile')->get();
        $sentRequests = $user->pendingFriendsSent()->with('profile')->get();

        return view('friends.index', compact('friends', 'incomingRequests', 'sentRequests'));
    }

    public function view(User $user)
    {
        $friends = $user->friends;

        return view('friends.view', compact('user', 'friends'));
    }

    public function sendRequest(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        if (!$authUser->isFriendsWith($user) && !$authUser->hasPendingRequestTo($user)) {
            $authUser->pendingFriendsSent()->attach($user->id, ['status' => 'pending']);
        }

        return back()->with('success', 'Friend request sent!');
    }

    public function acceptRequest(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->hasPendingRequestFrom($user)) {
            $authUser->pendingFriendsReceived()->updateExistingPivot($user->id, ['status' => 'accepted']);
        }

        return back()->with('success', 'Friend request accepted!');
    }

    public function removeFriend(User $user)
    {
        $authUser = Auth::user();

        // Detach friendship from both directions
        $authUser->friendsOfThisUser()->detach($user->id);
        $authUser->thisUserFriendOf()->detach($user->id);
        $authUser->pendingFriendsSent()->detach($user->id);
        $authUser->pendingFriendsReceived()->detach($user->id);

        return back()->with('success', 'Friend removed.');
    }
}
