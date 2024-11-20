<aside id="sidebar" class="js-sidebar">
	<!-- Content For Sidebar -->
	<div class="h-100 position-relative">
		<a href="{{ route('home') }}">
			<div class="sidebar-logo"> 
				<img class="w-100" src="{{ asset('assets/image/logo.png') }}" alt="Logo" style="max-width: 100px;">
			</div>
		</a>
		<button class="btn position-absolute top-0 end-0" id="sidebar-close" type="button">
			<i class="bi bi-x fs-4"></i>
		</button>
		<ul class="sidebar-nav">
			
			<li class="sidebar-item"> 
				<a href="{{ route('home') }}" class="sidebar-link active">
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
				<a href="{{ route('setting') }}" class="sidebar-link">
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