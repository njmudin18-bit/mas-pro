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
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%"
                                border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="">
                                    <th class="text-center align-middle bg-primary">No</th>
                                    <th class="text-center align-middle bg-primary">PartID</th>
                                    <th class="text-center align-middle bg-primary">Partname</th>
                                    <th class="text-center align-middle bg-primary">Customer Name</th>
                                    <th class="text-center align-middle bg-primary">No. PO Customer</th>
                                    <th class="text-center align-middle bg-primary">No. SO</th>
                                    <th class="text-center align-middle bg-primary">Tanggal SO</th>
                                    <th class="text-center align-middle bg-primary">Qty Order</th>
                                    <th class="text-center align-middle bg-primary">Qty Terkirim</th>
                                    <th class="text-center align-middle bg-primary">Qty Sisa</th>
                                    <th class="text-center align-middle bg-primary">Unit ID</th>
                                    <th class="text-center align-middle bg-primary">Unit ID</th>
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
  function simpanNobukti(noBukti) {
    // console.log(noBukti);

    localStorage.setItem("no_so_sales", noBukti);
  }
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
        url: "<?php echo base_url(); ?>sales_order/sales_order_by_po_list",
        type: 'POST',
        "data": function(data) {
          // data.start_date  = $('#start_date').val();
          // data.end_date = $('#end_date').val();
          data.periode_now = localStorage.getItem("periode_now_sales");
          data.partID = '<?php echo $partID; ?>';
        }
      },

      'aoColumns': [{
          "No": "No",
          "sClass": "text-right"
        },
        {
          "PartID": "PartID",
          "sClass": "text-left"
        },
        {
          "Partname": "Partname",
          "sClass": "text-left"
        },
        {
          "Customer Name": "Customer Name",
          "sClass": "text-right"
        },
        {
          "No. PO Customer": "No. PO Customer",
          "sClass": "text-right"
        },
        {
          "No. SO": "No. SO",
          "sClass": "text-right"
        },
        {
          "Tanggal SO": "Tanggal SO",
          "sClass": "text-right"
        },
        {
          "Qty Order": "Qty Order",
          "sClass": "text-left"
        },
        {
          "Qty Terkirim": "Qty Terkirim",
          "sClass": "text-left"
        },
        {
          "Qty Sisa": "Qty Sisa",
          "sClass": "text-center"
        },
        {
          "Unit ID": "Unit ID",
          "sClass": "text-center"
        },
        {
          "Unit ID": "Unit ID",
          "sClass": "text-center"
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