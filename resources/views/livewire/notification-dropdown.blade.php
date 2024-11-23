<li class="nav-item dropdown align-content-center">
    <a href="#" data-bs-toggle="dropdown">
        <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center bell-icon position-relative">
            <i class="bi bi-bell-fill text-primary"></i>
            <div class="indicator" style="display: {{ $unreadCount > 0 ? 'block' : 'none' }}">
				<div class="circle"></div>
			</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px;">
        <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p class="mb-0 content-3 text-muted opacity-75">
                {{ $unreadCount }} New Notifications
            </p>
        </div>
        <div class="p-1">
            @forelse ($notifications as $notification)
                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                        <img class="w-30px h-30px rounded-circle" src="{{ $notification['image'] }}" alt="user">
                    </div>
                    <div class="flex-grow-1 me-2">
                        <p class="mb-0 content-3 text-wrap">{{ $notification['message'] }}</p>
                        <p class="fs-12px text-muted opacity-50 content-4 mb-0">{{ $notification['time'] }}</p>
                    </div>
                </a>
            @empty
                <p class="text-center py-3 text-muted">No unread notifications</p>
            @endforelse
        </div>
        <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a class="content-3 text-primary" href="javascript:;" >View all</a>
        </div>
    </div>
</li>
