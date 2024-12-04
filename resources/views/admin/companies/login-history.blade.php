@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Login History')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Login History</h4>
	</div>  
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="loginHistoryDatatable" class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Type</th>
								<th>Ip Address</th>
								<th>Device</th>
								<th>Browser</th> 
								<th>Source</th>  
								<th>Created At</th> 
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
	var dataTable = $('#loginHistoryDatatable').DataTable({ 
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
			"url": "{{ route('admin.companies.login-history-ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
				d.userId  = "{{ $companyId }}"; 
			}
		},
		"columns": [
			{ "data": "id" },    
			{ "data": "type" },   
			{ "data": "ip_address" },  
			{ "data": "device" },  
			{ "data": "browser" },  
			{ "data": "source" },   
			{ "data": "created_at" }
		]
	}); 
	   
</script>
@endpush				