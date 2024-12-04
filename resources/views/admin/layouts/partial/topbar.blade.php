<nav class="navbar">
	<div class="navbar-content">
		
		<div class="logo-mini-wrapper">
			<img src="{{ asset('admin/images/logo-mini-light.png') }}" class="logo-mini logo-mini-light" alt="logo">
			<img src="{{ asset('admin/images/logo-mini-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo">
		</div>
		
		<form class="search-form">
			<div class="input-group">
				<div class="input-group-text">
					<i data-feather="search"></i>
				</div>
				<input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
			</div>
		</form>
		
		<ul class="navbar-nav">
			<li class="theme-switcher-wrapper nav-item">
				<input type="checkbox" value="" id="theme-switcher">
				<label for="theme-switcher">
					<div class="box">
						<div class="ball"></div>
						<div class="icons">
							<i class="feather icon-sun"></i>
							<i class="feather icon-moon"></i>
						</div>
					</div>
				</label>
			</li>
		   
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i data-feather="bell"></i>
					<div class="indicator">
						<div class="circle"></div>
					</div>
				</a>
				<div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
					<div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
						<p>6 New Notifications</p>
						<a href="javascript:;" class="text-secondary">Clear all</a>
					</div>
					<div class="p-1">
						<a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
							<div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
								<i class="icon-sm text-white" data-feather="gift"></i>
							</div>
							<div class="flex-grow-1 me-2">
								<p>New Order Recieved</p>
								<p class="fs-12px text-secondary">30 min ago</p>
							</div>	
						</a>
						<a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
							<div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
								<i class="icon-sm text-white" data-feather="alert-circle"></i>
							</div>
							<div class="flex-grow-1 me-2">
								<p>Server Limit Reached!</p>
								<p class="fs-12px text-secondary">1 hrs ago</p>
							</div>	
						</a>
						<a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
							<div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
								<img class="w-30px h-30px rounded-circle" src="https://via.placeholder.com/30x30" alt="userr">
							</div>
							<div class="flex-grow-1 me-2">
								<p>New customer registered</p>
								<p class="fs-12px text-secondary">2 sec ago</p>
							</div>	
						</a>
						<a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
							<div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
								<i class="icon-sm text-white" data-feather="layers"></i>
							</div>
							<div class="flex-grow-1 me-2">
								<p>Apps are ready for update</p>
								<p class="fs-12px text-secondary">5 hrs ago</p>
							</div>	
						</a>
						<a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
							<div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
								<i class="icon-sm text-white" data-feather="download"></i>
							</div>
							<div class="flex-grow-1 me-2">
								<p>Download completed</p>
								<p class="fs-12px text-secondary">6 hrs ago</p>
							</div>	
						</a>
					</div>
					<div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
						<a href="javascript:;">View all</a>
					</div>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
					@if(auth()->guard('admin')->user()->profile) 
						<img src="{{ url('storage/admin_profile', auth()->guard('admin')->user()->profile) }}" alt="user-image" class="w-30px h-30px ms-1 rounded-circle">
					@else
						<img src="{{ url('admin/default-profile.png') }}" alt="user-image" class="w-30px h-30px ms-1 rounded-circle">
					@endif 
				</a>
				<div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
					<div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
						<div class="mb-3"> 
							@if(auth()->guard('admin')->user()->profile) 
								<img src="{{ url('storage/admin_profile', auth()->guard('admin')->user()->profile) }}" alt="user-image" class="w-80px h-80px rounded-circle">
							@else
								<img src="{{ url('admin/default-profile.png') }}" alt="user-image" class="w-80px h-80px rounded-circle">
							@endif 
						</div>
						<div class="text-center">
							<p class="fs-16px fw-bolder">{{ auth()->guard('admin')->user()->name }}</p>
							<p class="fs-12px text-secondary">{{ auth()->guard('admin')->user()->email }}</p>
						</div>
					</div>
					<ul class="list-unstyled p-1">
						<a href="{{ route('admin.profile') }}" class="text-body ms-0">
							<li class="dropdown-item py-2">
								<i class="me-2 icon-md" data-feather="user"></i>
								<span>Profile</span>
							</li> 
						</a>
						<a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-body ms-0">
							<li class="dropdown-item py-2">
								<i class="me-2 icon-md" data-feather="log-out"></i>
								<span>Log Out</span>
								<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
									@csrf
								</form>
							</li>
						</a>
					</ul>
				</div>
			</li>
		</ul>
		
		<a href="#" class="sidebar-toggler">
			<i data-feather="menu"></i>
		</a> 
	</div>
</nav>