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
                                  <label class="col-md-1 col-sm-12 col-form-label m-t-10">Filter</label>
                                  <div class="col-md-2 col-sm-12 m-t-10">
                                    <select id="SupplierList" name="SupplierList" class="form-control">
                                      <option disabled value="">-- Pilih --</option>
                                      <option selected value="All">All</option>
                                      <option value="IN022">PT. INDOWIRE PRIMA INDUSTRINDO</option>
                                      <option value="TE002">PT. TEMBAGA MULIA SEMANAN TBK</option>
                                      <option value="SE008">PT. SEONG POONG INDONESIA</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <select id="POList" name="POList" class="form-control">
                                      <option value="">-- Pilih --</option>
                                    </select>
                                  </div>
                                  <div class="col-md-4 col-sm-12 m-t-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <div class="input-group-text bg-primary border-primary text-white">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-1 col-sm-12 m-t-10">
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead>
                                    <tr class="bg-primary text-white">
                                      <th class="text-center" width="2%">No</th>
                                      <th class="text-center" width="20%">Part Name</th>
                                      <th class="text-center" width="6%">Tgl. Masuk</th>
                                      <th class="text-center" width="5%">FIFO</th>
                                      <th class="text-center" width="10%">No. PO</th>
                                      <th class="text-center" width="15%">Supplier</th>
                                      <th class="text-center" width="5%">Netto</th>
                                      <th class="text-center" width="23%">Print Label Barcode</th>
                                      <th class="text-center" width="7%">QC</th>
                                      <th class="text-center" width="10%">Note</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr class="bg-primary text-white">
                                      <th colspan="2" class="text-center">Total Bobin</th>
                                      <th class="text-center"></th>
                                      <th class="text-center" id="TotalBobin"></th>
                                      <th colspan="2" class="text-center">Total Netto</th>
                                      <th class="text-right" id="TotalNetto"></th>
                                      <th colspan="3" class="text-center"></th>
                                    </tr>
                                  </tfoot>
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
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A3',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
              },
              customize: function (doc) {
                const tanggal = $('#tanggal').val();

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

                doc.defaultStyle.fontSize = 10;
                doc.pageMargins = [10, 40, 10, 50];
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
                        text: 'LAPORAN CONTROL TEMBAGA',
                        bold: true,
                        fontSize: 14,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    },
                    {
                        text: 'PERIODE : ' + tanggal,
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
                    const alignRightCols        = [0, 6]; // adjust index if needed
                    const body                  = mainTable.table.body;

                    const fifoColumnIndex       = 3; // FIFO
                    const tglMasukColumnIndex   = 2; // Tgl. Masuk

                    function getCanvasFromMonth(month) {
                        const warnaMap = {
                            1: '#18b7ec',
                            2: '#eb0f0f',
                            3: '#ffffff',
                            4: '#000000',
                            5: '#2cdb1f',
                            6: '#eff221',
                            7: '#18b7ec',
                            8: '#eb0f0f',
                            9: '#ffffff',
                            10: '#000000',
                            11: '#2cdb1f',
                            12: '#eff221',
                        };

                        const bentuk = month <= 6 ? 'kotak' : 'segitiga';
                        const warna = warnaMap[month];

                        if (bentuk === 'kotak') {
                            return {
                                canvas: [
                                    {
                                        type: 'rect',
                                        x: 0,
                                        y: 0,
                                        w: 20,
                                        h: 20,
                                        r: 2,
                                        color: warna,
                                        lineColor: 'black',
                                        lineWidth: 2
                                    }
                                ],
                                alignment: 'center',
                                margin: [0, 2, 0, 2]
                            };
                        } else {
                            return {
                                canvas: [
                                    {
                                        type: 'polyline',
                                        lineColor: 'black',
                                        color: warna,
                                        closePath: true,
                                        points: [
                                            { x: 10, y: 0 },
                                            { x: 20, y: 20 },
                                            { x: 0, y: 20 }
                                        ]
                                    }
                                ],
                                alignment: 'center',
                                margin: [0, 2, 0, 2]
                            };
                        }
                    }

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

                        // FIFO canvas rendering
                        const tglMasukText = body[i][tglMasukColumnIndex]?.text;
                        const month = tglMasukText ? parseInt(tglMasukText.split('-')[1]) : null;

                        if (month && month >= 1 && month <= 12) {
                            body[i][fifoColumnIndex] = getCanvasFromMonth(month);
                        }
                    }

                    // Baris terakhir
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
            "url": "<?php echo base_url(); ?>tembaga/tembaga_report_list",
            "type": "POST",
            "data": function(data) {
              data.supplier     = $('#SupplierList').val();
              data.po_number    = $('#POList').val();
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          fixedColumns: {
            left: 2
          },
          "aoColumns": [
            { "No": "No" , "sClass": "text-right"},
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Tgl. Masuk": "Tgl. Masuk" , "sClass": "text-center" },
            { "FIFO": "FIFO" , "sClass": "text-center" },
            { "Nomor PO": "Nomor PO" , "sClass": "text-left" },
            { "Supplier": "Supplier" , "sClass": "text-left" },
            { "Qty. Netto": "Qty. Netto" , "sClass": "text-right" },
            { "Print Label Barcode": "Print Label Barcode" , "sClass": "text-left" },
            { "QC": "QC" , "sClass": "text-left" },
            { "Note": "Note" , "sClass": "text-left" }
          ],
          "footerCallback": function (row, data, start, end, display) {
            const api = this.api();

            // Hitung total semua baris (tidak terpengaruh filter)
            const totalAllRows = api.rows().count();

            // Hitung total semua data yang difilter
            const totalFilteredRows = api.rows({ search: 'applied' }).count();

            // SUM kolom Qty. Netto (misal index 6)
            const totalNettoAll = api
                .column(6)           // index kolom Qty. Netto
                .data()
                .reduce(function (a, b) {
                    // Hapus tanda selain angka (misal jika ada titik/format)
                    const x = typeof a === 'string' ? a.replace(/[^\d.-]/g, '') : a;
                    const y = typeof b === 'string' ? b.replace(/[^\d.-]/g, '') : b;
                    return parseFloat(x) + parseFloat(y);
                }, 0);

            // SUM kolom Qty. Netto yang difilter
            const totalNettoFiltered = api
            .column(6, { search: 'applied' })
            .data()
            .reduce(function (a, b) {
                const x = typeof a === 'string' ? a.replace(/[^\d.-]/g, '') : a;
                const y = typeof b === 'string' ? b.replace(/[^\d.-]/g, '') : b;
                return parseFloat(x) + parseFloat(y);
            }, 0);

            console.log(totalNettoAll);
            console.log(totalNettoFiltered.toFixed(2));

            // Tampilkan hasil di element footer
            // $('#TotalBobin').html(
            //     `Total Netto: ${totalNettoFiltered.toLocaleString('id-ID')} dari ${totalNettoAll.toLocaleString('id-ID')}`
            // );

            // Jika ingin tetap menampilkan total baris juga:
            $('#TotalBobin').html(totalAllRows);
            $('#TotalNetto').html(totalNettoFiltered.toFixed(2));
          }
          // "footerCallback": function (row, data, start, end, display) {
          //   const api = this.api();

          //   // Hitung total data yang sedang tampil (setelah filter)
          //   const totalFiltered = api.rows({ search: 'applied' }).count();

          //   // Hitung total semua data (tanpa filter)
          //   const totalAll = api.rows().count();

          //   //SET NILAI KE FOOTER
          //   $('#TotalBobin').html(totalAll);
          // }
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        };

        $('#POList').select2({
          placeholder: "-- Pilih --",
          allowClear: true,
          ajax: {
            url: "<?php echo base_url(); ?>tembaga/get_po_number",
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function(params) {
              return {
                search: params.term,
                Supplier: $('#SupplierList').val(),
                StartDate: $('#start_date').val(),
                EndDate: $('#end_date').val()
              };
            },
            processResults: function(response) {
              let results = [
                { id: "all", text: "Select All" }
              ];
              
              // Append the fetched data
              $.each(response, function(index, item) {
                results.push({
                  id: item.id,
                  text: item.name
                });
              });

              return { results: results };
            }
          }
        });
      });
    </script>
  </body>
</html>