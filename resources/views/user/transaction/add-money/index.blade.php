@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Add Money')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<ul class="nav nav-pills mb-4 w-100" id="pills-tab" role="tablist">
				<li class="nav-item col-12 col-md-4" role="presentation">
					<button class="nav-link w-100 px-4 active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Visa/Master AMEX Card USD(MCB)</button>
				</li>
				<li class="nav-item col-12 col-md-4" role="presentation">
					<button class="nav-link px-4 w-100" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Mobile Money</button>
				</li>
				<li class="nav-item col-12 col-md-4" role="presentation">
					<button class="nav-link px-4 w-100" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Nigeria- Bank Debit</button>
				</li>
			</ul>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
					@include('user.transaction.add-money.visa-master-card')
				</div>
				<div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
					@include('user.transaction.add-money.mobile-money')
				</div>
				<div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
					@include('user.transaction.add-money.nigeria-bank')
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
