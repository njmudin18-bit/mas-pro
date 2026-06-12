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
						                  <div class="dt-responsive table-responsive">
						                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%">
						                      <thead class="bg-primary text-center">
						                       	<tr>
							                        <th class="text-center" width="7%">No</th>
							                        <th class="text-center">Kode</th>
                                      <th class="text-center">Status Mesin</th>
							                        <th class="text-center">No. Mesin</th>
							                        <th class="text-center">Durasi On</th>
							                        <th class="text-center">Durasi Off</th>
							                        <th class="text-center">Mold</th>
							                        <th class="text-center">Qty</th>
                                      <th class="text-center">Total</th>
                                      <th class="text-center">Downtime</th>
                                      <th class="text-center">Detik 1</th>
                                      <th class="text-center">Detik 2</th>
                                      <th class="text-center">Detik 3</th>
                                      <th class="text-center">Detik 4</th>
                                      <th class="text-center">Detik 5</th>
                                      <th class="text-center">Detik 6</th>
                                      <th class="text-center">Menit 1</th>
                                      <th class="text-center">Menit 2</th>
                                      <th class="text-center">Menit 3</th>
                                      <th class="text-center">Menit 4</th>
                                      <th class="text-center">Menit 5</th>
                                      <th class="text-center">Create Date</th>
							                      </tr>
						                    	</thead>
							                    <tbody>
							                      
							                    </tbody>
						                  	</table>
						                	</div>
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
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
	  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	  <script>
      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

	    $(document).ready(function() {

	      table = $('#order-table').DataTable({ 
	      		"pagingType": "full_numbers",
	      		"lengthMenu": [
		          [10, 25, 50, -1],
		          [10, 25, 50, "All"]
		        ],
		        responsive: true,
		        language: {
		          search: "_INPUT_",
		          searchPlaceholder: "Search records",
		        },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
              "url": "<?php echo base_url(); ?>mesin/show_data_mesin_list",
              "type": "POST",
              "data": function(data) {
                data.kode_mesin = "<?php echo $kode_mesin; ?>";
              }
            },

            "aoColumns": [
              { "No": "No" , "sClass": "text-center"},
              { "Kode": "Kode" , "sClass": "text-center" },
              { "Status Mesin": "Status Mesin" , "sClass": "text-center" },
              { "No. Mesin": "No. Mesin" , "sClass": "text-center" },
              { "Durasi On": "Durasi On" , "sClass": "text-right" },
              { "Durasi Off": "Durasi Off" , "sClass": "text-right" },
              { "Mold": "Mold" , "sClass": "text-right" },
              { "Qty": "Qty" , "sClass": "text-right" },
              { "Total": "Total" , "sClass": "text-right" },
              { "Downtime": "Downtime" , "sClass": "text-left" },
              { "Detik 1": "Detik 1" , "sClass": "text-right" },
              { "Detik 2": "Detik 2" , "sClass": "text-right" },
              { "Detik 3": "Detik 3" , "sClass": "text-right" },
              { "Detik 4": "Detik 4" , "sClass": "text-right" },
              { "Detik 5": "Detik 5" , "sClass": "text-right" },
              { "Detik 6": "Detik 6" , "sClass": "text-right" },
              { "Menit 1": "Menit 1" , "sClass": "text-right" },
              { "Menit 2": "Menit 2" , "sClass": "text-right" },
              { "Menit 3": "Menit 3" , "sClass": "text-right" },
              { "Menit 4": "Menit 4" , "sClass": "text-right" },
              { "Menit 5": "Menit 5" , "sClass": "text-right" },
              { "Create Date": "Create Date" , "sClass": "text-center" }
            ],

            //Set column definition initialisation properties.
            "columnDefs": [
              { 
                "targets": [ 0 ], //last column
                "orderable": false, //set not orderable
                className: 'text-right'
              },
            ]
        });
	    });
	  </script>
	</body>
</html>