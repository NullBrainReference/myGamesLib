<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    //
    public function shop(){
        $games = Game::all();
        return view('index', compact($games));
    }

    public function view(int $id){
        $game = Game::findOrFail($id);

        return view('games.view', compact('game'));
    }
}
