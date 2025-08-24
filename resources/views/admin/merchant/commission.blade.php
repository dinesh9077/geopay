<div class="modal fade" id="flatRateModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="flatRateForm" >
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Merchant Flat Rate Settings</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<input type="hidden" name="user_id" value="{{ $merchant->id}}">
				<div class="modal-body">
					<div class="row g-3"> 
						<!-- Mobile Money -->
						<div class="col-md-6">
							<label class="form-label">Mobile Money</label>
							<select name="charge_type[mobile_money]" class="form-select">
								<option value="flat" {{ $mobileMoney && $mobileMoney->charge_type == "flat" ? 'selected' : '' }}>Flat</option>
								<option value="percentage" {{ $mobileMoney && $mobileMoney->charge_type == "percentage" ? 'selected' : '' }}>Percentage</option>
							</select>
							<input type="number" step="0.01" min="0" 
							name="charge_value[mobile_money]" 
							class="form-control mt-2" placeholder="Enter rate" value="{{ $mobileMoney ? ($mobileMoney->charge_value ?? 0) : 0 }}">
							<select name="status[mobile_money]" class="form-select  mt-2">
								<option value="1"  {{ $mobileMoney && $mobileMoney->status == "1" ? 'selected' : '' }}>Active</option>
								<option value="0"  {{ $mobileMoney && $mobileMoney->status == "0" ? 'selected' : '' }}>InActive</option>
							</select>
						</div>
						
						<!-- Bank Transfer -->
						<div class="col-md-6">
							<label class="form-label">Bank Transfer</label>
							<select name="charge_type[bank_transfer]" class="form-select">
								<option value="flat"  {{ $bankTransfer && $bankTransfer->charge_type == "flat" ? 'selected' : '' }}>Flat</option>
								<option value="percentage"  {{ $bankTransfer && $bankTransfer->charge_type == "percentage" ? 'selected' : '' }}>Percentage</option>
							</select>
							<input type="number" step="0.01" min="0" 
							name="charge_value[bank_transfer]" 
							class="form-control mt-2" placeholder="Enter rate"  value="{{ $bankTransfer ? ($bankTransfer->charge_value ?? 0) : 0 }}">
							<select name="status[bank_transfer]" class="form-select  mt-2">
								<option value="1"  {{ $bankTransfer && $bankTransfer->status == "1" ? 'selected' : '' }}>Active</option>
								<option value="0"  {{ $bankTransfer && $bankTransfer->status == "0" ? 'selected' : '' }}>InActive</option>
							</select>
						</div> 
					</div>
				</div>
				@if (config("permission.merchant_commission.add"))
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				@endif
			</div>
		</form>
	</div>
</div>
<script> 
	$('#flatRateForm').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			url: "{{ route('admin.merchant.commission.store') }}",
			method: "POST",
			data: $(this).serialize(),
			success: function(res) { 
				if(res.status === "success")
				{ 
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
			}
		});
	}); 
</script>