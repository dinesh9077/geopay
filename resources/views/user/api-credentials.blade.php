@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Api Setup')
@section('header_title', 'Api Setup')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-12 mb-3 add-money-section"> 

			<h2>Generate Client ID & Secret</h2>
 
			<form method="POST" action="{{ route('api.credentials.store') }}">
				@csrf
				<label for="environment">Environment:</label>
				<select name="environment" id="environment" required>
					<option value="sandbox" {{ $credential && $credential->environment === "sandbox" ? 'selected' : '' }}>Sandbox</option>
					<option value="production" {{ $credential && $credential->environment === "production" ? 'selected' : '' }}>Production</option>
				</select>
				<br><br>
				<button type="submit">Generate</button>
			</form>

			@if($credential)
				<h3>Latest Generated Credentials</h3>
				<p><strong>Environment:</strong> {{ ucfirst($credential->environment) }}</p> 
				<p><strong>Client ID:</strong> {{ $credential->client_id }}</p>
				<p><strong>Client Secret:</strong> {{ $credential->client_secret }}</p>
				<p><strong>API URL:</strong> {{ $credential->api_url }}</p>
			@endif 
		</div>
		 
	</div>
</div>
@endsection
@push('js') 
@endpush
