<div class="page-header card d-print-none">
	<div class="row align-items-end">
		<div class="col-lg-8">
			<div class="page-header-title">
				<i class="feather <?php echo $icon_halaman; ?> bg-c-blue"></i>
				<div class="d-inline">
					<h5><?php echo $nama_halaman; ?></h5>
					<span><?php echo strtoupper(APPS_NAME); ?></span>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="page-header-breadcrumb">
				<ul class=" breadcrumb breadcrumb-title">
					<li class="breadcrumb-item">
						<a href="<?php echo base_url(); ?>adminx"><i class="feather icon-home"></i></a>
					</li>
					<li class="breadcrumb-item">
						<a href="#"><?php echo $nama_halaman; ?></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>