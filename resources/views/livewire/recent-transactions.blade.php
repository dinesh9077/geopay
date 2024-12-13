<div class="border recent-transactions-card mt-3"> 
    <div class="d-flex justify-content-between align-items-center">
		<h4 class="heading-6">Recent Transactions</h4>
		<a class="content-4 text-secondary" href="{{ route('transaction-list') }}">View All</a>
	</div>
    @forelse($transactions as $transaction)
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
                <div class="content-4">
                    <p class="ellipsis-2 mb-0">{{ $transaction->comments }}</p>
                    <p class="transaction-date text-secondary mb-0">{{ $transaction->created_at->format('d M, Y') }}</p>
                </div>
            </div>
            <span class="content-3 fw-normal text-nowrap {{ $transaction->transaction_type == 'debit' ? 'text-danger' : 'text-success' }}">
                {{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}
            </span>
        </div>
    @empty
	<div class="d-flex justify-content-between align-items-center my-3"> 
		<p class="content-3 m-auto">
			No Recent Transaction
		</p>
    </div>
	@endforelse
</div>
