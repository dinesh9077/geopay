<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Recharge Successful</title>
	</head>
	<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
		<div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
			<h2 style="color: #333;">Recharge Successful!</h2>
			<p>Hi {{ $user->fullname }},</p>
			<p>You have successfully recharge for {{ $transaction->api_response['product']['operator']['name'] ?? 'N/A' }} <strong>{{ $transaction->unit_amount }}</strong> worth of airtime for <strong>{{ $transaction->mobile_number }}</strong> on <strong>{{ $transaction->complete_transaction_at ? \Carbon\Carbon::parse($transaction->complete_transaction_at)->format('Y-m-d H:i') : 'N/A' }}</strong>.</p>
			<p>Order ID: <strong>{{ $transaction->order_id }}</strong></p>
			<p>Thank you for using our services!</p>
			<hr>
			<p style="font-size: 12px; color: #999;">This is an automated message. Please do not reply.</p>
		</div>
	</body>
</html>
