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
			
			@php
				$manageStaffs = [
					'role' => ['route' => 'admin.roles', 'label' => 'Roles'],
					'staff' => ['route' => 'admin.staff', 'label' => 'Staff'], 
				];
			@endphp

			@if(collect($manageStaffs)->keys()->some(fn($key) => config("permission.$key.view"))) 
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="collapse" href="#manageStaff" role="button" aria-expanded="false" aria-controls="manageStaff">
					<i class="link-icon" data-feather="users"></i>
					<span class="link-title">Manage Staff</span>
					<i class="link-arrow" data-feather="chevron-down"></i>
				</a>
				<div class="collapse" data-bs-parent="#sidebarNav" id="manageStaff">
					<ul class="nav sub-menu">
						@foreach ($manageStaffs as $key => $manageStaff)
							@if (config("permission.$key.view"))
							<li class="nav-item">
								<a href="{{ route($manageStaff['route']) }}" class="nav-link">{{ $manageStaff['label'] }}</a>
							</li>
							@endif
						@endforeach 
					</ul>
				</div>
			</li> 
			@endif
			
			
			@php
				$manageCompanies = [
					'active_company' => ['route' => 'admin.company.active', 'label' => 'Active Company'],
					'pending_company' => ['route' => 'admin.company.pending', 'label' => 'Pending Company'], 
					'block_company' => ['route' => 'admin.company.block', 'label' => 'Block Company'], 
				];
			@endphp

			@if(collect($manageCompanies)->keys()->some(fn($key) => config("permission.$key.view"))) 
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="collapse" href="#manageCompanies" role="button" aria-expanded="false" aria-controls="manageCompanies">
					<i class="link-icon" data-feather="users"></i>
					<span class="link-title">Manage Companies</span>
					<i class="link-arrow" data-feather="chevron-down"></i>
				</a>
				<div class="collapse" data-bs-parent="#sidebarNav" id="manageCompanies">
					<ul class="nav sub-menu">
						@foreach ($manageCompanies as $key => $manageCompany)
							@if (config("permission.$key.view"))
							<li class="nav-item">
								<a href="{{ route($manageCompany['route']) }}" class="nav-link">{{ $manageCompany['label'] }}</a>
							</li>
							@endif
						@endforeach 
					</ul>
				</div>
			</li> 
			@endif
			
			@php
				$settings = [
					'general_setting' => ['route' => 'admin.general-setting', 'label' => 'General'],
					'banner' => ['route' => 'admin.banner', 'label' => 'Banner'],
					'faqs' => ['route' => 'admin.faqs', 'label' => "FAQ's"],
					'third_party_api' => ['route' => 'admin.third-party-key', 'label' => 'Third Party API']
				];
			@endphp

			@if(collect($settings)->keys()->some(fn($key) => config("permission.$key.view")))
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="collapse" href="#settings" role="button" aria-expanded="false" aria-controls="settings">
					<i class="link-icon" data-feather="anchor"></i>
					<span class="link-title">Setting</span>
					<i class="link-arrow" data-feather="chevron-down"></i>
				</a>
				<div class="collapse" data-bs-parent="#sidebarNav" id="settings">
					<ul class="nav sub-menu">
						@foreach ($settings as $key => $setting)
							@if (config("permission.$key.view"))
							<li class="nav-item">
								<a href="{{ route($setting['route']) }}" class="nav-link">{{ $setting['label'] }}</a>
							</li>
							@endif
						@endforeach
					</ul>
				</div>
			</li>
			@endif

			
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