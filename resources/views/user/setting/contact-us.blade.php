<div class="grid-container mt-md-4">
	<div class="col-sm-12">
		<div class="d-flex flex-column position-relative mb-3">
			<div class="d-flex align-items-center justify-content-between">
				<h3 class="text-dark content-1 fw-semibold d-flex align-items-center">
					<img class="in-svg me-1" src="{{ asset('assets/image/icons/location-pin.svg') }}" alt="">
					<span>{{ config('setting.site_name') }}</span>
				</h3>
				<div class="d-flex align-items-center gap-3 order-1 order-md-2">
					<div class="btn btn-soft-success rounded-circle d-flex align-items-center justify-content-center p-1 p-md-2" >
						<a target="_blank" href="{{ config('setting.social_whatsapp') }}"><img class="in-svg" src="{{ asset('assets/image/icons/whatsapp-icon.svg') }}" alt=""></a>
					</div>
					<div class="btn btn-soft-danger rounded-circle d-flex align-items-center justify-content-center p-1 p-md-2" >
						<a target="_blank" href="{{ config('setting.social_instagram') }}"><img class="in-svg" src="{{ asset('assets/image/icons/insta-icon.svg') }}" alt=""></a>  
					</div>
					<div class="btn btn-soft-info rounded-circle d-flex align-items-center justify-content-center p-1 p-md-2" >
						<a target="_blank" href="{{ config('setting.social_facebook') }}"><img class="in-svg" src="{{ asset('assets/image/icons/facebook-icon.svg') }}" alt=""> </a> 
					</div>
					<div class="btn btn-soft-info rounded-circle d-flex align-items-center justify-content-center p-1 p-md-2" >
						<a target="_blank" href="{{ config('setting.social_linkedin') }}"><img class="in-svg" src="{{ asset('assets/image/icons/linkedin.svg') }}" alt=""> </a> 
					</div>
				</div>
			</div>
			<p class="mt-2 content-3 col-lg-4 text-muted">{{ config('setting.contact_address') }}</p>
			<div class="gap-2 w-100 m-auto row mt-3">
				<div class="d-flex align-items-center text-start text-nowrap mb-1 p-2 px-3 rounded-2 border border-dark border-opacity-25 col" >
					<img class="in-svg me-1" src="{{ asset('assets/image/icons/phone-icon.svg') }}" alt="">
					<h6 class="fw-bold mb-0 me-1 text-dark  content-3"> Call Support : </h6>
					<h6 class="m-0  content-3 text-muted">{{ config('setting.contact_phone') }}</h6>
				</div>
				<div class="d-flex align-items-center text-start text-nowrap mb-1 p-2 px-3 rounded-2 border border-dark border-opacity-25 col" >
					<img class="in-svg me-1" src="{{ asset('assets/image/icons/web-icon.svg') }}" alt="">
					<h6 class="fw-bold mb-0 me-1 text-dark  content-3">Website :</h6>
					<h6 class="m-0  content-3 text-muted">{{ config('setting.contact_website') }}</h6>
				</div>
				<div class="d-flex align-items-center text-start text-nowrap mb-1 p-2 px-3 rounded-2 border border-dark border-opacity-25 col" >
					<img class="in-svg me-1" src="{{ asset('assets/image/icons/email-icon.svg') }}" alt="">
					<h6 class="fw-bold mb-0 me-1 text-dark  content-3">Email :</h6>
					<h6 class="m-0  content-3 text-muted">{{ config('setting.contact_email') }}</h6>
				</div>
			</div>
		</div>
	</div>
</div>
