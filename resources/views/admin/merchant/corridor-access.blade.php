@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Merchant Corridor Access')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">Merchant Corridor Access</h4>
    </div>  
</div>

<form action="{{ route('admin.merchant.corridor.store') }}" method="POST">
    @csrf
	<input type="hidden" name="user_id" value="{{ $merchant->id }}">
    <div class="row">
        @foreach($services as $service => $countries)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
						<!-- Left side (service title) -->
						<h6 class="mb-0">
							<i class="mdi mdi-lan-connect me-1"></i> 
							{{ ucfirst(str_replace('_', ' ', $service)) }}
						</h6>

						<!-- Right side (checkboxes) --> 
						<div class="d-flex align-items-center gap-3">
							<!-- Bulk Commission button -->
							<button type="button" class="btn btn-success btn-sm" id="{{ $service }}" data-service="{{ $service }}" onclick="updateBulkCommission(this, event)">
								Update Bulk Commission
							</button>

							<!-- All checkbox -->
							<div class="form-check mb-0">
								<input type="checkbox" data-service="{{ $service }}" class="form-check-input check-all" id="all_{{ $service }}">
								<label class="form-check-label small" for="all_{{ $service }}">All</label>
							</div>
						</div>

					</div>

                    <div class="card-body">
                        <div class="row">
                            @foreach($countries as $country)
                               <div class="col-md-12 mb-3">
									<div class="border rounded p-2 bg-light d-flex align-items-center justify-content-between country-box">
										<!-- Country Checkbox -->
										<div class="form-check d-flex align-items-center">
											<input type="checkbox"
												   data-service="{{ $service }}"
												   class="form-check-input country-checkbox country-{{ $service }} me-2"
												   id="{{ $service }}_{{ $country['payout_country'] }}"
												   name="settings[{{ $service }}][{{ $country['payout_country'] }}][enabled]"
												   value="1"
												   {{ isset($merchantCorridor[$service][$country['payout_country']]['enabled']) && $merchantCorridor[$service][$country['payout_country']]['enabled'] ? 'checked' : '' }}
											>
											<label class="form-check-label fw-semibold small" for="{{ $service }}_{{ $country['payout_country'] }}">
												<i class="flag-icon flag-icon-{{ strtolower($country['payout_country']) }}"></i>
												{{ $country->label ?? $country['payout_country'] ?? '' }}
											</label>
										</div>

										<!-- Fee Setup Box -->
										<div class="d-flex align-items-center ms-3 fee-setup-box">
											<!-- Type dropdown -->
											<select class="form-select form-select-sm me-2 w-auto" 	name="settings[{{ $service }}][{{ $country['payout_country'] }}][fee_type]" @disabled(!isset($merchantCorridor[$service][$country['payout_country']]['enabled']) || !$merchantCorridor[$service][$country['payout_country']]['enabled'])> 
													
													<option value="flat" {{ (isset($merchantCorridor[$service][$country['payout_country']]['fee_type']) && $merchantCorridor[$service][$country['payout_country']]['fee_type'] == 'flat') ? 'selected' : '' }}>Flat</option>
													<option value="percentage" {{ (isset($merchantCorridor[$service][$country['payout_country']]['fee_type']) && $merchantCorridor[$service][$country['payout_country']]['fee_type'] == 'percentage') ? 'selected' : '' }}>Percentage</option> 
											</select>

											<!-- Value input -->
											<input type="number" step="0.01" min="0"
												   class="form-control form-control-sm w-25 me-2"
												   name="settings[{{ $service }}][{{ $country['payout_country'] }}][fee_value]"
												   placeholder="Value"
												   value="{{ $merchantCorridor[$service][$country['payout_country']]['fee_value'] ?? 0 }}"
												   @disabled(!isset($merchantCorridor[$service][$country['payout_country']]['enabled']) || !$merchantCorridor[$service][$country['payout_country']]['enabled'])>
										</div>

										<!-- Hidden inputs -->
										<input type="hidden" name="settings[{{ $service }}][{{ $country['payout_country'] }}][payout_country]" value="{{ $country['payout_country'] }}">
										<input type="hidden" name="settings[{{ $service }}][{{ $country['payout_country'] }}][payout_currency]" value="{{ $country['payout_currency'] ?? '' }}">
									</div>
								</div>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">
            <i class="mdi mdi-content-save"></i> Save Corridor Access
        </button>
    </div>
</form>

<div class="modal fade" id="bulkUpdateCommissionModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Bulk Commission Update</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="bulkUpdateCommissionForm" action="{{ route('admin.merchant.corridor.bulk-commission-update') }}" method="post" enctype="multipart/form-data">
			@csrf
				<input type="hidden" name="user_id" id="user_id" value="{{ $merchant->id }}" required>
				<input type="hidden" name="service" id="service" value="" required>
				<div class="modal-body"> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Fee Type <span class="text-danger">*</span></label>
						<select class="form-control" id="fee_type" name="fee_type" required>  
							<option value="flat"> Flat/Fixed </option>
							<option value="percentage"> Percentage </option>
						</select>
					</div>   
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Fee Value <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="fee_value" name="fee_value" autocomplete="off" placeholder="Fee Value Flat/%" value="0" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
					</div> 
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div> 
@endsection

@push('js')
<script>
	
	function updateBulkCommission(obj, event) {
		event.preventDefault(); // prevent default if button/link clicked

		const service = $(obj).data('service');
		const $form   = $('#bulkUpdateCommissionForm');

		// Reset the form first (clear inputs, errors, etc.)
		$form[0].reset();

		// Set hidden input service
		$form.find('#service').val(service);

		// Show modal
		$('#bulkUpdateCommissionModal').modal('show');
	}

	
	$(document).ready(function () {
		// Master "check all"
		$(".check-all").on("change", function () {
			let service = $(this).data("service");
			$(".country-" + service).prop("checked", $(this).prop("checked")).trigger("change");
		});

		// Enable/disable inputs based on checkbox
		$(".country-checkbox").on("change", function () {
			let row = $(this).closest(".form-check");
			row.find("input[type=number]").prop("disabled", !$(this).prop("checked"));

			let service = $(this).data("service");
			let allChecked = $(".country-" + service).length === $(".country-" + service + ":checked").length;
			$("#checkAll_" + service).prop("checked", allChecked);
		});
		
		$(".country-checkbox").on("change", function () {
			let row = $(this).closest(".country-box");
			let enabled = $(this).prop("checked");
			row.find("select, input[type=number]").prop("disabled", !enabled);
		})
	});
</script>
@endpush
