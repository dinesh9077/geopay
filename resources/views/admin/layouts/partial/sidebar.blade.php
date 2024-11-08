<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">
	
	<div class="slimscroll-menu">
		
		<!--- Sidemenu -->
		<div id="sidebar-menu">
			
			<ul class="metismenu" id="side-menu">
				
				<li class="menu-title">Navigation</li>
				
				<li>
					<a href="{{ route('admin.dashboard') }}">
						<i class="fe-airplay"></i> 
						<span> Dashboard </span>
					</a>
				</li>
				<li>
					<a href="{{ route('admin.user') }}">
						<i class="fe-users"></i> 
						<span> Users </span>
					</a>
				</li>
				<li>
					<a href="{{ route('admin.folder') }}">
						<i class="fe-folder"></i> 
						<span> Folders </span>
					</a>
				</li>
				<li>
					<a href="{{ route('admin.file') }}">
						<i class="fe-folder"></i> 
						<span> Upload Files </span>
					</a>
				</li>
				<li>
					<a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
						<i class="fe-log-out"></i> 
						<span> Logout </span>
					</a>
					<!-- Logout Form -->
					<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
						@csrf
					</form>
				</li>
				
				 
			</ul>
			
		</div>
		<!-- End Sidebar -->
		
		<div class="clearfix"></div>
		
	</div>
	<!-- Sidebar -left -->
	
</div>
<!-- Left Sidebar End -->