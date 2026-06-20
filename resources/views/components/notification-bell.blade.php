@php
    $user = auth()->user();
    $unread = $user ? $user->unreadNotifications()->count() : 0;
    $recent = $user ? $user->notifications()->latest()->take(6)->get() : collect();
    $locale = app()->getLocale();
@endphp
<details class="notification-bell" style="position:relative;display:inline-block;">
    <summary style="list-style:none;cursor:pointer;position:relative;display:inline-flex;align-items:center;color:inherit;">
        <i data-lucide="bell" style="width:20px;height:20px"></i>
        @if($unread > 0)
            <span style="position:absolute;top:-6px;right:-8px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;border-radius:99px;padding:1px 5px;">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
    </summary>
    <div style="position:absolute;right:0;margin-top:8px;width:300px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.15);z-index:50;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;font-weight:600;color:#0f172a;">Notifications</div>
        @forelse($recent as $n)
            <form method="POST" action="{{ route('notifications.read', ['locale' => $locale, 'id' => $n->id]) }}" style="margin:0;">
                @csrf
                <button type="submit" style="display:block;width:100%;text-align:left;background:{{ $n->read_at ? '#fff' : '#ecfdf5' }};border:none;border-bottom:1px solid #f1f5f9;padding:10px 16px;cursor:pointer;">
                    <span style="font-size:13px;font-weight:600;color:#0f172a;display:block;">{{ $n->data['title'] ?? 'Notification' }}</span>
                    <span style="font-size:12px;color:#64748b;display:block;">{{ \Illuminate\Support\Str::limit($n->data['body'] ?? '', 60) }}</span>
                </button>
            </form>
        @empty
            <div style="padding:16px;color:#94a3b8;font-size:13px;text-align:center;">No notifications.</div>
        @endforelse
        <a href="{{ route('notifications.index', ['locale' => $locale]) }}" style="display:block;padding:10px 16px;text-align:center;color:#00C896;font-size:13px;font-weight:600;text-decoration:none;">View all</a>
    </div>
</details>
