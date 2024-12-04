<div>
    <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow-1">
        <!-- Balance Card -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Balance</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ config('setting.default_currency') }} {{ number_format($balance, 2) }}</h3>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7 text-end">
                            <i class="fs-2" data-feather="dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposits Card -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Deposits</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ config('setting.default_currency') }} {{ number_format($deposits, 2) }}</h3>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7 text-end">
                            <i class="fs-2" data-feather="briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawals Card -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Withdrawals</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ config('setting.default_currency') }} {{ number_format($withdrawals, 2) }}</h3>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7 text-end">
                            <i class="fs-2" data-feather="database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transferred Card -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Total Transaction</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2">{{ $totalTransaction }}</h3>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7 text-end">
                            <i class="fs-2" data-feather="globe"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
