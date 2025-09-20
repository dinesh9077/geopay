<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Funds Added</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
        <h2 style="color: #333;">Funds Added Successfully!</h2>

        <p>Dear {{ $user->fullname }},</p>

        <p>
            Your wallet has been credited via 
            <strong>{{ $paymentMethod }}</strong> 
            with an amount of 
            <strong>{{ number_format($transaction->txn_amount, 2) }}</strong> 
            on 
            <strong>
                {{ $transaction->complete_transaction_at ? \Carbon\Carbon::parse($transaction->complete_transaction_at)->format('Y-m-d H:i') : 'N/A' }}
            </strong>.
        </p>

        <p>Order ID: <strong>{{ $transaction->order_id }}</strong></p>

        <p>Thank you for using our service!</p>
        <hr>
        <p style="font-size: 12px; color: #999;">This is an automated message. Please do not reply.</p>
    </div>
</body>
</html>
