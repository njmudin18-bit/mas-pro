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

    <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
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
                              <h5>JOB NO: #<?php echo $job_no; ?></h5>
                            </div>
                            <div class="card-block">
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Part ID</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6 id="partID">:</h6>
                                  </div>
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Part Name</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6 id="partName">:</h6>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Job No</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6>: <?php echo $job_no; ?></h6>
                                  </div>
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Qty. Order</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6 id="qtyOrder"></h6>
                                  </div>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsiveX">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">No.</th>
                                      <th class="text-center bg-primary">Barcode No.</th>
                                      <th class="text-center bg-primary">Tanggal Cetak</th>
                                      <th class="text-center bg-primary">Prod. Loc.</th>
                                      <th class="text-center bg-primary">Prod. Scan By</th>
                                      <th class="text-center bg-primary">Prod. Scan Date</th>
                                      <th class="text-center bg-primary">QC. Loc.</th>
                                      <th class="text-center bg-primary">QC. Scan By</th>
                                      <th class="text-center bg-primary">QC. Scan Date</th>
                                      <th class="text-center bg-primary">WH. Scan By</th>
                                      <th class="text-center bg-primary">WH. Scan Date</th>
                                      <!-- <th class="text-center bg-primary">Part ID.</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Qty. Order</th> -->
                                      <th class="text-center bg-primary">Qty. Pallet</th>
                                      <th class="text-center bg-primary">Unit ID</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript">
      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

      $(document).ready(function() {
        table = $('#order-table').DataTable( {
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
          scrollY       : "500px",
          scrollX       : true,
          scrollCollapse: true,
          paging        : true,
          fixedColumns: {
            leftColumns: 0,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url : "<?php echo base_url(); ?>barcode/barcode_trace_list",
            type : 'POST',
            "data": function(data) {
              data.job_no       = "<?php echo $job_no; ?>";
              data.qty_order    = "<?php echo $qty_order; ?>";
            },
            dataFilter: function(response){
              var temp = JSON.parse(response);
              $("#partID").html(': ' + temp.data[0][14]);
              $("#partName").html(': ' + temp.data[0][15]);
              $("#qtyOrder").html(': ' + temp.data[0][13]);

              return response;
            }
          },

          oLanguage: {sProcessing: "<div id='loading-screen' class='loading'>Loading&#8230;</div>"},

          'aoColumns': [
            { "No.": "No." , "sClass": "text-right"},
            { "Barcode No.": "Barcode No." , "sClass": "text-left" },
            { "Tanggal Cetak": "Tanggal Cetak" , "sClass": "text-center" },
            { "Prod. Loc.": "Prod. Loc." , "sClass": "text-left" },
            { "Prod. Scan By": "Prod. Scan By" , "sClass": "text-left" },
            { "Prod. Scan Date": "Prod. Scan Date" , "sClass": "text-left" },
            { "QC. Loc.": "QC. Loc." , "sClass": "text-left" },
            { "QC. Scan By": "QC. Scan By" , "sClass": "text-left" },
            { "QC. Scan Date": "QC. Scan Date" , "sClass": "text-left" },
            { "WH. Scan By": "QC. Scan By" , "sClass": "text-left" },
            { "WH. Scan Date": "WH. Scan Date" , "sClass": "text-left" },
            // { "Part ID.": "Part ID." , "sClass": "text-left" },
            // { "Part Name": "Part Name" , "sClass": "text-left" },
            // { "Qty. Order": "Qty. Order" , "sClass": "text-right" },
            { "Qty. Pallet": "Qty. Pallet" , "sClass": "text-right" },
            { "Unit ID": "Unit ID" , "sClass": "text-left" }
          ],

          "columnDefs": [
            { 
              //"targets": [ 0 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        } );

        table.on('click', 'tbody tr', (e) => {
            let classList = e.currentTarget.classList;
        
            if (classList.contains('selected')) {
                classList.remove('selected');
            }
            else {
                table.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
                classList.add('selected');
            }
        });
        
        document.querySelector('#button').addEventListener('click', function () {
            table.row('.selected').remove().draw(false);
        });
      });
    </script>
  </body>
</html>