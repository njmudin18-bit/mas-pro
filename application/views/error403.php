<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en-us" class="no-js">
	<head>
		<meta charset="utf-8">
		<title><?php echo APPS_NAME; ?> | <?php echo $perusahaan->nama; ?></title>
		<meta name="description" content="Able 7.0 404 Error page design" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Codedthemes">

		<link rel="shortcut icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets_404/css/style.css" />
	</head>
	<body>
		<!-- <canvas id="dotty"></canvas> -->
		<div class="image"></div>

		<a href="#" class="logo-link" title="back home">
			<img src="<?php echo base_url(); ?>files/uploads/logos/<?php echo $perusahaan->logo_name; ?>" class="logo" alt="<?php echo $perusahaan->nama; ?>" />
		</a>
		<div class="content">
			<div class="content-box">
				<div class="big-content">

					<div class="list-square">
						<span class="square"></span>
						<span class="square"></span>
						<span class="square"></span>
					</div>

					<div class="list-line">
						<span class="line"></span>
						<span class="line"></span>
						<span class="line"></span>
						<span class="line"></span>
						<span class="line"></span>
						<span class="line"></span>
					</div>

					<i class="fa fa-search" aria-hidden="true"></i>

					<div class="clear"></div>
				</div>

				<h1>ACCESS FORBIDDEN</h1>
				<p>The server understood the request but refuses to authorize it.</p>
			</div>
		</div>
		<footer class="light">
			<ul>
				<li><a href="<?php echo base_url(); ?>">HOME</a></li>
			</ul>
		</footer>
		<script src="<?php echo base_url(); ?>files/assets_404/js/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>files/assets_404/js/bootstrap.min.js"></script>

		<script src="<?php echo base_url(); ?>files/assets_404/js/mozaic.js"></script>
	</body>
</html>