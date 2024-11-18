<nav class="sidebar">
	<div class="sidebar-header">
		<a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
			<img src="{{ url('storage/setting', config('setting.site_logo')) }}" style="height:50px; width:150px">
		</a>
		<div class="sidebar-toggler">
			<span></span>
			<span></span>
			<span></span>
		</div>
	</div>
	<div class="sidebar-body">
		<ul class="nav" id="sidebarNav">
			<li class="nav-item nav-category">Main</li>
			<li class="nav-item">
				<a href="{{ route('admin.dashboard') }}" class="nav-link">
					<i class="link-icon" data-feather="box"></i>
					<span class="link-title">Dashboard</span>
				</a>
			</li>
			
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="collapse" href="#settings" role="button" aria-expanded="false" aria-controls="settings">
					<i class="link-icon" data-feather="anchor"></i>
					<span class="link-title">Setting</span>
					<i class="link-arrow" data-feather="chevron-down"></i>
				</a>
				<div class="collapse" data-bs-parent="#sidebarNav" id="settings">
					<ul class="nav sub-menu">
						<li class="nav-item">
							<a href="{{ route('admin.general-setting')}}" class="nav-link">General</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.banner')}}" class="nav-link">Banner</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('admin.faqs')}}" class="nav-link">FAQ's</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">Third Party API</a>
						</li>
					</ul>
				</div>
			</li> 
			
			<li class="nav-item">
				<a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
					<i class="link-icon" data-feather="log-out"></i>
					<span class="link-title">Logout</span>
				</a>
				<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</li>
		</ul>
	</div>
</nav>