<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Lembur | <?php echo APPS_CORP; ?></title>
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
            /* Warna diubah menjadi hijau untuk menandakan persetujuan (Y) */
            background-color: #e0ffe0; 
            color: #333333;
            text-align: left;
            padding-left: 15px;
            line-height: 28px;
            font-weight: bold;
            border-bottom: 1px solid #cccccc;
        }
        /* Style baru untuk cell data lembur */
        .detail-cell {
            line-height: 25px;
            padding-left: 15px;
            padding-right: 15px;
            font-weight: normal;
            font-size: 13px;
            text-align: left;
            border-color: #cccccc;
        }
        /* Media Query untuk Responsifitas: Digunakan untuk layar kecil (mobile) */
        @media only screen and (max-width: 600px) {
            /* Lebar maksimal disesuaikan ke 100% */
            .main-table, .content-table, .responsive-inner-table {
                width: 100% !important;
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
              <strong style="font-size: 14px;">Bapak/Ibu <?php echo $Lembur->NAME; ?>,</strong>
            </p>
            <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 20px 0;">
              Bersama email ini, kami sampaikan bahwa <strong>Permintaan Lembur</strong> Anda telah <strong>DISETUJUI</strong>.<br>
              Berikut adalah rincian data lembur yang disetujui:
            </p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
              <tr style="height: 60px; background-color: #28a745; color: #ffffff; text-align: center; font-size: 20px; font-weight: bold;">
                <td style="padding: 10px; line-height: 25px;">
                  PERSETUJUAN LEMBUR
                  <br>
                  <small style="font-size: 14px"><?php echo APPS_CORP; ?></small>
                </td>
              </tr>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 13px;">
              <tr>
                <td width="100%" valign="top">
                  <table class="responsive-inner-table" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px; border-color: #cccccc;">
                    <tr><th colspan="2" class="sub-header-cell" style="background-color: #004d99; color: #fff;">Detail Karyawan & Lembur</th></tr>
                    <tr><th width="20%" class="sub-header-cell" style="background-color: #f0f0f0;">NIP</th><td class="detail-cell"><?php echo $Lembur->EmployeeID; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Nama</th><td class="detail-cell"><?php echo $Lembur->NAME; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Departemen</th><td class="detail-cell"><?php echo $Lembur->DEPTNAME; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Tanggal Lembur</th><td class="detail-cell"><?php echo $Lembur->OvertimeDate; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Waktu</th><td class="detail-cell"><?php echo $Lembur->StartTime; ?> s/d <?php echo $Lembur->EndTime; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Total Jam</th><td class="detail-cell"><?php echo $Lembur->TotalHours; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Deskripsi</th><td class="detail-cell"><?php echo $Lembur->Notes; ?></td></tr>
                    <tr><th class="sub-header-cell" style="background-color: #f0f0f0;">Disetujui Oleh</th><td class="detail-cell"><?php echo $Lembur->ApprovedName; ?> pada <?php echo $Lembur->ApprovedDate; ?></td></tr>
                  </table>
                </td>
              </tr>
            </table>
            <br>
            <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="100%" style="background-color: #4CAF50; color: #ffffff; text-align: center; font-weight: bold; line-height: 45px; padding-right: 15px; border: 3px solid #4CAF50; font-size: 16px;">
                        STATUS: <?php echo $Lembur->Status; ?>
                    </td>
                </tr>
            </table>
            <br>
            <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 15px 0;">
              Terima kasih atas kontribusi Anda. Harap diperhatikan bahwa perhitungan upah lembur akan diproses sesuai jadwal penggajian perusahaan.
            </p>
            <p style="font-size: 13px; color: #333333; line-height: 1.6; margin: 0 0 15px 0;">
              Hormat kami.<br><br><br>Dept. HRD<br>
              <strong style="font-size: 14px;"><?php echo APPS_CORP; ?></strong>
            </p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 13px; text-align: center; margin-top: 20px;">
              <tr>
                <td style="padding-top: 10px; color: #007bff; font-weight: bold; line-height: 1.5;">
                  Untuk pertanyaan atau informasi lebih lanjut, silakan hubungi atasan langsung Anda atau Departemen HRD.
                </td>
              </tr>
            </table>
        </td>
      </tr>
    </table>
  </body>
</html>