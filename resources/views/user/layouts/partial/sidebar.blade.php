<aside id="sidebar" class="js-sidebar">
	<!-- Content For Sidebar -->
	<div class="h-100">
		<div class="sidebar-logo"> 
			<img src="{{ asset('assets/image/logo.svg') }}" alt="Logo">
		</div>
		<ul class="sidebar-nav">
			
			<li class="sidebar-item"> 
				<a href="javascript:;" class="sidebar-link">
					<i class="fa-solid fa-list pe-2"></i>
					Dashboard
				</a>
			</li>
			
			<li class="sidebar-item">
				<a href="#" class="sidebar-link">
					<i class="fa-solid fa-list pe-2"></i>
					Settings
				</a>
			</li>
			<li class="sidebar-item">
				<a class="sidebar-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
					<i class="fa-solid fa-list pe-2"></i>
					{{ __('Logout') }}
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
					@csrf
				</form>
			</li> 
		</ul>
	</div>
</aside>