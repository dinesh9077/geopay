@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - '. $title)
@section('header_title', $title)
@section('content')
    <style>
        .swal2-image.custom-image-class {
            position: absolute;
            top: -10px;
            right: 10px;
        }
    </style>
    <div class="container-fluid p-0">
        <!-- Filter Row -->
        <div class="row g-2">

            <div class=" col-md-3 col-lg-1">
                <select id="userDropdown" name="user_id" class="form-control content-3 select2">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->first_name }}  {{ $user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
 
            <div class=" col-md-3 col-lg-2">
                <input type="text" class="form-control default-input " id="start_date" name="start_date"
                    placeholder="Start date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class=" col-md-3 col-lg-2">
                <input type="text" class="form-control default-input" id="end_date" name="end_date"
                    placeholder="End date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
            </div>
            <div class=" col-md-3 col-lg-1">
                <select class="form-control default-input content-3 select2" name="txn_status" id="txn_status">
                    <option value="">ALL</option>
                    @foreach($txnStatuses as $txnStatus)
						<option value="{{ $txnStatus }}">{{ $txnStatus }}</option>
					@endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <input type="text" class="form-control default-input" name="search" id="search"
                    placeholder="Search Item">
            </div>
            <div class="filter-buttons col-md-4 col-lg-2">
                <button id="applyFilters" class="btn btn-primary">Filter</button>
                <button id="resetFilters" class="btn btn-secondary">Reset</button>
            </div>
        </div>
        <hr>
        <div class="data-table-container">
            <div class="left-head-deta mb-4">
                <div class="custom-entry">
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
                </div>
                <a href="javascript:;" class="btn btn-primary btn-sm" id="excelExport"> XLXS</a>
                <a href="javascript:;" class="btn btn-warning btn-sm" id="pdfExport"> PDF</a>
            </div>

            <table id="transaction-table" class="table table-borderless table-hover border-0 mb-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th> 
                        <th>Order Id</th> 
                        <th>Transaction Type</th>
                        <th>Total Amount</th> 
                        <th style="width: 20%;">Remark</th>
                        <th style="width: 15%;">Notes</th>
                        <th style="width: 15%;">Refund Reason</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table> 
        </div>
    </div> 
	
	<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="varyingModalLabel">Refund Transaction</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
				</div>
				<form id="refundTransactionForm" action="{{ route('admin.transaction.refund') }}" method="post" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="mb-3">
							<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
							<select class="form-control" id="txn_status" name="txn_status" >
								<option value="refund">Refund</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="recipient-name" class="form-label">Include Charge </label>
							<div class="form-check form-switch">
								<input 
									class="form-check-input" 
									type="checkbox" 
									name="include_charge" 
									role="switch" 
									id="include_charge" 
									value="1"
								>
								<label class="form-check-label" for="flexSwitchCheckDefault"></label>
							</div>
						</div>
						<div class="mb-3">
							<label for="recipient-name" class="form-label">Reason for refund the transaction <span class="text-danger">*</span></label>
							<textarea class="form-control" id="refund_reason" name="refund_reason" placeholder="Reason for refund the transaction"></textarea>
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

        const flatpickrStartDate = document.querySelector('#start_date');
        const flatpickrEndDate = document.querySelector('#end_date');

        if (flatpickrStartDate) {
            flatpickrStartDate.flatpickr("#start_date", {
                wrap: true,
                dateFormat: "Y-m-d"
            });
        }

        if (flatpickrEndDate) {
            flatpickrEndDate.flatpickr("#end_date", {
                wrap: true,
                dateFormat: "Y-m-d"
            });
        }
 
		// Initialize DataTable
		var dataTable = $('#transaction-table').DataTable({

			dom: 'Bfrtip',
			buttons: [{
					extend: 'excelHtml5',
					className: 'd-none',
					text: 'excel',
					exportOptions: {
						modifier: {
							page: 'current'
						},
						columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
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
						columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
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
			responsive: false,
			bInfo: true,
			iDisplayLength: 10,
			order: [
				[0, 'desc']
			],
			bAutoWidth: false,
			"ajax": {
				"url": "{{ route('admin.transaction.ajax') }}",
				"dataType": "json",
				"type": "POST",
				"data": function(d) {
					d._token = "{{ csrf_token() }}"; 
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.txn_status = $('#txn_status').val();
					d.search = $('#search').val();
					d.user_id = $('#userDropdown').val();
					d.platform_name = @json($platform_name);
					d.platform_provider = @json($platform_provider);
				}
			},
		   "columns": [
				{ "data": "id" },
				{ "data": "user_name" }, 
				{ "data": "order_id" },
				{ "data": "transaction_type" },
				{ "data": "txn_amount" },
				{ "data": "comments" },
				{ "data": "notes" },
				{ "data": "refund_reason" },
				{ "data": "status" },
				{ "data": "created_at" },
				{ "data": "action" }
			],
			drawCallback: function() { 
				$('[data-toggle="tooltip"]').tooltip();
			}
		});


		$("#excelExport").on("click", function() {
			$(".buttons-excel").trigger("click");
		});

		$("#pdfExport").on("click", function() {
			$(".buttons-pdf").trigger("click");
		});
		$('#page_length').change(function() {
			dataTable.page.len($(this).val()).draw();
		})
		$('#applyFilters').click(function() { // Corrected selector here
			dataTable.draw();
		});

		$('#resetFilters').click(function() {
			$('#txn_status, #platform_name, #userDropdown').val('').trigger('change');
			$('#search').val('');

			const startOfMonth = "{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}";
			const endOfMonth = "{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}";

			$('#start_date').val(startOfMonth); // Set start_date
			$('#end_date').val(endOfMonth); // Set end_date

			dataTable.draw(); // Refresh the dataTable
		});
      
		
		var $refundTransactionForm = $('#refundTransactionForm');

		function openRefundModal(obj, event) 
		{
			const transactionId = $(obj).attr('data-transactionId');

			// Reset the form
			$refundTransactionForm[0].reset();  // Resets all input fields
			$refundTransactionForm.find('#transaction_id').remove(); // Remove hidden input if it exists

			// Append the new hidden input
			$refundTransactionForm.append(`<input type="hidden" name="transaction_id" id="transaction_id" value="${transactionId}">`);

			// Enable the submit button
			$refundTransactionForm.find('button').prop('disabled', false);
			$refundTransactionForm.find('.error_msg').remove(); 
			// Show the modal
			$('#refundModal').modal('show');
		}
 
		$refundTransactionForm.submit(function(event) 
		{
			event.preventDefault();   
			
			$refundTransactionForm.find('button').prop('disabled', true);   
			var formData = new FormData(this);  
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
					$refundTransactionForm.find('button').prop('disabled', false);	 
					$refundTransactionForm.find('.error_msg').remove(); 
					
					if(res.status === "success")
					{   
						toastrMsg(res.status,res.message);  
						dataTable.draw();
						$('#refundModal').modal('hide');
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) {
							var inputField = $refundTransactionForm.find('#' + key);
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
