@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Setting')
@section('header_title', 'Setting')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 setting-section">
			<!-- Tab Navigation -->
			<ul class="nav nav-underline px-lg-2 px-xxl-4 gap-2 justify-content-between" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab" aria-controls="edit-profile" aria-selected="true">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">Edit Profile</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/user-edit.svg') }}" alt="">
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">Basic Info</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/user-edit.svg') }}" alt="">
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab" aria-controls="change-password" aria-selected="false">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">Change Password</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/change-pass.svg') }}" alt="">	
					</button>
				</li>
				<!--<li class="nav-item" role="presentation">
					<button class="nav-link" id="referral-code-tab" data-bs-toggle="tab" data-bs-target="#referral-code" type="button" role="tab" aria-controls="referral-code" aria-selected="false">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">Referral Code</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/referral-icon.svg') }}" alt="">	
					</button>
				</li>-->
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab" aria-controls="faq" aria-selected="false">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">FAQ</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/faq-icon.svg') }}" alt="">	
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="about-us-tab" data-bs-toggle="tab" data-bs-target="#about-us" type="button" role="tab" aria-controls="about-us" aria-selected="false">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">About Us</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/about-us-icon.svg') }}" alt="">	
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="contact-us-tab" data-bs-toggle="tab" data-bs-target="#contact-us" type="button" role="tab" aria-controls="contact-us" aria-selected="false">
						<span class="d-none d-lg-block py-1 px-3 px-xxl-4">Contact Us</span>
						<img class="in-svg d-lg-none mx-2" src="{{ asset('assets/image/icons/contact-us-icon.svg') }}" alt="">	
					</button>
				</li>
			</ul>

			<!-- Tab Content -->
			<div class="tab-content mt-3" id="myTabContent">
				<!-- Edit Profile -->
				<div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">User Profile</h2>
					@include('user.setting.edit-profile')
				</div>
				
				<!-- Basic Info -->
				<div class="tab-pane fade" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">Basic Information</h2>
					@include('user.setting.basic-info')
				</div>
				
				<!-- Change Password -->
				<div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">Change Password</h2>
					@include('user.setting.change-password')
				</div>

				<!-- Referral Code 
				<div class="tab-pane fade" id="referral-code" role="tabpanel" aria-labelledby="referral-code-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">Referral Code</h2>
					@include('user.setting.referral-code')
				</div>-->

				<!-- FAQ -->
				<div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="faq-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">Frequently Asked Question</h2>
					@include('user.setting.faq')
				</div>

				<!-- About Us -->
				<div class="tab-pane fade" id="about-us" role="tabpanel" aria-labelledby="about-us-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">About Us</h2>
					@include('user.setting.about-us')
				</div>

				<!-- Contact Us -->
				<div class="tab-pane fade" id="contact-us" role="tabpanel" aria-labelledby="contact-us-tab">
					<h2 class="text-center text-secondary d-lg-none heading-5">Contact Us</h2>
					@include('user.setting.contact-us')
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
 
