@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Transaction Report')
@section('header_title', 'Transaction Report')
@section('content') 
    <div class="container-fluid p-0">
        <!-- Filter Row -->
		@if (config('permission.live_exchange_rate.add'))
        <div class="row g-2"> 
            <div class=" col-md-3 col-lg-1">
                <select class="form-control default-input content-3 select2" name="channel" id="channel">  
					<option value="lightnet">Lightnet</option> 
					<option value="onafric">Onafric</option> 
					<option value="onafric mobile collection">onafric mobile collection</option> 
                </select>
            </div> 
			@if (config('permission.live_exchange_rate.edit')) 
				<div class="filter-buttons col-md-4 col-lg-2">
					<button onclick="getLiveRates(this, event)" class="btn btn-primary">Get Live Rate</button> 
				</div>
			@endif
        </div> 
        <hr>
		@endif
        <div class="data-table-container">
            <div class="left-head-deta mb-4 d-flex align-items-center justify-content-between">
				{{-- <div class="custom-entry">
                    <p>Show</p>
                    <select class="form-select form-select-sm" id="page_length">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                        <option value="2000">2000</option>
                    </select>
                    <p>entries</p>
                </div> --}}
				<div class="d-flex align-items-center gap-2">
					<a href="javascript:;" class="btn btn-primary btn-sm" id="excelExport"> XLXS</a>
					<a href="javascript:;" class="btn btn-warning btn-sm" id="pdfExport"> PDF</a> 
					@if (config('permission.live_exchange_rate.edit')) 
						<button id="updateRows" style="display:none;" class="btn btn-success btn-sm">Update Margin</button>
					@endif
				</div>
				
				<input class="form-control w-fit" type="search" id="search_table" placeholder="Search">
            </div>

            <table id="fxrate-table" class="table table-borderless table-hover border-0 mb-4">
                <thead>
                    <tr>
						<th><input type="checkbox" id="selectAll"></th>
                        <th>Channel</th>
                        <th>Country Name</th>
                        <th>Currency</th>
                        <th>Exchange Rate Aggregator</th> 
                        <th>Exchange Rate Against 1 USD</th>
                        <th>Margin Percentage(Flat / %)</th> 
                        <th>Api Rate Against 1 USD</th> 
                        <th>Api Percentage(Flat / %)</th> 
                        <th>Date</th> 
                        <th>Action</th> 
                    </tr>
                </thead>
            </table> 
        </div>
    </div>
	
	<div class="modal fade" id="bulkRateModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="varyingModalLabel">Edit Bulk Rate</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
				</div>
				<form id="bulkLiveRateForm" action="{{ route('admin.live.exchange-rate.bulk-update') }}" method="post" enctype="multipart/form-data">
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
						<div class="mb-3">
							<label for="recipient-name" class="form-label">Api Markdown Type <span class="text-danger">*</span></label>
							<select class="form-control" id="api_markdown_type" name="api_markdown_type"> 
								<option value="">Select Api Markdown Type</option>
								<option value="flat" > Flat/Fixed </option>
								<option value="percentage"> Percentage </option>
							</select>
						</div>  
						<div class="mb-3">
							<label for="recipient-name" class="form-label">Api Markdown Charge <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="api_markdown_charge" name="api_markdown_charge" autocomplete="off" placeholder="Api Markdown Charge Flat/%" value="0" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
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
   
		// Initialize DataTable
		var dataTable = $('#fxrate-table').DataTable({ 
			dom: 'Bfrtip',
			buttons: [{
					extend: 'excelHtml5',
					className: 'd-none',
					text: 'excel',
					exportOptions: {
						modifier: {
							page: 'current'
						},
						columns: [1, 2, 3, 4, 5, 6, 7]
					}
				},
				{
					extend: 'pdfHtml5',
					className: 'd-none',
					text: 'pdf',
					exportOptions: {
						modifier: {
							page: 'current'
						},
						columns: [1, 2, 3, 4, 5, 6, 7]
					}
				}
			], 
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
				"url": "{{ route('admin.live.exchange-rate.ajax') }}",
				"dataType": "json",
				"type": "POST",
				"data": function(d) {
					d._token = "{{ csrf_token() }}";
					d.channel = $('#channel').val(); 
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
				{ "data": "api_markdown_rate" }, 
				{ "data": "api_markdown_charge" },
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
		  
		$("#excelExport").on("click", function() {
			$(".buttons-excel").trigger("click");
		});

		$("#pdfExport").on("click", function() {
			$(".buttons-pdf").trigger("click");
		});
		
		$('#page_length, #channel').change(function() {
			dataTable.page.len($(this).val()).draw();
		})
		
		$('#search_table').keyup(function() {
			dataTable.draw();
		}) 
		 
		// Handle "Select All" checkbox change
		$('#selectAll').on('change', function () {
			$('.rowCheckbox').prop('checked', this.checked);
			toggleButtonVisibility()
		});

		// Handle individual row checkboxes
		$('#fxrate-table tbody').on('change', '.rowCheckbox', function () {
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
	
		function getLiveRates(obj, event)
		{
			event.preventDefault();  

			// Disable the submit button to prevent multiple submissions
			var $form = $(obj);
			$form.prop('disabled', true); 
			run_waitMe($('body'), 1, 'facebook');
		 
			const channel = $('#channel').val();
			const formData = {
				channel: channel 
			};
 
			// Encrypt data before sending
			const encrypted_data = encryptData(JSON.stringify(formData));
			 
			$.ajax({
				async: true,
				type: "post",
				url: "{{ route('admin.live.exchange-rate.fetch') }}",
				data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{   
					$form.prop('disabled',false);	 
					$('body').waitMe('hide'); 
					if(res.status == 'success')
					{ 
						dataTable.draw();
						toastrMsg(res.status, res.message);
					}
					else
					{ 
						toastrMsg(res.status, res.message);
					}
				} 
			}); 
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
			$('#bulkLiveRateForm').find('input').val(0);
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
						dataTable.draw();
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
    </script>
@endpush
