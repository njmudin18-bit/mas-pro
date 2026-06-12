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
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control" multiple>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" selected>
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
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="150%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="3">NO</th>
                                      <th class="text-center" rowspan="3">#</th>
                                      <th class="text-center" rowspan="3">DEPARTEMEN</th>
                                      <th class="text-center" rowspan="3">TGL JOB</th>
                                      <th class="text-center" rowspan="3">NO JOB</th>
                                      <th class="text-center" rowspan="3">QTY JOB</th>
                                      <th class="text-center" rowspan="3">PART ID</th>
                                      <th class="text-center" rowspan="3">PART NAME</th>
                                      <th class="text-center" colspan="5">PIN ASSY</th>
                                      <th class="text-center" rowspan="3"></th>
                                      <th class="text-center" rowspan="3">LINE</th>
                                      <th class="text-center" colspan="5">WIP LINE CRIMPING</th>
                                      <th class="text-center" rowspan="3">KETERANGAN</th>
                                      <th class="text-center" rowspan="3">CREATED DATE</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center" colspan="3">PLANNING</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">SISA PLAN</th>
                                      <th class="text-center" colspan="3">PLANNING</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">SISA PLAN</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">TANGGAL</th>
                                      <th class="text-center">PLAN</th>
                                      <th class="text-center">ACTUAL</th>
                                      <th class="text-center">TANGGAL</th>
                                      <th class="text-center">PLAN</th>
                                      <th class="text-center">ACTUAL</th>
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
            <form action="" method="post" id="fgForm">
              <input type="hidden" value="" name="kodeFirst">
              <div class="form-group row border-bottom">
                <label class="col-sm-7 mb-2 col-form-label">ITEM (S)</label>
                <label class="col-sm-2 mb-2 col-form-label text-right">PERIODE</label>
                <div class="col-sm-3 mb-2 text-right">
                  <input type="month" name="Periode" id="Periode" class="form-control" value="<?php echo date('Y-m') ?>">
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Job Number</label>
                <div class="col-sm-6 form-error mb-2">
                  <select name="JobList" id="JobList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-1 col-form-label">Part ID</label>
                <div class="col-sm-3 form-error mb-2">
                  <input type="text" name="PartID" id="PartID" class="form-control" required="required" placeholder="Part ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div class="col-sm-8 form-error mb-2">
                  <input type="text" name="PartName" id="PartName" class="form-control" required="required" placeholder="Part Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="JobDate" id="JobDate" class="form-control" required="required" placeholder="Tanggal Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Job Quantity</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="JobQuantity" id="JobQuantity" class="form-control" required="required" placeholder="Quantity Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Unit ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="UnitID" id="UnitID" class="form-control" required="required" placeholder="Unit ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10 form-error mb-2">
                  <input type="text" name="Noted" id="Noted" class="form-control" required="required" placeholder="Keterangan" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">SET LINE PRODUKSI</label>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4 form-error">
                  <!-- <select name="DeptID" id="DeptID" class="form-control" onchange="get_line_name(this);"> -->
                  <select name="DeptID" id="DeptID" class="form-control">
                    <option value="">-- Pilih --</option>
                    <?php foreach ($DeptList as $value): ?>
                      <option value="<?= $value->DEPTID; ?>" selected>
                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Line Name</label>
                <div class="col-sm-4">
                  <select name="LineName" id="LineName" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($LineList as $value): ?>
                      <option value="<?= $value->Id; ?>">
                        <?= htmlspecialchars($value->LineName, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-2 col-form-label">PIN ASSY</label>
                <div class="col-sm-4 mb-2 mt-2">
                  <select name="Mesin" id="Mesin" class="form-control">
                    <option value="">-- Pilih Mesin --</option>
                    <?php foreach ($MesinList as $value): ?>
                      <option value="<?= $value->Id; ?>">
                        <?= htmlspecialchars($value->Name, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div id="jumlahContainer">
                <div class="form-group row mb-2 mt-2" id="jumlahRow1">
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal</label>
                    <input type="date" name="PlanDate[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Plan Quantity</label>
                    <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">UPH</label>
                    <input type="text" name="Uph[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Jam</label>
                    <input type="text" name="Jam[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Sisa Planning</label>
                    <input type="text" name="SisaPlan[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">WIP LINE CRIMPING</label>
              </div>
              <div id="prosesContainer">
                <div class="form-group row mb-2 mt-3" id="prosesRow1">
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal</label>
                    <input type="date" name="PlanDate2[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Plan Quantity</label>
                    <input type="text" name="PlanQty2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">UPH</label>
                    <input type="text" name="Uph2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Jam</label>
                    <input type="text" name="Jam2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Sisa Planning</label>
                    <input type="text" name="SisaPlan2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
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
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
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
    <script type="text/javascript">
      var save_method;
      var url;

      function reset_all() {
        $('#fgForm')[0].reset();
        $('#modalForm').modal('hide');
        $('.modal-title').text('Tambah Data');

        $('#jumlahContainer').html(`
          <div class="form-group row mb-2 mt-3" id="jumlahRow1">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal</label>
              <input type="date" name="PlanDate[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan Quantity</label>
              <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">UPH</label>
              <input type="text" name="Uph[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Jam</label>
              <input type="text" name="Jam[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Sisa Planning</label>
              <input type="text" name="SisaPlan[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);

        $('#prosesContainer').html(`
          <div class="form-group row mb-2 mt-3" id="prosesRow1">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal</label>
              <input type="date" name="PlanDate2[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan Quantity</label>
              <input type="text" name="PlanQty2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">UPH</label>
              <input type="text" name="Uph2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Jam</label>
              <input type="text" name="Jam2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Sisa Planning</label>
              <input type="text" name="SisaPlan2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#fgForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Data');
        $('#JobList').val(null).trigger('change');
      }

      //FUNCTION RESET
      function reset() {
        $('#fgForm')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //SAVE HEADER
      function save() {
        var form_data = $("#fgForm").serialize();

        var url;
        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>planning/planning_save";
        } else {
          url = "<?php echo base_url(); ?>planning/planning_update";
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
                    $('#fgForm')[0].reset();
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

      //FUNCTION EDIT
      function edit(Ids, JobNumbers) 
      {
        save_method = 'update';
        $('#fgForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>planning/planning_edit",
          type: "POST",
          dataType: "JSON",
          data: {
            IdHeader: Ids,
            JobNumber: JobNumbers,
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
               // 📝 jika opsi PartID sudah ada di select2
              if ($('[name="JobList"] option[value="' + data.first.JobNumber + '"]').length > 0) {
                $('[name="JobList"]').val(data.first.JobNumber).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.first.JobNumber, data.first.JobNumber, true, true);
                $('[name="JobList"]').append(newOption).trigger('change');
              }

              var html  = '';
              var html2 = '';

              $('[name="kodeFirst"]').val(data.first.Id);
              $('[name="PartID"]').val(data.first.PartID);
              $('[name="PartName"]').val(data.first.PartID);
              $('[name="JobDate"]').val(data.first.JobDate);
              $('[name="JobQuantity"]').val(data.first.JobQuantity.replaceAll(",", "."));
              $('[name="UnitID"]').val(data.first.UnitID);
              $('[name="Noted"]').val(data.first.Noted);
              $('[name="Periode"]').val(data.first.JobPeriode);
              $('[name="DeptID"]').val(data.first.DeptID);
              $('[name="Mesin"]').val(data.first.MachineID);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Data #' + JobNumbers);
              $('#btnSave').text('Update');

              let DeptID   = data.first.DeptID;
              let LineID   = data.first.LineID;

              get_line_name(DeptID, LineID);
              
              data.second.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-3" id="jumlahRow${rowNumber}">
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Tanggal</label>
                      <input type="hidden" name="kodeSecond[]" value="${item.Id}">
                      <input type="date" name="PlanDate[]" value="${item.PlanDate}" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Plan Quantity</label>
                      <input type="text" name="PlanQty[]" value="${item.PlanQty.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">UPH</label>
                      <input type="text" name="Uph[]" value="${item.Uph.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Jam</label>
                      <input type="text" name="Jam[]" value="${item.Hours}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Sisa Planning</label>
                      <input type="text" name="SisaPlan[]" value="${item.SisaPlan.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRowJumlah('jumlahRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#jumlahRow${rowNumber}').remove()"
                      }
                    </div>
                  </div>
                `;
              });

              data.third.forEach((item, index) => {
                let rowNumber = index + 1;
                html2 += `
                  <div class="form-group row mb-2 mt-3" id="prosesRow${rowNumber}">
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Tanggal</label>
                      <input type="hidden" name="kodeThird[]" value="${item.Id}">
                      <input type="date" name="PlanDate2[]" value="${item.PlanDate}" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Plan Quantity</label>
                      <input type="text" name="PlanQty2[]" value="${item.PlanQty.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">UPH</label>
                      <input type="text" name="Uph2[]" value="${item.Uph.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Jam</label>
                      <input type="text" name="Jam2[]" value="${item.Hours}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Sisa Planning</label>
                      <input type="text" name="SisaPlan2[]" value="${item.SisaPlan.replace(',', '.')}" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
                      <span class="help-block"></span>
                    </div>
                    <div class="col-md-2 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="tambah${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusProsesJumlah('prosesRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>`
                      }
                    </div>
                  </div>
                `;
              });

              $('#jumlahContainer').html(html);
              $('#prosesContainer').html(html2);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCITON HAPUS ROW JUMLAH
      function hapusRowJumlah(rowId)
      {
        const row         = $('#' + rowId);
        // Ambil data sebelum dihapus
        const jobNumber   = $('#JobList').val();
        const kodeSecond  = row.find('input[name="kodeSecond[]"]').val();

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
              url: "<?php echo base_url(); ?>planning/hapus_row_jumlah",
              type: "POST",
              dataType: "JSON",
              data: {
                JobNumber: jobNumber,
                KodeSecond: kodeSecond
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                $("#loading").hide();
                //edit(jobNumber);
                reload_table();
                // Hapus elemen
                row.remove();
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $("#loading").hide();
                alert('Error hapus data');
              }
            });
          }
        });
      }

      function hapusProsesJumlah(rowId)
      {
        const row         = $('#' + rowId);
        console.log(row);
        // Ambil data sebelum dihapus
        const jobNumber   = $('#JobList').val();
        const kodeThird  = row.find('input[name="kodeThird[]"]').val();

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
              url: "<?php echo base_url(); ?>planning/hapus_row_proses",
              type: "POST",
              dataType: "JSON",
              data: {
                JobNumber: jobNumber,
                KodeThird: kodeThird
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                $("#loading").hide();
                //edit(jobNumber);
                reload_table();
                // Hapus elemen
                row.remove();
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
      function hapusAll(Ids, JobNumbers) 
      {
        Swal.fire({
          title: 'Hapus ' + JobNumbers + ' ?',
          text: "Data yang dihapus tidak bisa dikembalikan.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>planning/hapus_all',
              type: 'POST',
              data: {
                IdHeader: Ids,
                JobNumber: JobNumbers,
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                var result = JSON.parse(data);
                console.log(result);
                if (result.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  //$("#" + jobNumber).remove();
                  // Swal.fire({
                  //   title: "Sukses",
                  //   text: result.message,
                  //   icon: "success"
                  // });
                  reload_table();
                }

                $("#loading").hide();
              },
              error: function() {
                alert('Something is wrong');
              },
            });
          }
        })
      }

      function get_line_name(el, defaultValue = null) 
      {
        console.log(defaultValue);
        $.ajax({
          url : "<?php echo base_url();?>planning/get_proses_produksi_with_line",
          method : "POST",
          data : {
            id: (typeof el === "object" ? el.value : el)
          },
          dataType : 'json',
          success: function(data){
            var html = '<option value="">-- Pilih --</option>';
            for (var i = 0; i < data.length; i++) {
              // cek jika defaultValue sama dengan SSN maka tambahkan selected
              let selected = (defaultValue && data[i].Id == defaultValue) ? ' selected' : '';
              html += '<option value="'+ data[i].Id +'"'+selected+'>'+ data[i].LineName +'</option>';
            }
            if (typeof el === "object") {
              // jika dipanggil dari select onchange
              $(el).closest('.form-group.row').find('select[name="LineName"]').html(html);
            } else {
              // jika dipanggil dari ajax dengan UserID langsung
              $('#LineName').html(html);
            }
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      // TAMBAH KOLOM JUMLAH
      $(document).on('click', '#plus1', function () {
        let count = $('#jumlahContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-3" id="jumlahRow${count}">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal</label>
              <input type="date" name="PlanDate[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan Quantity</label>
              <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">UPH</label>
              <input type="text" name="Uph[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Jam</label>
              <input type="text" name="Jam[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Sisa Planning</label>
              <input type="text" name="SisaPlan[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#jumlahContainer').append(row);
      });

      $(document).on('click', '#tambah1', function () {
        let count = $('#prosesContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-3" id="prosesRow${count}">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal</label>
              <input type="date" name="PlanDate2[]" class="form-control" required placeholder="Plan Date" maxlength="35" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan Quantity</label>
              <input type="text" name="PlanQty2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Plan Qty." autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">UPH</label>
              <input type="text" name="Uph2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 10.000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Jam</label>
              <input type="text" name="Jam2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 25" autocomplete="off" data-required="true" readonly>
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Sisa Planning</label>
              <input type="text" name="SisaPlan2[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Contoh: 3000" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-proses" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#prosesContainer').append(row);
      });

      // HAPUS KOLOM JUMLAH
      $(document).on('click', '.remove-kolom-jumlah', function () {
        $(this).closest('.form-group').remove();
      });

      $(document).on('click', '.remove-kolom-proses', function () {
        $(this).closest('.form-group').remove();
      });

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading").hide();

        // table = $('#myTable').DataTable({
        //   dom: 'Bfrltip',
        //   button: [
        //     {
        //       extend: 'pdfHtml5',
        //       text: 'Export All',
        //       title: '',
        //       className: 'btn btn-danger',
        //       orientation: 'landscape',
        //       pageSize: 'A3',
        //       exportOptions: {
        //         columns: [0, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
        //       },
        //       customize: function (doc) {
        //         const rawTanggal = $('#tanggal').val();
        //         let tanggal      = rawTanggal;

        //         if (rawTanggal) {
        //           const dateObj = new Date(rawTanggal);
        //           const options = { day: 'numeric', month: 'long', year: 'numeric' };
        //           tanggal       = dateObj.toLocaleDateString('id-ID', options);
        //         }

        //         function formatRibuan(num) {
        //           if (num === null || num === undefined) return '0';
        //           if (typeof num === 'number') {
        //               return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        //           }
        //           let str = num.toString();
        //           const cleaned = str.replace(/[^\d.,-]/g, '');
        //           const normalized = cleaned.replace(',', '.');
        //           const n = parseFloat(normalized);

        //           return isNaN(n) ? str : n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        //         }

        //         doc.defaultStyle.fontSize = 8;
        //         doc.pageMargins           = [10, 40, 10, 60];
        //         doc.styles = {
        //           subheader: {
        //             fontSize: 12,
        //             bold: true,
        //             alignment: 'left'
        //           },
        //           tableHeader: {
        //             bold: true,
        //             fontSize: 8,
        //             color: 'white',
        //             fillColor: '#007bff',
        //             alignment: 'center'
        //           }
        //         };

        //         doc.content.unshift(
        //           {
        //             text: 'PT. MULTI ARTA SEKAWAN',
        //             bold: true,
        //             fontSize: 14,
        //             style: 'subheader',
        //             alignment: 'center',
        //             margin: [0, 0, 0, 10]
        //           },
        //           {
        //             text: 'PLANNING KIRIM HARIAN ',
        //             bold: true,
        //             fontSize: 12,
        //             style: 'subheader',
        //             alignment: 'center',
        //             margin: [0, 0, 0, 10]
        //           },
        //           {
        //             text: 'TANGGAL : ' + tanggal.toUpperCase(),
        //             bold: true,
        //             fontSize: 10,
        //             style: 'subheader',
        //             alignment: 'left',
        //             margin: [0, 0, 0, 10]
        //           }
        //         );


        //         // === Styling Main Table ===
        //         const mainTable = doc.content.find(item => item.table);
        //         if (mainTable) {
        //             mainTable.fontSize    = 7.5;
        //             const alignRightCols  = [0, 13, 15];
        //             const body            = mainTable.table.body;

        //             for (let i = 1; i < body.length; i++) {
        //                 for (let j = 0; j < body[i].length; j++) {
        //                     if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
        //                         body[i][j].alignment = 'right';
        //                     }
        //                 }

        //                 // SUB TOTAL styling
        //                 for (let j = 0; j < body[i].length; j++) {
        //                     if (
        //                         typeof body[i][j].text === 'string' &&
        //                         body[i][j].text.trim().toUpperCase() === 'SUB TOTAL'
        //                     ) {
        //                         for (let k = 0; k < body[i].length; k++) {
        //                             body[i][k].bold = true;
        //                             body[i][k].fillColor = '#6c757d';
        //                             body[i][k].color = '#fff';
        //                         }
        //                         break;
        //                     }
        //                 }

        //                 for (let j = 0; j < body[i].length; j++) {
        //                     if (
        //                         typeof body[i][j].text === 'string' &&
        //                         body[i][j].text.trim().toUpperCase() === 'TOTAL'
        //                     ) {
        //                         for (let k = 0; k < body[i].length; k++) {
        //                             body[i][k].bold = true;
        //                             body[i][k].fillColor = '#ff5370';
        //                             body[i][k].color = '#fff';
        //                         }
        //                         break;
        //                     }
        //                 }

        //                 for (let j = 0; j < body[i].length; j++) {
        //                     if (
        //                         typeof body[i][j].text === 'string' &&
        //                         body[i][j].text.trim().toUpperCase() === 'WAKTU'
        //                     ) {
        //                         for (let k = 0; k < body[i].length; k++) {
        //                             body[i][k].bold = true;
        //                             body[i][k].fillColor = '#2ed8b6';
        //                             body[i][k].color = '#fff';
        //                         }
        //                         break;
        //                     }
        //                 }
        //             }

        //             // Style baris terakhir
        //             // const lastRowIndex = body.length - 1;
        //             // for (let j = 0; j < body[lastRowIndex].length; j++) {
        //             //     if (body[lastRowIndex][j].text !== undefined) {
        //             //         body[lastRowIndex][j].fillColor = '#007bff';
        //             //         body[lastRowIndex][j].color = '#fff';
        //             //     }
        //             // }

        //             mainTable.layout = {
        //                 hLineWidth: () => 0.5,
        //                 vLineWidth: () => 0.5,
        //                 hLineColor: () => '#aaa',
        //                 vLineColor: () => '#aaa',
        //                 paddingLeft: () => 4,
        //                 paddingRight: () => 4,
        //                 paddingTop: () => 2,
        //                 paddingBottom: () => 2,
        //                 fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
        //             };
        //         }

        //         // === Footer ===
        //         doc.footer = function (currentPage, pageCount) {
        //             return {
        //                 columns: [
        //                     { text: 'Printed on: ' + new Date().toLocaleString(), alignment: 'left', margin: [10, 0, 0, 0] },
        //                     { text: 'PT MULTI ARTA SEKAWAN - CONFIDENTIAL', alignment: 'center' },
        //                     { text: 'Page ' + currentPage + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 10, 0] }
        //                 ],
        //                 fontSize: 8
        //             };
        //         };
        //       },
        //       // Tambahkan opsi filename di sini
        //       filename: function() {
        //         const rawTanggal = $('#tanggal').val();
        //         let tanggal      = rawTanggal;

        //         if (rawTanggal) {
        //           const dateObj = new Date(rawTanggal);
        //           const options = { day: 'numeric', month: 'long', year: 'numeric' };
        //           tanggal       = dateObj.toLocaleDateString('id-ID', options);
        //         }

        //         return 'Planning Kirim Harian Tanggal ' + tanggal.toUpperCase();
        //       }
        //     }
        //   ],
        //   "pagingType": "full_numbers",
        //   "lengthMenu": [
        //     [10, 25, 50, -1],
        //     [10, 25, 50, "All"]
        //   ],
        //   responsive: false,
        //   language: {
        //     search: "_INPUT_",
        //     searchPlaceholder: "Search records",
        //   },
        //   fixedColumns: {
        //     left: 5
        //   },
        //   select: {
        //     style: 'single'
        //   },
        //   "processing": true,
        //   "serverSide": false,
        //   "order": [],
        //   // Load data for the table's content from an Ajax source
        //   "ajax": {
        //     "url": "<?php echo base_url(); ?>planning/planning_list",
        //     "type": "POST",
        //     "data": function(data) {
        //       let DeptShow = [];
        //       $('input[name="DeptShow"]:checked').each(function () {
        //         if ($(this).val()) {
        //           DeptShow.push($(this).val());
        //         }
        //       });

        //       data.start_date   = $('#start_date').val();
        //       data.end_date     = $('#end_date').val();
        //       data.dept_id      = (DeptShow.length > 0) ? DeptShow : <?php echo $DeptID; ?>;
        //     }
        //   },
        //   "aoColumns": [
        //     { "NO": "NO" , "sClass": "text-right", "width": "50px"},
        //     { "#": "#" , "sClass": "text-center", "width": "50px"},
        //     { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "50px"},
        //     { "TGL. JOB": "TGL. JOB" , "sClass": "text-center", "width": "100px" },
        //     { "NO. JOB": "NO. JOB" , "sClass": "text-center", "width": "100px" },
        //     { "QTY. JOB": "QTY. JOB" , "sClass": "text-right", "width": "50px" },
        //     { "PART ID": "PART ID" , "sClass": "text-left", "width": "100px" },
        //     { "PART NAME": "PART NAME" , "sClass": "text-left", "width": "245px" },
        //     { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" }, 
        //     { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
        //     { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "100px" }, //10
        //     { "%": "%" , "sClass": "text-right", "width": "100px" },
        //     { "SISA PLAN": "SISA PLAN" , "sClass": "text-right", "width": "100px" },
        //     { "": "" , "sClass": "text-right", "width": "10px" },
        //     { "LINE": "LINE" , "sClass": "text-left", "width": "100px" },
        //     { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" }, 
        //     { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
        //     { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "100px" },
        //     { "%": "%" , "sClass": "text-right", "width": "100px" },
        //     { "SISA PLAN": "SISA PLAN" , "sClass": "text-right", "width": "100px" },
        //     { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
        //     { "CREATED DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" } //20
        //   ],
        //   //Set column definition initialisation properties.
        //   "columnDefs": [{
        //     "targets": [0], //last column
        //     "orderable": false, //set not orderable
        //     className: 'text-right'
        //   }, ]
        // });

        table = $('#myTable').DataTable({
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
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]
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
                    text: 'LAPORAN PLANNING CRIMPING PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'DEPARTEMEN : CRIMPING',
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
                    mainTable.fontSize    = 8.2;
                    const alignRightCols  = [0, 4, 8, 9, 10, 14, 15, 16, 17];
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
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN PLANNING CRIMPING PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
            "url": "<?php echo base_url(); ?>planning/planning_list",
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
              data.dept_id      = (DeptShow.length > 0) ? DeptShow : <?php echo $DeptID; ?>;
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "50px"},
            { "TGL. JOB": "TGL. JOB" , "sClass": "text-center", "width": "100px" },
            { "NO. JOB": "NO. JOB" , "sClass": "text-center", "width": "100px" },
            { "QTY. JOB": "QTY. JOB" , "sClass": "text-right", "width": "50px" },
            { "PART ID": "PART ID" , "sClass": "text-left", "width": "100px" },
            { "PART NAME": "PART NAME" , "sClass": "text-left", "width": "245px" },
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" }, 
            { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
            { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "100px" }, //10
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "SISA PLAN": "SISA PLAN" , "sClass": "text-right", "width": "100px" },
            { "": "" , "sClass": "text-right", "width": "10px" },
            { "LINE": "LINE" , "sClass": "text-left", "width": "100px" },
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" }, 
            { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
            { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "100px" },
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "SISA PLAN": "SISA PLAN" , "sClass": "text-right", "width": "100px" },
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
            { "CREATED DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" } //20
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
            
          },

          // 2. KONFIGURASI KOLOM (FIXED PART & RENDER)
          'columnDefs': [
            {
              'targets': [0, 1, 2],
              'orderable': false,
              'createdCell': function (td, cellData, rowData, row, col) {
                
              }
            },
          ],
        });

        table.on('click', 'tbody tr', function (e) {
            table.$('tr.selected').removeClass('selected');  // hilangkan selected di semua row
            $(this).addClass('selected');                    // tambahkan selected ke row yg diklik
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

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }

        $('#modalForm').on('shown.bs.modal', function () {
          $('#JobList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Nomor Job",
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>planning/get_job_number',
                type: 'POST',
                dataType: 'JSON',
                delay: 250,
                data: function(params) {
                  return {
                    search: params.term,
                    Periode: $('#Periode').val().replace('-', '')
                  };
                },
                processResults: function(data) {
                  return {
                    results: $.map(data, function(item) {
                      return {
                        id: item.id,
                        text: item.name + ' - ' + item.PartName,
                        PartID: item.PartID,
                        PartName: item.PartName,
                        Tgl: item.Tgl,
                        QtyOrder: item.QtyOrder,
                        Keterangan: item.Keterangan,
                        UnitID: item.UnitID
                      };
                    })
                  };
                },
                cache: true
            },
            minimumInputLength: 3
          });

          // Add callback function using select2:select event
          $('#JobList').on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#PartID').val(selectedData.PartID);
            $('#PartName').val(selectedData.PartName);
            $('#JobDate').val(selectedData.Tgl);
            $('#JobQuantity').val(selectedData.QtyOrder);
            $('#UnitID').val(selectedData.UnitID);
            $('#Remark').val(selectedData.Keterangan);

            $('.has-error').each(function() {
              $(this).removeClass('has-error');
              $(this).find('span.help-block').text('');
            });
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#JobList').select2('destroy');
        });

        $("#TanggalProduksi, #DeptID, #LineName, #Mesin").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#jumlahContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        // Gunakan Event Delegation karena radio button dimuat via Ajax
        // Targetkan Container utamanya ('#container-line-radios')
        $('#container-line-radios').on('change', 'input[name="line_id"]', function() {
            
            var container = $('#container-line-radios');

            // 1. Hapus class error (garis merah/kotak merah) dari container
            container.removeClass('has-error'); 

            // 2. Hapus pesan text error yang ada di bawah radio button
            container.find('.help-block').remove(); 
        });

        $('#ngContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $('#Noted').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });


        // 1. Helper: Mengubah string format "10.000,50" menjadi angka float
        function parseNumber(val) {
            if (!val) return 0;
            // Hapus titik ribuan, ganti koma desimal jadi titik
            var cleanVal = val.toString().split('.').join('').replace(',', '.');
            return parseFloat(cleanVal) || 0;
        }

        // 2. Helper: Mengubah angka float kembali ke format Indonesia "10.000,50"
        function formatNumber(num) {
            // Menggunakan built-in function toLocaleString untuk format ribuan
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 0, 
                maximumFractionDigits: 2 
            }); 
            // Note: Jika ingin memaksa koma sebagai desimal manual, bisa pakai .replace('.', ',')
        }

        // ==========================================================================
        // MAIN LOGIC: Dijalankan saat user mengetik di PlanQty atau UPH
        // ==========================================================================
        $(document).on('input', 'input[name^="PlanQty"], input[name^="Uph"]', function() {
            
            // A. Tentukan baris (row) mana yang sedang diedit
            var $row = $(this).closest('.form-group');

            // B. Ambil Nilai-nilai yang dibutuhkan
            // Ambil Job Quantity (ID Statis - diluar baris dinamis)
            var jobQty  = parseNumber($('#JobQuantity').val());
            
            // Ambil Plan Qty & UPH (Dinamis - didalam baris ini)
            var rawPlan = $row.find('input[name^="PlanQty"]').val();
            var rawUph  = $row.find('input[name^="Uph"]').val();
            
            var planQty = parseNumber(rawPlan);
            var uph     = parseNumber(rawUph);


            // --- PERHITUNGAN 1: JAM (Plan / UPH) ---
            var jam = 0;
            if (uph > 0) {
                jam = planQty / uph;
            }
            // Output Jam (Format 2 desimal)
            // toFixed(2) menghasilkan string misal "2.50", replace titik jadi koma
            $row.find('input[name^="Jam"]').val(jam.toFixed(2).replace('.', ','));


            // --- PERHITUNGAN 2: SISA PLANNING (Job Qty - Plan Qty) ---
            // Rumus: Sisa = Job Quantity - Plan Quantity
            var sisa = jobQty - planQty;
            
            // Output Sisa Planning (Format Ribuan Indonesia)
            $row.find('input[name^="SisaPlan"]').val(formatNumber(sisa));
        });

        // Opsional: Jika JobQuantity berubah (misal dari ajax), 
        // kita harus trigger ulang perhitungan semua baris.
        $('#JobQuantity').on('change input', function() {
          $('input[name^="PlanQty"]').trigger('input');
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