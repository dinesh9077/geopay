@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Dashboard') 
@section('header_title', 'Dashboard') 
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3">
			<!-- First Row -->
			<div id="carouselExampleSlidesOnly" class="carousel home-banner-carousel slide mb-3" data-bs-ride="carousel">
				<div class="carousel-inner">
					@foreach($banners as $key => $banner)
					<div class="carousel-item {{ $key == 0 ? 'active' : '' }}" data-bs-interval="5000">
						<img src="{{ url('storage/banner', $banner->image) }}" class="img-fluid w-100" alt="">
					</div> 
					@endforeach
				</div>
				<div class="carousel-indicators">
				   @foreach($banners as $key => $banner)
					   <button 
						   type="button" 
						   data-bs-target="#carouselExampleSlidesOnly" 
						   data-bs-slide-to="{{ $key }}" 
						   class="{{ $key == 0 ? 'active' : '' }}" 
						   aria-current="{{ $key == 0 ? 'true' : 'false' }}" 
						   aria-label="Slide {{ $key + 1 }}">
					   </button>
				   @endforeach
			   </div>
			</div>
			<!-- Second Row -->
			<div class="row g-4 g-lg-2 my-1">
				<!-- First Column (6 columns wide) -->
				<div class="col-lg-8 order-2 order-lg-1 border-linear-right">
					<!-- First Row inside First Column -->
					<p class="heading-4 mb-3 text-center">Services</p>
					<div class="row g-3 gx-xxl-5 mb-3 justify-content-center service-section mw-100 mx-auto">
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('add-money') }}"  wire:navigate>
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/plus-circle.svg') }}" alt="Add Money Icon">
									<p class="content-2">Add Money</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('wallet-to-wallet') }}" wire:navigate>
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/wallet.svg') }}" alt="Wallet to Wallet Icon">
									<p class="content-2">Wallet to Wallet</p>
								</div>
							</a>
						</div>
					</div>
					<!-- Second Row inside First Column -->
					<p class="heading-4 mb-3 text-center">Pay Services</p>
					<div class="row g-3 gx-xxl-5 justify-content-between pay-service-section mw-100 mx-auto">
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('transfer-to-mobile-money') }}"  wire:navigate>
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/expense.svg') }}" alt="Transfer to Mobile Money Icon">
									<p class="content-2">Transfer to Mobile Money</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('international-airtime') }}"  wire:navigate>
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/globe.svg') }}" alt="International Airtime Icon">
									<p class="content-2">International Airtime</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto" >
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/bank.svg') }}" alt="Transfer to Mobile Money Icon">
								<p class="content-2">Transfer to Bank</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Second Column (4 columns wide) -->
				<div class="col-lg-4 order-1 order-lg-2">
					<div class="border text-center d-flex flex-column qr-container p-4 h-100 ms-lg-3">
						<span class="content-3">To Pay {{ auth()->user()->first_name. ' ' .auth()->user()->last_name}}</span>
						<span class="content-3 opacity-75">Share your GEOPAY QR Code to receive payments</span> 
						<div class="img-fluid p-2 qr-code">
						<!-- {!! QrCode::size(300)->generate($mobileNumber) !!} -->
							{!! QrCode::generate($mobileNumber) !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
