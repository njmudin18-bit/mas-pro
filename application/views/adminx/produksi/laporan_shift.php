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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                              </h5>
                            </div>
                            <div class="card-block special">
                              <div class="dt-responsive table-responsiveX">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 mt-2 col-form-label">Filter by</label>
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
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th class="text-center bg-primary">No.</th>
                                      <th class="text-center bg-primary">#</th>
                                      <th class="text-center bg-primary">Job No.</th>
                                      <th class="text-center bg-primary">Part ID</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Qty. Job</th>
                                      <th class="text-center bg-primary">Created Date</th>
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

    <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Job <span class="text-danger" id="no_jobs"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
	        </div>
	        <div class="modal-body">
            <div class="container">
              <div class="row">
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
            <hr>
	          <div class="table-responsive mt-2">
              <table id="searchProdTable" class="table table-bordered table-striped" width="100%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center" width="50">No</th>
                    <th class="text-center" width="50">Status</th>
                    <th class="text-center" width="100">Tgl. Produksi</th>
                    <th class="text-center">Total Qty. WH</th>
                    <th class="text-center">Total Shift</th>
                    <th class="text-center">Qty. Shift 1</th>
                    <th class="text-center">Tgl. Shift 1</th>
                    <th class="text-center">Qty. Shift 2</th>
                    <th class="text-center">Tgl. Shift 2</th>
                    <th class="text-center">Qty. Shift 3</th>
                    <th class="text-center">Tgl. Shift 3</th>
                  </tr>
                </thead>
                <tbody id="isi_contents"></tbody>
              </table>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment().subtract(1, 'days'); //moment(); 
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
      function cek_detail_transaksi(nomor_job, part_id, part_name, qty_job) {
        $.ajax({
            url : "<?php echo base_url(); ?>lhp/laporan_job_detail_list",
            type: "POST",
            dataType: "JSON",
            data: {
              job_no: nomor_job
            },
            success: function(data)
            {
              $('#no_jobs').html(nomor_job);
              $('#job_no').html(": " + nomor_job);
              $('#part_id').html(": " + part_id);
              $('#qty_order').html(": " + qty_job);
              $('#part_name').html(": " + part_name);
              $('#modal_detail').modal('show');

              var myTable;
              myTable = $('#searchProdTable').DataTable({
                data: data.data,
                fixedHeader: {
                  header: true
                  // footer: true
                },
                order: [[1, 'asc']],  
                bSort: false,
                bAutoWidth: false,
                "bDestroy": true,
                "pageLength": 50,

                "aoColumns": [
                  { "No": "No" , "sClass": "text-center", "sWidth": "6%" },
                  { "Status": "Status" , "sClass": "text-center" },
                  { "Tgl. Produksi": "Tgl. Produksi" , "sClass": "text-center" },
                  { "Total Qty. WH": "Total Qty. WH" , "sClass": "text-right" },
                  { "Total Shift": "Total Shift" , "sClass": "text-right" },
                  { "Qty. Shift 1": "Qty. Shift 1" , "sClass": "text-right" },
                  { "Tgl. Shift 1": "Tgl. Shift 1" , "sClass": "text-center" },
                  { "Qty. Shift 2": "Qty. Shift 2" , "sClass": "text-right" },
                  { "Tgl. Shift 2": "Tgl. Shift 2" , "sClass": "text-center" },
                  { "Qty. Shift 3": "Qty. Shift 3" , "sClass": "text-right" },
                  { "Tgl. Shift 3": "Tgl. Shift 3" , "sClass": "text-center" }
                ]
              });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
      }

      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

      $(document).ready(function() {
        table = $('#order-table').DataTable( {
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
          scrollY       : "420px",
          scrollX       : true,
          scrollCollapse: true,
          paging        : true,
          fixedColumns: {
            leftColumns: 3,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url : "<?php echo base_url(); ?>lhp/laporan_job_per_shift_list",
            type : 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },

          oLanguage: { sProcessing: "<div id='loading-screen' class='loading'>Loading&#8230;</div>" },

          'aoColumns': [
            { "No.": "No." , "sClass": "text-right"},
            { "#": "#" , "sClass": "text-center"},
            { "Job No.": "Job No." , "sClass": "text-left"},
            { "Part ID": "Part ID" , "sClass": "text-left" },
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Qty. Job": "Qty. Job" , "sClass": "text-right" },
            { "Created Date": "Created Date" , "sClass": "text-left" }
          ],

          "columnDefs": [
            { 
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        } );
      });
    </script>
  </body>
</html>