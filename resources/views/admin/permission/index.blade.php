@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Permission')

@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Permission</h4>
	</div> 
	<button type="button" data-bs-toggle="modal" data-bs-target="#addPermissionModal" 
        class="btn btn-primary btn-icon-text mb-2 mb-md-0">
		<i class="btn-icon-prepend" data-feather="plus"></i>
		Add Permission
	</button> 
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="bannerDatatable" class="table">
						<thead>
							<tr>
								<th>Id</th>
								<th>Permission Name</th> 
								<th>Heading</th> 
								<th>Show Permission</th>   
								<th>Action</th>   
							</tr>
						</thead> 
						<tbody class="row_position">
							@foreach($permissions as $key => $permission)
								<tr id="{{ $permission->id }}">
									<td>{{ ($key + 1) }}</td>
									<td>{{ $permission->name }}</td> 
									<td>
										{{$permission->heading}}	
									</td>
									<td>
										{{ $permission->status == 1 ? 'Yes' : 'No' }} 
									</td> 
									<td>
										<button class="btn btn-primary btn-sm" data-permission='@json($permission)' onclick="editPermission(this, event)">Edit</button>
										<a href="{{ url('admin/permission/delete', $permission->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?')">Delete</a> 
									</td> 
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div> 
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Add Permission</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form action="{{ route('admin.permission.store') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Permission Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name">
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Permission Heading <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="heading" name="heading">
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control" id="status" name="status">
							<option value="1"> Active </option>
							<option value="0"> In-Active </option>
						</select>
					</div>  
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Edit Permission</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editPermissionForm" method="post" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Permission Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name">
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Permission Heading <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="heading" name="heading">
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control" id="status" name="status">
							<option value="1"> Active </option>
							<option value="0"> In-Active </option>
						</select>
					</div>  
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection 
@push('js')
 <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>  
	const baseUrl = "{{ url('/') }}";
	function editPermission(obj, event) {
		event.preventDefault();

		try {
			// Parse the permission data safely
			const permissionData = $(obj).attr('data-permission');
			if (!permissionData) throw new Error("Missing data-permission attribute");

			const permission = JSON.parse(permissionData);
			if (!permission?.id) throw new Error("Invalid permission data");

			// Cache jQuery selectors for better performance
			const $form = $('#editPermissionForm');
			
			// Update form action dynamically
			$form.attr('action', `${baseUrl}/admin/permission/update/${permission.id}`);

			// Populate form fields
			$form.find('#name').val(permission?.name || '');
			$form.find('#heading').val(permission?.heading || '');
			$form.find('#status').val(permission?.status || '');

			// Show the modal
			$('#editPermissionModal').modal('show');
		} catch (error) {
			console.error("Error in editPermission:", error.message);
		}
	}
	
	$( ".row_position" ).sortable({
		delay: 150,
		stop: function() {
			const selectedData = [];
			
			// Cache jQuery selector for better performance
			$('.row_position > tr').each(function() {
				selectedData.push($(this).attr("id"));
			});

			updateOrder(selectedData);
		}
	});

	function updateOrder(data) {
		$.ajax({
			url: `{{ url('admin/permission/position') }}`,  // Clean URL formatting with template literals
			type: 'POST',
			data: {
				position: data,
				_token: "{{ csrf_token() }}"
			},
			success: function() {
				// Optionally handle success if needed, e.g., show a success message
				// console.log('Order updated successfully');
			},
			error: function(xhr, status, error) {
				// Log the error to the console or show an alert
				console.error("Error updating position:", error);
			}
		});
	}


</script>
@endpush				