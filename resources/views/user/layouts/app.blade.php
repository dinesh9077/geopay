<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>@yield('title')</title>
		<link rel="icon" type="image/svg+xml" href="{{ url('storage/setting', config('setting.fevicon_icon')) }}">
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/css/slick/slick.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/waitMe.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />	
		<link rel="stylesheet" href="{{ asset('admin/vendors/flatpickr/flatpickr.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}">
		<style>
			.select2-container--default .select2-selection--single{
				background: #acbacf45 !important;
			}
			.select2-container .select2-selection--single {
				height: 42px !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__rendered {
				line-height: 42px !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__arrow {
				height: 42px;
			}
			.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
				background-color: var(--primary-color);
				color: white;
			}
			.select2-container--default .select2-selection--single { 
				border-radius: .5rem;
			}
		</style>
		 @livewireStyles
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
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script> 
		<script type="text/javascript" src="{{ asset('assets/js/slick/slick.min.js')}}"></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/select2.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>  
		<script src="{{ asset('assets/js/waitMe.js')}}" ></script>  
		
		<script src="{{ asset('assets/datatable/jquery.dataTables.min.js')}}" ></script> 
		
		<script src="{{ asset('admin/vendors/pickr/pickr.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/moment/moment.min.js') }}"></script>
		<script src="{{ asset('admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
		
		<x-scripts :cryptoKey="$cryptoKey" />	
		{{-- @livewireScripts --}}
		<script src="{{ asset('vendor/livewire/livewire.js') }}?v={{ \Carbon\Carbon::now()->timestamp }}"
        data-csrf="{{ csrf_token() }}"
        data-update-uri="{{ url('livewire/update') }}"
        data-navigate-once="true"></script>
		<script>
			 
			// for sidebar collapse
			var sidebarToggle = document.querySelector("#sidebar-toggle");
			var sidebarClose = document.querySelector("#sidebar-close");
			var sidebar = document.querySelector("#sidebar");
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
 
			document.addEventListener('DOMContentLoaded', function () {
				//Livewire.dispatch('refreshRecentTransactions');  
			});
			
			// Initial Slick Initialization
			$(".qt-slick-slider").slick({
				slidesToShow: 3,
				infinite: true,
				slidesToScroll: 1,
				autoplay: false,
				responsive: [
					{ breakpoint: 1024, settings: { slidesToShow: 3, slidesToScroll: 1, infinite: true } },
					{ breakpoint: 768, settings: { slidesToShow: 2, slidesToScroll: 1, infinite: true } },
					{ breakpoint: 320, settings: { slidesToShow: 1, slidesToScroll: 1, autoplay: true } }
				]
			}); 
			
			function run_waitMe(el, num, effect)
			{
				text = 'Please Wait...';
				fontSize = '';
				switch (num) {
					case 1:
					maxSize = '';
					textPos = 'vertical';
					break;
					case 2:
					text = '';
					maxSize = 30;
					textPos = 'vertical';
					break;
					case 3:
					maxSize = 30;
					textPos = 'horizontal';
					fontSize = '18px';
					break;
				}
				el.waitMe({
					effect: effect,
					text: text,
					bg: 'rgba(255,255,255,0.7)',
					color: '#000',
					maxSize: maxSize,
					waitTime: -1,
					source: "{{asset('assets/loader.gif')}}",
					textPos: textPos,
					fontSize: fontSize,
					onClose: function(el) {}
				});
			} 
		</script>
		@stack('js') 
	</body>
	
</html>
