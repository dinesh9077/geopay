<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Bank Account Number <span class="text-danger">*</span></label>
	<input id="bankaccountnumber" name="bankaccountnumber" placeholder="Enter Bank Account Number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $editData && isset($editData['bankaccountnumber']) ? $editData['bankaccountnumber'] : '' }}" required />
</div> 
<div class="mb-4 col-md-6">
	<label for="country_code" class="content-3 mb-0">Recipient mobile number (eg.700800900) <span class="text-danger">*</span></label> 
	<div class="d-flex align-items-center gap-2">
		<input id="mobile_code" type="text" name="mobile_code" class="form-control form-control-lg content-3 mobile-number px-2" style="max-width: 65px;" placeholder="+91" value="{{ $editData && isset($editData['mobile_code']) ? $editData['mobile_code'] : '+'.$isdcode }}" readonly />
		<input id="receivercontactnumber" type="number" name="receivercontactnumber" class="form-control form-control-lg content-3" placeholder="Enter Recipient mobile number" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" value="{{ $editData && isset($editData['receivercontactnumber']) ? $editData['receivercontactnumber'] : '' }}" required />
	</div> 
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

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Sender Date Of Birth <span class="text-danger">*</span></label>
	<input id="sender_placeofbirth" name="sender_placeofbirth" placeholder="Sender Date Of Birth" type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['sender_placeofbirth']) ? $editData['sender_placeofbirth'] : '' }}" required/>
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Purpose Of Transfer <span class="text-danger">*</span></label>
	<input id="purposeOfTransfer" name="purposeOfTransfer" placeholder="Enter Purpose Of Transfer such as Health/Medical Expense or Education." type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['purposeOfTransfer']) ? $editData['purposeOfTransfer'] : '' }}" required/>
</div>

<div class="mb-4 col-md-6">
	<label class="content-3 mb-0">Source Of Funds <span class="text-danger">*</span></label>
	<input id="sourceOfFunds" name="sourceOfFunds" placeholder="Enter Source Of Funds Common sources include Salary/Wages, Investment Income or Savings." type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['sourceOfFunds']) ? $editData['sourceOfFunds'] : '' }}" required/>
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
	flatpickr("#sender_placeofbirth", {
		dateFormat: "Y-m-d",
		maxDate: "today"
	});
	
	flatpickr("#idExpiry", {
		dateFormat: "Y-m-d"
	}); 
</script>