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
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary" width="5%">No</th>
                                      <th class="text-center bg-primary" wdith="5%">#</th>
                                      <th class="text-center bg-primary" width="30%">Type</th>
                                      <th class="text-center bg-primary" width="30%">Name</th>
                                      <th class="text-center bg-primary" width="30%">Company Code</th>
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

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript">
      function capitalize(word) {
        const lower = word.toLowerCase();

        return word.charAt(0).toUpperCase() + lower.slice(1);
      }
      
      //FUNCTION TAMBAHKAN DATA PARTNER
      function tambahkan_data(Type) {
        $.ajax({
          url: "<?php echo base_url(); ?>purchasing/send_type_supplier_data",
          method: "POST",
          data: {
            type: Type
          },
          dataType: 'JSON',
          beforeSend: function () {
            $("#loading-screen").show();
          },
          success: function(data) {
            $("#loading-screen").hide();
            Swal.fire({
              icon: data.status,
              title: capitalize(data.status),
              text: data.message
            });
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error when saving data');
          }
        });
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
        $("#loading-screen").hide();

        table = $('#order-table').DataTable( {
            dom: 'Bfrltip',
            buttons: [
              //'excel'
              //'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            scrollY       : "500px",
            scrollX       : true,
            scrollCollapse: true,
            paging        : true,
            fixedColumns: {
              leftColumns: 2
            },
            'processing': true,
            'serverSide': false,
            'serverMethod': 'POST',
            'ajax': {
              url : "<?php echo base_url(); ?>purchasing/get_daftar_type_supplier",
              type : 'POST',
              "data": function(data) {
                data.bulan        = $('#bulan').val();
                data.tahun        = $('#tahun').val();
              }
            },

            'aoColumns': [
              { "No": "No" , "sClass": "text-right"},
              { "#": "#" , "sClass": "text-center"},
              { "Type": "Type" , "sClass": "text-center"},
              { "Name": "Name" , "sClass": "text-left" },
              { "Company Code": "Company Code" , "sClass": "text-center" }
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