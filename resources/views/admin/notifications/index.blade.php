@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body pt-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="mb-0">Notifications</h5>
                    <small class="text-muted">{{ $unreadCount }} unread notification(s)</small>
                </div>
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Mark all as read</button>
                    </form>
                @endif
            </div>

            @if($notifications->isEmpty())
                <div class="alert alert-secondary mb-0">No notifications yet.</div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($notifications as $notification)
                        @php
                            $isUnread = is_null($notification->read_at);
                            $title = $notification->data['title'] ?? 'Notification';
                            $line = $notification->data['line'] ?? 'You have a new notification.';
                        @endphp
                        <article class="border rounded p-3 {{ $isUnread ? 'border-primary bg-light' : 'border-light-subtle' }}">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $title }}</h6>
                                    <p class="mb-2 text-muted">{{ $line }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('notifications.open', $notification->id) }}" class="btn btn-sm btn-primary">Open</a>
                                    @if($isUnread)
                                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark read</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
