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
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select name="PilihanDO" id="PilihanDO" class="form-control">
                                      <option value="ALL" selected>-- ALL DO --</option>
                                      <option value="NON WIP">NON WIP</option>
                                      <option value="WIP">WIP</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <form id="frm-example" action="#" method="POST">
                                  <table id="example" class="table table-striped table-bordered table-hover" width="110%">
                                    <thead>
                                      <tr class="bg-primary text-white">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center" width="5%">
                                          <button type="button" id="ProsesButton" class="btn btn-warning" onclick="set_plant()" disabled="disabled" title="Tambah Plant dan Noted">SALES</button>
                                        </th>
                                        <th class="text-center" width="5%">
                                          PERSIAPAN<!-- <button type="button" id="ProsesButtonPersiapan" class="btn btn-warning" onclick="save_persiapan()" disabled="disabled" title="Tambah Persiapan">PERSIAPAN</button> -->
                                        </th>
                                        <th class="text-center" width="9%">TANGGAL DO</th>
                                        <th class="text-center" width="5%">JAM</th>
                                        <th class="text-center" width="9%">NO. DO</th>
                                        <th class="text-center" width="7%">PO. CUSTOMER</th>
                                        <th class="text-center" width="7%">PART ID</th>
                                        <th class="text-center" width="15%">PART NAME</th>
                                        <th class="text-center" width="15%">NAMA CUSTOMER</th>
                                        <th class="text-center" width="5%">
                                          <button type="button" id="ProsesButtonJamKirim" class="btn btn-warning" onclick="set_jam_kirim()" disabled="disabled" title="Tambah Jam Kirim">JAM KIRIM</button> 
                                        </th>
                                        <th class="text-center" width="5%">EKSPEDISI</th>
                                        <th class="text-center" width="5%">JAM</th>
                                        <th class="text-center" width="5%">DRIVER</th>
                                        <th class="text-center" width="5%">NO. POLISI</th>
                                        <th class="text-center" width="5%">TANGGAL</th>
                                        <th class="text-center" width="5%">QUANTITY</th>
                                        <th class="text-center" width="5%">UNIT ID</th>
                                        <th class="text-center" width="6%">JLH. BOX</th>
                                        <th class="text-center" width="6%">PLANT</th>
                                        <th class="text-center" width="6%">NOTED</th>
                                      </tr>
                                    </thead>
                                    <tbody></tbody>
                                  </table>
                                </form>
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

    <div class="modal fade" id="modalPilihPlant" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Tambah Keterangan Tambahan</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <form id="formSimpanPlant">
                <table class="table table-striped table-bordered table-hover" width="180%">
                  <thead>
                    <tr class="bg-primary">
                      <th class="text-center" style="min-width: 120px;">Plant</th>
                      <th class="text-center" style="min-width: 250px;">Noted</th>
                      <th class="text-center" style="min-width: 140px;">Tanggal</th>
                      <th class="text-center" style="min-width: 200px;">No. DO</th>
                      <th class="text-center" style="min-width: 250px;">PO Customer</th>
                      <th class="text-center" style="min-width: 200px;">Part ID</th>
                      <th class="text-center" style="min-width: 400px;">Part Name</th>
                    </tr>
                  </thead>
                  <tbody id="listDataPengiriman"></tbody>
                </table>
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="btnSave" type="button" onclick="save_plant();" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalSetJamKirim" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Tambah Keterangan Jam Kirim</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <form id="formSimpanJamKirim">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Ekspedisi</label>
                  <div class="col-sm-4">
                    <select name="Ekspedisi" id="Ekspedisi" class="form-control">
                      <option value="" selected>-- Pilih --</option>
                      <option value="Y">YES</option>
                      <option value="N">NO</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Tanggal Kirim</label>
                  <div class="col-sm-4">
                    <input type="date" name="TanggalKirim" id="TanggalKirim" class="form-control">
                    <span class="help-block"></span>
                  </div>
                  <label class="col-sm-2 col-form-label">Jam Kirim</label>
                  <div class="col-sm-4">
                    <input type="time" name="JamKirim" id="JamKirim" class="form-control">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row mb-4">
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
                    <select id="Mobil" name="Mobil" class="form-control">
                      <option selected="selected" disabled="disabled">-- Pilih --</option>
                      <option value="A 8552 ZT">A 8552 ZT</option>
                      <option value="A 9372 ZA">A 9372 ZA</option>
                      <option value="A 9403 ZX">A 9403 ZX</option>
                      <option value="A 1193 YE">A 1193 YE</option>
                      <option value="A 8762 YX">A 8762 YX</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
                <table class="table table-striped table-bordered table-hover" width="180%">
                  <thead>
                    <tr class="bg-primary">
                      <th class="text-center" style="min-width: 140px;">Tanggal</th>
                      <th class="text-center" style="min-width: 200px;">No. DO</th>
                      <th class="text-center" style="min-width: 250px;">PO Customer</th>
                      <th class="text-center" style="min-width: 200px;">Part ID</th>
                      <th class="text-center" style="min-width: 400px;">Part Name</th>
                    </tr>
                  </thead>
                  <tbody id="listDataJamKirim"></tbody>
                </table>
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="btnSaveJamKirim" type="button" onclick="save_jam_kirim();" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div id="loading" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript">
      $(function() {
        $('input[name="tanggal"]').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 2020,
          maxYear: parseInt(moment().format('YYYY'), 10),
          maxDate: moment().endOf('month'),
          startDate: moment(),
          locale: {
            format: 'YYYY-MM-DD'
          }
        }, function(start, end, label) {
          var years = moment().diff(start, 'years');
        });
      });
    </script>
    <script>
      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION UPDATE TERKIRIM ATAU TIDAK
      function update_kirim(Id, Status)
      {
        console.log(Id);
        console.log(Status);
        $.ajax({
          url: "<?php echo base_url(); ?>pengiriman/update_status_kirim",
          dataType: 'JSON',
          data: {
            IdKirim: Id,
            StatusKirim: Status
          },
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();
            reload_table();
          }, 
          error: function() {
            alert('Oops error ketika proses data group');
          }
        });
      }

      function set_plant() {
        let PlantArray = [];
        
        // 1. Ambil semua value dari checkbox yang dicentang
        $("input[name='Plant[]']:checked").each(function(){
            PlantArray.push($(this).val());
        });

        // 2. Validasi jika kosong
        if (PlantArray.length === 0) {
            alert("Pilih setidaknya satu data!");
            return;
        }

        // 3. Bersihkan isi tbody
        $('#listDataPengiriman').html('');
        
        let html = '';
        let last_customer_name = ''; // Variabel untuk melacak perubahan nama customer

        // 4. Looping Array data
        PlantArray.forEach((val, index) => {
            
            let cleanVal = val.replace(/'/g, "");
            let data = cleanVal.split(", "); 
            let current_customer_name = data[0].trim();

            // --- LOGIKA GROUPING CUSTOMER (HEADER ROW) ---
            // Jika nama customer baris ini beda dengan sebelumnya, buat baris Header Customer
            if (current_customer_name !== last_customer_name) {
                html += '<tr class="bg-light">';
                // Colspan 7 agar header membentang sepanjang tabel
                html += '  <td colspan="7" class="font-weight-bold text-uppercase text-left" style="padding: 10px;">';
                html +=      current_customer_name; 
                html += '  </td>';
                html += '</tr>';
                
                // Update penampung nama terakhir
                last_customer_name = current_customer_name;
            }
            // ----------------------------------------------

            html += '<tr>';
            
            // --- KOLOM 1: PLANT ---
            html += '<td class="text-center" width="10%">';
            if (current_customer_name === 'PT. KENCANA GEMILANG') {
                html += '  <select name="plant_tujuan[]" class="form-control form-control-sm" required>';
                html += '    <option value="">- Pilih -</option>';
                html += '    <option value="KG 1">KG 1</option>';
                html += '    <option value="KG 2">KG 2</option>';
                html += '    <option value="KG 3">KG 3</option>';
                html += '  </select>';
            } else {
                // Bukan KG: Strip visual + Input hidden agar index array tetap sinkron
                html += '  - ';
                html += '  <input type="hidden" name="plant_tujuan[]" value="">'; 
            }
            html += '</td>';

            // --- KOLOM 2: NOTED ---
            html += '<td width="20%">';
            html += '  <input type="text" class="form-control form-control-sm text-capitalize" name="noted[]" placeholder="Catatan...">';
            html += '</td>';

            // --- KOLOM 3 - 7 (Data Readonly) ---
            html += '<td><input type="text" class="form-control form-control-sm" name="tanggal_do[]" value="'+data[1]+'" readonly></td>';
            html += '<td><input type="text" class="form-control form-control-sm" name="no_do[]" value="'+data[2]+'" readonly></td>';
            html += '<td><input type="text" class="form-control form-control-sm" name="po_cust[]" value="'+data[3]+'" readonly></td>';
            html += '<td><input type="text" class="form-control form-control-sm" name="part_id[]" value="'+data[4]+'" readonly></td>';
            html += '<td><input type="text" class="form-control form-control-sm" name="part_name[]" value="'+data[5]+'" readonly></td>';

            html += '</tr>';
        });

        // 5. Render & Show
        $('#listDataPengiriman').html(html);
        $('#modalPilihPlant').modal('show');
      }

      function save_plant() {
        let formData = $('#formSimpanPlant').serialize();

        $.ajax({
            url: "<?php echo base_url(); ?>pengiriman/save_plant", // Ganti dengan URL Controller Anda
            type: "POST",
            data: formData, // Data array otomatis terkirim
            dataType: "JSON",
            beforeSend: function() {
                $('#btnSave').attr('disabled', true).text('Menyimpan...');
            },
            success: function(response) {
              if(response.status == 'success') {
                $('#modalPilihPlant').modal('hide');
                reload_table();
              } else if (response.status == 'forbidden') {
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } 
              
              else {
                  alert('Gagal menyimpan data: ' + response.message);
              }
                $('#btnSave').attr('disabled', false).text('Simpan');
            },
            error: function (jqXHR, textStatus, errorThrown) {
              alert('Terjadi kesalahan server');
              $('#btnSave').attr('disabled', false).text('Simpan');
            }
        });
      }

      function save_jam_kirim() {
        $('.form-group, .col-sm-4').removeClass('has-error');
        $('.help-block').empty();

        var formData = $('#formSimpanJamKirim').serializeArray(); 

        var ekspedisi = $('#Ekspedisi').val(); 
        var namaSupir = '';

        if (ekspedisi === 'N') {
          namaSupir = $('#Supir option:selected').text().trim();
          
          if ($('#Supir').val() === '') {
            namaSupir = '';
          } 
        } else {
          namaSupir = $('#Supir').val(); 
        }

        formData.push({ name: "SupirNama", value: namaSupir });

        $.ajax({
          url: "<?php echo base_url(); ?>pengiriman/save_jam_kirim",
          type: "POST",
          data: formData,
          dataType: "JSON",
          beforeSend: function() { $("#loading").show(); },
          success: function(data) {
              $("#loading").hide();
              
              if (data.status === true || data.status === 'success') {
                $('#modalSetJamKirim').modal('hide');
                reload_table();
                $('#formSimpanJamKirim')[0].reset();
              } else {
                // Loop Error
                if (data.inputerror) {
                  for (var i = 0; i < data.inputerror.length; i++) {
                    // Sekarang ini PASTI KETEMU karena namanya sudah sama ('Supir')
                    var element = $('[name="' + data.inputerror[i] + '"]');
                    element.parent().addClass('has-error');
                    element.next('.help-block').text(data.error_string[i]);
                  }
                }
              }
          },
          error: function(jqXHR) {
            $("#loading").hide();
            alert('Error handling data');
          }
        });
      }

      function save_persiapan(CustomerName, DODate, DONumber, PONumber, PartID, PartName, Nilai) 
      {
        //console.log(CustomerName, DODate, DONumber, PONumber, PartID, PartName, Nilai);

        var formData = {
          customer_name : CustomerName,
          do_date       : DODate,
          do_number     : DONumber,
          po_number     : PONumber,
          part_id       : PartID,
          part_name     : PartName,
          persiapan     : Nilai
        };

        $.ajax({
          url: "<?php echo base_url(); ?>pengiriman/save_persiapan",
          type: "POST",
          data: formData,
          dataType: "JSON",
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(response) {
            if(response.status == 'success') {
              reload_table(); 
            } else if (response.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              alert('Gagal menyimpan data: ' + response.message);
            }
            $("#loading").hide();
          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert('Terjadi kesalahan server');
            $("#loading").hide();
          }
        });
      }

      function set_jam_kirim() {
        let JamKirimArray = [];

        // 1. Ambil semua value dari checkbox JamKirim yang dicentang
        $("input[name='JamKirim[]']:checked").each(function() {
          JamKirimArray.push($(this).val());
        });

        // 2. Validasi jika kosong
        if (JamKirimArray.length === 0) {
          alert("Pilih setidaknya satu data untuk diatur jam kirimnya!");

          return;
        }

        // 3. Bersihkan isi tbody target
        // Pastikan Anda sudah membuat <tbody> dengan ID ini di HTML modal baru
        $('#listDataJamKirim').html('');

        let html                = '';
        let last_customer_name  = ''; // Variabel untuk melacak perubahan nama customer

        // 4. Looping Array data
        JamKirimArray.forEach((val, index) => {
          // Cleaning data sesuai format value dari Controller
          // Format: 'Customer', 'Date', 'NoDO', 'PO', 'PartID', 'PartName'
          let cleanVal              = val.replace(/'/g, ""); // Hapus tanda kutip
          let data                  = cleanVal.split(", "); // Split berdasarkan koma dan spasi
          let current_customer_name = data[0].trim();

          // --- LOGIKA GROUPING CUSTOMER (HEADER ROW) ---
          // Sama persis dengan set_plant: Jika nama beda, buat header baru
          if (current_customer_name !== last_customer_name) {
            html += '<tr class="bg-light">';
            html += '  <td colspan="5" class="font-weight-bold text-uppercase text-left" style="padding: 10px; background-color: #e9ecef;">';
            html +=      current_customer_name;
            html += '  </td>';
            html += '</tr>';

            last_customer_name = current_customer_name;
          }
          // ----------------------------------------------

          html += '<tr>';
          // Tanggal DO
          html += '<td><input type="text" class="form-control form-control-sm" name="tanggal_do[]" value="' + data[1] + '" readonly></td>';        
          // No DO
          html += '<td><input type="text" class="form-control form-control-sm" name="no_do[]" value="' + data[2] + '" readonly></td>';      
          // PO Customer
          html += '<td><input type="text" class="form-control form-control-sm" name="po_cust[]" value="' + data[3] + '" readonly></td>';          
          // Part ID
          html += '<td><input type="text" class="form-control form-control-sm" name="part_id[]" value="' + data[4] + '" readonly></td>';          
          // Part Name
          html += '<td><input type="text" class="form-control form-control-sm" name="part_name[]" value="' + data[5] + '" readonly></td>';
          html += '</tr>';
        });

        // 5. Render & Show Modal
        // Pastikan ID Modal sesuai dengan yang ada di HTML Anda
        $('#listDataJamKirim').html(html);
        $('#modalSetJamKirim').modal('show');
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      };

      function updateStatusButtonJamKirim() {
          // Hitung berapa checkbox JamKirim yang tercentang
          var totalChecked = $('input[name="JamKirim[]"]:checked').length;

          // Logic enable/disable tombol
          if (totalChecked > 0) {
              $('#ProsesButtonJamKirim').prop("disabled", false);
          } else {
              $('#ProsesButtonJamKirim').prop("disabled", true);
          }
      }

      function toggleGroup(groupId) {
        var isChecked = $('#master_grp_' + groupId).is(':checked');
        $('.child_grp_' + groupId).prop('checked', isChecked);
        
        updateStatusButtonJamKirim();
      }

      function checkChild(groupId) {
        var totalChild    = $('.child_grp_' + groupId).length;
        var totalChecked  = $('.child_grp_' + groupId + ':checked').length;
        if (totalChild === totalChecked) {
          $('#master_grp_' + groupId).prop('checked', true);
        } else {
          $('#master_grp_' + groupId).prop('checked', false);
        }

        updateStatusButtonJamKirim();
      }

      $(document).ready(function() {
        $("#loading").hide();

        //CHECKBOX ENABLE AND DISBALE
        $('body').on('change', 'input[name="Plant[]"]', function() {
          var totalChecked = $('input[name="Plant[]"]:checked').length;
          if (totalChecked > 0) {
            $('#ProsesButton').prop("disabled", false);
          } else {
            $('#ProsesButton').prop("disabled", true);
          }
        });

        $('body').on('change', 'input[name="JamKirim[]"]', function() {
            // Panggil fungsi yang sudah kita buat tadi
            updateStatusButtonJamKirim();
        });
        //CHECKBOX ENABLE AND DISBALE

        // 1. Event Listener untuk Hapus Error (Gunakan kode baru ini)
        $(document).on('change input', '#TanggalKirim, #JamKirim, #Supir, #Mobil, #Ekspedisi', function() {
          if ($(this).val().trim() !== '') {
            $(this).parent().removeClass('has-error');
            $(this).next('.help-block').text('');
          }
        });

        // 1. SIMPAN DROPDOWN ASLI KE VARIABEL (Backup)
        // Kita clone agar saat dihapus/diganti, kita masih punya cadangannya
        var $backupSelectSupir = $('#Supir').clone();
        var $backupSelectMobil = $('#Mobil').clone();

        // 2. Fungsi Logika Utama
        function cekStatusEkspedisi() {
          var statusEkspedisi = $('#Ekspedisi').val();
          var elJamKirim      = $('#JamKirim');
          
          // --- LOGIKA JAM KIRIM (Sesuai request sebelumnya) ---
          var parentDivJam    = elJamKirim.parent(); 
          var errorSpanJam    = elJamKirim.next('.help-block');

          if (statusEkspedisi === 'Y') {
            elJamKirim.prop('readonly', true).val(''); 
            parentDivJam.removeClass('has-error');
            errorSpanJam.text('');

            if ($('#Supir').is('select')) {
              var inputSupir = $('<input>').attr({
                type: 'text',
                name: 'Supir',
                id: 'Supir',
                class: 'form-control text-uppercase',
                placeholder: 'Isi Nama Supir Ekspedisi...'
              });
              $('#Supir').replaceWith(inputSupir);
            }

            if ($('#Mobil').is('select')) {
              var inputMobil = $('<input>').attr({
                type: 'text',
                name: 'Mobil',
                id: 'Mobil',
                class: 'form-control text-uppercase',
                placeholder: 'Isi Nopol / Jenis Kendaraan...'
              });
              $('#Mobil').replaceWith(inputMobil);
            }
          } else {
            elJamKirim.prop('readonly', false);
            if ($('#Supir').is('input')) {
              $('#Supir').replaceWith($backupSelectSupir.clone());
            }

            if ($('#Mobil').is('input')) {
              $('#Mobil').replaceWith($backupSelectMobil.clone());
            }
          }
        }

        // 2. Panggil fungsi saat dropdown berubah
        $('#Ekspedisi').change(function() {
            cekStatusEkspedisi();
        });

        // 3. Panggil fungsi saat halaman pertama kali diload 
        // (Penting untuk form Edit/Update)
        cekStatusEkspedisi();

        table = $('#example').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export All',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A3',
              exportOptions: {
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
              },
              customize: function (doc) {
                const rawTanggal = $('#tanggal').val();
                let tanggal      = rawTanggal;

                if (rawTanggal) {
                  const dateObj = new Date(rawTanggal);
                  const options = { day: 'numeric', month: 'long', year: 'numeric' };
                  tanggal       = dateObj.toLocaleDateString('id-ID', options);
                }

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

                doc.defaultStyle.fontSize = 8;
                doc.pageMargins           = [10, 40, 10, 60];
                doc.styles = {
                  subheader: {
                    fontSize: 12,
                    bold: true,
                    alignment: 'left'
                  },
                  tableHeader: {
                    bold: true,
                    fontSize: 8,
                    color: 'white',
                    fillColor: '#007bff',
                    alignment: 'center'
                  }
                };

                doc.content.unshift(
                  {
                    text: 'PT. MULTI ARTA SEKAWAN',
                    bold: true,
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'PLANNING KIRIM HARIAN ',
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'TANGGAL : ' + tanggal.toUpperCase(),
                    bold: true,
                    fontSize: 10,
                    style: 'subheader',
                    alignment: 'left',
                    margin: [0, 0, 0, 10]
                  }
                );


                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    mainTable.fontSize    = 7.5;
                    const alignRightCols  = [0, 13, 15];
                    const body            = mainTable.table.body;

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

                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#ff5370';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'WAKTU'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#2ed8b6';
                                    body[i][k].color = '#fff';
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
                const rawTanggal = $('#tanggal').val();
                let tanggal      = rawTanggal;

                if (rawTanggal) {
                  const dateObj = new Date(rawTanggal);
                  const options = { day: 'numeric', month: 'long', year: 'numeric' };
                  tanggal       = dateObj.toLocaleDateString('id-ID', options);
                }

                return 'Planning Kirim Harian Tanggal ' + tanggal.toUpperCase();
              }
            },
            {
              extend: 'pdfHtml5',
              text: 'Export for Sales',
              title: '',
              className: 'btn btn-warning',
              orientation: 'landscape',
              pageSize: 'A4',
              exportOptions: {
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 16, 17, 18, 19, 20]
              },
              customize: function (doc) {
                const rawTanggal = $('#tanggal').val();
                let tanggal      = rawTanggal;

                if (rawTanggal) {
                  const dateObj = new Date(rawTanggal);
                  const options = { day: 'numeric', month: 'long', year: 'numeric' };
                  tanggal       = dateObj.toLocaleDateString('id-ID', options);
                }

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

                doc.defaultStyle.fontSize = 8;
                doc.pageMargins           = [10, 40, 10, 60];
                doc.styles = {
                  subheader: {
                    fontSize: 9,
                    bold: true,
                    alignment: 'left'
                  },
                  tableHeader: {
                    bold: true,
                    fontSize: 8,
                    color: 'white',
                    fillColor: '#007bff',
                    alignment: 'center'
                  }
                };

                doc.content.unshift(
                  {
                    text: 'PT. MULTI ARTA SEKAWAN',
                    bold: true,
                    fontSize: 10,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'PLANNING KIRIM HARIAN ',
                    bold: true,
                    fontSize: 10,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'TANGGAL : ' + tanggal.toUpperCase(),
                    bold: true,
                    fontSize: 10,
                    style: 'subheader',
                    alignment: 'left',
                    margin: [0, 0, 0, 10]
                  }
                );


                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    mainTable.fontSize    = 6.2;
                    const alignRightCols  = [0, 8, 10];
                    const body            = mainTable.table.body;

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

                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#ff5370';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'WAKTU'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#2ed8b6';
                                    body[i][k].color = '#fff';
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
                const rawTanggal = $('#tanggal').val();
                let tanggal      = rawTanggal;

                if (rawTanggal) {
                  const dateObj = new Date(rawTanggal);
                  const options = { day: 'numeric', month: 'long', year: 'numeric' };
                  tanggal       = dateObj.toLocaleDateString('id-ID', options);
                }

                return 'Planning Kirim Harian Sales Tanggal ' + tanggal.toUpperCase();
              }
            }
          ],
          fixedColumns: {
            left: 3
          },
          select: {
            style: 'multi'
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
          "order": [], // Matikan default sorting agar urutan SQL terjaga
          "ajax": {
            "url": "<?php echo base_url(); ?>pengiriman/pengiriman_harian_data",
            "type": "POST",
            "data": function(data) {
              data.tanggal    = $('#tanggal').val();
              data.PilihanDO  = $('#PilihanDO').val();
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right"},
            { "SALES": "SALES" , "sClass": "text-center"},
            { "PERSIAPAN": "PERSIAPAN" , "sClass": "text-center"},
            { "TANGGAL DO": "TANGGAL DO" , "sClass": "text-center"},
            { "JAM": "JAM" , "sClass": "text-center"},
            { "NO. DO": "NO. DO" , "sClass": "text-left"},
            { "PO. CUSTOMER": "PO. CUSTOMER" , "sClass": "text-left"},
            { "PART ID": "PART ID" , "sClass": "text-left"},
            { "PART NAME": "PART NAME" , "sClass": "text-left"},
            { "NAMA CUSTOMER": "NAMA CUSTOMER" , "sClass": "text-left"},
            { "#": "#" , "sClass": "text-center"},
            { "EKSPEDISI": "EKSPEDISI" , "sClass": "text-center"},
            { "JAM": "JAM" , "sClass": "text-center"},
            { "DRIVER": "DRIVER" , "sClass": "text-center"},
            { "NO. POLISI": "NO. POLISI" , "sClass": "text-center"},
            { "TANGGAL": "TANGGAL" , "sClass": "text-center"},
            { "QUANTITY": "QUANTITY" , "sClass": "text-right"},
            { "UNIT ID": "UNIT ID" , "sClass": "text-center"},
            { "JLH. BOX": "JLH. BOX" , "sClass": "text-right"},
            { "PLANT": "PLANT" , "sClass": "text-start"},
            { "NOTED": "NOTED" , "sClass": "text-start"}
          ],
          "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            var intVal = function(i) {
              return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            var TotalColly = api.column(9).data().reduce(function(a, b) { // Pastikan index kolom Qty benar (9 atau 10)
              return intVal(a) + intVal(b);
            }, 0);

            // Sesuaikan index kolom footer sesuai kebutuhan
            // $(api.column(9).footer()).html(formatNumber(TotalColly)); 
          },
          
          // 1. MEWARNAI BARIS (SCROLLING PART)
          'createdRow': function(row, data, dataIndex) {
            if (data[9] !== '' && data[9] !== null) {
              $(row).addClass('bg-success text-white font-weight-bold');
            }

            if (data[17] === 'SUB TOTAL') {
              $(row).addClass('bg-secondary text-white font-weight-bold');
            }

            if (data[4] === 'WAKTU') {
              $(row).addClass('bg-success text-white font-weight-bold');
            }

            if (data[17] === 'TOTAL') {
              $(row).addClass('bg-danger text-white font-weight-bold');
            }
          },

          // 2. KONFIGURASI KOLOM (FIXED PART & RENDER)
          'columnDefs': [
            {
              'targets': [0, 1, 2],
              'orderable': false,
              'createdCell': function (td, cellData, rowData, row, col) {
                // Mewarnai baris Header jika ada Nama Customer
                if (rowData[9] !== '' && rowData[9] !== null) {
                  $(td).addClass('bg-success text-white font-weight-bold');
                }

                if (rowData[17] === 'SUB TOTAL') {
                  $(td).addClass('bg-secondary text-white font-weight-bold');
                }

                if (rowData[4] === 'WAKTU') {
                  $(td).addClass('bg-success text-white font-weight-bold');
                }

                if (rowData[17] === 'TOTAL') {
                  $(td).addClass('bg-danger text-white font-weight-bold');
                }
              }
            },
          ],
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        };
      });
    </script>
  </body>
</html>