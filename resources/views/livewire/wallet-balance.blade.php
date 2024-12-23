<div>
    <div class="d-flex align-items-center my-1 px-3 btn btn-sm btn-primary gap-2">
		<i class="bi bi-wallet2 heading-3"></i>
		<span>{{ Helper::decimalsprint($balance, 2) }} {{ config('setting.default_currency') }}</span>
	</div>
</div>
