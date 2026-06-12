<?php
  defined('BASEPATH') OR exit('No direct script access allowed');
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

    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
    <!-- <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" /> -->
    <style>
      .selected .text-danger, .selected .text-danger:hover {
        color: #fff !important;
      }

      td a {
        font-weight: bolder;
      }
    </style>
  </head>
  <body>

    <div class="loader-bg">
      <div class="loader-bar"></div>
    </div>

    <div id="pcoded" class="pcoded">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="card">
              <div class="card-header text-center">
                <h5>
                  <?php echo strtoupper($nama_halaman); ?>
                </h5>
              </div>
              <div class="card-block special">
                <div class="dt-responsive table-responsive">
                  <form id="frm-example" action="#" method="POST">
                    <div class="form-group row">
                      <!-- <div class="col-md-2 col-sm-12 mt-2">
                        <button class="btn btn-danger btn-full-mobile">Set shift</button>
                        <div class="input-group">
                          <select class="custom-select" id="pilih_shift" name="pilih_shift">
                            <option selected disabled>-- Shift --</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                          </select>
                          <div class="input-group-append">
                            <button class="btn btn-danger btn-full-mobile">Simpan ke shift</button>
                          </div>
                        </div>
                      </div> -->
                      <label class="col-md-2 col-sm-12 mt-2 col-form-label">Filter by</label>
                      <div class="col-md-2 col-sm-12 mt-2">
                        <div class="input-group">
                          <select name="pilihan" id="pilihan" class="form-control">
                            <option disabled>-- Pilih --</option>
                            <option value="all" selected>All</option>
                            <option value="pc">Power Cord</option>
                            <option value="wiring">Wiring</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3 col-sm-12 mt-2">
                        <div class="input-group">
                          <input type="text" class="form-control" name="tanggal" id="tanggal">
                          <span class="input-group-append">
                            <label class="input-group-text"><i class="icofont icofont-calendar"></i></label>
                          </span>
                        </div>
                        <input type="hidden" name="start_date" id="start_date">
                        <input type="hidden" name="end_date" id="end_date">
                      </div>
                      <div class="col-md-2 col-sm-12 mt-2">
                        <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">Tampilkan</button>
                      </div>
                    </div>
                    <hr>
                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr class="">
                          <!-- <th class="text-center bg-primary no-sort">
                            <input name="select_all" value="1" id="example-select-all" type="checkbox" />
                          </th> -->
                          <th class="text-center bg-primary">No.</th>
                          <th class="text-center bg-primary">Part Name</th>
                          <th class="text-center bg-primary">Job No. & Part ID</th>
                          <th class="text-center bg-primary">Total Qty. Scan WH</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <!-- <tfoot>
                        <tr class="bg-primary">
                          <th colspan="3"></th>
                          <th></th>
                        </tr>
                      </tfoot> -->
                    </table>
                    <button class="btn btn-danger btn-sm pull-right mr-2" id="button-hapus" type="button">Hapus row</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- MODAL CEK DETAIL -->
    <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Detail Transaksi</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
	        </div>
	        <div class="modal-body">
            <div class="container">
              <div class="row">
                <div class="col-md-2 col-sm-12 mt-2 mb-2">
                  <h6>Job No.</h6>
                </div>
                <div class="col-md-3 col-sm-12 mt-2 mb-2">
                  <h6 id="jobNo">-</h6>
                </div>
                <div class="col-md-2 col-sm-12 mt-2 mb-2">
                  <h6>Part ID</h6>
                </div>
                <div class="col-md-5 col-sm-12 mt-2 mb-2">
                  <h6 id="partID">-</h6>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 col-sm-12 mt-2 mb-2">
                  <h6>Qty. Order</h6>
                </div>
                <div class="col-md-3 col-sm-12 mt-2 mb-2">
                  <h6 id="qtyOrder">-</h6>
                </div>
                <div class="col-md-2 col-sm-12 mt-2 mb-2">  
                  <h6>Part Name</h6>
                </div>
                <div class="col-md-5 col-sm-12 mt-2 mb-2">
                  <h6 id="partName">-</h6>
                  <input type="hidden" id="part_id" name="part_id">
                  <input type="hidden" id="part_name" name="part_name">
                  <input type="hidden" id="qty_order" name="qty_order">
                </div>
              </div>
            </div>
	          <div class="table-responsive mt-2">
              <table class="table table-bordered table-striped" width="100%">
                <thead>
                  <tr class="bg-primary">
                    <th class="text-center" width="60">No</th>
                    <th class="text-center" width="120">Tgl. Scan</th>
                    <th class="text-center" width="120">Sub Total</th>
                    <th class="text-center">#</th>
                  </tr>
                </thead>
                <tbody id="isi_content"></tbody>
                <tfoot id="isi_footer"></tfoot>
              </table>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>
    <!-- MODAL CEK DETAIL END -->

    <!-- MODAL PILIH SHIFT -->
    <!-- <div class="modal fade" id="modal_pilih_shift" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Pilih Shift</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
	        </div>
	        <div class="modal-body">
            <div class="container">
              <div class="row mb-2">
                <div class="col-md-4">
                  <h6>Shift</h6>
                </div>
                <div class="col-md-8">
                  <select class="custom-select" id="pilih_shift" name="pilih_shift">
                    <option selected disabled>-- Pilih shift --</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                  </select>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-md-4">
                  <h6>Tanggal</h6>
                </div>
                <div class="col-md-8">
                  <input type="text" name="tanggal_shift" id="tanggal_shift" class="form-control">
                </div>
              </div>
              <hr>
              <div class="row mb-2">
                <div class="col-md-4">
                  <h6></h6>
                </div>
                <div class="col-md-8">
                  <button id="btnSave" type="button" onclick="save_transaksi();" class="btn btn-primary">Save</button>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" width="100%">
                    <tr class="bg-primary">
                      <th class="text-center">No.</th>
                      <th class="text-center">Job No.</th>
                      <th class="text-center">Qty. Scan WH</th>
                      <th class="text-center">Part ID</th>
                      <th class="text-center">Part Name</th>
                      <th class="text-center">Qty. Order</th>
                    </tr>
                    <tbody id="isi"></tbody>
                  </table>
                </div>
              </div>
            </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger" data-dismiss="modal">Close</button>
          </div>
      	</div>
    	</div>
  	</div> -->
    <!-- MODAL PILIH SHIFT END -->

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
    <!-- <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script> -->
    <script type="text/javascript">
      $(function() {

        var start = moment(); //moment().subtract(7, 'days');
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

      $(function() {
        $('input[name="tanggal_shift"]').daterangepicker({
          maxDate: new Date(),
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 2020,
          maxYear: parseInt(moment().format('YYYY'),10)
        });
      });
    </script>
    <script type="text/javascript">
      //FUNCTION SIMPAN DATA SHIFT
      function save_data_shift(NoJob, Nomor, QtyScan) {
        let shift     = $("#shift_" + Nomor).val();
        let qty_scan  = $("#qty_" + Nomor).val();
        console.log(shift);
        console.log(qty_scan);

        if (shift != null) {
          if (qty_scan != '') {
            $.ajax({
              url : "<?php echo base_url(); ?>lhp_fg/save_data_shift",
              type: "POST",
              dataType: "JSON",
              data: {
                job_nomor: NoJob,
                qty_scaner_wh: qty_scan,
                shift_selected : shift
              },
              beforeSend: function(data) 
              {
                $("#loading-screen").show();
              },
              success: function(data)
              {
                console.log(data);
                // Swal.fire({
                //   icon: data.status,
                //   title: data.status.charAt(0).toUpperCase() + data.status.slice(1),
                //   text: data.message
                // })
                $("#loading-screen").hide();
              },
              error: function (jqXHR, textStatus, errorThrown)
              {
                alert('Error get data from ajax');
              }
            });
          } else {
            alert("Silahkan isi quantity dahulu!");
            $("#qty_" + Nomor).focus();
            
            return false;
          }
        } else {
          alert("Silahkan pilih shift dahulu!");
          $("#shift_" + Nomor).focus();

          return false;
        }
      }
      //FUNCTION SIMPAN TRANSAKSI SHIFT
      function save_transaksi() {
        let shift = $("#pilih_shift").val();
        if (shift != null) {
          let array_job = localStorage.getItem('nomor_job_array');
          $.ajax({
            url : "<?php echo base_url(); ?>lhp_fg/save_data_shift",
            type: "POST",
            dataType: "JSON",
            data: {
              job_nomor: JSON.parse(array_job),
              shift_selected : shift
            },
            beforeSend: function(data) 
            {
              $("#loading-screen").show();
            },
            success: function(data)
            {
              Swal.fire({
                icon: data.status,
                title: data.status.charAt(0).toUpperCase() + data.status.slice(1),
                text: data.message
              })
              $("#loading-screen").hide();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
          });
        } else {
          alert("Silahkan pilih shift dahulu");

          return false;
        }
      }

      //FUNCTION DETAIL TRANSAKSI
      function cek_detail_transaksi(no_job, PartName, PartID, QtyOrder) {  
        $.ajax({
            url : "<?php echo base_url(); ?>lhp_fg/cek_detail_transaksi",
            type: "POST",
            dataType: "JSON",
            data: {
              job_nomor: no_job,
              start_date : $('#start_date').val(),
              end_date : $('#end_date').val()
            },
            success: function(data)
            {
              console.log(parseFloat(QtyOrder.replace(',', '')));
              $('#modal_detail').modal('show');
              $('#isi_content').html(data.html);
              $('#isi_footer').html(data.footer);
              $('#jobNo').html(": " + no_job);
              $('#qtyOrder').html(": " + QtyOrder);
              $('#partID').html(": " + PartID);
              $('#partName').html(": " + PartName);
              
              $('#part_id').val(PartID);
              $('#part_name').val(PartName);
              $('#qty_order').val(parseFloat(QtyOrder.replace(',', '')));
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
      }

      //FUNCTION CARI
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      }

      $(document).ready(function() {
        $("#loading-screen").hide();
        table = $('#order-table').DataTable( {
          dom: 'Bfrltip',
          "pageLength": 50,
          buttons: [
            'excel'
          ],
          // "footerCallback": function(row, data, start, end, display) {
          //   var api = this.api(),
          //     data;

          //   // converting to interger to find total
          //   var intVal = function(i) {
          //     return typeof i === 'string' ?
          //       i.replace(/[\$,]/g, '') * 1 :
          //       typeof i === 'number' ?
          //       i : 0;
          //   };

          //   // computing column Total of the complete result 
          //   var total_qty = api
          //     .column(3)
          //     .data()
          //     .reduce(function(a, b) {
          //       const {body} = new DOMParser().parseFromString(b, 'text/html');
          //       const value = body.querySelector('a').innerText; // find <code> tag and get text
          //       return intVal(a) + intVal(value);
          //     }, 0);

          //   // Update footer by showing the total with the reference of the column index 
          //   $(api.column(0).footer()).html('GRAND TOTAL');
          //   $(api.column(3).footer()).html(formatNumber(total_qty));
          // },
          scrollY       : "100%",
          scrollX       : true,
          scrollCollapse: true,
          paging        : true,
          'processing': true,
          'serverSide': false,
          'serverMethod': 'POST',
          'ajax': {
            url : "<?php echo base_url(); ?>lhp_fg/hasil_scan_wh_data",
            type : 'POST',
            "data": function(data) {
              data.start_date   = $('#start_date').val();
              data.end_date     = $('#end_date').val();
              data.pilihan      = $('#pilihan').val();
            }
          },
          
          oLanguage: {sProcessing: "<div id='loading-screen' class='loading'>Loading&#8230;</div>"},

          'aoColumns': [
            // { "#": "#", "sClass": "text-center" },
            { "No.": "No." , "sClass": "text-right" },
            { "Part Name": "Part Name" , "sClass": "text-left" },
            { "Job No. & Part ID": " Job No. & Part ID" , "sClass": "text-left" },
            { "Total Qty. Scan WH": "Total Qty. Scan WH" , "sClass": "text-right" }
          ],

          'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'ordering': false,
            'className': 'dt-body-center'
            // 'render': function(data, type, full, meta) {
            //   return '<input type="checkbox" name="no_job[]" value="' + $('<div/>').text(data).html() + '">';
            // }
          }],

          'select': {
            'style': 'multi'
          }

          // 'order': [
          //   [1, 'asc']
          // ]
        } );

        //SET SELECTED
        table.on('click', 'tbody tr', (e) => {
          let classList = e.currentTarget.classList;
      
          if (classList.contains('selected')) {
              classList.remove('selected');
          }
          else {
              table.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
              classList.add('selected');
          }
        });
        //REMOVE COLUMN
        document.querySelector('#button-hapus').addEventListener('click', function () {
          table.row('.selected').remove().draw(false);
        });

        // Handle click on "Select all" control
        // $('#example-select-all').on('click', function() {
        //   // Check/uncheck all checkboxes in the table
        //   var rows = table.rows({
        //     'search': 'applied'
        //   }).nodes();
        //   $('input[type="checkbox"]', rows).prop('checked', this.checked);
        // });

        // // Handle click on checkbox to set state of "Select all" control
        // $('#example tbody').on('change', 'input[type="checkbox"]', function() {
        //   // If checkbox is not checked
        //   if (!this.checked) {
        //     var el = $('#example-select-all').get(0);
        //     // If "Select all" control is checked and has 'indeterminate' property
        //     if (el && el.checked && ('indeterminate' in el)) {
        //       // Set visual state of "Select all" control 
        //       // as 'indeterminate'
        //       el.indeterminate = true;
        //     }
        //   }
        // });

        // $('#frm-example').on('submit', function(e) {
        //   var form = this;
        //   e.preventDefault();

        //   // Iterate over all checkboxes in the table
        //   table.$('input[type="checkbox"]').each(function() {
        //     // If checkbox doesn't exist in DOM
        //     if (!$.contains(document, this)) {
        //       // If checkbox is checked
        //       if (this.checked) {
        //         // Create a hidden element 
        //         $(form).append(
        //           $('<input>')
        //           .attr('type', 'hidden')
        //           .attr('name', this.name)
        //           .val(this.value)
        //         );
        //       }
        //     }
        //   });

        //   // FOR TESTING ONLY

        //   // Output form data to a console
        //   $('#example-console').text($(form).serialize());
        //   var data_array = table.$('input[type="checkbox"]').serializeArray();
        //   if (data_array.length > 0) {
        //     localStorage.setItem('nomor_job_array', JSON.stringify(data_array));
        //     $('#modal_pilih_shift').modal('show');

        //     let isi = "";
        //     data_array.forEach(function callback(element, index) {
        //       let value = element.value;
        //       var arr = value.split("|");
        //       var no  = index + 1;
        //       isi +=  '<tr>' + 
        //                 '<td class="text-right">' + no + '</td>' +
        //                 '<td>' + arr[0] + '</td>' +
        //                 '<td class="text-right">' + arr[1] + '</td>' +
        //                 '<td>' + arr[2] + '</td>' +
        //                 '<td>' + arr[3] + '</td>' +
        //                 '<td class="text-right">' + arr[4] + '</td>' +
        //               '</tr>';
        //     });

        //     $("#isi").html(isi);
        //   } else {
        //     alert("Silahkan pilih item dahulu");
        //     return false;
        //   }

        //   // Prevent actual form submission
        //   e.preventDefault();
        // });

        function formatNumber(n) {
          return n.toLocaleString();
        };
      });
    </script>
  </body>
</html>