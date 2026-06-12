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
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter</label>
                                  <div class="col-md-4 col-sm-12 m-t-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <div class="input-group-text bg-primary border-primary text-white">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-1 col-sm-12 m-t-10">
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="example" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead>
                                  <tr class="bg-primary text-white">
                                    <th class="text-white text-center" width="2%">
                                      <button type="button" id="ProsesButton" class="btn btn-warning" onclick="proses_po()" disabled="disabled">PROSES</button>
                                    </th>
                                    <th class="text-white text-center" width="2%">No</th>
                                    <th class="text-white text-center" width="2%">#</th>
                                    <th class="text-white text-center" width="7%">No PO</th>
                                    <th class="text-white text-center" width="5%">Cetak</th>
                                    <th class="text-white text-center" width="6%">Supp. ID</th>
                                    <th class="text-white text-center" width="5%">Type</th>
                                    <th class="text-white text-center" width="20%">Supp. Name</th>
                                    <th class="text-white text-center" width="10%">Part ID</th>
                                    <th class="text-white text-center" width="25%">Part Name</th>
                                    <th class="text-white text-center" width="10%">Create Date</th>
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
    <div class="modal fade" id="modalQty" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Input Quantity Cetak</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" class="container" method="post" id="registerForm">
              <div class="form-group row mb-3">
                <div class="col-md-8"></div>
                <label class="col-md-1 col-form-label text-end">Bulan</label>
                <div class="col-md-3">
                  <select name="Months" id="Months" class="form-control">
                    <option value="00" disabled>-- Pilih --</option>
                    <?php
                      $now  = new DateTime('now');
                      $bln1 = $now->format('m');
                      for ($m = 1; $m <= 12; ++$m) {
                        $value = strlen($m) == 1 ? '0'.$m : $m;
                        if ($bln1 == $m) {
                          echo '<option selected value="'.$value.'">'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
                        } else {
                          echo '<option value="'.$value.'">'.date('F', mktime(0, 0, 0, $m, 1)).'</option>'."\n";
                        }
                      }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group row mb-3">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" width="100%">
                    <thead>
                      <tr class="bg-primary text-white">
                        <th class="text-center" width="5%">No</th>
                        <th class="text-center" width="20%">PO Number</th>
                        <th class="text-center">Supplier & Part Name</th>
                        <th class="text-center" width="30%">Berat Bersih & Lot Part</th>
                      </tr>
                    </thead>
                    <tbody id="isi_data_po">
                      <tr>
                        <td colspan="3" class="text-center">Data tidak ditemukan</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </form>
            <!--end col-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="btnSave" type="button" onclick="save_proses_cetak();" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
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

      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      //FUNCTION PROSES PO
      function proses_po() {
        let PONumberArray    = [];
        $("input:checkbox[name=PoNumber]:checked").each(function(){
          PONumberArray.push($(this).val());
        });

        let form_data = {
          "PONumber": PONumberArray
        }

        $.ajax({
          url: "<?php echo base_url(); ?>incoming/show_qty_cetak",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();
            $('#modalQty').modal('show');
            $("#isi_data_po").html(data.html);
          }, 
          error: function() {
            alert('Oops error ketika proses data group');
          }
        });
      };

      //FUNCTION HAPUS ALL FIFO CARD
      function openModalDelete(id, PartIDs) {
      	Swal.fire({
				  title: 'Apakah anda yakin?',
				  text: "Data yang dihapus tidak bisa dikembalikan!",
				  icon: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, hapus',
				  cancelButtonText: 'Tidak, Batal'
				}).then((result) => {
				  if (result.isConfirmed) {
				    $.ajax({
              url: '<?php echo base_url(); ?>incoming/hapus_semua_fifo_card',
              type: 'POST',
              data: {
                PONumber: id,
                PartID: PartIDs
              },
              beforeSend: function() {
                $("#loading").show();
              },
              success: function(data) {
                var result = JSON.parse(data);
                if (result.status == 'forbidden'){
                  Swal.fire(
                    'FORBIDDEN',
                    'Access Denied',
                    'info',
                  )
                } else {
                  //$("#"+id).remove();
                  reload_table();
                }
                $("#loading").hide();
              },
              error: function() {
                alert('Something is wrong');
              }
            });
				  }
				})
      };

      function save_proses_cetak() 
      {
        var formData = {
          Months: $("#Months").val(),
          Data: []
        };

        $('tr[data-group]').each(function() {
          var $group = $(this);

          var NomorPO      = $group.find('input[name="NomorPO[]"]').val();
          var PartID       = $group.find('input[name="PartID[]"]').val();
          var PartName     = $group.find('input[name="PartName[]"]').val();
          var SupplierID   = $group.find('input[name="SupplierID[]"]').val();
          var SupplierType = $group.find('input[name="SupplierType[]"]').val();
          var SupplierName = $group.find('input[name="SupplierName[]"]').val();

          // Dapatkan banyak LotNumber dan Berat di dalam grup ini
          var LotNumbers = $group.find('input[name="LotNumber[]"]').map(function() {
            return $(this).val();
          }).get();

          var Berats = $group.find('input[name="Berat[]"]').map(function() {
            return $(this).val();
          }).get();

          formData.Data.push({
            NomorPO: NomorPO,
            PartID: PartID,
            PartName: PartName,
            SupplierID: SupplierID,
            SupplierType: SupplierType,
            SupplierName: SupplierName,
            LotNumber: LotNumbers,
            Berat: Berats
          });
        });

        //console.log(formData);

        $.ajax({
          url: "<?php echo base_url(); ?>incoming/saving_qty_cetak",
          dataType: 'JSON',
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data: formData,
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();
            if (data.status_code == 200) {
              let isi = {
                "PONumber": data.PONumber,
                "PartID": data.PartID,
                "Date": data.Date
              };
              localStorage.removeItem("FIFO_CARD");
              localStorage.setItem("FIFO_CARD", JSON.stringify(isi));
              reload_table();
              openInNewTab(data.Url);
            } else {
              Swal.fire({
                title: capitalizeFirstLetter(data.status),
                text: data.message,
                icon: "info"
              });
            }
          }, 
          error: function() {
            $("#loading").hide();
            alert('Oops error ketika proses data group');
          }
        });
      }

      $(document).ready(function() {
        $("#loading").hide();

        //CHECKBOX ENABLE AND DISBALE
        var counterChecked = 0;
        $('body').on('change', 'input[type="checkbox"]', function() {
          this.checked ? counterChecked++ : counterChecked--;
          counterChecked > 0 ? $('#ProsesButton').prop("disabled", false): $('#ProsesButton').prop("disabled", true);
        });
        //CHECKBOX ENABLE AND DISBALE

        table = $('#example').DataTable({
          dom: 'Bfrltip',
          buttons: [
            'excel'
          ],
          fixedColumns: {
            left: 2
          },
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
          "processing": true,
          "serverSide": true,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>incoming/incoming_part_list",
            "type": "POST",
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
            }
          },

          "aoColumns": [
            { "#": "#" , "sClass": "text-center"},
            { "No": "No" , "sClass": "text-end"},
            { "#": "#" , "sClass": "text-center"},
            { "No Bukti": "No Bukti" , "sClass": "text-start" },
            { "Qty. Cetak": "Qty. Cetak" , "sClass": "text-end" },
            { "Supplier ID": "Supplier ID" , "sClass": "text-center" },
            { "Type": "Supplier ID" , "sClass": "text-center" },
            { "Supplier Name": "Supplier Name" , "sClass": "text-start" },
            { "Part ID": "Part ID" , "sClass": "text-start" },
            { "Part Name": "Part Name" , "sClass": "text-start" },
            { "Create Date": "Create Date" , "sClass": "text-center" }
          ],

          'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function(data, type, full, meta) {
              let QtyCetak = parseFloat(full[4]);

              return '<input type="checkbox" id="PoNumber_'+ full[1] +'" name="PoNumber" class="myCheckBox form-check-input" value="' + $('<div/>').text(data).html() + '">';
            }
          }],

          // 'select': {
          //   'style': 'multi'
          // },

          'order': [
            [1, 'asc']
          ]
        });

        function formatNumber(n) {
          return n.toLocaleString(); // or whatever you prefer here
        };

        // Handle click on "Select all" control
        $('#example-select-all').on('click', function() {
          var rows = table.rows({
            'search': 'applied'
          }).nodes();
          $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle click on checkbox to set state of "Select all" control
        $('#example tbody').on('change', 'input[type="checkbox"]', function() {
          if (!this.checked) {
            var el = $('#example-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
              el.indeterminate = true;
            }
          }
        });

        $('#frm-example').on('submit', function(e) {
          var form = this;
          e.preventDefault();

          table.$('input[type="checkbox"]').each(function() {
            if (!$.contains(document, this)) {
              if (this.checked) {
                $(form).append(
                  $('<input>')
                  .attr('type', 'hidden')
                  .attr('name', this.name)
                  .val(this.value)
                );
              }
            }
          });

          // FOR TESTING ONLY

          // Output form data to a console
          $('#example-console').text($(form).serialize());
          //console.log("Form submission", $(form).serialize());
          var data_array = table.$('input[type="checkbox"]').serializeArray();
          if (data_array.length > 0) {
            localStorage.setItem("data_scan_id", JSON.stringify(data_array));
            $('#modal_ng_all').modal('show');
          } else {
            alert("Silahkan pilih data dahulu");
            return false;
          }

          // Prevent actual form submission
          e.preventDefault();
        });

        $('#modalQty').on('shown.bs.modal', function () {
          $(".decimalInput").on("input", function () {
            let value = $(this).val();
            // Allow only numbers and commas
            let cleanedValue = value.replace(/[^0-9,]/g, '');
            
            $(this).val(cleanedValue);
          });

          $('.commaInput').on('input', function() {
              // Get the current value of the input
              let inputValue = $(this).val();

              // Use a regular expression to remove non-numeric and non-comma characters
              let cleanedValue = inputValue.replace(/[^0-9,]/g, '');

              // Ensure commas are used correctly (e.g., no leading or multiple commas)
              cleanedValue = cleanedValue.replace(/^,+/g, ''); // Remove leading commas
              cleanedValue = cleanedValue.replace(/,+/g, ','); // Replace multiple commas with a single comma

              // Update the input value with the cleaned value
              $(this).val(cleanedValue);
          });
        });
      });
    </script>
    <script>
      $(document).on('click', '[data-add-btn]', function () {
        const $wrapper = $(this).closest('[data-x-wrapper]');
        const $group = $(this).closest('[data-x-group]');

        const $clone = $group.clone();
        $clone.find('input').val(''); // kosongkan input

        // Ganti tombol '+' jadi '-'
        $clone.find('[data-add-btn]').remove();
        $clone.find('.input-group-append').html(`
          <button type="button" class="btn btn-danger" data-remove-btn title="Hapus kolom">-</button>
        `);

        $wrapper.append($clone);
      });

      $(document).on('click', '[data-remove-btn]', function () {
        const $wrapper = $(this).closest('[data-x-wrapper]');
        const $group = $(this).closest('[data-x-group]');

        if ($wrapper.find('[data-x-group]').length > 1) {
          $group.remove();
        }
      });
    </script>
  </body>
</html>