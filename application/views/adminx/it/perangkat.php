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
    <style>
      .pointer {
        cursor: pointer;
      }

      ul.timeline {
        list-style-type: none;
        position: relative;
      }

      ul.timeline:before {
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
      }

      ul.timeline > li {
        margin: 20px 0;
        padding-left: 60px;
      }

      ul.timeline > li:before {
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
      }

      .history {
        height: 200px;      
        overflow-y: auto;    
        overflow-x: hidden;  
        border: 1px solid #ddd;
        padding-right: 10px;
      }
    </style>
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
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select name="JenisShow" id="JenisShow" class="form-control">
                                    <option value="All" selected>ALL DATA</option>
                                    <option value="" disabled>-- Pilih --</option>
                                    <?php foreach ($jenis_perangkat as $value): ?>
                                      <option value="<?= $value->Id; ?>">
                                        <?= htmlspecialchars($value->Nama, ENT_QUOTES, 'UTF-8'); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control">
                                    <option value="All" selected>ALL DEPT</option>
                                    <option value="" disabled>-- Pilih --</option>
                                    <?php foreach ($department_att as $value): ?>
                                      <option value="<?= $value->DEPTID; ?>">
                                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-3">
                                  <input type="hidden" name="StartDateShow" id="StartDateShow">
                                  <input type="hidden" name="EndDateShow" id="EndDateShow">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center">NO</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">NOMOR</th>
                                      <th class="text-center">NAMA PERANGKAT</th>
                                      <th class="text-center">JENIS PERANGKAT</th>
                                      <th class="text-center">KATEGORI</th>
                                      <th class="text-center">MERK</th>
                                      <th class="text-center">TIPE</th>
                                      <th class="text-center">NO SERI</th>
                                      <th class="text-center">NOMOR PO</th>
                                      <th class="text-center">TGL. PEMBELIAN</th>
                                      <th class="text-center">DEPARTEMEN</th>
                                      <th class="text-center">USER/ PIC</th>
                                      <th class="text-center">AREA PEMASANGAN</th>
                                      <th class="text-center">CREATE DATE</th>
                                      <th class="text-center">CREATE BY</th>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Perangkat</label>
                <div class="col-sm-10 form-error">
                  <input type="text" name="Nama" id="Nama" placeholder="Contoh: Printer, Laptop" class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jenis Perangkat</label>
                <div class="col-sm-4 form-error">
                  <select name="JenisID" id="JenisID" class="form-control" onchange="get_jenis_perangkat(this);">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($jenis_perangkat as $value): ?>
                      <option value="<?= $value->Id; ?>">
                        <?= htmlspecialchars($value->Nama, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error">
                  <select name="Status" id="Status" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="AKTIF">AKTIF</option>
                    <option value="TIDAK">TIDAK</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Merk</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="Merk" id="Merk" class="form-control text-capitalize" placeholder="Merk perangkat">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tipe</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="Tipe" id="Tipe" class="form-control text-capitalize" placeholder="Tipe perangkat">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">No. Seri</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="NoSeri" id="NoSeri" class="form-control" placeholder="Nomor Seri perangkat">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4 form-error">
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
                <label class="col-sm-2 col-form-label">User</label>
                <div class="col-sm-4 form-error">
                  <select name="UserID" id="UserID" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Area Pemasangan</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="AreaPemasangan" id="AreaPemasangan" class="form-control text-capitalize" maxlength="75" placeholder="Area pemasangan perangkat" disabled>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-2 mt-2 border-top border-bottom">
                <label class="col-sm-7 col-form-label mt-2 mb-2">NOMOR PO PURCHASING</label>
                <label class="col-sm-2 col-form-label float-right mt-2 mb-2">Periode PO</label>
                <div class="col-sm-3 float-right mt-2 mb-2">
                  <input type="month" name="Periode" id="Periode" value="<?php echo date('Y-m'); ?>" class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">No. PO (Jika tahu)</label>
                <div class="col-sm-4 form-error">
                  <div id="NoBuktiListSelect" class="col-sm-10 form-error">
                  <select name="NoBuktiList" id="NoBuktiList" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal Pembelian</label>
                <div class="col-sm-4 form-error">
                  <input type="date" name="TanggalPembelian" id="TanggalPembelian" class="form-control">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10 form-error">
                  <textarea name="Keterangan" id="Keterangan" class="form-control" rows="5"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#StartDateShow').val(start.format('YYYY-MM-DD'));
          $('#EndDateShow').val(end.format('YYYY-MM-DD'));
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
      var save_method;
      var url;

      function get_jenis_perangkat(el) 
      {
        let areaInput   = document.getElementById('AreaPemasangan');
        let text        = el.options[el.selectedIndex].text;

        // Target 2 textbox
        let deptWrapper   = document.getElementById('DeptID').closest('.form-error');
        let userWrapper   = document.getElementById('UserID').closest('.form-error');
        let pasangWrapper = document.getElementById('AreaPemasangan').closest('.form-error');

        let deptHelp      = deptWrapper.querySelector('.help-block');
        let userHelp      = userWrapper.querySelector('.help-block');
        let pasangHelp    = pasangWrapper.querySelector('.help-block');

        if (text === "Access Point" || text === "Router" || text === "CCTV" || 
            text === "DVR/ NVR" || text === "Door access lock/ Access control") {
          $('#DeptID').val('');
          $('#UserID').val('');
          // Hilangkan error khusus Departemen & User
          deptWrapper.classList.remove('has-error');
          userWrapper.classList.remove('has-error');

          deptHelp.textContent = "";
          userHelp.textContent = "";

          areaInput.removeAttribute("disabled");
          // ubah placeholder sesuai jenis perangkat
          if (text === "Access Point") {
            areaInput.placeholder = "Masukkan area pemasangan Access Point";
          } else if (text === "Router") {
            areaInput.placeholder = "Masukkan area pemasangan Router";
          } else if (text === "CCTV") {
            areaInput.placeholder = "Masukkan lokasi pemasangan CCTV";
          } else if (text === "DVR/ NVR") {
            areaInput.placeholder = "Masukkan lokasi pemasangan DVR/ NVR";
          } else if (text === "Door access lock/ Access control") {
            areaInput.placeholder = "Masukkan lokasi pemasangan Door access lock/ Access control";
          }
        } else {
          areaInput.setAttribute("disabled", true);
          pasangWrapper.classList.remove('has-error');
          //pasangHelp.textContent = "";
        }
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
              html += '<option value="'+ data[i].SSN +'"'+selected+'>'+ data[i].NAME +'</option>';
            }
            if (typeof el === "object") {
              // jika dipanggil dari select onchange
              $(el).closest('.form-group.row').find('select[name="UserID"]').html(html);
            } else {
              // jika dipanggil dari ajax dengan UserID langsung
              $('#UserID').html(html);
            }
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      function reset_all() 
      {
        $('#modalForm').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Perangkat');
      }

      //FUNCTION OPEN MODAL
      function openModal() 
      {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modalForm').modal('show');
        $('.modal-title').text('Tambah Perangkat');
        $('#NoBuktiList').val(null).trigger('change');
      }

      //FUNCTION EDIT
      function edit(id) 
      {
        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>perangkat/perangkat_edit/" + id,
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            console.log(data);
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              // 📝 jika opsi PartID sudah ada di select2
              if ($('[name="NoBuktiList"] option[value="' + data.NoBukti + '"]').length > 0) {
                $('[name="NoBuktiList"]').val(data.NoBukti).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.NoBukti, data.NoBukti, true, true);
                $('[name="NoBuktiList"]').append(newOption).trigger('change');
              }

              $('[name="kode"]').val(data.Id);
              $('[name="Nama"]').val(data.Nama);
              $('[name="JenisID"]').val(data.JenisID);
              $('[name="Status"]').val(data.Status);
              $('[name="Merk"]').val(data.Merk);
              $('[name="Tipe"]').val(data.Tipe);
              $('[name="NoSeri"]').val(data.NoSeri);
              $('[name="DeptID"]').val(data.DeptID);
              $('[name="AreaPemasangan"]').val(data.AreaPasang);
              $('[name="TanggalPembelian"]').val(data.TanggalPembelian);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Perangkat');
              $('#btnSave').text('Update');

              let AreaPasang  = data.AreaPasang;
              let Nama        = data.NamaJP;
              let text        = $('#JenisID option:selected').text();
              let areaInput   = $('#AreaPemasangan');
              console.log(Nama);
              if (Nama === "Access Point" || Nama === "Router" || Nama === "CCTV" || 
              Nama === "DVR/ NVR" || Nama === "Door access lock/ Access control") {
                // Reset Dept & User
                $('#DeptID').val('');
                $('#UserID').val('');

                // Hilangkan error khusus Departemen & User
                $('#DeptID').closest('.form-error').removeClass('has-error').find('.help-block').text('');
                $('#UserID').closest('.form-error').removeClass('has-error').find('.help-block').text('');

                // Aktifkan AreaPemasangan
                areaInput.prop('disabled', false);

                // Ubah placeholder sesuai jenis perangkat
                if (Nama === "Access Point") {
                  areaInput.attr('placeholder', "Masukkan area pemasangan Access Point");
                } else if (Nama === "Router") {
                  areaInput.attr('placeholder', "Masukkan area pemasangan Router");
                } else if (Nama === "CCTV") {
                  areaInput.attr('placeholder', "Masukkan lokasi pemasangan CCTV");
                }

                // Hilangkan error Area Pemasangan
                areaInput.closest('.form-error').removeClass('has-error').find('.help-block').text('');
              } else {
                // Disable dan clear AreaPemasangan
                areaInput.prop('disabled', true).val('').attr('placeholder', '');
                areaInput.closest('.form-error').removeClass('has-error').find('.help-block').text('');
              }

              let DeptID = data.DeptID;
              let UserID = data.UserID;
              get_karyawan(DeptID, UserID);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION HAPUS
      function openModalDelete(id) 
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
              url: '<?php echo base_url(); ?>perangkat/perangkat_deleted/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
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
              }
            });
          }
        })
      }

      //FUNCTION SAVE AND UPDATE
      function save() 
      {
        var form_data = $('#RegisterValidation').serializeArray();

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>perangkat/perangkat_add";
        } else {
          url = "<?php echo base_url(); ?>perangkat/perangkat_update";
        }

        $.ajax({
          url: url,
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modalForm').modal('hide');
              $('#RegisterValidation')[0].reset();
              reload_table();
              reset_all();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else if (data.status == 'forbidden') {
              $("#loading").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else {
              $("#loading").hide();
              for (var i = 0; i < data.inputerror.length; i++) {
                var inputName = data.inputerror[i];
                var errorMsg  = data.error_string[i];

                var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                if (arrayMatch) {
                    var arrayName = arrayMatch[1];
                    var arrayIndex = parseInt(arrayMatch[2]);
                    var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                    inputElem.closest('.form-error').addClass('has-error');

                    if (inputElem.hasClass('select2-hidden-accessible')) {
                        var select2Container = inputElem.next('.select2'); // ambil wrapper select2
                        if (select2Container.next('.help-block').length === 0) {
                            select2Container.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    } else {
                        if (inputElem.next('.help-block').length === 0) {
                            inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    }
                } else {
                    var inputElem = $('[name="' + inputName + '"]');
                    inputElem.closest('.form-error').addClass('has-error');

                    if (inputElem.hasClass('select2-hidden-accessible')) {
                        var select2Container = inputElem.next('.select2');
                        if (select2Container.next('.help-block').length === 0) {
                            select2Container.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    } else {
                        if (inputElem.next('.help-block').length === 0) {
                            inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    }
                }
              }
            }

            if(save_method == 'add') {
              $("#btnSave").text('Save');
            } else {
              $("#btnSave").text('Update');
            }
            $("#btnSave").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save');
            $('#btnSave').prop('disabled', false);
          }
        });
      };

      //FUNCTION RELOAD TABLE
      function reload_table() 
      {
        table.ajax.reload(null, false);
      }
      
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
              pageSize: 'A3',
              exportOptions: {
                stripHtml: true,
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
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
                    text: 'LAPORAN DAFTAR PERANGKAT PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0];
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

                return 'LAPORAN DAFTAR PERANGKAT PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
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
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          fixedColumns: {
            left: 5
          },
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>perangkat/perangkat_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#StartDateShow').val();
              data.end_date     = $('#EndDateShow').val();
              data.jenis_pr     = $('#JenisShow').val();
              data.dept         = $('#DeptShow').val();
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "70px" },
            { "NOMOR": "NOMOR" , "sClass": "text-center", "width": "70px" },
            { "NAMA PERANGKAT": "NAMA PERANGKAT" , "sClass": "text-left", "width": "150px" },
            { "JENIS PERANGKAT": "JENIS PERANGKAT" , "sClass": "text-left", "width": "150px" },
            { "KATEGORI": "KATEGORI" , "sClass": "text-center", "width": "50px" },
            { "MERK": "MERK" , "sClass": "text-left", "width": "150px" },
            { "TIPE": "TIPE" , "sClass": "text-left", "width": "150px" },
            { "NO SERI": "NO SERI" , "sClass": "text-left", "width": "100px" },
            { "NOMOR PO": "NOMOR PO" , "sClass": "text-left", "width": "100px" },
            { "TGL. PEMBELIAN": "TGL. PEMBELIAN" , "sClass": "text-center", "width": "100px" },
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "100px" },
            { "USER/ PIC": "USER/ PIC" , "sClass": "text-left", "width": "100px" },
            { "AREA PEMASANGAN": "AREA PEMASANGAN" , "sClass": "text-left", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" }
          ],
          "columnDefs": [
            {
              "targets": [0],
              "orderable": false,
              className: 'text-right'
            } 
          ]
        });

        $('#modalForm').on('shown.bs.modal', function () {
          $('#NoBuktiList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Nomor PO",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>perangkat/get_all_bukti',
              type: 'POST',
              dataType: 'JSON',
              delay: 250,
              data: function(params) {
                return {
                  search: params.term,
                  periode: $('#Periode').val()
                };
              },
              processResults: function(data) {
                return {
                  results: $.map(data, function(item) {
                    return {
                      id: item.id,
                      text: item.name,
                      keterangan: item.keterangan,
                      tanggal: item.tanggal,
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          }).on('select2:select', function (e) {
            $('#NoBuktiList.has-error').removeClass('has-error').find('span.help-block').text('');

            // Ambil data terpilih
            var data = e.params.data;
            $('#Keterangan').val(data.keterangan);
            $('#TanggalPembelian').val(data.tanggal);
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#NoBuktiList').select2('destroy');
        });

        $(document).on('show.bs.dropdown', '.btn-group', function (e) {
          var $dropdown = $(e.target).find('.dropdown-menu');
          $('body').append($dropdown.detach());
          var eOffset = $(e.target).offset();
          $dropdown.css({
              'display': 'block',
              'top': eOffset.top + $(e.target).outerHeight(),
              'left': eOffset.left
          });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) {
          var $dropdown = $('body > .dropdown-menu');
          $(e.target).append($dropdown.detach());
          $dropdown.hide();
        });

        $("#Nama, #Status, #JenisID, #Merk, #Tipe, #NoSeri, #DeptID, #UserID, #AreaPemasangan").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Deskripsi').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });
    </script>
  </body>
</html>