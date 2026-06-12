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

    <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
    <style>
      #modal_shift_detail, #modal_detail, #modal_jam_transaksi  {
        overflow-y: scroll;
        height: 100vh;
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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                              </h5>
                            </div>
                            <div class="card-block special">
                              <div class="dt-responsive table-responsiveX">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 mt-2 col-form-label">Filter by</label>
                                  <div class="col-md-2 col-sm-12 mt-2">
                                    <div class="input-group">
                                      <select name="pilihan" id="pilihan" class="form-control">
                                        <option disabled>-- Pilih --</option>
                                        <option value="all" selected>All</option>
                                        <option value="pc">Power Cord</option>
                                        <option value="wiring">Wiring</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-4 col-sm-12 mt-2">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                  </div>
                                  <div class="col-md-3 col-sm-12 mt-2">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">Tampilkan</button>
                                    <!-- <a target="_blank" class="btn btn-warning btn-full-mobile" href='<?php echo base_url(); ?>lhp_fg'>Rincian</a> -->
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">No.</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Job No. & Part ID</th>
                                      <th class="text-center bg-primary">Tgl. Job</th>
                                      <th class="text-center bg-primary">Scan WH</th>
                                      <th class="text-center bg-primary">Total Qty. Scan WH</th>
                                      <th class="text-center bg-primary">Qty. Job</th>
                                      <th class="text-center bg-primary">Status Job</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr class="bg-info">
                                      <th colspan="5">GRAND TOTAL</th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                    </tr>
                                  </tfoot>
                                </table>
                                <!-- <button class="btn btn-danger btn-sm pull-right mr-2" id="button-hapus">Hapus row</button> -->
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

    <!-- MODAL JAM SCAN WH -->
    <div class="modal fade" id="modal_jam_transaksi" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Jam Transaksi</h5>
            <button onclick="ShowBackTransaksi()" type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
	        </div>
	        <div class="modal-body">
            <div class="container bg-secondary text-white">
              <div class="row">
                <div class="col-md-2 col-sm-12 mt-2 mb-2">
                  <h5>Job Number</h5>
                </div>
                <div class="col-md-10 col-sm-12 mt-2 mb-2">
                  <h5 id="JamJobNumber">-</h5>
                </div>
              </div>
            </div>
	          <div class="table-responsive mt-2">
              <table class="table table-bordered table-striped" width="100%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center" width="5%">No</th>
                    <th class="text-center" width="20%">Part ID</th>
                    <th class="text-center">Part Name</th>
                    <th class="text-center" width="8%">Sub Total</th>
                    <th class="text-center" width="12%">Tgl. Scan</th>
                    <th class="text-center" width="12%">Jam. Scan</th>
                  </tr>
                </thead>
                <tbody id="isi_jam_content"></tbody>
                <tfoot id="isi_jam_footer"></tfoot>
              </table>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>

    <!-- MODAL DETAIL SCAN WH -->
    <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Transaksi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
	        </div>
	        <div class="modal-body">
            <div class="container bg-secondary text-white">
              <div class="row">
                <div class="col-md-2 col-sm-12 mt-2 mb-2">
                  <h5>Part Name</h5>
                </div>
                <div class="col-md-10 col-sm-12 mt-2 mb-2">
                  <h5 id="partName">-</h5>
                </div>
              </div>
            </div>
	          <div class="table-responsive mt-2">
              <table class="table table-bordered table-striped" width="100%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center" width="5%">No</th>
                    <th class="text-center" width="20%">Job No.</th>
                    <th class="text-center" width="20%">Part ID</th>
                    <th class="text-center" width="8%">Sub Total</th>
                    <th class="text-center" width="12%">Tgl. Scan</th>
                  </tr>
                </thead>
                <tbody id="isi_content"></tbody>
                <tfoot id="isi_footer"></tfoot>
              </table>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>

    <!-- MODAL DETAIL INPUT DATA SHIFT -->
    <div class="modal fade" id="modal_shift_detail" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Job <span class="text-danger" id="no_jobs"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
	        </div>
	        <div class="modal-body">
            <div class="container">
              <div class="row mb-2">
                <div class="col-md-2 col-4">
                  <p>Part ID</p>
                </div>
                <div class="col-md-4 col-8">
                  <p id="part_id">-</p>
                </div>
                <div class="col-md-2 col-4">
                  <p>Part Name</p>
                </div>
                <div class="col-md-4 col-8">
                  <p id="part_name">-</p>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 col-4">
                  <p class="font-weight-bold text-danger">Job No</p>
                </div>
                <div class="col-md-4 col-8">
                  <p class="font-weight-bold text-danger" id="job_no">-</p>
                </div>
                <div class="col-md-2 col-4">
                  <p class="font-weight-bold text-danger">Qty. Order</p>
                </div>
                <div class="col-md-4 col-8">
                  <p class="font-weight-bold text-danger" id="qty_order">-</p>
                </div>
              </div>
            </div>
	          <div class="table-responsive mt-2">
              <table class="table table-bordered table-striped" width="110%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center" width="100">No</th>
                    <th class="text-center" width="10">#</th>
                    <th class="text-center" width="100">Tgl. Produksi</th>
                    <th class="text-center">Total Scan WH</th>
                    <th class="text-center">Total Shift</th>
                    <th class="text-center" width="70">Status</th>
                  </tr>
                </thead>
                <tbody id="isi_contents"></tbody>
                <tfoot id="isi_footers"></tfoot>
              </table>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>

    <!-- MODAL FORM DATA SHIFT -->
    <div class="modal fade" id="modal_shift_form" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Job <span class="text-danger font-weight-bold" id="no_jobs_form"></span></h5>
            <button type="button" class="close" onclick="go_back();" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
	        </div>
	        <div class="modal-body">
            <div class="container">
              <div class="row mb-1">
                <div class="col-md-2 col-5">
                  <p>Part ID</p>
                </div>
                <div class="col-md-3 col-7">
                  <p id="part_id_form">-</p>
                </div>
                <div class="col-md-2 col-5">
                  <p>Part Name</p>
                </div>
                <div class="col-md-5 col-7">
                  <p id="part_name_form">-</p>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 col-5">
                  <p class="font-weight-bold text-danger">Qty. Produksi</p>
                </div>
                <div class="col-md-3 col-7">
                  <p id="qty_produksi_form" class="font-weight-bold text-danger">-</p>
                </div>
                <div class="col-md-2 col-5">
                  <p class="font-weight-bold text-danger">Tgl. Produksi</p>
                </div>
                <div class="col-md-5 col-7">
                  <p class="font-weight-bold text-danger" id="tgl_produksi">-</p>
                </div>
              </div>
            </div>
            <hr>
	          <div class="table-responsive mt-2">
              <form id="form_shift_data" action="" method="post">
                <input type="hidden" name="kode_detail" id="kode_detail">
                <input type="hidden" name="qty_produksi" id="qty_produksi">
                <input type="hidden" name="job_qty" id="job_qty">
                <input type="hidden" name="job_nomor" id="job_nomor">
                <input type="hidden" name="tanggal_produksi" id="tanggal_produksi">
                <input type="hidden" name="part_id_" id="part_id_">
                <div class="container">
                  <div class="row">
                    <div class="col-md-4">A</div>
                    <div class="col-md-4">B</div>
                    <div class="col-md-4">C</div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-4 col-sm-12">
                      <label for="label_1">
                        <select name="label_shift_1" id="label_shift_1" class="form-control" width="100%">
                          <option value="1" selected readonly>Qty. Shift 1</option>
                        </select>
                      </label>
                      <input onkeypress="return isNumber(event)" value="0" type="text" class="form-control autonumber" data-v-max="9999" data-v-min="0" id="qty_shift_1" name="qty_shift_1" placeholder="Qty. Shift 1" maxlength="5" autocomplete="off" autofocus="on">
                    </div>
                    <div class="form-group col-md-4 col-sm-12">
                      <label for="label_2">
                        <select name="label_shift_2" id="label_shift_2" class="form-control" width="100%">
                          <option value="2" selected readonly>Qty. Shift 2</option>
                        </select>
                      </label>
                      <input onkeypress="return isNumber(event)" value="0" type="text" class="form-control autonumber" data-v-max="9999" data-v-min="0" id="qty_shift_2" name="qty_shift_2" placeholder="Qty. Shift 2" maxlength="5" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4 col-sm-12">
                      <label for="label_3">
                        <select name="label_shift_3" id="label_shift_3" class="form-control" width="100%">
                          <option value="3" selected readonly>Qty. Shift 3</option>
                        </select>
                      </label>
                      <input onkeypress="return isNumber(event)" value="0" type="text" class="form-control autonumber" data-v-max="9999" data-v-min="0" id="qty_shift_3" name="qty_shift_3" placeholder="Qty. Shift 3" maxlength="5" autocomplete="off">
                    </div>
                  </div>
                </div>
              </form>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button id="close_modal_shift" type="button" onclick="go_back()" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
            <button id="simpan_data_shift" type="button" onclick="save_data_shift()" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
      	</div>
    	</div>
  	</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/inputmask.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/jquery.inputmask.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/form-mask.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment(); //moment().subtract(7, 'days');
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
      //FUNCITON SHOW BACK
      function ShowBackTransaksi() {
        let JobNumber  = localStorage.getItem('JobNumber');
        let PartName   = localStorage.getItem('PartName');
        cek_detail_transaksi(JobNumber, PartName);
        $('#modal_jam_transaksi').modal('hide');
      }

      //FUNCITON CEK WAKTU
      function cek_waktu(JobNumber, PartID, ScanDate) {
        $.ajax({
            url : "<?php echo base_url(); ?>lhp/cek_tanggal_transaksi",
            type: "POST",
            dataType: "JSON",
            data: {
              NomorJob: JobNumber,
              IDPart : PartID,
              TanggalScan : ScanDate
            },
            success: function(data)
            {
              $('#modal_detail').modal('hide');
              $('#modal_jam_transaksi').modal('show');
              $('#isi_jam_content').html(data.html);
              $('#isi_jam_footer').html(data.footer);
              $('#JamJobNumber').html(": " + JobNumber);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
      }

      //FUNCTION SIMPAN QTY PER SHIFT
      function save_data_shift() {
        let total_per_shift = 0;
        let lebih_data      = 0;
        let qty_shift_1     = $("#qty_shift_1").val();
        let qty_shift_2     = $("#qty_shift_2").val();
        let qty_shift_3     = $("#qty_shift_3").val();
        let qty_produksi    = $("#qty_produksi").val();
        
        qty_shift_1         = qty_shift_1 == "" ? 0 : parseFloat(qty_shift_1.replace(",", ""));
        qty_shift_2         = qty_shift_2 == "" ? 0 : parseFloat(qty_shift_2.replace(",", ""));
        qty_shift_3         = qty_shift_3 == "" ? 0 : parseFloat(qty_shift_3.replace(",", ""));
        qty_produksi        = parseFloat(qty_produksi);

        total_per_shift     = qty_shift_1 + qty_shift_2 + qty_shift_3;
        lebih_data          = total_per_shift - qty_produksi;
        let data_shift      = $('#form_shift_data').serializeArray();
        if (qty_shift_1 > 0 || qty_shift_2 > 0 || qty_shift_3 > 0) {
          if (total_per_shift <= qty_produksi) {
            $.ajax({
              url : "<?php echo base_url(); ?>lhp/simpan_data_per_shift",
              type: "POST",
              dataType: "JSON",
              data: data_shift,
              beforeSend: function (data) {
                $("#simpan_data_shift").prop('disabled', true);
						    $("#simpan_data_shift").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
              },
              success: function(data)
              {
                //$('#form_shift_data')[0].reset();
                $("#simpan_data_shift").prop('disabled', false);
						    $("#simpan_data_shift").html('Simpan');

                Swal.fire({
                  icon: data.status,
                  title: data.status[0].toUpperCase() + data.status.substring(1),
                  text: data.message
                });
              },
              error: function (jqXHR, textStatus, errorThrown)
              {
                $("#simpan_data_shift").prop('disabled', false);
						    $("#simpan_data_shift").html('Simpan');
                alert('Error when saving data per shift');
              }
            });
          } else {
            Swal.fire({
              icon: 'info',
              title: 'Oops...',
              html: 'Data yang anda input melebihi hasil produksi.<br><b style="font-weight: bolder;">Qty. lebih ' + lebih_data + '</b>'
            })
          }
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            html: 'Inputan salah satu shift harus lebih besar dari 0'
          })
        }
      }

      //FUNCTION GO BACK
      function go_back() {
        $('#form_shift_data')[0].reset();
        var no_job    = localStorage.getItem('no_job_shift');
        var part_name = localStorage.getItem('part_name_shift');
        var part_id   = localStorage.getItem('part_id_shift');
        var qty_order = localStorage.getItem('qty_order_shift');

        set_data_shift(no_job, part_name, part_id, qty_order);
      }

      //FUNCTION SHOW DATA SHIFT
      function modal_input_shift(no_job, part_name, part_id, qty_produksi, tgl_produksi) {
        let qty_job = localStorage.getItem('qty_order_shift');
        $('#no_jobs_form').html(no_job);
        $('#tgl_produksi').html(": " + tgl_produksi);
        $('#part_id_form').html(": " + part_id);
        $('#qty_produksi_form').html(": " + qty_produksi);
        $('#part_name_form').html(": " + part_name);
        $('#qty_produksi').val(qty_produksi.replace(",", ""));
        $('#job_nomor').val(no_job);
        $('#tanggal_produksi').val(tgl_produksi);
        $('#part_id_').val(part_id);
        $('#job_qty').val(qty_job.replace(",", ""));
        $('#modal_shift_form').modal('show');

        $.ajax({
          url : "<?php echo base_url(); ?>lhp/check_data_shift",
          type: "POST",
          dataType: "JSON",
          data: {
            job_no: no_job,
            production_date: tgl_produksi,
            production_qty: qty_produksi.replace(",", "")
          },
          beforeSend: function (data) {
            
          },
          success: function(data)
          {
            if (data.status_code == 200) {
              $("#kode_detail").val(data.data.id);
              $("#qty_shift_1").val(data.data.qty_shift_1);
              $("#qty_shift_2").val(data.data.qty_shift_2);
              $("#qty_shift_3").val(data.data.qty_shift_3);
            } else {
              $("#qty_shift_1").val(0);
              $("#qty_shift_2").val(0);
              $("#qty_shift_3").val(0);
            }
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error when checking data per shift');
          }
        });
      }

      //FUNCTION SHOW MODAL INPUT DATA SHIFT
      function set_data_shift(no_job, part_name, part_id, qty_order) {
        localStorage.setItem('no_job_shift', no_job);
        localStorage.setItem('part_name_shift', part_name);
        localStorage.setItem('part_id_shift', part_id);
        localStorage.setItem('qty_order_shift', qty_order);

        $.ajax({
          url : "<?php echo base_url(); ?>lhp/cek_detail_transaksi_wh",
          type: "POST",
          dataType: "JSON",
          data: {
            job_nomor: no_job
          },
          success: function(data)
          {
            $('#no_jobs').html(no_job);
            $('#job_no').html(": " + no_job);
            $('#part_id').html(": " + part_id);
            $('#qty_order').html(": " + qty_order);
            $('#part_name').html(": " + part_name);

            $('#modal_shift_detail').modal('show');
            $('#isi_contents').html(data.html);
            $('#isi_footers').html(data.footer);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error get data from ajax');
          }
        });
      };

      //FUNCTION DETAIL TRANSAKSI
      function cek_detail_transaksi(no_job, PartName) {  
        $.ajax({
            url : "<?php echo base_url(); ?>lhp/cek_detail_transaksi",
            type: "POST",
            dataType: "JSON",
            data: {
              job_nomor: no_job,
              start_date : $('#start_date').val(),
              end_date : $('#end_date').val()
            },
            success: function(data)
            {
              $('#modal_detail').modal('show');
              $('#isi_content').html(data.html);
              $('#isi_footer').html(data.footer);
              $('#partName').html(": " + PartName);

              localStorage.setItem('JobNumber', no_job);
              localStorage.setItem('PartName', PartName);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
      };

      //FUNCTION CARI
      function cari() {
        reload_table();
      };

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      };

      $(document).ready(function() {
        table = $('#order-table').DataTable( {
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
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

            //computing column Total of the complete result 
            var total_qty = api
              .column(5)
              .data()
              .reduce(function(a, b) {
                const {body} = new DOMParser().parseFromString(b, 'text/html');
                const value = body.querySelector('a').innerText; // find <code> tag and get text
                return intVal(a) + intVal(value);
              }, 0);

            // var total_qty = api
            //   .column(5)
            //   .data()
            //   .reduce(function(a, b) {
            //     return parseFloat(a) + parseFloat(b);
            //   }, 0);

            // Update footer by showing the total with the reference of the column index 
            $(api.column(0).footer()).html('GRAND TOTAL');
            $(api.column(5).footer()).html(formatNumber(total_qty));
          },
          scrollY       : "380px",
          scrollX       : true,
          scrollCollapse: true,
          paging        : true,
          fixedColumns: {
            leftColumns: 0,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url : "<?php echo base_url(); ?>lhp/hasil_scan_wh_data",
            type : 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.pilihan      = $('#pilihan').val();
            }
          },
          
          oLanguage: {sProcessing: "<div id='loading-screen' class='loading'>Loading&#8230;</div>"},

          'aoColumns': [
            { "No.": "No." , "sClass": "text-right"},
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Job No. & Part ID": "Job No. & Part ID" , "sClass": "text-left"},
            { "Tgl. Job": "Tgl. Job" , "sClass": "text-left" },
            { "Scan WH": "Scan WH" , "sClass": "text-center" },
            { "Total Qty. Scan WH": "Total Qty. Scan WH" , "sClass": "text-right" },
            { "Qty. Job": "Qty. Job" , "sClass": "text-right" },
            { "Status Job": "Status Job" , "sClass": "text-center" }
          ],

          "columnDefs": [
            { 
              //"targets": [ 0 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        } );

        $('#qty_shift_1').on('change blur',function(){
          if($(this).val().trim().length === 0){
            $(this).val(0);
          }
        });

        $('#qty_shift_2').on('change blur',function(){
          if($(this).val().trim().length === 0){
            $(this).val(0);
          }
        });

        $('#qty_shift_3').on('change blur',function(){
          if($(this).val().trim().length === 0){
            $(this).val(0);
          }
        });

        function formatNumber(n) {
          return n.toLocaleString();
        }
      });
    </script>
  </body>
</html>