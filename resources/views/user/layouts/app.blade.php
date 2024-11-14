<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>@yield('title')</title>
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
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
						<div class="row text-muted">
							<div class="col-6 text-start">
								<p class="mb-0">
									<a href="#" class="text-muted">
										<strong>CodzSwod</strong>
									</a>
								</p>
							</div>
							<div class="col-6 text-end">
								<ul class="list-inline">
									<li class="list-inline-item">
										<a href="#" class="text-muted">Contact</a>
									</li>
									<li class="list-inline-item">
										<a href="#" class="text-muted">About Us</a>
									</li>
									<li class="list-inline-item">
										<a href="#" class="text-muted">Terms</a>
									</li>
									<li class="list-inline-item">
										<a href="#" class="text-muted">Booking</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</footer>
			</div>
		</div>
		<script src="https://kit.fontawesome.com/ae360af17e.js" ></script>
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/select2.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />	
		<script>
			// for sidebar collapse
			const sidebarToggle = document.querySelector("#sidebar-toggle");
			sidebarToggle.addEventListener("click",function(){
				document.querySelector("#sidebar").classList.toggle("collapsed");
			});

			// To convert img to svg
			function img2svg() {
				jQuery('.in-svg').each(function (i, e) {
					var $img = jQuery(e);
					var imgID = $img.attr('id');
					var imgClass = $img.attr('class');
					var imgURL = $img.attr('src');
					jQuery.get(imgURL, function (data) {
					// Get the SVG tag, ignore the rest
					var $svg = jQuery(data).find('svg');
					// Add replaced image's ID to the new SVG
					if (typeof imgID !== 'undefined') {
						$svg = $svg.attr('id', imgID);
					}
					// Add replaced image's classes to the new SVG
					if (typeof imgClass !== 'undefined') {
						$svg = $svg.attr('class', ' ' + imgClass + ' replaced-svg');
					}
					// Remove any invalid XML tags as per http://validator.w3.org
					$svg = $svg.removeAttr('xmlns:a');
					// Replace image with new SVG
					$img.replaceWith($svg);
					}, 'xml');
				});
			}
			img2svg();
		</script>
	</body>
	
</html>
