<div class="col-lg-4 col-xxl-3">
    <div class="p-4 border text-center quick-transfer-container">
        <!-- First Row: Avatar Selection (Screenshot Design) -->
        <div class="slick-slider qt-slick-slider mb-3">
            <div class="element element-1">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Livia Bator" class="avatar-md rounded-circle">
                    <p class="text-white content-3 mt-1">Livia Bator</p>
                </div>
            </div>
            <div class="element element-2">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-2.jpg') }}" alt="Randy Press" class="avatar-md rounded-circle">
                    <p class="text-white content-3 mt-1">Jack Roy</p>
                </div>
            </div>
            <div class="element element-3">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-1.jpg') }}" alt="Livia Bator" class="avatar-md rounded-circle">
                    <p class="text-white content-3 mt-1">Livia Bator</p>
                </div>
            </div>
            <div class="element element-4">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/image/avatar-2.jpg') }}" alt="Randy Press" class="avatar-md rounded-circle">
                    <p class="text-white content-3 mt-1">Jack Roy</p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <!-- <span style="font-size: 10px; color: white; white-space: nowrap; margin-right: 10px;">Write Amount</span> -->
            <div class="input-group rounded-pill bg-light pe-0 mt-4">
                <input placeholder="Write Amount" type="text" class="number-input form-control form-control-lg bg-transparent border-0 content-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 content-3">
                    Send <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>
   <livewire:recent-transactions /> 
</div>