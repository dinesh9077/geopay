@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr w-200px me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle><i
                        data-feather="calendar" class="text-primary"></i></span>
                <input type="text" class="form-control bg-transparent border-primary" placeholder="Select date" data-input>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Users</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">{{ $cards['total_users'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Verified Users</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">{{ $cards['verified_users'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Pending KYC</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">{{ $cards['pending_kyc'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Active User</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">{{ $cards['active_user'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total User Wallets</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ number_format($cards['total_wallet'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Merchant/Partner Wallets</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ number_format($cards['total_merchant_wallet'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Merchants/Partner</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ $cards['total_merchant'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Merchants/Partner Active</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ $cards['total_active_merchant'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Transactions Amount/Count (Pay)</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ $cards['transactions_pay']['amount'] }} /
                                        {{ $cards['transactions_pay']['count'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Transactions Commission Earned (Pay)</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ number_format($cards['commission_pay'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Transactions Amount/Count (Add)</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ $cards['transactions_add']['amount'] }} /
                                        {{ $cards['transactions_add']['count'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Transactions Commission Earned (Add)</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-12">
                                    <h3 class="mb-2">{{ number_format($cards['commission_add'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->

    <div class="row">
        <div class="col-12 col-xl-12 grid-margin stretch-card">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-md-3">
                        <h6 class="card-title mb-0">Total Transaction Count/Amount/Commission (PAY)</h6>
                    </div>
                    <div class="row align-items-start">
                        <div class="col-md-7">
                            <p class="text-secondary fs-13px mb-3 mb-md-0">Revenue is the income that a business has from
                                its normal business activities, usually from the sale of goods and services to customers.
                            </p>
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end">
                            <div class="btn-group mb-3 mb-md-0" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-primary" data-range="today">Today</button>
                                <button type="button" class="btn btn-outline-primary d-none d-md-block"
                                    data-range="week">Week</button>
                                <button type="button" class="btn btn-primary" data-range="month">Month</button>
                                <button type="button" class="btn btn-outline-primary" data-range="year">Year</button>
                            </div>
                        </div>
                    </div>
                    <div id="revenuePayChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 grid-margin stretch-card">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-md-3">
                        <h6 class="card-title mb-0">Total Transaction Count/Amount/Commission (ADD)</h6>
                    </div>
                    <div class="row align-items-start">
                        <div class="col-md-7">
                            <p class="text-secondary fs-13px mb-3 mb-md-0">Revenue is the income that a business has from
                                its normal business activities, usually from the sale of goods and services to customers.
                            </p>
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end">
                            <div class="btn-group mb-3 mb-md-0" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-primary" data-range-add="today">Today</button>
                                <button type="button" class="btn btn-outline-primary d-none d-md-block"
                                    data-range-add="week">Week</button>
                                <button type="button" class="btn btn-primary" data-range-add="month">Month</button>
                                <button type="button" class="btn btn-outline-primary" data-range-add="year">Year</button>
                            </div>
                        </div>
                    </div>
                    <div id="revenueAddChart"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="row">
        <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-0 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Inbox</h6>
                        <div class="dropdown mb-2">
                            <a type="button" id="dropdownMenuButton6" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon-lg text-secondary pb-3px" data-feather="more-horizontal"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton6">
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="edit-2" class="icon-sm me-2"></i> <span
                                        class="">Edit</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="trash" class="icon-sm me-2"></i> <span
                                        class="">Delete</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="printer" class="icon-sm me-2"></i> <span
                                        class="">Print</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="download" class="icon-sm me-2"></i> <span
                                        class="">Download</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:;" class="d-flex align-items-center border-bottom pb-3">
                            <div class="me-3">
                                <img src="https://via.placeholder.com/35x35" class="rounded-circle w-35px"
                                    alt="user">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-body mb-2">Leonardo Payne</h6>
                                    <p class="text-secondary fs-12px">12.30 PM</p>
                                </div>
                                <p class="text-secondary fs-13px">Hey! there I'm available...</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="d-flex align-items-center border-bottom py-3">
                            <div class="me-3">
                                <img src="https://via.placeholder.com/35x35" class="rounded-circle w-35px"
                                    alt="user">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-body mb-2">Carl Henson</h6>
                                    <p class="text-secondary fs-12px">02.14 AM</p>
                                </div>
                                <p class="text-secondary fs-13px">I've finished it! See you so..</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="d-flex align-items-center border-bottom py-3">
                            <div class="me-3">
                                <img src="https://via.placeholder.com/35x35" class="rounded-circle w-35px"
                                    alt="user">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-body mb-2">Jensen Combs</h6>
                                    <p class="text-secondary fs-12px">08.22 PM</p>
                                </div>
                                <p class="text-secondary fs-13px">This template is awesome!</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="d-flex align-items-center border-bottom py-3">
                            <div class="me-3">
                                <img src="https://via.placeholder.com/35x35" class="rounded-circle w-35px"
                                    alt="user">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-body mb-2">Amiah Burton</h6>
                                    <p class="text-secondary fs-12px">05.49 AM</p>
                                </div>
                                <p class="text-secondary fs-13px">Nice to meet you</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="d-flex align-items-center border-bottom py-3">
                            <div class="me-3">
                                <img src="https://via.placeholder.com/35x35" class="rounded-circle w-35px"
                                    alt="user">
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-body mb-2">Yaretzi Mayo</h6>
                                    <p class="text-secondary fs-12px">01.19 AM</p>
                                </div>
                                <p class="text-secondary fs-13px">Hey! there I'm available...</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-xl-8 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Projects</h6>
                        <div class="dropdown mb-2">
                            <a type="button" id="dropdownMenuButton7" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon-lg text-secondary pb-3px" data-feather="more-horizontal"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="edit-2" class="icon-sm me-2"></i> <span
                                        class="">Edit</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="trash" class="icon-sm me-2"></i> <span
                                        class="">Delete</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="printer" class="icon-sm me-2"></i> <span
                                        class="">Print</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="download" class="icon-sm me-2"></i> <span
                                        class="">Download</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Project Name</th>
                                    <th class="pt-0">Start Date</th>
                                    <th class="pt-0">Due Date</th>
                                    <th class="pt-0">Status</th>
                                    <th class="pt-0">Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>NobleUI jQuery</td>
                                    <td>01/01/2024</td>
                                    <td>26/04/2024</td>
                                    <td><span class="badge bg-danger">Released</span></td>
                                    <td>Leonardo Payne</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>NobleUI Angular</td>
                                    <td>01/01/2024</td>
                                    <td>26/04/2024</td>
                                    <td><span class="badge bg-success">Review</span></td>
                                    <td>Carl Henson</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>NobleUI ReactJs</td>
                                    <td>01/05/2024</td>
                                    <td>10/09/2024</td>
                                    <td><span class="badge bg-info">Pending</span></td>
                                    <td>Jensen Combs</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>NobleUI VueJs</td>
                                    <td>01/01/2024</td>
                                    <td>31/11/2024</td>
                                    <td><span class="badge bg-warning">Work in Progress</span>
                                    </td>
                                    <td>Amiah Burton</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>NobleUI Laravel</td>
                                    <td>01/01/2024</td>
                                    <td>31/12/2024</td>
                                    <td><span class="badge bg-danger">Coming soon</span></td>
                                    <td>Yaretzi Mayo</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>NobleUI NodeJs</td>
                                    <td>01/01/2024</td>
                                    <td>31/12/2024</td>
                                    <td><span class="badge bg-primary">Coming soon</span></td>
                                    <td>Carl Henson</td>
                                </tr>
                                <tr>
                                    <td class="border-bottom">3</td>
                                    <td class="border-bottom">NobleUI EmberJs</td>
                                    <td class="border-bottom">01/05/2024</td>
                                    <td class="border-bottom">10/11/2024</td>
                                    <td class="border-bottom"><span class="badge bg-info">Pending</span></td>
                                    <td class="border-bottom">Jensen Combs</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

@endsection
@push('js')
    <script src="{{ asset('admin/vendors/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin/js/dashboard.js') }}"></script> 
<script>
 
function initRevenueChart({ container, buttonAttr, route, defaultRange = 'month' }) {
  let chartInstance = null;
  let currentRange = defaultRange;

  async function fetchData(range) {
    const url = `${route}?range=${encodeURIComponent(range)}`;
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`Fetch failed: ${res.status}`);
    return res.json();
  }

  function mountChart(labels, counts, amounts, commissions) {
    const el = document.querySelector(container);
    if (!el) return console.error(`Container not found: ${container}`);
    if (chartInstance) chartInstance.destroy();

    const options = {
      chart: { type: 'line', height: 360, toolbar: { show: false } },
      series: [
        { name: 'Transactions', type: 'column', data: counts },
        { name: 'Amount (₹)',   type: 'line',   data: amounts },
        { name: 'Commission (₹)', type: 'area', data: commissions }
      ],
      stroke: { width: [0, 3, 2], curve: 'smooth' },
      dataLabels: { enabled: false },
      xaxis: { categories: labels, labels: { rotate: -45 } },
      yaxis: [
        { title: { text: 'Transactions' }, seriesName: 'Transactions' },
        { opposite: true, title: { text: '₹ Amount / Commission' }, seriesName: 'Amount (₹)' }
      ],
      tooltip: {
        shared: true,
        y: {
          formatter: (val, ctx) => {
            if (ctx.seriesIndex === 0) return `${val ?? 0}`;
            return '₹' + Number(val ?? 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
          }
        }
      },
      legend: { position: 'bottom' },
      grid: { strokeDashArray: 4 },
      colors: undefined
    };

    chartInstance = new ApexCharts(el, options);
    chartInstance.render();
  }

  function highlight(range) {
    document.querySelectorAll(`.btn[${buttonAttr}]`).forEach(b => {
      b.classList.remove('btn-primary');
      b.classList.add('btn-outline-primary');
    });
    const active = document.querySelector(`.btn[${buttonAttr}="${range}"]`);
    if (active) {
      active.classList.remove('btn-outline-primary');
      active.classList.add('btn-primary');
    }
  }

  async function load(range) {
    currentRange = range;
    highlight(range);
    try {
      const data = await fetchData(range);
      mountChart(data.labels, data.counts, data.amounts, data.commission);
    } catch (e) {
      console.error(`Error loading chart (${container}):`, e);
    }
  }

  // Hook buttons
  document.querySelectorAll(`.btn[${buttonAttr}]`).forEach(b => {
    b.addEventListener('click', () => load(b.getAttribute(buttonAttr)));
  });

  // initial load
  load(defaultRange);
}

// Initialize both charts once DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  // PAY chart
  initRevenueChart({
    container: '#revenuePayChart',
    buttonAttr: 'data-range',
    route: `{{ route('admin.dashboard.revenue-series') }}`,
    defaultRange: 'month'
  });

  // ADD chart
  initRevenueChart({
    container: '#revenueAddChart',
    buttonAttr: 'data-range-add',
    route: `{{ route('admin.dashboard.add-revenue-series') }}`,
    defaultRange: 'month'
  });
});
</script>

@endpush
