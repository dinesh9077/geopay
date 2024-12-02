<!DOCTYPE html>
<html>
<head>
    <title>KYC Rejection Notification</title>
</head>
<body>
    <h1>KYC Rejection Notification</h1>

    <p>Dear Director, {{ $directorName }},</p>

    <p>We regret to inform you that the following documents have been rejected during the KYC process:</p>

    @php
		// Filter out duplicate document types
		$uniqueRejectedDocuments = collect($rejectedDocuments)->unique('documentType');
	@endphp

	<ul>
		@foreach ($uniqueRejectedDocuments as $document)
			<li>
				<strong>{{ $document['documentType'] }}:</strong> {{ $document['reason'] }}
			</li>
		@endforeach
	</ul>

    <p>Please address the reasons mentioned and resubmit the documents at your earliest convenience.</p>

    <p>Thank you for your cooperation.</p>

    <p>Best Regards,<br> The {{ config('setting.site_name') }} Team</p>
</body>
</html>
