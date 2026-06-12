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

  <?php $this->load->view('adminx/components/header_css_chart'); ?>
</head>

<body class="grafik">

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
                            <h5>Total Scan Label per Bulan<br>Tahun <?php echo date('Y'); ?></h5>
                          </div>
                          <div class="card-block m-b-30">
                            <div id="loader_scan" class="text-center justify-content-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                              </div>
                            </div>
                            <canvas id="graphCanvas" width="400" height="180"></canvas>
                            <!-- <div id="scan-monthly-analytics" style="height: 390px;"></div> -->
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card">
                          <div class="card-header text-center">
                            <h5>Total Scan Label Sebulan Terakhir<br>
                              <label id="show_date"></label>
                            </h5>
                          </div>
                          <div class="card-block m-b-30">
                            <div id="loader_scan_analytics" class="text-center justify-content-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                              </div>
                            </div>
                            <div id="sales-analytics" style="height: 390px;"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card">
                          <div class="card-header text-center">
                            <h5>Total Job PPIC per Bulan<br>Tahun <?php echo date('Y'); ?></h5>
                          </div>
                          <div class="card-block m-b-30">
                            <div id="loader_job" class="text-center justify-content-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                              </div>
                            </div>
                            <canvas id="polarChart" width="100" height="100"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card">
                          <div class="card-header text-center">
                            <h5>Laporan Jumlah Total NG per Bulan<br>Tahun <?php echo date('Y'); ?></h5>
                          </div>
                          <div class="card-block m-b-30">
                            <div id="loader_status_job" class="text-center justify-content-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                              </div>
                            </div>
                            <div id="product-ng-analytics" style="height: 390px;"></div>
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

  <?php $this->load->view('adminx/components/bottom_js_chart'); ?>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/chart.js/js/Chart.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/Chart.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/amcharts.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/gauge.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/serial.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/light.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/pie.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/ammap.min.js"></script>
  <script src="<?php echo base_url(); ?>files/assets/pages/widget/amchart/usaLow.js"></script>
  <script type="text/javascript">
    //show data scan last week
    function show_data_scan_last_month() {
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_job_activity/get_data_scan_from_last_month",
        type: "POST",
        dataType: "JSON",
        beforeSend: function(data) {
          $("#loader_scan_analytics").show();
        },
        success: function(data) {
          $("#loader_scan_analytics").hide();
          //SET DATA UTK GRAFIK DONUT
          var dataGrafik = data.data;
          var values = [];
          dataGrafik.forEach(function(task) {
            values.push({
              "date": task.date,
              "value": task.value
            })
          });

          var start_date = [...values].shift();
          var start_date_format = moment(start_date.date).format('DD MMMM YYYY');
          var end_date = values[values.length - 1];
          var end_date_format = moment(end_date.date).format('DD MMMM YYYY');

          $("#show_date").text(start_date_format + " s/d " + end_date_format);

          var e = AmCharts.makeChart("sales-analytics", {
            type: "serial",
            theme: "light",
            marginRight: 15,
            marginLeft: 40,
            autoMarginOffset: 20,
            dataDateFormat: "YYYY-MM-DD",
            valueAxes: [{
              id: "v1",
              axisAlpha: 0,
              position: "left",
              ignoreAxisWidth: !0
            }],
            balloon: {
              borderThickness: 1,
              shadowAlpha: 0
            },
            graphs: [{
              id: "g1",
              balloon: {
                drop: !0,
                adjustBorderColor: !1,
                color: "#ffffff",
                type: "smoothedLine"
              },
              fillAlphas: 0.3,
              bullet: "round",
              bulletBorderAlpha: 1,
              bulletColor: "#FFFFFF",
              lineColor: "#9ccc65",
              bulletSize: 5,
              hideBulletsCount: 50,
              lineThickness: 3,
              title: "red line",
              useLineColorForBulletBorder: !0,
              valueField: "value",
              balloonText: "<span style='font-size:18px;'>[[value]]</span>",
            }, ],
            chartCursor: {
              valueLineEnabled: !0,
              valueLineBalloonEnabled: !0,
              cursorAlpha: 0,
              zoomable: !1,
              valueZoomable: !0,
              valueLineAlpha: 0.5
            },
            chartScrollbar: {
              autoGridCount: !0,
              graph: "g1",
              oppositeAxis: !0,
              scrollbarHeight: 40
            },
            categoryField: "date",
            categoryAxis: {
              parseDates: !0,
              dashLength: 1,
              minorGridEnabled: !0,
              title: "Tanggal"
            },
            export: {
              enabled: !0
            },
            dataProvider: values,
            valueAxes: [{
              "title": "Jumlah Scan"
            }]
          });
          setTimeout(function() {
            e.zoomToIndexes(Math.round(0.45 * e.dataProvider.length), Math.round(0.6 * e.dataProvider.length));
          }, 800);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }
    //show job by status per bulan
    function show_ng_data_permonth() {
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_job_activity/data_ng_perbulan_tahun_jalan",
        type: "POST",
        dataType: "JSON",
        beforeSend: function(data) {
          $("#loader_status_job").show();
        },
        success: function(data) {
          $("#loader_status_job").hide();
          //SET DATA UTK GRAFIK DONUT
          var dataGrafik = data.data;
          var nilai = [];
          dataGrafik.forEach(function(task) {
            nilai.push({
              "category": task.Nama_bulan,
              "count": task.Jumlah_NG
            })
          });

          console.log(nilai);

          var chart = AmCharts.makeChart("product-ng-analytics", {
            type: "serial",
            theme: "light",
            columnWidth: 1,
            dataProvider: nilai,
            graphs: [{
              fillColors: "#c55",
              fillAlphas: 0.5,
              lineColor: "#fff",
              lineAlpha: 0.5,
              type: "column",
              valueField: "count"
            }],
            categoryField: "category",
            categoryAxis: {
              startOnAxis: true,
              title: "Bulan berjalan"
            },
            valueAxes: [{
              title: "Jumlah NG"
            }]
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }
    //show chart job per month
    function show_job_per_month() {
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_job_activity/data_job_perbulan",
        type: "POST",
        dataType: "JSON",
        beforeSend: function(data) {
          $("#loader_job").show();
        },
        success: function(data) {
          $("#loader_job").hide();
          //SET DATA UTK GRAFIK DONUT
          var dataGrafik = data.data;
          //var dataDonuts = [];
          var labels = [];
          var values = [];
          dataGrafik.forEach(function(task) {
            //dataDonuts.push(task);
            labels.push(task.label);
            values.push(task.value);
          });
          var polarElem = document.getElementById("polarChart");
          var data3 = {
            datasets: [{
              data: values,
              backgroundColor: ["#7E81CB", "#1ABC9C", "#B8EDF0", "#B4C1D7", "#01C0C8"],
              hoverBackgroundColor: ["#a1a4ec", "#2adab7", "#a7e7ea", "#a5b0c3", "#10e6ef"],
              label: "My dataset"
            }],
            labels: labels
          };

          new Chart(polarElem, {
            data: data3,
            type: "polarArea",
            options: {
              responsive: true,
              maintainAspectRatio: true,
              elements: {
                arc: {
                  borderColor: ""
                }
              }
            }
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }

    //show chart label per month
    function show_label_per_month() {
      $.ajax({
        url: "<?php echo base_url(); ?>ppic_job_activity/data_label_per_bulan",
        type: "POST",
        dataType: "JSON",
        beforeSend: function(data) {
          $("#loader_scan").show();
        },
        success: function(data) {
          $("#loader_scan").hide();
          //SET DATA UTK GRAFIK BAR
          var dataGrafik = data.data;
          var label_bar = [];
          var nilai_bar = [];

          dataGrafik.forEach(function(task) {
            label_bar.push(task.Nama_bulan);
            nilai_bar.push(task.Jumlah_scan);
          });

          var chartdata = {
            labels: label_bar,
            datasets: [{
              label: 'Produksi dan QC', //data.data[0].Tahun_jalan,
              borderColor: 'rgb(75, 192, 192)',
              hoverOffset: 4,
              data: nilai_bar
            }]
          };

          var graphTarget = $("#graphCanvas");

          var barGraph = new Chart(graphTarget, {
            type: 'line',
            data: chartdata
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }

    $(document).ready(function() {
      show_data_scan_last_month();
      show_ng_data_permonth();
      show_label_per_month();
      show_job_per_month();

    });
  </script>
</body>

</html>