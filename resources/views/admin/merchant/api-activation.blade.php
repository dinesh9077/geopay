<div class="modal fade" id="apiActivationModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Api Activation</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<div class="modal-body">
				<form id="createClientForm">
					@csrf 
					
					<!-- ✅ New Service Section -->
					
					<h6 class="fw-bold">Services</h6>
					<div class="form-check mt-2">
						<input class="form-check-input" type="checkbox" name="services[]" id="service_mobile" value="mobile" 
							{{ $apiCredential && in_array('mobile', $apiCredential->services ?? []) ? 'checked' : '' }}>
						<label class="form-check-label" for="service_mobile">
							Transfer Mobile Money
						</label>
					</div>
					<div class="form-check mt-2">
						<input class="form-check-input" type="checkbox" name="services[]" id="service_bank" value="bank"
							{{ $apiCredential && in_array('bank', $apiCredential->services ?? []) ? 'checked' : '' }}>
						<label class="form-check-label" for="service_bank">
							Transfer Bank
						</label>
						<br>
						<span id="services"></span>
					</div>
					
					<div class="mb-3 mt-2">
						<label class="form-label">Status</label>
						<select name="status" id="status" class="form-select">
							<option value="active" {{ $apiCredential && $apiCredential->status == 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $apiCredential && $apiCredential->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select> 
					</div>
					<input type="hidden" name="user_id" id="user_id" value="{{ $merchant->id }}"> 
					<input type="hidden" name="environment" id="environment" value="{{ $apiCredential ? $apiCredential->environment : 'production' }}" > 
					@if (config("permission.api_activate_deactive.add"))
						<button type="submit" class="btn btn-success w-100">Generate Client Credential</button>
					@endif
				</form>
				<hr>
				<div id="clientDetails" class="{{ !$apiCredential ? 'd-none' : '' }}">
					<div class="mb-3">
						<label class="form-label">Api Url</label>
						<div class="input-group">
							<input type="text" id="api_url" class="form-control bg-light" value="{{ $apiCredential ? $apiCredential->api_url : '' }}" readonly>
							<button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('api_url')">Copy</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Client ID</label>
						<div class="input-group">
							<input type="text" id="client_id" class="form-control bg-light" value="{{ $apiCredential ? $apiCredential->client_id : '' }}" readonly>
							<button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('client_id')">Copy</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Client Secret</label>
						<div class="input-group">
							<input type="text" id="client_secret" class="form-control bg-light" value="{{ $apiCredential ? $apiCredential->client_secret : '' }}" readonly>
							<button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('client_secret')">Copy</button>
						</div>
					</div> 
				</div>
				  
			</div>
		</div>
	</div>
</div>
<script> 
	$('#createClientForm').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			url: "{{ route('admin.merchant.api-activation.generate') }}",
			method: "POST",
			data: $(this).serialize(),
			success: function(res) { 
				if(res.status === "success")
				{
					var result = decryptData(res.response); 
					$('#clientDetails').removeClass('d-none');
					$('#api_url').val(result.client.api_url);
					$('#client_id').val(result.client.client_id);
					$('#client_secret').val(result.client.client_secret);
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

	function copyToClipboard(id) {
		let input = document.getElementById(id);
		input.select();
		input.setSelectionRange(0, 99999); // mobile
		document.execCommand("copy");
		alert("Copied: " + input.value);
	} 
		
</script>  