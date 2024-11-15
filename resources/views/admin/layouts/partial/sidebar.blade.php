<aside class="sidebar">
	<button type="button" class="sidebar-close-btn">
		<iconify-icon icon="radix-icons:cross-2"></iconify-icon>
	</button>
	<div>
		<a href="index.html" class="sidebar-logo">
			<img src="{{ asset('admin/images/logo.png') }}" alt="site logo" class="light-logo">
			<img src="{{ asset('admin/images/logo-light.png') }}" alt="site logo" class="dark-logo">
			<img src="{{ asset('admin/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
		</a>
	</div>
	<div class="sidebar-menu-area">
		<ul class="sidebar-menu" id="sidebar-menu">
		
			<li>
				<a href="{{ route('admin.dashboard') }}">
					<iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
					<span>Dashboard</span>
				</a> 
			</li>
			
			<li>
				<a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
					<!-- Change to a logout icon -->
					<iconify-icon icon="lucide:power" class="menu-icon"></iconify-icon>
					<span>Logout</span>
				</a>
				
				<!-- Logout Form -->
				<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</li>

		</ul>
	</div>
</aside>