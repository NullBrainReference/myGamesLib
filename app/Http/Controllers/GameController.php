<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    //
    public function shop()
    {
        $games = Game::all();
        return view('games.shop', compact('games'));
        // return view('index', compact($games));
    }

    public function view(int $id)
    {
        $game = Game::findOrFail($id);

        return view('games.view', compact('game'));
    }

    public function confirmRemoval(int $id)
    {
        $game = Game::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized'); // Prevent non-admins from accessing this
        }

        return view('games.confirm_remove', compact('game'));
    }

    public function delete(int $id)
    {
        $game = Game::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized'); // Prevent deletion by non-admins
        }

        $game->delete();

        return redirect()->route('shop')->with('success', 'Game deleted successfully!');
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized'); // Only admins can create games
        }

        return view('games.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'img_src' => 'required|url',
        ]);

        Game::create($validated);

        return redirect()->route('shop')->with('success', 'Game created successfully!');
    }

    public function addToLibrary(int $gameId)
    {
        $user = Auth::user();
        if ($user) 
        {
            $game = Game::findOrFail($gameId);
            //dd($user->id, $game->game_id);

            $user->games()->syncWithoutDetaching($game->game_id);
            
            return back()->with('success', 'Game added to your library!');
        }

        return redirect()->route('login')->with('error', 'You must be logged in to add games.');
    }

    public function library()
    {
        $user = Auth::user();   

        if ($user) 
        {
            $games = $user->games;;

            return view('games.library', compact('games'));
        }

        return redirect()->route('login')->with('error', 'You must be logged in to add games.');
    }
}
