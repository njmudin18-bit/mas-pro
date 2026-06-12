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

    <?php $this->load->view('adminx/components/header_css_calender'); ?>
    <style type="text/css">
      .fc-today {
        background-color: #ff5370 !important;
        color: #fff;
      }
    </style>
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
                              </h5>
                            </div>
                            <div class="card-block text-center m-t-30 m-b-30">
                              <div id='loader'></div>
                              <div id='calendar'></div>
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

    <?php $this->load->view('adminx/components/bottom_js_calender'); ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      "use strict";
      /*function load_data_calendar() {
        $.ajax({
          url : "<?php echo base_url() ?>project/project_time_line_list",
          type: "POST",
          dataType: "JSON",
          beforeSend: function () {
            // body...
          },
          success: function(data)
          {
            var data_project        = data.data;
            var data_calendar_array = [];

            data_project.forEach(function (task) {
              data_calendar_array.push(
                {
                  "title" : task.nama_project + " ("+ task.nama +")",
                  "start" : task.start_date,
                  "end" : task.end_date,
                  "borderColor" : getRandomColor_Calendar(),
                  "backgroundColor" : getRandomColor_Calendar(true),
                  "textColor" : '#fff'
                }
              )
            });

            setTimeout(function(){
              $('#calendar').fullCalendar({
                header: {
                  left: 'prev, next, today',
                  center: 'title',
                  right: 'month, agendaWeek, agendaDay, listMonth, listYear'
                },
                views: {
                  month: { buttonText: "Bulan ini" },
                  agendaWeek: { buttonText: "Minggu ini" },
                  agendaDay: { buttonText: "Hari ini" },
                  listYear: { buttonText: "Tahun ini" }
                },
                year: '<?php echo date("Y") ?>',
                defaultDate: '<?php echo date("Y-m-d") ?>',
                defaultView: 'month',
                navLinks: true,
                businessHours: true,
                editable: false,
                droppable: false,
                events: data_calendar_array
              });
            },350);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error get data from ajax');
          }
        });
      };*/

      function load_data_calendar() {
        $.ajax({
          url : "<?php echo base_url() ?>project/project_time_line_list",
          type: "POST",
          dataType: "JSON",
          beforeSend: function () {
            $("#loader").html('<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading...</span></div>');
          },
          success: function(data)
          {
            var data_project        = data.data;
            var data_calendar_array = [];

            data_project.forEach(function (task) {
              data_calendar_array.push(
                {
                  "title" : task.nama_project + " ("+ task.nama +")",
                  "start" : task.start_date,
                  "end" : task.end_date,
                  "borderColor" : getRandomColor_Calendar(),
                  "backgroundColor" : getRandomColor_Calendar(true),
                  "textColor" : '#fff'
                }
              )
            });

            setTimeout(function(){
              $("#loader").hide();
              var calendar = $('#calendar').fullCalendar({
                // put your options and callbacks here
                header: {
                  left: 'prev, next, today',
                  center: 'title',
                  right: 'year, month, basicWeek, basicDay',
                },
                //timezone: 'local',
                height: 'auto',
                selectable: false,
                dragabble: false,
                defaultView: 'year',
                yearColumns: 4,
                durationEditable: false,
                bootstrap: false,
                events: data_calendar_array
              });
            },350);
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            $("#loader").hide();
            alert('Error get data from ajax');
          }
        });
      }


      $(document).ready(function() {
        load_data_calendar();
      });
    </script>
  </body>
</html>