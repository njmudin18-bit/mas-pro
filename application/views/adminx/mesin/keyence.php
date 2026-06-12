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
  <style>
    .custom-table thead tr, .custom-table thead th {
      font-size: 15px;
    }
  </style>
</head>

<body>
  <div id="fullscreen" class="content">
    <div class="container">
      <!-- <h2 class="mb-1 text-center"></h2> -->
      <h2 class="mb-4 text-center">
        AKTIVITAS MESIN INJEKSI <?php //echo strtoupper($perusahaan->nama); 
                                ?>
        <a data-type="clock" data-id="32268" class="tickcounter" style="display: block; width: 200px; height: 50px; margin: 10px auto" title="Time in Jakarta" href="//www.tickcounter.com/timezone/asia-jakarta">Time in Jakarta</a>
      </h2>
      <div class="table-responsive custom-table-responsive">
        <div class="float-right" id="full_screen" onclick="openFullscreen();">
          <i class="bi bi-fullscreen" style="cursor:pointer;" title="Klik to fullscreen"></i>
        </div>
        <div class="float-right mr-2" id="close_full_screen" onclick="closeFullscreen();">
          <i class="bi bi-fullscreen-exit" style="cursor:pointer;" title="Klik to exit fullscreen"></i>
        </div>

        <table class="table custom-table" width="100%">
          <thead>
            <tr class="text-white text-center bg-primary">
              <th scope="col">
                <label class="control control--checkbox">
                  <input type="checkbox" class="js-check-all" />
                  <div class="control__indicator"></div>
                </label>
              </th>
              <th scope="col" width="25%">MESIN & PART</th>
              <th scope="col">QR</th>
              <th scope="col">PLAN</th>
              <th scope="col">ACT</th>
              <th scope="col">% ACT</th>
              <th scope="col">% CT</th>
              <th scope="col">STATUS</th>
            </tr>
          </thead>
          <tbody id="data_mesin">
          </tbody>
          <tfoot>
            <tr>
              <td colspan="100"><small>NOTE: data update setiap 3 detik.</small></td>
            </tr>
          </tfoot>
        </table>

        <div id="loader_gue" class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url(); ?>files/keyence/js/jquery-3.3.1.min.js"></script>
  <script src="<?php echo base_url(); ?>files/keyence/js/popper.min.js"></script>
  <script src="<?php echo base_url(); ?>files/keyence/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>files/keyence/js/main.js"></script>
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
    var elem = document.documentElement;

    function openFullscreen() {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
        console.log('a');
        $('#full_screen').hide();
        $("#close_full_screen").show();
      } else if (elem.webkitRequestFullscreen) {
        /* Safari */
        elem.webkitRequestFullscreen();
        console.log('b');
        $('#full_screen').hide();
        $("#close_full_screen").show();
      } else if (elem.msRequestFullscreen) {
        /* IE11 */
        elem.msRequestFullscreen();
        console.log('c');
        $('#full_screen').hide();
        $("#close_full_screen").show();
      }
    }

    function closeFullscreen() {
      if (document.exitFullscreen) {
        document.exitFullscreen();
        $('#full_screen').show();
        $("#close_full_screen").hide();
      } else if (document.webkitExitFullscreen) {
        /* Safari */
        document.webkitExitFullscreen();
        $('#full_screen').show();
        $("#close_full_screen").hide();
      } else if (document.msExitFullscreen) {
        /* IE11 */
        document.msExitFullscreen();
        $('#full_screen').show();
        $("#close_full_screen").hide();
      }
    }
    //FUNGSI CALL DATA
    function show_data() {
      $.ajax({
        url: "<?php echo base_url(); ?>keyence/show_data_mesin",
        type: "GET",
        dataType: "JSON",
        beforeSend: function() {
          $("#loader_gue").show();
        },
        success: function(data) {
          $("#loader_gue").hide();
          $("#data_mesin").html(data.html);
        },
        complete: function function_name(data) {
          $("#loader_gue").hide();
          setTimeout(function() {
            show_data();
          }, 3000);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("#loader_gue").hide();
          show_data();
        }
      });
    }

    $(document).ready(function() {
      show_data();
      // $('.flipTimer').flipTimer({
      //   direction: 'up'
      // });
      $("#close_full_screen").hide();
    });
  </script>
</body>

</html>