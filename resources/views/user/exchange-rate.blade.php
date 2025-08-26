@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Exchange Rate')
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
					<th>Country Name</th>
					<th>Currency</th>
					<th>Rate</th> 
				</tr>
			</thead> 
			<tbody>
				@foreach($exchangeRates as $key => $exchangeRate)
					@php
						$exchangeRate->rate = $exchangeRate->aggregator_rate;
						if ($exchangeRate->merchantRates->isNotEmpty()) {
							$exchangeRate->rate = $exchangeRate->merchantRates->first()->markdown_rate ?? $exchangeRate->aggregator_rate;
						}
					@endphp
					<tr>
						<td>{{ ($key + 1) }}</td> 
						<td>{{ $exchangeRate->country_name }}</td>  
						<td>{{ $exchangeRate->currency  }}</td>  
						<td>{{ $exchangeRate->rate }}</td>  
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