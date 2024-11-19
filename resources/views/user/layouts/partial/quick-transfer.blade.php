<div class="col-lg-3">
    <div class="p-3 border text-center rounded-3 quick-transfer-container">
        <!-- First Row: Avatar Selection (Screenshot Design) -->
        <div class="slick-slider qt-slick-slider">
            <div class="element element-1">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Livia Bator" class="avatar-md rounded-circle">
                    <p class="text-white mt-1">Livia Bator</p>
                </div>
            </div>
            <div class="element element-2">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-2.jpg') }}" alt="Randy Press" class="avatar-md rounded-circle">
                    <p class="text-white mt-1">Jack Roy</p>
                </div>
            </div>
            <div class="element element-3">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Livia Bator" class="avatar-md rounded-circle">
                    <p class="text-white mt-1">Livia Bator</p>
                </div>
            </div>
            <div class="element element-4">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-2.jpg') }}" alt="Randy Press" class="avatar-md rounded-circle">
                    <p class="text-white mt-1">Jack Roy</p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <!-- <span style="font-size: 10px; color: white; white-space: nowrap; margin-right: 10px;">Write Amount</span> -->
            <div class="input-group rounded-pill bg-light pe-0 mt-4">
                <input placeholder="Write Amount" type="text" class="number-input form-control form-control-lg bg-transparent border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-3">
                    Send <!--<i class="fab fa-telegram-plane font-size-20"></i> -->
                </button>
            </div>
        </div>
    </div>
    <div class="border rounded px-3 py-2 mt-3">
        <b class="">Recent Transactions</b>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/card-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>Payment to John</span><br>
                    <span class="transaction-date">12 Nov, 2024</span>
                </div>
            </div>
            <span class="font-text-13 text-danger">$100</span>
        </div>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>Payment to John</span><br>
                    <span class="transaction-date">12 Nov, 2024</span>
                </div>
            </div>
            <span class="font-text-13 trans-green">$2000</span>
        </div>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/paypal-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>Payment to John</span><br>
                    <span class="transaction-date">12 Nov, 2024</span>
                </div>
            </div>
            <span class="font-text-13 text-danger">$100</span>
        </div>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>Payment to John</span><br>
                    <span class="transaction-date">12 Nov, 2024</span>
                </div>
            </div>
            <span class="font-text-13 trans-green">$2000</span>
        </div>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>Payment to John</span><br>
                    <span class="transaction-date">12 Nov, 2024</span>
                </div>
            </div>
            <span class="font-text-13 trans-green">$2000</span>
        </div>
    </div>
</div>