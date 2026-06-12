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

                              </h5>
                            </div>
                            <div class="row m-2 justify-content-center">
                              <div class="col-sm-2 text-center p-10  bg-primary">
                                <a href="<?php echo base_url() . 'warehouse_part/page_insert_data'; ?>" class="mb-2 text-white">INPUT DATA PART</a>
                              </div>
                              <div class="col-sm-2 text-center p-10  bg-warning">
                                <a href="<?php echo base_url() . 'warehouse_part/master_rack'; ?>" class="mb-2 text-white">MASTER RACK</a>
                              </div>
                              <div class="col-sm-2 text-center p-10  bg-success">
                                <a href="<?php echo base_url() . 'warehouse_part/index'; ?>" class="mb-2 text-white">WAREHOUSE PART</a>
                              </div>
                              <div class="col-sm-2 text-center p-10  bg-danger">
                                <a href="<?php echo base_url() . 'warehouse_part/delete'; ?>" class="mb-2 text-white">PART DELETE</a>
                              </div>
                            </div>
                            <hr class="border border-3" style="margin-top: 10px;">
                            <div class="card-header">
                              <span class="pull-right">
                                <button type="button" class="btn btn-info" onclick="openModal();">TAMBAH</button>
                              </span>
                            </div>
                            <div class="card-block">
                              <div class="dt-responsive table-responsive">
                                <table id="example" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="5%">No</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="10%">PIC</th>
                                      <th class="text-center">Rak</th>
                                      <th class="text-center" width="10%">WH Lokasi</th>
                                      <th class="text-center" width="8%">Isi Kolom</th>
                                      <th class="text-center" width="8%">Created Date</th>
                                      <th class="text-center" width="7%">Created By</th>
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
            <input type="hidden" value="" name="kode">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Rak</label>
              <div class="col-sm-10">
                <input type="text" id="Rak" name="Rak" maxlength="50" class="form-control text-uppercase" required="required" autocomplete="off" placeholder="Contoh: A, B, C dst.">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">WH Lokasi</label>
              <div class="col-sm-10">
                <select name="WHLokasi" id="WHLokasi" class="form-control">
                  <option value="" disabled selected>-- Pilih --</option>
                  <?php
                  foreach ($wh as $key => $value) {
                  ?>
                    <option value="<?php echo $value->LocationID; ?>">
                      <?php echo $value->LocationID; ?>
                    </option>
                  <?php
                  }
                  ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Isi Kolom</label>
              <div class="col-sm-10">
                <input type="number" id="Isi" name="Isi" maxlength="4" class="form-control" required="required" autocomplete="off" placeholder="Contoh: 1, 2, 3, 4 dst.">
                <span class="help-block"></span>
              </div>
            </div>
            <div id="Details">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Isi Rak</label>
                <div class="col-sm-10">
                  <ol class="list-group" id="IsiRak"></ol>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">User</label>
              <div class="col-sm-10">
                <input type="text" name="user" id="user" rows="3" class="form-control text-capitalize" readonly value="<?php echo strtoupper($this->session->userdata('user_name')); ?>">
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
      $("#Details").hide();
      save_method = 'add';
      $("#pass_div").show();
      $('#btnSave').text('Save');
      $('#RegisterValidation')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Rak'); // Set Title to Bootstrap modal title
    }

    function closeModal() {
      $('#RegisterValidation')[0].reset();
      $('#modal').modal('hide');
      $('.modal-title').text('Tambah Ext.');
    }

    //FUNCTION RESET
    function reset() {
      $('#RegisterValidation')[0].reset();
      $('.modal-title').text('Tambah Ext.');
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
        url: "<?php echo base_url(); ?>warehouse_part/rack_edit/" + id,
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
            $("#Details").show();
            $("#IsiRak").html(data.html_detail);

            $('[name="kode"]').val(data.data_header.id_rack);
            $('[name="Rak"]').val(data.data_header.nama_rack.trim());
            $('[name="WHLokasi"]').val(data.data_header.wh_lokasi);
            $('[name="Isi"]').val(data.data_header.jumlah_kolom);
            $('[name="user"]').val(data.data_header.pic);
            $('[name="Noted"]').val(data.data_header.noted);
            $('#modal').modal('show');
            $('.modal-title').text('Edit Rak');
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
            url: '<?php echo base_url(); ?>warehouse_part/rack_hapus/' + id,
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

      let Isi = parseFloat($('#Isi').val());
      if (Isi > 0) {
        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>warehouse_part/rack_add";
        } else {
          url = "<?php echo base_url(); ?>warehouse_part/rack_update";
        }

        var data_save = $('#RegisterValidation').serializeArray();
        $.ajax({
          url: url,
          type: "POST",
          data: data_save,
          dataType: "JSON",
          beforeSend: function() {
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true); //set button disable
          },
          success: function(data) {
            if (data.status == 'success') //if success close modal and reload ajax table
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
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled', false); //set button enable 
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled', false); //set button enable 
          }
        });
      } else {
        Swal.fire(
          'Oops',
          'Kolom isi/ baris harus lebih besar dari 0',
          'info',
        )
      }
    };

    $(document).ready(function() {
      $("#Details").hide();

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
        "serverSide": false,
        "order": [],
        "ajax": {
          "url": "<?php echo base_url(); ?>warehouse_part/rack_list",
          "type": "POST",
        },
        "aoColumns": [{
            "No": "No",
            "sClass": "text-right"
          },
          {
            "#": "#",
            "sClass": "text-center"
          },
          {
            "Status": "Status",
            "sClass": "text-center"
          },
          {
            "Rak": "Rak",
            "sClass": "text-left"
          },
          {
            "WH Lokasi": "WH Lokasi",
            "sClass": "text-left"
          },
          {
            "Isi": "Isi",
            "sClass": "text-center"
          },
          {
            "Created Date": "Created Date",
            "sClass": "text-center"
          },
          {
            "Created By": "Created By",
            "sClass": "text-center"
          }
        ],
        'columnDefs': [{
          'targets': 0,
          'searchable': false,
          'orderable': false,
          'className': 'dt-body-center',
          // 'render': function(data, type, full, meta) {
          //   return '<input type="checkbox" name="RakID[]" value="' + $('<div/>').text(data).html() + '">';
          // }
        }],
        'select': {
          'style': 'multi'
        },

      });

      $('#example-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var rows = table.rows({
          'search': 'applied'
        }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });

      $('#example tbody').on('change', 'input[type="checkbox"]', function() {
        if (!this.checked) {
          var el = $('#example-select-all').get(0);
          if (el && el.checked && ('indeterminate' in el)) {
            el.indeterminate = true;
          }
        }
      });

      $('#frm-example').on('submit', function(e) {
        var form = this;
        e.preventDefault();

        table.$('input[type="checkbox"]').each(function() {
          if (!$.contains(document, this)) {
            if (this.checked) {
              $(form).append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', this.name)
                .val(this.value)
              );
            }
          }
        });

        // Output form data to a console
        $('#example-console').text($(form).serialize());

        var data_array = table.$('input[type="checkbox"]').serializeArray();
        if (data_array.length > 0) {
          //let ArrayData = JSON.stringify(data_array);

          $.ajax({
            url: "<?php echo base_url(); ?>rak/pilih_rak",
            type: "POST",
            data: data_array,
            dataType: "JSON",
            beforeSend: function() {
              $('#BtnPilih').text('Processing...');
              $('#BtnPilih').attr('disabled', true); //set button disable
            },
            success: function(data) {
              console.log(data);
              window.open(data.Url, '_blank');
              $('#BtnPilih').text('PILIH RAK'); //change button text
              $('#BtnPilih').attr('disabled', false); //set button enable 
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert('Error adding / update data');
              $('#BtnPilih').text('PILIH RAK'); //change button text
              $('#BtnPilih').attr('disabled', false); //set button enable 
            }
          });

          // localStorage.setItem("data_scan_id", JSON.stringify(data_array));
        } else {
          alert("Silahkan pilih rak dahulu");

          return false;
        }

        // Prevent actual form submission
        e.preventDefault();
      });

      $("#Rak").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#WHLokasi").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#Isi").change(function() {
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