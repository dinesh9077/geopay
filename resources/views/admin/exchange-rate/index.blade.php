@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Exchange Rate')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Exchange Rate</h4>
	</div> 
	@if(config('permission.exchange_rate.add'))
	<div class="d-flex align-items-center flex-wrap text-nowrap"> 
		<button type="button" onclick="addExchangeRate(this, event)" class="btn btn-primary btn-icon-text mb-2 mb-md-0" style="margin-right: 5px;">
			<i class="btn-icon-prepend" data-feather="upload"></i>
			Import Exchange Rate
		</button>  
		<a href="{{ url('admin/exchange-rate-sample.xlsx') }}" class="btn btn-info btn-icon-text mb-2 ml-2 mb-md-0" download> 
			<i class="btn-icon-prepend" data-feather="arrow-down-circle"></i>
			Download Sample
		</a>
	</div> 
	@endif
</div>

<div class="row">
	<div class="example">
		<ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="metamap-line-tab" data-bs-toggle="tab" href="#line-metamap" role="tab" aria-controls="line-metamap" aria-selected="true">Add Service Currency Rate</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="smtpmail-line-tab" data-bs-toggle="tab" href="#line-smtpmail" role="tab" aria-controls="line-smtpmail" aria-selected="false">Pay Service Currency Rate</a>
			</li>  
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			<div class="tab-pane fade show active" id="line-metamap" role="tabpanel" aria-labelledby="metamap-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<div class="table-responsive">
								<table id="addServiceDatatable" class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>Currency</th>
											<th>Dollar Rate</th> 
											<th>Created By</th>
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
			<div class="tab-pane fade" id="line-smtpmail" role="tabpanel" aria-labelledby="smtpmail-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<div class="table-responsive">
								<table id="payServiceDatatable" class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>Currency</th>
											<th>Dollar Rate</th> 
											<th>Created By</th>
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
		</div>
	</div>
</div>

@endsection

@push('js')
<script>
	
	// Function to initialize DataTable
	function initializeDataTable(tableId, type) {
		return $(tableId).DataTable({
			processing: true,
			language: {
				loadingRecords: '&nbsp;',
				processing: 'Loading...'
			},
			serverSide: true,
			bLengthChange: true,
			searching: true,
			bFilter: true,
		responsive: true,  // Make table responsive
		bInfo: true,
		iDisplayLength: 10,
		order: [[0, 'desc']],
		bAutoWidth: false,
		deferRender: true,  // Improves performance for large datasets
		ajax: {
			url: "{{ route('admin.exchange-rate.ajax') }}",
			dataType: "json",
			type: "POST",
			data: function (d) {
				d._token = "{{ csrf_token() }}";
				d.type = type;
			}
		},
		columns: [
			{ data: "id" },
			{ data: "currency" },
			{ data: "exchange_rate" },
			{ data: "created_by" },
			{ data: "created_at" },
			{ data: "action" }
		]
		});
	}
	
	// Initialize DataTables with shared AJAX URL
	var addServiceTable = initializeDataTable('#addServiceDatatable', 1);
	var payServiceTable = initializeDataTable('#payServiceDatatable', 2);
	  
	function addExchangeRate(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{route('admin.exchange-rate.import')}}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#importRateModal').modal('show');  
			});
		} 
	}
</script>
@endpush				