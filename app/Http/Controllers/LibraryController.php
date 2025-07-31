<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;

class LibraryController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            $games = $user->games;
            $canDelete = true;
            return view('library.library', compact('games', 'canDelete'));
        }

        return redirect()->route('login')->with('error', 'You must be logged in to access your library.');
    }

    public function userLibrary($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $games = $user->games;
        $canDelete = (Auth::check() && Auth::id() == $user->id);
        return view('library.library', compact('games', 'user', 'canDelete'));
    }

    public function add(int $gameId)
    {
        $user = Auth::user();
        
        if ($user) {
            $game = Game::findOrFail($gameId);
            $user->games()->syncWithoutDetaching($game->game_id);

            return back()->with('success', 'Game added to your library!');
        }

        return redirect()->route('login')->with('error', 'You must be logged in to add games.');
    }

    public function remove($gameId)
    {
        $user = Auth::user();
        if ($user) {
            $user->games()->detach($gameId);
            return back()->with('success', 'Game was removed from your library.');
        }
        return redirect()->route('login')->with('error', 'You must be logged in.');
    }
}
