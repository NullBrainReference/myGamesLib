<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Game;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:tags,title',
            'description' => 'nullable|string|max:1000',
            'is_r18' => 'nullable|boolean',
        ]);

        Tag::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'is_r18' => $request->has('is_r18'),
        ]);

        return redirect()->back()->with('success', 'New global tag created successfully!');
    }

    public function attach(Request $request, int $gameId)
    {
        $request->validate([
            'tag_id' => 'required|exists:tags,tag_id',
        ]);

        $game = Game::findOrFail($gameId);

        // syncWithoutDetaching prevents duplicate rows in your pivot table
        $game->tags()->syncWithoutDetaching([$request->input('tag_id')]);

        return redirect()->back()->with('success', 'Tag added to game successfully!');
    }

    public function detach(int $gameId, int $tagId)
    {
        $game = Game::findOrFail($gameId);
        $game->tags()->detach($tagId);

        return redirect()->back()->with('success', 'Tag removed from game successfully!');
    }
}
