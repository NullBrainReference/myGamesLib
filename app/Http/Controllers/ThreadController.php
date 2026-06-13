<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);

        return view('forum.view', compact('thread', 'comments'));
    }

    public function create()
    {
        return view('forum.create');
    }

    /**
     * Store a newly created thread in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming text data
        $request->validate([
            'title'   => 'required|string|max:255|min:5',
            'content' => 'required|string|min:10',
        ]);

        // Create the record tied to the authenticated user
        $thread = Thread::create([
            'user_id'   => Auth::id(),
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_locked' => false,
            'is_pinned' => false, // Default to normal state; let admins pin later
        ]);

        // Redirect directly to the newly created thread view page
        return redirect()->route('forum.thread', $thread->id)
            ->with('success', 'Your thread has been posted successfully!');
    }

}
