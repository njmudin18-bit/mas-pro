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
						                  		<button class="btn btn-info" onclick="openModal();">TAMBAH</button>
						                  	</span>
						                  </h5>
						                </div>
						                <div class="card-block">
						                  <div class="dt-responsive table-responsive">
						                    <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
						                      <thead class="bg-primary text-center">
						                       	<tr>
							                        <th class="text-center" width="7%">No</th>
							                        <th class="text-center" width="10%">#</th>
							                        <th class="text-center" width="5%">ID</th>
							                        <th class="text-center">Nama Kategori</th>
							                        <th class="text-center">Create date</th>
							                        <th class="text-center">Create by</th>
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
	          <form id="RegisterValidation">
	          	<input type="hidden" value="" name="kode" >
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Status</label>
                <div class="col-sm-10">
                  <input type="text" id="nama_status" name="nama_status" class="form-control" required="required">
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
	  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
	  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
	  <script>
	  	var save_method;
      var url;

      //FUNCTION OPEN MODAL CABANG
      function openModal() {
        save_method = 'add';
        $('#btnSave').text('Save');
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Status'); // Set Title to Bootstrap modal title
      }

      function closeModal(){
        $('#RegisterValidation')[0].reset();
        $('#modal').modal('hide');
        $('.modal-title').text('Tambah Status');
      }

      //FUNCTION RESET
      function reset() {
        $('#RegisterValidation')[0].reset();
        $('.modal-title').text('Tambah');
      }

      //FUNCTION EDIT
      function edit(id) {

        save_method = 'update';
        $('#RegisterValidation')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('project/project_status_edit') ?>/" + id,
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
                $('[name="kode"]').val(data.id_status);
                $('[name="nama_status"]').val(data.nama_status);
                $('#modal').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Status'); // Set title to Bootstrap modal title
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
              url: '<?php echo site_url('project/project_status_hapus') ?>/' + id,
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
        $("#btnSave").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if(save_method == 'add') {
          url = "<?php echo site_url('project/project_status_add')?>";
        } else {
          url = "<?php echo site_url('project/project_status_update')?>";
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
              } else
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
            error: function (jqXHR, textStatus, errorThrown)
            {
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
              "url": "<?php echo base_url(); ?>project/project_status_list",
              "type": "POST",
            },

            "aoColumns": [
              { "No": "No" , "sClass": "text-right"},
              { "#": "#" , "sClass": "text-center" },
              { "ID": "ID" , "sClass": "text-right" },
              { "Nama Status": "Nama Status" , "sClass": "text-left" },
              { "Create date": "Create date" , "sClass": "text-left" },
              { "Create by": "Create by" , "sClass": "text-left" }
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

	      $("#nama_kategori").change(function(){
          $(this).parent().removeClass('has-error');
          $(this).next().empty();
        });
	    });
	  </script>
	</body>
</html>