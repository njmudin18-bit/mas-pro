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
                                  <button class="btn btn-info" onclick="openModal();">TAMBAH</button>
                                </span>
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <table id="order-table" class="table table-striped table-bordered table-hover" width="130%">
                                  <thead class="bg-primary text-center">
                                    <tr>
                                      <th class="text-center" width="4%">NO</th>
                                      <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="5%">PERIODE</th>
                                      <th class="text-center" width="7%">STATUS</th>
                                      <th class="text-center" width="7%">DEDUCTION NAME</th>
                                      <th class="text-center" width="7%">DEDUCTION TYPE</th>
                                      <th class="text-center" width="7%">AMOUNT</th>
                                      <th class="text-center" width="7%">PERCENTAGE</th>
                                      <th class="text-center" width="7%">EFFECTIVE DATE</th>
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
            <button type="button" class="close" aria-label="Close" onclick="reset()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <input type="hidden" value="" name="kode">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Periode</label>
                <div class="col-sm-4">
                  <input type="number" name="Periode" id="Periode" class="form-control" maxlength="4" required="required" autocomplete="off" placeholder="Contoh: 2025">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4">
                  <select name="IsActive" id="IsActive" class="form-control">
                    <option value="" selected readonly>-- Pilih --</option>
                    <option value="A">Aktif</option>
                    <option value="N">Non Aktif</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Deduction Name</label>
                <div class="col-sm-4">
                  <input type="text" name="DeductionName" id="DeductionName" class="form-control text-capitalize" required="required" autocomplete="off" placeholder="Contoh: BPJS Kesehatan">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Deduction Type</label>
                <div class="col-sm-4">
                  <select name="DeductionType" id="DeductionType" class="form-control" onchange="handleDeductionType()">
                    <option value="" selected readonly>-- Pilih --</option>
                    <option value="FIXED">FIXED</option>
                    <option value="PERCENTAGE">PERCENTAGE</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Amount</label>
                <div class="col-sm-4">
                  <input type="text" name="Amount" id="Amount" class="form-control" maxlength="12" required="required" autocomplete="off" placeholder="Contoh: 500.000" oninput="AllowDecimalAndComma(this)" readonly>
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Percentage</label>
                <div class="col-sm-4">
                  <input type="text" id="Percentage" name="Percentage" class="form-control" placeholder="Contoh: 1 dan tanpa %" maxlength="5" autocomplete="off" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Effective Date</label>
                <div class="col-sm-4">
                  <input type="date" name="EffectiveDate" id="EffectiveDate" class="form-control" maxlength="12" required="required" autocomplete="off">
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

      function handleDeductionType() {
        var type = $('#DeductionType').val();

        // reset semua input jadi readonly
        $('#Amount').prop('readonly', true).val('');
        $('#Percentage').prop('readonly', true).val('');

        if (type === 'PERCENTAGE') {
          $('#Percentage').prop('readonly', false);
          $('#Amount').closest('.col-sm-4').removeClass('has-error').find('.help-block').text('');
        } else if (type === 'FIXED') {
          $('#Amount').prop('readonly', false);
          $('#Percentage').closest('.col-sm-4').removeClass('has-error').find('.help-block').text('');
        }
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();
        $('#modal').modal('show');
        $('.modal-title').text('Tambah Potongan Gaji');
      }

      function closeModal() {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Potongan Gaji');
      }

      //FUNCTION RESET
      function reset() {
        $('#modal').modal('hide');
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Potongan Gaji');
      }

      //FUNCTION EDIT
      function edit(id) {
        save_method = 'update';
        $('#RegisterValidation')[0].reset();
        $('.form-group .has-error').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
          url: "<?php echo base_url(); ?>salary_deduction/salary_deduction_edit/" + id,
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
              $('[name="kode"]').val(data.DeductionID);
              $('[name="DeductionName"]').val(data.DeductionName);
              $('[name="DeductionType"]').val(data.DeductionType);
              $('[name="Amount"]').val(formatRupiahDecimal(data.Amount));
              $('[name="Percentage"]').val(data.Percentage);
              $('[name="EffectiveDate"]').val(data.EffectiveDate);
              $('[name="Periode"]').val(data.Period);
              $('[name="IsActive"]').val(data.IsActive);

              if (data.DeductionType == 'PERCENTAGE') {
                $('#Percentage').prop('readonly', false);
                //$('#CustomerNewAddress').prop('disabled', false);
              } else {
                $('#Amount').prop('readonly', false);
                //$('#CustomerNewAddress').prop('disabled', true);
              }

              $('#modal').modal('show');
              $('.modal-title').text('Edit Potongan Gaji');
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
              url: '<?php echo base_url(); ?>salary_deduction/salary_deduction_deleted/' + id,
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

      //VALIDATION AND ADD USER
      function save() {
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
          url = "<?php echo base_url(); ?>salary_deduction/salary_deduction_add";
        } else {
          url = "<?php echo base_url(); ?>salary_deduction/salary_deduction_update";
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
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
          // Load data for the table's content from an Ajax source
          "ajax": {
            "url": "<?php echo base_url(); ?>salary_deduction/salary_deduction_list",
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
            { "PERIODE": "PERIODE" , "sClass": "text-center", "width": "50px" },
            { "STATUS": "STATUS" , "sClass": "text-center", "width": "50px" },
            { "DEDUCTION NAME": "DEDUCTION NAME" , "sClass": "text-left", "width": "50px" },
            { "DEDUCTION TYPE": "DEDUCTION TYPE" , "sClass": "text-left", "width": "50px" },
            { "AMOUNT": "AMOUNT" , "sClass": "text-right", "width": "50px" },
            { "PERCENTAGE": "PERCENTAGE" , "sClass": "text-right", "width": "50px" },
            { "EFFECTIVE DATE": "EFFECTIVE DATE" , "sClass": "text-center", "width": "80px" },
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

        $("#DeductionName, #DeductionType, #Amount, #Percentage, #EffectiveDate, #Periode, #IsActive").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
      });
    </script>
  </body>
</html>