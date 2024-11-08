<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title')</title>
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
		
		<link href="{{ url('admin/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ url('admin/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
		
		<!-- third party css -->
        <link href="{{ url('admin/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('admin/libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('admin/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
		
		<link href="{{ url('admin/css/front-fancybox.css') }}" rel="stylesheet" type="text/css" />
		<style>
			.select2-container .select2-selection--single {
				height: 36px !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__rendered {
				line-height: 36px !important;
			}
			.select2-container--default .select2-selection--single .select2-selection__arrow {
				height: 35px !important;
			}
		</style>
    </head>

    <body>

        <!-- Begin page -->
        <div id="wrapper">
 
			@include('admin.layouts.partial.topbar')
             
			@include('admin.layouts.partial.sidebar') 
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                
				@yield('content')
                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
							{{ date('Y') }} &copy; Developed by <a href="https://softieons.com">Softieons</a>
                            </div>
            
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->
  
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- Vendor js -->
        <script src="{{ url('admin/js/vendor.min.js') }}"></script>
 
        <!-- App js -->
        <script src="{{ url('admin/js/app.min.js') }}"></script>
		<script src="{{ url('admin/libs/jquery-toast/jquery.toast.min.js') }}"></script>
        
		 <!-- Required datatable js -->
        <script src="{{ url('admin/libs/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ url('admin/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <!-- Buttons examples -->
        <script src="{{ url('admin/libs/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ url('admin/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ url('admin/libs/jszip/jszip.min.js') }}"></script>
        <script src="{{ url('admin/libs/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ url('admin/libs/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ url('admin/libs/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ url('admin/libs/datatables/buttons.print.min.js') }}"></script>
		
		<script src="{{ url('admin/libs/select2/select2.min.js') }}"></script>
		<script src="{{ url('admin/libs/bootstrap-select/bootstrap-select.min.js') }}"></script> 
			
        <!-- Responsive examples -->
        <script src="{{ url('admin/libs/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ url('admin/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
		
		<script src="{{ url('admin/js/front-fancybox.js') }}"></script> 
		<script>
			$(document).ready(function() {
				@if(session('success')) 
					$.toast({
						heading: "success",
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
						heading: "error",
						text: "{{ session('error') }}",
						position: "top-right",
						loaderBg: "#bf441d",
						icon: "error",
						hideAfter: 3000,
						stack: 1
					});
				@endif 
			});
			
			function toastrMsg(status, msg)
			{	
				$.toast({
					heading: status,
					text: msg,
					position: "top-right",
					loaderBg: "#5ba035",
					icon: status,
					hideAfter: 3000,
					stack: 1
				});
			}
		</script> 
		@stack('js')
    </body>
</html>