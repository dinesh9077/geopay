 @extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Account Detail')

@section('content') 
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Account Detail</h4>
	</div>  
</div> 

<div class="row">
	<div class="col-12 col-xl-12 stretch-card">
		<div class="row flex-grow-1">
			<div class="col-md-3 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-baseline">
							<h6 class="card-title mb-0">Balance</h6> 
						</div>
						<div class="row">
							<div class="col-6 col-md-12 col-xl-5">
								<h3 class="mb-2">3,897</h3> 
							</div> 
							<div class="col-6 col-md-12 col-xl-7 text-end">
								<i class="fs-2" data-feather="dollar-sign"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-baseline">
							<h6 class="card-title mb-0">Deposits</h6> 
						</div>
						<div class="row">
							<div class="col-6 col-md-12 col-xl-5">
								<h3 class="mb-2">3,897</h3> 
							</div> 
							<div class="col-6 col-md-12 col-xl-7 text-end">
								<i class="fs-2" data-feather="briefcase"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-baseline">
							<h6 class="card-title mb-0">Withdrawals</h6> 
						</div>
						<div class="row">
							<div class="col-6 col-md-12 col-xl-5">
								<h3 class="mb-2">3,897</h3> 
							</div> 
							<div class="col-6 col-md-12 col-xl-7 text-end">
								<i class="fs-2" data-feather="database"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-baseline">
							<h6 class="card-title mb-0">Total Transfered</h6> 
						</div>
						<div class="row">
							<div class="col-6 col-md-12 col-xl-5">
								<h3 class="mb-2">3,897</h3> 
							</div> 
							<div class="col-6 col-md-12 col-xl-7 text-end">
								<i class="fs-2" data-feather="globe"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> <!-- row -->
@endsection 
@push('js')   
	<script>
		$('#directorKycForm').submit(function(event) 
		{
			event.preventDefault();   
			
			$(this).find('button').prop('disabled',true);   
		 
			var formData = new FormData(this);  
			formData.append('_token', "{{ csrf_token() }}");
			 
			$.ajax({ 
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: formData,
				processData: false, 
				contentType: false,  
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{ 
					$('#directorKycForm').find('button').prop('disabled',false);	 
					$('.error_msg').remove(); 
					
					if(res.status === "success")
					{  
						toastrMsg(res.status,res.message);   
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) {
							var inputField = $('#' + key);
							var errorSpan = $('<span>')
							.addClass('error_msg text-danger') 
							.attr('id', key + 'Error')
							.text(value[0]);  
							inputField.parent().append(errorSpan);
						});
					}
					else
					{  
						toastrMsg(res.status,res.message); 
					}
				} 
			});
		});	
	</script>
@endpush				