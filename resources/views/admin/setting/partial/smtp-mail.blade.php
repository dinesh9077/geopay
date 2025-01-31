<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="metaMapForm" action="{{ route('admin.third-party-key.update') }}?module_type=smtp_mail_setting" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail Host</label>
					<input type="text" class="form-control" id="mail_host" name="mail_host" autocomplete="off" placeholder="Mail Host"  value="{{ config('setting.mail_host') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail Port</label>
					<input type="text" class="form-control" id="mail_port" name="mail_port" autocomplete="off" placeholder="Mail Port"  value="{{ config('setting.mail_port') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail Username</label>
					<input type="text" class="form-control" id="mail_username" name="mail_username" autocomplete="off" placeholder="Mail Username"  value="{{ config('setting.mail_username') }}">
				</div>  
				  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail Password</label>
					<input type="text" class="form-control" id="mail_password" name="mail_password" autocomplete="off" placeholder="Mail Password" value="{{ config('setting.mail_password') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail Encryption</label>
					<select class="form-control" id="mail_encryption" name="mail_encryption">
						<option value="tls">TLS</option>
						<option value="ssl">SSL</option>
					</select>
				</div>
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail From Address</label>
					<input type="text" class="form-control" id="mail_from_address" name="mail_from_address" autocomplete="off" placeholder="Mail From Address" value="{{ config('setting.mail_from_address') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Mail From Name</label>
					<input type="text" class="form-control" id="mail_from_name" name="mail_from_name" autocomplete="off" placeholder="Mail From Name" value="{{ config('setting.mail_from_name') }}">
				</div> 
				@if (config("permission.smtp_mail_setting.edit"))
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				@endif
			</form> 
		</div>
	</div>
</div> 