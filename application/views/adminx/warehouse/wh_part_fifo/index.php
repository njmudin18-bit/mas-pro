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

    <?php $this->load->view('adminx/components/header_css_datatable'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css"
        href="<?php echo base_url(); ?>files/bower_components/select2/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Ubah background Select2 single selection */
        .select2-container .select2-selection--single {
            background-color: #ccc !important;
        }

        /* Ubah background select saat terbuka */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: #ccc !important;
        }

        /* Ubah background dropdown list */
        .select2-container--default .select2-results {
            background-color: #ccc !important;
            /* Warna latar belakang dropdown */
        }

        /* Ubah warna teks di dropdown */
        .select2-container--default .select2-results__option {
            color: #000;
            /* Warna teks item dropdown */
        }

        /* Ubah warna item dropdown saat dihover */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #bbb !important;
            /* Warna latar belakang item yang dihover */
            color: #000 !important;
            /* Warna teks item yang dihover */
        }
    </style>

</head>


<body>
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>

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
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header text-center">
                                                        <h5><?php echo strtoupper($nama_halaman); ?></h5>
                                                    </div>
                                                    <?php $this->load->view('adminx/warehouse/header_part/header_part'); ?>

                                                    <hr class="m-2">
                                                    <!-- Tombol Buka Modal -->
                                                    <div class="container mb-3">
                                                        <h5 class="text-center mb-2">Filter Data</h5>
                                                        <form id="filter-form" class="mb-3">
                                                            <div class="row">
                                                                <!-- Filter Part -->
                                                                <div class="col-md-4">
                                                                    <label for="part_id">Part</label>
                                                                    <select id="part_id" class="form-control">
                                                                        <option value="">-- Pilih Part --</option>
                                                                        <?php foreach ($parts as $part): ?>
                                                                            <option value="<?= $part['id'] ?>">
                                                                                <?= $part['part_name'] ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <div class="form-check mt-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="part_all">
                                                                        <label class="form-check-label"
                                                                            for="part_all">Semua Part</label>
                                                                    </div>
                                                                </div>

                                                                <!-- Filter Lokasi -->
                                                                <div class="col-md-4">
                                                                    <label for="lokasi_id">Lokasi</label>
                                                                    <select id="lokasi_id" class="form-control">
                                                                        <option value="">-- Pilih Lokasi --</option>
                                                                        <?php foreach ($lokasi_list as $lokasi): ?>
                                                                            <option value="<?= $lokasi['id'] ?>">
                                                                                <?= $lokasi['nama_lokasi'] ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <div class="form-check mt-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="lokasi_all">
                                                                        <label class="form-check-label"
                                                                            for="lokasi_all">Semua Lokasi</label>
                                                                    </div>
                                                                </div>

                                                                <!-- Filter Rack -->
                                                                <div class="col-md-4">
                                                                    <label for="rack_id">Rack</label>
                                                                    <select id="rack_id" class="form-control">
                                                                        <option value="">-- Pilih Rack --</option>
                                                                        <?php foreach ($racks as $rack): ?>
                                                                            <option value="<?= $rack['id'] ?>">
                                                                                <?= $rack['nama_rack'] ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <div class="form-check mt-1">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="rack_all">
                                                                        <label class="form-check-label"
                                                                            for="rack_all">Semua Rack</label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mt-3 text-right">
                                                                <button type="button" class="btn btn-primary"
                                                                    id="btnFilterSubmit">Terapkan Filter</button>
                                                                <button type="button" id="export_excel"
                                                                    class="btn btn-danger">
                                                                    Export to Excel
                                                                </button>
                                                            </div>
                                                        </form>

                                                    </div>

                                                    <div class="m-2">
                                                        <div class="dt-responsive table-responsive">
                                                            <table id="order-table"
                                                                class="table table-striped table-bordered nowrap"
                                                                width="100%" border="1" cellpadding="0" cellspacing="0">
                                                                <thead>
                                                                    <tr class="bg-primary">
                                                                        <!-- <th class="text-center">No.</th> -->
                                                                        <th class="text-center">FIFO</th>
                                                                        <th class="text-center">IN</th>
                                                                        <th class="text-center">Units</th>
                                                                        <th class="text-center">Remaining</th>
                                                                        <th class="text-center">Rack</th>
                                                                        <th class="text-center">Lokasi WH</th>
                                                                        <th class="text-center">Note</th>
                                                                        <!-- <th class="text-center">Partname</th> -->
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="styleSelector"></div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    <!-- <div id="loading-screen" class="loading">Loading&#8230;</div> -->

    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

    <script src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>

    <script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi elemen
            const $partAll = $('#part_all');
            const $partId = $('#part_id');
            const $lokasiId = $('#lokasi_id');
            const $lokasiAll = $('#lokasi_all');
            const $rackAll = $('#rack_all');
            const $rackId = $('#rack_id');


            // Fungsi toggle input (true = disable)
            function toggleInput($checkbox, $input) {
                if ($checkbox.is(':checked')) {
                    $input.prop('disabled', true);
                    if ($input.is('select')) {
                        $input.val(null).trigger('change');
                    } else {
                        $input.val('');
                    }
                } else {
                    $input.prop('disabled', false).focus();
                }
            }

            // Inisialisasi nilai awal
            toggleInput($partAll, $partId);
            toggleInput($lokasiAll, $lokasiId);
            toggleInput($rackAll, $rackId);

            // Event checkbox change
            $partAll.on('change', () => toggleInput($partAll, $partId));
            $lokasiAll.on('change', () => toggleInput($lokasiAll, $lokasiId));
            $rackAll.on('change', () => toggleInput($rackAll, $rackId));

            // Inisialisasi select2 untuk Part ID
            $partId.select2({
                placeholder: "Masukkan Part Name ",
                allowClear: true,
                ajax: {
                    url: '<?php echo base_url(); ?>warehouse_part/get_part_id',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // LOKASI select2
            $lokasiId.select2({
                placeholder: "Masukkan Lokasi",
                allowClear: true,
                ajax: {
                    url: '<?php echo base_url(); ?>warehouse_part/get_lokasi', // Buat endpoint ini
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // RACK select2
            $rackId.select2({
                placeholder: "Masukkan Rack",
                allowClear: true,
                ajax: {
                    url: '<?php echo base_url(); ?>warehouse_part/get_rack', // Buat endpoint ini juga
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });

            // Export Excel
            $('#export_excel').on('click', function() {
                var table = document.getElementById('order-table');

                var html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <!--[if gte mso 9]>
            <xml>
              <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                  <x:ExcelWorksheet>
                    <x:Name>Kartu Stock</x:Name>
                    <x:WorksheetOptions>
                      <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                  </x:ExcelWorksheet>
                </x:ExcelWorksheets>
              </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
              table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
                text-align: center;
              }
            </style>
          </head>
          <body>
            ${table.outerHTML}
          </body>
        </html>
      `;

                // Gunakan Blob agar lebih stabil
                var blob = new Blob([html], {
                    type: 'application/vnd.ms-excel'
                });
                var url = URL.createObjectURL(blob);

                var a = document.createElement('a');
                a.href = url;
                a.download = 'Part FIFO.xls';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });



            // Inisialisasi DataTable
            const table = $('#order-table').DataTable({
                serverSide: false,
                paging: false, // Sembunyikan pagination (page)
                searching: true, // Sembunyikan search box
                lengthChange: false, // Opsional, sembunyikan dropdown show entries
                info: false, // Sembunyikan informasi "Showing x to y of z entries"
                ajax: {
                    url: '<?php echo base_url(); ?>warehouse_part/list_data_fifo',
                    type: 'POST',
                    data: function(d) {
                        return {
                            part_id: $partAll.is(':checked') ? 'all' : $partId.val(),
                            lokasi_id: $lokasiAll.is(':checked') ? 'all' : $lokasiId.val(),
                            rack_id: $rackAll.is(':checked') ? 'all' : $rackId.val()
                        };
                    }
                },
                columns: [{
                        data: 'fifo',
                        className: 'text-center'
                    },
                    {
                        data: 'in',
                        className: 'text-center',
                        render: $.fn.dataTable.render.number('.', ',', 2)

                    },
                    {
                        data: 'units',
                        className: 'text-center'
                    },
                    {
                        data: 'remaining',
                        className: 'text-center',
                        render: $.fn.dataTable.render.number('.', ',', 2)

                    },
                    {
                        data: 'rack',
                        className: 'text-left'
                    },
                    {
                        data: 'lokasi',
                        className: 'text-center'
                    },
                    {
                        data: 'noted',
                        className: 'text-left'
                    }
                    // {
                    //     data: 'pa',
                    //     className: 'text-center'
                    // }
                ],
                createdRow: function(row, data, dataIndex) {
                    // Tambahkan kelas ke kolom terakhir
                    $('td:first-child', row).css('background-color', '#ccc'); // Warna abu-abu
                },
                // Ini posisi yang benar
                rowGroup: {
                    dataSrc: 'partname',
                    className: 'text-left',
                    endRender: function(rows, group) {
                        let total_in = 0;
                        let total_remaining = 0;

                        rows.data().each(function(row) {
                            total_in += parseFloat(row.in) || 0;
                            total_remaining += parseFloat(row.remaining) || 0;
                        });

                        const formatNumber = $.fn.dataTable.render.number('.', ',', 2).display;

                        return $('<tr/>')
                            .append(
                                '<td colspan="3" class="text-right">Stock Total Akhir</td>'
                            )
                            .append('<td class="text-center fw-bold">' + formatNumber(total_remaining) +
                                '</td>')
                            .append('<td colspan="3"></td>');
                    },
                    className: 'fw-bold'
                },
                order: [],
                pageLength: 50,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Tombol Terapkan Filter
            $('#btnFilterSubmit').on('click', function() {
                // Reload datatable dengan parameter baru
                table.ajax.reload();
            });


        });
    </script>

</body>

</html>