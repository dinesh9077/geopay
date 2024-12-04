<button type="button" class="btn btn-warning w-100"
	data-block-text="{{ $blockText }} Account"
	data-block-msg="{{ $blockMsg }}"
	data-status="{{ $newStatus }}"
	onclick="banAccount(this, event)">
	<i data-feather="{{ $icon }}" style="height: 16px;"></i>
	{{ $blockText }} Account
</button>
