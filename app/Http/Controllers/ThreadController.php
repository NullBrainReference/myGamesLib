<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Strategies\EntityListBehavior\ThreadListProcessor;

class ThreadController extends Controller
{

    public function forum(Request $request)
    {
        $query = Thread::with('user')->withCount('comments');

        $processor = new ThreadListProcessor();

        $query = $processor->search($query, $request->input('search'));
        $query = $processor->applyExtraFilters($query, $request);
        $query = $processor->initialOrder($query, $request->input('sort', 'latest'));

        $threads = $query->paginate(10)->withQueryString();

        return view('forum.forum', compact('threads'));
    }

    public function view(int $id)
    {
        $thread = Thread::with('user')->findOrFail($id);

        $comments = $thread->comments()
                        ->with('user')
                        ->latest()
                        ->paginate(10);

        return view('forum.view', compact('thread', 'comments'));
    }

}
