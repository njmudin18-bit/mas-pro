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

    <?php //$this->load->view('adminx/components/header_css_datatable'); ?>
    <?php $this->load->view('adminx/components/header_css_datatable_v2'); ?>
    <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" /> -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/plugins/al-range-slider/build/plugin/css/al-range-slider.css" />
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css"> -->
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
						                <div class="card-header">
                              <h5 class="text-center"><?php echo strtoupper($nama_halaman); ?></h5>
						                </div>
						                <div class="card-block">
                              <div class="form-group row">
                                <label class="col-md-1 col-sm-12 col-form-label m-t-3">Filter</label>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select class="form-control" id="StatusProjek" name="StatusProjek" required="required">
                                    <option disabled="disabled" value="">-- Status --</option>
                                    <option value="ALL" selected>All</option>
                                    <?php 
                                      foreach ($status_project as $key => $value) {
                                        ?>
                                        <option value="<?php echo $value->id_status; ?>"><?php echo $value->nama_status; ?></option>
                                        <?php
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <select class="form-control" id="KategoriProjek" name="KategoriProjek" required="required">
                                    <option disabled="disabled" value="">-- Pilih --</option>
                                    <option value="ALL" selected>All</option>
                                    <?php 
                                      foreach ($kategori_project as $key => $value) {
                                        ?>
                                        <option value="<?php echo $value->id_kategori; ?>"><?php echo $value->nama_kategori; ?></option>
                                        <?php
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-3">
                                  <select class="form-control" name="TanggalProjek" id="TanggalProjek">
                                    <option value="ALL">All</option>
                                    <?php
                                    // Array bulan Indonesia
                                    $bulanIndo = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];

                                    // Awal mulai (Desember 2022)
                                    $start = new DateTime('2022-12-01');
                                    // Bulan sekarang
                                    $end   = new DateTime();
                                    $end->modify('first day of this month');

                                    $current = date('Y-m'); // untuk menandai bulan aktif

                                    while ($start <= $end) {
                                      $value = $start->format('Y-m'); // contoh: 2022-12
                                      $bulan = (int)$start->format('m'); // ambil angka bulan
                                      $tahun = $start->format('Y');
                                      $label = $bulanIndo[$bulan] . ' ' . $tahun; // contoh: Desember 2022
                                      $selected = ($value == $current) ? 'selected' : '';
                                      echo "<option value=\"$value\" $selected>$label</option>";
                                      $start->modify('+1 month'); // maju 1 bulan
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-sm-12 m-t-3">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                                <div class="col-md-1 col-sm-12 m-t-3 text-right">
                                  <button type="button" class="btn btn-success" onclick="openModal()">TAMBAH</button>
                                </div>
                              </div>
                              <hr>
						                  <div class="dt-responsive table-responsive">
						                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
						                      <thead class="bg-primary text-center">
						                       	<tr>
							                        <th class="text-center" width="7%">No</th>
							                        <th class="text-center" width="10%">#</th>
							                        <th class="text-center">Nama Project</th>
							                        <th class="text-center" width="5%">Progress</th>
							                        <th class="text-center">Status</th>
							                        <th class="text-center">Kategori</th>
							                        <th class="text-center">PIC</th>
							                        <th class="text-center">Institusi</th>
							                        <th class="text-center">URL</th>
							                        <th class="text-center">Start date</th>
							                        <th class="text-center">End date</th>
							                        <th class="text-center">Create date</th>
							                      </tr>
						                    	</thead>
							                    <tbody>
							                      
							                    </tbody>
						                  	</table>
						                	</div>
                              <p>NO DOKUMEN : <?= $no_document ?></p>
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

		<div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Modal title</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
	        </div>
	        <div class="modal-body">
	          <form id="RegisterValidation">
	          	<input type="hidden" value="" name="kode" >
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Department</label>
                <div class="col-sm-10">
                  <input type="text" id="nama_dept" name="nama_dept" class="form-control" required="required" value="<?php echo $this->session->userdata('user_dept_name'); ?>" readonly>
                  <input type="hidden" name="kode_dept" value="<?php echo $this->session->userdata('user_dept_id'); ?>">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Project</label>
                <div class="col-sm-10">
                  <input type="text" id="nama_project" name="nama_project" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                	<select class="form-control js-example-basic-multipleX" id="status" name="status" required="required">
                		<option disabled="disabled" selected="selected" value="">-- Pilih --</option>
	                  <?php 
	                  	foreach ($status_project as $key => $value) {
	                  		?>
	                  		<option value="<?php echo $value->id_status; ?>"><?php echo $value->nama_status; ?></option>
	                  		<?php
	                  	}
	                  ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-10">
                	<select class="form-control js-example-basic-multiple2X" id="kategori" name="kategori" required="required">
                		<option disabled="disabled" selected="selected" value="">-- Pilih --</option>
	                  <?php 
	                  	foreach ($kategori_project as $key => $value) {
	                  		?>
	                  		<option value="<?php echo $value->id_kategori; ?>"><?php echo $value->nama_kategori; ?></option>
	                  		<?php
	                  	}
	                  ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama PIC</label>
                <div class="col-sm-10">
                  <select class="form-control js-example-basic-multipleX" id="nama_pic" name="nama_pic" required="required">
                    <option disabled="disabled" selected="selected" value="">-- Pilih --</option>
                    <?php 
                      foreach ($karyawan as $key => $value) {
                        ?>
                        <option value="<?php echo $value->SSN; ?>"><?php echo $value->NAME; ?></option>
                        <?php
                      }
                    ?>
                  </select>
                  <!-- <input type="text" id="nama_pic" name="nama_pic" class="form-control" required="required"> -->
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Institusi</label>
                <div class="col-sm-10">
                	<select class="form-control js-example-basic-multiple3X" id="institusi" name="institusi" required="required">
                		<option disabled="disabled" selected="selected" value="">-- Pilih --</option>
	                  <?php 
	                  	foreach ($institusi_project as $key => $value) {
	                  		?>
	                  		<option value="<?php echo $value->id_institusi; ?>"><?php echo $value->nama; ?></option>
	                  		<?php
	                  	}
	                  ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">URL/ Link</label>
                <div class="col-sm-10">
                  <input type="text" id="project_url" name="project_url" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                  <textarea id="project_desc" name="project_desc" rows="3" class="form-control" required="required"></textarea>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Progress</label>
                <div class="col-sm-10">
                	<!-- <div class="basic"></div> -->
                	<input type="range" class="custom-range" id="progress" name="progress" min="0" max="100" step="5" oninput="$('#rangeval').html($(this).val())">
                	<span class="badge badge-danger"><span id="rangeval" style="font-size: 14px;">0</span>%</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tgl. Mulai</label>
                <div class="col-sm-4">
                  <input type="date" id="start_date" name="start_date" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
                <label class="col-sm-2 col-form-label">Tgl. Selesai</label>
                <div class="col-sm-4">
                  <input type="date" id="end_date" name="end_date" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
           	</form>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	          <button id="btnSave" type="button" onclick="save();" class="btn btn-primary waves-effect waves-light ">Simpan</button>
	        </div>
      	</div>
    	</div>
  	</div>

  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
	  <?php //$this->load->view('adminx/components/bottom_js_datatable'); ?>
    <?php $this->load->view('adminx/components/bottom_js_datatable_v2'); ?>
	  <!-- <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script> -->
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/plugins/al-range-slider/build/plugin/js/al-range-slider.js"></script>
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script> -->
	  <script>
	  	var save_method;
      var url;

      const options = {
			  orientation: "horizontal",
			  range: { min: 0, max: 100, step: 10,  },
			  theme: "dark"
			};
      
      $('.basic').alRangeSlider(options);

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Project'); // Set Title to Bootstrap modal title
      }

      function closeModal(){
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Project');
      }

      //FUNCTION RESET
      function reset() {
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Project');
      }

      //FUNCTION EDIT
      function edit(id) {

        save_method = 'update';
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('project/project_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
              if (data.status == 'forbidden'){
                Swal.fire(
                  'FORBIDDEN',
                  'Access Denied',
                  'info',
                )
              } else {
                $('[name="kode"]').val(data.id_project);
                $('[name="nama_project"]').val(data.nama_project);
                $('[name="status"]').val(data.id_status);
                $('[name="kategori"]').val(data.id_kategori);
                $('[name="nama_pic"]').val(data.id_pic);
                $('[name="institusi"]').val(data.id_institusi);
                $('[name="project_url"]').val(data.project_url);
                $('[name="project_desc"]').val(data.project_description);
                $('[name="progress"]').val(data.project_progress);
                $('#rangeval').html(data.project_progress);
                $('[name="start_date"]').val(data.start_date);
                $('[name="end_date"]').val(data.end_date);
                $('#modal').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Project'); // Set title to Bootstrap modal title
                $('#btnSave').text('Update'); // Set title to Bootstrap modal title
              }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
      }

      //FUNCTION HAPUS
      function openModalDelete(id) {
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
              url: '<?php echo site_url('project/project_hapus') ?>/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
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
                  $("#"+id).remove();
                  Swal.fire(
  								  'Sukses!',
  								  'Anda sukses menghapus data',
  								  'success'
  								)
                  reload_table();
                }
              }
            });
				  }
				})
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null,false);
      };

      //VALIDATION AND ADD USER
      function save()
      {
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if(save_method == 'add') {
          url = "<?php echo site_url('project/project_add')?>";
        } else {
          url = "<?php echo site_url('project/project_update')?>";
        }

        // ajax adding data to database
        $.ajax({
            url : url,
            type: "POST",
            data: $('#RegisterValidation').serialize(),
            dataType: "JSON",
            success: function(data)
            {
              if(data.status == 'ok') //if success close modal and reload ajax table
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
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
              }
              $('#btnSave').text('Save'); //change button text
              $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error adding / update data');
              $('#btnSave').text('Save'); //change button text
              $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
      };

      //FUNCTION CARI
      function cari() 
      {
        reload_table();
      }

	    $(document).ready(function() {

	    	$(".js-example-basic-multiple").select2();
	    	$(".js-example-basic-multiple2").select2();
	    	$(".js-example-basic-multiple3").select2();

	      table = $('#order-table').DataTable({
          dom: 'Bfrltip',
          buttons: [
            {
              extend: 'pdfHtml5',
              text: 'Export PDF',
              title: '',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A4',
              exportOptions: {
                columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
              },
              customize: function (doc) {
                const month = $('#TanggalProjek').find('option:selected').text().toUpperCase();

                function formatRibuan(num) {
                  if (num === null || num === undefined) return '0';
                  if (typeof num === 'number') {
                      return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                  }
                  let str = num.toString();
                  const cleaned = str.replace(/[^\d.,-]/g, '');
                  const normalized = cleaned.replace(',', '.');
                  const n = parseFloat(normalized);

                  return isNaN(n) ? str : n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
                }

                doc.defaultStyle.fontSize = 10;
                doc.pageMargins           = [10, 40, 10, 60];
                doc.styles = {
                  subheader: {
                    fontSize: 12,
                    bold: true,
                    alignment: 'left'
                  },
                  tableHeader: {
                    bold: true,
                    fontSize: 10,
                    color: 'white',
                    fillColor: '#007bff',
                    alignment: 'center'
                  }
                };

                doc.content.unshift(
                  {
                    text: 'PT. MULTI ARTA SEKAWAN',
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'LAPORAN DAFTAR PROJEK IT PERIODE ' + month,
                    bold: true,
                    fontSize: 12,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  {
                    text: 'NO DOKUMEN : ' + 'MAS/FO/IC/024 REV 01',
                    bold: true,
                    fontSize: 11,
                    style: 'subheader',
                    alignment: 'left',
                    margin: [0, 0, 0, 10]
                  }
                );

                // === Styling Main Table ===
                const mainTable = doc.content.find(item => item.table);
                if (mainTable) {
                    const alignRightCols = [0, 2];
                    const body = mainTable.table.body;

                    for (let i = 1; i < body.length; i++) {
                        for (let j = 0; j < body[i].length; j++) {
                            if (body[i][j].text !== undefined && alignRightCols.includes(j)) {
                                body[i][j].alignment = 'right';
                            }
                        }

                        // SUB TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'SUB TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                    body[i][k].fillColor = '#6c757d';
                                    body[i][k].color = '#fff';
                                }
                                break;
                            }
                        }

                        // TOTAL styling
                        for (let j = 0; j < body[i].length; j++) {
                            if (
                                typeof body[i][j].text === 'string' &&
                                body[i][j].text.trim().toUpperCase() === 'TOTAL'
                            ) {
                                for (let k = 0; k < body[i].length; k++) {
                                    body[i][k].bold = true;
                                }
                                break;
                            }
                        }
                    }

                    // Style baris terakhir
                    // const lastRowIndex = body.length - 1;
                    // for (let j = 0; j < body[lastRowIndex].length; j++) {
                    //     if (body[lastRowIndex][j].text !== undefined) {
                    //         body[lastRowIndex][j].fillColor = '#007bff';
                    //         body[lastRowIndex][j].color = '#fff';
                    //     }
                    // }

                    mainTable.layout = {
                        hLineWidth: () => 0.5,
                        vLineWidth: () => 0.5,
                        hLineColor: () => '#aaa',
                        vLineColor: () => '#aaa',
                        paddingLeft: () => 4,
                        paddingRight: () => 4,
                        paddingTop: () => 2,
                        paddingBottom: () => 2,
                        fillColor: rowIndex => (rowIndex > 0 && rowIndex % 2 === 0 ? '#ECF5FF' : null)
                    };
                }

                // === Footer ===
                doc.footer = function (currentPage, pageCount) {
                    return {
                        columns: [
                            { text: 'Printed on: ' + new Date().toLocaleString(), alignment: 'left', margin: [10, 0, 0, 0] },
                            { text: 'PT MULTI ARTA SEKAWAN - CONFIDENTIAL', alignment: 'center' },
                            { text: 'Page ' + currentPage + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 10, 0] }
                        ],
                        fontSize: 8
                    };
                };
              },
              // Tambahkan opsi filename di sini
              filename: function() {
                var month = $('#TanggalProjek').find('option:selected').text().toUpperCase();

                return 'Laporan Daftar Projek IT Periode ' + month;
              }
            }
          ],
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
            "url": "<?php echo base_url(); ?>project/project_list",
            "type": "POST",
            "data": function(data) {
            data.StatusProjek    = $('#StatusProjek').val();
            data.KategoriProjek  = $('#KategoriProjek').val();
            data.TanggalProjek   = $('#TanggalProjek').val();
          }
          },

          "aoColumns": [
            { "No": "No" , "sClass": "text-center"},
            { "#": "#" , "sClass": "text-center" },
            { "Nama Project": "Nama Project" , "sClass": "text-left" },
            { "Progress": "Progress" , "sClass": "text-center" },
            { "Status": "Status" , "sClass": "text-left" },
            { "Kategori": "Kategori" , "sClass": "text-left" },
            { "PIC": "PIC" , "sClass": "text-left" },
            { "Institusi": "Institusi" , "sClass": "text-left" },
            { "URL": "URL" , "sClass": "text-left" },
            { "Start date": "Start date" , "sClass": "text-left" },
            { "End date": "End date" , "sClass": "text-left" },
            { "Create date": "Create date" , "sClass": "text-left" }
          ],
          //Set column definition initialisation properties.
          "columnDefs": [
            { 
              "targets": [ 0 ], //last column
              "orderable": false, //set not orderable
              className: 'text-right'
            },
          ]
        });

	      $("#nama_project").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#nama_pic").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#project_url").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#project_desc").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#start_date").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#end_date").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
	    });
	  </script>
	</body>
</html>