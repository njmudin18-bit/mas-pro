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

    <!-- QR CODE JS -->
    <script src="<?php echo base_url(); ?>assets/code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-qrcode/src/jquery.qrcode.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-qrcode/src/qrcode.js"></script>
    <style>
      body {
        font-size: 14px;
      }
      .table-bordered td, .table-bordered th {
        border: 1px solid #000 !important;
      }
      .title {
        font-weight: bold;
        text-align: center;
        font-size: 18px;
        padding: 5px;
        border: 1px solid #000;
      }
      .section-title {
        font-weight: bold;
        background: #f8f9fa;
      }
      .btn-box {
        border: 1px solid #000;
        padding: 3px 10px;
        margin-right: 5px;
        display: inline-block;
      }
      .btn-125 {
        max-width: 125px;
      }
      .w-80 {
        width: 80% !important;
      }
      #isi-preview .table-bordered td, .table-bordered th {
        border: 2px solid #000000;
        padding: 0.6rem .75rem !important;
      }
      .circle-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        font-weight: bold;
        color: white;
        text-transform: uppercase;
      }
      .stamp {
        display: inline-block;
        transform: rotate(0deg);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .15em;
        border: 2px solid currentColor; /* ikut warna teks */
        padding: .25rem .6rem;
        line-height: 1;
        color: inherit; /* ambil dari class Bootstrap text-* */
      }
    </style>
    <style>
      @media print {
        .d-print-inline {
            display: inline !important;
        }
        .d-print-inline-block {
            display: inline-block !important;
        }
        .d-print-block {
            display: block !important;
        }
        .d-print-grid {
            display: grid !important;
        }
        .d-print-inline-grid {
            display: inline-grid !important;
        }
        .d-print-table {
            display: table !important;
        }
        .d-print-table-row {
            display: table-row !important;
        }
        .d-print-table-cell {
            display: table-cell !important;
        }
        .d-print-flex {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
        }
        .d-print-inline-flex {
            display: -webkit-inline-box !important;
            display: -ms-inline-flexbox !important;
            display: inline-flex !important;
        }
        .d-print-none {
            display: none !important;
        }
      }
    </style>
  </head>
  <body>
    <div class="loader-bg d-print-none">
      <div class="loader-bar"></div>
    </div>
    <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box d-print-none"></div>
      <div class="pcoded-container navbar-wrapper">
        <?php $this->load->view('adminx/components/navbar'); ?>
        <?php $this->load->view('adminx/components/navbar_chat'); ?>
        <div class="pcoded-main-container">
          <div class="pcoded-wrapper">
            <?php $this->load->view('adminx/components/sidebar'); ?>
            <div class="pcoded-content" style="background-color: #fff !important;">
              <?php $this->load->view('adminx/components/breadcrumb'); ?>
              <div class="pcoded-inner-content">
                <div class="main-body">
                  <div class="page-wrapper">
                    <div class="page-body">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card">
                            <div class="card-block m-b-10">
                              <a href="javascript:window.print()" class="btn btn-success mt-2 mb-5 d-print-none" title="Cetak QR">
                                <i class="fa fa-print ml-1 ml-1"></i> Print
                              </a>
                              <div class="row">
                                <div class="table-responsive">

                                  <table class="table table-bordered">
                                    <thead>
                                      <tr class="text-center">
                                        <th colspan="6" class="h5 font-weight-bold">LAPORAN HASIL TRIAL<br>PT. MULTI ARTA SEKAWAN</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td class="text-left h6 font-weight-bold">I. KETERANGAN TRIAL</td>
                                        <td class="text-left h6 font-weight-bold">NO. TRIAL: #<?php echo $Laporan->Nomor; ?></td>
                                        <td class="text-left h6 font-weight-bold text-center" colspan="4">PELAKSANA</td>
                                      </tr>
                                      <tr>
                                        <td>NO. FORM</td>
                                        <td><?php echo $no_form; ?></td>
                                        <td class="text-center">PRODUKSI</td>
                                        <td class="text-center">PPIC</td>
                                        <td class="text-center">QC</td>
                                        <td class="text-center">PD</td>
                                      </tr>
                                      <tr>
                                        <?php
                                          // Fungsi menentukan class dan teks
                                          function statusClass($status) {
                                            if ($status === "Setuju") {
                                              return ['class' => 'stamp text-success', 'text' => 'SETUJU'];
                                            } elseif ($status === "Tidak") {
                                              return ['class' => 'stamp text-danger', 'text' => 'TIDAK'];
                                            } else {
                                              return ['class' => 'stamp text-secondary d-none', 'text' => htmlspecialchars($status)];
                                            }
                                          }

                                          $PD_Status      = statusClass($Laporan->PD_Status);
                                          $PPIC_Status    = statusClass($Laporan->PPIC_Status);
                                          $EXTRUDE_Status = statusClass($Laporan->EXTRUDE_Status);
                                          $QC_Status      = statusClass($Laporan->QC_Status);
                                        ?>
                                        <td>NO. REGISTERASI</td>
                                        <td><?php echo $reg_form; ?></td>
                                        <td class="align-middle text-center display-5">
                                          <span class="<?php echo $EXTRUDE_Status['class']; ?>"><?php echo $EXTRUDE_Status['text']; ?></span>
                                        </td>
                                        <td class="align-middle text-center display-5">
                                          <span class="<?php echo $PPIC_Status['class']; ?>"><?php echo $PPIC_Status['text']; ?></span>
                                        </td>
                                        <td class="align-middle text-center display-5">
                                          <span class="<?php echo $QC_Status['class']; ?>"><?php echo $QC_Status['text']; ?></span>
                                        </td>
                                        <td class="align-middle text-center display-5">
                                          <span class="<?php echo $PD_Status['class']; ?>"><?php echo $PD_Status['text']; ?></span>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>SUBJECT</td>
                                        <td><?php echo $subject_form; ?></td>
                                        <td class="align-middle text-center">
                                          <?php echo get_instance()->get_pelaksana($Laporan->EXTRUDE_UserID)."<br>".$Laporan->EXTRUDE_UserID; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                          <?php echo get_instance()->get_pelaksana($Laporan->PPIC_UserID)."<br>".$Laporan->PPIC_UserID; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                          <?php echo get_instance()->get_pelaksana($Laporan->QC_UserID)."<br>".$Laporan->QC_UserID; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                          <?php echo get_instance()->get_pelaksana($Laporan->PD_UserID)."<br>".$Laporan->PD_UserID; ?>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>FORMULA ID</td>
                                        <td><?php echo $Laporan->FormulaID; ?></td>
                                        <td class="h6 font-weight-bold text-center" colspan="4">HASIL TRIAL</td>
                                      </tr>
                                      <tr>
                                        <td>PRODUCT TYPE</td>
                                        <td><?php echo $Laporan->Type; ?></td>
                                        <td class="text-center">PRODUKSI</td>
                                        <td class="text-center">PPIC</td>
                                        <td class="text-center">QC</td>
                                        <td class="text-center">PD</td>
                                      </tr>
                                      <tr>
                                        <td>PART ID</td>
                                        <td><?php echo $Laporan->PartID; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                      </tr>
                                      <tr>
                                        <td>PART NAME</td>
                                        <td><?php echo $Laporan->PartName; ?></td>
                                        <td class="text-center"><?php echo get_instance()->get_pelaksana($Laporan->EXTRUDE_DisetujuiUserID)."<br>".$Laporan->EXTRUDE_DisetujuiUserID; ?></td>
                                        <td class="text-center"><?php echo get_instance()->get_pelaksana($Laporan->PPIC_DisetujuiUserID)."<br>".$Laporan->PPIC_DisetujuiUserID; ?></td>
                                        <td class="text-center"><?php echo get_instance()->get_pelaksana($Laporan->QC_DisetujuiUserID)."<br>".$Laporan->QC_DisetujuiUserID; ?></td>
                                        <td class="text-center"><?php echo get_instance()->get_pelaksana($Laporan->PD_DisetujuiUserID)."<br>".$Laporan->PD_DisetujuiUserID; ?></td>
                                      </tr>
                                      <tr>
                                        <td class="h6 font-weight-bold text-left" colspan="3">II. PLANNING TRIAL</td>
                                        <td class="h6 font-weight-bold text-center" colspan="3">DECISION</td>
                                      </tr>
                                      <tr>
                                        <?php
                                          // Contoh nilai variabel
                                          $EXTRUDE_DisetujuiStatus = $Laporan->EXTRUDE_DisetujuiStatus; 
                                          $QC_DisetujuiStatus      = $Laporan->QC_DisetujuiStatus; 
                                          $PD_DisetujuiStatus      = $Laporan->PD_DisetujuiStatus; 

                                          // Default semua badge jadi "non-aktif" (misalnya warna abu atau transparan)
                                          $okClass = "circle-badge bg-light text-dark";
                                          $ngClass = "circle-badge bg-light text-dark";
                                          $saClass = "circle-badge bg-light text-dark";

                                          // Logika sesuai ketentuan
                                          if ($QC_DisetujuiStatus === "NG" || $EXTRUDE_DisetujuiStatus === "NG" || $PD_DisetujuiStatus === "NG") {
                                              $ngClass = "circle-badge bg-danger text-white";
                                          } elseif ($QC_DisetujuiStatus === "SA") {
                                              $saClass = "circle-badge bg-secondary text-white";
                                          } elseif ($EXTRUDE_DisetujuiStatus === "OK" && $QC_DisetujuiStatus === "OK" && $PD_DisetujuiStatus === "OK") {
                                              $okClass = "circle-badge bg-success text-white";
                                          }
                                        ?>
                                        <td>PROSES</td>
                                        <td colspan="2"><?php echo $Laporan->Proses; ?></td>
                                        <td rowspan="6" class="align-middle text-center display-4">
                                          <span class="<?php echo $okClass; ?>">OK</span>
                                        </td>
                                        <td rowspan="6" class="align-middle text-center display-4">
                                          <span class="<?php echo $ngClass; ?>">NG</span>
                                        </td>
                                        <td rowspan="6" class="align-middle text-center display-4">
                                          <span class="<?php echo $saClass; ?>">SA</span>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>JENIS MATERIAL</td>
                                        <td colspan="2"><?php echo $Laporan->JenisMaterial; ?></td>
                                      </tr>
                                      <tr>
                                        <td>MESIN YANG DIGUNAKAN</td>
                                        <td colspan="2"><?php echo $Laporan->Machine; ?></td>
                                      </tr>
                                      <tr>
                                        <td>JUMLAH TRIAL</td>
                                        <td colspan="2"><?php echo $Laporan->Quantity." ".$Laporan->UnitName; ?></td>
                                      </tr>
                                      <tr>
                                        <td>HARI/ TANGGAL</td>
                                        <td colspan="2"><?php echo $Laporan->ProcessDate; ?></td>
                                      </tr>
                                      <tr>
                                        <td>SHIFT</td>
                                        <td colspan="2"><?php echo $Laporan->Shift; ?></td>
                                      </tr>
                                      <tr>
                                        <td class="h6 font-weight-bold text-left" colspan="6">III. LAPORAN HASIL TRIAL</td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">1. PRODUKSI</td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">
                                          KETERANGAN:<br><br>
                                          <?php echo $Laporan->EXTRUDE_DisetujuiNoted; ?>
                                          <?php
                                            $EXT_DisetujuiId      = $Laporan->EXTRUDE_DisetujuiId;
                                            $EXT_DisetujuiStatus  = $Laporan->EXTRUDE_DisetujuiStatus;
                                            $EXT_DisetujuiNoted   = $Laporan->EXTRUDE_DisetujuiNoted;

                                            $IsiExtOK  = "'".$EXT_DisetujuiId."', 'OK', '".$EXT_DisetujuiNoted."', '".$Laporan->EXTRUDE_DisetujuiFiles."', 'EXTRUDE', 'Disetujui', '".$Laporan->Nomor."'";
                                            $IsiExtNG  = "'".$EXT_DisetujuiId."', 'NG', '".$EXT_DisetujuiNoted."', '".$Laporan->EXTRUDE_DisetujuiFiles."', 'EXTRUDE', 'Disetujui', '".$Laporan->Nomor."'";
                                            //$IsiExtNG  = "'".$EXT_DisetujuiId."', 'NG', '".$EXT_DisetujuiNoted."'";
                                          ?>
                                          <div class="mt-5 mb-2 d-flex align-items-center btn-group" role="group">
                                            <button onclick="show_hasil_trial(<?php echo $IsiExtOK; ?>)" class="btn <?php echo ($Laporan->EXTRUDE_DisetujuiStatus == 'OK') ? 'btn-success' : (($Laporan->EXTRUDE_DisetujuiStatus == 'NG') ? 'btn-outline-success' : 'btn-outline-success'); ?> btn-small-box btn-125 btn-lg">OK</button>
                                            <button onclick="show_hasil_trial(<?php echo $IsiExtNG; ?>)" class="btn <?php echo ($Laporan->EXTRUDE_DisetujuiStatus == 'OK') ? 'btn-outline-danger' : (($Laporan->EXTRUDE_DisetujuiStatus == 'NG') ? 'btn-danger' : 'btn-outline-danger'); ?> btn-small-box btn-125 btn-lg">NG</button>
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">2. QC</td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">
                                          KETERANGAN:<br><br>
                                          <?php echo $Laporan->QC_DisetujuiNoted; ?>
                                          <?php
                                            $QC_DisetujuiId      = $Laporan->QC_DisetujuiId;
                                            $QC_DisetujuiStatus  = $Laporan->QC_DisetujuiStatus;
                                            $QC_DisetujuiNoted   = $Laporan->QC_DisetujuiNoted;

                                            $IsiQcOK  = "'".$QC_DisetujuiId."', 'OK', '".$QC_DisetujuiNoted."', '".$Laporan->QC_DisetujuiFiles."', 'QC', 'Disetujui', '".$Laporan->Nomor."'";
                                            $IsiQcNG  = "'".$QC_DisetujuiId."', 'NG', '".$QC_DisetujuiNoted."', '".$Laporan->QC_DisetujuiFiles."', 'QC', 'Disetujui', '".$Laporan->Nomor."'";
                                          ?>
                                          <div class="mt-5 mb-2 d-flex align-items-center btn-group" role="group">
                                            <button onclick="show_hasil_trial(<?php echo $IsiQcOK; ?>)" class="btn <?php echo ($Laporan->QC_DisetujuiStatus == 'OK') ? 'btn-success' : (($Laporan->QC_DisetujuiStatus == 'NG') ? 'btn-outline-success' : 'btn-outline-success'); ?> btn-small-box btn-125 btn-lg">OK</button>
                                            <button onclick="show_hasil_trial(<?php echo $IsiQcNG; ?>)" class="btn <?php echo ($Laporan->QC_DisetujuiStatus == 'OK') ? 'btn-outline-danger' : (($Laporan->QC_DisetujuiStatus == 'NG') ? 'btn-danger' : 'btn-outline-danger'); ?> btn-small-box btn-125 btn-lg">NG</button>
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">3. PD</td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">KETERANGAN:<br><br>
                                          <?php echo $Laporan->PD_DisetujuiNoted; ?>
                                          <?php
                                            $PD_DisetujuiId      = $Laporan->PD_DisetujuiId;
                                            $PD_DisetujuiStatus  = $Laporan->PD_DisetujuiStatus;
                                            $PD_DisetujuiNoted   = $Laporan->PD_DisetujuiNoted;

                                            // $IsiPdOK  = "'".$PD_DisetujuiId."', 'OK', '".$PD_DisetujuiNoted."'";
                                            // $IsiPdNG  = "'".$PD_DisetujuiId."', 'NG', '".$PD_DisetujuiNoted."'";

                                            $IsiPdOK  = "'".$PD_DisetujuiId."', 'OK', '".$PD_DisetujuiNoted."', '".$Laporan->PD_DisetujuiFiles."', 'PD', 'Disetujui', '".$Laporan->Nomor."'";
                                            $IsiPdNG  = "'".$PD_DisetujuiId."', 'NG', '".$PD_DisetujuiNoted."', '".$Laporan->PD_DisetujuiFiles."', 'PD', 'Disetujui', '".$Laporan->Nomor."'";
                                          ?>
                                          <div class="mt-5 mb-2 d-flex align-items-center btn-group" role="group">
                                            <button onclick="show_hasil_trial(<?php echo $IsiPdOK; ?>)" class="btn <?php echo ($Laporan->PD_DisetujuiStatus == 'OK') ? 'btn-success' : (($Laporan->PD_DisetujuiStatus == 'NG') ? 'btn-outline-success' : 'btn-outline-success'); ?> btn-small-box btn-125 btn-lg">OK</button>
                                            <button onclick="show_hasil_trial(<?php echo $IsiPdNG; ?>)" class="btn <?php echo ($Laporan->PD_DisetujuiStatus == 'OK') ? 'btn-outline-danger' : (($Laporan->PD_DisetujuiStatus == 'NG') ? 'btn-danger' : 'btn-outline-danger'); ?> btn-small-box btn-125 btn-lg">NG</button>
                                          </div></td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">IV. REMARK</td>
                                      </tr>
                                      <tr>
                                        <td colspan="6">
                                          KETERANGAN:<br><br>
                                          <?php echo $Laporan->Noted.(!empty($Laporan->Files) ? "<br>" : ""); ?>
                                          <?php if (!empty($Laporan->Files)) : ?>
                                            <img width="200px" class="mt-5" src="<?php echo base_url(); ?>files/uploads/trial/<?php echo $Laporan->Files; ?>" alt="PT. MAS" class="img-thumbnail">
                                          <?php endif; ?>
                                        </td>
                                      </tr>
                                    </tbody></table>
                                </div>
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
    
    <!-- MODAL UPDATE HASIL TRIAL -->
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
                  <textarea name="HasilKeterangan" id="HasilKeterangan" rows="3" class="form-control" placeholder="Isi keterangan jika anda tidak setuju"></textarea>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_with_qrcode'); ?>
    <script src="http://10.11.9.22:8080/global-application-system/assets/js/pages/invoicedetails.js"></script>
    <script>
      function reset_update() 
      {
        $('#updateForm')[0].reset();
        $('#modalUpdate').modal('hide');
        $('#modalUpdate .modal-title').text('Update Hasil Trial');
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
              // jika ada file baru dikembalikan dari server, update preview gambar
              if (data.file_name) {
                let baseUrl  = "<?php echo base_url('files/uploads/trial_hasil/'); ?>";
                let fullPath = baseUrl + data.file_name + "?t=" + new Date().getTime(); // tambahin timestamp biar gak cache
                $("#ShowImageHasil img").attr("src", fullPath).show();
              }
              location.reload();
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
    </script>
  </body>
</html>