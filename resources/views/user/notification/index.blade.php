@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Notifications')
@section('header_title', 'Notifications')
@section('content')
 
<div class="container-fluid p-0">
  <ul class="list-group gap-2">
	  @forelse($recentNotifications as $notification)
		<li class="list-group-item border rounded-2">
		  <div class="d-flex">
			<div class="flex-shrink-0 me-3"><img src="{{ $notification->data['receive_image'] ?? url('admin/default-profile.png') }}" class="avatar-xs img-fluid rounded-circle " alt=""></div>
			<div class="flex-grow-1"> 
			  <p class="content-3 text-muted mb-1">{{ $notification->data['comment'] }}</p>
			  <p class="text-muted mb-0 content-4">{{ $notification->created_at->format('d M, Y') }}</p>
			</div>
		  </div>
		</li>
     @empty
		 <li class="list-group-item border rounded-2">
			<p>No notifications in the last 2 days.</p>
		</li>
    @endforelse
  </ul>
</div>

@endsection