<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_slipgaji extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *        http://example.com/index.php/welcome
   *    - or -
   *        http://example.com/index.php/welcome/index
   *    - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart', 'email'));

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  //DASHBOARD
  public function index()
  {
    $this->load->view('slip_gaji');
  }

  function kirim_slip_gaji()
  {
    $this->load->library('email');
    $this->load->config('email');

    $method = $this->input->method(TRUE);
    if ($method !== 'POST') {
      echo json_encode([
          "status_code" => 405,
          "status"      => "Error",
          "message"     => "Method Not Allowed",
          "data"        => []
      ]);
      return;
    }

    // Autentikasi basic
    $username = $this->input->server('PHP_AUTH_USER');
    $password = $this->input->server('PHP_AUTH_PW');

    if ($username !== 'njmudin@omas-mfg.com' || $password !== '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
      echo json_encode([
        "status_code" => 401,
        "status"      => "Unauthorized",
        "message"     => "Restricted data",
        "data"        => []
      ]);

      return;
    }

    $DeptID = $this->input->post('DeptID');
    //echo $DeptID; exit;
    $Date   = "2025-11-21"; // bisa diganti date('Y-m-d')
    $Sql    = "SELECT StartDate, EndDate, Payday, Cycle FROM Trans_PeriodeGaji WHERE Payday = ?";
    $Query  = $this->Attendance->query($Sql, [$Date]);

    if ($Query->num_rows() === 0) {
      echo json_encode([
        "status_code" => 404,
        "status"      => "Not Found",
        "message"     => "Data tidak ditemukan",
        "data"        => []
      ]);

      return;
    }

    $Res       = $Query->row();
    $StartDate = $Res->StartDate;
    $EndDate   = $Res->EndDate;
    $Cycle     = $Res->Cycle;
    //$DeptID    = '1231'; // bisa disesuaikan atau NULL jika ingin tanpa departemen

    // Ambil data gaji pegawai
    $Sql2 = "EXEC dbo.GetGajiDataForEmail 
            @StartDate = ?, 
            @EndDate = ?, 
            @DeptID = ?, 
            @Cycle = ?";

            //echo $Sql2; exit;

    $Query2 = $this->Attendance->query($Sql2, [$StartDate, $EndDate, $DeptID, $Cycle]);
    $Result = $Query2->result();
    $response = [
      'success' => [],
      'failed'  => []
    ];

    //echo json_encode(array('status' => 'error', 'data' => $Result)); exit;

    if (count($Result) === 0) {
      //echo "Tidak ada pegawai yang perlu dikirim slip gaji.";
      $response['failed'][] = [
        'name'  => null,
        'email' => null,
        'status'=> 'Tidak ada pegawai yang perlu dikirimi slip gaji.'
      ];

      return;
    }

    $email_logs = []; // array untuk menyimpan status email

    foreach ($Result as $value) {
      if (!empty($value->Email)) {
        $data['Slip'] = $value; // hanya data pegawai ini
        $message = $this->load->view('slip_gaji', $data, TRUE);

        $this->email->clear(true);
        $this->email->from('notifications@omas-mfg.com', 'PT. MULTI ARTA SEKAWAN');
        //$this->email->to($value->Email); // kirim ke masing-masing pegawai
        $this->email->to('nj.mudin18@gmail.com');
        // $this->email->cc([
        //   'personaliaga.omas@gmail.com'
        // ]);
        $this->email->subject('Slip Gaji Periode '.$value->PeriodeTanggal);
        $this->email->message($message);

        if ($this->email->send()) {
          $date = date('Y-m-d H:i:s');
          $this->Attendance->query(
              "UPDATE Trans_Gaji SET KirimEmail = ?, KirimEmailOn = ? WHERE Id = ?",
              ['Y', $date, $value->Id]
          );

          $response['success'][] = [
            'name'  => $value->NAME,
            'email' => $value->Email,
            'status'=> 'Email berhasil dikirim'
          ];
        } else {
          $response['failed'][] = [
            'name'  => $value->NAME,
            'email' => $value->Email,
            'status'=> 'Gagal mengirim email'
          ];
          $email_logs[] = $this->email->print_debugger(['headers']);
        }
      } else {
        $response['failed'][] = [
          'name'  => $value->NAME,
          'email' => null,
          'status'=> 'Pegawai tidak memiliki email'
        ];
      }
    }

    // Output JSON atau array sesuai kebutuhan
    echo json_encode($response, JSON_PRETTY_PRINT);
  }

  function kirim_slip_gaji_OLD()
  {
    $this->load->library('email');
    $this->load->config('email');

    $method = $this->input->method(TRUE);
    if ($method !== 'POST') {
      echo json_encode([
          "status_code" => 405,
          "status"      => "Error",
          "message"     => "Method Not Allowed",
          "data"        => []
      ]);
      return;
    }

    // Autentikasi basic
    $username = $this->input->server('PHP_AUTH_USER');
    $password = $this->input->server('PHP_AUTH_PW');

    if ($username !== 'njmudin@omas-mfg.com' || $password !== '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
      echo json_encode([
        "status_code" => 401,
        "status"      => "Unauthorized",
        "message"     => "Restricted data",
        "data"        => []
      ]);

      return;
    }

    $Date  = "2025-09-26"; // bisa diganti date('Y-m-d')
    $Sql   = "SELECT StartDate, EndDate, Payday, Cycle FROM Trans_PeriodeGaji WHERE Payday = ?";
    $Query = $this->Attendance->query($Sql, [$Date]);

    if ($Query->num_rows() === 0) {
      echo json_encode([
        "status_code" => 404,
        "status"      => "Not Found",
        "message"     => "Data tidak ditemukan",
        "data"        => []
      ]);

      return;
    }

    $Res       = $Query->row();
    $StartDate = $Res->StartDate;
    $EndDate   = $Res->EndDate;
    $Cycle     = $Res->Cycle;
    $DeptID    = '1231'; // bisa disesuaikan atau NULL jika ingin tanpa departemen

    // Ambil data gaji pegawai
    $Sql2 = "SELECT
                TOP 10
                a.Id, a.EmployeeID AS SSN, UPPER(b.NAME) AS NAME, 
                a.DeptID AS DEPTID, c.DEPTNAME, a.KirimEmail, d.Email,
                e.Payday,
                FORMAT(e.Payday, 'MMMM yyyy', 'id-ID') AS PeriodeBulan,
                (FORMAT(a.StartDate, 'dd') + ' - ' + FORMAT(a.EndDate, 'dd') + ' ' + FORMAT(a.EndDate, 'MMMM yyyy', 'id-ID')) AS PeriodeTanggal,
                CASE b.CITY WHEN 'F' THEN 'TETAP' WHEN 'C' THEN 'KONTRAK' WHEN 'I' THEN 'MAGANG' ELSE '-' END AS STATUS,
                a.StartDate, a.EndDate, a.Cycle, a.HK, a.HD, a.Sakit, a.Ijin, a.Alpa, a.TelatMore10, a.TelatMore15, a.Libur, 
                FORMAT(a.GajiPokok, 'N0', 'id-ID') AS GajiPokok,
                a.Pembagi,
                FORMAT(a.Upah, 'N0', 'id-ID') AS Upah,
                FORMAT(a.UangMakan, 'N0', 'id-ID') AS UangMakan,
                FORMAT(a.UangTunjHadir, 'N0', 'id-ID') AS UangTunjHadir,
                FORMAT(a.UangShift, 'N0', 'id-ID') AS UangShift,
                FORMAT(a.UangLiburLembur, 'N0', 'id-ID') AS UangLiburLembur,
                FORMAT(a.JamLembur, 'N0', 'id-ID') AS JamLembur,
                FORMAT(a.TotalUpah, 'N0', 'id-ID') AS TotalUpah,
                FORMAT(a.TotalTunjMakan, 'N0', 'id-ID') AS TotalTunjMakan,
                FORMAT(a.TotalTunjHadir, 'N0', 'id-ID') AS TotalTunjHadir,
                FORMAT(a.TotalTunjLembur, 'N0', 'id-ID') AS TotalTunjLembur,
                FORMAT(a.TotalTunjShift, 'N0', 'id-ID') AS TotalTunjShift,
                FORMAT(a.TotalLembur, 'N0', 'id-ID') AS TotalLembur,
                FORMAT(a.PotBPJS, 'N0', 'id-ID') AS PotBPJS,
                FORMAT(a.TotalGaji, 'N0', 'id-ID') AS TotalGaji,
                FORMAT(ISNULL(a.TunjLainnya, 0), 'N0', 'id-ID') AS TunjLainnya,
                FORMAT(ISNULL(a.PotHutang, 0), 'N0', 'id-ID') AS PotHutang,
                FORMAT(a.TotalUpah + a.TotalTunjMakan + a.TotalTunjHadir + a.TotalTunjLembur + a.TotalTunjShift + a.TotalLembur + ISNULL(a.TunjLainnya,0), 'N0','id-ID') AS TotalPendapatan,
                FORMAT(ISNULL(a.PotBPJS,0) + ISNULL(a.PotHutang,0), 'N0','id-ID') AS TotalPotongan,
                FORMAT(a.GajiBersih, 'N0','id-ID') AS GajiBersih,
                CONVERT(VARCHAR(19), a.CreatedDate, 120) AS CreatedDate,
                a.CreatedBy
             FROM Trans_Gaji a
             LEFT JOIN USERINFO b ON b.SSN = a.EmployeeID
             LEFT JOIN DEPARTMENTS c ON c.DEPTID = a.DeptID
             LEFT JOIN USERINFO_PROPERTIES d ON d.SSN = a.EmployeeID
             LEFT JOIN Trans_PeriodeGaji e ON e.StartDate = a.StartDate AND e.EndDate = a.EndDate
             WHERE a.StartDate = ? AND a.EndDate = ? 
               AND (? IS NULL OR a.DeptID = ?)
               AND a.Cycle = ? 
               AND a.KirimEmail IS NULL
             ORDER BY c.DEPTNAME ASC, b.NAME ASC, a.CreatedDate DESC";

    $Query2 = $this->Attendance->query($Sql2, [$StartDate, $EndDate, $DeptID, $DeptID, $Cycle]);
    $Result = $Query2->result();
    $response = [
      'success' => [],
      'failed'  => []
    ];

    if (count($Result) === 0) {
      //echo "Tidak ada pegawai yang perlu dikirim slip gaji.";
      $response['failed'][] = [
        'name'  => null,
        'email' => null,
        'status'=> 'Tidak ada pegawai yang perlu dikirimi slip gaji.'
      ];

      return;
    }

    $email_logs = []; // array untuk menyimpan status email

    foreach ($Result as $value) {
      if (!empty($value->Email)) {
        $data['Slip'] = $value; // hanya data pegawai ini
        $message = $this->load->view('slip_gaji', $data, TRUE);

        $this->email->clear(true);
        $this->email->from('notifications@omas-mfg.com', 'PT. MULTI ARTA SEKAWAN');
        //$this->email->to($value->Email); // kirim ke masing-masing pegawai
        $this->email->to('nj.mudin18@gmail.com');
        // $this->email->cc([
        //   'personaliaga.omas@gmail.com'
        // ]);
        $this->email->subject('Slip Gaji Periode '.$value->PeriodeTanggal);
        $this->email->message($message);

        if ($this->email->send()) {
          $date = date('Y-m-d H:i:s');
          $this->Attendance->query(
              "UPDATE Trans_Gaji SET KirimEmail = ?, KirimEmailOn = ? WHERE Id = ?",
              ['Y', $date, $value->Id]
          );

          $response['success'][] = [
            'name'  => $value->NAME,
            'email' => $value->Email,
            'status'=> 'Email berhasil dikirim'
          ];
        } else {
          $response['failed'][] = [
            'name'  => $value->NAME,
            'email' => $value->Email,
            'status'=> 'Gagal mengirim email'
          ];
          $email_logs[] = $this->email->print_debugger(['headers']);
        }
      } else {
        $response['failed'][] = [
          'name'  => $value->NAME,
          'email' => null,
          'status'=> 'Pegawai tidak memiliki email'
        ];
      }
    }

    // Output JSON atau array sesuai kebutuhan
    echo json_encode($response, JSON_PRETTY_PRINT);
  }

  public function send()
  {
    $this->load->library('email');
    $this->load->config('email');

    // Set pengirim
    $this->email->from('notification@omas-mfg.com', 'MASPRO');

    // Penerima
    $this->email->to('nj.mudin18@gmail.com');

    // Subject & body
    $this->email->subject('Pengingat Sertifikat Expired');
    $this->email->message('<h3>Halo!</h3><p>Email ini dikirim pakai SMTP cPanel.</p>');

    // Kirim
    if ($this->email->send()) {
      echo "✅ Email berhasil dikirim.";
    } else {
      echo "❌ Gagal mengirim email.<br>";
      echo $this->email->print_debugger(['headers']);
    }
  }

  public function notif_temp()
  {
    $data['User']   = "Mas";
    $this->load->view('notif_temp', $data, FALSE);
  }
}
