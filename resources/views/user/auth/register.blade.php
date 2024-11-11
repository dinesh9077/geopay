<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ env('APP_NAME') }} | Register</title>
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
	</head>
	
	<body>
		<div class="container-fluid">
			<div class="row min-vh-100">
				<!-- Left Section -->
				<div class="col-lg-7 d-none d-lg-flex align-items-end justify-content-start text-white auth-left-image-banner">
					<div class="content-wrapper m-4">
						<div class="mb-4">
							<img class="mb-4" src="{{ asset('assets/image/icons/spark-icon.svg') }}" alt="">
							<h2 class="mb-4">Very Good Works are waiting for you. Login now.</h2>
							<p class="font-sm">Access your account to explore exclusive features, personalized content, and stay up-to-date with the latest updates.</p>
						</div>
						<div class="d-flex align-items-center">
							<div class="avatar-group me-3">
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-1.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 1"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-2.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 2"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-3.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 3"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-4.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 4"></div>
							</div>
							<div class="d-flex flex-column ms-2">
								<div class="d-flex gap-2 mb-1">
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
								</div>
								<span class="text-sm">From 200+ reviews</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Right Form Section -->
				<div class="col-lg-5 d-flex align-items-center justify-content-center position-relative bg-white z-1">
					<div id="container" class="container d-flex align-items-center justify-content-center py-4">
						<div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div>
						<div class="w-100 px-4 register-form-container z-2">
							<h6 class="fw-semibold text-black text-center mb-4">Register</h6>
							<ul class="nav nav-pills my-3 d-flex flex-nowrap justify-content-center gap-2" id="pills-tab" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link px-5 active" id="register-individual-tab" data-bs-toggle="pill"
                                    data-bs-target="#register-individual" type="button" role="tab" aria-controls="register-individual"
                                    aria-selected="true">Individual</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link px-5" id="register-company-tab" data-bs-toggle="pill"
                                    data-bs-target="#register-company" type="button" role="tab" aria-controls="register-company"
                                    aria-selected="false">Company</button>
								</li>
							</ul>
							
							<div class="tab-content" id="pills-tabContent">
								<div class="tab-pane fade show active" id="register-individual" role="tabpanel"
                                aria-labelledby="register-individual-tab"> 
									<form id="individualRegisterForm" action="{{ route('register.submit') }}" method="post">
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="first_name" class="required font-md">First Name <span class="text-danger">*</span></label>
												<input id="first_name" name="first_name" type="text" class="form-control bg-light border-light"/>
											</div>
											<div class="col-md-6 mb-3">
												<label for="last_name" class="required font-md">Last Name <span class="text-danger">*</span></label>
												<input id="last_name" name="last_name" type="text" class="form-control bg-light border-light"/> 
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="email" class="required font-md">Email <span class="text-danger">*</span></label>
												<div class="input-group">
													<input id="email" name="email" type="email" class="form-control bg-light border-light" />
													<a class="input-group-text bg-light border-2 verify-text" data-bs-toggle="modal" data-bs-target="#myModal">Verify</a>
												</div> 
											</div>
											<div class="col-md-6 mb-3">
												<label for="password" class="required font-md">Password <span class="text-danger">*</span></label>
												<input id="password" name="password" type="password"
                                                class="form-control bg-light border-light" />
												<div class="text-danger" id="password_error"></div>
											</div> 
										</div>
										<div class="row">
											
											<div class="col-md-6 mb-3">
												<label for="confirmPassword" class="required font-md">Confirm Password <span class="text-danger">*</span></label>
												<input id="confirmPasswordIndividual" name="confirmPasswordIndividual" type="password"
                                                class="form-control bg-light border-light" />
												<div class="text-danger" id="confirmPassword_error"></div>
											</div>
											<div class="col-md-6 mb-3">
												<label for="country" class="required font-md">Country <span class="text-danger">*</span></label>
												<select id="country" name="country_id" class="form-control bg-light border-light">
													<option value="">Select Country</option>
													<option value="1">United States</option>
													<option value="2">Canada</option>
													<option value="3">United Kingdom</option>
												</select>
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="mobile_number" class="required font-md">Mobile Number <span class="text-danger">*</span></label>
												<input id="mobile_number" name="mobile_number" type="tel"
                                                class="form-control bg-light border-light" pattern="[0-9]*" inputmode="numeric" />
												<div class="text-danger" id="mobile_number_error"></div>
											</div>
											
											<div class="col-md-6 mb-3">
												<label for="referalcode" class="required font-md">Promo Code</label>
												<input id="referalcode" name="referalcode" type="text" class="form-control bg-light border-light"/> 
											</div> 
										</div> 
										<div class="mb-3">
											<div class="d-flex">
												<input type="checkbox" id="terms" name="terms" required class="me-2 font-md" />
												<label for="terms" class="d-flex text-secondary font-md"> I have read the User agreement and I accept it</label>
											</div> 
										</div> 
										<div class="text-center">
											<button type="submit" class="btn btn-primary w-100 font-md">Register</button>
										</div>
									</form>
								</div>
								<div class="tab-pane fade" id="register-company" role="tabpanel" aria-labelledby="register-company-tab">
									<form class="mt-4">
										<!-- Company Form 1 -->
										<div class="step step-1">
											<div class="row mb-3">
												<div class="col-md-6">
													<label for="firstName">First Name</label>
													<input type="text" id="firstName" name="firstName"
                                                    class="form-control bg-light border-light">
													<div class="text-danger" id="firstName-error"></div>
												</div>
												<div class="col-md-6">
													<label for="lastName">Last Name</label>
													<input type="text" id="lastName" name="lastName"
                                                    class="form-control bg-light border-light">
													<div class="text-danger" id="lastName-error"></div>
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-md-6">
													<label for="mobile">Mobile Number</label>
													<div class="d-flex">
														<div class="col-4">
															<select class="form-control bg-light border-light">
																<option>+1</option>
															</select>
														</div>
														<div class="col-8 d-flex">
															<input type="text" id="mobile" name="mobile"
                                                            class="form-control bg-light border-light ms-2">
														</div>
													</div>
													<div class="text-danger" id="mobile-error"></div>
												</div>
												<div class="col-md-6">
													<label for="companyEmail" class="required font-md">Email</label>
													<div class="input-group">
														<input id="companyEmail" name="companyEmail" type="email"
                                                        class="form-control bg-light border-light" />
														<!-- <a class="input-group-text bg-light border-0" data-bs-toggle="modal" data-bs-target="#myModal">&#9993;</a> -->
													</div>
													<div class="text-danger" id="email_error"></div>
												</div>
											</div>
											<div class="row mb-4">
												<div class="col-md-6">
													<label for="password">Password</label>
													<input type="password" id="companyPassword" name="companyPassword"
                                                    class="form-control bg-light border-light">
													<div class="text-danger" id="password-error"></div>
												</div>
												<div class="col-md-6">
													<label for="confirmPassword">Confirm Password</label>
													<input type="password" id="confirmPassword" name="confirmPassword"
                                                    class="form-control bg-light border-light">
													<div class="text-danger" id="confirmPassword-error"></div>
												</div>
											</div>
											<div class="text-center">
												<button type="button" class="btn btn-primary w-100 font-md">Register</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Email Verification Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel"
        aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="d-flex justify-content-center align-items-center">
						<img src="favicon.png" width="50" class="modal-logo">
					</div>
					<div class="modal-body p-4 mt-4">
						<form>
							<div class="mb-4">
								<b class="text-center d-block mb-3">Verify the OTP</b>
								<input type="text" class="form-control" id="otp" name="otp"
                                placeholder="Enter OTP">
							</div>
							<div class="text-center">
								<button type="submit" class="btn btn-primary w-100">Verify</button>
							</div>
						</form>
					</div>                                                    
				</div>
			</div>
		</div>  
		<script src="https://kit.fontawesome.com/ae360af17e.js" ></script>
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
	</body> 
</html>
