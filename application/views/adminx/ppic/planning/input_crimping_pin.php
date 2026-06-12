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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/css/filter_multi_select.css">
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
                                      <option value="<?= $dept->DEPTID; ?>" selected>
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="120%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" rowspan="2">NO</th>
                                      <th class="text-center" rowspan="2">#</th>
                                      <th class="text-center" rowspan="2">TGL JOB</th>
                                      <th class="text-center" rowspan="2">NO JOB</th>
                                      <th class="text-center" rowspan="2">QTY JOB</th>
                                      <th class="text-center" rowspan="2">PART ID</th>
                                      <th class="text-center" rowspan="2">PART NAME</th>
                                      <th class="text-center" colspan="3">PLANNING</th>
                                      <th class="text-center" rowspan="2">%</th>
                                      <th class="text-center" rowspan="2">SISA PLAN</th>
                                      <th class="text-center" rowspan="2">NOTED</th>
                                      <th class="text-center" rowspan="2">DOWNTIME (MENIT)</th>
                                      <th class="text-center" rowspan="2">CREATED DATE</th>
                                      <th class="text-center" rowspan="2">CREATED BY</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">TANGGAL</th>
                                      <th class="text-center">PLAN</th>
                                      <th class="text-center">ACTUAL</th>
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
    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="post" id="fgForm">
              <div class="form-group row border-bottom">
                <label class="col-sm-4 mb-2 col-form-label">TAMBAH QUANTITY</label>
              </div>
              <div class="form-group row mt-3">
                <div class="col-md-3 form-error">
                  <label class="col-form-label">Plan Date</label>
                  <input type="date" name="PlanDate" id="PlanDate" class="form-control" placeholder="Plan Date" maxlength="35" readonly required autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-3 form-error mb-1">
                  <label class="col-form-label">Plan Quantity</label>
                  <input type="text" name="PlanQty" id="PlanQty" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" readonly required placeholder="Plan Qty." autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-3 form-error mb-1">
                  <label class="col-form-label">Actual Date</label>
                  <input type="date" name="ActualDate" id="ActualDate" maxlength="12" class="form-control" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-3 form-error mb-1">
                  <label class="col-form-label">Actual Quantity</label>
                  <input type="text" name="ActualQty" id="ActualQty" maxlength="12" oninput="AllowDecimalAndComma(this)" class="form-control" placeholder="Contoh: 10.000" autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-4 form-error mb-1">
                  <label class="col-form-label">Down Time Start</label>
                  <input type="datetime-local" name="DowntimeStart" id="DowntimeStart" class="form-control" placeholder="Masukan downtime" required autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-4 form-error mb-1">
                  <label class="col-form-label">Down Time End</label>
                  <input type="datetime-local" name="DowntimeEnd" id="DowntimeEnd" class="form-control" placeholder="Masukan downtime" required autocomplete="off">
                  <span class="help-block"></span>
                </div>
                <div class="col-md-4 form-error mb-1">
                  <label class="col-form-label">Keterangan</label>
                  <select name="Keterangan" id="Keterangan" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="MESIN BEROPERASI">MESIN BEROPERASI</option>
                    <option value="PERBAIKAN MESIN">PERBAIKAN MESIN</option>
                    <option value="SETTING MESIN">SETTING MESIN</option>
                    <option value="MESIN TROUBLE">MESIN TROUBLE</option>
                    <option value="SUPPLY MATERIAL">SUPPLY MATERIAL</option>
                    <option value="NG MATRIAL">NG MATRIAL</option>
                    <option value="GANTI TYPE">GANTI TYPE</option>
                    <option value="NO OPERATOR">NO OPERATOR</option>
                    <option value="NO PLANNING">NO PLANNING</option>
                    <option value="LAINNYA">LAINNYA</option>
                  </select>
                  <!-- <input type="text" name="Keterangan" id="Keterangan" maxlength="150" class="form-control" placeholder="Keterangan downtime" required autocomplete="off"> -->
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row border-top border-bottom">
                <label class="col-sm-7 col-form-label">ITEM</label>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Job Number</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="JobNumber" id="JobNumber" placeholder="Job Number" class="form-control" required="required" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Part ID</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="PartID" id="PartID" placeholder="Part ID" class="form-control" required="required" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Part Name</label>
                <div class="col-sm-10 form-error mb-2">
                  <input type="text" name="PartName" id="PartName" class="form-control" required="required" placeholder="Part Name" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Job Date</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="JobDate" id="JobDate" class="form-control" required="required" placeholder="Tanggal Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Job Quantity</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="JobQuantity" id="JobQuantity" class="form-control" required="required" placeholder="Quantity Job" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Unit ID</label>
                <div class="col-sm-2 form-error mb-2">
                  <input type="text" name="UnitID" id="UnitID" class="form-control" required="required" placeholder="Unit ID" autocomplete="off" readonly>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {
        var start = moment().startOf('month');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#start_date').val(start.format('YYYY-MM-DD'));
          $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('#tanggal').daterangepicker({
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

      function reset_all() {
        $('#fgForm')[0].reset();
        $('#modalForm').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      function tambah(JobNumber, PartID, PartName, JobDate, JobQuantity, UnitID, PlanDate, PlanQty) {

        $('#fgForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Data');
        $('#btnSave').text('Simpan');

        $('[name="JobNumber"]').val(JobNumber);
        $('[name="PartID"]').val(PartID);
        $('[name="PartName"]').val(PartName);
        $('[name="JobDate"]').val(JobDate);
        $('[name="JobQuantity"]').val(JobQuantity);
        $('[name="UnitID"]').val(UnitID);
        $('[name="PlanDate"]').val(PlanDate);
        $('[name="PlanQty"]').val(PlanQty);
      }

      //FUNCTION EDIT
      function edit(JobNumber, PartID, PartName, JobDate, JobQuantity, UnitID, PlanDate, PlanQty, ActualQty, Noted, ActualDate, DownTimeStart, DownTimeEnd) {

        $('#fgForm')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modalForm').modal('show'); // show bootstrap modal
        $('.modal-title').text('Edit Data');
        $('#btnSave').text('Update');

        $('[name="JobNumber"]').val(JobNumber);
        $('[name="PartID"]').val(PartID);
        $('[name="PartName"]').val(PartName);
        $('[name="JobDate"]').val(JobDate);
        $('[name="JobQuantity"]').val(JobQuantity);
        $('[name="UnitID"]').val(UnitID);
        $('[name="PlanDate"]').val(PlanDate);
        $('[name="PlanQty"]').val(PlanQty);
        $('[name="ActualQty"]').val(ActualQty);
        $('[name="Keterangan"]').val(Noted);
        $('[name="ActualDate"]').val(ActualDate);
        $('[name="DowntimeStart"]').val(DownTimeStart);
        $('[name="DowntimeEnd"]').val(DownTimeEnd);
      }

      //FUNCTION HAPUS
      function hapus(JobNumber, TransID) 
      {
        Swal.fire({
          title: 'Hapus ?',
          text: "Data yang dihapus tidak bisa dikembalikan.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, hapus',
          cancelButtonText: 'Tidak, Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '<?php echo base_url(); ?>wip/wip_crimping_delete',
              type: 'POST',
              data: {
                JobNumbers: JobNumber,
                TransIDs: TransID
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                var result = JSON.parse(data);
                console.log(result);
                if (result.status == 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  //$("#" + jobNumber).remove();
                  // Swal.fire({
                  //   title: "Sukses",
                  //   text: result.message,
                  //   icon: "success"
                  // });
                  reload_table();
                }

                $("#loading").hide();
              },
              error: function() {
                alert('Something is wrong');
              },
            });
          }
        })
      }

      function save() {
        var form_data = $("#fgForm").serialize();

        $.ajax({
          url: "<?php echo base_url(); ?>wip/wip_crimping_save",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function(data) {
            // Bersihkan error standar
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
                $("#loading").hide();
                $('#modalForm').modal('hide');
                $('#fgForm')[0].reset();
                reload_table();
                reset_all();
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

                // LOOPING ERROR
                for (var i = 0; i < data.inputerror.length; i++) {
                    var inputName = data.inputerror[i];
                    var errorMsg = data.error_string[i];

                    // --- LOGIKA KHUSUS UNTUK RADIO BUTTON (LINE_ID) ---
                    if (inputName === 'line_id') {
                        // Targetkan ID Container, bukan input name-nya
                        var container = $('#container-line-radios');
                        
                        // Tambahkan class error (jika perlu styling merah)
                        container.addClass('has-error'); 

                        // Cek agar pesan tidak muncul dobel
                        if (container.find('.help-block').length === 0) {
                            // Append pesan error di bagian bawah container
                            container.append('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    } 
                    // --- LOGIKA UNTUK INPUT ARRAY (PlanDate[], PlanQty[]) ---
                    else {
                        var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                        if (arrayMatch) {
                            var arrayName = arrayMatch[1];
                            var arrayIndex = parseInt(arrayMatch[2]);
                            var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                            
                            inputElem.closest('.form-error').addClass('has-error');
                            if (inputElem.next('.help-block').length === 0) {
                                inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                            }
                        } 
                        // --- LOGIKA UNTUK INPUT BIASA LAINNYA ---
                        else {
                            var inputElem = $('[name="' + inputName + '"]');
                            inputElem.closest('.form-error').addClass('has-error');
                            if (inputElem.next('.help-block').length === 0) {
                                inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                            }
                        }
                    }
                }
            }
            $("#btnSave").prop('disabled', false);
          },
          error: function() {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save');
            $('#btnSave').prop('disabled', false);
          }
        });
      }
      
      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      } 

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#myTable').DataTable({
          dom: 'frltip',
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
            left: 0
          },
          select: {
            style: 'single'
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>wip/wip_crimping_list",
            "type": "POST",
            "data": function(data) {
              let DeptShow = [];
              $('input[name="DeptShow"]:checked').each(function () {
                if ($(this).val()) {
                  DeptShow.push($(this).val());
                }
              });

              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.dept_id      = (DeptShow.length > 0) ? DeptShow : <?php echo $DeptID; ?>;
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "TGL. JOB": "TGL. JOB" , "sClass": "text-center", "width": "100px" },
            { "NO. JOB": "NO. JOB" , "sClass": "text-center", "width": "100px" },
            { "QTY. JOB": "QTY. JOB" , "sClass": "text-right", "width": "50px" },
            { "PART ID": "PART ID" , "sClass": "text-left", "width": "100px" },
            { "PART NAME": "PART NAME" , "sClass": "text-left", "width": "245px" },
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "100px" }, 
            { "PLAN": "PLAN" , "sClass": "text-right", "width": "100px" },
            { "ACTUAL": "ACTUAL" , "sClass": "text-right", "width": "100px" }, //10
            { "%": "%" , "sClass": "text-right", "width": "100px" },
            { "SISA PLAN": "SISA PLAN" , "sClass": "text-right", "width": "100px" },
            { "NOTED": "NOTED" , "sClass": "text-left", "width": "150px" },
            { "DOWNTIME (MENIT)": "DOWNTIME (MENIT)" , "sClass": "text-right", "width": "50px" },
            { "CREATED DATE": "CREATE DATE" , "sClass": "text-left", "width": "100px" },
            { "CREATED BY": "CREATE BY" , "sClass": "text-center", "width": "100px" }
          ],
          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        table.on('click', 'tbody tr', function (e) {
            table.$('tr.selected').removeClass('selected');  // hilangkan selected di semua row
            $(this).addClass('selected');                    // tambahkan selected ke row yg diklik
        });

        $(document).on('show.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $(e.target).find('.dropdown-menu');
            $('body').append($dropdown.detach()); // pindahkan ke body
            var eOffset = $(e.target).offset();
            $dropdown.css({
                'display': 'block',
                'top': eOffset.top + $(e.target).outerHeight(),
                'left': eOffset.left
            });
        });

        $(document).on('hide.bs.dropdown', '.btn-group', function (e) {
            var $dropdown = $('body > .dropdown-menu');
            $(e.target).append($dropdown.detach()); // kembalikan ke dalam btn-group
            $dropdown.hide();
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }

        $("#ActualDate, #ActualQty, #DowntimeStart, #DowntimeEnd, #Keterangan").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Keterangan').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            // Ubah huruf pertama jadi Kapital + sisa teks aslinya
            $(this).val(val.charAt(0).toUpperCase() + val.slice(1));
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