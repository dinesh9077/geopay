<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="lightnetPlusForm" action="{{ route('admin.third-party-key.lightnet-update') }}?module_type=lightnet_setting" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Base Url</label>
					<input type="url" class="form-control" id="lightnet_url" name="lightnet_url" autocomplete="off" placeholder="Base Url"  value="{{ config('setting.lightnet_url') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Api Key</label>
					<input type="text" class="form-control" id="lightnet_apikey" name="lightnet_apikey" autocomplete="off" placeholder="Api Key"  value="{{ config('setting.lightnet_apikey') }}">
				</div>  
				 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Api Secret</label>
					<input type="text" class="form-control" id="lightnet_secretkey" name="lightnet_secretkey" autocomplete="off" placeholder="Secret Key"  value="{{ config('setting.lightnet_secretkey') }}">
				</div>  
				<hr>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="lightnet_commission_type" name="lightnet_commission_type" > 
						<option value="flat" {{ config('setting.lightnet_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.lightnet_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="lightnet_commission_charge" name="lightnet_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.lightnet_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div> 
				@if (config("permission.lightnet_setting.edit")) 
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
				@endif
			</form>   
			<div class="row mt-3" id="lightnetView">  
			</div> 
		</div>
	</div>
</div>