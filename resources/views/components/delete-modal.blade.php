<style>
	#deleteConfirmModal input {
	border-radius: 10px;
	border: 1px solid #D8E0F0;
	background: #FFF;
	box-shadow: 0px 1px 2px 0px rgba(184, 200, 224, 0.22);
	font-size: 14px;
	color: #7D8592;
	width: 100%;
}
</style>
<!-- Delete Modal Component -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
			<form id="deleteFormModal" method="post" action="">
				<div class="modal-body">
					<p id="deleteMessage">Are you sure you want to delete this item?</p>
					<div class="raw">
						<label class="label-main">To Confirm deletion, type "DELETE". Deleting instance cannot be undone.</label>
						<input type="text" class="modal-input" id="confirm_delete_input" placeholder="DELETE" autocomplete="off" > 
						<span class="text-danger" id="delete_error_msg"></span>
					</div>  
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger" >Delete</button>
				</div>
			</form>
        </div>
    </div>
</div>
<script>
	function deleteConfirmModal(obj, event)
	{
		event.preventDefault(); 

        // Get the URL and message from the button that triggered the modal
        var deleteUrl = $(obj).data('url');
        var deleteMessage = $(obj).data('message') || 'Are you sure you want to delete this item?';

        // Update the modal content
        $('#deleteMessage').text(deleteMessage);
        $('#delete_error_msg').text(''); 
        $('#confirm_delete_input').val(''); 
 
        // Set delete URL for the AJAX call 
		$('#deleteFormModal').find('input[name="ids"]').remove();
		$('#deleteFormModal').attr('action', deleteUrl);
		
		// Show the modal
        $('#deleteConfirmModal').modal('show'); 
	}
	
	// Form submission with AJAX
	$('#deleteFormModal').submit(function(event) 
	{
		event.preventDefault(); 

		var form = $(this); 
		var confirm_delete_input = $('#confirm_delete_input').val();
		 
		$('#delete_error_msg').text('');
 
		if (!confirm_delete_input) {
			$('#delete_error_msg').text('The input field is required.');
			return false;
		}

		if (confirm_delete_input !== "DELETE") {
			$('#delete_error_msg').text('You must type DELETE exactly to confirm permanent deletion.');
			return false;
		}
		
		var formData = new FormData(form[0]); 
		formData.append('_token', "{{csrf_token()}}");
		
		var currentUrl = form.attr('action');
		var params = getQueryParams(currentUrl);
		   
		$.ajax({
			url: currentUrl,
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				toastrMsg(response.status, response.msg);
				
				if(response.status === 'success') {
					$('#deleteConfirmModal').modal('hide');
					if(params && params['datatable'] == "true")
					{
						dataTable.draw();
					}
					else
					{
						table.setPage(table.getPage()); 
					}
				}  
			} 
		});
	});
</script>