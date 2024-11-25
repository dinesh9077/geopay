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
            <div class="input-group rounded-pill bg-light pe-0 mt-4">
                <input placeholder="Write Amount" type="text" class="number-input form-control form-control-lg bg-transparent border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-3">
                    Send  
                </button>
            </div>
        </div>
    </div>
   <livewire:recent-transactions /> 
</div>