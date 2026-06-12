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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
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
                              <h5><?php echo strtoupper($nama_halaman); ?> WAREHOUSE</h5>
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
                              <hr class="m-t-10 m-b-10">
                              <div class="dt-responsive table-responsive">
                                <h5 class="text-center">HASIL SCAN WAREHOUSE</h5>
                                <hr class="m-t-10 m-b-10">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter by</label>
                                  <div class="col-md-4 col-sm-12 m-t-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary text-white">
                                      <th class="text-center" width="1%">No</th>
                                      <th class="text-center" width="2%">#</th>
                                      <th class="text-center" width="5%">Sequent</th>
                                      <th class="text-center" width="22%">Barcode No.</th>
                                      <th class="text-center" width="25%">Part Name</th>
                                      <th class="text-center" width="15%">Part ID</th>
                                      <th class="text-center" width="20%">Supplier Name</th>
                                      <th class="text-center" width="10%">Create Date</th>
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
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
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
            var start = moment();
            var end   = moment();
            
            function cb(start, end) {
                var sd = start.format('YYYY-MM-DD');
                var ed = end.format('YYYY-MM-DD');
                
                $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                $('#start_date').val(start.format('YYYY-MM-DD'));
                $('#end_date').val(end.format('YYYY-MM-DD'));
            }
            
            $('#tanggal').daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: new Date(),
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
                }
            }, cb);
            
            cb(start, end);
        });
    </script>
    <script type="text/javascript">
      $(function() {
        $("#scanForm").submit(function() {
          const barcode_no = $("#barcode_no").val();
          $.ajax({
            url: "<?php echo base_url(); ?>scan_incoming_part/saving_scanning_part",
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
                $('#scanForm')[0].reset();
                reload_table();
              } else if (hasil.status_code == 400) {
                Swal.fire(
                  'Oops!',
                  hasil.message,
                  'error'
                );
              } else if (hasil.status == 'forbidden') {
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } else {
                Swal.fire(
                  'Oops!',
                  hasil.message,
                  'warning'
                );
              }

              $("#loading-screen").hide();
            }
          })
          return false;
        });
      });

      //FUNCTION HAPUS DATA
      function hapus_satu_barcode(id) {
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
              url: '<?php echo base_url(); ?>scan_incoming_part/hapus_satu_barcode',
              type: 'POST',
              data: {
                BarcodeNomor: id
              },
              error: function() {
                alert('Something is wrong');
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
                  //$("#" + id).remove();
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
            url: "<?php echo base_url(); ?>scan_incoming_part/scan_incoming_part_list",
            type: 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          'aoColumns': [
            { "No": "No" , "sClass": "text-right"},
            { "#": "#" , "sClass": "text-center" },
            { "Sequent": "Sequent" , "sClass": "text-center" },
            { "Barcode No.": "Barcode No." , "sClass": "text-left" },
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Part ID": "Part ID" , "sClass": "text-left" },
            { "Supplier Name": "Supplier Name" , "sClass": "text-left" },
            { "Create Date": "Create Date" , "sClass": "text-center" }
          ],
          "columnDefs": [
            { 
              "targets": [ 1 ],
              "orderable": false,
              className: 'text-end'
            }
          ]
        });
      });
    </script>
  </body>
</html>