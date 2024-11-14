<nav class="navbar navbar-expand px-3 border-bottom"> 
	<button class="btn d-md-none" id="sidebar-toggle" type="button">
		<span class="navbar-toggler-icon"></span>
	</button>
	<h6 class="mb-0">Dashboard</h6>
	<div class="navbar-collapse navbar">
		<ul class="navbar-nav gap-3">
			<!-- Wallet Balance -->
			<div class="d-flex align-items-center my-1 px-3 balance gap-2">
				<i class="fa-solid fa-wallet"></i>
				<span>1200 USD</span>
			</div>
			<!-- Bell Icon Container -->
			<li class="nav-item dropdown align-content-center">
				<a href="#" data-bs-toggle="dropdown">
					<div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center bell-icon">
						<i class="fas fa-bell text-dark"></i>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-end">
					<a href="#" class="dropdown-item">Notification 1</a>
					<a href="#" class="dropdown-item">Notification 2</a>
					<a href="#" class="dropdown-item">Notification 3</a>
				</div>
			</li>
			<!-- Profile Container -->
			<li class="nav-item dropdown d-flex align-items-center gap-2">
				<a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
					<img src="{{asset('assets/image/profile.jpg') }}" class="avatar img-fluid rounded-circle " alt="">
				</a>
				<div class="d-flex flex-column me-3" data-bs-toggle="dropdown">
					<span class="fw-semibold">Jenuar Rhapsody</span>
					<span class="text-muted small">ID 223812*****</span>
				</div>
				<div class="dropdown-menu dropdown-menu-end">
					<a href="#" class="dropdown-item">Profile</a>
					<a href="#" class="dropdown-item">Setting</a>
					<a href="/login.html" class="dropdown-item">Logout</a>
				</div>
			</li>
		</ul>
	</div>
</nav>