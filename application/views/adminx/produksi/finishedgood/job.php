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
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select class="form-control" name="Kategori" id="Kategori">
                                    <option value="ALL" selected>All</option>
                                    <option value="" disabled>-- Pilih --</option>
                                    <option value="M-001 KG">M-001 KG</option>
                                    <option value="M-003 KG">M-003 KG</option>
                                    <option value="MCM">MCM</option>
                                    <option value="UL">UL</option>
                                    <option value="WD">WD</option>
                                    <option value="M-003 & M-005 NON KG">M-003 & M-005 NON KG</option>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <span class="input-group-append">
                                      <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                    </span>
                                  </div>

                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                </div>
                                <!-- <div class="col-md-2 col-sm-12 m-t-3">
                                  <select class="form-control" name="Months" id="Months">
                                    <?php
                                      // $months = [
                                      //   "January", "February", "March", "April", "May", "June",
                                      //   "July", "August", "September", "October", "November", "December"
                                      // ];
                                      // $currentMonth = date('n'); // Get current month (1-12)

                                      // foreach ($months as $index => $month) {
                                      //   $value    = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                                      //   $selected = ($value == str_pad($currentMonth, 2, "0", STR_PAD_LEFT)) ? "selected" : "";
                                      //   echo "<option value='$value' $selected>$month</option>";
                                      // }
                                    ?>
                                  </select>
                                </div> -->
                                <!-- <div class="col-md-2 col-sm-12 m-t-3">
                                  <select class="form-control" name="Years" id="Years">
                                    <?php
                                      // $startYear    = 2025;
                                      // $endYear      = 2050;
                                      // $currentYear  = date('Y'); // Get current year

                                      // for ($year = $startYear; $year <= $endYear; $year++) {
                                      //   $selected = ($year == $currentYear) ? "selected" : "";
                                      //   echo "<option value='$year' $selected>$year</option>";
                                      // }
                                    ?>
                                  </select>
                                </div> -->
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-4 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="250%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="2">NO</th>
                                      <th class="text-center" rowspan="2">#</th>
                                      <th class="text-center" rowspan="2">TGL. JOB</th>
                                      <th class="text-center" rowspan="2">KATEGORI</th>
                                      <th class="text-center" rowspan="2">STATUS</th>
                                      <th class="text-center" rowspan="2">TYPE</th>
                                      <th class="text-center" rowspan="2">PART ID</th>
                                      <th class="text-center" rowspan="2">NO JOB</th>
                                      <th class="text-center" rowspan="2">QTY JOB</th>
                                      <th class="text-center" rowspan="2">UNIT ID</th>
                                      <th class="text-center" rowspan="2">TOTAL MASUK WH</th>
                                      <th class="text-center" rowspan="2">TGL. PROSES</th>
                                      <th class="text-center" colspan="2">JUMLAH</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">FINISHED GOOD</th>
                                      <th class="text-center" rowspan="2">SISA JOB</th>
                                      <th class="text-center" rowspan="2">KETERANGAN</th>
                                      <th class="text-center" rowspan="2">CREATED DATE</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">PRODUKSI</th>
                                      <th class="text-center">WH</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr class="bg-primary text-white">
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold text-center"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                      <td class="text-right font-weight-bold"></td>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                              <hr>
                              <h6 class="font-weight-bold mt-2 mb-2">REKAP TOTAL DATA</h6>
                              <table id="Table1" class="table table-striped table-bordered" style="width: 40%;">
                                <thead>
                                  <tr class="bg-primary">
                                    <th class="text-center">NO</th>
                                    <th class="text-center">#</th>
                                    <th class="text-center">JUMLAH</th>
                                    <th class="text-center">%</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="text-right">1</td>
                                    <td>JOB ALL</td>
                                    <td id="LblTotalJob" class="text-right">0</td>
                                    <td id="PersentaseJob" rowspan="2" class="text-center" style="vertical-align: middle"></td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">2</td>
                                    <td>PRODUKSI OK</td>
                                    <td id="LblTotalProduksi" class="text-right">0</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">3</td>
                                    <td>SISA JOB</td>
                                    <td id="LblSisaJob" class="text-right">0</td>
                                    <td class="text-center">Belum masuk WH</td>
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
                  <input type="text" name="UnitID" id="UnitID" class="form-control" required="required" placeholder="Unit ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="Remark" id="Remark" class="form-control text-uppercase" required="required" placeholder="Keterangan" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="Kategori" id="Kategori" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                    <option value="M-001 KG">M-001 KG</option>
                    <option value="M-003 KG">M-003 KG</option>
                    <option value="MCM">MCM</option>
                    <option value="UL">UL</option>
                    <option value="WD">WD</option>
                    <option value="M-003 & M-005 NON KG">M-003 & M-005 NON KG</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">JUMLAH ITEM PER TRANSAKSI</label>
              </div>
              <div id="jumlahContainer">
                <div class="form-group row mb-2 mt-2" id="jumlahRow1">
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Tanggal Proses</label>
                    <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Produksi</label>
                    <input type="text" name="ProductionQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">WH</label>
                    <input type="text" name="WarehouseQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
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
          <div class="form-group row mb-2 mt-2" id="jumlahRow1">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Produksi</label>
              <input type="text" name="ProductionQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">WH</label>
              <input type="text" name="WarehouseQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
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
      function save() 
      {
        var form_data = $("#fgForm").serialize();

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>finishedgood/save_data";
        } else {
          url = "<?php echo base_url(); ?>finishedgood/update_data";
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
        $('#fgForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>finishedgood/edit_data",
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
              $('[name="Kategori"]').val(data.first.Kategori);
              $('[name="Periode"]').val(data.first.JobPeriode);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Data #' + JobNumbers);
              $('#btnSave').text('Update');
              
              data.second.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-2" id="jumlahRow${rowNumber}">
                    <div class="col-md-2 form-error">
                      <label class="col-form-label">Tanggal Proses</label>
                      <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required value="${item.ProcessDate}">
                      <input type="hidden" name="kodeSecond[]" value="${item.Id}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Produksi</label>
                      <input type="text" name="ProductionQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required value="${item.ProductionQty.replace(',', '.')}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">WH</label>
                      <input type="text" name="WarehouseQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required value="${item.WarehouseQty.replace(',', '.')}">
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
              url: "<?php echo base_url(); ?>finishedgood/hapus_row_jumlah",
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
              url: "<?php echo base_url(); ?>finishedgood/hapus_row_ng",
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
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>finishedgood/hapus_all',
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
            <div class="col-md-2 form-error">
              <label class="col-form-label">Tanggal Proses</label>
              <input type="date" name="TanggalProses[]" class="form-control text-uppercase" required placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Produksi</label>
              <input type="text" name="ProductionQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">WH</label>
              <input type="text" name="WarehouseQty[]" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity" autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#jumlahContainer').append(row);
      });

      // HAPUS KOLOM JUMLAH
      $(document).on('click', '.remove-kolom-jumlah', function () {
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
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18]
              },
              customize: function (doc) {
                const month     = $('#Months').find('option:selected').text().toUpperCase();
                const year      = $('#Years').val();
                const kategori  = $('#Kategori').val();

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
                        text: 'CONTROL FINISHED GOOD PERIODE ' + month + ' ' + year,
                        bold: true,
                        fontSize: 14,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    },
                    {
                        text: 'KATEGORI : ' + kategori,
                        bold: true,
                        fontSize: 12,
                        style: 'subheader',
                        alignment: 'left',
                        margin: [0, 0, 0, 10]
                    }
                );

                // === Table1 (TOTAL RINGKASAN) ===
                const totalJob = $('#LblTotalJob').text().trim();
                const totalProduksi = $('#LblTotalProduksi').text().trim();
                const sisaJob = $('#LblSisaJob').text().trim();
                const persen = $('#PersentaseJob').text().trim();

                const summary1Table = [
                    [
                      { text: 'NO', style: 'tableHeader' },
                      { text: '#', style: 'tableHeader' },
                      { text: 'JUMLAH', style: 'tableHeader' },
                      { text: '%', style: 'tableHeader' }
                    ],
                    [
                      { text: '1', alignment: 'right' },
                      { text: 'JOB ALL', alignment: 'left' },
                      { text: totalJob, alignment: 'right' },
                      {
                        text: persen,
                        alignment: 'center',
                        rowSpan: 2,
                        margin: [0, 10, 0, 0], // middle vertical align
                      }
                    ],
                    [
                      { text: '2', alignment: 'right' },
                      { text: 'PRODUKSI OK', alignment: 'left' },
                      { text: totalProduksi, alignment: 'right' },
                      {}
                    ],
                    [
                      { text: '3', alignment: 'right' },
                      { text: 'SISA JOB', alignment: 'left' },
                      { text: sisaJob, alignment: 'right' },
                      { text: 'Belum masuk WH', alignment: 'center' }
                    ]
                ];

                doc.content.push(
                    {
                      text: 'TOTAL RINGKASAN',
                      style: 'subheader',
                      margin: [0, 20, 0, 8]
                    },
                    {
                      columns: [
                          {
                              width: '50%',
                              alignment: 'center',
                              table: {
                                  headerRows: 1,
                                  widths: ['8%', '15%', '15%', '25%'],
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

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 7, 9, 11, 12, 13, 14, 15];
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
                var month = $('#Months').find('option:selected').text().toUpperCase();
                var year  = $('#Years').val();

                return 'Laporan Control Job Finished Good Periode ' + month + ' ' + year;
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
          //select: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>finishedgood/list_data",
            "type": "POST",
            "data": function(data) {
              // data.Months    = $('#Months').val();
              // data.Years     = $('#Years').val();

              data.StartDate = $('#start_date').val();
              data.EndDate   = $('#end_date').val();
              data.Kategori  = $('#Kategori').val();
            }
          },
          fixedColumns: {
            left: 4
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "TGL. JOB": "TGL. JOB" , "sClass": "text-center", "width": "100px" },
            { "KATEGORI": "KATEGORI" , "sClass": "text-center", "width": "100px" },
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px" },
            { "TYPE": "TYPE" , "sClass": "text-left", "width": "180px" },
            { "PART ID": "PART ID" , "sClass": "text-left", "width": "100px" },
            { "NO. JOB": "NO. JOB" , "sClass": "text-left", "width": "245px" },
            { "QTY. JOB": "QTY. JOB" , "sClass": "text-right", "width": "100px" },              //7
            { "UNIT ID": "UNIT ID" , "sClass": "text-center", "width": "100px" },
            { "TOTAL MASUK WH": "TOTAL MASUK WH" , "sClass": "text-right", "width": "100px" },  //9
            { "TGL. PROSES": "TGL. PROSES" , "sClass": "text-center", "width": "100px" },  
            { "PRODUKSI": "PRODUKSI" , "sClass": "text-right", "width": "100px" },              //11
            { "WH": "WH" , "sClass": "text-right", "width": "120px" },                          //12
            { "%": "%" , "sClass": "text-right", "width": "100px" },                            //13
            { "FINISHED GOOD": "FINISHED GOOD" , "sClass": "text-right", "width": "150px" },    //14
            { "SISA JOB": "SISA JOB" , "sClass": "text-right", "width": "150px" },              //15
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" }
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

              let totalJob      = 0;
              let totalProduksi = 0;
              let totalWH       = 0;

              api.columns().every(function () {
                  var column = this;
                  var columnIndex = column.index();

                  var isNumeric = [8, 10, 12, 13, 15, 16].includes(columnIndex);

                  if (isNumeric) {
                      var total = 0;

                      // Ambil semua baris dengan label "SUB TOTAL" di kolom index 1 (JobDate)
                      //api.rows({ page: 'current' }).every(function () {
                      api.rows().every(function () {
                          var rowData = this.data();
                          if (rowData[2] === 'SUB TOTAL') {
                              total += parseLocaleFloatCustom(rowData[columnIndex]);
                          }
                      });

                      var decimalDigits = (columnIndex === 10) ? 4 : 2;

                      var formattedTotal = total.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: decimalDigits,
                        useGrouping: true
                      });

                      $(column.footer()).html(formattedTotal);

                      // Inject ke luar DataTable
                      if (columnIndex === 8) {
                        $('#LblTotalJob').html(formattedTotal);
                        totalJob = total;
                      } else if (columnIndex === 10) {
                        $('#LblTotalProduksi').html(formattedTotal);
                        totalProduksi = total;
                      } else if (columnIndex === 12) {
                        totalWH = total;
                      } else if (columnIndex === 16) {
                        $('#LblSisaJob').html(formattedTotal);
                      }

                  } else if (columnIndex === 6) {
                      $(column.footer()).html('TOTAL');
                  } else if (columnIndex === 14) {
                      // PERSENTASE TOTAL
                      let totalPersen = totalJob > 0 ? (totalWH / totalJob) * 100 : 0;
                      $(column.footer()).html(totalPersen.toFixed(2) + '%');
                  } else {
                      $(column.footer()).html('');
                  }
              });

              // Update elemen di luar tabel
              if (totalJob > 0) {
                var persentaseJob = (totalWH / totalJob) * 100;
                $('#PersentaseJob').html(persentaseJob.toFixed(2) + '%');
              } else {
                $('#PersentaseJob').html('0%');
              }
          },
          "createdRow": function(row, data, dataIndex) {
            if (data[2].toString().toUpperCase().includes("TOTAL")) {
              // Tambahkan class ke seluruh baris (opsional)
              $(row).addClass('bg-secondary text-white');

              // Kolom 0, 1, dan 2 adalah kolom yang difreeze (left: 3)
              [0, 1, 2, 3].forEach(function(i) {
                $('td', row).eq(i).addClass('bg-secondary');
              });
            }

            if (data[2] && data[2].toString().toUpperCase().includes("SUB TOTAL")) {
              $(row).addClass('font-weight-bold');
            }
          }
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
                url: '<?php echo base_url(); ?>finishedgood/get_job_number',
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

        $("#Kategori").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
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