<p>Transaction E-Receipt</p>
<table style="text-align: start;" class="text-uppercase">
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">SERVICE NAME <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->platform_name }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">ORDER ID <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->order_id }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">TOTAL AMOUNT <span class="mx-1">:</span></th>
        <td class="content-4">{{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">EXCHANGE RATE <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->unit_convert_exchange ?? '1.00' }}</td>
    </tr>

    @php
        $user = $transaction->user;
        $receive = $transaction->receive;
        $userName = $user ? "{$user->first_name} {$user->last_name}" : '';
        $userNumber = $user ? "({$user->formatted_number})" : '';
        $receiveName = $receive ? "{$receive->first_name} {$receive->last_name}" : '';
        $receiveNumber = $receive ? "({$receive->formatted_number})" : '';
    @endphp

    @if ($transaction->platform_name == 'geopay to geopay wallet')
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">FROM ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $userName }} {{ $userNumber }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">TO ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $receiveName }} {{ $receiveNumber }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">COUNTERPARTY NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $receiveName }}</td>
        </tr>
    @elseif ($transaction->platform_name == 'international airtime')
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">FROM ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $userName }} {{ $userNumber }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">OPERATOR NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->api_response_as_array['product']['operator']['name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">PRODUCT NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->product_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">MOBILE NO <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->mobile_number ?? 'N/A' }}</td>
        </tr>
    @endif

    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">PAYMENT DATE <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->created_at->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">DESCRIPTION <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->comments }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">REMARK <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->notes }}</td>
    </tr>
    <tr>
        <th class="content-4 d-flex justify-content-between text-nowrap">TRANSACTION STATUS <span class="mx-1">:</span></th>
        <td class="content-4">{{ $transaction->txn_status }}</td>
    </tr>
</table>
