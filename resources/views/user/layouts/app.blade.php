<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>@yield('title')</title>
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/css/slick/slick.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />	
	</head>
	
	<body>
		<div class="wrapper">
			@include('user.layouts.partial.sidebar')
			<div class="main">
				@include('user.layouts.partial.topbar')
				<main class="content p-4">
					@yield('content')
				</main>
				
				<footer class="footer">
					<div class="container-fluid">
						<p class="mb-0 p-2 text-center content-3">Copyright © 2024 <a class="text-secondary" href="https://www.softieons.com" target="_blank">Softieons Technologies</a>. All rights reserved.</p>
					</div>
				</footer>
			</div>
		</div>
		<script src="https://kit.fontawesome.com/ae360af17e.js" ></script>
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		<script type="text/javascript" src="{{ asset('assets/js/slick/slick.min.js')}}"></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/select2.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />	
		<script>
			// for sidebar collapse
			const sidebarToggle = document.querySelector("#sidebar-toggle");
			const sidebarClose = document.querySelector("#sidebar-close");
			const sidebar = document.querySelector("#sidebar");
			sidebarToggle.addEventListener("click", function () {
				sidebar.classList.toggle("collapsed");
			});
			sidebarClose.addEventListener("click", function () {
				sidebar.classList.remove("collapsed");
			});

			// To convert img to svg
			function img2svg() {
				jQuery('.in-svg').each(function (i, e) {
					var $img = jQuery(e);
					var imgID = $img.attr('id');
					var imgClass = $img.attr('class');
					var imgURL = $img.attr('src');
					jQuery.get(imgURL, function (data) {
					var $svg = jQuery(data).find('svg');
					if (typeof imgID !== 'undefined') {
						$svg = $svg.attr('id', imgID);
					}
					if (typeof imgClass !== 'undefined') {
						$svg = $svg.attr('class', ' ' + imgClass + ' replaced-svg');
					}
					$svg = $svg.removeAttr('xmlns:a');
					$img.replaceWith($svg);
					}, 'xml');
				});
			}
			img2svg();

			// Quick Transfer Slick Slider
			$(".qt-slick-slider").slick({
				slidesToShow: 3,
				infinite: true,
				slidesToScroll: 1,
				autoplay: false,
				responsive: [
					{ breakpoint: 1024, settings: { slidesToShow: 3, slidesToScroll: 1, infinite: true, } },
					{ breakpoint: 768, settings: { slidesToShow: 2, slidesToScroll: 1, infinite: true, } },
					{ breakpoint: 320, settings: { slidesToShow: 1, slidesToScroll: 1, autoplay: true, } }
				]
			});

			// Profile Image Upload
			const imageUpload = document.getElementById('imageUpload');
			const profileImage = document.getElementById('profileImage');
			const editIcon = document.getElementById('editIcon');
			editIcon.addEventListener('click', function () {
				imageUpload.click();
			});
			imageUpload.addEventListener('change', function (event) {
				const file = event.target.files[0];
				if (file) {
					const reader = new FileReader();
					reader.onload = function (e) {
						profileImage.src = e.target.result;
					};
					reader.readAsDataURL(file);
				}
			});
			document.getElementById('updateBtn').addEventListener('click', function () {
				alert('Profile updated successfully!');
			});

		</script>
	</body>
	
</html>
