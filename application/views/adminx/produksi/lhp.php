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
                                  <!-- <div class="col-md-3 col-sm-12">
                                    <div class="input-group">
                                      <select name="pilihan" id="pilihan" class="form-control">
                                        <option disabled>-- Pilih --</option>
                                        <option value="all" selected>All</option>
                                        <option value="power cord">Power Cord</option>
                                        <option value="wiring">Wiring</option>
                                      </select>
                                    </div>
                                  </div> -->
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
                                    <tr class="">
                                      <th class="text-center bg-primary">No.</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Job No. & Part ID</th>
                                      <th class="text-center bg-primary">Tgl. Job</th>
                                      <th class="text-center bg-primary">Barcode PPIC</th>
                                      <th class="text-center bg-primary">Sisa No. Barcode PPIC</th>
                                      <th class="text-center bg-primary">Scan WH</th>
                                      <th class="text-center bg-primary">Total Qty. Scan WH</th>
                                      <th class="text-center bg-primary">Qty. Job</th>
                                      <th class="text-center bg-primary">Sisa Job</th>
                                      <th class="text-center bg-primary">Status Job</th>
                                      <th class="text-center bg-primary">Keterangan</th>
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
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Detail Transaksi</h4>
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
              <table class="table table-bordered table-striped" width="110%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center">No</th>
                    <th class="text-center">Job No.</th>
                    <th class="text-center">Part ID</th>
                    <th class="text-center">Sub Total</th>
                    <th class="text-center">Tgl. Scan</th>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <!-- <script>
      $(function() {
        $('input[name="tanggal"]').daterangepicker({
          maxDate: new Date(),
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 2010,
          maxYear: parseInt(moment().format('YYYY'),10),
          locale: {
            format: 'YYYY-MM-DD'
          }
        });
      });
    </script> -->
    <script type="text/javascript">
      $(function() {

        var start = moment(); //moment().subtract(6, 'days');
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
              $('#partName').html(PartName);
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
          scrollY       : "350px",
          scrollX       : true,
          scrollCollapse: true,
          paging        : true,
          fixedColumns: {
            leftColumns: 2,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url : "<?php echo base_url(); ?>lhp/show_no_barcode_production",
            type : 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },

          oLanguage: {sProcessing: "<div id='loading-screen' class='loading'>Loading&#8230;</div>"},

          'aoColumns': [
            { "No.": "No." , "sClass": "text-right"},
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Job No. & Part ID": "Job No. & Part ID" , "sClass": "text-left"},
            { "Tgl. Job": "Tgl. Job" , "sClass": "text-left" },
            { "Barcode PPIC": "Barcode PPIC" , "sClass": "text-center"},
            { "Sisa No. Barcode PPIC": "Sisa No. Barcode PPIC" , "sClass": "text-center" },
            { "Scan WH": "Scan WH" , "sClass": "text-center" },
            { "Total Qty. Scan WH": "Total Qty. Scan WH" , "sClass": "text-right" },
            { "Qty. Job": "Qty. Job" , "sClass": "text-right" },
            { "Sisa Job": "Sisa Job" , "sClass": "text-right" },
            { "Status Job": "Status Job" , "sClass": "text-center" },
            { "Keterangan": "Keterangan" , "sClass": "text-left" }
          ],

          "columnDefs": [
            { 
              //"targets": [ 0 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        } );
      });
    </script>
  </body>
</html>