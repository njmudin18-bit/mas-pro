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

  <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/timeline.css" />
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />
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
                                <div class="col-md-4 col-sm-12 m-t-30">
                                  <div id="reportrange"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span>
                                  </div>
                                  <!-- <input class="form-control" type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">

                                </div>


                                <div class="col-md-3 col-sm-12 m-t-30">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile"
                                    onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <!-- <form id="frm-example" action="#" method="POST">
                                <p><button id="btn_kirim" class="btn btn-danger" disabled>KIRIM</button></p>
                                <hr> -->
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%"
                                border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="">
                                    <th class="text-center align-middle bg-primary">No</th>
                                    <th class="text-center align-middle bg-primary">No. SO</th>
                                    <th class="text-center align-middle bg-primary">Nama Customer</th>
                                    <th class="text-center align-middle bg-primary">No. PO Customer</th>
                                    <th class="text-center align-middle bg-primary">No. DO</th>
                                    <!-- <th class="text-center align-middle bg-primary">No. DP</th> -->
                                    <th class="text-center align-middle bg-primary">Tanggal DO</th>
                                    <th class="text-center align-middle bg-primary">PartID</th>
                                    <th class="text-center align-middle bg-primary">Qty Kirim</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                              <!-- </form> -->
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
  <!-- modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Status Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="content">
            <!-- timeline disini -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal -->

  <div id="loading-screen" class="loading">Loading&#8230;</div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script type="text/javascript"
    src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

  <script type="text/javascript">
  //RANGE DATE PICKER
  $(function() {
    var start = moment();
    var end = moment();

    function cb(start, end) {
      $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
      var start_date = start.format('YYYY-MM-DD');
      var end_date = end.format('YYYY-MM-DD');
      $("#start_date").val(start_date);
      $("#end_date").val(end_date);
    }

    $('#reportrange').daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
          'month')]
      }
    }, cb);

    cb(start, end);

  });

  //FUNCTION CARI
  function cari() {
    reload_table();
  }

  //FUNCTION RELOAD TABLE
  function reload_table() {
    table.ajax.reload(null, false);
  }

  function detail_po(part_id) {
    console.log(part_id);
  }


  //show isi table
  $(document).ready(function() {
    localStorage.setItem("periode_awal_sales", $('#start_date').val());
    localStorage.setItem("periode_now_sales", $('#end_date').val());

    var counterChecked = 0;

    $('body').on('change', 'input[type="checkbox"]', function() {
      this.checked ? counterChecked++ : counterChecked--;
      counterChecked > 0 ? $('#btn_kirim').prop("disabled", false) : $('#btn_kirim').prop("disabled", true);
    });


    $("#loading-screen").hide();

    table = $('#order-table').DataTable({

      dom: 'Bfrltip',
      buttons: [
        'excel'
        //'copy', 'csv', 'excel', 'pdf', 'print'
      ],
      scrollY: "500px",
      scrollX: true,
      scrollCollapse: true,
      paging: true,
      // fixedColumns: {
      //   leftColumns: 2
      // },
      'processing': true,
      'serverSide': false,
      'serverMethod': 'POST',
      'ajax': {
        url: "<?php echo base_url(); ?>sales_order_do/sales_order_by_do",
        type: 'POST',
        "data": function(data) {
          data.start_date = $('#start_date').val();
          data.end_date = $('#end_date').val();

        }
      },

      'aoColumns': [{
          "No": "No",
          "sClass": "text-center"
        },
        {
          "No. SO": "No. SO",
          "sClass": "text-left"
        },
        {
          "No. DO": "No. DO",
          "sClass": "text-left"
        },
        {
          "No. SO": "No. SO",
          "sClass": "text-left"
        },
        {
          "No. DO": "No. DO",
          "sClass": "text-left"
        },
        // {
        //   "No. DP": "No. DP",
        //   "sClass": "text-left"
        // },
        {
          "Tanggal DO": "Tanggal DO",
          "sClass": "text-center"
        },
        {
          "Tanggal DO": "Tanggal DO",
          "sClass": "text-left"
        },
        {
          "Qty Kirim": "Qty Kirim",
          "sClass": "text-right"
        }
      ],

      "columnDefs": [{
        "targets": [-1, 0, 1],
        "orderable": false,
        className: 'text-right'
      }, ]

    });

  });
  </script>
</body>

</html>