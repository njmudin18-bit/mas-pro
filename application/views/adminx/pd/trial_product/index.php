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
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="3">NO</th>
                                      <th class="text-center" rowspan="3">NOMOR</th>
                                      <th class="text-center" rowspan="3">DOCUMENT</th>
                                      <th class="text-center" rowspan="3">#</th>
                                      <th class="text-center" rowspan="3">TYPE</th>
                                      <th class="text-center" rowspan="3">PART ID</th>
                                      <th class="text-center" rowspan="3">PART NAME</th>
                                      <th class="text-center" rowspan="3">FORMULA ID</th>
                                      <th class="text-center" rowspan="3">PROSES</th>
                                      <th class="text-center" rowspan="3">MATERIAL</th>
                                      <th class="text-center" rowspan="3">MESIN</th>
                                      <th class="text-center" rowspan="3">QUANTITY</th>
                                      <th class="text-center" rowspan="3">UNIT</th>
                                      <th class="text-center" rowspan="3">NOTED</th>
                                      <th class="text-center" rowspan="3">DIAJUKAN OLEH</th>
                                      <th class="text-center" colspan="12">PELAKSANA</th>
                                      <th class="text-center" colspan="12">PENGAJUAN TRIAL</th>
                                      <th class="text-center" colspan="16">HASIL TRIAL</th>
                                      <th class="text-center" rowspan="3">CREATE DATE</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center" colspan="3">PD</th>
                                      <th class="text-center" colspan="3">PPIC</th>
                                      <th class="text-center" colspan="3">PRODUKSI</th>
                                      <th class="text-center" colspan="3">QC</th>
                                      <th class="text-center" colspan="3">PD</th>
                                      <th class="text-center" colspan="3">PPIC</th>
                                      <th class="text-center" colspan="3">PRODUKSI</th>
                                      <th class="text-center" colspan="3">QC</th>
                                      <th class="text-center" colspan="4">PD</th>
                                      <th class="text-center" colspan="4">PPIC</th>
                                      <th class="text-center" colspan="4">PRODUKSI</th>
                                      <th class="text-center" colspan="4">QC</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">FILES</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">FILES</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">FILES</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BY</th>
                                      <th class="text-center">ON</th>
                                      <th class="text-center">FILES</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                </table>
                              </div>
                              NO DOKUMEN: MAS/FO/PD/002
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
            <form action="" method="post" id="trialForm" enctype="multipart/form-data">
              <input type="hidden" value="" name="Nomor">
              <div class="form-group row border-bottom">
                <label class="col-sm-12 mb-2 col-form-label">PRODUK <span class="pull-right">NO DOKUMEN: <?php echo $no_form; ?></span></label>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div id="PartListSelect" class="col-sm-10 form-error mb-2">
                  <select name="PartList" id="PartList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Product Type</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="ProductType" id="ProductType" class="form-control">
                    <option value="" disabled selected>-- Pilih --</option>
                    <option value="Power Cord Assy">Power Cord Assy</option>
                    <option value="Wiring Harness Assy">Wiring Harness Assy</option>
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
                <label class="col-sm-2 col-form-label">Formula ID</label>
                <div id="FormulaListSelect" class="col-sm-4 form-error  mb-2">
                  <select name="FormulaList" id="FormulaList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Part ID Formula</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartIDFormula" id="PartIDFormula" class="form-control" placeholder="Part ID Formula" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-6"></div>
                <label class="col-sm-2 col-form-label">Keterangan Formula</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="KeteranganFormula" id="KeteranganFormula" class="form-control" placeholder="Keterangan Formula" readonly>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">PLANNING TRIAL</label>
              </div>
              <div class="form-group row mb-1 mt-4">
                <label class="col-sm-2 col-form-label">Proses</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="Proses" name="Proses" class="form-control text-capitalize" placeholder="Contoh: Extrude" maxlength="75" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Jenis Material</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="JenisMaterial" name="JenisMaterial" class="form-control text-uppercase" placeholder="Contoh: PVC" maxlength="75" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Mesin Yang Digunakan</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="Mesin" name="Mesin" class="form-control text-capitalize" placeholder="Contoh: Extrude" maxlength="75" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Jumlah Trial</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="Quantity" name="Quantity" class="form-control" placeholder="Contoh: 300" maxlength="12" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Tanggal Pengerjaan</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="date" id="ProsesDate" name="ProsesDate" class="form-control" placeholder="Tanggal Pengerjaan" maxlength="75" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-2 form-error mb-2">
                  <select name="Shift" id="Shift" class="form-control">
                    <option value="" selected disabled>-- Shift --</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Unit</label>
                <div id="UnitListSelect" class="col-sm-4 form-error mb-2">
                  <select name="UnitList" id="UnitList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Files</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="Files" class="form-control" placeholder="Contoh: Files" autocomplete="off">
                  <span class="help-block"></span>
                  <div class="mt-2 mb-2" id="ShowDrawing"></div>
                </div>
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-4 form-error mb-2">
                  <textarea id="Keterangan" name="Keterangan" class="form-control" rows="3"></textarea>
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

    <!-- MODAL TAMBAH PELAKSANA -->
    <div class="modal fade" id="modalPelaksana" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_pelaksana()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="pelaksanaForm" enctype="multipart/form-data">
              <div class="form-group row border-bottom">
                <label class="col-sm-2 mb-2 col-form-label">No. Trial</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PelaksanaNoTrial" id="PelaksanaNoTrial" class="form-control" required="required" placeholder="Nomor Trial" autocomplete="off" readonly>
                </div>
                <label class="col-sm-2 mb-2 col-form-label">Jenis</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PelaksanaJenis" id="PelaksanaJenis" class="form-control" required="required" placeholder="Nomor Trial" autocomplete="off" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PelaksanaPartName" id="PelaksanaPartName" class="form-control" required="required" placeholder="Partner Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Part ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PelaksanaPartID" id="PelaksanaPartID" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Formula ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PelaksanaFormulaID" id="PelaksanaFormulaID" class="form-control" required autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="PelaksanaDept" name="PelaksanaDept" class="form-control" readonly >
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label" id="LabelSwitch">Pilihan</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="PelaksanaID" id="PelaksanaID" class="form-control"></select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="PelaksanaStatus" id="PelaksanaStatus" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <option value="Setuju">Setuju</option>
                    <option value="Tidak">Tidak</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Files</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="PelaksanaFiles" id="PelaksanaFiles" class="form-control">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10 form-error mb-2">
                  <textarea name="PelaksanaKeterangan" id="PelaksanaKeterangan" rows="3" class="form-control" placeholder="Isi keterangan jika anda tidak setuju"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_pelaksana()">Close</button>
            <button id="btnSave" type="button" onclick="save_pelaksana();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_update()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="updateForm" enctype="multipart/form-data">
              <input type="hidden" name="DetailID" id="DetailID">
              <input type="hidden" name="DetailDept" id="DetailDept">
              <input type="hidden" name="DetailTrans" id="DetailTrans">
              <input type="hidden" name="DetailNomor" id="DetailNomor">
              <div class="form-group row mb-1">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-9 form-error mb-2">
                  <select name="HasilStatus" id="HasilStatus" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <option value="OK">OK</option>
                    <option value="NG">NG</option>
                    <option value="SA">SA</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-3 col-form-label">Keterangan</label>
                <div class="col-sm-9 form-error mb-2">
                  <textarea name="HasilKeterangan" id="HasilKeterangan" rows="5" class="form-control" placeholder="Isi keterangan jika anda tidak setuju"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-3 col-form-label">Files</label>
                <div class="col-sm-9 form-error mb-2">
                  <input type="file" name="HasilFiles" id="HasilFiles" class="form-control">
                  <span class="help-block"></span>
                </div>
              </div>
              <div id="ShowImageHasil" class="form-group row mb-1">
                <label class="col-sm-3 col-form-label"></label>
                <div class="col-sm-9 form-error mb-2">
                  <embed src="" type="" width="100%" height="200px" style="display:none;">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_update()">Close</button>
            <button id="btnSave" type="button" onclick="update_hasil_trial();" class="btn btn-primary waves-effect waves-light ">Update</button>
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

      function reset_all() 
      {
        $('#trialForm')[0].reset();
        $('#modalForm').modal('hide');
        $('.modal-title').text('Tambah Data');

        $('#jumlahContainer').html(`
          <div class="form-group row mb-2 mt-2" id="jumlahRow1">
            <div class="col-md-3 form-error">
              <label class="col-form-label">Files</label>
              <input type="file" name="Files[]" class="form-control" required autocomplete="off" data-required="true">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Quantity Sample</label>
              <input type="text" name="Quantity[]" class="form-control" placeholder="Quantity Sample" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Notes</label>
              <input type="text" name="Notes[]" maxlength="150" class="form-control" required placeholder="Contoh: Rev. 01 dst." autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      function reset_pelaksana() 
      {
        $('#pelaksanaForm')[0].reset();
        $('#modalPelaksana').modal('hide');
        $('#modalPelaksana .modal-title').text('Tambah Pelaksana');
      }

      function reset_update() 
      {
        $('#updateForm')[0].reset();
        $('#modalUpdate').modal('hide');
        $('#modalUpdate .modal-title').text('Update Hasil Trial');
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() 
      {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#trialForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Pengajuan Trial');
        $('#PartList').val(null).trigger('change');
        $('#UnitList').val(null).trigger('change');
        $('#FormulaList').val(null).trigger('change');
      }

      function tambah_transaksi(Nomor, PartID, PartName, FormulaID, Dept, Transaksi) 
      {
        let Trans       = Transaksi;
        let Departemen  = Dept;
        $('#modalPelaksana').modal('show');
        $('#pelaksanaForm')[0].reset();
        $('#modalPelaksana .modal-title').text('Tambah ' + Transaksi);
        $('#modalPelaksana #LabelSwitch').text(Transaksi + ' oleh');
        $('#PelaksanaNoTrial').val(Nomor);
        $('#PelaksanaJenis').val(Transaksi);
        $('#PelaksanaPartName').val(PartName);
        $('#PelaksanaPartID').val(PartID);
        $('#PelaksanaFormulaID').val(FormulaID);
        $('#PelaksanaDept').val(Dept);

        // 🔹 Atur option select berdasarkan nilai Trans
        let statusSelect = $('#PelaksanaStatus');
        statusSelect.empty();
        statusSelect.append('<option value="" selected disabled>-- Pilih --</option>');

        if (Trans === "Disetujui") {
          statusSelect.append('<option value="OK">OK</option>');
          statusSelect.append('<option value="NG">NG</option>');
          statusSelect.append('<option value="SA">SA</option>');
        } else {
          statusSelect.append('<option value="Setuju">Setuju</option>');
          statusSelect.append('<option value="Tidak">Tidak</option>');
        }

        $.ajax({
          url: "<?php echo base_url(); ?>trial/cek_pelaksana",
          type: "POST",
          dataType: "JSON",
          data: {
            NoTrial: Nomor,
            Departemen: Dept,
            Jenis: Transaksi
          },
          success: function(data) {
            let Pelaksana = data.data.PelaksanaID;
            $('#PelaksanaStatus').val(data.data.Status);
            $('#PelaksanaKeterangan').val(data.data.Noted);

            let pelaksanaSelect = $('#PelaksanaID');
            pelaksanaSelect.empty();
            pelaksanaSelect.append('<option value="" selected disabled>-- Pilih --</option>');

            if (Departemen === "EXTRUDE") {
              pelaksanaSelect.append('<option value="0012011030044">ANWAR BIN MADHASAN</option>');
              pelaksanaSelect.append('<option value="0012011060132">MOH.YULIANTO BIN AHMADI</option>');
              pelaksanaSelect.append('<option value="0012008031274">WINDO FEBRI F.</option>');
            } else {
              get_user_dept(Dept, Pelaksana);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //UPDATE HASIL TRIAL
      function show_hasil_trial(DetailID, Hasil, Noted, Files, Dept, Trans, Nomor)
      {
        $('#modalUpdate').modal('show');
        $('#updateForm')[0].reset();
        $('#modalUpdate .modal-title').text('Update Hasil Trial ');
        
        $('#DetailID').val(DetailID);
        $('#DetailDept').val(Dept);
        $('#DetailTrans').val(Trans);
        $('#DetailNomor').val(Nomor);

        $('#HasilStatus').val(Hasil);
        $('#HasilKeterangan').val(Noted);

        if(Files && Files !== ""){
          let baseUrl   = "<?php echo base_url(); ?>files/uploads/trial_hasil/";
          let fullPath  = baseUrl + Files;
          let ext       = Files.split('.').pop().toLowerCase();

          // set src + type sesuai ekstensi
          if(ext === "pdf"){
            $("#ShowImageHasil embed").attr("src", fullPath).attr("type", "application/pdf").show();
          } else {
            $("#ShowImageHasil embed").attr("src", fullPath).attr("type", "image/" + ext).show();
          }
        } else {
          $("#ShowImageHasil embed").attr("src", "").hide();
        }
      }

      function get_user_dept(Dept, Pelaksana)
      {
        $.ajax({
          url: "<?php echo base_url(); ?>trial/get_user_dept",
          type: "POST",
          dataType: "JSON",
          data: {
            search: Dept
          },
          success: function(data) {
            let $select = $('#PelaksanaID');
            $select.empty(); // hapus semua option lama
            $select.append('<option value="" selected disabled>-- Pilih --</option>');

            $.each(data, function(index, item) {
              $select.append(
                $('<option>', {
                  value: item.SSN,
                  text: item.NAME
                })
              );
            });

            // Set default jika Pelaksana ada
            if (Pelaksana) {
              $select.val(Pelaksana);
            } else {
              $select.prop('selectedIndex', 0);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      function get_user_dept2(Dept, Pelaksana)
      {
        $.ajax({
          url: "<?php echo base_url(); ?>trial/get_user_dept",
          type: "POST",
          dataType: "JSON",
          data: {
            search: Dept
          },
          success: function(data) {
            let $select = $('#HasilID');
            $select.empty(); // hapus semua option lama
            $select.append('<option value="" selected disabled>-- Pilih --</option>');

            $.each(data, function(index, item) {
              $select.append(
                $('<option>', {
                  value: item.SSN,
                  text: item.NAME
                })
              );
            });

            // Set default jika Pelaksana ada
            if (Pelaksana) {
              $select.val(Pelaksana);
            } else {
              $select.prop('selectedIndex', 0);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION RESET
      function reset() {
        $('#trialForm')[0].reset();
        $('.modal-title').text('Pengajuan Trial');
      }

      //SAVE
      function save() 
      {
        var form      = $('#trialForm')[0];
        var form_data = new FormData(form);

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>trial/trial_add";
        } else {
          url = "<?php echo base_url(); ?>trial/trial_update";
        }

        $.ajax({
          url: url,
          dataType: 'JSON',
          cache: false,
          contentType: false,
          processData: false,
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
              $('#trialForm')[0].reset();
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

              // for (var i = 0; i < data.inputerror.length; i++) {
              //   var inputName = data.inputerror[i];
              //   var errorMsg = data.error_string[i];

              //   var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
              //   if (arrayMatch) {
              //     var arrayName = arrayMatch[1];
              //     var arrayIndex = parseInt(arrayMatch[2]);
              //     var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
              //     inputElem.closest('.form-error').addClass('has-error');
              //     if (inputElem.next('.help-block').length === 0) {
              //       inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
              //     }
              //   } else {
              //     var inputElem = $('[name="' + inputName + '"]');
              //     inputElem.closest('.form-error').addClass('has-error');
              //     if (inputElem.next('.help-block').length === 0) {
              //       inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
              //     }
              //   }
              // }
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

      function save_pelaksana() 
      {
        var form      = $('#pelaksanaForm')[0];
        var form_data = new FormData(form);
        var Url;
        var Dept      = $('#PelaksanaDept').val();
        var Jenis     = $('#PelaksanaJenis').val();

        if (Dept == 'PD') {
          Url = "<?php echo base_url(); ?>trial/pelaksana_save_pd";
        } else if (Dept == 'PPIC') {
          Url = "<?php echo base_url(); ?>trial/pelaksana_save_ppic";
        } else if (Dept == 'EXTRUDE') {
          Url = "<?php echo base_url(); ?>trial/pelaksana_save_extrude";
        } else {
          Url = "<?php echo base_url(); ?>trial/pelaksana_save_qc";
        }

        $.ajax({
          url: Url,
          dataType: 'JSON',
          cache: false,
          contentType: false,
          processData: false,
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
              $('#modalPelaksana').modal('hide');
              $('#pelaksanaForm')[0].reset();
              reload_table();
              reset_pelaksana();
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

      function update_hasil_trial()
      {
        var form      = $('#updateForm')[0];
        var form_data = new FormData(form);

        $.ajax({
          url: "<?php echo base_url(); ?>trial/update_hasil_trial",
          dataType: 'JSON',
          cache: false,
          contentType: false,
          processData: false,
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
              $('#modalUpdate').modal('hide');
              $('#updateForm')[0].reset();
              reload_table();
              reset_update();
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
      function edit(Nomor) 
      {
        save_method = 'update';
        $('#trialForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>trial/trial_edit",
          type: "POST",
          dataType: "JSON",
          data: {
            NoTrial: Nomor
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
              if ($('[name="PartList"] option[value="' + data.first.PartID + '"]').length > 0) {
                $('[name="PartList"]').val(data.first.PartID).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.first.PartName, data.first.PartID, true, true);
                $('[name="PartList"]').append(newOption).trigger('change');
              }

              if ($('[name="FormulaList"] option[value="' + data.first.FormulaID + '"]').length > 0) {
                $('[name="FormulaList"]').val(data.first.FormulaID).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.first.FormulaID, data.first.FormulaID, true, true);
                $('[name="FormulaList"]').append(newOption).trigger('change');
              }

              if ($('[name="UnitList"] option[value="' + data.first.UnitID + '"]').length > 0) {
                $('[name="UnitList"]').val(data.first.UnitID).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.first.UnitName, data.first.UnitID, true, true);
                $('[name="UnitList"]').append(newOption).trigger('change');
              }

              $('[name="Nomor"]').val(data.first.Nomor);
              $('[name="ProductType"]').val(data.first.Type);
              $('[name="PartID"]').val(data.first.PartID);
              $('[name="PartIDFormula"]').val(data.first.PartIDFormula);
              $('[name="KeteranganFormula"]').val(data.first.KeteranganFormula);
              $('[name="Proses"]').val(data.first.Proses);
              $('[name="JenisMaterial"]').val(data.first.JenisMaterial);
              $('[name="Mesin"]').val(data.first.Machine);
              $('[name="Quantity"]').val(formatRupiah(data.first.Quantity));
              $('[name="ProsesDate"]').val(data.first.ProcessDate);
              $('[name="Shift"]').val(data.first.Shift);
              $('[name="Keterangan"]').val(data.first.Noted);
              
              // Cek apakah ada file PDF
              if (data.first.Files) {
                var timestamp = new Date().getTime(); // waktu saat ini
                var embedHtml = `<embed src="<?php echo base_url(); ?>files/uploads/trial/${data.first.Files}?t=${timestamp}" type="application/pdf" width="100%" height="100px" />`;
                $('#ShowDrawing').html(embedHtml);
              } else {
                $('#ShowDrawing').html('<p class="text-danger">Tidak ada file terlampir.</p>');
              }

              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Pengajuan Trial #' + Nomor);
              $('#btnSave').text('Update');
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
        const row         = $('#' + rowId);
        // Ambil data sebelum dihapus
        const NoRequest   = $('input[name="NoRequest"]').val();
        const IdDetail    = row.find('input[name="kodeSecond[]"]').val();
        const OldFiles    = row.find('input[name="OldFiles[]"]').val();

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
              url: "<?php echo base_url(); ?>request_sample/hapus_single_row",
              type: "POST",
              dataType: "JSON",
              data: {
                OldFile: OldFiles,
                NoReq: NoRequest,
                IdDt: IdDetail
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
                  edit(NoRequest);
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

      // function hapusRowQc(rowId)
      // {
      //   const row          = $('#' + rowId);
      //   const Nomor        = $('input[name="QcNomorReqs"]').val();
      //   const PartnerName  = $('input[name="QcPartnerName"]').val();
      //   const PartnerID    = $('input[name="QcPartnerID"]').val();
      //   const Quantity     = $('input[name="QcSampleQuantity"]').val();
      //   const Etd          = $('input[name="QcEtd"]').val();
      //   const IdDetail     = row.find('input[name="kodeThird[]"]').val();

      //   Swal.fire({
      //     title: "Yakin ingin hapus?",
      //     text: "Data yang dihapus tidak bisa dikembalikan!",
      //     icon: "question",
      //     showCancelButton: true,
      //     confirmButtonColor: "#3085d6",
      //     cancelButtonColor: "#d33",
      //     confirmButtonText: "Yes, hapus",
      //     cancelButtonText: "Batal"
      //   }).then((result) => {
      //     if (result.isConfirmed) {
      //       $.ajax({
      //         url: "<?php echo base_url(); ?>request_sample/sample_qc_delete_row",
      //         type: "POST",
      //         dataType: "JSON",
      //         data: {
      //           NoReq: Nomor,
      //           IdDt: IdDetail
      //         },
      //         beforeSend: function() {
      //           $("#loading").show();
      //         },
      //         success: function(data) {
      //           if (data.status == 'forbidden') {
      //             $("#loading").hide();
      //             Swal.fire('FORBIDDEN', 'Access Denied', 'info');
      //           } else {
      //             $("#loading").hide();
      //             cek_qc(Nomor, PartnerName, PartnerID, Quantity, Etd);
      //             reload_table();
      //             // Hapus elemen
      //             row.remove();
      //           }
      //         },
      //         error: function(jqXHR, textStatus, errorThrown) {
      //           $("#loading").hide();
      //           alert('Error hapus data');
      //         }
      //       });
      //     }
      //   });
      // }

      //FUNCITON HAPUS
      function hapus(Nomor)
      {
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
              url: "<?php echo base_url(); ?>trial/trial_deleted",
              type: "POST",
              dataType: "JSON",
              data: {
                NoTrial: Nomor
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
                  reload_table();
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
            <div class="col-md-3 form-error">
              <label class="col-form-label">Files</label>
              <input type="file" name="Files[]" class="form-control" required autocomplete="off">
              <input type="hidden" name="kodeSecond[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Quantity Sample</label>
              <input type="text" name="Quantity[]" class="form-control" placeholder="Quantity Sample" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-3 form-error mb-1">
              <label class="col-form-label">Notes</label>
              <input type="text" name="Notes[]" maxlength="150" class="form-control" required placeholder="Contoh: Rev. 0${count} dst." autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#jumlahContainer').append(row);
      });

      $(document).on('click', '#plus2', function () {
        let count = $('#QcContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="QcRow${count}">
            <div class="col-md-2 form-error">
              <label class="col-form-label">Status</label>
              <select id="QcStatus" name="QcStatus[]" class="form-control fill" required>
                <option value="" selected>-- Pilih --</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
                <option value="HOLD">HOLD</option>
              </select>
              <input type="hidden" name="kodeThird[]" value="">
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Quantity Pengecekan</label>
              <input type="text" name="QcQuantity[]" class="form-control" placeholder="Jumlah Sample" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-2 form-error mb-1">
              <label class="col-form-label">Tanggal Pengecekan</label>
              <input type="date" name="QcTanggal[]" class="form-control" placeholder="Tanggal Pengecekan" autocomplete="off" data-required="true">
              <span class="help-block"></span>
            </div>
            <div class="col-md-5 form-error mb-1">
              <label class="col-form-label">Keterangan</label>
              <textarea id="QcKeterangan" name="QcKeterangan[]" rows="2" class="form-control" required placeholder="Keterangan hasil pengecekan" autocomplete="off" data-required="true"></textarea>
            </div>
            <div class="col-md-1 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-qc" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#QcContainer').append(row);
      });

      // HAPUS KOLOM JUMLAH
      $(document).on('click', '.remove-kolom-jumlah', function () {
        $(this).closest('.form-group').remove();
      });

      $(document).on('click', '.remove-kolom-qc', function () {
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
              pageSize: 'A0',
              exportOptions: {
                stripHtml: true,
                columns: [0, 1, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
                          21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 
                          36, 37, 38, 39, 40, 41, 43, 44, 45, 47, 48, 49,  
                          51, 52, 53, //54, 50, 46, 42
                          55]
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
                    text: 'LAPORAN TRIAL PRODUCT PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                    bold: true,
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'NOMOR DOKUMEN : ' + 'MAS/FO/PD/002',
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'left',
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

                return 'LAPORAN REQUEST SAMPLE PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
          "displayLength": 10,
          responsive: false,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": true,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>trial/trial_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          fixedColumns: {
            left: 2
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "NOMOR": "NOMOR" , "sClass": "text-left", "width": "100px" },
            { "DOCUMENT": "DOCUMENT" , "sClass": "text-center", "width": "100px" },
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "TYPE": "TYPE" , "sClass": "text-left", "width": "50px" },
            { "PART ID": "PART ID" , "sClass": "text-left", "width": "50px" },
            { "PART NAME": "PART NAME" , "sClass": "text-left", "width": "180px" },
            { "FORMULA ID": "FORMULA ID" , "sClass": "text-center", "width": "80px" },
            
            { "PROSES": "PROSES" , "sClass": "text-left", "width": "80px" },
            { "MATERIAL": "MATERIAL" , "sClass": "text-left", "width": "80px" },
            { "MESIN": "MESIN" , "sClass": "text-left", "width": "100px" },
            { "QUANTITY": "QUANTITY" , "sClass": "text-right", "width": "100px" },
            { "UNIT": "UNIT" , "sClass": "text-center", "width": "100px" },
            { "NOTED": "NOTED" , "sClass": "text-left", "width": "100px" },
            { "DIAJUKAN OLEH": "DIAJUKAN OLEH" , "sClass": "text-left", "width": "80px" },

            { "PELAKSANA PD STATUS": "PELAKSANA PD STATUS" , "sClass": "text-center", "width": "100px" },
            { "PELAKSANA PD BY": "PELAKSANA PD BY" , "sClass": "text-center", "width": "120px" },
            { "PELAKSANA PD ON": "PELAKSANA PD ON" , "sClass": "text-center", "width": "120px" },

            { "PELAKSANA PPIC STATUS": "PELAKSANA PPIC STATUS" , "sClass": "text-center", "width": "100px" },
            { "PELAKSANA PPIC BY": "PELAKSANA PPIC BY" , "sClass": "text-center", "width": "120px" },
            { "PELAKSANA PPIC ON": "PELAKSANA PPIC ON" , "sClass": "text-center", "width": "120px" },

            { "PELAKSANA PRODUKSI STATUS": "PELAKSANA PRODUKSI STATUS" , "sClass": "text-center", "width": "100px" },
            { "PELAKSANA PRODUKSI BY": "PELAKSANA PRODUKSI BY" , "sClass": "text-center", "width": "120px" },
            { "PELAKSANA PRODUKSI ON": "PELAKSANA PRODUKSI ON" , "sClass": "text-center", "width": "120px" },

            { "PELAKSANA QC STATUS": "PELAKSANA QC STATUS" , "sClass": "text-center", "width": "100px" },
            { "PELAKSANA QC BY": "PELAKSANA QC BY" , "sClass": "text-center", "width": "120px" },
            { "PELAKSANA QC ON": "PELAKSANA QC ON" , "sClass": "text-center", "width": "120px" },

            { "PENGAJUAN PD STATUS": "PENGAJUAN PD STATUS" , "sClass": "text-center", "width": "100px" },
            { "PENGAJUAN PD BY": "PENGAJUAN PD BY" , "sClass": "text-center", "width": "120px" },
            { "PENGAJUAN PD ON": "PENGAJUAN PD ON" , "sClass": "text-center", "width": "120px" },

            { "PENGAJUAN PPIC STATUS": "PENGAJUAN PPIC STATUS" , "sClass": "text-center", "width": "100px" },
            { "PENGAJUAN PPIC BY": "PENGAJUAN PPIC BY" , "sClass": "text-center", "width": "120px" },
            { "PENGAJUAN PPIC ON": "PENGAJUAN PPIC ON" , "sClass": "text-center", "width": "120px" },

            { "PENGAJUAN PRODUKSI STATUS": "PENGAJUAN PRODUKSI STATUS" , "sClass": "text-center", "width": "100px" },
            { "PENGAJUAN PRODUKSI BY": "PENGAJUAN PRODUKSI BY" , "sClass": "text-center", "width": "120px" },
            { "PENGAJUAN PRODUKSI ON": "PENGAJUAN PRODUKSI ON" , "sClass": "text-center", "width": "120px" },

            { "PENGAJUAN QC STATUS": "PENGAJUAN QC STATUS" , "sClass": "text-center", "width": "100px" },
            { "PENGAJUAN QC BY": "PENGAJUAN QC BY" , "sClass": "text-center", "width": "120px" },
            { "PENGAJUAN QC ON": "PENGAJUAN QC ON" , "sClass": "text-center", "width": "120px" },

            { "HASIL PD STATUS": "HASIL PD STATUS" , "sClass": "text-center", "width": "100px" },
            { "HASIL PD BY": "HASIL PD BY" , "sClass": "text-center", "width": "120px" },
            { "HASIL PD ON": "HASIL PD ON" , "sClass": "text-left", "width": "120px" },
            { "HASIL PD FILES": "HASIL PD FILES" , "sClass": "text-left", "width": "120px" },

            { "HASIL PPIC STATUS": "HASIL PPIC STATUS" , "sClass": "text-center", "width": "100px" },
            { "HASIL PPIC BY": "HASIL PPIC BY" , "sClass": "text-center", "width": "120px" },
            { "HASIL PPIC ON": "HASIL PPIC ON" , "sClass": "text-center", "width": "120px" },
            { "HASIL PPIC FILES": "HASIL PPIC FILES" , "sClass": "text-left", "width": "120px" },

            { "HASIL PRODUKSI STATUS": "HASIL PRODUKSI STATUS" , "sClass": "text-center", "width": "100px" },
            { "HASIL PRODUKSI BY": "HASIL PRODUKSI BY" , "sClass": "text-center", "width": "120px" },
            { "HASIL PRODUKSI ON": "HASIL PRODUKSI ON" , "sClass": "text-center", "width": "120px" },
            { "HASIL PRODUKSI FILES": "HASIL PRODUKSI FILES" , "sClass": "text-center", "width": "120px" },

            { "HASIL QC STATUS": "HASIL QC STATUS" , "sClass": "text-center", "width": "100px" },
            { "HASIL QC BY": "HASIL QC BY" , "sClass": "text-center", "width": "120px" },
            { "HASIL QC ON": "HASIL QC ON" , "sClass": "text-center", "width": "120px" },
            { "HASIL QC FILES": "HASIL QC FILES" , "sClass": "text-center", "width": "120px" },
            
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" }
          ],
          columnDefs: [
            {
              targets: 1,
              render: function (data, type, row) {
                if (typeof data === 'string') {
                  return data.replace('Baru', '').trim();
                }
                return data;
              }
            }
          ]
        });

        // table.on('click', 'tbody tr', function (e) {
        //     table.$('tr.selected').removeClass('selected');  // hilangkan selected di semua row
        //     $(this).addClass('selected');                    // tambahkan selected ke row yg diklik
        // });

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
          $('#PartList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Part Name atau ID",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>trial/get_part',
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
                      id: item.PartID,
                      text: item.PartID + " - " + item.PartName,
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          });

          // Add callback function using select2:select event
          $('#PartList').on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#PartID').val(selectedData.id);
            $('#PartListSelect.has-error').removeClass('has-error').find('span.help-block').text('');
          });

          $('#UnitList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Nama Unit",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>trial/get_unit',
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
                      id: item.UnitID,
                      text: item.UnitID + " - " + item.UnitName,
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          }).on('select2:select', function (e) {
            $('#UnitListSelect.has-error').removeClass('has-error').find('span.help-block').text('');
          });

          $('#FormulaList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Formula ID atau Part ID",
            allowClear: true,
            ajax: {
              url: '<?php echo base_url(); ?>trial/get_formula',
              type: 'POST',
              dataType: 'JSON',
              delay: 250,
              data: function(params) {
                return { search: params.term };
              },
              processResults: function(data) {
                return {
                  results: $.map(data, function(item) {
                    return { 
                      id: item.FormulaID, 
                      text: item.FormulaID,
                      keterangan: item.Keterangan,
                      part_id: item.PartID
                    };
                  })
                };
              },
              cache: true
            },
            minimumInputLength: 3
          }).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#KeteranganFormula').val(selectedData.keterangan);
            $('#PartIDFormula').val(selectedData.part_id);
            $('#FormulaListSelect.has-error').removeClass('has-error').find('span.help-block').text('');
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#PartList').select2('destroy');
          $('#UnitList').select2('destroy');
          $('#FormulaList').select2('destroy');
        });

        $("#ProductType").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Proses").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#JenisMaterial").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Quantity").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ProsesDate").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Keterangan").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ProcessDate").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#PelaksanaID, #PelaksanaStatus").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Keterangan, #PelaksanaKeterangan').on('input', function() {
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