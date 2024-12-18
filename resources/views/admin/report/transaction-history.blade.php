@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Transaction Report')
@section('header_title', 'Transaction Report')
@section('content')
    <style>
        .swal2-image.custom-image-class {
            position: absolute;
            top: -10px;
            right: 10px;
        }
    </style>
    <div class="container-fluid p-0">
        <!-- Filter Row -->
        <div class="row g-2">

            <div class=" col-md-3 col-lg-1">
                <select id="userDropdown" name="user_id" class="form-control content-3 select2">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->first_name }}  {{ $user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class=" col-md-3 col-lg-2">
                <select class="form-control default-input content-3 select2" name="platform_name" id="platform_name">
                    <option value="">ALL</option>
                    <option value="international airtime">International Airtime</option>
                    <option value="geopay to geopay wallet">Geopay To Geopay Wallet</option>
                    <option value="admin transfer">Admin Transfer</option>
                </select>
            </div>
            <div class=" col-md-3 col-lg-2">
                <input type="text" class="form-control default-input " id="start_date" name="start_date"
                    placeholder="Start date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class=" col-md-3 col-lg-2">
                <input type="text" class="form-control default-input" id="end_date" name="end_date"
                    placeholder="End date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
            </div>
            <div class=" col-md-3 col-lg-1">
                <select class="form-control default-input content-3 select2" name="txn_status" id="txn_status">
                    <option value="">ALL</option>
                    @foreach($txnStatuses as $txnStatus)
						<option value="{{ $txnStatus }}">{{ $txnStatus }}</option>
					@endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <input type="text" class="form-control default-input" name="search" id="search"
                    placeholder="Search Item">
            </div>
            <div class="filter-buttons col-md-4 col-lg-2">
                <button id="applyFilters" class="btn btn-primary">Filter</button>
                <button id="resetFilters" class="btn btn-secondary">Reset</button>
            </div>
        </div>
        <hr>
        <div class="data-table-container">
            <div class="left-head-deta mb-4">
                <div class="custom-entry">
                    <p>Show</p>
                    <select class="form-select form-select-sm" id="page_length">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                        <option value="2000">2000</option>
                    </select>
                    <p>entries</p>
                </div>
                <a href="javascript:;" class="btn btn-primary btn-sm" id="excelExport"> XLXS</a>
                <a href="javascript:;" class="btn btn-warning btn-sm" id="pdfExport"> PDF</a>
            </div>

            <table id="transaction-table" class="table table-borderless table-hover border-0 mb-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Service Name</th>
                        <th>Order Id</th>
                        <th>Fees</th>
                        <th>Total Amount</th>
                        <th>Exchange Rate</th>
                        <th style="width: 20%;">Remark</th>
                        <th style="width: 15%;">Notes</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>

@endsection

@push('js')
    <script>
        $('.select2').select2({
            width: "100%"
        });

        const flatpickrStartDate = document.querySelector('#start_date');
        const flatpickrEndDate = document.querySelector('#end_date');

        if (flatpickrStartDate) {
            flatpickrStartDate.flatpickr("#start_date", {
                wrap: true,
                dateFormat: "Y-m-d"
            });
        }

        if (flatpickrEndDate) {
            flatpickrEndDate.flatpickr("#end_date", {
                wrap: true,
                dateFormat: "Y-m-d"
            });
        }

        $(document).ready(function() {
            // Initialize DataTable
            var dataTable = $('#transaction-table').DataTable({

                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'd-none',
                        text: 'excel',
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            },
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'd-none',
                        text: 'pdf',
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            },
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    }
                ],


                processing: true,
                "language": {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                serverSide: true,
                bLengthChange: false,
                searching: false,
                bFilter: true,
                responsive: false,
                bInfo: true,
                iDisplayLength: 10,
                order: [
                    [0, 'desc']
                ],
                bAutoWidth: false,
                "ajax": {
                    "url": "{{ route('admin.report.transaction-history-ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.platform_name = $('#platform_name').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.txn_status = $('#txn_status').val();
                        d.search = $('#search').val();
                        d.user_id = $('#userDropdown').val();
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "user_name"
                    },
                    {
                        "data": "platform_name"
                    },
                    {
                        "data": "order_id"
                    },
                    {
                        "data": "fees"
                    },
                    {
                        "data": "txn_amount"
                    },
                    {
                        "data": "unit_convert_exchange"
                    },
                    {
                        "data": "comments"
                    },
                    {
                        "data": "notes"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "action"
                    }
                ],
                drawCallback: function() { 
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });


            $("#excelExport").on("click", function() {
                $(".buttons-excel").trigger("click");
            });

            $("#pdfExport").on("click", function() {
                $(".buttons-pdf").trigger("click");
            });
            $('#page_length').change(function() {
                transactionTable.page.len($(this).val()).draw();
            })
            $('#applyFilters').click(function() { // Corrected selector here
                dataTable.draw();
            });

            $('#resetFilters').click(function() {
                $('#txn_status, #platform_name, #userDropdown').val('').trigger('change');
                $('#search').val('');

                const startOfMonth = "{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}";
                const endOfMonth = "{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}";

                $('#start_date').val(startOfMonth); // Set start_date
                $('#end_date').val(endOfMonth); // Set end_date

                dataTable.draw(); // Refresh the dataTable
            });
        });

        function viewReceipt(obj, event) {
            event.preventDefault();

            $.get(obj, function(res) {
                const result = decryptData(res.response);
                Swal.fire({
                    type: "success",
                    confirmButtonColor: "#188ae2",
                    confirmButtonText: "Close",
                    imageUrl: "{{ url('storage/setting', config('setting.site_logo')) }}",
                    imageWidth: 70,
                    imageHeight: 70,
                    customClass: {
                        image: 'custom-image-class'
                    },
                    html: result.view
                }).then(function(result) {
                    if (result.isConfirmed) {}
                });
            });
        }
    </script>
@endpush
