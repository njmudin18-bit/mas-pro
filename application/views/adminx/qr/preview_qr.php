<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <meta name="description" content="<?php echo APPS_DESC; ?>" />
    <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
    <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
    <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">
    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <style type="text/css">
    	.imgblock {
				margin: 10px 5px;
				text-align: center;
				float: left;
				min-height: 420px;
				/*border-bottom: 1px solid #B4B7B4;*/
			}

			.title {
				font-size: 15px;
				font-weight: bold;
				color: #fff;
				text-align: center;
				width: 330px;
				margin: 10px 5px;
				height: 60px;
				background-color: #0084C6;
				line-height: 60px;
			}

			body {
				background-image: none;
			}
    </style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12 ju">
					<div id="container"></div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
		  
		<script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/easy.qrcode.min.js"></script>
		<script type="text/template" id="qrcodeTpl">
			<div class="imgblock">
				
				<div class="qr" id="qrcode_{i}"></div>
			</div>
		</script>
		<script type="text/javascript">
			var isi = JSON.parse(localStorage.getItem("data_qr"));
			var demoParams 	= [];
			var content 		= [];
			$.each(isi, function(key, val) {
				content.push (
					{
						title: val.nama_document,
						config: {
							text: val.content_qr, // Content

							width: 200, // Widht
							height: 200, // Height
							colorDark: "#000000", // Dark color
							colorLight: "#ffffff", // Light color
	            quietZone: 15,
	            quietZoneColor: '#fff',

	            // === Title
							title: val.nomor_document, // Title
							titleFont: "normal normal bold 14px open sans,sans-serif", // Title font
							titleColor: "#004284", // Title Color
							titleBackgroundColor: "#fff", // Title Background
							titleHeight: 70, // Title height, include subTitle
							titleTop: 25, // Title draw position(Y coordinate), default is 30


							// === SubTitle
							subTitle: val.nama_document, // Subtitle content
							subTitleFont: "normal normal normal 11px open sans,sans-serif", // Subtitle font
							subTitleColor: "#004284", // Subtitle color
							subTitleTop: 45, // Subtitle drwa position(Y coordinate), default is 50

							// === Logo
							//logo: "logo-transparent.png", // LOGO
							logo: '<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>',  
							logoWidth: 50, 
							logoHeight: 50,
							logoBackgroundColor: '#ffffff', // Logo backgroud color, Invalid when `logBgTransparent` is true; default is '#ffffff'
							logoBackgroundTransparent: false, // Whether use transparent image, default is false
							correctLevel: QRCode.CorrectLevel.H // L, M, Q, H
						}
					}
				);

				demoParams = content;
			});

			/*var demoParams 	= [
				{
					//title: '<?php echo strtoupper($department_name); ?>',
					config: {
						text: '<?php echo $content_qr; ?>', // Content

						width: 240, // Widht
						height: 240, // Height
						colorDark: "#000000", // Dark color
						colorLight: "#ffffff", // Light color
            quietZone: 15,
            quietZoneColor: '#fff',

            // === Title
						title: '<?php echo $nomor_document; ?>', // Title
						titleFont: "normal normal bold 14px open sans,sans-serif", // Title font
						titleColor: "#004284", // Title Color
						titleBackgroundColor: "#fff", // Title Background
						titleHeight: 70, // Title height, include subTitle
						titleTop: 25, // Title draw position(Y coordinate), default is 30


						// === SubTitle
						subTitle: '<?php echo $nama_document; ?>', // Subtitle content
						subTitleFont: "normal normal normal 11px open sans,sans-serif", // Subtitle font
						subTitleColor: "#004284", // Subtitle color
						subTitleTop: 45, // Subtitle drwa position(Y coordinate), default is 50

						// === Logo
						//logo: "logo-transparent.png", // LOGO
						logo: '<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>',  
						logoWidth: 50, 
						logoHeight: 50,
						logoBackgroundColor: '#ffffff', // Logo backgroud color, Invalid when `logBgTransparent` is true; default is '#ffffff'
						logoBackgroundTransparent: false, // Whether use transparent image, default is false
						correctLevel: QRCode.CorrectLevel.H // L, M, Q, H
					}
				}
			];*/
			console.log(demoParams);

			var qrcodeTpl = document.getElementById("qrcodeTpl").innerHTML;
			var container = document.getElementById('container');

			for (var i = 0; i < demoParams.length; i++) {
				var qrcodeHTML = qrcodeTpl.replace(/\{title\}/, demoParams[i].title).replace(/{i}/, i);
				container.innerHTML+=qrcodeHTML;
			}
			for (var i = 0; i < demoParams.length; i++) {
				 var t=new QRCode(document.getElementById("qrcode_"+i), demoParams[i].config);
			}
		</script>
	</body>
</html>