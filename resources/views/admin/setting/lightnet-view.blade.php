<hr>
<div class="d-flex justify-content-between align-items-center">
	<h4 class="mb-3 mb-md-0">Available Countries</h4>
	<div class="d-flex align-items-center gap-2">
		<a href="{{ route('admin.third-party-key.sync-countries') }}" onclick="syncLightNet(this, event)" class="btn btn-sm btn-info">Sync Countries</a>
		<a href="{{ route('admin.third-party-key.sync-catalogue') }}" onclick="syncLightNet(this, event)" class="btn btn-sm btn-primary">Sync Catalogue</a>
	</div>
</div>
<div class="data-table-container">
	<table class="table mb-4">
		<thead>
			<tr>
				<th>#</th>
				<th>Payout Country</th>
				<th>Payout Currency</th>
				<th>Status / Country Name</th>
			</tr>
		</thead>
		<tbody>
			@foreach($lightnetCountries as $key => $lightnetCountry)
			<tr>
				<td>{{ ($key + 1) }}</td>
				<td>{{ $lightnetCountry->data }}</td>
				<td>{{ $lightnetCountry->value }}</td> 
				<td>
					<form action="{{ route('admin.third-party-key.lightnet-country-update') }}" method="POST" class="d-flex align-items-center">
						@csrf 
						<input type="hidden" name="id" value="{{ $lightnetCountry->id }}"> 
						<input type="hidden" name="service_name" value="lightnet"> 
						<select class="form-control form-control-sm me-2" name="status">
							<option value="1" {{ $lightnetCountry->status == 1 ? 'selected' : '' }}> Active </option>
							<option value="0" {{ $lightnetCountry->status == 0 ? 'selected' : '' }}> In-Active </option>
						</select>
						<input type="text" name="label" class="form-control form-control-sm me-2" placeholder="Enter Country Full Name" value="{{ $lightnetCountry->label }}">
						<button type="submit" class="btn btn-sm btn-primary">Submit</button>
					</form>
				</td>
			</tr>
			@endforeach 
		</tbody>
	</table>
</div> 
<script>
	$(document).ready(function() {
		// Intercept form submission
		$('form').on('submit', function(event) {
			event.preventDefault();  // Prevent the default form submission

			// Disable the submit button to prevent multiple submissions
			var $form = $(this);
			$form.find('button').prop('disabled', true);

			run_waitMe($('body'), 1, 'facebook');
		
			var formData = {};
			$form.find('input, select').each(function() {
				var inputName = $(this).attr('name');

				if ($(this).is(':checkbox')) {
					// For checkboxes, store whether it is checked (true or false)
					formData[inputName] = $(this).is(':checked');
				} else {
					// For other inputs, use the value
					formData[inputName] = $(this).val();
				}
			});

			// Encrypt data before sending
			const encrypted_data = encryptData(JSON.stringify(formData));
			 
			$.ajax({
				async: true,
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{   
					$form.find('button').prop('disabled',false);	 
					$('body').waitMe('hide');
					$('.error_msg').remove(); 
					if(res.status === "success")
					{ 
						toastrMsg(res.status, res.message);  
					} 
					else
					{ 
						toastrMsg(res.status, res.message);
					}
				} 
			});
		});
	});
 
	function syncLightNet(obj, event)
	{  
		event.preventDefault();
		run_waitMe($('body'), 1, 'facebook')
		$.get(obj, function(res)
		{
			$('body').waitMe('hide');
			if(res.status == "success")
			{
				toastrMsg(res.status, res.message); 
			}
			else
			{
				toastrMsg(res.status, res.message); 
			}
		}, 'Json');  
	}
	 
</script>