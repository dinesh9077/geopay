@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Staffs')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Staffs</h4>
	</div> 
	@if (config("permission.staff.add"))
	<div class="d-flex align-items-center flex-wrap text-nowrap"> 
		<button type="button" onclick="addStaff(this, event)" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
			<i class="btn-icon-prepend" data-feather="plus"></i>
			Add Staff
		</button>
	</div>
	@endif
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="userDatatable" class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Profile</th> 
								<th>Name</th> 
								<th>Email</th> 
								<th>Mobile</th> 
								<th>Date Of Birth</th> 
								<th>Role</th> 
								<th>Status</th> 
								<th>Created At</th>
								<th>Action</th>
							</tr>
						</thead> 
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('js')
<script> 
	var dataTable = $('#userDatatable').DataTable({ 
		processing:true,
		"language": {
			'loadingRecords': '&nbsp;',
			'processing': 'Loading...'
		},
		serverSide:true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		responsive:false,
		bInfo: true,
		iDisplayLength: 10,
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
			"url": "{{ route('admin.staff.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
			}
		},
		"columns": [
		{ "data": "id" },    
		{ "data": "profile" },   
		{ "data": "name" },   
		{ "data": "email" },   
		{ "data": "mobile" },   
		{ "data": "dob" },   
		{ "data": "role" },   
		{ "data": "status" },  
		{ "data": "created_at" }, 
		{ "data": "action" }
		]
	}); 
	
	function addStaff(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{route('admin.staff.create')}}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#addStaffModal').modal('show');  
			});
		} 
	}
	
	function editStaff(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(obj, function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#editStaffModal').modal('show');  
			});
		} 
	} 
	
	function editPermission(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(obj, function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#editPermissionModal').modal('show');  
			});
		} 
	} 
</script>
@endpush				