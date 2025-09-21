@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Add Funds')
@section('header_title', 'Add Funds')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			
			<div class="tab-content" id="pills-tabContent"> 
				<div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
					<div class="card-body">
						<h5 class="card-title mb-3">Enter Card Details</h5>
						
						<!-- Placeholder for errors -->
						<div id="form-messages"></div>
						
						<!-- IMPORTANT: in production, use the gateway JS tokenization and NOT this form submit directly -->
						<form id="paymentForm" method="post" action="{{ route('deposit.payment-link') }}"> 
							<div class="mb-3" style="display:none">
								<label for="cardtype" class="form-label">Card Type <span class="text-danger">*</span></label>
								<select class="form-select" id="cardtype" name="cardtype" required>
									<option value="">-- Select Card Type --</option>
									<option value="visa">Visa</option>
									<option value="mastercard">MasterCard</option>
									<option value="amex">American Express</option>
									<option value="discover">Discover</option>
									<option value="diners">Diners</option>
								</select>
							</div>
	
							<div class="mb-3">
								<label class="form-label">Cardholder Name <span class="text-danger">*</span></label>
								<input type="text" name="cardname" id="cardname" class="form-control"  autocomplete="cc-name" required/>
								<div class="invalid-feedback">Please enter the name on card.</div>
							</div>
							
							<div class="mb-3">
								<label class="form-label">Card Number <span class="text-danger">*</span></label>
								<div class="input-group">
									<input type="tel" inputmode="numeric" pattern="[0-9\s]{13,19}" maxlength="23"
									name="cardnumber" id="cardnumber" class="form-control" placeholder="•••• •••• •••• ••••"
									autocomplete="cc-number" required />
									<span class="input-group-text card-type" id="card-type-display">—</span>
								</div>
								<div class="invalid-feedback" id="cardnumber-feedback">Enter a valid card number.</div>
							</div>
							
							<div class="row">
								<div class="col-6 mb-3">
									<label class="form-label">Expiry Month <span class="text-danger">*</span></label>
									<select name="month" id="month" class="form-select"  autocomplete="cc-exp-month" required>
										<option value="">Month</option>
										<!-- 01..12 -->
										<script> for(let m=1;m<=12;m++){ document.write(`<option value="${String(m).padStart(2,'0')}">${String(m).padStart(2,'0')}</option>`); } </script>
									</select>
									<div class="invalid-feedback">Select expiry month.</div>
								</div>
								
								<div class="col-6 mb-3">
									<label class="form-label">Expiry Year <span class="text-danger">*</span></label>
									<select name="year" id="year" class="form-select"  autocomplete="cc-exp-year" required>
										<option value="">Year</option>
										<script>
											const start = new Date().getFullYear();
											for(let y = start; y <= start + 12; y++){
												document.write(`<option value="${y}">${y}</option>`);
											}
										</script>
									</select>
									<div class="invalid-feedback">Select expiry year.</div>
								</div>
							</div>
							
							<div class="mb-3">
								<label class="form-label">CVV / CVC <span class="text-danger">*</span></label>
								<input type="tel" inputmode="numeric" name="cvv" id="cvv" class="form-control" placeholder="123" autocomplete="cc-csc"required />
								<div class="invalid-feedback" id="cvv-feedback">Enter valid CVV.</div>
							</div>
							<div class="mb-3">
								<label class="form-label">Amount <span class="text-danger">*</span></label>
								<input type="number" inputmode="numeric" name="amount" id="amount" class="form-control" placeholder="Enter Amount"  required /> 
							</div>
							<input type="hidden" inputmode="numeric" name="netAmount" id="netAmount" class="form-control" /> 
							<input type="hidden" inputmode="numeric" name="platformCharge" id="platformCharge" class="form-control" /> 
							
							<div class="col-12" id="commissionHtml"></div>  
							<div class="d-flex justify-content-between"> 
								<button type="submit" class="btn btn-primary">Pay</button>
							</div>
						</form> 
					</div> 
				</div> 
			</div>
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection

@push('js')
<script>
	const commissionType = @json($commissionType);
	const commissionCharge = parseFloat(@json($commissionCharge)); // ✅ ensure numeric
	const remitCurrency = @json($remitCurrency);

	$('#paymentForm #amount').on('input', function() {  
		const amount = parseFloat($(this).val()) || 0;
		let commissionAmount = 0;

		if (commissionType === 'flat') {
			commissionAmount = commissionCharge; 
		} else if (commissionType === 'percentage') {
			commissionAmount = (amount * commissionCharge) / 100;
		}

		// force numeric safety
		commissionAmount = parseFloat(commissionAmount) || 0;

		$('#platformCharge').val(commissionAmount.toFixed(2));

		const netAmount = amount + commissionAmount;
		$('#netAmount').val(netAmount.toFixed(2));

		const commissionDetails = `
			<div class="w-100 text-start mb-3 p-2 rounded-2 border g-2 removeCommission">
				<div class="w-100 row m-auto">
					<div class="col-6 col-md-6">
						<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
							Processing Fee (${remitCurrency})
							<div class="text-muted fw-normal">${commissionAmount.toFixed(2)}</div>
						</span>
					</div>
					<div class="col-6 col-md-6">
						<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
							Net Amount In ${remitCurrency}
							<div class="text-muted fw-normal">${netAmount.toFixed(2)}</div>
						</span>
					</div> 
				</div> 
			</div>`;

		$('#paymentForm #commissionHtml').html(commissionDetails);
	});
	 
		 
	$(function () {
		
		const CARD_TYPES = {
			visa: /^4[0-9]{0,}$/,                                // Visa
			mastercard: /^(5[1-5][0-9]{0,}|2[2-7][0-9]{0,})$/,    // Mastercard
			amex: /^3[47][0-9]{0,}$/,                             // American Express
			diners: /^3(?:0[0-5]|[68][0-9])[0-9]{0,}$/,           // Diners Club
			discover: /^6(?:011|5[0-9]{2})[0-9]{0,}$/,            // Discover
			jcb: /^(?:35[2-8][0-9]{0,})$/,                        // JCB (3528–3589)
			unionpay: /^62[0-9]{0,}$/,                            // China UnionPay
			maestro: /^(50|5[6-9]|6[0-9])[0-9]{0,}$/              // Maestro / EFTPOS
			// Cartes Bancaires = handled by Visa/Mastercard regex
		};

		const CARD_LABELS = {
			visa: "Visa",
			mastercard: "Mastercard",
			amex: "American Express",
			diners: "Diners Club",
			discover: "Discover",
			jcb: "JCB",
			unionpay: "China UnionPay",
			maestro: "Maestro / EFTPOS",
			unknown: "—"
		};

		const $form = $("#paymentForm");
		const $cardNumber = $("#cardnumber");
		const $cardTypeDisplay = $("#card-type-display");
		const $cardTypeHidden = $("#cardtype");
		const $cvv = $("#cvv");
		const $messages = $("#form-messages");

		// remove maxlength from HTML, handle dynamically
		$cvv.removeAttr("maxlength");

		// format number (add spaces every 4 digits)
		function formatCardNumber(val) {
			return val.replace(/\D/g, "").replace(/(.{4})/g, "$1 ").trim();
		}

		// detect card type
		function detectCardType(num) {
			const digits = num.replace(/\s+/g, "");
			for (const [type, pattern] of Object.entries(CARD_TYPES)) {
				if (pattern.test(digits)) return type;
			}
			return "unknown";
		}

		// Luhn check
		function luhnCheck(num) {
			const arr = (num + "").split("").reverse().map(x => parseInt(x, 10));
			let sum = 0;
			for (let i = 0; i < arr.length; i++) {
				let val = arr[i];
				if (i % 2 === 1) {
					val *= 2;
					if (val > 9) val -= 9;
				}
				sum += val;
			}
			return sum % 10 === 0;
		}

		// on input card number
		$cardNumber.on("input", function () {
			const formatted = formatCardNumber($(this).val());
			$(this).val(formatted);

			const type = detectCardType(formatted);
			$cardTypeDisplay.text(CARD_LABELS[type] || "—");
			$cardTypeHidden.val(type);

			// update CVV max length (Amex uses 4, others use 3)
			$cvv.attr("maxlength", type === "amex" ? 4 : 3);
		});

		// utility alert
		function showAlert(type, html) {
			$messages.html(`
				<div class="alert alert-${type} alert-dismissible fade show" role="alert">
					${html}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			`);
		} 
		
		/* const CARD_TYPES = {
			visa: /^4[0-9]{0,}$/,
			mastercard: /^(5[1-5]|2[2-7])[0-9]{0,}$/,
			amex: /^3[47][0-9]{0,}$/,
			diners: /^3(?:0[0-5]|[68][0-9])[0-9]{0,}$/,
			discover: /^6(?:011|5[0-9]{2})[0-9]{0,}$/,
		};

		const $form = $("#paymentForm");
		const $cardNumber = $("#cardnumber");
		const $cardTypeDisplay = $("#card-type-display");
		const $cardTypeHidden = $("#cardtype");
		const $cvv = $("#cvv");
		const $messages = $("#form-messages");

		// remove maxlength from HTML, handle dynamically
		$cvv.removeAttr("maxlength");

		// format number (spaces)
		function formatCardNumber(val) {
			return val.replace(/\D/g, "").replace(/(.{4})/g, "$1 ").trim();
		}

		// detect card type
		function detectCardType(num) {
			const digits = num.replace(/\s+/g, "");
			for (const [type, pattern] of Object.entries(CARD_TYPES)) {
				if (pattern.test(digits)) return type;
			}
			return "unknown";
		}

		// Luhn check
		function luhnCheck(num) {
			const arr = (num + "").split("").reverse().map(x => parseInt(x, 10));
			let sum = 0;
			for (let i = 0; i < arr.length; i++) {
				let val = arr[i];
				if (i % 2 === 1) {
					val *= 2;
					if (val > 9) val -= 9;
				}
				sum += val;
			}
			return sum % 10 === 0;
		}

		// on input card number
		$cardNumber.on("input", function () {
			const formatted = formatCardNumber($(this).val());
			$(this).val(formatted);

			const type = detectCardType(formatted);
			$cardTypeDisplay.text(type === "unknown" ? "—" : type.toUpperCase());
			$cardTypeHidden.val(type);

			// update CVV max length
			$cvv.attr("maxlength", type === "amex" ? 4 : 3);
		});

		// utility alert
		function showAlert(type, html) {
			$messages.html(`
				<div class="alert alert-${type} alert-dismissible fade show" role="alert">
					${html}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			`);
		} */

		// submit form
		$form.on("submit", function (e) {
			e.preventDefault();
			$messages.empty();

			const cardname = $("#cardname").val().trim();
			const cardnumber = $cardNumber.val().replace(/\s+/g, "");
			const month = $("#month").val();
			const year = $("#year").val();
			const cvv = $cvv.val().trim();
			const ctype = $cardTypeHidden.val();

			let errors = [];

			if (!cardname) errors.push("Cardholder name is required.");
			if (!cardnumber || !/^\d{13,19}$/.test(cardnumber)) errors.push("Card number is invalid.");
			else if (!luhnCheck(cardnumber)) errors.push("Card number failed Luhn check.");

			if (!month) errors.push("Expiry month is required.");
			if (!year) errors.push("Expiry year is required.");

			if (month && year) {
				const exp = new Date(Number(year), Number(month) - 1, 1);
				const now = new Date();
				const expEnd = new Date(exp.getFullYear(), exp.getMonth() + 1, 0, 23, 59, 59);
				if (expEnd < now) errors.push("Card has expired.");
			}

			if (!cvv || !/^\d{3,4}$/.test(cvv)) errors.push("CVV is invalid.");
			if (ctype === "amex" && cvv.length !== 4) errors.push("AMEX requires 4-digit CVV.");
			if (ctype !== "amex" && cvv.length !== 3) errors.push("CVV must be 3 digits.");

			if (errors.length) {
				showAlert("danger", `<strong>Fix these errors:</strong><br>${errors.join("<br>")}`);
				return;
			}

			$form.find('[type="submit"]')
			.prop('disabled', true) 
			.addClass('loading-span') 
			.html('<span class="spinner-border"></span>');

			var formData = {};
			$(this).find('input, select, textarea').each(function() {
				var inputName = $(this).attr('name'); 
				formData[inputName] = $(this).val();
			});
			
			formData['category_name'] = 'add money'; 
			formData['service_name'] = 'deposit card payment';  
			console.log(formData);
			// Encrypt data before sending
			const encrypted_data = encryptData(JSON.stringify(formData));
			
			$.ajax({
				async: true,
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
				cache: false, 
				dataType: 'Json', 
				success: function (res) 
				{ 
					$form.find('[type="submit"]')
					.prop('disabled', false)  
					.removeClass('loading-span') 
					.html('Submit'); 
					
					$('.error_msg').remove(); 
					if(res.status === "success")
					{ 
						var result = decryptData(res.response);
						window.location.href = result.payment_link;
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) 
						{
							var inputField = $form.find('#' + key);
							var errorSpan = $('<span>')
							.addClass('error_msg text-danger content-4') 
							.attr('id', key + 'Error')
							.text(value[0]); 
						 
							inputField.parent().append(errorSpan);
						});
					}
					else
					{ 
						toastrMsg(res.status, res.message);
					}
				} 
			}); 
		});
	});
   
    document.addEventListener("DOMContentLoaded", function () {
		const params = new URLSearchParams(window.location.search);
		const status = params.get('status');

		if (status) {
			let alertOptions = {};

			if (status.toLowerCase() === 'authorised') {
				alertOptions = {
					icon: 'success',
					title: 'Payment Authorized!',
					text: 'Your payment was authorized. It will be reviewed shortly and the final status (approved/rejected) will be updated. You can check your transaction list for the latest update.',
					confirmButtonColor: '#3085d6',
				};
			} else if (status.toLowerCase() === 'failed') {
				alertOptions = {
					icon: 'error',
					title: 'Payment Failed!',
					text: 'Your payment could not be processed. Please try again.',
					confirmButtonColor: '#d33',
				};
			} else {
				alertOptions = {
					icon: 'info',
					title: 'Payment Status',
					text: 'Payment status: ' + status,
				};
			}

			Swal.fire(alertOptions).then(() => {
				// ✅ Remove query string without reload
				window.history.replaceState({}, document.title, window.location.pathname);
			});
		}
	});

</script>
@endpush
