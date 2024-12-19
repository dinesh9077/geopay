<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Transaction Receipt</title>
	</head>
	<body style="margin: 0; padding: 0 10px; box-sizing: border-box; font-family: sans-serif;">
		<table border="0" cellpadding="0" cellspacing="0"
		style="width: 100%; margin: 0 auto;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);border-radius: 5px;padding: 0 0px; text-transform: capitalize; font-size: 12px;">
			<tr>
				<td> 
					<!-- Company and Transaction Info -->
					<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin: 10px auto;">
						<tr>
							<td style="text-align: center;"> 
								<img width="200" src="{{ url('storage/setting', config('setting.site_logo')) }}" alt=""> 
							</td>
						</tr> 
						<tr>
							<td>
								<p
								style="text-align: center;line-height: 1.5;border-bottom: 1px solid #bbb;padding-bottom: 15px;margin-bottom: 0;">
									<span style="font-weight: 600;">Address:</span> {{ config('setting.contact_address') }}
								</p>
							</td>
						</tr>
					</table>

					<!-- Transaction Information -->
					<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin: 10px auto;">
						<tr>
							<td>
								<h3 style="text-align: center;">Transaction E-Receipt</h3>
							</td>
						</tr>
					</table>

					<!-- Details Table -->
					<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin: 10px auto;"> 
						<tr>
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">SERVICE NAME</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
								{{ $transaction->platform_name }}</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">ORDER ID</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
								{{ $transaction->order_id }}</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">TOTAL AMOUNT</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
								{{ Helper::decimalsprint($transaction->txn_amount, 2) }} {{ config('setting.default_currency') }}</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">EXCHANGE RATE</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
								{{ $transaction->unit_convert_exchange ?? '1.00' }}</p>
							</td>
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
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">FROM ACCOUNT</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $userName }} {{ $userNumber }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">COUNTERPARTY NAME</span></p>
								</td> 
								<td> 
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $receiveName }} {{ $receiveNumber }}</p>
								</td>
							</tr>
						@elseif ($transaction->platform_name == 'international airtime')
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">FROM ACCOUNT</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $userName }} {{ $userNumber }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">OPERATOR NAME</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->api_response_as_array['product']['operator']['name'] ?? 'N/A' }}</p>
								</td>
							</tr>
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">PRODUCT NAME</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->product_name ?? 'N/A' }}
									</p>
								</td>
							</tr>
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">MOBILE NO</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
										{{ $transaction->mobile_number ?? 'N/A' }}
									</p>
								</td>
							</tr>
						@elseif ($transaction->platform_name == 'transfer to bank') 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">FROM ACCOUNT</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $userName }} {{ $userNumber }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Confirmation Id</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->unique_identifier ?? 'N/A' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Bank Location Id</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->product_id ?? 'N/A' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Bank Name</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->product_name ?? 'N/A' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Account Number</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->beneficiary_request['data']['bankAccountNumber'] ?? 'N/A' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Counterparty Name</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ isset($transaction->beneficiary_request['data']['beneficiaryFirstName']) && isset($transaction->beneficiary_request['data']['beneficiaryLastName']) ? 
										$transaction->beneficiary_request['data']['beneficiaryFirstName'] . ' ' . $transaction->beneficiary_request['data']['beneficiaryLastName'] : 'N/A' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Mobile</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->beneficiary_request['data']['beneficiaryMobile'] ?? 'N/A' }}</p>
								</td>
							</tr>
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Platform Fee</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->fees ? Helper::decimalsprint($transaction->fees, 2) : '0.00' }}</p>
								</td>
							</tr> 
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Service Charge</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->service_charge ? Helper::decimalsprint($transaction->service_charge, 2) : '0.00' }}</p>
								</td>
							</tr>  
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Total Charge</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->total_charge ? Helper::decimalsprint($transaction->total_charge, 2) : '0.00' }}</p>
								</td>
							</tr>  
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Txn Amount</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->unit_amount ? Helper::decimalsprint($transaction->unit_amount, 2) : '0.00' }}</p>
								</td>
							</tr>  
							<tr> 
								<td style="width: 40%;">
									<p style="margin-top: 0;"><span style="font-weight: 600;">Net Amount</span></p>
								</td> 
								<td>
									<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->txn_amount ? Helper::decimalsprint($transaction->txn_amount, 2) : '0.00' }}</p>
								</td>
							</tr> 
						@endif

						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">PAYMENT DATE</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->created_at->format('Y-m-d') }}
								</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">DESCRIPTION</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->comments }}
								</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">REMARK</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->notes }}
								</p>
							</td>
						</tr>
						<tr> 
							<td style="width: 40%;">
								<p style="margin-top: 0;"><span style="font-weight: 600;">TRANSACTION STATUS</span></p>
							</td> 
							<td>
								<p style="margin-top: 0;"> <span style="font-weight: 600; padding-right: 10px;">:</span>
									{{ $transaction->txn_status }}
								</p>
							</td>
						</tr> 
						<tr>
							<td style="padding-top: 30px;">
								<p style="margin-top: 0;  margin-bottom: 5px;"><span
								style="font-weight: 600;">Authorized</span> </p>
							</td>
						</tr>
						<tr>
							<td style="text-align: start; "> 
								{{ config('setting.site_name') }}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
