<aside id="sidebar" class="js-sidebar">
	<!-- Content For Sidebar -->
	<div class="h-100">
		<div class="sidebar-logo"> 
			<img src="{{ asset('assets/image/logo.svg') }}" alt="Logo">
		</div>
		<ul class="sidebar-nav">
			
			<li class="sidebar-item"> 
				<a href="javascript:;" class="sidebar-link active">
					<img class="in-svg" src="{{ asset('assets/image/icons/home.svg') }}" alt="">
					Dashboard
				</a>
			</li>
			
			<li class="sidebar-item">
				<a href="#" class="sidebar-link">
					<img class="in-svg" src="{{ asset('assets/image/icons/cash.svg') }}" alt="">
					Transaction
				</a>
			</li>
			<li class="sidebar-item">
				<a href="#" class="sidebar-link">
					<img class="in-svg" src="{{ asset('assets/image/icons/bell.svg') }}" alt="">
					Notification
				</a>
			</li>
			<li class="sidebar-item">
				<a href="#" class="sidebar-link">
					<img class="in-svg" src="{{ asset('assets/image/icons/chart.svg') }}" alt="">
					Statistics
				</a>
			</li>
			<li class="sidebar-item">
				<a href="#" class="sidebar-link">
					<img class="in-svg" src="{{ asset('assets/image/icons/setting.svg') }}" alt="">
					Settings
				</a>
			</li>
			<li class="sidebar-item">
				<a class="sidebar-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
					<img class="in-svg" src="{{ asset('assets/image/icons/logout.svg') }}" alt="">
					{{ __('Logout') }}
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
					@csrf
				</form>
			</li> 
		</ul>
	</div>
</aside>