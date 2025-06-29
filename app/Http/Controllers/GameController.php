<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    //
    public function shop()
    {
        $games = Game::paginate(2);
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
        $game = Game::findOrFail($id);

        $comments = $game->comments()->with('user')->latest()->paginate(5);

        return view('games.view', compact('game', 'comments'));
    }

    public function confirmRemoval(int $id)
    {
        $game = Game::findOrFail($id);

        $backUrl = request()->headers->get('referer') ?? route('shop');

        return view('games.confirm_remove', compact('game', 'backUrl'));
    }

    public function delete(Request $request, int $id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        // $redirectUrl = $this->fallbackBackUrl('shop');

        $redirect = $request->input('back_url', route('shop'));

        return redirect($redirect)->with('success', 'Game deleted successfully!');

        // return redirect($redirectUrl)->with('success', 'Game deleted successfully!');
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
            'img_src' => 'required|url',
        ]);

        Game::create($validated);

        return redirect($this->fallbackBackUrl('shop'))->with('success', 'Game created successfully!');
        // return redirect()->route('shop')->with('success', 'Game created successfully!');
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
            'img_src' => 'required|url',
        ]);

        $game = Game::findOrFail($id);
        $game->update($validated);

        $redirect = $request->input('back_url', route('shop'));

        return redirect($redirect)->with('success', 'Game updated successfully!');

        // return redirect($this->fallbackBackUrl('shop'))->with('success', 'Game updated successfully!');
        // return redirect()->route('game.view', ['id' => $game->game_id])->with('success', 'Game updated successfully!');
    }

    private function fallbackBackUrl(string $defaultRoute)
    {
        $referer = request()->headers->get('referer');

        // Безопасная проверка пути
        if ($referer && str_contains($referer, '/dashboard')) {
            return route('dashboard.games');
        }

        return route($defaultRoute);
    }
}
