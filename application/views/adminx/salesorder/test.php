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
  <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />

  <!-- Mobiscroll JS and CSS Includes -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>files/eventcalendar-month-viewdemo/css/mobiscroll.jquery.min.css">
  <!-- <script src="<?php echo base_url(); ?>files/eventcalendar-month-viewdemo/js/mobiscroll.jquery.min.js"></script> -->
  <style>
    .mbsc-ios.mbsc-schedule-event {
      width: 15% !important;
    }

    .mbsc-timeline-resource-footer,
    .mbsc-timeline-resource-header,
    .mbsc-timeline-resource-title,
    .mbsc-timeline-sidebar-footer,
    .mbsc-timeline-sidebar-header,
    .mbsc-timeline-sidebar-resource-title {
      font-size: 12px;
      /* color: red; */
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
                          <div class="card-block m-t-10 m-b-30">
                            <div class="dt-responsive table-responsiveXX">
                              <div class="form-group row">
                                <label class="col-md-2 col-sm-12 col-form-label m-t-10">Filter data by</label>
                                <div class="col-md-4 col-sm-12 m-t-10">
                                  <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span>
                                  </div>
                                  <input type="hidden" name="start_date" id="start_date">
                                  <input type="hidden" name="end_date" id="end_date">
                                </div>
                                <div class="col-md-3 col-sm-12 m-t-10">
                                  <button id="btnCari" type="button" class="btn btn-info btn-full-mobile" onclick="cari();">TAMPILKAN</button>
                                </div>
                              </div>
                              <hr>

                              <div mbsc-page class="demo-month-view table table-striped">
                                <div style="height:100%">
                                  <div id="demo-month-view" class=""></div>
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

  <!-- <div id="loading-screen" class="loading">Loading&#8230;</div> -->

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
  <script src="<?php echo base_url(); ?>files/eventcalendar-month-viewdemo/js/mobiscroll.jquery.min.js"></script>

  <script>
    mobiscroll.setOptions({
      locale: mobiscroll.localeEn, // Specify language like: locale: mobiscroll.localePl or omit setting to use default
      theme: 'ios', // Specify theme like: theme: 'ios' or omit setting to use default
      themeVariant: 'light' // More info about themeVariant: https://docs.mobiscroll.com/5-25-1/eventcalendar#opt-themeVariant
    });

    // $(function() {
    //   // Mobiscroll Event Calendar initialization
    //   $('#demo-month-view').mobiscroll().eventcalendar({
    //     view: { // More info about view: https://docs.mobiscroll.com/5-25-1/eventcalendar#opt-view
    //       timeline: {
    //         type: 'month'
    //       }
    //     },
    //     data: [{ // More info about data: https://docs.mobiscroll.com/5-25-1/eventcalendar#opt-data
    //       start: '2023-07-02T00:00',
    //       end: '2023-07-05T00:00',
    //       title: 'Event 1',
    //       resource: 1
    //     }, {
    //       start: '2023-07-10T09:00',
    //       end: '2023-07-15T15:00',
    //       title: 'Event 2',
    //       resource: 3
    //     }, {
    //       start: '2023-07-12T00:00',
    //       end: '2023-07-14T00:00',
    //       title: 'Event 3',
    //       resource: 4
    //     }, {
    //       start: '2023-07-15T07:00',
    //       end: '2023-07-20T12:00',
    //       title: 'Event 4',
    //       resource: 5
    //     }, {
    //       start: '2023-07-03T00:00',
    //       end: '2023-07-10T00:00',
    //       title: 'Event 5',
    //       resource: 6
    //     }, {
    //       start: '2023-07-10T08:00',
    //       end: '2023-07-11T20:00',
    //       title: 'Event 6',
    //       resource: 7
    //     }, {
    //       start: '2023-07-22T00:00',
    //       end: '2023-07-28T00:00',
    //       title: 'Event 7',
    //       resource: 7
    //     }, {
    //       start: '2023-07-08T00:00',
    //       end: '2023-07-13T00:00',
    //       title: 'Event 8',
    //       resource: 15
    //     }, {
    //       start: '2023-07-25T00:00',
    //       end: '2023-07-27T00:00',
    //       title: 'Event 9',
    //       resource: 10
    //     }, {
    //       start: '2023-07-20T00:00',
    //       end: '2023-07-23T00:00',
    //       title: 'Event 10',
    //       resource: 12
    //     }],
    //     resources: [{ // More info about resources: https://docs.mobiscroll.com/5-25-1/eventcalendar#opt-resources
    //       id: 1,
    //       name: 'Resource A',
    //       color: '#e20000'
    //     }, {
    //       id: 2,
    //       name: 'Resource B',
    //       color: '#76e083'
    //     }, {
    //       id: 3,
    //       name: 'Resource C',
    //       color: '#4981d6'
    //     }, {
    //       id: 4,
    //       name: 'Resource D',
    //       color: '#e25dd2'
    //     }, {
    //       id: 5,
    //       name: 'Resource E',
    //       color: '#1dab2f'
    //     }, {
    //       id: 6,
    //       name: 'Resource F',
    //       color: '#d6d145'
    //     }, {
    //       id: 7,
    //       name: 'Resource G',
    //       color: '#34c8e0'
    //     }, {
    //       id: 8,
    //       name: 'Resource H',
    //       color: '#9dde46'
    //     }, {
    //       id: 9,
    //       name: 'Resource I',
    //       color: '#166f6f'
    //     }, {
    //       id: 10,
    //       name: 'Resource J',
    //       color: '#f7961e'
    //     }, {
    //       id: 11,
    //       name: 'Resource K',
    //       color: '#34c8e0'
    //     }, {
    //       id: 12,
    //       name: 'Resource L',
    //       color: '#af0000'
    //     }, {
    //       id: 13,
    //       name: 'Resource M',
    //       color: '#446f1c'
    //     }, {
    //       id: 14,
    //       name: 'Resource N',
    //       color: '#073138'
    //     }, {
    //       id: 15,
    //       name: 'Resource O',
    //       color: '#4caf00'
    //     }]
    //   });
    // });
  </script>
  <script type="text/javascript">
    function getRandomColor() {
      var colors = [];
      for (var i = 0; i < 3; i++) {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var x = 0; x < 6; x++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        colors.push(color);
      }
      return colors;
    };

    function get_data_awal() {
      const start_date = $("#start_date").val();
      const end_date = $("#end_date").val();

      $.ajax({
        url: "<?php echo base_url(); ?>sales_order_customer/sales_order_by_po",
        type: "POST",
        data: {
          start_date: start_date,
          end_date: end_date
        },
        dataType: "JSON",
        success: function(data) {
          //console.log(data.data);
          const isi = data.data;

          if (isi.length > 0) {
            let data_kiri = {
              'id': '',
              'name': '',
              'color': ''
            };

            let data_kanan = {
              'start': '',
              'end': '',
              'title': '',
              'resource': ''
            };

            const array_data_kiri = [];
            const array_data_kanan = [];
            let nomor = 0;

            isi.forEach(function(value, key) {
              // console.log(value.NoBukti);
              // console.log(key);
              data_kiri = {
                'id': key,
                'name': "No. " + ++key + " - " + value.PartnerName + " - " + value.NoBukti + " - " + value.tgl,
                'color': getRandomColor()
              };

              data_kanan = {
                'start': value.tgl,
                'end': value.tgl,
                'title': "- " + value.PoCustomer + "<br>- " + value.PartID + "<br>- " + value.PartName,
                'resource': key
              };

              array_data_kiri.push(data_kiri);
              array_data_kanan.push(data_kanan);
            });

            mobiscroll.setOptions({
              theme: 'ios',
              themeVariant: 'light'
            });

            $('#demo-month-view').mobiscroll().eventcalendar({
              // view: {
              //   timeline: {
              //     type: 'month'
              //   }
              // },
              // view: {
              //   timeline: {
              //     type: 'month',
              //     startDay: 1,
              //     endDay: 6,
              //     eventList: true,
              //     weekNumbers: true
              //   }
              // },
              view: {
                timeline: {
                  type: 'month',
                  timeCellStep: 1440,
                  timeLabelStep: 1440
                }
              },
              data: array_data_kanan,
              resources: array_data_kiri
            });
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }


    function simpanNobukti(noBukti) {
      // console.log(noBukti);
      localStorage.setItem("periode_awal_sales", $('#start_date').val());
      localStorage.setItem("periode_awal_sales", $('#start_date').val());
      localStorage.setItem("no_so_sales", noBukti);
    }

    //RANGE DATE PICKER
    $(function() {
      var start = moment().subtract(14, 'days');
      var end = moment();

      function cb(start, end) {
        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        var start_date = start.format('YYYY-MM-DD');
        var end_date = end.format('YYYY-MM-DD');
        $("#start_date").val(start_date);
        $("#end_date").val(end_date);
      }

      $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
            'month')]
        }
      }, cb);

      cb(start, end);
    });

    //FUNCTION CARI
    function cari() {
      reload_table();
    }

    //FUNCTION RELOAD TABLE
    function reload_table() {
      table.ajax.reload(null, false);
    }

    //show isi table
    $(document).ready(function() {
      get_data_awal();
    });
  </script>
</body>

</html>