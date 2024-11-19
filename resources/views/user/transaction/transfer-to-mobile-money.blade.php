@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transfer To Mobile Money')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<div class="d-flex justify-content-end">
				@include('user.transaction.modal.add-beneficiary-details-modal')
			</div>

			<form id="profileForm" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1">
					<select id="country_id1" name="country_id" class="form-control form-control-lg default-input mb-3" >
						<option value="" selected disabled>Select Country*</option>
						<option value="1">Channel 1</option>
						<option value="2">Channel 2</option>
						<option value="3">Channel 3</option>
					</select>
					<select id="channel_id" name="channel_id" class="form-control form-control-lg default-input mb-3" >
						<option value="" selected disabled>Select Beneficiary*</option>
						<option value="1">Channel 1</option>
						<option value="2">Channel 2</option>
						<option value="3">Channel 3</option>
					</select>
					<div class=" mb-3">
						<input id="amount" type="text" class="form-control form-control-lg default-input mb-3" placeholder="Enter Amount in USD (eg : 100 or eg : 0.0)">
						<div class="w-100 text-start mb-3 p-2 rounded-2 border g-2">
							<div class="w-100 row m-auto">
								<div class="col-6 col-md-4">
									<span class="content-3 mb-0 text-dark fw-semibold text-nowrap" >Fee(USD) <div class="text-muted fw-normal">0</div></span >
								</div>
								<div class="col-6 col-md-4">
									<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">Net Amount In USD <div class="text-muted fw-normal">100</div></span >
								</div>
								<div class="text-md-end col-6 col-md-4">
									<span class="content-3 mb-0 text-dark fw-semibold text-nowrap"> Debit In (XOF) <div class="text-muted fw-normal">65,500</div></span >
								</div>
							</div>
						</div>
					</div>

					<textarea name="address" id="address" class="form-control form-control-lg default-input mb-3" id="" placeholder="Account Description"></textarea>

				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
					<p class="content-3 text-muted col-md-7"> Once a new amount is entered or payment method is changed, the exchange rate and fees will be recalculated. </p>
					<button type="button" class="btn btn-lg btn-primary rounded-2 text-nowrap" id="addMoney">Add Money</button>
				</div>
			</form>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
