<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Mechanic;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Strategies\EntityListBehavior\GameListProcessor;

class GameController extends Controller
{

    public function shop(Request $request)
    {
        $query = Game::query();

        $processor = new GameListProcessor();

        $query = $processor->search($query, $request->input('search'));
        $query = $processor->applyExtraFilters($query, $request);
        $query = $processor->initialOrder($query, $request->input('sort', 'latest'));

        $games = $query->paginate(2)->withQueryString();

        return view('games.shop', compact('games'));
    }

    public function dashboard(Request $request)
    {
        $query = Game::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%$search%");
        }

        $games = $query->orderByDesc('created_at')->paginate(10);

        return view('dashboard.games', compact('games', 'search'));
    }

    public function view(int $id)
    {
        $game = Game::with('tags')->findOrFail($id);

        $mechanicsQuery = $game->mechanics();
        if (!Auth::check() || !Auth::user()->isAdmin()){
            $mechanicsQuery = $mechanicsQuery->where('approved', true);
        }
        $mechanics = $mechanicsQuery->paginate(10);

        $comments = $game->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(5);
        $reviews = $game->reviews()->with('user')->latest()->paginate(5);

        $backUrl = request()->input('back_url') ?? $this->fallbackBackUrl('shop');

        $allTags = null;

        $userReview = null;
        if (Auth::check()) {
            $userReview = $game->reviews()->where('user_id', Auth::id())->first();
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user && $user->isAdmin()) {
                $allTags = Tag::all();
            }
        }

        return view('games.view',
            compact('game',
            'comments',
            'reviews',
            'backUrl',
            'userReview',
            'allTags',
            'mechanics'));
    }

    public function confirmRemoval(int $id)
    {
        $game = Game::findOrFail($id);

        $backUrl = request()->input('back_url') ?? $this->fallbackBackUrl('shop');

        return view('games.confirm_remove', compact('game', 'backUrl'));
    }

    public function delete(Request $request, int $id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        $redirect = $request->input('back_url', route('shop'));

        return redirect($redirect)->with('success', 'Game deleted successfully!');
    }

    public function create()
    {

        return view('games.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('game_images', 'public');

        Game::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'img_src' => '/storage/' . $path,
        ]);

        return redirect($this->fallbackBackUrl('shop'))->with('success', 'Game created successfully!');
    }

    public function edit(int $id)
    {

        $game = Game::findOrFail($id);

        $backUrl = request()->headers->get('referer') ?? route('shop');

        return view('games.edit', compact('game', 'backUrl'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $game = Game::findOrFail($id);

        $game->title = $validated['title'];
        $game->description = $validated['description'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('game_images', 'public');
            $game->img_src = '/storage/' . $path;
        }

        $game->save();

        $redirect = $request->input('back_url', route('shop'));

        return redirect($redirect)->with('success', 'Game updated successfully!');
    }

    private function fallbackBackUrl(string $defaultRoute)
    {
        $referer = request()->headers->get('referer');

        if ($referer && str_contains($referer, '/dashboard')) {
            return route('dashboard.games');
        }

        return route($defaultRoute);
    }
}
