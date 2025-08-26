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
                        <h6 class="mb-0">
                            <i class="mdi mdi-lan-connect me-1"></i> 
                            {{ ucfirst(str_replace('_', ' ', $service)) }}
                        </h6>
                        <div class="form-check">
                            <input type="checkbox" data-service="{{ $service }}" class="form-check-input check-all" id="checkAll_{{ $service }}">
                            <label class="form-check-label small" for="checkAll_{{ $service }}">All</label>
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
@endsection

@push('js')
<script>
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
