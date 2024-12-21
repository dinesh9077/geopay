@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Manual Exchange Rate')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Manual Exchange Rate</h4>
	</div> 
	@if(config('permission.manual_exchange_rate.add'))
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
								<div class="left-head-deta mb-4 d-flex align-items-center justify-content-between" id="addServiceAction"> 
									<div class="d-flex align-items-center gap-2">
										<a href="javascript:;" class="btn btn-primary btn-sm" id="excelExport"> XLXS</a>
										<a href="javascript:;" class="btn btn-warning btn-sm" id="pdfExport"> PDF</a> 
										<button id="updateRows" style="display:none;" class="btn btn-success btn-sm">Update Margin</button>
									</div> 
									<input class="form-control w-fit" type="search" id="search_table" placeholder="Search">
								</div>
								<table id="addServiceDatatable" class="table">
									<thead>
										<tr>
											<th>#</th> 
											<th>Created By</th>  
											<th>Country Name</th>
											<th>Currency</th>
											<th>Exchange Rate Against 1 USD</th>
											<th>Exchange Rate Aggregator</th> 
											<th>Margin Percentage(Flat / %)</th> 
											<th>Date</th> 
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
								<div class="left-head-deta mb-4 d-flex align-items-center justify-content-between" id="payServiceAction"> 
									<div class="d-flex align-items-center gap-2">
										<a href="javascript:;" class="btn btn-primary btn-sm" id="excelExport"> XLXS</a>
										<a href="javascript:;" class="btn btn-warning btn-sm" id="pdfExport"> PDF</a> 
										<button id="updateRows" style="display:none;" class="btn btn-success btn-sm">Update Margin</button>
									</div> 
									<input class="form-control w-fit" type="search" id="search_table" placeholder="Search">
								</div>
								<table id="payServiceDatatable" class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>Created By</th>  
											<th>Country Name</th>
											<th>Currency</th>
											<th>Exchange Rate Against 1 USD</th>
											<th>Exchange Rate Aggregator</th> 
											<th>Margin Percentage(Flat / %)</th> 
											<th>Date</th> 
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
			dom: 'Bfrtip',
			buttons: [
				{
					extend: 'excelHtml5',
					className: 'd-none',
					text: 'Excel',
					exportOptions: {
						modifier: { page: 'current' },
						columns: [1, 2, 3, 4, 5, 6, 7]
					}
				},
				{
					extend: 'pdfHtml5',
					className: 'd-none',
					text: 'PDF',
					exportOptions: {
						modifier: { page: 'current' },
						columns: [1, 2, 3, 4, 5, 6, 7]
					}
				}
			],
			processing: true,
			language: {
				loadingRecords: '&nbsp;',
				processing: 'Loading...'
			},
			serverSide: true,
			lengthChange: false,
			searching: false,
			responsive: true,
			info: true,
			paginate: false,
			pageLength: 100000,
			order: [[0, 'desc']],
			autoWidth: false,
			deferRender: true, // Improve performance for large datasets
			ajax: {
				url: "{{ route('admin.manual.exchange-rate.ajax') }}",
				type: "POST",
				dataType: "json",
				data: function (d) {
					d._token = "{{ csrf_token() }}";
					d.search = $('#payServiceAction #search_table').val() || $('#addServiceAction #search_table').val() || '';
					d.type = type;
				}
			},
			columns: [
				{ data: "id" },
				{ data: "created_by" },
				{ data: "country_name" },
				{ data: "currency" },
				{ data: "exchange_rate" },
				{ data: "aggregator_rate" },
				{ data: "markdown_charge" },
				{ data: "updated_at" },
				{ data: "action" }
			]
		});
	}

	// Initialize DataTables for the two tables
	var addServiceTable = initializeDataTable('#addServiceDatatable', 1);
	var payServiceTable = initializeDataTable('#payServiceDatatable', 2);

	// Trigger DataTable redraw on search input
	$('#payServiceAction #search_table').keyup(function () {
		payServiceTable.draw();
	});

	$('#addServiceAction #search_table').keyup(function () {
		addServiceTable.draw();
	});

	// Excel and PDF export functionality
	$('#payServiceAction').on('click', '#excelExport', function () {
		$('#payServiceDatatable').DataTable().button('.buttons-excel').trigger();
	});

	$('#payServiceAction').on('click', '#pdfExport', function () {
		$('#payServiceDatatable').DataTable().button('.buttons-pdf').trigger();
	});

	$('#addServiceAction').on('click', '#excelExport', function () {
		$('#addServiceDatatable').DataTable().button('.buttons-excel').trigger();
	});

	$('#addServiceAction').on('click', '#pdfExport', function () {
		$('#addServiceDatatable').DataTable().button('.buttons-pdf').trigger();
	});


	function addExchangeRate(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{route('admin.manual.exchange-rate.import')}}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#importRateModal').modal('show');  
			});
		} 
	}
	
	function editManualRate(obj, event)
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
				$('#editManualRateModal').modal('show');  
			});
		} 
	}
</script>
@endpush				