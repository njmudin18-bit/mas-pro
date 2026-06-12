<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Periode_gaji extends CI_Controller
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
    $this->load->model('periodegaji_model', 'periodegaji');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Setting Periode Gaji";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      // Log akses
      log_helper(base_url().$this->contoller_name."/".$this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/periode_gaji', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function periodegaji_add()
  {
    $this->_validation_periodegaji();

    $Data = array(
      'StartDate'    => $this->input->post('StartDate'),
      'EndDate'      => $this->input->post('EndDate'),
      'Payday'       => $this->input->post('Payday'),
      'Cycle'        => $this->input->post('Cycle'),
      'CreatedDate'  => date('Y-m-d H:i:s'),
      'CreatedBy'    => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $insert = $this->periodegaji->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function periodegaji_list()
  {
    $StartDate = $this->input->post('start_date');
    $EndDate   = $this->input->post('end_date');

    $list = $this->periodegaji->get_datatables($StartDate, $EndDate);
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $value) {
      $Isi   = "'".$value->Id."'";
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
      $row[] = $value->Payday;
      $row[] = $value->StartDate;
      $row[] = $value->EndDate;
      $row[] = $value->Cycle;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->periodegaji->count_all($StartDate, $EndDate),
      "recordsFiltered" => $this->periodegaji->count_filtered($StartDate, $EndDate),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function periodegaji_edit($id)
  {
    $data = $this->periodegaji->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function periodegaji_update()
  {
    $this->_validation_periodegaji();

    $data = array(
      'StartDate'    => $this->input->post('StartDate'),
      'EndDate'      => $this->input->post('EndDate'),
      'Payday'       => $this->input->post('Payday'),
      'Cycle'        => $this->input->post('Cycle'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->periodegaji->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function periodegaji_deleted($id)
  {
    $data_delete    = $this->periodegaji->get_by_id($id); //DATA DELETE
    $data           = $this->periodegaji->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_periodegaji()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('StartDate') == '') {
      $data['inputerror'][]   = 'StartDate';
      $data['error_string'][] = 'Start Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EndDate') == '') {
      $data['inputerror'][]   = 'EndDate';
      $data['error_string'][] = 'End Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Payday') == '') {
      $data['inputerror'][]   = 'Payday';
      $data['error_string'][] = 'Payday is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Cycle') == '') {
      $data['inputerror'][]   = 'Cycle';
      $data['error_string'][] = 'Cycle is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}