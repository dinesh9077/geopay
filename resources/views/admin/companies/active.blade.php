@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Active Company')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Active Company</h4>
	</div>  
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="companyDatatable" class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Mobile</th> 
								<th>Country</th> 
								<th>Wallet Balance</th> 
								<th>Is Kyc Verified</th> 
								<th>Is Email Verified</th> 
								<th>Is Mobile Verified</th> 
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
	var dataTable = $('#companyDatatable').DataTable({ 
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
			"url": "{{ route('admin.companies.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
				d.page_status  = 'active';
				d.is_kyc_verify   = 1; 
				d.status   = 1; 
			}
		},
		"columns": [
			{ "data": "id" },    
			{ "data": "name" },   
			{ "data": "email" },  
			{ "data": "mobile" },  
			{ "data": "country" },  
			{ "data": "wallet_balance" },  
			{ "data": "is_kyc_verify" },  
			{ "data": "is_email_verify" },  
			{ "data": "is_mobile_verify" },  
			{ "data": "status" },  
			{ "data": "created_at" }, 
			{ "data": "action" }
		]
	}); 
	  
	function editCompany(obj, event)
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
				$('#editCompanyModal').modal('show');  
			});
		} 
	} 
	
	$(document).on('change', '.companyActiveInactive', function () {
		const userId = $(this).data('id');
		const status = $(this).is(':checked') ? 1 : 0; 
		
		var formDataInput = {}; 
		formDataInput['id'] = userId; 
		formDataInput['status'] = status; 

		// Ensure encryptData function works properly
		const encrypted_data = encryptData(JSON.stringify(formDataInput));

		var formData = new FormData(); 
		formData.append('encrypted_data', encrypted_data);  
		formData.append('_token', "{{ csrf_token() }}");

		$.ajax({
			type: 'POST', // Correct method type for POST request
			url: '{{ route("admin.companies.update-status") }}', // Use the correct route URL
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
			dataType: 'json', // Ensure the correct data type is specified
			success: function (res) {
				$('.error_msg').remove();
				if(res.status === "success") {
					dataTable.draw(); // Refresh your data table
					toastrMsg(res.status, res.message);   
				} else {
					toastrMsg(res.status, res.message); 
				}
			},
			error: function(xhr, status, error) { 
				toastrMsg('error', 'Something went wrong!');
			}
		});
	}); 
</script>
@endpush				