<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Game;

class CommentController extends Controller
{

    public function store(Request $request, string $type, int $id)
    {
        if (auth()->user()->isBanned()) {
            return back()->with('error', 'Banned users cannot post comments.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $modelClass = match ($type) {
            'game' => \App\Models\Game::class,
            'blog' => \App\Models\Blog::class,
            default => throw new \InvalidArgumentException('Type mismatch'),
        };

        $commentable = $modelClass::findOrFail($id);

        $commentable->comments()->create([
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
        $objectTitle = $request->input('object');
        $userName    = $request->input('user');

        $comments = Comment::with(['user', 'commentable'])
            ->when($commentText, fn ($q) => $q->where('content', 'like', "%{$commentText}%"))
            ->when($objectTitle, function ($q) use ($objectTitle) {
                $q->where(function ($subQuery) use ($objectTitle) {
                    $subQuery
                        ->where(function ($qGame) use ($objectTitle) {
                            $qGame->where('commentable_type', \App\Models\Game::class)
                                ->whereHasMorph('commentable', [\App\Models\Game::class], fn ($g) => 
                                    $g->where('title', 'like', "%{$objectTitle}%")
                                );
                        })
                        ->orWhere(function ($qPost) use ($objectTitle) {
                            $qPost->where('commentable_type', \App\Models\Blog::class)
                                ->whereHasMorph('commentable', [\App\Models\Blog::class], fn ($p) => 
                                    $p->where('title', 'like', "%{$objectTitle}%")
                                );
                        });
                });
            })
            ->when($userName, fn ($q) => 
                $q->whereHas('user', fn ($u) => 
                    $u->where('name', 'like', "%{$userName}%")
                )
            )
            ->latest()
            ->paginate(10);

        return view('dashboard.comments', compact('comments', 'commentText', 'objectTitle', 'userName'));
    }

}
