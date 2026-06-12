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
                                  <select name="DeptShow" id="DeptShow" class="form-control">
                                    <option value="" <?= empty($DEPTID) ? 'selected' : '' ?> disabled>-- Pilih --</option>
                                    
                                    <?php if (!empty($DEPTNAME) && (strtoupper($DEPTNAME) === 'IT' || strtoupper($DEPTNAME) === 'HRD' || strtoupper($DEPTNAME) === 'ACCOUNTING')): ?>
                                      <option value="" <?= ($DEPTID === 'ALL') ? 'selected' : '' ?>>ALL DEPARTEMEN</option>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
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

    <!-- MODAL EDIT TUNJANGAN -->
    <div class="modal fade" id="tunjanganModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Tambah Tunjangan</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_tunjangan()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="tunjanganForm">
              <input type="hidden" name="Nomor" id="Nomor">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="Nip" id="Nip" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="Name" id="Name" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Tunjangan</label>
                <div class="col-sm-4 mb-1 form-error">
                  <input type="text" name="Amount" id="Amount" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Jenis</label>
                <div class="col-sm-4 mb-1 form-error">
                  <input type="text" name="JenisTunjangan" id="JenisTunjangan" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="terapkan_tunjangan_pegawai()" id="btnUpdate" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Terapkan</button>
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
      // =======================================================
      // === FUNGSI UTILITAS GLOBAL (DAPAT DIPANGGIL DI MANA SAJA) ===
      // =======================================================

      /**
       * Mengkonversi string angka format Indonesia (ID) menjadi string numerik standar (tanpa pemisah ribuan)
       * @param {string} data Nilai sel dari DataTables.
       * @returns {string} String numerik yang sudah dibersihkan atau '0'.
       */
      function cleanIDNumber(data) {
          let clean = String(data).trim();
          
          // Hapus semua karakter non-numerik (kecuali titik, koma, dan minus)
          clean = clean.replace(/[^\d.,-]/g, '');

          if (clean.includes(',')) {
              // JIKA ada koma (desimal), hapus titik (ribuan) dan ganti koma dengan titik (desimal standar)
              clean = clean.replace(/\./g, ''); 
              clean = clean.replace(/,/g, '.'); 
          } else {
              // JIKA tidak ada koma, HAPUS SEMUA TITIK (dianggap sebagai ribuan)
              clean = clean.replace(/\./g, '');
          }

          return (!isNaN(clean) && clean !== '') ? clean : '0';
      }

      /**
       * Menghasilkan objek exportOptions untuk memformat kolom uang ke format numerik Excel.
       * Logika ini sama dengan yang sebelumnya berada di dalam fungsi cari().
       * @returns {object} Objek exportFormatter siap pakai.
       */
      function generateExportFormatter() {
          // Kolom uang yang memerlukan pemformatan: GAJI POKOK (14) hingga GAJI BERSIH (31)
          const moneyColumns = [14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
          const dynamicColsStart = 32;

          let exportFormatter = { 
              format: { 
                  body: function (data, row, column, node) {
                      
                      // 1. Perbaikan Darurat untuk UANG MAKAN (Indeks 17)
                      if (column === 17) {
                          const rawValue = String(data).trim();
                          if (rawValue === "8" || rawValue === "8.000" || Number(data) === 8) {
                              return "8000";
                          }
                      }
                      
                      // 2. Proses Kolom Uang (Indeks 14, 16-31)
                      if (moneyColumns.includes(column)) {
                          return cleanIDNumber(data);
                      }
                      
                      // 3. Proses Kolom Dinamis (Absensi, Indeks 32+) - Hapus tag HTML
                      if (column >= dynamicColsStart && typeof data === 'string') {
                          // Hapus semua tag HTML dan bersihkan spasi ekstra
                          return data.replace(/<[^>]*>?/gm, ' ').trim().replace(/\s+/g, ' ');
                      }
                      
                      // 4. Kembalikan data asli untuk kolom lainnya
                      return data;
                  } 
              } 
          };
          
          return exportFormatter;
      }

      //FUNCTION CARI
      function cari() {
        const start   = $('#start_date').val();
        const end     = $('#end_date').val();
        const deptid  = $('#DeptShow').val();

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
           "url": "<?php echo base_url(); ?>salary_calculation/calculate_list",
            type: 'POST',
            data: {
              StartDate: start,
              EndDate: end,
              DeptID: deptid
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
              pageSize: '2A0',
              customize: function (doc) {
                  const StartDate       = new Date($('#start_date').val());
                  const EndDate         = new Date($('#end_date').val());
                  let DeptElement       = $('#DeptShow');
                  let DeptLabel         = DeptElement.find('option:selected').text().trim();

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
                      text: 'LAPORAN PRESENSI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                      bold: true,
                      fontSize: 14,
                      style: 'subheader',
                      alignment: 'center',
                      margin: [0, 0, 0, 10]
                    },
                    {
                      text: 'DEPARTEMEN : ' + DeptLabel,
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
                    const alignRightCols = [0, 4, 5, 6, 7, 8, 9, 10, 11];
                    const body = mainTable.table.body;

                    for (let i = 1; i < body.length; i++) {
                        for (let j = 0; j < body[i].length; j++) {
                            if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                body[i][j].alignment = 'right';
                            }
                        }

                        // Tambahkan styling khusus untuk baris SUB TOTAL dan TOTAL
                        const cellText = typeof body[i][j].text === 'string' ? body[i][j].text.trim().toUpperCase() : null;
                        if (cellText === 'SUB TOTAL' || cellText === 'TOTAL') {
                            for (let k = 0; k < body[i].length; k++) {
                                body[i][k].bold = true;
                                if (cellText === 'SUB TOTAL') {
                                    body[i][k].fillColor = '#6c757d';
                                    body[i][k].color = '#fff';
                                }
                            }
                            break;
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
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN PRESENSI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            },
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-success',
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: generateExportFormatter() 
            },
            {
              extend: 'excelHtml5',
              text: 'Export Excel tanpa tanggal',
              title: '',
              className: 'btn btn-info',
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: {
                columns: ':lt(41)',
                format: {
                  body: function(data, row, column, node) {
                      // pastikan data string
                      let cleanData = (data === null || data === undefined) ? '' : data.toString();

                      // hapus tag HTML
                      cleanData = cleanData.replace(/<[^>]*>/g, '');

                      // hapus format uang di kolom tertentu
                      const moneyColumns = [19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,42];
                      if (moneyColumns.includes(column)) {
                          cleanData = cleanData.replace(/\./g, '').replace(/,/g, '');
                      }

                      return cleanData;
                  }
                }
              },

              // exportOptions: {
              //   //columns: ':lt(37)',
              //   columns: ':lt(40)',
              //   format: {
              //     body: function(data, row, column, node) {
              //         //const moneyColumns = [14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
              //         const moneyColumns = [19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40];
              //         if (moneyColumns.includes(column)) {
              //             // hapus titik dan koma
              //             return data.toString().replace(/\./g, '').replace(/,/g, '');
              //         }
              //         return data;
              //     }
              //   }
              // },
              customize: function (xlsx) {
                  const sheet = xlsx.xl.worksheets['sheet1.xml'];

                  // Hapus dua baris pertama (biasanya judul/tambahan bawaan DataTables)
                  $('row[r="2"]', sheet).remove(); // hapus baris 1
                  $('row[r="3"]', sheet).remove(); // hapus baris 2

                  // Re-index ulang baris setelah dihapus agar Excel tidak error
                  let rowIndex = 1;
                  $('row', sheet).each(function() {
                      $(this).attr('r', rowIndex++);
                      $('c', this).each(function() {
                          const cellRef = $(this).attr('r');
                          const col = cellRef.replace(/[0-9]/g, '');
                          $(this).attr('r', col + (rowIndex - 1));
                      });
                  });
              }
            }
          ],
          // select: {
          //   style: 'single'
          // },
          rowCallback: function(row, data, index) {
              $('td', row).each(function(colIndex) {
                  const cellValue = $(this).text().trim().toUpperCase();
                  $(this).removeClass('bg-success bg-info bg-danger bg-secondary text-white');

                  if (cellValue === 'IJIN') {
                      $(this).addClass('bg-success text-white');
                  } else if (cellValue === 'SAKIT') {
                      $(this).addClass('bg-info text-white');
                  } else if (cellValue === 'MINGGU') {
                      $(this).addClass('bg-secondary text-white');
                  } else if (cellValue === 'ALPA') {
                      $(this).addClass('bg-danger text-white');
                  }
              });
          }
        });
      }

      function parseFormattedNumber(str) 
      {
        if(!str) return 0;

        return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
      }

      function formatNumber(num) {
        return num.toLocaleString('id-ID', {maximumFractionDigits: 2});
      }

      function calculateNet(index) 
      {
        console.log(index);
        let totalEl    = $('#TotalGaji_' + index).text();
        let potonganEl = $('#PotonganHutang_' + index);
        let netEl      = $('#GajiBersih_' + index);

        //console.log(totalEl);
        //console.log(potonganEl);
        //console.log(netEl);

        let total      = parseFormattedNumber(totalEl);
        let potongan   = parseFormattedNumber(potonganEl.val());

        //console.log(total);
        //console.log(potongan);

        let net = total - potongan;
        console.log(net);

        netEl.text(formatNumber(net));
      }

      function openModalTunjangan(Nip, Name, TunjanganID, Jenis, No)
      {
        console.log(Nip, Name, TunjanganID, Jenis, No);
        $('#tunjanganForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#tunjanganModal .modal-title').text('Tambah Tunjangan Lembur');
        $('#Nip').val(Nip);
        $('#Name').val(Name);
        $('#Nomor').val(No);
        $('#JenisTunjangan').val(Jenis);
        
        $.ajax({
          url: "<?php echo base_url(); ?>salary_calculation/get_tunjangan_pegawai",
          type: "POST",
          data: {
            Id: TunjanganID
          },
          dataType: "JSON",
          success: function(response) {
            if (response.status_code == 200) {
              let Amount = response.data.Amount;
              $('#Amount').val(Amount);

              $('#tunjanganModal').modal('show');
            } else {
              Swal.fire('Oops...', response.message, 'info');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      function terapkan_tunjangan_pegawai()
      {
        //alert('aaaa');
       
        let NomorID         = $('#Nomor').val();
        let TotalGaji       = $('#TotalGaji_'+ NomorID).val();
        let Tunjangan       = $('#Amount').val();
        let TunjanganReal   = parseFloat(Tunjangan.replace(/\./g, ''), 10);
        let TotalGajiReal   = parseFloat(TotalGaji.replace(/\./g, ''), 10);
        let GrandTotalGaji  = TunjanganReal + TotalGajiReal;

        // Hapus atribut onclick
        $("#btnGroupAddon_"+ NomorID).removeAttr('onclick');
        // Setting nilai ke masing2 kolom
        $('#TunjanganHadir_'+ NomorID).val(Tunjangan);
        $('#TotalGaji_'+ NomorID).val(GrandTotalGaji.toLocaleString('id-ID'));
        $('#NettGaji_'+ NomorID).val(GrandTotalGaji.toLocaleString('id-ID'));

        reset_tunjangan();
      }

      function reset_tunjangan()
      {
        $('#tunjanganForm')[0].reset();
        $('#tunjanganModal').modal('hide');
        $('#tunjanganModal .modal-title').text('Tambah Tunjangan Lembur');
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
        row1 += '<th class="text-center" width="100px" rowspan="3">STATUS</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">DEPARTEMEN</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">SUNDAY</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">CYCLE</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">HK</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">HD</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">SAKIT</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">IJIN</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">ALPA</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TELAT<10</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TELAT>10</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TELAT>15</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">HOLIDAY</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">HOLIDAY DATE</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">OT MINGGU</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">OT MINGGU DATE</th>';
        row1 += '<th class="text-center" width="150px" rowspan="3">GAJI POKOK</th>';
        row1 += '<th class="text-center" width="50px" rowspan="3">PEMBAGI</th>';
        row1 += '<th class="text-center" width="150px" rowspan="3">UPAH</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">UANG MAKAN</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">UANG TUNJ. HADIR</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">UANG SHIFT</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">UANG LIBUR LEMBUR</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">JAM LEMBUR</th>';
        //row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL UPAH</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL TUNJ. MAKAN</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL TUNJ. HADIR</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL TUNJ. LEMBUR</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL TUNJ. SHIFT</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TOTAL LEMBUR</th>';
        row1 += '<th class="text-center" width="150px" rowspan="3">TOTAL UPAH</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">TUNJ. LAINNYA</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">POT. BPJS</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">POT. HUTANG</th>';
        row1 += '<th class="text-center" width="150px" rowspan="3">GAJI BERSIH</th>';
        row1 += '<th class="text-center" width="50px" rowspan="3">DEPTID</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">START DATE</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">END DATE</th>';
        row1 += '<th class="text-center" width="100px" rowspan="3">KETERANGAN</th>';
        row1 += `<th class="text-center" colspan="${dateList.length * 6}">TANGGAL</th>`;
        row1 += '</tr>';

        // 3. Baris kedua: Tanggal (01, 02, dst)
        let row2 = '<tr>';
        dateList.forEach(date => {
          row2 += `<th width="100px" class="text-center" colspan="6">${date}</th>`;
        });
        row2 += '</tr>';

        // 4. Baris ketiga: IN/OUT
        let row3 = '<tr>';
        dateList.forEach(() => {
          row3 += '<th width="100px" class="text-center bg-success">IN</th>';
          row3 += '<th width="100px" class="text-center bg-warning">OUT</th>';
          row3 += '<th width="100px" class="text-center bg-danger">TELAT</th>';
          row3 += '<th width="100px" class="text-center bg-secondary">GH</th>';
          row3 += '<th width="100px" class="text-center bg-dark">IK</th>';
          row3 += '<th width="100px" class="text-center bg-info">TGH</th>';
        });
        row3 += '</tr>';
        //console.log(row3);

        return row1 + row2 + row3;
      }

      function generateDynamicAoColumns(start, end) {
        const startDate = new Date(start);
        const endDate   = new Date(end);
        const columns   = [];

        // --- BAGIAN 1: KOLOM TETAP (STATIC) ---
        columns.push({ sTitle: "NO", sClass: "text-right", width: "50px" });
        columns.push({ sTitle: "NIP", sClass: "text-center", width: "150px" });
        columns.push({ sTitle: "NAME", sClass: "text-left", width: "200px" });
        columns.push({ sTitle: "STATUS", sClass: "text-center", width: "80px" });
        columns.push({ sTitle: "DEPARTEMEN", sClass: "text-center", width: "100px" });
        columns.push({ sTitle: "SUNDAY", sClass: "text-right", width: "90px" });
        columns.push({ sTitle: "CYCLE", sClass: "text-right", width: "100px" });
        columns.push({ sTitle: "HK", sClass: "text-right", width: "100px" });
        columns.push({ sTitle: "HD", sClass: "text-right", width: "100px" });
        columns.push({ sTitle: "SAKIT", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "IJIN", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "ALPA", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TELAT<10", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TELAT>10", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TELAT>15", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "HOLIDAY", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "HOLIDAY DATE", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "OT MINGGU", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "OT MINGGU DATE", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "GAJI POKOK", sClass: "text-right", width: "150px" });
        columns.push({ sTitle: "PEMBAGI", sClass: "text-right", width: "50px" });
        columns.push({ sTitle: "UPAH", sClass: "text-right", width: "150px" });
        columns.push({ sTitle: "UANG MAKAN", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "UANG TUNJ. HADIR", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "UANG SHIFT", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "UANG LIBUR LEMBUR", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "JAM LEMBUR", sClass: "text-right", width: "80px" });
        //columns.push({ sTitle: "TOTAL UPAH", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL TUNJ. MAKAN", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL TUNJ. HADIR", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL TUNJ. LEMBUR", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL TUNJ. SHIFT", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL LEMBUR", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "TOTAL UPAH", sClass: "text-right", width: "150px" });
        columns.push({ sTitle: "TUNJ. LAINNYA", sClass: "text-right", width: "100px" });
        columns.push({ sTitle: "POT. BPJS", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "POT. HUTANG", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "GAJI BERSIH", sClass: "text-right", width: "120px" });
        columns.push({ sTitle: "DEPTID", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "START DATE", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "END DATE", sClass: "text-right", width: "80px" });
        columns.push({ sTitle: "KETERANGAN", sClass: "text-left", width: "100px" });

        // --- BAGIAN 2: KOLOM DINAMIS (PER TANGGAL) ---
        while (startDate <= endDate) {
            // Ambil tanggal (misal "01") dan Nama Bulan (misal "NOV") untuk sTitle
            // Note: sTitle ini opsional karena header HTML sudah digenerate manual,
            // tapi berguna jika fitur export excel/pdf diaktifkan.
            const dayStr = ("0" + startDate.getDate()).slice(-2);
            const monthStr = startDate.toLocaleString('en-US', { month: 'short' }).toUpperCase();
            
            // 1. IN
            columns.push({
                sTitle: `${dayStr} ${monthStr} IN`,
                sClass: "align-middle text-center",
                width: "100px",
                render: function (data, type, row) {
                    if (!data) return '';
                    const parts = data.split('|'); // [ShiftName, Time]
                    if (parts.length === 2 && parts[1]) {
                        const timeHHMM = parts[1].substr(0, 5);
                        return '<span style="font-size: 12px;">' + parts[0] + '</span>' + '<br><span class="font-weight-light">(' + timeHHMM + ')</span>';
                    }
                    return parts[0];
                }
            });

            // 2. OUT
            columns.push({
                sTitle: `${dayStr} ${monthStr} OUT`,
                sClass: "align-middle text-center",
                width: "100px",
                render: function (data, type, row) {
                    if (!data) return '';
                    const parts = data.split('|');
                    if (parts.length === 2 && parts[1]) {
                        const timeHHMM = parts[1].substr(0, 5);
                        return '<span style="font-size: 12px;">' + parts[0] + '</span>' + '<br><span class="font-weight-light">(' + timeHHMM + ')</span>';
                    }
                    return parts[0];
                }
            });

            // 3. TELAT (Tambahan Baru)
            columns.push({
                sTitle: `${dayStr} ${monthStr} TELAT`,
                sClass: "align-middle text-center", // Rata tengah untuk menit
                width: "80px",
                render: function (data, type, row) {
                    // Jika datanya 0 atau null, mungkin dikosongkan atau tetap 0
                    return (data && data != 0) ? data : ''; 
                }
            });

            // 4. GH (Tambahan Baru - Gaji Harian/Upah)
            columns.push({
                sTitle: `${dayStr} ${monthStr} GH`,
                sClass: "align-middle text-right", // Rata kanan untuk uang
                width: "100px",
                render: function (data, type, row) {
                    // Asumsi data sudah diformat string 'N0' dari SQL, atau raw number
                    return data ? data : '';
                }
            });

            // 5. IK (Tambahan Baru - Ijin Keluar)
            columns.push({
                sTitle: `${dayStr} ${monthStr} IK`,
                sClass: "align-middle text-right", // Rata kanan untuk uang
                width: "100px",
                render: function (data, type, row) {
                    return data ? data : '';
                }
            });

            // 6. TGH (Tambahan Baru - Total Gaji Harian setelah pot)
            columns.push({
                sTitle: `${dayStr} ${monthStr} TGH`,
                sClass: "align-middle text-right", // Rata kanan untuk uang
                width: "100px",
                render: function (data, type, row) {
                    return data ? data : '';
                }
            });

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
              pageSize: '2A0',
              customize: function (doc) {
                  const StartDate       = new Date($('#start_date').val());
                  const EndDate         = new Date($('#end_date').val());
                  let DeptElement       = $('#DeptShow');
                  let DeptLabel         = DeptElement.find('option:selected').text().trim();

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
                      text: 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                      bold: true,
                      fontSize: 14,
                      style: 'subheader',
                      alignment: 'center',
                      margin: [0, 0, 0, 10]
                    },
                    {
                      text: 'DEPARTEMEN : ' + DeptLabel,
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
                    const alignRightCols = [0, 4, 5, 6, 7, 8, 9, 10, 11];
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
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            },
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-success',
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: generateExportFormatter() 
            },
            {
              extend: 'excelHtml5',
              text: 'Export Excel tanpa tanggal',
              title: '',
              className: 'btn btn-info',
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'HITUNG GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: {
                columns: ':lt(41)',
                format: {
                  body: function(data, row, column, node) {
                      //const moneyColumns = [14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
                      const moneyColumns = [19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42];
                      if (moneyColumns.includes(column)) {
                          // hapus titik dan koma
                          return data.toString().replace(/\./g, '').replace(/,/g, '');
                      }
                      return data;
                  }
                }
              },
              customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                // Hapus dua baris pertama (biasanya judul/tambahan bawaan DataTables)
                $('row[r="2"]', sheet).remove(); // hapus baris 1
                $('row[r="3"]', sheet).remove(); // hapus baris 2

                // Re-index ulang baris setelah dihapus agar Excel tidak error
                let rowIndex = 1;
                $('row', sheet).each(function() {
                    $(this).attr('r', rowIndex++);
                    $('c', this).each(function() {
                        const cellRef = $(this).attr('r');
                        const col = cellRef.replace(/[0-9]/g, '');
                        $(this).attr('r', col + (rowIndex - 1));
                    });
                });
              }
            }
          ],
          // select: {
          //   style: 'single'
          // },
          rowCallback: function(row, data, index) {
            // Loop semua kolom
            $('td', row).each(function(colIndex) {
              const cellValue = $(this).text().trim().toUpperCase();

              // Reset class dulu
              $(this).removeClass('bg-success bg-info bg-danger bg-secondary text-white');

              if (cellValue === 'IJIN') {
                $(this).addClass('bg-success text-white');
              } else if (cellValue === 'SAKIT') {
                $(this).addClass('bg-info text-white');
              } else if (cellValue === 'MINGGU') {
                $(this).addClass('bg-secondary text-white');
              } else if (cellValue === 'ALPA') {
                $(this).addClass('bg-danger text-white');
              }
            });
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
            "url": "<?php echo base_url(); ?>salary_calculation/calculate_list",
            "type": "POST",
            "data": function(data) {
              data.StartDate   = $('#start_date').val();
              data.EndDate     = $('#end_date').val();
              data.DeptID      = $('#DeptShow').val();
            }
          },
          fixedColumns: {
            left: 4
          },
          aoColumns: dynamicColumns
        });
      });
    </script>
  </body>
</html>