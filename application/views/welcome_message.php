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
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/css/util.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/Login_v11/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <style>
      .input100.is-invalid {
        border: 1px solid #dc3545;
      }
      
      .invalid-feedback {
        font-size: 11px;
        font-style: italic;
      }
    </style>
  </head>
  <body>
    <div class="limiter">
      <div class="container-login100">
        <div class="wrap-login100 p-l-50 p-r-50 p-t-25 p-b-30">
          <form id="login_form" class="login100-form validate-form">
            <span class="login100-form-title p-b-20">
              <img src="<?php echo base_url(); ?>assets/img/MASPro Black.png" alt="<?php echo $perusahaan->nama; ?>" width="70%" />
            </span>

            <div class="wrap-input100 m-b-16">
              <input class="input100" type="text" id="username" name="username" placeholder="Username">
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <span class="lnr lnr-user"></span>
              </span>
            </div>

            <!-- <div class="wrap-input100 m-b-16">
              <input class="input100" type="password" id="password" name="password" placeholder="Password">
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <span class="lnr lnr-lock"></span>
              </span>
            </div> -->

            <div class="wrap-input100 m-b-16" style="position: relative;">
                <input class="input100" type="password" id="password" name="password" placeholder="Password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock"></i>
                </span>

                <span id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;">
                    <i class="fa fa-eye"></i>
                </span>
            </div>

            <div class="contact100-form-checkbox m-l-4">
              <input class="input-checkbox100" type="checkbox" value="isRememberMe" id="rememberMe">
              <label class="label-checkbox100" for="rememberMe">
                Remember me
              </label>
            </div>

            <!-- <hr>
            <div class="form-group form-primary align-items-center justify-content-center m-t-10">
              <div class="">
                <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('site_key'); ?>"></div>
              </div>
            </div> -->

            <div class="container-login100-form-btn p-t-15">
              <button id="button_login" type="submit" onclick="isRememberMe()" class="login100-form-btn">
                LOGIN
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src="<?php echo base_url(); ?>files/Login_v11/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="<?php echo base_url(); ?>files/Login_v11/vendor/bootstrap/js/popper.js"></script>
    <script src="<?php echo base_url(); ?>files/Login_v11/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>files/Login_v11/vendor/select2/select2.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/Login_v11/js/main.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript">
      const rmCheck     = document.getElementById("rememberMe");
      const emailInput  = document.getElementById("username");

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
            element.closest('.wrap-input100').append(error);
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
              $("#button_login").html('Checking data...');
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
                $("#button_login").html('Masuk aplikasi...');
                setTimeout('window.location.href = "' + res.url + '"', 500);
              }

              $("#button_login").prop('disabled', false);
              $("#button_login").html('LogIn');
              grecaptcha.reset();
            }
          });
          return false;
        }

        var width = $('.g-recaptcha').parent().width();
        if (width < 302) {
          var scale = width / 302;
          $('.g-recaptcha').css('transform', 'scale(' + scale + ')');
          $('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
          $('.g-recaptcha').css('transform-origin', '0 0');
          $('.g-recaptcha').css('-webkit-transform-origin', '0 0');
        }
      });

      $(document).ready(function() {
          $("#togglePassword").click(function() {
              var input = $("#password");
              var icon = $(this).find("i");

              // 1. Toggle Tipe Input (Password <-> Text)
              if (input.attr("type") === "password") {
                  input.attr("type", "text");
              } else {
                  input.attr("type", "password");
              }

              // 2. Toggle Ikon (fa-eye <-> fa-eye-slash)
              // Ini akan menukar class secara otomatis
              icon.toggleClass("fa-eye fa-eye-slash");
          });
      });
    </script>
  </body>
</html>