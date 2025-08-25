@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Merchant API Setup')
@section('header_title', 'Merchant API Setup')
@section('content')
<style> 
    h1, h2 {
        color: #102030;
    }
    .btn-custom {
        background-color: #81a8c7;
        color: #fff;
    }
    .btn-custom:hover {
        background-color: #6d94b3;
        color: #fff;
    }
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .copy-btn {
        color: #81a8c7;
        cursor: pointer;
    }
    .copy-btn:hover {
        text-decoration: underline;
    }
</style>
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="container py-4">
			<!-- Page Header -->
			<div class="mb-4">
				<h1 class="fw-bold">Merchant API Setup</h1>
				<p class="text-muted">Manage your credentials, API URLs, and documentation access.</p>
			</div>

			<!-- Credentials Card -->
			<!--<form method="POST" action="{{ route('api.credentials.store') }}">
				@csrf-->
				<div class="card mb-4 p-4">
					<h2 class="h5 fw-semibold mb-3">API Credentials</h2>
					<input type="hidden" name="environment" value="production" readonly class="form-control bg-light">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label">Client ID</label>
							<div class="input-group">
								<input type="text" id="clientId" value="{{ $credential->client_id ?? '' }}" readonly class="form-control bg-light">
								<span class="input-group-text bg-light" style="cursor: pointer;" onclick="copyToClipboard('{{ $credential->client_id ?? '' }}', this.querySelector('i'))">
									<i class="bi bi-clipboard" title="Copy"></i>
								</span>
							</div>
						</div>

						<div class="col-md-6">
							<label class="form-label">Secret</label>
							<div class="input-group">
								<input type="password" id="clientSecret" value="{{ $credential->client_secret ?? '' }}" readonly class="form-control bg-light">
								<span class="input-group-text bg-light" style="cursor: pointer;" onclick="toggleSecret()">
									<i class="bi bi-eye" id="toggleIcon" title="Show/Hide"></i>
								</span>
								<span class="input-group-text bg-light" style="cursor: pointer;" onclick="copyToClipboard('{{ $credential->client_secret ?? '' }}', this.querySelector('i'))">
									<i class="bi bi-clipboard" title="Copy"></i>
								</span>
							</div>
						</div>
					</div>

					<!--<button type="submit" class="btn btn-custom mt-3">Generate New Credentials</button>-->
				</div>
			<!--</form>-->

			<!-- API & Webhook URLs -->
			<div class="row g-4 mb-4">
				<div class="col-md-6">
					<div class="card p-4">
						<h2 class="h6 fw-semibold mb-2">API Base URL</h2>
						<div class="d-flex justify-content-between align-items-center bg-light border rounded p-2">
							<span class="text-truncate">{{ $credential->api_url }}</span>
							<i class="bi bi-clipboard copy-btn" style="cursor: pointer;" title="Copy"
							   onclick="copyToClipboard('{{ $credential->api_url }}', this)"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card p-4">
						<h2 class="h6 fw-semibold mb-2">Webhook URL</h2>
						<div class="d-flex justify-content-between align-items-center bg-light border rounded p-2">
							<span class="text-truncate">{{ $credential->user->webhook->url ?? 'N/A' }}</span>
							<i class="bi bi-clipboard copy-btn" style="cursor: pointer;" title="Copy"
							   onclick="copyToClipboard('{{ $credential->user->webhook->url ?? '' }}', this)"></i>
						</div>
					</div>
				</div>
			</div>
			<!-- API Documentation -->
			<div class="card p-4">
				<h2 class="h5 fw-semibold mb-3">API Documentation</h2>
				<ul class="list-unstyled">
					<li class="mb-2">
						<a href="{{ url('api-documentation') }}" target="_blank" class="btn btn-custom">View Documentation</a>
					</li>
					<li>
						<a href="{{ asset('Geopay Service.postman_collection.json') }}" target="_blank" download class="btn btn-custom">Download Postman Collection</a>
					</li>
				</ul>
			</div>
		</div> 
	</div>
</div>
@endsection
@push('js') 
<script>
	function copyToClipboard(text, iconElement) {
		navigator.clipboard.writeText(text).then(() => {
			// Change icon to check-circle
			iconElement.classList.remove('bi-clipboard');
			iconElement.classList.add('bi-check-circle', 'text-success');

			// Revert after 1.5 seconds
			setTimeout(() => {
				iconElement.classList.remove('bi-check-circle', 'text-success');
				iconElement.classList.add('bi-clipboard');
			}, 1500);
		});
	}
		
	function toggleSecret() {
		let secretInput = document.getElementById("clientSecret");
		let icon = document.getElementById("toggleIcon");

		if (secretInput.type === "password") {
			secretInput.type = "text";
			icon.classList.remove("bi-eye");
			icon.classList.add("bi-eye-slash");
		} else {
			secretInput.type = "password";
			icon.classList.remove("bi-eye-slash");
			icon.classList.add("bi-eye");
		}
	}
</script>
@endpush
