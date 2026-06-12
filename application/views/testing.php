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
    <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  </head>
  <body>
    <main class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
      <div class="p-5XX rounded">
        <p id="loading" class="lead">Loading content...</p>
        <table id="tableContent" class="table table-responsive table-bordered table-striped" width="100%" border="1" cellpadding="0" cellspacing="0">
          <thead>
            <tr class="bg-primary bg-gradient text-white">
              <th class="text-center" width="15%">Customer Name</th>
              <th class="text-center">DO Number</th>
              <th class="text-center">Part Name</th>
              <th class="text-center">Qty</th>
              <th class="text-center">Address</th>
            </tr>
          </thead>
          <tbody id="messages"></tbody>
        </table>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script>
        // Initialize Pusher
        var pusher = new Pusher('5edb73018471f125ad33', {
          cluster: 'ap1'
        });

        // Subscribe to a channel
        var channel = pusher.subscribe('my-channel');
        
        // Listen for the 'my-event' and display the message or file
        channel.bind('my-event', function(data) {
          $('#loading').hide();
          $('#tableContent').show();
          //console.log(data);

          // Append new row to the DataTable
          var table = $('#tableContent').DataTable();
          table.row.add([
            data.data.CustomerName,
            data.data.DONumber,
            data.data.PartName,
            data.data.QtyOrder,
            data.data.Alamat
          ]).draw();

          // Adjust the column widths after adding the data
          table.columns.adjust().draw();
        });

        // Update user list when a user connects
        channel.bind('user-connected', function(data) {
          $('#users-online').append('<li>' + data.name + '</li>');
        });

        // Remove user from list when disconnected
        channel.bind('user-disconnected', function(data) {
          $('#users-online').find('li:contains(' + data.name + ')').remove();
        });

        // Send message or file to the backend (CodeIgniter Controller)
        function sendMessage() {
          var message   = $('#message').val();
          var formData  = new FormData();
          formData.append('message', message);

          $.ajax({
            url: '<?php echo base_url("loadingcontroller/loading_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
              console.log(response);
              //$('#message').val('');
              //$('#file').val('');

              $('#loading').hide();
              $('#tableContent').show();
            },
            error: function(xhr, status, error) {
              console.error("Error:", error);
            }
          });
        }

        $(document).ready(function() {
          $('#tableContent').hide();

          var groupColumn = 0;
          var table = $('#tableContent').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            columnDefs: [{ visible: false, targets: groupColumn }],
            order: [[groupColumn, 'asc']],
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
        
                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before(
                                    '<tr class="group bg-success bg-gradient"><td class="text-white" colspan="5">' +
                                        group +
                                        '</td></tr>'
                                );
        
                            last = group;
                        }
                    });
            }
          });
        });
    </script>
  </body>
</html>
