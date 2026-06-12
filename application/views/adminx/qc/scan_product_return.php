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

    <?php //$this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/select_jquery.dataTables.min.css"> -->
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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?> QC
                              </h5>
                            </div>
                            <div class="card-block m-t-30 m-b-30">
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
                                  <input type="search" id="barcode_no" name="barcode_no" class="form-control form-control-round form-control-uppercase text-center form-control-lg form-txt-danger form-control-danger form-search" autofocus="on" autocomplete="off" placeholder="SCAN BARCODE DISINI">
                                </div>
                              </form>
                              <hr class="m-t-50 m-b-20">
                              <div class="dt-responsive table-responsive">
                                <h5 class="text-center">HASIL SCAN QC</h5>
                                <hr class="m-t-20 m-b-20">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter data by</label>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select name="tanggal" id="tanggal" class="form-control">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Pilih --</option>
                                      <?php
                                      $now = date('d');
                                      for ($day = 1; $day <= 31; $day++) {
                                        if ($day <= 9) {
                                          if ($day == $now) {
                                            echo "<option selected value = '0" . $day . "'>0" . $day . "</option>";
                                          } else {
                                            echo "<option value = '0" . $day . "'>0" . $day . "</option>";
                                          }
                                        } else {
                                          if ($day == $now) {
                                            echo "<option selected value = '" . $day . "'>" . $day . "</option>";
                                          } else {
                                            echo "<option value = '" . $day . "'>" . $day . "</option>";
                                          }
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="bulan" id="bulan" required="required">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Bulan --</option>
                                      <?php
                                      $now  = new DateTime('now');
                                      $bln1 = $now->format('m');
                                      for ($m = 1; $m <= 12; ++$m) {
                                        if ($bln1 == $m) {
                                          echo '<option selected value=' . $m . '>' . date('F', mktime(0, 0, 0, $m, 1)) . '</option>' . "\n";
                                        } else {
                                          echo '<option  value=' . $m . '>' . date('F', mktime(0, 0, 0, $m, 1)) . '</option>' . "\n";
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="tahun" id="tahun" required="required">
                                      <option disabled="disabled">-- Tahun --</option>
                                      <?php
                                      $now    = new DateTime('now');
                                      $year1  = $now->format('Y');
                                      for ($year = 2022; $year <= 2050; ++$year) {
                                        if ($year1 == $year) {
                                          echo '<option selected value=' . $year . '>' . $year . '</option>' . "\n";
                                        } else {
                                          echo '<option  value=' . $year . '>' . $year . '</option>' . "\n";
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="jenis_part" id="jenis_part" required="required">
                                      <option disabled="disabled">-- Pilih --</option>
                                      <option value="All" selected="selected">All</option>
                                      <option value="Power Cord">Power Cord</option>
                                      <option value="Wiring">Wiring</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">NO.</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">NO. JOB</th>
                                      <th class="text-center">DATE/ TIME SCANNED</th>
                                      <th class="text-center">PART ID.</th>
                                      <th class="text-center">PART NAME</th>
                                      <th class="text-center">QTY. JOB</th>
                                      <th class="text-center">QTY. BOX</th>
                                      <th class="text-center">UNIT ID</th>
                                      <th class="text-center">DATE CREATED</th>
                                      <th class="text-center">LOC. RESULT</th>
                                      <th class="text-center">NOTES</th>
                                      <th class="text-center">BARCODE NO.</th>
                                      <th class="text-center">SCANNED BY</th>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/dataTables.select.min.js"></script>
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

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading-screen").hide();

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
            //'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          paging: true,
          select: true,
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url: "<?php echo base_url(); ?>qc/show_barcode_data_list",
            type: 'POST',
            "data": function(data) {
              data.bulan = $('#bulan').val();
              data.tahun = $('#tahun').val();
              data.tanggal = $('#tanggal').val();
              data.jenis_part = $('#jenis_part').val();
            }
          },

          'aoColumns': [{
              "NO.": "NO.",
              "sClass": "text-right"
            },
            {
              "#": "#",
              "sClass": "text-center"
            },
            {
              "STATUS": "STATUS",
              "sClass": "text-center"
            },
            {
              "NO. JOB": "NO. JOB",
              "sClass": "text-left"
            },
            {
              "DATE/ TIME SCANNED": "DATE/ TIME SCANNED",
              "sClass": "text-center"
            },
            {
              "PART ID.": "PART ID.",
              "sClass": "text-left"
            },
            {
              "PART NAME": "PART NAME",
              "sClass": "text-left"
            },
            {
              "QTY. JOB": "QTY. JOB",
              "sClass": "text-right"
            },
            {
              "QTY. BOX": "QTY. BOX",
              "sClass": "text-right"
            },
            {
              "UNIT ID": "UNIT ID",
              "sClass": "text-center"
            },
            {
              "DATE CREATED": "DATE CREATED",
              "sClass": "text-right"
            },
            {
              "LOC. RESULT": "LOC. RESULT",
              "sClass": "text-center"
            },
            {
              "NOTES": "NOTES",
              "sClass": "text-center"
            },
            {
              "BARCODE NO.": "BARCODE NO.",
              "sClass": "text-center"
            },
            {
              "SCANNED BY": "SCANNED BY",
              "sClass": "text-left"
            }
          ],

          "columnDefs": [{
            "targets": [1],
            "orderable": false,
            className: 'text-right'
          }, ]
        });
      });
    </script>
  </body>
</html>