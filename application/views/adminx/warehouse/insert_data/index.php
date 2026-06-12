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
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Tambahkan link CDN Flatpickr -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    /* Ubah background Select2 single selection */
    .select2-container .select2-selection--single {
      background-color: #ccc !important;
    }

    /* Ubah background select saat terbuka */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      background-color: #ccc !important;
    }

    /* Ubah background dropdown list */
    .select2-container--default .select2-results {
      background-color: #ccc !important;
      /* Warna latar belakang dropdown */
    }

    /* Ubah warna teks di dropdown */
    .select2-container--default .select2-results__option {
      color: #000;
      /* Warna teks item dropdown */
    }

    /* Ubah warna item dropdown saat dihover */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #bbb !important;
      /* Warna latar belakang item yang dihover */
      color: #000 !important;
      /* Warna teks item yang dihover */
    }
  </style>
  <?php $this->load->view('adminx/components/header_css_datatable'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" />
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
                          <?php $this->load->view('adminx/warehouse/header_part/header_part'); ?>
                          <hr class="border border-3" style="margin-top: 10px;">
                          <div class="col-sm-12 mt-2">
                            <form id="trans_rack">
                              <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Nama rack
                                </label>
                                <div class="col-sm-2">
                                  <select name="nama_rack" id="nama_rack"
                                    class="form-control">
                                    <option value="" disabled selected>-- Pilih --
                                    </option>
                                    <?php
                                    foreach ($rack as $key => $value) {
                                    ?>
                                      <option value="<?php echo $value->id_rack; ?>">
                                        <?php echo  $value->nama_rack; ?>
                                      </option>
                                    <?php
                                    }
                                    ?>
                                  </select>
                                  <span class="help-block"></span>
                                </div>
                                <label class="col-sm-2 col-form-label">Nama Kolom
                                </label>
                                <div class="col-sm-2">
                                  <select name="nama_kolom" id="nama_kolom"
                                    class="form-control">
                                    <option value="" disabled selected>-- Pilih --
                                    </option>
                                  </select>
                                  <span class="help-block"></span>
                                </div>

                                <label class="col-sm-2 col-form-label">WH Lokasi</label>
                                <div class="col-sm-2">
                                  <input type="text" name="wh_lokasi" id="wh_lokasi"
                                    class="form-control" readonly>
                                  <span class="help-block"></span>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Part Name</label>
                                <div class="col-sm-10">
                                  <select id="PartID" name="PartID"
                                    class="form-control select-ajax">
                                    <option selected="selected" disabled="disabled">
                                      -- Pilih --</option>
                                  </select>
                                  <span class="help-block"></span>
                                </div>
                              </div>

                              <div class="form-group row">


                                <label class="col-sm-2 col-form-label">Jumlah</label>
                                <div class="col-sm-2">
                                  <input type="text" name="qty" id="qty"
                                    class="form-control autonumeric" data-a-sep="."
                                    data-a-dec=",">
                                  <span class="help-block"></span>
                                </div>



                                <label class="col-sm-2 col-form-label">Units</label>
                                <div class="col-sm-2">
                                  <input type="text" name="units" id="units"
                                    class="form-control" readonly>
                                  <span class="help-block"></span>
                                </div>

                                <label class="col-sm-2 col-form-label">Type
                                  Trans</label>
                                <div class="col-sm-2">
                                  <select name="type_trans" id="type_trans"
                                    class="form-control">
                                    <option value="" disabled selected>-- Pilih --
                                    </option>
                                    <option value="IN">IN</option>
                                    <option value="OUT">OUT</option>
                                  </select> <span class="help-block"></span>
                                </div>
                              </div>

                              <div class="form-group row">


                                <label class="col-sm-2 col-form-label">Noted</label>
                                <div class="col-sm-2">
                                  <textarea name="noted" id="noted"
                                    class="form-control" rows="2"
                                    style="resize:vertical;"></textarea>
                                  <span class="help-block"></span>
                                </div>

                                <label class="col-sm-2 col-form-label">Tanggal
                                  FIFO</label>
                                <div class="col-sm-2">
                                  <input type="date" id="tgl_fifo" name="tgl_fifo"
                                    class="form-control" placeholder="dd-mm-yyyy">
                                  <span class="help-block"></span>
                                </div>

                                <label class="col-sm-2 col-form-label">Fifo</label>
                                <div class="col-sm-2">
                                  <select id="pilih_fifo" name="pilih_fifo"
                                    class="form-control"></select>
                                  <span class="help-block"></span>
                                </div>


                              </div>

                              <div class="form-group row justify-content-right">
                                <div class="col-sm-12 text-left">
                                  <button type="button" id="btnSimpan"
                                    class="btn btn-primary">Simpan</button>
                                </div>
                              </div>

                            </form>


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

  <div id="loading-screen" class="loading">Loading&#8230;</div>


  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

  <script src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
  <script src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>
  <script>
    flatpickr("#tgl_fifo", {
      dateFormat: "d-m-Y", // Format Indonesia
      onChange: function(selectedDates, dateStr, instance) {
        const dateObj = selectedDates[0];
        if (!dateObj) return;

        const monthNames = [
          "januari", "februari", "maret", "april", "mei", "juni",
          "juli", "agustus", "september", "oktober", "november", "desember"
        ];

        const namaBulan = monthNames[dateObj.getMonth()];

        $('#pilih_fifo').val(namaBulan).trigger(
          'change'); // asalkan <option value="juli">, bukan "Juli"
      }
    });
  </script>

  <script>
    $(document).ready(function() {

      const iconMap = {
        kotak: 'fa-solid fa-square', // Mengubah dari 'square' menjadi 'kotak'
        segitiga: 'fa-solid fa-play fa-rotate-270' // Mengubah dari 'triangle' menjadi 'segitiga'
      };

      const $select = $('#pilih_fifo');

      // Ambil data dari controller via AJAX
      $.ajax({
        url: '<?= base_url("warehouse_part/get_ms_fifo"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(dataBulan) {
          console.log(dataBulan);
          dataBulan.forEach(item => {
            // Sesuaikan penggunaan 'bulan' untuk nama bulan
            const iconClass = iconMap[item.bentuk] ||
              'fa-circle'; // Pastikan menggunakan 'bentuk' dan 'warna'

            // Menambahkan opsi ke dropdown dengan data yang diterima
            $select.append(`
                <option value="${item.bulan.toLowerCase()}"
                        data-icon="${iconClass}" 
                        data-color="${item.warna}">
                  ${item.bulan}
                </option>
            `);
          });

          // Inisialisasi Select2 setelah data dimasukkan
          $select.select2({
            templateResult: formatOption,
            templateSelection: formatOption
          });
        },
        error: function() {
          alert('Gagal mengambil data FIFO');
        }
      });

      // Fungsi format tampilan select
      function formatOption(state) {
        if (!state.id) return state.text;

        const icon = $(state.element).data('icon');
        let color = $(state.element).data('color');



        return $(
          `<span><i class="${icon}" style="color:${color}; margin-right: 8px;"></i> ${state.text}</span>`
        );
      }


      $('#btnSimpan').on('click', function() {
        // Ambil semua data form sebagai object
        var formData = {
          nama_rack: $('#nama_rack').val(),
          nama_kolom: $('#nama_kolom').val(),
          wh_lokasi: $('#wh_lokasi').val(),
          PartID: $('#PartID').val(),
          qty: $('#qty').val().replace(/\./g, '').replace(',', '.'),
          units: $('#units').val(),
          noted: $('#noted').val(),
          fifo: $('#pilih_fifo').val(),
          type_trans: $('#type_trans').val(),
          tgl_fifo: $('#tgl_fifo').val()
        };


        $.ajax({
          url: "<?php echo base_url('warehouse_part/insert_transaksi'); ?>",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire('Sukses', response.message, 'success');
              // reset form jika perlu
              $('#trans_rack')[0].reset();
              $('#units').val('');
              $('#PartID').val(null).trigger('change');
            } else {
              Swal.fire('Gagal', response.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
          }
        });

      });

      $('.autonumeric').autoNumeric('init');

      $('#loading-screen').hide();

      $('#nama_rack').change(function() {
        var id_rack = $(this).val();
        $('#nama_kolom').val('');
        $('#wh_lokasi').val('');
        if (id_rack) {
          $.ajax({
            url: "<?php echo base_url('warehouse_part/get_rack_detail/'); ?>" + id_rack,
            type: "GET",
            dataType: "json",
            success: function(data) {
              var html =
                '<option value="" disabled selected>-- Pilih --</option>';
              $.each(data.kolom, function(i, v) {
                html += '<option value="' + v.id_kolom + '">' + v
                  .nama_kolom + '</option>';
              });
              $('#nama_kolom').html(html);
              // Isi WH Lokasi
              $('#wh_lokasi').val(data.wh_lokasi);
            },
            error: function() {
              $('#nama_kolom').val('');
              $('#wh_lokasi').val('');
            }
          });
        }
      });

      $('.select-ajax').select2({
        placeholder: 'Masukan Part Name atau Part ID',
        minimumInputLength: 3,
        ajax: {
          url: '<?php echo base_url(); ?>warehouse_part/cari_partname',
          type: 'GET',
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              term: params.term
            };
          },
          processResults: function(data) {
            return {
              results: data
            };
          },
          cache: true // Enable caching to improve performance
        }
      }).on('select2:select', function(e) {
        var selectedData = e.params.data;
        let Unit = selectedData.unit;
        $("#units").val(Unit);
      });


    });
  </script>

</body>

</html>