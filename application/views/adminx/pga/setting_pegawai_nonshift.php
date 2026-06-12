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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/dual-listbox/src/bootstrap-duallistbox.css">
    <style>
      .bootstrap-duallistbox-container .moveall, .bootstrap-duallistbox-container .remove
      {
        width: 38% !important;
      }

      .pointer {
        cursor: pointer;
      }
    </style>
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
                                  <select name="DeptShow" id="DeptShow" class="form-control">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option value="" selected>ALL DEPARTEMEN</option>
                                    
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>">
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-4 col-sm-12 m-t-3 text-right">
                                  <button id="btnTambah" type="button" class="btn btn-success btn-full-mobile" onclick="openModal();">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead id="thead-shift" class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center">NO</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">NIP</th>
                                      <th class="text-center">NAME</th>
                                      <th class="text-center">DEPARTEMEN</th>
                                      <th class="text-center">GENDER</th>
                                      <th class="text-center">SHIFT OPERATION</th>
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
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="addForm">
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Shift Operation</label>
                <div class="col-sm-4 mb-1 form-error">
                  <select name="ShiftOperation" id="ShiftOperation" class="form-control">
                    <option value="" selected disabled>-- Pilih --</option>
                    <?php foreach ($shiftList as $shift): ?>
                      <option value="<?php echo $shift->ShiftID; ?>">
                        <?php echo strtoupper($shift->ShiftName)." (".$shift->JamIn." - ".$shift->JamOut.")"; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group row mb-1">
                <div class="col-sm-12 mb-2">
                  <div class="form-error">
                    <select multiple="multiple" name="Member[]" class="form-control" id="Member"></select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="add_selected();" class="btn btn-primary waves-effect waves-light ">Add Selected</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- MODAL EDIT -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Edit Schedule</h4>
            <button type="button" class="close" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="editForm">
              <input type="hidden" value="" name="UserIDEdit" id="UserIDEdit">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDEdit" id="DeptIDEdit" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameEdit" id="DeptNameEdit" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">NIP</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NipEdit" id="NipEdit" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="NameEdit" id="NameEdit" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Shift Operation</label>
                <div class="col-sm-4 mb-1">
                  <select name="ShiftOperationEdit" id="ShiftOperationEdit" class="form-control">
                    <option value="">KOSONGKAN</option>
                    <option value="" selected disabled>-- Pilih --</option>
                    <?php foreach ($shiftList as $shift): ?>
                      <option value="<?php echo $shift->ShiftID; ?>">
                        <?php echo strtoupper($shift->ShiftName); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_pegawai_nonshift()" id="btnUpdate" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/dual-listbox/src/jquery.bootstrap-duallistbox.js"></script>

    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
        var end   = moment().endOf('month');

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#DateShow').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#StartDateShow').val(start.format('YYYY-MM-DD'));
          $('#EndDateShow').val(end.format('YYYY-MM-DD'));
        }

        $('#DateShow').daterangepicker({
          maxDate: moment().add(3, 'months'),
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
      var DEPTID    = "<?php echo $DEPTID; ?>";
      var DEPTNAME  = "<?php echo $DEPTNAME; ?>";

      //FUNCTION TAMBAHKAN PEGAWAI
      function add_selected() 
      {
        let selected = $('#Member').val(); 
        if (selected && selected.length > 0) {
          $('#SelectedEmployee').val(selected.length + ' Selected');
          $('#hiddenContainer').empty();
          selected.forEach(function(id){
            let label     = $("#Member option[value='" + id + "']").text();
            let ShiftID   = $('#ShiftOperation').val();
            // pisahkan dengan tanda " - " dan ambil nama saja
            let parts     = label.split('-');
            let nama      = (parts.length > 1) ? parts[1].trim() : label.trim();

            console.log(id);
            console.log(nama);

            $.ajax({
              url: "<?php echo base_url(); ?>absensi/update_pegawai_nonshift",
              dataType: 'JSON',
              data: {
                NipEdit: id,
                Name: nama,
                ShiftOperation: ShiftID
              },
              type: 'POST',
              beforeSend: function () {
                $("#loading").show();
                $("#btnUpdate").prop('disabled', true);
              },
              success: function (data) {
                $(".form-group").removeClass('has-error');
                $(".help-block").remove();

                if (data.status == 'success') {
                  $("#loading").hide();
                  $('#addModal').modal('hide');
                  $('#addForm')[0].reset();
                  reload_table();
                } else if (data.status == 'error') {
                  $("#loading").hide();
                  Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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
                      inputElem.closest('.form-error').addClass('has-error');
                      if (inputElem.next('.help-block').length === 0) {
                        inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                      }
                    } else {
                      var inputElem = $('[name="' + inputName + '"]');

                      // Penanganan khusus dual listbox
                      if (inputName === 'Member') {
                        var dualListboxContainer = $('#Member').closest('.form-error');
                        dualListboxContainer.addClass('has-error');
                        if (dualListboxContainer.find('.help-block').length === 0) {
                          dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                      } else {
                        inputElem.closest('.form-error').addClass('has-error');
                        if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                          inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                      }
                    }
                  }
                }

                $("#btnUpdate").text('Update');
                $("#btnUpdate").prop('disabled', false);
              },
              error: function () {
                $("#loading").hide();
                alert('Error adding / update data');
                $('#btnUpdate').text('Update');
                $('#btnUpdate').prop('disabled', false);
              }
            });

            // hidden untuk NIP (value saja)
            // $('#hiddenContainer').append(
            //   '<input type="hidden" name="SelectedNip[]" value="' + id + '">'
            // );

            // // hidden untuk nama (tanpa NIP)
            // $('#hiddenContainer').append(
            //   '<input type="hidden" name="SelectedName[]" value="' + nama + '">'
            // );
          });
          //$('#addModal').modal('hide');
        } else {
          Swal.fire({title: "Oops...", text: "Harap pilih setidaknya 1 pegawai.", icon: "info" });

          return false;
        }
      }

      function reset_all() 
      {
        $('#addForm')[0].reset();
        $('#addModal').modal('hide');
        $('.modal-title').text('Tambah Data');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN
      function openModal() 
      {
        save_method = 'add';
        $('#addForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#addModal').modal('show');
        $('.modal-title').text('Tambah Data');
        $('#DeptIDModal').val(DEPTID);
        $('#DeptNameModal').val(DEPTNAME);
      }

      function openModalEdit(UserID, Nip, Name, DeptID, DeptName, ShiftID) 
      {
        $('#editForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#editModal').modal('show');
        $('#editModal .modal-title').text('Edit Data');
        $('#UserIDEdit').val(UserID);
        $('#DeptIDEdit').val(DeptID);
        $('#DeptNameEdit').val(DeptName);
        $('#NipEdit').val(Nip);
        $('#NameEdit').val(Name);
        $('#ShiftOperationEdit').val(ShiftID);
      }

      //APPLY SCHEDULE
      function apply() 
      {
        //generateNewTable();
        var form_data = $('#shiftForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>shiftsetting/set_schedule",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnFilter").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              reload_table();
              generateNewTable();
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
                  inputElem.closest('.form-error').addClass('has-error');
                  if (inputElem.next('.help-block').length === 0) {
                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                  }
                } else {
                  var inputElem = $('[name="' + inputName + '"]');

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }

                  // var inputElem = $('[name="' + inputName + '"]');

                  // // Penanganan khusus untuk dual listbox (Members)
                  // if (inputName === 'Member') {
                  //   var dualListboxContainer = $('#Member').closest('.form-error');
                  //   dualListboxContainer.addClass('has-error');
                  //   // Cek agar tidak duplikat
                  //   if (dualListboxContainer.find('.help-block').length === 0) {
                  //     dualListboxContainer.prepend('<span class="help-block text-danger">' + errorMsg + '</span>');
                  //   }
                  // } else {
                  //   inputElem.closest('.form-error').addClass('has-error');
                  //   if (inputElem.next('.help-block').length === 0) {
                  //     inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                  //   }
                  // }
                }
              }
            }

            $("#btnFilter").text('Apply');
            $("#btnFilter").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnFilter').text('Apply');
            $('#btnFilter').prop('disabled', false);
          }
        });
      }

      //UPDATE
      function update_pegawai_nonshift()
      {
        var form_data = $('#editForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>absensi/update_pegawai_nonshiftXXX",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnUpdate").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#editModal').modal('hide');
              $('#editForm')[0].reset();
              reload_table();
            } else if (data.status == 'error') {
              $("#loading").hide();
              Swal.fire({icon: 'error', title: 'Oops...', html: data.message});
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
                  inputElem.closest('.form-error').addClass('has-error');
                  if (inputElem.next('.help-block').length === 0) {
                    inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                  }
                } else {
                  var inputElem = $('[name="' + inputName + '"]');

                  // Penanganan khusus dual listbox
                  if (inputName === 'Member') {
                    var dualListboxContainer = $('#Member').closest('.form-error');
                    dualListboxContainer.addClass('has-error');
                    if (dualListboxContainer.find('.help-block').length === 0) {
                      dualListboxContainer.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  } else {
                    inputElem.closest('.form-error').addClass('has-error');
                    if (inputElem.closest('.form-error').find('.help-block').length === 0) {
                      inputElem.closest('.form-error').append('<span class="help-block text-danger">' + errorMsg + '</span>');
                    }
                  }
                }
              }
            }

            $("#btnUpdate").text('Update');
            $("#btnUpdate").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnUpdate').text('Update');
            $('#btnUpdate').prop('disabled', false);
          }
        });
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      };

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#myTable').DataTable({
          "pagingType": "full_numbers",
          "lengthMenu": [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"]
          ],
          "displayLength": 10,
          responsive: false,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
          "processing": true,
          "serverSide": false,
          "ordering": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>absensi/set_pegawai_nonshift_list",
            "type": "POST",
            "data": function(data) {
              data.DeptShow      = $('#DeptShow').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "NIP": "NIP" , "sClass": "text-left", "width": "50px" },
            { "NAME": "NAME" , "sClass": "text-left", "width": "180px" },
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-left", "width": "80px" },
            { "GENDER": "GENDER" , "sClass": "text-left", "width": "80px" },
            { "SHIFT OPERATION": "SHIFT OPERATION" , "sClass": "text-left", "width": "80px" }
          ]
        });

        // EDIT SCHEDULE
        $(document).on('click', 'td.pointer span', function() {
          let EmployeeID    = $(this).data('empid');
          let EmployeeName  = $(this).data('empname');
          let DeptID        = $(this).data('deptid');
          let ShiftID       = $(this).data('shiftid');
          let ScheduleID    = $(this).data('scheduleid');

          console.log(EmployeeID);
          console.log(EmployeeName);

          $('#kodeEdit').val(ScheduleID);
          $('#DeptIDEdit').val(DEPTID);
          $('#DeptNameEdit').val(DEPTNAME);
          $('#NipEdit').val(EmployeeID);
          $('#NameEdit').val(EmployeeName);
          $('#ShiftOperationEdit').val(ShiftID);

          // buka modal
          $('#editModal').modal('show');
        });

        $(document).on('click', '#editModal button.close', function() {
          // tutup modal
          $('#editModal').modal('hide');
          $('#editForm')[0].reset();
        });

        // ================== DualList Modal dsb tetap ==================
        let demo1;
        $('#addModal').on('shown.bs.modal', function () {
          if (!demo1) {
            demo1 = $('#Member').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 250,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('absensi/get_user_all'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#Member').empty();
              $.each(response, function (i, item) {
                $('#Member').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTNAME + ' - ' + item.NAME })
                );
              });
              demo1.bootstrapDualListbox('refresh');
            }
          });
        });

        // ================== Dropdown Fix dsb tetap ==================
        $(document).on('show.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $(e.target).find('.dropdown-menu');
            $('body').append($dropdown.detach());
            var eOffset = $(e.target).offset();
            $dropdown.css({
                'display': 'block',
                'top': eOffset.top + $(e.target).outerHeight(),
                'left': eOffset.left
            });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $('body > .dropdown-menu');
            $(e.target).append($dropdown.detach());
            $dropdown.hide();
        });

        $("#ShiftCode").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('[name="StartDate"], [name="EndDate"], #ShiftOperation').on('change keyup', function () {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).closest('.form-error').find('.help-block').remove();
        });
      });
    </script>
  </body>
</html>