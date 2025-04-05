<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body">  
			<form class="forms-sample row" id="onafricBankForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_mobile_collection_setting" method="post" enctype="multipart/form-data"> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Api Url</label>
					<input type="url" class="form-control" id="onafric_collection_api_url" name="onafric_collection_api_url" placeholder="Api Url" value="{{ config('setting.onafric_collection_api_url') ?? '' }}" >
				</div> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Token</label>
					<input type="text" class="form-control" id="onafric_collection_token" name="onafric_collection_token" placeholder="Token" value="{{ config('setting.onafric_collection_token') ?? '' }}">
				</div>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="onafric_collection_commission_type" name="onafric_collection_commission_type" > 
						<option value="flat" {{ config('setting.onafric_collection_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.onafric_collection_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="onafric_collection_commission_charge" name="onafric_collection_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.onafric_collection_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				@if (config("permission.onafric_mobile_collection_setting.edit")) 
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				@endif
			</form> 
		</div>
	</div>
</div>  