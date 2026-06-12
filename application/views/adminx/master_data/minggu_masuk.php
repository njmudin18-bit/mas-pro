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
                              <table id="table_libur" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead class="bg-primary text-center">
                                  <tr>
                                    <th class="text-center" width="2%">NO</th>
                                    <th class="text-center" width="3%">#</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center" width="10%">IS ACTIVE</th>
                                    <th class="text-center" width="10%">NOTED</th>
                                    <th class="text-center" width="5%">CREATED DATE</th>
                                    <th class="text-center" width="8%">CREATED BY</th>
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
          <button type="button" class="close" aria-label="Close" onclick="reset_all()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="RegisterValidation">
            <input type="hidden" value="" name="kode">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Tanggal</label>
              <div class="col-sm-4">
                <input type="date" name="Tanggal" id="Tanggal" class="form-control" required="required" autocomplete="off">
                <span class="help-block"></span>
              </div>
              <label class="col-sm-2 col-form-label">Status</label>
              <div class="col-sm-4">
                <select name="IsActive" id="IsActive" class="form-control">
                  <option value="" selected readonly>-- Pilih --</option>
                  <option value="A">Aktif</option>
                  <option value="N">Non Aktif</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Noted</label>
              <div class="col-sm-10">
                <input type="text" name="Noted" id="Noted" class="form-control" required="required" autocomplete="off" placeholder="Contoh: Keterangan Minggu Masuk">
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
      $('#btnSave').text('Save');
      $('#RegisterValidation')[0].reset(); // reset form on modals
      $('.form-group .has-error').removeClass('has-error');
      $('.help-block').empty(); // clear error string
      $('#modal').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Minggu Masuk'); // Set Title to Bootstrap modal title
    }

    function closeModal() {
      $('#RegisterValidation')[0].reset();
      $('#modal').modal('hide');
      $('.modal-title').text('Tambah Minggu Masuk');
    }

    //FUNCTION RESET
    function reset() {
      $('#RegisterValidation')[0].reset();
      $('.modal-title').text('Tambah Minggu Masuk');
    }

    //FUNCTION EDIT
    function edit(id) {
      save_method = 'update';
      $('#RegisterValidation')[0].reset();
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();

      // Ajax Load data dari server
      $.ajax({
        url: "<?php echo base_url(); ?>minggu_masuk/minggu_masuk_edit/" + id,
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
            $('[name="kode"]').val(data.Id);
            $('[name="Tanggal"]').val(data.Tanggal);
            $('[name="Noted"]').val(data.Noted);
            $('[name="IsActive"]').val(data.IsActive);

            $('#modal').modal('show');
            $('.modal-title').text('Edit Minggu Masuk');
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
            url: '<?php echo base_url(); ?>minggu_masuk/minggu_masuk_deleted/' + id,
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


    //VALIDATION AND ADD USER
    function save() {
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>minggu_masuk/minggu_masuk_add";
        } else {
          url = "<?php echo base_url(); ?>minggu_masuk/minggu_masuk_update";
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
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          "url": "<?php echo base_url(); ?>minggu_masuk/minggu_masuk_list",
          "type": "POST"
        },
        "aoColumns": [
          { "NO": "NO" , "sClass": "text-right", "width": "20px"},
          { "#": "#" , "sClass": "text-center", "width": "20px"},
          { "STATUS": "STATUS" , "sClass": "text-center", "width": "20px" },
          { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" },
          { "NOTED": "NOTED" , "sClass": "text-left", "width": "100px" },
          { "CREATED DATE": "CREATED DATE" , "sClass": "text-center", "width": "50px" },
          { "CREATED BY": "CREATED BY" , "sClass": "text-center", "width": "50px" }
        ],
        "columnDefs": [
          {
            "targets": [0],
            "orderable": false,
            className: 'text-right'
          }
        ]
      });

      $("#Tanggal, #Noted, #IsActive").change(function(){
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $('#Noted').on('input', function() {
        var input = $(this);
        var val = input.val();
        
        if (val.length > 0) {
            // Ambil huruf pertama, ubah ke kapital, lalu gabung dengan sisa kalimat
            var capitalized = val.charAt(0).toUpperCase() + val.slice(1);
            input.val(capitalized);
        }
    });
    });
  </script>
</body>

</html>