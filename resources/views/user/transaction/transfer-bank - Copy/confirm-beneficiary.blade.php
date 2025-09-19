<!-- Modal Confirm Beneficiary -->
<div class="modal fade" id="confirmBeneficiaryModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="confirmBeneficiaryLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title heading-5 fw-normal" id="confirmBeneficiaryLabel">Confirm Recipient Detail</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<!-- Category Name -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Category Name <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ $beneficiary->category_name ?? 'N/A' }}</span>
					</div>

					<!-- Country Name -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Country Name <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['payoutCountryName'] ?? 'N/A' }}</span>
					</div>

					<!-- Bank Name -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Bank Name <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['bankName'] ?? 'N/A' }}</span>
					</div>

					<!-- Bank Account Number -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Bank Account No. <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['bankaccountnumber'] ?? 'N/A' }}</span>
					</div>

					<!-- Mobile Number -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Mobile Number <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->dataArr)['mobile_code'] ?? '' }}{{ optional($beneficiary->dataArr)['receivercontactnumber'] ?? 'N/A' }}</span>
					</div>

					<!-- First Name -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">First Name <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['receiverfirstname'] ?? 'N/A' }}</span>
					</div>

					<!-- Last Name -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Last Name <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['receiverlastname'] ?? 'N/A' }}</span>
					</div>

					<!-- Receiver Address -->
					<div class="mb-2 col-4">
						<label class="content-3 mb-1 d-flex justify-content-between">Receiver Address <span>:</span></label>
					</div>
					<div class="mb-2 col-8">
						<span class="content-3 text-secondary">{{ optional($beneficiary->data)['receiveraddress'] ?? 'N/A' }}</span>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button type="button" class="btn content-3 btn-primary" data-bs-dismiss="modal">Confirm</button>
				<button type="button" class="btn content-3 btn-secondary" data-beneficiary-id="{{ $beneficiary->id }}" onclick="editBeneficiary(this, event)">Edit</button>
				<button type="button" class="btn content-3 btn-danger opacity-75" data-bs-toggle="modal" data-bs-target="#transferBankDeleteBeneficiary" onclick="return $('#transferBankDeleteBeneficiary').remove(); ">Delete</button>
			</div>
		</div>
	</div> 
	<!-- Delete Edit Beneficiary -->
	<div class="modal fade" id="transferBankDeleteBeneficiary" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
					<h6 class="content-2 text-center text-danger mb-2">Are you sure</h6>
					<h6 class="content-4 text-center text-muted mb-3">You want to delete the beneficiary ?</h6>
					<div class="text-center d-flex align-items-center gap-2">
						<button type="button" class="btn content-3 w-100 btn-secondary" data-bs-dismiss="modal" onclick="return $('#transferToBankForm #beneficiaryId').val('').select2(); ">Cancel</button>
						<a href="{{ route('transfer-to-bank.beneficiary-delete', ['id' => $beneficiary->id]) }}" class="btn content-3 w-100 btn-danger opacity-75" >Delete</a>
					</div> 
				</div>                                                    
			</div>
		</div>
	</div>   
	<script>
		function editBeneficiary(obj, event)
		{ 
			event.preventDefault();
			var beneficiaryId = $(obj).data('beneficiary-id');
			if (!modalOpen)
			{
				modalOpen = true;
				closemodal(); 
				run_waitMe($('#confirmBeneficiaryModal'), 1, 'facebook');
				$.get("{{ url('transfer-to-bank/beneficiary-edit') }}/"+beneficiaryId, function(res)
				{
					const result = decryptData(res.response);
					$('body').find('#modal-view-render').html(result.view);
					$('#editTransferBankBeneficiary').modal('show');  
					$('#confirmBeneficiaryModal').waitMe('hide');
				});
			} 
		}
	</script>
</div>


