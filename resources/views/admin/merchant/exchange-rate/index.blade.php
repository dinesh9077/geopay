@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Merchant Exchange Rate')
@section('header_title', 'Merchant Exchange Rate')
@section('content') 
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Merchant Exchange Rate</h4>
	</div>  
</div>
    
<div class="row">
	<div class="example">
		<ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="manualrate-line-tab" onclick="liveDatatable()" data-bs-toggle="tab" href="#line-liverate" role="tab" aria-controls="line-liverate" aria-selected="true">Live Rate</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="manualrate-line-tab" data-bs-toggle="tab" onclick="manualDatatable()" href="#line-manualrate" role="tab" aria-controls="line-manualrate" aria-selected="false">Manual Rate</a>
			</li>  
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			<div class="tab-pane fade show active" id="line-liverate" role="tabpanel" aria-labelledby="liverate-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					 
					<div class="card">
						<div class="card-body"> 
							<div class="table-responsive">
								<div class="row g-2"> 
									<div class=" col-md-3 col-lg-2">
										<select class="form-control default-input content-3 select2" name="channel" id="channel">  
											<option value="lightnet">Lightnet (Pay Service)</option> 
											<option value="onafric">Onafric (Pay Service)</option>  
										</select>
									</div>   
								</div> 
								<hr>
								<div class="left-head-deta mb-4 d-flex align-items-center justify-content-between" id="addServiceAction"> 
									<div class="d-flex align-items-center gap-2"> 
										<button id="updateRows" style="display:none;" class="btn btn-success btn-sm">Update Margin</button>
									</div> 
									<input class="form-control w-fit" type="search" id="search_table" placeholder="Search">
								</div>
								<table id="livesDatatable" class="table">
									<thead>
										<tr>
											<th><input type="checkbox" id="selectAll"></th>
											<th>Channel</th>
											<th>Country Name</th>
											<th>Currency</th>
											<th>Exchange Rate Aggregator</th> 
											<th>Exchange Rate Against 1 USD</th>
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
			<div class="tab-pane fade" id="line-manualrate" role="tabpanel" aria-labelledby="manualrate-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<div class="table-responsive">
								<div class="left-head-deta mb-4 d-flex align-items-center justify-content-between" id="manualDatatableSearch"> 
									<div class="d-flex align-items-center gap-2"> 
										<button id="updateManualRows" style="display:none;" class="btn btn-success btn-sm">Update Margin</button>
									</div> 
									<input class="form-control w-fit" type="search" id="search_table" placeholder="Search">
								</div>
								<table id="manualDatatable" class="table">
									<thead>
										<tr>
											<th><input type="checkbox" id="selectManualAll"></th>
											<th>Created By</th>  
											<th>Service Name</th>
											<th>Country Name</th>
											<th>Currency</th>
											<th>Exchange Rate Aggregator</th> 
											<th>Exchange Rate Against 1 USD</th>
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

<div class="modal fade" id="bulkRateModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Bulk Rate</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="bulkLiveRateForm" action="{{ route('admin.merchant.exchange-rate.bulk-update') }}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="user_id" id="user_id" value="{{ $userId }}"> 
				<div class="modal-body"> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Type <span class="text-danger">*</span></label>
						<select class="form-control" id="markdown_type" name="markdown_type"> 
							<option value="">Select Markdown Type</option>
							<option value="flat" > Flat/Fixed </option>
							<option value="percentage"> Percentage </option>
						</select>
					</div>  
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Charge <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="markdown_charge" name="markdown_charge" autocomplete="off" placeholder="Markdown Charge Flat/%" value="0" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div> 
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="bulkManualRateModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Bulk Rate</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="bulkManualRateForm" action="{{ route('admin.merchant.exchange-rate.bulk-manual-update') }}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="user_id" id="user_id" value="{{ $userId }}"> 
				<div class="modal-body"> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Type <span class="text-danger">*</span></label>
						<select class="form-control" id="markdown_type" name="markdown_type"> 
							<option value="">Select Markdown Type</option>
							<option value="flat" > Flat/Fixed </option>
							<option value="percentage"> Percentage </option>
						</select>
					</div>  
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Charge <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="markdown_charge" name="markdown_charge" autocomplete="off" placeholder="Markdown Charge Flat/%" value="0" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
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
    <script>
        $('.select2').select2({
            width: "100%"
        });
		
		liveDatatable()
		function liveDatatable()
		{
			$('#updateRows').hide(); 
			if ($.fn.DataTable.isDataTable('#livesDatatable')) {
				$('#livesDatatable').DataTable().destroy();
			} 
			
			window.dataTable = $('#livesDatatable').DataTable({  
				processing: true,
				"language": {
					'loadingRecords': '&nbsp;',
					'processing': 'Loading...'
				},
				serverSide: true,
				bLengthChange: false,
				searching: false,
				bFilter: true,
				bPaginate: false,
				responsive: false,
				bInfo: true,
				iDisplayLength: 100000,
				order: [
					[0, 'desc']
				],
				bAutoWidth: false,
				"ajax": {
					"url": "{{ route('admin.merchant.exchange-rate.ajax') }}",
					"dataType": "json",
					"type": "POST",
					"data": function(d) {
						d._token = "{{ csrf_token() }}";
						d.channel = $('#channel').val(); 
						d.user_id = @json($userId); 
						d.search = $('#search_table').val(); 
					}
				},
				"columns": [
					{ "data": "id" },
					{ "data": "channel" },
					{ "data": "country_name" },
					{ "data": "currency" },
					{ "data": "aggregator_rate" },
					{ "data": "markdown_rate" },
					{ "data": "markdown_charge" }, 
					{ "data": "updated_at" },
					{ "data": "action" }
				], 
				drawCallback: function() { 
					$('[data-toggle="tooltip"]').tooltip();
				},
				columnDefs: [
					{ targets: 0, orderable: false } // Disable ordering for the first column
				]
			});
		}
		
		$('#channel').change(function() {
			window.dataTable.page.len($(this).val()).draw();
		})
		
		$('#search_table').keyup(function() {
			window.dataTable.draw();
		}) 
		 
		// Handle "Select All" checkbox change
		$('#selectAll').on('change', function () {
			$('.rowCheckbox').prop('checked', this.checked);
			toggleButtonVisibility()
		});

		// Handle individual row checkboxes
		$(document).on('change', '.rowCheckbox', function () {
			const allCheckboxes = $('.rowCheckbox');
			const checkedCheckboxes = $('.rowCheckbox:checked');

			// Update "Select All" checkbox state
			$('#selectAll').prop('checked', allCheckboxes.length === checkedCheckboxes.length);
			toggleButtonVisibility()
		});
		
		// Function to toggle button visibility
		function toggleButtonVisibility() {
			const anyChecked = $('.rowCheckbox:checked').length > 0;
			if (anyChecked) {
				$('#updateRows').show(); // Show button
			} else {
				$('#updateRows').hide(); // Hide button
			}
		}
	  
		function editLiveRate(obj, event)
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
					$('#editLiveRateModal').modal('show');  
				});
			} 
		}
		
		$('#updateRows').on('click', function () {
			$('#bulkLiveRateForm').find('select').val('');
			$('#bulkLiveRateForm').find('#markdown_charge').val(0); 
			$('#bulkLiveRateForm').find('button').prop('disabled',false);	 
			$('#bulkRateModal').modal('show'); 
		});
		
		$('#bulkLiveRateForm').submit(function(event) 
		{
			event.preventDefault();   
			
			$(this).find('button').prop('disabled',true);   
			
			// Initialize the form data object
			let formDataInput = { ids: [] };
			
			$(this).find("input, select").each(function() {
				var inputName = $(this).attr('name'); 
				formDataInput[inputName] = $(this).val();
			}); 
		
			// Collect all selected row IDs
			$('.rowCheckbox:checked').each(function () {
				let id = $(this).data('id');  
				formDataInput.ids.push(id);
			});

			// Check if any rows are selected
			if (formDataInput.ids.length === 0) {
				alert('No rows selected.');
				return;
			}

			// Encrypt the data (ensure encryptData is defined)
			const encrypted_data = encryptData(JSON.stringify(formDataInput));

			// Prepare form data for the AJAX request
			let formData = new FormData(); 
			formData.append('encrypted_data', encrypted_data);  
			formData.append('_token', "{{ csrf_token() }}");
		   
			$.ajax({ 
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: formData,
				processData: false, 
				contentType: false,  
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{ 
					$('#bulkLiveRateForm').find('button').prop('disabled',false);	 
					$('.error_msg').remove(); 
					
					if(res.status === "success")
					{   
						toastrMsg(res.status,res.message);  
						window.dataTable.draw();
						$('#bulkRateModal').modal('hide');
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
					else
					{  
						toastrMsg(res.status, res.message); 
					}
				} 
			}); 
		});	
		 
		//Manual Rate 
		function manualDatatable()
		{
			$('#updateManualRows').hide(); 
			if ($.fn.DataTable.isDataTable('#manualDatatable')) {
				$('#manualDatatable').DataTable().destroy();
			}

			window.manualDataTable = $('#manualDatatable').DataTable({  
				processing: true,
				"language": {
					'loadingRecords': '&nbsp;',
					'processing': 'Loading...'
				},
				serverSide: true,
				bLengthChange: false,
				searching: false,
				bFilter: true,
				bPaginate: false,
				responsive: false,
				bInfo: true,
				iDisplayLength: 100000,
				order: [
					[0, 'desc']
				],
				bAutoWidth: false,
				"ajax": {
					"url": "{{ route('admin.merchant.exchange-rate.manual-ajax') }}",
					"dataType": "json",
					"type": "POST",
					"data": function(d) {
						d._token = "{{ csrf_token() }}";
						d.channel = $('#channel').val(); 
						d.user_id = @json($userId); 
						d.search = $('#manualDatatableSearch #search_table').val() || $('#manualDatatableSearch #search_table').val() || ''; 
					}
				},
				"columns": [
					{ data: "id" },
					{ data: "created_by" },
					{ data: "service_name" },
					{ data: "country_name" },
					{ data: "currency" },
					{ data: "aggregator_rate" },
					{ data: "exchange_rate" },
					{ data: "markdown_charge" }, 
					{ data: "updated_at" },
					{ data: "action" }
				], 
				drawCallback: function() { 
					$('[data-toggle="tooltip"]').tooltip();
				},
				columnDefs: [
					{ targets: 0, orderable: false } // Disable ordering for the first column
				]
			});
		}
		
		$('#manualDatatableSearch #search_table').keyup(function() {
			window.manualDataTable.draw();
		}) 
		
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
		
		// Handle "Select Manual All" checkbox change
		$('#selectManualAll').on('change', function () {
			$('.rowManualCheckbox').prop('checked', this.checked);
			toggleManualButtonVisibility()
		});

		// Handle individual row checkboxes
		$(document).on('change', '.rowManualCheckbox', function () {
			const allCheckboxes = $('.rowManualCheckbox');
			const checkedCheckboxes = $('.rowManualCheckbox:checked');

			// Update "Select All" checkbox state
			$('#selectManualAll').prop('checked', allCheckboxes.length === checkedCheckboxes.length);

			toggleManualButtonVisibility();
		});
 
		// Function to toggle button visibility
		function toggleManualButtonVisibility() {
			const anyChecked = $('.rowManualCheckbox:checked').length > 0;
			if (anyChecked) {
				$('#updateManualRows').show(); // Show button
			} else {
				$('#updateManualRows').hide(); // Hide button
			}
		}
		
		$('#updateManualRows').on('click', function () {
			$('#bulkManualRateForm').find('select').val('');
			$('#bulkManualRateForm').find('#markdown_charge').val(0); 
			$('#bulkManualRateForm').find('button').prop('disabled',false);	 
			$('#bulkManualRateModal').modal('show'); 
		});
		
		$('#bulkManualRateForm').submit(function(event) 
		{
			event.preventDefault();   
			
			$(this).find('button').prop('disabled',true);   
			
			// Initialize the form data object
			let formDataInput = { ids: [] };
			
			$(this).find("input, select").each(function() {
				var inputName = $(this).attr('name'); 
				formDataInput[inputName] = $(this).val();
			}); 
		
			// Collect all selected row IDs
			$('.rowManualCheckbox:checked').each(function () {
				let id = $(this).data('id');  
				formDataInput.ids.push(id);
			});

			// Check if any rows are selected
			if (formDataInput.ids.length === 0) {
				alert('No rows selected.');
				return;
			}

			// Encrypt the data (ensure encryptData is defined)
			const encrypted_data = encryptData(JSON.stringify(formDataInput));

			// Prepare form data for the AJAX request
			let formData = new FormData(); 
			formData.append('encrypted_data', encrypted_data);  
			formData.append('_token', "{{ csrf_token() }}");
		   
			$.ajax({ 
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: formData,
				processData: false, 
				contentType: false,  
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{ 
					$('#bulkManualRateForm').find('button').prop('disabled',false);	 
					$('.error_msg').remove(); 
					
					if(res.status === "success")
					{   
						toastrMsg(res.status,res.message);  
						window.manualDataTable.draw();
						$('#bulkManualRateModal').modal('hide');
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
					else
					{  
						toastrMsg(res.status, res.message); 
					}
				} 
			}); 
		});
    </script>
@endpush
