<script> 
	// Toastr options (optional)
	toastr.options = {
		"closeButton": true,
		"progressBar": true,
		"positionClass": "toast-top-right",
		"timeOut": "3000",
	};
		
	@if(Session::has('info')) 
		toastr.success("{{ session('info') }}", 'info');
	@endif
	
	@if(Session::has('error'))
		toastr.success("{{ session('error') }}", 'error');
	@endif
	
    @if(Session::has('success'))
		toastr.success("{{ session('success') }}", 'success');
    @endif
	
	@if(Session::has('warning'))
		toastr.success("{{ session('warning') }}", 'warning');
	@endif
	
	function toastrMsg(type, msg) {
		// Make sure toastr is available
		if (typeof toastr !== "undefined") {
			// Display the message based on the type
			toastr[type](msg, type); 
		} else {
			console.error("Toastr is not defined.");
		}
	}
	 
	const secretKey = @json($cryptoKey);

	// Encrypt function
	function encryptData(data) {
		const key = CryptoJS.enc.Base64.parse(secretKey);
		const encrypted = CryptoJS.AES.encrypt(data, key, {
			mode: CryptoJS.mode.ECB,
			padding: CryptoJS.pad.Pkcs7
		}).toString();
		return encrypted;
	}

	// Decrypt function
	function decryptData(encryptedData) {
		const key = CryptoJS.enc.Base64.parse(secretKey);
		const decryptedBytes = CryptoJS.AES.decrypt(encryptedData, key, {
			mode: CryptoJS.mode.ECB,
			padding: CryptoJS.pad.Pkcs7
		});
		const decryptedText = decryptedBytes.toString(CryptoJS.enc.Utf8);
		return JSON.parse(decryptedText);
	}
	 
</script>