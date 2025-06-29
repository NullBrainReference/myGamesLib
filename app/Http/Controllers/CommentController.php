<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Game;

class CommentController extends Controller
{

    public function store(Request $request, int $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $game = Game::findOrFail($id);

        $game->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Comment created!');
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'No permission');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted.');
    }

    public function dashboard(Request $request)
    {
        $commentText = $request->input('comment');
        $gameTitle   = $request->input('game');
        $userName    = $request->input('user');

        $comments = Comment::with(['user', 'game'])
            ->when($commentText, fn ($q) => $q->where('content', 'like', "%{$commentText}%"))
            ->when($gameTitle, fn ($q) => $q->whereHas('game', fn ($g) => $g->where('title', 'like', "%{$gameTitle}%")))
            ->when($userName, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$userName}%")))
            ->latest()
            ->paginate(10);

        return view('dashboard.comments', compact('comments', 'commentText', 'gameTitle', 'userName'));
    }
}
