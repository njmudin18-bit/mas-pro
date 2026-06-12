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
                            <h5 id="test">
                              <?php //echo strtoupper($nama_halaman); 
                              ?>
                              DETAIL PARTID. <?php echo $partID; ?>
                            </h5>
                          </div>
                          <div class="card-block m-t-30 m-b-30">
                            <div class="dt-responsive table-responsiveX">
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="">
                                    <th class="text-center align-middle bg-primary">No</th>
                                    <th class="text-center align-middle bg-primary">No. SO</th>
                                    <th class="text-center align-middle bg-primary">No. DO</th>
                                    <th class="text-center align-middle bg-primary">No. DP</th>
                                    <th class="text-center align-middle bg-primary">Tanggal DO</th>
                                    <th class="text-center align-middle bg-primary">PartID</th>
                                    <th class="text-center align-middle bg-primary">Qty Kirim</th>
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
    $(document).ready(function() {
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
        //   // rightColumns: 1
        // },
        'processing': true,
        'serverSide': false,
        'serverMethod': 'POST',
        'ajax': {
          url: "<?php echo base_url(); ?>sales_order_customer/sales_order_by_do_list",
          type: 'POST',
          "data": function(data) {
            // data.start_date  = $('#start_date').val();
            // data.end_date = $('#end_date').val();
            data.periode_awal = localStorage.getItem("periode_awal_sales");
            data.periode_now = localStorage.getItem("periode_now_sales");
            data.no_so = localStorage.getItem("no_so_sales");
            data.partID = '<?php echo $partID; ?>';
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
            "No. DP": "No. DP",
            "sClass": "text-left"
          },
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
          "targets": [-1, 0, 1], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });
    });
  </script>
</body>

</html>