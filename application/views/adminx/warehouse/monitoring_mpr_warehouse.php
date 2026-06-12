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
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/timeline.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/pages.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/coret.css" />

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
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-30">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="">
                                    <th rowspan="2" class="text-center align-middle bg-primary">No</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">
                                      <button id="button_cek" onclick="cek_data()" type="button" class="btn btn-danger btn-sm">CEK</button>
                                    </th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">No. Bukti</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Job Date</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Part ID</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Partname</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Qty</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Keterangan</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Create By</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Create Date</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Status</th>
                                    <th colspan="2" class="text-center  align-middle bg-primary">Action</th>
                                    <th rowspan="2" class="text-center align-middle bg-primary">Lihat Detail</th>
                                  </tr>
                                  <tr>
                                    <th class="text-center align-middle bg-primary">Proses</th>
                                    <th class="text-center align-middle bg-primary">Kirim</th>
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

  <!-- modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Status Details</h5>
          <h5 class="modal-title" id="exampleModalLabel2">Detail MPR</h5>
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

  <!-- modal with checkbox -->
  <div class="modal fade" id="modalWithCB" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Pilih item dari MPR</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <div class="mt-1 mb-3" id="item_terpilih"></div>
            <form id="form_mpr_item">
              <table class="table table-striped table-borderless" width="100%">
                <thead>
                  <tr class="bg-primary">
                    <th>No</th>
                    <th>#</th>
                    <th>Part ID</th>
                    <th>Part Name</th>
                    <th>Qty</th>
                    <th>Std. Packing</th>
                    <th>Stock</th>
                    <th>Qty. Sisa Prod.</th>
                    <th>Unit ID</th>
                    <th>Location ID</th>
                  </tr>
                </thead>
                <tbody id="isi_item">

                </tbody>
              </table>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" onclick="simpan_multiple_item()" class="btn btn-primary">Simpan</button> -->
        </div>
      </div>
    </div>
  </div>
  <!-- modal with checkbox end -->

  <!-- LOADING -->
  <div id="loading-screen" class="loading">Loading&#8230;</div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>

  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script type="text/javascript">
    //RANGE DATE PICKER
    $(function() {

      var start = moment().subtract(7, 'days'); //moment();
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

    //FUNCTION SIMPAN ITEM MULTIPLE
    function simpan_multiple_item(id) {
      let part_id = $("#part_id_" + id).val();
      let part_name = $("#part_name_" + id).val();
      let qty_item = $("#qty_item_" + id).val();
      let qty_std_pkg = $("#qty_std_pkg_" + id).val();
      let no_bukti = $("#no_bukti_" + id).val();
      let id_mpr_detail = $("#id_" + id).val();
      console.log(no_bukti);

      $.ajax({
        url: "<?php echo base_url(); ?>warehouse_mpr/simpan_mpr_detail",
        data: {
          part_ids: part_id,
          part_names: part_name,
          qty_items: qty_item,
          qty_std_pkgs: qty_std_pkg,
          no_buktis: no_bukti,
          id_mpr_details: id_mpr_detail
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          //$("#loading-screen").show();
        },
        success: function(hasil) {

        },
        error: function(error) {
          alert("Error when proses data from controller");
        }
      })
    }

    //FUNCTION CEK DATA
    function cek_data() {
      let jlh_item = [];
      $("input:checkbox[name=item]:checked").each(function() {
        jlh_item.push($(this).val());
      });

      if (jlh_item.length > 0) {
        $.ajax({
          url: "<?php echo base_url(); ?>warehouse_mpr/proses_mpr",
          data: {
            items: jlh_item
          },
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
            //$("#loading-screen").show();
          },
          success: function(hasil) {
            $('#modalWithCB').modal('show');
            $("#isi_item").html(hasil.html);

            var item_badge = "";
            hasil.mpr_item.forEach(function(item) {
              item_badge += "<span class='badge badge-pill badge-success mt-2 mr-3'><h5>" + item + "</h5></span>";
            });

            $("#item_terpilih").html(item_badge);
          },
          error: function(error) {
            alert("Error when proses data from controller");
          }
        })
      } else {
        alert("Silahkan pilih item dahulu!");
      }
    }

    //FUNCTION CARI
    function cari() {
      reload_table();
    }

    //FUNCTION KIRIM MPR
    function kirim_mpr_wh(nobukti) {

      var loc_id = 'WH002';
      $.ajax({
        url: "<?php echo base_url(); ?>warehouse_mpr/kirim_mpr_wh",
        data: {
          nobukti: nobukti,
          loc_id: loc_id
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          $("#loading-screen").show();
        },
        success: function(hasil) {
          console.log(hasil);
          if (hasil.status_code == 200) {
            // $("#data_ng").html(hasil.html);
            $("#loading-screen").hide();
            // $('#modal_ng').modal('show');
            Swal.fire(
              'OK',
              hasil.message,
              'success'
            );
            reload_table();
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

    //FUNCTION TERIMA MPR
    function proses_mpr_wh(nobukti) {
      var bulan = $("#bulan").val();
      var tahun = $("#tahun").val();
      var loc_id = 'WH001';
      $.ajax({
        url: "<?php echo base_url(); ?>warehouse_mpr/proses_mpr_wh",
        data: {
          nobukti: nobukti,
          bulan: bulan,
          tahun: tahun,
          loc_id: loc_id
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          $("#loading-screen").show();
        },
        success: function(hasil) {
          console.log(hasil);
          if (hasil.status_code == 200) {

            // $("#data_ng").html(hasil.html);
            $("#loading-screen").hide();
            Swal.fire(
              'OK',
              hasil.message,
              'success'
            );
            // $('#modal_ng').modal('show');
            reload_table();
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

    //LIHAT STATUS
    function lihat_status(nobukti) {
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
            $('#exampleModalLabel1').show();
            $('#exampleModalLabel2').hide();
            $("#content").html(hasil.html);
            $("#loading-screen").hide();
            $('#exampleModal').modal('show');
          } else if (hasil.status_code == 409) {
            $('#exampleModalLabel1').show();
            $('#exampleModalLabel2').hide();
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

    //CEKLIS
    function ceklis(id, qtyStd, stockSimpan, qtySisa) {
      var isi = $('#ceklis' + id).val();
      var qtyStd = qtyStd;
      var stockSimpan = stockSimpan;
      var qtySisa = qtySisa;

      $.ajax({
        url: "<?php echo base_url(); ?>warehouse_mpr/ceklis_update",
        data: {
          id: id,
          value: isi,
          qtyStd: qtyStd,
          stockSimpan: stockSimpan,
          qtySisa: qtySisa
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function() {
          $("#loading-screen").show();
        },
        success: function(hasil) {
          mpr_detail(hasil.nobukti);
        }
      })
    }

    //MPR DETAIL
    function mpr_detail(nobukti) {
      $.ajax({
        url: "<?php echo base_url(); ?>warehouse_mpr/mpr_detail",
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
            $('#exampleModalLabel1').hide();
            $('#exampleModalLabel2').show();
            $("#content").html(hasil.html);
            $("#loading-screen").hide();
            $('#exampleModal').modal('show');
          } else if (hasil.status_code == 409) {
            $('#exampleModalLabel1').hide();
            $('#exampleModalLabel2').show();
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

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    $(document).ready(function() {
      $("#loading-screen").hide();

      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        buttons: [
          'excel'
          //'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollY: "800px",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        // fixedColumns: {
        //   leftColumns: 2
        //   // rightColumns: 1
        // },
        'processing': true,
        'serverSide': false,
        'serverMethod': 'POST',
        'ajax': {
          url: "<?php echo base_url(); ?>warehouse_mpr/monitoring_mpr_list_new",
          type: 'POST',
          "data": function(data) {
            data.start_date = $('#start_date').val();
            data.end_date = $('#end_date').val();
          }
        },

        'aoColumns': [{
            "No": "No",
            "sClass": "text-right"
          },
          {
            "#": "#",
            "sClass": "text-center"
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
            "Proses": "Proses",
            "sClass": "text-center"
          },
          {
            "Kirim": "Kirim",
            "sClass": "text-center"
          },
          {
            "Lihat Detail": "Lihat Detail",
            "sClass": "text-center"
          }
        ],

        "columnDefs": [{
          "targets": [-1, 0, 1], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });
    });
  </script>
</body>

</html>