<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transfer Successful</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px;">
        <h2 style="color: #333;">Transfer Completed!</h2>

        <p>Hi {{ $user->fullname }},</p>

        <p>
            Your transfer of 
            <strong>{{ number_format($transaction->txn_amount, 2) }}</strong> 
            to 
            <strong>{{ $transaction->beneficiary_request['receiverfirstname'].' '.$transaction->beneficiary_request['receiverlastname'] }}</strong> 
            ({{ $transaction->beneficiary_request['bankaccountnumber'] }}) 
            was successful.
        </p>

        <p>Order ID: <strong>{{ $transaction->order_id }}</strong></p>
        <p>Date: <strong> {{ $transaction->complete_transaction_at ? \Carbon\Carbon::parse($transaction->complete_transaction_at)->format('Y-m-d H:i') : 'N/A' }}</strong></p>

        <p>Thank you for using our platform!</p>
        <hr>
        <p style="font-size: 12px; color: #999;">This is an automated message. Please do not reply.</p>
    </div>
</body>
</html>
