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
						                  <div class="dt-responsive table-responsive">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter data by</label>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <!-- <select class="form-control" name="tanggal" id="tanggal" required="required">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Pilih --</option>
                                      <?php
                                        $now = date("d");
                                        for($a = 1; $a <= 31; $a += 1){
                                          if ($a >= 1 && $a <= 9) {
                                            if ($now == $a) {
                                              ?>
                                                <option value="0<?php echo $a; ?>" selected="selected">0<?php echo $a; ?></option>
                                              <?php
                                            } else {
                                              ?>
                                                <option value="0<?php echo $a; ?>">0<?php echo $a; ?></option>
                                              <?php
                                            }
                                          } else {
                                            ?>
                                              <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
                                            <?php
                                          }
                                        }
                                      ?>
                                    </select> -->
                                    <select name="tanggal" id="tanggal" class="form-control">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Pilih --</option>
                                      <?php
                                        $now = date('d');
                                        for($day = 1; $day <= 31; $day++){
                                          if ($day <= 9) {
                                            if ($day == $now) {
                                              echo "<option selected value = '0".$day."'>0".$day."</option>";
                                            } else {
                                              echo "<option value = '0".$day."'>0".$day."</option>";
                                            }
                                          } else {
                                            if ($day == $now) {
                                              echo "<option selected value = '".$day."'>".$day."</option>";
                                            } else {
                                              echo "<option value = '".$day."'>".$day."</option>";
                                            }
                                          }
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="bulan" id="bulan" required="required">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Bulan --</option>
                                      <?php 
                                        $now  = new DateTime('now');
                                        $bln1 = $now->format('m');
                                        for ($m = 1; $m <= 12; ++$m){
                                          if ($bln1 == $m){
                                            echo '<option selected value='.$m.'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
                                          }else{
                                            echo '<option  value='.$m.'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
                                          }
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="tahun" id="tahun" required="required">
                                      <option disabled="disabled">-- Tahun --</option>
                                      <?php 
                                        $now    = new DateTime('now');
                                        $year1  = $now->format('Y');
                                        for ($year = 2022; $year <= 2050; ++$year){
                                          if ($year1 == $year){
                                            echo '<option selected value='.$year.'>'.$year.'</option>'."\n";
                                          }else{
                                            echo '<option  value='.$year.'>'.$year.'</option>'."\n";
                                          }
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
						                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
						                      <thead class="bg-primary">
						                       	<tr>
							                        <th class="text-center">NO.</th>
                                      <th class="text-center">NO. DO</th>
                                      <th class="text-center">PO CUSTOMER</th>
                                      <!-- <th class="text-center">BARCODE ID</th> -->
                                      <th class="text-center">PART ID</th>
                                      <th class="text-center">PART NAME</th>
                                      <!-- <th class="text-center">JLH. BOX</th> -->
                                      <th class="text-center">QTY. ORDER</th>
                                      <th class="text-center">CUSTOMER</th>
                                      <!-- <th class="text-center">QTY. PALLET</th>
                                      <th class="text-center">LOKASI SCAN</th>
                                      <th class="text-center">TGL. SCAN</th>
                                      <th class="text-center">SCAN BY</th> -->
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
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>

	  <script>
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

	    $(document).ready(function() {
        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
            //'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url : "<?php echo base_url(); ?>warehouse/summary_barang_delivery_list",
            type : 'POST',
            "data": function(data) {
              data.bulan    = $('#bulan').val();
              data.tahun    = $('#tahun').val();
              data.tanggal  = $('#tanggal').val();
            }
          },

          'aoColumns': [
            { "NO": "NO" , "sClass": "text-right"},
            { "NO. DO": "NO. DO" , "sClass": "text-left" },
            { "PO CUSTOMER": "PO CUSTOMER" , "sClass": "text-left" },
            //{ "BARCODE ID": "BARCODE ID" , "sClass": "text-center" },
            { "PART ID": "PART ID" , "sClass": "text-left" },
            { "PART NAME": "PART NAME" , "sClass": "text-left" },
            //{ "JLH. BOX": "JLH. BOX" , "sClass": "text-center" },
            { "QTY. ORDER": "QTY. ORDER" , "sClass": "text-right" },
            { "CUSTOMER": "CUSTOMER" , "sClass": "text-left" }
            //{ "QTY. PALLET": "QTY. PALLET" , "sClass": "text-right" },
            //{ "LOKASI SCAN": "LOKASI SCAN" , "sClass": "text-center" },
            //{ "TGL. SCAN": "TGL. SCAN" , "sClass": "text-center" },
            //{ "SCAN BY": "SCAN BY" , "sClass": "text-left" }
          ],

          "columnDefs": [
            { 
              "targets": [ 1 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        });
	    });
	  </script>
	</body>
</html>