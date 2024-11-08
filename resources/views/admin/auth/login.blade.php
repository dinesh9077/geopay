<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ env('APP_NAME') }} - Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ url('front-end/img/favicon.png') }}">

        <!-- App css -->
        <link href="{{ url('admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('admin/css/app.min.css') }}" rel="stylesheet" type="text/css" /> 
		<link href="{{ url('admin/libs/jquery-toast/jquery.toast.min.css')}}" rel="stylesheet" type="text/css" />
		<style>
			.bg-gradient {
				background-image: url("{{ url('/front-end/img/banner/banner_shape.jpg') }}");
			}
		</style> 
    </head> 
    <body class="authentication-bg bg-gradient"> 
            <div class="account-pages mt-5 pt-5 mb-5">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-5">
                            <div class="card bg-pattern">
    
                                <div class="card-body p-4"> 
                                    <div class="text-center w-75 m-auto">
                                        <a href="#">
                                            <span><img src="{{ url('front-end/img/logo/logo.png') }}" alt="" height="40"></span>
                                        </a>
                                        <h5 class="text-uppercase text-center font-bold mt-4"></h5> 
                                    </div>
    
                                    <form action="{{ route('admin.login.submit') }}" method="POST">
										@csrf
                                        <div class="form-group mb-3">
                                            <label for="emailaddress">Email address</label>
                                            <input class="form-control" type="email" id="email" name="email" placeholder="Enter your email" required>
											@if(session('email'))
												<span class="text-danger">{{ session('email') }}</span>
											@endif
                                        </div>
    
                                        <div class="form-group mb-3"> 
                                            <label for="password">Password</label>
                                            <input class="form-control" type="password" id="password" name="password" placeholder="Enter your password" required>
											@if(session('password'))
												<span class="text-danger">{{ session('password') }}</span>
											@endif
                                        </div>
      
                                        <div class="form-group mb-0 text-center">
                                            <button class="btn btn-gradient btn-block" type="submit"> Log In </button>
                                        </div> 
                                    </form> 
                                </div> <!-- end card-body -->
                            </div>
                            <!-- end card -->
    
                       
    
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end page -->


        <!-- Vendor js -->
        <script src="{{ url('admin/js/vendor.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ url('admin/js/app.min.js') }}"></script> 
		<script src="{{ url('admin/libs/jquery-toast/jquery.toast.min.js') }}"></script>
		
		<script>
			$(document).ready(function() {
				@if(session('success')) 
					$.toast({
						heading: "Well done!",
						text: "{{ session('success') }}",
						position: "top-right",
						loaderBg: "#5ba035",
						icon: "success",
						hideAfter: 3000,
						stack: 1
					});
				@endif
				
				@if(session('error'))
					$.toast({
						heading: "Oh snap!",
						text: "{{ session('error') }}",
						position: "top-right",
						loaderBg: "#bf441d",
						icon: "error",
						hideAfter: 3000,
						stack: 1
					});
				@endif 
			});
			</script> 
    </body>
</html>