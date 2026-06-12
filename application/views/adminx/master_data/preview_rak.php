
<!DOCTYPE html>
<html lang="zxx">
  <head>
    <title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="<?php echo APPS_DESC; ?>" />
    <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
    <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
    <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/font-awesome/css/font-awesome.min.css">

    <!-- Favicon icon -->
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
    <style>
      h1 {
        color: #000 !important;
      }

      .border-p5 {
        border: 5px solid #000
      }

      .border-p2 {
        border: 2px solid #000;
        border-radius: 2px;
      }

      .fs-title {
        font-size: 8rem; 
        margin-bottom: 0px;
        color: #000 !important;
      }

      .fs-subtitle {
        font-size: 2.5rem;
        margin-bottom: 0px;
        color: #000 !important;
      }

      .border-left {
        border-bottom: 5px solid #000;
        border-top: 5px solid #000;
        border-left: 5px solid #000;
      }

      .border-right {
        border-bottom: 5px solid #000;
        border-top: 5px solid #000;
        border-right: 5px solid #000;
      }
    </style>
  </head>
  <body>
    <!-- Invoice 1 start -->
    <div class="invoice-1 invoice-content">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="invoice-inner clearfix">
              <div class="invoice-info clearfix" id="invoice_wrapper">
                <div id="Isi-preview" class="invoice-top"></div>
              </div>
              <div class="invoice-btn-section clearfix d-print-none">
                <a href="javascript:window.print()" class="btn btn-lg btn-print">
                  <i class="fa fa-print"></i> Print Label
                </a>
                <!-- <a id="invoice_download_btn" class="btn btn-lg btn-download btn-theme">
                  <i class="fa fa-download"></i> Download Invoice
                </a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Invoice 1 end -->

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jspdf.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/html2canvas.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/app.js"></script>
    <!-- QR CODE JS -->
    <script type="text/javascript" src="<?php echo base_url(); ?>files/jquery-qrcode/src/jquery.qrcode.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>files/jquery-qrcode/src/qrcode.js"></script>
    <script>
      function show_rak(RakIdArray) {
        $.ajax({
          url: "<?php echo base_url(); ?>rak/tampilkan_pilihan_rak",
          type: "POST",
          data: {
            RakID: RakIdArray
          },
          dataType: "JSON",
          beforeSend: function() {
            // Optional: Show a loading spinner or message here
          },
          success: function(data) {
            const Datas = data.data;
            let items   = [];
            
            Datas.forEach(function(value, index) {
              let Details       = value.Details;
              let detailsHTML   = '';
              let detailsHTMLQR = '';
              
              // Generate HTML for Details
              Details.forEach(function(detail) {
                detailsHTML += 
                  '<div class="col-sm-12 img-thumbnail border-p2">' +
                    '<h5 class="fs-subtitle">' + detail.Sequent.trim() + '</h5>' +
                  '</div>';
                
                detailsHTMLQR += 
                  '<div class="col-md-2 border-p5 me-1">' +
                    '<div class="row p-1">' +
                      '<div class="col-sm-12 p-0 mt-auto mb-auto mb-2">' +
                        '<h1 class="p-3 text-center img-thumbnail border-p2">' + detail.Sequent.trim() + '</h1>' +
                      '</div>' +
                      '<div class="col-sm-12 p-0 mt-auto mb-auto">' +
                        '<div class="img-thumbnail border-p2" id="QRSub_' + detail.Id + '"></div>' +
                      '</div>' +
                    '</div>' +
                  '</div>';
              });
              
              // Generate overall structure
              items.push(
                '<div id="LabelUtama_' + index + '" class="row mb-4">' +
                  '<div class="col-sm-12">' +
                    '<div class="row text-center">' +
                      '<div class="col-sm-4 mt-auto mb-auto border-left">' +
                        '<div class="row p-2">' +
                          '<div class="col-sm-12 mb-2">' +
                            '<h1 class="fs-title img-thumbnail border-p2">' + value.Rak + '</h1>' +
                          '</div>' +
                          '<div class="col-sm-12">' + 
                            '<div class="p-3 img-thumbnail border-p2" id="qrcodeTable_' + index + '"></div>' +
                          '</div>' +
                        '</div>' +
                      '</div>' +
                      '<div class="col-sm-2 border-right">' +
                        '<div class="row g-2 p-2">' + detailsHTML + '</div>' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div id="LabelSub_' + index + '" class="row mb-4">' + detailsHTMLQR + '</div>'
              );
            });

            // Append the generated HTML to the desired container
            $("#Isi-preview").html(items.join(''));

            // Initialize QR codes after elements are in the DOM
            Datas.forEach(function(value, index) {
              $("#qrcodeTable_" + index).qrcode({
                render: "canvas",
                width: 180,
                height: 180,
                ecLevel: "H",
                text: value.QRCode
              });

              value.Details.forEach(function(detail) {
                $("#QRSub_" + detail.Id).qrcode({
                  render: "canvas",
                  width: 120,
                  height: 120,
                  ecLevel: "H",
                  text: detail.QRCode
                });
              });
            });
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
          }
        });
      }

      $(document).ready(function() {
        let RakID = "<?php echo $RakIdArray; ?>";

        show_rak(RakID);
      });
    </script>
  </body>
</html>
