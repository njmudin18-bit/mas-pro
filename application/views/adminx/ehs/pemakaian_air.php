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
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-3 col-sm-12 m-t-3">
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
                                <div class="col-md-6 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="4%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">HARI</th>
                                      <th class="text-center" width="7%">TANGGAL</th>
                                      <th class="text-center" width="7%">06:00</th>
                                      <th class="text-center" width="7%">23:00</th>
                                      <th class="text-center" width="7%">M<sup>3</sup></th>
                                      <th class="text-center" width="8%">CREATED DATE</th>
                                      <th class="text-center" width="4%">CREATED BY</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot class="bg-primary text-center">
                                    <tr>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th class="text-center">TOTAL</th>
                                      <th id="TotalPemakaian"></th>
                                      <th></th>
                                      <th></th>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card">
                            <div class="card-header text-center">
                              <h5>SUMMARY BULANAN</h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select name="Tahun" id="Tahun" class="form-control">
                                    <?php
                                      $CurrentYear = (int)date('Y');
                                      $StartYear   = 2025;
                                      $EndYear     = $StartYear + 10;
                                      for ($Year = $StartYear; $Year <= $EndYear; $Year++) {
                                        $Selected = ($Year == $CurrentYear) ? 'selected' : '';
                                        
                                        echo "<option value=\"$Year\" $Selected>$Year</option>\n";
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <button id="btnCari2" type="button" class="btn btn-info btn-full-mobile" onclick="cari2();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table2" class="table table-striped table-bordered table-hover" width="70%">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="4%">NO</th>
                                      <th class="text-center" width="5%">TAHUN</th>
                                      <th class="text-center" width="7%">BULAN</th>
                                      <th class="text-center" width="7%">TOTAL PEMAKAIAN</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot class="bg-primary text-center">
                                    <tr>
                                      <th></th>
                                      <th></th>
                                      <th class="text-center">TOTAL</th>
                                      <th id="TotalPemakaian2"></th>
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
            <form id="RegisterValidation">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Angka Pagi</label>
                <div class="col-sm-4">
                  <input type="text" name="AngkaPagi" id="AngkaPagi" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Contoh: 4.000" maxlength="12" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Angka Malam</label>
                <div class="col-sm-4">
                  <input type="text" id="AngkaMalam" name="AngkaMalam" class="form-control" oninput="AllowDecimalAndComma(this)" placeholder="Contoh: 5.000" maxlength="12" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Volume</label>
                <div class="col-sm-4">
                  <input type="text" name="Volume" id="Volume" class="form-control" maxlength="12" required="required" placeholder="Hasil perhitungan otomatis" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-4">
                  <input type="date" name="Date" id="Date" class="form-control" required="required" autocomplete="off" placeholder="Tanggal">
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
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
      var save_method;
      var url;

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      function cari2() 
      {
        reload_table2();
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Data');
      }

      function closeModal() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>pemakaian_air/pemakaian_air_edit/" + id,
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
              $('[name="kode"]').val(data.Id);
              $('[name="AngkaMalam"]').val(formatRupiah(data.AngkaMalam));
              $('[name="AngkaPagi"]').val(formatRupiah(data.AngkaPagi));
              $('[name="Volume"]').val(formatRupiah(data.Volume));
              $('[name="Date"]').val(data.Date);
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
              url: '<?php echo base_url(); ?>pemakaian_air/pemakaian_air_deleted/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
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
              }
            });
          }
        })
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      function reload_table2() {
        table2.ajax.reload(null, false);
      }

      //VALIDATION AND ADD USER
      function save() {
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>pemakaian_air/pemakaian_air_add";
        } else {
          url = "<?php echo base_url(); ?>pemakaian_air/pemakaian_air_update";
        }

        var data_save = $('#RegisterValidation').serializeArray();

        // ajax adding data to database
        $.ajax({
          url: url,
          type: "POST",
          data: data_save,
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'ok') //if success close modal and reload ajax table
            {
              $('#modal').modal('hide');
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }
            $("#loading").hide();
            if(save_method == 'add') {
              $("#btnSave").text('Save');
            } else {
              $("#btnSave").text('Update');
            }
            $("#btnSave").prop('disabled', false);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled', false); //set button enable 
          }
        });
      };

      function cleanAndParse(value) {
        let cleanedValue  = value.replace(/\./g, ''); 
        cleanedValue      = cleanedValue.replace(/,/g, '.'); 
        
        return parseFloat(cleanedValue) || 0;
      }

      function formatNumberIndonesia(number) {
        let fixedNumber       = number.toFixed(2);       
        let parts             = fixedNumber.split('.');
        let integerPart       = parts[0];
        let decimalPart       = parts.length > 1 ? parts[1] : '';
        let formattedInteger  = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Gabungkan kembali dengan koma sebagai pemisah desimal
        return formattedInteger + (decimalPart ? ',' + decimalPart : '');
      }

      function hitungVolume() {
        let angkaMalamStr = $('#AngkaMalam').val();
        let angkaPagiStr  = $('#AngkaPagi').val();
        let angkaMalam    = cleanAndParse(angkaMalamStr);
        let angkaPagi     = cleanAndParse(angkaPagiStr);
        let volume        = angkaMalam - angkaPagi; 

        $('#Volume').val(formatNumberIndonesia(volume));
      }

      function formatRupiah(input, decimals = 2) {
          // ⭐️ Solusi: Paksa konversi input ke Number
          let number = parseFloat(input); 

          // Cek jika konversi gagal atau hasilnya NaN
          if (isNaN(number)) {
              console.error("Input tidak valid untuk formatRupiah:", input);
              return '0,00'; // Kembalikan nilai default jika gagal
          }

          // 1. Bulatkan angka ke jumlah desimal yang diinginkan dan ubah ke string dengan titik
          let fixedNumber = number.toFixed(decimals);
          
          // ... (Logika pemformatan sisanya sama) ...
          let parts = fixedNumber.split('.');
          let integerPart = parts[0];
          let decimalPart = parts.length > 1 ? parts[1] : '';
          let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

          return formattedInteger + (decimalPart ? ',' + decimalPart : '');
      }

      $(document).ready(function() {
        $("#loading").hide();

        $('#AngkaPagi, #AngkaMalam').on('input', function() {
          hitungVolume(); // Memanggil fungsi global
        });

        // Panggil hitungVolume saat dokumen dimuat (untuk nilai default)
        hitungVolume();

        table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-info',
              filename: function() {
                const StartDate       = new Date($('#start_date').val());
                const EndDate         = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN PEMAKAIAN AIR PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: {
                columns: [0, 2, 3, 4, 5, 6, 7, 8],
                format: {
                  body: function ( data, row, column, node ) {
                    
                    let dataString = String(data); 
                    let cleanedData = dataString.replace(/<[^>]*>/g, '');

                    // ⭐️ PERUBAHAN KHUSUS UNTUK KOLOM M³ (Indeks 6)
                    if (column === 6) { 
                      let excelFriendlyFloat = cleanedData.replace(/\./g, '').replace(/,/g, '.');

                      return excelFriendlyFloat; 
                    } else if (column === 4 || column === 5) {
                      return cleanedData; 
                    }
                    else {
                      return cleanedData;
                    }
                  },
                  footer: function ( data, column, node ) {
                    if (column === 5) {
                      let formattedTotal = $('#TotalPemakaian').text().trim();
                      if (formattedTotal) {
                        let excelFriendlyTotal = formattedTotal.replace(/\./g, '').replace(/,/g, '.');

                        return excelFriendlyTotal;
                      }
                    }
                    return data;
                  }
                }
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
          "processing": true, //Feature control the processing indicator.
          "serverSide": false, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>pemakaian_air/pemakaian_air_list",
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
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "HARI": "HARI" , "sClass": "text-left", "width": "50px" },
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "50px" },
            { "06:00": "06:00" , "sClass": "text-right", "width": "50px" },
            { "23:00": "23:00" , "sClass": "text-right", "width": "50px" },
            //{ "M<sup>3</sup>": "M<sup>3</sup>" , "sClass": "text-right", "width": "50px" },
            { "M<sup>3</sup>": "M<sup>3</sup>" , "sClass": "text-right", "width": "50px",
                "render": function(data, type, row) {
                    
                    let dataString = String(data); 
                    let cleanedData = dataString.replace(/<[^>]*>/g, ''); 

                    // ⭐️ PERUBAHAN UTAMA UNTUK TAMPILAN BROWSER
                    if (type === 'display' || type === 'filter') {
                        // Tampilkan string yang sudah diformat Indonesia ('12,70')
                        return cleanedData; 
                    }
                    
                    // Untuk sorting, ubah ke float yang benar
                    if (type === 'sort' || type === 'type') {
                        // '1.270' atau '12,70' -> 12.70
                        let floatData = cleanedData.replace(/\./g, '').replace(/,/g, '.');
                        return parseFloat(floatData);
                    }
                    
                    return cleanedData;
                }
            },
            { "CREATED DATE": "CREATED DATE" , "sClass": "text-center", "width": "50px" },
            { "CREATED BY": "CREATED BY" , "sClass": "text-center", "width": "30px" }
          ],
          "footerCallback": function (row, data, start, end, display) {
              let api = this.api();
              const columnIndex = 6; // Indeks kolom M³

              // Fungsi untuk membersihkan nilai angka dari format Indonesia (titik ribuan dan koma desimal)
              let cleanFormat = function (i) {
                  if (typeof i === 'string') {
                      // Menghapus titik (pemisah ribuan) dan mengganti koma (pemisah desimal) dengan titik
                      return i.replace(/\./g, '').replace(/,/g, '.') * 1;
                  }
                  return typeof i === 'number' ? i : 0;
              };

              // Hitung total kolom M³ (hanya data yang sedang ditampilkan)
              let totalVolume = api
                  .column(columnIndex, { page: 'all' }) 
                  .data()
                  .reduce(function (a, b) {
                      return cleanFormat(a) + cleanFormat(b);
                  }, 0); 

              // Format total hasil (kembalikan ke format Indonesia: titik ribuan, koma desimal)
              // Gunakan toFixed(2) untuk membulatkan ke 2 desimal
              let formattedTotal = totalVolume.toFixed(2).replace(/\./g, '#').replace(/,/g, '.').replace(/#/g, ',');
              
              // ⭐️ MEMASUKKAN NILAI KE TH DENGAN ID="TotalPemakaian"
              $('#TotalPemakaian').html(formattedTotal);
          },
          //Set column definition initialisation properties.
          "columnDefs": [
            {
              "targets": [0], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            } 
          ]
        });

        table2 = $('#order-table2').DataTable({
          dom: 'Bfrltip',
          "paging": false,
          "searching": false,
          "ordering": false,
          "info": false,
          buttons: [
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-info',
              filename: function() {
                const Tahun = $('#Tahun').val();

                return 'LAPORAN SUMMARY BULANAN PEMAKAIAN AIR TAHUN ' + Tahun;
              },
              // BARIS PENTING: Terapkan formatter baru
              exportOptions: {
                columns: [0, 1, 2, 3],
                format: {
                  body: function ( data, row, column, node ) {
                    
                    let dataString = String(data); 
                    let cleanedData = dataString.replace(/<[^>]*>/g, '');

                    // ⭐️ PERUBAHAN KHUSUS UNTUK KOLOM M³ (Indeks 6)
                    if (column === 3) { 
                      let excelFriendlyFloat = cleanedData.replace(/\./g, '').replace(/,/g, '.');

                      return excelFriendlyFloat; 
                    } else if (column === 4 || column === 5) {
                      return cleanedData; 
                    }
                    else {
                      return cleanedData;
                    }
                  },
                  footer: function ( data, column, node ) {
                    if (column === 3) {
                      let formattedTotal = $('#TotalPemakaian2').text().trim();
                      if (formattedTotal) {
                        let excelFriendlyTotal = formattedTotal.replace(/\./g, '').replace(/,/g, '.');

                        return excelFriendlyTotal;
                      }
                    }
                    return data;
                  }
                }
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
          "processing": true, //Feature control the processing indicator.
          "serverSide": false, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>pemakaian_air/summary_pemakaian_air_list",
            "type": "POST",
            "data": function(data) {
              data.tahun   = $('#Tahun').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          select: {
            style: 'single'
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "TAHUN": "TAHUN" , "sClass": "text-center", "width": "50px"},
            { "BULAN": "BULAN" , "sClass": "text-left", "width": "50px" },
            { "TOTAL PEMAKAIAN": "TOTAL PEMAKAIAN" , "sClass": "text-right", "width": "50px" }
          ],
          "footerCallback": function (row, data, start, end, display) {
              let api = this.api();
              const columnIndex = 3; // Indeks kolom M³

              // Fungsi untuk membersihkan nilai angka dari format Indonesia (titik ribuan dan koma desimal)
              let cleanFormat = function (i) {
                  if (typeof i === 'string') {
                      // Menghapus titik (pemisah ribuan) dan mengganti koma (pemisah desimal) dengan titik
                      return i.replace(/\./g, '').replace(/,/g, '.') * 1;
                  }
                  return typeof i === 'number' ? i : 0;
              };

              // Hitung total kolom M³ (hanya data yang sedang ditampilkan)
              let totalVolume = api
                  .column(columnIndex, { page: 'all' }) 
                  .data()
                  .reduce(function (a, b) {
                      return cleanFormat(a) + cleanFormat(b);
                  }, 0);

              let formattedTotal = totalVolume.toFixed(2).replace(/\./g, '#').replace(/,/g, '.').replace(/#/g, ',');

              $('#TotalPemakaian2').html(formattedTotal);
          },
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

        $("#AngkaPagi, #AngkaMalam, #Date").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>