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
    <?php $this->load->view('adminx/components/header_css_datatable_v2'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/css/filter_multi_select.css">
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
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-3 col-sm-12 mt-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 mt-3">
                                  <select name="Supir" id="Supir" class="form-control">
                                    <option value="All" selected>-- ALL SUPIR --</option>
                                    <?php foreach ($SupirList as $value): ?>
                                      <option value="<?= $value->SSN; ?>">
                                        <?= htmlspecialchars($value->NAME, ENT_QUOTES, 'UTF-8'); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 mt-3">
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-4 col-sm-12 mt-3 text-right">
                                  <button type="button" class="btn btn-success btn-full-mobile" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">REQ. GROUP</th>
                                      <th class="text-center" width="5%">SUPIR</th>
                                      <th class="text-center" width="5%">MOBIL</th>
                                      <th class="text-center" width="5%">TANGGAL</th>
                                      <th class="text-center" width="5%">E-TOLL</th>
                                      <th class="text-center" width="5%">BBM</th>
                                      <th class="text-center" width="5%">KM AWAL</th>
                                      <th class="text-center" width="5%">SOLAR</th>
                                      <th class="text-center" width="7%">ISI SOLAR</th>
                                      <th class="text-center" width="7%">TOTAL LITER</th>
                                      <th class="text-center" width="7%">ESTIMASI PER LITER</th>
                                      <th class="text-center" width="7%">ESTIMASI KM AKHIR</th>
                                      <th class="text-center" width="5%">TGL. KIRIM KE ACC</th>
                                      <th class="text-center" width="7%">BON ACCOUNTING</th>
                                      <th class="text-center" width="10%">CREATED DATE</th>
                                      <th class="text-center" width="10%">CREATED BY</th>
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

    <div id="loading" class="loading">Loading&#8230;</div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
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
    <script>
      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      function cari() 
      {
        reload_table();
      }

      function CheckGroupID(GroupIDNumber) {
        $.ajax({
          url: "<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_check_group_id",
          type: "POST",
          data: {GroupID: GroupIDNumber},
          success: function(data) {
            if(data == "") {
              alert("Data tidak ditemukan");
            } else {
              $("#GroupID").val(data);
            }
          }
        });
      }

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#order-table').DataTable({
          dom: 'frltip',
          "pagingType": "full_numbers",
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
          ],
          responsive: false,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          fixedColumns: {
            left: 0
          },
          select: {
            style: 'single'
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>pengeluaran_mobil/laporan_pengeluaran_mobil_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.supir        = $('#Supir').val();
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "REQ. GROUP": "REQ. GROUP" , "sClass": "text-left", "width": "50px"},
            { "SUPIR": "SUPIR" , "sClass": "text-left", "width": "50px"},
            { "MOBIL": "MOBIL" , "sClass": "text-left", "width": "50px"},
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "50px"},
            { "E-TOLL": "E-TOLL" , "sClass": "text-center", "width": "50px"},
            { "BBM": "BBM" , "sClass": "text-center", "width": "50px"},
            { "KM AWAL": "KM AWAL" , "sClass": "text-right", "width": "50px"},
            { "SOLAR": "SOLAR" , "sClass": "text-right", "width": "50px"},
            { "ISI SOLAR": "ISI SOLAR" , "sClass": "text-right", "width": "50px"},
            { "TOTAL LITER": "TOTAL LITER" , "sClass": "text-right", "width": "50px"},
            { "ESTIMASI PER LITER": "ESTIMASI PER LITER" , "sClass": "text-right", "width": "50px"},
            { "ESTIMASI KM AKHIR": "ESTIMASI KM AKHIR" , "sClass": "text-right", "width": "50px"},
            { "TGL. KIRIM KE ACC": "TGL. KIRIM KE ACC" , "sClass": "text-center", "width": "50px"},
            { "BON ACCOUNTING": "BON ACCOUNTING" , "sClass": "text-right", "width": "50px"},
            { "CREATED DATE": "CREATED DATE" , "sClass": "text-center", "width": "50px"},
            { "CREATED BY": "CREATED BY" , "sClass": "text-center", "width": "50px"}
          ],
          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        $(document).on('show.bs.dropdown', '.btn-group', function (e) {
          var $dropdown = $(e.target).find('.dropdown-menu');
          $('body').append($dropdown.detach()); // pindahkan ke body
          var eOffset = $(e.target).offset();
          $dropdown.css({
              'display': 'block',
              'top': eOffset.top + $(e.target).outerHeight(),
              'left': eOffset.left
          });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) {
          var $dropdown = $('body > .dropdown-menu');
          $(e.target).append($dropdown.detach()); // kembalikan ke dalam btn-group
          $dropdown.hide();
        });

        $("#Supir, #Mobil, #TanggalPengiriman, #HargaSolar, #KMAwal, #IsiSolar, #Files").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#modalGroup').on('shown.bs.modal', function () {
          $('#GroupList').select2({
            dropdownParent: $('#modalGroup'),
            placeholder: "Masukan Group ID",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>pengeluaran_mobil/get_groupid',
              type: 'POST',
              dataType: 'JSON',
              delay: 250,
              data: function(params) {
                return {
                  search: params.term
                };
              },
              processResults: function(data) {
                return {
                  results: $.map(data, function(item) {
                    return {
                      id: item.GroupID,
                      text: item.GroupID,
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          });
        });
      });

      $(document).on('change', '.check-customer', function() {
        // Cari baris (tr) tempat checkbox ini berada
        var row = $(this).closest('tr');
        var inputJumlah = row.find('.input-jumlah');

        if ($(this).is(':checked')) {
            // Jika dicentang: Aktifkan input dan beri warna border agar user tahu harus isi
            inputJumlah.prop('readonly', false).focus();
            inputJumlah.css('border', '1px solid #d33f8d'); // Warna pink sesuai tema Anda
        } else {
            // Jika batal centang: Kosongkan nilai, set readonly kembali, dan reset border
            inputJumlah.val('').prop('readonly', true);
            inputJumlah.css('border', '1px solid #ccc');
        }
    });
    </script>
  </body>
</html>