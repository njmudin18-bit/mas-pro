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

    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" />
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
                                <div class="row">
                                  <label class="col-md-2 col-3 col-form-label">Part ID</label>
                                  <label class="col-md-3 col-9 col-form-label"><?php echo $PartID ?></label>
                                  <label class="col-md-2 col-3 col-form-label">Part Name</label>
                                  <label class="col-md-5 col-9 col-form-label"><?php echo $PartName ?></label>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th class="text-center bg-primary">No</th>
                                      <th class="text-center bg-primary">Tanggal</th>
                                      <th class="text-center bg-primary">No. Job</th>
                                      <!-- <th class="text-center bg-primary">No. MPR</th> -->
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Quantity</th>
                                      <th class="text-center bg-primary">Unit</th>
                                      <th class="text-center bg-primary">FIFO</th>
                                      <th class="text-center bg-primary">WH Lokasi</th>
                                      <th class="text-center bg-primary">Noted</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                </table>
                              </div>
                            </div>
                            <div class="card-footer">
                              <p class="text-left">NO. DOKUMEN: MAS/FO/WH/15</p>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
    <script type="text/javascript">
      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        //var groupColumn = 1;
        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
          paging: true,
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url: "<?php echo base_url(); ?>laporan_rak/daftar_laporan_item_detail",
            type: 'POST',
            "data": function(data) {
              data.PartID = "<?php echo $PartID; ?>";
            }
          },
          'aoColumns': [
            {
              "No": "No",
              "sClass": "text-right"
            },
            {
              "Tanggal": "Tanggal",
              "sClass": "text-left"
            },
            {
              "No. Job": "No. Job",
              "sClass": "text-left"
            },
            // {
            //   "No. MPR": "No. MPR",
            //   "sClass": "text-left"
            // },
            {
              "Part name": "Part name",
              "sClass": "text-left"
            },
            {
              "Quantity": "Quantity",
              "sClass": "text-right"
            },
            {
              "Unit": "Unit",
              "sClass": "text-center"
            },
            {
              "FIFO": "FIFO",
              "sClass": "text-center"
            },
            {
              "WH Lokasi": "WH Lokasi",
              "sClass": "text-center"
            },
            {
              "Noted": "Noted",
              "sClass": "text-left"
            }
          ],
          "columnDefs": [
            {
              "targets": [-1, 0, 1], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            }
          ],
          // drawCallback: function (settings) {
          //   var api   = this.api();
          //   var rows  = api.rows({ page: 'current' }).nodes();
          //   var last  = null;
    
          //   api.column(groupColumn, { page: 'current' })
          //   .data()
          //   .each(function (group, i) {
          //     if (last !== group) {
          //       $(rows)
          //       .eq(i)
          //       .before(
          //         '<tr class="group bg-info"><th colspan="13">' + group + '</th></tr>'
          //       );

          //       last = group;
          //     }
          //   });
          // }
        });
      });
    </script>
  </body>
</html>