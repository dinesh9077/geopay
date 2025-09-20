<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Geopay Transaction</title>
	</head>
	<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
		<div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
			<h2 style="color: #333;">Geopay Wallet Transaction Successful!</h2>
			<p>Hi {{ $user->send_full_name }},</p>
			<p>You have successfully sent <strong>{{ number_format($transaction->txn_amount, 2) }}</strong> to <strong>{{ $transaction->fullname }}</strong> via Geopay on <strong>{{ $transaction->complete_transaction_at ? \Carbon\Carbon::parse($transaction->complete_transaction_at)->format('Y-m-d H:i') : 'N/A' }}</strong>.</p>
			<p>Order ID: <strong>{{$transaction->order_id}}</strong></p>
			<p>Keep enjoying seamless payments!</p>
			<hr>
			<p style="font-size: 12px; color: #999;">This is an automated message. Please do not reply.</p>
		</div>
	</body>
</html>
