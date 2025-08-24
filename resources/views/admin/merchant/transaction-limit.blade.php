<!-- Service Limit Modal -->
<div class="modal fade" id="serviceLimitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="serviceLimitForm">
            @csrf
            <input type="hidden" name="user_id" id="user_id" value="{{ $merchant->id }}">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Service-wise Daily Transaction Limits</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Example Service: Mobile Money -->
                        <div class="col-md-12 mb-3">
                            <label>Mobile Money Daily Limit</label>
                            <input type="text" name="limits[mobile_money]" class="form-control" placeholder="Enter Daily limit" value="{{ $mobileMoneyLimit }}">
                        </div>

                        <!-- Example Service: Bank Transfer -->
                        <div class="col-md-12 mb-3">
                            <label>Bank Transfer Daily Limit</label>
                            <input type="text" name="limits[bank_transfer]" class="form-control" placeholder="Enter Daily limit" value="{{ $bankTransferLimit }}">
                        </div>
                    </div>
                </div>
				@if (config('permission.merchant_daily_transaction_limit.add'))	
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				@endif
            </div>
        </form>
    </div>
</div>
<script> 
	$('#serviceLimitForm').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			url: "{{ route('admin.merchant.transaction-limit.store') }}",
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
						inputField.append(errorSpan);
					});
				}
				else{ 
					toastrMsg(res.status, res.message);
				}
			}
		});
	}); 
</script>