<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $gameId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        Review::create([
            'user_id' => $user->id,
            'game_id' => $gameId,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Review posted!');
    }

    public function update(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if (Auth::id() !== $review->user_id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        $review->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Review updated!');
    }

    public function delete($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized');
        }

        $review->delete();
        return back()->with('success', 'Review deleted!');
    }
}
