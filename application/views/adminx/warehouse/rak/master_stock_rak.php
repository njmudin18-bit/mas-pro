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
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block">
                              <div class="dt-responsive table-responsive">
                                <div class="row">
                                  <label class="col-md-2 col-sm-12 col-form-label">Filter by Rak</label>
                                  <div class="col-md-3 col-sm-12">
                                    <select name="Rak" id="Rak" class="form-control">
                                      <option value="All" selected>-- All Rak --</option>
                                      <?php foreach ($rak as $key => $value): ?>
                                        <option value="<?php echo $value->QRCode; ?>">Rak <?php echo $value->Rak." - ".$value->WHLokasi; ?></option>
                                      <?php endforeach ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12">
                                    <select name="Baris" id="Baris" class="form-control">
                                      <option value="All" selected>-- All Baris --</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th class="text-center bg-primary">No</th>
                                      <th class="text-center bg-primary">#</th>
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
                            <div class="card-footer">
                              <p class="text-left">NO. DOKUMEN: MAS/FO/WH/25</p>
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
        var groupColumn = 4;
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
            url: "<?php echo base_url(); ?>laporan_rak/master_stock_rak_list",
            type: 'POST',
            "data": function(data) {
              data.rak    = $('#Rak').val();
              data.baris  = $('#Baris').val();
            }
          },
          'aoColumns': [
            {
              "No": "No",
              "sClass": "text-right"
            },
            {
              "#": "No",
              "sClass": "text-center"
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
              console.log(rows)
              console.log(group)
              console.log(last)
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

        $('#Rak').on('change', function() {
          let Selected = $(this).val();
          if (Selected == 'All') {
            var BarisHtml = '';
            BarisHtml += '<option value="All">All Baris</option>';
            $('#Baris').html(BarisHtml);
          } else {
            $.ajax({
              url: "<?php echo base_url(); ?>scan_rak/get_baris",
              data: {
                QrRak: Selected
              },
              type: 'POST',
              dataType: 'JSON',
              beforeSend: function() {
                $("#loading-screen").show();
              },
              success: function(hasil) {
                $("#loading-screen").hide();
                let Details = hasil.data;
                if (Details.length > 1) {
                  var BarisHtml = '';
                  var i;
                  BarisHtml += '<option disabled>-- Pilih --</option>';
                  BarisHtml += '<option selected value="All">All Baris</option>';
                  for(i = 0; i < Details.length; i++){
                    BarisHtml += '<option value="'+ Details[i].QRCode +'">'+ Details[i].Sequent.trim() +'</option>';
                  }
                  $('#Baris').html(BarisHtml);
                }
              },
              error: function() {
                alert('Error, Please try again!');
                $("#loading-screen").hide();
              }
            })
          }
        });
      });
    </script>
  </body>
</html>