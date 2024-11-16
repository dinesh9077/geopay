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
			<div class="row g-2">
				<!-- First Column (6 columns wide) -->
				<div class="col-lg-8 order-2 order-lg-1">
					<!-- First Row inside First Column -->
					<p class="heading-4 mb-2 text-center">Services</p>
					<div class="row g-3 gx-xxl-5 mb-3 justify-content-center service-section mw-100 mx-auto">
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/plus-circle.svg') }}" alt="Add Money Icon">
								<p class="content-2">Add Money</p>
							</div>
						</div>
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/wallet.svg') }}" alt="Wallet to Wallet Icon">
								<p class="content-2">Wallet to Wallet</p>
							</div>
						</div>
					</div>
					<!-- Second Row inside First Column -->
					<p class="heading-4 mb-2 text-center">Pay Services</p>
					<div class="row g-3 gx-xxl-5 justify-content-between pay-service-section mw-100 mx-auto">
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/expense.svg') }}" alt="Transfer to Mobile Money Icon">
								<p class="content-2">Transfer to Mobile Money</p>
							</div>
						</div>
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/globe.svg') }}" alt="International Airtime Icon">
								<p class="content-2">International Airtime</p>
							</div>
						</div>
						<div class="col-6 col-md-4">
							<div class="text-center h-100 align-content-center service-box m-auto">
								<img class="in-svg mb-3" src="{{ asset('assets/image/icons/bank.svg') }}" alt="Transfer to Mobile Money Icon">
								<p class="content-2">Transfer to Bank</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Second Column (4 columns wide) -->
				<div class="col-lg-4 order-1 order-lg-2">
					<div class="border text-center d-flex flex-column qr-container">
						<span class="font-text-13">To Pay Pritesh Salla</span>
						<span class="">Share your GEOPAY QR Code to receive payments</span>
						<!-- <span class="mb-3">Share your GEOPAY QR Code to receive payments</span> -->
						<img src="{{ asset('assets/image/dashboard/QRCode.png') }}" class="img-fluid p-2 mw-100 qr-code" alt="Centered Image">
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
				<b class="">Recent Transactions</b>
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
