<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Wowdash - Tailwind CSS Admin Dashboard HTML Template</title>
		<link rel="icon" type="image/png" href="{{ asset('admin/images/favicon.png') }}" sizes="16x16">
		<!-- google fonts -->
		<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
		<!-- remix icon font css  -->
		<link rel="stylesheet" href="{{ asset('admin/css/remixicon.css') }}">
		<!-- Apex Chart css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/apexcharts.css') }}">
		<!-- Data Table css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/dataTables.min.css') }}">
		<!-- Text Editor css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/editor-katex.min.css') }}">
		<link rel="stylesheet" href="{{ asset('admin/css/lib/editor.atom-one-dark.min.css') }}">
		<link rel="stylesheet" href="{{ asset('admin/css/lib/editor.quill.snow.css') }}">
		<!-- Date picker css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/flatpickr.min.css') }}">
		<!-- Calendar css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/full-calendar.css') }}">
		<!-- Vector Map css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/jquery-jvectormap-2.0.5.css') }}">
		<!-- Popup css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/magnific-popup.css') }}">
		<!-- Slick Slider css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/slick.css') }}">
		<!-- prism css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/prism.css') }}">
		<!-- file upload css -->
		<link rel="stylesheet" href="{{ asset('admin/css/lib/file-upload.css') }}">
		
		<link rel="stylesheet" href="{{ asset('admin/css/lib/audioplayer.css') }}">
		<!-- main css -->
		<link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
	</head>
	<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">
		
		@include('admin.layouts.partial.sidebar')
		<main class="dashboard-main"> 
			@include('admin.layouts.partial.topbar') 
			
			@yield('content')
			<footer class="d-footer">
				<div class="flex items-center justify-between gap-3">
					<p class="mb-0">© 2024 WowDash. All Rights Reserved.</p>
					<p class="mb-0">Made by <span class="text-primary-600">wowtheme7</span></p>
				</div>
			</footer>
		</main>
		
		<!-- jQuery library js -->
		<script src="{{ asset('admin/js/lib/jquery-3.7.1.min.js') }}"></script>
		<!-- Apex Chart js -->
		<script src="{{ asset('admin/js/lib/apexcharts.min.js') }}"></script>
		<!-- Data Table js -->
		<script src="{{ asset('admin/js/lib/simple-datatables.min.js') }}"></script>
		<!-- Iconify Font js -->
		<script src="{{ asset('admin/js/lib/iconify-icon.min.js') }}"></script>
		<!-- jQuery UI js -->
		<script src="{{ asset('admin/js/lib/jquery-ui.min.js') }}"></script>
		<!-- Vector Map js -->
		<script src="{{ asset('admin/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
		<script src="{{ asset('admin/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
		<!-- Popup js -->
		<script src="{{ asset('admin/js/lib/magnifc-popup.min.js') }}"></script>
		<!-- Slick Slider js -->
		<script src="{{ asset('admin/js/lib/slick.min.js') }}"></script>
		<!-- prism js -->
		<script src="{{ asset('admin/js/lib/prism.js') }}"></script>
		<!-- file upload js -->
		<script src="{{ asset('admin/js/lib/file-upload.js') }}"></script>
		<!-- audioplayer -->
		<script src="{{ asset('admin/js/lib/audioplayer.js') }}"></script>
		
		<script src="{{ asset('admin/js/flowbite.min.js') }}"></script>
		<!-- main js -->
		<script src="{{ asset('admin/js/app.js') }}"></script>
		
		<script src="{{ asset('admin/js/homeOneChart.js') }}"></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" /> 
		
		@stack('js')
	</body>
</html>