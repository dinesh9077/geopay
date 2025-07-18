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
		$('#faqSearch').on('keyup', function() {
			var searchQuery = $(this).val().toLowerCase(); 
			var matchFound = false;

			$('.accordion-item').each(function(index) {
				var title = $(this).find('.accordion-button').text().toLowerCase();
				var description = $(this).find('.accordion-body').text().toLowerCase();
				var collapseBody = $(this).find('.accordion-collapse');

				if (title.includes(searchQuery) || description.includes(searchQuery)) {
					$(this).show();

					// Only open the first matching accordion
					if (!matchFound) {
						matchFound = true;
						// Open this one
						collapseBody.addClass('show');
					} else {
						collapseBody.removeClass('show');
					}
				} else {
					$(this).hide();
					collapseBody.removeClass('show');
				}
			});

			// If no matches found, open the first visible (default/fallback)
			if (!matchFound) {
				var firstItem = $('.accordion-item:visible').first();
				if (firstItem.length) {
					firstItem.find('.accordion-collapse').addClass('show');
				}
			}
		});
	});
</script>
@endpush
