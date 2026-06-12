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
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/timeline.css" />
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />



  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
  <style>
  /* CSS untuk mengatur scroll horizontal */
  .fc-scroller.fc-time-grid-container {
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
  }

  .fc-time-grid {
    min-width: 1200px;
    /* Atur lebar konten yang ingin ditampilkan untuk tiga bulan */
  }
  </style>
</head>

<body>

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
                            </h5>
                          </div>
                          <div class="card-block m-t-30 m-b-30">
                            <div class="dt-responsive table-responsiveXX">
                              <div id="calendar"></div>
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

  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/popper.js/js/popper.min.js">
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/bootstrap/js/bootstrap.min.js">
  </script>

  <script src="<?php echo base_url(); ?>files/assets/pages/waves/js/waves.min.js"></script>

  <script type="text/javascript"
    src="<?php echo base_url(); ?>files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>

  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/modernizr.js">
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/modernizr/js/css-scrollbars.js">
  </script>

  <script src="<?php echo base_url(); ?>files/assets/js/pcoded.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/js/vertical/vertical-layout.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/js/script.js"></script>

  <script>
  $(document).ready(function() {
    $("#calendar").fullCalendar({
      defaultView: "listMonth", // Tampilan awal adalah list mingguan
      // header: {
      //   left: "prev,next today",
      //   center: "title",
      //   right: "listWeek,listMonth",
      // },
      // views: {

      //   listWeek: {
      //     buttonText: "List Week",
      //   },
      //   listMonth: {
      //     type: "basic",
      //     duration: {
      //       months: 3
      //     }, // Menampilkan tiga bulan
      //     buttonText: "List Month",
      //   },
      // },
      views: {
        listDay: {
          buttonText: 'list day'
        },
        listWeek: {
          buttonText: 'list week'
        },
        listMonth: {
          type: "basic",
          duration: {
            months: 3
          }, // Menampilkan tiga bulan
          buttonText: 'list month'
        }
      },

      header: {
        left: "prev,next today",
        center: 'title',
        right: 'listDay,listWeek,listMonth'
      },
      events: function(start, end, timezone, callback) {
        // Use $.ajax() to fetch events data from your API
        $.ajax({
          url: "<?php echo base_url(); ?>schedule_tanggal/schedule_list",
          dataType: "json",
          success: function(data) {
            // Assuming your API returns an array of event objects
            var events = data.data;
            callback(events);
          },
          error: function() {
            // Handle error if the request fails
          },
        });
      },
    });

  });
  </script>
</body>

</html>