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
                                  <button id="btnOpenModal" type="button" class="btn btn-success btn-full-mobile" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <!-- <th class="text-center" width="8%">#</th> -->
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="5%">REQ. NUMBER</th>
                                      <!-- <th class="text-center" width="5%">REQ. GROUP</th> -->
                                      <th class="text-center" width="5%">SUPIR</th>
                                      <th class="text-center" width="5%">MOBIL</th>
                                      <th class="text-center" width="5%">TGL. AWAL KIRIM</th>
                                      <th class="text-center" width="5%">TGL. AKHIR KIRIM</th>
                                      <th class="text-center" width="5%">TGL. KIRIM</th>
                                      <th class="text-center" width="5%">E-TOLL</th>
                                      <th class="text-center" width="5%">BBM</th>
                                      <th class="text-center" width="5%">CUSTOMER</th>
                                      <th class="text-center" width="5%">X KIRIM</th>
                                      <th class="text-center" width="5%">KM AWAL</th>
                                      <th class="text-center" width="5%">KM AKHIR</th>
                                      <th class="text-center" width="5%">SOLAR</th>
                                      <th class="text-center" width="7%">ISI SOLAR</th>
                                      <th class="text-center" width="7%">TOTAL LITER</th>
                                      <th class="text-center" width="7%">ESTIMASI JARAK (KM)</th>
                                      <th class="text-center" width="7%">ESTIMASI KM AKHIR</th>
                                      <th class="text-center" width="5%">APPROVED BY WH</th>
                                      <th class="text-center" width="5%">APPROVED BY WH DATE</th>
                                      <th class="text-center" width="7%">APPROVED BY FINANCE</th>
                                      <th class="text-center" width="7%">APPROVED BY FINANCE DATE</th>
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

    <!-- MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formData" method="post" enctype="multipart/form-data">
              <input type="hidden" value="" name="KodeHeader">
              <input type="hidden" value="" name="Nomor">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Supir</label>
                <div class="col-sm-4">
                  <select name="Supir" id="Supir" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($SupirList as $value): ?>
                      <option value="<?= $value->SSN; ?>">
                        <?= htmlspecialchars($value->NAME, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Mobil</label>
                <div class="col-sm-4">
                  <select name="Mobil" id="Mobil" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($MobilList as $value): ?>
                      <option value="<?= $value['value']; ?>">
                        <?= $value['label']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Harga Solar (Per Liter)</label>
                <div class="col-sm-4">
                  <div class="input-group" style="margin-bottom: 0px !important;">
                    <input type="text" name="HargaSolar" id="HargaSolar" class="form-control" placeholder="Harga BBM" oninput="AllowDecimalAndComma(this)" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <span class="input-group-text bg-danger" id="basic-addon2" onclick="getHargaSolar()">Cek Harga</span>
                    </div>
                  </div>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Isi Solar (Rp)</label>
                <div class="col-sm-4">
                  <input type="text" name="IsiSolar" id="IsiSolar" class="form-control" placeholder="Isi Solar dalam rupiah" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tanggal Awal Kirim</label>
                <div class="col-sm-4">
                  <input type="date" name="TanggalAwalKirim" id="TanggalAwalKirim" class="form-control" onchange="cekDataDO()" placeholder="Tanggal pengiriman">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal Akhir Kirim</label>
                <div class="col-sm-4">
                  <input type="date" name="TanggalAkhirKirim" id="TanggalAkhirKirim" class="form-control" onchange="cekDataDO()" placeholder="Tanggal Akhir Kirim">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kilometer Awal</label>
                <div class="col-sm-4">
                  <input type="text" name="KMAwal" id="KMAwal" class="form-control" placeholder="Kilometer Awal Jalan" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Kilometer Akhir</label>
                <div class="col-sm-4">
                  <input type="text" name="KMAkhir" id="KMAkhir" class="form-control" placeholder="Kilometer Akhir Jalan" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Upload Struk E-Toll</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="FilesEtoll" id="FilesEtoll" class="form-control" required autocomplete="off" data-required="true">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Upload Struk BBM</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="FilesBbm" id="FilesBbm" class="form-control" required autocomplete="off" data-required="true">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1" id="DivShowFile">
                <label class="col-sm-2 col-form-label">File Struk E-Toll</label>
                <div class="col-sm-4 mt-1" id="Preview_StrukToll"></div>
                <label class="col-sm-2 col-form-label">File Struk BBM</label>
                <div class="col-sm-4 mt-1" id="Preview_StrukBbm"></div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10 form-error mb-2">
                  <textarea name="Noted" id="Noted" rows="3" placeholder="Tambahkan keterangan" class="form-control" ></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">CUSTOMER PT. MULTI ARTA SEKAWAN</label>
              </div>
              <div class="form-group row" id="DivCustomer">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" style="width:100%">
                    <thead class="bg-primary text-white">
                      <tr>
                        <th class="text-center" width="3%">#</th>
                        <th class="text-center" width="55%">Nama Penerima</th>
                        <th class="text-center" width="15%">Tanggal</th>
                        <th class="text-center" width="10%">Berapa X Kirim</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyCustomer">
                      <tr>
                        <td colspan="4" class="text-center">Data tidak ditemukan</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">CUSTOMER PT. MULTI ARTA INDUSTRI</label>
              </div>
              <div id="mainContainer">
                <div class="form-group row mb-2 mt-3" id="mainRow1">
                  <div class="col-md-4 form-error">
                    <label class="col-form-label">Nama Penerima</label>
                    <input type="text" name="NamaPenerimaMain[]" class="form-control text-uppercase" required placeholder="Nama Penerima MAiN" maxlength="75" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal Kirim</label>
                    <input type="date" name="TanggalKirimMain[]" class="form-control" required placeholder="Tanggal Kirim" maxlength="75" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Berapa X Kirim</label>
                    <input type="text" name="BerapaXKirimMain[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Berapa X Kirim" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalGroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Kirim ke Accounting?</h4>
            <button type="button" class="close" aria-label="Close" onclick="resetGroup()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formGroup" method="post">
              <div class="form-group row" id="DivCustomer">
                <div class="table-responsive">
                  <h6 class="text-center">Gabungkan ke Group sebelumnya?</h6>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Pilih Group</label>
                    <div class="col-sm-9">
                      <select name="GroupList" id="GroupList" class="form-control">
                        <option value="">-- Pilih --</option>
                      </select>
                    </div>
                  </div>
                  <table class="table table-bordered table-striped" style="width:100%">
                    <thead class="bg-primary text-white">
                      <tr>
                        <th class="text-center" width="5%">NO</th>
                        <th class="text-center" width="90%">REQ NUMBER</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyGroup">
                      <tr>
                        <td colspan="2" class="text-center">Data tidak ditemukan</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="resetGroup()">Close</button>
            <button id="btnSave" type="button" onclick="saveGroup();" class="btn btn-primary waves-effect waves-light ">Kirim</button>
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
      var save_method;
      var url;

      function approved_by_wh_head(ReqNumber) {
        Swal.fire({
          title: "Approved by WH #" + ReqNumber,
          text: "Apakah anda yakin akan menyetujui pengeluaran mobil dengan nomor request ini?",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, Setujui"
        }).then((result) => {
          $.ajax({
            url : "<?php echo base_url();?>pengeluaran_mobil/approved_by_wh_head",
            method : "POST",
            dataType : 'json',
            data: {
              RequestNumber: ReqNumber
            },
            beforeSend: function() {
              $("#loading").show();
            },
            success: function(data){
              if (data.status == 'forbidden') {
                Swal.fire('FORBIDDEN', 'Access Denied', 'info');
              } else {
                console.log(data);
                reload_table();
                $("#loading").hide();
              }
            },
            complete: function() {
              $("#loading").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
              $("#loading").hide();
              console.log(xhr.status);
              console.log(thrownError);
            }
          });
        });
      }

      function approved_by_finance(ReqNumber) {
        Swal.fire({
          title: "Approved by WH #" + ReqNumber,
          text: "Apakah anda yakin akan menyetujui pengeluaran mobil dengan nomor request ini?",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, Setujui"
        }).then((result) => {
          $.ajax({
            url : "<?php echo base_url();?>pengeluaran_mobil/approved_by_finance",
            method : "POST",
            dataType : 'json',
            data: {
              RequestNumber: ReqNumber
            },
            beforeSend: function() {
              $("#loading").show();
            },
            success: function(data){
              if (data.status == 'forbidden') {
                Swal.fire('FORBIDDEN', 'Access Denied', 'info');
              } else {
                console.log(data);
                reload_table();
                $("#loading").hide();
              }
            },
            complete: function() {
              $("#loading").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
              $("#loading").hide();
              console.log(xhr.status);
              console.log(thrownError);
            }
          });
        });
      }

      function getHargaSolar() {
        $.ajax({
          url : "<?php echo base_url();?>pengeluaran_mobil/get_harga_solar",
          method : "GET",
          dataType : 'json',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data){
            console.log(data);
            $("#loading").hide();

            var hargaFormatted = data.harga.replace(',', '.');
            $("#HargaSolar").val(hargaFormatted);
          },
          complete: function() {
            $("#loading").hide();
          },
          error: function (xhr, ajaxOptions, thrownError) {
            $("#loading").hide();
            console.log(xhr.status);
            console.log(thrownError);
          }
        });
      }

      function cekDataDO() {
        var tanggalAwal  = $("#TanggalAwalKirim").val();
        var tanggalAkhir = $("#TanggalAkhirKirim").val();
        var supir        = $("#formData #Supir").val();

        // 1. Cek jika supir belum dipilih
        if (!supir) {
          Swal.fire({
            icon: 'warning',
            title: 'Supir Belum Dipilih',
            text: 'Silakan pilih supir terlebih dahulu sebelum memilih tanggal kirim.',
            confirmButtonColor: '#3085d6'
          });
          $("#TanggalAwalKirim").val('');
          $("#TanggalAkhirKirim").val('');
          $("#tbodyCustomer").empty();
          return;
        }

        // 2. Cek jika salah satu atau kedua tanggal belum diisi
        if (!tanggalAwal || !tanggalAkhir) {
          $("#tbodyCustomer").empty(); // Kosongkan tabel
          return;
        }

        $.ajax({
          url: "<?php echo base_url(); ?>pengeluaran_mobil/get_data_do_by_date",
          method: "POST",
          dataType: 'json',
          data: {
            TanggalAwalKirim  : tanggalAwal,
            TanggalAkhirKirim : tanggalAkhir,
            Supir             : supir
          },
          beforeSend: function() {
            $("#loading").show();
            // Tampilkan indikator loading di dalam tabel atau kosongkan dulu
            $("#tbodyCustomer").html('<tr><td colspan="3" class="text-center">Sedang memuat data...</td></tr>');
          },
          success: function(response) {
            $("#loading").hide();
            var tbody = $("#tbodyCustomer");
            tbody.empty(); // Bersihkan loading/data lama

            if (response.status === 'success' && response.data.length > 0) {
              var html = '';

              $.each(response.data, function(index, item) {
                  html += '<tr>';
                  // KOLOM 1: Checkbox
                  html += '<td class="text-center">';
                  var isChecked = (item.BerapaXKirim !== null && item.BerapaXKirim !== '' && item.BerapaXKirim !== undefined) ? 'checked' : '';
                  var cbValue  = (item.BerapaXKirim !== null && item.BerapaXKirim !== '' && item.BerapaXKirim !== undefined) ? item.ShipmentID : item.NamaPenerima;
                  html += '  <input type="checkbox" name="PilihCustomer[]" class="check-customer" value="' + cbValue + '" ' + isChecked + ' style="transform: scale(1.2); cursor:pointer;">';
                  html += '</td>';

                  // KOLOM 2: Nama Penerima
                  html += '<td>';
                  html += '  <input type="text" name="NamaPenerima[]" value="' + item.NamaPenerima + '" class="form-control form-control-sm border-0 bg-transparent" readonly>';
                  html += '</td>';

                  // KOLOM 3: Tanggal
                  html += '<td>';
                  html += '  <input type="date" name="TanggalKirimCustomer[]" value="' + item.TglFaktur + '" class="form-control form-control-sm" readonly>';
                  html += '</td>';

                  // KOLOM 4: Berapa X Kirim
                  html += '<td>';
                  // Tambahkan class 'input-jumlah' dan set readonly secara default
                  html += '  <input type="number" name="BerapaXKirim[]" value="' + item.BerapaXKirim + '" class="form-control form-control-sm text-center input-jumlah" readonly>';
                  html += '  <input type="hidden" name="ListNoBukti[]" value="' + item.ListNoBukti + '">';
                  html += '  <input type="hidden" name="TotalDO[]" value="' + item.TotalDO + '">';
                  html += '  <input type="hidden" name="ShipmentID[]" value="' + item.ShipmentID + '">';
                  html += '</td>';
                  html += '</tr>';
              });

              // Masukkan baris-baris tersebut ke tbody
              tbody.html(html);

            } else {
              // Jika Data Kosong
              tbody.html('<tr><td colspan="4" class="text-center text-danger font-italic">Tidak ada data pengiriman pada tanggal ini.</td></tr>');
            }
          },
          complete: function() {
            $("#loading").hide();
          },
          error: function(xhr, ajaxOptions, thrownError) {
            $("#loading").hide();
            console.log("Error: " + xhr.status + " - " + thrownError);
            $("#tbodyCustomer").html('<tr><td colspan="4" class="text-center text-danger">Gagal mengambil data. Silakan coba lagi.</td></tr>');
          }
        });
      }

      function cekDataDO_OLD(tanggal) {
        // 1. Cek jika tanggal kosong
        if (!tanggal) {
          $("#tbodyCustomer").empty(); // Kosongkan tabel
          return;
        }

        $.ajax({
          url: "<?php echo base_url(); ?>pengeluaran_mobil/get_data_do_by_date",
          method: "POST",
          dataType: 'json',
          data: {
            TanggalKirim: tanggal
          },
          beforeSend: function() {
            $("#loading").show();
            // Tampilkan indikator loading di dalam tabel atau kosongkan dulu
            $("#tbodyCustomer").html('<tr><td colspan="3" class="text-center">Sedang memuat data...</td></tr>');
          },
          success: function(response) {
            $("#loading").hide();
            var tbody = $("#tbodyCustomer");
            tbody.empty(); // Bersihkan loading/data lama

            if (response.status === 'success' && response.data.length > 0) {
              var html = '';

              $.each(response.data, function(index, item) {
                  html += '<tr>';
                  // KOLOM 1: Checkbox
                  html += '<td class="text-center">';
                  html += '  <input type="checkbox" name="PilihCustomer[]" class="check-customer" value="' + item.NamaPenerima + '" style="transform: scale(1.2); cursor:pointer;">';
                  html += '</td>';

                  // KOLOM 2: Nama Penerima
                  html += '<td>';
                  html += '  <input type="text" name="NamaPenerima[]" class="form-control form-control-sm border-0 bg-transparent" value="' + item.NamaPenerima + '" readonly>';
                  html += '</td>';

                  // KOLOM 3: Berapa X Kirim
                  html += '<td>';
                  // Tambahkan class 'input-jumlah' dan set readonly secara default
                  html += '  <input type="number" name="BerapaXKirim[]" class="form-control form-control-sm text-center input-jumlah" readonly>';
                  html += '  <input type="hidden" name="ListNoBukti[]" value="' + item.ListNoBukti + '">';
                  html += '  <input type="hidden" name="TotalDO[]" value="' + item.TotalDO + '">';
                  html += '  <input type="hidden" name="ShipmentID[]" value="' + item.ShipmentID + '">';
                  html += '</td>';
                  html += '</tr>';
              });

              // Masukkan baris-baris tersebut ke tbody
              tbody.html(html);

            } else {
              // Jika Data Kosong
              tbody.html('<tr><td colspan="3" class="text-center text-danger font-italic">Tidak ada data pengiriman pada tanggal ini.</td></tr>');
            }
          },
          complete: function() {
            $("#loading").hide();
          },
          error: function(xhr, ajaxOptions, thrownError) {
            $("#loading").hide();
            console.log("Error: " + xhr.status + " - " + thrownError);
            $("#tbodyCustomer").html('<tr><td colspan="3" class="text-center text-danger">Gagal mengambil data. Silakan coba lagi.</td></tr>');
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() 
      {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Data');
        $('#ShowDrawing').hide();
        $('#DivShowFile').hide();
      }

      function closeModal() 
      {
        $('#formData')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION RESET
      function reset() 
      {
        $('#btnOpenModal').focus();
        $('#modal').modal('hide');
        $('#formData')[0].reset();
        $('.modal-title').text('Tambah Data');
        $('#tbodyCustomer').html('<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>');

        $('#mainContainer').html(`
          <div class="form-group row mb-2 mt-3" id="mainRow1">
            <div class="col-md-4 form-error">
              <label class="col-form-label">Nama Penerima</label>
              <input type="text" name="NamaPenerimaMain[]" class="form-control text-uppercase" required placeholder="Nama Penerima MAiN" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Kirim</label>
              <input type="date" name="TanggalKirimMain[]" class="form-control" required placeholder="Tanggal Kirim" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Berapa X Kirim</label>
              <input type="text" name="BerapaXKirimMain[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berapa X Kirim" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      function resetGroup() 
      {
        $('#modalGroup').modal('hide');
        $('#formGroup')[0].reset();
        $('.modal-title').text('Kirim ke Accounting');
      }

      //FUNCTION EDIT
      function edit(id) 
      {
        save_method = 'update';
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#DivShowFile').show();

        // Reset Container agar bersih sebelum diisi data baru
        $('#Preview_StrukToll').html('');
        $('#Preview_StrukBbm').html('');
        $('#tbodyCustomer').html(''); // Reset Tabel Detail 1
        $('#mainContainer').html(''); // Reset Input Detail 2

        $.ajax({
            url: "<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_edit",
            type: "POST",
            data: {Nomor: id},
            dataType: "JSON",
            success: function(data) {
                if (data.status == 'forbidden') {
                    Swal.fire('FORBIDDEN', 'Access Denied', 'info');
                } else {

                    // ==========================================
                    // 1. ISI DATA HEADER
                    // ==========================================
                    var HargaSolar      = data.header.HargaSolar ? parseFloat(data.header.HargaSolar).toLocaleString('id-ID') : '';
                    var IsiSolar        = data.header.IsiSolar ? parseFloat(data.header.IsiSolar).toLocaleString('id-ID') : '';
                    var KilometerAwal   = data.header.KilometerAwal ? parseFloat(data.header.KilometerAwal).toLocaleString('id-ID') : '';
                    var KilometerAkhir  = data.header.KilometerAkhir ? parseFloat(data.header.KilometerAkhir).toLocaleString('id-ID') : '';
                    var TotalIsiSolar   = data.header.TotalIsiSolar ? parseFloat(data.header.TotalIsiSolar).toLocaleString('id-ID') : '';

                    $('[name="KodeHeader"]').val(data.header.Id);
                    $('[name="Nomor"]').val(data.header.Nomor);
                    $('[name="Supir"]').val(data.header.EmployeeID);
                    $('[name="Mobil"]').val(data.header.MobilID_Value).trigger('change');
                    $('[name="TanggalAwalKirim"]').val(data.header.TanggalAwalKirim);
                    $('[name="TanggalAkhirKirim"]').val(data.header.TanggalAkhirKirim);
                    $('[name="Noted"]').val(data.header.Noted);
                    $('[name="HargaSolar"]').val(HargaSolar);
                    $('[name="KMAwal"]').val(KilometerAwal);
                    $('[name="KMAkhir"]').val(KilometerAkhir);
                    $('[name="IsiSolar"]').val(TotalIsiSolar);

                    // Preview Struk Toll
                    if (data.header.StrukToll) {
                        var htmlToll = '<a href="' + data.header.Link_StrukToll + '" target="_blank" class="text-primary" style="font-size:11px;">' +
                                      '<i class="fa fa-paperclip"></i> ' + data.header.StrukToll + 
                                      '</a>';
                        $('#Preview_StrukToll').html(htmlToll);
                    } else {
                        $('#Preview_StrukToll').html('<span class="text-muted font-italic">- Tidak ada file -</span>');
                    }

                    // Preview Struk BBM
                    if (data.header.StrukBbm) {
                        var htmlBbm = '<a href="' + data.header.Link_StrukBbm + '" target="_blank" class="text-primary" style="font-size:11px;">' +
                                      '<i class="fa fa-paperclip"></i> ' + data.header.StrukBbm + 
                                      '</a>';
                        $('#Preview_StrukBbm').html(htmlBbm);
                    } else {
                        $('#Preview_StrukBbm').html('<span class="text-muted font-italic">- Tidak ada file -</span>');
                    }

                    // ==========================================
                    // 2. ISI DETAIL 1 (TABEL CHECKBOX)
                    // ==========================================
                    var htmlDetail1 = '';
                    if(data.detail1 && data.detail1.length > 0){
                        $.each(data.detail1, function(index, item) {
                            htmlDetail1 += '<tr>';
                            // Checkbox (Checked karena data tersimpan)
                            htmlDetail1 += '<td class="text-center">';
                            htmlDetail1 += '  <input type="checkbox" name="PilihCustomer[]" class="check-customer" value="' + item.ShipmentID + '" checked style="transform: scale(1.2); cursor:pointer;">';
                            htmlDetail1 += '</td>';
                            // Nama Customer
                            htmlDetail1 += '<td>';
                            htmlDetail1 += '  <input type="text" name="NamaPenerima[]" class="form-control form-control-sm border-0 bg-transparent" value="' + item.CustomerName + '" readonly>';
                            htmlDetail1 += '  <input type="hidden" name="KodeDetail1[]" value="' + item.Id + '">';
                            htmlDetail1 += '</td>';
                            // Tanggal
                            htmlDetail1 += '<td>';
                            htmlDetail1 += '  <input type="date" name="TanggalKirimCustomer[]" value="' + (item.TanggalKirim || '') + '" class="form-control form-control-sm" value="' + (item.TanggalKirimCustomer || '') + '" readonly>';
                            htmlDetail1 += '</td>';
                            // Jumlah Kirim & Hidden Fields
                            htmlDetail1 += '<td>';
                            htmlDetail1 += '  <input type="number" name="BerapaXKirim[]" class="form-control form-control-sm text-center input-jumlah" value="' + item.BerapaXKirim + '">';
                            htmlDetail1 += '  <input type="hidden" name="ListNoBukti[]" value="' + item.ListNoBukti + '">';
                            htmlDetail1 += '  <input type="hidden" name="TotalDO[]" value="' + $.trim(item.TotalDO) + '">';
                            htmlDetail1 += '  <input type="hidden" name="ShipmentID[]" value="' + item.ShipmentID + '">';
                            htmlDetail1 += '</td>';
                            htmlDetail1 += '</tr>';
                        });
                    } else {
                        htmlDetail1 = '<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>';
                    }
                    $('#tbodyCustomer').html(htmlDetail1);

                    // ==========================================
                    // 3. ISI DETAIL 2 (INPUT DINAMIS - CUSTOMER MAI)
                    // ==========================================
                    // Sesuai kode permintaan Anda:
                    
                    var htmlDetail2 = '';
                  
                    if (data.detail2 && data.detail2.length > 0) {
                        data.detail2.forEach((item, index) => {
                            let rowNumber = index + 1;
                            // Tombol Plus hanya di baris pertama
                            let btnAction = (index === 0) 
                                ? `<a href="javascript:void(0)" class="btn btn-success" id="tambah1" title="Tambah"><i class="fa fa-plus"></i></a>`
                                : `<a href="javascript:void(0)" class="btn btn-danger" onclick="hapusRow('mainRow${rowNumber}')" title="Hapus"><i class="fa fa-minus"></i></a>`;

                            htmlDetail2 += `
                            <div class="form-group row mb-2 mt-2" id="mainRow${rowNumber}">
                                <div class="col-md-4 form-error">
                                    <label class="col-form-label">Nama Penerima</label>
                                    <input type="text" value="${item.CustomerName}" name="NamaPenerimaMain[]" class="form-control text-uppercase" required placeholder="Nama Penerima" maxlength="75">
                                    <input type="hidden" name="KodeDetail2[]" value="${item.Id}">
                                </div>
                                <div class="col-md-2 form-error">
                                    <label class="col-form-label">Tanggal Kirim</label>
                                    <input type="date" name="TanggalKirimMain[]" value="${item.TanggalKirim}" class="form-control" required placeholder="Tanggal Kirim" maxlength="75" autocomplete="off" data-required="true">
                                    <span class="help-block"></span>
                                </div>
                                <div class="col-md-2 form-error">
                                    <label class="col-form-label">Berapa X Kirim</label>
                                    <input type="text" value="${item.BerapaXKirim}" name="BerapaXKirimMain[]" class="form-control" oninput="AllowDecimalAndComma(this)">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    ${btnAction}
                                </div>
                            </div>`;
                        });
                    } else {
                        // Jika data kosong, tampilkan 1 baris kosong default
                        htmlDetail2 += `
                        <div class="form-group row mb-2 mt-2" id="mainRow1">
                            <div class="col-md-4 form-error">
                                <label class="col-form-label">Nama Penerima</label>
                                <input type="text" name="NamaPenerimaMain[]" class="form-control text-uppercase" required placeholder="Nama Penerima">
                                <input type="hidden" name="KodeDetail2[]" value="">
                            </div>
                            <div class="col-md-2 form-error">
                                <label class="col-form-label">Berapa X Kirim</label>
                                <input type="text" name="BerapaXKirimMain[]" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Berapa X Kirim">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="javascript:void(0)" class="btn btn-success" id="tambah1"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>`;
                    }

                    $('#mainContainer').html(htmlDetail2);


                    // ==========================================
                    // 4. BUKA MODAL
                    // ==========================================
                    $('#modal').modal('show');
                    $('.modal-title').text('Edit Data');
                    $('#btnSave').text('Update');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
      }

      function hapusRow(rowId)
      {
        const row         = $('#' + rowId);
        // Ambil data sebelum dihapus
        const NoRequest   = $('input[name="Nomor"]').val();
        const KodeDetail  = row.find('input[name="KodeDetail2[]"]').val();

        Swal.fire({
          title: "Yakin ingin hapus?",
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, hapus",
          cancelButtonText: "Batal"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?php echo base_url(); ?>pengeluaran_mobil/hapus_customer_main",
              type: "POST",
              dataType: "JSON",
              data: {
                Nomor: NoRequest,
                IdDetail: KodeDetail
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                if (data.status == 'forbidden') {
                  $("#loading").hide();
                  Swal.fire('FORBIDDEN', 'Access Denied', 'info');
                } else {
                  $("#loading").hide();
                  //edit(NoRequest);
                  reload_table();
                  // Hapus elemen
                  row.remove();
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $("#loading").hide();
                alert('Error hapus data');
              }
            });
          }
        });
      }

      //FUNCTION HAPUS
      function hapus(id) 
      {
        Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_deleted',
              type: 'POST',
              data: {Nomor: id},
              error: function() {
                alert('Something is wrong');
                $("#loading").hide();
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                var result = JSON.parse(data);
                if (result.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  $("#" + id).remove();
                  reload_table();
                }

                $("#loading").hide();
              }
            });
          }
        })
      }

      //FUNCTION RELOAD TABLE
      function reload_table() 
      {
        table.ajax.reload(null, false);
      }

      function SetGroupID(ReqNumber, ReqStatus) 
      {
        $.ajax({
          url: '<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_kirim_group',
          type: 'POST',
          data: {Nomor: ReqNumber, Status: ReqStatus},
          error: function() {
            alert('Something is wrong');
            $("#loading").hide();
          },
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            var result = JSON.parse(data);
            if (result.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else {
              reload_table();
            }

            $("#loading").hide();
          }
        });
      }

      $(document).on('click', '#tambah1', function () 
      {
        let count = $('#mainContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-3" id="mainRow${count}">
            <div class="col-md-4 form-error">
              <label class="col-form-label">Nama Penerima</label>
              <input type="text" name="NamaPenerimaMain[]" class="form-control text-uppercase" required placeholder="Nama Penerima MAiN" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Kirim</label>
              <input type="date" name="TanggalKirimMain[]" class="form-control" required placeholder="Tanggal Kirim" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Berapa X Kirim</label>
              <input type="text" name="BerapaXKirimMain[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berapa X Kirim" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-main" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#mainContainer').append(row);
      });

      $(document).on('click', '.remove-kolom-main', function () 
      {
        $(this).closest('.form-group').remove();
      });

      //SAVE GROUP
      function saveGroup() 
      {
        var form  = $('#formGroup').serialize();
        $.ajax({
          url: '<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_kirim_group',
          type: 'POST',
          data: form,
          error: function() {
            alert('Something is wrong');
            $("#loading").hide();
          },
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            var result = JSON.parse(data);
            if (result.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else {
              resetGroup();
              reload_table();
            }

            $("#loading").hide();
          }
        });
      }

      //SAVE
      function save() 
      {
        var valid = true;
        var pesan = "";

        // Loop setiap baris yang checkbox-nya dicentang
        $('.check-customer:checked').each(function() 
        {
          var row = $(this).closest('tr');
          var nama = row.find('input[name="NamaPenerima[]"]').val();
          var jumlah = row.find('.input-jumlah').val();

          if (jumlah === "" || jumlah <= 0) {
            valid = false;
            pesan += "Jumlah kiriman untuk <b>" + nama + "</b> belum diisi!<br>";
            row.find('.input-jumlah').css('border', '2px solid red');
          }
        });

        // Cek apakah minimal ada satu yang dicentang
        if ($('.check-customer:checked').length === 0) 
        {
          Swal.fire('Info', 'Silahkan pilih minimal satu Nama Penerima!', 'warning');
          return;
        }

        if (!valid) 
        {
          Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: pesan
          });
          return;
        }

        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_add";
        } else {
          url = "<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_update";
        }

        var form      = $('#formData')[0];
        var data_save = new FormData(form);
        var namaSupir = $.trim($('#formData #Supir option:selected').text());
        data_save.append('NamaSupir', namaSupir);

        // ajax adding data to database
        $.ajax({
          url: url,
          type: "POST",
          cache: false,
          contentType: false,
          processData: false,
          data: data_save,
          dataType: "JSON",
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            if (data.status == 'success') //if success close modal and reload ajax table
            {
              $('#modal').modal('hide');
              reload_table();
              reset();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }
            console.log(save_method);
            $("#loading").hide();
            if(save_method == 'add') {
              $("#btnSave").text('Save');
            } else {
              $("#btnSave").text('Update');
            }
            $("#btnSave").prop('disabled', false);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled', false); //set button enable 
          }
        });
      };

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            // {
            //   text: '<i class="fa fa-send"></i> Kirim Data',
            //   className: 'btn btn-danger',
            //   attr: {
            //     id: 'btn-simpan-custom'
            //   },
            //   action: function ( e, dt, node, config ) {
            //     // 1. Ambil data Checkbox yang terpilih
            //     var Nomor = [];
                
            //     $('input[name="GroupID[]"]:checked').each(function() {
            //         Nomor.push($(this).val()); 
            //     });

            //     // Validasi sederhana
            //     if (Nomor.length === 0) {
            //         Swal.fire('Info', 'Pilih setidaknya satu data untuk dikirim!', 'warning');
            //         return;
            //     }

            //     // --- PROSES INSERT KE TABEL ---
            //     var html = '';
            //     $.each(Nomor, function(index, value) {
            //         var no = index + 1;
            //         html += '<tr>' +
            //                     '<td class="text-center">' + no + '</td>' +
            //                     '<td>' +
            //                         '<input type="text" name="ReqNumber[]" class="form-control form-control-sm" value="' + value + '" readonly>' +
            //                     '</td>' +
            //                 '</tr>';
            //     });

            //     // Masukkan ke dalam tbody
            //     $('#tbodyGroup').html(html);

            //     // Tampilkan modal
            //     $('#modalGroup').modal('show');
            //   }
            // }
          ],
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
            "url": "<?php echo base_url(); ?>pengeluaran_mobil/pengeluaran_mobil_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.supir     = $('#Supir').val();
            }
          },
          "aoColumns": [
            // { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px"},
            { "REQ. NUMBER": "REQ. NUMBER" , "sClass": "text-left", "width": "50px"},
            // { "REQ. GROUP": "REQ. GROUP" , "sClass": "text-left", "width": "50px"},
            { "SUPIR": "SUPIR" , "sClass": "text-left", "width": "50px"},
            { "MOBIL": "MOBIL" , "sClass": "text-left", "width": "50px"},
            { "TGL. AWAL KIRIM": "TGL. AWAL KIRIM" , "sClass": "text-center", "width": "50px"},
            { "TGL. AKHIR KIRIM": "TGL. AKHIR KIRIM" , "sClass": "text-center", "width": "50px"},
            { "TGL. KIRIM": "TGL. KIRIM" , "sClass": "text-center", "width": "50px"},
            { "E-TOLL": "E-TOLL" , "sClass": "text-center", "width": "50px"},
            { "BBM": "BBM" , "sClass": "text-center", "width": "50px"},
            { "CUSTOMER": "CUSTOMER" , "sClass": "text-left", "width": "50px"},
            { "X KIRIM": "X KIRIM" , "sClass": "text-center", "width": "50px"},
            { "KM AWAL": "KM AWAL" , "sClass": "text-right", "width": "50px"},
            { "KM AKHIR": "KM AKHIR" , "sClass": "text-right", "width": "50px"},
            { "SOLAR": "SOLAR" , "sClass": "text-right", "width": "50px"},
            { "ISI SOLAR": "ISI SOLAR" , "sClass": "text-right", "width": "50px"},
            { "TOTAL LITER": "TOTAL LITER" , "sClass": "text-right", "width": "50px"},
            { "ESTIMASI JARAK (KM)": "ESTIMASI JARAK (KM)" , "sClass": "text-right", "width": "50px"},
            { "ESTIMASI KM AKHIR": "ESTIMASI KM AKHIR" , "sClass": "text-right", "width": "50px"},
            { "APPROVED BY WH": "APPROVED BY WH" , "sClass": "text-center", "width": "50px"},
            { "APPROVED BY WH DATE": "APPROVED BY WH DATE" , "sClass": "text-center", "width": "50px"},
            { "APPROVED BY FINANCE": "APPROVED BY FINANCE" , "sClass": "text-center", "width": "50px"},
            { "APPROVED BY FINANCE DATE": "APPROVED BY FINANCE DATE" , "sClass": "text-center", "width": "50px"},
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

        $(document).on('show.bs.dropdown', '.btn-group', function (e) 
        {
          var $dropdown = $(e.target).find('.dropdown-menu');
          $('body').append($dropdown.detach()); // pindahkan ke body
          var eOffset = $(e.target).offset();
          $dropdown.css({
              'display': 'block',
              'top': eOffset.top + $(e.target).outerHeight(),
              'left': eOffset.left
          });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) 
        {
          var $dropdown = $('body > .dropdown-menu');
          $(e.target).append($dropdown.detach()); // kembalikan ke dalam btn-group
          $dropdown.hide();
        });

        $("#Supir, #Mobil, #TanggalAwalKirim, #TanggalAkhirKirim, #HargaSolar, #KMAwal, #KMAkhir, #IsiSolar, #Files").change(function()
        {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#modalGroup').on('shown.bs.modal', function () 
        {
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

      // Reset tanggal dan tabel customer ketika supir diganti
      $(document).on('change', '#formData #Supir', function() 
      {
        $('#TanggalAwalKirim').val('');
        $('#TanggalAkhirKirim').val('');
        $('#tbodyCustomer').html('<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>');
      });

      $(document).on('change', '.check-customer', function() 
      {
        // Cari baris (tr) tempat checkbox ini berada
        var row = $(this).closest('tr');
        var inputJumlah  = row.find('.input-jumlah');
        var inputTanggal = row.find('input[name="TanggalKirimCustomer[]"]');

        if ($(this).is(':checked')) {
          // Jika dicentang: Aktifkan input-jumlah dan TanggalKirimCustomer
          inputJumlah.prop('readonly', false).focus();
          inputJumlah.css('border', '1px solid #d33f8d');
          // inputTanggal.prop('readonly', false);
          // inputTanggal.css('border', '1px solid #d33f8d');
        } else {
          // Jika batal centang: set readonly kembali, dan reset border (Nilai tidak dihapus)
          inputJumlah.prop('readonly', true);
          inputJumlah.css('border', '1px solid #ccc');
          inputTanggal.prop('readonly', true);
          inputTanggal.css('border', '1px solid #ccc');
        }
      });
    </script>
  </body>
</html>