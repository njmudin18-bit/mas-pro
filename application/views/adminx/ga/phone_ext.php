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
                                <button class="btn btn-info" onclick="openModal();">TAMBAH</button>
                              </span>
                            </h5>
                          </div>
                          <div class="card-block">
                            <div class="dt-responsive table-responsive">
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead class="bg-primary text-center">
                                  <tr>
                                    <th class="text-center" width="8%">No</th>
                                    <th class="text-center" width="5%">#</th>
                                    <th class="text-center">Department.</th>
                                    <th class="text-center" width="7%">Aktivasi</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center" width="10%">Ext. Nomor</th>
                                  </tr>
                                </thead>
                                <tbody>

                                </tbody>
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
              <label class="col-sm-2 col-form-label">Custom</label>
              <div class="col-sm-10">
                <select id="custom" name="custom" class="form-control" onchange="custom_value(this)">
                  <option disabled="disabled">-- Pilih --</option>
                  <option selected="selected" value="M" selected="selected">MAS</option>
                  <option value="C">Custom</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div id="show_mas">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Department</label>
                <div class="col-sm-10">
                  <select id="dept_id" name="dept_id" class="form-control" required="required" onchange="get_karyawan(this);">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <?php
                    foreach ($department_att as $key => $value) {
                    ?>
                      <option value="<?php echo $value->DEPTID; ?>"><?php echo $value->DEPTNAME; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Karyawan</label>
                <div class="col-sm-10">
                  <select id="nip" name="nip" class="form-control" required="required">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>

                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div id="show_custom">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Department</label>
                <div class="col-sm-10">
                  <input type="text" name="dept_name" id="dept_name" class="form-control">
                  <input type="hidden" name="dept_id_custom" id="dept_id_custom" value="00">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Karyawan</label>
                <div class="col-sm-10">
                  <input type="text" name="nama_pegawai" id="nama_pegawai" class="form-control">
                  <input type="hidden" name="nip_custom" id="nip_custom" value="00">
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Ext. Nomor</label>
              <div class="col-sm-10">
                <input type="text" id="ext_no" name="ext_no" maxlength="4" class="form-control" required="required" autocomplete="off">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Aktivasi</label>
              <div class="col-sm-10">
                <select id="aktivasi" name="aktivasi" class="form-control">
                  <option selected="selected" disabled="disabled">-- Pilih --</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Tidak">Tidak</option>
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
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script>
    var save_method;
    var url;

    function reset_all() {
      //alert('aaaa');
      $("#show_custom").hide();
      $('[name="custom"]').val('M');
      $('#modal').modal('hide');
    }

    function custom_value(data) {
      console.log(data.value);
      if (data.value == 'M') {
        $("#show_mas").show();
        $("#show_custom").hide();
      } else {
        $("#show_mas").hide();
        $("#show_custom").show();
      }
    }

    //FUNGSI GET NAMA KARYAWAN PER DEPT
    function get_karyawan(id) {
      //alert(sel.value);
      $.ajax({
        url: "<?php echo base_url(); ?>users/get_karyawan_dept",
        method: "POST",
        data: {
          id: id.value
        },
        async: false,
        dataType: 'json',
        success: function(data) {
          var html = '';
          if (data.length > 0) {
            var i;
            for (i = 0; i < data.length; i++) {
              html += '<option value="' + data[i].SSN + '">' + data[i].NAME + '</option>';
            }
          } else {
            html += '<option disabled="disables"> data not found </option>';
          }

          $('#nip').html(html);
        }
      });
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
      $('.modal-title').text('Tambah Ext.'); // Set Title to Bootstrap modal title
      var html = '';
      html += '<option disabled="disabled"> NO DATA FOUND </option>';

      $('#nip').html(html);

      const isi = $('#custom').val();
      if (isi == 'M') {
        $("#show_mas").show();
        $("#show_custom").hide();
      } else {
        $("#show_mas").hide();
        $("#show_custom").show();
      }
      $(".form-group").parent().find('div').removeClass("has-error");
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
        url: "<?php echo base_url(); ?>phone/phone_edit/" + id,
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
            var custom_val = '';
            if (data.dept_id == null) {
              $("#show_mas").hide();
              $("#show_custom").show();

              custom_val = 'C';
              $('[name="custom"]').val(custom_val);
              $('[name="dept_name"]').val(data.dept_name);
              $('[name="nama_pegawai"]').val(data.nama_pegawai);
            } else {
              $("#show_mas").show();
              $("#show_custom").hide();

              custom_val = 'M';
              $('[name="custom"]').val(custom_val);
              $('[name="dept_id"]').val(data.dept_id);
              var html = '';
              html += '<option value="' + data.nip + '">' + data.nama_pegawai + '</option>';

              $('#nip').html(html);
            }

            $('[name="kode"]').val(data.id);
            $('[name="ext_no"]').val(data.ext_no);
            $('[name="aktivasi"]').val(data.aktivasi);
            $('#modal').modal('show');
            $('.modal-title').text('Edit Phone Ext.');
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
            url: '<?php echo base_url(); ?>phone/phone_deleted/' + id,
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
        $("#pass_div").show();
        url = "<?php echo base_url(); ?>phone/phone_add";
      } else {
        $("#pass_div").hide();
        url = "<?php echo base_url(); ?>phone/phone_update";
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
    };

    $(document).ready(function() {
      $("#show_custom").hide();

      //console

      table = $('#order-table').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: true,
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search records",
        },
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": "<?php echo base_url(); ?>phone/phone_list",
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
            "DEPT": "DEPT",
            "sClass": "text-left"
          },
          {
            "AKTIF": "AKTIF",
            "sClass": "text-center"
          },
          {
            "Nama": "Nama",
            "sClass": "text-left"
          },
          {
            "Ext. Nomor": "Ext. Nomor",
            "sClass": "text-center"
          }
        ],

        //Set column definition initialisation properties.
        "columnDefs": [{
          "targets": [0], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });

      $("#dept_id").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#nip").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#ext_no").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });

      $("#aktivasi").change(function() {
        $(this).parent().removeClass('has-error');
        $(this).next().empty();
      });
    });
  </script>
</body>

</html>