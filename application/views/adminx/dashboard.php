<!DOCTYPE html>
<html lang="en">

<head>
	<title>Dashboard | <?php echo APPS_NAME; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="<?php echo APPS_DESC; ?>" />
	<meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
	<meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
	<meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">

	<?php $this->load->view('adminx/components/header_css_chart'); ?>
</head>

<body>

	<?php $this->load->view('adminx/components/loader'); ?>

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

											<div class="col-md-12 col-lg-6">
												<div class="card" style="height: 96%;">
													<div class="card-header text-center">
														<h5>Jumlah Aplikasi By Perusahaan</h5>
													</div>
													<div class="card-block">
														<canvas id="pieChart" width="400" height="300"></canvas>
													</div>
												</div>
											</div>

											<div class="col-md-12 col-lg-6">
												<div class="card" style="height: 96%;">
													<div class="card-header text-center">
														<h5>Jumlah Aplikasi Berdasarkan Platform</h5>
													</div>
													<div class="card-block">
														<div id="donut-example"></div>
													</div>
												</div>
											</div>



											<!-- <div class="col-md-12 col-lg-6">
						              <div class="card">
						                <div class="card-header">
						                  <h5>Site Visit Chart</h5>
						                  <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						                </div>
						                <div class="card-block">
						                  <div id="morris-site-visit"></div>
						                </div>
						              </div>
						            </div>

						            <div class="col-md-12 col-lg-6">
						              <div class="card">
						                <div class="card-header">
						                  <h5>Bar Chart</h5>
						                  <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						                </div>
						                <div class="card-block">
						                  <div id="morris-bar-chart"></div>
						                </div>
						              </div>
						            </div>-->

											<!-- <div class="col-lg-12">
												<div class="card">
													<div class="card-header">
														<h5>Status Progress Aplikasi</h5>
													</div>
													<div class="card-block">
														<canvas id="graphCanvas" width="400" height="180"></canvas>
													</div>
												</div>
											</div> -->

                      <div class="col-sm-12">
                        <div class="card">
                          <div class="card-header text-center">
                            <h5>Status Progress Aplikasi</h5>
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

											<!--<div class="col-lg-12">
						              <div class="card">
						                <div class="card-header">
						                  <h5>Area Chart</h5>
						                  <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						                </div>
						                <div class="card-block">
						                  <div id="area-example"></div>
						                </div>
						              </div>
						            </div>

						            <div class="col-md-12 col-lg-6">
						              <div class="card">
						                <div class="card-header">
						                  <h5>Line Chart</h5>
						                  <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						                </div>
						                <div class="card-block">
						                  <div id="line-example"></div>
						                </div>
						              </div>
						            </div> -->

										</div>
									</div>
								</div>
							</div>
							<div id="styleSelector">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php $this->load->view('adminx/components/bottom_js_chart'); ?>
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
		function getRandomColor() { //generates random colours and puts them in string
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

		"use strict";
		setTimeout(function() {
			$(document).ready(function() {
				$.ajax({
					url: "<?php echo base_url(); ?>adminx/show_data_grafik",
					type: "GET",
					dataType: "JSON",
					success: function(data) {
						//SET DATA UTK GRAFIK DONUT
						var dataGrafik = data.chart_data;
						console.log(dataGrafik);
						var dataDonuts = [];
						dataGrafik.forEach(function(task) {
							dataDonuts.push(task);
						});

						//GRAFIK DONUTS [BY PLATFORM]
						window.areaChart = Morris.Donut({
							element: "donut-example",
							redraw: true,
							data: dataDonuts,
							colors: ["#5FBEAA", "#34495E", "#FF9F55"],
						});

						//SET DATA UTK GRAFIK PIE
						var dataGrafik2 = data.chart_data_perusahaan;
						var label_pie = [];
						var nilai_pie = [];

						dataGrafik2.forEach(function(task) {
							label_pie.push(task.nama);
							nilai_pie.push(task.jlh_apps);
						});

						//GRAFIK PIE [BY PERUSAHAAN]
						var pieElem = document.getElementById("pieChart");
						var data4 = {
							labels: label_pie,
							datasets: [{
								data: nilai_pie,
								backgroundColor: ["#00799e", "#f58025", "#01C0C8"],
								hoverBackgroundColor: ["#00799e", "#f58025", "#0dedf7"]
							}]
						};
						var myPieChart = new Chart(pieElem, {
							type: "pie",
							data: data4
						});

						//SET DATA UTK GRAFIK BAR
						var dataGrafik3 = data.chart_data_status;
						var label_bar = [];
						var nilai_bar = [];

						dataGrafik3.forEach(function(task) {
							label_bar.push(task.nama_project);
							nilai_bar.push(task.project_progress);
						});

						var chartdata = {
							labels: label_bar,
							datasets: [{
								label: 'Progres Aplikasi dalam (%)',
								backgroundColor: getRandomColor(),
								borderColor: getRandomColor(),
								hoverBackgroundColor: getRandomColor(),
								hoverBorderColor: getRandomColor(),
								hoverOffset: 4,
								data: nilai_bar
							}]
						};

						// var graphTarget = $("#graphCanvas");

						// var barGraph = new Chart(graphTarget, {
						// 	type: 'bar',
						// 	data: chartdata
						// });

            data_status = data.chart_data_status;
            var nilai   = [];
            data_status.forEach(function(task) {
              nilai.push({
                "category": task.nama_project,
                "count": task.project_progress
              })
            });

            //graphic bar baru
            var chart = AmCharts.makeChart("product-ng-analytics", {
              type: "serial",
              theme: "light",
              columnWidth: 1,
              dataProvider: nilai,
              graphs: [{
                fillColors: "#c55",
                fillAlphas: 0.5,
                //lineColor: ["#fff", "#fff"],
                // lineAlpha: 0.5,
                type: "column",
                valueField: "count"
              }],
              categoryField: "category",
              categoryAxis: {
                startOnAxis: true,
                title: "Nama aplikasi"
              },
              valueAxes: [{
                title: "Aplikasi progress (%)"
              }]
            });

            $("#loader_status_job").hide();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert('Error get data from ajax');
					}
				});
			});
		}, 350);
	</script>
</body>

</html>