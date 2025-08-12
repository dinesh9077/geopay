<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<div class="d-flex align-item-center justify-content-between mb-3">
				<h3></h3>   
				<a href="{{ route('admin.third-party-key.onafric-bank-list') }}" class="btn btn-info btn-sm" onclick="fetchBanks(this, event)"> Fetch Banks</a> 
			</div>	
			<form class="forms-sample row" id="onafricBankForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_bank_setting" method="post" enctype="multipart/form-data"> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Onafric Fees</label>
					<input type="text" class="form-control" id="onafric_bank_send_fees" name="onafric_bank_send_fees" autocomplete="off" placeholder="Onafric Fees" value="{{ config('setting.onafric_bank_send_fees') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="onafric_bank_commission_type" name="onafric_bank_commission_type" > 
						<option value="flat" {{ config('setting.onafric_bank_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.onafric_bank_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="onafric_bank_commission_charge" name="onafric_bank_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.onafric_bank_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				@if (config("permission.onafric_bank_setting.edit")) 
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				@endif
			</form> 
			<hr>
			<div class="row mt-3" id="onafricCollectionView">   
				<div class="container">
					<ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
						 
						<li class="nav-item">
							<a class="nav-link active" id="onafric-country-tab" data-bs-toggle="tab" href="#onafric-country-list" role="tab" aria-controls="line-metamap" aria-selected="true">Country List</a>
						</li>
					 
						<li class="nav-item">
							<a class="nav-link " id="onafric-bank-tab" data-bs-toggle="tab" href="#onafric-bank-list" role="tab" aria-controls="line-smtpmail" aria-selected="false">Bank List</a>
						</li>  
					</ul>
					<div class="tab-content mt-3" id="lineTabContent">
						<div class="tab-pane fade show active" id="onafric-country-list" role="tabpanel" aria-labelledby="onafric-country-tab"> 
							<form id="onafricOnafricBankCountryform" action="{{ route('admin.third-party-key.onafric-bank-transfer-update')}}" method="POST">
								@csrf
								<div id="countries-form"> 
									<div class="d-flex align-item-center justify-content-between mb-3">
										<h3 >Country List  </h3>     
									</div>
									<div class="country-section mb-3 ms-3" id="onafricBankCountryAppend"> 
										@if(count($onafricBankCountries) > 0)
											@foreach($onafricBankCountries as $key => $collectionCountry)
												<div class="row" id="remove_row">
													<div class="col-md-6">
														<label class="form-label">Country Name</label>
														<select name="country_name[]" id="bank_country_name_{{ $key }}" class="form-control select2" required>
															<option value="">Select Country</option>
															@foreach($countries as $country)
																<option value="{{ $country->nicename }}" {{ $collectionCountry->country_name == $country->nicename ? 'selected' : '' }}>{{ $country->nicename}}</option>
															@endforeach
														</select>
													</div>
													<input type="hidden" name="ids[]" id="id" value="{{ $collectionCountry->id }}">
													 
													<div class="col-md-4 d-flex align-items-end gap-3"> 
														@if($key == 0)
															<button type="button" class="btn btn-primary" onclick="addOnacfricBankCountry(this, event)">Add Country</button>  
														@else
															<button type="button" class="btn btn-danger" onclick="removeOnacfricBankCountry(this)">Remove Country</button>  
														@endif
													</div>	
												</div>
											@endforeach
										@else  
											<div class="row" id="remove_row">
												<div class="col-md-6">
													<label class="form-label">Country Name</label>
													<select name="country_name[]" id="bank_country_name" class="form-control select2" required>
														<option value="">Select Country</option>
														@foreach($countries as $country)
														<option value="{{ $country->nicename }}">{{ $country->nicename}}</option>
														@endforeach
													</select>
												</div>
												<input type="hidden" name="ids[]" id="id" value=""> 
												
												<div class="col-md-4 d-flex align-items-end gap-3"> 
													<button type="button" class="btn btn-primary" onclick="addOnacfricBankCountry(this, event)">Add Country</button>  
												</div>
											</div>
										@endif 
									</div>	 
								</div>
								@if (config("permission.onafric_bank_setting.edit"))
									<div class="d-flex justify-content-end">
										<button type="submit" class="btn btn-success">Save</button>
									</div>
								@endif
							</form>
						</div> 
						<div class="tab-pane fade" id="onafric-bank-list" role="tabpanel" aria-labelledby="onafric-bank-tab">  
							<div id="countries-form"> 
								<div class="d-flex align-item-center justify-content-between mb-3">
									<h3>Bank List  </h3>     
								</div>
								<div class="country-section mb-3 ms-3"> 
									<table id="bankOnaficListDatatable" class="table">
										<thead>
											<tr>
												<th>#</th>
												<th>Payout Country</th>
												<th>Bank Name</th>
												<th>Bank Code </th>
												<th>Status </th>  
											</tr>
										</thead>
										<tbody>
											@foreach($onafricBankLists as $key => $onafricBankList)
												<tr> 
													<td>{{ ($key + 1) }}</td>
													<td>{{ $onafricBankList->payout_iso }}</td>
													<td>{{ $onafricBankList->bank_name }}</td>
													<td>{{ $onafricBankList->mfs_bank_code }}</td>
													<td> 
														<select class="form-control form-control-sm me-2" id="status_{{ $onafricBankList->id }}" data-id="{{ $onafricBankList->id }}" onchange="updateOnafricBankStatus(this, event)">
															<option value="1" {{ $onafricBankList->status == 1 ? 'selected' : '' }}>Active</option>
															<option value="0" {{ $onafricBankList->status == 0 ? 'selected' : '' }}>In-Active</option>
														</select>
													</td> 
												</tr>
											@endforeach 
										</tbody>
									</table> 
								</div>	 
							</div> 
						</div> 
					</div> 
				</div> 
			</div>
		</div>
	</div>
</div> 
<script>
	function fetchBanks(obj, event) {
		event.preventDefault(); // Prevent default action if it's a form button

		Swal.fire({
			title: "Are you sure?",
			text: "Do you want to fetch Bank lists?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes!",
			cancelButtonText: "Cancel"
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: obj, // Replace with your actual endpoint
					type: 'POST',
					data: { _token: "{{ csrf_token() }}" }, 
					dataType: "Json",
					success: function(response) 
					{
						if(response.status == "success")
						{
							Swal.fire("Success!", response.message, "success");
						}
						else
						{
							Swal.fire("Error!", response.message, "error");
						}
					},
					error: function(xhr) {
						Swal.fire("Error!", "Something went wrong.", "error");
					}
				});
			}
		});
	}
	
	function updateOnafricBankStatus(obj, event) {
		event.preventDefault(); // Prevent default action if it's a form button
		
		const id = $(obj).attr('data-id');
		const status = $(obj).val();
		
		$.ajax({
			url: `{{ route('admin.third-party-key.onafric-bank-status-update') }}`, 
			type: 'POST',
			data: { _token: "{{ csrf_token() }}", id: id, status: status }, 
			dataType: "Json",
			success: function(response) 
			{
				if(response.status == "success")
				{
					Swal.fire("Success!", response.message, "success");
				}
				else
				{
					Swal.fire("Error!", response.message, "error");
				}
			},
			error: function(xhr) {
				Swal.fire("Error!", "Something went wrong.", "error");
			}
		}); 
	} 
	 
</script>