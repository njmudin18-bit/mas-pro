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
    <?php //$this->load->view('adminx/components/header_css_datatable_v2'); ?>
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
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center">SHIFT NAME</th>
                                      <th class="text-center" width="7%">AKTIVASI</th>
                                      <th class="text-center" width="7%">MONDAY START TIME</th>
                                      <th class="text-center" width="7%">MONDAY END TIME</th>
                                      <th class="text-center" width="7%">TUESDAY START TIME</th>
                                      <th class="text-center" width="7%">TUESDAY END TIME</th>
                                      <th class="text-center" width="7%">WEDNESDAY START TIME</th>
                                      <th class="text-center" width="7%">WEDNESDAY END TIME</th>
                                      <th class="text-center" width="7%">THURSDAY START TIME</th>
                                      <th class="text-center" width="7%">THURSDAY END TIME</th>
                                      <th class="text-center" width="7%">FRIDAY START TIME</th>
                                      <th class="text-center" width="7%">FRIDAY END TIME</th>
                                      <th class="text-center" width="7%">SATURDAY START TIME</th>
                                      <th class="text-center" width="7%">SATURDAY END TIME</th>
                                      <th class="text-center" width="7%">SUNDAY START TIME</th>
                                      <th class="text-center" width="7%">SUNDAY END TIME</th>
                                      <th class="text-center" width="15%">GRACE PRIOD</th>
                                      <th class="text-center" width="7%">SHIFT ALLOWANCE</th>
                                      <th class="text-center" width="7%">MEAL ALLOWANCE</th>
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
                <label class="col-sm-2 col-form-label">Shift Name</label>
                <div class="col-sm-10">
                  <input type="text" name="ShiftName" id="ShiftName" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Monday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="MondayStartTime" id="MondayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Monday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="MondayEndTime" name="MondayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tuesday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="TuesdayStartTime" id="TuesdayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tuesday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="TuesdayEndTime" name="TuesdayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Wednesday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="WednesdayStartTime" id="WednesdayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Wednesday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="WednesdayEndTime" name="WednesdayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Thursday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="ThursdayStartTime" id="ThursdayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Thursday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="ThursdayEndTime" name="ThursdayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Friday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="FridayStartTime" id="FridayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Friday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="FridayEndTime" name="FridayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Saturday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="SaturdayStartTime" id="SaturdayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Saturday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="SaturdayEndTime" name="SaturdayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sunday Start Time</label>
                <div class="col-sm-4">
                  <input type="time" name="SundayStartTime" id="SundayStartTime" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Sunday End Time</label>
                <div class="col-sm-4">
                  <input type="time" id="SundayEndTime" name="SundayEndTime" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Grace Period</label>
                <div class="col-sm-4">
                  <input type="text" id="GracePeriod" name="GracePeriod" maxlength="4" class="form-control" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Aktivasi</label>
                <div class="col-sm-4">
                  <select id="Aktivasi" name="Aktivasi" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak">Tidak</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Shift Allowance</label>
                <div class="col-sm-4">
                  <input type="text" id="ShiftAllowance" name="ShiftAllowance" maxlength="12" class="form-control" required="required" oninput="AllowDecimalAndComma(this)" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Meal Allowance</label>
                <div class="col-sm-4">
                  <select name="MealAllowance" id="MealAllowance" class="form-control">
                    <option selected="selected" value="">-- Pilih --</option>
                    <option value="Y">Ya</option>
                    <option value="N">Tidak</option>
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
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Shift'); // Set Title to Bootstrap modal title
      }

      function closeModal() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Shift');
      }

      //FUNCTION RESET
      function reset() {
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Shift');
      }

      //FUNCTION EDIT
      function edit(id) {

        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>shift/shift_edit/" + id,
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
              $('[name="kode"]').val(data.ShiftID);
              $('[name="ShiftName"]').val(data.ShiftName);
              // $('[name="MondayStartTime"]').val(data.MondayStartTime);
              // $('[name="MondayEndTime"]').val(data.MondayEndTime);
              // $('[name="TuesdayStartTime"]').val(data.TuesdayStartTime);
              // $('[name="TuesdayEndTime"]').val(data.TuesdayEndTime);
              // $('[name="WednesdayStartTime"]').val(data.WednesdayStartTime);
              // $('[name="WednesdayEndTime"]').val(data.WednesdayEndTime);
              // $('[name="ThursdayStartTime"]').val(data.ThursdayStartTime);
              // $('[name="ThursdayEndTime"]').val(data.ThursdayEndTime);
              // $('[name="FridayStartTime"]').val(data.FridayStartTime);
              // $('[name="FridayEndTime"]').val(data.FridayEndTime);
              // $('[name="SaturdayStartTime"]').val(data.SaturdayStartTime);
              // $('[name="SaturdayEndTime"]').val(data.SaturdayEndTime);
              // $('[name="SundayStartTime"]').val(data.SundayStartTime);
              // $('[name="SundayEndTime"]').val(data.SundayEndTime);

              const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

              days.forEach(day => {
                let start = data[day + 'StartTime'];
                let end   = data[day + 'EndTime'];
                
                $(`[name="${day}StartTime"]`).val(start === "00:00:00" ? "" : start);
                $(`[name="${day}EndTime"]`).val(end === "00:00:00" ? "" : end);
              });

              $('[name="EndTime"]').val(data.EndTime);
              $('[name="GracePeriod"]').val(data.GracePeriod);
              $('[name="MealAllowance"]').val(data.MealAllowance);
              
              let allowance = data.ShiftAllowance;
              if (allowance === null || allowance === undefined || allowance === "") {
                $('[name="ShiftAllowance"]').val(""); // kosongkan input kalau null
              } else {
                let formatted = allowance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                $('[name="ShiftAllowance"]').val(formatted);
              }

              $('[name="Aktivasi"]').val(data.Aktivasi);
              $('#modal').modal('show');
              $('.modal-title').text('Edit Shift');
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
              url: '<?php echo base_url(); ?>shift/shift_deleted/' + id,
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
            "url": "<?php echo base_url(); ?>shift/shift_list",
            "type": "POST",
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "SHIFT NAME": "SHIFT NAME" , "sClass": "text-left", "width": "50px" },
            { "AKTIVASI": "AKTIVASI" , "sClass": "text-center", "width": "80px" },
            { "MONDAY START TIME": "MONDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "MONDAY END TIME": "MONDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "TUESDAY START TIME": "TUESDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "TUESDAY END TIME": "TUESDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "WEDNESDAY START TIME": "WEDNESDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "WEDNESDAY END TIME": "WEDNESDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "THURSDAY START TIME": "THURSDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "THURSDAY END TIME": "THURSDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "FRIDAY START TIME": "FRIDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "FRIDAY END TIME": "FRIDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "SATURDAY START TIME": "SATURDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "SATURDAY END TIME": "SATURDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "SUNDAY START TIME": "SUNDAY START TIME" , "sClass": "text-center", "width": "80px" },
            { "SUNDAY END TIME": "SUNDAY END TIME" , "sClass": "text-center", "width": "80px" },
            { "GRACE PRIOD": "GRACE PRIOD" , "sClass": "text-center", "width": "80px" },
            { "SHIFT ALLOWANCE": "SHIFT ALLOWANCE" , "sClass": "text-right", "width": "80px" },
            { "MEAL ALLOWANCE": "MEAL ALLOWANCE" , "sClass": "text-center", "width": "80px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" }
          ],
          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        $("#ShiftName").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#MondayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#MondayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#TuesdayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#TuesdayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#WednesdayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#WednesdayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ThursdayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ThursdayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#FridayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#FridayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#SaturdayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#SaturdayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#SundayStartTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#SundayEndTime").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#GracePeriod").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#ShiftAllowance").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#MealAllowance").change(function() {
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