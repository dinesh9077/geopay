<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="smsPlusForm" action="{{ route('admin.third-party-key.update') }}?module_type=smsplus_setting" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">SMS Host</label>
					<input type="url" class="form-control" id="sms_host" name="sms_host" autocomplete="off" placeholder="SMS Host"  value="{{ config('setting.sms_host') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">SMS Username</label>
					<input type="text" class="form-control" id="sms_username" name="sms_username" autocomplete="off" placeholder="SMS Username"  value="{{ config('setting.sms_username') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">SMS Password</label>
					<input type="text" class="form-control" id="sms_password" name="sms_password" autocomplete="off" placeholder="SMS Password"  value="{{ config('setting.sms_password') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">SMS Sender</label>
					<input type="text" class="form-control" id="sms_sender" name="sms_sender" autocomplete="off" placeholder="SMS Sender"  value="{{ config('setting.sms_sender') }}">
				</div>  
				@if (config("permission.smsplus_setting.edit"))
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
				@endif
			</form> 
		</div>
	</div>
</div>