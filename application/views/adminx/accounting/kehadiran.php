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
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-3">Filter by</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <span class="input-group-append">
                                      <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                    </span>
                                  </div>

                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="250%">
                                  <thead id="thead-absensi" class="bg-primary text-white"></thead>
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

    <div id="loading" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
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
        const start = $('#start_date').val();
        const end   = $('#end_date').val();

        // 1. Generate header dan kolom baru
        const dynamicHeader  = generateDynamicHeader(start, end);
        const dynamicColumns = generateDynamicAoColumns(start, end);

        // 2. Set ulang thead HTML
        document.getElementById("thead-absensi").innerHTML = dynamicHeader;

        // 3. Destroy DataTable lama jika sudah ada
        if ($.fn.DataTable.isDataTable('#myTable')) {
          $('#myTable').DataTable().destroy();
          $('#myTable').empty(); // Kosongkan tabel untuk menghindari duplikasi
          $('#myTable').html('<thead id="thead-absensi" class="bg-primary text-white"></thead><tbody></tbody>');
          document.getElementById("thead-absensi").innerHTML = dynamicHeader;
        }

        // 4. Inisialisasi ulang DataTable dengan kolom baru
        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          "processing": true,
          "serverSide": false,
          "ordering": false,
          ajax: {
            url: '<?php echo base_url(); ?>absensi/kehadiran_list',
            type: 'POST',
            data: {
              StartDate: start,
              EndDate: end
            }
          },
          fixedColumns: {
            left: 3
          },
          columns: dynamicColumns,
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A0',
              customize: function (doc) {
                  const StartDate = new Date($('#start_date').val());
                  const EndDate   = new Date($('#end_date').val());

                  const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                  const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                  const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                  doc.defaultStyle.fontSize = 10;
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
                        text: 'LAPORAN DAFTAR KEHADIRAN KARYAWAN',
                        bold: true,
                        fontSize: 14,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    },
                    {
                      text: 'PERIODE : ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0];
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
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN DAFTAR KEHARIDAN PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            }
          ]
        });
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      function generateDynamicHeader(start, end) {
        const startDate   = new Date(start);
        const endDate     = new Date(end);
        const dateList    = [];

        // 1. Buat list tanggal dari start ke end
        while (startDate <= endDate) {
          const day   = ("0" + startDate.getDate()).slice(-2);
          const month = startDate.toLocaleString('en-US', { month: 'short' }).toUpperCase(); // e.g. JUN
          dateList.push(`${day} ${month}`);
          startDate.setDate(startDate.getDate() + 1);
        }

        // 2. Baris pertama (kolom identitas + tanggal)
        let row1 = '<tr>';
        row1 += '<th class="text-center" width="100px" rowspan="3">NO</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">NIP</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">NAME</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">HARI KERJA</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL HADIR</th>';
        row1 += `<th class="text-center" colspan="${dateList.length * 2}">TANGGAL</th>`;
        row1 += '</tr>';

        // 3. Baris kedua: Tanggal (01, 02, dst)
        let row2 = '<tr>';
        dateList.forEach(date => {
          row2 += `<th width="100px" class="text-center" colspan="2">${date}</th>`;
        });
        row2 += '</tr>';

        // 4. Baris ketiga: IN/OUT
        let row3 = '<tr>';
        dateList.forEach(() => {
          row3 += '<th width="100px" class="text-center">IN</th>';
          row3 += '<th width="100px" class="text-center">OUT</th>';
        });
        row3 += '</tr>';
        //console.log(row3);

        return row1 + row2 + row3;
      }

      function generateDynamicAoColumns(start, end) {
        const startDate = new Date(start);
        const endDate   = new Date(end);
        const columns   = [];

        // Kolom tetap
        columns.push({ sTitle: "NO", sClass: "text-right", width: "50px" });
        columns.push({ sTitle: "NIP", sClass: "text-center", width: "150px" });
        columns.push({ sTitle: "NAME", sClass: "text-left", width: "200px" });
        columns.push({ sTitle: "HARI KERJA", sClass: "text-center", width: "100px" });
        columns.push({ sTitle: "TOTAL HADIR", sClass: "text-center", width: "100px" });

        // Kolom dinamis: per tanggal
        while (startDate <= endDate) {
          const day = ("0" + startDate.getDate()).slice(-2); // misalnya "01"
          columns.push({ sTitle: `${day} IN`, sClass: "text-center", width: "100px" });
          columns.push({ sTitle: `${day} OUT`, sClass: "text-center", width: "100px" });
          startDate.setDate(startDate.getDate() + 1);
        }

        return columns;
      }
      
      $(document).ready(function() {
        const start = $('#start_date').val();
        const end   = $('#end_date').val();

        // Inject header sebelum inisialisasi DataTable
        const dynamicHeader   = generateDynamicHeader(start, end);
        const dynamicColumns  = generateDynamicAoColumns(start, end);
        document.getElementById("thead-absensi").innerHTML = dynamicHeader;

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
              pageSize: 'A0',
              customize: function (doc) {
                  const StartDate = new Date($('#start_date').val());
                  const EndDate   = new Date($('#end_date').val());

                  const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                  const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                  const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                  doc.defaultStyle.fontSize = 10;
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
                        text: 'LAPORAN DAFTAR KEHADIRAN KARYAWAN',
                        bold: true,
                        fontSize: 14,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    },
                    {
                      text: 'PERIODE : ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0];
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
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN DAFTAR KEHARIDAN PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
          select: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>absensi/kehadiran_list",
            "type": "POST",
            "data": function(data) {
              data.StartDate   = $('#start_date').val();
              data.EndDate     = $('#end_date').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          aoColumns: dynamicColumns
        });
      });
    </script>
  </body>
</html>