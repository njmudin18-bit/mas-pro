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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/css/filter_multi_select.css">
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
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-4 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control" multiple>
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="DateShow" id="DateShow">
                                    <span class="input-group-append">
                                      <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                    </span>
                                  </div>

                                  <input type="hidden" name="StartDateShow" id="StartDateShow">
                                  <input type="hidden" name="EndDateShow" id="EndDateShow">
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3 text-right">
                                  <button id="btnTambah" type="button" class="btn btn-success btn-full-mobile" onclick="openModal();">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="150%">
                                  <thead id="thead-shift" class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center">NO</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">NOMOR</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">DEPARTEMEN</th>
                                      <th class="text-center">NIP</th>
                                      <th class="text-center">NAME</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">OVERTIME DATE</th>
                                      <th class="text-center">START TIME</th>
                                      <th class="text-center">END TIME</th>
                                      <?php if ($this->session->userdata('user_dept_name') == 'IT' || $this->session->userdata('user_dept_name') == 'HRD' || $this->session->userdata('user_dept_name') == 'ACCOUNTING'): ?>
                                      <th class="text-center">TOTAL HOURS</th>
                                      <th class="text-center">TOTAL AMOUNT</th>
                                      <?php endif; ?>
                                      <th class="text-center">NOTES</th>
                                      <th class="text-center">APPROVED BY</th>
                                      <th class="text-center">APPROVED DATE</th>
                                      <th class="text-center">CREATED DATE</th>
                                      <th class="text-center">CREATED BY</th>
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
              <input type="hidden" name="Kode" id="Kode">
              <input type="hidden" name="Nomor" id="Nomor">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4">
                  <select name="DeptID" id="DeptID" class="form-control" onchange="get_karyawan(this);">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($DeptList as $dept): ?>
                      <option value="<?= $dept->DEPTID; ?>">
                        <?= strtoupper($dept->DEPTNAME); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Pegawai</label>
                <div class="col-sm-4">
                  <select name="EmployeeID" id="EmployeeID" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Start Time</label>
                <div class="col-sm-4 mb-1 form-error">
                  <input type="datetime-local" name="StartTime" id="StartTime" class="form-control">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 mb-1 col-form-label">End Time</label>
                <div class="col-sm-4 mb-1 form-error">
                  <input type="datetime-local" name="EndTime" id="EndTime" class="form-control">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 mb-1 col-form-label">Keterangan</label>
                <div class="col-sm-10 mb-1 form-error">
                  <textarea name="Notes" id="Notes" rows="5" class="form-control"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="reset_all()">Close</button>
            <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Save</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/dual-listbox/src/jquery.bootstrap-duallistbox.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>

    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {
        var start = moment().startOf('month');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#DateShow').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#StartDateShow').val(start.format('YYYY-MM-DD'));
          $('#EndDateShow').val(end.format('YYYY-MM-DD'));
        }

        $('#DateShow').daterangepicker({
          maxDate: new Date(),
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

      function get_karyawan(el, defaultValue = null) 
      {
        console.log(defaultValue);
        $.ajax({
          url : "<?php echo base_url();?>users/get_karyawan_dept",
          method : "POST",
          data : {id: (typeof el === "object" ? el.value : el)},
          dataType : 'json',
          success: function(data){
            var html = '<option value="">-- Pilih --</option>';
            for (var i = 0; i < data.length; i++) {
              // cek jika defaultValue sama dengan SSN maka tambahkan selected
              let selected = (defaultValue && data[i].SSN == defaultValue) ? ' selected' : '';
              html += '<option value="'+ data[i].SSN +'"'+selected+'>'+ data[i].NAME.toUpperCase() +'</option>';
            }
            if (typeof el === "object") {
              // jika dipanggil dari select onchange
              $(el).closest('.form-group.row').find('select[name="EmployeeID"]').html(html);
            } else {
              // jika dipanggil dari ajax dengan UserID langsung
              $('#EmployeeID').html(html);
            }
          }
        });
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

      function reset_all() 
      {
        $('#addForm')[0].reset();
        $('#addModal').modal('hide');
        $('.modal-title').text('Tambah Data');
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
      }

      function edit(Nomor) {
        save_method = 'update';
        $('#addForm')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>pengajuan_ot/pengajuan_edit",
          type: "POST",
          data: {
            NoReq: Nomor
          },
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              $('[name="Kode"]').val(data.HeaderID);
              $('[name="Nomor"]').val(data.Nomor);
              $('[name="DeptID"]').val(data.DeptID);
              $('[name="StartTime"]').val(data.StartTime);
              $('[name="EndTime"]').val(data.EndTime);
              $('[name="Notes"]').val(data.Notes);

              let DeptID = data.DeptID;
              let UserID = data.EmployeeID;
              get_karyawan(DeptID, UserID);

              $('#addModal').modal('show');
              $('.modal-title').text('Edit Data');
              $('#btnSave').text('Update');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION HAPUS
      function hapus(Nomor) {
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
              url: '<?php echo base_url(); ?>pengajuan_ot/pengajuan_deleted',
              type: 'POST',
              data: {
                NoReq: Nomor
              },
              dataType: "JSON",
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                if (data.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  $("#" + Nomor).remove();
                  reload_table();
                }

                $("#loading").hide();
              },
              error: function() {
                alert('Something is wrong');
                $("#loading").hide();
              },
            });
          }
        })
      }

      //FUNCTION APPROVED OR PENDING
      function approved(Nomor, Status, Label, NIP) {
        Swal.fire({
          title: Label + '?',
          text: "Yakin ingin " + Label + " status ini?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, ' + Label,
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>pengajuan_ot/pengajuan_approved',
              type: 'POST',
              data: {
                Id: Nomor,
                isApproved: Status,
                EmployeeID: NIP
              },
              dataType: "json", // lowercase lebih aman
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                if (data.status === 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info'
                  );
                } else if (data.status == 'error') {
                    $("#loading").hide();
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      html: data.message
                    });
                } else {
                  reload_table();
                }

                $("#loading").hide();
              },
              error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Something is wrong');
                $("#loading").hide();
              }
            });
          }
        });
      }

      function save() {
        var data_save = $('#addForm').serializeArray();

        var url;
        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>pengajuan_ot/pengajuan_add";
        } else {
          url = "<?php echo base_url(); ?>pengajuan_ot/pengajuan_update";
        }

        // ajax adding data to database
        $.ajax({
          url: url,
          type: "POST",
          data: data_save,
          dataType: "JSON",
          beforeSend: function(data) {
            $("#btnSave").html('Saving...');
            $('#btnSave').attr('disabled', true); //set button disable 
          },
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
            } else if (data.status == 'success') {
              $('#addModal').modal('hide');
              reset_all();
              reload_table();
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

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      };

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#myTable').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A3',
              exportOptions: {
                stripHtml: true,
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]
              },
              customize: function (doc) {
                const StartDate = new Date($('#StartDateShow').val());
                const EndDate   = new Date($('#EndDateShow').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                function formatRibuan(num) {
                  if (num === null || num === undefined) return '0';
                  if (typeof num === 'number') {
                      return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }
                  let str = num.toString();
                  const cleaned = str.replace(/[^\d.,-]/g, '');
                  const normalized = cleaned.replace(',', '.');
                  const n = parseFloat(normalized);

                  return isNaN(n) ? str : n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                }

                doc.defaultStyle.fontSize = 10;
                doc.pageMargins           = [10, 40, 10, 60];
                doc.styles = {
                  subheader: {
                    fontSize: 12,
                    bold: true,
                    alignment: 'left'
                  },
                  tableHeader: {
                    bold: true,
                    fontSize: 10,
                    color: 'white',
                    fillColor: '#007bff',
                    alignment: 'center'
                  }
                };

                doc.content.unshift(
                  {
                    text: 'PT. MULTI ARTA SEKAWAN',
                    bold: true,
                    fontSize: 16,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'LAPORAN PENGAJUAN LEMBUR PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
                    bold: true,
                    fontSize: 14,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 9];
                    const body = mainTable.table.body;

                    for (let i = 1; i < body.length; i++) {
                        for (let j = 0; j < body[i].length; j++) {
                            if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                body[i][j].alignment = 'right';
                            }
                        }

                        // SUB TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'SUB TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#6c757d';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        // TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                }
                                break;
                            }
                        }
                    }

                    // Style baris terakhir
                    // const lastRowIndex = body.length - 1;
                    // for (let j = 0; j < body[lastRowIndex].length; j++) {
                    //     if (body[lastRowIndex][j].text !== undefined) {
                    //         body[lastRowIndex][j].fillColor = '#007bff';
                    //         body[lastRowIndex][j].color = '#fff';
                    //     }
                    // }

                    mainTable.layout = {
                        hLineWidth: () => 0.5,
                        vLineWidth: () => 0.5,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa',
                        paddingLeft: () => 4,
                        paddingRight: () => 4,
                        paddingTop: () => 2,
                        paddingBottom: () => 2,
                        fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                    };
                }

                // === Footer ===
                doc.footer = function (currentPage, pageCount) {
                    return {
                        columns: [
                            { text: 'Printed on: ' + new Date().toLocaleString(), alignment: 'left', margin: [10, 0, 0, 0] },
                            { text: 'PT MULTI ARTA SEKAWAN - CONFIDENTIAL', alignment: 'center' },
                            { text: 'Page ' + currentPage + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 10, 0] }
                        ],
                        fontSize: 8
                    };
                };
              },
              // Tambahkan opsi filename di sini
              filename: function() {
                const StartDate = new Date($('#StartDateShow').val());
                const EndDate   = new Date($('#EndDateShow').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN PENGAJUAN LEMBUR PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            }
          ],
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
          fixedColumns: {
            left: 4
          },
          select: {
            style: 'single'
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>pengajuan_ot/pengajuan_list",
            "type": "POST",
            "data": function(data) {
              // ambil DeptShow dari checkbox
              let DeptShow = [];
              $('input[name="DeptShow"]:checked').each(function () {
                if ($(this).val()) {
                  DeptShow.push($(this).val());
                }
              });

              // kirim ke server
              data.DeptShow     = (DeptShow.length > 0) ? DeptShow : <?php echo $DEPTID; ?>;
              data.start_date   = $('#StartDateShow').val();
              data.end_date     = $('#EndDateShow').val();
            }
          },
          "aoColumns": [
            { "NO": "NO", "sClass": "text-right", "width": "50px" },
            { "#": "#", "sClass": "text-center", "width": "50px" },
            { "NOMOR": "NOMOR", "sClass": "text-left", "width": "50px" },
            { "STATUS": "STATUS", "sClass": "text-left", "width": "80px" },
            { "DEPARTEMEN": "DEPARTEMEN", "sClass": "text-left", "width": "80px" },
            { "NIP": "NIP", "sClass": "text-left", "width": "50px" },
            { "NAME": "NAME", "sClass": "text-left", "width": "180px" },
            { "STATUS": "STATUS", "sClass": "text-left", "width": "70px" },
            { "OVERTIME DATE": "OVERTIME DATE", "sClass": "text-center", "width": "80px" },
            { "START TIME": "START TIME", "sClass": "text-center", "width": "80px" },
            { "END TIME": "END TIME", "sClass": "text-center", "width": "80px" },
            <?php if ($this->session->userdata('user_dept_name') == 'IT' || $this->session->userdata('user_dept_name') == 'HRD' || $this->session->userdata('user_dept_name') == 'ACCOUNTING'): ?>
            { "TOTAL HOURS": "TOTAL HOURS", "sClass": "text-right", "width": "80px" },
            { "TOTAL AMOUNT": "TOTAL AMOUNT", "sClass": "text-right", "width": "80px" },
            <?php endif; ?>
            { "NOTES": "NOTES", "sClass": "text-left", "width": "180px" },
            { "APPROVED BY": "APPROVED BY", "sClass": "text-left", "width": "80px" },
            { "APPROVED DATE": "APPROVED DATE", "sClass": "text-center", "width": "80px" },
            { "CREATED DATE": "CREATED DATE", "sClass": "text-center", "width": "80px" },
            { "CREATED BY": "CREATED BY", "sClass": "text-center", "width": "80px" }
          ],
          "columnDefs": [
            {
              "targets": 3, // indeks kolom STATUS (0-based)
              "render": function(data, type, row) {
                if (data === 'PENDING') {
                  return '<span class="text-danger font-weight-bolder">' + data + '</span>';
                } else if (data === 'APPROVED') {
                  return '<span class="text-success font-weight-bolder">' + data + '</span>';
                } else {
                  return data; // default
                }
              }
            },
            {
              "targets": [0],
              "orderable": false,
              className: 'text-right'
            }
          ]
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

        $("#DeptID, #EmployeeID, #StartTime, #EndTime, #Notes").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Notes').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });
    </script>
    <script>
      $(function () {
        const DeptShow = $('#DeptShow').filterMultiSelect({
          placeholderText: "Pilih",
          filterText: "Filter",
          selectAllText: "SELECT ALL",
          labelText: "",
          selectionLimit: 0,
          caseSensitive: false,
          allowEnablingAndDisabling: true,
        });
      });
    </script>
  </body>
</html>