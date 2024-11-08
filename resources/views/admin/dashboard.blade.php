@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' - Dashboard')

@section('content')
<div class="content">
	
	<!-- Start Content-->
	<div class="container-fluid">
		
		<!-- start page title -->
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<div class="page-title-right">
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a href="javascript: void(0);"> {{ env('APP_NAME') }} </a></li>
							<li class="breadcrumb-item active">Dashboard</li>
						</ol>
					</div>
					<h4 class="page-title">Dashboard</h4>
				</div>
			</div>
		</div>     
		<!-- end page title --> 
		
		<div class="row">
			<div class="col-md-6 col-xl-3">
				<div class="card-box tilebox-one">
					<i class="fe-users float-right"></i>
					<h5 class="text-muted text-uppercase mb-3 mt-0">Total Users</h5>
					<h3 class="mb-3" data-plugin="counterup">{{ $totalUsers }}</h3>  
				</div>
			</div>
			
			<div class="col-md-6 col-xl-3">
				<div class="card-box tilebox-one">
					<i class="fe-folder float-right"></i>
					<h5 class="text-muted text-uppercase mb-3 mt-0">Total Folders</h5>
					<h3 class="mb-3"><span data-plugin="counterup">{{ $totalFolders }}</span></h3> 
				</div>
			</div>
			
			<div class="col-md-6 col-xl-3">
				<div class="card-box tilebox-one">
					<i class="fe-file float-right"></i>
					<h5 class="text-muted text-uppercase mb-3 mt-0">Total Files</h5>
					<h3 class="mb-3"><span data-plugin="counterup">{{ $totalFiles }}</span></h3> 
				</div>
			</div>
			 
		</div> 
	</div> <!-- end container-fluid -->
	
</div> <!-- end content -->
@endsection