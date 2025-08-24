@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Merchants')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Merchants</h4>
	</div> 
	@if (config("permission.merchant.add"))
		<div class="d-flex align-items-center flex-wrap text-nowrap"> 
			<button type="button" onclick="addMerchant(this, event)" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
				<i class="btn-icon-prepend" data-feather="plus"></i>Add Marchant
			</button>
		</div>
	@endif
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive" style="min-height:650px">
					<table id="userDatatable" class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Company Name</th> 
								<th>Name</th> 
								<th>Email</th> 
								<th>Mobile</th> 
								<th>Address</th> 
								<th>Date Of Birth</th>  
								<th>Balance</th>  
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
			"url": "{{ route('admin.merchant.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
			}
		},
		"columns": [
			{ "data": "id" },    
			{ "data": "company_name" },   
			{ "data": "name" },   
			{ "data": "email" },   
			{ "data": "mobile_number" },   
			{ "data": "address" },   
			{ "data": "date_of_birth" },   
			{ "data": "balance" },   
			{ "data": "status" },  
			{ "data": "created_at" }, 
			{ "data": "action" }
		]
	}); 
	
	function addMerchant(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{route('admin.merchant.create')}}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#addMerchantModal').modal('show');  
			});
		} 
	}
	
	function editMerchant(obj, event)
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
				$('#editMerchantModal').modal('show');  
			});
		} 
	} 
	
	function apiActivation(obj, event)
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
				$('#apiActivationModal').modal('show');  
			});
		} 
	} 
	
	function merchantCommission(obj, event)
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
				$('#flatRateModal').modal('show');  
			});
		} 
	} 
	
	function transactionLimit(obj, event)
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
				$('#serviceLimitModal').modal('show');  
			});
		} 
	} 
	 
</script>
@endpush				