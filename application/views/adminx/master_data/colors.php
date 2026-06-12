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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
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
                          <form id="frm-example" action="" method="POST">
                            <div class="card">
                              <div class="card-header text-center">
                                <h5>
                                  <?php echo strtoupper($nama_halaman); ?>
                                  <span class="pull-right">
                                    <button type="button" class="btn btn-info" onclick="openModal();">TAMBAH</button>
                                  </span>
                                </h5>
                              </div>
                              <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                  <table id="example" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                    <thead class="bg-primary text-center">
                                      <tr class="bg-primary text-white">
                                        <th class="text-center" width="7%">No</th>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="15%">Nomor Bulan</th>
                                        <th class="text-center" width="15%">Nama Bulan</th>
                                        <th class="text-center">Bentuk & Warna</th>
                                        <th class="text-center" width="5%">Status</th>
                                      </tr>
                                    </thead>
                                    <tbody></tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </form>
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
          <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <input type="hidden" value="" id="kode" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Bulan</label>
                <div class="col-sm-10">
                  <select name="Months" id="Months" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Warna</label>
                <div class="col-sm-10">
                <input type="color" id="Colors" name="Colors" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Bentuk</label>
                <div class="col-sm-10">
                  <select name="Shapes" id="Shapes" class="form-control">
                    <option selected disabled>-- Pilih --</option>
                    <option value="Kotak">Kotak</option>
                    <option value="Segitiga">Segitiga</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Aktivasi</label>
                <div class="col-sm-10">
                  <select id="Aktivasi" name="Aktivasi" class="form-control" required="required">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="AKTIF">AKTIF</option>
                    <option value="TIDAK">TIDAK</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script>
      var save_method;
      var url;

      function reset_all() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Rak');
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Warna FIFO Card'); // Set Title to Bootstrap modal title
      }

      function closeModal() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Warna FIFO Card');
      }

      //FUNCTION RESET
      function reset() {
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Warna FIFO Card');
      }

      //FUNCTION EDIT
      function edit(id) {

        save_method = 'update';
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>colors/colors_edit/" + id,
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
              $('[name="Months"]').val(data.MonthNumber);
              $('[name="Colors"]').val(data.Colors);
              $('[name="Shapes"]').val(data.Shapes);
              $('[name="Aktivasi"]').val(data.Aktivasi);
              $('#modal').modal('show');
              $('.modal-title').text('Edit Warna FIFO Card');
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
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>colors/colors_deleted/',
              type: 'POST',
              data: {
                id_temp: id
              },
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
                  Swal.fire({
                    title: "Sukses",
                    text: result.message,
                    icon: "success"
                  });
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
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>colors/colors_add";
        } else {
          url = "<?php echo base_url(); ?>colors/colors_update";
        }

        let data_save = {
          "kode": $("#kode").val(),
          "MonthNumber": $("#Months option:selected").val(),
          "MonthName": $("#Months option:selected").text(),
          "Colors": $("#Colors").val(),
          "Shapes": $("#Shapes").val(),
          "Aktivasi": $("#Aktivasi").val()
        };

        $.ajax({
          url: url,
          type: "POST",
          data: data_save,
          dataType: "JSON",
          beforeSend: function() {
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
          },
          success: function(data) {
            if (data.status == 'success')
            {
              $('#modal').modal('hide');
              reload_table();
            } else if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else if (data.status == 'error') {
              Swal.fire(
                'Oops',
                data.message,
                'info',
              )
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }
            $('#btnSave').text('Save');
            $('#btnSave').attr('disabled', false);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled', false); //set button enable 
          }
        });
      };

      $(document).ready(function() {
        table = $('#example').DataTable({
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
            "url": "<?php echo base_url(); ?>colors/colors_list",
            "type": "POST",
          },
          "aoColumns": [
            { "No": "No" , "sClass": "text-right"},
            { "#": "#" , "sClass": "text-center" },
            { "Nomor Bulan": "Nomor Bulan" , "sClass": "text-center" },
            { "Nama Bulan": "Nama Bulan" , "sClass": "text-start" },
            { "Bentuk & Warna": "Bentuk & Warna" , "sClass": "text-center" },
            { "Status": "Status" , "sClass": "text-center" }
          ],
          "columnDefs": [
            { 
              "targets": [ 1 ],
              "orderable": false,
              className: 'table-action'
            }
          ],
          'select': {
            'style': 'multi'
          },
          'order': [
            [1, 'asc']
          ]
        });

        $("#Months").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Colors").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Shapes").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Aktivasi").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>