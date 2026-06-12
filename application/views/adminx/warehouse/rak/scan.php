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

    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" />
    <style>
      #MprItems.list-group-horizontal {
        font-size: 10px;
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
                            <div class="card-block m-t-10 m-b-10">
                              <div class="text-center" style="background: #eee;">
                                <video id="previewKamera" style="width: 300px;height: 300px;"></video>
                                <br>
                                <div class="form-group row justify-content-center">
                                  <select id="pilihKamera" class="form-control" style="width: 40%;">
                                  </select>
                                </div>
                              </div>
                              <hr>
                              <form id="scanForm">
                                <div class="form-group row justify-content-center">
                                  <input type="search" id="code_barcode" name="code_barcode" class="form-control form-control-round form-control-uppercase text-center form-control-lg form-txt-danger form-control-danger form-search" autofocus="on" autocomplete="off" placeholder="SCAN QR RAK DISINI" maxlength="25">
                                </div>
                              </form>
                              <hr class="m-t-10 m-b-10">
                              <div id="IsiRak" class="container">
                                <form id="RegisterValidation">
                                  <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Baris</label>
                                    <div class="col-sm-7">
                                      <select id="Baris" name="Baris" class="form-control"></select>
                                      <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-3">
                                      <button onclick="cari_baris()" type="button" class="btn btn-danger btn-block mt-2">CARI</button>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Part Name</label>
                                    <div class="col-sm-10">
                                      <select id="PartID" name="PartID" class="form-control select-ajax">
                                        <option selected="selected" disabled="disabled">-- Pilih --</option>
                                      </select>
                                      <span class="help-block"></span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Quantity</label>
                                    <div class="col-sm-4">
                                      <input id="Quantity" name="Quantity" type="text" class="form-control autonumeric" data-a-sep="." data-a-dec=",">
                                      <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-2 mt-3 mt-md-0 mt-sm-0">
                                      <input id="Unit" name="Unit" type="text" class="form-control" placeholder="Unit ID" readonly>
                                      <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-4 mt-3 mt-md-0 mt-sm-0">
                                      <input id="LotNumber" name="LotNumber" type="text" class="form-control" placeholder="Lot Number" maxlength="10">
                                      <span class="help-block"></span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-md-4 col-6">
                                      <label class="col-form-label">Rak :</label>
                                      <input type="text" class="form-control" name="Rak" id="Rak" readonly="readonly">
                                    </div>
                                    <div class="col-md-4 col-6">
                                      <label class="col-form-label">WH Lokasi :</label>
                                      <input type="text" class="form-control" name="WHLokasi" id="WHLokasi" readonly="readonly">
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                      <label class="col-form-label">Noted :</label>
                                      <input type="text" class="form-control" name="Noted" id="Noted">
                                    </div>
                                  </div>
                                  <hr>
                                  <div class="form-group row">
                                    <div class="col-md-12 col-sm-12 text-right">
                                      <button onclick="tambah_item()" type="button" class="btn btn-info">TAMBAH</button>
                                    </div>
                                  </div>
                                  <hr>
                                </form>
                              </div>
                              <div id="DetailRak" class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">No.</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">Rak</th>
                                      <th class="text-center">Baris</th>
                                      <th class="text-center">Part Name</th>
                                      <th class="text-center">Stock</th>
                                      <th class="text-center">Unit</th>
                                      <th class="text-center">Part ID</th>
                                      <th class="text-center">WH Lokasi</th>
                                      <th class="text-center">Noted</th>
                                      <th class="text-center">Created Date</th>
                                      <th class="text-center">Created By</th>
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

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="EditValidation">
              <input type="hidden" value="" name="KodeEdit">
              <input type="hidden" value="" name="RakEdit">
              <input type="hidden" value="" name="QrRakEdit">
              <input type="hidden" value="" name="BarisEdit">
              <input type="hidden" value="" name="QrBarisEdit">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Part ID & Name</label>
                <div class="col-sm-4 mb-2">
                  <input type="text" id="PartIdEdit" name="PartIdEdit" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-6">
                  <input type="text" id="PartNameEdit" name="PartNameEdit" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-4 mb-2">
                  <input type="text" id="QuantityEdit" name="QuantityEdit" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-3 mb-2">
                  <input type="text" id="UnitEdit" name="UnitEdit" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-3">
                  <input type="text" id="WHLokasiEdit" name="WHLokasiEdit" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                  <select id="Status" name="Status" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="IN">Tambah Stock</option>
                    <option value="OUT">Kurangi Stock</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div id="DetailKurangi">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Peruntukan</label>
                  <div class="col-sm-10">
                    <select id="Peruntukan" name="Peruntukan" class="form-control">
                      <option selected="selected" disabled="disabled">-- Pilih --</option>
                      <option value="Job">Job</option>
                      <option value="Bon">Bon</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
                <div id="DetailJob" class="form-group row">
                  <label class="col-sm-2 col-form-label">Job</label>
                  <div class="col-sm-10">
                    <select id="JobNomor" name="JobNomor" class="form-control select-job">
                      <option selected="selected" disabled="disabled">-- Pilih --</option>
                    </select>
                    <span class="help-block"></span>
                    <ol id="MprItems" class="list-group list-group-horizontal-md mt-2"></ol>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Quantity <span id="LabelTambahan"></span></label>
                <div class="col-sm-10">
                  <input id="QuantityBaru" name="QuantityBaru" type="text" class="form-control autonumeric" data-a-sep="." data-a-dec=",">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Noted</label>
                <div class="col-sm-10">
                  <textarea name="NotedEdit" id="NotedEdit" rows="2" class="form-control text-capitalize"></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="update_item();" class="btn btn-primary waves-effect waves-light ">Update</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL TRANSFER -->
    <div class="modal fade" id="modalTransfer" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="TransferValidation">
              <input type="hidden" value="" name="KodeItemTransfer">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Part ID</label>
                <div class="col-sm-10">
                  <input type="text" id="PartIdTransfer" name="PartIdTransfer" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div class="col-sm-10">
                  <input type="text" id="PartNameTransfer" name="PartNameTransfer" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-6 mb-2">
                  <input type="text" id="QuantityStockTransfer" name="QuantityStockTransfer" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-4">
                  <input type="text" id="UnitTransfer" name="UnitTransfer" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-6 mb-2">
                  <select id="StatusTransfer" name="StatusTransfer" class="form-control">
                    <option disabled="disabled">-- Pilih --</option>
                    <option selected="selected" value="TF">Transfer Stock</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-4">
                  <input type="text" id="WHLokasiTransfer" name="WHLokasiTransfer" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Rak Baru</label>
                <div class="col-sm-10">
                  <select id="RakTransfer" name="RakTransfer" class="form-control"></select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Baris</label>
                <div class="col-sm-10">
                  <select id="BarisTransfer" name="BarisTransfer" class="form-control">
                    <option disabled="disabled" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-10">
                  <input id="QuantityTransfer" name="QuantityTransfer" type="text" class="form-control autonumeric" data-a-sep="." data-a-dec=",">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Noted</label>
                <div class="col-sm-10">
                  <textarea name="NotedTransfer" id="NotedTransfer" rows="2" class="form-control text-capitalize"></textarea>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="proses_transfer_item();" class="btn btn-primary waves-effect waves-light ">Transfer</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

    <script src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>

    <script type="text/javascript">
      let selectedDeviceId = null;
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const sourceSelect = $("#pilihKamera");

      $(document).on('change', '#pilihKamera', function() {
        selectedDeviceId = $(this).val();
        if (codeReader) {
          codeReader.reset()
          initScanner()
        }
      })

      function initScanner() {
        codeReader
          .listVideoInputDevices()
          .then(videoInputDevices => {
            videoInputDevices.forEach(device =>
              console.log(`${device.label}, ${device.deviceId}`)
            );

            if (videoInputDevices.length > 0) {

              if (selectedDeviceId == null) {
                if (videoInputDevices.length > 1) {
                  selectedDeviceId = videoInputDevices[1].deviceId
                } else {
                  selectedDeviceId = videoInputDevices[0].deviceId
                }
              }


              if (videoInputDevices.length >= 1) {
                sourceSelect.html('');
                videoInputDevices.forEach((element) => {
                  const sourceOption = document.createElement('option')
                  sourceOption.text = element.label
                  sourceOption.value = element.deviceId
                  if (element.deviceId == selectedDeviceId) {
                    sourceOption.selected = 'selected';
                  }
                  sourceSelect.append(sourceOption)
                })
              }

              codeReader
                .decodeOnceFromVideoDevice(selectedDeviceId, 'previewKamera')
                .then(result => {

                  //hasil scan
                  console.log(result.text)
                  let Isi = $("#code_barcode").val(result.text);
                  console.log(Isi);
                  $('#scanForm').submit();
                  if (codeReader) {
                    //codeReader.reset();
                    initScanner()
                  }
                })
                .catch(err => console.error(err));
            } else {
              alert("Camera not found!")
            }
          })
          .catch(err => console.error(err));
      }

      if (navigator.mediaDevices) {
        initScanner()
      } else {
        alert('Cannot access camera.');
      }
    </script>
    <script type="text/javascript">
      $(function() {
        $("#scanForm").submit(function() {
          let ArrayForm = $('#scanForm').serializeArray();

          $.ajax({
            url: "<?php echo base_url(); ?>scan_rak/cek_rak",
            data: ArrayForm,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success: function(hasil) {
              if (hasil.status_code == 200) {
                reload_table();
                $("#IsiRak").show();
                $("#DetailRak").show();
                $("#loading-screen").hide();
                $('#IdRak').val(hasil.data.Id);
                $('#Rak').val(hasil.data.Rak);
                $('#WHLokasi').val(hasil.data.WHLokasi);
                $('#Noted').val(hasil.data.Noted);
                
                if (hasil.data.WHLokasi == 'WH-B') {
                  $('#LotNumber').prop('readonly', false);
                } else {
                  $('#LotNumber').prop('readonly', true);
                }

                let Details = hasil.detail;
                if (Details.length > 1) {
                  var BarisHtml = '';
                  var i;
                  BarisHtml += '<option selected value="" disabled>-- Pilih --</option>';
                  for(i = 0; i < Details.length; i++){
                    BarisHtml += '<option value="'+ Details[i].Sequent.trim() +'">'+ Details[i].Sequent.trim() +'</option>';
                  }
                  BarisHtml += '<option value="All">All Baris</option>';

                  $('#Baris').html(BarisHtml);
                } else {
                  var BarisHtml = '';
                  var i;
                  BarisHtml += '<option value="" disabled>-- Pilih --</option>';
                  for(i = 0; i < Details.length; i++){
                    BarisHtml += '<option selected value="'+ Details[i].Sequent.trim() +'">'+ Details[i].Sequent.trim() +'</option>';
                  }
                  BarisHtml += '<option value="All">All Baris</option>';

                  $('#Baris').html(BarisHtml);
                }
              } else {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'warning'
                );
                $("#loading-screen").hide();
              }
            },
            error: function() {
              alert('Error, Please try again!');
              $("#loading-screen").hide();
            }
          })
          return false;
        });
      });

      //FUNCTION CARI BARIS
      function cari_baris() {
        let Rak   = $("#code_barcode").val();
        let Baris = $("#Baris").val();
        if (Baris == null || Baris == '') {
          Swal.fire({
            title: "Oops...",
            text: "Silahkan pilih Baris dahulu",
            icon: "info"
          });
          $('#Baris').focus();
        } else {
          reload_table();
        }
      }

      //FUNCTION GET NOMOR JOB
      function show_modal_job()
      {
        $('#modalJob').modal('show');
        $('#modalJob .modal-title').text('Tambahkan Job');
      }

      //FUNCTION PROSES TRANSFER ITEM
      function proses_transfer_item() {
        let ArrayForm = $('#TransferValidation').serializeArray();
        ArrayForm.push({
          name: 'BarisTransferLabel',
          value: $("#BarisTransfer option:selected").text()
        });

        ArrayForm.push({
          name: 'RakTransferLabel',
          value: $("#RakTransfer option:selected").text()
        });

        $.ajax({
          url: "<?php echo base_url(); ?>scan_rak/transfer_item",
          data: ArrayForm,
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success: function(data) {
            $("#loading-screen").hide();
            if (data.status_code == 200)
            {
              $('.select-ajax').val(null).trigger('change');
              $('#TransferValidation')[0].reset();
              $('#modalTransfer').modal('hide');
              reload_table();
            } else if (data.status_code == 500)
            {
              Swal.fire({
                title: data.status,
                text: data.message,
                icon: data.status
              });
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
              }
            }
          },
          error: function() {
            alert('Error, Please try again!');
            $("#loading-screen").hide();
          }
        })
      }

      //FUNCTION TRANSFER ITEM
      function transfer_item(Id, Rak, SubRak, PartID, PartName, Qty, Unit, QrRak, WHLokasi) {
        $.ajax({
          url: "<?php echo base_url(); ?>scan_rak/cek_rak_except",
          data: {
            code_barcode: QrRak
          },
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success: function(hasil) {
            if (hasil.status_code == 200) {
              reload_table();
              $("#loading-screen").hide();
              $('#modalTransfer').modal('show');
              $('#modalTransfer .modal-title').text('Transfer Quantity Rak ' + Rak + ' Baris ' + SubRak);
              $('[name="KodeItemTransfer"]').val(Id);
              $('[name="PartIdTransfer"]').val(PartID);
              $('[name="PartNameTransfer"]').val(PartName);
              $('[name="QuantityStockTransfer"]').val(Qty);
              $('[name="UnitTransfer"]').val(Unit);
              $('[name="WHLokasiTransfer"]').val(WHLokasi);
              $('[name="NotedTransfer"]').val('Pindahan dari Rak ' + Rak + ' Baris ' + SubRak);

              let Details = hasil.data;
              if (Details.length > 1) {
                var BarisHtml = '';
                var i;
                BarisHtml += '<option selected value="">-- Pilih --</option>';
                for(i = 0; i < Details.length; i++){
                  BarisHtml += '<option value="'+ Details[i].QRCode +'">'+ Details[i].Rak.trim() +'</option>';
                }
                $('#RakTransfer').html(BarisHtml);
              } else {
                var BarisHtml = '';
                var i;
                BarisHtml += '<option value="">-- Pilih --</option>';
                for(i = 0; i < Details.length; i++){
                  BarisHtml += '<option selected value="'+ Details[i].Id +'">'+ Details[i].Rak.trim() +'</option>';
                }
                $('#RakTransfer').html(BarisHtml);
              }
            } else {
              Swal.fire(
                'Oops',
                hasil.message,
                'warning'
              );
              $("#loading-screen").hide();
            }
          },
          error: function() {
            alert('Error, Please try again!');
            $("#loading-screen").hide();
          }
        })
      }

      //FUNCTION HAPUS ITEM
      function openModalDelete(Id, Rak, SubRak, PartID, PartName) {
        Swal.fire({
          title: "Hapus?",
          text: "Data " + PartName + " di Rak " + Rak + ", Baris " + SubRak + " akan dihapus. Data yang dihapus tidak bisa dikembalikan.",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, hapus",
          cancelButtonText: "Batal"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?php echo base_url(); ?>scan_rak/hapus_item",
              data: {
                "IdItem": Id,
                "PartID": PartID,
                "Rak": Rak,
                "Baris": SubRak
              },
              type: 'POST',
              dataType: 'JSON',
              beforeSend: function() {
                $("#loading-screen").show();
              },
              success: function(data) {
                $("#loading-screen").hide();
                reload_table();
              },
              error: function() {
                alert('Error, Please try again!');
                $("#loading-screen").hide();
              }
            })
          }
        });
      }

      //FUNCTION UPDATE ITEM
      function update_item() {
        let Status    = $("#Status").val();
        let LabelSts  = Status == 'IN' ? 'Tambah' : 'Kurangi';
        if (Status == null || Status == '') {
          Swal.fire({
            title: "Oops...",
            text: "Silahkan pilih Status terlebih dahulu.",
            icon: "info"
          });
        } else {
          Swal.fire({
            title: LabelSts + " Stock?",
            text: "Pastikan quantity yang input sesuai.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + LabelSts + " Stock!",
            cancelButtonText: "Batal"
          }).then((result) => {
            if (result.isConfirmed) {
              let ArrayForm = $('#EditValidation').serializeArray();
              $.ajax({
                url: "<?php echo base_url(); ?>scan_rak/update_item",
                data: ArrayForm,
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function() {
                  $("#loading-screen").show();
                },
                success: function(data) {
                  $("#loading-screen").hide();
                  if (data.status_code == 200) {
                    reload_table();
                    $('#EditValidation')[0].reset();
                    $('#modalEdit').modal('hide');
                    $('#MprItems').empty();
                    $('#JobNomor').val(null).trigger('change');
                    $("#DetailJob").hide();
                  } else {
                    Swal.fire({
                      title: data.status,
                      text: data.message,
                      icon: data.status
                    });
                  }
                },
                error: function() {
                  alert('Error, Please try again!');
                  $("#loading-screen").hide();
                }
              })
            }
          });
        }
      }

      //FUNCTION RESET
      function reset_all() {
        $("#DetailKurangi").hide();
        $("#DetailJob").hide();
        $('#EditValidation')[0].reset();
        $('#TransferValidation')[0].reset();
        $('#modalEdit').modal('hide');
        $('#modalTransfer').modal('hide');
        $('#modalJob').modal('hide');
        $('.modal-title').text('Edit Quantity Rak');
        $('#modalTransfer .modal-title').text('Transfer Quantity Rak');
        $('#modalJob .modal-title').text('Tambahkan Job');
        $('#JobNomor').val(null).trigger('change');
        $('#MprItems').empty();
      }

      //FUNCITON EDIT ITEMS
      function edit_item(Id, Rak, SubRak, PartID, PartName, Qty, Unit, WHLokasi, QrRak, QrSubRak){
        $('#modalEdit').modal('show');
        $('.modal-title').text('Edit Quantity Rak ' + Rak + ' Baris ' + SubRak);
        $('[name="KodeEdit"]').val(Id);
        $('[name="PartIdEdit"]').val(PartID);
        $('[name="PartNameEdit"]').val(PartName);
        $('[name="QuantityEdit"]').val(Qty);
        $('[name="UnitEdit"]').val(Unit);
        $('[name="WHLokasiEdit"]').val(WHLokasi);
        $('[name="RakEdit"]').val(Rak);
        $('[name="QrRakEdit"]').val(QrRak);
        $('[name="BarisEdit"]').val(SubRak);
        $('[name="QrBarisEdit"]').val(QrSubRak);
        $('#JobNomor').next(".select2-container").hide();
        $('#MprItems').hide();
        $("#DetailKurangi").hide();
      }

      //FUNCTION MODAL RAK DETAIL
      function tambah_item() {
        let ArrayForm = $('#RegisterValidation').serializeArray();
        ArrayForm.push({
          name: 'QR',
          value: $("#code_barcode").val()
        });

        $.ajax({
          url: "<?php echo base_url(); ?>scan_rak/tambah_item",
          data: ArrayForm,
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success: function(data) {
            $("#loading-screen").hide();
            if (data.status_code == 200)
            {
              $('.select-ajax').val(null).trigger('change');
              //$('#RegisterValidation')[0].reset();
              //$('#Baris').val('');
              $('#Quantity').val('');
              $('#Unit').val('');
              $('#Noted').val('');
              $('#LotNumber').val('');
              reload_table();
            } else if (data.status_code == 500)
            {
              Swal.fire({
                title: data.status,
                text: data.message,
                icon: data.status
              });
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
              }
            }
          },
          error: function() {
            alert('Error, Please try again!');
            $("#loading-screen").hide();
          }
        })
      }

      //FUNCTION CARI BERDASARKAN TANGGAL
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      function formatNumber(num) {
        let str = num.toString().replace('.', ',');

        return str.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      $(document).ready(function() {
        $('#LotNumber').prop('readonly', true);
        $("#DetailKurangi").hide();
        $("#DetailJob").hide();
        $("#IsiRak").hide();
        $("#DetailRak").hide();
        $("#loading-screen").hide();

        $('.autonumeric').autoNumeric('init');

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
            //'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          paging: true,
          fixedColumns: {
            leftColumns: 0,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url: "<?php echo base_url(); ?>scan_rak/daftar_items",
            type: 'POST',
            "data": function(data) {
              data.qr     = $('#code_barcode').val();
              data.baris  = $('#Baris').val();
            }
          },
          'aoColumns': [
            {
              "No": "No",
              "sClass": "text-right"
            },
            {
              "#": "#",
              "sClass": "text-right"
            },
            {
              "Rak": "Rak",
              "sClass": "text-center"
            },
            {
              "Baris": "Baris",
              "sClass": "text-center"
            },
            {
              "Part Name": "Part Name",
              "sClass": "text-left"
            },
            {
              "Stock": "Stock",
              "sClass": "text-right"
            },
            {
              "Unit": "Unit",
              "sClass": "text-left"
            },
            {
              "Part ID": "Part ID",
              "sClass": "text-left"
            },
            {
              "WH Lokasi": "WH Lokasi",
              "sClass": "text-center"
            },
            {
              "Noted": "Noted",
              "sClass": "text-left"
            },
            {
              "Created Date": "Created Date",
              "sClass": "text-right"
            },
            {
              "Created By": "Created By",
              "sClass": "text-center"
            }
          ],

          "columnDefs": [{
            "targets": [1], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        $('.select-ajax').select2({
          placeholder: 'Masukan Part Name atau Part ID',
          minimumInputLength: 3,
          ajax: {
            url: '<?php echo base_url(); ?>scan_rak/cari_partname',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                term: params.term
              };
            },
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true // Enable caching to improve performance
          }
        }).on('select2:select', function(e) {
          var selectedData  = e.params.data;
          let Unit          = selectedData.unit;
          $("#Unit").val(Unit);
        });

        $('.select-job').select2({
          placeholder: 'Masukan Nomor Job',
          minimumInputLength: 3,
          ajax: {
            url: '<?php echo base_url(); ?>scan_rak/cari_nomor_job',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                term: params.term
              };
            },
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true // Enable caching to improve performance
          }
        }).on('select2:select', function(e) {
          let Mpr       = e.params.data.Mpr;
          let BarisHtml = '';
          let i;
          for(i = 0; i < Mpr.length; i++){
            BarisHtml += '<li class="list-group-item bg-info d-flex">'+
                            '<input class="mr-1" type="checkbox" name="MprItems[]" value="'+ Mpr[i].NoBukti +'">'+ 
                              Mpr[i].NoBukti +
                         '</li>';
          }
          $('#MprItems').html(BarisHtml);
        });

        $('#Peruntukan').on('change', function() {
          var selectedValue = $(this).val();

          if (selectedValue == 'Job') {
            $("#DetailJob").show();
            $('#JobNomor').next(".select2-container").show();
            $('#MprItems').empty();
            $('#NotedEdit').val('Untuk '+ selectedValue);
          } else {
            $("#DetailJob").hide();
            $('#JobNomor').next(".select2-container").hide();
            $('#MprItems').empty();
            $('#NotedEdit').val('Untuk '+ selectedValue);
          }
        });

        $('#Status').on('change', function() {
          var selectedValue = $(this).val();
          let Text = selectedValue == 'IN' ? 'Penambah' : 'Pengurang';
          $("#LabelTambahan").html(Text);

          if (selectedValue == 'OUT') {
            $("#DetailKurangi").show();
            $('#MprItems').show();
            $('#NotedEdit').val('Stock keluar');
          } else {
            $("#DetailKurangi").hide();
            $('#MprItems').hide();
            $('#NotedEdit').val('Stock masuk');
          }
        });

        $('#RakTransfer').on('change', function() {
          var value = $(this).val();

          $.ajax({
            url: "<?php echo base_url(); ?>scan_rak/get_baris",
            data: {
              QrRak: value
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success: function(hasil) {
              $("#loading-screen").hide();
              let Details = hasil.data;
              if (Details.length > 1) {
                var BarisHtml = '';
                var i;
                BarisHtml += '<option selected value="">-- Pilih --</option>';
                for(i = 0; i < Details.length; i++){
                  BarisHtml += '<option value="'+ Details[i].QRCode +'">'+ Details[i].Sequent.trim() +'</option>';
                }
                $('#BarisTransfer').html(BarisHtml);
              }
            },
            error: function() {
              alert('Error, Please try again!');
              $("#loading-screen").hide();
            }
          })
        });

        $("#Baris").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Quantity").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#RakTransfer").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#BarisTransfer").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#QuantityTransfer").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>