<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="onafricForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_mobile_setting" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Sync Api</label>
					<input type="url" class="form-control" id="onafric_sync_url" name="onafric_sync_url" autocomplete="off" placeholder="Sync Api Url"  value="{{ config('setting.onafric_sync_url') }}">
				</div>
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Async Api</label>
					<input type="url" class="form-control" id="onafric_async_callservice" name="onafric_async_callservice" autocomplete="off" placeholder="Async Api"  value="{{ config('setting.onafric_async_callservice') }}">
				</div>
				  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Corporate Code</label>
					<input type="text" class="form-control" id="onafric_corporate" name="onafric_corporate" autocomplete="off" placeholder="Corporate Code"  value="{{ config('setting.onafric_corporate') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Password</label>
					<input type="text" class="form-control" id="onafric_password" name="onafric_password" autocomplete="off" placeholder="Password" value="{{ config('setting.onafric_password') }}">
				</div>  
				 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Unique Key</label>
					<input type="text" class="form-control" id="onafric_unique_key" name="onafric_unique_key" autocomplete="off" placeholder="Unique Key" value="{{ config('setting.onafric_unique_key') }}">
				</div>    
				@if (config("permission.onafric_mobile_setting.edit")) 
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				@endif
			</form>
			<hr>
			<div class="row mt-3" id="onafricMobileView"> 
			</div> 
		</div>
	</div>
</div> 