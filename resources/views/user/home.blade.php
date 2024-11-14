@extends('user.layouts.app')
@section('title', env('APP_NAME').' - Dashboard')
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
			<div class="row g-2">
				<!-- First Column (6 columns wide) -->
				<div class="col-md-7">
					<!-- First Row inside First Column -->
					<p class="service-title">Services</p>
					<div class="row g-5 mb-2 justify-content-center">
						<div class="col-md-4 col-sm-3">
							<div class="text-center h-100 align-content-center service-box">
								<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
									<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
									<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
								</svg>
								<p class="service-title">Add Money</p>
							</div>
						</div>
						<div class="col-md-4 col-sm-3">
							<div class="text-center h-100 align-content-center service-box">
								<img src="{{ asset('assets/image/dashboard/wallet-to-wallet.svg') }}" alt="wallet">
								<p class="service-title">Wallet to Wallet</p>
							</div>
						</div>
					</div>
					<!-- Second Row inside First Column -->
					<p class="service-title text-center">Pay Services</p>
					<div class="row g-5 justify-content-between">
						<div class="col-md-4 col-sm-3">
							<div class="text-center h-100 align-content-center service-box">
								<img src="{{ asset('assets/image/dashboard/mobile-money.svg') }}" alt="wallet">
								<p class="service-title">Transfer to Mobile Money</p>
							</div>
						</div>
						<div class="col-md-4 col-sm-3">
							<div class="text-center h-100 align-content-center service-box">
								<img src="{{ asset('assets/image/dashboard/airtime.svg') }}" alt="wallet">
								<p class="service-title">International Airtime</p>
							</div>
						</div>
						<div class="col-md-4 col-sm-3">
							<div class="text-center h-100 align-content-center service-box">
								<img src="{{ asset('assets/image/dashboard/bank.svg') }}" alt="wallet">
								<p class="service-title">Transfer to Bank</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Second Column (4 columns wide) -->
				<div class="col-md-5">
					<div class="border text-center d-flex flex-column qr-container">
						<span class="font-text-13">To Pay Pritesh Salla</span>
						<span class="qr-caption">Share your GEOPAY QR Code to receive payments</span>
						<!-- <span class="mb-3">Share your GEOPAY QR Code to receive payments</span> -->
						<img src="{{ asset('assets/image/dashboard/QRCode.png') }}" class="img-fluid p-2 mw-100" alt="Centered Image">
					</div>
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		<div class="col-lg-3">
			<div class="p-3 border text-center rounded-3 quick-transfer-container">
				<!-- First Row: Avatar Selection (Screenshot Design) -->
				<div class="d-flex justify-content-around align-items-center quick-transfer-sub-container">
					<div class="text-center">
						<img src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Livia Bator" class="avatar-sm rounded-circle">
						<p class="text-white mt-1">Livia Bator</p>
					</div>
					<div class="text-center">
						<img src="{{ asset('assets/image/avatar-2.jpg') }}" alt="Randy Press" class="avatar-sm rounded-circle">
						<p class="text-white mt-1">Jack Roy</p>
					</div>
					<!-- Right Arrow -->
					<!-- <div class="d-flex justify-content-center align-items-center" style="background-color: white; border-radius: 50%; width: 30px; height: 30px;">
						<i class="fa fa-arrow-right" style="color: #1a2b4e;"></i>
					</div> -->
				</div>
				<!-- Second Row: Input Group for Amount -->
				<div class="d-flex align-items-center">
					<!-- <span style="font-size: 10px; color: white; white-space: nowrap; margin-right: 10px;">Write Amount</span> -->
					<div class="input-group rounded-pill bg-light pe-0 mt-4">
						<input placeholder="Write Amount" type="text" class="number-input form-control form-control-lg bg-transparent border-0">
						<button type="button" class="btn btn-secondary rounded-pill px-3">
							Send <!--<i class="fab fa-telegram-plane font-size-20"></i> -->
						</button>
					</div>
				</div>
			</div>
			<div class="border rounded px-3 py-2 mt-3">
				<b class="service-title">Recent Transactions</b>
				<div class="d-flex justify-content-between align-items-center my-3">
					<div class="d-flex gap-lg-2 gap-md-3">
						<img src="{{ asset('assets/image/dashboard/card-sign.svg') }}" class="transaction-icon"/>
						<div class="font-text-13">
							<span>Payment to John</span><br>
							<span class="transaction-date">12 Nov, 2024</span>
						</div>
					</div>
					<span class="font-text-13 text-danger">$100</span>
				</div>
				<div class="d-flex justify-content-between align-items-center my-3">
					<div class="d-flex gap-lg-2 gap-md-3">
						<img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
						<div class="font-text-13">
							<span>Payment to John</span><br>
							<span class="transaction-date">12 Nov, 2024</span>
						</div>
					</div>
					<span class="font-text-13 trans-green">$2000</span>
				</div>
				<div class="d-flex justify-content-between align-items-center my-3">
					<div class="d-flex gap-lg-2 gap-md-3">
						<img src="{{ asset('assets/image/dashboard/paypal-sign.svg') }}" class="transaction-icon"/>
						<div class="font-text-13">
							<span>Payment to John</span><br>
							<span class="transaction-date">12 Nov, 2024</span>
						</div>
					</div>
					<span class="font-text-13 text-danger">$100</span>
				</div>
				<div class="d-flex justify-content-between align-items-center my-3">
					<div class="d-flex gap-lg-2 gap-md-3">
						<img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
						<div class="font-text-13">
							<span>Payment to John</span><br>
							<span class="transaction-date">12 Nov, 2024</span>
						</div>
					</div>
					<span class="font-text-13 trans-green">$2000</span>
				</div>
				<div class="d-flex justify-content-between align-items-center my-3">
					<div class="d-flex gap-lg-2 gap-md-3">
						<img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
						<div class="font-text-13">
							<span>Payment to John</span><br>
							<span class="transaction-date">12 Nov, 2024</span>
						</div>
					</div>
					<span class="font-text-13 trans-green">$2000</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
