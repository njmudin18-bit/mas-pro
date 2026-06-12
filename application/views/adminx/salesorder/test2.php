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

  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo base_url(); ?>files/jQuery.Gantt-master/css/style.css" type="text/css" rel="stylesheet">
  <link href="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css" rel="stylesheet" type="text/css">
  <style type="text/css">
    body {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 13px;
      padding: 0 0 50px 0;
    }

    h1 {
      margin: 40px 0 20px 0;
    }

    h2 {
      font-size: 1.5em;
      padding-bottom: 3px;
      border-bottom: 1px solid #DDD;
      margin-top: 50px;
      margin-bottom: 25px;
    }

    table th:first-child {
      width: 150px;
    }

    .github-corner:hover .octo-arm {
      animation: octocat-wave 560ms ease-in-out
    }

    @keyframes octocat-wave {

      0%,
      100% {
        transform: rotate(0)
      }

      20%,
      60% {
        transform: rotate(-25deg)
      }

      40%,
      80% {
        transform: rotate(10deg)
      }
    }

    @media (max-width:500px) {
      .github-corner:hover .octo-arm {
        animation: none
      }

      .github-corner .octo-arm {
        animation: octocat-wave 560ms ease-in-out
      }
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="gantt"></div>
      </div>
    </div>
  </div>

  <script src="<?php echo base_url(); ?>files/jQuery.Gantt-master/js/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
  <script src="<?php echo base_url(); ?>files/jQuery.Gantt-master/js/jquery.fn.gantt.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
  <script>
    function get_data_awal() {
      const start_date = $("#start_date").val();
      const end_date = $("#end_date").val();

      $.ajax({
        url: "<?php echo base_url(); ?>sales_order_customer/sales_order_by_po",
        type: "POST",
        data: {
          start_date: start_date,
          end_date: end_date
        },
        dataType: "JSON",
        success: function(data) {
          const isi = data.data;
          if (isi.length > 0) {
            const array_data = [];
            let isi_data = {
              name: '',
              desc: '',
              values: [{
                from: '',
                to: '',
                label: '',
                customClass: ''
              }]
            };

            isi.forEach(function(value, key) {
              //console.log(value);
              isi_data = {
                name: value.PartnerName,
                desc: value.NoBukti,
                values: [{
                  from: value.tgl,
                  to: value.tgl,
                  label: value.PoCustomer,
                  customClass: ''
                }]
              };

              array_data.push(isi_data);

              $(".gantt").gantt({
                source: array_data,
                waitText: "Mohon tunggu...",
                navigate: "scroll",
                scale: "weeks",
                maxScale: "months",
                minScale: "hours",
                itemsPerPage: 10,
                scrollToToday: true,
                useCookie: true,
                onItemClick: function(data) {
                  alert("Item clicked - show some details");
                },
                onAddClick: function(dt, rowId) {
                  //alert("Empty space clicked - add an item!");
                },
                onRender: function() {
                  if (window.console && typeof console.log === "function") {
                    console.log("chart rendered");
                  }
                }
              });

              $(".gantt").popover({
                selector: ".bar",
                title: function _getItemText() {
                  console.log(this.textContent);
                  return this.textContent;
                },
                container: '.gantt',
                content: "Here's some useful information.",
                trigger: "hover",
                placement: "auto right"
              });

              prettyPrint();

            });
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    }

    $(function() {
      "use strict";

      // var demoSource = [{
      //   name: "Sprint 0",
      //   desc: "AnalysisXX",
      //   values: [{
      //     from: 1320192000000,
      //     to: 1322401600000,
      //     label: "Requirement Gathering",
      //     customClass: "ganttRed"
      //   }]
      // }, {
      //   desc: "ScopingZZ",
      //   values: [{
      //     from: 1322611200000,
      //     to: 1323302400000,
      //     label: "Scoping",
      //     customClass: "ganttRed"
      //   }]
      // }, {
      //   name: "Sprint 1XX",
      //   desc: "Development",
      //   values: [{
      //     from: 1323802400000,
      //     to: 1325685200000,
      //     label: "Development",
      //     customClass: "ganttGreen"
      //   }]
      // }, {
      //   name: " ",
      //   desc: "Showcasing",
      //   values: [{
      //     from: 1325685200000,
      //     to: 1325695200000,
      //     label: "Showcasing",
      //     customClass: "ganttBlue"
      //   }]
      // }, {
      //   name: "Sprint 2",
      //   desc: "Development",
      //   values: [{
      //     from: 1325695200000,
      //     to: 1328785200000,
      //     label: "Development",
      //     customClass: "ganttGreen"
      //   }]
      // }, {
      //   desc: "Showcasing",
      //   values: [{
      //     from: 1328785200000,
      //     to: 1328905200000,
      //     label: "Showcasing",
      //     customClass: "ganttBlue"
      //   }]
      // }, {
      //   name: "Release Stage",
      //   desc: "Training",
      //   values: [{
      //     from: 1330011200000,
      //     to: 1336611200000,
      //     label: "Training",
      //     customClass: "ganttOrange"
      //   }]
      // }, {
      //   desc: "Deployment",
      //   values: [{
      //     from: 1336611200000,
      //     to: 1338711200000,
      //     label: "Deployment",
      //     customClass: "ganttOrange"
      //   }]
      // }, {
      //   desc: "Warranty Period",
      //   values: [{
      //     from: 1336611200000,
      //     to: 1349711200000,
      //     label: "Warranty Period",
      //     customClass: "ganttOrange"
      //   }]
      // }];

      // shifts dates closer to Date.now()
      // var offset = new Date().setHours(0, 0, 0, 0) -
      //   new Date(demoSource[0].values[0].from).setDate(35);
      // for (var i = 0, len = demoSource.length, value; i < len; i++) {
      //   value = demoSource[i].values[0];
      //   value.from += offset;
      //   value.to += offset;
      // }

      // $(".gantt").gantt({
      //   source: demoSource,
      //   navigate: "scroll",
      //   scale: "weeks",
      //   maxScale: "months",
      //   minScale: "hours",
      //   itemsPerPage: 10,
      //   scrollToToday: false,
      //   useCookie: true,
      //   onItemClick: function(data) {
      //     alert("Item clicked - show some details");
      //   },
      //   onAddClick: function(dt, rowId) {
      //     alert("Empty space clicked - add an item!");
      //   },
      //   onRender: function() {
      //     if (window.console && typeof console.log === "function") {
      //       console.log("chart rendered");
      //     }
      //   }
      // });

      // $(".gantt").popover({
      //   selector: ".bar",
      //   title: function _getItemText() {
      //     return this.textContent;
      //   },
      //   container: '.gantt',
      //   content: "Here's some useful information.",
      //   trigger: "hover",
      //   placement: "auto right"
      // });

      // prettyPrint();

    });

    //show isi table
    $(document).ready(function() {
      get_data_awal();
    });
  </script>
</body>

</html>