<!doctype html>
<html lang="en">
  <head>
    <?php //print_r($company_profile->nama); exit; ?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $nama_halaman; ?> - <?php echo $company_profile->nama; ?></title>
    <meta name="google-site-verification" content="TpgnwLl52KnKINDB0zPxwAQwdqGG2e0XwT8hLmggAAI" />
    <meta name="title" content="<?php echo $nama_halaman; ?> - <?php echo $company_profile->nama; ?>">
    <meta name="description" content="Kontak dan alamat kantor resmi <?php echo $company_profile->nama; ?> untuk pemesanan, kerja sama dan informasi produk lainnya silakan hubungi di sini.">
    <meta name="subject" content="<?php echo $company_profile->nama; ?>">
    <meta name="language" content="ID">
    <meta name="author" content="<?php echo $company_profile->nama; ?>">
    <meta name="designer" content="IT Department - <?php echo $company_profile->nama; ?>">
    <meta name="copyright" content="Copyright &copy; <?php echo $company_profile->nama; ?>">
    <meta name="url" content="<?php echo base_url(); ?>contact-us">
    <meta name="identifier-URL" content="<?php echo base_url(); ?>contact-us">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <link rel="canonical" href="<?php echo base_url(); ?>contact-us" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo base_url(); ?>contact-us">
    <meta property="og:title" content="<?php echo $nama_halaman; ?> - <?php echo $company_profile->nama; ?>">
    <meta property="og:description" content="Kontak dan alamat kantor resmi <?php echo $company_profile->nama; ?> untuk pemesanan, kerja sama dan informasi produk lainnya silakan hubungi di sini.">
    <!-- <meta property="og:image" content="<?php //echo API_URL; ?>upload/general_images/<?php echo $company_profile->icon_name; ?>"> -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo base_url(); ?>contact-us">
    <meta property="twitter:title" content="<?php echo $nama_halaman; ?> - <?php echo $company_profile->nama; ?>">
    <meta property="twitter:description" content="Kontak dan alamat kantor resmi <?php echo $company_profile->nama; ?> untuk pemesanan, kerja sama dan informasi produk lainnya silakan hubungi di sini.">
    <!-- <meta property="twitter:image" content="<?php //echo API_URL; ?>upload/general_images/<?php echo $company_profile->icon_name; ?>"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="icon" type="image/png" href="<?php //echo API_URL; ?>upload/general_images/<?php echo $company_profile->icon_name; ?>"> -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Hello, world!</title>
    <style>
        .fs-sb{
            font-size: 350px;
        }
    </style>
  </head>
  <body>
      <div class="container-fluid">
          <div class="row mb-3">
              <div class="col-md-6 mt-3">
                  <div class="card bg-primary bg-gradient text-center">
                      <div class="card-header">
                          <h5 class="text-white">TOTAL PESANAN</h5>
                      </div>
                      <div class="card-body">
                          <h1 id="LabelJumlahPesan" class="fs-sb text-white">0</h1>
                      </div>
                      <div class="card-footer">
                          <small id="LabelTanggalPesan" class="text-white">-</small>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 mt-3">
                  <div class="card bg-success bg-gradient text-center">
                      <div class="card-header">
                          <h5 class="text-white">TOTAL AMBIL</h5>
                      </div>
                      <div class="card-body">
                          <h1 id="LabelJumlahAmbil" class="fs-sb text-white">0</h1>
                      </div>
                      <div class="card-footer">
                          <small id="LabelTanggalAmbil" class="text-white">-</small>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- Option 1: Bootstrap Bundle with Popper -->
      <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
      <script src="https://www.jqueryscript.net/demo/Simple-jQuery-Animated-Counter-With-Easing-Support-SimpleCounter-js/jQuerySimpleCounter.js"></script>
      <script>
          function get_jumlah_pesanan()
          {
              $.ajax({
                  url : '<?php echo base_url(); ?>welcome/get_jumlah_pesanan',
                  type: "POST",
                  data: {
                      'tanggal': '<?php echo date('Y-m-d'); ?>'
                  },
                  dataType: "JSON",
                  success: function(data)
                  {
                      let JumlahPesanan   = parseFloat(data.data.TotalPesanan);
                      let JumlahAmbil     = parseFloat(data.data.JumlahTerima);
                      let TanggalPesanan  = moment(data.data.Tanggal).format("DD MMMM YYYY");
                      //$('#LabelJumlahPesan').html(JumlahPesanan);
                      //$('#LabelJumlahAmbil').html(JumlahAmbil);
                      $('#LabelTanggalPesan, #LabelTanggalAmbil').html(TanggalPesanan);
                      
                      $('#LabelJumlahPesan').jQuerySimpleCounter({
                        start: 0,
                        end: JumlahPesanan,
                        duration: 1000
                      });

                      $('#LabelJumlahAmbil').jQuerySimpleCounter({
                        start: 0,
                        end: JumlahAmbil,
                        duration: 1000
                      });
                  },
                  error: function (jqXHR, textStatus, errorThrown)
                  {
                      alert('Error when showing data');
                  }
              });
          }
          
          $(document).ready(function() {
              get_jumlah_pesanan();
          });
      </script>
  </body>
</html>