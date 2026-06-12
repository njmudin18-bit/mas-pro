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
                              <div class="dt-responsive table-responsiveXX">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter data by</label>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="bulan" id="bulan" required="required">
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
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="jenis_part" id="jenis_part" required="required">
                                      <option disabled="disabled">-- Pilih --</option>
                                      <option value="All" selected="selected">All</option>
                                      <option value="Power Cord">Power Cord</option>
                                      <option value="Wiring">Wiring</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">No</th>
                                      <th class="text-center bg-primary">No. Job</th>
                                      <th class="text-center bg-primary">Date Created</th>
                                      <th class="text-center bg-primary">Part ID</th>
                                      <th class="text-center bg-primary">Partname</th>
                                      <th class="text-center bg-primary">Unit ID</th>
                                      <th class="text-center bg-primary">Qty. Order</th>
                                      <th class="text-center bg-primary">Qty. Uncompleted</th>
                                      <th class="text-center bg-primary">Location Result</th>
                                      <th class="text-center bg-primary">Notes</th>
                                      <th class="text-center bg-primary">Status</th>
                                      <th class="text-center bg-primary">Lihat Detail</th>
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
    <!-- <script type="text/javascript">
      $(function() {

        var start = moment().subtract(7, 'days');
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
    </script> -->
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
              url : "<?php echo base_url(); ?>ppic/monitoring_job_list_new",
              type : 'POST',
              "data": function(data) {
                data.bulan        = $('#bulan').val();
                data.tahun        = $('#tahun').val();
                data.jenis_part   = $('#jenis_part').val();
              }
            },

            'aoColumns': [
              { "No": "No" , "sClass": "text-right"},
              { "No. Job": "No. Job" , "sClass": "text-center" },
              { "Date Created": "Date Created" , "sClass": "text-right"},
              { "Part ID": "Part ID" , "sClass": "text-left" },
              { "Part name": "Part name" , "sClass": "text-left" },
              { "UnitID": "UnitID" , "sClass": "text-center" },
              { "Qty. Order": "Qty. Order" , "sClass": "text-right" },
              { "Qty. Uncompleted": "Qty. Uncompleted" , "sClass": "text-right" },
              { "Location Result": "Location Result" , "sClass": "text-center" },
              { "Notes": "Notes" , "sClass": "text-center" },
              { "Status": "Status" , "sClass": "text-center" },
              { "Lihat Detail": "Lihat Detail" , "sClass": "text-center" }
            ],

            "columnDefs": [
              { 
                "targets": [ -1, 0, 1 ], //last column
                "orderable": false, //set not orderable
                className: 'text-right'
              },
            ]
        } );
      });
    </script>
  </body>
</html>