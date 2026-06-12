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
                                  <button class="btn btn-info" onclick="openModalLine();">TAMBAH LINE</button>
                                  <!-- <button class="btn btn-info" onclick="openModal();">TAMBAH PROSES</button> -->
                                </span>
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="4%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <!-- <th class="text-center" width="5%">STATUS</th> -->
                                      <!-- <th class="text-center" width="5%">DEPT ID</th> -->
                                      <th class="text-center" width="7%">DEPT NAME</th>
                                      <!-- <th class="text-center" width="15%">PROCESS NAME</th> -->
                                      <th class="text-center" width="7%">LINE NAME</th>
                                      <th class="text-center" width="10%">CREATED DATE</th>
                                      <th class="text-center" width="10%">CREATED BY</th>
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

    <!-- MODAL PROSES -->
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
            <form id="RegisterValidation">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4">
                  <select name="DeptID" id="DeptID" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($DeptList as $value): ?>
                      <option value="<?= $value->DEPTID; ?>">
                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4">
                  <select name="isActive" id="isActive" class="form-control">
                    <option value="" selected readonly>-- Pilih --</option>
                    <option value="Y">Aktif</option>
                    <option value="N">Non Aktif</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Process Name</label>
                <div class="col-sm-10">
                  <input type="text" name="ProcessName" id="ProcessName" class="form-control text-uppercase" maxlength="75" required="required" autocomplete="off" placeholder="Contoh: KUPAS JAKET, PIN dll.">
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

    <!-- MODAL LINE -->
    <div class="modal fade" id="modalLine" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Modal title</h4>
            <button type="button" class="close" aria-label="Close" onclick="resetLine()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formLine">
              <input type="hidden" value="" name="kode">
              <input type="hidden" value="" name="kodeFirst">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Departemen</label>
                <div class="col-sm-4 form-error">
                  <select name="DeptIDLine" id="DeptIDLine" class="form-control" onchange="get_proses_produksi(this);">
                    <option value="" selected>-- Pilih --</option>
                    <?php foreach ($DeptList as $value): ?>
                      <option value="<?= $value->DEPTID; ?>">
                        <?= htmlspecialchars($value->DEPTNAME, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="help-block"></span>
                </div>
                <!-- <label class="col-sm-2 col-form-label">Proses Name</label>
                <div class="col-sm-4 form-error">
                  <select name="ProcessNameLine" id="ProcessNameLine" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                  </select>
                  <span class="help-block"></span>
                </div> -->
              </div>
              <div class="form-group row mb-1 border-top border-bottom">
                <label class="col-sm-4 col-form-label">LINE(S)</label>
              </div>
              <div id="jumlahContainer">
                <div class="form-group row mb-2 mt-2" id="jumlahRow1">
                  <div class="col-md-10 form-error mb-1">
                    <label class="col-form-label">Line</label>
                    <input type="text" name="LineName[]" maxlength="150" class="form-control text-capitalize" required placeholder="Contoh: Line Nomor 1 dst." autocomplete="off" data-required="true">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-md-2 button-center">
                    <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" onclick="resetLine()">Close</button>
            <button id="btnSaveLine" type="button" onclick="saveLine();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
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

      function openModalLine() {
        save_method = 'add';
        $('#btnSaveLine').text('Save');
        $('#formLine')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modalLine').modal('show');
        $('#modalLine .modal-title').text('Tambah Line');
      }

      function resetLine() {
        $('#modalLine').modal('hide');
        $('#formLine')[0].reset();
        $('#formLine .modal-title').text('Tambah Line');

        $('#jumlahContainer').html(`
          <div class="form-group row mb-2 mt-2" id="jumlahRow1">
            <div class="col-md-10 form-error mb-1">
              <label class="col-form-label">Line</label>
              <input type="text" name="LineName[]" maxlength="150" class="form-control text-capitalize" required placeholder="Contoh: Line Nomor 1 dst." autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      function editLine(Id) {
        save_method = 'update';
        $('#formLine')[0].reset();
        // $('.form-group').removeClass('has-error');
        // $('.help-block').empty();

        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>proses_produksi/line_edit",
          type: "POST",
          dataType: "JSON",
          data: {
            Kode: Id
          },
          success: function(data) {
            if (data.status_code && data.status_code != 200 || data.status == 'error') {
              Swal.fire(
                capitalizeFirstLetter(data.status),
                data.message || 'Terjadi kesalahan saat mengambil data atau data tidak ditemukan.',
                'error'
              );
              return; // Hentikan eksekusi lebih lanjut
            }

            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {

              var html      = '';
              var html2     = '';
              //var DeptID    = data.second[0].DeptID;
              //var IdDetail  = data.second[0].Id;

              $('[name="kodeFirst"]').val(data.second[0].Id);
              $('[name="DeptIDLine"]').val(data.second[0].DeptID);
              //$('[name="ProcessNameLine"]').val(data.first.ProcessName);
              $('#modalLine').modal('show');
              $('.modal-title').text('Edit Data');
              $('#btnSave').text('Update');

              //get_proses_produksi(DeptID, IdDetail);
              
              data.second.forEach((item, index) => {
                let rowNumber = index + 1;
                html += `
                  <div class="form-group row mb-2 mt-2" id="jumlahRow${rowNumber}">
                    <div class="col-md-10 form-error mb-1">
                      <label class="col-form-label">Line</label>
                      <input type="text" name="LineName[]" value="${item.LineName}" maxlength="150" class="form-control text-capitalize" required placeholder="Contoh: Line Nomor 1 dst." autocomplete="off">
                      <input type="hidden" name="kodeSecond[]" value="${item.Id}">
                    </div>
                    <div class="col-md-2 button-center">
                      ${rowNumber == 1 
                        ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                        : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRow('jumlahRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>` //onclick="$('#jumlahRow${rowNumber}').remove()"
                      }
                    </div>
                  </div>
                `;
              });

              $('#jumlahContainer').html(html);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCITON HAPUS SINGLE ROW
      function hapusRow(rowId)
      {
        const row        = $('#' + rowId);
        const IdHeader   = $('input[name="kodeFirst"]').val();
        const IdDetail   = row.find('input[name="kodeSecond[]"]').val();

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
              url: "<?php echo base_url(); ?>proses_produksi/hapus_single_row",
              type: "POST",
              dataType: "JSON",
              data: {
                IdHD: IdHeader,
                IdDt: IdDetail
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
                  //editLine(IdDetail);
                  reload_table();
                  // Hapus elemen
                  row.remove();
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

      // TAMBAH KOLOM JUMLAH
      $(document).on('click', '#plus1', function () {
        let count = $('#jumlahContainer .form-group').length + 1;
        let row = `
          <div class="form-group row mb-2 mt-2" id="jumlahRow${count}">
            <div class="col-md-10 form-error mb-1">
              <label class="col-form-label">Line ${count}</label>
              <input type="text" name="LineName[]" maxlength="150" class="form-control text-capitalize" required placeholder="Contoh: Line Nomor ${count} dst." autocomplete="off">
            </div>
            <div class="col-md-2 button-center">
              <a href="javascript:void(0)" class="btn btn-danger text-bottom remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
            </div>
          </div>
          `;
        $('#jumlahContainer').append(row);
      });

      // HAPUS KOLOM JUMLAH
      $(document).on('click', '.remove-kolom-jumlah', function () {
        $(this).closest('.form-group').remove();
      });

      //FUNCTION OPEN MODAL PROSES
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Data');
      }

      function closeModal() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Data');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Data');
      }

      function get_proses_produksi(el, defaultValue = null, IdDetail) 
      {
        console.log(IdDetail)
        $.ajax({
          url : "<?php echo base_url();?>proses_produksi/get_proses_produksi",
          method : "POST",
          data : {id: (typeof el === "object" ? el.value : el)},
          dataType : 'json',
          success: function(data){
            var html = '<option value="">-- Pilih --</option>';
            for (var i = 0; i < data.length; i++) {
              // cek jika defaultValue sama dengan SSN maka tambahkan selected
              let selected = (defaultValue && data[i].Id == defaultValue) ? ' selected' : '';
              html += '<option value="'+ data[i].Id +'"'+selected+'>'+ data[i].ProcessName.toUpperCase() +'</option>';
            }
            if (typeof el === "object") {
              // jika dipanggil dari select onchange
              $(el).closest('.form-group.row').find('select[name="ProcessNameLine"]').html(html);
            } else {
              // jika dipanggil dari ajax dengan UserID langsung
              $('#ProcessNameLine').html(html);
            }
          }
        });
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>proses_produksi/proses_edit/" + id,
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
              $('[name="DeptID"]').val(data.DeptID);
              $('[name="isActive"]').val(data.Status);
              $('[name="ProcessName"]').val(data.ProcessName);
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
              url: '<?php echo base_url(); ?>proses_produksi/proses_deleted/' + id,
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

      //VALIDATION
      function save() {
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>proses_produksi/proses_add";
        } else {
          url = "<?php echo base_url(); ?>proses_produksi/proses_update";
        }

        var data_save = $('#RegisterValidation').serializeArray();

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

      function saveLine() {
        // 1. Ubah tombol menjadi loading
        $("#btnSaveLine").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSaveLine').attr('disabled', true); 

        // 2. Ambil data form
        var data_save = $('#formLine').serializeArray();

        // 3. Eksekusi AJAX
        $.ajax({
          url: "<?php echo base_url(); ?>proses_produksi/line_add",
          type: "POST",
          data: data_save,
          dataType: "JSON",
          success: function(data) {
            if (data.status == 'success') {
              resetLine();
              reload_table();
            } else if (data.status == 'forbidden') {
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else if (data.status == 'error') {
              Swal.fire(capitalizeFirstLetter(data.status), 'Error', 'error');
            } else {
                // JIKA ADA ERROR VALIDASI
                $("#loading").hide();

                // Loop semua error dari server
                for (var i = 0; i < data.inputerror.length; i++) {
                    var inputName = data.inputerror[i]; // Misal: DeptIDLine atau LineName[0]
                    var errorMsg  = data.error_string[i]; // Pesan error

                    // Deteksi apakah input berupa array (misal LineName[])
                    var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                    var inputElem;

                    if (arrayMatch) {
                        // Jika array (Line 1, Line 2, dst)
                        var arrayName  = arrayMatch[1];
                        var arrayIndex = parseInt(arrayMatch[2]);
                        inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                    } else {
                        // Jika input biasa (Departemen, Proses Name)
                        inputElem = $('[name="' + inputName + '"]');
                    }

                    // --- BAGIAN INI YANG DIPERBAIKI ---
                    if (inputElem.length > 0) {
                        // 1. Tambahkan warna merah ke parent div (class: form-error)
                        inputElem.closest('.form-error').addClass('has-error');

                        // 2. Tentukan elemen acuan untuk menaruh teks error
                        // (Handle khusus jika pakai plugin Select2)
                        var targetElem = inputElem;
                        if (inputElem.hasClass('select2-hidden-accessible')) {
                            targetElem = inputElem.next('.select2');
                        }

                        // 3. Cek apakah help-block sudah ada di HTML?
                        var helpBlock = targetElem.next('.help-block');

                        if (helpBlock.length > 0) {
                            // KASUS ANDA: Tag sudah ada tapi kosong. Kita isi teksnya.
                            helpBlock.text(errorMsg).addClass('text-danger');
                        } else {
                            // Jika tag belum ada, kita buat baru.
                            targetElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    }
                }
            }
            
            // Kembalikan tombol ke kondisi semula
            $('#btnSaveLine').text('Save'); 
            $('#btnSaveLine').attr('disabled', false); 
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
            $('#btnSaveLine').text('Save'); 
            $('#btnSaveLine').attr('disabled', false); 
          }
        });
      }

      $(document).ready(function() {
        $("#loading").hide();

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
          "serverSide": false, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>proses_produksi/proses_list",
            "type": "POST",
          },
          fixedColumns: {
            left: 3
          },
          select: {
            style: 'single'
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            // { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px"},
            // { "DEPT ID": "DEPT ID" , "sClass": "text-center", "width": "50px" },
            { "DEPT NAME": "DEPT NAME" , "sClass": "text-left", "width": "50px" },
            // { "PROCESS NAME": "PROCESS NAME" , "sClass": "text-left", "width": "50px" },
            { "LINE NAME": "LINE NAME" , "sClass": "text-left", "width": "50px" },
            { "CREATED DATE": "CREATED DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATED BY": "CREATED BY" , "sClass": "text-center", "width": "80px" }
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

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        }

        $('#jumlahContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        $("#DeptIDLine, #isActive, #ProcessNameLine").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>