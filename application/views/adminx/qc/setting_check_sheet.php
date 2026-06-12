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

  <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
  <style>
    /* div.dataTables_wrapper div.dataTables_filter {
      float: left;
    } */
  </style>
</head>

<body>

  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>

  <div id="pcoded" class="pcoded iscollapsed">
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
                          <div class="card-block accordion-block color-accordion-block m-t-10 m-b-10">
                            <div class="container">
                              <div class="row">
                                <div class="col-md-2">
                                  <label class="text-left">Part ID</label>
                                </div>
                                <div class="col-md-3">
                                  <label class="text-left">: <?php echo $part_details->PartID; ?></label>
                                </div>
                                <div class="col-md-2">
                                  <label class="text-left">Part Name</label>
                                </div>
                                <div class="col-md-5">
                                  <label class="text-left">: <?php echo $part_details->PartName; ?></label>
                                </div>
                              </div>
                            </div>
                            <hr>
                            <div class="container">
                              <div class="row">
                                <div class="col-md-5">
                                  <h5 class="text-center">Pilihan Variable</h5>
                                  <hr>
                                  <form id="form-pilihan-variable" action="#" method="POST">
                                    <div class="table-responsive">
                                      <button class="btn btn-info">TAMBAH</button>
                                      <input type="hidden" id="part_id" name="part_id" value="<?php echo $part_details->PartID; ?>">
                                      <input type="hidden" id="part_name" name="part_name" value="<?php echo $part_details->PartName; ?>">
                                      <table id="table_variable" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                        <thead>
                                          <tr class="bg-primary">
                                            <th class="text-center" width="5%">
                                              <input name="select_all" value="1" id="variable-select-all" type="checkbox" />
                                            </th>
                                            <th class="text-center" width="5%">No.</th>
                                            <th class="text-center">Item Name</th>
                                            <th class="text-center">Standard</th>
                                            <th class="text-center">Toleransi</th>
                                            <th class="text-center">Tools</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                  </form>
                                </div>
                                <div class="col-md-7">
                                  <h5 class="text-center">Variable Terpilih</h5>
                                  <hr>
                                  <form id="form-variable-terpilih" action="#" method="POST">
                                    <div class="table-responsive">
                                      <button class="btn btn-danger">HAPUS</button>
                                      <table id="variable_terpilih" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                        <thead>
                                          <tr class="bg-primary">
                                            <th class="text-center" width="5%">
                                              <input name="select_all" value="1" id="pilih-all" type="checkbox" />
                                            </th>
                                            <th class="text-center" width="5%">No.</th>
                                            <th class="text-center">Item Name</th>
                                            <th class="text-center">Standard</th>
                                            <th class="text-center">Toleransi</th>
                                            <th class="text-center">Tools</th>
                                          </tr>
                                        </thead>
                                      </table>
                                    </div>
                                  </form>
                                </div>
                              </div>
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

  <div id="loading-screen" class="loading">Loading&#8230;</div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script> -->
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <!-- <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
  <script type="text/javascript">
    function refresh_tabel() {
      reload_table();
    }

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    $(document).ready(function() {
      $("#loading-screen").hide();

      table = $('#variable_terpilih').DataTable({
        dom: 'Bfrltip',
        buttons: [
          //'excel', 'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollY: "100%",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        language: {
          search: ""
        },
        "lengthChange": false,
        "bInfo": false,
        "ordering": false,
        'processing': true,
        'serverSide': false,
        'serverMethod': 'post',
        'ajax': {
          url: "<?php echo base_url(); ?>check_sheet/data_variable_list",
          type: 'POST',
          "data": function(data) {

          }
        },

        'aoColumns': [{
            "#": "#",
            "sClass": "text-center"
          },
          {
            "No.": "No.",
            "sClass": "text-right"
          },
          {
            "Item Name": "Item Name",
            "sClass": "text-left"
          },
          {
            "Standard": "Standard",
            "sClass": "text-left"
          },
          {
            "Toleransi": "Toleransi",
            "sClass": "text-left"
          },
          {
            "Tools": "Tools",
            "sClass": "text-left"
          }
        ],

        'columnDefs': [{
          'targets': 0,
          'searchable': false,
          'orderable': false,
          'className': 'dt-body-center',
          'render': function(data, type, full, meta) {

            return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
          }
        }],

        'select': {
          'style': 'multi'
        },

        'order': [
          [1, 'asc']
        ]
      });

      table_pilihan = $('#table_variable').DataTable({
        dom: 'Bfrltip',
        buttons: [
          //'excel', 'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollY: "100%",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        language: {
          search: ""
        },
        "lengthChange": false,
        "bInfo": false,
        "ordering": false,
        'processing': true,
        'serverSide': false,
        'serverMethod': 'post',
        'ajax': {
          url: "<?php echo base_url(); ?>check_sheet/data_variable_list",
          type: 'POST',
          "data": function(data) {

          }
        },

        'aoColumns': [{
            "#": "#",
            "sClass": "text-center"
          },
          {
            "No.": "No.",
            "sClass": "text-right"
          },
          {
            "Item Name": "Item Name",
            "sClass": "text-left"
          },
          {
            "Standard": "Standard",
            "sClass": "text-left"
          },
          {
            "Toleransi": "Toleransi",
            "sClass": "text-left"
          },
          {
            "Tools": "Tools",
            "sClass": "text-left"
          }
        ],

        'columnDefs': [{
          'targets': 0,
          'searchable': false,
          'orderable': false,
          'className': 'dt-body-center',
          'render': function(data, type, full, meta) {

            return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
          }
        }],

        'select': {
          'style': 'multi'
        },

        'order': [
          [1, 'asc']
        ]
      });

      // Handle click on "Select all" control
      $('#variable-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var rows = table_pilihan.rows({
          'search': 'applied'
        }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });

      // Handle click on checkbox to set state of "Select all" control
      $('#table_variable tbody').on('change', 'input[type="checkbox"]', function() {
        // If checkbox is not checked
        if (!this.checked) {
          var el = $('#variable-select-all').get(0);
          // If "Select all" control is checked and has 'indeterminate' property
          if (el && el.checked && ('indeterminate' in el)) {
            // Set visual state of "Select all" control 
            // as 'indeterminate'
            el.indeterminate = true;
          }
        }
      });

      $('#form-pilihan-variable').on('submit', function(e) {
        var form = this;
        e.preventDefault();

        // Iterate over all checkboxes in the table
        table_pilihan.$('input[type="checkbox"]').each(function() {
          // If checkbox doesn't exist in DOM
          if (!$.contains(document, this)) {
            // If checkbox is checked
            if (this.checked) {
              // Create a hidden element 
              $(form).append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', this.name)
                .val(this.value)
              );
            }
          }
        });

        // Output form data to a console
        $('#example-console').text($(form).serialize());
        //console.log("Form submission", $(form).serialize());
        var data_array = table_pilihan.$('input[type="checkbox"]').serializeArray();
        const part_id = {
          name: 'part_id',
          value: $("#part_id").val()
        };

        const part_name = {
          name: 'part_name',
          value: $("#part_name").val()
        };

        if (data_array.length > 0) {
          console.log(data_array);

          data_array.unshift(part_id);
          data_array.unshift(part_name);
          console.log(data_array);
          $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>check_sheet/save_transaksi_variable",
            data: data_array,
            dataType: "JSON",
            beforeSend: function(res) {
              $("#loading-screen").show();
            },
            success: function(response) {

            },
            error: function(error) {

            }
          });
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