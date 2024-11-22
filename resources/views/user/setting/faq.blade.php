<div class="grid-container mt-md-4">
	<div class="d-flex flex-column position-relative mb-3">
		<h3 class="heading-4 text-dark text-center mb-3">How can I help you?</h3>
		<div class="position-relative col-12 col-md-5 m-auto">
			<input id="faqSearch" type="text" class="form-control form-control-lg default-input ps-5" placeholder="Search for your question..." />
			<span class="input-search-icon"><i class="bi bi-search"></i></span>
		</div>
		<div class="mt-5 mb-4 d-flex align-items-center justify-content-between">
			<h3 class="heading-5 text-dark text-center">Top Questions</h3> 
		</div>
		<div class="accordion" id="accordionExample">
			@foreach($faqs as $key => $faq)
				<div class="accordion-item mb-2 border rounded-2 overflow-hidden">
					<h2 class="accordion-header">
						<button class="accordion-button content-2 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ( $key+1 ) }}" aria-expanded="true" aria-controls="collapse{{ ( $key+1 ) }}">
							{{ $faq->title }}
						</button>
					</h2>
					<div id="collapse{{ ( $key+1 ) }}" class="accordion-collapse collapse {{ $key == 0 ? ' show' : '' }}" data-bs-parent="#accordionExample">
						<div class="accordion-body content-3 text-muted">
							{!! $faq->description !!}
						</div>
					</div>
				</div>
			@endforeach 
		</div>
	</div>
</div>  
@push('js')
<script>
	$(document).ready(function() {
		// Event listener for search input
		$('#faqSearch').on('keyup', function() {
			var searchQuery = $(this).val().toLowerCase(); 
			// Loop through all FAQ items
			$('.accordion-item').each(function() {
				var title = $(this).find('.accordion-button').text().toLowerCase();
				
				// If the title includes the search query, show the item; otherwise, hide it
				if (title.indexOf(searchQuery) !== -1) {
					$(this).show();  // Show the FAQ item
				} else {
					$(this).hide();  // Hide the FAQ item
				}
			});
		});
	});
</script>
@endpush