@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transaction List')
@section('header_title', 'Transaction List')
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
		<div class=" col-md-4 col-lg-2">
			<select class="form-control default-input content-3 select2" name="platform_name" id="platform_name">
				<option value="">ALL</option>
				<option value="international airtime">International Airtime</option>
				<option value="geopay to geopay wallet">Geopay To Geopay Wallet</option>
				<option value="admin transfer">Admin Transfer</option>
			</select>
		</div>
		<div class=" col-md-4 col-lg-2" >
			<input type="text" class="form-control form-control-lg default-input " id="start_date" name="start_date" placeholder="Start date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="form-control form-control-lg default-input" id="end_date" name="end_date" placeholder="End date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
		</div>
		<div class=" col-md-4 col-lg-2">
			<select class="form-control default-input content-3 select2" name="txn_status" id="txn_status">
				<option value="">ALL</option>
				<option value="pending">Pending</option>
				<option value="process">Process</option>
				<option value="success">Success</option>
			</select>
		</div>
		<div class="col-md-4 col-lg-2">
			<input type="text" class="form-control form-control-lg default-input " name="search" id="search" placeholder="Search Item">
		</div> 
		<div class="filter-buttons col-md-4 col-lg-2">
			<button id="applyFilters" class="btn btn-primary btn-lg">Filter</button>
			<button id="resetFilters" class="btn btn-secondary btn-lg">Reset</button>
		</div>
	</div>
	<hr>
	<div class="data-table-container">
		<table id="transaction-table" class="table table-borderless table-hover border-0 mb-4">
			<thead>
				<tr>
					<th>#</th> 
					<th>Service Name</th>
					<th>Order Id</th>
					<th>Fees</th>
					<th>Total Amount</th>
					<th>Exchange Rate</th> 
					<th style="width: 20%;">Remark</th>
					<th style="width: 15%;">Notes</th> 
					<th>Status</th>
					<th>Created At</th>
					<th>Action</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@endsection

@push('js')

<script>
	$('.select2').select2({
		width:"100%"
	});
	
	const flatpickrStartDate = document.querySelector('#start_date');
	const flatpickrEndDate = document.querySelector('#end_date');

	if (flatpickrStartDate) {
		flatpickrStartDate.flatpickr("#start_date",{
			wrap: true,
			dateFormat: "Y-m-d" 
		});
	}

	if (flatpickrEndDate) {
		flatpickrEndDate.flatpickr("#end_date",{
			wrap: true,
			dateFormat: "Y-m-d" 
		});
	}

	$(document).ready(function() {
		// Initialize DataTable
		var dataTable = $('#transaction-table').DataTable({ 
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
			order: [[0, 'desc']],
			bAutoWidth: false,			 
			"ajax": {
				"url": "{{ route('transaction-ajax') }}",
				"dataType": "json",
				"type": "POST",
				"data": function (d) {
					d._token = "{{csrf_token()}}"; 
					d.platform_name = $('#platform_name').val();
					d.start_date = $('#start_date').val(); 
					d.end_date = $('#end_date').val(); 
					d.txn_status = $('#txn_status').val(); 
					d.search = $('#search').val(); 
				}
			},
			"columns": [
				{ "data": "id" },    
				{ "data": "platform_name" },   
				{ "data": "order_id" },  
				{ "data": "fees" },  
				{ "data": "txn_amount" },  
				{ "data": "unit_convert_exchange" },  
				{ "data": "comments" },  
				{ "data": "notes" },  
				{ "data": "status" },  
				{ "data": "created_at" }, 
				{ "data": "action" }
			]
		});  
			
		$('#applyFilters').click(function() { // Corrected selector here
			dataTable.draw();	
		});
			
		$('#resetFilters').click(function() { 
			$('#txn_status, #platform_name').val('').trigger('change');
			$('#search').val('');
 
			const startOfMonth = "{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}";  
			const endOfMonth = "{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}";
 
			$('#start_date').val(startOfMonth); // Set start_date
			$('#end_date').val(endOfMonth); // Set end_date

			dataTable.draw(); // Refresh the dataTable
		}); 
	});
	
	function viewDetail(obj, event)
	{
		event.preventDefault();
		
		
		var featch_detail = `
			<p>Transaction E-Receipt</p>
			<table style="text-align: start;">
				<tr><th class="content-3">SERVICE NAME: </th><td class="content-2">Geopay To Geopay Wallet</td></tr>
				<tr><th class="content-3">ORDER ID: </th><td class="content-2">GPWW-5487548754</td></tr> 
				<tr><th class="content-3">TOTAL AMOUNT: </th><td class="content-2">0.19 USD</td></tr> 
				<tr><th class="content-3">EXCHANGE RATE: </th><td class="content-2">1.00</td></tr> 
				<tr><th class="content-3">FROM ACCOUNT: </th><td class="content-2">DINESH PATIL (+917507642090) </td></tr> 
				<tr><th class="content-3">TO ACCOUNT: </th><td class="content-2">NITESH SINGH (+917507642091) </td></tr> 
				<tr><th class="content-3">COUNTERPARTY NAME: </th><td class="content-2">DINESH PATIL</td></tr> 
				<tr><th class="content-3">PAYMENT DATE: </th><td class="content-2">2024-05-23</td></tr> 
				<tr><th class="content-3">DESCRIPTION: </th><td class="content-2">payment transfer to account</td></tr> 
				<tr><th class="content-3">TRANSACTION STATUS: </th><td class="content-2">Success</td></tr>
			</table>
		`;

		Swal.fire({  
			type: "success",
			confirmButtonColor: "#188ae2",
			confirmButtonText: "Close",
			imageUrl: "{{ url('storage/setting', config('setting.site_logo')) }}",
			imageWidth: 70,
			imageHeight: 70,
			customClass: {
				image: 'custom-image-class'
			},
			html: featch_detail
		}).then(function(result) {
			if (result.isConfirmed) {
				// Action after the close button is clicked (if needed)
			}
		});
	}	
	 
</script>
@endpush