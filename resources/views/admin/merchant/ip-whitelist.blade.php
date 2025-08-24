@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Merchant Ip Whitelist')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Merchant Ip Whitelist</h4>
	</div> 
	@if (config("permission.merchant_ip_whitelist.add"))
		<div class="d-flex align-items-center flex-wrap text-nowrap"> 
			<button type="button" id="addModalOpen" data-bs-toggle="modal" data-bs-target="#ipModal" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
				<i class="btn-icon-prepend" data-feather="plus"></i>Add IP
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
								<th>IP Address</th> 
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

<!-- Add Fund Modal -->
<!-- Modal -->
<div class="modal fade" id="ipModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form id="ipForm" class="modal-content">
			@csrf
			<input type="hidden" name="id" id="ip_id">
			<div class="modal-header">
				<h5 class="modal-title">Add IP</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label">IP Address <span class="text-danger">*</span></label>
					<input type="text" name="ip_address" id="ip_address" class="form-control" placeholder="e.g. 203.0.113.7"> 
				</div>
				<div class="mb-3">
					<label class="form-label">Status</label>
					<select name="status" id="status" class="form-control" required>
						<option value="1" selected>Active</option>
						<option value="0">In-Active</option> 
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save</button>
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>

@endsection

@push('js')
<script> 
	$('.select2').select2({
		width: "100%",
		dropdownParent: $('#ipModal') // 👈 set the modal or popup container
	}); 
	
	const userId = @json(request('id') ?? '');
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
			"url": "{{ route('admin.merchant.ipwhitelist.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{ csrf_token() }}"; 
				d.user_id   = userId; 
			}
		},
		columns: [
		{ data: 'id',         name: 'id',         orderable: false, searchable: false },
		{ data: 'ip_address', name: 'ip_address' },  
		{ data: 'status',  name: 'status',  orderable: false, searchable: false },
		{ data: 'created_at', name: 'created_at' },
		{ data: 'action',     name: 'action',     orderable: false, searchable: false }
        ]
	}); 
	
	const ipForm   = document.getElementById('ipForm');
	
	$('#addModalOpen').click(function(){
		ipForm.reset();
        $('#ip_id').val(''); 
	}) 
	 
	$(document).ready(function () {
		$('#ipForm').on('submit', function(e){
			e.preventDefault();
			
			var formData = new FormData(this);
			formData.append('user_id', userId);
			
			$.ajax({
				url: "{{ route('admin.merchant.ipwhitelist.store') }}",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				success: function(res)
				{
					if(res.status === "success")
					{
						$('#ipModal').modal('hide');
						dataTable.draw();  
						toastrMsg(res.status, res.message);
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) {
							var inputField = $('#' + key);
							var errorSpan = $('<span>')
							.addClass('error_msg text-danger') 
							.attr('id', key + 'Error')
							.text(value[0]);  
							inputField.parent().append(errorSpan);
						});
					}
					else{ 
						toastrMsg(res.status, res.message);
					}
				},
				error: function(err){
					toastr.error("Something went wrong!");
				}
			});
		});
	}); 
	
	function editIpWhitelist(obj, event)
	{
		const id = $(obj).data('id');
		const ipAddress = $(obj).data('ip');
		const status = $(obj).data('status');
		 
		$('#ipForm').find('#ip_address').val(ipAddress);
		$('#ipForm').find('#status').val(status);
		$('#ipForm').find('#ip_id').val(id);
		  
		$('#ipModal').modal('show');
	}
</script>
@endpush				