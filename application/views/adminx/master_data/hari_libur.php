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
  <?php //$this->load->view('adminx/components/header_css_datatable_v2'); 
  ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
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
                              <span class="pull-right">
                                <button class="btn btn-info"
                                  onclick="openModal();">TAMBAH</button>
                              </span>
                            </h5>
                          </div>
                          <div class="card-block">
                            <div class="dt-responsive table-responsive">
                              <table id="table_libur" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                <thead class="bg-primary text-center">
                                  <tr>
                                    <th class="text-center" width="4%">NO</th>
                                    <th class="text-center" width="5%">#</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center" width="17%">DESKRIPSI</th>
                                    <th class="text-center" width="5%">TYPE</th>
                                    <th class="text-center" width="5%">NATIONAL DAYS</th>
                                    <th class="text-center" width="20%">KETERANGAN</th>
                                    <th class="text-center" width="10%">CREATE DATE</th>
                                    <th class="text-center" width="10%">CREATE BY</th>
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
          <h4 class="modal-title">Form Jadwal Libur / Kegiatan</h4>
          <button type="button" class="close" aria-label="Close" onclick="reset_all()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formLibur">
            <!-- Tanggal -->
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="hidden" name="HolidayID" id="HolidayID">
                <input type="date" name="tanggal" id="tanggal" class="form-control" required
                  autocomplete="off">
                <span class="help-block text-danger" id="error-tanggal"></span>
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Deskripsi <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="text" name="deskripsi" id="deskripsi" class="form-control"
                  placeholder="Contoh: Hari Kemerdekaan, Cuti Bersama" autocomplete="off">
                <span class="help-block text-muted">Opsional</span>
              </div>
            </div>

            <!-- Type -->
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Type </label>
              <div class="col-sm-9">
                <input type="text" name="type" id="type" class="form-control"
                  placeholder="Contoh: Libur Nasional, Event Internal" autocomplete="off">
                <span class="help-block text-muted">Opsional</span>
              </div>
            </div>

            <!-- National Days -->
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">National Day? <span
                  class="text-danger">*</span></label>
              <div class="col-sm-9">
                <select name="nationalDays" id="nationalDays" class="form-control">
                  <option value="" selected disabled>-- Pilih --</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
                <span class="help-block text-muted">Apakah ini hari libur nasional?</span>
              </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Keterangan</label>
              <div class="col-sm-9">
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                  placeholder="Catatan tambahan..." autocomplete="off"></textarea>
                <span class="help-block text-muted">Opsional</span>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-outline-danger waves-effect" onclick="reset_all()">
            Close
          </button>
          <button type="button" id="btnSave" class="btn btn-primary waves-effect waves-light"
            onclick="save()">
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>



  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js">
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js">
  </script>
  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script>
    var save_method;
    var url;

    function reset_all() {
      $("#show_custom").hide();
      $('#modal').modal('hide');
    }

    //FUNCTION OPEN MODAL CABANG
    function openModal() {
      save_method = 'add';
      $("#pass_div").show();
      $('#btnSave').text('Save');
      $('#formLibur')[0].reset(); // reset form on modals
      $('.form-group .has-error').removeClass('has-error');
      $('.help-block').empty(); // clear error string
      $('#modal').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Hari Libur'); // Set Title to Bootstrap modal title
    }

    function closeModal() {
      $('#formLibur')[0].reset();
      $('#modal').modal('hide');
      $('.modal-title').text('Tambah Hari Libur');
    }

    //FUNCTION RESET
    function reset() {
      $('#formLibur')[0].reset();
      $('.modal-title').text('Tambah Hari Libur');
    }

    //FUNCTION EDIT
    function edit(id) {
      save_method = 'update';
      $('#formLibur')[0].reset();
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();

      // Ajax Load data dari server
      $.ajax({
        url: "<?php echo base_url(); ?>hari_libur/holidays_edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'forbidden') {
            Swal.fire(
              'FORBIDDEN',
              'Access Denied',
              'info'
            )
          } else {
            // Isi field form sesuai payload Holiday
            $('[name="HolidayID"]').val(data.HolidayID);
            $('[name="tanggal"]').val(data.HolidayDate);
            $('[name="deskripsi"]').val(data.HolidayName);
            $('[name="type"]').val(data.HolidayType);
            $('[name="nationalDays"]').val(data.IsNational == 1 ? 'Yes' : 'No');
            $('[name="keterangan"]').val(data.Notes);

            $('#modal').modal('show');
            $('.modal-title').text('Edit Hari Libur');
            $('#btnSave').text('Update');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          Swal.fire('Error', 'Gagal mengambil data dari server', 'error');
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
            url: '<?php echo base_url(); ?>hari_libur/holidays_deleted/' + id,
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
                Swal.fire(
                  'Sukses!',
                  'Anda sukses menghapus data',
                  'success'
                )
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

    // =========================
    // SIMPAN HARI LIBUR
    // =========================
    function save() {
      $('#btnSave').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

      const url = $('#HolidayID').val() ?
        "<?php echo base_url('hari_libur/holidays_update'); ?>" :
        "<?php echo base_url('hari_libur/holidays_add'); ?>";

      $.ajax({
        url: url,
        method: "POST",
        data: $('#formLibur').serialize(),
        dataType: "JSON",
        success: function(data) {
          if (data.status) {
            $('#modal').modal('hide');
            Swal.fire('Berhasil!', data.msg, 'success');
            reload_table();
          } else {
            for (let i = 0; i < data.inputerror.length; i++) {
              const input = $('[name="' + data.inputerror[i] + '"]');
              input.closest('.form-group').addClass('has-error');
              input.next('.help-block').text(data.error_string[i]);
            }
          }
          $('#btnSave').prop('disabled', false).text('Simpan');
        },
        error: function() {
          Swal.fire('Error', 'Gagal menyimpan', 'error');
          $('#btnSave').prop('disabled', false).text('Simpan');
        }
      });
    }


    //VALIDATION AND ADD USER
    function save_old() {
      $("#btnSave").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
      $('#btnSave').attr('disabled', true); //set button disable 
      var url;

      if (save_method == 'add') {
        $("#pass_div").show();
        url = "<?php echo base_url(); ?>shift/shift_add";
      } else {
        $("#pass_div").hide();
        url = "<?php echo base_url(); ?>shift/shift_update";
      }

      var data_save = $('#RegisterValidation').serializeArray();
      var pegawai_name = $('#nip option:selected').text();
      var dept_name = $('#dept_id option:selected').text();
      var custom = $('#custom option:selected').val();
      //console.log(custom);
      //push to array serialize

      if (custom == 'M') {
        data_save.push({
          name: "dept_name",
          value: dept_name
        });
        data_save.push({
          name: "nama_pegawai",
          value: pegawai_name
        });
      };

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
          } else if (data.status == 'forbidden') {
            Swal.fire(
              'FORBIDDEN',
              'Access Denied',
              'info',
            )
          } else {
            for (var i = 0; i < data.inputerror.length; i++) {
              console.log(data.inputerror[i]);
              $('[name="' + data.inputerror[i] + '"]').parent().addClass(
                'has-error'
              ); //select parent twice to select div form-group class and add has-error class
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[
                i]); //select span help-block class set text error string
            }
          }
          $('#btnSave').text('Save'); //change button text
          $('#btnSave').attr('disabled', false); //set button enable 
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error adding / update data');
          $('#btnSave').text('Save'); //change button text
          $('#btnSave').attr('disabled', false); //set button enable 
        }
      });
    };

    $(document).ready(function() {
      $("#show_custom").hide();

      //console

      table = $('#table_libur').DataTable({
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
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": "<?php echo base_url(); ?>hari_libur/holidays_list",
          "type": "POST",
        },
        "aoColumns": [{
            "data": "No",
            "className": "text-center"
          },
          {
            "data": "Button"
          },
          {
            "data": "HolidayDate",
            "className": "text-center"
          },
          {
            "data": "HolidayName"
          },
          {
            "data": "HolidayType",
            "className": "text-center"
          },
          {
            "data": "IsNational",
            "render": d => d == 1 ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>',
            "className": "text-center"
          },
          {
            "data": "Notes"
          },
          {
            "data": "CreatedDate"
          },
          {
            "data": "CreatedBy"
          },

        ],
        //Set column definition initialisation properties.
        "columnDefs": [{
          "targets": [0], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });

      $("#tanggal").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#deskripsi").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      // $("#type").change(function() {
      //   $(this).parent().removeClass('has-error');
      //   $(this).next().empty();
      // });

      $("#nationalDays").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      // $("#keterangan").change(function() {
      //   $(this).parent().removeClass('has-error');
      //   $(this).next().empty();
      // });

    });
  </script>
</body>

</html>