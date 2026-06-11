<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Mechanic;
use Illuminate\Support\Facades\Auth;

class MechanicController extends Controller
{
    public function store(Request $request, $game_id)
    {
        $approved = true;
        if (!Auth::user()->isAdmin()) {
            $approved = false;
            // abort(403);
        }

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $mechanic = Mechanic::create([
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'approved' => $approved,
        ]);

        $game = Game::findOrFail($game_id);
        $game->mechanics()->attach($mechanic->mechanic_id);

        return redirect()->back()->with('success', 'Gameplay mechanic created and linked successfully!');
    }

    // 2. Locate your update method:
    public function update(Request $request, $mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $mechanic = Mechanic::where('mechanic_id', $mechanic_id)->firstOrFail();

        $mechanic->update([
            'title'   => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Operational architecture parameters altered successfully.');
    }

    public function destroy($mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $mechanic = Mechanic::where('mechanic_id', $mechanic_id)->firstOrFail();
        $mechanic->delete();

        return redirect()->back()->with('success', 'Mechanic classification completely cleared from catalog schemas.');
    }
}
