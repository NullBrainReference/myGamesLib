@extends('layouts.app')

@section('content')
<div class="container">
    <x-callback-message />

    <h1>Users</h1>

    <form method="GET" action="{{ route('dashboard.users') }}" class="mb-3 d-flex">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Поиск по имени или email">
        <button class="btn btn-primary">Search</button>
    </form>

    @if($users->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <form method="POST" action="{{ route('dashboard.users.updateRole', $user->id) }}" class="d-flex">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select me-2">
                                    @foreach(\App\Enums\UserRole::cases() as $role)
                                        <option value="{{ $role->value }}" @selected($user->role === $role->value)>{{ $role->value }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-success">OK</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->withQueryString()->links('pagination::simple-bootstrap-5') }}

        {{-- {{ $users->withQueryString()->links() }} --}}
    @else
        <p>No users (o_o)</p>
    @endif
</div>
@endsection