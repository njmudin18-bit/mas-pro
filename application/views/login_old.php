<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
  	<title>Log In | <?php echo $perusahaan->nama; ?></title>
    <meta name="title" content="<?php echo APPS_NAME; ?> | <?php echo $perusahaan->nama; ?>">
		<meta name="description" content="<?php echo $perusahaan->nama; ?>">
		<meta name="keywords" content="<?php echo $perusahaan->nama; ?>">
		<meta name="subject" content="Login Form <?php echo APPS_NAME; ?>">
		<meta name="language" content="ID">
		<meta name="author" content="IT Department - <?php echo $perusahaan->nama; ?>">
		<meta name="designer" content="IT Department - <?php echo $perusahaan->nama; ?>">
		<meta name="copyright" content="<?php echo $perusahaan->nama; ?> &copy; 2022">
		<meta name="url" content="<?php echo base_url(); ?>">
		<meta name="identifier-URL" content="<?php echo base_url(); ?>">
		<meta name="robots" content="index, follow" />
		<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
		<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
		<link rel="canonical" href="<?php echo base_url(); ?>" />

		<!-- Open Graph / Facebook -->
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo base_url(); ?>">
		<meta property="og:title" content="<?php echo APPS_NAME; ?> | <?php echo $perusahaan->nama; ?>">
		<meta property="og:description" content="<?php echo $perusahaan->nama; ?>">
		<meta property="og:image" content="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">

		<!-- Twitter -->
		<meta property="twitter:card" content="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>">
		<meta property="twitter:url" content="<?php echo base_url(); ?>">
		<meta property="twitter:title" content="<?php echo APPS_NAME; ?> | <?php echo $perusahaan->nama; ?>">
		<meta property="twitter:description" content="<?php echo $perusahaan->nama; ?>">
		<meta property="twitter:image" content="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>">

		<link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">

    <!-- <link rel="icon" href="<?php echo base_url(); ?>files/assets/images/favicon.ico" type="image/x-icon"> -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/pages/waves/css/waves.min.css" type="text/css" media="all"> 
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/feather/css/feather.css">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/themify-icons/themify-icons.css">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/icofont/css/icofont.css">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/pages.css">
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
            <form id="login_form" action="#" method="post" autocomplete="off" class="md-float-material form-material">
              <div class="text-center">
                <img src="<?php echo base_url(); ?>files/uploads/logos/logo-mas.png" alt="logo.png">
              </div>
              <div class="auth-box card">
                <div class="card-block">
                  <div class="row m-b-20">
                    <div class="col-md-12">
                      <h3 class="text-center txt-primary">Sign In</h3>
                    </div>
                  </div>
                  <p class="text-muted text-center p-b-5"><?php echo strtoupper(APPS_NAME); ?></p>
                  <div class="form-group form-primary">
                    <input type="text" id="username" name="username" class="form-control" required="required" autofocus="on" value="" placeholder="Username"><!--ubah reza 20221213-->
                    <span class="form-bar"></span>
                    <!-- <label class="float-label">Username</label> -->
                  </div>
                  <div class="form-group form-primary">
                    <input type="password" id="password" name="password" class="form-control" required="required" value="" placeholder="Password"><!--ubah reza 20221213-->
                    <span class="form-bar"></span>
                    <!-- <label class="float-label">Password</label> -->
                  </div>
                  <div class="form-group form-primary d-flex align-items-center justify-content-center">
                    <?php //echo $captcha; ?>
                  </div>
                  <!-- <div class="form-group form-primary d-flex align-items-center justify-content-center">
                  	<div class="g-recaptcha" data-sitekey="<?php //echo $this->config->item('site_key'); ?>"></div>
                  </div> -->
                  <div class="row m-t-30">
                    <div class="col-md-12">
                      <button type="button_login" id="button_login" type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">LOGIN</button>
                    </div>
                  </div>
                  <!-- <p class="text-inverse text-left">Don't have an account?<a href="auth-sign-up-social.html"> <b>Register here </b></a>for free!</p> -->
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

	  <script src="<?php echo base_url(); ?>files/assets/pages/waves/js/waves.min.js"></script>

	  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>

	  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/modernizr.js"></script>
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/css-scrollbars.js"></script>
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/common-pages.js"></script>
	  <script src="<?php echo base_url(); ?>files/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script type="text/javascript">
			$(function () {
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
		   		errorPlacement: function (error, element) {
		      	error.addClass('invalid-feedback');
		      	element.closest('.form-group').append(error);
		    	},
		    	highlight: function (element, errorClass, validClass) {
		      	$(element).addClass('is-invalid');
		    	},
		    	unhighlight: function (element, errorClass, validClass) {
		      	$(element).removeClass('is-invalid');
		    	}
		  	});
		  	function loginAction(){  
      		var data = $("#login_form").serialize();
      		$.ajax({
        		type : 'POST',
        		url : "<?php echo base_url(); ?>welcome/login_proses",
        		data : data,
            beforeSend: function(){ 
            	$("#error").fadeOut();
            	$("#button_login").prop('disabled', true);
            	$("#button_login").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success :  function(response){
            	const res = JSON.parse(response);
            	if (res.status_code == 400 || res.status_code == 404 || res.status_code == 401) {
								Swal.fire({
								  icon: 'info',
								  title: 'Oops...',
								  text: res.message
								})
            	} else {
            		$("#button_login").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Masuk aplikasi...');
                setTimeout('window.location.href = "'+ res.url +'"', 500);
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