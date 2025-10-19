@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">User Management</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Add User</a>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->student_id }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->user_role) }}</td>
                        <td>
                            @if ($user->status === 'enabled')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                            @endif
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>

                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>

                            <form action="{{ route('users.toggle', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button
                                    class="btn btn-sm {{ $user->status ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                    {{ $user->status ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
