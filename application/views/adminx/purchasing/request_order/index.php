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
                                <div class="col-md-4 col-sm-12 m-t-3">
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
                                <div class="col-md-2 col-sm-12 m-t-3 text-right">
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
                                      <th class="text-center" width="5%">REQ. NO</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="5%">DEPARTEMEN</th>
                                      <th class="text-center" width="5%">NIP</th>
                                      <th class="text-center" width="7%">NAME</th>
                                      <th class="text-center" width="7%">REQ. DATE</th>
                                      <th class="text-center" width="7%">PRIORITAS</th>
                                      <th class="text-center" width="7%">NAMA ITEM</th>
                                      <th class="text-center" width="7%">QUANTITY</th>
                                      <th class="text-center" width="7%">UNIT ID</th>
                                      <th class="text-center" width="7%">HARGA/ ITEM</th>
                                      <th class="text-center" width="7%">SUB TOTAL</th>
                                      <th class="text-center" width="7%">LINK</th>
                                      <th class="text-center" width="7%">KETERANGAN</th>
                                      <th class="text-center" width="10%">CREATE DATE</th>
                                      <th class="text-center" width="10%">CREATE BY</th>
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
              <input type="hidden" value="" name="Nomor">
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
                <label class="col-sm-2 col-form-label">Pegawai</label>
                <div class="col-sm-4 form-error">
                  <select name="EmployeeID" id="EmployeeID" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Prioritas</label>
                <div class="col-sm-4 form-error">
                  <select name="Prioritas" id="Prioritas" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-4 form-error">
                  <input type="date" name="TanggalPermintaan" id="TanggalPermintaan" class="form-control" placeholder="Tanggal permintaan">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-10 form-error">
                  <textarea name="AlasanPermintaan" id="AlasanPermintaan" class="form-control" rows="3" maxlength="255" placeholder="Keterangan permintaan"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">ITEM(S) BARANG PERMINTAAN</label>
              </div>
              <div id="mainContainer">
                <div class="form-group row mb-2 mt-3" id="mainRow1">
                  <div class="col-md-4 form-error">
                    <label class="col-form-label">Nama Barang</label>
                    <input type="text" name="NamaBarang[]" class="form-control text-capitalize" required placeholder="Nama Barang" maxlength="75" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-1 form-error">
                    <label class="col-form-label">Qty</label>
                    <input type="text" name="Quantity[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Qty" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Unit</label>
                    <select name="UnitID[]" class="form-control">
                      <option value="">-- Pilih --</option>
                      <?php foreach ($unit as $u): ?>
                        <option value="<?= $u->UnitID; ?>"><?= htmlspecialchars($u->UnitName, ENT_QUOTES, 'UTF-8'); ?></option>
                      <?php endforeach; ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Estimasi Harga</label>
                    <input type="text" name="Harga[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Harga" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Link</label>
                    <input type="text" name="Link[]" class="form-control text-lowercase" required placeholder="Link Produk" maxlength="255" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-1 button-center">
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

      // Inject options Unit dari PHP agar bisa dipakai di template JS dinamis
      var unitOptions = '<option value="">-- Pilih --</option>';
      <?php foreach ($unit as $u): ?>
        unitOptions += '<option value="<?= $u->UnitID; ?>"><?= htmlspecialchars($u->UnitName, ENT_QUOTES, 'UTF-8'); ?></option>';
      <?php endforeach; ?>

      // Data unit dalam bentuk array JS untuk generate options dengan selected value
      var unitData = [
        <?php foreach ($unit as $u): ?>
          { id: '<?= $u->UnitID; ?>', name: '<?= htmlspecialchars($u->UnitName, ENT_QUOTES, "UTF-8"); ?>' },
        <?php endforeach; ?>
      ];

      // Fungsi untuk generate unit options dengan selected value
      function getUnitOptions(selectedValue) {
        var options = '<option value="">-- Pilih --</option>';
        for (var i = 0; i < unitData.length; i++) {
          var sel = (unitData[i].id == selectedValue) ? ' selected' : '';
          options += '<option value="' + unitData[i].id + '"' + sel + '>' + unitData[i].name + '</option>';
        }
        return options;
      }
      $(document).on('click', '#tambah1', function () 
      {
        let count = $('#mainContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-3" id="mainRow${count}">
            <div class="col-md-4 form-error">
              <label class="col-form-label">Nama Barang</label>
              <input type="text" name="NamaBarang[]" class="form-control text-capitalize" required placeholder="Nama Barang" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-1 form-error">
              <label class="col-form-label">Qty</label>
              <input type="text" name="Quantity[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Qty" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Unit</label>
              <select name="UnitID[]" class="form-control">${unitOptions}</select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Estimasi Harga</label>
              <input type="text" name="Harga[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Harga" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Link</label>
              <input type="text" name="Link[]" class="form-control text-lowercase" required placeholder="Link Produk" maxlength="255" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-1 button-center">
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

      function updateStatus(ReqNumber) {
        Swal.fire({
            title: 'Update Status ' + ReqNumber,
            input: 'select',
            inputOptions: {
                'P': 'PENDING',
                'A': 'APPROVED',
                'R': 'REJECTED',
                'CO': 'COMPLETED',
                'CA': 'CANCELED'
            },
            inputPlaceholder: 'Pilih status baru',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Simpan Status',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    if (value !== '') {
                        resolve();
                    } else {
                        resolve('Anda harus memilih status!');
                    }
                });
            }
        }).then((result) => {
          if (result.isConfirmed) {
              // result.value berisi kode status (P, A, R, dst)
              const selectedStatus = result.value;

              $.ajax({
                  url: '<?php echo base_url(); ?>request_order/request_approved', // Pastikan backend Anda siap menerima parameter status
                  type: 'POST',
                  data: {
                      Nomor: ReqNumber,
                      Status: selectedStatus // Mengirimkan status yang dipilih
                  },
                  dataType: "json",
                  beforeSend: function() {
                      $("#loading").show();
                  },
                  success: function(data) {
                      console.log("Response:", data);

                      if (data.status === 'forbidden') {
                          Swal.fire(
                              'FORBIDDEN',
                              'Access Denied',
                              'error'
                          );
                      } else {
                          Swal.fire({
                              icon: 'success',
                              title: 'Berhasil!',
                              text: 'Status Request ' + ReqNumber + ' telah diperbarui.',
                              timer: 1500,
                              showConfirmButton: false
                          });
                          reload_table();
                      }
                      $("#loading").hide();
                  },
                  error: function(xhr, status, error) {
                      console.error("AJAX Error:", status, error);
                      Swal.fire('Oops!', 'Terjadi kesalahan saat memperbarui status.', 'error');
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

        $('#mainContainer').html(`
          <div class="form-group row mb-2 mt-3" id="mainRow1">
            <div class="col-md-4 form-error">
              <label class="col-form-label">Nama Barang</label>
              <input type="text" name="NamaBarang[]" class="form-control text-capitalize" required placeholder="Nama Barang" maxlength="75" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-1 form-error">
              <label class="col-form-label">Qty</label>
              <input type="text" name="Quantity[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Qty" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Unit</label>
              <select name="UnitID[]" class="form-control">${unitOptions}</select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Estimasi Harga</label>
              <input type="text" name="Harga[]" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Harga" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Link</label>
              <input type="text" name="Link[]" class="form-control text-lowercase" required placeholder="Link Produk" maxlength="255" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-1 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>request_order/request_edit/" + id,
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              $('[name="kode"]').val(data.header.Nomor);
              $('[name="Nomor"]').val(data.header.Nomor);
              $('[name="DeptID"]').val(data.header.DeptID);
              $('[name="Prioritas"]').val(data.header.Priority);
              $('[name="TanggalPermintaan"]').val(data.header.RequestDate);
              $('[name="AlasanPermintaan"]').val(data.header.Noted);

              let DeptID = data.header.DeptID;
              let UserID = data.header.EmployeeID;
              get_karyawan(DeptID, UserID);

              var html   = '';

              data.detail.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-3" id="mainRow${rowNumber}">
                    <input type="hidden" name="IdDetail[]" value="${item.Id}">
                    <div class="col-md-4 form-error">
                      <label class="col-form-label">Nama Barang</label>
                      <input type="text" name="NamaBarang[]" value="${item.ItemName}" class="form-control text-capitalize" required placeholder="Nama Barang" maxlength="75" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-1 form-error">
                      <label class="col-form-label">Qty</label>
                      <input type="text" name="Quantity[]" value="${item.Quantity}" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Qty" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Unit</label>
                      <select name="UnitID[]" class="form-control">${getUnitOptions(item.UnitID)}</select>
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Estimasi Harga</label>
                      <input type="text" name="Harga[]" value="${item.Prices}" class="form-control" maxlength="12" oninput="AllowDecimalAndComma(this)" required placeholder="Harga" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Link</label>
                      <input type="text" name="Link[]" value="${item.Link}" class="form-control text-lowercase" required placeholder="Link Produk" maxlength="255" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-1 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRowJumlah('jumlahRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#jumlahRow${rowNumber}').remove()"
                      }
                    </div>
                  </div>
                `;
              });

              $('#mainContainer').html(html);

              $('#modal').modal('show');
              $('.modal-title').text('Edit Data');
              if (save_method == 'add') {
                $('#btnSave').text('Update');
              } else {
                $('#btnSave').text('Save');
              }
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
              url: '<?php echo base_url(); ?>request_order/request_deleted/' + id,
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

      function save() {
        var form_data = $("#formData").serialize();
        
        // Ambil label (text) dari select yang terpilih
        var deptName     = $("#DeptID option:selected").text().trim();
        var employeeName = $("#EmployeeID option:selected").text().trim();

        // Tambahkan ke payload form_data
        form_data += "&DeptName=" + encodeURIComponent(deptName);
        form_data += "&EmployeeName=" + encodeURIComponent(employeeName);

        var url;
        if (save_method == 'add') {
          $('#btnSave').text('Save');
          url = "<?php echo base_url(); ?>request_order/request_add";
        } else {
          $('#btnSave').text('Update');
          url = "<?php echo base_url(); ?>request_order/request_update";
        }

        $.ajax({
            url: url,
            dataType: 'JSON',
            data: form_data,
            type: 'POST',
            beforeSend: function() {
                $("#loading").show();
                $("#btnSave").prop('disabled', true);
                
                // --- TAMBAHAN 1: Reset khusus untuk container radio button sebelum kirim ---
                $("#container-line-radios").removeClass('has-error'); 
                // --------------------------------------------------------------------------
            },
            success: function(data) {
                // Bersihkan error standar
                $(".form-group").removeClass('has-error'); // Atau .form-error sesuai class Anda
                $(".help-block").remove();
                
                // --- TAMBAHAN 2: Pastikan error di container radio juga bersih ---
                $("#container-line-radios").removeClass('has-error');
                // ----------------------------------------------------------------

                if (data.status == 'success') {
                    $("#loading").hide();
                    $('#modalForm').modal('hide');
                    $('#formData')[0].reset();
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
                    $("#loading").hide();
                    Swal.fire('FORBIDDEN', 'Access Denied', 'info');
                } else {
                    $("#loading").hide();

                    // LOOPING ERROR
                    for (var i = 0; i < data.inputerror.length; i++) {
                        var inputName = data.inputerror[i];
                        var errorMsg = data.error_string[i];

                        // --- LOGIKA KHUSUS UNTUK RADIO BUTTON (LINE_ID) ---
                        if (inputName === 'line_id') {
                            // Targetkan ID Container, bukan input name-nya
                            var container = $('#container-line-radios');
                            
                            // Tambahkan class error (jika perlu styling merah)
                            container.addClass('has-error'); 

                            // Cek agar pesan tidak muncul dobel
                            if (container.find('.help-block').length === 0) {
                                // Append pesan error di bagian bawah container
                                container.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                            }
                        } 
                        // --- LOGIKA UNTUK INPUT ARRAY (PlanDate[], PlanQty[]) ---
                        else {
                            var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                            if (arrayMatch) {
                                var arrayName = arrayMatch[1];
                                var arrayIndex = parseInt(arrayMatch[2]);
                                var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                                
                                inputElem.closest('.form-error').addClass('has-error');
                                if (inputElem.next('.help-block').length === 0) {
                                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                                }
                            } 
                            // --- LOGIKA UNTUK INPUT BIASA LAINNYA ---
                            else {
                                var inputElem = $('[name="' + inputName + '"]');
                                inputElem.closest('.form-error').addClass('has-error');
                                if (inputElem.next('.help-block').length === 0) {
                                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                                }
                            }
                        }
                    }
                }

                if (save_method == 'add') {
                  $("#btnSave").text('Save');
                } else {
                  $("#btnSave").text('Update');
                }
                $("#btnSave").prop('disabled', false);
            },
            error: function() {
              $("#loading").hide();
              alert('Error adding / update data');
              $('#btnSave').text('Save');
              $('#btnSave').prop('disabled', false);
            }
        });
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
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
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
                    text: 'LAPORAN KETIDAKHADIRAN PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0, 9];
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

                return 'LAPORAN KETIDAKHADIRAN PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
            "url": "<?php echo base_url(); ?>request_order/request_list",
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
            { "REQ. NO": "REQ. NO" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px"},
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "80px" },
            { "NIP": "NIP" , "sClass": "text-center", "width": "50px" },
            { "NAME": "NAME" , "sClass": "text-left", "width": "150px" },
            { "REQ. DATE": "REQ. DATE" , "sClass": "text-center", "width": "50px" },
            { "PRIORITAS": "PRIORITAS" , "sClass": "text-center", "width": "80px" },
            { "NAMA ITEM": "NAMA ITEM" , "sClass": "text-left", "width": "80px" },
            { "QUANTITY": "QUANTITY" , "sClass": "text-right", "width": "80px" },
            { "UNIT": "UNIT" , "sClass": "text-left", "width": "50px" },
            { "HARGA/ ITEM": "HARGA/ ITEM" , "sClass": "text-right", "width": "50px" },
            { "SUB TOTAL": "SUB TOTAL" , "sClass": "text-right", "width": "50px" },
            { "LINK": "LINK" , "sClass": "text-left", "width": "50px" },
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "250px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" }
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

        $("#DeptID, #EmployeeID, #Prioritas, #TanggalPermintaan, #AlasanPermintaan").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#mainContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $('#mainContainer').on('input change', 'select', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $('#AlasanPermintaan').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });

      function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: 'Link has been copied to clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      }
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
          selectionLimit: 0,
        });
      });
    </script>
  </body>
</html>