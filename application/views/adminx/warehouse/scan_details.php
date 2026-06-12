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
    <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <style>
      .bottom-text {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        padding: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
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
                            <div class="card-block">
                              <div class="dt-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th class="text-center bg-primary">Nomor</th>
                                      <th class="text-center bg-primary">Barcode ID</th>
                                      <th class="text-center bg-primary">Part ID</th>
                                      <th class="text-center bg-primary">Part Name</th>
                                      <th class="text-center bg-primary">Qty. Order</th>
                                      <th class="text-center bg-primary">Qty. Pallet</th>
                                      <th class="text-center bg-primary">NO. DO</th>
                                      <th class="text-center bg-primary">PO. Customer</th>
                                      <th class="text-center bg-primary">Keterangan</th>
                                      <th class="text-center bg-primary">Customer</th>
                                      <th class="text-center bg-primary">Creatime</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                </table>
                                <p class="h6 font-italic mt-4">MAS/FO/WH/05</p>
                                <hr>
                                <div class="container">
                                  <div class="row">
                                    <div class="table-responsive">
                                      <p class="h6">INSPECTION - INSPECTOR CARD FG</p>
                                      <table class="table table-striped table-bordered" width="100%" style="width:100%">
                                        <thead>
                                          <tr class="bg-secondary text-white">
                                            <th class="text-center">Persiapan Planning</th>
                                            <th class="text-center">Checker</th>
                                            <!-- <th class="text-center">Total Coly Per Pallet</th> -->
                                            <th class="text-center">Total Coly</th>
                                            <th class="text-center">Driver</th>
                                            <th class="text-center">Note</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr style="height: 100px;">
                                            <td class="text-center align-bottom"><?php echo $pers_planning == 'KOSONG' ? '' : $pers_planning; ?></td>
                                            <td class="text-center align-bottom"><?php echo $header->checker; ?></td>
                                            <!-- <td class="text-center align-middle">
                                              <?php //foreach ($detail as $index => $row): ?>
                                                Pallet <?php echo $index + 1 ?>: <?php echo $row->Total ?>
                                                <?php //if ($index < count($detail) - 1): ?>
                                                  <hr>
                                                <?php //endif; ?>
                                              <?php //endforeach; ?>
                                            </td> -->
                                            <td class="text-center align-middle font-weight-bold h2" id="LblTotalColy"></td>
                                            <td class="text-center align-middle">
                                              <?php foreach ($driverList as $index => $row): ?>
                                                <?php echo $row->nama_driver ?>
                                                <br>
                                                (<?php echo $row->no_polisi ?>)
                                                <!-- <br><br>
                                                Total Box Kirim: <?php //echo $row->TotalBox; ?>
                                                <br>
                                                Tanggal Kirim: <?php //echo $row->CreateDate; ?> -->
                                                <?php if ($index < count($driverList) - 1): ?>
                                                  <hr>
                                                <?php endif; ?>
                                              <?php endforeach; ?>

                                              <!-- <?php //echo $driver; ?>
                                              <hr>
                                              (<?php //echo $nopol; ?>) -->
                                            </td>
                                            <td class="text-left align-middle"><?php echo $header->notes; ?></td>
                                          </tr>
                                        </tbody>
                                      </table>
                                      <p class="h6 font-italic">MAS/FO/WH/10</p>
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
                </div>
                <div id="styleSelector"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script>
      $(document).ready(function() {
        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
          scrollY: "100%",
          scrollX: true,
          scrollCollapse: true,
          paging: true,
          fixedColumns: {
            leftColumns: 1,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url: "<?php echo base_url(); ?>warehouse/scan_details_list",
            type: 'POST',
            "data": function(data) {
              data.no_po = '<?php echo $no_po; ?>';
              data.no_do = '<?php echo $no_do; ?>';
              data.part_id = '<?php echo $part_id; ?>';
              data.qty_order = '<?php echo $qty_order; ?>';
              data.po_date = '<?php echo $po_date; ?>';
            }
          },
          'aoColumns': [
            {
              "NO": "NO", "sClass": "text-right"
            },
            {
              "Barcode ID": "Barcode ID", "sClass": "text-left"
            },
            {
              "Part ID": "Part ID", "sClass": "text-left"
            },
            {
              "Part Name": "Part Name", "sClass": "text-left"
            },
            {
              "Qty. Order": "Qty. Order", "sClass": "text-right"
            },
            {
              "Qty. Pallet": "Qty. Pallet", "sClass": "text-right"
            },
            {
              "NO. DO": "NO. DO", "sClass": "text-left"
            },
            {
              "PO. Customer": "PO. Customer", "sClass": "text-left"
            },
            {
              "Keterangan": "Keterangan", "sClass": "text-left"
            },
            {
              "Customer": "Customer", "sClass": "text-left"
            },
            {
              "Creatime": "Creatime", "sClass": "text-left"
            }
          ],
          "columnDefs": [
            {
              "targets": [1], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            }
          ],
          "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // total semua data dari server
            var totalRows = $('#order-table').data('totalAllRows') || 0;

            // total halaman saat ini
            var pageRows = data.length;
            $('#LblTotalColy').html(pageRows);
          }
        });
      });
    </script>
  </body>
</html>