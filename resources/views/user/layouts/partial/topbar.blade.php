<style> 
.indicator {
	position: absolute;
	top: 0;
	right: 2px;
}
.indicator .circle {
	background: #6571ff;
	width: 7px;
	height: 7px;
	border-radius: 50%;
}
.indicator .circle::before {
	background-color: #6571ff;
	content: "";
	display: table;
	border-radius: 50%;
	position: absolute;
}
.indicator .circle::before, .pulse-1 {
	animation-name: pulse-1;
	animation-duration: .9s;
	animation-iteration-count: infinite;
	animation-timing-function: ease-out;
}
@keyframes pulse-1 {
	0% {
		opacity: 1;
		width: 7px;
		height: 7px;
		left: 0;
		top: 0;
	}
	95% {
		opacity: .1;
		left: -10.5px;
		top: -10.5px;
		width: 28px;
		height: 28px;
	}
	100% {
		opacity: 0;
		width: 7px;
		height: 7px;
		left: 0;
		top: 0;
	}
}
</style>
<nav class="navbar navbar-expand px-3 border-bottom"> 
	<button class="btn" id="sidebar-toggle" type="button">
		<span class="navbar-toggler-icon"></span>
	</button>
	<h6 class="mb-0 d-none d-lg-block heading-4">@yield('header_title')</h6>
	<div class="navbar-collapse navbar py-0">
		<ul class="navbar-nav gap-3">
			<!-- Wallet Balance -->
			<div class="d-flex align-items-center my-1 px-3 btn btn-sm btn-primary gap-2">
				<i class="bi bi-wallet2 heading-3"></i>
				<span>{{ Helper::decimalsprint(auth()->user()->balance, 2) }} {{ config('setting.default_currency') }}</span>
			</div>
			 
			<!-- Bell Icon Container -->
			<livewire:notification-dropdown />
			
			<!-- Profile Container -->
			<li class="nav-item dropdown d-flex align-items-center gap-2">
				<a href="javascipt:;" data-bs-toggle="dropdown" class="nav-icon pe-md-0"> 
					@if(auth()->user()->profile_image)  
					<img src="{{ url('storage/profile', auth()->user()->profile_image) }}" class="avatar img-fluid rounded-circle " alt="">
					@else
					<img src="{{ url('admin/default-profile.png') }}" class="avatar img-fluid rounded-circle " alt="">
					@endif 
				</a>
				<div class="d-flex flex-column me-3" data-bs-toggle="dropdown">
					<span class="fw-semibold">{{ auth()->user()->first_name. ' ' . auth()->user()->last_name }}</span>
					<span class="text-muted small">ID #{{ auth()->user()->id }}</span>
				</div> 
			</li>
		</ul>
	</div>
</nav>