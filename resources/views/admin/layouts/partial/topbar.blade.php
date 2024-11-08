<!-- Topbar Start -->
<div class="navbar-custom">
	<ul class="list-unstyled topnav-menu float-right mb-0">
		 
		<li class="dropdown notification-list">
			<a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="false" aria-expanded="false">
				@if(auth()->guard('admin')->user()->profile)
					<img src="{{ url('storage', auth()->guard('admin')->user()->profile) }}" alt="user-image" class="rounded-circle">
				@else
					<img src="{{ url('default-profile.png') }}" alt="user-image" class="rounded-circle">
				@endif 
				<span class="ml-1">{{ auth()->guard('admin')->user()->name }} <i class="mdi mdi-chevron-down"></i> </span>
			</a>
			<div class="dropdown-menu dropdown-menu-right profile-dropdown ">
				<!-- item-->
				<div class="dropdown-header noti-title">
					<h6 class="text-overflow m-0">Welcome !</h6>
				</div>
				
				<!-- item-->
				<a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
					<i class="fe-user"></i>
					<span>Profile</span>
				</a> 
				<div class="dropdown-divider"></div>
				
				<a class="dropdown-item notify-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
					<i class="fe-log-out"></i>
					<span>{{ __('Logout') }}</span>
				</a>

				<!-- Logout Form -->
				<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</div>
		</li>
		  
	</ul>
	
	<!-- LOGO -->
	<div class="logo-box">
		<a href="{{ route('index') }}" class="logo text-center">
			<span class="logo-lg">
				<img src="{{ url('front-end/img/logo/logo.png') }}" alt="" height="40">
				<!-- <span class="logo-lg-text-light">UBold</span> -->
			</span>
			<span class="logo-sm">
				<!-- <span class="logo-sm-text-dark">U</span> -->
				<img src="{{ url('front-end/img/logo/logo.png') }}" alt="" height="40">
			</span>
		</a>
	</div>
	
	<ul class="list-unstyled topnav-menu topnav-menu-left m-0">
		<li>
			<button class="button-menu-mobile waves-effect waves-light">
				<i class="fe-menu"></i>
			</button>
		</li> 
	</ul>
</div>
<!-- end Topbar -->