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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                              </h5>
                            </div>
                            <div class="card-block">
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
                                  <input type="search" id="barcode_no" name="barcode_no" class="form-control form-control-round form-control-uppercase text-center form-control-lg form-txt-danger form-control-danger form-search" autofocus="on" autocomplete="off" placeholder="SCAN BARCODE DISINI"><!-- readonly="readonly" -->
                                </div>
                              </form>
                              <form id="attributeForm">
                                <div class="row">
                                  <div class="col-md-6 mt-2">
                                    <input type="search" value="" name="MaterialBarcode" id="MaterialBarcode" class="form-control" placeholder="Material" readonly>
                                  </div>
                                  <div class="col-md-6 mt-2">
                                    <input type="search" value="" name="MachineID" id="MachineID" class="form-control" placeholder="Mesin" readonly>
                                  </div>
                                  <div class="col-md-6 mt-2">
                                    <input type="search" value="" name="BobinID" id="BobinID" class="form-control" placeholder="Bobin" readonly>
                                  </div>
                                  <div class="col-md-6 mt-2">
                                    <input type="search" value="" name="JobBarcode" id="JobBarcode" class="form-control" placeholder="Nomor Job" readonly>
                                  </div>
                                  <div class="col-md-6 mt-2">
                                    <button type="button" onclick="SimpanData()" class="btn btn-primary btn-full-mobile">SIMPAN DATA</button>
                                  </div>
                                </div>
                              </form>
                              <hr class="m-t-20 m-b-20">
                              <div class="dt-responsive table-responsive">
                                <h5 class="text-center">HASIL SCAN EXTRUDE</h5>
                                <hr class="m-t-20 m-b-20">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter data by</label>
                                  <div class="col-md-2 col-sm-12 m-t-10">
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
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="bulan" id="bulan" required="required">
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
                                  <div class="col-md-2 col-sm-12 m-t-10">
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
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">No.</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">Job Number</th>
                                      <th class="text-center">Bobin</th>
                                      <th class="text-center">Mesin</th>
                                      <th class="text-center">Part ID</th>
                                      <th class="text-center">Part Name</th>
                                      <th class="text-center">Scanned Date</th>
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
      $(function() {
        $("#scanForm").submit(function() {
          let BarcodeNumber   = $("#barcode_no").val();
          let ExplodedArray   = BarcodeNumber.split('/');
          let ArrayData       = [];
          if (ExplodedArray[0] == 'MT-PO') {
            $("#MaterialBarcode").val(BarcodeNumber);   //READ MATERIAL LABEL
          } else if (ExplodedArray[0] == '|PCG') {
            $("#JobBarcode").val(BarcodeNumber);        //READ JOB LABEL
          } else if (ExplodedArray[0].substring(0, 2) == 'MS') {
            $("#MachineID").val(BarcodeNumber);         //READ MACHINE LABEL
          } else {
            $("#BobinID").val(BarcodeNumber);           //READ BOBIN LABEL
          }
          //CLEAR BOX AND SETUP TO AUTOFOCUS
          $("#barcode_no").val("").focus();

          return false;
        });
      });

      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });

      //FUNCTION SIMPAN DATA
      function SimpanData() {
        var SaveData = $('#attributeForm').serializeArray();
        for (var i = 0; i < SaveData.length; i++) {
          var field = SaveData[i];
          // Check if the value of the field is empty
          if (field.value.trim() === '') {
            //console.log(field.name + ' is empty');
            // Optionally, you can handle empty fields here
            Toast.fire({
              icon: "error",
              title: field.name + " masih kosong!"
            });

            return false;
          }
        }

        $.ajax({
          url: "<?php echo base_url(); ?>extrude/save_barcode_extrude",
          data: SaveData,
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success: function(data) {
            $("#loading-screen").hide();
            $("#barcode_no").val("").focus();
            if (data.status_code == 200) {
              reload_table();
              $('#attributeForm')[0].reset();
            } else {
              Swal.fire(
                'FORBIDDEN',
                data.message,
                data.status,
              )
            }
          },
          error: function() {
            alert('Something is wrong');
            $("#loading-screen").hide();
          }
        })
      }

      //FUNCTION HAPUS DATA
      function HapusData(id) {
        //console.log(id);
        Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Tidak, batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>extrude/delete_extrude_barcode',
              type: 'POST',
              data: {
                Id: id
              },
              error: function() {
                alert('Something is wrong');
              },
              success: function(data) {
                var result = JSON.parse(data);
                console.log(result);
                if (result.status_code == 403) {
                  Swal.fire(
                    'FORBIDDEN',
                    result.message,
                    result.status,
                  )
                } else if (result.status == 'error') {
                  Swal.fire(
                    'Oops',
                    result.message,
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

      //FUNCTION CARI BERDASARKAN TANGGAL
      function cari() {
        //SET JENIS PART INTO LOCAL STORAGE
        //UNTUK DEFAULT PILIHAN, CUKUP PILIH SEKALI
        var jenis_part = $('#jenis_part').val();
        localStorage.setItem("jenis_part", jenis_part);

        reload_table();
      }

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
          ],
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url: "<?php echo base_url(); ?>extrude/show_extrude_list",
            type: 'POST',
            "data": function(data) {
              data.bulan    = $('#bulan').val();
              data.tahun    = $('#tahun').val();
              data.tanggal  = $('#tanggal').val();
            }
          },
          'aoColumns': [
            {
              "No.": "No.",
              "sClass": "text-right"
            },
            {
              "#": "#",
              "sClass": "text-center"
            },
            {
              "Job Number": "Job Number",
              "sClass": "text-left"
            },
            {
              "Bobin": "Bobin",
              "sClass": "text-left"
            },
            {
              "Machine": "Machine",
              "sClass": "text-left"
            },
            {
              "Part ID": "Part ID",
              "sClass": "text-left"
            },
            {
              "Part Name": "Part Name",
              "sClass": "text-left"
            },
            {
              "Scanned Date": "Scanned Date",
              "sClass": "text-left"
            },
          ],

          "columnDefs": [
            {
              "targets": [ 1 ],
              "orderable": false,
              className: 'text-right'
            }
          ]
        });
      });
    </script>
  </body>
</html>