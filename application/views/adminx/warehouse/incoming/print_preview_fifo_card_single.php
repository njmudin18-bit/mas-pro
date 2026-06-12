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

    <!-- QR CODE JS -->
    <script src="<?php echo base_url(); ?>assets/code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-qrcode/src/jquery.qrcode.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-qrcode/src/qrcode.js"></script>
    <style>
      h2.fw-bold {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 0;
      }

      h6.fw-bold {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 0;
      }

      p {
        font-size: 0.8125rem !important;
      }

      #isi-preview .table-bordered {
        border: 2px solid #000000;
      }

      #isi-preview .table-bordered td, .table-bordered th {
        border: 2px solid #000000;
        padding: 0.6rem .75rem !important;
      }

      .card .card-block p {
        margin-bottom: 0;
        font-size: 11px !important;
      }

      .preview-fifo p.fs-8 {
        font-size: 8px !important;
      }

      .preview-fifo p.fs-9 {
        font-size: 9px !important;
      }
    </style>
    <style>
      @media print {
        .d-print-inline {
            display: inline !important;
        }
        .d-print-inline-block {
            display: inline-block !important;
        }
        .d-print-block {
            display: block !important;
        }
        .d-print-grid {
            display: grid !important;
        }
        .d-print-inline-grid {
            display: inline-grid !important;
        }
        .d-print-table {
            display: table !important;
        }
        .d-print-table-row {
            display: table-row !important;
        }
        .d-print-table-cell {
            display: table-cell !important;
        }
        .d-print-flex {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
        }
        .d-print-inline-flex {
            display: -webkit-inline-box !important;
            display: -ms-inline-flexbox !important;
            display: inline-flex !important;
        }
        .d-print-none {
            display: none !important;
        }
      }
    </style>
  </head>
  <body>
    <div class="loader-bg d-print-none">
      <div class="loader-bar"></div>
    </div>
    <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box d-print-none"></div>
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
                      <div class="row d-print-none">
                        <div class="col-sm-12">
                          <div class="card">
                            <div class="card-header text-center">
                              <h5>
                                <?php echo strtoupper($nama_halaman); ?>
                              </h5>
                            </div>
                            <div class="card-block m-b-10">
                              <div class="dt-responsive table-responsive">
                                <table id="example" class="table table-striped table-bordered table-hover" width="200%">
                                  <thead>
                                    <tr class="bg-primary text-white">
                                    <th class="text-center" width="3%">No.</th>
                                    <th class="text-center" width="2%">#</th>
                                    <th class="text-center" width="15%">PO Number</th>
                                    <th class="text-center" width="3%">Cetak</th>
                                    <th class="text-center" width="8%">Tgl. Cetak</th>
                                    <th class="text-center" width="20%">Supplier Name</th>
                                    <th class="text-center" width="13%">Part ID</th>
                                    <th class="text-center">Part Name</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card">
                            <div class="card-block m-b-10">
                              <div class="row d-print-none">
                                <div class="col-sm-6 text-right">
                                  <a href="javascript:window.print()" class="btn btn-success mt-2 me-5" title="Cetak QR">
                                    <i class="fa fa-print ml-1 ml-1"></i> Print QR
                                  </a>
                                </div>
                                <div class="col-sm-6 text-left">
                                  <button onclick="show_modal_tambah()" class="btn btn-warning mt-2 me-5" type="button" title="Tambah QR">
                                    <i class="fa fa-qrcode ml-1"></i> Tambah QR ZZZ
                                  </button>
                                </div>
                              </div>
                              <hr class="d-print-none">
                              <div class="form-group row d-print-none">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter</label>
                                <div class="col-md-8 col-sm-12 m-t-10">
                                  <select name="pilihan_data" id="pilihan_data" class="form-control" style="height: 150px; width: 100%; border: 1px solid;" multiple>
                                    <option selected value="All">All</option>
                                  </select>
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-10">
                                  <button onclick="tampilkan_data_terpilih()" type="button" class="btn btn-primary">Tampilkan</button>
                                </div>
                              </div>
                              <hr class="d-print-none">
                              <div id="isi-preview" class="row"></div>
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

    <?php $this->load->view('adminx/components/bottom_js_datatable_with_qrcode'); ?>
    <script src="http://10.11.9.22:8080/global-application-system/assets/js/pages/invoicedetails.js"></script>
    <script type="text/javascript">

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() 
      {
        table.ajax.reload(null, false);
      }

      //FUNCTION HIDE
      function hide_barcode(Sequent) 
      {
        $("#preview_fifo_"+ Sequent).hide();
      }

      //FUNCTION SHOW MODAL TAMBAH
      function show_modal_tambah() 
      {
        let FIFO_CARD     = JSON.parse(localStorage.getItem('FIFO_CARD'));
        let PONumberArray = FIFO_CARD.PONumber;
        let PartIDArray   = FIFO_CARD.PartID;
        let form_data = {
          "PONumber": PONumberArray
        }

        console.log(form_data);

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
            alert('Oops error ketika proses data PO');
          }
        });
      };

      //FUNCTION HAPUS BARCODE
      function hapus_barcode(NomorBarcode) 
      {
        $.ajax({
          url: "<?php echo base_url(); ?>incoming/hapus_fifo_card",
          dataType: 'JSON',
          data: {
            Barcode: NomorBarcode
          },
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();
            if(data.status == 'success')
            {
              $("#loading").hide();
              //let PONumberArray = JSON.parse(localStorage.getItem('PONumber'));
              let FIFO_CARD     = JSON.parse(localStorage.getItem('FIFO_CARD'));
              let PONumberArray = FIFO_CARD.PONumber;
              let PartIDArray   = FIFO_CARD.PartID;
              show_fifo_card(PONumberArray, PartIDArray);
              location.reload();
            } else {
              Swal.fire(
                'FORBIDDEN',
                'Access Denied',
                'info',
              )
            }
          }, 
          error: function() {
            $("#loading").hide();
            alert('Oops error ketika hapus fifo card');
          }
        });
      }

      function tampilkan_data_terpilih() {
        var Pilihan       = $('#pilihan_data').find(':selected');
        var PilihanArray = [];
        
        // Iterate over the selected options and push their values to the array
        Pilihan.each(function() {
          PilihanArray.push($(this).val());
        });

        if (PilihanArray.length == 1 && PilihanArray[0] == 'all') {
          let PilihanAll    = PilihanArray[0];
          let PONumber      = "<?php echo $nomor_po ?>";

          show_fifo_card(PONumber);
        } else {
          $.ajax({
            url: "<?php echo base_url(); ?>incoming/get_selected_fifo_card",
            dataType: 'JSON',
            data: {
              selected: PilihanArray
            },
            type: 'POST',
            beforeSend: function() {
              $("#loading").show();
            },
            success: function(data) {
              console.log(data);
              $("#loading").hide();

              let items     = [];
              let responses = data.data;
              if (responses.length > 0) {

                responses.forEach(function callback(value, index) {
                  let NomorBarcode  = '"' + value.BarcodeNumber + '"';
                  let NomorBarcode2 = "'" + value.BarcodeNumber + "'";
                  let Sequent       = "'" + value.Sequent + "'";
                  let Sequents      = value.Sequent;
                  let NoDoc         = "<?php echo WH_FIFO; ?>";
                  let QRIndex       = "'#qrcodeTable_" + index + "'";
                  let Nomor         = index + 1;
                  if (value.Shapes == 'Kotak') {
                    bentuk = '<svg width="65" height="50"><rect x="0" y="0" width="65" height="50" style="fill:'+ value.Colors +';stroke:black;stroke-width:4" /></svg>';
                  } else {
                    bentuk = '<svg width="65" height="50"><polygon points="30, 0 0, 50 65, 50" style="fill:' + value.Colors + ';stroke:black;stroke-width:2" /></svg>';
                  }

                  let Weight = '';
                  if (value.Weight > 1) {
                    Weight = '<small class="font-italic fs-9 font-weight-bold">Neto: ' + value.Weight + ' Kg</small>';
                  } else {
                    Weight = '';
                  }

                  let LotNumber = '';
                  if (value.LotNumber == '' || value.LotNumber == null) {
                    LotNumber = '';
                  } else {
                    LotNumber = '<span class="pull-right">Lot No: ' + value.LotNumber + '</span>';
                  }

                  //<div id="preview-fifo" class="col-md-5">' +
                  items.push('<div id="preview_fifo_' + Sequents + '" class="col-md-5 preview-fifo">' +
                              '<p class="d-print-none fs-8 font-weight-bold mt-3">' + 
                                Nomor + '. ' + value.BarcodeNumber +
                                '<button onclick="hapus_barcode(' + NomorBarcode2 + ');" type="button" title="Hapus Barcode ' + value.BarcodeNumber + '" class="btn btn-danger btn-sm pull-right mb-2">' +
                                  '<i class="fa fa-trash"></i>'+
                                '</button>' +
                                '<button onclick="hide_barcode(' + Sequent + ');" type="button" title="Sembunyikan Barcode ' + value.BarcodeNumber + '" class="btn btn-dark btn-sm pull-right mb-2 mr-1">' +
                                  '<i class="fa fa-eye"></i>'+
                                '</button>' +
                              '</p>' +
                              '<table class="table table-bordered" border="1" width="100%">' +
                                '<thead>' +
                                  '<tr>' +
                                    '<td><h6 class="fw-bold">PT. MULTI ARTA SEKAWAN</h6></td>' +
                                    '<td class="text-center"><h6 class="fw-bold">'+ value.MonthName.toUpperCase() +'</h6></td>' +
                                  '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                  '<tr>' +
                                    '<td class="align-middle"><p class="font-weight-bold">WAREHOUSE <span class="pull-right">' + NoDoc + '</span></p></td>' +
                                    '<td rowspan="2" class="text-center">'+ bentuk +'</td>' +
                                  '</tr>' +
                                  '<tr>' +
                                    '<td class="text-center align-middle"><p class="font-weight-bold">'+ value.PONumber +'</p></td>' +
                                  '</tr>' +
                                  '<tr>' +
                                    '<td class="text-center align-middle"><h2 class="fw-bold">FIFO CARD</h2></td>' +
                                    '<td class="text-center" rowspan="3">' +
                                      '<div id="qrcodeTable_'+ index +'" class="mt-2"></div>' + Weight +
                                    '</td>' +
                                  '</tr>' +
                                  '<tr>' +
                                    '<td class="align-middle">' +
                                      '<p class="fs-9 font-weight-bold">' + value.PartID + '</p>' +
                                      '<p class="fs-special">' + value.PartName + '</p>' +
                                    '</td>' +
                                  '</tr>' +
                                  '<tr>' +
                                    '<td class="align-middle"><p class="font-weight-bold">DATE IN : ' + value.TglCetak + '<span class="pull-right">' + value.Sequent + '</span>' + '</p></td>' +
                                  '</tr>' +
                                  '<tr>' +
                                    '<td class="align-middle" colspan="2"><p class="fs-9 font-weight-bold">Supplier : ' + value.SupplierType + '. ' + value.SupplierName  + LotNumber + '</p></td>' +
                                  '<tr>' +
                                '</tbody>' +
                              '</table>' +
                            '</div>');
                  
                  var head    = document.getElementsByTagName('head')[0];
                  var script  = document.createElement('script');
                  script.innerHTML = '$(document).ready(function() { jQuery('+ QRIndex +').qrcode({render	: "canvas", width: 90, height: 90, ecLevel: "H", text: '+ NomorBarcode +'}) })';
                  head.appendChild(script);

                  $("#loading").hide();
                });

                $("#isi-preview").html(items);
              } else {
                
              }
            }, 
            error: function() {
              $("#loading").hide();
              alert('Oops error ketika show data terpilih');
            }
          });
        }
      }

      //FUNCTION SAVE CETAK PO
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
              location.reload();
              //openInNewTab(data.Url);
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

      //FUNCTION CALL PREVIEW FIFO CARD
      function show_fifo_card(PONumbers) 
      {
        $.ajax({
          url: "<?php echo base_url(); ?>incoming/get_fifo_card_single",
          dataType: 'JSON',
          data: {
            PONumber: PONumbers
          },
          type: 'POST',
          beforeSend: function() {
            $("#loading").show();
          },
          success: function(data) {
            $("#loading").hide();

            let items       = [];
            let bentuk      = '';
            let option      = [];
            let OptionGroup = [];
            let responses   = data.datas;
            let selects     = data.data_select;
            OptionGroup.push('<option value="all">Tampilkan semua item</option>');
            OptionGroup.push('<option value="00" disabled>-- Pilih --</option>');
            if (responses.length > 0) {

              const groupedData = Object.groupBy(selects, item => item.PartName);
              for (const groupKey in groupedData) {
                const group = groupedData[groupKey];
                OptionGroup.push('<optgroup label="' + groupKey + '">');
                for (const item of group) {
                  let isi_nilai = item.TglBuat + '|' + item.PONumber + '|' + item.PartID;
                  OptionGroup.push('<option value="' + isi_nilai + '">' +
                                    '(' + item.TglBuat + ') - ' + item.QtyCetak + 'pcs - ' + item.PONumber + ' - ' + item.PartID +
                                  '</option>');
                }
                OptionGroup.push('</optgroup>');
              }

              responses.forEach(function callback(value, index) {
                let NomorBarcode  = '"' + value.BarcodeNumber + '"';
                let NomorBarcode2 = "'" + value.BarcodeNumber + "'";
                let Sequent       = "'" + value.Sequent + "'";
                let NoDoc         = "<?php echo WH_FIFO; ?>";
                let Sequents      = value.Id;
                let QRIndex       = "'#qrcodeTable_" + index + "'";
                let Nomor         = index + 1;
                if (value.Shapes == 'Kotak') {
                  bentuk = '<svg width="65" height="50"><rect x="0" y="0" width="65" height="50" style="fill:'+ value.Colors +';stroke:black;stroke-width:4" /></svg>';
                } else {
                  bentuk = '<svg width="65" height="50"><polygon points="30, 0 0, 50 65, 50" style="fill:' + value.Colors + ';stroke:black;stroke-width:2" /></svg>';
                }

                let Weight = '';
                if (value.Weight > 1) {
                  Weight = '<small class="font-italic fs-9 font-weight-bold">Neto: ' + value.Weight + ' Kg</small>';
                } else {
                  Weight = '';
                }

                let LotNumber = '';
                if (value.LotNumber == '' || value.LotNumber == null) {
                  LotNumber = '';
                } else {
                  LotNumber = '<span class="pull-right">Lot No: ' + value.LotNumber + '</span>';
                }

                items.push('<div id="preview_fifo_' + Sequents + '" class="col-md-5 preview-fifo">' +
                            '<p class="d-print-none fs-8 font-weight-bold mt-3">' + 
                              Nomor + '. ' + value.BarcodeNumber +
                              '<button onclick="hapus_barcode(' + NomorBarcode2 + ');" type="button" title="Hapus Barcode ' + value.BarcodeNumber + '" class="btn btn-danger btn-sm pull-right mb-2 d-print-none">' +
                                '<i class="fa fa-trash"></i>'+
                              '</button>' +
                              '<button onclick="hide_barcode(' + Sequents + ');" type="button" title="Sembunyikan Barcode ' + value.BarcodeNumber + '" class="btn btn-dark btn-sm pull-right mb-2 mr-1 d-print-none">' +
                                '<i class="fa fa-eye"></i>'+
                              '</button>' +
                            '</p>' +
                            '<table class="table table-bordered" border="2" width="100%" cellpadding="0" cellspacing="0">' +
                              '<thead>' +
                                '<tr>' +
                                  '<td><h6 class="fw-bold">PT. MULTI ARTA SEKAWAN</h6></td>' +
                                  '<td class="text-center"><h6 class="fw-bold">'+ value.MonthName.toUpperCase() +'</h6></td>' +
                                '</tr>' +
                              '</thead>' +
                              '<tbody>' +
                                '<tr>' +
                                  '<td class="align-middle"><p class="font-weight-bold">WAREHOUSE <span class="pull-right">' + NoDoc + '</span></p></td>' +
                                  '<td rowspan="2" class="text-center">'+ bentuk +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                  '<td class="text-center align-middle"><p class="font-weight-bold">'+ value.PONumber +'</p></td>' +
                                '</tr>' +
                                '<tr>' +
                                  '<td class="text-center align-middle"><h2 class="fw-bold">FIFO CARD</h2></td>' +
                                  '<td class="text-center" rowspan="3">' +
                                    '<div id="qrcodeTable_'+ index +'" class="mt-2"></div>' + Weight +
                                  '</td>' +
                                '</tr>' +
                                '<tr>' +
                                  '<td class="align-middle">' +
                                    '<p class="fs-9 font-weight-bold">' + value.PartID + '</p>' +
                                    '<p class="fs-special">' + value.PartName + '</p>' +
                                  '</td>' +
                                '</tr>' +
                                '<tr>' +
                                  '<td class="align-middle"><p class="font-weight-bold">DATE IN : ' + value.TglCetak + '<span class="pull-right">' + value.Sequent + '</span>' + '</p></td>' +
                                '</tr>' +
                                '<tr>' +
                                  '<td class="align-middle" colspan="2"><p class="fs-9 font-weight-bold">Supplier : ' + value.SupplierType + '. ' + value.SupplierName  + LotNumber + '</p></td>' +
                                '<tr>' +
                              '</tbody>' +
                            '</table>' +
                          '</div>');
                
                var head    = document.getElementsByTagName('head')[0];
                var script  = document.createElement('script');
                script.innerHTML = '$(document).ready(function() { jQuery('+ QRIndex +').qrcode({render	: "canvas", width: 90, height: 90, ecLevel: "H", text: '+ NomorBarcode +'}) })';
                head.appendChild(script);

                $("#loading").hide();
              });

              $("#isi-preview").html(items);
              $("#isi_total_rekap").html(data.total_cetak);
              $("#pilihan_data").html(OptionGroup);
            }
          }, 
          error: function() {
            $("#loading").hide();
            alert('Oops error ketika show data fifo card');
          }
        });
      }

      $(document).ready(function() 
      {
        $("#loading").hide();
        let PONumber = "<?php echo $nomor_po ?>";
        show_fifo_card(PONumber);

        table = $('#example').DataTable({
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
          "serverSide": false,
          "order": [],
          "ajax": {
            "url": "<?php echo base_url(); ?>incoming/get_fifo_card_single",
            "type": "POST",
            "data": function(data) {
              data.PONumber   = "<?php echo $nomor_po ?>";
            }
          },

          "aoColumns": [
            { "No": "No" , "sClass": "text-end"},
            { "#": "#" , "sClass": "text-center"},
            { "PO Number": "PO Number" , "sClass": "text-start" },
            { "Cetak": "Cetak" , "sClass": "text-end" },
            { "Tgl. Cetak": "Tgl. Cetak" , "sClass": "text-center" },
            { "Supplier Name": "Supplier Name" , "sClass": "text-start" },
            { "Part ID": "Part ID" , "sClass": "text-start" },
            { "Part Name": "Part Name" , "sClass": "text-start" }
          ],

          'order': [
            [1, 'asc']
          ]
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