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
                              <h5>JOB NO: #<?php echo $no_job; ?></h5>
                            </div>
                            <div class="card-block "><!-- m-t-30 m-b-30 -->
                              <div class="container">
                                <div class="row">
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Part ID</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6>: <?php echo $part_id; ?></h6>
                                  </div>
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Part Name</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6>: <?php echo $part_name; ?></h6>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-2 col-sm-12">
                                    <h6>Job No</h6>
                                  </div>
                                  <div class="col-md-4 col-sm-12">
                                    <h6>: <?php echo $no_job; ?></h6>
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
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">No.</th>
                                      <th class="text-center bg-primary">No. Barcode</th>
                                      <th class="text-center bg-primary">Qty. Box</th>
                                      <th class="text-center bg-primary">Scan Date</th>
                                      <th class="text-center bg-primary">Scan By</th>
                                      <!-- <th class="text-center bg-primary">No. Job</th>
                                      <th class="text-center bg-primary">Qty. Job</th>-->
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr class="bg-info">
                                      <th colspan="2"></th>
                                      <th></th>
                                      <th colspan="2"></th>
                                    </tr>
                                  </tfoot>
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
          "footerCallback": function(row, data, start, end, display) {
            var api = this.api(),
              data;

            // converting to interger to find total
            var intVal = function(i) {
              return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
            };

            // computing column Total of the complete result 
            var total_qty = api
              .column(2)
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);

            // Update footer by showing the total with the reference of the column index 
            $(api.column(1).footer()).html('TOTAL');
            $(api.column(2).footer()).html(formatNumber(total_qty));
          },
          scrollY       : "400px",
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
            url : "<?php echo base_url(); ?>lhp/list_barcode_wh_data",
            type : 'POST',
            "data": function(data) {
              data.no_job     = '<?php echo $no_job; ?>';
              data.start_date = '<?php echo $start_date; ?>';
              data.end_date   = '<?php echo $end_date; ?>';
            },
            dataFilter: function(response){
              var temp = JSON.parse(response);
              $("#qtyOrder").html(': ' + temp.qty_order);

              return response;
            }
          },

          'aoColumns': [
            { "No.": "No." , "sClass": "text-right"},
            { "No. Barcode": "No. Barcode" , "sClass": "text-left" },
            { "Qty. Box": "Qty. Box" , "sClass": "text-right" },
            { "Scan Date": "Scan Date" , "sClass": "text-left" },
            { "Scan By": "Scan By" , "sClass": "text-left" }
            // { "No. Job": "No. Job" , "sClass": "text-left"},
            // { "Qty. Job": "Qty. Job" , "sClass": "text-right" },
          ],

          "columnDefs": [
            { 
              //"targets": [ 0 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        } );

        function formatNumber(n) {
          return n.toLocaleString();
        }
      });
    </script>
  </body>
</html>