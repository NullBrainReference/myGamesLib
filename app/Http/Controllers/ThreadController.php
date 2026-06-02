<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function forum(Request $request)
    {
        $query = Thread::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $threads = $query->paginate(10);

        return view('forum.forum', compact('threads'));
    }

    public function view(int $id)
    {
        $thread = Thread::with(['user', 'comments.user'])->findOrFail($id);

        return view('forum.show', compact('thread'));
    }

}
