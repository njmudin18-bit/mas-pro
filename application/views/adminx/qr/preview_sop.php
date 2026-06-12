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
	</head>
	<body>

		<div style="margin-left: auto; margin-right: auto; display: block;">
		  <button id="prev">Previous</button>
		  <button id="next">Next</button>
		  &nbsp; &nbsp;
		  <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>
		</div>

		<canvas id="the-canvas" style="margin-left: auto; margin-right: auto; display: block;"></canvas>
		
		<script src="<?php echo base_url(); ?>files/assets/plugins/pdfjs-dist-3.2.146/package/build/pdf.js"></script>
		<!-- <script src="//mozilla.github.io/pdf.js/build/pdf.js"></script> -->
		<script>
			// If absolute URL from the remote server is provided, configure the CORS
			// header on that server.
			var url = '<?php echo $file; ?>'; //'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

			// Loaded via <script> tag, create shortcut to access PDF.js exports.
			var pdfjsLib = window['pdfjs-dist/build/pdf'];

			// The workerSrc property shall be specified.
			//pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

			pdfjsLib.GlobalWorkerOptions.workerSrc = '<?php echo base_url(); ?>files/assets/plugins/pdfjs-dist-3.2.146/package/build/pdf.worker.js';

			var pdfDoc = null,
			    pageNum = 1,
			    pageRendering = false,
			    pageNumPending = null,
			    scale = 0.8,
			    canvas = document.getElementById('the-canvas'),
			    ctx = canvas.getContext('2d');

			/**
			 * Get page info from document, resize canvas accordingly, and render page.
			 * @param num Page number.
			 */
			function renderPage(num) {
			  pageRendering = true;
			  // Using promise to fetch the page
			  pdfDoc.getPage(num).then(function(page) {
			    var viewport = page.getViewport({scale: scale});
			    canvas.height = viewport.height;
			    canvas.width = viewport.width;

			    // Render PDF page into canvas context
			    var renderContext = {
			      canvasContext: ctx,
			      viewport: viewport
			    };
			    var renderTask = page.render(renderContext);

			    // Wait for rendering to finish
			    renderTask.promise.then(function() {
			      pageRendering = false;
			      if (pageNumPending !== null) {
			        // New page rendering is pending
			        renderPage(pageNumPending);
			        pageNumPending = null;
			      }
			    });
			  });

			  // Update page counters
			  document.getElementById('page_num').textContent = num;
			}

			/**
			 * If another page rendering in progress, waits until the rendering is
			 * finised. Otherwise, executes rendering immediately.
			 */
			function queueRenderPage(num) {
			  if (pageRendering) {
			    pageNumPending = num;
			  } else {
			    renderPage(num);
			  }
			}

			/**
			 * Displays previous page.
			 */
			function onPrevPage() {
			  if (pageNum <= 1) {
			    return;
			  }
			  pageNum--;
			  queueRenderPage(pageNum);
			}

			document.getElementById('prev').addEventListener('click', onPrevPage);

			/**
			 * Displays next page.
			 */
			function onNextPage() {
			  if (pageNum >= pdfDoc.numPages) {
			    return;
			  }
			  pageNum++;
			  queueRenderPage(pageNum);
			}
			
			document.getElementById('next').addEventListener('click', onNextPage);

			/**
			 * Asynchronously downloads PDF.
			 */
			pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
			  pdfDoc = pdfDoc_;
			  document.getElementById('page_count').textContent = pdfDoc.numPages;

			  // Initial/first page rendering
			  renderPage(pageNum);
			});
		</script>
	</body>
</html>