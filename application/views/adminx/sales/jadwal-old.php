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
    <?php //$this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/pages/waves/css/waves.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/feather/css/feather.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/icofont/css/icofont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/font-awesome/css/font-awesome.min.css">
    <!-- DATATABLE CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/css/buttons.bootstrap5.css" />
    <!-- DATATABLE FREEZE COLUMN CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/fixedcolumns/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/pages.css">
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
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter by</label>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="LocationList" id="LocationList">
                                      <option disabled value="">-- Pilih --</option>
                                      <option selected value="KG">KG</option>
                                      <option value="Non KG">Non KG</option>
                                      <option value="Gresik">Gresik</option>
                                      <option value="Medan">Medan</option>
                                      <option value="Kendal">Kendal</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="Months" id="Months">
                                      <?php
                                        $months = [
                                          "January", "February", "March", "April", "May", "June",
                                          "July", "August", "September", "October", "November", "December"
                                        ];
                                        $currentMonth = date('n'); // Get current month (1-12)

                                        foreach ($months as $index => $month) {
                                          $value    = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                                          $selected = ($value == str_pad($currentMonth, 2, "0", STR_PAD_LEFT)) ? "selected" : "";
                                          echo "<option value='$value' $selected>$month</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="Years" id="Years">
                                      <?php
                                        $startYear    = 2025;
                                        $endYear      = 2050;
                                        $currentYear  = date('Y'); // Get current year

                                        for ($year = $startYear; $year <= $endYear; $year++) {
                                          $selected = ($year == $currentYear) ? "selected" : "";
                                          echo "<option value='$year' $selected>$year</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="550%">
                                  <thead>
                                    <tr class="bg-primary text-white">
                                      <th class="text-center" rowspan="2">NO</th>
                                      <th class="text-center" rowspan="2">ITEMS</th>
                                      <th class="text-center" rowspan="2">PART ID</th>
                                      <th class="text-center" rowspan="2">NO PO</th>
                                      <th class="text-center" rowspan="2">QTY. PO</th>
                                      <th class="text-center" rowspan="2">PO LEBIH</th>
                                      <?php for ($day = 1; $day <= 31; $day++) : ?>
                                        <th colspan="2" class="text-center"><?php echo $day; ?></th>
                                      <?php endfor; ?>
                                      <th class="text-center" rowspan="2">TOTAL PLAN</th>
                                      <th class="text-center" rowspan="2">TOTAL KIRIM</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">TOTAL FORECAST</th>
                                      <th class="text-center" rowspan="2">STOCK</th>
                                      <th class="text-center" rowspan="2">KETERANGAN</th>
                                      <th class="text-center" rowspan="2">KURANG KIRIM</th>
                                    </tr>
                                    <tr class="bg-primary text-white">
                                      <?php for ($day = 1; $day <= 31; $day++) : ?>
                                        <th class="text-center">PLAN</th>
                                        <th class="text-center">ACT</th>
                                      <?php endfor; ?>
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

    <?php //$this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <script src="<?php echo base_url(); ?>assets/code.jquery.com/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/bootstrap/js/bootstrap.min.js"></script>

    <script src="<?php echo base_url(); ?>files/assets/pages/waves/js/waves.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/css-scrollbars.js"></script>

    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.buttons.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/buttons.bootstrap5.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/buttons.html5.min.js"></script>
    <!-- DATATABLE FREEZE COLUMN -->
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/fixedcolumns/dataTables.fixedColumns.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>

    <script src="<?php echo base_url(); ?>files/assets/js/pcoded.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/vertical/vertical-layout.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/script.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment().subtract(7, 'days');
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

      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            { 
              extend: 'excelHtml5',
              text: 'Download data',
              title: '',
              className: 'btn btn-primary'
            }
          ],
          select: {
            style: 'single'
          },
          "pagingType": "full_numbers",
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
          ],
          responsive: false,
          select: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>pengiriman/timeline_jadwal_kirim_data",
            "type": "POST",
            "data": function(data) {
              data.Location  = $('#LocationList').val();
              data.Months    = $('#Months').val();
              data.Years     = $('#Years').val();
            }
          },
          fixedColumns: {
            left: 2
          },
          "aoColumns": [
            { "No": "No" , "sClass": "text-right", "width": "50px"},
            { "Part Name": "Part Name" , "sClass": "text-left", "width": "470px" },
            { "Part ID": "Part ID" , "sClass": "text-left", "width": "180px" },
            { "Nomor PO": "Nomor PO" , "sClass": "text-left", "width": "245px" },
            { "Qty PO": "Qty PO" , "sClass": "text-right", "width": "100px" },
            { "PO Lebih": "PO Lebih" , "sClass": "text-right", "width": "100px" },
            <?php for ($day = 1; $day <= 62; $day++) : ?>
              { "<?php echo $day; ?>": "<?php echo $day; ?>" , "sClass": "text-right", "width": "60px" },
            <?php endfor; ?>
            { "Total Plan": "Total Plan" , "sClass": "text-right", "width": "100px" },
            { "Total Kirim": "Total Kirim" , "sClass": "text-right", "width": "120px" },
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "Total Forecast": "Total Forecast" , "sClass": "text-right", "width": "150px" },
            { "Stock": "Stock" , "sClass": "text-right", "width": "100px" },
            { "Keterangan": "Keterangan" , "sClass": "text-left" },
            { "Kurang Kirim": "Kurang Kirim" , "sClass": "text-right", "width": "150px" }
          ]
        });

        // table.on('click', 'tbody tr', function (e) {
        //   e.currentTarget.classList.toggle('selected');
        // });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }
      });
    </script>
  </body>
</html>