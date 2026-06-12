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
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/css_google.css">
    <!-- <style type="text/css">
      html { 
        background: url('<?php echo base_url(); ?>files/assets/images/bg-search.png') no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }
    </style> -->
  </head>
  <body>
    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-md-7 text-center">
          <img class="img-fluid" src="<?php echo base_url(); ?>files/uploads/logos/<?php echo $perusahaan->logo_name; ?>" alt="<?php echo $perusahaan->nama; ?>" 
            title="Back to Dashboard" style="cursor: pointer;" onclick="window.location.href='<?php echo base_url(); ?>adminx'" />
        </div>
      </div>
      <div class="row d-flex justify-content-center mt-5">
        <div class="col-md-7">
          <div class="input-group input-group-lg">
            <div class="input-group-prepend">
              <span class="input-group-text google" style="background-color: #fff;">
                <img class="img-document" src="https://img.icons8.com/color/48/000000/google-logo.png">
              </span>
            </div>
            <input type="search" id="cari_doc" name="cari_doc" class="form-control" required="required" placeholder="Masukan judul document disini" autofocus="on">
            <div class="input-group-append">
              <span class="input-group-text microphone" style="background-color: #fff;">
                <img class="img-document" src="https://img.icons8.com/nolan/48/000000/microphone.png">
              </span>
            </div>
          </div>
        </div>
      </div>
      <div id="show_result" class="mt-5 mb-5">
        <div id="loader"></div>
      </div>
    </div>


    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/jquery-ui.min.js"></script>
    <script type='text/javascript'>
      function get_doc_by_type(id, pilihan) {
        $.ajax({
          url: "<?php echo base_url(); ?>document/document_list_result",
          type: 'POST',
          dataType: "JSON",
          data: {search: id},
          beforeSend: function (data) {
            //$("#cari_doc").val(id);
          },
          success: function( ui ) {
            $("#loader").hide();
            var items = [];

            Object.keys(ui).forEach(key => {
              var doc_type    = "'"+ui[key].doc_type+"'";
              var id_div      = "'"+ui[key].dept+"'";
              var id_divisi   = ui[key].dept;
              var nama_divisi;
              $.ajax({
                url: "<?php echo base_url(); ?>document/get_nama_divisi",
                type: 'POST',
                dataType: "JSON",
                async: false,
                data: {id: id_divisi},
                success: function(data) {
                  var hasil   = data;
                  nama_divisi = hasil.DEPTNAME;
                },
                error: function (jqXHR, textStatus, errorThrown)
                { 

                }
              });

              //SET NEW VALUE INTO SEARCH BOX
              if (pilihan == 1) {
                $("#cari_doc").val(nama_divisi);
              } else {
                $("#cari_doc").val(id);
              }

              items.push('<div class="row mb-3">' +
                          '<div class="col-md-2 col-sm-12">' +
                            '<h6 class="cursor" class="cursor" onclick="get_doc_by_type('+id_div+', 1)" title="Klik untuk menampilkan semua document dari divisi '+nama_divisi+'">' + nama_divisi + '</h6>' +
                          '</div>' +
                          '<div class="col-md-7 col-sm-12">' +
                            '<h6><a href="' + ui[key].link_file + '" target="_blank" title="Klik untuk melihat file '+ ui[key].label +'" style="font-size: 18px;">Doc. No. #' + ui[key].nomor_doc + '</a></h6>' +
                            '<p>' + ui[key].label + '</p>' +
                            '<p><small> Doc. Type : <span class="text-c-red cursor" onclick="get_doc_by_type('+doc_type+', 2)" title="Klik untuk menampilkan document dengan type '+ui[key].doc_type+'">'+ ui[key].doc_type +'</span> </small><small class="float-right"><em>Uploader: '+ ui[key].uploader +' on ' + ui[key].tgl_upload + '</em></small></p>' +
                          '</div>' +
                          '<div class="col-md-3 col-sm-12 text-center">' +
                            '<a href="' + ui[key].link_file + '" target="_blank" title="Klik untuk melihat file '+ ui[key].label +'">' +
                              '<img class="img-fluid img-thumbnail cursor" src="<?php echo base_url(); ?>files/assets/images/pdf-icon.png" style="height: 90px">' +
                            '</a>' +
                          '</div>' +
                        '</div>');
            });

            $('#show_result').html(items);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {

          }
        });
      }

      function get_nama_divisi(id) {
        $.ajax({
          url: "<?php echo base_url(); ?>document/get_nama_divisi",
          type: 'POST',
          dataType: "JSON",
          data: {id: id},
          success: function(data) {
            var hasil = data;
            console.log(hasil);
            return data;
          },
          error: function (jqXHR, textStatus, errorThrown)
          {

          }
        });
      }

      $(document).ready(function(){
        $("#show_result").show();

        // Initialize 
        $("#cari_doc").autocomplete({
          source: function( request, response ) {
            // Fetch data
            $.ajax({
              url: "<?php echo base_url(); ?>document/document_list_result",
              type: 'POST',
              dataType: "JSON",
              data: {
                search: request.term
              },
              success: function( data ) {
                response(data);
              }
            });
          },
          select: function (event, ui) {
            // Set selection
            $('#cari_doc').val(ui.item.label); // display the selected text

            $("#show_result").show();
            var items = [];

            Object.keys(ui).forEach(key => {
              var doc_type    = "'"+ui[key].doc_type+"'";
              var id_div      = "'"+ui[key].dept+"'";
              var id_divisi   = ui[key].dept;
              var nama_divisi;
              $.ajax({
                url: "<?php echo base_url(); ?>document/get_nama_divisi",
                type: 'POST',
                dataType: "JSON",
                async: false,
                data: {id: id_divisi},
                success: function(data) {
                  var hasil   = data;
                  nama_divisi = hasil.DEPTNAME;
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
                }
              });

              items.push('<div class="row">' +
                          '<div class="col-md-2 col-sm-12">' +
                            '<h6 class="cursor" onclick="get_doc_by_type('+id_div+', 1)" title="Klik untuk menampilkan semua document dari divisi '+nama_divisi+'">' + nama_divisi + '</h6>' +
                          '</div>' +
                          '<div class="col-md-7 col-sm-12">' +
                            '<h6><a href="' + ui[key].link_file + '" target="_blank" title="Klik untuk melihat file '+ ui[key].label +'" style="font-size: 18px;">Doc. No. #' + ui[key].nomor_doc + '</a></h6>' +
                            '<p>' + ui[key].label + '</p>' +
                            '<p><small> Doc. Type : <span class="text-c-red cursor" onclick="get_doc_by_type('+doc_type+', 2)" title="Klik untuk menampilkan document dengan type '+ui[key].doc_type+'">'+ ui[key].doc_type +'</span> </small><small class="float-right"><em>Uploader: '+ ui[key].uploader +' on ' + ui[key].tgl_upload + '</em></small></p>' +
                          '</div>' +
                          '<div class="col-md-3 col-sm-12 text-center">' +
                            '<a href="' + ui[key].link_file + '" target="_blank" title="Klik untuk melihat file '+ ui[key].label +'">' +
                              '<img class="img-fluid img-thumbnail cursor" src="<?php echo base_url(); ?>files/assets/images/pdf-icon.png" style="height: 90px;">' +
                            '</a>' +
                          '</div>' +
                        '</div>');
            });

            $('#show_result').html(items);

            //$('#userid').val(ui.item.value);
            return false;
          }
        });
      });
      </script>
	</body>
</html>