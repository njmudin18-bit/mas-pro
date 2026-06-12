<?php
  defined('BASEPATH') OR exit('No direct script access allowed');
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
                                  <input type="search" id="barcode_no" name="barcode_no" class="form-control form-control-round form-control-uppercase text-center form-control-lg form-txt-danger form-control-danger form-search" autofocus="on" autocomplete="off" placeholder="SCAN BARCODE DISINI" readonly="readonly" value="" ><!-- value="|PCG/JOB/12/202212/00001-001|001|-|0|WH-GRS01|15000|200|-|" -->
                                </div>
                              </form>
                              <hr class="m-t-50 m-b-50">
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
                                        for($day = 1; $day <= 31; $day++){
                                          if ($day <= 9) {
                                            if ($day == $now) {
                                              echo "<option selected value = '0".$day."'>0".$day."</option>";
                                            } else {
                                              echo "<option value = '0".$day."'>0".$day."</option>";
                                            }
                                          } else {
                                            if ($day == $now) {
                                              echo "<option selected value = '".$day."'>".$day."</option>";
                                            } else {
                                              echo "<option value = '".$day."'>".$day."</option>";
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
                                        for ($m = 1; $m <= 12; ++$m){
                                          if ($bln1 == $m){
                                            echo '<option selected value='.$m.'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
                                          }else{
                                            echo '<option  value='.$m.'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
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
                                        for ($year = 2022; $year <= 2050; ++$year){
                                          if ($year1 == $year){
                                            echo '<option selected value='.$year.'>'.$year.'</option>'."\n";
                                          }else{
                                            echo '<option  value='.$year.'>'.$year.'</option>'."\n";
                                          }
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">#</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">BARCODE NO</th>
                                      <th class="text-center">LOC. ID</th>
                                      <th class="text-center">SCAN BY</th>
                                      <th class="text-center">SCAN DATE</th>
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

    <!-- MODAL SAVE -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Aprroved</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Barcode No.</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="no_barcode" id="no_barcode" readonly="readonly">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                  <select id="status" name="status" class="form-control">
                    <option disabled="disabled">-- Plih --</option>
                    <option value="OK" selected="selected">OK</option>
                    <option value="NG">NG</option>
                    <option value="SA">SA</option>
                  </select>
                  <span class="help-block"></span>

                  <input type="hidden" name="status_old" id="status_old">
                </div>
              </div>
              <div id="show_details">
                <hr>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Penyebab NG</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="penyebab" id="penyebab" autocomplete="off">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Qty.</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="qty" id="qty" maxlength="4" autocomplete="off" onkeypress="return isNumber(event)">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">PIC Repair</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control text-capitalize" name="pic_repair" id="pic_repair">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
            <button id="btnSave" type="button" onclick="save_transaksi();" class="btn btn-primary waves-effect waves-light ">Approved</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL VIEW NG DETAIL -->
    <div class="modal fade" id="modal_ng" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Detail Produk NG</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_all()">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body">
            <div class="dt-responsive table-responsive">
              <table class="table table-bordered nowrap">
                <thead>
                  <tr class="bg-info">
                    <th class="text-center">No</th>
                    <th class="text-center">Barcode No.</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Qty.</th>
                    <th class="text-center">PIC Repair</th>
                    <th class="text-center">Tanggal</th>
                  </tr>
                </thead>
                <tbody id="data_ng">
                  
                </tbody>
              </table>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <button id="btn_tambah" type="button" class="btn btn-warning text-white float-right">TAMBAH</button>
              </div>
            </div>
            <form id="more_ng_form">
              <div id="contentku">
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Barcode No.</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="no_barcode_view" id="no_barcode_view" readonly="readonly">

                    <input type="hidden" name="scan_id_view" id="scan_id_view">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="status_view" id="status_view" readonly="readonly">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Penyebab NG</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="penyebab_view" id="penyebab_view">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">Qty.</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="qty_view" id="qty_view" maxlength="4" autocomplete="off" onkeypress="return isNumber(event)">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label">PIC Repair</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control text-capitalize" name="pic_repair_view" id="pic_repair_view">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-2 col-form-label"></label>
                  <div class="col-sm-10">
                    <button type="button" class="btn btn-primary" onclick="save_more_ng()">SIMPAN</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal" onclick="reset_all()">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script type="text/javascript">
      let selectedDeviceId  = null;
      const codeReader      = new ZXing.BrowserMultiFormatReader();
      const sourceSelect    = $("#pilihKamera");

      $(document).on('change', '#pilihKamera', function(){
        selectedDeviceId = $(this).val();
        if(codeReader){
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

          if(videoInputDevices.length > 0){
               
            if(selectedDeviceId == null){
              if(videoInputDevices.length > 1){
                selectedDeviceId = videoInputDevices[1].deviceId
              } else {
                selectedDeviceId = videoInputDevices[0].deviceId
              }
            }
             
             
            if (videoInputDevices.length >= 1) {
              sourceSelect.html('');
              videoInputDevices.forEach((element) => {
                const sourceOption  = document.createElement('option')
                sourceOption.text   = element.label
                sourceOption.value  = element.deviceId
                if(element.deviceId == selectedDeviceId){
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
                if(codeReader){
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
      function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
      }

      //FUNCTION SCAN BARCODE
      $(function(){
        $("#scanForm").submit(function(){
          const barcode_no = $("#barcode_no").val();
          $.ajax({
            url:  "<?php echo base_url(); ?>qc/cek_barcode",
            data: {barcode_no: barcode_no},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success:function(hasil) {
              $("#loading-screen").hide();
              if (hasil.status_code == 200) {

                if (hasil.data != null) {
                  open_modal(barcode_no);
                  $('#status').val(hasil.data.scan_status);
                  $('#status_old').val(hasil.data.scan_status);
                  console.log(hasil.data.scan_status);
                } else {
                  $('#status').val("OK");
                  open_modal(barcode_no);
                  //$('#status').val(hasil.data.scan_status);
                  //$('#status_old').val(hasil.data.scan_status);
                  //console.log(hasil.data.scan_status);
                }
                
              } else {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'error',
                )
              }
            }
          })
          return false;
        });
      });

      //RESET ALL MODAL FORM
      function reset_all() {
        $("#contentku").hide();
        $("#btn_tambah").text("TAMBAH");
      }

      //SAVE MORE NG
      function save_more_ng() {
        var penyebab        = $("#penyebab_view").val();
        var qty             = $("#qty_view").val();
        var pic_repair_view = $("#pic_repair_view").val();
        var data            = $("#more_ng_form").serialize();
        if (penyebab == '' || penyebab == null) {
          alert("Penyebab tidak boleh kosong");
          $("#penyebab_view").focus();
        } else if (qty == '' || qty == null) {
          alert("Qty tidak boleh kosong");
          $("#qty_view").focus();
        } else if (pic_repair_view == '' || pic_repair_view == null) {
          alert("PIC Repair tidak boleh kosong");
          $("#pic_repair_view").focus();
        } else {

          $.ajax({
            url:  "<?php echo base_url(); ?>qc/save_more_ng",
            data: data,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success:function(hasil) {

              if (hasil.status_code == 200) {
                $("#loading-screen").hide();
                $('#more_ng_form')[0].reset();
                $("#contentku").hide();
                $("#btn_tambah").text("TAMBAH");
                view_details(hasil.data.scan_id)
              } else {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'warning'
                );
                $("#loading-screen").hide();
              }
            }
          })
        }
      }

      //FUNCTION SHOW HIDE BUTTON TAMBAH/ SEMBUNYIKAN
      $("#btn_tambah").click(function(ev) {
        var a = $(this).text();
        setTimeout(function() { 
          document.getElementById("contentku").scrollIntoView( {behavior: "smooth" });
          if (a == 'TAMBAH') {
            $("#btn_tambah").text("SEMBUNYIKAN");
          } else {
            $("#btn_tambah").text("TAMBAH");
          }
        }, 0);
        $("#contentku").toggle("slow");
      });

      //FUNCTION CARI BERDASARKAN TANGGAL
      function cari() {
        reload_table();
      }

      //FUNCTION CHECK STATUS
      $("#status").change(function(){
        var selValue = $(this).val();
        if (selValue == 'NG') {
          $("#show_details").show();
        } else {
          $("#show_details").hide();
        }
      });

      //FUNCTION SAVE TRANSAKSI
      function save_transaksi() {
        var status      = $("#status").val();
        var penyebab    = $("#penyebab").val();
        var qty         = $("#qty").val();
        var pic_repair  = $("#pic_repair").val();
        var data        = $("#RegisterValidation").serialize();
        if (status == 'NG') {
          if (penyebab == '' || penyebab == null) {
            alert("Penyebab tidak boleh kosong");
            $("#penyebab").focus();
          } else if (qty == '' || qty == null) {
            alert("Qty NG tidak boleh kosong");
            $("#qty").focus();
          } else if (pic_repair == '' || pic_repair == null) {
            alert("PIC Repair tidak boleh kosong");
            $("#pic_repair").focus();
          } else {

            var data = $("#RegisterValidation").serialize();
            $.ajax({
              url:  "<?php echo base_url(); ?>qc/save_barcode",
              data: data,
              type: 'POST',
              dataType: 'JSON',
              beforeSend: function() {
                $("#loading-screen").show();
              },
              success:function(hasil) {
                if (hasil.status_code == 200) {
                  $("#loading-screen").hide();
                  $('#RegisterValidation')[0].reset();
                  $('#scanForm')[0].reset();
                  $("#show_details").hide();
                  reload_table();
                  reset_all();
                  $('#modal').modal('hide');
                } else if(hasil.status_code == 400) {
                  Swal.fire(
                    'Oops',
                    hasil.message,
                    'error'
                  );
                  $("#loading-screen").hide();
                  $('#modal').modal('hide');
                  $('#scanForm')[0].reset();
                } else if (hasil.status == 'forbidden') {
                  $("#loading-screen").hide();
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  Swal.fire(
                    'Oops',
                    hasil.message,
                    'warning'
                  );
                  $("#loading-screen").hide();
                  $('#modal').modal('hide');
                  $('#scanForm')[0].reset();
                }
                
              }
            })
          }
        } else {
          $.ajax({
            url:  "<?php echo base_url(); ?>qc/save_barcode",
            data: data,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success:function(hasil) {
              if (hasil.status_code == 200) {
                $("#loading-screen").hide();
                $('#RegisterValidation')[0].reset();
                $('#scanForm')[0].reset();
                $("#show_details").hide();
                reload_table();
                reset_all();
                $('#modal').modal('hide');
              } else if(hasil.status_code == 400) {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'error'
                );
                $("#loading-screen").hide();
                $('#modal').modal('hide');
                $('#scanForm')[0].reset();
              } else if (hasil.status == 'forbidden') {
                $("#loading-screen").hide();
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } else {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'warning'
                );
                $("#loading-screen").hide();
                $('#modal').modal('hide');
                $('#scanForm')[0].reset();
              }
            }
          })
        }
      }

      //FUNCTION VIEW DETAIL NG
      function view_details(scan_id, job_no) {
        console.log(job_no);
        $.ajax({
          url:  "<?php echo base_url(); ?>qc/view_product_ng",
          data: {scan_id: scan_id},
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            $("#loading-screen").show();
          },
          success:function(hasil) {
            if (hasil.status_code == 200) {
              $('#scan_id_view').val(hasil.header.scan_id);
              $('#no_barcode_view').val(hasil.header.barcode_no);
              $('#status_view').val(hasil.header.scan_status);

              $("#data_ng").html(hasil.html);
              $("#loading-screen").hide();
              $('#modal_ng').modal('show');
            } else {
              Swal.fire(
                'Oops',
                hasil.message,
                'warning'
              );
              $("#loading-screen").hide();
            }
          }
        })
      }

      //FUNCTION OPEN MODAL
      function open_modal(barcode_no) {
        $('#no_barcode').val(barcode_no);
        $('#modal').modal('show');
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

      $(document).ready(function() {
        $("#show_details").hide();
        $("#contentku").hide();
        $("#loading-screen").hide();

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
            //'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url : "<?php echo base_url(); ?>qc/show_barcode_data_list",
            type : 'POST',
            "data": function(data) {
              data.bulan    = $('#bulan').val();
              data.tahun    = $('#tahun').val();
              data.tanggal  = $('#tanggal').val();
            }
          },

          'aoColumns': [
            { "NO": "NO" , "sClass": "text-right"},
            { "STATUS": "STATUS" , "sClass": "text-center" },
            { "BARCODE NO": "BARCODE NO" , "sClass": "text-left" },
            { "LOC. ID": "LOC. ID" , "sClass": "text-left" },
            { "SCAN BY": "SCAN BY" , "sClass": "text-center" },
            { "SCAN DATE": "SCAN DATE" , "sClass": "text-left" }
          ],

          "columnDefs": [
            { 
              "targets": [ 1 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        });
      });
    </script>
  </body>
</html>