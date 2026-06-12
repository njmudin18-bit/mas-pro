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
    <?php //$this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/pages/waves/css/waves.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/feather/css/feather.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/icofont/css/icofont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/icon/font-awesome/css/font-awesome.min.css">
    <!-- DATATABLE CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/css/buttons.bootstrap5.css" />
    <!-- DATATABLE FREEZE COLUMN CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/cdn.datatables.net/fixedcolumns/fixedColumns.dataTables.min.css">
    <!-- DATATABLE CHECKBOX CSS -->
    <link type="text/css" href="<?php echo base_url(); ?>assets/cdn.datatables.net/checkboxes/dataTables.checkboxes.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/pages.css">
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
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter by</label>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="tanggal" id="tanggal">
                                      <span class="input-group-append">
                                        <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                                      </span>
                                    </div>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-10">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <form id="frm-example" action="#" method="POST">
                                  <table id="example" class="table table-striped table-bordered table-hover" width="190%">
                                    <thead>
                                      <tr class="bg-primary text-white">
                                        <th class="text-center" width="3%">NO</th>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="15%">PART NAME</th>
                                        <th class="text-center" width="9%">PART ID</th>
                                        <th class="text-center" width="7%">STD. PACKING</th>
                                        <th class="text-center" width="7%">QTY. PECAHAN</th>
                                        <th class="text-center" width="6%">STD. COLLY</th>
                                        <th class="text-center" width="6%">PCH. COLLY</th>
                                        <th class="text-center" width="6%">TOTAL COLLY</th>
                                        <th class="text-center" width="7%">PLANNING KIRIM</th>
                                        <th class="text-center" width="12%">TUJUAN</th>
                                        <th class="text-center" width="12%">NOTE</th>
                                      </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                      <tr class="bg-primary text-white">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center" colspan="5">TOTAL</th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th></th>
                                        <th></th>
                                      </tr>
                                    </tfoot>
                                  </table>
                                </form>
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

    <?php //$this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
    <script src="<?php echo base_url(); ?>assets/code.jquery.com/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/bootstrap/js/bootstrap.min.js"></script>

    <script src="<?php echo base_url(); ?>files/assets/pages/waves/js/waves.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/css-scrollbars.js"></script>

    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/dataTables.buttons.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/buttons.bootstrap5.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/2.2.2/js/buttons.html5.min.js"></script>
    <!-- DATATABLE FREEZE COLUMN -->
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/fixedcolumns/dataTables.fixedColumns.min.js"></script>
    <!-- DATATABALE CHECKBOX JS -->
    <script src="<?php echo base_url(); ?>assets/cdn.datatables.net/checkboxes/dataTables.checkboxes.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>

    <script src="<?php echo base_url(); ?>files/assets/js/pcoded.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/vertical/vertical-layout.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/script.js"></script>
    <script type="text/javascript">
      $(function() {
        $('input[name="tanggal"]').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 2020,
          maxYear: parseInt(moment().format('YYYY'), 10),
          maxDate: moment().endOf('month'),
          startDate: moment(),
          locale: {
            format: 'YYYY-MM-DD'
          }
        }, function(start, end, label) {
          var years = moment().diff(start, 'years');
        });
      });
    </script>
    <script>
      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION UPDATE TERKIRIM ATAU TIDAK
      function update_kirim(Id, Status)
      {
        console.log(Id);
        console.log(Status);
        $.ajax({
          url: "<?php echo base_url(); ?>pengiriman/update_status_kirim",
          dataType: 'JSON',
          data: {
            IdKirim: Id,
            StatusKirim: Status
          },
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();
            reload_table();
          }, 
          error: function() {
            alert('Oops error ketika proses data group');
          }
        });
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      };

      $(document).ready(function() {
        $("#loading").hide();
        //$('#modalQty').modal('show');

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
            { 
              extend: 'excelHtml5',
              text: 'Download data',
              title: '',
              className: 'btn btn-primary'
            }
          ],
          fixedColumns: {
            left: 3
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
          "serverSide": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>pengiriman/pengiriman_harian_data",
            "type": "POST",
            "data": function(data) {
              data.tanggal  = $('#tanggal').val();
            }
          },
          "aoColumns": [
            { "NO": "NO" , "sClass": "text-end"},
            { "#": "#" , "sClass": "text-center"},
            { "PART NAME": "PART NAME" , "sClass": "text-start"},
            { "PART ID": "PART ID" , "sClass": "text-start"},
            { "STD. PACKING": "STD. PACKING" , "sClass": "text-end"},
            { "QTY. PECAHAN": "QTY. PECAHAN" , "sClass": "text-end"},
            { "JLH. STD. COLLY": "JLH. STD. COLLY" , "sClass": "text-end"},
            { "JLH. PECAHAN COLLY": "JLH. PECAHAN COLLY" , "sClass": "text-end"},
            { "TOTAL COLLY": "TOTAL COLLY" , "sClass": "text-end"},
            { "PLANNING KIRIM": "PLANNING KIRIM" , "sClass": "text-end"},
            { "TUJUAN": "TUJUAN" , "sClass": "text-start"},
            { "NOTE": "NOTE" , "sClass": "text-start"}
          ],
          "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // Function to convert values to integers
            var intVal = function(i) {
              return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            var TotalColly = api.column(8).data().reduce(function(a, b) {
              return intVal(a) + intVal(b);
            }, 0);

            var PlanningKirim = api.column(9).data().reduce(function(a, b) {
              return intVal(a) + intVal(b);
            }, 0);

            // Count total rows
            var totalRows = api.rows().count();
            // Update footer with total sum and row count
            $(api.column(8).footer()).html(formatNumber(TotalColly));
            $(api.column(9).footer()).html(formatNumber(PlanningKirim));
          },
          'columnDefs': [
            {
              'targets': [0, 1],
              'orderable': false,
            },
            {
              'targets': 1,
              'searchable': false,
              'orderable': false,
              'className': 'dt-body-center',
              'render': function(data, type, full, meta) {
                let Isi = "'" + full[1] + "', '" + full[12] + "'";
                if (full[12] == 'BELUM') {
                  return '<input type="checkbox" id="IdKirim_'+ full[12] +'" name="IdKirim" onclick="update_kirim(' + Isi + ')" class="myCheckBox" value="' + $('<div/>').text(data).html() + '">';
                } else {
                  return '<input checked type="checkbox" id="IdKirim_'+ full[12] +'" name="IdKirim" onclick="update_kirim(' + Isi + ')" class="myCheckBox" value="' + $('<div/>').text(data).html() + '">';
                }
              }
            }
          ],
          'select': {
            'style': 'multi'
          }
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
      });
    </script>
  </body>
</html>