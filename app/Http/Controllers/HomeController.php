<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class HomeController extends Controller
{
    //
    public function index()
    {
        $latestGame = Game::orderBy('created_at', 'desc')->first();
        return view('index', compact('latestGame'));
        // return view('index');
    }
}
