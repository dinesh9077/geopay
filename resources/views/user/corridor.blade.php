@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Corridor Access')
@section('header_title', 'Exchange Rate')
@section('content')
<style>
	.swal2-image.custom-image-class {
		position: absolute;
		top: -10px;
		right: 10px;
	}
</style>
<div class="container-fluid p-0"> 
	<div class="data-table-container">
		<table id="exchnage-rate-table" class="table table-borderless table-hover border-0 mb-4">
			<thead>
				<tr>
					<th>#</th> 
					<th>Service</th>
					<th>Country Name</th>
					<th>Payout Country</th>
					<th>Payout Currency</th>
					<th>Fee Type</th>
					<th>Fee Charge</th> 
				</tr>
			</thead> 
			<tbody>
				@foreach($corridors as $key => $corridor)
					@php
						$countryName = $corridor->country->nicename ?? 'N/A';
					@endphp
					<tr>
						<td>{{ ($key + 1) }}</td> 
						<td>{{ ucwords(str_replace('_', ' ', $corridor->service)) }}</td>  
						<td>{{ $countryName }}</td>  
						<td>{{ $corridor->payout_country  }}</td>  
						<td>{{ $corridor->payout_currency }}</td>  
						<td>{{ $corridor->fee_type }}</td>  
						<td>{{ $corridor->fee_value }}</td>  
					</tr>
				@endforeach
			</tbody>
			
		</table>

	</div>
</div>

@endsection

@push('js') 
<script>   
	$(document).ready(function() {
		$('#exchnage-rate-table').DataTable({
			paging: true,        // disable pagination
			info: true,          // hide "showing x of y"
			searching: true,     // optional: disable search box
			lengthChange: true,  // hide "show X entries" dropdown
			pageLength: 25,   
			lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "All"] ] // -1 = all rows
		});
	}); 
</script>
@endpush