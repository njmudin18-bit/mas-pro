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
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block">
                              <div class="dt-responsive table-responsive">
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
                                      <th class="text-center" width="7%">Nomor PO</th>
                                      <th class="text-center" width="3%">Supplier ID</th>
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

    <!-- MODAL -->
    <div class="modal fade" id="modalDetailPO" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail PO #<span id="LabelPONumber"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" class="container" method="post" id="registerForm">
              <div class="form-group row mb-3">
                <label class="col-md-2 col-form-label">Supplier ID</label>
                <label class="col-md-2 col-form-label" id="LabelSupplierType"></label>
                <label class="col-md-2 col-form-label">Supplier Name</label>
                <label class="col-md-4 col-form-label" id="LabelSupplierName"></label>
              </div>
              <hr>
              <div class="form-group row mb-3">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" width="100%">
                    <thead>
                      <tr class="bg-primary text-white">
                        <th class="text-center" width="5%">No</th>
                        <th class="text-center" width="5%">#</th>
                        <th class="text-center" width="5%">Jlh. Label</th>
                        <th class="text-center" width="18%">PO Number</th>
                        <th class="text-center" width="18%">Part ID</th>
                        <th class="text-center">Part Name</th>
                      </tr>
                    </thead>
                    <tbody id="isi_data_po"></tbody>
                  </table>
                </div>
              </div>
            </form>
            <!--end col-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDetaiItem" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Item #<span id="LabelPONumbers"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <div class="form-group row mb-3">
                <label class="col-md-2 col-form-label">Part ID</label>
                <label class="col-md-2 col-form-label" id="LabelPartIds"></label>
                <label class="col-md-2 col-form-label">Part Name</label>
                <label class="col-md-4 col-form-label" id="LabelPartNames"></label>
              </div>
              <hr>
              <table id="modal-table" class="table table-striped table-bordered" width="200%">
                <thead>
                  <tr class="bg-primary text-white">
                    <th class="text-center">Sequent</th>
                    <th class="text-center">PO Number</th>
                    <th class="text-center">Part ID</th>
                    <th class="text-center">Part Name</th>
                    <th class="text-center">Barcode Number</th>
                    <th class="text-center">Supplier Name</th>
                    <th class="text-center">Month</th>
                    <th class="text-center">Lot Number</th>
                    <th class="text-center">Tgl. Cetak</th>
                    <th class="text-center">Tgl. Scan</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <!--end col-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var start = moment().startOf('month');
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
      function lihat_item_po(NomorPO, PartIDs, PartNames) {
        $("#LabelPONumbers").html(': ' + NomorPO);
        $("#LabelPartIds").html(': ' + PartIDs);
        $("#LabelPartNames").html(': ' + PartNames);
        $("#modalDetaiItem").on("shown.bs.modal", function(e) {
          $("#modal-table")
          .DataTable({
            "bDestroy": true,
            "pagingType": "full_numbers",
            "lengthMenu": [
              [10, 20, 25, 50, -1],
              [10, 20, 25, 50, "All"]
            ],
            responsive: false,
            language: {
              search: "_INPUT_",
              searchPlaceholder: "Search records",
            },
            "processing": true,
            "serverSide": false,
            "order": [],
            'ajax': {
              url : "<?php echo base_url(); ?>scan_incoming_part/lihat_item_po",
              type : 'POST',
              "data": function(data) {
                data.PONumber   = NomorPO;
                data.PartID     = PartIDs;
              }
            },
            columns: [
              {  data: 'Sequent', "sClass": "text-right" },
              {  data: 'PONumber', "sClass": "text-left" },
              {  data: "PartID", "sClass": "text-left" },
              {  data: "PartName", "sClass": "text-left" },
              {  data: "BarcodeNumber", "sClass": "text-left" },
              {  data: "SupplierName", "sClass": "text-left" },
              {  data: "Month", "sClass": "text-center" },
              {  data: "LotNumber", "sClass": "text-left" },
              {  data: "CreateDate", "sClass": "text-left" },
              {  data: "TglKedatangan", "sClass": "text-left" }
            ],
            "columnDefs": [
              { "width": "5%", "targets": 0},
              { "width": "10%", "targets": 1},
              { "orderable": false, "targets": [0, 1]} // Can't order
            ],
          })
          .columns.adjust()
          .responsive.recalc();
        }).modal('show');
      }

      //FUNCTION HAPUS DATA
      function lihat_detail_po(NomorPO) {
        $.ajax({
          url: '<?php echo base_url(); ?>scan_incoming_part/lihat_detail_po',
          type: 'POST',
          data: {
            PONumber: NomorPO
          },
          beforeSend: function (data) {
            $("#loading").show();
          },
          success: function(data) {
            let response = JSON.parse(data);
            $('#modalDetailPO').modal('show');
            $("#LabelPONumber").html(NomorPO);
            $("#LabelSupplierType").html(': ' + response.data_header.SupplierID);
            $("#LabelSupplierName").html(': ' + response.data_header.Type + '. ' + response.data_header.PartnerName);
            $("#isi_data_po").html(response.html);
            $("#loading").hide();
          },
          error: function() {
            $("#loading").show();
            alert('Oops something went wrong');
          }
        });
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
            url: "<?php echo base_url(); ?>scan_incoming_part/scan_incoming_part_report_list",
            type: 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          'aoColumns': [
            { "No": "No" , "sClass": "text-right"},
            { "#": "#" , "sClass": "text-center" },
            { "Nomor PO": "Nomor PO" , "sClass": "text-left" },
            { "Supplier ID": "Supplier ID" , "sClass": "text-center" },
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