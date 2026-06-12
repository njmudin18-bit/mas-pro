<!DOCTYPE html>
<html lang="en">

<head>
	<title>Log In | <?php echo $perusahaan->nama; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="<?php echo $perusahaan->nama; ?>" />
	<meta name="keywords" content="<?php echo $perusahaan->nama; ?>" />
	<meta name="author" content="IT Department - <?php echo $perusahaan->nama; ?>" />
	<link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap/css/bootstrap.min.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/pages/waves/css/waves.min.css" type="text/css" media="all">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/feather/css/feather.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/themify-icons/themify-icons.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/icofont/css/icofont.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/pages.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
	<style type="text/css">
		body.login {
			/* background-image: url('<?php echo base_url(); ?>files/assets/images/bg/orange_abstract.jpg'); */
			background-image: url('<?php echo base_url(); ?>files/assets/images/bg/gzvv-bg.png');
			background-size: cover;
			background-attachment: fixed;
		}

		.g-recaptcha {
			margin: 15px auto !important;
			width: auto !important;
			height: auto !important;
			text-align: -webkit-center;
			text-align: -moz-center;
			text-align: -o-center;
			text-align: -ms-center;
		}
	</style>
	<!-- <script src='<?php //echo base_url(); 
										?>files/assets/js/api.js'></script> -->
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body class="login" themebg-pattern="theme1">

	<div class="theme-loader">
		<div class="loader-track">
			<div class="preloader-wrapper">
				<div class="spinner-layer spinner-blue">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="gap-patch">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
				<div class="spinner-layer spinner-red">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="gap-patch">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
				<div class="spinner-layer spinner-yellow">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="gap-patch">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
				<div class="spinner-layer spinner-green">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="gap-patch">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<section class="login-block">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12">
					<form id="login_form" class="md-float-material form-material" autocomplete="off">
						<div class="text-center">
							<img src="<?php echo base_url(); ?>files/uploads/logos/logo-mas.png" alt="<?php echo $perusahaan->nama; ?>">
						</div>
						<div class="auth-box card">
							<div class="card-block">
								<div class="row m-b-20">
									<div class="col-md-12">
										<h3 class="text-center txt-primary"><?php echo strtoupper($perusahaan->nama); ?></h3>
									</div>
								</div>
								<p class="text-muted text-center p-b-5"><?php echo strtoupper(APPS_NAME); ?></p>
								<div class="form-group form-primary">
									<input type="text" id="username" name="username" class="form-control" autocomplete="off" required="required" autofocus="autofocus" placeholder="username">
									<span class="form-bar"></span>
									<!-- <label class="float-label">Username</label> -->
								</div>
								<div class="form-group form-primary">
									<input type="password" id="password" name="password" class="form-control" autocomplete="off" required="required" placeholder="password">
									<span class="form-bar"></span>
									<!-- <label class="float-label">Password</label> -->
								</div>
								<div class="row m-t-10 text-left">
									<div class="col-12">
										<div class="checkbox-fade fade-in-primary">
											<label>
												<input type="checkbox" value="isRememberMe" id="rememberMe">
												<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
												<span class="text-inverse">Remember me</span>
											</label>
										</div>
										<!-- <div class="forgot-phone text-right float-right">
												<a href="auth-reset-password.html" class="text-right f-w-600"> Forgot Password?</a>
											</div> -->
									</div>
								</div>
                <hr>
								<div class="form-group form-primary align-items-center justify-content-center m-t-25">
                  <div class="">
                    <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('site_key'); ?>"></div>
                  </div>
                </div>
								<div class="form-group form-primary m-t-25">
									<div class="">
										<button id="button_login" type="submit" onclick="isRememberMe()" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">
											LOGIN
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>

	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/popper.js/js/popper.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/assets/pages/waves/js/waves.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/modernizr.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/css-scrollbars.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/common-pages.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
	<script type="text/javascript">
		const rmCheck = document.getElementById("rememberMe");
		const emailInput = document.getElementById("username");

		if (localStorage.checkbox && localStorage.checkbox !== "") {
			rmCheck.setAttribute("checked", "checked");
			emailInput.value = localStorage.username;
		} else {
			rmCheck.removeAttribute("checked");
			emailInput.value = "";
		}

		function isRememberMe() {
			if (rmCheck.checked && emailInput.value !== "") {
				localStorage.username = emailInput.value;
				localStorage.checkbox = rmCheck.value;
			} else {
				localStorage.username = "";
				localStorage.checkbox = "";
			}
		}

		$(function() {
			$.validator.setDefaults({
				submitHandler: loginAction
			});
			$('#login_form').validate({
				rules: {
					username: {
						required: true,
						minlength: 3,
					},
					password: {
						required: true,
						minlength: 5
					}
				},
				errorElement: 'span',
				errorPlacement: function(error, element) {
					error.addClass('invalid-feedback');
					element.closest('.form-group').append(error);
				},
				highlight: function(element, errorClass, validClass) {
					$(element).addClass('is-invalid');
				},
				unhighlight: function(element, errorClass, validClass) {
					$(element).removeClass('is-invalid');
				}
			});

			function loginAction() {
				var data = $("#login_form").serialize();
				$.ajax({
					type: 'POST',
					url: "<?php echo base_url(); ?>welcome/login_proses",
					data: data,
					beforeSend: function() {
						$("#error").fadeOut();
						$("#button_login").prop('disabled', true);
						$("#button_login").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
					},
					success: function(response) {
						const res = JSON.parse(response);
						if (res.status_code == 400 || res.status_code == 404 || res.status_code == 401) {
							Swal.fire({
								icon: 'info',
								title: 'Oops...',
								text: res.message
							})
						} else {
							$("#button_login").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Masuk aplikasi...');
							setTimeout('window.location.href = "' + res.url + '"', 500);
						}

						$("#button_login").prop('disabled', false);
						$("#button_login").html('Log In');
						grecaptcha.reset();
					}
				});
				return false;
			}

			var width = $('.g-recaptcha').parent().width();
			console.log(width);
			if (width < 302) {
				var scale = width / 302;
				$('.g-recaptcha').css('transform', 'scale(' + scale + ')');
				$('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
				$('.g-recaptcha').css('transform-origin', '0 0');
				$('.g-recaptcha').css('-webkit-transform-origin', '0 0');
			}
		});
	</script>
</body>

</html>