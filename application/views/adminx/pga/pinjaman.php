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
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control" multiple>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">NOMOR</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="5%">DEPARTEMEN</th>
                                      <th class="text-center" width="5%">NIP</th>
                                      <th class="text-center" width="7%">NAME</th>
                                      <th class="text-center" width="7%">JANGKA WAKTU</th>
                                      <th class="text-center" width="7%">JUMLAH PENGAJUAN</th>
                                      <th class="text-center" width="7%">TANGGAL DISETUJUI</th>
                                      <th class="text-center" width="7%">NOTES</th>
                                      <th class="text-center" width="10%">CREATED DATE</th>
                                      <th class="text-center" width="10%">CREATED BY</th>
                                      <th class="text-center" width="10%">HRD APPROVED DATE</th>
                                      <th class="text-center" width="10%">HRD APPROVED BY</th>
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
            <form id="formData" method="post">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4">
                  <select name="DeptID" id="DeptID" class="form-control" onchange="get_karyawan(this);">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($department_att as $value): ?>
                      <option value="<?= $value->DEPTID; ?>">
                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Pegawai</label>
                <div class="col-sm-4">
                  <select name="EmployeeID" id="EmployeeID" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jumlah Pengajuan</label>
                <div class="col-sm-4">
                  <input type="text" id="JumlahPengajuan" name="JumlahPengajuan" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Contoh: 5.000.000" maxlength="12" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Jangka Waktu</label>
                <div class="col-sm-2">
                  <select name="JangkaWaktu" id="JangkaWaktu" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php
                      // Melakukan looping (perulangan) dari 1 hingga 60
                      for ($bulan = 1; $bulan <= 60; $bulan++) {
                        ?>
                        <option value="<?php echo $bulan; ?>"><?php echo $bulan; ?> x Bayar</option>
                        <?php
                      }
                    ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-1">
                  <button id="btnProses" type="button" class="btn btn-warning" onclick="">Simulasi</button>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Notes</label>
                <div class="col-sm-10">
                  <textarea name="Notes" id="Notes" class="form-control" rows="3" maxlength="255" placeholder="Keterangan mengajukan pinjaman"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mt-2">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <hr>
                    <h5 class="text-center">SIMULASI PENGAJUAN PINJAMAN</h5>
                    <hr>
                    <table class="table table-bordered table-striped" id="tabelCicilan">
                      <thead class="bg-primary text-white">
                        <tr>
                          <th class="text-center" width="10%">ANGSURAN KE</th>
                          <th class="text-center" width="45%">NOMINAL ANGSURAN</th>
                          <th class="text-center" width="45%">SISA PINJAMAN</th>
                        </tr>
                      </thead>
                      <tbody id="bodyCicilan">
                        <tr>
                          <td class="text-center" colspan="3">Data tidak ditemukan</td>
                        </tr>
                      </tbody>
                    </table>
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
        // Fungsi format Rupiah sederhana
        function formatRibuan(angka) {
          return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Trigger otomatis saat mengetik nominal atau mengganti jangka waktu
        $('#JumlahPengajuan, #JangkaWaktu').on('input change', function() {
          hitungSimulasi();
        });

        // Jika tombol "Proses" masih ingin dipertahankan fungsinya (opsional)
        $('#btnProses').click(function() {
          hitungSimulasi();
        });

        function hitungSimulasi() {
            // 1. Ambil Nilai
            var pengajuanStr  = $('#JumlahPengajuan').val().replace(/\./g, '');
            var tenor         = parseInt($('#JangkaWaktu').val());
            var totalPinjaman = parseFloat(pengajuanStr);

            // 2. Validasi Diam (Silent Validation)
            // Jika data belum lengkap/nol, jangan alert, tapi tampilkan 'Data tidak ditemukan'
            if (!totalPinjaman || !tenor) {
                $('#bodyCicilan').html('<tr><td class="text-center" colspan="3">Data tidak ditemukan</td></tr>');
                return; 
            }

            // 3. Hitung Angsuran Dasar
            var angsuranPerBulan = Math.floor(totalPinjaman / tenor);
            var sisaPinjaman     = totalPinjaman;
            var html             = '';

            // 4. Looping
            for (var i = 1; i <= tenor; i++) {
                var bayarSaatIni = angsuranPerBulan;

                // Koreksi pembulatan di bulan terakhir
                if (i === tenor) {
                    bayarSaatIni = sisaPinjaman;
                }

                sisaPinjaman -= bayarSaatIni;

                html += `
                    <tr>
                      <td class="text-center">
                        ${i}
                        <input type="hidden" name="AngsuranKe[]" value="${i}">
                      </td>
                      <td class="text-right">
                        Rp ${formatRibuan(bayarSaatIni)}
                        <input type="hidden" name="NominalAngsuran[]" value="${bayarSaatIni}">
                      </td>
                      <td class="text-right">
                        Rp ${formatRibuan(sisaPinjaman)}
                        <input type="hidden" name="SisaPinjaman[]" value="${sisaPinjaman}">
                      </td>
                    </tr>
                `;
            }

            // 5. Tampilkan Hasil
            $('#bodyCicilan').html(html);
            $('#tabelCicilan').show();
        }
    </script>
    <script>
      var save_method;
      var url;

      function approved(Nomor, Status, Label) 
      {
        Swal.fire({
          title: Label + '?',
          text: "Yakin ingin " + Label + " status ini?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, ' + Label,
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>pinjaman/pinjaman_approved',
              type: 'POST',
              data: {
                Id: Nomor,
                isApproved: Status
              },
              dataType: "json", // lowercase lebih aman
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                console.log("Response:", data);

                if (data.status === 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info'
                  );
                } else {
                  reload_table();
                }

                $("#loading").hide();
              },
              error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Something is wrong');
                $("#loading").hide();
              }
            });
          }
        });
      }

      function get_karyawan(el, defaultValue = null) 
      {
        console.log(defaultValue);
        $.ajax({
          url : "<?php echo base_url();?>users/get_karyawan_dept",
          method : "POST",
          data : {id: (typeof el === "object" ? el.value : el)},
          dataType : 'json',
          success: function(data){
            var html = '<option value="">-- Pilih --</option>';
            for (var i = 0; i < data.length; i++) {
              // cek jika defaultValue sama dengan SSN maka tambahkan selected
              let selected = (defaultValue && data[i].SSN == defaultValue) ? ' selected' : '';
              html += '<option value="'+ data[i].SSN +'"'+selected+'>'+ data[i].NAME.toUpperCase() +'</option>';
            }
            if (typeof el === "object") {
              // jika dipanggil dari select onchange
              $(el).closest('.form-group.row').find('select[name="EmployeeID"]').html(html);
            } else {
              // jika dipanggil dari ajax dengan UserID langsung
              $('#EmployeeID').html(html);
            }
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Data');
        $('#ShowDrawing').hide();
        $('#bodyCicilan').html('<tr><td class="text-center" colspan="3">Data tidak ditemukan</td></tr>');
      }

      function closeModal() {
        $('#formData')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#formData')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>pinjaman/pinjaman_edit",
          type: "POST",
          data: {
            Nomor: id
          },
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              $('[name="kode"]').val(data.HD.Nomor);
              $('[name="DeptID"]').val(data.HD.DEPTID);
              $('[name="JangkaWaktu"]').val(data.HD.JangkaWaktu);
              $('[name="JumlahPengajuan"]').val(data.HD.JumlahPengajuan);
              $('[name="Notes"]').val(data.HD.Noted);

              let DeptID  = data.HD.DEPTID;
              let UserID  = data.HD.EmployeeID;
              get_karyawan(DeptID, UserID);

              var details = data.DT;
              var html    = '';

              if (details && details.length > 0) {
                for (var i = 0; i < details.length; i++) {
                  var item          = details[i];
                  var nominalClean  = item.NominalAngsuran.toString().replace(/\./g, '');
                  var sisaClean     = item.SisaPinjaman.toString().replace(/\./g, '');

                  html += `
                    <tr>
                        <td class="text-center">
                            ${item.AngsuranKe}
                            <input type="hidden" name="AngsuranKe[]" value="${item.AngsuranKe}">
                        </td>
                        <td class="text-right">
                            Rp ${item.NominalAngsuran}  <input type="hidden" name="NominalAngsuran[]" value="${nominalClean}">
                        </td>
                        <td class="text-right">
                            Rp ${item.SisaPinjaman}     <input type="hidden" name="SisaPinjaman[]" value="${sisaClean}">
                        </td>
                    </tr>
                  `;
                }
              } else {
                html = '<tr><td class="text-center" colspan="3">Data tidak ditemukan</td></tr>';
              }

              // Masukkan ke dalam tabel
              $('#bodyCicilan').html(html);
              $('#tabelCicilan').show();

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

      //FUNCTION HAPUS
      function openModalDelete(id) {
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
              url: '<?php echo base_url(); ?>pinjaman/pinjaman_deleted/' + id,
              type: 'DELETE',
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
      function reload_table() {
        table.ajax.reload(null, false);
      }

      //VALIDATION AND ADD USER
      function save() 
      {
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>pinjaman/pinjaman_add";
        } else {
          url = "<?php echo base_url(); ?>pinjaman/pinjaman_update";
        }

        var form      = $('#formData')[0];
        var data_save = new FormData(form);

        // ajax adding data to database
        $.ajax({
          url: url,
          type: "POST",
          processData: false,
          contentType: false,
          cache: false,
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
              $('#bodyCicilan').html('<tr><td class="text-center" colspan="3">Data tidak ditemukan</td></tr>');
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
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A2',
              exportOptions: {
                stripHtml: true,
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]
              },
              customize: function (doc) {
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                function formatRibuan(num) {
                  if (num === null || num === undefined) return '0';
                  if (typeof num === 'number') {
                      return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }
                  let str = num.toString();
                  const cleaned = str.replace(/[^\d.,-]/g, '');
                  const normalized = cleaned.replace(',', '.');
                  const n = parseFloat(normalized);

                  return isNaN(n) ? str : n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                }

                doc.defaultStyle.fontSize = 10;
                doc.pageMargins           = [10, 40, 10, 60];
                doc.styles = {
                  subheader: {
                    fontSize: 12,
                    bold: true,
                    alignment: 'left'
                  },
                  tableHeader: {
                    bold: true,
                    fontSize: 10,
                    color: 'white',
                    fillColor: '#007bff',
                    alignment: 'center'
                  }
                };

                doc.content.unshift(
                  {
                    text: 'PT. MULTI ARTA SEKAWAN',
                    bold: true,
                    fontSize: 16,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'LAPORAN IJIN KELUAR PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                    bold: true,
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 11];
                    const body = mainTable.table.body;

                    for (let i = 1; i < body.length; i++) {
                        for (let j = 0; j < body[i].length; j++) {
                            if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                body[i][j].alignment = 'right';
                            }
                        }

                        // SUB TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'SUB TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#6c757d';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        // TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                }
                                break;
                            }
                        }
                    }

                    // Style baris terakhir
                    // const lastRowIndex = body.length - 1;
                    // for (let j = 0; j < body[lastRowIndex].length; j++) {
                    //     if (body[lastRowIndex][j].text !== undefined) {
                    //         body[lastRowIndex][j].fillColor = '#007bff';
                    //         body[lastRowIndex][j].color = '#fff';
                    //     }
                    // }

                    mainTable.layout = {
                        hLineWidth: () => 0.5,
                        vLineWidth: () => 0.5,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa',
                        paddingLeft: () => 4,
                        paddingRight: () => 4,
                        paddingTop: () => 2,
                        paddingBottom: () => 2,
                        fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                    };
                }

                // === Footer ===
                doc.footer = function (currentPage, pageCount) {
                    return {
                        columns: [
                            { text: 'Printed on: ' + new Date().toLocaleString(), alignment: 'left', margin: [10, 0, 0, 0] },
                            { text: 'PT MULTI ARTA SEKAWAN - CONFIDENTIAL', alignment: 'center' },
                            { text: 'Page ' + currentPage + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 10, 0] }
                        ],
                        fontSize: 8
                    };
                };
              },
              // Tambahkan opsi filename di sini
              filename: function() {
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN IJIN KELUAR PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            }
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
            left: 4
          },
          select: {
            style: 'single'
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>pinjaman/pinjaman_list",
            "type": "POST",
            "data": function(data) {
              let DeptShow = [];
              $('input[name="DeptShow"]:checked').each(function () {
                if ($(this).val()) {
                  DeptShow.push($(this).val());
                }
              });

              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.dept_id      = (DeptShow.length > 0) ? DeptShow : <?php echo $DEPTID; ?>;
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "NOMOR": "NOMOR" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px"},
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "80px" },
            { "NIP": "NIP" , "sClass": "text-center", "width": "50px" },
            { "NAME": "NAME" , "sClass": "text-left", "width": "150px" },
            { "JANGKA WAKTU": "JANGKA WAKTU" , "sClass": "text-center", "width": "50px" },
            { "JUMLAH PENGAJUAN": "JUMLAH PENGAJUAN" , "sClass": "text-right", "width": "50px" },
            { "TANGGAL DISETUJUI": "TANGGAL DISETUJUI" , "sClass": "text-center", "width": "80px" },
            { "NOTES": "NOTES" , "sClass": "text-left", "width": "250px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" },
            { "HRD APPROVED DATE": "HRD APPROVED DATE" , "sClass": "text-center", "width": "80px" },
            { "HRD APPROVED BY": "HRD APPROVED BY" , "sClass": "text-left", "width": "80px" }
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

        $("#DeptID, #EmployeeID, #JumlahPengajuan, #JangkaWaktu, #Notes").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Notes').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });
    </script>
    <script>
      $(function () {
        const DeptShow = $('#DeptShow').filterMultiSelect({
          placeholderText: "Pilih",
          filterText: "Filter",
          selectAllText: "SELECT ALL",
          labelText: "",
          selectionLimit: 0,
          caseSensitive: false,
          allowEnablingAndDisabling: true,
        });
      });
    </script>
  </body>
</html>