<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/keyence/fonts/icomoon/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/keyence/css/owl.carousel.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/keyence/css/bootstrap.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>files/keyence/css/style.css">
    <title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
      .content {
          padding: 2rem 0;
          background: radial-gradient(ellipse at center, #533ce1 0%, #4087ea 100%);
          /* background-size: 250% 250%; */
      }
    </style>
  </head>
  <body>
    <div id="fullscreen" class="content">
      <div class="container">
        <h2 class="mb-3 text-center text-white">DAFTAR TELEPON EXTENSI</h2>
        <h2 class="mb-4 text-center text-white"><?php echo APPS_CORP; ?></h2>
        <div class="table-responsive custom-table-responsive">
          <table id="tabel" class="table custom-table table-striped" width="100%">
            <thead>
              <tr class="text-white text-center bg-info">
                <th scope="col">NO</th>
                <th scope="col">EXT.</th>
                <th scope="col">DEPT</th>
                <th scope="col">NAMA</th>
                <th scope="col">AKTIF?</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="<?php echo base_url(); ?>files/keyence/js/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>files/keyence/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>files/keyence/js/main.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
      (function(d, s, id) {
        var js, pjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//www.tickcounter.com/static/js/loader.js";
        pjs.parentNode.insertBefore(js, pjs);
      }(document, "script", "tickcounter-sdk"));
    </script>
    <script type="text/javascript" charset="utf-8">
      $(document).ready(function() {
        //show_data();
        table = $('#tabel').DataTable({
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
            "url": "<?php echo base_url(); ?>telepon/phone_list",
            "type": "POST",
          },

          "aoColumns": [{
              "No": "No",
              "sClass": "text-right"
            },
            {
              "EXT.": "EXT.",
              "sClass": "text-center"
            },
            {
              "DEPT.": "DEPT.",
              "sClass": "text-left"
            },
            {
              "NAMA": "NAMA",
              "sClass": "text-left"
            },
            {
              "AKTIF?": "AKTIF?",
              "sClass": "text-center"
            }
          ],

          //Set column definition initialisation properties.
          "columnDefs": [{
            "targets": [0], //last column
            "orderable": false, //set not orderable
            className: 'text-right'
          }, ]
        });

        $("#close_full_screen").hide();
      });
    </script>
  </body>
</html>