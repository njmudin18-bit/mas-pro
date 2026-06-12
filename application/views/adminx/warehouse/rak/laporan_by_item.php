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
                                  <label class="col-md-2 col-sm-12 col-form-label">Filter by</label>
                                  <!-- <div class="col-md-4 col-sm-12">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                  </div> -->
                                  <div class="col-md-8 col-sm-12">
                                    <select name="WHItems" id="WHItems" class="form-control js-items">
                                      <option value="All" selected>All Items</option>
                                      <?php foreach ($item as $key => $value): ?>
                                        <option value="<?php echo $value->PartID; ?>"><?php echo $value->PartName." - (".$value->PartID.")"; ?></option>
                                      <?php endforeach ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 mt-2">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th class="text-center bg-primary">No</th>
                                      <th class="text-center bg-primary">WH Lokasi</th>
                                      <th class="text-center bg-primary">Rak</th>
                                      <th class="text-center bg-primary">Baris</th>
                                      <th class="text-center bg-primary">Part ID</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Quantity</th>
                                      <th class="text-center bg-primary">Unit</th>
                                      <th class="text-center bg-primary">Noted</th>
                                      <th class="text-center bg-primary">Create Date</th>
                                      <th class="text-center bg-primary">Create By</th>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month'); //moment().subtract(7, 'days');
        var end   = moment();

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
      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $('.js-items').select2();

        var groupColumn = 5;
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
            url: "<?php echo base_url(); ?>laporan_rak/daftar_laporan_by_item",
            type: 'POST',
            "data": function(data) {
              //data.start_date = $('#start_date').val();
              //data.end_date   = $('#end_date').val();
              data.items      = $('#WHItems').val();
            }
          },
          'aoColumns': [
            {
              "No": "No",
              "sClass": "text-right"
            },
            {
              "WH Lokasi": "WH Lokasi",
              "sClass": "text-center"
            },
            {
              "Rak": "Rak",
              "sClass": "text-center"
            },
            {
              "Baris": "Baris",
              "sClass": "text-center"
            },
            {
              "Part ID": "Part ID",
              "sClass": "text-left"
            },
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
              "Noted": "Noted",
              "sClass": "text-left"
            },
            {
              "Create Date": "Create Date",
              "sClass": "text-left"
            },
            {
              "Create By": "Create By",
              "sClass": "text-center"
            }
          ],
          "columnDefs": [
            {
              "targets": [-1, 0, 1], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            }
          ],
          drawCallback: function (settings) {
            var api   = this.api();
            var rows  = api.rows({ page: 'current' }).nodes();
            var last  = null;
    
            api.column(groupColumn, { page: 'current' })
            .data()
            .each(function (group, i) {
              if (last !== group) {
                $(rows)
                .eq(i)
                .before(
                  '<tr class="group bg-info"><th colspan="12">' + group + '</th></tr>'
                );

                last = group;
              }
            });
          }
        });
      });
    </script>
  </body>
</html>