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
        $games = Game::paginate(2);
        return view('games.shop', compact('games'));
    }

    public function view(int $id)
    {
        $game = Game::findOrFail($id);

        return view('games.view', compact('game'));
    }

    public function confirmRemoval(int $id)
    {
        $game = Game::findOrFail($id);

        return view('games.confirm_remove', compact('game'));
    }

    public function delete(int $id)
    {
        $game = Game::findOrFail($id);

        $game->delete();

        return redirect()->route('shop')->with('success', 'Game deleted successfully!');
    }

    public function create()
    {

        return view('games.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'img_src' => 'required|url',
        ]);

        Game::create($validated);

        return redirect()->route('shop')->with('success', 'Game created successfully!');
    }

    public function edit(int $id)
    {

        $game = Game::findOrFail($id);
        return view('games.edit', compact('game'));
    }

    public function update(Request $request, int $id)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'img_src' => 'required|url',
        ]);

        $game = Game::findOrFail($id);
        $game->update($validated);

        return redirect()->route('game.view', ['id' => $game->game_id])->with('success', 'Game updated successfully!');
    }

}
