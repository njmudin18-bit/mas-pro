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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/dual-listbox/src/bootstrap-duallistbox.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/css/filter_multi_select.css">
    <style>
      .bootstrap-duallistbox-container .moveall, .bootstrap-duallistbox-container .remove
      {
        width: 38% !important;
      }

      .pointer {
        cursor: pointer;
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
                            <div class="card-header text-left">
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                                <span class="pull-right">
                                  <button id="btnTambah4" type="button" class="btn btn-dark text-white btn-full-mobile" onclick="openModalTunjangan();">SET TUNJANGAN</button>
                                  <button id="btnTambah3" type="button" class="btn btn-danger btn-full-mobile" onclick="openModalGapok();">SET GAJI POKOK</button>
                                  <button id="btnTambah2" type="button" class="btn btn-warning btn-full-mobile" onclick="openModalBPJS();">IKUT BPJS</button>
                                  <button id="btnTambah" type="button" class="btn btn-success btn-full-mobile" onclick="openModal();">SET NON-SHIFT</button>
                                </span>
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control" multiple>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>">
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-3">
                                  <select name="Status" id="Status" class="form-control">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option value="AKTIF" selected>AKTIF</option>
                                    <option value="OFF">OFF</option>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select name="StatusPekerja" id="StatusPekerja" class="form-control" multiple>
                                    <option value="TETAP">TETAP</option>
                                    <option value="KONTRAK">KONTRAK</option>
                                    <option value="OUTSOURCING">OUTSOURCING</option>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <select name="PeriodePenggajian" id="PeriodePenggajian" class="form-control" multiple>
                                    <?php 
                                      $periodes = get_periode_penggajian();
                                      foreach ($periodes as $periode): 
                                        $isSelected = ($periode['selected'] === TRUE) ? ' selected' : '';
                                      ?>
                                      <option value="<?php echo $periode['value']; ?>" <?php echo $isSelected; ?>>
                                        <?php echo $periode['label']; ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <!-- <div class="col-md-6 col-sm-12 m-t-3 text-right"> -->
                                  <!-- <button id="btnTambah4" type="button" class="btn btn-dark text-white btn-full-mobile" onclick="openModalTunjangan();">SET TUNJANGAN</button>
                                  <button id="btnTambah3" type="button" class="btn btn-danger btn-full-mobile" onclick="openModalGapok();">SET GAJI POKOK</button>
                                  <button id="btnTambah2" type="button" class="btn btn-warning btn-full-mobile" onclick="openModalBPJS();">IKUT BPJS</button>
                                  <button id="btnTambah" type="button" class="btn btn-success btn-full-mobile" onclick="openModal();">SET NON-SHIFT</button> -->
                                <!-- </div> -->
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="150%">
                                  <thead id="thead-shift" class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center">NO</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">NIP</th>
                                      <th class="text-center">DEPARTEMEN</th>
                                      <th class="text-center">NAME</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">GAPOK</th>
                                      <th class="text-center">AKTIF?</th>
                                      <th class="text-center">PERIODE GAJI</th>
                                      <th class="text-center">BPJS</th>
                                      <th class="text-center">TUNJANGAN GROUP</th>
                                      <th class="text-center">SHIFT</th>
                                      <th class="text-center">GENDER</th>
                                      <th class="text-center">EMAIL</th>
                                      <th class="text-center">BOD</th>
                                      <th class="text-center">HIRED DAY</th>
                                      <th class="text-center">ADDRESS</th>
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
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="addForm">
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Shift Operation</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="ShiftOperation" id="ShiftOperation" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <?php foreach ($shiftList as $shift): ?>
                      <option value="<?php echo $shift->ShiftID; ?>">
                        <?php echo strtoupper($shift->ShiftName)." (".$shift->JamIn." - ".$shift->JamOut.")"; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-12 mb-2">
                  <div class="form-error">
                    <select multiple="multiple" name="Member[]" class="form-control" id="Member"></select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="add_selected();" class="btn btn-primary waves-effect waves-light ">Add Selected</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- MODAL EDIT NON SHIFT -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Schedule</h4>
            <button type="button" class="close" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="editForm">
              <input type="hidden" value="" name="UserIDEdit" id="UserIDEdit">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDEdit" id="DeptIDEdit" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameEdit" id="DeptNameEdit" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipEdit" id="NipEdit" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameEdit" id="NameEdit" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Shift Operation</label>
                <div class="col-sm-4 mb-1">
                  <select name="ShiftOperationEdit" id="ShiftOperationEdit" class="form-control">
                    <option value="KOSONGKAN">KOSONGKAN</option>
                    <option value="" selected disabled>-- Pilih --</option>
                    <?php foreach ($shiftList as $shift): ?>
                      <option value="<?php echo $shift->ShiftID; ?>">
                        <?php echo strtoupper($shift->ShiftName); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_pegawai_nonshift()" id="btnUpdate" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT GAPOK -->
    <div class="modal fade" id="editGapokModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Gaji Pokok</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="editGapokForm">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDGapok" id="DeptIDGapok" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameGapok" id="DeptNameGapok" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipGapok" id="NipGapok" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameGapok" id="NameGapok" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Gaji Pokok</label>
                <div class="col-sm-10 mb-1 form-error">
                  <select name="DaftarGapokEdit" id="DaftarGapokEdit" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($GapokList as $value): ?>
                      <option value="<?= $value->BasicSalaryID; ?>">
                        <?= $value->BasicSalary." - ".$value->JobTitle; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_gaji_pokok()" id="btnUpdateGapok" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL BPJS -->
    <div class="modal fade" id="modalBPJS" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_bpjs()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="bpjsForm">
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Daftar BPJS?</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="DaftarBpjs" id="DaftarBpjs" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="Y">DAFTAR</option>
                    <option value="N">TIDAK</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-12 mb-2">
                  <div class="form-error">
                    <select multiple="multiple" name="MemberBPJS[]" class="form-control" id="MemberBPJS"></select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_bpjs()">Close</button>
            <button id="btnBpjs" type="button" onclick="add_bpjs();" class="btn btn-primary waves-effect waves-light ">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT BPJS -->
    <div class="modal fade" id="editBPJSModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Schedule</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_edit_bpjs()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="editBPJSForm">
              <input type="hidden" value="" name="UserIDBPJS" id="UserIDBPJS">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipEditBPJS" id="NipEditBPJS" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameEditBPJS" id="NameEditBPJS" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Daftar BPJS?</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="DaftarEditBpjs" id="DaftarEditBpjs" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="Y">DAFTAR</option>
                    <option value="N">TIDAK</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_bpjs_pegawai()" id="btnUpdateBPJS" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT TUNJANGAN -->
    <div class="modal fade" id="editTunjanganModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Tunjangan</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_edit_tunjangan()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="editTunjanganForm">
              <input type="hidden" value="" name="UserIDTunjangan" id="UserIDTunjangan">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipEditTunjangan" id="NipEditTunjangan" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameEditTunjangan" id="NameEditTunjangan" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Tunjangan</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="EditTunjangan" id="EditTunjangan" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($TunjanganList as $value): ?>
                      <option value="<?= $value->HeaderID; ?>">
                        <?= $value->GroupName; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_tunjangan_pegawai()" id="btnUpdateTunjangan" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL GAPOK -->
    <div class="modal fade" id="modalGapok" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_gapok()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="gapokForm">
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Setting Gaji Pokok</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="DaftarGapok" id="DaftarGapok" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($GapokList as $value): ?>
                      <option value="<?= $value->BasicSalaryID; ?>">
                        <?= $value->BasicSalary." - ".$value->JobTitle; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-12 mb-2">
                  <div class="form-error">
                    <select multiple="multiple" name="MemberGapok[]" class="form-control" id="MemberGapok"></select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_gapok()">Close</button>
            <button id="btnGapok" type="button" onclick="add_gapok();" class="btn btn-primary waves-effect waves-light ">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL TUNJANGAN -->
    <div class="modal fade" id="modalTunjangan" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_tunjangan()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="tunjanganForm">
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Pilih Tunjangan</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="DaftarTunjangan" id="DaftarTunjangan" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($TunjanganList as $value): ?>
                      <option value="<?= $value->HeaderID; ?>">
                        <?= $value->GroupName; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-12 mb-2">
                  <div class="form-error">
                    <select multiple="multiple" name="MemberTunjangan[]" class="form-control" id="MemberTunjangan"></select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_tunjangan()">Close</button>
            <button id="btnGapok" type="button" onclick="add_tunjangan();" class="btn btn-primary waves-effect waves-light ">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT NON SHIFT -->
    <div class="modal fade" id="modalStatusPegawai" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Schedule</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_sp()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="statusPegawaiForm">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDSP" id="DeptIDSP" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameSP" id="DeptNameSP" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipSP" id="NipSP" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameSP" id="NameSP" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Status Pegawai</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="StatusSP" id="StatusSP" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="F">TETAP</option>
                    <option value="C">KONTRAK</option>
                    <option value="I">MAGANG</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_status_pegawai()" id="btnUpdateSP" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL AKTIVASI PEGAWAI -->
    <div class="modal fade" id="modalAktivasiPegawai" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Aktivasi Pegawai</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_aktivasi()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="statusAktivasiForm">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDAktivasi" id="DeptIDAktivasi" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameAktivasi" id="DeptNameAktivasi" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipAktivasi" id="NipAktivasi" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameAktivasi" id="NameAktivasi" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Aktivasi Pegawai</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="StatusAktivasi" id="StatusAktivasi" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="A">AKTIF</option>
                    <option value="O">OFF</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_aktivasi_pegawai()" id="btnUpdateAktivasi" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL UBAH DEPARTEMEN -->
    <div class="modal fade" id="modalUbahDepartemen" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Aktivasi Pegawai</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_departemen()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="departemenForm">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipUbahDepartemen" id="NipUbahDepartemen" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameUbahDepartemen" id="NameUbahDepartemen" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Departemen</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="DeptList" id="DeptList" class="form-control">
                    <?php foreach ($DeptList as $dept): ?>
                      <option value="<?= $dept->DEPTID; ?>">
                        <?= strtoupper($dept->DEPTNAME); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_departemen_pegawai()" id="btnUpdateDepartemen" type="button" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/dual-listbox/src/jquery.bootstrap-duallistbox.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>

    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
        var end   = moment().endOf('month');

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#DateShow').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#StartDateShow').val(start.format('YYYY-MM-DD'));
          $('#EndDateShow').val(end.format('YYYY-MM-DD'));
        }

        $('#DateShow').daterangepicker({
          maxDate: moment().add(3, 'months'),
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
      var DEPTID    = "<?php echo $DEPTID; ?>";
      var DEPTNAME  = "<?php echo $DEPTNAME; ?>";

      function update_departemen_pegawai()
      {
        var form_data = $('#departemenForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_departemen_pegawai",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateDepartemen").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              reset_departemen();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateDepartemen").text('Update');
            $("#btnUpdateDepartemen").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateDepartemen').text('Update');
            $('#btnUpdateDepartemen').prop('disabled', false);
          }
        });
      }


      function update_aktivasi_pegawai()
      {
        var form_data = $('#statusAktivasiForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_aktivasi_pegawai",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateAktivasi").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              reset_aktivasi();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateAktivasi").text('Update');
            $("#btnUpdateAktivasi").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateAktivasi').text('Update');
            $('#btnUpdateAktivasi').prop('disabled', false);
          }
        });
      }

      //FUNCTION UPDATE STATUS PEGAWAI
      function update_status_pegawai()
      {
        var form_data = $('#statusPegawaiForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_status_pegawai",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateSP").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              reset_sp();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateSP").text('Update');
            $("#btnUpdateSP").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateSP').text('Update');
            $('#btnUpdateSP').prop('disabled', false);
          }
        });
      }

      //FUNCTION UPDATE GAJI POKOK
      function update_gaji_pokok()
      {
        var form_data = $('#editGapokForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_gaji_pokok",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateGapok").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#editGapokModal').modal('hide');
              $('#editGapokForm')[0].reset();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateGapok").text('Update');
            $("#btnUpdateGapok").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateGapok').text('Update');
            $('#btnUpdateGapok').prop('disabled', false);
          }
        });
      }

      //FUNCTION TAMBAH GAJI POKOK
      function add_gapok() 
      {
        let selected = $('#MemberGapok').val(); 
        let Gapok    = $('#DaftarGapok').val();

        if (selected && selected.length > 0) {
          // kumpulkan data pegawai jadi array of object
          let employees = selected.map(function(id) {
            let label = $("#MemberGapok option[value='" + id + "']").text();
            let parts = label.split('-');
            let dept  = (parts.length > 1) ? parts[1].trim() : label.trim();
            let name  = (parts.length > 1) ? parts[2].trim() : label.trim();

            return { Nip: id, DeptID: dept, Name: name};
          });

          $.ajax({
            url: "<?php echo base_url(); ?>pegawai/setting_gapok",
            dataType: 'JSON',
            type: 'POST',
            data: {
              Employees: employees, // array berisi {Nip, Name}
              DaftarGapok: Gapok
            },
            beforeSend: function () {
              $("#loading").show();
              $("#btnGapok").prop('disabled', true);
            },
            success: function (data) {
              $(".form-group").removeClass('has-error');
              $(".help-block").remove();

              if (data.status == 'success') {
                $("#loading").hide();
                $('#modalGapok').modal('hide');
                $('#gapokForm')[0].reset();
                reload_table();
              } else if (data.status == 'error') {
                $("#loading").hide();
                Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
              } else if (data.status == 'forbidden') {
                $("#loading").hide();
                Swal.fire('FORBIDDEN', 'Access Denied', 'info');
              } else {
                $("#loading").hide();
                // looping error input dari backend
                for (var i = 0; i < data.inputerror.length; i++) {
                  var inputName = data.inputerror[i];
                  var errorMsg  = data.error_string[i];
                  var inputElem = $('[name="' + inputName + '"]');

                  if (inputName === 'MemberGapok') {
                    var dualListboxContainer = $('#MemberGapok').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }

              $("#btnGapok").text('Save');
              $("#btnGapok").prop('disabled', false);
            },
            error: function () {
              $("#loading").hide();
              alert('Error adding / update data');
              $('#btnGapok').text('Save');
              $('#btnGapok').prop('disabled', false);
            }
          });

        } else {
          Swal.fire({title: "Oops...", text: "Harap pilih setidaknya 1 pegawai.", icon: "info" });
          return false;
        }
      }

      //FUNCTION TAMBAH BPJS
      function add_bpjs() 
      {
        let selected = $('#MemberBPJS').val(); 
        let Bpjs     = $('#DaftarBpjs').val();

        if (selected && selected.length > 0) {
          // kumpulkan data pegawai jadi array of object
          let employees = selected.map(function(id) {
            let label = $("#MemberBPJS option[value='" + id + "']").text();
            let parts = label.split('-');
            let dept  = (parts.length > 1) ? parts[1].trim() : label.trim();
            let name  = (parts.length > 1) ? parts[2].trim() : label.trim();

            return { Nip: id, DeptID: dept, Name: name};
          });

          $.ajax({
            url: "<?php echo base_url(); ?>pegawai/daftar_bpjs",
            dataType: 'JSON',
            type: 'POST',
            data: {
              Employees: employees, // array berisi {Nip, Name}
              DaftarBpjs: Bpjs
            },
            beforeSend: function () {
              $("#loading").show();
              $("#btnBpjs").prop('disabled', true);
            },
            success: function (data) {
              $(".form-group").removeClass('has-error');
              $(".help-block").remove();

              if (data.status == 'success') {
                $("#loading").hide();
                $('#modalBPJS').modal('hide');
                $('#bpjsForm')[0].reset();
                reload_table();
              } else if (data.status == 'error') {
                $("#loading").hide();
                Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
              } else if (data.status == 'forbidden') {
                $("#loading").hide();
                Swal.fire('FORBIDDEN', 'Access Denied', 'info');
              } else {
                $("#loading").hide();
                // looping error input dari backend
                for (var i = 0; i < data.inputerror.length; i++) {
                  var inputName = data.inputerror[i];
                  var errorMsg  = data.error_string[i];
                  var inputElem = $('[name="' + inputName + '"]');

                  if (inputName === 'MemberBPJS') {
                    var dualListboxContainer = $('#MemberBPJS').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }

              $("#btnBpjs").text('Save');
              $("#btnBpjs").prop('disabled', false);
            },
            error: function () {
              $("#loading").hide();
              alert('Error adding / update data');
              $('#btnBpjs').text('Save');
              $('#btnBpjs').prop('disabled', false);
            }
          });

        } else {
          Swal.fire({title: "Oops...", text: "Harap pilih setidaknya 1 pegawai.", icon: "info" });
          return false;
        }
      }

      //FUNCTION TAMBAHKAN PEGAWAI
      function add_selected() 
      {
        let selected = $('#Member').val(); 
        if (selected && selected.length > 0) {
          $('#SelectedEmployee').val(selected.length + ' Selected');
          $('#hiddenContainer').empty();
          selected.forEach(function(id){
            let label     = $("#Member option[value='" + id + "']").text();
            let ShiftID   = $('#ShiftOperation').val();
            // pisahkan dengan tanda " - " dan ambil nama saja
            let parts     = label.split('-');
            let nama      = (parts.length > 1) ? parts[1].trim() : label.trim();

            $.ajax({
              url: "<?php echo base_url(); ?>absensi/update_pegawai_nonshift",
              dataType: 'JSON',
              data: {
                NipEdit: id,
                Name: nama,
                ShiftOperation: ShiftID
              },
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
                  $('#addModal').modal('hide');
                  $('#addForm')[0].reset();
                  reload_table();
                } else if (data.status == 'error') {
                  $("#loading").hide();
                  Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                      // Penanganan khusus dual listbox
                      if (inputName === 'Member') {
                        var dualListboxContainer = $('#Member').closest('.form-error');
                        dualListboxContainer.addClass('has-error');
                        if (dualListboxContainer.find('.help-block').length === 0) {
                          dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                      } else {
                        inputElem.closest('.form-error').addClass('has-error');
                        if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                          inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
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

            // hidden untuk NIP (value saja)
            // $('#hiddenContainer').append(
            //   '<input type="hidden" name="SelectedNip[]" value="' + id + '">'
            // );

            // // hidden untuk nama (tanpa NIP)
            // $('#hiddenContainer').append(
            //   '<input type="hidden" name="SelectedName[]" value="' + nama + '">'
            // );
          });
          //$('#addModal').modal('hide');
        } else {
          Swal.fire({title: "Oops...", text: "Harap pilih setidaknya 1 pegawai.", icon: "info" });

          return false;
        }
      }

      //FUNCTION TAMBAHKAN TUNJANGAN
      function add_tunjangan()
      {
        let selected   = $('#MemberTunjangan').val(); 
        let Tunjangan  = $('#DaftarTunjangan').val();

        if (selected && selected.length > 0) {
          // kumpulkan data pegawai jadi array of object
          let employees = selected.map(function(id) {
            let label = $("#MemberTunjangan option[value='" + id + "']").text();
            let parts = label.split('-');
            let dept  = (parts.length > 1) ? parts[1].trim() : label.trim();
            let name  = (parts.length > 1) ? parts[2].trim() : label.trim();

            return { Nip: id, DeptID: dept, Name: name};
          });

          $.ajax({
            url: "<?php echo base_url(); ?>pegawai/save_tunjangan",
            dataType: 'JSON',
            type: 'POST',
            data: {
              Employees: employees, // array berisi {Nip, Name}
              DaftarTunjangan: Tunjangan
            },
            beforeSend: function () {
              $("#loading").show();
              $("#btnTunjangan").text('Saving...');
              $("#btnTunjangan").prop('disabled', true);
            },
            success: function (data) {
              $(".form-group").removeClass('has-error');
              $(".help-block").remove();

              if (data.status == 'success') {
                $("#loading").hide();
                $('#modalTunjangan').modal('hide');
                $('#tunjanganForm')[0].reset();
                reload_table();
              } else if (data.status == 'error') {
                $("#loading").hide();
                Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
              } else if (data.status == 'forbidden') {
                $("#loading").hide();
                Swal.fire('FORBIDDEN', 'Access Denied', 'info');
              } else {
                $("#loading").hide();
                // looping error input dari backend
                for (var i = 0; i < data.inputerror.length; i++) {
                  var inputName = data.inputerror[i];
                  var errorMsg  = data.error_string[i];
                  var inputElem = $('[name="' + inputName + '"]');

                  if (inputName === 'MemberTunjangan') {
                    var dualListboxContainer = $('#MemberTunjangan').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }

              $("#btnTunjangan").text('Save');
              $("#btnTunjangan").prop('disabled', false);
            },
            error: function () {
              $("#loading").hide();
              alert('Error adding / update data');
              $('#btnTunjangan').text('Save');
              $('#btnTunjangan').prop('disabled', false);
            }
          });

        } else {
          Swal.fire({title: "Oops...", text: "Harap pilih setidaknya 1 pegawai.", icon: "info" });
          return false;
        }
      }

      function reset_all() 
      {
        $('#addForm')[0].reset();
        $('#addModal').modal('hide');
        $('.modal-title').text('Tambah Data');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_departemen() 
      {
        $('#departemenForm')[0].reset();
        $('#modalUbahDepartemen').modal('hide');
        $('#modalUbahDepartemen .modal-title').text('Edit Departemen Pegawai');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_bpjs() 
      {
        $('#bpjsForm')[0].reset();
        $('#modalBPJS').modal('hide');
        $('#modalBPJS .modal-title').text('Tambah Peserta');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_gapok() 
      {
        $('#gapokForm')[0].reset();
        $('#modalGapok').modal('hide');
        $('#modalGapok .modal-title').text('Setting Gaji Pokok');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_edit_bpjs()
      {
        $('#editBPJSForm')[0].reset();
        $('#editBPJSModal').modal('hide');
        $('#editBPJSModal .modal-title').text('Tambah Peserta');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_tunjangan() 
      {
        $('#tunjanganForm')[0].reset();
        $('#modalTunjangan').modal('hide');
        $('#modalTunjangan .modal-title').text('Setting Tunjangan');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_edit_tunjangan() 
      {
        $('#editTunjanganForm')[0].reset();
        $('#editTunjanganModal').modal('hide');
        $('#editTunjanganModal .modal-title').text('Edit Tunjangan');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function reset_sp()
      {
        $('#statusPegawaiForm')[0].reset();
        $('#modalStatusPegawai').modal('hide');
        $('#modalStatusPegawai .modal-title').text('Edit Status Pegawai');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }
      
      function reset_aktivasi()
      {
        $('#statusAktivasiForm')[0].reset();
        $('#modalAktivasiPegawai').modal('hide');
        $('#modalAktivasiPegawai .modal-title').text('Edit Aktivasi Pegawai');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN
      function openModal() 
      {
        save_method = 'add';
        $('#addForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#addModal').modal('show');
        $('.modal-title').text('Tambah Data');
        $('#DeptIDModal').val(DEPTID);
        $('#DeptNameModal').val(DEPTNAME);
      }

      function openModalBPJS() 
      {
        save_method = 'add';
        $('#bpjsForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalBPJS').modal('show');
        $('#modalBPJS .modal-title').text('Tambah Peserta');
      }

      function openModalGapok() 
      {
        save_method = 'add';
        $('#gapokForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalGapok').modal('show');
        $('#modalGapok .modal-title').text('Setting Gaji Pokok');
      }

      function openModalTunjangan()
      {
        save_method = 'add';
        $('#tunjanganForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalTunjangan').modal('show');
        $('#modalTunjangan .modal-title').text('Setting Tunjangan');
      }

      function openModalEdit(UserID, Nip, Name, DeptID, DeptName, ShiftID) 
      {
        $('#editForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#editModal').modal('show');
        $('#editModal .modal-title').text('Edit Data');
        $('#UserIDEdit').val(UserID);
        $('#DeptIDEdit').val(DeptID);
        $('#DeptNameEdit').val(DeptName);
        $('#NipEdit').val(Nip);
        $('#NameEdit').val(Name);
        $('#ShiftOperationEdit').val(ShiftID);
      }

      function modalEditTunjangan(UserID, Nip, Name, DeptID, DeptName, TunjanganID)
      {
        $('#editTunjanganForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#editTunjanganModal').modal('show');
        $('#editTunjanganModal .modal-title').text('Edit Data');
        $('#UserIDTunjangan').val(UserID);
        $('#NipEditTunjangan').val(Nip);
        $('#NameEditTunjangan').val(Name);
        $('#EditTunjangan').val(TunjanganID);
      }

      function modalGajiPokok(UserID, Nip, Name, DeptID, DeptName, SalaryID) 
      {
        $('#editGapokForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#editGapokModal').modal('show');
        $('#editGapokModal .modal-title').text('Edit Gaji Pokok');
        $('#UserIDEdit').val(UserID);
        $('#DeptIDGapok').val(DeptID);
        $('#DeptNameGapok').val(DeptName);
        $('#NipGapok').val(Nip);
        $('#NameGapok').val(Name);
        $('#DaftarGapokEdit').val(SalaryID);
      }

      function modalBPJS(UserID, Nip, Name, DeptID, DeptName, BPJS) 
      {
        let BPJSd = BPJS == 'YES' ? 'Y' : 'N';
        $('#editBPJSForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#editBPJSModal').modal('show');
        $('#editBPJSModal .modal-title').text('Edit Peserta');
        $('#UserIDBPJS').val(UserID);
        $('#NipEditBPJS').val(Nip);
        $('#NameEditBPJS').val(Name);
        $('#DaftarEditBpjs').val(BPJSd);
      }

      function openModalStatusPegawai(UserID, Nip, Name, DeptID, DeptName, Status)
      {
        let statusPegawai = "";
        if (Status == 'TETAP') {
          statusPegawai = 'F';
        } else if (Status == 'KONTRAK') {
          statusPegawai = 'C';
        } else {
          statusPegawai = 'I';
        }


        $('#statusPegawaiForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalStatusPegawai').modal('show');
        $('#modalStatusPegawai .modal-title').text('Edit Status Pegawai');
        $('#DeptIDSP').val(DeptID);
        $('#DeptNameSP').val(DeptName);
        $('#NipSP').val(Nip);
        $('#NameSP').val(Name);
        $('#StatusSP').val(statusPegawai);
      }

      function openModalAktivasiPegawai(UserID, Nip, Name, DeptID, DeptName, Status)
      {
        $('#statusAktivasiForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalAktivasiPegawai').modal('show');
        $('#modalAktivasiPegawai .modal-title').text('Edit Aktivasi Pegawai');
        $('#DeptIDAktivasi').val(DeptID);
        $('#DeptNameAktivasi').val(DeptName);
        $('#NipAktivasi').val(Nip);
        $('#NameAktivasi').val(Name);
        $('#StatusAktivasi').val(Status);
      }

      function openModalUbahDepartemen(UserID, Nip, Name, DeptID, DeptName, Status)
      {
        $('#departemenForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalUbahDepartemen').modal('show');
        $('#modalUbahDepartemen .modal-title').text('Edit Departemen Pegawai');
        $('#NipUbahDepartemen').val(Nip);
        $('#NameUbahDepartemen').val(Name);
        $('#DeptList').val(DeptID);
      }

      //UPDATE
      function update_pegawai_nonshift()
      {
        var form_data = $('#editForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>absensi/update_pegawai_nonshift2",
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
              $('#editModal').modal('hide');
              $('#editForm')[0].reset();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
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

      function update_bpjs_pegawai()
      {
        let DaftarBpjs = $('#DaftarEditBpjs').val();
        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_bpjs_pegawai",
          dataType: 'JSON',
          data: {
            EmployeeID: $('#NipEditBPJS').val(),
            EmployeeName: $('#NameEditBPJS').val(),
            DaftarEditBpjs: DaftarBpjs
          },
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateBPJS").text('Updating...');
            $("#btnUpdateBPJS").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#editBPJSModal').modal('hide');
              $('#editBPJSForm')[0].reset();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateBPJS").text('Update');
            $("#btnUpdateBPJS").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateBPJS').text('Update');
            $('#btnUpdateBPJS').prop('disabled', false);
          }
        });
      }

      function update_tunjangan_pegawai()
      {
        let Tunjangan = $('#EditTunjangan').val();
        $.ajax({
          url: "<?php echo base_url(); ?>pegawai/update_tunjangan_pegawai",
          dataType: 'JSON',
          data: {
            EmployeeID: $('#NipEditTunjangan').val(),
            EmployeeName: $('#NameEditTunjangan').val(),
            EditTunjangan: Tunjangan
          },
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdateTunjangan").text('Updating...');
            $("#btnUpdateTunjangan").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#editTunjanganModal').modal('hide');
              $('#editTunjanganForm')[0].reset();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdateTunjangan").text('Update');
            $("#btnUpdateTunjangan").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdateTunjangan').text('Update');
            $('#btnUpdateTunjangan').prop('disabled', false);
          }
        });
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      };

      $(document).ready(function() 
      {
        $("#loading").hide();

        let maskSalary = true;
        table = $('#myTable').DataTable({
          pagingType: "full_numbers",
          lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"]
          ],
          displayLength: 10,
          responsive: false,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          processing: true,
          serverSide: false, // client-side load
          paging: true,
          ordering: true,
          order: [],
          ajax: {
            url: "<?php echo base_url(); ?>pegawai/pegawai_list",
            type: "POST",
            data: function (data) {
              // ambil DeptShow dari checkbox
              let DeptShow = [];
              $('input[name="DeptShow"]:checked').each(function () {
                if ($(this).val()) {
                  DeptShow.push($(this).val());
                }
              });

              let PenggajianShow = [];
              $('input[name="PeriodePenggajian"]:checked').each(function () {
                if ($(this).val()) {
                  PenggajianShow.push($(this).val());
                }
              });

              let StatusPekerja = [];
              $('input[name="StatusPekerja"]:checked').each(function () {
                if ($(this).val()) {
                  StatusPekerja.push($(this).val());
                }
              });

              // kirim ke server
              data.DeptShow            = (DeptShow.length > 0) ? DeptShow : null;
              data.Status              = $('#Status').val();
              data.PeriodePenggajian   = (PenggajianShow.length > 0) ? PenggajianShow : null;
              data.StatusPekerja       = (StatusPekerja.length > 0) ? StatusPekerja : null;
            }
          },
          fixedColumns: { left: 5 },
          select: { style: 'single' },
          dom: 'Bfrltip',
          buttons: [
            {
              text: 'Show SALARY',
              className: 'btn btn-primary',
              action: function (e, dt, node, config) {
                maskSalary = !maskSalary;

                // update teks tombol
                dt.button(node).text(maskSalary ? 'Show SALARY' : 'Hide SALARY');

                // force redraw supaya render() dipanggil ulang
                dt.rows().invalidate().draw(false);
              }
            },
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A3',
              exportOptions: {
                stripHtml: true,
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                modifier: {
                  page: 'all',
                  search: 'applied',
                  order: 'applied'
                },
                format: {
                  body: function (data, row, column, node) {
                    // // masking kolom GAPOK (index 6)
                    // if (column === 6 && maskSalary) {
                    //   if (typeof data === 'string' && data.length > 1) {
                    //     return 'X' + data.substring(1);
                    //   }
                    //   return 'X';
                    // }
                    // return data;

                    // hanya untuk kolom GAPOK (index 6)
                    if (column === 6) {
                      // handle null, undefined, kosong, "X", atau "-" → jadikan string kosong
                      if (
                        data === null ||
                        data === undefined ||
                        data === '' ||
                        data === 'X' ||
                        data.toString().trim().toUpperCase() === 'NULL' ||
                        data.toString().trim() === '-'
                      ) {
                        return '';
                      }

                      // kalau tombol posisi HIDE SALARY (maskSalary = true)
                      if (maskSalary) {
                        let s = String(data).trim();
                        return (s.length > 1) ? 'X' + s.substring(1) : 'X';
                      }

                      // kalau SHOW SALARY → tampilkan asli
                      return data;
                    }
                    return data;
                  }
                }
              },
              customize: function (doc) {
                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
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
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'LAPORAN DAFTAR PEGAWAI',
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 5];
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
              filename: function () {
                return 'LAPORAN DAFTAR PEGAWAI';
              }
            }
          ],
          aoColumns: [
            { "NO": "NO", "sClass": "text-right", "width": "50px" },
            { "#": "#", "sClass": "text-center", "width": "50px" },
            { "NIP": "NIP", "sClass": "text-left", "width": "50px" },
            { "DEPARTEMEN": "DEPARTEMEN", "sClass": "text-left", "width": "50px" },
            { "NAME": "NAME", "sClass": "text-left", "width": "180px" },
            { "STATUS": "STATUS", "sClass": "text-left", "width": "100px" },
            {
              "GAPOK": "GAPOK",
              "sClass": "text-right",
              "width": "80px",
              "render": function (data, type, row, meta) {
                if (!data) return '';

                // tampilkan asli untuk sorting/filter/export
                if (type !== 'display') {
                  return data;
                }

                // kalau display → masking atau asli tergantung toggle
                if (maskSalary) {
                  let s = String(data).trim();
                  return 'X' + s.substring(1); // contoh: 9.901.117,00 → X.901.117,00
                }

                return data; // tampilan normal
              }
            },
            { "AKTIF?": "AKTIF?", "sClass": "text-left", "width": "100px" },
            { "PERIODE GAJI": "PERIODE GAJI", "sClass": "text-left", "width": "100px" },
            { "BPJS": "BPJS", "sClass": "text-center", "width": "80px" },
            { "TUNJANGAN GROUP": "TUNJANGAN GROUP", "sClass": "text-left", "width": "80px" },
            { "SHIFT": "SHIFT", "sClass": "text-left", "width": "80px" },
            { "GENDER": "GENDER", "sClass": "text-left", "width": "80px" },
            { "EMAIL": "EMAIL", "sClass": "text-left", "width": "120px" },
            { "BOD": "BOD", "sClass": "text-center", "width": "80px" },
            { "HIRED DAY": "HIRED DAY", "sClass": "text-center", "width": "80px" },
            { "ADDRESS": "ADDRESS", "sClass": "text-left", "width": "180px" }
          ]
        });

        // EDIT SCHEDULE
        $(document).on('click', 'td.pointer span', function() {
          let EmployeeID    = $(this).data('empid');
          let EmployeeName  = $(this).data('empname');
          let DeptID        = $(this).data('deptid');
          let ShiftID       = $(this).data('shiftid');
          let ScheduleID    = $(this).data('scheduleid');

          console.log(EmployeeID);
          console.log(EmployeeName);

          $('#kodeEdit').val(ScheduleID);
          $('#DeptIDEdit').val(DEPTID);
          $('#DeptNameEdit').val(DEPTNAME);
          $('#NipEdit').val(EmployeeID);
          $('#NameEdit').val(EmployeeName);
          $('#ShiftOperationEdit').val(ShiftID);

          // buka modal
          $('#editModal').modal('show');
        });

        $(document).on('click', '#editModal button.close', function() {
          // tutup modal
          $('#editModal').modal('hide');
          $('#editForm')[0].reset();
        });

        // ================== DualList Modal dsb tetap ==================
        let demo1;
        $('#addModal').on('shown.bs.modal', function () {
          if (!demo1) {
            demo1 = $('#Member').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 250,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('absensi/get_user_all'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#Member').empty();
              $.each(response, function (i, item) {
                $('#Member').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTNAME + ' - ' + item.NAME })
                );
              });
              demo1.bootstrapDualListbox('refresh');
            }
          });
        });

        let demo2;
        $('#modalBPJS').on('shown.bs.modal', function () {
          if (!demo2) {
            demo2 = $('#MemberBPJS').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 250,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('absensi/get_user_bpjs'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#MemberBPJS').empty();
              $.each(response, function (i, item) {
                $('#MemberBPJS').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTNAME + ' - ' + item.NAME })
                );
              });
              demo2.bootstrapDualListbox('refresh');
            }
          });
        });

        let demo3;
        $('#modalGapok').on('shown.bs.modal', function () {
          if (!demo3) {
            demo3 = $('#MemberGapok').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 250,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('absensi/get_user_gapok'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#MemberGapok').empty();
              $.each(response, function (i, item) {
                $('#MemberGapok').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTNAME + ' - ' + item.NAME })
                );
              });
              demo3.bootstrapDualListbox('refresh');
            }
          });
        });

        let demo4;
        $('#modalTunjangan').on('shown.bs.modal', function () {
          if (!demo4) {
            demo4 = $('#MemberTunjangan').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 250,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('absensi/get_user_tunjangan'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#MemberTunjangan').empty();
              $.each(response, function (i, item) {
                $('#MemberTunjangan').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTNAME + ' - ' + item.NAME })
                );
              });
              demo4.bootstrapDualListbox('refresh');
            }
          });
        });

        // ================== Dropdown Fix dsb tetap ==================
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

        $("#ShiftCode").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#ShiftOperation, #DaftarBpjs, #DaftarGapok, #DaftarGapokEdit, #DaftarEditBpjs, #DaftarTunjangan, #StatusSP, #StatusAktivasi').on('change keyup', function () {
          var $formGroup = $(this).closest('.form-error');
          $formGroup.removeClass('has-error');
          $formGroup.find('.help-block').remove(); // hapus pesan error
        });
      });
    </script>
    <script>
      $(function () {
        const languages = $('#DeptShow').filterMultiSelect({
          placeholderText: "Departemen",
          filterText: "Filter",
          selectAllText: "SELECT ALL",
          labelText: "",
          selectionLimit: 0,
          caseSensitive: false,
          allowEnablingAndDisabling: true,
        });
      });

      $(function () {
        const languages = $('#PeriodePenggajian').filterMultiSelect({
          placeholderText: "Periode Gaji",
          filterText: "Filter",
          selectAllText: "SELECT ALL",
          labelText: "",
          selectionLimit: 0,
          caseSensitive: false,
          allowEnablingAndDisabling: true,
        });
      });

      $(function () {
        const languages = $('#StatusPekerja').filterMultiSelect({
          placeholderText: "Status Pekerja",
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