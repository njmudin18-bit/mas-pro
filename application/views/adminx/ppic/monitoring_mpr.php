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
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/timeline.css" />
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
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
                            <div class="dt-responsive table-responsiveXX">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter data by</label>
                                <div class="col-md-4 col-sm-12 m-t-30">
                                  <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span>
                                  </div>
                                  <!-- <input class="form-control" type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">

                                </div>


                                <div class="col-md-3 col-sm-12 m-t-30">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <form id="frm-example" action="#" method="POST">
                                <p><button id="btn_kirim" class="btn btn-danger" disabled>KIRIM</button></p>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="">
                                      <th class="text-center bg-primary">
                                        <input name="select_all" value="1" id="example-select-all" type="checkbox" />
                                      </th>
                                      <th class="text-center bg-primary">No</th>
                                      <th class="text-center bg-primary">No. Bukti</th>
                                      <th class="text-center bg-primary">Job Date</th>
                                      <th class="text-center bg-primary">Part ID</th>
                                      <th class="text-center bg-primary">Partname</th>
                                      <th class="text-center bg-primary">Qty</th>
                                      <th class="text-center bg-primary">Keterangan</th>
                                      <th class="text-center bg-primary">WH?</th>
                                      <th class="text-center bg-primary">Create By</th>
                                      <th class="text-center bg-primary">Create Date</th>
                                      <th class="text-center bg-primary">Status</th>
                                      <th class="text-center bg-primary">Action</th>
                                      <th class="text-center bg-primary">Lihat Detail</th>
                                      <th class="text-center bg-primary">#</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
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
  <!-- modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Status Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="content">
            <!-- timeline disini -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal -->

  <div id="loading-screen" class="loading">Loading&#8230;</div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

  <script type="text/javascript">
    //RANGE DATE PICKER
    $(function() {
      var start = moment();
      var end = moment();

      function cb(start, end) {
        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        var start_date = start.format('YYYY-MM-DD');
        var end_date = end.format('YYYY-MM-DD');
        $("#start_date").val(start_date);
        $("#end_date").val(end_date);
      }

      $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb);

      cb(start, end);

    });

    //FUNCTION CARI
    function cari() {
      reload_table();
    }

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    //DELETE MPR
    function deleteMpr(nobukti){
      // console.log(nobukti);
      Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Tidak, batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?php echo base_url(); ?>ppic_mpr/deleteMpr",
              data: {
                nobukti: nobukti
              },
              type: 'POST',
              dataType: 'JSON',
              error: function() {
                alert('Something is wrong');
              },
              success: function(hasil) {
              console.log(hasil);
              if (hasil.status == "success") {
                $("#timeline").html(hasil.html);
                $("#loading-screen").hide();
                Swal.fire(
                  'OK',
                  hasil.message,
                  'success'
                );
                reload_table()
              } else if (hasil.status == "error") {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'warning'
                );
                $("#loading-screen").hide();
              } else {
                Swal.fire(
                  'Oops',
                  hasil.message,
                  'warning'
                );
                $("#loading-screen").hide();
              }
            }
            });
          }
        })
    }

    //LIHAT STATUS
    function lihat_status(nobukti) {
      // console.log(nobukti);
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_mpr/lihat_status",
        data: {
          nobukti: nobukti
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          $("#loading-screen").show();
        },
        success: function(hasil) {
          console.log(hasil);
          if (hasil.status_code == 200) {
            $("#content").html(hasil.html);
            $("#loading-screen").hide();
            $('#exampleModal').modal('show');
          } else if (hasil.status_code == 409) {
            $("#content").html(hasil.html);
            $("#loading-screen").hide();
            $('#exampleModal').modal('show');
          } else {
            Swal.fire(
              'Oops',
              hasil.message,
              'warning'
            );
            $("#loading-screen").hide();
          }
        }
      })

    }

    //KIRIM MPR
    function modalwhmpr(nobukti, tahun, bulan) {
      // console.log(nobukti);
      localStorage.setItem("nobukti_wh", nobukti);
      localStorage.setItem("tahun_wh", tahun);
      localStorage.setItem("bulan_wh", bulan);
      // $('#modalPilihWarehouse').modal('show');
      Swal.fire({
        title: 'Pilih Warehouse',
        input: 'select',
        inputOptions: {
          'WH-A': 'WH-A',
          'WH-B': 'WH-B'
        },
        inputPlaceholder: 'required',
        showCancelButton: true,
        inputValidator: function (value) {
          return new Promise(function (resolve, reject) {
            if (value !== '') {
              resolve();
            } else {
              resolve('Anda harus pilih WH dahulu!');
            }
          });
        }
      }).then(function (result) {
        if (result.isConfirmed) {
          var pilihan_wh = result.value;
          kirim_mpr(pilihan_wh);
          }
      });
    }

    function kirim_mpr(pilihan_wh) {
      // console.log(nobukti);
      var pilihan_wh = pilihan_wh;
      var loc_id = 'PPIC001';
      var nobukti = localStorage.getItem("nobukti_wh");
      var tahun = localStorage.getItem("tahun_wh");
      var bulan = localStorage.getItem("bulan_wh");
      
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_mpr/kirim_mpr",
        data: {
          nobukti: nobukti,
          bulan: bulan,
          tahun: tahun,
          loc_id: loc_id,
          pilihan_wh:pilihan_wh
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          $("#loading-screen").show();
        },
        success: function(hasil) {
          // console.log(hasil);
          if (hasil.status_code == 200) {
            $("#timeline").html(hasil.html);
            $("#loading-screen").hide();
            Swal.fire(
              'OK',
              hasil.message,
              'success'
              );
              
              $("#modalPilihWarehouse").hide();
              // $(".modal-backdrop").hide();
              // location.reload();
            reload_table()

          } else if (hasil.status_code == 409) {
            Swal.fire(
              'Oops',
              hasil.message,
              'warning'
            );
            $("#loading-screen").hide();
          } else {
            Swal.fire(
              'Oops',
              hasil.message,
              'warning'
            );
            $("#loading-screen").hide();
          }
        }
      })
    }

    //UPDATE WH
    function ganti_wh(no_bukti, part_id) {
      let data_array = no_bukti.split("-");
      Swal.fire({
        title: 'Ganti WH<br><small class="text-danger">MPR NO: ' + data_array[0] + '</small><hr>',
        input: 'select',
        inputOptions: {
          'WH-A': 'WH-A',
          'WH-B': 'WH-B'
        },
        inputPlaceholder: '-- Pilih --',
        showCancelButton: true,
        inputValidator: function (value) {
          return new Promise(function (resolve, reject) {
            if (value !== '') {
              resolve();
            } else {
              resolve('Silahkan pilih WH dahulu');
            }
          });
        }
      }).then(function (result) {
        if (result.isConfirmed) {

          let data_form = {
            'part_id' : part_id,
            'no_bukti' : no_bukti,
            'wh_pilihan' : result.value,
            'loc_id' : 'PPIC001'
          }

          $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>ppic_mpr/ganti_wh_mpr",
            data: data_form,
            dataType: "JSON",
            beforeSend: function(res) {
              $("#loading-screen").show();
            },
            success: function(response) {
              $("#loading-screen").hide();
              Swal.fire(
                response.status.toUpperCase(),
                response.message,
                'success'
              );
            },
            error: function(xhr, ajaxOptions, thrownError) {
              //alert(xhr.status);
              $("#loading-screen").hide();
              console.log(xhr);
              console.log(ajaxOptions);
              console.log(thrownError)
            }
          });
        }
      });
    }

    //show isi table
    $(document).ready(function() {
      var counterChecked = 0;

      $('body').on('change', 'input[type="checkbox"]', function() {
          this.checked ? counterChecked++ : counterChecked--;
          counterChecked > 0 ? $('#btn_kirim').prop("disabled", false): $('#btn_kirim').prop("disabled", true);
      });


      $("#loading-screen").hide();

      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        buttons: [
          'excel'
          //'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollY: "500px",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        // fixedColumns: {
        //   leftColumns: 2
        // },
        'processing': true,
        'serverSide': false,
        'serverMethod': 'POST',
        'ajax': {
          url: "<?php echo base_url(); ?>ppic_mpr/monitoring_mpr_list_new",
          type: 'POST',
          "data": function(data) {
            data.start_date = $('#start_date').val();
            data.end_date = $('#end_date').val();
          }
        },

        'aoColumns': [
          {
            "#": "#",
            "sClass": "text-center"
          },
          {
            "No": "No",
            "sClass": "text-right"
          },
          {
            "No. Bukti": "No. Bukti",
            "sClass": "text-left"
          },
          {
            "Job Date": "Job Date",
            "sClass": "text-right"
          },
          {
            "Part ID": "Part ID",
            "sClass": "text-left"
          },
          {
            "Part name": "Part name",
            "sClass": "text-left"
          },
          {
            "Qty": "Qty",
            "sClass": "text-right"
          },
          {
            "Keterangan": "Keterangan",
            "sClass": "text-left"
          },
          {
            "WH?": "WH?",
            "sClass": "text-center"
          },
          {
            "Create By": "Create By",
            "sClass": "text-left"
          },
          {
            "Create Date": "Create Date",
            "sClass": "text-left"
          },
          {
            "Status": "Status",
            "sClass": "text-center"
          },
          {
            "Action": "Action",
            "sClass": "text-center"
          },
          {
            "Lihat Detail": "Lihat Detail",
            "sClass": "text-center"
          },
          {
            "#": "#",
            "sClass": "text-center"
          }
        ],

        // "columnDefs": [{
        //   "targets": [-1, 0, 1],
        //   "orderable": false,
        //   className: 'text-right'
        // }, ]

        'columnDefs': [{
          'targets': 0,
          'searchable': false,
          'orderable': false,
          'className': 'dt-body-center',
          'render': function(data, type, full, meta) {
            let isi  = "'" + full[0] + "', '" + full[4] + "'";
            console.log(full[14]);
            if (full[15] == null || full[15] == '') {
              return '<input type="checkbox" class="checks" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            } else {
              return '<button type="button" class="btn btn-info btn-sm" title="Ganti WH" onclick="ganti_wh(' + isi + ')"><i class="fa fa-edit"></i></button>';
            }
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
      $('#example-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var rows = table.rows({
          'search': 'applied'
        }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });

      // Handle click on checkbox to set state of "Select all" control
      $('#example tbody').on('change', 'input[type="checkbox"]', function() {
        // If checkbox is not checked
        if (!this.checked) {
          var el = $('#example-select-all').get(0);
          // If "Select all" control is checked and has 'indeterminate' property
          if (el && el.checked && ('indeterminate' in el)) {
            // Set visual state of "Select all" control 
            // as 'indeterminate'
            el.indeterminate = true;
          }
        }
      });

      $('#frm-example').on('submit', function(e) {
        var form = this;
        e.preventDefault();

        // Iterate over all checkboxes in the table
        table.$('input[type="checkbox"]').each(function() {
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

        // FOR TESTING ONLY

        // Output form data to a console
        $('#example-console').text($(form).serialize());
        //console.log("Form submission", $(form).serialize());
        var data_array = table.$('input[type="checkbox"]').serializeArray();
        if (data_array.length > 0) {
          localStorage.setItem("no_bukti_array", JSON.stringify(data_array));
          
          Swal.fire({
            title: 'Pilih WH',
            input: 'select',
            inputOptions: {
              'WH-A': 'WH-A',
              'WH-B': 'WH-B'
            },
            inputPlaceholder: '-- Pilih --',
            showCancelButton: true,
            inputValidator: function (value) {
              return new Promise(function (resolve, reject) {
                if (value !== '') {
                  resolve();
                } else {
                  resolve('Silahkan pilih WH dahulu');
                }
              });
            }
          }).then(function (result) {
            if (result.isConfirmed) {

              let data_form = {
                'no_bukti_array' : localStorage.getItem("no_bukti_array"),
                'wh_pilihan' : result.value,
                'loc_id' : 'PPIC001'
              }

              $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>ppic_mpr/kirim_mpr_multiple",
                data: data_form,
                dataType: "JSON",
                beforeSend: function(res) {
                  $("#loading-screen").show();
                },
                success: function(response) {
                  console.log(response);
                  $("#loading-screen").hide();
                  $('#frm-example')[0].reset();
                  reload_table();
                  Swal.fire(
                    response.status.toUpperCase(),
                    response.message,
                    'success'
                  );
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  //alert(xhr.status);
                  $("#loading-screen").hide();
                  console.log(xhr);
                  console.log(ajaxOptions);
                  console.log(thrownError)
                }
              });
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