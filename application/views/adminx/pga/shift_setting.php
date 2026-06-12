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
                        <div class="col-sm-3">
                          <div class="card">
                            <div class="card-header text-center">
                              <h5>FILTER</h5>
                            </div>
                            <div class="card-block m-b-10">
                              <form action="" method="post" id="shiftForm">
                                <input type="hidden" name="DeptID" id="DeptID" value="<?php echo $DEPTID; ?>">
                                <input type="hidden" name="DeptName" id="DeptName" value="<?php echo $DEPTNAME; ?>">
                                <div class="form-group form-error">
                                  <label for="exampleInputEmail1">Select Employee</label>
                                  <div class="input-group mb-3">
                                    <input type="text" id="SelectedEmployee" name="SelectedEmployee" class="form-control" placeholder="Select Employee" aria-describedby="basic-addon2">
                                    <div class="input-group-append" onclick="openModal()">
                                      <span class="input-group-text" id="basic-addon2">
                                        <i class="fa fa-users" aria-hidden="true"></i>
                                      </span>
                                    </div>
                                  </div>
                                  <span class="help-block"></span>
                                  <!-- wadah untuk hidden input -->
                                  <div id="hiddenContainer"></div>
                                </div>
                                <div class="form-group form-error">
                                  <label for="StartDate">Date Range</label>
                                  <input type="text" name="StartDate" id="StartDate" class="form-control" placeholder="Start date">
                                  <span class="help-block"></span>
                                </div>
                                <div class="form-group form-error">
                                  <input type="text" name="EndDate" id="EndDate" class="form-control" placeholder="End date">
                                </div>
                                <div class="form-group form-error">
                                  <label for="exampleInputEmail1">Shift Operation</label>
                                  <select name="ShiftOperation" id="ShiftOperation" class="form-control">
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <?php foreach ($shiftList as $shift): ?>
                                      <option value="<?php echo $shift->ShiftID; ?>">
                                        <?php echo strtoupper($shift->ShiftName)." (".$shift->JamIn." - ".$shift->JamOut.")"; ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                  <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                  <button onclick="apply()" id="btnFilter" type="button" class="btn btn-primary btn-block">Apply</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-9">
                          <div class="card">
                            <div class="card-header text-center">
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-3">Filter by</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="DateShow" id="DateShow">
                                    <span class="input-group-append">
                                      <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                    </span>
                                  </div>

                                  <input type="hidden" name="StartDateShow" id="StartDateShow">
                                  <input type="hidden" name="EndDateShow" id="EndDateShow">
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control">
                                    <option value="" <?= empty($DEPTID) ? 'selected' : '' ?> disabled>-- Pilih --</option>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead id="thead-shift" class="bg-primary text-white"></thead>
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
              <input type="hidden" value="" name="kodeFirst">
              <div class="form-group row">
                <label class="col-sm-2 mb-1 col-form-label">Dept. ID</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptIDModal" id="DeptIDModal" class="form-control" readonly>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">Dept. Name</label>
                <div class="col-sm-4 mb-1">
                  <input type="text" name="DeptNameModal" id="DeptNameModal" class="form-control" readonly>
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
              <input type="hidden" value="" name="kodeEdit" id="kodeEdit">
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
                    <option value="" selected disabled>-- Pilih --</option>
                    <?php foreach ($shiftList as $shift): ?>
                      <option value="<?php echo $shift->ShiftID; ?>">
                        <?php echo strtoupper($shift->ShiftName)." (".$shift->JamIn." - ".$shift->JamOut.")"; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button onclick="update_schedule()" id="btnUpdate" type="button" class="btn btn-primary waves-effect waves-light update-schedule">Update</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/dual-listbox/src/jquery.bootstrap-duallistbox.js"></script>

    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      // $(function() {

      //   var start = moment().startOf('month');
      //   var end   = moment().endOf('month');

      //   function cb(start, end) {
      //     var sd = start.format('YYYY-MM-DD');
      //     var ed = end.format('YYYY-MM-DD');

      //     $('#DateShow').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
      //     $('#StartDateShow').val(start.format('YYYY-MM-DD'));
      //     $('#EndDateShow').val(end.format('YYYY-MM-DD'));
      //   }

      //   $('#DateShow').daterangepicker({
      //     maxDate: moment().add(3, 'months'),
      //     startDate: start,
      //     endDate: end,
      //     ranges: {
      //       'Today': [moment(), moment()],
      //       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      //       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      //       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      //       'This Month': [moment().startOf('month'), moment().endOf('month')],
      //       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      //     },
      //     locale: {
      //       format: 'YYYY-MM-DD'
      //     },
      //     function(start, end, label) {
      //       startDate = start;
      //       endDate = end;
      //       console.log(startDate);
      //       console.log(endDate);
      //       console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      //     }
      //   }, cb);
      //   cb(start, end);
      // });

      $(function() {
          var maxSaturday = moment().day(6);
          var start       = moment().startOf('month');
          var end         = maxSaturday; //moment();

          function cb(start, end) {
            var sd = start.format('YYYY-MM-DD');
            var ed = end.format('YYYY-MM-DD');

            $('#DateShow').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            $('#StartDateShow').val(start.format('YYYY-MM-DD'));
            $('#EndDateShow').val(end.format('YYYY-MM-DD'));
          }

          $('#DateShow').daterangepicker({
            maxDate: maxSaturday, 
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
            let label = $("#Member option[value='" + id + "']").text();
            // pisahkan dengan tanda " - " dan ambil nama saja
            let parts   = label.split('-');
            let nama    = (parts.length > 1) ? parts[3].trim() : label.trim();
            let DeptID  = (parts.length > 1) ? parts[1].trim() : label.trim();

            // hidden untuk NIP (value saja)
            $('#hiddenContainer').append(
              '<input type="hidden" name="SelectedNip[]" value="' + id + '">'
            );

            // hidden untuk nama (tanpa NIP)
            $('#hiddenContainer').append(
              '<input type="hidden" name="SelectedName[]" value="' + nama + '">'
            );

            $('#hiddenContainer').append(
              '<input type="hidden" name="SelectedDeptID[]" value="' + DeptID + '">'
            );
          });
          $('#addModal').modal('hide');
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

      //FUNCTION OPEN MODAL CABANG
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
              $('#shiftForm')[0].reset();
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

      //UPDATE SCHEDULE
      function update_schedule()
      {
        var form_data = $('#editForm').serializeArray();

        $.ajax({
          url: "<?php echo base_url(); ?>shiftsetting/update_schedule",
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
              generateNewTable();
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

      //FUNCTION CARI
      function cari() 
      {
        //reload_table();
        generateNewTable();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      };

      function generateDynamicHeader(start, end) {
        const startDate   = new Date(start);
        const endDate     = new Date(end);
        const dateList    = [];

        // 1. Buat list tanggal dari start ke end
        while (startDate <= endDate) {
          const day   = ("0" + startDate.getDate()).slice(-2);
          const month = startDate.toLocaleString('en-US', { month: 'short' }).toUpperCase(); // e.g. JUN
          dateList.push(`${day} ${month}`);
          startDate.setDate(startDate.getDate() + 1);
        }

        // 2. Baris pertama (kolom identitas + tanggal)
        let row1 = '<th class="text-center" width="100px">NO</th>';
        row1    += '<th class="text-center" width="100px">NIP</th>';
        row1    += '<th class="text-center" width="100px">NAME</th>';
        row1    += '<th class="text-center" width="100px">DEPARTEMEN</th>';

        //Baris kedua: Tanggal (01, 02, dst)
        dateList.forEach(date => {
          row1 += `<th width="100px" class="text-center">${date}</th>`;
        });

        return row1;
      }

      function generateDynamicAoColumns(start, end) 
      {
        const startDate = new Date(start);
        const endDate   = new Date(end);
        const columns   = [];

        // Kolom tetap
        columns.push({ sTitle: "NO", sClass: "text-right", width: "50px" });
        columns.push({ sTitle: "NIP", sClass: "text-center", width: "150px" });
        columns.push({ sTitle: "NAME", sClass: "text-left", width: "200px" });
        columns.push({ sTitle: "DEPARTEMEN", sClass: "text-center", width: "100px" });

        // Kolom dinamis: per tanggal
        let currentDate = moment(startDate);
        const lastDate  = moment(endDate);

        while (currentDate <= lastDate) {
          let colTitle = currentDate.format("ddd, DD MMM YYYY").toUpperCase();

          columns.push({ 
            sTitle: colTitle, 
            sClass: "text-left", 
            width: "160px",
            createdCell: function (td, cellData) {
              if (cellData) {
                if (/<span/i.test(cellData)) {
                  // kalau sudah ada <span>
                  let tmp = $('<div>').html(cellData).find('span');
                  let text = tmp.text().trim();
                  let parts = text.split(/\s(?=\()/);

                  if (parts.length > 1) {
                    tmp.html(parts[0] + "<br><span class='font-weight-light' title='Editable'>" + parts[1] + "</span>");
                  } else {
                    tmp.html(parts[0]);
                  }

                  $(td).html(tmp).addClass("pointer");
                } else {
                  // tanpa span
                  let parts = cellData.split(/\s(?=\()/);
                  if (parts.length > 1) {
                    $(td).html(parts[0] + "<br><span class='font-weight-light'>" + parts[1] + "</span>").addClass("");
                  } else {
                    $(td).html(parts[0]).addClass("");
                  }
                }
              }
            }
          });

          currentDate.add(1, "days");
        }

        return columns;
      }

      function generateNewTable() {
        const start = $('#StartDateShow').val();
        const end   = $('#EndDateShow').val();

        // 1. Generate header dan kolom baru
        const dynamicHeader  = generateDynamicHeader(start, end);
        const dynamicColumns = generateDynamicAoColumns(start, end);

        // 2. Set ulang thead HTML
        document.getElementById("thead-shift").innerHTML = dynamicHeader;

        // 3. Destroy DataTable lama jika sudah ada
        if ($.fn.DataTable.isDataTable('#myTable')) {
          $('#myTable').DataTable().destroy();
          $('#myTable').empty(); // Kosongkan tabel untuk menghindari duplikasi
          $('#myTable').html('<thead id="thead-shift" class="bg-primary text-white"></thead><tbody></tbody>');
          document.getElementById("thead-shift").innerHTML = dynamicHeader;
        }

        // 4. Inisialisasi ulang DataTable dengan kolom baru
        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-success',
              filename: function() {
                const StartDate       = new Date($('#StartDateShow').val());
                const EndDate         = new Date($('#EndDateShow').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN JADWAL SHIFT PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                
                $('row c', sheet).attr('s', '25');
              }
            }
          ],
          "processing": true,
          "serverSide": false,
          "ordering": false,
          ajax: {
            url: "<?php echo base_url(); ?>shiftsetting/set_schedule_list",
            type: 'POST',
            data: {
              start_date: start,
              end_date: end,
              dept_id: $('#DeptShow').val()
            }
          },
          fixedColumns: {
            left: 3
          },
          columns: dynamicColumns
        });
      }

      $(document).ready(function() {
        $("#loading").hide();

        // ================== Inisialisasi DatePicker ==================
        $('input[name="StartDate"]').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          autoUpdateInput: true,
          minYear: 2025,
          maxYear: parseInt(moment().format('YYYY'), 10),
          startDate: moment().startOf('week').add(1, 'days'),
          locale: { format: "YYYY-MM-DD" }
        });

        $('input[name="EndDate"]').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          autoUpdateInput: true,
          minYear: 2025,
          maxYear: parseInt(moment().format('YYYY'), 10),
          startDate: moment().startOf('week').add(6, 'days'),
          locale: { format: "YYYY-MM-DD" }
        });

        const start           = $('#StartDateShow').val();
        const end             = $('#EndDateShow').val();

        // Inject header sebelum inisialisasi DataTable
        const dynamicHeader   = generateDynamicHeader(start, end);
        const dynamicColumns  = generateDynamicAoColumns(start, end);
        document.getElementById("thead-shift").innerHTML = dynamicHeader;

        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'excelHtml5',
              text: 'Export Excel',
              title: '',
              className: 'btn btn-success',
              filename: function() {
                const StartDate       = new Date($('#StartDateShow').val());
                const EndDate         = new Date($('#EndDateShow').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN JADWAL SHIFT PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              },
              customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                
                $('row c', sheet).attr('s', '25');
              }
            }
          ],
          "pagingType": "full_numbers",
          "lengthMenu": [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "All"]
          ],
          "displayLength": 5,
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
            "url": "<?php echo base_url(); ?>shiftsetting/set_schedule_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#StartDateShow').val();
              data.end_date     = $('#EndDateShow').val();
              data.dept_id      = $('#DeptShow').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          aoColumns: dynamicColumns
        });

        // EDIT SCHEDULE
        $(document).on('click', 'td.pointer span', function() {
          let EmployeeID    = $(this).data('empid');
          let EmployeeName  = $(this).data('empname');
          let DeptID        = $(this).data('deptid');
          let DeptName      = $(this).data('deptname');
          let ShiftID       = $(this).data('shiftid');
          let ScheduleID    = $(this).data('scheduleid');

          console.log(EmployeeID);
          console.log(EmployeeName);

          $('#kodeEdit').val(ScheduleID);
          // $('#DeptIDEdit').val(DEPTID);
          // $('#DeptNameEdit').val(DEPTNAME);
          $('#DeptIDEdit').val(DeptID);
          $('#DeptNameEdit').val(DeptName);
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

        //UPDATE SCHEDULE
        $(document).on('click', '#editModal button.update-schedule', function() {
          var form_data = $('#editForm').serializeArray();

          $.ajax({
            url: "<?php echo base_url(); ?>shiftsetting/update_schedule",
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
                generateTable(startDate, endDate);
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
        });

        // ================== DualList Modal dsb tetap ==================
        let demo1;
        $('#addModal').on('shown.bs.modal', function () {
          if (!demo1) {
            demo1 = $('#Member').bootstrapDualListbox({
              nonSelectedListLabel: 'Tersedia',
              selectedListLabel: 'Dipilih',
              moveOnSelect: true,
              selectorMinimalHeight: 200,
              infoText: 'Total {0} data',
              infoTextEmpty: 'Empty list'
            });
          }

          $.ajax({
            url: '<?php echo base_url('shiftsetting/get_user_by_deptid'); ?>',
            type: 'POST',
            data: { DeptID: "<?php echo $DEPTID; ?>" },
            dataType: 'json',
            success: function (response) {
              $('#Member').empty();
              $.each(response, function (i, item) {
                $('#Member').append(
                  $('<option>', { value: item.SSN, text: item.SSN + ' - ' + item.DEPTID + ' - ' + item.DEPTNAME + ' - ' + item.NAME})
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