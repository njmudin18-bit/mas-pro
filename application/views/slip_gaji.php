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
                <strong style="font-size: 14px;">Bapak/Ibu <?php echo $Slip->NAME; ?>,</strong>
            </p>
            <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 20px 0;">
                Bersama email ini, kami lampirkan Slip Gaji Periode <strong><?php echo $Slip->PeriodeTanggal; ?></strong> sebagai informasi resmi mengenai rincian penghasilan Anda.<br>
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
                  <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">NIP</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->SSN; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Nama</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->NAME; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Departemen</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->DEPTNAME; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Status</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->STATUS; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Bulan</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->PeriodeBulan; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Periode</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->PeriodeTanggal; ?></td></tr>
                </table>
              </td>

              <td class="responsive-cell" width="50%" valign="top" style="padding-left: 10px;">
                <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                  <tr><th colspan="2" class="sub-header-cell">Detail Kehadiran</th></tr>
                  <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">HK (Hari Kalender)</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->HK; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">HD (Hari Dibayar)</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->HD; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Sakit</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->Sakit; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Ijin</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->Ijin; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Alpa</th><td class="data-cell" style="border-color: #cccccc;"><?php echo $Slip->Alpa; ?></td></tr>
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
                    <th class="sub-header-cell" style="background-color: #004d99 !important; color: #fff; text-align: center;">Jumlah</th>
                  </tr>
                  <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">Upah</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TotalGaji; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Makan</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TotalTunjMakan; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Kehadiran</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TotalTunjHadir; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Lembur</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TotalTunjLembur; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Shift</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TotalTunjShift; ?></td></tr>
                  <tr>
                    <th class="sub-header-cell" style="background-color: #f0f0f0;">Total Lembur</th>
                    <td class="data-cell" style="border-color: #cccccc;text-align: right;">
                      <?php echo $Slip->TotalLembur; ?>
                      <?php if ($Slip->JamLembur > 0): ?>
                        <span style="float: right; margin-left: 5px;">(<?php echo $Slip->JamLembur; ?> Jam)</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tunj. Lainnya</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->TunjLainnya; ?></td></tr>
                  <tr>
                    <th class="sub-header-cell" style="background-color: #e0f0ff;border-top: 2px solid #000;font-size: 15px;">Total Pendapatan</th>
                    <th class="data-cell" style="background-color: #e0f0ff;border-color: #cccccc;text-align: right;border-top: 2px solid #000;font-size: 15px;"><?php echo $Slip->TotalPendapatan; ?></th>
                  </tr>
                </table>
              </td>

              <td class="responsive-cell" width="50%" valign="top" style="padding-left: 10px;">
                <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                  <tr>
                    <th class="sub-header-cell" style="background-color: #cc0000 !important; color: #fff;">Potongan</th>
                    <th class="sub-header-cell" style="background-color: #cc0000 !important; color: #fff; text-align: center;">Jumlah</th>
                  </tr>
                  <tr><th width="40%" class="sub-header-cell" style="background-color: #f0f0f0;">BPJS (TK + KS)</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->PotBPJS; ?></td></tr>
                  <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Hutang</th><td class="data-cell" style="border-color: #cccccc;text-align: right;"><?php echo $Slip->PotHutang; ?></td></tr>
                  <tr>
                    <th class="sub-header-cell" style="background-color: #ffe0e0;border-top: 2px solid #000;font-size: 15px;">Total Potongan</th>
                    <th class="data-cell" style="background-color: #ffe0e0;border-color: #cccccc;text-align: right;border-top: 2px solid #000;font-size: 15px;"><?php echo $Slip->TotalPotongan; ?></th>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br>

          <table class="content-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; border-color: #cccccc;">
            <tr>
              <td colspan="2" style="background-color: #4CAF50; color: #ffffff; text-align: right; font-weight: bold; line-height: 45px; padding-right: 15px; border: 3px solid #4CAF50; font-size: 16px;">
                GAJI BERSIH
              </td>
              <td colspan="2" style="background-color: #4CAF50; color: #ffffff; text-align: right; font-weight: bold; line-height: 45px; padding-right: 15px; border: 3px solid #4CAF50; font-size: 16px;">
                <?php echo $Slip->GajiBersih; ?>
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