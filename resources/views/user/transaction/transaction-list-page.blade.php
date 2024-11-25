@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transaction List')
@section('header_title', 'Transaction List')
@section('content')
 
<div class="container-fluid p-0">
	<div class="grid-margin stretch-card">
	  <div class="card">
		<div class="card-body">
		  <div class="table-responsive">
			<table class="table table-hover">
			  <thead>
				<tr>
				  <th>User</th>
				  <th>Product</th>
				  <th>Sale</th>
				  <th>Status</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td>Hitesh Chauhan</td>
				  <td>Engine</td>
				  <td class="text-danger">18.76% <i class="fa fa-arrow-down"></i></td>
				  <td><label class="badge text-bg-primary content-4 fw-normal">Pending</label></td>
				</tr>
				<tr>
				  <td>Samso Palto</td>
				  <td>Brakes</td>
				  <td class="text-danger">11.06% <i class="fa fa-arrow-down"></i></td>
				  <td><label class="badge text-bg-warning content-4 fw-normal">In progress</label></td>
				</tr>
				<tr>
				  <td>Tiplis mang</td>
				  <td>Window</td>
				  <td class="text-danger">35.00% <i class="fa fa-arrow-down"></i></td>
				  <td><label class="badge text-bg-success content-4 fw-normal">Fixed</label></td>
				</tr>
				<tr>
				  <td>Pter parker</td>
				  <td>Head light</td>
				  <td class="text-success">22.00% <i class="fa fa-arrow-up"></i></td>
				  <td><label class="badge text-bg-success content-4 fw-normal">Completed</label></td>
				</tr>
				<tr>
				  <td>Ankit Dave</td>
				  <td>Back light</td>
				  <td class="text-success">28.05% <i class="fa fa-arrow-up"></i></td>
				  <td><label class="badge text-bg-warning content-4 fw-normal">In progress</label></td>
				</tr>
			  </tbody>
			</table>
		  </div>
		</div>
	  </div>
	</div>
</div>

@endsection