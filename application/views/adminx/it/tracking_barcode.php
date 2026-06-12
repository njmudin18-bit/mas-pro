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
                            <div class="container">
                              <div class="row">
                                <div class="col-md-5 col-sm-12">
                                  <input type="text" name="barcode_ppic" id="barcode_ppic" class="form-control" placeholder="Masukan Barcode PPIC">
                                  <!-- <div class="input-group mb-3">
                                    <input type="text" name="barcode_ppic" id="barcode_ppic" class="form-control" placeholder="Masukan Barcode PPIC">
                                    <div class="input-group-append" onclick="open_modal_ppic();">
                                      <span class="input-group-text" id="basic-addon2">
                                        <i class="fa fa-solid fa-camera"></i>
                                      </span>
                                    </div>
                                  </div> -->
                                </div>
                                <div class="col-md-5 col-sm-12">
                                  <input type="text" name="barcode_sales" id="barcode_sales" class="form-control" placeholder="Masukan Barcode Sales">
                                  <!-- <div class="input-group mb-3">
                                    <input type="text" name="barcode_sales" id="barcode_sales" class="form-control" placeholder="Masukan Barcode Sales">
                                    <div class="input-group-append">
                                      <span class="input-group-text" id="basic-addon2">
                                        <i class="fa fa-solid fa-camera"></i>
                                      </span>
                                    </div>
                                  </div> -->
                                </div>
                                <div class="col-md-2 col-sm-12">
                                  <button type="button" onclick="cari_barcode()" class="btn btn-primary btn-block">Cari</button>
                                </div>
                              </div>
                            </div>
                            <hr>
                            <div class="dt-responsive table-responsive">
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="200%" border="2" cellpadding="0" cellspacing="0">
                                <thead class="bg-primary">
                                  <tr>
                                    <th class="text-center" rowspan="2">No.</th>
                                    <th class="text-center" rowspan="2">PPIC Barcode No.</th>
                                    <th class="text-center" rowspan="2">Job No.</th>
                                    <th class="text-center" colspan="2" rowspan="1">Produksi</th>
                                    <th class="text-center" colspan="2" rowspan="1">QC</th>
                                    <th class="text-center" colspan="2" rowspan="1">WH</th>
                                    <th class="text-center" rowspan="2">Sales Barcode No.</th>
                                    <th class="text-center" colspan="3" rowspan="1">Delivery</th>
                                    <th class="text-center" rowspan="2">PO No.</th>
                                    <th class="text-center" rowspan="2">DO No.</th>
                                    <th class="text-center" rowspan="2">Part ID.</th>
                                    <th class="text-center" rowspan="2">Part Name</th>
                                  </tr>
                                  <tr>
                                    <th class="text-center" rowspan="1" colspan="1">Scan By</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan Date</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan By</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan Date</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan By</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan Date</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan By</th>
                                    <th class="text-center" rowspan="1" colspan="1">Scan Date</th>
                                    <th class="text-center" rowspan="1" colspan="1">Driver</th>
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
  <div class="modal fade" id="modal_ppic" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Scan Barcode</h4>
          <button type="button" class="close" aria-label="Close" onclick="reset_all()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="text-center" style="background: #eee;">
            <video id="previewKamera" style="width: 300px;height: 300px;"></video>
            <br>
            <div class="form-group row justify-content-center">
              <select id="pilihKamera" class="form-control" style="width: 40%;">
              </select>
            </div>
          </div>
          <form id="scanForm">
            <div class="form-group row">
              <div class="col-sm-12">
                <input type="text" id="barcode_no" name="barcode_no" class="form-control" required="required" autocomplete="off" placeholder="Scan Barcode No">
                <span class="help-block"></span>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger">Close</button>
          <button id="btnSave" type="button" onclick="terapkan();" class="btn btn-primary waves-effect waves-light ">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
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
                $("#barcode_no").val(result.text);
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
    //MODAL PPIC
    function open_modal_ppic() {
      $('#modal_ppic').modal('show');
    }

    function reset_all() {
      $('#modal_ppic').modal('hide');
    }

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    //FUNCTION CARI
    function cari_barcode() {
      var barcode_ppic = $("#barcode_ppic").val();
      var barcode_sales = $("#barcode_sales").val();

      if (barcode_ppic == '' || barcode_ppic == null) {
        alert("Harap isi PPIC Barcode No.");
        $("#barcode_ppic").focus();
      } else {
        reload_table();
      }
    }

    $(document).ready(function() {
      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        buttons: [
          'excel'
          //'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        paging: true,
        'processing': true,
        'serverSide': false,
        'serverMethod': 'POST',
        'ajax': {
          url: "<?php echo base_url(); ?>tracking/tracking_list",
          type: 'POST',
          "data": function(data) {
            data.barcode_ppic = $('#barcode_ppic').val();
            data.barcode_sales = $('#barcode_sales').val();
          }
        },

        'aoColumns': [{
            "No": "No",
            "sClass": "text-right"
          },
          {
            "PPIC Barcode No.": "PPIC Barcode No.",
            "sClass": "text-left"
          },
          {
            "Job No.": "Job No.",
            "sClass": "text-left"
          },
          {
            "Scan By": "Scan By",
            "sClass": "text-left"
          },
          {
            "Scan Date": "Scan Date",
            "sClass": "text-left"
          },
          {
            "Scan By": "Scan By",
            "sClass": "text-left"
          },
          {
            "Scan Date": "Scan Date",
            "sClass": "text-left"
          },
          {
            "Scan By": "Scan By",
            "sClass": "text-left"
          },
          {
            "Scan Date": "Scan Date",
            "sClass": "text-left"
          },
          {
            "Sales Barcode No.": "Sales Barcode No.",
            "sClass": "text-left"
          },
          {
            "Scan Date": "Scan Date",
            "sClass": "text-left"
          },
          {
            "Scan By": "Scan By",
            "sClass": "text-left"
          },
          {
            "Driver": "Driver",
            "sClass": "text-left"
          },
          {
            "PO No.": "PO No.",
            "sClass": "text-left"
          },
          {
            "DO No.": "DO No.",
            "sClass": "text-left"
          },
          {
            "Part ID": "Part ID",
            "sClass": "text-left"
          },
          {
            "Part Name": "Part Name",
            "sClass": "text-left"
          }
        ]
      });
    });
  </script>
</body>

</html>