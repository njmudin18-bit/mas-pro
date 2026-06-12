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
						            		<img alt="Card image cap" class="card-img-top img-fluid" src="<?php echo base_url(); ?>files/assets/images/forbidden_image.png" style="width: 100%; height: auto;" />
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
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	</body>
</html>