<div class="grid-container mt-md-4">
    <form id="profileForm" class="animate__animated animate__fadeIn g-2">
		<div class="d-flex d-md-block justify-content-center">
			<div class="mb-3 w-fit position-relative">
				<img class="avatar-2xl rounded-4 shadow-lg" id="profileImage" src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Profile Image" height="100" width="100">
				<input type="file" id="imageUpload" name="profile_image" accept=".jpg, .png" style="display: none;">
				<div class="edit-icon btn btn-light aspect-sq" id="editIcon"><i class="bi bi-pencil-fill small"></i></div>
			</div>
		</div>
        
        <div class="row text-start col-lg-8">
            <div class="col-md-6 mb-1">
                <label for="first_name" class="form-label content-2 fw-semibold mb-1">First Name</label>
                <input id="first_name" type="text" class="form-control form-control-lg default-input mb-3">
            </div>
			<!-- Last Name -->
			<div class="col-md-6 mb-1">
				<label for="last_name" class="form-label content-2 fw-semibold mb-1">Last Name</label>
				<input id="last_name" type="text" class="form-control form-control-lg default-input mb-3">
			</div>
			<!-- Email Address -->
			<div class="col-md-6 mb-1">
				<label for="email" class="form-label content-2 fw-semibold mb-1">Email Address</label>
				<div class="position-relative mb-3">
					<input id="email" type="email" class="form-control form-control-lg default-input">
					<span class="kyc-status kyc-success">verified</span>
				</div>
			</div>
			<!-- KYC -->
			<div class="col-md-6 mb-1">
				<label for="kyc" class="form-label content-2 fw-semibold mb-1">KYC</label>
				<div class="position-relative mb-3">
					<input id="kyc" type="text" class="form-control form-control-lg default-input">
					<span class="kyc-status kyc-pending">pending</span>
				</div>
			</div>
			<!-- Mobile No. -->
			<div class="col-md-6 mb-1">
				<label for="mobile" class="form-label content-2 fw-semibold mb-1">Mobile No.</label>
				<div class="position-relative mb-3">
					<input id="mobile" type="text" class="form-control form-control-lg default-input" maxlength="10" pattern="\d{10}">
					<span class="kyc-status kyc-failed">Failed</span>
				</div>
			</div>
			<!-- Daily Withdraw Limit -->
			<div class="col-md-6 mb-3">
				<label for="daily_withdraw_limit" class="form-label content-2 fw-semibold mb-1">Daily Withdraw Limit</label>
				<div class="position-relative mb-4">
					<input id="daily_withdraw_limit" type="number" class="form-control form-control-lg default-input mb-3">
					<span class="kyc-status kyc-success">verified</span>
				</div>
			</div>
        </div>
        <button type="button" class="btn btn-lg btn-grey rounded-2 col-12 col-md-2" id="updateBtn">Cancel</button>
        <button type="button" class="btn btn-lg btn-secondary rounded-2 col-12 col-md-2 mt-3 mt-md-0 ms-md-3" id="updateBtn">Save</button>
    </form>
</div>