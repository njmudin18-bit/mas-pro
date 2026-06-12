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
    <!-- Tambahkan link CDN Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                                                    <div class="col-sm-12 mt-2">
                                                        <form id="formSisaMPR">
                                                            <div class="form-group row justify-content-left">
                                                                <label class="col-sm-2 col-form-label">Periode </label>
                                                                <div class="col-sm-4">
                                                                    <input type="date" id="pilih_periode"
                                                                        name="pilih_periode" class="form-control"
                                                                        placeholder="dd-mm-yyyy">

                                                                </div>
                                                            </div>
                                                            <div class="form-group row justify-content-left">
                                                                <label class="col-sm-2 col-form-label">Area </label>
                                                                <div class="col-sm-4">
                                                                    <select name="wh_lokasi" id="wh_lokasi"
                                                                        class="form-control">
                                                                        <option value="" disabled selected>-- Pilih --
                                                                        </option>
                                                                        <option value="ALL">ALL
                                                                        </option>
                                                                        <?php
                                                                        foreach ($wh_lokasi as $key => $value) {
                                                                        ?>
                                                                            <option
                                                                                value="<?php echo $value->wh_lokasi; ?>">
                                                                                <?php echo  $value->wh_lokasi; ?>
                                                                            </option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <span class="help-block"></span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row justify-content-left">
                                                                <label class="col-sm-2 col-form-label">User</label>
                                                                <div class="col-sm-4">
                                                                    <input type="text" name="user" id="user"
                                                                        class="form-control" readonly>
                                                                    <span class="help-block"></span>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <button type="button" id="btnProses"
                                                                        class="btn btn-primary mt-2 mt-md-0">
                                                                        Proses
                                                                    </button>
                                                                    <!-- <button type="button" id="btnTerima"
                                                                        class="btn btn-success mt-2 mt-md-0">
                                                                        Terima
                                                                    </button> -->
                                                                </div>

                                                                <!-- <div class="col-sm-2 ml-auto text-right">
                                                                    <button type="button" id="btnExportExcel"
                                                                        class="btn btn-success mt-2 mt-md-0">
                                                                        Export Excel
                                                                    </button>
                                                                </div> -->
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="row col-md-12">
                                                        <div class="col-md-12 d-flex justify-content-between">
                                                            <div>
                                                                <button type="button" id="btnTerima"
                                                                    class="btn btn-success mt-2 mt-md-0">
                                                                    Input No Bukti UNITED
                                                                </button>
                                                                <!-- <button type="button" id="btnCancel"
                                                                    class="btn btn-danger mt-2 mt-md-0">
                                                                    Batal Terima
                                                                </button> -->
                                                            </div>
                                                            <div>
                                                                <button type="button" id="btnExportExcel"
                                                                    class="btn btn-primary mt-2 mt-md-0">
                                                                    Export Excel
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr class="m-2">
                                                    <div class="m-2">
                                                        <div id="DetailRak" class="dt-responsive table-responsive">
                                                            <table id="order-table"
                                                                class="table table-striped table-bordered nowrap"
                                                                width="100%" border="1" cellpadding="0" cellspacing="0">
                                                                <thead>
                                                                    <tr class="bg-primary">
                                                                        <th><input type="checkbox" id="checkAll"></th>

                                                                        <!-- <th class="text-center">No.</th> -->
                                                                        <!-- <th class="text-center">Urutan Rack</th> -->
                                                                        <th class="text-center">Part Kolom</th>
                                                                        <th class="text-center">Part ID | Part NAME</th>
                                                                        <th class="text-center">Sisa Mpr</th>
                                                                        <th class="text-center">Units</th>
                                                                        <th class="text-center">Job</th>
                                                                        <th class="text-center">No Transaksi UNITED</th>
                                                                        <th class="text-center">Proses</th>
                                                                        <th class="text-center">Terima Produksi</th>
                                                                        <th class="text-center">Created date</th>
                                                                        <th style="display:none;">Group Label</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class=" m-2 table-responsive" style="width: 90%;">
                                                        <h3>Proses Sisa MPR</h3>
                                                        <table class="table table-bordered table-sm  mx-auto"
                                                            style="background:#fff;">
                                                            <tr>
                                                                <td class="py-2 px-3 align-middle">
                                                                    <div
                                                                        style="background-color:#28a745; width:54px; height:24px; border-radius:4px;">
                                                                    </div>
                                                                </td>
                                                                <td class="py-2 px-3 align-middle">Sudah diproses oleh
                                                                    warehouse/produksi</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-3 align-middle">
                                                                    <div
                                                                        style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;">
                                                                    </div>
                                                                </td>
                                                                <td class="py-2 px-3 align-middle">Belum diproses oleh
                                                                    warehouse/produksi</td>
                                                            </tr>
                                                        </table>
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

    <div id="loading-screen" class="loading">Loading&#8230;</div>




    <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>

    <script src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/js/index.min.js"></script>
    <script src="<?php echo base_url(); ?>files/bower_components/select2/js/select2.full.min.js"></script>
    <script src="<?php echo base_url(); ?>files/assets/pages/form-masking/autoNumeric.js"></script>

    <script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

    <script>
        flatpickr("#pilih_periode", {
            mode: "range",
            // dateFormat: "Y-m-d"
            dateFormat: "d-m-Y", // Format Indonesia
        });


        $(document).ready(function() {

            $('#loading-screen').hide();

            //export exel
            $('#btnExportExcel').on('click', function() {
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
                a.download = 'warehouse_sisa_mpr.xls';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });



            $('#wh_lokasi').on('change', function() {
                get_rack_user(); // Jalankan ajax untuk ambil user di wh_lokasinya
            });

            function get_rack_user() {
                let wh_lokasi = $('#wh_lokasi').val();

                // Kalau lokasi kosong, hentikan
                if (!wh_lokasi) {
                    $('#user').val('');
                    return;
                }

                // Kalau pilih ALL, langsung isi user dengan ALL (tanpa AJAX)
                if (wh_lokasi === "ALL") {
                    $('#user').val("ALL");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('warehouse_part/get_rack_user'); ?>",
                    data: {
                        wh_lokasi: wh_lokasi
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data && data.pic) {
                            $('#user').val(data.pic);
                        } else {
                            $('#user').val('');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: 'User tidak ditemukan untuk lokasi ini.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal mengambil data user!',
                        });
                    }
                });
            }


            let groupCounters = {};

            // Inisialisasi DataTable
            var table = $('#order-table').DataTable({
                processing: true,
                serverSide: false,
                paging: false,
                searching: true,
                lengthChange: false,
                info: false,
                ajax: {
                    url: "<?= base_url('warehouse_part/lihat_sisa_mpr') ?>",
                    type: "POST",
                    data: function(d) {
                        d.pilih_periode = $("#pilih_periode").val();
                        d.user = $("#user").val();
                        d.wh_lokasi = $('#wh_lokasi').val();
                    },
                    dataSrc: 'data',
                    deferLoading: 0 // jangan load otomatis, tunggu klik Proses
                },
                columnDefs: [{
                    orderable: false,
                    targets: '_all'
                }],
                order: [
                    [5, 'asc']
                ], // urutkan berdasarkan kolom job

                columns: [{
                        data: 'id_sisa_mpr',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input type="checkbox" class="checkItem" value="' + data + '">';
                        }
                    },
                    // {
                    //     data: null,
                    //     className: 'text-center',
                    //     render: function(data, type, row, meta) {
                    //         let grp = row.job;

                    //         if (!groupCounters[grp]) {
                    //             groupCounters[grp] = 1; // pertama kali ketemu group ini
                    //         } else {
                    //             groupCounters[grp]++;
                    //         }

                    //         return groupCounters[grp];
                    //     }
                    // }, 
                    {
                        data: 'nama_kolom',
                        className: 'text-center'
                    },
                    {
                        data: null,
                        className: 'text-left',
                        render: function(data, type, row) {
                            return row.partid + ' <br> ' + row.partname;
                        }
                    },
                    {
                        data: 'qty',
                        className: 'text-center'
                    },
                    {
                        data: 'units',
                        className: 'text-center'
                    },
                    {
                        data: 'job',
                        className: 'text-center'
                    },
                    
                    {
                        data: 'no_transaksi',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'terima_produksi',
                        className: 'text-center'
                    },
                    {
                        data: 'created_date',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (!data) return '';
                            let date = new Date(data); // pastikan format ISO/SQL datetime
                            let day = ("0" + date.getDate()).slice(-2);
                            let month = ("0" + (date.getMonth() + 1)).slice(-2);
                            let year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        }
                    },
                    {
                        data: 'group_label',
                        visible: false,
                        searchable: true
                    }
                ],
                rowGroup: {
                    dataSrc: 'group_label',
                    startRender: function(rows, group) {
                        let showButton = true;

                        // Loop semua row di group ini
                        rows.every(function(rowIdx, tableLoop, rowLoop) {
                            let data = this.data();
                            let noTrans = data.no_transaksi;

                            if (noTrans !== null && noTrans !== '') {
                                showButton = false;
                                return false; // stop loop
                            }
                        });

                        //     // bikin container row group
                        let $container = $('<div/>').append('<span class="fw-bold text-dark">' + group +
                            '</span>');

                        if (showButton) {
                            $container.append(
                                ' <button class="btn btn-sm btn-primary proses-transfer" data-job="' +
                                group + '">Proses Transfer Stok United</button>'
                            );
                        }

                        return $container;
                    },
                    className: 'text-left fw-bold' // kalau mau style langsung via class
                }

            });

            // fungsi checkbox all
            $('#checkAll').on('click', function() {
                $('.checkItem').prop('checked', this.checked);
            });


            // tombol ambil nilai
            $("#btnTerima").on("click", function() {
                let selected = [];
                $(".checkItem:checked").each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Peringatan",
                        text: "Belum ada data yang dipilih!"
                    });
                    return;
                }

                // Swal input no transaksi
                Swal.fire({
                    title: 'Masukkan No Transaksi United',
                    input: 'text',
                    inputPlaceholder: 'No Transaksi...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        const input = Swal.getInput();
                        input.addEventListener("input", function() {
                            this.value = this.value.toUpperCase();
                        });
                    },
                    preConfirm: (no_transaksi) => {
                        if (!no_transaksi) {
                            Swal.showValidationMessage("No Transaksi wajib diisi!");
                        }
                        return no_transaksi.toUpperCase();
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let noTransaksi = result.value;

                        $.ajax({
                            url: "<?= base_url('warehouse_part/update_nobuktiunited'); ?>",
                            type: "POST",
                            data: {
                                ids: selected,
                                no_transaksi: noTransaksi
                            },
                            dataType: "json",
                            success: function(res) {
                                if (res.status === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil",
                                        text: res.message ||
                                            "Data berhasil diperbarui!"
                                    }).then(() => {
                                        table.ajax.reload(null,
                                            false
                                        ); // reload tanpa reset pagination
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal",
                                        text: res.message ||
                                            "Terjadi kesalahan saat update!"
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Terjadi kesalahan: " + error
                                });
                            }
                        });
                    }
                });
            });

            $('#order-table').on('preDraw.dt', function() {
                groupCounters = {}; // reset counter setiap redraw
            });

            // Event klik tombol Proses
            $("#btnProses").on("click", function() {
                let periode = $("#pilih_periode").val();
                let user = $("#user").val();
                let lokasi = $("#wh_lokasi").val();

                if (user === "ALL") {
                    if (!periode) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Periode harus diisi!',
                        });
                        return;
                    }
                } else {
                    if (!periode || !user || !lokasi) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Semua field harus diisi!',
                        });
                        return;
                    }
                }


                // reload dataTables dengan parameter yang dikirim
                table.ajax.reload();
            });

            // // event click di link qty
            $(document).on("click", ".edit-qty", function(e) {
                e.preventDefault();

                let relasiId = $(this).data("relasi"); // id relasi sisa_mpr
                let oldQty = $(this).data("qty"); // qty lama

                Swal.fire({
                    title: "Edit Qty",
                    input: "number",
                    inputValue: oldQty,
                    inputAttributes: {
                        min: 0,
                        step: "0.01"
                    },
                    showCancelButton: true,
                    confirmButtonText: "Simpan",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let newQty = result.value;

                        if (newQty === "" || isNaN(newQty)) {
                            Swal.fire("Error", "Qty harus angka!", "error");
                            return;
                        }

                        // Kirim AJAX ke server
                        $.ajax({
                            url: "<?= base_url('warehouse_part/update_qty_mpr') ?>",
                            type: "POST",
                            data: {
                                relasi_id: relasiId,
                                oldQty: oldQty,
                                qty: newQty
                            },
                            success: function(res) {
                                // Pastikan res di-parse jika belum JSON
                                if (typeof res === 'string') {
                                    res = JSON.parse(res);
                                }

                                if (res.status === 'success') {
                                    Swal.fire('Sukses', res.message ||
                                            'Qty berhasil diproses!', 'success')
                                        .then(() => {
                                            // reload dataTables setelah sukses
                                            table.ajax.reload(null, false);
                                        });
                                } else {
                                    Swal.fire('Error', res.message ||
                                        'Terjadi kesalahan!', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire("Error", "Terjadi kesalahan server", "error");
                            }
                        });
                    }
                });
            });



            $(document).on('click', '.proses-transfer', function() {
                let jobNo = $(this).data('job');

                Swal.fire({
                    title: 'Masukkan No Transaksi',
                    input: 'text',
                    inputPlaceholder: 'No Transaksi...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        const input = Swal.getInput();
                        input.addEventListener("input", function() {
                            this.value = this.value.toUpperCase();
                        });
                    },
                    preConfirm: (no_transaksi) => {
                        if (!no_transaksi) {
                            Swal.showValidationMessage("No Transaksi wajib diisi!");
                        }
                        return no_transaksi.toUpperCase();
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let noTransaksi = result.value;

                        if (noTransaksi) {
                            $.ajax({
                                url: "<?= base_url('warehouse_part/input_transaksi_united'); ?>",
                                type: "POST",
                                data: {
                                    job_no: jobNo,
                                    no_transaksi: noTransaksi
                                },
                                success: function(res) {

                                    console.log(res);
                                    // Pastikan res di-parse jika belum JSON
                                    if (typeof res === 'string') {
                                        res = JSON.parse(res);
                                    }

                                    if (res.status === 'success') {
                                        Swal.fire('Sukses', res.message ||
                                                'Qty berhasil diproses!', 'success')
                                            .then(() => {
                                                // reload dataTables setelah sukses
                                                table.ajax.reload(null, false);
                                            });
                                    } else {
                                        Swal.fire('Error', res.message ||
                                            'Terjadi kesalahan!', 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Gagal memproses data!',
                                        'error');
                                }
                            });
                        } else {
                            Swal.fire('Peringatan', 'No Transaksi wajib diisi!', 'warning');
                        }
                    }
                });
            });


        });
    </script>

</body>
</html>