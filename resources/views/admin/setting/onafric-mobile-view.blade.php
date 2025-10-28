<div class="container">
	<form id="onafricMobileCountryform" action="{{ route('admin.third-party-key.onafric-mobile-update')}}" method="POST">
		@csrf
		<div id="countries-form"> 
			<div class="d-flex align-item-center justify-content-between mb-3">
				<h3 >Country List  </h3>   
				<a href="{{ route('admin.third-party-key.onafric-mobile-webhook') }}" class="btn btn-info btn-sm" onclick="registerWebhook(this, event)"> Register WebHook</a> 
			</div>
			@foreach($onafricCuntries as $onafricCuntry)
				<div class="country-section mb-3 ms-3">
					<h4 class="mb-2 text-primary" >{{ $onafricCuntry->name }}</h4>
					<div class="channels" id="html_channel_{{ $onafricCuntry->id }}">
						@if($onafricCuntry->channels->isNotEmpty()) 
							@foreach($onafricCuntry->channels as $key => $channel)
								<div class="channel-row mb-3">
									<input type="hidden" class="form-control" name="channel_id[{{ $onafricCuntry->id }}][]" value="{{ $channel->id }}">
									<div class="row">
										<div class="col-md-2">
											<label class="form-label">Channel Name</label>
											<input type="text" class="form-control" name="channel[{{ $onafricCuntry->id }}][]" value="{{ $channel->channel }}">
										</div>
										<div class="col-md-2">
											<label class="form-label">Onafric Fees</label>
											<input type="text" class="form-control" name="fees[{{ $onafricCuntry->id }}][]" value="{{ $channel->fees }}" required>
										</div>
										<div class="col-md-2">
											<label class="form-label">Percent Type</label>
											<select class="form-control" name="commission_type[{{ $onafricCuntry->id }}][]" required>
												<option value="flat" {{ $channel->commission_type === 'flat' ? 'selected' : '' }}>Flat</option>
												<option value="percentage" {{ $channel->commission_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
											</select>
										</div>
										<div class="col-md-2">
											<label class="form-label">Charge</label>
											<input type="number" class="form-control" name="commission_charge[{{ $onafricCuntry->id }}][]" value="{{ $channel->commission_charge }}" required>
										</div> 
										<div class="col-md-2">
											<label class="form-label">Channel Status</label>
											<select class="form-control" name="channel_status[{{ $onafricCuntry->id }}][]" required>
												<option value="1" {{ $channel->status == 1 ? 'selected' : '' }}>Active</option>
												<option value="0" {{ $channel->status == 0 ? 'selected' : '' }}>In-Active</option>
											</select>
										</div>
										<div class="col-md-2 d-flex align-items-end gap-3">
											@if($key == 0)
												<button type="button" class="btn btn-primary" data-country-id="{{ $onafricCuntry->id }}" onclick="addChannel(this, event)">Add Channel</button> 
											@else
												{{-- <button type="button" class="btn btn-danger " data-country-id="{{ $onafricCuntry->id }}" onclick="removeChannel(this, event)">Remove Channel</button>  --}}
											@endif
										</div>
									</div>
								</div>
							@endforeach
						@else 
							<div class="channel-row mb-3">
								<input type="hidden" class="form-control" name="channel_id[{{ $onafricCuntry->id }}][]" value="">
								<div class="row">
									<div class="col-md-2">
										<label class="form-label">Channel Name</label>
										<input type="text" class="form-control" name="channel[{{ $onafricCuntry->id }}][]" value="">
									</div>
									<div class="col-md-2">
										<label class="form-label">Onafric Fees</label>
										<input type="text" class="form-control" name="fees[{{ $onafricCuntry->id }}][]" value="0" required>
									</div>
									<div class="col-md-2">
										<label class="form-label">Percent Type</label>
										<select class="form-control" name="commission_type[{{ $onafricCuntry->id }}][]" required>
											<option value="flat">Flat</option>
											<option value="percentage">Percentage</option>
										</select>
									</div>
									<div class="col-md-2">
										<label class="form-label">Charge</label>
										<input type="number" class="form-control" name="commission_charge[{{ $onafricCuntry->id }}][]" value="0" required>
									</div>
									<div class="col-md-2">
										<label class="form-label">Channel Status</label>
										<select class="form-control" name="channel_status[{{ $onafricCuntry->id }}][]" required>
											<option value="1">Active</option>
											<option value="0">In-Active</option>
										</select>
									</div>
									<div class="col-md-2 d-flex align-items-end gap-3">
										<button type="button" class="btn btn-primary" data-country-id="{{ $onafricCuntry->id }}" onclick="addChannel(this, event)">Add Channel</button> 
									</div>
								</div>
							</div>	
						@endif
					</div>
				</div>
			@endforeach 
		</div>
		@if (config("permission.onafric_mobile_setting.edit"))
			<div class="d-flex justify-content-end">
				<button type="submit" class="btn btn-success">Save</button>
			</div>
		@endif
	</form>
	<script> 
		var $onafricMobileCountryform = $(`#onafricMobileCountryform`);
		function addChannel(obj, event) 
		{
			event.preventDefault();
			const countryId = $(obj).attr('data-country-id'); 
			let html = `<div class="channel-row mb-3">
				<input type="hidden" class="form-control" name="channel_id[${countryId}][]" value="">
				<div class="row">
					<div class="col-md-2">
						<label class="form-label">Channel Name</label>
						<input type="text" class="form-control" name="channel[${countryId}][]" value="" required>
					</div>
					<div class="col-md-2">
						<label class="form-label">Onafric Fees</label>
						<input type="text" class="form-control" name="fees[${countryId}][]" value="0" required>
					</div>
					<div class="col-md-2">
						<label class="form-label">Percent Type</label>
						<select class="form-control" name="commission_type[${countryId}][]" required>
							<option value="flat">Flat</option>
							<option value="percentage">Percentage</option>
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Charge</label>
						<input type="number" class="form-control" name="commission_charge[${countryId}][]" value="0" required>
					</div>
					<div class="col-md-2">
						<label class="form-label">Channel Status</label>
						<select class="form-control" name="channel_status[${countryId}][]" required>
							<option value="1">Active</option>
							<option value="0">In-Active</option>
						</select>
					</div>
					<div class="col-md-2 d-flex align-items-end gap-3">
						<button type="button" class="btn btn-danger" data-country-id="${countryId}" onclick="removeChannel(this, event)">Remove Channel</button> 
					</div>
				</div>
			</div>`;
			
			$onafricMobileCountryform.find(`#html_channel_${countryId}`).append(html);
		}
		
		function removeChannel(obj, event) 
		{
			event.preventDefault();
			$(obj).closest('.channel-row').remove();
		}
		
		$onafricMobileCountryform.submit(function (event) {
			event.preventDefault(); 

			let submitButton = $onafricMobileCountryform.find('[type="submit"]');
			submitButton.prop('disabled', true).addClass('loading-span').html('<span class="spinner-border"></span>');

			let formData = new FormData(this); // Correct way to capture form fields

			$.ajax({
				url: $(this).attr('action'),
				type: $(this).attr('method'),
				data: formData,
				processData: false, // Prevent automatic data processing
				contentType: false, // Prevent setting content-type header
				cache: false,
				dataType: 'json',
				success: function (res) {   
					submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
					$('.error_msg').remove(); 

					toastrMsg(res.status, res.message); 
					
					if (res.status === "success") { 
						getOnafricMobileView(event); // Refresh the view
					}
				},
				error: function (xhr) {
					submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
					console.error(xhr.responseText);
					toastrMsg("error", "Something went wrong. Please try again.");
				}
			});
		});
		
		function registerWebhook(obj, event) {
			event.preventDefault(); // Prevent default action if it's a form button

			Swal.fire({
				title: "Are you sure?",
				text: "Do you want to register this webhook?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, Register!",
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
</div>