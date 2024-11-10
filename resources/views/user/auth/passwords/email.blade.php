<h6 class="fw-semibold text-black text-center mb-4">Forgot Password?</h6> 
<label for="terms" class="d-flex text-center text-secondary font-md mb-3">
	If you've forgotten your password, simply follow the instructions to reset it and regain access.
</label>
<form id="resetFormMail" action="{{ route('password.sendOtp') }}" method="post">
	<div class="mb-4">
		<label for="email" class="required text-black font-md mb-2">Email</label>
		<div class="input-group">
			<input type="text" class="form-control border-0 bg-light" id="email" name="email" placeholder="Enter your email">
		</div> 
	</div>
	<div class="text-center">
		<button type="submit" class="btn btn-primary w-100">Send</button>
	</div>
</form>
