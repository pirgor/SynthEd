@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-bell me-2"></i> Notifications
                        @if ($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </h4>
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-check-all me-1"></i> Mark All as Read
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    @if ($notifications->count() > 0)
                        <div class="list-group">
                            @foreach ($notifications as $notification)
                                <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'list-group-item-info' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $notification->title }}</h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            @if ($notification->type == 'quiz_submitted')
                                                <i class="bi bi-clipboard-check me-1"></i> Quiz Submitted
                                            @elseif ($notification->type == 'new_student')
                                                <i class="bi bi-person-plus me-1"></i> New Student
                                            @elseif ($notification->type == 'deadline_approaching')
                                                <i class="bi bi-clock me-1"></i> Deadline Approaching
                                            @elseif ($notification->type == 'announcement')
                                                <i class="bi bi-megaphone me-1"></i> Announcement
                                                @if(isset($notification->data['sender_name']))
                                                    by {{ $notification->data['sender_name'] }}
                                                @endif
                                            @endif
                                        </small>
                                        @if (!$notification->is_read)
                                            <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                    Mark as Read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                            <p class="mt-3 text-muted">No notifications found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection