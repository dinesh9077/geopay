@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Add Funds')
@section('header_title', 'Add Funds')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			 
			<div class="tab-content" id="pills-tabContent"> 
				<div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
					@include('user.transaction.add-money.mobile-money')
				</div> 
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection
