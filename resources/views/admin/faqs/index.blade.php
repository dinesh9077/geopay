@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Faqs')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Faqs</h4>
	</div> 
	<div class="d-flex align-items-center flex-wrap text-nowrap"> 
		<button type="button" onclick="addFaqs(this, event)" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
			<i class="btn-icon-prepend" data-feather="plus"></i>
			Add Faqs
		</button>
	</div>
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="faqDatatable" class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Description</th>
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
	var dataTable = $('#faqDatatable').DataTable({ 
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
			"url": "{{ route('admin.faqs.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
			}
		},
		"columns": [
			{ "data": "id" },    
			{ "data": "title" },   
			{ "data": "description" },  
			{ "data": "status" },  
			{ "data": "created_at" }, 
			{ "data": "action" }
		]
	}); 
	
	function addFaqs(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{route('admin.faqs.create')}}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#addFaqModal').modal('show');  
			});
		} 
	}
	
	function editFaq(obj, event)
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
				$('#editFaqModal').modal('show');  
			});
		} 
	}
	 
</script>
@endpush				