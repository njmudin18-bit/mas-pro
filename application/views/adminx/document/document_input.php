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
						                  <h5>
						                  	<?php echo strtoupper($nama_halaman); ?>
						                  	<span class="pull-right">
                                  <button id="btn_qr" class="btn btn-danger" onclick="createQR();">BUAT QR CODE</button>
						                  		<button class="btn btn-info" onclick="openModal();">TAMBAH</button>
						                  	</span>
						                  </h5>
						                </div>
						                <div class="card-block">
						                  <div class="dt-responsive table-responsive">
						                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%">
						                      <thead class="bg-primary text-center">
						                       	<tr>
							                        <th class="text-center" width="7%">No</th>
							                        <th class="text-center" width="5%">#</th>
                                      <th class="text-center" width="10%">#</th>
							                        <th class="text-center" width="5%">Aktif?</th>
							                        <th class="text-center" width="8%">Type</th>
							                        <th class="text-center">Nomor Document</th>
                                      <th class="text-center">Nama Document</th>
                                      <th class="text-center">Department</th>
                                      <th class="text-center">File</th>
							                        <th class="text-center">Upload date</th>
							                      </tr>
						                    	</thead>
							                    <tbody>
							                      
							                    </tbody>
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
	          <form id="RegisterValidation" enctype="multipart/form-data">
	          	<input type="hidden" value="" name="kode" >
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Department</label>
                <div class="col-sm-9">
                  <select id="nama_department" name="nama_department" class="form-control" required="required">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <?php 
                      foreach ($department as $key => $value) {
                        ?>
                        <option value="<?php echo $value->DEPTID; ?>"><?php echo $value->DEPTNAME; ?></option>
                        <?php
                      }
                    ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Type Doc.</label>
                <div class="col-sm-9">
                  <select id="type_doc" name="type_doc" class="form-control" required="required">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <?php 
                      foreach ($type_doc as $key => $value) {
                        ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->nama_type; ?></option>
                        <?php
                      }
                    ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Aktif ?</label>
                <div class="col-sm-9">
                  <select name="aktif" id="aktif" class="form-control" required="required">
                    <option selected="selected" disabled="disabled">-- Pilih --</option>
                    <option value="Ya">Ya</option>
                    <option value="Tidak">Tidak</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nomor Document</label>
                <div class="col-sm-9">
                  <input type="text" id="no_doc" name="no_doc" class="form-control text-uppercase" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Judul Document</label>
                <div class="col-sm-9">
                  <input type="text" id="judul_doc" name="judul_doc" class="form-control text-capitalize" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">File Document</label>
                <div class="col-sm-9">
                  <input type="file" id="file" name="file" class="form-control" required="required" accept="application/pdf">
                  <span class="help-block"></span>
                </div>
              </div>
              <div id="show_pdf" class="form-group row">
                <embed id="pdf_view" type="application/pdf" src="" width="100%" height="400"></embed>
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
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
	  <script>
	  	var save_method;
      var url;

      function createQR() {
        var chekedValue     = [];
        $('.ids:checked').each(function(){
          chekedValue .push($(this).val());
        })

        var formData = {
          'id_doc': chekedValue
        };

        $.ajax({
          url : '<?php echo base_url(); ?>qr/generate_qr_banyak',
          type: "POST",
          data: formData,
          dataType: "JSON",
          beforeSend: function() {
            $("#btn_qr").html('Processing...');
            $('#btn_qr').attr('disabled', true); //set button disable 
          },
          success: function(data)
          {
            if(data.status == 'success' && data.data.length > 0)
            {
              localStorage.setItem("data_qr", JSON.stringify(data.data));
              window.open('<?php echo base_url(); ?>qr/generate_qr', "_blank");
            } else {
              Swal.fire(
                'Oops',
                'Gagal',
                'info',
              )
            }
            $('#btn_qr').text('BUAT QR CODE'); //change button text
            $('#btn_qr').attr('disabled',false); //set button enable 
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error adding / update data');
            $('#btn_qr').text('BUAT QR CODE'); //change button text
            $('#btn_qr').attr('disabled',false); //set button enable 
          }
        });
      }

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#show_pdf').hide();
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Document'); // Set Title to Bootstrap modal title

        document.getElementById('pdf_view').src = '';
      }

      function closeModal(){
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Document');
      }

      //FUNCTION RESET
      function reset() {
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah Document');
      }

      //FUNCTION EDIT
      function edit(id) {

        save_method = 'update';
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url(); ?>document/document_edit/" + id,
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
                $('[name="kode"]').val(data.id);
                $('[name="nama_department"]').val(data.id_dept);
                $('[name="type_doc"]').val(data.id_doc_type);
                $('[name="aktif"]').val(data.is_aktif);
                $('[name="no_doc"]').val(data.nomor_document);
                $('[name="judul_doc"]').val(data.nama_document);
                $('#modal').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Document'); // Set title to Bootstrap modal title
                $('#btnSave').text('Update'); // Set title to Bootstrap modal title
                $('#show_pdf').show();
                var link        = '<?php echo base_url(); ?>files/uploads/docx/';
                var html_string = link + data.nama_file;

                document.getElementById('pdf_view').src = html_string;
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
              url: '<?php echo base_url(); ?>document/document_deleted/' + id,
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
      }

      //VALIDATION AND ADD USER
      function save()
      {
        var url;

        if(save_method == 'add') {
          url = "<?php echo base_url(); ?>document/document_add";
        } else {
          url = "<?php echo base_url(); ?>document/document_update";
        }

        var form      = $('#RegisterValidation')[0];
        var form_data = new FormData(form);

        // ajax adding data to database
        $.ajax({
          url: url,
          dataType: 'JSON', 
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'POST',
          beforeSend: function (response) {
            $("#btnSave").prop('disabled', true);
            $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
          },
          success: function (data) {
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
            }
            else
            {
              for (var i = 0; i < data.inputerror.length; i++) 
              {
                $('[name="'+data.inputerror[i]+'"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 
          },
          error: function (response) {
            alert('Error adding / update data');
            $('#btnSave').text('Save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 
          }
        });
      };

	    $(document).ready(function() {

	      table = $('#order-table').DataTable({ 
	      		"pagingType": "full_numbers",
	      		"lengthMenu": [
		          [10, 25, 50, -1],
		          [10, 25, 50, "All"]
		        ],
		        responsive: true,
		        language: {
		          search: "_INPUT_",
		          searchPlaceholder: "Search records",
		        },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
              "url": "<?php echo base_url(); ?>document/document_list",
              "type": "POST",
            },

            "aoColumns": [
              { "No": "No" , "sClass": "text-right"},
              { "#": "#" , "sClass": "text-center" },
              { "#": "#" , "sClass": "text-center" },
              { "Aktif?": "Aktif?" , "sClass": "text-center" },
              { "Type": "Type" , "sClass": "text-center" },
              { "Nomor Document": "Nomor Document" , "sClass": "text-left" },
              { "Nama Document": "Nama Document" , "sClass": "text-left" },
              { "Department": "Department" , "sClass": "text-center" },
              { "File": "File" , "sClass": "text-left" },
              { "Create date": "Create date" , "sClass": "text-center" }
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

	      $("#nama_department").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#type_doc").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#aktif").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#no_doc").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#judul_doc").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });

        $("#file").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
	    });
	  </script>
	</body>
</html>