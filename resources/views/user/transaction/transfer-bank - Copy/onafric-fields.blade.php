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
	<input id="receiveraddress" name="receiveraddress" placeholder="(Apt/Street/Area/Zip code)" type="text" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['receiveraddress']) ? $editData['receiveraddress'] : '' }}" />
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
	<input id="idExpiry" name="idExpiry" placeholder="Enter Document Id Expiry." type="date" class="form-control form-control-lg content-3" value="{{ $editData && isset($editData['idExpiry']) ? $editData['idExpiry'] : '' }}" onclick="this.showPicker()" style="cursor: pointer;"/>
</div> 
<script>
	// Initialize Select2 for dropdowns
	$('#transferBankBeneficiaryForm .select2').select2({
		dropdownParent: $('#addTransferBankBeneficiary'),
		width: "100%"
	});
	
	// Initialize Flatpickr for date inputs 
	/* flatpickr("#sender_placeofbirth", {
		dateFormat: "Y-m-d",
		maxDate: "today"
	});
	
	flatpickr("#idExpiry", {
		dateFormat: "Y-m-d"
	});  */ 
	
	document.querySelectorAll('input[type="date"]').forEach(input => {
		input.addEventListener('focus', function () {
			this.showPicker?.();
		});
	});
</script>