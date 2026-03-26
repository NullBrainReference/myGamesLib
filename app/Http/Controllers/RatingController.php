<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request, $gameId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        Rating::updateOrCreate(
            ['user_id' => $user->id, 'game_id' => $gameId],
            ['rating' => $request->rating]
        );

        return back()->with('success', 'Rating submitted!');
    }
}
