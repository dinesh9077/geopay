<!-- Button trigger modal -->
<button type="button" class="btn btn-lg btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#add-beneficiary">Add Beneficiary Details</button>

<!-- Modal -->
<div class="modal fade" id="add-beneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Beneficiary Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row">
          <div class="mb-4 col-lg-4">
            <label class="content-3 mb-0">Beneficiary Type</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Beneficiary Type</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-lg-4">
            <label class="content-3 mb-0">Country</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Country</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-lg-4">
            <label class="content-3 mb-0">Channel Provider</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Channel Provider</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Bank Account Number</label>
            <input placeholder="Enter Bank Account Number" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary First Name</label>
            <input placeholder="Enter First Name" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Middle Name ( Optional )</label>
            <input placeholder="Enter Middle Name" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Last Name</label>
            <input placeholder="Enter Last Name" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Address</label>
            <input placeholder="Enter Address" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary State</label>
            <input placeholder="Enter State" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Email</label>
            <input placeholder="Enter Email id" type="email" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Mobile No</label>
            <input placeholder="Enter Mobile No" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Select Beneficiary Relationship with sender</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Beneficiary Relationship with sender</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Beneficiary Id Type</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Beneficiary Id Type</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Select Source of Fund</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Source of Fund</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 col-md-6">
            <label class="content-3 mb-0">Select Remittance purpose</label>
            <select id="country_id1" name="country_id" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" >
                <option value="" selected disabled>Select Remittance purpose</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
          </div>
          <div class="mb-4 mb-md-0 col-sm-6">
            <label class="content-3 mb-0">Beneficiary Id Number</label>
            <input placeholder="Enter Id Number" type="text" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
          <div class="col-sm-6">
            <label class="content-3 mb-0">Receiver Id Expiry Date</label>
            <input type="date" class="form-control form-control-sm content-3 border-0 border-bottom rounded-0 text-secondary" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
