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
	
	function getQueryParams(url) {
		var queryString = url.split('?')[1];
		
		if (!queryString) {
			return {};
		}

		var queryParams = {};
		var queryArray = queryString.split('&');

		queryArray.forEach(function(pair) {
			var keyValue = pair.split('=');
			queryParams[keyValue[0]] = decodeURIComponent(keyValue[1] || '');
		});

		return queryParams;
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
	
	/* // Disable right-click on the entire document
	document.addEventListener('contextmenu', function(event) {
		event.preventDefault();  // Prevent right-click context menu
		return false; // Ensure no other context menu can be triggered
	});
	 
	// Prevent mouse button event that could trigger context menu (for right-click prevention)
	document.addEventListener('mousedown', function(event) {
		// Check if it's a right-click (button 2 is the right-click mouse button)
		if (event.button === 2) {
			event.preventDefault(); // Prevent right-click
		}
	});

	// Optionally, disable drag event, which could reveal the page source or cause issues
	document.addEventListener('dragstart', function(event) {
		event.preventDefault();  // Prevent dragging
	});

	// Prevent Developer Tools Access by monitoring focus events (URL bar & Dev Tools)
	let devToolsOpen = false;
	Object.defineProperty(document, 'hidden', {
		get: function() {
			devToolsOpen = true;
			return false;
		}
	});

	// Detect if the developer tools are open and prevent right-click while in this state
	setInterval(function() {
		if (devToolsOpen) {
			// Disable right-click on the page while developer tools are open
			document.body.style.pointerEvents = 'none';  
		} else {
			document.body.style.pointerEvents = 'auto';  // Allow interaction once developer tools are closed
		}
	}, 1000);

	document.onkeydown = function(event) {
		// Preventing common developer tools and inspect element shortcuts
		if (
			(event.key === 'F12') || // F12 - Developer Tools
			(event.ctrlKey && event.shiftKey && event.key === 'I' || event.ctrlKey && event.shiftKey && event.key === 'i') || // Ctrl + Shift + I - Developer Tools
			(event.ctrlKey && event.shiftKey && event.key === 'J' || event.ctrlKey && event.shiftKey && event.key === 'j') || // Ctrl + Shift + J - Console
			(event.ctrlKey && event.shiftKey && event.key === 'Z' || event.ctrlKey && event.shiftKey && event.key === 'z') || // Ctrl + Shift + Z - Console
			(event.ctrlKey && event.shiftKey && event.key === 'K' || event.ctrlKey && event.shiftKey && event.key === 'k') || // Ctrl + Shift + K - Console
			(event.ctrlKey && event.shiftKey && event.key === 'E' || event.ctrlKey && event.shiftKey && event.key === 'e') || // Ctrl + Shift + E - Console
			(event.shiftKey && event.key === 'F7') || 
			(event.shiftKey && event.key === 'F5') || 
			(event.shiftKey && event.key === 'F9') || 
			(event.shiftKey && event.key === 'F12') || 
			(event.shiftKey && event.key === 'F2') || 
			(event.ctrlKey && event.key === 'U' || event.ctrlKey && event.key === 'u') || // Ctrl + U - View Source
			(event.ctrlKey && event.key === 'C' || event.ctrlKey && event.key === 'c') || // Ctrl + C - In some browsers, used for copying the inspected code
			(event.ctrlKey && event.key === 'S' || event.ctrlKey && event.key === 's') || // Ctrl + S - Save Page, can be used for code inspection
			(event.ctrlKey && event.key === 'P' || event.ctrlKey && event.key === 'p') || // Ctrl + P - Print Page, also can open Developer Tools
			(event.key === 'F11') || // F11 - Full Screen, can sometimes be used to enter DevTools in some browsers
			(event.altKey && event.key === 'F12') // Alt + F12 - Developer Tools in some browsers
		) {
			event.preventDefault(); 
			alert(12)
		}
 
		if (event.button === 2) {
			event.preventDefault();  
		}  
	};  */
	
</script>