<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
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

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  //DASHBOARD
  public function index()
  {
    $this->load->view('testing');
  }

  function send_notifikasi_sertikat()
  {
    $this->load->library('email');
    $this->load->config('email');

    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        if (date('N') != 1 && $this->input->post('bypass_day') !== 'true') {
          echo json_encode(
            array(
              "status_code"   => 200,
              "status"        => "Success",
              "message"       => "Hari ini bukan hari Senin. Pengiriman email diabaikan (hanya dilakukan setiap hari Senin).",
              "data"          => array()
            )
          );
          exit;
        }

        // $Sql    = "SELECT 
        //             Id, CertificateName, CertificateCode, DeptID, IssueDate,
        //             FORMAT(ExpiryDate, 'dddd, dd MMMM yyyy', 'id-ID') AS ExpiryDate,
        //             ReminderIn, ReminderStatus,
        //             CASE 
        //               WHEN ReminderIn LIKE '%month%' 
        //                 THEN DATEADD(MONTH, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
        //               WHEN ReminderIn LIKE '%day%'
        //                 THEN DATEADD(DAY, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
        //               ELSE ExpiryDate
        //             END AS ReminderDate
        //           FROM Trans_Certificates
        //           WHERE ReminderStatus = 'Enabled'
        //           AND ExpiryDate >= CAST(GETDATE() AS DATE)
        //           AND GETDATE() BETWEEN 
        //             CASE 
        //               WHEN ReminderIn LIKE '%month%' 
        //                 THEN DATEADD(MONTH, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
        //               WHEN ReminderIn LIKE '%day%'
        //                 THEN DATEADD(DAY, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
        //               ELSE ExpiryDate
        //             END
        //           AND ExpiryDate";
        $Result = $this->BJGMAS01->query("EXEC dbo.GetCertificateReminders")->result();

        if (count($Result) > 0) {
            $data['Result'] = $Result;
            // Load view sebagai string
            $message = $this->load->view('notif_temp', $data, TRUE);

            // Set pengirim
            $this->email->from('notifications@omas-mfg.com', 'MASPRO');

            // Penerima
            //$this->email->to('nj.mudin18@gmail.com');
            $this->email->to('m.representative@omas-mfg.com');
            $this->email->cc([
              'chandjoe@omas-mfg.com',
              'humanresource@omas-mfg.com',
              'qcontrol@omas-mfg.com',
              'ehs@omas-mfg.com',
              'it.ptmas@omas-mfg.com'
            ]);
            $this->email->bcc('nj.mudin18@gmail.com');

            // Subject & body
            $this->email->subject('Reminder Sertifikat Expired');
            $this->email->message($message);

            // Kirim
            if ($this->email->send()) {
              echo "✅ Email berhasil dikirim.";
            } else {
              echo "❌ Gagal mengirim email.<br>";
              echo $this->email->print_debugger(['headers']);
            }
        } else {
          echo "Tidak ada data reminder.";
        }

        //ADDING TO LOG
        $log_url 	  = base_url().$this->contoller_name."/".$this->function_name;
        $log_type 	= "API REMINDER SERTIFIKAT";
        $log_data 	= json_encode($Result);
        
        log_helper($log_url, $log_type, $log_data);
        //END LOG
        exit;
      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
  }

  function send_notifikasi_sertikat_OLD()
  {
    $this->load->library('email');
    $this->load->config('email');

    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        $Sql    = "SELECT 
                    Id, CertificateName, CertificateCode, DeptID, IssueDate,
                    FORMAT(ExpiryDate, 'dddd, dd MMMM yyyy', 'id-ID') AS ExpiryDate,
                    ReminderIn, ReminderStatus,
                    CASE 
                      WHEN ReminderIn LIKE '%month%' 
                        THEN DATEADD(MONTH, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
                      WHEN ReminderIn LIKE '%day%'
                        THEN DATEADD(DAY, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
                      ELSE ExpiryDate
                    END AS ReminderDate
                  FROM Trans_Certificates
                  WHERE ReminderStatus = 'Enabled'
                  AND ExpiryDate >= CAST(GETDATE() AS DATE)
                  AND GETDATE() BETWEEN 
                    CASE 
                      WHEN ReminderIn LIKE '%month%' 
                        THEN DATEADD(MONTH, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
                      WHEN ReminderIn LIKE '%day%'
                        THEN DATEADD(DAY, -CAST(LEFT(ReminderIn, CHARINDEX(' ', ReminderIn)-1) AS INT), ExpiryDate)
                      ELSE ExpiryDate
                    END
                  AND ExpiryDate";
        $Query  = $this->BJGMAS01->query($Sql);
        $Result = $Query->result();

        if (count($Result) > 0) {
            $data['Result'] = $Result;
            // Load view sebagai string
            $message = $this->load->view('notif_temp', $data, TRUE);

            // Set pengirim
            $this->email->from('notifications@omas-mfg.com', 'MASPRO');

            // Penerima
            //$this->email->to('nj.mudin18@gmail.com');
            $this->email->to('m.representative@omas-mfg.com');
            $this->email->cc([
              'chandjoe@omas-mfg.com',
              'humanresource@omas-mfg.com',
              'qcontrol@omas-mfg.com',
              'ehs@omas-mfg.com',
              'it.ptmas@omas-mfg.com'
            ]);
            $this->email->bcc('nj.mudin18@gmail.com');

            // Subject & body
            $this->email->subject('Reminder Sertifikat Expired');
            $this->email->message($message);

            // Kirim
            if ($this->email->send()) {
              echo "✅ Email berhasil dikirim.";
            } else {
              echo "❌ Gagal mengirim email.<br>";
              echo $this->email->print_debugger(['headers']);
            }
        } else {
          echo "Tidak ada data reminder.";
        }

        //ADDING TO LOG
        $log_url 	  = base_url().$this->contoller_name."/".$this->function_name;
        $log_type 	= "API REMINDER SERTIFIKAT";
        $log_data 	= json_encode($Result);
        
        log_helper($log_url, $log_type, $log_data);
        //END LOG
        exit;
      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
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
