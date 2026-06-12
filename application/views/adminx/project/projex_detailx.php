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
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/widget.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet"> -->
    <link href="<?php echo base_url(); ?>files/assets/plugins/summernote-0.8.18-dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/plugins/al-range-slider/build/plugin/css/al-range-slider.css" />

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <style type="text/css">
    	.image-center {
    		display: block;
		    height: auto;
		    max-width: 100%;
		    margin: 0 auto;
		    object-fit: cover;
    	}

    	.box-center {
		    width: 50px;
		    height: 50px;
		    position: absolute;
		    top: 50%;
		    left: 38%;
		    margin: -25px 0 0 -25px;
    	}

    	#task-table thead{
    		display: none;
    	}

    	#order-table thead{
    		display: none;
    	}

    	.coret {
    		text-decoration: line-through;
    	}

    	.icofont {
    		cursor: pointer;
    	}

    	.circle {
    		position: absolute;
			  top: 50%;
			  left: 50%;
			  transform: translate(-50%, -50%);
    		font-size: 73px;
		    background: #ffb64d;
			  width: 300px; 
			  height: 300px;
			  border-radius: 50%;
			  display: flex; /* or inline-flex */
			  align-items: center; 
			  justify-content: center;
			}
    </style>
  </head>
  <body class="project_detail">

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
													<div class="card proj-progress-card">
														<div class="card-block">
															<div class="row">
																<div class="col-xl-12 col-md-12">
																	<h5 class="m-b-30 f-w-700 text-center"><?php echo strtoupper($project_details->nama_project); ?> PROGRESS</h5>

																	<input type="range" class="custom-range" id="progress" name="progress" min="0" max="100" step="5" value="<?php echo $project_details->project_progress; ?>" oninput="sliderChange(this.value)">
                									<span class="badge badge-danger">
                										<span id="rangeval" style="font-size: 14px;"><?php echo $project_details->project_progress; ?></span>%
                									</span>
																</div>
															</div>
															<div class="row m-t-10">
																<div class="col-lg-12 col-xl-12">

																	<!-- TAB START -->
                                  <ul class="nav nav-tabs md-tabs" role="tablist">
                                    <li class="nav-item">
                                      <a class="nav-link active" data-toggle="tab" href="#home3" role="tab">Detail</a>
                                      <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-toggle="tab" href="#profile3" role="tab">Catatan</a>
                                      <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-toggle="tab" href="#messages3" role="tab">Files</a>
                                      <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-toggle="tab" href="#settings3" role="tab">Menu atau Modul</a>
                                      <div class="slide"></div>
                                    </li>
                                  </ul>
                                  <!-- TAB END -->

                                  <div class="tab-content card-block">
                                    <div class="tab-pane active" id="home3" role="tabpanel">
                                    	<div class="row">
                                    		<div class="col-sm-7">
																					<div class="row">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Nama Project</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->nama_project; ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Status</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->nama_status; ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Kategori</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->nama_kategori; ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Institusi</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->nama; ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">URL</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <a href="<?php echo $project_details->project_url; ?>" target="_blank"><?php echo $project_details->project_url; ?></a></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Tgl. Mulai</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo date_indo($project_details->start_date); ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Tgl. Selesai</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo date_indo($project_details->end_date); ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Tgl. Buat</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->create_date; ?></h5>
																						</div>
																					</div>
																					<div class="row m-t-10">
																						<div class="col-sm-3">
																							<h5 class="col-form-label">Deskripsi</h5>
																						</div>
																						<div class="col-sm-9">
																							<h5 class="col-form-label">: <?php echo $project_details->project_description; ?></h5>
																						</div>
																					</div>
																				</div>
																				<div class="col-sm-5">
																					<div id="rangeval2" class="circle"><?php echo $project_details->project_progress; ?>%</div>
																				</div>
                                    	</div>  
                                    </div>

                                    <!-- FORM CATATAN -->
                                    <div class="tab-pane" id="profile3" role="tabpanel">
                                    	<div id="section_noted">
	                                    	
																			</div>
                                    	<form id="form_catatan">
                                    		<input type="hidden" id="kode" name="kode">
	                                      <textarea id="catatan" name="catatan" rows="5">
				                                </textarea>
				                                <input type="hidden" name="id_project" 
				                                value="<?php echo $project_details->id_project; ?>">
				                                <button type="button" id="btn_save_catatan" class="btn btn-primary m-t-20" onclick="save_catatan()">SIMPAN CATATAN</button>
			                                </form>
                                    </div>

                                    <!-- FORM UPLOAD -->
                                    <div class="tab-pane" id="messages3" role="tabpanel">
                                    	<h5 class="col-form-label">Tambahkan file terkait tentang projek disini.
                                    	<button class="btn btn-info float-right" onclick="openModal();">TAMBAH FILES</button>
                                    	</h5>
                                    	<hr>
                                    	<table id="order-table" class="table table-striped table-borderless" width="100%">
                                    		<thead>
                                    			<tr class="bg-primary">
                                    				<th class="text-center" width="5%">No</th>
                                    				<th class="text-center" width="8%">#</th>
                                    				<th class="text-center" width="35%">Title</th>
                                    				<th class="text-center" width="20%">Nama File</th>
                                    				<th class="text-center" width="10%">Type File</th>
                                    				<th class="text-center" width="30%">Tgl. Upload</th>
                                    			</tr>
                                    		</thead>
                                    		<tbody></tbody>
                                    	</table>
                                    </div>

                                    <!-- FORM TASK -->
                                    <div class="tab-pane" id="settings3" role="tabpanel">
                                    	<div class="row">
																			  <div class="col-xl-12">
																			    <div class="cardX">
																			      <div class="card-headerX m-b-30">
																			        <h5>Daftar Menu atau Modul</h5>
																			      </div>
																			      <div class="card-blockX">
																			        <div class="form-material m-b-30">
																			          <div class="right-icon-control">
																			            <form id="form_task" class="form-material">
																			            	<input type="hidden" name="id_project" 
				                                							value="<?php echo $project_details->id_project; ?>">
																			              <div class="form-group form-primary">
																			                <input type="text" id="nama_task" name="nama_task" class="form-control" required="required">
																			                <span class="form-bar"></span>
																			                <label class="float-label">Masukan nama menu atau modul</label>
																			              </div>
																			            </form>
																			            <div class="form-icon ">
																			              <button id="btn_save_task" type="button" class="btn btn-success btn-icon  waves-effect waves-light" onclick="simpan_task()" >
																			                <i class="fa fa-plus"></i>
																			              </button>
																			            </div>
																			          </div>
																			        </div>

																			        <table id="task-table" class="table  table-striped table-borderless m-t-20" width="100%">
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
			</div>
		</div>

		<!-- MODAL TAMBAH IMAGE -->
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
	          <form id="upload_form" enctype="multipart/form-data">
	          	<input type="hidden" value="" name="kode" >
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Judul File</label>
                <div class="col-sm-10">
                	<input type="hidden" name="kode_project" value="<?php echo $project_details->id_project; ?>">
                  <input type="text" id="judul_file" name="judul_file" class="form-control" required="required" minlength="5">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">File</label>
                <div class="col-sm-10">
                  <input type="file" id="file" name="file" class="form-control" required="required">
                  <span class="help-block"></span>
                </div>
              </div>
           	</form>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	          <button id="btn_upload" type="button" onclick="upload_file();" class="btn btn-primary waves-effect waves-light ">Upload</button>
	        </div>
      	</div>
    	</div>
  	</div>

  	<!--MODAL SHOW IMAGE -->
  	<div class="modal fade" id="modal_files" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
           <h4 class="modal-title">Modal title</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
	        </div>
	        <div id="modal_body_file_show" class="modal-body">
	          
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
	        </div>
      	</div>
    	</div>
  	</div>

  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
  	<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
		<?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
	  <!-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script> -->
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/plugins/summernote-0.8.18-dist/summernote.min.js"></script>

	  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/pages/todo/todo.js"></script>
		<script>
			var save_method;
      var url;

      //FUNCTION READ SLIDER VALUE
      function sliderChange(val) {
      	$("#rangeval").html(val);
      	$("#rangeval2").html(val + "%");

      	var id = "<?php echo $project_details->id_project; ?>";

      	$.ajax({
          url : "<?php echo base_url(); ?>project/project_progress_update",
          type: "POST",
          data: {kode: id, progress: val},
          dataType: "JSON",
          beforeSend: function() {

			    },
          success: function(data)
          {
            
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error adding / update data');
          }
        });
			}

      //FUNCTION HAPUS TASK
      function deleted_task(id) {
				$.ajax({
	        url : "<?php echo base_url(); ?>task/task_deleted/" + id,
	        type: "DELETE",
	        dataType: "JSON",
	        beforeSend: function() {
			    	$("#btn_deleted_task_" + id).prop('disabled', true);
	          $("#btn_deleted_task_" + id).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
			    },
	        success: function(data)
	        {
	          reload_table_task(); //load catatan terbaru 
	        },
	        error: function (jqXHR, textStatus, errorThrown)
	        {
	          alert('Error adding / update data');
	        }
	      });
			}

      //FUNCTION UPDATE TASK 
      function update_task(id) {
      	var status_task = $("#task_" + id).val();
      	$.ajax({
          url : "<?php echo base_url(); ?>task/task_update",
          type: "POST",
          data: {kode: id, status_task: status_task},
          dataType: "JSON",
          beforeSend: function() {

			    },
          success: function(data)
          {
            reload_table();
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error adding / update data');
          }
        });
      }

      //FUNCTION SIMPAN TASK
      function simpan_task() {
      	var nama_task 	= $("#nama_task").val();
      	if (nama_task == "" ||nama_task == null) {
      		alert("Nama Task harus diisi!");
      		$("#nama_task").focus();
      	} else{

	      	$.ajax({
	          url : "<?php echo base_url(); ?>task/task_add",
	          type: "POST",
	          data: $('#form_task').serialize(),
	          dataType: "JSON",
	          beforeSend: function() {
				    	$("#btn_save_task").prop('disabled', true);
	            $("#btn_save_task").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
				    },
	          success: function(data)
	          {
	            $('#btn_save_task').html('<i class="fa fa-plus"></i>');
	            $('#btn_save_task').attr('disabled', false);
	            $('#form_task')[0].reset();
	            reload_table();
	          },
	          error: function (jqXHR, textStatus, errorThrown)
	          {
	            alert('Error adding / update data');
	            $('#btn_save_task').html('<i class="fa fa-plus"></i>');
	            $('#btn_save_task').attr('disabled', false);
	          }
	        });
	      }
      }

      //FUNCTION DELETE FILES
      function open_modal_files_delete(id) {
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
              url: '<?php echo base_url() ?>/upload/files_hapus/' + id,
              type: 'DELETE',
              error: function() {
                alert('Something is wrong');
              },
              success: function(data) {
                $("#"+id).remove();
                Swal.fire(
								  'Sukses!',
								  'Anda sukses menghapus data',
								  'success'
								)
                reload_table();
              }
            });
				  }
				})
      }

      //FUNCTION VIEW FILES
      function open_modal_files(id) {
      	$.ajax({
          url : "<?php echo base_url() ?>upload/show_only_files/" + id,
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {
          	var link 			= "<?php echo base_url(); ?>files/uploads/files/";
          	var files_ext = getExt(data.nama_file);
          	var files 		= link + data.nama_file;
          	var items 		= [];
          	if (files_ext == 'pdf') {
          		items.push('<embed src="'+ files +'" width="100%" height="700" type="application/pdf">');
          	} else {
          		items.push('<img id="files_uploader_show" src="'+ files +'" class="img-fluid img-thumbnail image-center">');
          	}

          	$("#modal_body_file_show").html(items);
          	$('.modal-title').text(data.title);
            $('#modal_files').modal('show');
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error get data from ajax');
          }
        });
      }

      //FUNCTION UPLOAD FILE
      function upload_file() {
      	var judul_file 	= $("#judul_file").val();
      	if (judul_file == "" ||judul_file == null) {
      		alert("Judul File harus diisi!");
      		$("#judul_file").focus();
      	} else if (document.getElementById("file").files.length == 0) {
      		alert("File harus diisi!");
      		$("#file").focus();
      	} else {
      		var form 			= $('#upload_form')[0];
	        var form_data = new FormData(form);

	        $.ajax({
	            url: '<?php echo base_url(); ?>upload/do_upload',
	            dataType: 'JSON', 
	            cache: false,
	            contentType: false,
	            processData: false,
	            data: form_data,
	            type: 'POST',
	            beforeSend: function (response) {
	            	$("#btn_upload").prop('disabled', true);
		            $("#btn_upload").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
	            },
	            success: function (data) {
	            	if(data.status) //if success close modal and reload ajax table
	              {
	                $('#modal').modal('hide');
	                reload_table();
	              }
	              else
	              {
	              	alert(data);
	              }
	              $('#btn_upload').text('Save'); //change button text
	              $('#btn_upload').attr('disabled',false); //set button enable 
	            },
	            error: function (response) {
	              alert('Error adding / update data');
	              $('#btn_upload').text('Save'); //change button text
	              $('#btn_upload').attr('disabled',false); //set button enable 
	            }
	        });
      	}
      }

      //FUNCTION OPEN FILE
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#upload_form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Files'); // Set Title to Bootstrap modal title
      }

      //FUNCTION RELOAD TABLE
      function reload_table(){
        table.ajax.reload(null, false);
        table_task.ajax.reload(null, false);
      }

      function reload_table_task() {
      	table_task.ajax.reload(null, false);
      }

			//HAPUS CATATAN
			function hapus_catatan(id) {
				$.ajax({
	        url : "<?php echo base_url(); ?>noted/noted_deleted/" + id,
	        type: "DELETE",
	        dataType: "JSON",
	        beforeSend: function() {
			    	$("#btn_hapus_" + id).prop('disabled', true);
	          $("#btn_hapus_" + id).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
			    },
	        success: function(data)
	        {
	          load_catatan(); //load catatan terbaru 
	        },
	        error: function (jqXHR, textStatus, errorThrown)
	        {
	          alert('Error adding / update data');
	        }
	      });
			}

			//EDIT CATATAN
			function edit_catatan(id) {
				save_method = 'update';
				$('#catatan').summernote('reset');
				$.ajax({
            url : "<?php echo site_url('noted/noted_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
              $('[name="kode"]').val(data.id);
              $('#catatan').summernote(
								'code', data.content
							);
              $('#btn_save_catatan').text('UPDATE CATATAN'); //change button text

              location.href = "#btn_save_catatan";
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
              alert('Error get data from ajax');
            }
        });
			}

			//FUNCTION SAVE CATATAN
			function save_catatan(id) {
				var textareaValue = $('#catatan').summernote('code');
				if (textareaValue.length == 36) {
					Swal.fire(
					  'Oops',
					  'Kolom catatan harus diisi',
					  'info'
					)
				} else {
	        if(save_method == 'update') {
	          url = "<?php echo base_url(); ?>noted/noted_update";
	        } else {
	          url = "<?php echo base_url(); ?>noted/noted_add";
	        }

					$.ajax({
	          url : url,
	          type: "POST",
	          data: $('#form_catatan').serialize(),
	          dataType: "JSON",
	          beforeSend: function() {
				    	$("#btn_save_catatan").prop('disabled', true);
	            $("#btn_save_catatan").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
				    },
	          success: function(data)
	          {
	            $('#catatan').summernote('reset');
	            $('#btn_save_catatan').text('SIMPAN CATATAN'); //change button text
	            $('#btn_save_catatan').attr('disabled', false); //set button enable
	            load_catatan(); //load catatan terbaru 
	          },
	          error: function (jqXHR, textStatus, errorThrown)
	          {
	            alert('Error adding / update data');
	            $('#btn_save_catatan').text('SIMPAN CATATAN'); //change button text
	            $('#btn_save_catatan').attr('disabled', false); //set button enable 
	          }
	        });
				}
			};

			//LOAD CATATAN
			function load_catatan() {
				var id = "<?php echo $project_details->id_project; ?>";

				$.ajax({
          url : "<?php echo base_url(); ?>noted/noted_list_by_project/" + id,
          type: "GET",
          dataType: "JSON",
          beforeSend: function() {
			    	
			    },
          success: function(data)
          {
            $("#section_noted").html(data.html);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error adding / update data');
          }
        });
			}

			function getExt(filepath){
			  return filepath.split("?")[0].split("#")[0].split('.').pop();
			}

			$(document).ready(function() {

				load_catatan();

				$('#catatan').summernote({
					height: 150
				});

				table = $('#order-table').DataTable({
						"dom": 'rftp',
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
              "url": "<?php echo base_url(); ?>upload/upload_list_by_project",
              "type": "POST",
              "data": function(data) {
		            data.id_project = "<?php echo $project_details->id_project; ?>";
		          }
            },

            "aoColumns": [
              { "No": "No" , "sClass": "text-right"},
              { "#": "#" , "sClass": "text-center" },
              { "Title": "Title" , "sClass": "text-left" },
              { "Nama File": "Nama File" , "sClass": "text-left" },
              { "Type File": "Type File" , "sClass": "text-center" },
              { "Tgl. Upload": "Tgl. Upload" , "sClass": "text-center" }
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

        table_task = $('#task-table').DataTable({
        		"dom": 'rftp',
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
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
              "url": "<?php echo base_url(); ?>task/task_list",
              "type": "POST",
              "data": function(data) {
		            data.id_project = "<?php echo $project_details->id_project; ?>";
		          }
            },

            "aoColumns": [
              { "" : "", "sClass": "text-center"},
              { "" : "", "sClass": "text-left" },
              { "" : "", "sClass": "text-center" },
              { "" : "", "sClass": "text-center" },
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
			});
		</script>
	</body>
</html>