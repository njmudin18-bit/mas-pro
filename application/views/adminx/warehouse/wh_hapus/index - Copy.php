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
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
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
                          <div class="row m-2 justify-content-center">
                            <div class="col-sm-2 text-center p-10  bg-primary">
                              <a href="<?php echo base_url() . 'warehouse_part/page_insert_data'; ?>"
                                class="mb-2 text-white">INPUT DATA PART</a>
                            </div>
                            <div class="col-sm-2 text-center p-10  bg-warning">
                              <a href="<?php echo base_url() . 'warehouse_part/master_rack'; ?>"
                                class="mb-2 text-white">MASTER RACK</a>
                            </div>
                            <div class="col-sm-2 text-center p-10  bg-success">
                              <a href="<?php echo base_url() . 'warehouse_part/index'; ?>"
                                class="mb-2 text-white">WAREHOUSE PART</a>
                            </div>
                            <div class="col-sm-2 text-center p-10  bg-danger">
                              <a href="<?php echo base_url() . 'warehouse_part/delete'; ?>"
                                class="mb-2 text-white">PART DELETE</a>
                            </div>
                          </div>
                          <hr class="m-2">
                          <div class="col-sm-12 mt-2">
                            <form id="trans_rack">
                              <div class="form-group row justify-content-left">
                                <label class="col-sm-2 col-form-label">Area </label>
                                <div class="col-sm-2">
                                  <select name="wh_lokasi" id="wh_lokasi"
                                    class="form-control">
                                    <option value="" disabled selected>-- Pilih --
                                    </option>
                                    <?php
                                    foreach ($wh_lokasi as $key => $value) {
                                    ?>
                                      <option
                                        value="<?php echo $value->wh_lokasi; ?>">
                                        <?php echo  $value->wh_lokasi; ?>
                                      </option>
                                    <?php
                                    }
                                    ?>
                                  </select>
                                  <span class="help-block"></span>
                                </div>

                              </div>
                              <div class="form-group row justify-content-left">
                                <label class="col-sm-2 col-form-label">User</label>
                                <div class="col-sm-2">
                                  <input type="text" name="user" id="user"
                                    class="form-control" readonly>
                                  <span class="help-block"></span>
                                </div>
                                <div class="col-sm-2">
                                  <button type="button" id="btnExportExcel"
                                    class="btn btn-success mt-2 mt-md-0">
                                    Export Excel
                                  </button>
                                </div>
                                <div class="col-sm-2">
                                  <button type="button" id="btnClearTable"
                                    class="btn btn-primary mt-2 mt-md-0">
                                    Clear table
                                  </button>
                                </div>
                              </div>


                            </form>
                          </div>
                          <hr class="m-2">
                          <div class="m-2">
                            <div id="DetailRak" class="dt-responsive table-responsive">
                              <table id="order-table"
                                class="table table-striped table-bordered nowrap"
                                width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="bg-primary">
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Part Kolom</th>
                                    <th class="text-center">Part ID | Part NAME</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-center">Lihat Mapping</th>
                                    <th class="text-center">Lokasi WH</th>
                                    <th class="text-center">Status Part</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                          <div class=" m-2 table-responsive" style="width: 90%;">
                            <h3>Status Part</h3>
                            <table class="table table-bordered table-sm  mx-auto"
                              style="background:#fff;">
                              <tr>
                                <td class="py-2 px-3 align-middle">
                                  <div
                                    style="background-color:#28a745; width:54px; height:24px; border-radius:4px;">
                                  </div>
                                </td>
                                <td class="py-2 px-3 align-middle">Part Bergerak kurang
                                  dari 1-2 Bulan</td>
                              </tr>
                              <tr>
                                <td class="py-2 px-3 align-middle">
                                  <div
                                    style="background-color:#ffc107; width:54px; height:24px; border-radius:4px;">
                                  </div>
                                </td>
                                <td class="py-2 px-3 align-middle">Part Bergerak kurang
                                  dari 2-3 Bulan</td>
                              </tr>
                              <tr>
                                <td class="py-2 px-3 align-middle">
                                  <div
                                    style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;">
                                  </div>
                                </td>
                                <td class="py-2 px-3 align-middle">Part Bergerak kurang
                                  dari 3-6 Bulan dan seterusnya</td>
                              </tr>
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


  <!-- Modal -->
  <div class="modal fade" id="modalPindah" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog">
      <form id="formPindah">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="judul_modal">Pindah Lokasi Part</h5>
          </div>
          <div class="modal-body">
            <div class="mb-3 row ">
              <input type="text" name="partid" id="partid" class="col-sm-4 form-control" hidden>
              <input type="text" name="id_rack_awal" id="id_rack_awal" class="col-sm-4 form-control"
                hidden>
              <input type="text" name="id_kolom_awal" id="id_kolom_awal" class="col-sm-4 form-control"
                hidden>
              <input type="text" name="wh_lokasi_awal" id="wh_lokasi_awal" class="col-sm-4 form-control"
                hidden>
              <label class="col-sm-4 col-form-label" for="id_rack_tujuan">Rack Tujuan</label>
              <select name="id_rack_tujuan" id="id_rack_tujuan" class="col-sm-4 form-select"
                required></select>
            </div>
            <div class="mb-3 row ">
              <label class="col-sm-4 col-form-label" for="id_kolom_tujuan">Kolom Tujuan</label>
              <select name="id_kolom_tujuan" id="id_kolom_tujuan" class="col-sm-4 form-select"
                required></select>
            </div>
            <div class="mb-3 row ">
              <label class="col-sm-4 col-form-label" for="wh_lokasi_tujuan">Lokasi Tujuan</label>
              <input type="text" name="wh_lokasi_tujuan" id="wh_lokasi_tujuan"
                class="col-sm-4 form-control" readonly>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Pindahkan</button>
          </div>
        </div>
      </form>
    </div>
  </div>


  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

  <script src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
  <script src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>

  <script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
  <script>
    function hapus(data) {
      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '<?= base_url("warehouse_part/hapus_data") ?>', // Ganti dengan URL endpoint Anda
            type: 'POST',
            data: {
              id: data
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                Swal.fire('Berhasil!', response.message, 'success');
                // misal reload tabel:
                $('#order-table').DataTable().ajax.reload();
              } else {
                Swal.fire('Gagal!', response.message, 'error');
              }
            },
            error: function(xhr, status, error) {
              Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
            }
          });
        }
      });
    }



    $('#formPindah').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        url: '<?= base_url("warehouse_part/pindah_data") ?>',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {

            Swal.fire('Berhasil!', response.message, 'success');

            $('#order-table').DataTable().ajax.reload(null, false); // false = keep paging
            $('#modalPindah').modal('hide');
          } else {
            Swal.fire('Gagal!', response.message, 'error');
          }
        },
        error: function() {
          Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
        }
      });
    });

    function pindah(data) {
      $('#pindah_id').val(data);

      // Opsional: Isi select option dari AJAX

      $.getJSON('<?= base_url("warehouse_part/get_rack_kolom") ?>', function(data) {
        let optionsRack = '';
        let optionsKolom = '';

        optionsRack = '<option value="" disabled selected>Pilih Rack</option>';

        data.rack.forEach(r => {
          optionsRack += `<option value="${r.id_rack}">${r.nama_rack}</option>`;
        });

        $('#id_rack_tujuan').html(optionsRack);

      });

      data_array = data.split('|');
      id_rack_awal = data_array[0];
      id_kolom_awal = data_array[1];
      partid = data_array[2];
      wh_lokasi_awal = data_array[3];


      $('#modalPindah').modal('show');
      $('#judul_modal').text('Pindah Part ID: ' + partid);
      $('#partid').val(partid);
      $('#id_rack_awal').val(id_rack_awal);
      $('#id_kolom_awal').val(id_kolom_awal);
      $('#wh_lokasi_awal').val(wh_lokasi_awal);
    }



    $('#id_rack_tujuan').change(function() {
      var id_rack = $(this).val();
      $('#nama_kolom').val('');
      $('#wh_lokasi_tujuan').val('');
      if (id_rack) {
        $.ajax({
          url: "<?php echo base_url('warehouse_part/get_rack_detail/'); ?>" + id_rack,
          type: "GET",
          dataType: "json",
          success: function(data) {
            var html = '<option value="" disabled selected>-- Pilih --</option>';
            $.each(data.kolom, function(i, v) {
              html += '<option value="' + v.id_kolom + '">' + v.nama_kolom +
                '</option>';
            });
            $('#id_kolom_tujuan').html(html);
            // Isi WH Lokasi
            $('#wh_lokasi_tujuan').val(data.wh_lokasi);
          },
          error: function() {
            $('#id_kolom_tujuan').val('');
            $('#wh_lokasi_tujuan').val('');
          }
        });
      }
    });


    $(document).ready(function() {

      $('#loading-screen').hide();

      // Clear table
      $('#btnClearTable').on('click', function() {
        Swal.fire({
          title: 'Yakin ingin menghapus semua data?',
          text: 'Data yang dihapus tidak dapat dikembalikan!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            console.info('Clear table button clicked');

            $.ajax({
              url: '<?php echo base_url(); ?>warehouse_part/clear_table',
              type: 'POST',
              dataType: 'json', // ← penting!
              success: function(response) {
                if (response.status === 'success') {
                  console.info('Table cleared successfully');
                  $('#order-table').DataTable().ajax.reload();
                  Swal.fire('Berhasil!', response.message, 'success');
                } else {
                  // Tampilkan pesan error dari server
                  Swal.fire('Ditolak!', response.message, 'error');
                }
              },
              error: function(xhr, status, error) {
                // Jika error jaringan atau 403
                let message = 'Terjadi kesalahan saat menghapus data.';

                // Coba parse JSON jika tersedia
                try {
                  const res = JSON.parse(xhr.responseText);
                  message = res.message || message;
                } catch (e) {
                  // Gunakan default
                }

                Swal.fire('Gagal!', message, 'error');
                console.error('Error clearing table:', error);
              }
            });
          }
        });
      });

      //export exel
      $('#btnExportExcel').on('click', function() {
        var table = document.getElementById('order-table');

        var html = `
                      <html xmlns:o="urn:schemas-microsoft-com:office:office"
                            xmlns:x="urn:schemas-microsoft-com:office:excel"
                            xmlns="http://www.w3.org/TR/REC-html40">
                        <head>
                          <meta charset="UTF-8">
                          <!--[if gte mso 9]>
                          <xml>
                            <x:ExcelWorkbook>
                              <x:ExcelWorksheets>
                                <x:ExcelWorksheet>
                                  <x:Name>Kartu Stock</x:Name>
                                  <x:WorksheetOptions>
                                    <x:DisplayGridlines/>
                                  </x:WorksheetOptions>
                                </x:ExcelWorksheet>
                              </x:ExcelWorksheets>
                            </x:ExcelWorkbook>
                          </xml>
                          <![endif]-->
                          <style>
                            table, th, td {
                              border: 1px solid black;
                              border-collapse: collapse;
                              text-align: center;
                            }
                          </style>
                        </head>
                        <body>
                          ${table.outerHTML}
                        </body>
                      </html>
                    `;

        // Gunakan Blob agar lebih stabil
        var blob = new Blob([html], {
          type: 'application/vnd.ms-excel'
        });
        var url = URL.createObjectURL(blob);

        var a = document.createElement('a');
        a.href = url;
        a.download = 'warehouse_part.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      });



      $('#wh_lokasi').on('change', function() {
        table.ajax.reload();
        get_rack_user(); // Jalankan ajax untuk ambil user di wh_lokasinya
      });

      function get_rack_user() {
        let wh_lokasi = $('#wh_lokasi').val();
        $.ajax({
          type: "POST",
          url: "<?php echo base_url(); ?>warehouse_part/get_rack_user",
          data: {
            wh_lokasi: wh_lokasi
          },
          dataType: "json",
          success: function(data) {
            $('#user').val(data.pic);
          },
          error: function(xhr, status, error) {
            console.error(xhr);
          }
        });
      }


      var table = $('#order-table').DataTable({
        processing: true,
        serverSide: false,
        paging: false, // Sembunyikan pagination (page)
        searching: true, // Sembunyikan search box
        lengthChange: false, // Opsional, sembunyikan dropdown show entries
        info: false, // Sembunyikan informasi "Showing x to y of z entries"
        ajax: {
          url: '<?php echo base_url(); ?>warehouse_part/get_list_transaksi_hapus',
          type: 'POST',
          data: function(d) {
            d.wh_lokasi = $('#wh_lokasi').val();
          },
          dataSrc: 'data'
        },

        columnDefs: [{
          orderable: false,
          targets: '_all' // Nonaktifkan sorting di semua kolom
        }],

        columns: [{
            data: 'no',
            className: 'text-center'
          },

          {
            data: 'nama_kolom',
            className: 'text-center'
          },
          {
            data: null,
            className: 'text-left',
            render: function(data, type, row) {
              return row.partid + ' <br> ' + row.partname;
            }
          },
          {
            data: 'qty',
            className: 'text-center'
          },
          {
            data: 'units',
            className: 'text-center'
          },
          {
            data: 'lihat',
            className: 'text-center'
          },
          // {
          //   data: 'hapus',
          //   className: 'text-center',

          // },
          // {
          //   data: 'pindah',
          //   className: 'text-center',

          // },
          {
            data: 'wh_lokasi',
            className: 'text-center'
          },
          {
            data: 'status_part',
            className: 'text-center'
          }
        ],

        // Ini posisi yang benar
        rowGroup: {
          dataSrc: 'nama_rack',
          className: 'text-left',
        }
      });



    });
  </script>

</body>

</html>