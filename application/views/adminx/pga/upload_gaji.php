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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/css/filter_multi_select.css">
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
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control" multiple>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <!-- <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div> -->
                                  <?php //echo json_encode($PeriodeList); ?>
                                  <select name="PeriodeTanggal" id="PeriodeTanggal" class="form-control">
                                    <option value="">-- Pilih Periode --</option>
                                    <?php
                                      $i = 0;
                                      foreach ($PeriodeList as $periode):
                                        $selected = ($i == 0) ? 'selected' : '';
                                    ?>
                                      <option value="<?php echo $periode->StartDate . '|' . $periode->EndDate; ?>" <?php echo $selected; ?>>
                                        <?php echo $periode->Periode; ?>
                                      </option>
                                      <?php $i++; ?>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <input type="hidden" name="start_date" id="start_date" value="<?php echo $SelectedPeriode->StartDate; ?>">
                                  <input type="hidden" name="end_date" id="end_date" value="<?php echo $SelectedPeriode->EndDate; ?>">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">UPLOAD GAJI</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <!-- <th class="text-center" width="5%">#</th> -->
                                      <th class="text-center" width="5%">NIP</th>
                                      <th class="text-center" width="5%">NAME</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="5%">DEPARTEMEN</th>
                                      <th class="text-center" width="7%">START DATE</th>
                                      <th class="text-center" width="7%">END DATE</th>
                                      <th class="text-center" width="7%">SUNDAY</th>
                                      <th class="text-center" width="7%">CYCLE</th>
                                      <th class="text-center" width="7%">HK</th>
                                      <th class="text-center" width="7%">HD</th>
                                      <th class="text-center" width="7%">SAKIT</th>
                                      <th class="text-center" width="7%">IJIN</th>
                                      <th class="text-center" width="7%">ALPA</th>
                                      <th class="text-center" width="7%">TELAT<'10</th>
                                      <th class="text-center" width="7%">TELAT>10</th>
                                      <th class="text-center" width="7%">TELAT>15</th>
                                      <th class="text-center" width="7%">OT MINGGU</th>
                                      <th class="text-center" width="7%">HOLIDAY</th>
                                      <th class="text-center" width="7%">GAJI POKOK</th>
                                      <th class="text-center" width="7%">PEMBAGI</th>
                                      <th class="text-center" width="7%">UPAH</th>
                                      <th class="text-center" width="7%">UANG MAKAN</th>
                                      <th class="text-center" width="7%">UANG TUNJ. HADIR</th>
                                      <th class="text-center" width="7%">UANG SHIFT</th>
                                      <th class="text-center" width="7%">UANG LIBUR LEMBUR</th>
                                      <th class="text-center" width="7%">JAM LEMBUR</th>
                                      <!-- <th class="text-center" width="7%">TOTAL UPAH</th> -->
                                      <th class="text-center" width="7%">TOTAL TUNJ. MAKAN</th>
                                      <th class="text-center" width="7%">TOTAL TUNJ. HADIR</th>
                                      <th class="text-center" width="7%">TOTAL TUNJ. LEMBUR</th>
                                      <th class="text-center" width="7%">TOTAL TUNJ. SHIFT</th>
                                      <th class="text-center" width="7%">TOTAL LEMBUR</th>
                                      <th class="text-center" width="7%">POT. BPJS</th>
                                      <th class="text-center" width="7%">TOTAL GAJI</th>
                                      <th class="text-center" width="7%">TUNJ. LAINNYA</th>
                                      <th class="text-center" width="7%">POT. HUTANG</th>
                                      <th class="text-center" width="7%">GAJI BERSIH</th>
                                      <th class="text-center" width="10%">KETERANGAN</th>
                                      <th class="text-center" width="10%">CREATE DATE</th>
                                      <th class="text-center" width="10%">CREATE BY</th>
                                      <th class="text-center" width="10%">SUDAH KIRIM</th>
                                      <th class="text-center" width="10%">SUDAH KIRIM DI</th>
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

    <!-- MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formData" method="post">
              <input type="hidden" value="" name="kode">
              <input type="hidden" value="" name="Nomor">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Files</label>
                <div class="col-sm-6">
                  <input type="file" id="excelFile" name="excelFile" class="form-control" required="required" placeholder="Masukan keterangan" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset()">Close</button>
            <button id="btnSave" type="button" onclick="importExcel();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div id="loading" class="loading">Loading&#8230;</div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>
    <!-- JS IMPORT EXCEL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          //$('#start_date').val(start.format('YYYY-MM-DD'));
          //$('#end_date').val(end.format('YYYY-MM-DD'));
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
    <script>
      var save_method;
      var url;

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Upload gaji');
      }

      function closeModal() {
        $('#formData')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Upload gaji');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#formData')[0].reset();
        $('.modal-title').text('Upload gaji');
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>koreksi_absensi/koreksi_edit/" + id,
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              $('[name="kode"]').val(data.KoreksiID);
              $('[name="Nomor"]').val(data.Nomor);
              $('[name="DeptID"]').val(data.DEFAULTDEPTID);
              $('[name="Tanggal"]').val(data.Tanggal);
              $('[name="CheckInAsli"]').val(data.CheckInAsli);
              $('[name="CheckOutAsli"]').val(data.CheckOutAsli);
              $('[name="ChangeTo"]').val(data.ColumnChange);
              $('[name="CheckInKoreksi"]').val(data.CheckInKoreksi);
              $('[name="CheckOutKoreksi"]').val(data.CheckOutKoreksi);
              $('[name="Notes"]').val(data.Notes);

              let DeptID    = data.DEFAULTDEPTID;
              let UserID    = data.EmployeeID;
              let ChangeCol = data.ColumnChange;
              get_karyawan(DeptID, UserID);

              if (ChangeCol === "IN") {
                $("#CheckInKoreksi").prop("readonly", false);
              } else if (ChangeCol === "OUT") {
                $("#CheckOutKoreksi").prop("readonly", false);
              } else if (ChangeCol === "ALL") {
                $("#CheckInKoreksi").prop("readonly", false);
                $("#CheckOutKoreksi").prop("readonly", false);
              }

              $('#modal').modal('show');
              $('.modal-title').text('Edit Data');
              $('#btnSave').text('Update');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION HAPUS
      function openModalDelete(id) {
        Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>koreksi_absensi/koreksi_deleted/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
                $("#loading").hide();
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                var result = JSON.parse(data);
                if (result.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  $("#" + id).remove();
                  reload_table();
                }

                $("#loading").hide();
              }
            });
          }
        })
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      //IMPORT AND SAVE EXCEL
      function save(jsonData) {
        $.ajax({
          url : "<?php echo base_url(); ?>upload_gaji/proses_upload_gaji",
          type: "POST",
          data: JSON.stringify(jsonData),
          contentType: 'application/json',
          dataType: "JSON",
          beforeSend: function(data) {
            $("#loading").show();
          },
          success: function(data)
          {
            if (data.status_code == 200) {
              reload_table();
              $("#loading").hide();
              $('#formData')[0].reset();
            } else if (data.status_code == 400) {
              Swal.fire({
                title: capitalizeFirstLetter(data.status),
                text: data.message,
                icon: "warning"
              });
              $("#loading").hide();
            } else {
              Swal.fire({
                title: capitalizeFirstLetter(data.status),
                text: data.message,
                icon: "error"
              });
              $("#loading").hide();
            }
            $("#loading").hide();
            $('#modal').modal('hide');
            $('#formData')[0].reset();

            $('#btnSave').text('Save');
            $('#btnSave').attr('disabled', false);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            $("#loading").hide();
            alert('Error adding / update data');
            $("#loading").hide();
            $('#btnSave').text('Save');
            $('#btnSave').attr('disabled', false);
          }
        });
      }

      function importExcel() {
          const fileInput = document.getElementById('excelFile');
          const file = fileInput.files[0];
          
          if (!file) {
              Swal.fire({
                  title: "Oops....",
                  text: "Please select an Excel file.",
                  icon: "info"
              });
              return;
          }

          const reader = new FileReader();
          
          reader.onload = function(e) {
              const data = new Uint8Array(e.target.result);
              const workbook = XLSX.read(data, { type: 'array' });
              
              // Ambil sheet pertama
              const sheetName = workbook.SheetNames[0];
              const worksheet = workbook.Sheets[sheetName];

              // Konversi ke JSON (dengan cell kosong = null)
              let jsonData = XLSX.utils.sheet_to_json(worksheet, {
                  raw: false,
                  defval: null
              });

              // 🔹 Kolom yang ingin diformat menjadi yyyy-mm-dd
              const dateColumns = [
                'START DATE', 'END DATE', 'HOLIDAY DATE'
              ];

              // 🔹 Ubah hanya kolom tertentu ke format yyyy-mm-dd
              jsonData = jsonData.map(row => {
                  Object.keys(row).forEach(key => {
                      if (dateColumns.includes(key)) {
                          const value = row[key];
                          if (value) {
                              const d = new Date(value);
                              if (!isNaN(d)) {
                                  const yyyy = d.getFullYear();
                                  const mm = ('0' + (d.getMonth() + 1)).slice(-2);
                                  const dd = ('0' + d.getDate()).slice(-2);
                                  row[key] = `${yyyy}-${mm}-${dd}`;
                              }
                          }
                      }
                  });
                  return row;
              });

              console.log(jsonData);
              save(jsonData); // kirim ke backend jika perlu
          };
          
          reader.readAsArrayBuffer(file);
      }

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A2',
              exportOptions: {
                stripHtml: true,
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]
              },
              customize: function (doc) {
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

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
                    text: 'LAPORAN GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                    bold: true,
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 9];
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
                    }

                    // Style baris terakhir
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
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN GAJI PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            }
          ],
          "pagingType": "full_numbers",
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
          ],
          responsive: false,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          fixedColumns: {
            left: 4
          },
          select: {
            style: 'single'
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>upload_gaji/gaji_list",
            "type": "POST",
            "data": function(data) {
              let DeptShow = [];
              $('input[name="DeptShow"]:checked').each(function () {
                if ($(this).val()) {
                  DeptShow.push($(this).val());
                }
              });

              data.StartDate   = $('#start_date').val();
              data.EndDate     = $('#end_date').val();
              data.DeptID      = (DeptShow.length > 0) ? DeptShow : <?php echo $DEPTID; ?>;
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            // { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "NIP": "NIP" , "sClass": "text-left", "width": "50px"},
            { "NAME": "NAME" , "sClass": "text-left", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-left", "width": "50px"},
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "80px" },
            { "START DATE": "START DATE" , "sClass": "text-center", "width": "50px" },
            { "END DATE": "END DATE" , "sClass": "text-center", "width": "150px" },
            { "SUNDAY": "SUNDAY" , "sClass": "text-center", "width": "50px" },
            { "CYCLE": "CYCLE" , "sClass": "text-center", "width": "50px" },
            { "HK": "CHECK IN" , "sClass": "text-right", "width": "80px" },
            { "HD": "HD" , "sClass": "text-right", "width": "80px" },
            { "SAKIT": "SAKIT" , "sClass": "text-right", "width": "80px" },
            { "IJIN": "IJIN" , "sClass": "text-right", "width": "80px" },
            { "ALPA": "ALPA" , "sClass": "text-right", "width": "80px" },
            { "TELAT<10": "TELAT<10" , "sClass": "text-right", "width": "80px" },
            { "TELAT>10": "TELAT>10" , "sClass": "text-right", "width": "80px" },
            { "TELAT>15": "TELAT>15" , "sClass": "text-right", "width": "80px" },
            { "OT MINGGU": "OT MINGGU" , "sClass": "text-right", "width": "80px" },
            { "HOLIDAY": "HOLIDAY" , "sClass": "text-right", "width": "80px" },
            { "GAJI POKOK": "GAJI POKOK" , "sClass": "text-right", "width": "80px" },
            { "PEMBAGI": "PEMBAGI" , "sClass": "text-right", "width": "80px" },
            { "UPAH": "UPAH" , "sClass": "text-right", "width": "80px" },
            { "UANG MAKAN": "UANG MAKAN" , "sClass": "text-right", "width": "80px" },
            { "UANG TUNJ. HADIR": "UANG TUNJ. HADIR" , "sClass": "text-right", "width": "80px" },
            { "UANG SHIFT": "UANG SHIFT" , "sClass": "text-right", "width": "80px" },
            { "UANG LIBUR LEMBUR": "UANG LIBUR LEMBUR" , "sClass": "text-right", "width": "80px" },
            { "JAM LEMBUR": "JAM LEMBUR" , "sClass": "text-right", "width": "80px" },
            // { "TOTAL UPAH": "TOTAL UPAH" , "sClass": "text-right", "width": "80px" },
            { "TOTAL TUNJ. MAKAN": "TOTAL TUNJ. MAKAN" , "sClass": "text-right", "width": "80px" },
            { "TOTAL TUNJ. HADIR": "TOTAL TUNJ. HADIR" , "sClass": "text-right", "width": "80px" },
            { "TOTAL TUNJ. LEMBUR": "TOTAL TUNJ. LEMBUR" , "sClass": "text-right", "width": "80px" },
            { "TOTAL TUNJ. SHIFT": "TOTAL TUNJ. SHIFT" , "sClass": "text-right", "width": "80px" },
            { "TOTAL LEMBUR": "TOTAL LEMBUR" , "sClass": "text-right", "width": "80px" },
            { "POT. BPJS": "POT. BPJS" , "sClass": "text-right", "width": "80px" },
            { "TOTAL GAJI": "TOTAL GAJI" , "sClass": "text-right", "width": "80px" },
            { "TUNJ. LAINNYA": "TUNJ. LAINNYA" , "sClass": "text-right", "width": "80px" },
            { "POT. HUTANG": "POT. HUTANG" , "sClass": "text-right", "width": "80px" },
            { "GAJI BERSIH": "GAJI BERSIH" , "sClass": "text-right", "width": "80px" },
            { "KETERANGAN": "KETERANGAN" , "sClass": "text-left", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" },
            { "SUDAH KIRIM": "SUDAH KIRIM" , "sClass": "text-center", "width": "80px" },
            { "SUDAH KIRIM DI": "SUDAH KIRIM DI" , "sClass": "text-center", "width": "80px" }
          ],
          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
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

        $("#DeptID, #EmployeeID, #Tanggal, #ChangeTo, #CheckInPerubahan, #CheckOutPerubahan, #Notes").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Notes').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });

        $('#PeriodeTanggal').select2();

        $('#PeriodeTanggal').on('change', function() {
          const value = $(this).val(); // Nilai format: "2025-09-17|2025-09-30"
          
          if (value) {
            // 1. Pisahkan string
            const dates = value.split('|'); 
            const startDate = dates[0];
            const endDate = dates[1];

            // ---------------------------------------------------------
            // BAGIAN INI YANG MENYIMPAN NILAI BARU KE INPUT HIDDEN
            // ---------------------------------------------------------
            $('#start_date').val(startDate); // Update value input hidden start_date
            $('#end_date').val(endDate);     // Update value input hidden end_date
          }
        });
      });
    </script>
    <script>
      $(function () {
        const DeptShow = $('#DeptShow').filterMultiSelect({
          placeholderText: "Pilih",
          filterText: "Filter",
          selectAllText: "SELECT ALL",
          labelText: "",
          selectionLimit: 0,
          caseSensitive: false,
          allowEnablingAndDisabling: true,
        });
      });
    </script>
  </body>
</html>