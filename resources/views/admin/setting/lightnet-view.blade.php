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
				<th>Status </th>
				<th>Country Name </th>
				{{-- <th>Markdown Type</th>
				<th>Markdown Charge</th> --}}
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($lightnetCountries as $key => $lightnetCountry)
				<tr> 
					<td>{{ ($key + 1) }}</td>
					<td>{{ $lightnetCountry->data }}</td>
					<td>{{ $lightnetCountry->value }}</td>
					<td>
						<input type="hidden" name="id" value="{{ $lightnetCountry->id }}">
						<input type="hidden" name="service_name" value="lightnet">

						<select class="form-control form-control-sm me-2" id="status_{{ $lightnetCountry->id }}" name="status">
							<option value="1" {{ $lightnetCountry->status == 1 ? 'selected' : '' }}>Active</option>
							<option value="0" {{ $lightnetCountry->status == 0 ? 'selected' : '' }}>In-Active</option>
						</select>
					</td>
					<td>
						<input type="text" id="label_{{ $lightnetCountry->id }}" name="label" class="form-control form-control-sm me-2" placeholder="Enter Country Full Name" value="{{ $lightnetCountry->label }}">
					</td>
					{{-- <td>
						<select class="form-control form-control-sm me-2" id="markdown_type_{{ $lightnetCountry->id }}" name="markdown_type">
							<option value="flat" {{ $lightnetCountry->markdown_type == 'flat' ? 'selected' : '' }}>Flat/Fix</option>
							<option value="percentage" {{ $lightnetCountry->markdown_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
						</select>
					</td>
					<td>
						<input type="text" class="form-control" id="markdown_charge_{{ $lightnetCountry->id }}" name="markdown_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ $lightnetCountry->markdown_charge ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</td> --}}
					<td>
						<button type="button" class="btn btn-sm btn-primary update-button" 
						data-id="{{ $lightnetCountry->id }}">Submit</button>
					</td> 
				</tr>
			@endforeach 
		</tbody>
	</table>
</div> 
<script>
	$(document).ready(function() {
		// Intercept form submission
		 $('.update-button').on('click', function (event) {
			event.preventDefault();  // Prevent the default form submission

			// Disable the submit button to prevent multiple submissions
			var $form = $(this);
			$form.prop('disabled', true); 
			run_waitMe($('body'), 1, 'facebook');
		 
			const id = $(this).data('id');
			const formData = {
				id: id,
				status: $(`#status_${id}`).val(),
				label: $(`#label_${id}`).val(),
				markdown_type: 'flat',
				markdown_charge: 0,
				/* markdown_type: $(`#markdown_type_${id}`).val(),
				markdown_charge: $(`#markdown_charge_${id}`).val(), */
			};
 
			// Encrypt data before sending
			const encrypted_data = encryptData(JSON.stringify(formData));
			 
			$.ajax({
				async: true,
				type: "post",
				url: "{{ route('admin.third-party-key.lightnet-country-update') }}",
				data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{   
					$form.prop('disabled',false);	 
					$('body').waitMe('hide'); 
					toastrMsg(res.status, res.message);
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