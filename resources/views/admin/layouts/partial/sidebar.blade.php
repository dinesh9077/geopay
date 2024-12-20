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

            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
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

            @if (collect($manageStaffs)->keys()->some(fn($key) => config("permission.$key.view")))
                <li
                    class="nav-item {{ collect($manageStaffs)->contains(fn($item) => request()->routeIs($item['route'])) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#manageStaff" role="button"
                        aria-expanded="{{ collect($manageStaffs)->contains(fn($item) => request()->routeIs($item['route'])) ? 'true' : 'false' }}"
                        aria-controls="manageStaff">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Manage Staff</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ collect($manageStaffs)->contains(fn($item) => request()->routeIs($item['route'])) ? 'show' : '' }}"
                        id="manageStaff">
                        <ul class="nav sub-menu">
                            @foreach ($manageStaffs as $key => $item)
                                @if (config("permission.$key.view"))
                                    <li class="nav-item">
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            @php
                $manageCompanies = [
                    'active_company' => ['route' => 'admin.companies.active', 'label' => 'Active Company'],
                    'pending_company' => ['route' => 'admin.companies.pending', 'label' => 'Pending Company'],
                    'block_company' => ['route' => 'admin.companies.block', 'label' => 'Blocked Company'],
                ];

                // Define additional routes that should open the dropdown without a menu item
                $extraRoutes = ['admin.companies.edit', 'admin.companies.view-kyc', 'admin.companies.login-history'];

                // Combine routes for dropdown open logic
                $allRoutes = array_merge(array_column($manageCompanies, 'route'), $extraRoutes);
            @endphp

            @if (collect($manageCompanies)->keys()->some(fn($key) => config("permission.$key.view")))
                <li
                    class="nav-item {{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#manageCompanies" role="button"
                        aria-expanded="{{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'true' : 'false' }}"
                        aria-controls="manageCompanies">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Manage Companies</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'show' : '' }}"
                        id="manageCompanies">
                        <ul class="nav sub-menu">
                            @foreach ($manageCompanies as $key => $item)
                                @if (config("permission.$key.view"))
                                    <li class="nav-item">
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            @php
                $manageExchanges = [
                    'manual_exchange_rate' => ['route' => 'admin.manual.exchange-rate', 'label' => 'Manual Exchange Rate'],
                ];
            @endphp

            @if (collect($manageExchanges)->keys()->some(fn($key) => config("permission.$key.view")))
                <li class="nav-item">
                    <a href="{{ route('admin.manual.exchange-rate') }}"
                        class="nav-link {{ request()->routeIs('admin.manual.exchange-rate') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Manual Exchange Rate</span>
                    </a>
                </li>
            @endif

            @php
                $liveExchanges = [
                    'live_exchange_rate' => ['route' => 'admin.live.exchange-rate', 'label' => 'Live Exchange Rate'],
                ];
            @endphp

            @if (collect($liveExchanges)->keys()->some(fn($key) => config("permission.$key.view")))
                <li class="nav-item">
                    <a href="{{ route('admin.live.exchange-rate') }}"
                        class="nav-link {{ request()->routeIs('admin.live.exchange-rate') ? 'active' : '' }}">
                        <i class="link-icon" data-feather="box"></i>
                        <span class="link-title">Live Exchange Rate</span>
                    </a>
                </li>
            @endif

            @php
                $manageUsers = [
                    'active_user' => ['route' => 'admin.user.active', 'label' => 'Active Company'],
                    'pending_user' => ['route' => 'admin.user.pending', 'label' => 'Pending Company'],
                    'block_user' => ['route' => 'admin.user.block', 'label' => 'Blocked Company'],
                ];

                // Define additional routes that should open the dropdown without a menu item
                $extraRoutes = ['admin.user.edit', 'admin.user.login-history'];

                // Combine routes for dropdown open logic
                $allRoutes = array_merge(array_column($manageUsers, 'route'), $extraRoutes);
            @endphp

            @if (collect($manageUsers)->keys()->some(fn($key) => config("permission.$key.view")))
                <li
                    class="nav-item {{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#manageUsers" role="button"
                        aria-expanded="{{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'true' : 'false' }}"
                        aria-controls="manageUsers">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Manage Users</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ collect($allRoutes)->contains(fn($route) => request()->routeIs($route)) ? 'show' : '' }}"
                        id="manageUsers">
                        <ul class="nav sub-menu">
                            @foreach ($manageUsers as $key => $item)
                                @if (config("permission.$key.view"))
                                    <li class="nav-item">
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            @php
                $Reports = [
                    'transaction_history' => ['route' => 'admin.report.transaction-history', 'label' => 'Transaction History'],
                ];
            @endphp

            @if (collect($manageExchanges)->keys()->some(fn($key) => config("permission.$key.view")))

                <li
                    class="nav-item {{ collect($Reports)->contains(fn($item) => request()->routeIs($item['route'])) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#reports" role="button"
                        aria-expanded="{{ collect($Reports)->contains(fn($item) => request()->routeIs($item['route'])) ? 'true' : 'false' }}"
                        aria-controls="reports">
                        <i class="link-icon" data-feather="file"></i>
                        <span class="link-title">Reports</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ collect($Reports)->contains(fn($item) => request()->routeIs($item['route'])) ? 'show' : '' }}"
                        id="reports">
                        <ul class="nav sub-menu">
                            @foreach ($Reports as $key => $item)
                                @if (config("permission.$key.view"))
                                    <li class="nav-item">
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
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
                    'third_party_api' => ['route' => 'admin.third-party-key', 'label' => 'Third Party API'],
                ];
            @endphp

            @if (collect($settings)->keys()->some(fn($key) => config("permission.$key.view")))
                <li
                    class="nav-item {{ collect($settings)->contains(fn($item) => request()->routeIs($item['route'])) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#settings" role="button"
                        aria-expanded="{{ collect($settings)->contains(fn($item) => request()->routeIs($item['route'])) ? 'true' : 'false' }}"
                        aria-controls="settings">
                        <i class="link-icon" data-feather="anchor"></i>
                        <span class="link-title">Settings</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ collect($settings)->contains(fn($item) => request()->routeIs($item['route'])) ? 'show' : '' }}"
                        id="settings">
                        <ul class="nav sub-menu">
                            @foreach ($settings as $key => $item)
                                @if (config("permission.$key.view"))
                                    <li class="nav-item">
                                        <a href="{{ route($item['route']) }}"
                                            class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('admin.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="nav-link">
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
