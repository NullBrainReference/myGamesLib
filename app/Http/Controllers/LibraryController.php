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

            return view('library.library', compact('games'));
        }

        return redirect()->route('login')->with('error', 'You must be logged in to access your library.');
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
}
