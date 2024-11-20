@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transfer To Mobile Money')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 international-airtime-section">
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
					<div class="d-flex align-items-center gap-2">
						<input id="mobile-number" type="number" class="form-control form-control-lg default-input mobile-number mb-3 px-2" style="max-width: 65px;" placeholder="+91" />
						<input id="mobile-number" type="number" class="form-control form-control-lg default-input mobile-number mb-3" placeholder="Enter your mobile number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;"/>
					</div>
					<select id="channel_id" name="channel_id" class="form-control form-control-lg default-input mb-3" >
						<option value="" selected disabled>Select Operator*</option>
						<option value="1">Channel 1</option>
						<option value="2">Channel 2</option>
						<option value="3">Channel 3</option>
					</select>
					<div class="plan-tabs">
						<ul class="nav nav-pills" id="pills-tab" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link content-3 active" id="top-plans-tab" data-bs-toggle="pill" data-bs-target="#top-plans" type="button" role="tab" aria-controls="top-plans" aria-selected="true">Top Plans</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link content-3" id="data-plans-tab" data-bs-toggle="pill" data-bs-target="#data-plans" type="button" role="tab" aria-controls="data-plans" aria-selected="false">Data</button>
							</li>
						</ul>

						<div class="tab-content" id="pills-tabContent">
							<!-- Top Plans Tab -->
							<div class="tab-pane fade show active" id="top-plans" role="tabpanel" aria-labelledby="top-plans-tab">
								<div class="d-flex align-items-stretch overflow-auto gap-2 plan-container mb-3">
									<!-- Card 1 -->
									<div class="border position-relative rounded-3 card plan d-flex flex-column mb-1" onclick="selectCard(this)">
										<div class="card-body d-flex flex-column">
											<input id="checkbox-1" name="checkbox" type="checkbox" class="position-absolute top-0 end-0 m-2 form-check-input" />
											<div class="card-title">16.31 USD</div>
											<p class="mb-0 text-muted content-3 text-muted">Topup Plan - 1000 INR</p>
										</div>
									</div>
									<!-- Card 2 -->
									<div class="border position-relative rounded-3 card plan d-flex flex-column mb-1" onclick="selectCard(this)">
										<div class="card-body d-flex flex-column">
											<input id="checkbox-2" name="checkbox" type="checkbox" class="position-absolute top-0 end-0 m-2 form-check-input" />
											<div class="card-title">20.50 USD</div>
											<p class="mb-0 text-muted content-3 text-muted">Topup Plan - 2000 INR</p>
										</div>
									</div>
								</div>
							</div>

							<!-- Data Plans Tab -->
							<div class="tab-pane fade" id="data-plans" role="tabpanel" aria-labelledby="data-plans-tab">
								<div class="d-flex align-items-stretch overflow-auto gap-2 plan-container mb-3">
									<!-- Card 3 -->
									<div class="border position-relative rounded-3 card plan d-flex flex-column mb-1" onclick="selectCard(this)">
										<div class="card-body d-flex flex-column">
											<input id="checkbox-3" name="checkbox" type="checkbox" class="position-absolute top-0 end-0 m-2 form-check-input" />
											<div class="card-title">30.75 USD</div>
											<p class="mb-0 text-muted content-3 text-muted">Data Plan - 3GB</p>
										</div>
									</div>
									<!-- Card 4 -->
									<div class="border position-relative rounded-3 card plan d-flex flex-column mb-1" onclick="selectCard(this)">
										<div class="card-body d-flex flex-column">
											<input id="checkbox-4" name="checkbox" type="checkbox" class="position-absolute top-0 end-0 m-2 form-check-input" />
											<div class="card-title">50.00 USD</div>
											<p class="mb-0 text-muted content-3 text-muted">Data Plan - 5GB</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<input id="amount" type="text" class="form-control form-control-lg default-input mb-3" placeholder="Beneficiary Name">
					<textarea name="address" id="address" class="form-control form-control-lg default-input mb-3" id="" placeholder="Account Description"></textarea>
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-end align-items-start gap-2">
					<!-- <p class="content-3 text-muted col-md-7"> Once a new amount is entered or payment method is changed, the exchange rate and fees will be recalculated. </p> -->
					<button type="button" class="btn btn-lg btn-primary rounded-2 text-nowrap" id="addMoney">Add Money</button>
				</div>
			</form>

			<div>
				<p class="content-2 fw-semibold mb-2">My Recharge & Bills</p>
				<div class="w-100 text-start mb-1 p-2 rounded-2 border g-2">
					<label class="content-3 text-dark fw-semibold text-nowrap border-bottom w-100 pb-1">Jio</label>
					<div class="d-flex content-3 justify-content-between align-items-center">
						<div class="w-100 row pt-2">
							<div class="col-6 col-md-3">
								<span class="mb-0 text-dark fw-semibold text-nowrap">Name: <span class="text-muted fw-normal">Tejash Sharma</span></span>
							</div>
							<div class="col-6 col-md-3">
								<span class="mb-0 text-dark fw-semibold text-nowrap">Date: <span class="text-muted fw-normal">25/10/2024</span></span>
							</div>
							<div class="col-6 col-md-3">
								<span class="mb-0 text-dark fw-semibold text-nowrap">Mobile: <span class="text-muted fw-normal">+919874563210</span></span>
							</div>
							<div class="col-6 col-md-3">
								<span class="mb-0 text-dark fw-semibold text-nowrap">Amount: <span class="text-muted fw-normal">16.31 USD</span></span>
							</div>
						</div>
						<img class="in-svg btn btn-secondary p-1 rounded-circle mt-2" src="{{ asset('assets/image/icons/repeate-icon.svg') }}" alt="">
					</div>
				</div>
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection


<script>
    function selectCard(card) {
        // Uncheck all checkboxes globally across tabs
        document.querySelectorAll('.plan input[type="checkbox"]').forEach(input => input.checked = false);

        // Check the checkbox inside the clicked card
        const checkbox = card.querySelector('.plan input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = true;
        }
    }
</script>