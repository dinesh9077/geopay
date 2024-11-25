@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transfer To Bank')
@section('header_title', 'Transfer To Bank')
@section('content')
 
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<form id="walletToWalletForm" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1 row">
					<div class="col-12 mb-3"> 
						<select id="country_id" name="country_id" class="form-control form-control-lg default-input">
							<option>Select Country</option> <!-- Placeholder option -->
						</select>
					</div>
					<div class="col-12 mb-3"> 
						<select id="country_id" name="country_id" class="form-control form-control-lg default-input">
							<option>Select Beneficiery</option> <!-- Placeholder option -->
						</select>
					</div>
					<div class="col-12 mb-3">
						<input id="amount" name="amount" type="text" class="form-control form-control-lg default-input" placeholder="Enter Amount in USD (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>
					<div class="col-12 mb-3">
						<textarea name="notes" id="notes" class="form-control form-control-lg default-input" id="" placeholder="Account Description"></textarea>
					</div> 
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2"> 
					<button type="submit" class="btn btn-lg btn-primary rounded-2 text-nowrap">Pay Money</button>
				</div>
			</form>
			<button class="btn btn-lg btn-primary mt-3 rounded-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#confirmBeneficiary">Confirm Beneficiary</button>
		</div>   
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>

<!-- Modal Confirm Beneficiary -->
<div class="modal fade" id="confirmBeneficiary" tabindex="-1" aria-labelledby="confirmBeneficiaryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title heading-5 fw-normal" id="confirmBeneficiaryLabel">Confirm Beneficiary Detail</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="mb-2 col-sm-12">
            <label class="content-3 mb-1">Service Name</label>
            <h6 class="content-3 text-secondary">Transfer to mobile money</h6>
          </div>
          <div class="mb-2 col-sm-12">
            <label class="content-3 mb-1">Country Name</label>
            <h6 class="content-3 text-secondary">Ivory Coast (CI)</h6>
          </div>
          <div class="mb-2 col-md-6">
            <label class="content-3 mb-1">Operator Name</label>
            <h6 class="content-3 text-secondary">Orange</h6>
          </div>
          <div class="mb-2 col-md-6">
            <label class="content-3 mb-1">Mobile No.</label>
            <h6 class="content-3 text-secondary">2250264984581</h6>
          </div>
          <div class="mb-2 col-md-6">
            <label class="content-3 mb-1">First Name</label>
            <h6 class="content-3 text-secondary"></h6>
          </div>
          <div class="mb-2 col-md-6">
            <label class="content-3 mb-1">Last Name</label>
            <h6 class="content-3 text-secondary"></h6>
          </div>
          <div class="col-sm-12">
            <label class="content-3 mb-1">Email</label>
            <h6 class="content-3 text-secondary">pritesh@gmail.com</h6>
          </div>
        </div>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn content-3 btn-primary" data-bs-dismiss="modal">Confirm</button>
        <button type="button" class="btn content-3 btn-secondary" data-bs-toggle="modal" data-bs-target="#editBeneficiary">Edit</button>
        <button type="button" class="btn content-3 btn-danger opacity-75" data-bs-toggle="modal" data-bs-target="#deleteBeneficiary">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Beneficiary -->
<div class="modal fade" id="editBeneficiary" tabindex="-1" aria-labelledby="editBeneficiaryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title heading-5 fw-normal" id="editBeneficiaryLabel">Confirm Beneficiary Detail</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="mb-3 col-sm-12">
				<label class="content-3 mb-0">Service Name</label>
				<input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="Artisanal kale" />
			</div>
			<div class="mb-3 col-sm-12">
				<label class="content-3 mb-0">Country Name</label>
				<select id="country_id" name="country_id" class="form-control form-control-sm border-0 bg-light content-3">
					<option>Select Country</option> <!-- Placeholder option -->
				</select>
			</div>
			<div class="mb-3 col-sm-6">
				<label class="content-3 mb-0">Operator Name</label>
				<input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="Artisanal kale" />
			</div>
			<div class="mb-3 col-sm-6">
				<label class="content-3 mb-0">Mobile No.</label>
				<input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="+27 50264984581" />
			</div>
			<div class="mb-3 col-sm-6">
				<label class="content-3 mb-0">First Name</label>
				<input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="Pritesh" />
			</div>
			<div class="mb-3 col-sm-6">
				<label class="content-3 mb-0">Last Name</label>
				<input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="Salla" />
			</div>
			<div class="mb-3 col-sm-12">
				<label class="content-3 mb-0">Email</label>
				<input type="email" class="form-control form-control-sm border-0 border-bottom rounded-0 text-secondary content-3" value="pritesh@gmail.com" />
			</div>
		</div>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn content-3 btn-secondary" data-bs-dismiss="modal">Cancel</button>
		<button type="button" class="btn content-3 btn-primary" data-bs-dismiss="modal">Update</button>
      </div>
    </div>
  </div>
</div>


<!-- Delete Edit Beneficiary -->
<div class="modal fade" id="deleteBeneficiary" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="d-flex justify-content-center align-items-center">
			<!-- <img class="in-svg" src="{{ asset('assets/image/icons/setting.svg') }}" alt=""> -->
				<img src="{{ asset('assets/image/icons/delete-confirmation.gif') }}" width="80" height="80" class="modal-logo p-1 border border-2 border-danger object-fit-cover" style="border-color: #f46a6a !important;">
			</div>
				<!-- Modal Header -->
			<div class="text-end m-2">
				<button type="button" class="content-4 btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body p-4 pt-0">
				<form id="deleteBeneficiaryForm">
					<h6 class="content-2 text-center text-danger mb-2">Are you sure</h6>
					<h6 class="content-4 text-center text-muted mb-3">You want to delete the beneficiary ?</h6>
					<div class="text-center d-flex align-items-center gap-2">
						<button type="submit" class="btn content-3 w-100 btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn content-3 w-100 btn-danger opacity-75" data-bs-dismiss="modal">Delete</button>
					</div>
				</form>
			</div>                                                    
		</div>
	</div>
</div>  

@endsection