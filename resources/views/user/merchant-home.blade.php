@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Dashboard') 
@section('header_title', 'Dashboard') 
@section('content')

<div class="container-fluid p-0">
    <div class="row g-4">

        <!-- Today Transactions -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="mb-2 text-primary">
                        <i class="bi bi-cash-stack fs-2"></i>
                    </div>
                    <h6 class="text-muted">Today’s Transactions</h6>
                    <h3 class="fw-bold">{{ $today['count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <!-- Today Volume -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="mb-2 text-info">
                        <i class="bi bi-graph-up-arrow fs-2"></i>
                    </div>
                    <h6 class="text-muted">Today’s Volume</h6>
                    <h3 class="fw-bold">{{ number_format($today['amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="mb-2 text-success">
                        <i class="bi bi-check-circle-fill fs-2"></i>
                    </div>
                    <h6 class="text-muted">Success Rate</h6>
                    <h3 class="fw-bold text-success">{{ $today['success_rate'] ?? 0 }}%</h3>
                </div>
            </div>
        </div>

        <!-- Today Fees -->
        <div class="col-md-3">
            <div class="card  border-0 rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="mb-2 text-danger">
                        <i class="bi bi-coin fs-2"></i>
                    </div>
                    <h6 class="text-muted">Today’s Fee</h6>
                    <h3 class="fw-bold">{{ number_format($today['fee'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4 mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-calendar3 text-primary"></i> This Month Overview
                    </h6>
                    <p class="mb-2">📌 Total Transactions: <strong>{{ $month['count'] ?? 0 }}</strong></p>
                    <p class="mb-2">💰 Total Volume: <strong>{{ number_format($month['amount'] ?? 0, 2) }}</strong></p>
                    <p class="mb-2">🏦 Total Fees: <strong>{{ number_format($month['fee'] ?? 0, 2) }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Service Breakdown -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4 mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-pie-chart-fill text-warning"></i> Service Breakdown
                    </h6>
                    <ul class="list-group list-group-flush">
                        @foreach($services ?? [] as $srv)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ ucfirst($srv->service) }}
                                <span>
                                    <strong>{{ $srv->total }}</strong> Txns /
                                    {{ number_format($srv->amount, 2) }}
                                </span>
                            </li>
                        @endforeach
                        @if(empty($services) || count($services) == 0)
                            <li class="list-group-item text-muted">No data available</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-12">
            <div class="card border-0 rounded-4 mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock-history text-secondary"></i> Recent Transactions
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent ?? [] as $txn)
                                    <tr>
                                        <td>{{ $txn->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ ucfirst($txn->service) }}</td>
                                        <td>{{ number_format($txn->amount, 2) }}</td>
                                        <td>
                                            @if($txn->status == 'success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif($txn->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if(empty($recent) || count($recent) == 0)
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No transactions found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- row -->
</div>

{{-- Extra styling for theme look --}}
<style>
    .hover-card:hover {
        transform: translateY(-4px);
        transition: 0.3s;
        box-shadow: 0 6px 18px rgba(0,0,0,0.15) !important;
    }
</style>

@endsection
