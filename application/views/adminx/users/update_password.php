<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
  	<title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="<?php echo APPS_DESC; ?>" />
    <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
    <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
    <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">

    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  </head>
  <body>

    <div class="loader-bg">
      <div class="loader-bar"></div>
    </div>

    <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box"></div>
      <div class="pcoded-container navbar-wrapper">

        <?php $this->load->view('adminx/components/navbar'); ?>

        <?php $this->load->view('adminx/components/navbar_chat'); ?>

      	<div class="pcoded-main-container">
        	<div class="pcoded-wrapper">

          	<?php $this->load->view('adminx/components/sidebar'); ?>

						<div class="pcoded-content">

						  <?php $this->load->view('adminx/components/breadcrumb'); ?>

						  <div class="pcoded-inner-content">
						    <div class="main-body">
						      <div class="page-wrapper">
						        <div class="page-body">
						          <div class="row">
						            <div class="col-sm-12">

						              <div class="card">
						                <div class="card-header text-center">
						                  <h5>
						                  	<?php echo strtoupper($nama_halaman); ?>
						                  </h5>
						                </div>
						                <div class="card-block">
                              <form id="update_pass_form" autocomplete="off">
  						                  <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">New Password</label>
                                  <div class="col-md-3 col-sm-12 m-t-30 new_div">
                                    <input type="password" id="new_password" name="new_password" class="form-control" required="required" autocomplete="off">
                                    <span class="help-block"></span>
                                  </div>
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">Confirm New Pass</label>
                                  <div class="col-md-3 col-sm-12 m-t-30 new_div">
                                    <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required="required" autocomplete="off">
                                    <span class="help-block"></span>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <button id="btn_update" type="submit" class="btn btn-primary btn-block">UPDATE</button>
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
								<div id="styleSelector"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	  <script type="text/javascript">
      $(function () {
        $.validator.setDefaults({
          submitHandler: loginAction
        });
        $('#update_pass_form').validate({
          rules: {
            new_password: {
              required: true,
              minlength: 5,
            },
            confirm_new_password: {
              required: true,
              minlength: 5,
              equalTo : "#new_password"
            }
          },
          errorElement: 'span',
          errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.new_div').append(error);
          },
          highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
          },
          unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
          }
        });

        function loginAction() {
          var data = $("#update_pass_form").serialize();
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url(); ?>users/update_password_action",
            data: data,
            beforeSend: function () {
              $("#error").fadeOut();
              $("#btn_update").prop('disabled', true);
              $("#btn_update").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function (response) {
              const res = JSON.parse(response);
              console.log(res);
              if(res.status) {
                Swal.fire(
                  'Good job!',
                  'Anda sukses mengganti password',
                  'success'
                );
                $('#update_pass_form')[0].reset();
              } else {
                Swal.fire(
                  'Oops!',
                  'Anda gagal mengganti password',
                  'info'
                )
              }

              $("#btn_update").prop('disabled', false);
              $("#btn_update").html('UPDATE');
            }
          });
          return false;
        }
      });
    </script>
	</body>
</html>