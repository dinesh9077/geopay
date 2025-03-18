<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<div class="d-flex align-item-center justify-content-between mb-3">
				<h3></h3>   
				<a href="{{ route('admin.third-party-key.onafric-bank-list') }}" class="btn btn-info btn-sm" onclick="fetchBanks(this, event)"> Fetch Banks</a> 
			</div>	
			<form class="forms-sample row" id="onafricBankForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_bank_setting" method="post" enctype="multipart/form-data"> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Onafric Fees</label>
					<input type="text" class="form-control" id="onafric_bank_send_fees" name="onafric_bank_send_fees" autocomplete="off" placeholder="Onafric Fees" value="{{ config('setting.onafric_bank_send_fees') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="onafric_bank_commission_type" name="onafric_bank_commission_type" > 
						<option value="flat" {{ config('setting.onafric_bank_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.onafric_bank_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="onafric_bank_commission_charge" name="onafric_bank_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.onafric_bank_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				@if (config("permission.onafric_bank_setting.edit")) 
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
				@endif
			</form> 
		</div>
	</div>
</div> 
<script>
	function fetchBanks(obj, event) {
		event.preventDefault(); // Prevent default action if it's a form button

		Swal.fire({
			title: "Are you sure?",
			text: "Do you want to fetch Bank lists?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes!",
			cancelButtonText: "Cancel"
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: obj, // Replace with your actual endpoint
					type: 'POST',
					data: { _token: "{{ csrf_token() }}" }, 
					dataType: "Json",
					success: function(response) 
					{
						if(response.status == "success")
						{
							Swal.fire("Success!", response.message, "success");
						}
						else
						{
							Swal.fire("Error!", response.message, "error");
						}
					},
					error: function(xhr) {
						Swal.fire("Error!", "Something went wrong.", "error");
					}
				});
			}
		});
	} 
</script>