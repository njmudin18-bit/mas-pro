<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machines extends CI_Controller
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
    $this->load->model('machines_model', 'machines');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Mesin Produksi";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan'] 		= $this->perusahaan->get_details();
      $data['DeptList']       = get_department_att();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/ms_machines', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function machine_add()
  {
    $this->_validation_machine();

    $Data = array(
      'DeptID'         => $this->input->post('DeptID'),
      'Name'           => strtoupper($this->input->post('Name')),
      'IsActive'       => $this->input->post('IsActive'),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $insert = $this->machines->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function machine_list()
  {
    $list = $this->machines->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $machine) {
      $Isi   = "'".$machine->Id."'";
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
      $row[] = $machine->DeptName;
      $row[] = $machine->Name;
      $row[] = $machine->IsActive;
      $row[] = $machine->CreatedDate;
      $row[] = $machine->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->machines->count_all(),
      "recordsFiltered" => $this->machines->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function machine_edit($id)
  {
    $data = $this->machines->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function machine_update()
  {
    $this->_validation_machine();

    $Data = array(
      'DeptID'         => ucwords($this->input->post('DeptID')),
      'Name'           => strtoupper($this->input->post('Name')),
      'IsActive'       => $this->input->post('IsActive'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->machines->update(array('Id' => $this->input->post('kode')), $Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function machine_deleted($id)
  {
    $data_delete    = $this->machines->get_by_id($id); //DATA DELETE
    $data           = $this->machines->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_machine()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Department is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IsActive') == '') {
      $data['inputerror'][]   = 'IsActive';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Name') == '') {
      $data['inputerror'][]   = 'Name';
      $data['error_string'][] = 'Nama Mesin is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
