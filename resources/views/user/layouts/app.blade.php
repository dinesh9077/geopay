<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>@yield('title')</title>
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}"> 
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> 
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" /> 
	</head>
	
	<body>
		<div class="wrapper">
			@include('user.layouts.partial.sidebar')
			<div class="main">
				@include('user.layouts.partial.topbar')
				<main class="content px-3 py-2">
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
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		 
	</body>
	
</html>
