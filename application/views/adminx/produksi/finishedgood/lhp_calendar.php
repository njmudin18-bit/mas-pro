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
    <style>
      .bg-actual {
        background-color: #FFCC99 !important;
      
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
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-3">Filter by</label>
                                <div class="col-md-2 col-sm-12 m-t-3">
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
                                <div class="col-md-2 col-sm-12 m-t-3">
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
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="250%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="3">NO</th>
                                      <th class="text-center" rowspan="3">PARTNAME</th>
                                      <th class="text-center" rowspan="3">KATEGORI</th>
                                      <th class="text-center" rowspan="3">QTY JOB</th>
                                      <th class="text-center" rowspan="3">JLH MASUK WH</th>
                                      <th class="text-center" rowspan="3">SISA JOB</th>
                                      <th class="text-center" rowspan="3">%</th>
                                      <th class="text-center" colspan="62">TANGGAL</th>
                                      <th class="text-center" rowspan="3">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                      <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <th colspan="2" class="text-center"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></th>
                                      <?php endfor; ?>
                                    </tr>
                                    <tr>
                                      <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <th class="text-center">PLAN</th>
                                        <th class="text-center">ACT</th>
                                      <?php endfor; ?>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  </tbody>
                                  <tfoot class="bg-primary text-white">
                                    <tr>
                                      <th></th>
                                      <th class="text-center font-weight-bold"></th>
                                      <th></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <th class="text-right font-weight-bold"></th>
                                      <?php for ($day = 1; $day <= 62; $day++) : ?>
                                        <th class="text-right font-weight-bold"></th>
                                      <?php endfor; ?>
                                      <th></th>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                              <hr>
                              <h6 class="font-weight-bold mt-2 mb-2">REKAP TOTAL DATA</h6>
                              <table id="Table1" class="table table-striped table-bordered" style="width: 40%;">
                                <thead>
                                  <tr class="bg-primary">
                                    <th class="text-center">NO</th>
                                    <th class="text-center">#</th>
                                    <th class="text-center">JUMLAH</th>
                                    <th class="text-center">%</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="text-right">1</td>
                                    <td>JOB ALL</td>
                                    <td id="LblTotalJob" class="text-right">0</td>
                                    <td id="PersentaseJob" rowspan="2" class="text-center" style="vertical-align: middle"></td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">2</td>
                                    <td>PRODUKSI OK</td>
                                    <td id="LblTotalProduksi" class="text-right">0</td>
                                  </tr>
                                  <tr>
                                    <td class="text-right">3</td>
                                    <td>SISA JOB</td>
                                    <td id="LblSisaJob" class="text-right">0</td>
                                    <td class="text-center">Belum masuk WH</td>
                                  </tr>
                                </tbody>
                              </table>
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A1',
              exportOptions: {
                columns: [0,  1,   3,  4,  5,  6,  7,  8,  9, 10, 
                          11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
                          21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 
                          31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
                          41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 
                          51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
                          61, 62, 63, 64, 65, 66, 67, 68]
              },
              customize: function (doc) {
                const month     = $('#Months').find('option:selected').text().toUpperCase();
                const year      = $('#Years').val();

                function formatRibuan(num) {
                  if (num === null || num === undefined) return '0';
                  if (typeof num === 'number') {
                      return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }
                  let str = num.toString();
                  const cleaned = str.replace(/[^\d.,-]/g, '');
                  const normalized = cleaned.replace(',', '.');
                  const n = parseFloat(normalized);

                  return isNaN(n) ? str : n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                }

                doc.defaultStyle.fontSize = 10;
                doc.pageMargins           = [10, 40, 10, 60];
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
                      text: 'LHP PRODUKSI PERIODE ' + month + ' ' + year,
                      bold: true,
                      fontSize: 14,
                      style: 'subheader',
                      alignment: 'center',
                      margin: [0, 0, 0, 10]
                  }
                );

                // === Table1 (TOTAL RINGKASAN) ===
                const totalJob      = $('#LblTotalJob').text().trim();
                const totalProduksi = $('#LblTotalProduksi').text().trim();
                const sisaJob       = $('#LblSisaJob').text().trim();
                const persen        = $('#PersentaseJob').text().trim();

                const summary1Table = [
                    [
                      { text: 'NO', style: 'tableHeader' },
                      { text: '#', style: 'tableHeader' },
                      { text: 'JUMLAH', style: 'tableHeader' },
                      { text: '%', style: 'tableHeader' }
                    ],
                    [
                      { text: '1', alignment: 'right' },
                      { text: 'JOB ALL', alignment: 'left' },
                      { text: totalJob, alignment: 'right' },
                      {
                        text: persen,
                        alignment: 'center',
                        rowSpan: 2,
                        margin: [0, 10, 0, 0], // middle vertical align
                      }
                    ],
                    [
                      { text: '2', alignment: 'right' },
                      { text: 'PRODUKSI OK', alignment: 'left' },
                      { text: totalProduksi, alignment: 'right' },
                      {}
                    ],
                    [
                      { text: '3', alignment: 'right' },
                      { text: 'SISA JOB', alignment: 'left' },
                      { text: sisaJob, alignment: 'right' },
                      { text: 'Belum masuk WH', alignment: 'center' }
                    ]
                ];

                doc.content.push(
                    {
                      text: 'TOTAL RINGKASAN',
                      style: 'subheader',
                      margin: [0, 20, 0, 8]
                    },
                    {
                      columns: [
                          {
                              width: '50%',
                              alignment: 'center',
                              table: {
                                  headerRows: 1,
                                  widths: ['8%', '15%', '15%', '25%'],
                                  body: summary1Table
                              },
                              layout: {
                                  hLineWidth: () => 0.5,
                                  vLineWidth: () => 0.5,
                                  hLineColor: () => '#aaa',
                                  vLineColor: () => '#aaa',
                                  paddingLeft: () => 2,
                                  paddingRight: () => 2,
                                  paddingTop: () => 2,
                                  paddingBottom: () => 2,
                                  fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                              }
                          }
                      ],
                      columnGap: 10
                    }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 
                                            11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
                                            21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 
                                            31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
                                            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 
                                            51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
                                            61, 62, 63, 64, 65, 66];
                    const body = mainTable.table.body;

                    for (let i = 1; i < body.length; i++) {
                        for (let j = 0; j < body[i].length; j++) {
                            if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                body[i][j].alignment = 'right';
                            }
                        }

                        // SUB TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase().startsWith('SUB TOTAL')
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#6c757d';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        // TOTAL styling
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

                    // Style baris terakhir
                    const lastRowIndex = body.length - 1;
                    for (let j = 0; j < body[lastRowIndex].length; j++) {
                        if (body[lastRowIndex][j].text !== undefined) {
                            body[lastRowIndex][j].fillColor = '#007bff';
                            body[lastRowIndex][j].color = '#fff';
                        }
                    }

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

                // === Footer ===
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

                return 'LHP Produksi Periode ' + month + ' ' + year;
              }
            }
          ],
          select: {
            style: 'single'
          },
          "pagingType": "full_numbers",
          "lengthMenu": [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"]
          ],
          "displayLength": 5,
          responsive: false,
          //select: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>finishedgood/lhp_calender_data",
            "type": "POST",
            "data": function(data) {
              data.Months    = $('#Months').val();
              data.Years     = $('#Years').val();
            }
          },
          fixedColumns: {
            left: 2
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "PART NAME": "PART NAME" , "sClass": "text-left", "width": "50px"},
            { "KATEGORI": "KATEGORI" , "sClass": "text-left", "width": "100px" },
            { "QTY JOB": "QTY JOB" , "sClass": "text-right", "width": "100px" },
            { "JLH MASUK WH": "JLH MASUK WH" , "sClass": "text-right", "width": "180px" },
            { "SISA JOB": "SISA JOB" , "sClass": "text-right", "width": "100px" },
            { "%": "%" , "sClass": "text-right", "width": "50px" },
            <?php for ($day = 1; $day <= 31; $day++) : ?>
              { "<?php echo $day; ?>": "<?php echo $day; ?>" , "sClass": "text-right", "width": "50px" },
              { "<?php echo $day; ?>": "<?php echo $day; ?>" , "sClass": "text-right bg-actual", "width": "50px" },
            <?php endfor; ?>
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
          ],
          "footerCallback": function (row, data, start, end, display) {
              var api = this.api();

              var parseLocaleFloatCustom = function(i) {
                  if (typeof i === 'string') {
                      var cleaned = i.replace(/<[^>]*>/g, '').trim();
                      if (cleaned === '') return 0;

                      var angkaDesimalSaja = cleaned.replace(/[^\d.,-]/g, '');
                      var formatted = angkaDesimalSaja.replace(/\./g, '').replace(',', '.');
                      var val = parseFloat(formatted);
                      return isNaN(val) ? 0 : val;
                  } else if (typeof i === 'number') {
                      return i;
                  } else {
                      return 0;
                  }
              };

              let totalJob      = 0;
              let totalWH       = 0;

              api.columns().every(function () {
                  var column = this;
                  var columnIndex = column.index();

                  var isNumeric = [3, 4, 5, 7, 8, 9, 10, 
                                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
                                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 
                                    31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
                                    41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 
                                    51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
                                    61, 62, 63, 64, 65, 66, 67, 68].includes(columnIndex);

                  if (isNumeric) {
                      var total = 0;

                      // Ambil semua baris dengan label "SUB TOTAL" di kolom index 1 (JobDate)
                      //api.rows({ page: 'current' }).every(function () {
                      api.rows().every(function () {
                          var rowData = this.data();
                          if (rowData[1] === 'SUB TOTAL') {
                              total += parseLocaleFloatCustom(rowData[columnIndex]);
                          }
                      });

                      var decimalDigits = (columnIndex === 10) ? 4 : 2;

                      var formattedTotal = total.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: decimalDigits,
                        useGrouping: true
                      });

                      $(column.footer()).html(formattedTotal);

                      // Inject ke luar DataTable
                      if (columnIndex === 3) {
                          $('#LblTotalJob').html(formattedTotal);
                          totalJob = total;
                      } else if (columnIndex === 4) {
                          //$('#LblTotalProduksi').html(formattedTotal);
                          totalWH = total;
                      } else if (columnIndex === 15) {
                          $('#LblSisaJob').html(formattedTotal);
                      }

                  } else if (columnIndex === 1) {
                      $(column.footer()).html('TOTAL');
                  } else if (columnIndex === 6) {
                      // PERSENTASE TOTAL
                      let totalPersen = totalJob > 0 ? (totalWH / totalJob) * 100 : 0;
                      $(column.footer()).html(totalPersen.toFixed(2) + '%');
                  } else {
                      $(column.footer()).html('');
                  }
              });

              // Update elemen di luar tabel
              if (totalJob > 0) {
                  var persentaseJob = (totalWH / totalJob) * 100;
                  $('#PersentaseJob').html(persentaseJob.toFixed(2) + '%');
              } else {
                  $('#PersentaseJob').html('0%');
              }
          },
          "createdRow": function(row, data, dataIndex) {
            if (data[1].toString().toUpperCase().includes("SUB TOTAL")) {
              // Tambahkan class ke seluruh baris (opsional)
              $(row).addClass('bg-secondary text-white');

              // Kolom 0, 1, dan 2 adalah kolom yang difreeze (left: 3)
              [0, 1].forEach(function(i) {
                $('td', row).eq(i).addClass('bg-secondary');
              });
            }

            if (data[1] && data[1].toString().toUpperCase().includes("SUB TOTAL")) {
              $(row).addClass('font-weight-bold text-black');
            }
          }
        });

        table.on('click', 'tbody tr', function (e) {
            table.$('tr.selected').removeClass('selected');  // hilangkan selected di semua row
            $(this).addClass('selected');                    // tambahkan selected ke row yg diklik
        });

        $(document).on('show.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $(e.target).find('.dropdown-menu');
            $('body').append($dropdown.detach()); // pindahkan ke body
            var eOffset = $(e.target).offset();
            $dropdown.css({
                'display': 'block',
                'top': eOffset.top + $(e.target).outerHeight(),
                'left': eOffset.left
            });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $('body > .dropdown-menu');
            $(e.target).append($dropdown.detach()); // kembalikan ke dalam btn-group
            $dropdown.hide();
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }
      });
    </script>
  </body>
</html>