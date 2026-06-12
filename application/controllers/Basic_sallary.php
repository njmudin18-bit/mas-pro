<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Basic_sallary extends CI_Controller
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
    $this->load->model('basic_sallary_model', 'basic');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Gaji Pokok";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/ms_gapok', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function basic_sallary_add()
  {
    $this->_validation_basic();
    $BasicSalary    = floatval(str_replace(".", "", $this->input->post('BasicSalary')));
    $SalaryDivider  = floatval($this->input->post('SalaryDivider'));
    $DailySalary    = $BasicSalary / $SalaryDivider;

    $Data = array(
      'JobTitle'       => ucwords($this->input->post('JobTitle')),
      'Grade'          => strtoupper($this->input->post('Grade')),
      'JobLevel'       => ucfirst($this->input->post('JobLevel')),
      'BasicSalary'    => $BasicSalary,
      'SalaryDivider'  => $SalaryDivider,
      'DailySalary'    => $DailySalary,
      'Period'         => $this->input->post('Periode'),
      'EffectiveDate'  => $this->input->post('EffectiveDate'),
      'IsActive'       => $this->input->post('IsActive'),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $insert = $this->basic->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function basic_sallary_list()
  {
    $list = $this->basic->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $basic) {
      $Isi   = "'".$basic->BasicSalaryID."'";
      $no++;
      $row   = array();
      $row[] = $no;
      //add html for action
      $row[] = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>';
      $row[] = $basic->Period;
      $row[] = $basic->IsActive;
      $row[] = $basic->JobLevel;
      $row[] = $basic->JobTitle;
      $row[] = $basic->Grade;
      $row[] = $basic->BasicSalary;
      $row[] = $basic->SalaryDivider;
      $row[] = $basic->DailySalary;
      $row[] = $basic->EffectiveDate;
      $row[] = $basic->CreatedDate;
      $row[] = $basic->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->basic->count_all(),
      "recordsFiltered" => $this->basic->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function basic_sallary_edit($id)
  {
    $data = $this->basic->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function basic_sallary_update()
  {
    $this->_validation_basic();
    $BasicSalary    = floatval(str_replace(".", "", $this->input->post('BasicSalary')));
    $SalaryDivider  = floatval($this->input->post('SalaryDivider'));
    $DailySalary    = $BasicSalary / $SalaryDivider;

    $data = array(
      'JobTitle'       => ucwords($this->input->post('JobTitle')),
      'Grade'          => strtoupper($this->input->post('Grade')),
      'JobLevel'       => ucfirst($this->input->post('JobLevel')),
      'BasicSalary'    => $BasicSalary,
      'SalaryDivider'  => $SalaryDivider,
      'DailySalary'    => ceil($DailySalary),
      'Period'         => $this->input->post('Periode'),
      'EffectiveDate'  => $this->input->post('EffectiveDate'),
      'IsActive'       => $this->input->post('IsActive'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->basic->update(array('BasicSalaryID' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function basic_sallary_deleted($id)
  {
    $data_delete    = $this->basic->get_by_id($id); //DATA DELETE
    $data           = $this->basic->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_basic()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('JobTitle') == '') {
      $data['inputerror'][]   = 'JobTitle';
      $data['error_string'][] = 'Job Title is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Grade') == '') {
      $data['inputerror'][]   = 'Grade';
      $data['error_string'][] = 'Grade is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JobLevel') == '') {
      $data['inputerror'][]   = 'JobLevel';
      $data['error_string'][] = 'Job Level is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('BasicSalary') == '') {
      $data['inputerror'][]   = 'BasicSalary';
      $data['error_string'][] = 'Basic Salary is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Periode') == '') {
      $data['inputerror'][]   = 'Periode';
      $data['error_string'][] = 'Periode is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EffectiveDate') == '') {
      $data['inputerror'][]   = 'EffectiveDate';
      $data['error_string'][] = 'Effective Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IsActive') == '') {
      $data['inputerror'][]   = 'IsActive';
      $data['error_string'][] = 'is Active is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
