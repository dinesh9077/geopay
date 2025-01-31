@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Setting')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">General Setting</h4>
	</div> 
</div>

<div class="row"> 
	<div class="example">
		@php
			$tabs = [
				'general_setting',
				'social_media_setting',
				'aboutus_setting',
				'company_user_limit_setting'
			];

			// Filter only tabs that the user has permission to view
			$visibleTabs = collect($tabs)->filter(fn($tab) => config("permission.$tab.view"));
			
			// Get the first tab's ID for setting the active class dynamically
			$firstTab = $visibleTabs->first(); 
		@endphp

		<ul class="nav nav-tabs nav-tabs-line" id="settingsTabs" role="tablist">
			@if (config("permission.general_setting.view"))
			<li class="nav-item">
				<a class="nav-link {{ $firstTab == 'general_setting' ? 'active' : '' }}" id="general-setting-tab" data-bs-toggle="tab" href="#general-setting" role="tab" aria-controls="general-setting" aria-selected="true">General Setting</a>
			</li>
			@endif
			@if (config("permission.social_media_setting.view"))
			<li class="nav-item">
				<a class="nav-link {{ $firstTab == 'social_media_setting' ? 'active' : '' }}" id="social-line-tab" data-bs-toggle="tab" href="#social-line" role="tab" aria-controls="social-line" aria-selected="false">Social Media Link</a>
			</li>  
			@endif
			@if (config("permission.aboutus_setting.view"))
			<li class="nav-item">
				<a class="nav-link {{ $firstTab == 'aboutus_setting' ? 'active' : '' }}" id="aboutus-line-tab" data-bs-toggle="tab" href="#aboutus-line" role="tab" aria-controls="aboutus-line" aria-selected="false">About Us</a>
			</li>  
			@endif
			@if (config("permission.company_user_limit_setting.view"))
			<li class="nav-item">
				<a class="nav-link {{ $firstTab == 'company_user_limit_setting' ? 'active' : '' }}" id="user-limit-tab" data-bs-toggle="tab" href="#user-limit" role="tab" aria-controls="user-limit" aria-selected="false">Company / User Limit</a>
			</li>  
			@endif
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			@if (config("permission.general_setting.view"))
			<div class="tab-pane fade {{ $firstTab == 'general_setting' ? 'show active' : '' }}" id="general-setting" role="tabpanel" aria-labelledby="general-setting-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="generalForm" action="{{ route('admin.general-setting.update') }}?module_type=general_setting" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Site Name</label>
									<input type="text" class="form-control" id="site_name" name="site_name" autocomplete="off" placeholder="Site Name"  value="{{ config('setting.site_name') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Contact Address</label>
									<input type="text" class="form-control" id="contact_address" name="contact_address" autocomplete="off" placeholder="Contact Address"  value="{{ config('setting.contact_address') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Contact Phone</label>
									<input type="text" class="form-control" id="contact_phone" name="contact_phone" autocomplete="off" placeholder="Contact Phone"  value="{{ config('setting.contact_phone') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Contact Email</label>
									<input type="text" class="form-control" id="contact_email" name="contact_email" autocomplete="off" placeholder="Contact Email"  value="{{ config('setting.contact_email') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Contact Website </label>
									<input type="text" class="form-control" id="contact_website" name="contact_website" autocomplete="off" placeholder="Contact Website"  value="{{ config('setting.contact_website') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputEmail1" class="form-label">Default Currency</label>
									<input type="text" class="form-control" id="default_currency" name="default_currency" maxlength="3" placeholder="Default Currency" value="{{ config('setting.default_currency') }}"> 
								</div>
								 
								<div class="mb-3 col-md-4">
									<label for="exampleInputPassword1" class="form-label">Site Logo</label> 
									<input type="file" name="site_logo" id="site_logo" class="form-control" accept=".jpg,.jpeg,.png,.svg"> 
									<img class="mt-3" src="{{ url('storage/setting', config('setting.site_logo')) }}" style="height:80px; width:80px">
								</div>
								
								<div class="mb-3 col-md-4">
									<label for="exampleInputPassword1" class="form-label">Login Logo</label> 
									<input type="file" name="login_logo" id="login_logo" class="form-control" accept=".jpg,.jpeg,.png,.svg"> 
									<img class="mt-3" src="{{ url('storage/setting', config('setting.login_logo')) }}" style="height:80px; width:80px">
								</div>
								
								<div class="mb-3 col-md-4">
									<label for="exampleInputPassword1" class="form-label">Fevicon Icon</label> 
									<input type="file" name="fevicon_icon" id="fevicon_icon" class="form-control" accept=".jpg,.jpeg,.png,.ico"> 
									<img class="mt-3" src="{{ url('storage/setting', config('setting.fevicon_icon')) }}" style="height:80px; width:80px">
								</div> 
								@if (config("permission.general_setting.edit"))
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div> 
								@endif
							</form> 
						</div>
					</div>
				</div> 
			</div>
			@endif
			@if (config("permission.social_media_setting.view"))
			<div class="tab-pane fade {{ $firstTab == 'social_media_setting' ? 'show active' : '' }}" id="social-line" role="tabpanel" aria-labelledby="social-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="socialForm" action="{{ route('admin.general-setting.update') }}?module_type=social_media_setting" method="post" enctype="multipart/form-data">
								   
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Facebook</label>
									<input type="url" class="form-control" id="social_facebook" name="social_facebook" autocomplete="off" placeholder="Facebook Url"  value="{{ config('setting.social_facebook') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Instagram</label>
									<input type="url" class="form-control" id="social_instagram" name="social_instagram" autocomplete="off" placeholder="Instagram Url"  value="{{ config('setting.social_instagram') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">WhatsApp</label>
									<input type="url" class="form-control" id="social_whatsapp" name="social_whatsapp" autocomplete="off" placeholder="WhatsApp Url"  value="{{ config('setting.social_whatsapp') }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">LinkedIn</label>
									<input type="url" class="form-control" id="social_linkedin" name="social_linkedin" autocomplete="off" placeholder="LinkedIn Url"  value="{{ config('setting.social_linkedin') }}">
								</div>
								@if (config("permission.social_media_setting.edit"))
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
								@endif
							</form> 
						</div>
					</div>
				</div> 
			</div>
			@endif
			@if (config("permission.aboutus_setting.view"))
			<div class="tab-pane fade {{ $firstTab == 'aboutus_setting' ? 'show active' : '' }}" id="aboutus-line" role="tabpanel" aria-labelledby="aboutus-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="aboutUsForm" action="{{ route('admin.general-setting.update') }}?module_type=aboutus_setting" method="post" enctype="multipart/form-data"> 
								<div class="mb-3 col-md-12">
									<label for="exampleInputPassword1" class="form-label">About Us</label> 
									<textarea class="form-control tinymce" name="aboutus" id="aboutus" rows="10">{{ config('setting.aboutus') ?? '' }}</textarea>
								</div> 
								@if (config("permission.aboutus_setting.edit"))
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div> 
								@endif 
							</form> 
						</div>
					</div>
				</div> 
			</div>
			@endif 
			@if (config("permission.company_user_limit_setting.view"))
			<div class="tab-pane fade {{ $firstTab == 'company_user_limit_setting' ? 'show active' : '' }}" id="user-limit" role="tabpanel" aria-labelledby="user-limit-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<h4>Company Limit</h4>
							<hr>
							<form class="forms-sample row" id="companyLimitForm" action="{{ route('admin.third-party-key.update') }}?module_type=company_limit_setting" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Company Add Monthly Limit</label>
									<input type="text" class="form-control" id="company_add_monthly_limit" name="company_add_monthly_limit" autocomplete="off" placeholder="Company Add Monthly Limit"  value="{{ config('setting.company_add_monthly_limit') ?? 0 }}">
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Company Pay Monthly Limit</label>
									<input type="text" class="form-control" id="company_pay_monthly_limit" name="company_pay_monthly_limit" autocomplete="off" placeholder="Company Pay Monthly Limit"  value="{{ config('setting.company_pay_monthly_limit') ?? 0 }}">
								</div> 
								@if (config("permission.company_user_limit_setting.edit"))
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div> 
								@endif
							</form> 
						</div>
					</div>
				</div>   
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<h4>User Limit</h4>
							<hr>
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Plan Name</th>
											<th>Daily Add Limit</th>
											<th>Daily Pay Limit</th>  
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@foreach($userLimits as $userLimit)
										<tr id="plan-row-{{ $userLimit->id }}">
											<td class="plan-name">{{ $userLimit->name }}</td>
											<td class="plan-daily-add-limit">{{ $userLimit->daily_add_limit }}</td>
											<td class="plan-daily-pay-limit">{{ $userLimit->daily_pay_limit }}</td> 
											<td>
												@if (config("permission.company_user_limit_setting.edit"))
													<button class="btn btn-primary btn-sm edit-user-limit" 
														data-id="{{ $userLimit->id }}"
														data-name="{{ $userLimit->name }}"
														data-daily-add-limit="{{ $userLimit->daily_add_limit }}"
														data-daily-pay-limit="{{ $userLimit->daily_pay_limit }}"  
													>Edit</button>
												@endif
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div> 
			</div> 
			@endif
		</div>
	</div>
</div>

<div class="modal fade" id="editUserLimitModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPlanModalLabel">Edit User Limit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userLimitForm" action="{{ route('admin.user-limit.update') }}" method="post">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="plan_name" class="form-label">Plan Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="daily_add_limit" class="form-label">Daily Add Limit</label>
                        <input type="number" id="daily_add_limit" name="daily_add_limit" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="daily_pay_limit" class="form-label">Daily Pay Limit</label>
                        <input type="number" id="daily_pay_limit" name="daily_pay_limit" class="form-control" required>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('js')
<script>
	const tinymceExample = document.querySelector('#aboutus');
	   
	if (tinymceExample) {
		const options = {
			selector: '#aboutus',
			min_height: 350,
			default_text_color: 'red',
			plugins: [
			'advlist', 'autoresize', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'pagebreak',
			'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen',
			],
			toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons | codesample help',
			image_advtab: true, 
			promotion: false,
		};
		
		const theme = localStorage.getItem('theme');
		if (theme === 'dark') {
			options["content_css"] = "dark";
			options["content_style"] = `body{background: ${getComputedStyle(document.documentElement).getPropertyValue('--bs-body-bg')}}`
			} else if (theme === 'light') {
			options["content_css"] = "default";
		}
		
		tinymce.init(options);
	}
	
	$(document).on('click', '.edit-user-limit', function () {
		// Get the latest data from the button's data attributes
		var userLimitId = $(this).data('id');
		var name = $(this).attr('data-name');
		var dailyAddLimit = $(this).attr('data-daily-add-limit');
		var dailyPayLimit = $(this).attr('data-daily-pay-limit');
 
		// Clear and populate modal fields
		var form = $('#userLimitForm');
		form.trigger('reset'); // Clear previous values
		form.find('#id').val(userLimitId);
		form.find('#name').val(name);
		form.find('#daily_add_limit').val(dailyAddLimit);
		form.find('#daily_pay_limit').val(dailyPayLimit);

		// Show the modal
		$('#editUserLimitModal').modal('show');
	});

 
	$('#generalForm, #aboutUsForm, #companyLimitForm, #socialForm').submit(function(event) {
		event.preventDefault(); // Prevent default form submission
		
		var $form = $(this);
		$form.find('button').prop('disabled', true); // Disable submit button to prevent multiple submissions
		
		var formDataInput = {};
		
		// Process all input, textarea, and select fields
		$form.find("input, textarea, select").each(function() {
			var inputName = $(this).attr('name');
			
			if ($(this).is('textarea') && $(this).hasClass('tinymce')) {
				// Get TinyMCE content for textareas
				formDataInput[inputName] = tinymce.get($(this).attr('id')).getContent();
			} else if ($(this).attr('type') !== 'file') {
				// For other inputs, get value directly
				formDataInput[inputName] = $(this).val();
			}
		});
		
		// Encrypt the collected data
		const encrypted_data = encryptData(JSON.stringify(formDataInput));
		
		// Create FormData object for file uploads and encrypted data
		var formData = new FormData();
		formData.append('encrypted_data', encrypted_data);
		formData.append('_token', "{{ csrf_token() }}"); // Add CSRF token for security
		
		// Add file inputs to FormData
		$form.find("input[type='file']").each(function() {
			var inputName = $(this).attr('name');
			var files = $(this)[0].files;
			
			$.each(files, function(index, file) {
				formData.append(inputName, file); // Append files with their input name
			});
		});
		
		// Send AJAX request
		$.ajax({
			type: $form.attr('method'), // Get form method (POST/PUT)
			url: $form.attr('action'),  // Get form action URL
			data: formData,
			processData: false, // Required for FormData
			contentType: false, // Required for FormData
			cache: false,
			dataType: 'json', // Expect JSON response
			success: function(res) {
				$form.find('button').prop('disabled', false); // Re-enable submit button
				$('.error_msg').remove(); // Clear existing error messages
				
				if (res.status === "success") {
					// Success: Show toast message or perform other actions
					toastrMsg(res.status, res.message); 
				} else if (res.status === "validation") {
					// Validation error: Display error messages
					$.each(res.errors, function(key, value) {
						var inputField = $form.find('[name="' + key + '"]'); // Locate input by name
						var errorSpan = $('<span>')
							.addClass('error_msg text-danger') // Add error styling
							.attr('id', key + 'Error') // Unique ID for the error
							.text(value[0]); // Error message from server
						
						// Append error span to parent of the input field
						inputField.parent().append(errorSpan);
					});
				} else {
					// Other errors: Show toast message
					toastrMsg(res.status, res.message);
				}
			},
			error: function(xhr, status, error) {
				// Handle unexpected errors (optional)
				$form.find('button').prop('disabled', false);
				toastrMsg('error', 'An unexpected error occurred. Please try again.');
			}
		});
	});
	
	
	$('#userLimitForm').submit(function(event) {
		event.preventDefault(); // Prevent default form submission
		
		var $form = $(this);
		$form.find('button').prop('disabled', true); // Disable submit button to prevent multiple submissions
		
		var formDataInput = {};
		
		// Process all input, textarea, and select fields
		$form.find("input, textarea, select").each(function() {
			var inputName = $(this).attr('name');
			
			if ($(this).is('textarea') && $(this).hasClass('tinymce')) {
				// Get TinyMCE content for textareas
				formDataInput[inputName] = tinymce.get($(this).attr('id')).getContent();
			} else if ($(this).attr('type') !== 'file') {
				// For other inputs, get value directly
				formDataInput[inputName] = $(this).val();
			}
		});
		
		// Encrypt the collected data
		const encrypted_data = encryptData(JSON.stringify(formDataInput));
		
		// Create FormData object for file uploads and encrypted data
		var formData = new FormData();
		formData.append('encrypted_data', encrypted_data);
		formData.append('_token', "{{ csrf_token() }}"); // Add CSRF token for security
		
		// Add file inputs to FormData
		$form.find("input[type='file']").each(function() {
			var inputName = $(this).attr('name');
			var files = $(this)[0].files;
			
			$.each(files, function(index, file) {
				formData.append(inputName, file); // Append files with their input name
			});
		});
		
		// Send AJAX request
		$.ajax({
			type: $form.attr('method'), // Get form method (POST/PUT)
			url: $form.attr('action'),  // Get form action URL
			data: formData,
			processData: false, // Required for FormData
			contentType: false, // Required for FormData
			cache: false,
			dataType: 'json', // Expect JSON response
			success: function(res) {
				$form.find('button').prop('disabled', false); // Re-enable submit button
				$('.error_msg').remove(); // Clear existing error messages
				
				if (res.status === "success") {
					// Success: Show toast message or perform other actions
					toastrMsg(res.status, res.message);
					
					// Get the plan ID from the form
					const planId = $('#userLimitForm').find('#id').val();

					// Select the table row dynamically
					const row = $(`#plan-row-${planId}`);

					// Decrypt and parse the response
					const response = decryptData(res.response);
					const { name, daily_add_limit, daily_pay_limit } = response.data;

					// Update table row values
					row.find('.plan-name').text(name);
					row.find('.plan-daily-add-limit').text(daily_add_limit);
					row.find('.plan-daily-pay-limit').text(daily_pay_limit);

					// Update data attributes for the edit button
					const editButton = row.find('.edit-user-limit');
					editButton.attr({
						'data-name': name,
						'data-daily-add-limit': daily_add_limit,
						'data-daily-pay-limit': daily_pay_limit
					});
 
					$('#editUserLimitModal').modal('hide');
					
				} else if (res.status === "validation") {
					// Validation error: Display error messages
					$.each(res.errors, function(key, value) {
						var inputField = $form.find('[name="' + key + '"]'); // Locate input by name
						var errorSpan = $('<span>')
							.addClass('error_msg text-danger') // Add error styling
							.attr('id', key + 'Error') // Unique ID for the error
							.text(value[0]); // Error message from server
						
						// Append error span to parent of the input field
						inputField.parent().append(errorSpan);
					});
				} else {
					// Other errors: Show toast message
					toastrMsg(res.status, res.message);
				}
			},
			error: function(xhr, status, error) {
				// Handle unexpected errors (optional)
				$form.find('button').prop('disabled', false);
				toastrMsg('error', 'An unexpected error occurred. Please try again.');
			}
		});
	});
</script>
@endpush				