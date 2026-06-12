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
    <?php $this->load->view('adminx/components/header_css_datatable_v2'); ?>
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
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter by</label>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="LocationList" id="LocationList">
                                      <option selected value="All">-- All --</option>
                                      <option disabled value="">-- Pilih --</option>
                                      <option value="KG">KG</option>
                                      <option value="Non KG">Non KG</option>
                                      <option value="Gresik">Gresik</option>
                                      <option value="Medan">Medan</option>
                                      <option value="Kendal">Kendal</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="Months" id="Months">
                                      <?php
                                        $months = [
                                          "January", "February", "March", "April", "May", "June",
                                          "July", "August", "September", "October", "November", "December"
                                        ];
                                        $currentMonth = date('n'); // Get current month (1-12)

                                        foreach ($months as $index => $month) {
                                          $value    = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                                          $selected = ($value == str_pad($currentMonth, 2, "0", STR_PAD_LEFT)) ? "selected" : "";
                                          echo "<option value='$value' $selected>$month</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select class="form-control" name="Years" id="Years">
                                      <?php
                                        $startYear    = 2025;
                                        $endYear      = 2050;
                                        $currentYear  = date('Y'); // Get current year

                                        for ($year = $startYear; $year <= $endYear; $year++) {
                                          $selected = ($year == $currentYear) ? "selected" : "";
                                          echo "<option value='$year' $selected>$year</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="550%">
                                  <thead>
                                    <tr class="bg-primary text-white">
                                      <th class="text-center" rowspan="2">NO</th>
                                      <th class="text-center" rowspan="2">LOCATION</th>
                                      <th class="text-center" rowspan="2">ITEMS</th>
                                      <th class="text-center" rowspan="2">PART ID</th>
                                      <th class="text-center" rowspan="2">NO PO</th>
                                      <th class="text-center" rowspan="2">QTY. PO</th>
                                      <th class="text-center" rowspan="2">PO LEBIH</th>
                                      <?php for ($day = 1; $day <= 31; $day++) : ?>
                                        <th colspan="2" class="text-center"><?php echo $day; ?></th>
                                      <?php endfor; ?>
                                      <th class="text-center" rowspan="2">TOTAL PLAN</th>
                                      <th class="text-center" rowspan="2">TOTAL KIRIM</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">TOTAL FORECAST</th>
                                      <th class="text-center" rowspan="2">STOCK</th>
                                      <th class="text-center" rowspan="2">KETERANGAN</th>
                                      <th class="text-center" rowspan="2">KURANG KIRIM</th>
                                    </tr>
                                    <tr class="bg-primary text-white">
                                      <?php for ($day = 1; $day <= 31; $day++) : ?>
                                        <th class="text-center">PLAN</th>
                                        <th class="text-center">ACT</th>
                                      <?php endfor; ?>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript">
      $(function() {

        var start = moment().subtract(7, 'days');
        var end = moment();

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

      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        var groupColumn = 1;
        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            { 
              extend: 'excelHtml5',
              text: 'Download data',
              title: '',
              className: 'btn btn-primary'
            },
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A0',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 
                          11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
                          21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 
                          31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 
                          41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 
                          51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
                          61, 62, 63, 64, 65, 66, 67, 68, 69, 70,
                          71, 72, 73, 74]
              },
              customize: function (doc) {
                  const month = $('#Months').find('option:selected').text().toUpperCase();
                  const year = $('#Years').val();

                  function formatRibuan(num) {
                      if (num === null || num === undefined) return '0';

                      if (typeof num === 'number') {
                          return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                      }

                      let str = num.toString();
                      const cleaned = str.replace(/[^\d.,-]/g, '');
                      const normalized = cleaned.replace(',', '.');
                      const n = parseFloat(normalized);

                      if (isNaN(n)) return str;

                      return n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }

                  doc.defaultStyle.fontSize = 11;
                  doc.pageMargins           = [10, 40, 10, 50];
                  doc.styles = {
                      subheader: {
                          fontSize: 12,
                          bold: true,
                          alignment: 'left'
                      },
                      tableHeader: {
                          bold: true,
                          fontSize: 10,
                          color: 'white',
                          fillColor: '#007bff',
                          alignment: 'center'
                      }
                  };

                  doc.content.unshift(
                      {
                          text: 'PT. MULTI ARTA SEKAWAN',
                          bold: true,
                          fontSize: 16,
                          style: 'subheader',
                          alignment: 'center',
                          margin: [0, 0, 0, 10]
                      },
                      {
                          text: 'PLANNING KIRIM BULANAN',
                          bold: true,
                          fontSize: 14,
                          style: 'subheader',
                          alignment: 'center',
                          margin: [0, 0, 0, 10]
                      },
                      {
                        text: 'PERIODE : ' + month + ' ' + year,
                        bold: true,
                        fontSize: 12,
                        style: 'subheader',
                        alignment: 'left',
                        margin: [0, 0, 0, 10]
                      }
                  );

                  // === Main Table Styling ===
                  const mainTable = doc.content.find(item => item.table);
                  if (mainTable) {
                      const alignRightCols = [0, 5, 6, 7, 8, 9, 10, 11, 12, 
                                              13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 
                                              23, 24, 25, 26, 27, 28, 29, 30, 31, 32,
                                              33, 34, 35, 36, 37, 38, 39, 40, 41, 42,
                                              43, 44, 45, 46, 47, 48, 49, 50, 51, 52,
                                              53, 54, 55, 56, 57, 58, 59, 60, 61, 62,
                                              63, 64, 65, 66, 67, 68, 69, 70, 71, 72,
                                              73, 74];
                      const body = mainTable.table.body;

                      for (let i = 1; i < body.length; i++) {
                          for (let j = 0; j < body[i].length; j++) {
                              if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                  body[i][j].alignment = 'right';
                              }
                          }

                          // Tambahkan styling khusus untuk baris SUB TOTAL
                          for (let j = 0; j < body[i].length; j++) {
                              if (
                                  typeof body[i][j].text === 'string' &&
                                  body[i][j].text.trim().toUpperCase() === 'SUB TOTAL'
                              ) {
                                  for (let k = 0; k < body[i].length; k++) {
                                      body[i][k].bold = true;
                                      body[i][k].fillColor = '#6c757d';
                                      body[i][k].color = '#fff';
                                  }
                                  break;
                              }
                          }

                          // Tambahkan styling khusus untuk baris TOTAL
                          for (let j = 0; j < body[i].length; j++) {
                              if (
                                  typeof body[i][j].text === 'string' &&
                                  body[i][j].text.trim().toUpperCase() === 'TOTAL'
                              ) {
                                  for (let k = 0; k < body[i].length; k++) {
                                      body[i][k].bold = true;
                                  }
                                  break;
                              }
                          }
                      }

                      // Style baris terakhir (misal GRAND TOTAL)
                      // const lastRowIndex = body.length - 1;
                      // for (let j = 0; j < body[lastRowIndex].length; j++) {
                      //     if (body[lastRowIndex][j].text !== undefined) {
                      //         body[lastRowIndex][j].fillColor = '#007bff';
                      //         body[lastRowIndex][j].color = '#fff';
                      //     }
                      // }

                      mainTable.layout = {
                          hLineWidth: () => 0.5,
                          vLineWidth: () => 0.5,
                          hLineColor: () => '#aaa',
                          vLineColor: () => '#aaa',
                          paddingLeft: () => 4,
                          paddingRight: () => 4,
                          paddingTop: () => 2,
                          paddingBottom: () => 2,
                          fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                      };
                  }

                  doc.footer = function (currentPage, pageCount) {
                      return {
                          columns: [
                              { text: 'Printed on: ' + new Date().toLocaleString(), alignment: 'left', margin: [10, 0, 0, 0] },
                              { text: 'PT MULTI ARTA SEKAWAN - CONFIDENTIAL', alignment: 'center' },
                              { text: 'Page ' + currentPage + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 10, 0] }
                          ],
                          fontSize: 8
                      };
                  };
              },
              // Tambahkan opsi filename di sini
              filename: function() {
                var month = $('#Months').find('option:selected').text().toUpperCase();
                var year  = $('#Years').val();

                return 'Laporan Planning Kirim Bulanan Periode ' + month + ' ' + year;
              }
            }
          ],
          select: {
            style: 'single'
          },
          "pagingType": "full_numbers",
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
          ],
          pageLength: -1,
          responsive: false,
          select: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          //"order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>pengiriman/timeline_jadwal_kirim_data",
            "type": "POST",
            "data": function(data) {
              data.Location  = $('#LocationList').val();
              data.Months    = $('#Months').val();
              data.Years     = $('#Years').val();
            }
          },
          fixedColumns: {
            left: 2
          },
          "aoColumns": [
            { "No": "No" , "sClass": "text-right", "width": "50px"},
            { "Location": "Location" , "sClass": "text-center", "width": "50px" },
            { "Part Name": "Part Name" , "sClass": "text-left", "width": "470px" },
            { "Part ID": "Part ID" , "sClass": "text-left", "width": "180px" },
            { "Nomor PO": "Nomor PO" , "sClass": "text-left", "width": "245px" },
            { "Qty PO": "Qty PO" , "sClass": "text-right", "width": "100px" },
            { "PO Lebih": "PO Lebih" , "sClass": "text-right", "width": "100px" },
            <?php for ($day = 1; $day <= 62; $day++) : ?>
              { "<?php echo $day; ?>": "<?php echo $day; ?>" , "sClass": "text-right", "width": "60px" },
            <?php endfor; ?>
            { "Total Plan": "Total Plan" , "sClass": "text-right", "width": "100px" },
            { "Total Kirim": "Total Kirim" , "sClass": "text-right", "width": "120px" },
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "Total Forecast": "Total Forecast" , "sClass": "text-right", "width": "150px" },
            { "Stock": "Stock" , "sClass": "text-right", "width": "100px" },
            { "Keterangan": "Keterangan" , "sClass": "text-left" },
            { "Kurang Kirim": "Kurang Kirim" , "sClass": "text-right", "width": "150px" }
          ],
          order: [[groupColumn, 'asc']],
          drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;
    
            api.column(groupColumn, { page: 'current' })
            .data()
            .each(function (group, i) {
              if (last !== group) {
                $(rows)
                  .eq(i)
                  .before(
                    '<tr class="group"><td colspan="72" class="bg-secondary text-white font-weight-bold">' + group + '</td></tr>'
                  );

                last = group;
              }
            });
          }
        });

        // Order by the grouping
        $('#myTable tbody').on('click', 'tr.group', function () {
          var currentOrder = table.order()[0];
          if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
              table.order([[groupColumn, 'desc']]).draw();
          }
          else {
              table.order([[groupColumn, 'asc']]).draw();
          }
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }
      });
    </script>
  </body>
</html>