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
  <style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice span {
      color: #555;
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
                            </h5>
                          </div>
                          <div class="card-block m-t-30 m-b-30">
                            <form id="add_form">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter part name</label>
                                <div class="col-md-8 col-sm-12 m-t-30">
                                  <select id="part_name" name="part_name" class="js-data-example-ajax form-control" multiple="multiple"></select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-30">
                                  <button id="btn_add" type="button" class="btn btn-info btn-full-mobile" onclick="tambahkan();">TAMBAHKAN</button>
                                </div>
                              </div>
                            </form>
                            <hr class="m-t-20 m-b-20">
                            <div class="dt-responsive table-responsive">
                              <h5 class="text-center">DAFTAR PARTNAME</h5>
                              <hr class="m-t-20 m-b-20">
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="bg-primary">
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center" width="7%">#</th>
                                    <th class="text-center" width="4%">Status</th>
                                    <th class="text-center">Part ID</th>
                                    <th class="text-center">Part Name</th>
                                    <th class="text-center" width="10%">Module</th>
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

  <div id="loading-screen" class="loading">Loading&#8230;</div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script> -->
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script type="text/javascript">
    //FUNGSI HAPUS
    function hapus_part_name(id) {
      Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Data yang di hapus tidak bisa dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Tidak, jangan',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>qc_transaction/part_deleted",
            data: {
              'part_id': id
            },
            dataType: "JSON",
            beforeSend: function(res) {
              $("#loading-screen").show();
            },
            success: function(response) {
              $("#loading-screen").hide();
              console.log(response);
              // var result = JSON.parse(response);
              if (response.status == 'forbidden') {
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } else {
                $("#" + id).remove();
                Swal.fire(
                  'Sukses!',
                  'Anda sukses menghapus data',
                  'success'
                )
                reload_table();
              }
            },
            error: function(err) {
              $("#loading-screen").hide();
              Swal.fire(
                'Oops!',
                'Something went wrong',
                'error'
              )
            }
          });
        }
      })
    }

    //FUNCTION TAMBAHKAN
    function tambahkan() {
      //GET VALUE
      var part_name_array = $('#part_name').val();
      var values = [];

      part_name_array.forEach(function(part) {
        values.push(part);
      });

      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>qc_transaction/save_part",
        data: {
          'part_name': values
        },
        dataType: "JSON",
        beforeSend: function(res) {
          $("#loading-screen").show();
        },
        success: function(response) {
          $("#loading-screen").hide();
          if (response.status_code == '200' || response.status_code == 200) {
            Swal.fire(
              response.status,
              response.message,
              'success'
            );
            reload_table();
          } else {
            Swal.fire(
              response.status,
              response.message,
              'info'
            )
          }
        },
        error: function(err) {
          $("#loading-screen").hide();
          Swal.fire(
            'Oops!',
            'Something went wrong',
            'error'
          )
        }
      });
    }

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    $(document).ready(function() {
      $("#loading-screen").hide();

      $("#part_name").select2({
        placeholder: "Masukan kode atau nama part",
        allowClear: true,
        theme: "classic",
        minimumResultsForSearch: 20,
        width: 'resolve',
        ajax: {
          url: '<?php echo base_url(); ?>qc_transaction/search_part',
          dataType: 'JSON',
          type: 'POST',
          delay: 250,
          data: function(params) {
            return {
              term: params.term, // search term
              page: 10
            };
          },
          processResults: function(data, page) {
            return {
              results: data
            };
          },
          cache: true
        },
        escapeMarkup: function(markup) {
          return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        maximumInputLength: 20
      });

      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        buttons: [
          'excel'
          //'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollY: "100%",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        // fixedColumns: {
        //   leftColumns: 2,
        //   rightColumns: 0
        // },
        'processing': true,
        'serverSide': false,
        'serverMethod': 'post',
        'ajax': {
          url: "<?php echo base_url(); ?>qc_transaction/show_data_list",
          type: 'POST',
          "data": function(data) {
            // data.bulan = $('#bulan').val();
            // data.tahun = $('#tahun').val();
            // data.tanggal = $('#tanggal').val();
            // data.jenis_part = $('#jenis_part').val();
          }
        },

        'aoColumns': [{
            "NO.": "NO.",
            "sClass": "text-right"
          },
          {
            "#": "#",
            "sClass": "text-center"
          },
          {
            "Status": "Status",
            "sClass": "text-center"
          },
          {
            "Part ID": "Part ID",
            "sClass": "text-left"
          },
          {
            "Part Name": "Part Name",
            "sClass": "text-left"
          },
          {
            "Module": "Module",
            "sClass": "text-right"
          }
        ],

        "columnDefs": [{
          "targets": [1, 2], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });
    });
  </script>
</body>

</html>