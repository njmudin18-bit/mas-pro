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
                                <label class="col-md-2 col-sm-12 col-form-label m-t-3">Filter by</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
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
                                <div class="col-md-4 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="10%">TYPE</th>
                                      <th class="text-center" width="10%">STATUS</th>
                                      <th class="text-center">NAMA LISENSI</th>
                                      <th class="text-center" width="10%">KEY</th>
                                      <th class="text-center" width="10%">VENDOR</th>
                                      <th class="text-center" width="10%">TGL. PEMBELIAN</th>
                                      <th class="text-center" width="10%">TGL. EXPIRED</th>
                                      <th class="text-center" width="10%">JUMLAH AKUN</th>
                                      <th class="text-center" width="10%">JUMLAH TERPAKAI</th>
                                      <th class="text-center" width="10%">SISA</th>
                                      <th class="text-center" width="10%">KETERANGAN</th>
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
                <label class="col-sm-2 col-form-label">Nama Lisensi</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="LicenseName" id="LicenseName" placeholder="Contoh: Office 365" class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Lisensi Type</label>
                <div class="col-sm-4 form-error">
                  <select name="LicenseType" id="LicenseType" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Lisensi Key</label>
                <div class="col-sm-10 form-error">
                  <input type="text" name="LicenseKey" id="LicenseKey" placeholder="Contoh: XXXX-XXXX" class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Vendor</label>
                <div id="VendorListSelect" class="col-sm-10 form-error">
                  <select name="VendorList" id="VendorList" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tanggal Pembelian</label>
                <div class="col-sm-4 form-error">
                  <input type="date" id="PurchaseDate" name="PurchaseDate" class="form-control" required="required text-lowercase" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal Expired</label>
                <div class="col-sm-4 form-error">
                  <input type="date" id="ExpiryDate" name="ExpiryDate" class="form-control" required="required text-lowercase" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jumlah Akun</label>
                <div class="col-sm-4 form-error">
                  <input type="text" id="SeatsAllowed" name="SeatsAllowed" class="form-control" placeholder="Contoh: 10" maxlength="12" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error">
                  <select name="Status" id="Status" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <option value="Active">Active</option>
                    <option value="Expired">Expired</option>
                    <option value="Terminated">Terminated</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10">
                  <input type="text" name="Notes" id="Notes" placeholder="Contoh: Jika ada masukan keterangan" class="form-control" required="required" autocomplete="off">
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

    <!-- MODAL TRANSAKSI USER -->
    <div class="modal fade" id="modalTransaksi" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_trans()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formTransaksi">
              <input type="hidden" value="" name="TransID" id="TransID">
              <input type="hidden" value="" name="TransVendorID" id="TransVendorID">
              <input type="hidden" value="" name="TransVendorName" id="TransVendorName">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Lisensi</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransLicenseName" id="TransLicenseName" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Lisensi Type</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransLicenseType" id="TransLicenseType" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jumlah Akun</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransSeatsAllowed" id="TransSeatsAllowed" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransStatus" id="TransStatus" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-4 form-error">
                  <select name="TransType" id="TransType" class="form-control">
                    <option value="" disabled>-- Pilih --</option>
                    <option value="User" selected>User</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">TRANSAKSI</label>
              </div>
              <div id="userContainer">
                <div class="form-group row mb-2 mt-2" id="userRow1">
                  <div class="col-md-1 form-error mb-1">
                    <label class="col-form-label">Quantity</label>
                    <input type="text" value="1" name="TransQty[]" maxlength="12" readonly class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Departemen</label>
                    <select name="TransDept[]" class="form-control" required onchange="get_karyawan(this);">
                      <option value="" selected>-- Pilih --</option>
                      <?php foreach ($department_att as $value): ?>
                        <option value="<?= $value->DEPTID; ?>">
                          <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">User</label>
                    <select name="TransUserID[]" class="form-control" required>
                      <option value="" selected>-- Pilih --</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Keterangan</label>
                    <input type="text" name="TransNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan untuk" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-1 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div id="FooterDiv" class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnUpdate" type="button" onclick="save_transaksi_user();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL TRANSAKSI DEVICE -->
    <div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_device()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formDevice">
              <input type="hidden" value="" name="TransDeviceID" id="TransDeviceID">
              <input type="hidden" value="" name="TransDeviceVendorID" id="TransDeviceVendorID">
              <input type="hidden" value="" name="TransDeviceVendorName" id="TransDeviceVendorName">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Lisensi</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransDeviceLicenseName" id="TransDeviceLicenseName" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Lisensi Type</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransDeviceLicenseType" id="TransDeviceLicenseType" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Jumlah Akun</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransDeviceSeatsAllowed" id="TransDeviceSeatsAllowed" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error">
                  <input type="text" name="TransDeviceStatus" id="TransDeviceStatus" readonly class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Type</label>
                <div class="col-sm-4 form-error">
                  <select name="TransDeviceType" id="TransDeviceType" class="form-control">
                    <option value="" disabled>-- Pilih --</option>
                    <option value="Device" selected>Device</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">TRANSAKSI</label>
              </div>
              <div id="deviceContainer">
                <div class="form-group row mb-2 mt-2" id="deviceRow1">
                  <div class="col-md-1 form-error mb-1">
                    <label class="col-form-label">Quantity</label>
                    <input type="text" name="TransDeviceQty[]" maxlength="4" class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Departemen</label>
                    <select name="TransDeviceDept[]" class="form-control" required>
                      <option value="" selected>-- Pilih --</option>
                      <?php foreach ($department_att as $value): ?>
                        <option value="<?= $value->DEPTID; ?>">
                          <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Mesin</label>
                    <input type="text" name="TransDeviceMesin[]" id="TransDeviceMesin" class="form-control" placeholder="Masukan Nama Mesin">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Keterangan</label>
                    <input type="text" name="TransDeviceNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan untuk" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-1 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div id="FooterDevDiv" class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_device()">Close</button>
            <button id="btnUpdate" type="button" onclick="save_transaksi_device();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
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

      function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
      }

      function get_karyawan(el) {
        $.ajax({
          url : "<?php echo base_url();?>users/get_karyawan_dept",
          method : "POST",
          data : {id: el.value},
          dataType : 'json',
          success: function(data){
            var html = '<option value="" selected>-- Pilih --</option>';
            for (var i = 0; i < data.length; i++) {
              html += '<option value="'+ data[i].SSN +'">'+ data[i].NAME +'</option>';
            }
            $(el).closest('.form-group.row').find('select[name="TransUserID[]"]').html(html);
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      // TAMBAH KOLOM USER
      $(document).on('click', '#plus1', function () {
        let transType = $("#TransType").val();
        let count     = $('#userContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="userRow${count}">
            <div class="col-md-1 form-error mb-1">
              <label class="col-form-label">Quantity</label>
              <input type="text" value="1" name="TransQty[]" maxlength="12" readonly class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off">
              <input type="hidden" name="kodeTrans[]" value="">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Departemen</label>
              <select name="TransDept[]" class="form-control" required onchange="get_karyawan(this);">
                <option value="" selected>-- Pilih --</option>
                <?php foreach ($department_att as $value): ?>
                  <option value="<?= $value->DEPTID; ?>">
                    <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">User</label>
              <select name="TransUserID[]" class="form-control" required>
                <option value="" selected>-- Pilih --</option>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Keterangan</label>
              <input type="text" name="TransNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan oleh..." autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-user" title="Hapus Kolom">
                <span class="fa fa-minus"></span>
              </a>
            </div>
          </div>
        `;
        $('#userContainer').append(row);
      });

      // TAMBAH KOLOM DEVICE
      $(document).on('click', '#plus2', function () {
        let transType = $("#TransType").val();
        let count     = $('#deviceContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="deviceRow${count}">
            <div class="col-md-1 form-error mb-1">
              <label class="col-form-label">Quantity</label>
              <input type="text" name="TransDeviceQty[]" maxlength="12" class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off">
              <input type="hidden" name="kodeTrans[]" value="">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Departemen</label>
              <select name="TransDeviceDept[]" class="form-control" required onchange="get_karyawan(this);">
                <option value="" selected>-- Pilih --</option>
                <?php foreach ($department_att as $value): ?>
                  <option value="<?= $value->DEPTID; ?>">
                    <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">User</label>
              <input type="text" name="TransDeviceMesin[]" class="form-control" placeholder="Masukan Nama Mesin">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Keterangan</label>
              <input type="text" name="TransDeviceNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan oleh..." autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-device" title="Hapus Kolom">
                <span class="fa fa-minus"></span>
              </a>
            </div>
          </div>
        `;
        $('#deviceContainer').append(row);
      });

      // HAPUS KOLOM USER
      $(document).on('click', '.remove-kolom-user', function () {
        $(this).closest('.form-group').remove();
      });

      // HAPUS KOLOM DEVICE
      $(document).on('click', '.remove-kolom-device', function () {
        $(this).closest('.form-group').remove();
      });

      function reset_trans()
      {
        $('#userContainer').show();
        $('#FooterDiv').show();
        $('#modalTransaksi').modal('hide');
        $('#formTransaksi')[0].reset();
        $('#modalTransaksi .modal-title').text('Tambah User');
      }

      function reset_device()
      {
        $('#deviceContainer').show();
        $('#FooterDevDiv').show();
        $('#modalDevice').modal('hide');
        $('#formDevice')[0].reset();
        $('#modalDevice .modal-title').text('Tambah Device');

        $('#deviceContainer').html(`
          <div class="form-group row mb-2 mt-2" id="userRow1">
            <div class="col-md-1 form-error mb-1">
              <label class="col-form-label">Quantity</label>
              <input type="text" name="TransDeviceQty[]" maxlength="4" class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off">
              <input type="hidden" name="kodeTrans[]" value="">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Departemen</label>
              <select name="TransDeviceDept[]" class="form-control" required onchange="get_karyawan(this);">
                <option value="" selected>-- Pilih --</option>
                <?php foreach ($department_att as $value): ?>
                  <option value="<?= $value->DEPTID; ?>">
                    <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Mesin</label>
              <input type="text" name="TransDeviceMesin[]" id="TransDeviceMesin" class="form-control" placeholder="Masukan Nama Mesin">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Keterangan</label>
              <input type="text" name="TransDeviceNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan oleh..." autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      function reset_all() 
      {
        $('#modalForm').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Lisensi');

        $('#userContainer').html(`
          <div class="form-group row mb-2 mt-2" id="userRow1">
            <div class="col-md-1 form-error mb-1">
              <label class="col-form-label">Quantity</label>
              <input type="text" value="1" name="TransQty[]" maxlength="12" readonly class="form-control" oninput="AllowDecimalAndComma(this)" required placeholder="Contoh: 1" autocomplete="off">
              <input type="hidden" name="kodeTrans[]" value="">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Departemen</label>
              <select name="TransDept[]" class="form-control" required onchange="get_karyawan(this);">
                <option value="" selected>-- Pilih --</option>
                <?php foreach ($department_att as $value): ?>
                  <option value="<?= $value->DEPTID; ?>">
                    <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">User</label>
              <select name="TransUserID[]" class="form-control" required>
                <option value="" selected>-- Pilih --</option>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Keterangan</label>
              <input type="text" name="TransNotes[]" maxlength="150" class="form-control text-capitalize" required placeholder="Digunakan oleh..." autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      //FUNCTION OPEN MODAL
      function openModal() 
      {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Lisensi'); // Set Title to Bootstrap modal title
        $('#VendorList').val(null).trigger('change');
      }

      //FUNCTION CLOSE MODAL
      function closeModal() 
      {
        $('#RegisterValidation')[0].reset();
        $('#modalForm').modal('hide');
        $('.modal-title').text('Tambah Lisensi');
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
          url: "<?php echo base_url(); ?>lisensi/lisensi_edit/" + id,
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
              if ($('[name="VendorList"] option[value="' + data.VendorID + '"]').length > 0) {
                $('[name="VendorList"]').val(data.VendorID).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.VendorName, data.VendorID, true, true);
                $('[name="VendorList"]').append(newOption).trigger('change');
              }

              $('[name="kode"]').val(data.Id);
              $('[name="LicenseName"]').val(data.LicenseName);
              $('[name="LicenseType"]').val(data.LicenseType);
              $('[name="LicenseKey"]').val(data.LicenseKey);
              $('[name="PurchaseDate"]').val(data.PurchaseDate);
              $('[name="ExpiryDate"]').val(data.ExpiryDate);
              $('[name="SeatsAllowed"]').val(data.SeatsAllowed);
              $('[name="Status"]').val(data.Status);
              $('[name="Notes"]').val(data.Notes);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Lisensi');
              $('#btnSave').text('Update');
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
              url: '<?php echo base_url(); ?>lisensi/lisensi_deleted/' + id,
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

      //FUNCTION TRANSAKSI LISENSI
      function openModalTransaksi(Id, LicenseName, SeatsAllowed, VendorId, VendorName, LicenseType, Status)
      {
        $('#formTransaksi')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modalTransaksi').modal('show');
        $('#modalTransaksi .modal-title').text('Tambah User ' + LicenseName);

        $.ajax({
          url: "<?php echo base_url(); ?>lisensi/transaksi_cek",
          type: "POST",
          dataType: "JSON",
          data: {
            IdLisensi: Id,
            Type: 'User'
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              var html  = '';

              $('#TransID').val(Id);
              $('#TransVendorID').val(VendorId);
              $('#TransVendorName').val(VendorName);
              $('#TransLicenseName').val(LicenseName);
              $('#TransLicenseTypeHidden').val(ucfirst(LicenseType));
              $('#TransLicenseType').val(ucfirst(LicenseType));
              $('#TransSeatsAllowed').val(SeatsAllowed);
              $('#TransStatus').val(ucfirst(Status));

              if (ucfirst(Status) == 'Active') {
                $('#FooterDiv').show();
              } else {
                $('#FooterDiv').hide();
              }

              if (data.status_code == 200) {
                let AssignedType = data.data[0].AssignedType;
                $('#TransType').val(AssignedType);
                data.data.forEach((item, index) => {
                  let rowNumber = index + 1;
                  html += `
                    <div class="form-group row mb-2 mt-2" id="userRow${rowNumber}">
                      <div class="col-md-1 form-error">
                        <label class="col-form-label">Quantity</label>
                        <input type="text" value="${item.Quantity}" name="TransQty[]" maxlength="12" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Contoh: 1" readonly>
                        <input type="hidden" name="kodeTrans[]" value="${item.Id}">
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">Departemen</label>
                        <select name="TransDept[]" class="form-control" required="" onchange="get_karyawan(this);">
                          <option disabled>-- Pilih --</option>
                          <option selected value="${item.AssignedDeptID}">${item.AssignedDeptName}</option>
                        </select>
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">User</label>
                        <select name="TransUserID[]" class="form-control" required="">
                          <option disabled>-- Pilih --</option>
                          <option selected value="${item.AssignedID}">${item.AssignedName}</option>
                        </select>
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">Keterangan</label>
                        <input type="text" name="TransNotes[]" value="${item.Notes}" class="form-control" required placeholder="Contoh: Rev. 01 dst.">
                      </div>
                      <div class="col-md-2 button-center">
                        ${rowNumber == 1 
                          ? `<a href="javascript:void(0)" class="btn btn-danger text-bottom mr-2" onclick="hapusRow('userRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a> <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                          : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRow('userRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>`
                        }
                      </div>
                    </div>
                  `;
                });

                $('#userContainer').html(html);
              } else {
                reset_all();
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      function openModalDevice(Id, LicenseName, SeatsAllowed, VendorId, VendorName, LicenseType, Status)
      {
        $('#formDevice')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modalDevice').modal('show');
        $('#modalDevice .modal-title').text('Tambah Device ' + LicenseName);

        $.ajax({
          url: "<?php echo base_url(); ?>lisensi/transaksi_cek",
          type: "POST",
          dataType: "JSON",
          data: {
            IdLisensi: Id,
            Type: 'Device'
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              var html  = '';

              $('#TransDeviceID').val(Id);
              $('#TransDeviceVendorID').val(VendorId);
              $('#TransDeviceVendorName').val(VendorName);
              $('#TransDeviceLicenseName').val(LicenseName);
              $('#TransDeviceLicenseType').val(ucfirst(LicenseType));
              $('#TransDeviceSeatsAllowed').val(SeatsAllowed);
              $('#TransDeviceStatus').val(ucfirst(Status));

              if (ucfirst(Status) == 'Active') {
                $('#FooterDevDiv').show();
              } else {
                $('#FooterDevDiv').hide();
              }

              if (data.status_code == 200) {
                let AssignedType = data.data[0].AssignedType;
                $('#TransDeviceType').val(AssignedType);
                data.data.forEach((item, index) => {
                  let rowNumber = index + 1;
                  html += `
                    <div class="form-group row mb-2 mt-2" id="deviceRow${rowNumber}">
                      <div class="col-md-1 form-error">
                        <label class="col-form-label">Quantity</label>
                        <input type="text" value="${item.Quantity}" name="TransDeviceQty[]" maxlength="12" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Contoh: 1" readonly>
                        <input type="hidden" name="kodeTrans[]" value="${item.Id}">
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">Departemen</label>
                        <select name="TransDeviceDept[]" class="form-control" required="" onchange="get_karyawan(this);">
                          <option disabled>-- Pilih --</option>
                          <option selected value="${item.AssignedDeptID}">${item.AssignedDeptName}</option>
                        </select>
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">User</label>
                        <input type="text" name="TransDeviceMesin[]" value="${item.AssignedName}" class="form-control" placeholder="Masukan Nama Mesin">
                      </div>
                      <div class="col-md-3 form-error mb-1">
                        <label class="col-form-label">Keterangan</label>
                        <input type="text" name="TransDeviceNotes[]" value="${item.Notes}" class="form-control" required placeholder="Contoh: Rev. 01 dst.">
                      </div>
                      <div class="col-md-2 button-center">
                        ${rowNumber == 1 
                          ? `<a href="javascript:void(0)" class="btn btn-danger text-bottom mr-2" onclick="hapusRow('deviceRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a> <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                          : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRow('deviceRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>`
                        }
                      </div>
                    </div>
                  `;
                });

                $('#deviceContainer').html(html);
              } else {
                reset_all();
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCITON HAPUS SINGLE ROW
      function hapusRow(rowId)
      {
        const row             = $('#' + rowId);
        // Ambil data sebelum dihapus
        const kodeTrans       = row.find('input[name="kodeTrans[]"]').val();
        let Id                = $('#TransID').val();
        let LicenseName       = $('#TransLicenseName').val();
        let SeatsAllowed      = $('#TransSeatsAllowed').val();
        let LicenseType       = $('#TransLicenseType').val();
        let Status            = $('#TransStatus').val();
        let VendorId          = $('#TransVendorID').val();
        let VendorName        = $('#TransVendorName').val();
        let TransSeatsAllowed = $('#TransSeatsAllowed').val();
        

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
              url: "<?php echo base_url(); ?>lisensi/transaksi_delete_row",
              type: "POST",
              dataType: "JSON",
              data: {
                IdDetail: kodeTrans
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
                  openModalTransaksi(Id, LicenseName, SeatsAllowed, VendorId, VendorName, LicenseType, Status);
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

      //FUNCTION SAVE AND UPDATE
      function save() 
      {
        var form_data = $('#RegisterValidation').serializeArray();

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>lisensi/lisensi_add";
        } else {
          url = "<?php echo base_url(); ?>lisensi/lisensi_update";
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

      //FUNCTION SAVE TRANSAKSI USER
      function save_transaksi_user() 
      {
        var form_data = $('#formTransaksi').serializeArray();
        // ambil semua text dari option TransDept[]
        var deptText = $('select[name="TransDept[]"] option:selected').map(function () {
          return $(this).text().trim();
        }).get();

        // ambil semua text dari option TransUserID[]
        var userText = $('select[name="TransUserID[]"] option:selected').map(function () {
          return $(this).text().trim();
        }).get();

        // tambahkan ke form_data
        deptText.forEach(function (val) {
          form_data.push({
              name: 'TransDeptText[]',
              value: val
          });
        });

        userText.forEach(function (val) {
          form_data.push({
              name: 'TransUserText[]',
              value: val
          });
        });

        $.ajax({
          url: "<?php echo base_url(); ?>lisensi/transaksi_user_add",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdate").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modalTransaksi').modal('hide');
              $('#formTransaksi')[0].reset();
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

            $("#btnUpdate").text('Update');
            $("#btnUpdate").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdate').text('Update');
            $('#btnUpdate').prop('disabled', false);
          }
        });
      }

      //FUNCTION SAVE TRANSAKSI DEVICE
      function save_transaksi_device()
      {
        var form_data = $('#formDevice').serializeArray();
        // ambil semua text dari option TransDept[]
        var deptText = $('select[name="TransDeviceDept[]"] option:selected').map(function () {
          return $(this).text().trim();
        }).get();

        // tambahkan ke form_data
        deptText.forEach(function (val) {
          form_data.push({
              name: 'TransDeptText[]',
              value: val
          });
        });

        $.ajax({
          url: "<?php echo base_url(); ?>lisensi/transaksi_device_add",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdate").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modalDevice').modal('hide');
              $('#formDevice')[0].reset();
              reload_table();
              reset_device();
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

            $("#btnUpdate").text('Update');
            $("#btnUpdate").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdate').text('Update');
            $('#btnUpdate').prop('disabled', false);
          }
        });
      }

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
              pageSize: 'A2',
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
                    text: 'LAPORAN DAFTAR LISENSI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0, 7, 8, 9];
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

                return 'LAPORAN DAFTAR LISENSI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>lisensi/lisensi_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "TYPE": "TYPE" , "sClass": "text-center", "width": "50px" },
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "70px" },
            { "NAMA LISENSI": "NAMA LISENSI" , "sClass": "text-left", "width": "80px" },
            { "KEY": "KEY" , "sClass": "text-left", "width": "80px" },
            { "VENDOR": "VENDOR" , "sClass": "text-left", "width": "80px" },
            { "TGL. PEMBELIAN": "TGL. PEMBELIAN" , "sClass": "text-center", "width": "100px" },
            { "TGL. EXPIRED": "TGL. EXPIRED" , "sClass": "text-center", "width": "100px" },
            { "JUMLAH AKUN": "JUMLAH AKUN" , "sClass": "text-right", "width": "100px" },
            { "JUMLAH TERPAKAI": "JUMLAH TERPAKAI" , "sClass": "text-right", "width": "100px" },
            { "SISA": "SISA" , "sClass": "text-right", "width": "50px" },
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "80px" },
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

        $('#modalForm').on('shown.bs.modal', function () {
          $('#VendorList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Nama Vendor",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>lisensi/get_all_vendor',
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
                      id: item.Id,
                      text: item.VendorName,
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          }).on('select2:select', function (e) {
            $('#VendorListSelect.has-error').removeClass('has-error').find('span.help-block').text('');
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#VendorList').select2('destroy');
        });

        // Event handler gabungan
        $(document).on("change keyup", 
          "[name='TransType'], #userContainer [name='TransQty[]'], #userContainer [name='TransDept[]'], #userContainer [name='TransUserID[]'], #userContainer [name='TransNotes[]']", 
          function() {
            // hapus class has-error dari parent terdekat
            $(this).closest(".form-error").removeClass("has-error");

            // hapus pesan error di dalam parent
            $(this).closest(".form-error").find(".help-block").empty();
          }
        );

        $("#LicenseName, #LicenseType, #LicenseKey, #PurchaseDate, #ExpiryDate, #SeatsAllowed, #Status").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>