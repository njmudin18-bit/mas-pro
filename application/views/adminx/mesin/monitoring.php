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
    <!-- <meta http-equiv="refresh" content="<?php //echo APPS_REFRESH; ?>"> -->
    <meta http-equiv="refresh" content="900">
    
    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <style type="text/css">
      #monitoring_value h5 {
        font-size: 13px;
      }

      #monitoring_value h6 {
        font-size: 11px;
      }

      #monitoring_value .col {
        padding-right: 2px;
        padding-left: 2px;
      }

      #monitoring_value .form-control {
        font-size: 12px;
      }

      #loader {
        position: absolute;
        top: 150%;
        left: 50%;
        transform: translate(-50%, -50%);
      }

      .border-green{
        border: 1px solid #2ed8b6;
        border-radius: 5px;
      }

      .border-danger{
        border: 1px solid #ff5370;
        border-radius: 5px;
      }

      .pcoded[theme-layout=vertical][vertical-placement=left][vertical-nav-type=expanded][vertical-effect=shrink] .pcoded-content {
        margin-left: 0px;
      }

      .card {
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body>

    <div class="loader-bg">
      <div class="loader-bar"></div>
    </div>

    <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box"></div>
      <div class="pcoded-container navbar-wrapper">

        <?php //$this->load->view('adminx/components/navbar'); ?>

        <?php //$this->load->view('adminx/components/navbar_chat'); ?>

      	<div class="pcoded-main-container">
        	<div class="pcoded-wrapper">

          	<?php //$this->load->view('adminx/components/sidebar'); ?>

						<div class="pcoded-content">

						  <?php //$this->load->view('adminx/components/breadcrumb'); ?>

						  <div class="pcoded-inner-content">
						    <div class="main-body">
						      <div class="page-wrapper">
						        <div class="page-body">
						          <div class="row" id="monitoring_value">
                        <div id='loader'></div>
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
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	  <script>
      //FUNCTION OPEN DETAIL
      function open_details(id) {
        Object.assign(document.createElement('a'), {
          target: '_blank',
          rel: 'noopener noreferrer',
          href: '<?php echo base_url(); ?>mesin/show_data_mesin_detail/' + id
        }).click();
      }

	    //FUNGSI CALL DATA
      function show_data() {
        $.ajax({
          url : "<?php echo base_url(); ?>monitoring/show_data_mesin",
          type: "GET",
          dataType: "JSON",
          beforeSend: function () {
            $("#loader").html('<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading...</span></div>');
          },
          success: function(data)
          {
            //show_data();
            $("#loader").hide();
            $("#monitoring_value").html(data.html);
          },
          complete: function function_name(data) {
            setTimeout(function(){
              show_data();
            }, 1000);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            show_data();
            //alert('Error get data from ajax');
          }
        });
      }

	    $(document).ready(function() {
        show_data();
	    });
	  </script>
	</body>
</html>