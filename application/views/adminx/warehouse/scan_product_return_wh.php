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
    <style>
      .btn i {
        margin-right: 0px;
      }

      .font-24 {
        font-size: 24px;
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
                              <div class="dt-responsive table-responsiveXX">
                                <h5 class="text-center">HASIL SCAN</h5>
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
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <div class="row">
                                  <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="col-form-label">Nomor DO</label>
                                    <input type="text" class="form-control text-uppercase" id="NomorDO" name="NomorDO" placeholder="Nomor DO" maxlength="25">
                                  </div>
                                  <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="col-form-label">Qty. Return</label>
                                    <input type="text" class="form-control" id="QtyReturn" name="QtyReturn" placeholder="Qty. Return" maxlength="9">
                                  </div>
                                  <div class="col-md-10 col-sm-12">
                                    <label class="col-form-label">Keterangan Reject</label>
                                    <input type="text" class="form-control" id="KeteranganReject" name="KeteranganReject" placeholder="Keterangan Reject" maxlength="255">
                                  </div>
                                  <div class="col-md-2 col-sm-12 mb-3">
                                    <label class="col-form-label">&nbsp;</label>
                                    <button type="button" onclick="simpan_data()" class="btn btn-danger btn-block">Simpan</button>
                                  </div>
                                </div>
                                <hr>
                                <div class="row">
                                  <div class="table-responsive">
                                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                      <thead>
                                        <tr class="bg-primary">
                                          <th class="text-center">No.</th>
                                          <th class="text-center">#</th>
                                          <th class="text-center">Qty. Box</th>
                                          <th class="text-center">No. Job</th>
                                          <th class="text-center">Qty. Job</th>
                                          <th class="text-center">Loc. ID</th>
                                          <th class="text-center">Part ID</th>
                                          <th class="text-center">Part Name</th>
                                          <th class="text-center">Barcode No</th>
                                          <th class="text-center">Urutan</th>
                                          <th class="text-center">Create Date</th>
                                        </tr>
                                      </thead>
                                      <tbody></tbody>
                                      <tfoot>
                                        <tr class="bg-primary">
                                          <th colspan="2">TOTAL</th>
                                          <th class="font-24"></th>
                                          <th colspan="8"></th>
                                        </tr>
                                      </tfoot>
                                    </table>
                                  </div>
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
      $(function() {
        $("#scanForm").submit(function() {
          const barcode_no = $("#barcode_no").val();
          $.ajax({
            url: "<?php echo base_url(); ?>returns/save_barcode_temp",
            data: {
              barcode_no: barcode_no
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success: function(hasil) {
              if (hasil.status_code == 200) {
                $("#loading-screen").hide();
                $('#scanForm')[0].reset();
                reload_table();
              } else {
                Swal.fire(
                  hasil.status,
                  hasil.message,
                  hasil.status
                );
                $('#scanForm')[0].reset();
                $("#loading-screen").hide();
              }
            }
          })
          return false;
        });
      });

      function simpan_data() {
        let NomorDO = $("#NomorDO").val();
        if (NomorDO === "" || NomorDO === null) {
          alert("Nomor DO harus diisi!");
          $("#NomorDO").focus();
        } else if (NomorDO.length < 10) {
          alert("Cek Nomor DO");
          $("#NomorDO").focus();
        } else {
          $.ajax({
            url: "<?php echo base_url(); ?>returns/save_data",
            data: {
              nomor_do: $("#NomorDO").val(),
              qty_return: $("#QtyReturn").val(),
              keterangan: $("#KeteranganReject").val()
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading").show();
            },
            success: function(data) {
              $("#loading").hide();
              if (data.status == 'forbidden'){
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } else {
                Swal.fire(
                  data.status,
                  data.message,
                  data.status,
                );
                reload_table();
                $("#NomorDO").val();
                $("#QtyReturn").val();
                $("#KeteranganReject").val();
              }
            }, 
            error: function() {
              alert('Oops something went wrong');
            }
          })
        }
      }

      function hapus_data_temp(ID) {
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
              url: '<?php echo base_url(); ?>returns/delete_data',
              type: 'POST',
              data: {
                id: ID
              },
              success: function(data) {
                var result = JSON.parse(data);
                if (result.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else if (result.status == 'error') {
                  Swal.fire(
                    'Oops',
                    result.message,
                    'info',
                  )
                } else {
                  $("#" + ID).remove();
                  reload_table();
                }
              },
              error: function() {
                alert('Oops something went wrong');
              },
            });
          }
        })
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
            //'excel'
          ],
          paging: true,
          select: true,
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url: "<?php echo base_url(); ?>returns/scan_product_return_list",
            type: 'POST',
            "data": function(data) {
              data.bulan    = $('#bulan').val();
              data.tahun    = $('#tahun').val();
              data.tanggal  = $('#tanggal').val();
            }
          },
          "footerCallback": function(row, data, start, end, display) {
            var api = this.api(),
              data;

            // converting to interger to find total
            var intVal = function(i) {
              return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
            };

            // computing column Total of the complete result 
            var total_qty = api
            .column(2)
            .data()
            .reduce(function(a, b) {
              return intVal(a) + intVal(b);
            }, 0);

            $("#QtyReturn").val(formatNumber(total_qty));

            // Update footer by showing the total with the reference of the column index 
            $(api.column(0).footer()).html('TOTAL');
            $(api.column(2).footer()).html(formatNumber(total_qty));
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
              "Qty. Box": "Qty. Box",
              "sClass": "text-right"
            },
            {
              "No. Job": "No. Job",
              "sClass": "text-left"
            },
            {
              "Qty. Job": "Qty. Job",
              "sClass": "text-right"
            },
            {
              "Loc. ID": "Loc. ID",
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
              "Barcode No": "Barcode No",
              "sClass": "text-left"
            },
            {
              "Urutan": "Urutan",
              "sClass": "text-right"
            },
            {
              "Create Date": "Create Date",
              "sClass": "text-right"
            }
          ],
          "columnDefs": [{
            "targets": [1],
            "orderable": false,
            className: 'text-right'
          }, ]
        });

        function formatNumber(n) {
          return n.toLocaleString();
        }
      });
    </script>
  </body>
</html>