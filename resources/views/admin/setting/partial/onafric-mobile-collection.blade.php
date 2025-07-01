<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body">  
			<form class="forms-sample row" id="onafricBankForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_mobile_collection_setting" method="post" enctype="multipart/form-data"> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Api Url</label>
					<input type="url" class="form-control" id="onafric_collection_api_url" name="onafric_collection_api_url" placeholder="Api Url" value="{{ config('setting.onafric_collection_api_url') ?? '' }}" >
				</div> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Account Id</label>
					<input type="text" class="form-control" id="onafric_collection_account_id" name="onafric_collection_account_id" placeholder="Account Id" value="{{ config('setting.onafric_collection_account_id') ?? '' }}" >
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
				<div class="mb-3 col-md-6"> </div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Rate Api Url</label>
					<input type="url" class="form-control" id="onafric_rate_api_url" name="onafric_rate_api_url" placeholder="Rate Api Url" value="{{ config('setting.onafric_rate_api_url') ?? '' }}" >
				</div>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Rate Partner Code</label>
					<input type="text" class="form-control" id="onafric_rate_partner_code" name="onafric_rate_partner_code" placeholder="Rate Partner Code" value="{{ config('setting.onafric_rate_partner_code') ?? '' }}" >
				</div> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Rate Auth Key</label>
					<input type="text" class="form-control" id="onafric_rate_auth_key" name="onafric_rate_auth_key" placeholder="Rate Auth Key" value="{{ config('setting.onafric_rate_auth_key') ?? '' }}" >
				</div>  
				@if (config("permission.onafric_mobile_collection_setting.edit")) 
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
				@endif
			</form> 
			<hr>
			<div class="row mt-3" id="onafricCollectionView">   
				<div class="container">
					<form id="onafricCollectionCountryform" action="{{ route('admin.third-party-key.onafric-collection-update')}}" method="POST">
						@csrf
						<div id="countries-form"> 
							<div class="d-flex align-item-center justify-content-between mb-3">
								<h3 >Country List  </h3>     
							</div>
							<div class="country-section mb-3 ms-3" id="collectionCountryAppend"> 
								@if(count($collectionCountries) > 0)
									@foreach($collectionCountries as $key => $collectionCountry)
										<div class="row" id="remove_row">
											<div class="col-md-3">
												<label class="form-label">Country Name</label>
												<select name="country_name[]" id="country_name_{{ $key }}" class="form-control select2" required>
													<option value="">Select Country</option>
													@foreach($countries as $country)
														<option value="{{ $country->nicename }}" {{ $collectionCountry->country_name == $country->nicename ? 'selected' : '' }}>{{ $country->nicename}}</option>
													@endforeach
												</select>
											</div>
											<input type="hidden" name="ids[]" id="id" value="{{ $collectionCountry->id }}">
											<div class="col-md-5">
												<label class="form-label">Channel Name</label>
												<input type="text" class="form-control" name="channels[]" value="{{ collect($collectionCountry->channels)->implode(',')}}">
											</div>
											<div class="col-md-4 d-flex align-items-end gap-3"> 
												@if($key == 0)
													<button type="button" class="btn btn-primary" onclick="addCollectionCountry(this, event)">Add Country</button>  
												@else
													<button type="button" class="btn btn-danger" onclick="removeCollectionCountry(this)">Remove Country</button>  
												@endif
											</div>	
										</div>
									@endforeach
								@else  
									<div class="row" id="remove_row">
										<div class="col-md-3">
											<label class="form-label">Country Name</label>
											<select name="country_name[]" id="country_name" class="form-control select2" required>
												<option value="">Select Country</option>
												@foreach($countries as $country)
												<option value="{{ $country->nicename }}">{{ $country->nicename}}</option>
												@endforeach
											</select>
										</div>
										<input type="hidden" name="ids[]" id="id" value="">
										<div class="col-md-5">
											<label class="form-label">Channel Name</label>
											<input type="text" class="form-control" name="channels[]" value="">
										</div>
										
										<div class="col-md-4 d-flex align-items-end gap-3"> 
											<button type="button" class="btn btn-primary" onclick="addCollectionCountry(this, event)">Add Country</button>  
										</div>
									</div>
								@endif 
							</div>	 
						</div>
						@if (config("permission.onafric_mobile_collection_setting.edit"))
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-success">Save</button>
						</div>
						@endif
					</form>
				</div> 
			</div> 
		</div>
	</div> 
</div>  