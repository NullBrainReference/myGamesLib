@extends('layouts.app')

@section('title', 'Admin Dashboard - Users')

@section('content')
<div class="container py-4">
    <x-callback-message />

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-b border-gray-200">
        <div>
            <h1 class="h2 fw-bold text-gray-900 mb-1">Admin Dashboard</h1>
            <p class="text-gray-500 small mb-0">Manage platform users, update access permissions, and assign roles.</p>
        </div>
        <div class="btn-group shadow-sm bg-white rounded">
            <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-primary active px-3">Users</a>
            <a href="{{ route('dashboard.games') }}" class="btn btn-sm btn-outline-secondary px-3">Games</a>
            <a href="{{ route('dashboard.posts') }}" class="btn btn-sm btn-outline-secondary px-3">Posts</a>
            <a href="{{ route('dashboard.comments') }}" class="btn btn-sm btn-outline-secondary px-3">Comments</a>
        </div>
    </div>

    <div class="card border border-gray-200 rounded-lg p-3 bg-white shadow-sm mb-4">
        <form method="GET" action="{{ route('dashboard.users') }}" class="row g-2">
            <div class="col col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Search name or email...">
                    <button class="btn btn-primary px-3" type="submit">Search</button>
                </div>
            </div>
            @if(request('search'))
                <div class="col-auto d-flex align-items-center">
                    <a href="{{ route('dashboard.users') }}" class="text-sm text-gray-500 hover:text-gray-900 text-decoration-none">Clear Filters</a>
                </div>
            @endif
        </form>
    </div>

    @if($users->count())
        <div class="card border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase tracking-wider small text-gray-600 border-bottom border-gray-200">
                        <tr>
                            <th class="ps-4 py-3 font-semibold">Name</th>
                            <th class="py-3 font-semibold">Email</th>
                            <th class="py-3 font-semibold text-center">Status</th>
                            <th class="py-3 font-semibold">Role</th>
                            <th class="pe-4 py-3 text-end font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4 py-3 fw-medium text-gray-900">
                                    <a href="{{ route('profile.view', $user->id) }}" class="text-decoration-none text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $user->name }}
                                    </a>
                                </td>

                                <td class="py-3 text-gray-600">{{ $user->email }}</td>

                                <td class="py-3 text-center">
                                    @if($user->isBanned())
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 border border-danger-subtle rounded-pill font-medium">Banned</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1.5 border border-success-subtle rounded-pill font-medium">Active</span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    <span class="badge bg-light text-gray-700 px-2.5 py-1.5 border border-gray-200 rounded font-medium text-capitalize">
                                        {{ $user->role }}
                                    </span>
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    <form method="POST" action="{{ route('dashboard.users.updateRole', $user->id) }}" class="d-inline-flex align-items-center justify-content-end gap-2 ms-auto">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select form-select-sm border-gray-300 rounded w-auto shadow-sm text-sm" style="min-width: 120px;">
                                            @foreach(\App\Enums\UserRole::cases() as $role)
                                                <option value="{{ $role->value }}" @selected($user->role === $role->value)>
                                                    {{ ucfirst($role->value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-success font-medium shadow-sm px-2.5" type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 px-1">
            {{ $users->withQueryString()->links('pagination::simple-bootstrap-5') }}
        </div>
    @else
        <div class="card border border-gray-200 rounded-lg p-5 text-center bg-white shadow-sm">
            <div class="py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-2.533-2.336C18.151 15.653 16.7 15.4 15 15.4s-3.151.253-4.213.812a4.125 4.125 0 00-2.533 2.336 9.354 9.354 0 004.122.952 9.38 9.38 0 002.625-.372zm0-9.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
                </svg>
                <h5 class="fw-bold text-gray-900 mb-1">No matching users found</h5>
                <p class="text-gray-500 small mb-3">Your search query did not match any accounts currently registered in the system database.</p>
                <a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-secondary shadow-sm">Reset View Layout</a>
            </div>
        </div>
    @endif
</div>
@endsection
