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
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="160%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <td class="text-center" rowspan="3">NO</td>
                                      <td class="text-center" rowspan="3">NO REQ</td>
                                      <td class="text-center" rowspan="3">DOCUMENT</td>
                                      <td class="text-center" rowspan="3">DRAWING</td>
                                      <td class="text-center" rowspan="3">#</td>
                                      <td class="text-center" rowspan="3">PARTNER ID</td>
                                      <td class="text-center" rowspan="3">PARTNER NAME</td>
                                      <td class="text-center" colspan="6">INFO</td>
                                      <td class="text-center" rowspan="3">QUANTITY</td>
                                      <td class="text-center" rowspan="3">CUST. PART NAME</td>
                                      <td class="text-center" rowspan="3">CUST. PART ID</td>
                                      <td class="text-center" rowspan="3">ETD</td>
                                      <?php if ($this->session->userdata('user_dept_name') == 'IT' || $this->session->userdata('user_dept_name') == 'PD'): ?>
                                      <td class="text-center" rowspan="3">PRICES/ PCS</td>
                                      <?php endif; ?>
                                      <td class="text-center" rowspan="3">STATUS</td>
                                      <td class="text-center" rowspan="3">NOTES</td>
                                      <td class="text-center" rowspan="3">CREATE DATE</td>
                                      <td class="text-center" rowspan="3">CREATE BY</td>
                                    </tr>
                                    <tr>
                                      <td class="text-center" rowspan="2">SALES</td>
                                      <td class="text-center" colspan="2">PD</td>
                                      <td class="text-center" colspan="3">QC</td>
                                    </tr>
                                    <tr>
                                      <td class="text-center">STATUS</td>
                                      <td class="text-center">ON</td>
                                      <td class="text-center">STATUS</td>
                                      <td class="text-center">ON</td>
                                      <td class="text-center">INFO</td>
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
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="fgForm" enctype="multipart/form-data">
              <input type="hidden" value="" name="kodeFirst">
              <input type="hidden" value="" name="NoRequest">
              <input type="hidden" value="" name="NoRequest">
              <div class="form-group row border-bottom">
                <label class="col-sm-7 mb-2 col-form-label">CUSTOMER EXISTING</label>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Customer Name</label>
                <div class="col-sm-6 form-error mb-2">
                  <select name="CustomerList" id="CustomerList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Partner ID</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="PartnerID" id="PartnerID" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <input type="hidden" name="PartnerName" id="PartnerName" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Customer Address</label>
                <div class="col-sm-10 form-error mb-2">
                  <input type="text" name="Alamat" id="Alamat" class="form-control" required="required" placeholder="Alamat" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">
                  <input type="checkbox" id="CustomerNewCheck" name="CustomerNewCheck" value="on" onclick="toggleCheck(this)">
                  <span class="ml-2">CUSTOMER NEW(S)</span>
                </label>
              </div>
              <div class="form-group row mb-2 mt-2 border-bottom">
                <label class="col-sm-2 col-form-label">Customer Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CustomerNew" id="CustomerNew" disabled class="form-control text-uppercase" required="required" placeholder="Customer New" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Customer Address</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CustomerNewAddress" id="CustomerNewAddress" disabled class="form-control" required="required" placeholder="Customer Address" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Customer Part ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CustomerPartID" id="CustomerPartID" class="form-control text-uppercase" required="required" placeholder="Customer Part ID" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Customer Part Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CustomerPartName" id="CustomerPartName" class="form-control text-uppercase" required="required" placeholder="Customer Part Name" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="StatusRequest" id="StatusRequest" class="form-control">
                    <option value="" disabled selected>-- Pilih --</option>
                    <option value="Permintaan Harga">Permintaan Harga</option>
                    <option value="Permintaan Sample">Permintaan Sample</option>
                    <option value="Permintaan Harga & Sample">Permintaan Harga & Sample</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Harga per pcs</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="Harga" id="Harga" class="form-control" oninput="AllowDecimalAndComma(this)" maxlength="12" required="required" placeholder="Harga sample per pcs" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Estimasi Time Delivery</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="date" name="Etd" id="Etd" class="form-control" required="required" placeholder="Estimasi pengiriman sample" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-6 form-error mb-2">
                  <input type="text" name="Keterangan" id="Keterangan" class="form-control" required="required" placeholder="Keterangan" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">UPLOAD FILE(S)</label>
              </div>
              <div id="jumlahContainer">
                <div class="form-group row mb-2 mt-2" id="jumlahRow1">
                  <div class="col-md-3 form-error">
                    <label class="col-form-label">Files</label>
                    <input type="file" name="Files[]" class="form-control" required autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 form-error mb-1">
                    <label class="col-form-label">Quantity Sample</label>
                    <input type="text" name="Quantity[]" class="form-control" placeholder="Quantity Sample" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-3 form-error mb-1">
                    <label class="col-form-label">Notes</label>
                    <input type="text" name="Notes[]" maxlength="150" class="form-control" required placeholder="Contoh: Rev. 01 dst." autocomplete="off" data-required="true">
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
    
    <!-- MODAL PD -->
    <div class="modal fade" id="modalEditForm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_edit()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="fgEditForm" enctype="multipart/form-data">
              <div class="form-group row border-bottom">
                <label class="col-sm-2 mb-2 col-form-label">No. Request</label>
                <label class="col-sm-4 mb-2 col-form-label" id="ReqNoLabel"></label>
                <input type="hidden" name="NomorReqs" id="NomorReqs">
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Partner Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartnerNames" id="PartnerNames" class="form-control" required="required" placeholder="Partner Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Partner ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartnerIDs" id="PartnerIDs" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="StatusList" id="StatusList" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                    <option value="Review">Review</option>
                    <option value="Proses Produksi">Proses Produksi</option>
                    <option value="Pembuatan Mold">Pembuatan Mold</option>
                    <option value="Permintaan Sample Material">Permintaan Sample Material</option>
                    <option value="Finish">Finish</option>
                    <option value="Hold">Hold</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal Proses</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="ProcessDate" name="ProcessDate" class="form-control" />
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Harga</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" id="Hargas" name="Hargas" class="form-control" oninput="AllowDecimalAndComma(this)" maxlength="12" required="required" placeholder="Harga sample per pcs" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10 form-error mb-2">
                  <textarea id="Keterangans" name="Keterangans" class="form-control" rows="3" placeholder="Keterangan"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
            <div id="HistoryContainer">
              <hr>
              <h4>History proses</h4>
              <div class="container mb-1 history">
                <div class="row">
                  <div class="col-md-6 offset-md-3">
                    <ul id="HistoryList" class="timeline"></ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_edit()">Close</button>
            <button id="btnSave" type="button" onclick="save_keterangan();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL QC -->
    <div class="modal fade" id="modalQCForm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_qc()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="QcForm">
              <div class="form-group row border-bottom">
                <label class="col-sm-2 mb-2 col-form-label">No. Request</label>
                <label class="col-sm-4 mb-2 col-form-label" id="QcReqNoLabel"></label>
                <input type="hidden" name="QcNomorReqs" id="QcNomorReqs">
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Partner Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="QcPartnerName" id="QcPartnerName" class="form-control" required="required" placeholder="Partner Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Partner ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="QcPartnerID" id="QcPartnerID" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Sample Quantity</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="QcSampleQuantity" id="QcSampleQuantity" class="form-control" required="required" placeholder="Sample Quantity" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Estimasi Time Delivery</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="QcEtd" name="QcEtd" class="form-control" required="required" placeholder="Estimasi Time Delivery" autocomplete="off" readonly />
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">TAMBAHKAN STATUS PENGECEKAN</label>
              </div>
              <div id="QcContainer">
                <div class="form-group row mb-2 mt-2" id="QcRow1">
                  <div class="col-md-2 form-error">
                    <label class="col-form-label">Status</label>
                    <select id="QcStatus" name="QcStatus[]" class="form-control" required>
                      <option value="" selected>-- Pilih --</option>
                      <option value="OK">OK</option>
                      <option value="NG">NG</option>
                      <option value="HOLD">HOLD</option>
                    </select>
                    <span class="help-block"></span>
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
                    <textarea id="QcKeterangan" name="QcKeterangan[]" rows="3" class="form-control" required placeholder="Keterangan hasil pengecekan" autocomplete="off" data-required="true"></textarea>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-1 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
            </form>
            <div id="QcHistoryContainer">
              <hr>
              <h4>History proses</h4>
              <div class="container mb-1 history">
                <div class="row">
                  <div class="col-md-6 offset-md-3">
                    <ul id="QcHistoryList" class="timeline"></ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_edit()">Close</button>
            <button id="btnSave" type="button" onclick="save_keterangan_qc();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL UPLOAD DRAWING -->
    <div class="modal fade" id="modalUploadForm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_upload()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="uploadForm">
              <div class="form-group row border-bottom">
                <label class="col-sm-2 mb-2 col-form-label">No. Request</label>
                <label class="col-sm-4 mb-2 col-form-label" id="UploadReqNoLabel"></label>
                <input type="hidden" name="UploadNomorReqs" id="UploadNomorReqs">
                <input type="hidden" name="UploadKodeDetail" id="UploadKodeDetail">
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Partner Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="UploadPartnerName" id="UploadPartnerName" class="form-control" required="required" placeholder="Partner Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Partner ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="UploadPartnerID" id="UploadPartnerID" class="form-control" required="required" placeholder="Partner ID" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Upload Drawing</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="Files" id="Files" class="form-control" required autocomplete="off" data-required="true">
                  <span class="help-block"></span>
                  <div class="mt-2 mb-2" id="ShowDrawing"></div>
                </div>
                <label class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-4 form-error mb-2">
                  <textarea name="UploadNoted" id="UploadNoted" class="form-control" rows="3"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_upload()">Close</button>
            <button id="btnSave" type="button" onclick="save_drawing();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
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

      function toggleCheck(checkbox) {
          const CustomerNew      = document.getElementById('CustomerNew');
          const CustomerNewAdd   = document.getElementById('CustomerNewAddress');

          // partner & alamat container
          const PartnerCont      = document.getElementById('PartnerID').parentElement;
          const PartnerHelpBlock = PartnerCont.querySelector('.help-block');

          const AlamatCont       = document.getElementById('Alamat').parentElement;
          const AlamatHelpBlock  = AlamatCont.querySelector('.help-block');

          if (checkbox.checked) {
              // ✅ Aktifkan input
              CustomerNew.disabled    = false;
              CustomerNewAdd.disabled = false;
          } else {
              // ❌ Nonaktifkan + kosongkan
              CustomerNew.value       = '';
              CustomerNew.disabled    = true;

              CustomerNewAdd.value    = '';
              CustomerNewAdd.disabled = true;
          }

          // 🔄 Bersihkan PartnerID
          PartnerCont.classList.remove('has-error');
          if (PartnerHelpBlock) PartnerHelpBlock.textContent = '';

          // 🔄 Bersihkan Alamat
          AlamatCont.classList.remove('has-error');
          if (AlamatHelpBlock) AlamatHelpBlock.textContent = '';
      }

      function reset_all() 
      {
        $('#fgForm')[0].reset();
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

      function reset_edit() 
      {
        $('#fgEditForm')[0].reset();
        $('#modalEditForm').modal('hide');
        $('.modal-title').text('Tambah Keterangan');
      }

      function reset_qc() 
      {
        $('#QcForm')[0].reset();
        $('#modalQCForm').modal('hide');
        $('#modalQCForm .modal-title').text('Tambah Status');
      }

      function reset_upload()
      {
        $('#uploadForm')[0].reset();
        $('#modalUploadForm').modal('hide');
        $('#modalUploadForm .modal-title').text('Tambah Status');
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() 
      {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#fgForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Data');
        $('#CustomerList').val(null).trigger('change');
      }

      function tambah_keterangan(Nomor, PartnerName, PartnerID) 
      {
        $('#HistoryList').html('');
        $('#fgEditForm')[0].reset();
        $('#modalEditForm .modal-title').text('Tambah Keterangan');
        $('#ReqNoLabel').text(Nomor);

        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/sample_keterangan_cek",
          type: "POST",
          dataType: "JSON",
          data: {
            NoRequest: Nomor,
            PartnerNames: PartnerName,
            PartnerIDs: PartnerID
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              $("#loading").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else {
              $('#modalEditForm').modal('show');
              $('#NomorReqs').val(Nomor);
              $('#PartnerNames').val(PartnerName);
              $('#PartnerIDs').val(PartnerID);
              if (data.status_code != 404) {
                $('#StatusList').val(data.data.Status);
                $('#Keterangans').val(data.data.Noted);
                $('#ProcessDate').val(data.data.ProcessDate);
                $('#Hargas').val(formatRupiah(data.prices.Prices));
                //$('[name="Harga"]').val(formatRupiah(data.first.Prices));

                // Bersihkan history sebelumnya
                $("#HistoryContainer").show();
                $('#HistoryList').html('');

                // Looping data history
                data.history.forEach(function(item) {
                  $('#HistoryList').append(`
                    <li>
                      <a href="#">${item.Status}</a>
                      <a href="#" class="float-right">${formatTanggalWaktu(item.CreateDate)}</a>
                      <p>${item.Noted}</p>
                    </li>
                  `);
                });
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      function cek_qc(Nomor, PartnerName, PartnerID, Quantity, Etd)
      {
        $('#QcHistoryList').html('');
        $('#QcForm')[0].reset();
        $('#modalQCForm .modal-title').text('Tambah Status');
        $('#QcReqNoLabel').text(Nomor);

        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/sample_qc_cek",
          type: "POST",
          dataType: "JSON",
          data: {
            NoRequest: Nomor,
            PartnerNames: PartnerName,
            PartnerIDs: PartnerID
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              $("#loading").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else {
              $('#modalQCForm').modal('show');
              $('#QcNomorReqs').val(Nomor);
              $('#QcPartnerName').val(PartnerName);
              $('#QcPartnerID').val(PartnerID);
              $('#QcSampleQuantity').val(Quantity);
              $('#QcEtd').val(Etd);

              var html  = '';
              if (data.status_code != 404) {
                data.history.forEach((item, index) => {
                  let rowNumber = index + 1;
                  let statusVal = item.Status || 'OK';
                  html += `
                      <div class="form-group row mb-2 mt-2" id="QcRow${rowNumber}">
                        <div class="col-md-2 form-error">
                          <label class="col-form-label">Status</label>
                          <select id="QcStatus" name="QcStatus[]" class="form-control fill" required="">
                            <option value="" ${statusVal == '' ? 'selected' : ''}>-- Pilih --</option>
                            <option value="OK" ${statusVal == 'OK' ? 'selected' : ''}>OK</option>
                            <option value="NG" ${statusVal == 'NG' ? 'selected' : ''}>NG</option>
                            <option value="HOLD" ${statusVal == 'HOLD' ? 'selected' : ''}>HOLD</option>
                          </select>
                          <input type="hidden" name="kodeThird[]" value="${item.Id}">
                        </div>
                        <div class="col-md-2 form-error mb-1">
                          <label class="col-form-label">Quantity Pengecekan</label>
                          <input type="text" name="QcQuantity[]" value="${item.Quantity}" class="form-control" placeholder="Jumlah Sample" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
                          <span class="help-block"></span>
                        </div>
                        <div class="col-md-2 form-error mb-1">
                          <label class="col-form-label">Tanggal Pengecekan</label>
                          <input type="date" name="QcTanggal[]" value="${item.ProcessDate}" class="form-control" placeholder="Tanggal Pengecekan" autocomplete="off" data-required="true">
                          <span class="help-block"></span>
                        </div>
                        <div class="col-md-5 form-error mb-1">
                          <label class="col-form-label">Keterangan</label>
                          <textarea id="QcKeterangan" name="QcKeterangan[]" rows="3" class="form-control" required placeholder="Keterangan hasil pengecekan" autocomplete="off" data-required="true">${item.Noted}</textarea>
                          <span class="help-block"></span>
                        </div>
                        <div class="col-md-1 button-center">
                          ${rowNumber == 1 
                            ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus2" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                            : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRowQc('QcRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#jumlahRow${rowNumber}').remove()"
                          }
                        </div>
                      </div>
                    `;
                });

                $('#QcContainer').html(html);

                // Bersihkan history sebelumnya
                $("#QcHistoryContainer").show();
                $('#QcHistoryList').html('');

                // Looping data history
                data.history.forEach(function(item) {
                  $('#QcHistoryList').append(`
                    <li>
                      <a href="#">${item.Status} - ${item.Quantity} (pcs)</a>
                      <a href="#" class="float-right">${formatTanggal(item.ProcessDate)}</a>
                      <p>${item.Noted}</p>
                    </li>
                  `);
                });

                
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      function cek_drawing(Nomor, PartnerName, PartnerID) 
      {
        $('#uploadForm')[0].reset();
        $('#modalUploadForm .modal-title').text('Upload Drawing');
        $('#modalUploadForm').modal('show');
        $('#UploadReqNoLabel').text(Nomor);
        $('#UploadNomorReqs').val(Nomor);
        $('#UploadPartnerName').val(PartnerName);
        $('#UploadPartnerID').val(PartnerID);

        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/cek_drawing",
          type: "POST",
          dataType: "JSON",
          data: {
            NoRequest: Nomor
          },
          success: function(data) {
            if (data.status_code == 200) {
              $('#UploadKodeDetail').val(data.data.Id);
              $('#UploadNoted').val(data.data.Notes);

              // Cek apakah ada file PDF
              if (data.data.Files) {
                var timestamp = new Date().getTime(); // waktu saat ini
                var embedHtml = `<embed src="<?php echo base_url(); ?>files/uploads/drawing/${data.data.Files}?t=${timestamp}" type="application/pdf" width="100%" height="100px" />`;
                $('#ShowDrawing').html(embedHtml);
              } else {
                $('#ShowDrawing').html('<p class="text-danger">Tidak ada file terlampir.</p>');
              }

            } else {
              $('#UploadKodeDetail').val('');
              $('#ShowDrawing').html('');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION RESET
      function reset() {
        $('#fgForm')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //SAVE
      function save() 
      {
        var form      = $('#fgForm')[0];
        var form_data = new FormData(form);

        form_data.delete('CustomerNewCheck');
        if ($('#CustomerNewCheck').is(':checked')) {
          form_data.append('CustomerNewCheck', 'on');
        } else {
          form_data.append('CustomerNewCheck', 'off');
        }

        var selectedData = $('#CustomerList').select2('data')[0]; 
        console.log(selectedData);
        if (selectedData) {
          console.log(selectedData.text);
          // Kirim text (label)
          form_data.append('CustomerLabel', selectedData.text); 
        }

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>request_sample/sample_add";
        } else {
          url = "<?php echo base_url(); ?>request_sample/sample_update";
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
                var errorMsg  = data.error_string[i];

                var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                var inputElem;

                if (arrayMatch) {
                    var arrayName  = arrayMatch[1];
                    var arrayIndex = parseInt(arrayMatch[2]);
                    inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                } else {
                    inputElem = $('[name="' + inputName + '"]');
                }

                // ✅ cek apakah element ditemukan
                if (inputElem.length > 0) {
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
      }

      function save_keterangan_qc() 
      {
        var form  = $('#QcForm').serialize();

        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/sample_qc_cek_add",
          dataType: 'JSON',
          data: form,
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
              $('#modalQCForm').modal('hide');
              $('#QcForm')[0].reset();
              reload_table();
              reset_qc();
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

      //SAVE KETERANGAN
      function save_keterangan()
      {
        var form  = $('#fgEditForm').serialize();
        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/sample_keterangan_add",
          type: "POST",
          dataType: "JSON",
          data: form,
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              $("#loading").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else if(data.status == 'success') {
              $("#loading").hide();
              //$('#modalEditForm').modal('hide');
              let Nomor       = data.data.Nomor;
              let PartnerID   = data.data.PartnerID;
              let PartnerName = data.data.PartnerName;
              reload_table();
              $('#fgEditForm')[0].reset();
              $("#HistoryContainer").show();
              tambah_keterangan(Nomor, PartnerName, PartnerID);
            } else {
              $("#loading").hide();
              for (var i = 0; i < data.inputerror.length; i++) 
              {
                $('[name="'+data.inputerror[i]+'"]').parent().addClass('has-error');
                $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $("#loading").hide();
            alert('Error hapus data');
          }
        });
      }

      //UPLOAD DRAWING
      function save_drawing()
      {
        var form      = $('#uploadForm')[0];
        var form_data = new FormData(form);

        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/save_drawing",
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
              $('#modalUploadForm').modal('hide');
              $('#uploadForm')[0].reset();
              reload_table();
              reset_upload();
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

            $("#btnSave").text('Save');
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
      function edit(NoReq) 
      {
        save_method = 'update';
        $('#fgForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>request_sample/sample_edit",
          type: "POST",
          dataType: "JSON",
          data: {
            NoRequest: NoReq
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
              if ($('[name="CustomerList"] option[value="' + data.first.PartnerID + '"]').length > 0) {
                $('[name="CustomerList"]').val(data.first.PartnerID).trigger('change');
              } else {
                // 📝 jika opsi PartID belum ada → tambahkan secara manual
                var newOption = new Option(data.first.PartnerName, data.first.PartnerID, true, true);
                $('[name="CustomerList"]').append(newOption).trigger('change');
              }

              var html  = '';
              var html2 = '';

              $('[name="kodeFirst"]').val(data.first.Id);
              $('[name="NoRequest"]').val(data.first.Nomor);
              $('[name="PartnerID"]').val(data.first.PartnerID);
              $('[name="CustomerNew"]').val(data.first.CustomerName);
              $('[name="CustomerNewAddress"]').val(data.first.CustomerAddress);
              $('[name="Alamat"]').val(data.first.Address);
              $('[name="CustomerPartID"]').val(data.first.CustomerPartID);
              $('[name="CustomerPartName"]').val(data.first.CustomerPartName);
              $('[name="StatusRequest"]').val(data.first.Status);
              $('[name="Harga"]').val(formatRupiah(data.first.Prices));
              $('[name="Etd"]').val(data.first.Etd);
              $('[name="Keterangan"]').val(data.first.Notes);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Data #' + NoReq);
              $('#btnSave').text('Update');

              if (data.first.CustomerCheck == 'on') {
                // centang checkbox
                $('#CustomerNewCheck').prop('checked', true);
                $('#CustomerNew').prop('disabled', false);
                $('#CustomerNewAddress').prop('disabled', false);
              } else {
                // uncheck checkbox
                $('#CustomerNewCheck').prop('checked', false);
                $('#CustomerNew').prop('disabled', true);
                $('#CustomerNewAddress').prop('disabled', true);
              }
              
              data.second.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-2" id="jumlahRow${rowNumber}">
                    <div class="col-md-3 form-error">
                      <label class="col-form-label">Tanggal Proses</label>
                      <input type="file" name="Files[]" value="${item.Files}" class="form-control text-uppercase" required>
                      <input type="hidden" name="kodeSecond[]" value="${item.Id}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <embed src="<?php echo base_url(); ?>files/uploads/request/${item.Files}" type="application/pdf" width="100%" height="100px" />
                      <input type="hidden" name="OldFiles[]" value="${item.Files}">
                    </div>
                    <div class="col-md-2 form-error mb-1">
                      <label class="col-form-label">Quantity Sample</label>
                      <input type="text" name="Quantity[]" value="${item.Quantity}" maxlength="8" oninput="AllowDecimalAndComma(this)" class="form-control" required placeholder="Quantity Sample">
                    </div>
                    <div class="col-md-3 form-error mb-1">
                      <label class="col-form-label">Notes</label>
                      <input type="text" name="Notes[]" value="${item.Notes}" class="form-control" required placeholder="Contoh: Rev. 01 dst.">
                    </div>
                    <div class="col-md-2 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRow('jumlahRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#jumlahRow${rowNumber}').remove()"
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

      function hapusRowQc(rowId)
      {
        const row          = $('#' + rowId);
        const Nomor        = $('input[name="QcNomorReqs"]').val();
        const PartnerName  = $('input[name="QcPartnerName"]').val();
        const PartnerID    = $('input[name="QcPartnerID"]').val();
        const Quantity     = $('input[name="QcSampleQuantity"]').val();
        const Etd          = $('input[name="QcEtd"]').val();
        const IdDetail     = row.find('input[name="kodeThird[]"]').val();

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
              url: "<?php echo base_url(); ?>request_sample/sample_qc_delete_row",
              type: "POST",
              dataType: "JSON",
              data: {
                NoReq: Nomor,
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
                  cek_qc(Nomor, PartnerName, PartnerID, Quantity, Etd);
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

      //FUNCITON HAPUS ALL
      function hapusAll(NoReq)
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
              url: "<?php echo base_url(); ?>request_sample/sample_deleted_all",
              type: "POST",
              dataType: "JSON",
              data: {
                NoRequest: NoReq
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
        $("#HistoryContainer").hide();

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
                columns: [0, 1, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
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
                        text: 'LAPORAN REQUEST SAMPLE PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                        bold: true,
                        fontSize: 14,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    }
                    // {
                    //     text: 'NOMOR DOKUMEN : ' + 'MAS',
                    //     bold: true,
                    //     fontSize: 12,
                    //     style: 'subheader',
                    //     alignment: 'left',
                    //     margin: [0, 0, 0, 10]
                    // }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 10, 14];
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
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>request_sample/sample_list",
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
            { "NO. REQ": "NO. REQ" , "sClass": "text-center", "width": "100px" },
            { "DOCUMENT": "DOCUMENT" , "sClass": "text-center", "width": "100px" },
            { "DRAWING": "DRAWING" , "sClass": "text-center", "width": "100px" },
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "PARTNER ID": "PARTNER ID" , "sClass": "text-left", "width": "50px" },
            { "PARTNER NAME": "PARTNER NAME" , "sClass": "text-left", "width": "180px" },
            { "SALES": "SALES" , "sClass": "text-center", "width": "80px" },
            { "PD STATUS": "PD STATUS" , "sClass": "text-center", "width": "80px" },
            { "PD ON": "PD ON" , "sClass": "text-center", "width": "80px" },
            { "QC STATUS": "QC STATUS" , "sClass": "text-center", "width": "80px" },
            { "QC ON": "QC ON" , "sClass": "text-center", "width": "80px" },
            { "QC INFO": "QC INFO" , "sClass": "text-center", "width": "150px" },
            { "QUANTITY": "QUANTITY" , "sClass": "text-right", "width": "50px" },
            { "CUST. PART NAME": "CUST. PART NAME" , "sClass": "text-left", "width": "245px" },
            { "CUST. PART ID": "CUST. PART ID" , "sClass": "text-left", "width": "100px" },
            { "ETD": "ETD" , "sClass": "text-right", "width": "75px" },
            <?php if ($this->session->userdata('user_dept_name') == 'IT' || $this->session->userdata('user_dept_name') == 'PD'): ?>
            { "PRICES/ PCS": "PRICES/ PCS" , "sClass": "text-right", "width": "75px" },
            <?php endif; ?>
            { "STATUS": "STATUS" , "sClass": "text-left", "width": "150px" },
            { "NOTES": "NOTES" , "sClass": "text-left", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "150px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-left", "width": "150px" }
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
          $('#CustomerList').select2({
            dropdownParent: $('#modalForm'),
            placeholder: "Masukan Customer Name atau ID",
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>request_sample/get_customer',
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
                        id: item.PartnerID,
                        text: item.PartnerName + " - " + item.PartnerID,
                        Type: item.Type,
                        Address: item.Address
                      };
                    })
                  };
                },
                cache: true
            },
            minimumInputLength: 3
          });

          // Add callback function using select2:select event
          $('#CustomerList').on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#PartnerID').val(selectedData.id);
            // $('#PartnerName').val(selectedData.id);
            $('#Alamat').val(selectedData.Address);

            $('.has-error').each(function() {
              $(this).removeClass('has-error');
              $(this).find('span.help-block').text('');
            });
          });
        });

        $('#modalForm').on('hidden.bs.modal', function () {
          $('#CustomerList').select2('destroy');
        });

        $("#CustomerPartName").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#StatusRequest").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Harga").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Etd").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#jumlahContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $("#StatusList").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Keterangans").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ProcessDate").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#CustomerNew").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#CustomerNewAddress").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Files").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Keterangan, #CustomerNewAddress').on('input', function() {
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