<!DOCTYPE html>
<html>
	<head>
		<title>Approval Notification</title>
	</head>
	<body>
		<h1>Congratulations, {{ $directorName }}!</h1>
		<p>All your submitted documents have been successfully approved on {{ $approvedAt->format('F j, Y') }}.</p>
		<p>Thank you for completing the requirements. You can now proceed with the next steps.</p>
		<p>Best regards,</p>
		<p>The {{ config('setting.site_name') }} Team</p>
	</body>
</html>
