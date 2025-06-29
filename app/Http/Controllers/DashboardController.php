<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class DashboardController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->orderByDesc('created_at')->paginate(10);

        return view('dashboard.index', compact('users', 'search'));
    }

    public function updateRole(Request $request, int $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->input('role');
        $user->save();

        return back()->with('success', 'Role updated.');
    }
}
