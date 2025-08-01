<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{

    public function view($id): View
    {
        $user = User::findOrFail($id);
        $profile = $user->profile;
        $games = $user->games;
        $isSelf = Auth::check() && Auth::id() === $user->id;

        return view('profile.view', compact('user', 'profile', 'games', 'isSelf'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->profile;
        return view('profile.edit', [
            'user' => $request->user(),
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $data = $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'about_me' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $user->profile()->create($data);
        }

        return redirect()->route('profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function comments($id)
    {
        $user = User::findOrFail($id);
        $comments = $user->comments()->with('commentable')->latest()->paginate(10);

        return view('profile.comments', compact('user', 'comments'));
    }

    public function ban($id)
    {
        $user = User::findOrFail($id);
        if (auth()->user()->isAdmin()) {
            $user->banned = true;
            $user->save();
        }
        return back();
    }

    public function unban($id)
    {
        $user = User::findOrFail($id);
        if (auth()->user()->isAdmin()) {
            $user->banned = false;
            $user->save();
        }
        return back();
    }
}
