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
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-3">Filter by</label>
                                <div class="col-md-2 col-sm-12 m-t-3">
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
                                <div class="col-md-2 col-sm-12 m-t-3">
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
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="250%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="3">NO</th>
                                      <th class="text-center" rowspan="3">#</th>
                                      <th class="text-center" rowspan="3">TGL. JOB</th>
                                      <th class="text-center" rowspan="3">TYPE</th>
                                      <th class="text-center" rowspan="3">PART ID</th>
                                      <th class="text-center" rowspan="3">NO. JOB</th>
                                      <th class="text-center" rowspan="3">QTY. JOB (M)</th>
                                      <th class="text-center" rowspan="3">MATERIAL PVC (KG)</th>
                                      <th class="text-center" rowspan="3">TGL. PROSES</th>
                                      <th class="text-center" colspan="3">QUANTITY</th>
                                      <th class="text-center" rowspan="3">%</th>
                                      <th class="text-center bg-danger" colspan="7">NG EXTRUDE</th>
                                      <th class="text-center" rowspan="3">(+) / (-)</th>
                                      <th class="text-center" rowspan="3">TOTAL PRODUKSI</th>
                                      <th class="text-center" rowspan="3">REMARKS</th>
                                      <th class="text-center" rowspan="3">CREATE DATE</th>
                                      <th class="text-center" rowspan="3">UNIT ID</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center" rowspan="2">PLAN</th>
                                      <th class="text-center" rowspan="2">ACTUAL</th>
                                      <th class="text-center" rowspan="2">FINISH GOODS</th>
                                      <th class="text-center bg-danger" rowspan="2">TGL</th>
                                      <th class="text-center bg-danger" colspan="4">KABEL</th>
                                      <th class="text-center bg-danger" colspan="2">PVC</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center bg-danger">QTY (KG)</th>
                                      <th class="text-center bg-danger">%</th>
                                      <th class="text-center bg-danger">QTY (MTR)</th>
                                      <th class="text-center bg-danger">%</th>
                                      <th class="text-center bg-danger">QTY (KG)</th>
                                      <th class="text-center bg-danger">%</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr class="bg-primary text-white">
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                      <th class="font-weight-bold"></th>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                              <hr>
                              <h6 class="font-weight-bold mt-2 mb-2">TOTAL SHEATING EXTRUDE</h6>
                              <table id="Table2" class="table table-striped table-bordered" style="width: 70%;">
                                <thead>
                                  <tr class="bg-primary text-white">
                                    <th width="5%" class="text-center">NO</th>
                                    <th width="50%" class="text-center">TYPE</th>
                                    <th width="10%" class="text-center">JUMLAH</th>
                                    <th width="10%" class="text-center">SATUAN</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                </tbody>
                                <tfoot>
                                  <tr class="bg-primary text-white">
                                    <th class="text-right" colspan="2">GRAND TOTAL</th>
                                    <th id="TotalGT">0</th>
                                    <th></th>
                                  </tr>
                                </tfoot>
                              </table>
                              <table id="Table1" class="table table-striped table-bordered" style="width: 70%;">
                                <tbody>
                                  <tr>
                                    <td width="6%" class="text-right">1</td>
                                    <td width="50%">QTY JOB</td>
                                    <td width="10%" id="LblTotalJob" class="text-right">0</td>
                                    <td width="10%" class="text-center">MTR</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">2</td>
                                    <td>QTY PVC</td>
                                    <td id="LblTotalPvc" class="text-right">0</td>
                                    <td class="text-center">KG</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">3</td>
                                    <td>PRODUKSI OK</td>
                                    <td id="LblTotalProduksi" class="text-right">0</td>
                                    <td class="text-center">M</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">4</td>
                                    <td>NG KABEL (KG)</td>
                                    <td id="LblTotalBeratKabelNG" class="text-right">0</td>
                                    <td class="text-center">KG</td>
                                  </tr>
                                  <tr>  
                                    <td class="text-right">5</td>
                                    <td>NG KABEL (MTR)</td>
                                    <td id="LblTotalPanjangKabelNG" class="text-right">0</td>
                                    <td class="text-center">MTR</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">6</td>
                                    <td>NG  PVC (KG)</td>
                                    <td id="LblTotalBeratPvcNG" class="text-right">0</td>
                                    <td class="text-center">KG</td>
                                  </tr>
                                </tbody>
                              </table>
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
            <form action="" method="post" id="sheatingForm">
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
                <div class="col-sm-4 form-error mb-2">
                  <select name="JobList" id="JobList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Part ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartID" id="PartID" class="form-control" required="required" placeholder="Part ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartName" id="PartName" class="form-control" required="required" placeholder="Part Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Job Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="JobDate" id="JobDate" class="form-control" required="required" placeholder="Tanggal Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Job Quantity</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="JobQuantity" id="JobQuantity" class="form-control" required="required" placeholder="Quantity Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="UnitID" id="UnitID" class="form-control" required="required" placeholder="Unit ID" autocomplete="off" maxlength="12" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Remark</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="Remark" id="Remark" class="form-control text-uppercase" required="required" placeholder="Keterangan" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Material PVC (KG)</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PvcWeight" id="PvcWeight" class="form-control" required="required" placeholder="Berat PVC" autocomplete="off" oninput="AllowDecimalAndComma(this)" maxlength="12">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">JUMLAH ITEM PER TRANSAKSI</label>
              </div>
              <div id="jumlahContainer">
                <div class="form-group row mb-2 mt-2" id="jumlahRow1">
                  <div class="col-md-2"></div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal Proses</label>
                    <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Plan</label>
                    <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Actual</label>
                    <input type="text" name="ActualQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">NG ITEM PER TRANSAKSI</label>
              </div>
              <div id="ngContainer">
                <div class="form-group row mb-2 mt-2" id="ngRow1">
                  <div class="col-md-2"></div>
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal Proses</label>
                    <input type="date" name="TanggalNG[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Kabel (KG)</label>
                    <input type="text" name="BeratKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Kabel (M)</label>
                    <input type="text" name="PanjangKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Meter" autocomplete="off">
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">PVC (KG)</label>
                    <input type="text" name="BeratPvcNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
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

    <div id="loading" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript">
      var save_method;
      var url;

      function reset_all() {
        $('#sheatingForm .form-group .has-error').removeClass('has-error');
        $('#sheatingForm')[0].reset();
        $('#modalForm').modal('hide');
        $('.modal-title').text('Tambah Data');

        $('#jumlahContainer').html(`
          <div class="form-group row mb-2 mt-2" id="jumlahRow1">
            <div class="col-md-2"></div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan</label>
              <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Actual</label>
              <input type="text" name="ActualQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);

        $('#ngContainer').html(`
          <div class="form-group row mb-2 mt-2" id="ngRow1">
            <div class="col-md-2"></div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalNG[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeThird[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Kabel (KG)</label>
              <input type="text" name="BeratKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Kabel (M)</label>
              <input type="text" name="PanjangKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">PVC (KG)</label>
              <input type="text" name="BeratPvcNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#sheatingForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Data');
        $('#JobList').val(null).trigger('change');
      }

      //FUNCTION RESET
      function reset() {
        $('#sheatingForm')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //SAVE HEADER
      function save() 
      {
        var form_data = $("#sheatingForm").serialize();

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>sheating/save_data";
        } else {
          url = "<?php echo base_url(); ?>sheating/update_data";
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
              $('#sheatingForm')[0].reset();
              reset_all();
              reload_table();
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
                var errorMsg = data.error_string[i];

                var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                if (arrayMatch) {
                  var arrayName = arrayMatch[1];
                  var arrayIndex = parseInt(arrayMatch[2]);
                  var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                  inputElem.closest('.form-error').addClass('has-error');
                  if (inputElem.next('.help-block').length === 0) {
                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                  }
                } else {
                  var inputElem = $('[name="' + inputName + '"]');
                  inputElem.closest('.form-error').addClass('has-error');
                  if (inputElem.next('.help-block').length === 0) {
                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
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
      }

      //FUNCTION EDIT
      function edit(JobNumbers) 
      {
        save_method = 'update';
        $('#sheatingForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>sheating/edit_data",
          type: "POST",
          dataType: "JSON",
          data: {
            JobNumber: JobNumbers
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
              $('[name="Remark"]').val(data.first.Notes);
              $('[name="PvcWeight"]').val(data.first.PvcWeight.replaceAll(" ", ""));
              $('[name="Periode"]').val(data.first.JobPeriode);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Data #' + JobNumbers);
              $('#btnSave').text('Update');
              
              data.second.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-2" id="jumlahRow${rowNumber}">
                    <div class="col-md-2"></div>
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Tanggal Proses</label>
                      <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required value="${item.ProcessDate}">
                      <input type="hidden" name="kodeSecond[]" value="${item.Id}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Plan</label>
                      <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required value="${item.PlanQty.replace(',', '.')}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Actual</label>
                      <input type="text" name="ActualQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required value="${item.ActualQty.replace(',', '.')}">
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

              $('#jumlahContainer').html(html);

              data.third.forEach((item, index) => {
                let rowNumber = index + 1;
                html2 += `
                  <div class="form-group row mb-2 mt-2" id="ngRow${rowNumber}">
                    <div class="col-md-2"></div>
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Tanggal Proses</label>
                      <input type="date" name="TanggalNG[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off" value="${item.ProcessDate}">
                      <input type="hidden" name="kodeThird[]" value="${item.Id}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Kabel (KG)</label>
                      <input type="text" name="BeratKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off" value="${item.BeratKabel.replace('.', ',')}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Kabel (M)</label>
                      <input type="text" name="PanjangKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off" value="${item.PanjangKabel.replace('.', ',')}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">PVC (KG)</label>
                      <input type="text" name="BeratPvcNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off" value="${item.BeratPvc.replace('.', ',')}">
                    </div>
                    <div class="col-md-2 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRowNg('ngRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#ngRow${rowNumber}').remove()"
                      }
                    </div>
                  </div>
                `;
              });

              $('#ngContainer').html(html2);
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
              url: "<?php echo base_url(); ?>sheating/hapus_row_jumlah",
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
                edit(jobNumber);
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

      function hapusRowNg(rowId)
      {
        const row         = $('#' + rowId);
        // Ambil data sebelum dihapus
        const jobNumber   = $('#JobList').val();
        const kodeThird   = row.find('input[name="kodeThird[]"]').val();

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
              url: "<?php echo base_url(); ?>sheating/hapus_row_ng",
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
                edit(jobNumber);
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
      function hapusAll(jobNumber) 
      {
        Swal.fire({
          title: 'Hapus ' + jobNumber + ' ?',
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
              url: '<?php echo base_url(); ?>sheating/hapus_all',
              type: 'POST',
              data: {
                JobNumber: jobNumber
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
                  Swal.fire({
                    title: "Sukses",
                    text: result.message,
                    icon: "success"
                  });
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

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      // TAMBAH KOLOM JUMLAH
      $(document).on('click', '#plus1', function () {
        let count = $('#jumlahContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="jumlahRow${count}">
            <div class="col-md-2"></div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Plan</label>
              <input type="text" name="PlanQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Actual</label>
              <input type="text" name="ActualQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#jumlahContainer').append(row);
      });

      $(document).on('click', '#plus2', function () {
        let count = $('#ngContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="ngRow${count}">
            <div class="col-md-2"></div>
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalNG[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeThird[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Kabel (KG)</label>
              <input type="text" name="BeratKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Kabel (M)</label>
              <input type="text" name="PanjangKabelNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">PVC (KG)</label>
              <input type="text" name="BeratPvcNG[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Berat" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-ng" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;

        $('#ngContainer').append(row);
      });

      // HAPUS KOLOM JUMLAH
      $(document).on('click', '.remove-kolom-jumlah', function () {
        $(this).closest('.form-group').remove();
      });

      $(document).on('click', '.remove-kolom-ng', function () {
        $(this).closest('.form-group').remove();
      });

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#myTable').DataTable({
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
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23]
              },
              customize: function (doc) {
                  const month = $('#Months').find('option:selected').text().toUpperCase();
                  const year = $('#Years').val();

                  function formatRibuan(num) {
                      if (num === null || num === undefined) return '0';

                      if (typeof num === 'number') {
                          return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                      }

                      let str = num.toString();
                      const cleaned = str.replace(/[^\d.,-]/g, '');
                      const normalized = cleaned.replace(',', '.');
                      const n = parseFloat(normalized);

                      if (isNaN(n)) return str;

                      return n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }

                  doc.defaultStyle.fontSize = 10;
                  doc.pageMargins           = [10, 40, 10, 50];
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
                          text: 'LAPORAN CONTROL JOB SHEATING EXTRUDE',
                          bold: true,
                          fontSize: 14,
                          style: 'subheader',
                          alignment: 'center',
                          margin: [0, 0, 0, 10]
                      },
                      {
                        text: 'PERIODE : ' + month + ' ' + year,
                        bold: true,
                        fontSize: 12,
                        style: 'subheader',
                        alignment: 'left',
                        margin: [0, 0, 0, 10]
                      }
                  );

                  // === Table 2 ===
                  const summaryTable = [
                      [
                          { text: 'NO', style: 'tableHeader' },
                          { text: 'TYPE', style: 'tableHeader' },
                          { text: 'JUMLAH', style: 'tableHeader' },
                          { text: 'SATUAN', style: 'tableHeader' }
                      ]
                  ];

                  $('#Table2 tbody tr').each(function () {
                      const cells = $(this).find('td');
                      if (cells.length === 4) {
                          let valText = $(cells[2]).text().trim();
                          let valNum = parseFloat(valText.replace(/[^\d.-]/g, '')) || 0;
                          valNum = valNum * 1000;

                          summaryTable.push([
                              { text: $(cells[0]).text(), alignment: 'right' },
                              { text: $(cells[1]).text(), alignment: 'left' },
                              { text: formatRibuan(valNum), alignment: 'right' },
                              { text: $(cells[3]).text(), alignment: 'center' }
                          ]);
                      }
                  });

                  let grandTotalJumlahNum = 0;
                  let grandTotalSatuan = '-';
                  const tfootCells = $('#Table2 tfoot tr th');

                  if (tfootCells.length === 4) {
                      let grandTotalJumlahText = $(tfootCells[2]).text().trim();
                      if (grandTotalJumlahText && !isNaN(parseFloat(grandTotalJumlahText.replace(/[^\d.-]/g, '')))) {
                          grandTotalJumlahNum = parseFloat(grandTotalJumlahText.replace(/[^\d.-]/g, ''));
                      } else {
                          $('#Table2 tbody tr').each(function () {
                              let val = $(this).find('td').eq(2).text().trim();
                              let valNum = parseFloat(val.replace(/[^\d.-]/g, '')) || 0;
                              grandTotalJumlahNum += valNum;
                          });
                      }
                      grandTotalJumlahNum = grandTotalJumlahNum * 1000;
                      grandTotalSatuan = $(tfootCells[3]).text().trim() || '-';
                  } else {
                      $('#Table2 tbody tr').each(function () {
                          let val = $(this).find('td').eq(2).text().trim();
                          let valNum = parseFloat(val.replace(/[^\d.-]/g, '')) || 0;
                          grandTotalJumlahNum += valNum;

                          let sat = $(this).find('td').eq(3).text().trim();
                          if (sat) grandTotalSatuan = sat;
                      });
                      grandTotalJumlahNum = grandTotalJumlahNum * 1000;
                  }

                  summaryTable.push([
                      { text: 'GRAND TOTAL', alignment: 'right', bold: true, colSpan: 2 },
                      {},
                      { text: formatRibuan(grandTotalJumlahNum), alignment: 'right', bold: true },
                      { text: grandTotalSatuan, alignment: 'center' }
                  ]);

                  doc.content.push(
                      {
                          text: 'TOTAL SHEATING EXTRUDE',
                          style: 'subheader',
                          margin: [0, 20, 0, 8]
                      },
                      {
                          columns: [
                              {
                                  width: '40%',
                                  alignment: 'center',
                                  table: {
                                      headerRows: 1,
                                      widths: ['10%', '*', '20%', '15%'],
                                      body: summaryTable
                                  },
                                  layout: {
                                      hLineWidth: () => 0.5,
                                      vLineWidth: () => 0.5,
                                      hLineColor: () => '#aaa',
                                      vLineColor: () => '#aaa',
                                      paddingLeft: () => 2,
                                      paddingRight: () => 2,
                                      paddingTop: () => 2,
                                      paddingBottom: () => 2,
                                      fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                                  }
                              }
                          ],
                          columnGap: 10
                      }
                  );

                  // === Table 1 ===
                  const summary1Table = [
                      [
                          { text: 'NO', style: 'tableHeader' },
                          { text: '#', style: 'tableHeader' },
                          { text: 'JUMLAH', style: 'tableHeader' },
                          { text: 'SATUAN', style: 'tableHeader' }
                      ]
                  ];

                  $('#Table1 tbody tr').each(function () {
                      const cells = $(this).find('td');
                      if (cells.length === 4) {
                          summary1Table.push([
                              { text: $(cells[0]).text(), alignment: 'right' },
                              { text: $(cells[1]).text(), alignment: 'left' },
                              { text: $(cells[2]).text(), alignment: 'right' },
                              { text: $(cells[3]).text(), alignment: 'center' }
                          ]);
                      }
                  });

                  doc.content.push(
                      {
                          text: 'TOTAL RINGKASAN',
                          style: 'subheader',
                          margin: [0, 20, 0, 8]
                      },
                      {
                          columns: [
                              {
                                  width: '40%',
                                  alignment: 'center',
                                  table: {
                                      headerRows: 1,
                                      widths: ['10%', '*', '20%', '15%'],
                                      body: summary1Table
                                  },
                                  layout: {
                                      hLineWidth: () => 0.5,
                                      vLineWidth: () => 0.5,
                                      hLineColor: () => '#aaa',
                                      vLineColor: () => '#aaa',
                                      paddingLeft: () => 2,
                                      paddingRight: () => 2,
                                      paddingTop: () => 2,
                                      paddingBottom: () => 2,
                                      fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                                  }
                              }
                          ],
                          columnGap: 10
                      }
                  );

                  // === Main Table Styling ===
                  const mainTable = doc.content.find(item => item.table);
                  if (mainTable) {
                      const alignRightCols = [0, 5, 6, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20];
                      const body = mainTable.table.body;

                      for (let i = 1; i < body.length; i++) {
                          for (let j = 0; j < body[i].length; j++) {
                              if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                  body[i][j].alignment = 'right';
                              }
                          }

                          // Tambahkan styling khusus untuk baris SUB TOTAL
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

                          // Tambahkan styling khusus untuk baris TOTAL
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

                      // Style baris terakhir (misal GRAND TOTAL)
                      const lastRowIndex = body.length - 1;
                      for (let j = 0; j < body[lastRowIndex].length; j++) {
                          if (body[lastRowIndex][j].text !== undefined) {
                              body[lastRowIndex][j].fillColor = '#007bff';
                              body[lastRowIndex][j].color = '#fff';
                          }
                      }

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
                var month = $('#Months').find('option:selected').text().toUpperCase();
                var year  = $('#Years').val();

                return 'Laporan Control Job Sheating Extrude Periode ' + month + ' ' + year;
              }
            }
          ],
          select: {
            style: 'single'
          },
          "pagingType": "full_numbers",
          "lengthMenu": [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"]
          ],
          "displayLength": 5,
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
            "url": "<?php echo base_url(); ?>sheating/list_data",
            "type": "POST",
            "data": function(data) {
              data.Months    = $('#Months').val();
              data.Years     = $('#Years').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-right", "width": "50px"},
            { "TGL. JOB": "TGL. JOB" , "sClass": "text-center", "width": "470px" },
            { "TYPE": "TYPE" , "sClass": "text-left", "width": "180px" },
            { "PART ID": "PART ID" , "sClass": "text-left", "width": "100px" },
            { "NO. JOB": "NO. JOB" , "sClass": "text-left", "width": "245px" },
            { "QTY. JOB": "QTY. JOB" , "sClass": "text-right", "width": "100px" },
            { "MATERIAL PVC (KG)": "MATERIAL PVC (KG)" , "sClass": "text-right", "width": "100px" },
            { "TGL. PROSES": "TGL. PROSES" , "sClass": "text-right", "width": "120px" },
            { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
            { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "150px" }, // 10
            { "FINISH GOODS": "FINISH GOODS" , "sClass": "text-right", "width": "150px" },
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "TGL": "TGL" , "sClass": "text-left" },
            { "KABEL QTY (KG)": "QTY (KG)" , "sClass": "text-right", "width": "150px" },
            { "%": "%" , "sClass": "text-right", "width": "150px" },
            { "KABEL QTY (MTR)": "QTY (MTR)" , "sClass": "text-right", "width": "150px" },
            { "%": "%" , "sClass": "text-right", "width": "150px" },
            { "PVC QTY (KG)": "PVC QTY (KG)" , "sClass": "text-right", "width": "150px" },
            { "%": "%" , "sClass": "text-right", "width": "150px" },
            { "(+)/(-)": "(+)/(-)" , "sClass": "text-right", "width": "150px" }, //20
            { "FINISH GOODS": "FINISH GOODS" , "sClass": "text-right", "width": "150px" },
            { "REMARKS": "REMARKS" , "sClass": "text-left", "width": "150px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" },
            { "UNIT ID": "UNIT ID" , "sClass": "text-left", "width": "10px" }
          ],
          "footerCallback": function (row, data, start, end, display) {
            var api = this.api();

            var parseLocaleFloatCustom = function(i) {
                if (typeof i === 'string') {
                    var cleaned = i.replace(/<[^>]*>/g, '').trim();
                    if (cleaned === '') return 0;
                    var angkaDesimalSaja = cleaned.replace(/[^\d.,-]/g, '');
                    var formatted = angkaDesimalSaja.replace(/\./g, '').replace(',', '.');
                    var val = parseFloat(formatted);
                    return isNaN(val) ? 0 : val;
                } else if (typeof i === 'number') {
                    return i;
                } else {
                    return 0;
                }
            };

            // Kolom yang ingin dijumlahkan
            const targetColumns = [6, 7, 9, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21];

            let grandTotalSums  = {};
            targetColumns.forEach(function(colIdx) {
                grandTotalSums[colIdx] = 0;
            });

            // Ambil nilai subtotal
            api.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var rowData = this.data();
                if (rowData[2].toString().toUpperCase().includes("SUB TOTAL")) {
                    targetColumns.forEach(function(colIdx) {
                        grandTotalSums[colIdx] += parseLocaleFloatCustom(rowData[colIdx]);
                    });
                }
            });

            // Update nilai ke elemen di tabel lain
            const formatID = function(val) {
                return val.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                    useGrouping: true
                });
            };

            // Lempar nilai ke elemen target
            $('#LblTotalJob').text(formatID(grandTotalSums[6]));               // QTY JOB
            $('#LblTotalPvc').text(formatID(grandTotalSums[7]));               // QTY PVC
            $('#LblTotalProduksi').text(formatID(grandTotalSums[9]));          // PRODUKSI OK
            $('#LblTotalBeratKabelNG').text(formatID(grandTotalSums[14]));     // NG KABEL KG
            $('#LblTotalPanjangKabelNG').text(formatID(grandTotalSums[16]));   // NG KABEL MTR
            $('#LblTotalBeratPvcNG').text(formatID(grandTotalSums[18]));       // NG PVC KG

            // Tampilkan juga di footer DataTable
            api.columns().every(function() {
                var columnIdx = this.index();
                if (targetColumns.includes(columnIdx)) {
                    var total = grandTotalSums[columnIdx];
                    var formattedTotal = formatID(total);
                    $(this.footer()).html(formattedTotal);
                } else if (columnIdx === 5) {
                    $(this.footer()).html('TOTAL');
                } else {
                    $(this.footer()).html('');
                }
            });

            // =============================
            // KUMPULKAN SUB TOTAL KE TABLE2
            // =============================

            // Hapus isi Table2
            $('#Table2 tbody').empty();
            $('#Table2 tfoot').empty();

            let subtotalList  = [];
            let nomor         = 1;
            let grandTotal    = 0;

            api.rows().every(function(rowIdx, tableLoop, rowLoop) {
              var rowData = this.data();

              if (rowData[2].toString().toUpperCase().includes("SUB TOTAL")) {
                  var typePartName  = rowData[3];
                  var finishedGoods = parseLocaleFloatCustom(rowData[11]);
                  var unitID        = rowData[24];
                  grandTotal       += finishedGoods;
                  subtotalList.push({
                    no: nomor++,
                    partName: typePartName,
                    finishedGoods: finishedGoods,
                    satuan: unitID
                  });
              }
            });

            subtotalList.forEach(function(item) {
              $('#Table2 tbody').append(`
                <tr>
                  <td class="text-right">${item.no}</td>
                  <td class="text-left">${item.partName}</td>
                  <td class="text-right">${item.finishedGoods.toLocaleString('id-ID')}</td>
                  <td class="text-center">${item.satuan}</td>
                </tr>
              `);
            });

            $('#Table2 tfoot').append(`
              <tr class="bg-primary text-white">
                <th class="text-right" colspan="2">GRAND TOTAL</th>
                <th class="text-right">${grandTotal.toLocaleString('id-ID')}</th>
                <th class="text-center"></th>
              </tr>
            `);
          },
          "createdRow": function(row, data, dataIndex) {
            if (data[2].toString().toUpperCase().includes("TOTAL")) {
              // Tambahkan class ke seluruh baris (opsional)
              $(row).addClass('bg-secondary text-white');

              // Kolom 0, 1, dan 2 adalah kolom yang difreeze (left: 3)
              [0, 1, 2].forEach(function(i) {
                $('td', row).eq(i).addClass('bg-secondary');
              });
            }

            if (data[2] && data[2].toString().toUpperCase().includes("SUB TOTAL")) {
              $(row).addClass('font-weight-bold');
            }
          },
          columnDefs: [
            {
              targets: 24,   // index kolom terakhir UNIT ID
              visible: false
            }
          ]
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

        $("#PvcWeight").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#modalForm').on('shown.bs.modal', function () {
          $('#JobList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Nomor Job",
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>sheating/get_job_number',
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
                        text: item.name,
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
            $('#Remark').val(selectedData.Keterangan);
            $('#UnitID').val(selectedData.UnitID);

            $('.has-error').each(function() {
              $(this).removeClass('has-error');
              $(this).find('span.help-block').text('');
            });
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#JobList').select2('destroy');
        });

        $('#jumlahContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $('#ngContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });
      });
    </script>
  </body>
</html>