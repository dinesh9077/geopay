<div class="border rounded px-3 py-2 mt-3"> 
    <div class="d-flex justify-content-between align-items-center">
		<h4 class="heading-6">Recent Transactions</h4>
		<a class="content-4 text-secondary" href="#">View All</a>
	</div>
    @forelse($transactions as $transaction)
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex gap-lg-2 gap-md-3">
                <img src="{{ asset('assets/image/dashboard/dollar-sign.svg') }}" class="transaction-icon"/>
                <div class="font-text-13">
                    <span>{{ $transaction->comments }}</span><br>
                    <span class="transaction-date">{{ $transaction->created_at->format('d M, Y') }}</span>
                </div>
            </div>
            <span class="font-text-13 text-nowrap {{ $transaction->transaction_type == 'debit' ? 'text-danger' : 'text-success' }}">
                {{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}
            </span>
        </div>
    @empty
	<div class="d-flex justify-content-between align-items-center my-3"> 
			<p class="font-text-13 m-auto">
                 No Recent Transaction
			</p>
    </div>
	@endforelse
</div>
