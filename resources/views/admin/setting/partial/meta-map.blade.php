<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="metaMapForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Meta Host</label>
					<input type="url" class="form-control" id="meta_host" name="meta_host" autocomplete="off" placeholder="Meta Host"  value="{{ config('setting.meta_host') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Meta Verification Api Key</label>
					<input type="text" class="form-control" id="meta_verification_api_key" name="meta_verification_api_key" autocomplete="off" placeholder="Meta Verification Api Key"  value="{{ config('setting.meta_verification_api_key') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Meta Verification Flow Id</label>
					<input type="text" class="form-control" id="meta_verification_flow_id" name="meta_verification_flow_id" autocomplete="off" placeholder="Meta Verification Flow Id"  value="{{ config('setting.meta_verification_flow_id') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Meta Verification Secret</label>
					<input type="text" class="form-control" id="meta_verification_secret" name="meta_verification_secret" autocomplete="off" placeholder="Meta Verification Secret"  value="{{ config('setting.meta_verification_secret') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Meta Bearer Token</label>
					<input type="text" class="form-control" id="meta_bearer" name="meta_bearer" autocomplete="off" placeholder="Meta Bearer Token" value="{{ config('setting.meta_bearer') }}">
				</div>  
				
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
			</form> 
		</div>
	</div>
</div> 