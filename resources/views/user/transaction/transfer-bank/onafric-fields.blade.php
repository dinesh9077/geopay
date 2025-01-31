<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Bank Account Number <span class="text-danger">*</span></label>
	<input id="bankaccountnumber" name="bankaccountnumber" placeholder="Enter Bank Account Number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $editData && isset($editData['bankaccountnumber']) ? $editData['bankaccountnumber'] : '' }}" required />
</div>
<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Recipient mobile number e.g. 250700800900.<span class="text-danger">*</span></label>
	<input id="receivercontactnumber" name="receivercontactnumber" placeholder="Enter Recipient mobile number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $editData && isset($editData['receivercontactnumber']) ? $editData['receivercontactnumber'] : '' }}" required />
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Recipient Name <span class="text-danger">*</span></label>
	<input id="receiverfirstname" name="receiverfirstname" placeholder="Enter Recipient Name" type="text" class="form-control form-control-lg content-3" required value="{{ $editData && isset($editData['receiverfirstname']) ? $editData['receiverfirstname'] : '' }}" />
</div>
  
<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Recipient Surname <span class="text-danger">*</span></label>
	<input id="receiverlastname" name="receiverlastname" placeholder="Enter Recipient Surname" type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['receiverlastname']) ? $editData['receiverlastname'] : '' }}" required />
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Recipient Address</label>
	<input id="receiveraddress" name="receiveraddress" placeholder="Enter Recipient Address" type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['receiveraddress']) ? $editData['receiveraddress'] : '' }}" />
</div> 

<div class="mb-4 col-lg-6">
	<label class="content-3 mb-0">Sender Country <span class="text-danger">*</span></label>
	<select id="sender_country" name="sender_country" class="form-control form-control-lg content-3 select2" required>
		<option value="">Select Sender Country</option> 
		@foreach($countries as $country)
			<option value="{{ $country->id }}" data-iso="{{ $country->iso }}" data-name="{{ $country->label }}" {{ $editData && isset($editData['sender_country']) ? ($country->id == $editData['sender_country'] ? 'selected' : '' ) : '' }}>{{ $country->label }}</option> 
		@endforeach
	</select>
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Sender mobile number e.g. 250700800900.<span class="text-danger">*</span></label>
	<input id="sender_mobile" name="sender_mobile" placeholder="Enter Sender mobile number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $editData && isset($editData['sender_mobile']) ? $editData['sender_mobile'] : '' }}" required />
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Sender Name <span class="text-danger">*</span></label>
	<input id="sender_name" name="sender_name" placeholder="Enter Sender Name" type="text" class="form-control form-control-lg content-3"  value="{{ $editData && isset($editData['sender_name']) ? $editData['sender_name'] : '' }}" required />
</div>
  
<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Sender Surname <span class="text-danger">*</span></label>
	<input id="sender_surname" name="sender_surname" placeholder="Enter Sender Surname" type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['sender_surname']) ? $editData['sender_surname'] : '' }}" required />
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Purpose Of Transfer</label>
	<input id="purposeOfTransfer" name="purposeOfTransfer" placeholder="Enter Purpose Of Transfer such as Health/Medical Expense or Education." type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['purposeOfTransfer']) ? $editData['purposeOfTransfer'] : '' }}"/>
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Source Of Funds</label>
	<input id="sourceOfFunds" name="sourceOfFunds" placeholder="Enter Source Of Funds Common sources include Salary/Wages, Investment Income or Savings." type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['sourceOfFunds']) ? $editData['sourceOfFunds'] : '' }}" />
</div> 

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Document Id Number</label>
	<input id="idNumber" name="idNumber" placeholder="Enter Document Id Number." type="text" class="form-control form-control-lg content-3"value="{{ $editData && isset($editData['idNumber']) ? $editData['idNumber'] : '' }}" />
</div> 
<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Document Id Type</label>
	<input id="idType" name="idType" placeholder="Enter Document Id Type." type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['idType']) ? $editData['idType'] : '' }}" />
</div> 
<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Document Id Expiry</label>
	<input id="idExpiry" name="idExpiry" placeholder="Enter Document Id Expiry." type="text" class="form-control form-control-lg content-3"value="{{ $editData && isset($editData['idExpiry']) ? $editData['idExpiry'] : '' }}" />
</div> 
<script>
	// Initialize Select2 for dropdowns
	$('#transferBankBeneficiaryForm .select2').select2({
		dropdownParent: $('#addTransferBankBeneficiary'),
		width: "100%"
	});
	
	// Initialize Flatpickr for date inputs
	flatpickr("#idExpiry", {
		dateFormat: "Y-m-d"
	});
</script>