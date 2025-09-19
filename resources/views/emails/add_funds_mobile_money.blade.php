<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Funds Added</title>
	</head>
	<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
		<div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
			<h2 style="color: #333;">Funds Added Successfully!</h2>
			<p>Dear {{ $user->fullname}},</p>
			<p>Your wallet has been credited via <strong>{{$paymentMethod}}</strong> with an amount of <strong>{{ $transaction->txn_amount }}</strong> on <strong>{{date}}</strong>.</p>
			<p>Order Id : <strong>{{ $transaction->order_id }}</strong></p>
			<p>Thank you for using our service!</p>
			<hr>
			<p style="font-size: 12px; color: #999;">This is an automated message. Please do not reply.</p>
		</div>
	</body>
</html>
