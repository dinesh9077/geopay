<!DOCTYPE html>

<html lang="en">
	<head> 
		<title>@yield('title')</title>
		<link rel="shortcut icon" href="{{ url('storage/setting', config('setting.fevicon_icon')) }}" />
		<!-- color-modes:js -->
		<script src="{{ asset('admin/js/color-modes.js') }}"></script>
		<!-- endinject -->
		
		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
		<!-- End fonts -->
		
		<!-- core:css -->
		<link rel="stylesheet" href="{{ asset('admin/vendors/core/core.css') }}">
		<!-- endinject -->
		 
		<!-- inject:css -->
		<link rel="stylesheet" href="{{ asset('admin/fonts/feather-font/css/iconfont.css') }}">
		<!-- endinject -->
		
		<!-- Plugin css for this page -->
		<link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs5/dataTables.bootstrap5.css') }}">
		<link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs5/buttons.dataTables.css') }}">
		<!-- End plugin css for this page -->
		
		<link rel="stylesheet" href="{{ asset('admin/vendors/select2/select2.min.css') }}">
		
		<link rel="stylesheet" href="{{ asset('admin/vendors/sweetalert2/sweetalert2.min.css') }}">
		
		<!-- Layout styles -->  
		<link rel="stylesheet" href="{{ asset('admin/css/demo1/style.css') }}">
		<!-- End layout styles -->
		<link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="{{ asset('admin/vendors/flatpickr/flatpickr.min.css') }}">
		@livewireStyles
		<script> 
            let modalOpen = false;
            function closemodal()
            {
                setTimeout(function()
                {
                    modalOpen = false;
				},1000)
			}
		</script>
		<style>
			.custom-entry {
				display: flex;
				align-items: center;
				gap: 8px;
			}
			.left-head-deta {
				display: flex;
				align-items: center;
				gap: 8px;
			}
			.table td, .table th { 
			  font-size: 0.875rem;
			}
		</style>
	</head>
	<body>
		<div class="main-wrapper">
			
			<!-- partial:partials/_sidebar.html -->
			@include('admin.layouts.partial.sidebar')
			<!-- partial -->
			
			<div class="page-wrapper">
				
				<!-- partial:partials/_navbar.html -->
				@include('admin.layouts.partial.topbar')
				<!-- partial -->
				
				<div class="page-content">
					@yield('content')
				</div>
				
				<!-- partial:partials/_footer.html -->
				<footer class="footer d-flex flex-row align-items-center justify-content-between px-4 py-3 border-top small">
					<p class="text-secondary mb-1 mb-md-0">Copyright © {{ date('Y') }} <a href="https://www.softieons.com" target="_blank">Softieons Technologies</a>.</p> 
				</footer>
				<!-- partial --> 
			</div>
		</div> 
		
		<div id="modal-view-render"></div>
		<x-delete-modal/>
		
		<script src="{{ asset('admin/vendors/jquery/jquery.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net/dataTables.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/dataTables.buttons.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/buttons.dataTables.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/jszip.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/pdfmake.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/vfs_fonts.js') }}"></script>
		<script src="{{ asset('admin/vendors/datatables.net-bs5/buttons.html5.min.js') }}"></script>
		
		<!-- core:js -->
		<script src="{{ asset('admin/vendors/core/core.js') }}"></script>
		<!-- endinject -->
		 
		<!-- inject:js -->
		<script src="{{ asset('admin/vendors/feather-icons/feather.min.js') }}"></script>
		<script src="{{ asset('admin/js/app.js') }}"></script>
		<!-- endinject -->
		
		<script src="{{ asset('admin/vendors/tinymce/tinymce.min.js') }}"></script>
			
		<script src="{{ asset('admin/vendors/pickr/pickr.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/moment/moment.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
		
		<script src="{{ asset('admin/vendors/select2/select2.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
		<!-- End custom js for this page -->
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" /> 
		{{-- @livewireScripts --}}
		<script src="{{ asset('vendor/livewire/livewire.js') }}?v={{ \Carbon\Carbon::now()->timestamp }}"
        data-csrf="{{ csrf_token() }}"
        data-update-uri="{{ url('livewire/update') }}"
        data-navigate-once="true"></script>
		
		@stack('js')
	</body>
</html>    