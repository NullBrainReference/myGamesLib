<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Thread;
use App\Models\Project;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $latestGames = Game::with('tags')->orderBy('created_at', 'desc')->take(8)->get();

        $popularThreads = Thread::withCount('comments')
            ->with('user')
            ->orderBy('comments_count', 'desc')
            ->take(3)
            ->get();

        $currentProject = null;
        if ($user) {
            $currentProject = Project::whereHas('owners', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest()->first();
        }
        if (!$currentProject) {
            $currentProject = Project::where('is_public', true)->latest()->first();
        }

        $lastLibraryGame = null;
        if ($user) {
            $lastLibraryGame = $user->games()
                ->withPivot('updated_at')
                ->orderBy('game_user.updated_at', 'desc')
                ->first();
        }

        $latestBlogs = Blog::with('user')->latest()->take(3)->get();

        $projects = Project::where('is_public', true)
            ->withCount('mechanics')
            ->latest()
            ->take(6)
            ->get();

        return view('index', compact(
            'latestGames',
            'popularThreads',
            'currentProject',
            'lastLibraryGame',
            'latestBlogs',
            'projects'
        ));
    }
}
