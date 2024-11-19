@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Wallet to Wallet')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
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
					<input id="amount" type="text" class="form-control form-control-lg default-input mb-3" placeholder="Enter Amount in USD (eg : 100 or eg : 0.0)">
					<input id="amount" type="text" class="form-control form-control-lg default-input mb-3" placeholder="Beneficiary Name">
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
