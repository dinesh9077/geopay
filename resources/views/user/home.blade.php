@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Dashboard')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3">
			<!-- First Row -->
			<div id="carouselExampleSlidesOnly" class="carousel home-banner-carousel slide mb-3" data-bs-ride="carousel">
				<div class="carousel-inner">
					<div class="carousel-item active" data-bs-interval="5000">
						<img src="{{ asset('assets/image/dashboard/carousel-img.jfif') }}" class="img-fluid w-100" alt="">
					</div>
					<div class="carousel-item" data-bs-interval="5000">
						<img src="{{ asset('assets/image/dashboard/carousel-img.jfif') }}" class="img-fluid w-100" alt="">
					</div>
					<div class="carousel-item" data-bs-interval="5000">
						<img src="{{ asset('assets/image/dashboard/carousel-img.jfif') }}" class="img-fluid w-100" alt="">
					</div>
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
							<a class="text-primary" href="{{ route('add-money') }}">
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/plus-circle.svg') }}" alt="Add Money Icon">
									<p class="content-2 fw-semibold">Add Money</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('wallet-to-wallet') }}">
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/wallet.svg') }}" alt="Wallet to Wallet Icon">
									<p class="content-2 fw-semibold">Wallet to Wallet</p>
								</div>
							</a>
						</div>
					</div>
					<!-- Second Row inside First Column -->
					<p class="heading-4 mb-3 text-center">Pay Services</p>
					<div class="row g-3 gx-xxl-5 justify-content-between pay-service-section mw-100 mx-auto">
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('transfer-to-mobile-money') }}">
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/expense.svg') }}" alt="Transfer to Mobile Money Icon">
									<p class="content-2 fw-semibold">Transfer to Mobile Money</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<a class="text-primary" href="{{ route('international-airtime') }}">
								<div class="text-center h-100 align-content-center service-box m-auto">
									<img class="in-svg mb-3" src="{{ asset('assets/image/icons/globe.svg') }}" alt="International Airtime Icon">
									<p class="content-2 fw-semibold">International Airtime</p>
								</div>
							</a>
						</div>
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/bank.svg') }}" alt="Transfer to Mobile Money Icon">
								<p class="content-2 fw-semibold">Transfer to Bank</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Second Column (4 columns wide) -->
				<div class="col-lg-4 order-1 order-lg-2">
					<div class="border text-center d-flex flex-column qr-container p-4 h-100 ms-lg-3">
						<span class="content-3">To Pay Pritesh Salla</span>
						<span class="content-3 fw-semibold">Share your GEOPAY QR Code to receive payments</span>
						<!-- <span class="mb-3">Share your GEOPAY QR Code to receive payments</span> -->
						<img src="{{ asset('assets/image/dashboard/QRCode.png') }}" class="img-fluid p-2 qr-code" alt="Centered Image">
					</div>
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
