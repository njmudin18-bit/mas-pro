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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                              </h5>
                            </div>
                            <div class="card-block m-t-30 m-b-30">
                              <div class="dt-responsive table-responsiveX">
                                <div class="form-group row">
                                  <label class="col-md-3 col-sm-12 col-form-label m-t-30">Filter Production date</label>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>

                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">NO</th>
                                      <th class="text-center bg-primary">JOB NO</th>
                                      <th class="text-center bg-primary">PROD BARCODE NO</th>
                                      <th class="text-center bg-primary">PROD SCAN LOC</th>
                                      <th class="text-center bg-primary">PROD SCAN STATUS</th>
                                      <th class="text-center bg-primary">PROD SCAN DATE</th>
                                      <th class="text-center bg-primary">PROD SCAN BY</th>
                                      <th class="text-center bg-primary">QC BARCODE NO</th>
                                      <th class="text-center bg-primary">QC SCAN LOC</th>
                                      <th class="text-center bg-primary">QC SCAN DATE</th>
                                      <th class="text-center bg-primary">QC SCAN BY</th>
                                      <th class="text-center bg-primary">QC STATUS</th>
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

    <!-- MODAL VIEW NG DETAIL -->
    <div class="modal fade" id="modal_ng" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Detail Produk NG</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body">
            <div class="dt-responsive table-responsive">
              <table class="table table-bordered nowrap">
                <thead>
                  <tr class="bg-info">
                    <th class="text-center">No</th>
                    <th class="text-center">Barcode No.</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">PIC Repair</th>
                    <th class="text-center">Tanggal</th>
                  </tr>
                </thead>
                <tbody id="data_ng">
                  
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
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
      $(function() {

        var start = moment().subtract(5, 'days');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#start_date').val(start.format('YYYY-MM-DD'));
          $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('#tanggal').daterangepicker({
          startDate: start,
          endDate: end,
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          locale: {
            format: 'YYYY-MM-DD'
          },
          function(start, end, label) {
            startDate = start;
            endDate = end;
            console.log(startDate);
            console.log(endDate);
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
          }
        }, cb);
        cb(start, end);
      });
    </script>
    <script type="text/javascript">

      //FUNCTION CEK DETAIL NG
      function cek_details(barcode_no, scan_qc_id) {
        $.ajax({
          url:  "<?php echo base_url(); ?>ppic/view_product_ng",
          data: {scan_qc_id: scan_qc_id, barcode_no: barcode_no},
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success:function(hasil) {
            if (hasil.status_code == 200) {

              $("#data_ng").html(hasil.html);
              $("#loading-screen").hide();
              $('#modal_ng').modal('show');
            } else {
              Swal.fire(
                'Oops',
                hasil.message,
                'warning'
              );
              $("#loading-screen").hide();
            }
          }
        })
      }

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
              //'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            scrollY       : "500px",
            scrollX       : true,
            scrollCollapse: true,
            paging        : true,
            fixedColumns: {
              leftColumns: 2,
              rightColumns: 1
            },
            'processing': true,
            'serverSide': false,
            'serverMethod': 'POST',
            'ajax': {
              url : "<?php echo base_url(); ?>ppic/monitoring_job_list",
              type : 'POST',
              "data": function(data) {
                data.start_date   = $('#start_date').val();
                data.end_date     = $('#end_date').val();
              }
            },

            'aoColumns': [
              { "NO": "NO" , "sClass": "text-right"},
              { "JOB NO": "JOB NO" , "sClass": "text-center" },
              { "PROD BARCODE NO": "PROD BARCODE NO" , "sClass": "text-center" },
              { "PROD SCAN LOC": "PROD SCAN LOC" , "sClass": "text-center" },
              { "PROD SCAN STATUS": "PROD SCAN STATUS" , "sClass": "text-center" },
              { "PROD SCAN DATE": "PROD SCAN DATE" , "sClass": "text-center" },
              { "PROD SCAN BY": "PROD SCAN BY" , "sClass": "text-center" },
              { "QC BARCODE NO": "QC BARCODE NO" , "sClass": "text-center" },
              { "QC SCAN LOC ": "QC SCAN LOC" , "sClass": "text-center" },
              { "QC SCAN DATE": "QC SCAN DATE" , "sClass": "text-center" },
              { "QC SCAN BY": "QC SCAN BY" , "sClass": "text-center" },
              { "QC STATUS": "QC STATUS" , "sClass": "text-center" },
            ],

            "columnDefs": [
              { 
                //"targets": [ 0 ], //last column
                "orderable": false, //set not orderable
                className: 'text-right'
              },
            ]
        } );
      });
    </script>
  </body>
</html>