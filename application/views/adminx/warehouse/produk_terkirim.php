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
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead class="bg-primary text-center">
                                    <tr class="bg-primary">
                                      <th class="text-center">NO.</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">TGL. SCAN</th>
                                      <th class="text-center">NO. DO</th>
                                      <th class="text-center">PO CUSTOMER</th>
                                      <th class="text-center">PART ID</th>
                                      <th class="text-center">PART NAME</th>
                                      <th class="text-center">QTY. ORDER</th>
                                      <th class="text-center">QTY. PER BOX</th>
                                      <th class="text-center">TOTAL BOX</th>
                                      <!-- <th class="text-center">TOTAL BOX KIRIM</th> -->
                                      <th class="text-center">APPROVED BY</th>
                                      <th class="text-center">BARCODE ID</th>
                                      <th class="text-center">LOKASI SCAN</th>
                                      <th class="text-center">DIVISI</th>
                                      <th class="text-center">CUSTOMER</th>
                                      <th class="text-center">DRIVER + MOBIL</th>
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
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Tambahkan Driver dan Mobil</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <input type="hidden" name="Id" id="Id">
              <div class="form-group row">
                <div class="col-sm-10">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Ekspedisi</label>
                  </div>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nama Driver</label>
                <div class="col-sm-9">
                  <input type="text" id="nama_driver_input" name="nama_driver" class="form-control text-capitalize" hidden disabled>
                  <select id="nama_driver_select" name="nama_driver" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="BENI">BENI</option>
                    <option value="ROHMAN">ROHMAN</option>
                    <option value="WAHYUDIN">WAHYUDIN</option>
                    <!-- <option value="ROBI A.R">ROBI A.R</option> -->
                    <option value="MUHAMAD AHYADI MA'RUF">MUHAMAD AHYADI MA'RUF</option>
                    <option value="MUSTAKIM">MUSTAKIM</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">No. Polisi</label>
                <div class="col-sm-9">
                  <input type="text" id="no_polisi_input" name="no_polisi" class="form-control text-uppercase" hidden disabled>
                  <select id="no_polisi_select" name="no_polisi" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="A 8552 ZT">A 8552 ZT</option>
                    <option value="A 9372 ZA">A 9372 ZA</option>
                    <option value="A 9403 ZX">A 9403 ZX</option>
                    <option value="A 1193 YE">A 1193 YE</option>
                    <option value="A 8762 YX">A 8762 YX</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Checker</label>
                <div class="col-sm-9">
                  <select id="Checker2" name="Checker2" class="form-control">
                    <option value="0" selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="CARMONO">CARMONO</option>
                    <!-- <option value="FAJAR MAULANA">FAJAR MAULANA</option> -->
                    <option value="MUSTAKIM">MUSTAKIM</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Persiapan Planning</label>
                <div class="col-sm-9">
                  <input type="text" id="PersiapanPlanning" name="PersiapanPlanning" value="SLAMET HARYONO" class="form-control" placeholder="Persiapan Planning" maxlength="8" autocomplete="off" data-required="true" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Keterangan</label>
                <div class="col-sm-9">
                  <textarea name="Notes" id="Notes" rows="3" class="form-control" placeholder="Keterangan tambahan"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">No. DO :</label>
                  <input type="text" class="form-control" name="no_do" id="no_do" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">No. PO :</label>
                  <input type="text" class="form-control" name="no_po" id="no_po" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">Customer :</label>
                  <input type="text" class="form-control" name="nm_customer" id="nm_customer" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">QR Code :</label>
                  <input type="text" class="form-control" name="no_barcode" id="no_barcode" readonly="readonly">
                  <input type="hidden" name="part_no" id="part_no">
                  <input type="hidden" name="qty_order" id="qty_order">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
            <button id="btnSave" type="button" onclick="update_data();" class="btn btn-primary waves-effect waves-light ">Update</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {

        var start = moment().subtract(1, 'days');
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
      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      //FUNCTION HAPUS
      function openModalDelete(id, barcode_id) {
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
              url: '<?php echo base_url(); ?>warehouse/produk_terkirim_hapus',
              data: {
                Id: id,
                BarcodeId: barcode_id
              },
              type: 'POST',
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
              },
              error: function() {
                $("#loading").hide();
                alert('Something is wrong');
              },
            });
          }
        })
      }

      //FUNCTION RESET
      function reset_all() {
        $('#modal').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Edit Barang Terkirim');
      }

      //FUNCTION EDIT
      function edit(id, barcode_id) {
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>warehouse/produk_terkirim_edit",
          type: "POST",
          data: {
            Id: id,
            BarcodeId: barcode_id
          },
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              console.log(data.DataHD)
              $('[name="Id"]').val(data.DataHD.id);
              $('[name="no_do"]').val(data.DataHD.no_do);
              $('[name="no_po"]').val(data.DataHD.no_po);
              $('[name="nm_customer"]').val(data.DataHD.nama_customer);
              $('[name="no_barcode"]').val(data.DataHD.barcode_id);
              $('[name="nama_driver"]').val(data.DataHD.nama_driver);
              $('[name="no_polisi"]').val(data.DataHD.no_polisi);
              $('[name="Checker2"]').val(data.DataHD.checker);
              $('[name="PersiapanPlanning"]').val(data.DataHD.persiapan_planning);
              $('[name="Notes"]').val(data.DataHD.notes);

              // SET EKSPEDISI
              if (data.DataHD.ekspedisi === 'Y') {
                $('#flexSwitchCheckDefault').prop('checked', true);
                $('#nama_driver_input').removeAttr('disabled').removeAttr('hidden').show();
                $('#nama_driver_select').hide();
                $('#no_polisi_input').removeAttr('disabled').removeAttr('hidden').show();
                $('#no_polisi_select').hide();
              } else {
                $('#flexSwitchCheckDefault').prop('checked', false);
                $('#nama_driver_input').prop('readonly', false).attr('disabled', true).hide();
                $('#nama_driver_select').show();
                $('#no_polisi_input').prop('readonly', false).attr('disabled', true).hide();
                $('#no_polisi_select').show();
              }

              $('#modal').modal('show');
              $('.modal-title').text('Edit Barang Terkirim');
              $('#btnSave').text('Update');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION UPDATE
      function update_data() {
        var form_data = $('#RegisterValidation').serializeArray();

        // Tambahkan nilai checkbox ekspedisi ke payload (tidak ikut serializeArray jika unchecked)
        var ekspedisi = $('#flexSwitchCheckDefault').is(':checked') ? 'Y' : 'N';
        form_data.push({ name: 'ekspedisi', value: ekspedisi });

        $.ajax({
          url: "<?php echo base_url(); ?>warehouse/produk_terkirim_update",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();
            $(".help-block").html('');

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modal').modal('hide');
              reload_table();
              reset_all();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else if (data.status == 'forbidden') {
              $("#loading").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else {
              $("#loading").hide();

              for (var i = 0; i < data.inputerror.length; i++) {
                  var inputName = data.inputerror[i];
                  var errorMsg = data.error_string[i];

                  var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                  if (arrayMatch) {
                      var arrayName = arrayMatch[1];
                      var arrayIndex = parseInt(arrayMatch[2]);
                      var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                      if (!inputElem.prop('disabled')) {
                          inputElem.closest('.form-error').addClass('has-error');
                          if (inputElem.next('.help-block').length === 0) {
                              inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                          }
                      }
                  } else {
                      var inputElem = $('[name="' + inputName + '"]:not(:disabled)');
                      inputElem.each(function () {
                          var $el = $(this);
                          $el.closest('.form-error').addClass('has-error');
                          if ($el.next('.help-block').length === 0) {
                              $el.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                          }
                      });
                  }
              }
            }

            $("#btnSave").text('Update');
            $("#btnSave").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Update');
            $('#btnSave').prop('disabled', false);
          }
        });
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      // function generateExportFormatter() 
      // {
      //     // Kolom uang yang memerlukan pemformatan: GAJI POKOK (14) hingga GAJI BERSIH (31)
      //     const moneyColumns = [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
      //     const dynamicColsStart = 32;

      //     let exportFormatter = { 
      //         format: { 
      //             body: function (data, row, column, node) {
                      
      //                 // 1. Perbaikan Darurat untuk UANG MAKAN (Indeks 17)
      //                 if (column === 17) {
      //                     const rawValue = String(data).trim();
      //                     if (rawValue === "8" || rawValue === "8.000" || Number(data) === 8) {
      //                         return "8000";
      //                     }
      //                 }
                      
      //                 // 2. Proses Kolom Uang (Indeks 14, 16-31)
      //                 if (moneyColumns.includes(column)) {
      //                     return cleanIDNumber(data);
      //                 }
                      
      //                 // 3. Proses Kolom Dinamis (Absensi, Indeks 32+) - Hapus tag HTML
      //                 if (column >= dynamicColsStart && typeof data === 'string') {
      //                     // Hapus semua tag HTML dan bersihkan spasi ekstra
      //                     return data.replace(/<[^>]*>?/gm, ' ').trim().replace(/\s+/g, ' ');
      //                 }
                      
      //                 // 4. Kembalikan data asli untuk kolom lainnya
      //                 return data;
      //             } 
      //         } 
      //     };
          
      //     return exportFormatter;
      // }

      // function generateExportFormatter() {
      //     // Indeks kolom yang ingin Anda EKSPOR (menghilangkan indeks 1: '#').
      //     const columnsToExport = [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

      //     // Indeks kolom ASLI yang datanya perlu dibersihkan dari tag HTML (hanya indeks 0: 'NO').
      //     const columnsToStripHtml = [0]; 
          
      //     // Indeks kolom ASLI yang berisi angka (7: QTY. ORDER, 8: QTY. PER BOX, 9: TOTAL BOX)
      //     const numericColumns = [7, 8, 9]; 

      //     return {
      //         // 1. Definisikan kolom yang akan diekspor berdasarkan indeks aslinya
      //         columns: columnsToExport,

      //         // 2. Modifier
      //         modifier: {
      //             page: 'all', 
      //             search: 'applied' 
      //         },
              
      //         // 3. Fungsi untuk memformat data sebelum dimasukkan ke dalam Excel
      //         format: {
      //             body: function (data, rowIdx, colIdx, node) {
      //                 // colIdx di sini adalah indeks kolom ASLI DataTables (0, 2, 3, 4, ...)
                      
      //                 // Fungsi helper untuk menghapus tag HTML
      //                 const strip_tags = (html) => {
      //                     const tmp = document.createElement("DIV");
      //                     tmp.innerHTML = html;
      //                     return tmp.textContent || tmp.innerText || "";
      //                 };

      //                 // a) Bersihkan HTML (Hanya untuk kolom 'NO' / indeks 0)
      //                 if (columnsToStripHtml.includes(colIdx)) {
      //                     // Pastikan data adalah string sebelum strip_tags
      //                     return strip_tags(data.toString());
      //                 }

      //                 // b) Format Kolom Angka
      //                 if (numericColumns.includes(colIdx)) {
      //                     // Pastikan data adalah string, hapus pemisah, dan ganti desimal ke titik
      //                     let cleanedData = data.toString().replace(/[^0-9.,]/g, '');
      //                     cleanedData = cleanedData.replace(/\./g, '').replace(/,/g, '.'); 
      //                     return cleanedData;
      //                 }

      //                 // c) Untuk kolom lain, kembalikan data sebagai string
      //                 return data.toString();
      //             },
                  
      //             // Hapus format header. Biarkan DataTables mengambil header secara otomatis
      //             // dari konfigurasi aoColumns atau <th> tabel.
      //             header: undefined 
      //         }
      //     };
      // }

      function generateExportFormatter() {
        // Indeks kolom yang ingin Anda EKSPOR (menghilangkan indeks 1: '#').
        const columnsToExport = [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

        // Indeks kolom ASLI yang datanya perlu dibersihkan dari tag HTML.
        // DITAMBAH indeks 3 ('NO. DO')
        const columnsToStripHtml = [0, 3]; // Indeks 0 ('NO'), Indeks 3 ('NO. DO') 
        
        // Indeks kolom ASLI yang berisi angka (7, 8, 9)
        const numericColumns = [7, 8, 9]; 

        return {
          // 1. Definisikan kolom yang akan diekspor berdasarkan indeks aslinya
          columns: columnsToExport,

          // 2. Modifier
          modifier: {
              page: 'all', 
              search: 'applied' 
          },
          
          // 3. Fungsi untuk memformat data sebelum dimasukkan ke dalam Excel
          format: {
              body: function (data, rowIdx, colIdx, node) {
                  // colIdx di sini adalah indeks kolom ASLI DataTables
                  
                  // Fungsi helper untuk menghapus tag HTML
                  const strip_tags = (html) => {
                      // Membuat elemen DIV sementara untuk memparsing HTML
                      const tmp = document.createElement("DIV");
                      tmp.innerHTML = html;
                      // Mengambil hanya teks kontennya
                      return tmp.textContent || tmp.innerText || "";
                  };

                  // a) Bersihkan HTML (Untuk kolom 'NO' dan 'NO. DO')
                  if (columnsToStripHtml.includes(colIdx)) {
                      // Pastikan data adalah string sebelum strip_tags
                      return strip_tags(data.toString());
                  }

                  // b) Format Kolom Angka
                  if (numericColumns.includes(colIdx)) {
                      // Pastikan data adalah string, hapus pemisah, dan ganti desimal ke titik
                      let cleanedData = data.toString().replace(/[^0-9.,]/g, '');
                      cleanedData = cleanedData.replace(/\./g, '').replace(/,/g, '.'); 
                      return cleanedData;
                  }

                  // c) Untuk kolom lain, kembalikan data sebagai string
                  return data.toString();
              },
              
              // Biarkan DataTables mengambil header secara otomatis
              header: undefined 
          }
      };
    }

      function cleanIDNumber(data) 
      {
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

      $(document).ready(function() {
        $("#loading").hide();

        // EVENT HANDLER CHECKBOX EKSPEDISI
        $('#flexSwitchCheckDefault').on('change', function() {
          if ($(this).is(':checked')) {
            // Ekspedisi aktif: tampilkan input text readonly, sembunyikan select
            $('#nama_driver_input').removeAttr('disabled').removeAttr('hidden').show();
            $('#nama_driver_select').hide();
            $('#no_polisi_input').removeAttr('disabled').removeAttr('hidden').show();
            $('#no_polisi_select').hide();
          } else {
            // Ekspedisi tidak aktif: sembunyikan input text (nilai dipertahankan), tampilkan select
            $('#nama_driver_input').prop('readonly', false).attr('disabled', true).hide();
            $('#nama_driver_select').show();
            $('#no_polisi_input').prop('readonly', false).attr('disabled', true).hide();
            $('#no_polisi_select').show();
          }
        });

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
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

                return 'LAPORAN BARANG TERKIRIM PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: generateExportFormatter() 
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
          "processing": true, //Feature control the processing indicator.
          "serverSide": false, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>warehouse/produk_terkirim_list_range",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          select: {
            style: 'single'
          },
          "aoColumns": [
            {"NO": "NO", "sClass": "text-right"},
            {"#": "#", "sClass": "text-center"},
            {"TGL. SCAN": "TGL. SCAN", "sClass": "text-center"},
            {"NO. DO": "NO. DO", "sClass": "text-left"},
            {"PO CUSTOMER": "PO CUSTOMER", "sClass": "text-left"},
            {"PART ID": "PART ID", "sClass": "text-left"},
            {"PART NAME": "PART NAME","sClass": "text-left"},
            {"QTY. ORDER": "QTY. ORDER", "sClass": "text-right"},
            {"QTY. PER BOX": "QTY. PER BOX", "sClass": "text-right"},
            {"TOTAL BOX": "TOTAL BOX", "sClass": "text-right"},
            // {"TOTAL BOX KIRIM": "TOTAL BOX KIRIM", "sClass": "text-right"},
            {"APPROVED BY": "APPROVED BY", "sClass": "text-left"},
            {"BARCODE ID": "BARCODE ID", "sClass": "text-left"},
            {"LOKASI SCAN": "LOKASI SCAN", "sClass": "text-center"},
            {"DIVISI": "DIVISI", "sClass": "text-left"},
            {"CUSTOMER": "CUSTOMER","sClass": "text-left"},
            {"DRIVER + MOBIL": "DRIVER + MOBIL", "sClass": "text-left"}
          ],
          //Set column definition initialisation properties.
          "columnDefs": [
            {
              "targets": [0], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            }
          ]
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

        $('#Notes').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });
    </script>
  </body>
</html>