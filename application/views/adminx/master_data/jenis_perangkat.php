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
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                                <span class="pull-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </span>
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="100%">
                                  <thead class="bg-primary text-white">
                                    <tr>
                                      <th class="text-center" width="5%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">STATUS</th>
                                      <th class="text-center" width="20%">NAMA JENIS PERANGKAT</th>
                                      <th class="text-center" width="5%">KATEGORI</th>
                                      <th class="text-center" width="20%">DESKRIPSI</th>
                                      <th class="text-center" width="10%">CREATE DATE</th>
                                      <th class="text-center" width="8%">CREATE BY</th>
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
            <form id="RegisterValidation">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Jenis Perangkat</label>
                <div class="col-sm-10 form-error">
                  <input type="text" name="Nama" id="Nama" placeholder="Contoh: Printer, Laptop" class="form-control text-capitalize" required="required" autocomplete="off">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-4 form-error">
                  <select name="Kategori" id="Kategori" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="IT">IT</option>
                    <option value="Non-IT">Non-IT</option>
                  </select>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4 form-error">
                  <select name="Status" id="Status" class="form-control">
                    <option value="" selected>-- Pilih --</option>
                    <option value="AKTIF">AKTIF</option>
                    <option value="TIDAK">TIDAK</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Deskripsi</label>
                <div class="col-sm-10 form-error">
                  <textarea name="Deskripsi" id="Deskripsi" rows="5" class="form-control" placeholder="Informasi tentang jenis perangkat."></textarea>
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

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      function reset_all() 
      {
        $('#modalForm').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Jenis Perangkat');
      }

      //FUNCTION OPEN MODAL
      function openModal() 
      {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modalForm').modal('show');
        $('.modal-title').text('Tambah Jenis Perangkat')
      }

      //FUNCTION EDIT
      function edit(id) 
      {
        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        $("#pass_div").hide();
        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>jenis_perangkat/jp_edit/" + id,
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            console.log(data);
            if (data.status == 'forbidden') {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            } else {
              $('[name="kode"]').val(data.Id);
              $('[name="Nama"]').val(data.Nama);
              $('[name="Status"]').val(data.Status);
              $('[name="Kategori"]').val(data.Kategori);
              $('[name="Deskripsi"]').val(data.Deskripsi);
              $('#modalForm').modal('show');
              $('.modal-title').text('Edit Jenis Perangkat');
              $('#btnSave').text('Update');
            }

          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION HAPUS
      function openModalDelete(id) 
      {
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
              url: '<?php echo base_url(); ?>jenis_perangkat/jp_deleted/' + id,
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

      //FUNCTION SAVE AND UPDATE
      function save() 
      {
        var form_data = $('#RegisterValidation').serializeArray();

        var url;
        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>jenis_perangkat/jp_add";
        } else {
          url = "<?php echo base_url(); ?>jenis_perangkat/jp_update";
        }

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
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();

            if (data.status == 'success') {
              $("#loading").hide();
              $('#modalForm').modal('hide');
              $('#RegisterValidation')[0].reset();
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
              for (var i = 0; i < data.inputerror.length; i++) {
                var inputName = data.inputerror[i];
                var errorMsg  = data.error_string[i];

                var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                if (arrayMatch) {
                    var arrayName = arrayMatch[1];
                    var arrayIndex = parseInt(arrayMatch[2]);
                    var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                    inputElem.closest('.form-error').addClass('has-error');

                    if (inputElem.hasClass('select2-hidden-accessible')) {
                        var select2Container = inputElem.next('.select2'); // ambil wrapper select2
                        if (select2Container.next('.help-block').length === 0) {
                            select2Container.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    } else {
                        if (inputElem.next('.help-block').length === 0) {
                            inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                        }
                    }
                } else {
                    var inputElem = $('[name="' + inputName + '"]');
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

      //FUNCTION RELOAD TABLE
      function reload_table() 
      {
        table.ajax.reload(null, false);
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
          fixedColumns: {
            left: 4
          },
          "processing": true,
          "serverSide": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>jenis_perangkat/jp_list",
            "type": "POST"
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-right", "width": "50px"},
            { "#": "#" , "sClass": "text-center", "width": "50px"},
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "70px" },
            { "NAMA JENIS PERANGKAT": "NAMA JENIS PERANGKAT" , "sClass": "text-left", "width": "150px" },
            { "KATEGORI": "KATEGORI" , "sClass": "text-center", "width": "50px" },
            { "DESKRIPSI": "DESKRIPSI" , "sClass": "text-left", "width": "250px" },
            { "CREATE DATE": "CREATE DATE" , "sClass": "text-center", "width": "80px" },
            { "CREATE BY": "CREATE BY" , "sClass": "text-center", "width": "80px" }
          ],
          "columnDefs": [
            {
              "targets": [0],
              "orderable": false,
              className: 'text-right'
            } 
          ]
        });

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

        $("#Nama, #Status, #Kategori, #Deskripsi").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#Deskripsi').on('input', function() {
          var val = $(this).val();
          if (val.length > 0) {
            var formatted = val.charAt(0).toUpperCase() + val.slice(1);
            $(this).val(formatted);
          }
        });
      });
    </script>
  </body>
</html>