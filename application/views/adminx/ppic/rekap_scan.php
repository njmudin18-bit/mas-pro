<?php
defined('BASEPATH') or exit('No direct script access allowed');
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

  <?php //$this->load->view('adminx/components/header_css_datatable_fix_column'); 
  ?>
  <?php $this->load->view('adminx/components/header_css_datatable'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/widget.css" />
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
                                <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter by date</label>
                                <div class="col-md-4 col-sm-12 m-t-30">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">

                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                    <span class="input-group-append">
                                      <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                    </span>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-30">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="refresh();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-20">
                                  <a data-type="clock" data-id="32782" class="tickcounter" style="display: block; width: 250px; height: 62px; margin: 0 auto" title="Time in Jakarta" href="//www.tickcounter.com/timezone/asia-jakarta">Time in Jakarta</a>
                                </div>
                              </div>
                              <hr>
                              <div id="loader_gue" class="text-center mb-3">
                                <div class="spinner-border text-primary" role="status">
                                  <span class="sr-only">Loading...</span>
                                </div>
                              </div>
                              <div class="row justify-content-center">
                                <div class="col-xl-6 col-md-6">
                                  <div class="card prod-p-card card-red">
                                    <div class="card-body">
                                      <div class="row align-items-center m-b-30">
                                        <div class="col text-center">
                                          <h6 class="m-b-20 text-white">Total Scan Produksi di <?php echo date('d M Y'); ?></h6>
                                          <h1 id="label_produksi" class="m-b-0 f-w-700 text-white">0</h1>
                                        </div>
                                        <div class="col-auto">
                                          <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                                        </div>
                                      </div>
                                      <p class="m-b-0 text-white text-center">
                                        Last scan on <span id="tgl_produksi" class="label label-danger m-r-10" style="font-size:12px"><?php echo date('Y-m-d H:i:s') ?></span>
                                      </p>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                  <div class="card prod-p-card card-green">
                                    <div class="card-body">
                                      <div class="row align-items-center m-b-30">
                                        <div class="col text-center">
                                          <h6 class="m-b-20 text-white">Total Scan QC di <?php echo date('d M Y'); ?></h6>
                                          <h1 id="label_qc" class="m-b-0 f-w-700 text-white">0</h1>
                                        </div>
                                        <div class="col-auto">
                                          <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                                        </div>
                                      </div>
                                      <p class="m-b-0 text-white text-center">
                                        Last scan on <span id="tgl_qc" class="label label-green m-r-10" style="font-size:12px"><?php echo date('Y-m-d H:i:s') ?></span>
                                      </p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <hr>
                              <h5 class="text-center">DAFTAR YANG BELUM DI SCAN</h5>
                              <hr>
                              <div class="row">
                                <div class="col-md-12">
                                  <table id="order-table" class="table table-striped table-bordered nowrap dataTable" width="100%" border="1" cellpadding="0" cellspacing="0">
                                    <thead>
                                      <tr class="bg-primary">
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Scan PR</th>
                                        <th class="text-center">Scan QC</th>
                                        <th class="text-center">Barcode No.</th>
                                        <th class="text-center">Job No.</th>
                                        <th class="text-center">Part ID.</th>
                                        <th class="text-center">Part Name</th>
                                        <th class="text-center">Unit ID.</th>
                                        <th class="text-center">Qty. Job</th>
                                        <th class="text-center">Qty. Box</th>
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
                </div>
              </div>
              <div id="styleSelector"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div id="loading-screen" class="loading">Loading&#8230;</div> -->

  <?php //$this->load->view('adminx/components/bottom_js_datatable_fix_column'); 
  ?>
  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
  <script>
    (function(d, s, id) {
      var js, pjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s);
      js.id = id;
      js.src = "//www.tickcounter.com/static/js/loader.js";
      pjs.parentNode.insertBefore(js, pjs);
    }(document, "script", "tickcounter-sdk"));
  </script>
  <script type="text/javascript">
    // $(function() {
    //   $('#tanggal').daterangepicker({
    //     singleDatePicker: true,
    //     showDropdowns: true,
    //     minYear: 2022,
    //     maxYear: parseInt(moment().format('YYYY'), 10),
    //     maxDate: new Date(),
    //     locale: {
    //       format: 'YYYY-MM-DD'
    //     }
    //   }, function(start, end, label) {

    //   });
    // });

    $(function() {

      var start = moment().subtract(0, 'days');
      var end = moment();

      function cb(start, end) {
        var sd = start.format('YYYY-MM-DD');
        var ed = end.format('YYYY-MM-DD');

        $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
      }

      $('#tanggal').daterangepicker({
        maxDate: new Date(),
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
    function refresh() {
      load_data();
      reload_table();
    }

    function load_data() {
      let date = $("#tanggal").val();
      let start_date = $("#start_date").val();
      let end_date = $("#end_date").val();

      $.ajax({
        url: "<?php echo base_url(); ?>rekapitulasi/get_jumlah_scan",
        type: "POST",
        dataType: "JSON",
        data: {
          "start_date": start_date,
          "end_date": end_date
        },
        beforeSend: function() {
          $("#loader_gue").show();
          //$("#loading-screen").show();
        },
        success: function(data) {
          //$("#loading-screen").hide();
          $("#loader_gue").hide();

          $("#label_produksi").html(data.data.jlh_scan_prod);
          $("#label_qc").html(data.data.jlh_scan_qc);

          if (data.tanggal != null) {
            $("#tgl_produksi").html(data.tanggal.last_scan_prod.slice(0, -4));
            $("#tgl_qc").html(data.tanggal.last_scan_qc.slice(0, -4));
          }
        },
        complete: function function_name(data) {
          //$("#loading-screen").hide();
          $("#loader_gue").hide();
          setTimeout(function() {
            load_data();
            reload_table();
          }, 10000);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          //$("#loading-screen").hide();
          $("#loader_gue").hide();
          window.location.reload();
          //load_data();
        }
      });
    }

    function reload_table() {
      table.ajax.reload(null, false);
    }

    $(document).ready(function() {
      load_data();

      new ClipboardJS('.btn');

      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        buttons: [
          'excel'
        ],
        paging: true,
        'processing': true,
        'serverSide': false,
        'serverMethod': 'post',
        'ajax': {
          url: "<?php echo base_url(); ?>rekapitulasi/get_data_belum_scan",
          type: 'POST',
          "data": function(data) {
            //data.tanggal = $('#tanggal').val();
            data.start_date = $('#start_date').val();
            data.end_date = $('#end_date').val();
          }
        },

        'aoColumns': [{
            "NO.": "NO.",
            "sClass": "text-right"
          },
          {
            "SCAN PROD": "SCAN PROD",
            "sClass": "text-center"
          },
          {
            "SCAN QC": "SCAN QC",
            "sClass": "text-center"
          },
          {
            "BARCODE NO.": "BARCODE NO.",
            "sClass": "text-left"
          },
          {
            "Job No.": "Job No.",
            "sClass": "text-left"
          },
          {
            "PART ID.": "PART ID.",
            "sClass": "text-left"
          },
          {
            "PART NAME": "PART NAME",
            "sClass": "text-left"
          },
          {
            "UNIT ID": "UNIT ID",
            "sClass": "text-center"
          },
          {
            "QTY. JOB": "QTY. JOB",
            "sClass": "text-right"
          },
          {
            "QTY. BOX": "QTY. BOX",
            "sClass": "text-right"
          },
        ],

        "columnDefs": [{
          "targets": [1], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });
    });
  </script>
</body>

</html>