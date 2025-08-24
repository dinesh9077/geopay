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
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox"
												data-service="{{ $service }}"
                                               class="form-check-input country-checkbox country-{{ $service }}"
                                               id="{{ $service }}_{{ $country['payout_country'] }}"
                                               name="settings[{{ $service }}][{{ $country['payout_country'] }}][enabled]"
                                               value="1"
                                               {{ isset($merchantCorridor[$service][$country['payout_country']]) && $merchantCorridor[$service][$country['payout_country']] ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label fw-semibold small" for="{{ $service }}_{{ $country['payout_country'] }}">
                                            <i class="flag-icon flag-icon-{{ strtolower($country['payout_country']) }}"></i>
                                            {{ $country->label ?? $country['payout_country'] ?? '' }}
                                        </label>

                                        <!-- Hidden inputs -->
                                        <input type="hidden" 
                                               name="settings[{{ $service }}][{{ $country['payout_country'] }}][payout_country]" 
                                               value="{{ $country['payout_country'] }}">

                                        <input type="hidden" 
                                               name="settings[{{ $service }}][{{ $country['payout_country'] }}][payout_currency]" 
                                               value="{{ $country['payout_currency'] ?? '' }}">
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
			// When master "check all" is clicked
			$(".check-all").on("change", function () {
				let service = $(this).data("service"); // get service name 
				$(".country-" + service).prop("checked", $(this).prop("checked"));
			});

			// If any single country is unchecked, uncheck master
			$(".country-checkbox").on("change", function () {
				let service = $(this).data("service");

				// If all are checked → check master, else uncheck
				let allChecked = $(".country-" + service).length === $(".country-" + service + ":checked").length;
				$("#checkAll_" + service).prop("checked", allChecked);
			});
		});
	</script>
@endpush