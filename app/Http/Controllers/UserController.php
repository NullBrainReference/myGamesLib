<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search'));

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderBy('name', 'asc')
        ->paginate(12)
        ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }
}
