@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Merchant Fund')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Merchant Fund</h4>
	</div> 
	@if (config("permission.merchant_fund.add"))
		<div class="d-flex align-items-center flex-wrap text-nowrap"> 
			<button type="button" data-bs-toggle="modal" data-bs-target="#addFundModal" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
				<i class="btn-icon-prepend" data-feather="plus"></i>Add Fund
			</button>
		</div>
	@endif
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<div class="table-responsive">
					<table id="userDatatable" class="table">
						<thead>
							<tr>
								<th>#</th> 
								<th>Amount</th>
								<th>Payment Mode</th>
								<th>Transaction ID</th>
								<th>Date</th>
								<th>Remarks</th>
								<th>Receipt</th>
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

<!-- Add Fund Modal -->
<div class="modal fade" id="addFundModal" tabindex="-1" aria-labelledby="addFundModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			
			<div class="modal-header">
				<h5 class="modal-title" id="addFundModalLabel">Add Fund</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<form id="addFundForm" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row g-3">
						
						<div class="col-md-6">
							<label class="form-label">Amount</label>
							<input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
						</div>
						
						<div class="col-md-6">
							<label class="form-label">Payment Mode</label>
							<select name="payment_mode" id="payment_mode" class="form-control select2" required>
								<option value="">Select Mode</option>
								<option value="cash">Cash</option>
								<option value="bank">Bank</option>
								<option value="upi">UPI</option>
								<option value="cheque">Cheque</option>
							</select>
						</div>
						
						<div class="col-md-6">
							<label class="form-label">Transaction ID</label>
							<input type="text" name="transaction_id" id="transaction_id" class="form-control">
						</div>
						
						<div class="col-md-6">
							<label class="form-label">Date</label>
							<input type="date" name="date" id="date" class="form-control" required>
						</div>
						
						<div class="col-md-12">
							<label class="form-label">Remarks</label>
							<textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
						</div>
						
						<div class="col-md-12">
							<label class="form-label">Receipt (Upload)</label>
							<input type="file" name="receipt" id="receipt" class="form-control" accept="image/*,application/pdf">
						</div>
						
					</div>
				</div>
				
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save Fund</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</form>
			
		</div>
	</div>
</div>


@endsection

@push('js')
<script> 
	const userId = @json(request('id') ?? '');
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
			"url": "{{ route('admin.merchant.fund.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{ csrf_token() }}"; 
				d.user_id   = userId; 
			}
		},
		"columns": [
		{ "data": "id" },              
		{ "data": "amount" },          
		{ "data": "payment_mode" },   
		{ "data": "transaction_id" },  
		{ "data": "date" },          
		{ "data": "remarks" },       
		{ "data": "receipt" },        
		{ "data": "created_at" },     
		{ "data": "action" }          
		] 
	}); 
	
	$('#addFundModal').on('show.bs.modal', function () {
		let form = $('#addFundForm')[0];
		form.reset();

		// reset select2
		$(form).find('.select2').val(null).trigger('change');

		// reset file preview if any
		$(form).find('input[type="file"]').val('');
	});

	$(document).ready(function () {
		$('#addFundForm').on('submit', function(e){
			e.preventDefault();

			var formData = new FormData(this);
			formData.append('user_id', userId);
			
			$.ajax({
				url: "{{ route('admin.merchant.fund.store') }}", // your route
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				success: function(res)
				{
					if(res.status === "success")
					{
						$('#addFundModal').modal('hide');
						dataTable.draw();  
						toastrMsg(res.status, res.message);
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
					else{ 
						toastrMsg(res.status, res.message);
					}
				},
				error: function(err){
					toastr.error("Something went wrong!");
				}
			});
		});
	});

	
</script>
@endpush				