<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uang_makan extends CI_Controller
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
    $this->load->model('uangmakan_model', 'uang');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Setting Tanggal Uang Makan";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      // Log akses
      log_helper(base_url().$this->contoller_name."/".$this->function_name, "VIEW", "");

      $this->load->view('adminx/pga/uang_makan', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function uang_makan_add()
  {
    $this->_validation_uang_makan();

    $Data = array(
      'Date'           => $this->input->post('Date'),
      'Notes'          => ucwords($this->input->post('Notes')),
      'IsActive'       => $this->input->post('IsActive'),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;
    $insert = $this->uang->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function uang_makan_list()
  {
    $StartDate = $this->input->post('start_date');
    $EndDate   = $this->input->post('end_date');

    $list = $this->uang->get_datatables($StartDate, $EndDate);
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
      $row[] = $value->IsActive;
      $row[] = $value->Date;
      $row[] = $value->Notes;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->uang->count_all($StartDate, $EndDate),
      "recordsFiltered" => $this->uang->count_filtered($StartDate, $EndDate),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function uang_makan_edit($id)
  {
    $data = $this->uang->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function uang_makan_update()
  {
    $this->_validation_uang_makan();

    $data = array(
      'Date'           => $this->input->post('Date'),
      'Notes'          => ucwords($this->input->post('Notes')),
      'IsActive'       => $this->input->post('IsActive'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->uang->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function uang_makan_deleted($id)
  {
    $data_delete    = $this->uang->get_by_id($id); //DATA DELETE
    $data           = $this->uang->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_uang_makan()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Date') == '') {
      $data['inputerror'][]   = 'Date';
      $data['error_string'][] = 'Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Notes') == '') {
      $data['inputerror'][]   = 'Notes';
      $data['error_string'][] = 'Notes is required';
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