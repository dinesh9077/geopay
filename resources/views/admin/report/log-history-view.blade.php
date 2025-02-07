<div class="modal fade" id="view_activity_log_modal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">View Activity Properties</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div> 
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="ann-deta-show"> 
							<p id="json" > </p>
						</div>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div> 
		</div>
	</div>
	<script>
		var jsonData = {!! $activity->properties !!};
		setTimeout(function()
		{    
			var prettyJson = JSON.stringify(jsonData, null, 4);
			
			prettyJson = prettyJson.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); 
			 
			var html = '<pre>' + prettyJson + '</pre>';
			document.getElementById('json').innerHTML = html;
		},100)
		
		@if(isset($_GET['is_popup']))
    		$('.btn-close,.red-btn').click(function()
    		{  
    			$('#view_activity_log_modal').remove();
    			$('.modal-backdrop').remove()
    			$('.modal-backdrop.show').css({'opacity': '0'}); 
    			$('<div class="modal-backdrop fade show"></div>').appendTo(document.body);
    			setTimeout(function()
    			{ 
    				$('body').addClass('modal-open');
    			},200); 
    		})
		@endif 
		
	</script>
</div>