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
    <style>
      .pointer {
        cursor: pointer;
      }

      ul.timeline {
        list-style-type: none;
        position: relative;
      }

      ul.timeline:before {
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
      }

      ul.timeline > li {
        margin: 20px 0;
        padding-left: 60px;
      }

      ul.timeline > li:before {
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
      }

      .history {
        height: 200px;      
        overflow-y: auto;    
        overflow-x: hidden;  
        border: 1px solid #ddd;
        padding-right: 10px;
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
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select name="DeptShow" id="DeptShow" class="form-control">
                                    <option value="" <?= empty($DEPTID) ? 'selected' : '' ?> disabled>-- Pilih --</option>
                                    
                                    <?php if (!empty($DEPTNAME) && (strtoupper($DEPTNAME) === 'IT' || strtoupper($DEPTNAME) === 'HRD' || strtoupper($DEPTNAME) === 'IC/MR')): ?>
                                      <option value="" <?= ($DEPTID === 'ALL') ? 'selected' : '' ?>>ALL DEPARTEMEN</option>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($DeptList as $dept): ?>
                                      <option value="<?= $dept->DEPTID; ?>">
                                        <?= strtoupper($dept->DEPTNAME); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <input type="hidden" name="StartDateShow" id="StartDateShow">
                                  <input type="hidden" name="EndDateShow" id="EndDateShow">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
                              <div class="dt-responsive table-responsive">
                                <table id="myTable" class="table table-striped table-bordered table-hover" width="150%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center">NO</th>
                                      <th class="text-center">#</th>
                                      <th class="text-center">DOCUMENT</th>
                                      <th class="text-center">STATUS</th>
                                      <th class="text-center">DEPARTEMEN</th>
                                      <th class="text-center">CERTIFICATE NAME</th>
                                      <th class="text-center">CERTIFICATE CODE</th>
                                      <th class="text-center">ISSUE DATE</th>
                                      <th class="text-center">NO EXPIRED</th>
                                      <th class="text-center">EXPIRY DATE</th>
                                      <th class="text-center">REVOKED DATE</th>
                                      <th class="text-center">RENEWED DATE</th>
                                      <th class="text-center">NEXT SURVEY DATE</th>
                                      <th class="text-center">DESCRIPTION</th>
                                      <th class="text-center">REMINDER STATUS</th>
                                      <th class="text-center">REMINDER IN</th>
                                      <th class="text-center">CREATE DATE</th>
                                      <th class="text-center">CREATE BY</th>
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
            <form action="" method="post" id="certificateForm" enctype="multipart/form-data">
              <input type="hidden" value="" name="Id" id="Id">
              <div class="form-group row border-bottom">
                <label class="col-sm-12 mb-2 col-form-label">KETERANGAN SERTIFIKAT</label>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="DeptID" id="DeptID" class="form-control">
                    <option value="" <?= empty($DEPTID) ? 'selected' : '' ?>>-- Pilih --</option>
                    <?php foreach ($DeptList as $dept): ?>
                      <option value="<?= $dept->DEPTID; ?>" <?= (!empty($DEPTID) && $DEPTID == $dept->DEPTID) ? 'selected' : '' ?>>
                        <?= strtoupper($dept->DEPTNAME); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Certificate Name</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CertificateName" id="CertificateName" class="form-control text-uppercase" maxlength="150" placeholder="Nama dari sertifikat">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Certificate Number</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="text" name="CertificateCode" id="CertificateCode" class="form-control text-uppercase" maxlength="150" placeholder="Nomor dari sertifikat">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Certificate Status</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="CertificateStatus" id="CertificateStatus" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="Active">Active</option>
                    <option value="Expired">Expired</option>
                    <option value="Revoked">Revoked</option>
                    <option value="Renewed">Renewed</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Issue Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="IssueDate" name="IssueDate" class="form-control" placeholder="Contoh: 18-08-2025">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Expiry Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="ExpiryDate" name="ExpiryDate" class="form-control" placeholder="Contoh: 18-08-2026">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-6 col-form-label"></label>
                <label class="col-sm-2 col-form-label">No Expired</label>
                <div class="col-sm-4 form-error">
                  <input type="hidden" name="NoExpire" value="off">
                  <input type="checkbox" id="NoExpire" name="NoExpire" value="on" onclick="toggleExpiryDate(this)">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Revoked Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="RevokedDate" name="RevokedDate" class="form-control" placeholder="Contoh: 19-08-2026">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Renewed Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="RenewedDate" name="RenewedDate" class="form-control" placeholder="Contoh: 20-08-2026">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Next Survey Date</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="date" id="NextSurvDue" name="NextSurvDue" class="form-control" placeholder="Contoh: 19-08-2026">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Files</label>
                <div class="col-sm-4 form-error mb-2">
                  <input type="file" name="Files" class="form-control" placeholder="Contoh: Files" autocomplete="off">
                  <span class="help-block"></span>
                  <div class="mt-2 mb-2" id="ShowDrawing"></div>
                </div>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Deskripsi</label>
                <div class="col-sm-10 form-error mb-2">
                  <textarea name="Description" id="Description" rows="5" class="form-control" placeholder="Deskripsi tambahan jika diperlukan"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row mb-1 border-top border-bottom mt-4">
                <label class="col-sm-4 col-form-label">SET NOTIFIKASI</label>
              </div>
              <div class="form-group row mb-1">
                <label class="col-sm-2 col-form-label">Reminder Status</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="ReminderStatus" id="ReminderStatus" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="Enabled">Enabled</option>
                    <option value="Disabled">Disabled</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Reminder In</label>
                <div class="col-sm-4 form-error mb-2">
                  <select name="ReminderIn" id="ReminderIn" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="1 month">1 month</option>
                    <option value="2 month">2 month</option>
                    <option value="3 month">3 month</option>
                    <option value="4 month">4 month</option>
                    <option value="5 month">5 month</option>
                    <option value="6 month">6 month</option>
                    <option value="7 month">7 month</option>
                    <option value="8 month">8 month</option>
                    <option value="9 month">9 month</option>
                    <option value="10 month">10 month</option>
                    <option value="">None</option>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <div id="loading" class="loading">Loading&#8230;</div>
    <script type="text/javascript">
      $(function() {

        var start = moment().startOf('month');
        var end   = moment();

        function cb(start, end) {
          var sd = start.format('YYYY-MM-DD');
          var ed = end.format('YYYY-MM-DD');

          $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
          $('#StartDateShow').val(start.format('YYYY-MM-DD'));
          $('#EndDateShow').val(end.format('YYYY-MM-DD'));
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

      function toggleExpiryDate(checkbox) 
      {
        const expiryDate = document.getElementById('ExpiryDate');
        const container  = expiryDate.parentElement;
        const helpBlock  = container.querySelector('.help-block');

        if (checkbox.checked) {
          // Kosongkan & disable input
          expiryDate.value    = '';
          expiryDate.disabled = true;

          // Hapus class has-error
          container.classList.remove('has-error');

          // Kosongkan pesan error
          if (helpBlock) {
            helpBlock.textContent = '';
          }
        } else {
          expiryDate.disabled = false;
        }
      }

      //FUNCTION OPEN MODAL
      function openModal() 
      {
        save_method = 'add';
        $("#pass_div").show();
        $('#btnSave').text('Save');
        $('#certificateForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modalForm').modal('show');
        $('#modalForm .modal-title').text('Tambah Data');
        $('#Description').summernote('reset');
      }

      //FUNCTION RESET
      function reset_all() 
      {
        $('#certificateForm')[0].reset();
        $('#modalForm').modal('hide');
        $('#modalForm .modal-title').text('Tambah Data');
        $('#Description').summernote('reset'); 
      }

      //SAVE
      function save() 
      {
        var form      = $('#certificateForm')[0];
        var form_data = new FormData(form);

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>certificates/certificates_add";
        } else {
          url = "<?php echo base_url(); ?>certificates/certificates_update";
        }

        $.ajax({
          url: url,
          dataType: 'JSON',
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modalForm').modal('hide');
              $('#certificateForm')[0].reset();
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
              // Reset semua error sebelum validasi baru
              $('.form-error').removeClass('has-error');
              $('.help-block').remove();

              for (var i = 0; i < data.inputerror.length; i++) {
                var inputName = data.inputerror[i];
                var errorMsg  = data.error_string[i];

                var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                var inputElem;

                if (arrayMatch) {
                    var arrayName  = arrayMatch[1];
                    var arrayIndex = parseInt(arrayMatch[2]);
                    inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                } else {
                    inputElem = $('[name="' + inputName + '"]');
                }

                // ✅ cek apakah element ditemukan
                if (inputElem.length > 0) {
                    inputElem.closest('.form-error').addClass('has-error');

                    if (inputElem.hasClass('select2-hidden-accessible')) {
                        var select2Container = inputElem.next('.select2');
                        if (select2Container.next('.help-block').length === 0) {
                            select2Container.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    } else {
                        if (inputElem.next('.help-block').length === 0) {
                            inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    }
                }
              }
            }

            if(save_method == 'add') {
              $("#btnSave").text('Save');
            } else {
              $("#btnSave").text('Update');
            }

            $('#Description').summernote('reset');
            $("#btnSave").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save');
            $('#btnSave').prop('disabled', false);
          }
        });
      }

      //FUNCTION EDIT
      function edit(Id) 
      {
        save_method = 'update';
        $('#certificateForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>certificates/certificates_edit",
          type: "POST",
          dataType: "JSON",
          data: {
            Kode: Id
          },
          success: function(data) {
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {

              $('[name="Id"]').val(data.data.Id);
              $('[name="DeptID"]').val(data.data.DeptID);
              $('[name="CertificateName"]').val(data.data.CertificateName);
              $('[name="CertificateCode"]').val(data.data.CertificateCode);
              $('[name="CertificateStatus"]').val(data.data.Status);
              $('[name="IssueDate"]').val(data.data.IssueDate);
              $('[name="ExpiryDate"]').val(data.data.ExpiryDate);
              $('[name="RevokedDate"]').val(data.data.RevokedDate);
              $('[name="RenewedDate"]').val(data.data.RenewedDate);
              $('[name="NextSurvDue"]').val(data.data.NextSurvDue);
              $('[name="ReminderStatus"]').val(data.data.ReminderStatus);
              $('[name="ReminderIn"]').val(data.data.ReminderIn);
              $('#Description').summernote('code', data.data.Description);

              if (data.data.NoExpired == 'on') {
                // centang checkbox
                $('#NoExpire').prop('checked', true);
                $('#ExpiryDate').prop('disabled', true);
              } else {
                // uncheck checkbox
                $('#NoExpire').prop('checked', false);
                $('#ExpiryDate').prop('disabled', false);
              }

              if (data.data.ReminderStatus == 'Disabled') {
                $("#ReminderIn").val('');
              }
              
              // Cek apakah ada file PDF
              if (data.data.Files) {
                var timestamp = new Date().getTime(); // waktu saat ini
                var embedHtml = `<embed src="<?php echo base_url(); ?>files/uploads/sertifikat/${data.data.Files}?t=${timestamp}" type="application/pdf" width="100%" height="100px" />`;
                $('#ShowDrawing').html(embedHtml);
              } else {
                $('#ShowDrawing').html('<p class="text-danger">Tidak ada file terlampir.</p>');
              }

              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Data');
              $('#btnSave').text('Update');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCITON HAPUS
      function hapus(Id)
      {
        Swal.fire({
          title: "Yakin ingin hapus?",
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, hapus",
          cancelButtonText: "Batal"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?php echo base_url(); ?>certificates/certificates_deleted",
              type: "POST",
              dataType: "JSON",
              data: {
                Kode: Id
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                if (data.status == 'forbidden') {
                  $("#loading").hide();
                  Swal.fire('FORBIDDEN', 'Access Denied', 'info');
                } else {
                  $("#loading").hide();
                  reload_table();
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $("#loading").hide();
                alert('Error hapus data');
              }
            });
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
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A2',
              exportOptions: {
                stripHtml: true,
                columns: [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
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
                    text: 'LAPORAN DAFTAR SERTIFIKAT PERIODE ' + formattedStart + ' s/d ' + formattedEnd,
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

                return 'LAPORAN DAFTAR SERTIFIKAT PERIODE ' + formattedStart + ' s/d ' + formattedEnd;
              }
            }
          ],
          select: {
            style: 'single'
          },
          "pagingType": "full_numbers",
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
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
            "url": "<?php echo base_url(); ?>certificates/certificates_list",
            "type": "POST",
            "data": function(data) {
              data.StartDate   = $('#StartDateShow').val();
              data.EndDate     = $('#EndDateShow').val();
              data.DeptID      = $('#DeptShow').val();
            }
          },
          fixedColumns: {
            left: 3
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "100px" },
            { "DOCUMENT": "DOCUMENT" , "sClass": "text-center", "width": "100px" },
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "80px" },
            { "DEPARTEMEN": "DEPARTEMEN" , "sClass": "text-center", "width": "50px"},
            { "CERTIFICATE NAME": "CERTIFICATE NAME" , "sClass": "text-left", "width": "50px" },
            { "CERTIFICATE CODE": "CERTIFICATE CODE" , "sClass": "text-left", "width": "50px" },
            { "ISSUE DATE": "ISSUE DATE" , "sClass": "text-center", "width": "80px" },
            { "NO EXPIRED": "NO EXPIRED" , "sClass": "text-center", "width": "80px" },
            { "EXPIRY DATE": "EXPIRY DATE" , "sClass": "text-center", "width": "80px" },
            { "REVOKED DATE": "REVOKED DATE" , "sClass": "text-center", "width": "80px" },
            { "RENEWED DATE": "RENEWED DATE" , "sClass": "text-center", "width": "80px" },
            { "NEXT SURVEY DATE": "NEXT SURVEY DATE" , "sClass": "text-center", "width": "80px" },
            { "DESCRIPTION": "DESCRIPTION" , "sClass": "text-left", "width": "180px" },
            { "REMINDER STATUS": "REMINDER STATUS" , "sClass": "text-center", "width": "100px" },
            { "REMINDER IN": "REMINDER IN" , "sClass": "text-center", "width": "100px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-left", "width": "100px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" }
          ],
          // columnDefs: [
          //   {
          //     targets: 1,
          //     render: function (data, type, row) {
          //       if (typeof data === 'string') {
          //         return data.replace('Baru', '').trim();
          //       }
          //       return data;
          //     }
          //   }
          // ]
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

        $("#DeptID, #CertificateName, #CertificateCode, #CertificateStatus, #IssueDate, #ExpiryDate, #ReminderStatus, #ReminderIn").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Description').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });

        $('#Description').summernote({
          placeholder: 'Deskripsi tambahan jika diperlukan',
          tabsize: 2,
          height: 100,
          toolbar: [
            // hilangkan 'insert' → 'picture'
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']] // tanpa 'picture'
          ],
          callbacks: {
            onInit: function() {
              $('#Description').next('.note-editor').find('.note-editable').addClass('text-capitalize');
            }
          }
        });
      });
    </script>
  </body>
</html>