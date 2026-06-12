<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE HTML>
<html>
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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    
    <!-- <script type="text/javascript" src="http://10.11.9.22:8080/omas-monitoring-projek/files/bower_components/jquery/js/jquery.min.js"></script> -->
    <script src="<?php echo base_url(); ?>files/vis-js/vis.min.js"></script>
    <link href="<?php echo base_url(); ?>files/vis-js/vis.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
      #visualization {
          position: relative;
      }

      .menu {
          position: absolute;
          top: 0;
          right: 0;
          margin: 10px;
          z-index: 9999;
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

                          <div id="visualization">
                            <div class="menu">
                                <!--<input type="button" id="toggleRollingMode" class="btn btn-secondary" value="toggleRollingMode"/> -->

                                <button type="button" id="zoomIn" class="btn btn-danger btn-sm" title="Zoom in"><i class="fa fa-search-plus fa-lg"></i></button>
                                <button type="button" id="zoomOut" class="btn btn-warning btn-sm" title="Zoom out"><i class="fa fa-search-minus fa-lg"></i></button>
                                <button type="button" id="moveLeft" class="btn btn-secondary btn-sm" title="Geser kiri"><i class="fa fa-chevron-left fa-lg"></i></button>
                                <button type="button" id="moveRight" class="btn btn-secondary btn-sm" title="Geser kanan"><i class="fa fa-chevron-right fa-lg"></i></button>
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

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $.ajax({
          url: "<?php echo base_url(); ?>schedule_tanggal/schedule_list",
          dataType: "json",
          beforeSend: function(data) {
            $("#loading-screen").show();
          },
          success: function(data) {
            console.log(data);

            var isi_event = [];
            data.data.forEach(element => {
              var array_data  = {
                id: element.id, 
                content: element.title, 
                start: element.start_date
              };
              isi_event.push(array_data)
            });

            // create a timeline with some data
            var container = document.getElementById('visualization');
            var items     = new vis.DataSet(isi_event);
            var options   = {};
            var timeline  = new vis.Timeline(container, items, options);

            /**
             * Move the timeline a given percentage to left or right
             * @param {Number} percentage   For example 0.1 (left) or -0.1 (right)
             */
            function move (percentage) {
                var range = timeline.getWindow();
                var interval = range.end - range.start;

                timeline.setWindow({
                    start: range.start.valueOf() - interval * percentage,
                    end:   range.end.valueOf()   - interval * percentage
                });
            }

            // attach events to the navigation buttons
            document.getElementById('zoomIn').onclick    = function () { timeline.zoomIn( 0.2); };
            document.getElementById('zoomOut').onclick   = function () { timeline.zoomOut( 0.2); };
            document.getElementById('moveLeft').onclick  = function () { move( 0.2); };
            document.getElementById('moveRight').onclick = function () { move(-0.2); };
            // document.getElementById('toggleRollingMode').onclick = function () { timeline.toggleRollingMode() };
          
            $("#loading-screen").hide();
          },
          error: function() {
            alert("error when show data");
          },
        });
      });
    </script>
  </body>
</html>
