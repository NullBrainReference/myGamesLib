@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>

        @if(auth()->user()->unread_notifications_count > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="block p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-indigo-300 transition-all {{ !$notification->is_read ? 'border-l-4 border-l-indigo-600 bg-indigo-50/20' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">
                                {{ str_replace('_', ' ', $notification->type) }}
                            </span>
                            <h3 class="text-sm font-bold text-gray-900 mt-0.5">{{ $notification->title }}</h3>
                            @if($notification->message)
                                <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
                            @endif
                        </div>
                        <span class="text-[11px] text-gray-400 shrink-0">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <h3 class="text-sm font-bold text-gray-800">No notifications yet</h3>
            <p class="text-xs text-gray-500 mt-1">We'll notify you when something important happens.</p>
        </div>
    @endif
</div>
@endsection
