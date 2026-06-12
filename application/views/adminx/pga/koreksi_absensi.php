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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
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
                            <div class="card-block">
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
                                <div class="col-md-2 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="125%" border="1" cellpadding="0" cellspacing="0">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="8%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">NOMOR</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="5%">DEPARTEMEN</th>
                                      <th class="text-center" width="5%">NIP</th>
                                      <th class="text-center" width="7%">NAME</th>
                                      <th class="text-center" width="7%">TANGGAL</th>
                                      <th class="text-center" width="7%">CHECK IN</th>
                                      <th class="text-center" width="7%">CHECK OUT</th>
                                      <th class="text-center" width="7%">CHANGE COLOM</th>
                                      <th class="text-center" width="7%">CHECK IN KOREKSI</th>
                                      <th class="text-center" width="7%">CHECK OUT KOREKSI</th>
                                      <th class="text-center" width="7%">NOTES</th>
                                      <th class="text-center" width="10%">CREATE DATE</th>
                                      <th class="text-center" width="10%">CREATE BY</th>
                                      <th class="text-center" width="10%">APPROVED DATE</th>
                                      <th class="text-center" width="10%">APPROVED BY</th>
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
            <button type="button" class="close" aria-label="Close" onclick="reset()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formData" method="post">
              <input type="hidden" value="" name="kode">
              <input type="hidden" value="" name="Nomor">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4">
                  <select name="DeptID" id="DeptID" class="form-control" onchange="get_karyawan(this);">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($department_att as $value): ?>
                      <option value="<?= $value->DEPTID; ?>">
                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
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
              <div class="form-group row mb-2 border-top border-bottom">
                <label class="col-sm-4 col-form-label">TANGGAL</label>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-4">
                  <input type="date" name="Tanggal" id="Tanggal" class="form-control" placeholder="Tanggal mulai">
                  <span class="help-block"></span>
                </div>
                <div class="col-sm-2 mb-2">
                  <button type="button" class="btn btn-primary btn-block" onclick="cek_absensi_before()">CEK DATA</button>
                </div>
              </div>
              <div class="form-group row mb-2 border-top border-bottom">
                <label class="col-sm-4 col-form-label">ABSENSI SEBELUMNYA</label>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Check In</label>
                <div class="col-sm-4">
                  <input type="datetime-local" name="CheckInAsli" id="CheckInAsli" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Check Out</label>
                <div class="col-sm-4">
                  <input type="datetime-local" name="CheckOutAsli" id="CheckOutAsli" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-2 border-top border-bottom">
                <label class="col-sm-4 col-form-label">ABSENSI SESUDAHNYA</label>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Perubahan Pada</label>
                <div class="col-sm-4">
                  <select name="ChangeTo" id="ChangeTo" class="form-control">
                    <option value="" readonly selected>-- Pilih --</option>
                    <option value="IN">Hanya In</option>
                    <option value="OUT">Hanya Out</option>
                    <option value="ALL">Keduanya</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Check In</label>
                <div class="col-sm-4">
                  <input type="datetime-local" name="CheckInKoreksi" id="CheckInKoreksi" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Check Out</label>
                <div class="col-sm-4">
                  <input type="datetime-local" name="CheckOutKoreksi" id="CheckOutKoreksi" class="form-control" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Notes</label>
                <div class="col-sm-10">
                  <textarea name="Notes" id="Notes" class="form-control" rows="3" maxlength="255" placeholder="Keterangan melakukan koreksi."></textarea>
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

    <div id="loading" class="loading">Loading&#8230;</div>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/multi-select-filter-checkbox/js/filter-multi-select-bundle.min.js"></script>
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
    <script>
      var save_method;
      var url;

      $("#ChangeTo").on("change", function () {
        // reset ke readonly dulu
        $("#CheckInKoreksi").prop("readonly", true).val("").closest(".col-sm-4").removeClass("has-error").find(".help-block").text("");
        $("#CheckOutKoreksi").prop("readonly", true).val("").closest(".col-sm-4").removeClass("has-error").find(".help-block").text("");

        if ($(this).val() === "IN") {
          $("#CheckInKoreksi").prop("readonly", false);
        } else if ($(this).val() === "OUT") {
          $("#CheckOutKoreksi").prop("readonly", false);
        } else if ($(this).val() === "ALL") {
          $("#CheckInKoreksi").prop("readonly", false);
          $("#CheckOutKoreksi").prop("readonly", false);
        }
      });

      function cek_absensi_before()
      {
        let DeptIDs      = $('#DeptID').val();
        let EmployeeIDs  = $('#EmployeeID').val();
        let Tanggals     = $('#Tanggal').val();
        let changeTo     = $('#ChangeTo').val();

        $.ajax({
          url: '<?php echo base_url(); ?>koreksi_absensi/koreksi_periksa_absensi',
          type: 'POST',
          dataType: "json",
          data: {
            DeptID: DeptIDs,
            EmployeeID: EmployeeIDs,
            Tanggal: Tanggals
          },
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            if (data.status_code === 200 && data.data.length > 0) {
              // Ambil jam pertama (paling awal) dan terakhir (paling akhir)
              let checkIn   = data.data[data.data.length - 1].CHECKTIME;  
              let checkOut  = data.data[0].CHECKTIME;

              // Set nilai ke input
              $('#CheckInAsli').val(checkIn);
              $('#CheckOutAsli').val(checkOut);

              // Kosongkan dulu nilai perubahan
              $('#CheckInPerubahan').val("");
              $('#CheckOutPerubahan').val("");

              // Isi sesuai pilihan
              if (changeTo === "IN") {
                $('#CheckInPerubahan').val(checkIn);
              } else if (changeTo === "OUT") {
                $('#CheckOutPerubahan').val(checkOut);
              } else if (changeTo === "ALL") {
                $('#CheckInPerubahan').val(checkIn);
                $('#CheckOutPerubahan').val(checkOut);
              }

            } else if(data.status_code === 404 && data.data.length === 0) {
              $("#loading").hide();
            } else {
              
              for (var i = 0; i < data.inputerror.length; i++) {
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
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

      function approved(AbsenceID, Status, Label) {
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
              url: '<?php echo base_url(); ?>koreksi_absensi/koreksi_approved',
              type: 'POST',
              data: {
                Id: AbsenceID,
                isApproved: Status
              },
              dataType: "json", // lowercase lebih aman
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                console.log("Response:", data);

                if (data.status === 'forbidden') {
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info'
                  );
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
              html += '<option value="'+ data[i].SSN +'"'+selected+'>'+ data[i].NAME +'</option>';
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

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Data');
        $('#ShowDrawing').hide();
      }

      function closeModal() {
        $('#formData')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#formData')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#formData')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>koreksi_absensi/koreksi_edit/" + id,
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
              $('[name="kode"]').val(data.KoreksiID);
              $('[name="Nomor"]').val(data.Nomor);
              $('[name="DeptID"]').val(data.DEFAULTDEPTID);
              $('[name="Tanggal"]').val(data.Tanggal);
              $('[name="CheckInAsli"]').val(data.CheckInAsli);
              $('[name="CheckOutAsli"]').val(data.CheckOutAsli);
              $('[name="ChangeTo"]').val(data.ColumnChange);
              $('[name="CheckInKoreksi"]').val(data.CheckInKoreksi);
              $('[name="CheckOutKoreksi"]').val(data.CheckOutKoreksi);
              $('[name="Notes"]').val(data.Notes);

              let DeptID    = data.DEFAULTDEPTID;
              let UserID    = data.EmployeeID;
              let ChangeCol = data.ColumnChange;
              get_karyawan(DeptID, UserID);

              if (ChangeCol === "IN") {
                $("#CheckInKoreksi").prop("readonly", false);
              } else if (ChangeCol === "OUT") {
                $("#CheckOutKoreksi").prop("readonly", false);
              } else if (ChangeCol === "ALL") {
                $("#CheckInKoreksi").prop("readonly", false);
                $("#CheckOutKoreksi").prop("readonly", false);
              }

              $('#modal').modal('show');
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
              url: '<?php echo base_url(); ?>koreksi_absensi/koreksi_deleted/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
                $("#loading").hide();
              },
              beforeSend: function() {
                $("#loading").show();
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
                  reload_table();
                }

                $("#loading").hide();
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
      function save() 
      {
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>koreksi_absensi/koreksi_add";
        } else {
          url = "<?php echo base_url(); ?>koreksi_absensi/koreksi_update";
        }

        var form_data = $('#formData').serializeArray();

        $.ajax({
          url: url,
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function (data) {
            //$(".form-group").removeClass('has-error');
            //$(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              reload_table();
              reset();
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
                console.log(data.inputerror[i]);
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }

            $("#btnSave").text('Save');
            $("#btnSave").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save');
            $('#btnSave').prop('disabled', false);
          }
        });
      };

      $(document).ready(function() {
        $("#loading").hide();

        table = $('#order-table').DataTable({
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
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]
              },
              customize: function (doc) {
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

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
                    text: 'LAPORAN KOREKSI KEHADIRAN PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
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
                    const alignRightCols = [0];
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
                const StartDate = new Date($('#start_date').val());
                const EndDate   = new Date($('#end_date').val());

                const options         = { day: '2-digit', month: 'long', year: 'numeric' };
                const formattedStart  = StartDate.toLocaleDateString('id-ID', options).toUpperCase();
                const formattedEnd    = EndDate.toLocaleDateString('id-ID', options).toUpperCase();

                return 'LAPORAN KOREKSI KEHADIRAN PEGAWAI PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
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
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>koreksi_absensi/koreksi_list",
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
              data.dept_id      = (DeptShow.length > 0) ? DeptShow : <?php echo $DEPTID; ?>;
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "NOMOR": "NOMOR" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px"},
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-left", "width": "80px" },
            { "NIP": "NIP" , "sClass": "text-center", "width": "50px" },
            { "NAME": "NAME" , "sClass": "text-left", "width": "150px" },
            { "TANGGAL": "TANGGAL" , "sClass": "text-center", "width": "50px" },
            { "CHECK IN": "CHECK IN" , "sClass": "text-center", "width": "80px" },
            { "CHECK OUT": "CHECK OUT" , "sClass": "text-center", "width": "80px" },
            { "CHANGE COLOM": "CHANGE COLOM" , "sClass": "text-center", "width": "80px" },
            { "CHECK IN KOREKSI": "CHECK IN KOREKSI" , "sClass": "text-center", "width": "80px" },
            { "CHECK OUT KOREKSI": "CHECK OUT KOREKSI" , "sClass": "text-center", "width": "80px" },
            { "NOTES": "NOTES" , "sClass": "text-left", "width": "250px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" },
            { "APPROVED BY": "APPROVED BY" , "sClass": "text-center", "width": "80px" },
            { "APPROVED BY": "APPROVED BY" , "sClass": "text-center", "width": "80px" },
          ],
          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
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

        $("#DeptID, #EmployeeID, #Tanggal, #ChangeTo, #CheckInPerubahan, #CheckOutPerubahan, #Notes").change(function(){
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