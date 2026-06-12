<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   * 		http://example.com/index.php/welcome
   *	- or -
   * 		http://example.com/index.php/welcome/index
   *	- or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/userguide3/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('auth_model', 'auth');
    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');

    //START ADD THIS FOR USER ROLE MANAGMENT
    $this->contoller_name = $this->router->class;
    $this->function_name  = $this->router->method;
    //END
  }

  public function index()
  {
    $data['perusahaan'] = $this->perusahaan->get_details();
    $this->load->view('welcome_message', $data, FALSE);
  }

  public function login_proses()
  {
    // $recaptchaResponse  = trim($this->input->post('g-recaptcha-response'));
    // $userIp             = $this->input->ip_address();
    // $secret             = $this->config->item('secret_key');

    // $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptchaResponse . "&remoteip=" . $userIp;

    // $ch     = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output = curl_exec($ch);
    // curl_close($ch);

    // $status = json_decode($output, true);

    //if ($status['success']) {
      //CEK DATA APAKAH ADA
      $data['username'] = htmlspecialchars($this->input->post('username'));
      $data['password'] = htmlspecialchars($this->input->post('password'));
      $res     = $this->auth->islogin($data);
      $params = array();

      if ($res == 0) {
        $params = array(
          "status_code" => 404,
          "status"       => "not found",
          "message"     => "Username tidak ditemukan!",
          "url"          => null
        );

        echo json_encode($params);
      } elseif ($res == 10) {
        $params = array(
          "status_code" => 400,
          "status"       => "error",
          "message"     => "Username atau password salah!",
          "url"          => null
        );

        echo json_encode($params);
      } elseif ($res == 20) {
        $params = array(
          "status_code" => 401,
          "status"       => "error",
          "message"     => "Username anda di block!",
          "url"          => null
        );

        echo json_encode($params);
      } elseif ($res == 30) {
        $params = array(
          "status_code" => 200,
          "status"       => "success",
          "message"     => "Login sukses",
          "url"          => base_url() . "adminx"
        );

        echo json_encode($params);
      };
    // } else {
    // 	$params = array(
		// 		"status_code" => 400,
		// 		"status" 			=> "error",
		// 		"message" 		=> "Harap centang I'm not a robot!",
		// 		"url"					=> null
		// 	);

    // 	echo json_encode($params);
    // }

    array_push($params, array(
      'username' => $this->input->post('username'),
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
    ));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "LOGIN";
    $log_data   = json_encode($params);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function not_found()
  {
    $login = $this->session->userdata('user_valid');
    //echo $login; exit();
    if ($login == 1 || $login == true) {
      $data['group_halaman']    = "404 Not Found";
      $data['nama_halaman']     = "Page Not Found";
      $data['icon_halaman']     = "icon-home";

      $data['perusahaan'] = $this->perusahaan->get_details();
      // print_r($data['perusahaan']);
      // exit;
      $this->load->view('not_founds_after_login', $data, FALSE);
    } else {
      $data['perusahaan'] = $this->perusahaan->get_details();
      $this->load->view('not_found', $data, FALSE);
    }
  }

  public function not_found_old()
  {
    $data['perusahaan'] = $this->perusahaan->get_details();
    $this->load->view('not_found', $data, FALSE);
  }

  public function jumlah_pesanan()
	{
    $data['nama_halaman'] 		= "Jumlah Pesanan";
    $data['company_profile']  = $this->perusahaan->get_details();
    //print_r($data['company_profile']); exit;
    
    $this->load->view('numbers', $data, FALSE);
	}

  public function get_jumlah_pesanan()
	{
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://dashboard.main-mfg.com/catering/jumlah_pesanan',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=833f96c56b69fd9768a54ecd2b91c2d5a5becd0a'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
	}

  public function slip_gaji()
  {
    $data['perusahaan'] = $this->perusahaan->get_details();
    $this->load->view('slip_gaji2', $data, FALSE);
  }

  public function slip_lembur()
  {
    $data['perusahaan'] = $this->perusahaan->get_details();
    $this->load->view('slip_lembur2', $data, FALSE);
  }
}
