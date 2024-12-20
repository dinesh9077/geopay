@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Transaction Report')
@section('header_title', 'Transaction Report')
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
                <select class="form-control default-input content-3 select2" name="channel" id="channel">  
					<option value="lightnet">Lightnet</option> 
                </select>
            </div> 
            <div class="filter-buttons col-md-4 col-lg-2">
                <button onclick="getLiveRates(this, event)" class="btn btn-primary">Get Live Rate</button> 
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

            <table id="fxrate-table" class="table table-borderless table-hover border-0 mb-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Channel</th>
                        <th>Currency</th>
                        <th>Exchange Rate Against 1 USD</th>
                        <th>Exchange Rate Aggregator</th> 
                        <th>Margin Percentage(Flat / %)</th> 
                        <th>Date</th> 
                    </tr>
                </thead>
            </table> 
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
				"url": "{{ route('admin.live.exchange-rate.ajax') }}",
				"dataType": "json",
				"type": "POST",
				"data": function(d) {
					d._token = "{{ csrf_token() }}";
					d.channel = $('#channel').val(); 
				}
			},
			"columns": [{
					"data": "id"
				},
				{
					"data": "channel"
				},
				{
					"data": "currency"
				},
				{
					"data": "markdown_rate"
				},
				{
					"data": "aggregator_rate"
				},
				{
					"data": "markdown_charge"
				} ,
				{
					"data": "created_at"
				} 
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
          
    </script>
@endpush
