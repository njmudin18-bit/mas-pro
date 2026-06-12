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

    <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/samples/style.css" />
    <script src="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/lib/webviewer.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/samples/old-browser-checker.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/samples/global.js"></script>
    <title>JavaScript PDF Viewer Demo</title>
    <script src="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/samples/modernizr.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/bower_components/bootstrap/css/bootstrap.min.css">
  </head>
  <body>
    <header>
      <div class="title sample">
        <img class="img-fluid" src="<?php echo base_url(); ?>files/uploads/logos/<?php echo $perusahaan->logo_name; ?>" alt="<?php echo $perusahaan->nama; ?>" style="max-width: 80%;" />
      </div>
    </header>
    <!-- <div class="container">
      <div class="row">
        <div class="col-md-12"></div>
      </div>
    </div>
    <div>
      <h5><?php echo strtoupper($nama_document); ?></h5>
      <p><?php echo $nomor_document; ?></p>
    </div> -->
    <br><br>
    <h5>test</h5>
    <div id="viewer" style="margin-left: auto; margin-right: auto;"></div>
    <script src="<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/samples/menu-button.js"></script>

    <!--ga-tag-->

    <script>
      Modernizr.addTest('async', function() {
        try {
          var result;
          eval('let a = () => {result = "success"}; let b = async () => {await a()}; b()');
          return result === 'success';
        } catch (e) {
          return false;
        }
      });

      // test for async and fall back to code compiled to ES5 if they are not supported
      /*['viewing.js'].forEach(function(js) {
        var script = Modernizr.async ? js : js.replace('.js', '.ES5.js');
        var scriptTag = document.createElement('script');
        scriptTag.src = script;
        scriptTag.type = 'text/javascript';
        document.getElementsByTagName('head')[0].appendChild(scriptTag);
      });*/

      WebViewer(
        {
          licenseKey: 'vQBpU5UjK5wvdRaXdDWm',
          path: '<?php echo base_url(); ?>files/assets/plugins/pdf-js-express/lib/', //'../../../lib',
          initialDoc: '<?php echo $file; ?>',
          //initialDoc: 'https://pdftron.s3.amazonaws.com/downloads/pl/demo-annotated.pdf',
        },
        document.getElementById('viewer')
      ).then(instance => {
        /*samplesSetup(instance);

        document.getElementById('select').onchange = e => {
          instance.UI.loadDocument(e.target.value);
        };

        document.getElementById('file-picker').onchange = e => {
          const file = e.target.files[0];
          if (file) {
            instance.UI.loadDocument(file);
          }
        };

        document.getElementById('url-form').onsubmit = e => {
          e.preventDefault();
          instance.UI.loadDocument(document.getElementById('url').value);
        };*/
      });
    </script>
  </body>
</html>
