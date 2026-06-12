<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemakaian_air extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if ($this->auth->isNotLogin()) redirect('auth/login');

    // START ROLE MANAGEMENT
    $this->contoller_name = $this->router->class;
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    // END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('pemakaian_air_model', 'pemakaian');

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "EHS";
      $data['nama_halaman']   = "Pemakaian Air";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/ehs/pemakaian_air', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function pemakaian_air_add()
  {
    $this->_validation_pemakaian();
    $AngkaPagi    = str_replace(".", "", $this->input->post('AngkaPagi'));
    $AngkaMalam   = str_replace(".", "", $this->input->post('AngkaMalam'));
    $Volume       = str_replace(".", "", $this->input->post('Volume'));

    $Data = array(
      'Date'           => $this->input->post('Date'),
      'AngkaPagi'      => floatval(str_replace(",", ".", $AngkaPagi)),
      'AngkaMalam'     => floatval(str_replace(",", ".", $AngkaMalam)),
      'Volume'         => floatval(str_replace(",", ".", $Volume)),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $insert = $this->pemakaian->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function pemakaian_air_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');

    $Sql            = "EXEC dbo.GetPemakaianAir @StartDate = ?, @EndDate = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi   = "'".$value->Id."'";
      $row   = [];
      $row[] = $No++;
      $row[] = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>';
      $row[] = $value->Hari;
      $row[] = $value->Date;
      $row[] = $value->AngkaPagi;
      $row[] = $value->AngkaMalam;
      $row[] = $value->Volume;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function summary_pemakaian_air_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$Tahun          = $this->input->post('tahun');

    $Sql            = "EXEC dbo.GetSummaryBulananPemakaianAir @Tahun = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$Tahun]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $row   = [];
      $row[] = $No++;
      $row[] = $value->TAHUN;
      $row[] = $value->BULAN;
      $row[] = $value->TOTAL_PAKAI;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function pemakaian_air_edit($id)
  {
    $data = $this->pemakaian->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function pemakaian_air_update()
  {
    $this->_validation_pemakaian();
    $AngkaPagi    = str_replace(".", "", $this->input->post('AngkaPagi'));
    $AngkaMalam   = str_replace(".", "", $this->input->post('AngkaMalam'));
    $Volume       = str_replace(".", "", $this->input->post('Volume'));

    $Data = array(
      'Date'           => $this->input->post('Date'),
      'AngkaPagi'      => floatval(str_replace(",", ".", $AngkaPagi)),
      'AngkaMalam'     => floatval(str_replace(",", ".", $AngkaMalam)),
      'Volume'         => floatval(str_replace(",", ".", $Volume)),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $this->pemakaian->update(array('Id' => $this->input->post('kode')), $Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function pemakaian_air_deleted($id)
  {
    $data_delete    = $this->pemakaian->get_by_id($id); //DATA DELETE
    $data           = $this->pemakaian->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_pemakaian()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Date') == '') {
      $data['inputerror'][]   = 'Date';
      $data['error_string'][] = 'Tanggal is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AngkaPagi') == '') {
      $data['inputerror'][]   = 'AngkaPagi';
      $data['error_string'][] = 'Angka Pagi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AngkaMalam') == '') {
      $data['inputerror'][]   = 'AngkaMalam';
      $data['error_string'][] = 'Angka Malam is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}