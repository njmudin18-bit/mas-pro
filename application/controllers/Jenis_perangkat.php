<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jenis_perangkat extends CI_Controller
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
   * @see https://codeigniter.com/user_guide/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if ($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
    $this->contoller_name = $this->router->class;
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('jenisperangkat_model', 'jenis');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Daftar Jenis Perangkat";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/master_data/jenis_perangkat', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function jp_add()
  {
    $this->_validation_jp();

    $Data = array(
      'Nama'        => ucfirst($this->input->post('Nama')),
      'Status'      => $this->input->post('Status'),
      'Kategori'    => $this->input->post('Kategori'),
      'Deskripsi'   => ucfirst($this->input->post('Deskripsi')),
      'CreateDate'  => date('Y-m-d H:i:s'),
      'CreateBy'    => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "Data" => $Data)); exit;
    $insert = $this->jenis->save($Data);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "ADD";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function jp_list_OLD()
	{
		$Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));

    $Sql         = "SELECT 
                      Id, Nama, Deskripsi, Status, Kategori,
                      CASE 
                        WHEN UPPER(Status) = 'AKTIF' THEN 'badge-success'
                        WHEN UPPER(Status) = 'TIDAK' THEN 'badge-danger'
                        ELSE 'badge-secondary'
                      END AS StatusClass,
                      CONVERT(VARCHAR(19), CreateDate, 120) AS CreateDate, CreateBy 
                    FROM Ms_JenisPerangkat
                    ORDER BY CreateDate DESC";
    $Query       = $this->BJGMAS01->query($Sql);
    $Result      = $Query->result();
    $Total       = count($Result);
    $Paged       = array_slice($Result, $Start, $Length);

    $Data        = [];
    $No          = $Start + 1;
    foreach ($Paged as $key => $Res) {
      $Isi    = "'".$Res->Id."'";
      $Row    = array();
      $Row[]  = $No++;
      $Row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>';
      $Row[]  = '<span class="badge badge-pill '.$Res->StatusClass.'">'.$Res->Status.'</span>';
      $Row[]  = $Res->Nama;
      $Row[]  = $Res->Kategori;
      $Row[]  = $Res->Deskripsi;
      $Row[]  = $Res->CreateDate;
      $Row[]  = $Res->CreateBy;

      $Data[] = $Row;
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $Data
    ]);
    exit();
	}

  public function jp_list()
	{
		$draw 			 = intval($this->input->get("draw"));
		$start 			 = intval($this->input->get("start"));
		$length 		 = intval($this->input->get("length"));
    //CONVERT(VARCHAR(19), a.CreateDate, 120) AS CreateDate,
    $Sql         = "SELECT 
                      Id, Nama, Deskripsi, Status, Kategori,
                      CASE 
                        WHEN UPPER(Status) = 'AKTIF' THEN 'badge-success'
                        WHEN UPPER(Status) = 'TIDAK' THEN 'badge-danger'
                        ELSE 'badge-secondary'
                      END AS StatusClass,
                      CONVERT(VARCHAR(19), CreateDate, 120) AS CreateDate, CreateBy 
                    FROM Ms_JenisPerangkat
                    ORDER BY CreateDate DESC";
    $Query       = $this->BJGMAS01->query($Sql);
    $Result      = $Query->result();
		$Data        = [];
		$No 		     = 1;

    foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Id."'";
			$Data[] = array(
				$No++,
				'<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
            </div>
          </div>
        </div>',
        '<span class="badge badge-pill '.$value->StatusClass.'">'.$value->Status.'</span>',
        $value->Nama,
        $value->Kategori,
        $value->Deskripsi,
        $value->CreateDate,
        $value->CreateBy,
        $value->CreateDate,
        $value->CreateBy
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($result);
		exit();
	}

  public function jp_edit($id)
  {
    $data = $this->jenis->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function jp_update()
  {
    $this->_validation_jp();

    $Data = array(
      'Nama'        => ucfirst($this->input->post('Nama')),
      'Status'      => $this->input->post('Status'),
      'Kategori'    => $this->input->post('Kategori'),
      'Deskripsi'   => ucfirst($this->input->post('Deskripsi')),
      'UpdateDate'  => date('Y-m-d H:i:s'),
      'UpdateBy'    => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "Data" => $Data)); exit;

    $this->jenis->update(array('Id' => $this->input->post('kode')), $Data);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function jp_deleted($id)
  {
    $data_delete    = $this->jenis->get_by_id($id); //DATA DELETE
    $data           = $this->jenis->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_jp()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Nama') == '') {
      $data['inputerror'][]   = 'Nama';
      $data['error_string'][] = 'Nama Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Status') == '') {
      $data['inputerror'][]   = 'Status';
      $data['error_string'][] = 'Status Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Kategori') == '') {
      $data['inputerror'][]   = 'Kategori';
      $data['error_string'][] = 'Kategori Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}