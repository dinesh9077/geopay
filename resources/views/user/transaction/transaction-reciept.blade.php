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
     
    @php
        $user = $transaction->user;
        $receive = $transaction->receive;
        $userName = $user ? "{$user->first_name} {$user->last_name}" : '';
        $userNumber = $user ? "({$user->formatted_number})" : '';
        $receiveName = $receive ? "{$receive->first_name} {$receive->last_name}" : '';
        $receiveNumber = $receive ? "({$receive->formatted_number})" : '';
    @endphp

    @if ($transaction->platform_name == 'geopay to geopay wallet' || $transaction->platform_name == 'admin transfer')
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">TOTAL AMOUNT <span class="mx-1">:</span></th>
			<td class="content-4">{{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}</td>
		</tr>
		
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">FROM ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $userName }} {{ $userNumber }}</td>
        </tr> 
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">COUNTERPARTY NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $receiveName }} {{ $receiveNumber }}</td>
        </tr>
    @elseif ($transaction->platform_name == 'international airtime')
	
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">Unit Amount <span class="mx-1">:</span></th>
			<td class="content-4">{{ $transaction->unit_rates}}</td>
		</tr>
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">Platform Fees <span class="mx-1">:</span></th>
			<td class="content-4">{{ $transaction->fees }}</td>
		</tr>
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">EXCHANGE RATE <span class="mx-1">:</span></th>
			<td class="content-4">{{ $transaction->rates }}</td>
		</tr>
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">TOTAL AMOUNT <span class="mx-1">:</span></th>
			<td class="content-4">{{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}</td>
		</tr>
		
		<tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">Destination Amount <span class="mx-1">:</span></th>
			<td class="content-4">{{ Helper::decimalsprint($transaction->unit_amount, 2) }} {{ $transaction->unit_currency }}</td>
		</tr>
		
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">FROM ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $userName }} {{ $userNumber }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">OPERATOR NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->api_response['product']['operator']['name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">PRODUCT NAME <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->product_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">MOBILE NO <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->mobile_number ?? 'N/A' }}</td>
        </tr>
	@elseif ($transaction->platform_name == 'transfer to bank')
		<tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">FROM ACCOUNT <span class="mx-1">:</span></th>
            <td class="content-4">{{ $userName }} {{ $userNumber }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Confirmation Id <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->unique_identifier ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Bank Location Id <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->product_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Bank Name <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->product_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Account Number <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->beneficiary_request['data']['bankAccountNumber'] ?? 'N/A' }}</td>
        </tr>
        <tr>
			<th class="content-4 d-flex justify-content-between text-nowrap">Counterparty Name <span class="mx-1">:</span></th>
			<td class="content-4">
				{{ isset($transaction->beneficiary_request['data']['beneficiaryFirstName']) && isset($transaction->beneficiary_request['data']['beneficiaryLastName']) ? 
					$transaction->beneficiary_request['data']['beneficiaryFirstName'] . ' ' . $transaction->beneficiary_request['data']['beneficiaryLastName'] : 'N/A' }}
			</td>
		</tr> 
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Mobile <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->beneficiary_request['data']['beneficiaryMobile'] ?? 'N/A' }}</td>
        </tr> 
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Platform Fee <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->fees ? Helper::decimalsprint($transaction->fees, 2) : '0.00' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Service Charge <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->service_charge ? Helper::decimalsprint($transaction->service_charge, 2) : '0.00' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Total Charge <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->total_charge ? Helper::decimalsprint($transaction->total_charge, 2) : '0.00' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Txn Amount <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->unit_amount ? Helper::decimalsprint($transaction->unit_amount, 2) : '0.00' }}</td>
        </tr>
        <tr>
            <th class="content-4 d-flex justify-content-between text-nowrap">Net Amount <span class="mx-1">:</span></th>
            <td class="content-4">{{ $transaction->txn_amount ? Helper::decimalsprint($transaction->txn_amount, 2) : '0.00' }}</td>
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
