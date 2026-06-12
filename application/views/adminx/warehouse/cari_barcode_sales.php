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
    <?php //$this->load->view('adminx/components/header_css_datatable'); 
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
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
                              <h5><?php echo strtoupper($nama_halaman); ?></h5>
                            </div>
                            <div class="card-block m-t-30 m-b-30">
                              <div class="text-center" style="background: #eee;">
                                <video id="previewKamera" style="width: 300px;height: 300px;"></video>
                                <br>
                                <div class="form-group row justify-content-center">
                                  <select id="pilihKamera" class="form-control" style="width: 40%;">
                                  </select>
                                </div>
                              </div>
                              <hr>
                              <form id="scanForm">
                                <div class="form-group row justify-content-center">
                                  <input type="search" id="code_barcode" name="code_barcode" class="form-control form-control-round form-control-uppercase text-center form-control-lg form-txt-danger form-control-danger form-search" autofocus="on" autocomplete="off" placeholder="SCAN BARCODE DISINI">
                                </div>
                              </form>
                              <hr class="m-t-10 m-b-10">
                              <div class="dt-responsive">
                                <div class="form-group row">
                                  <label class="col-md-2 col-sm-12 col-form-label m-t-30">Filter data by</label>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select name="tanggal" id="tanggal" class="form-control">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Pilih --</option>
                                      <?php
                                      $now = date('d');
                                      for ($day = 1; $day <= 31; $day++) {
                                        if ($day <= 9) {
                                          if ($day == $now) {
                                            echo "<option selected value = '0" . $day . "'>0" . $day . "</option>";
                                          } else {
                                            echo "<option value = '0" . $day . "'>0" . $day . "</option>";
                                          }
                                        } else {
                                          if ($day == $now) {
                                            echo "<option selected value = '" . $day . "'>" . $day . "</option>";
                                          } else {
                                            echo "<option value = '" . $day . "'>" . $day . "</option>";
                                          }
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="bulan" id="bulan" required="required">
                                      <option value="All">All</option>
                                      <option disabled="disabled">-- Bulan --</option>
                                      <?php
                                      $now  = new DateTime('now');
                                      $bln1 = $now->format('m');
                                      for ($m = 1; $m <= 12; ++$m) {
                                        if ($bln1 == $m) {
                                          echo '<option selected value=' . $m . '>' . date('F', mktime(0, 0, 0, $m, 1)) . '</option>' . "\n";
                                        } else {
                                          echo '<option  value=' . $m . '>' . date('F', mktime(0, 0, 0, $m, 1)) . '</option>' . "\n";
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-sm-12 m-t-30">
                                    <select class="form-control" name="tahun" id="tahun" required="required">
                                      <option disabled="disabled">-- Tahun --</option>
                                      <?php
                                      $now    = new DateTime('now');
                                      $year1  = $now->format('Y');
                                      for ($year = 2022; $year <= 2050; ++$year) {
                                        if ($year1 == $year) {
                                          echo '<option selected value=' . $year . '>' . $year . '</option>' . "\n";
                                        } else {
                                          echo '<option  value=' . $year . '>' . $year . '</option>' . "\n";
                                        }
                                      }
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-md-3 col-sm-12 m-t-30">
                                    <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                  </div>
                                </div>
                                <hr>
                                <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">NO.</th>
                                      <th class="text-center">NO. DO</th>
                                      <th class="text-center">PO CUSTOMER</th>
                                      <th class="text-center">BARCODE ID</th>
                                      <th class="text-center">LOKASI SCAN</th>
                                      <th class="text-center">APPROVED BY</th>
                                      <th class="text-center">DIVISI</th>
                                      <th class="text-center">TGL. SCAN</th>
                                      <th class="text-center">JLH. BOX</th>
                                      <th class="text-center">QTY. BOX</th>
                                      <th class="text-center">QTY. ORDER</th>
                                      <th class="text-center">PART ID</th>
                                      <th class="text-center">PART NAME</th>
                                      <th class="text-center">CUSTOMER</th>
                                      <th class="text-center">DRIVER + MOBIL</th>
                                    </tr>
                                  </thead>
                                  <tbody id="body_barcode_sales"></tbody>
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

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Tambahkan Driver dan Mobil</h4>
            <button type="button" class="close" aria-label="Close" onclick="reset_all()">
              <span aria-hidden="true">&times;<modalForm/span>
            </button>
          </div>
          <div class="modal-body">
            <form id="RegisterValidation">
              <div class="form-group row">
                <div class="col-sm-10">
                  <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                      <label class="form-check-label" for="flexSwitchCheckDefault">Ekspedisi</label>
                  </div>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nama Driver</label>
                <div class="col-sm-9">
                  <input type="text" id="nama_driver_input" name="nama_driver" class="form-control text-capitalize" hidden disabled>
                  <select id="nama_driver_select" name="nama_driver" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="BENI">BENI</option>
                    <option value="CARMONO">CARMONO</option>
                    <option value="ROHMAN">ROHMAN</option>
                    <option value="WAHYUDIN">WAHYUDIN</option>
                    <option value="MUHAMAD AHYADI MA'RUF">MUHAMAD AHYADI MA'RUF</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">No. Polisi</label>
                <div class="col-sm-9">
                  <input type="text" id="no_polisi_input" name="no_polisi" class="form-control text-uppercase" hidden disabled>
                  <select id="no_polisi_select" name="no_polisi" class="form-control">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="A 8552 ZT">A 8552 ZT</option>
                    <option value="A 9372 ZA">A 9372 ZA</option>
                    <option value="A 9403 ZX">A 9403 ZX</option>
                    <option value="A 1193 YE">A 1193 YE</option>
                   <option value="A 8762 YX">A 8762 YX</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Checker</label>
                <div class="col-sm-9">
                  <select id="Checker2" name="Checker2" class="form-control">
                    <option value="0" selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="CARMONO">CARMONO</option>
                    <option value="SLAMET HARYONO">SLAMET HARYONO</option>
                    <!-- <option value="FAJAR MAULANA">FAJAR MAULANA</option> -->
                    <option value="MUSTAKIM">MUSTAKIM</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Persiapan Planning</label>
                <div class="col-sm-9">
                  <input type="text" id="PersiapanPlanning" name="PersiapanPlanning" value="SLAMET HARYONO" class="form-control" placeholder="Persiapan Planning" maxlength="8" autocomplete="off" data-required="true" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Keterangan</label>
                <div class="col-sm-9">
                  <textarea name="Notes" id="Notes" rows="3" class="form-control" placeholder="Keterangan tambahan"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <!-- <div id="collyContainer">
                <div class="form-group row mb-2 mt-2" id="collyRow1">
                  <label class="col-sm-3 col-form-label">Total Colly/ Palet</label>
                  <div class="col-7 form-error mb-1">
                    <input type="text" id="TotalColly" name="TotalColly[]" class="form-control" placeholder="Total Colly" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
                    <input type="hidden" name="kodeDetail[]" value="">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-2">
                    <a href="javascript:void(0)" class="btn btn-success" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
                  </div>
                </div>
              </div> -->
              <hr>
              <div class="form-group row">
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">No. DO :</label>
                  <input type="text" class="form-control" name="no_do" id="no_do" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">No. PO :</label>
                  <input type="text" class="form-control" name="no_po" id="no_po" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">Customer :</label>
                  <input type="text" class="form-control" name="nm_customer" id="nm_customer" readonly="readonly">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="col-form-label">QR Code :</label>
                  <input type="text" class="form-control" name="no_barcode" id="no_barcode" readonly="readonly">
                  <input type="hidden" name="part_no" id="part_no">
                  <input type="hidden" name="qty_order" id="qty_order">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
            <button id="btnSave" type="button" onclick="update_status();" class="btn btn-primary waves-effect waves-light ">Approved</button>
          </div>
        </div>
      </div>
    </div>

    <div id="loading-screen" class="loading">Loading&#8230;</div>

    <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>

    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script type="text/javascript">
      let selectedDeviceId = null;
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const sourceSelect = $("#pilihKamera");

      $(document).on('change', '#pilihKamera', function() {
        selectedDeviceId = $(this).val();
        if (codeReader) {
          codeReader.reset()
          initScanner()
        }
      })

      function initScanner() {
        codeReader
          .listVideoInputDevices()
          .then(videoInputDevices => {
            videoInputDevices.forEach(device =>
              console.log(`${device.label}, ${device.deviceId}`)
            );

            if (videoInputDevices.length > 0) {

              if (selectedDeviceId == null) {
                if (videoInputDevices.length > 1) {
                  selectedDeviceId = videoInputDevices[1].deviceId
                } else {
                  selectedDeviceId = videoInputDevices[0].deviceId
                }
              }


              if (videoInputDevices.length >= 1) {
                sourceSelect.html('');
                videoInputDevices.forEach((element) => {
                  const sourceOption = document.createElement('option')
                  sourceOption.text = element.label
                  sourceOption.value = element.deviceId
                  if (element.deviceId == selectedDeviceId) {
                    sourceOption.selected = 'selected';
                  }
                  sourceSelect.append(sourceOption)
                })
              }

              codeReader
                .decodeOnceFromVideoDevice(selectedDeviceId, 'previewKamera')
                .then(result => {

                  //hasil scan
                  console.log(result.text)
                  $("#code_barcode").val(result.text);
                  $('#scanForm').submit();
                  if (codeReader) {
                    //codeReader.reset();
                    initScanner()
                  }
                })
                .catch(err => console.error(err));
            } else {
              alert("Camera not found!")
            }
          })
          .catch(err => console.error(err));
      }

      if (navigator.mediaDevices) {
        initScanner()
      } else {
        alert('Cannot access camera.');
      }
    </script>
    <script type="text/javascript">
      $(function() {
        // EVENT HANDLER CHECKBOX EKSPEDISI
        $('#flexSwitchCheckDefault').on('change', function() {
          if ($(this).is(':checked')) {
            // Ekspedisi aktif: tampilkan input text, sembunyikan select
            $('#nama_driver_input').removeAttr('disabled').removeAttr('hidden').show();
            $('#nama_driver_select').hide();
            $('#no_polisi_input').removeAttr('disabled').removeAttr('hidden').show();
            $('#no_polisi_select').hide();
          } else {
            // Ekspedisi tidak aktif: sembunyikan input text (nilai dipertahankan), tampilkan select
            $('#nama_driver_input').prop('readonly', false).attr('disabled', true).hide();
            $('#nama_driver_select').show();
            $('#no_polisi_input').prop('readonly', false).attr('disabled', true).hide();
            $('#no_polisi_select').show();
          }
        });

        $("#scanForm").submit(function() {

          $.ajax({
            url: "<?php echo base_url(); ?>warehouse/cari_barcode",
            data: $('#scanForm').serialize(),
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
              $("#loading-screen").show();
            },
            success: function(hasil) {
              if (hasil.status_code == 200) {
                $("#loading-screen").hide();
                var no_do       = hasil.data[0].nodo;
                var no_po       = hasil.data[0].pocustomer;
                var no_barcode  = hasil.data[0].barcodeid;
                //var nm_customer   = hasil.customer.PartnerName;
                var nm_customer = hasil.data[0].customer;
                var part_no     = hasil.data[0].partid;
                var qty_order   = parseFloat(hasil.data[0].qtyorder);
                //SET DRIVER
                if (hasil.detail1) {
                  $('#nama_driver_select').val(hasil.detail1.nama_driver);
                  $('#nama_driver_input').val(hasil.detail1.nama_driver);
                  $('#no_polisi_select').val(hasil.detail1.no_polisi);
                  $('#no_polisi_input').val(hasil.detail1.no_polisi);
                  $('#part_no').val(hasil.detail1.part_id);
                  $('#qty_order').val(hasil.detail1.qty_order);

                  // SET EKSPEDISI
                  if (hasil.detail1.ekspedisi === 'Y') {
                    $('#flexSwitchCheckDefault').prop('checked', true);
                    $('#nama_driver_input').removeAttr('disabled').removeAttr('hidden').show();
                    $('#nama_driver_select').hide();
                    $('#no_polisi_input').removeAttr('disabled').removeAttr('hidden').show();
                    $('#no_polisi_select').hide();
                  } else {
                    $('#flexSwitchCheckDefault').prop('checked', false);
                    $('#nama_driver_input').prop('readonly', false).attr('disabled', true).hide();
                    $('#nama_driver_select').show();
                    $('#no_polisi_input').prop('readonly', false).attr('disabled', true).hide();
                    $('#no_polisi_select').show();
                  }
                  if (hasil.detail1.checker !== null && hasil.detail1.checker !== '') {
                    $('#Checker2').val(hasil.detail1.checker);
                  } else {
                    $('#Checker2').val('0');
                  }

                  if (hasil.detail1.persiapan_planning !== null && hasil.detail1.persiapan_planning !== '') {
                    $('#PersiapanPlanning').val(hasil.detail1.persiapan_planning);
                  } else {
                    $('#PersiapanPlanning').val('');
                  }

                  if (hasil.detail1.notes !== null && hasil.detail1.notes !== '') {
                    $('#Notes').val(hasil.detail1.notes);
                  } else {
                    $('#Notes').val('');
                  }
                }

                if (hasil.detail2.length > 0) {
                  var html  = '';
                  hasil.detail2.forEach((item, index) => {
                    let rowNumber = index + 1;
                    html += `
                      <div class="form-group row mb-2 mt-2" id="collyRow${rowNumber}">
                        <label class="col-sm-3 col-form-label">Total Colly/ Palet</label>
                        <div class="col-7 form-error mb-1">
                          <input type="text" name="TotalColly[]" value="${item.Total}" class="form-control" placeholder="Total Colly" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
                          <input type="hidden" name="kodeDetail[]" value="${item.Id}">
                          <span class="help-block"></span>
                        </div>
                        <div class="col-2">
                          ${rowNumber == 1 
                            ? `<a href="javascript:void(0)" class="btn btn-success text-bottom" id="plus${rowNumber}" title="Tambah Kolom"><span class="fa fa-plus"></span></a>` 
                            : `<a href="javascript:void(0)" class="btn btn-danger text-bottom" onclick="hapusRow('collyRow${rowNumber}')" title="Hapus Kolom"><span class="fa fa-minus"></span></a>`
                          }
                        </div>
                      </div>
                    `;
                  });

                  $('#collyContainer').html(html);
                }

                open_modal_driver(no_do, no_po, no_barcode, nm_customer, part_no, qty_order);
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
          return false;
        });
      });

      function reset_all() 
      {
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('.modal-title').text('Tambahkan Driver dan Mobil');

        $('#collyContainer').html(`
          <div class="form-group row mb-2 mt-2" id="collyRow1">
            <label class="col-sm-3 col-form-label">Total Colly/ Palet</label>
            <div class="col-7 form-error mb-1">
              <input type="text" name="TotalColly[]" class="form-control" placeholder="Total Colly" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
              <input type="hidden" name="kodeDetail[]" value="">
              <span class="help-block"></span>
            </div>
            <div class="col-2">
              <a href="javascript:void(0)" class="btn btn-success" id="plus1" title="Tambah Kolom"><span class="fa fa-plus"></span></a>
            </div>
          </div>
        `);
      }

      //OPEN MODAL DRIVER
      function open_modal_driver(no_do, no_po, no_barcode, nm_customer, part_no, qty_order) 
      {
        console.log('aaa');
        $('#no_do').val(no_do);
        $('#no_po').val(no_po);
        $('#no_barcode').val(no_barcode);
        $('#nm_customer').val(nm_customer);
        $('#part_no').val(part_no);
        $('#qty_order').val(qty_order);
        $('#modal').modal('show');
      }

      //APPROVED BARCODE
      function update_status_OLD() {

        var nama_driver = $('#nama_driver').val();
        var no_polisi = $('#no_polisi').val();
        console.log(nama_driver);
        console.log(no_polisi);
        if (nama_driver == '' || nama_driver == null) {
          alert("Nama driver harus diisi");
          $("#nama_driver").focus();
        } else if (no_polisi == '' || no_polisi == null) {
          alert("Nomor polisi harus diisi");
          $("#no_polisi").focus();
        } else {

          Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang sudah di approved tidak bisa dirubah",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approved!',
            cancelButtonText: 'Tidak, jangan'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: "<?php echo base_url(); ?>warehouse/approved_status",
                type: "POST",
                data: $('#RegisterValidation').serialize(),
                dataType: "JSON",
                beforeSend: function() {
                  $("#loading-screen").show();
                },
                success: function(data) {
                  $("#loading-screen").hide();
                  if (data.status_code == 200 || data.status == 'success') {
                    Swal.fire(
                      data.status.toUpperCase(),
                      data.message,
                      'success'
                    )
                    location.reload();
                  } else {
                    Swal.fire(
                      'Oops!',
                      data.message,
                      'info'
                    )
                    $('#modal').modal('hide');
                  }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  $("#loading-screen").hide();
                }
              });
            } else {
              console.log("aaa");
            }
          })
        }
      }

      function update_status() 
      {
        var form_data = $('#RegisterValidation').serializeArray();

        // Tambahkan nilai checkbox ekspedisi ke payload (tidak ikut serializeArray jika unchecked)
        var ekspedisi = $('#flexSwitchCheckDefault').is(':checked') ? 'Y' : 'N';
        form_data.push({ name: 'ekspedisi', value: ekspedisi });

        $.ajax({
          url: "<?php echo base_url(); ?>warehouse/approved_status",
          dataType: 'JSON',
          data: form_data,
          type: 'POST',
          beforeSend: function () {
            $("#loading-screen").show();
            $("#btnSave").prop('disabled', true);
          },
          success: function (data) {
            $(".form-group").removeClass('has-error');
            $(".help-block").remove();
            $(".help-block").html('');

            if (data.status == 'success') {
              $("#loading-screen").hide();
              $('#modal').modal('hide');
              $('#RegisterValidation')[0].reset();
              reload_table();
              reset_all();
            } else if (data.status == 'error') {
              $("#loading-screen").hide();
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: data.message
              });
            } else if (data.status == 'forbidden') {
              $("#loading-screen").hide();
              Swal.fire('FORBIDDEN', 'Access Denied', 'info');
            } else {
              $("#loading-screen").hide();

              for (var i = 0; i < data.inputerror.length; i++) {
                  var inputName = data.inputerror[i];
                  var errorMsg = data.error_string[i];

                  var arrayMatch = inputName.match(/^(\w+)\[(\d+)\]$/);
                  if (arrayMatch) {
                      var arrayName = arrayMatch[1];
                      var arrayIndex = parseInt(arrayMatch[2]);
                      var inputElem = $('[name="' + arrayName + '[]"]').eq(arrayIndex);
                      if (!inputElem.prop('disabled')) {
                          inputElem.closest('.form-error').addClass('has-error');
                          if (inputElem.next('.help-block').length === 0) {
                              inputElem.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                          }
                      }
                  } else {
                      var inputElem = $('[name="' + inputName + '"]:not(:disabled)');
                      inputElem.each(function () {
                          var $el = $(this);
                          $el.closest('.form-error').addClass('has-error');
                          if ($el.next('.help-block').length === 0) {
                              $el.after('<span class="help-block text-danger">' + errorMsg + '</span>');
                          }
                      });
                  }
              }
            }

            $("#btnSave").text('Approved');
            $("#btnSave").prop('disabled', false);
          },
          error: function () {
            $("#loading").hide();
            alert('Error adding / update data');
            $('#btnSave').text('Save');
            $('#btnSave').prop('disabled', false);
          }
        });
      }

      function hapusRow(rowId)
      {
        const row         = $('#' + rowId);
        // Ambil data sebelum dihapus
        const no_barcode  = $('input[name="no_barcode"]').val();
        const no_do       = $('input[name="no_do"]').val();
        const no_po       = $('input[name="no_po"]').val();
        const nm_customer = $('input[name="nm_customer"]').val();
        const IdDetail    = row.find('input[name="kodeDetail[]"]').val();

        Swal.fire({
          title: "Yakin ingin hapus?",
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, hapus",
          cancelButtonText: "Batal"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?php echo base_url(); ?>warehouse/hapus_single_row",
              type: "POST",
              dataType: "JSON",
              data: {
                Barcode: no_barcode,
                Id: IdDetail
              },
              beforeSend: function() {
                $("#loading-screen").show();
              },
              success: function(data) {
                if (data.status == 'forbidden') {
                  $("#loading-screen").hide();
                  Swal.fire('FORBIDDEN', 'Access Denied', 'info');
                } else {
                  $("#loading-screen").hide();
                  open_modal_driver(no_do, no_po, no_barcode, nm_customer, part_no, qty_order);
                  // Hapus elemen
                  row.remove();
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $("#loading").hide();
                alert('Error hapus data');
              }
            });
          }
        });
      }

      //FUNCTION CARI BERDASARKAN TANGGAL
      function cari() {
        reload_table();
      }

      //FUNCTION RELOAD TABLE
      function reload_table() {
        table.ajax.reload(null, false);
      }

      $(document).ready(function() {
        $("#loading-screen").hide();

        // TAMBAH KOLOM JUMLAH
        $(document).on('click', '#plus1', function () {
          let count = $('#collyContainer .form-group').length + 1;
          let row = `
            <div class="form-group row mb-2 mt-2" id="collyRow${count}">
              <label class="col-sm-3 col-form-label">Total Colly/ Palet</label>
              <div class="col-7 form-error mb-1">
                <input type="text" name="TotalColly[]" class="form-control" placeholder="Total Colly" maxlength="8" oninput="AllowDecimalAndComma(this)" autocomplete="off" data-required="true">
                <span class="help-block"></span>
                <input type="hidden" name="kodeDetail[]" value="">
              </div>
              <div class="col-2">
                <a href="javascript:void(0)" class="btn btn-danger remove-kolom-jumlah" title="Hapus Kolom"><span class="fa fa-minus"></span></a>
              </div>
            </div>
            `;
          $('#collyContainer').append(row);
        });

        // HAPUS KOLOM JUMLAH
        $(document).on('click', '.remove-kolom-jumlah', function () {
          $(this).closest('.form-group').remove();
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
          fixedColumns: {
            leftColumns: 1,
            rightColumns: 0
          },
          'processing': true,
          'serverSide': false,
          'serverMethod': 'post',
          'ajax': {
            url: "<?php echo base_url(); ?>warehouse/produk_terkirim_list",
            type: 'POST',
            "data": function(data) {
              data.bulan = $('#bulan').val();
              data.tahun = $('#tahun').val();
              data.tanggal = $('#tanggal').val();
            }
          },

          'aoColumns': [{
              "NO": "NO",
              "sClass": "text-right"
            },
            {
              "BARCODE ID": "BARCODE ID",
              "sClass": "text-center"
            },
            {
              "NO. DO": "NO. DO",
              "sClass": "text-left"
            },
            {
              "PO CUSTOMER": "PO CUSTOMER",
              "sClass": "text-left"
            },
            {
              "LOKASI SCAN": "LOKASI SCAN",
              "sClass": "text-center"
            },
            {
              "APPROVED BY": "APPROVED BY",
              "sClass": "text-left"
            },
            {
              "DIVISI": "DIVISI",
              "sClass": "text-left"
            },
            {
              "TGL. SCAN": "TGL. SCAN",
              "sClass": "text-center"
            },
            {
              "JLH. BOX": "JLH. BOX",
              "sClass": "text-center"
            },
            {
              "QTY. BOX": "QTY. BOX",
              "sClass": "text-right"
            },
            {
              "QTY. ORDER": "QTY. ORDER",
              "sClass": "text-right"
            },
            {
              "PART ID": "PART ID",
              "sClass": "text-left"
            },
            {
              "PART NAME": "PART NAME",
              "sClass": "text-left"
            },
            {
              "CUSTOMER": "CUSTOMER",
              "sClass": "text-left"
            },
            {
              "DRIVER + MOBIL": "DRIVER + MOBIL",
              "sClass": "text-left"
            }
          ],

          "columnDefs": [{
            "targets": [1], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        $("#nama_driver_select").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#nama_driver_input").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#no_polisi_input").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#no_polisi_select").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#Checker2").change(function() {
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $('#collyContainer').on('input change', 'input', function() {
          $(this).closest('.form-error').removeClass('has-error');
          $(this).siblings('.help-block').empty();
        });

        // $("#flexSwitchCheckDefault").change(function() {
        //   const isChecked = $(this).is(":checked");

        //   // Toggle 'hidden' and 'disabled' attributes between select and input
        //   $("#nama_driver_select, #no_polisi_select").attr("hidden", isChecked).prop("disabled", isChecked);
        //   $("#nama_driver_input, #no_polisi_input").attr("hidden", !isChecked).prop("disabled", !isChecked);
        // });
        $('#flexSwitchCheckDefault').on('change', function () {
          if ($(this).is(':checked')) {
              // Show input, hide select
            $('#nama_driver_input').prop('hidden', false).prop('disabled', false).attr('name', 'nama_driver');
            $('#nama_driver_select').prop('hidden', true).prop('disabled', true).removeAttr('name');

            $('#no_polisi_input').prop('hidden', false).prop('disabled', false).attr('name', 'no_polisi');
            $('#no_polisi_select').prop('hidden', true).prop('disabled', true).removeAttr('name');
          } else {
              // Show select, hide input
            $('#nama_driver_input').prop('hidden', true).prop('disabled', true).removeAttr('name');
            $('#nama_driver_select').prop('hidden', false).prop('disabled', false).attr('name', 'nama_driver');

            $('#no_polisi_input').prop('hidden', true).prop('disabled', true).removeAttr('name');
            $('#no_polisi_select').prop('hidden', false).prop('disabled', false).attr('name', 'no_polisi');
          }
        });

      });
    </script>
  </body>
</html>