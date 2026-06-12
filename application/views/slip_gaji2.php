<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji | <?php echo APPS_CORP; ?></title>
    <style type="text/css">
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
        }
        /* Style umum untuk sel */
        .data-cell {
            line-height: 25px;
            padding-left: 15px;
            padding-right: 15px;
            font-size: 13px;
        }
        .header-cell {
            background-color: #004d99; /* Navy Blue */
            color: #ffffff;
            text-align: center;
            padding-left: 15px;
            line-height: 30px;
            font-size: 13px;
        }
        .sub-header-cell {
            background-color: #e6e6ff; /* Light Lavender/Blue */
            color: #333333;
            text-align: left;
            padding-left: 15px;
            line-height: 28px;
            font-weight: bold;
            border-bottom: 1px solid #cccccc;
        }
        .amount-cell {
            line-height: 25px;
            padding-left: 15px;
            padding-right: 15px;
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }

        /* Media Query untuk Responsifitas: Digunakan untuk layar kecil (mobile) */
        @media only screen and (max-width: 600px) {
            /* Lebar maksimal disesuaikan ke 100% */
            .main-table, .content-table, .responsive-inner-table {
                width: 100% !important;
            }
            /* Menyusun ulang kolom 2x2 menjadi 1x1 di mobile */
            .responsive-cell {
                width: 100% !important;
                display: block !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }
            /* Memberi jarak antar blok data di mobile */
            .responsive-cell:last-child table {
                margin-top: 10px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">

    <table class="main-table" align="center" border="0" cellpadding="0" cellspacing="0" width="900" style="border-collapse: collapse; border: 1px solid #cccccc; background-color: #ffffff;">
        <tr>
            <td style="padding: 20px;">

                <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 15px 0;">
                    Kepada Yth.<br>
                    <strong style="font-size: 14px;">Bapak/Ibu [Nama Karyawan],</strong>
                </p>
                <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 20px 0;">
                    Bersama email ini, kami lampirkan Slip Gaji Periode [Bulan Tahun] sebagai informasi resmi mengenai rincian penghasilan Anda.<br>
                    Mohon untuk memeriksa kembali data yang tertera, dan segera hubungi bagian HRD apabila terdapat ketidaksesuaian.
                </p>

                <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 15px 0;">
                    Hormat kami.<br><br><br>Dept. HRD<br>
                    <strong style="font-size: 14px;"><?php echo APPS_CORP; ?></strong>
                </p>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr style="height: 60px; background-color: #007bff; color: #ffffff; text-align: center; font-size: 20px; font-weight: bold;">
                        <td style="padding: 10px; line-height: 25px;">
                            SLIP GAJI<br>
                            <small style="font-size: 14px"><?php echo APPS_CORP; ?></small>
                        </td>
                    </tr>
                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td class="responsive-cell" width="50%" valign="top" style="padding-right: 10px;">
                            <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                                <tr><th colspan="2" class="sub-header-cell">Detail Karyawan</th></tr>
                                <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">NIP</th><td class="data-cell" style="border-color: #cccccc;">12011030044</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Nama</th><td class="data-cell" style="border-color: #cccccc;">ANWAR BIN MADHASAN</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Departemen</th><td class="data-cell" style="border-color: #cccccc;">EXTRUDE</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Status</th><td class="data-cell" style="border-color: #cccccc;">TETAP</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Bulan</th><td class="data-cell" style="border-color: #cccccc;">September 2025</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Periode</th><td class="data-cell" style="border-color: #cccccc;">03 - 16 September 2025</td></tr>
                            </table>
                        </td>

                        <td class="responsive-cell" width="50%" valign="top" style="padding-left: 10px;">
                            <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                                <tr><th colspan="2" class="sub-header-cell">Detail Kehadiran</th></tr>
                                <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">HK (Hari Kalender)</th><td class="data-cell" style="border-color: #cccccc;">14</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">HD (Hari Dibayar)</th><td class="data-cell" style="border-color: #cccccc;">12</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Sakit</th><td class="data-cell" style="border-color: #cccccc;">2</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Ijin</th><td class="data-cell" style="border-color: #cccccc;">1</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Alpa</th><td class="data-cell" style="border-color: #cccccc;">0</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Cuti</th><td class="data-cell" style="border-color: #cccccc;">-</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td class="responsive-cell" width="50%" valign="top" style="padding-right: 10px;">
                            <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                                <tr>
                                    <th class="sub-header-cell" style="background-color: #004d99 !important; color: #fff;">Pendapatan</th>
                                    <th class="sub-header-cell" style="background-color: #004d99 !important; color: #fff; text-align: right; padding-right: 15px;">Jumlah</th>
                                </tr>
                                <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">Upah</th><td class="amount-cell" style="border-color: #cccccc;">2.287.194</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Makan</th><td class="amount-cell" style="border-color: #cccccc;">16.000</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Kehadiran</th><td class="amount-cell" style="border-color: #cccccc;">180.000</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Lembur</th><td class="amount-cell" style="border-color: #cccccc;">1.062.381</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Shift</th><td class="amount-cell" style="border-color: #cccccc;">0</td></tr>
                                <tr>
                                  <th class="sub-header-cell" style="background-color: #f0f0f0;">Total Lembur</th>
                                  <td class="amount-cell" style="border-color: #cccccc;">
                                    0 <span style="float: right; margin-left: 5px;">(4 Jam)</span>
                                  </td>
                                </tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Lainnya</th><td class="amount-cell" style="border-color: #cccccc;">0</td></tr>
                                <tr>
                                    <th class="sub-header-cell" style="background-color: #e0f0ff;border-top: 2px solid #000;font-size: 14px;">Total Pendapatan</th>
                                    <th class="amount-cell" style="background-color: #e0f0ff;border-color: #cccccc;border-top: 2px solid #000;font-size: 14px;">3.545.575</th>
                                </tr>
                            </table>
                        </td>

                        <td class="responsive-cell" width="50%" valign="top" style="padding-left: 10px;">
                            <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                                <tr>
                                    <th class="sub-header-cell" style="background-color: #cc0000 !important; color: #fff;">Potongan</th>
                                    <th class="sub-header-cell" style="background-color: #cc0000 !important; color: #fff; text-align: right; padding-right: 15px;">Jumlah</th>
                                </tr>
                                <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">BPJS (TK + KS)</th><td class="amount-cell" style="border-color: #cccccc;">91.488</td></tr>
                                <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Hutang</th><td class="amount-cell" style="border-color: #cccccc;">0</td></tr>
                                <tr><td colspan="2" class="data-cell" style="border: none; padding-top: 5px; padding-bottom: 5px; background-color: #ffffff;">&nbsp;</td></tr>
                                <tr><td colspan="2" class="data-cell" style="border: none; padding-top: 5px; padding-bottom: 5px; background-color: #ffffff;">&nbsp;</td></tr>
                                <tr><td colspan="2" class="data-cell" style="border: none; padding-top: 5px; padding-bottom: 5px; background-color: #ffffff;">&nbsp;</td></tr>
                                <tr><td colspan="2" class="data-cell" style="border: none; padding-top: 5px; padding-bottom: 5px; background-color: #ffffff;">&nbsp;</td></tr>
                                <tr>
                                    <th class="sub-header-cell" style="background-color: #ffe0e0;border-top: 2px solid #000;font-size: 14px;">Total Potongan</th>
                                    <th class="amount-cell" style="background-color: #ffe0e0;border-color: #cccccc;border-top: 2px solid #000;font-size: 14px;">3.545.575</th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>

                <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td width="70%" style="background-color: #4CAF50; color: #ffffff; text-align: right; font-weight: bold; line-height: 45px; padding-right: 15px; border: 3px solid #4CAF50; font-size: 16px;">
                            GAJI BERSIH
                        </td>
                        <td width="30%" style="background-color: #4CAF50; color: #ffffff; text-align: right; font-weight: bold; line-height: 45px; padding-right: 15px; border: 3px solid #4CAF50; font-size: 18px;">
                            Rp. 38.158.512
                        </td>
                    </tr>
                </table>

                <br>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 13px; text-align: center; margin-top: 20px;">
                    <tr>
                        <td style="padding-top: 5px; color: #333333; line-height: 1.5;"> 
                            <strong style="color: #cc0000;">DOKUMEN INI BERSIFAT RAHASIA DAN PRIBADI.</strong><br> 
                            Harap simpan slip gaji ini dengan baik. Penggunaan data dalam dokumen ini di luar kepentingan pribadi<br>dapat dikenakan sanksi sesuai peraturan perusahaan.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px; color: #007bff; font-weight: bold; line-height: 1.5;">
                            Untuk pertanyaan atau informasi lebih lanjut, silahkan hubungi Departemen HRD.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>