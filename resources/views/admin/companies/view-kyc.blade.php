@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - View Kyc')

@section('content')
	<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
		<div>
			<h4 class="mb-3 mb-md-0">View Kyc Details</h4>
		</div>  
	</div> 
	<div class="example"> 
		@livewire('company-kyc-tab', ['userId' => $userId])
	</div>
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