<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="guardianPgForm" action="{{ route('admin.third-party-key.update') }}?module_type=guardian_payment_gateway" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Base Url</label>
					<input type="url" class="form-control" id="guardian_endpoint" name="guardian_endpoint" autocomplete="off" placeholder="Base Url"  value="{{ config('setting.guardian_endpoint') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Merchant Site Key</label>
					<input type="text" class="form-control" id="guardian_merchant_key" name="guardian_merchant_key" autocomplete="off" placeholder="Merchant Site Key"  value="{{ config('setting.guardian_merchant_key') }}">
				</div>  
				 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Merchant Secret Key</label>
					<input type="text" class="form-control" id="guardian_merchant_secret" name="guardian_merchant_secret" autocomplete="off" placeholder="Secret Key"  value="{{ config('setting.guardian_merchant_secret') }}">
				</div>  
				@if (config("permission.internation_airtime_setting.edit")) 
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				@endif
			</form> 
		</div>
	</div>
</div>