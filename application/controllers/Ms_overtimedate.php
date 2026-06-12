<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ms_overtimedate extends CI_Controller
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
    $this->load->model('overtimedate_model', 'tanggal');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Setting Tanggal Lembur";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      // Log akses
      log_helper(base_url().$this->contoller_name."/".$this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/ms_overtime_date', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function ot_date_add()
  {
    $this->_validation_ot();

    $Data = array(
      'OvertimeDate'   => $this->input->post('OvertimeDate'),
      'OvertimeName'   => ucwords($this->input->post('OvertimeName')),
      'Amount'         => floatval(str_replace(".", "", $this->input->post('Amount'))),
      'IsActive'       => $this->input->post('IsActive'),
      'Notes'          => ucfirst($this->input->post('Notes')),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $insert = $this->tanggal->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_date_list()
  {
    $StartDate = $this->input->post('start_date');
    $EndDate   = $this->input->post('end_date');

    $list = $this->tanggal->get_datatables($StartDate, $EndDate);
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
      $row[] = $value->OvertimeDate;
      $row[] = $value->OvertimeName;
      $row[] = $value->Amount;
      $row[] = $value->Notes;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->tanggal->count_all($StartDate, $EndDate),
      "recordsFiltered" => $this->tanggal->count_filtered($StartDate, $EndDate),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function ot_date_edit($id)
  {
    $data = $this->tanggal->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_date_update()
  {
    $this->_validation_ot();

    $data = array(
      'OvertimeDate'   => $this->input->post('OvertimeDate'),
      'OvertimeName'   => ucwords($this->input->post('OvertimeName')),
      'Amount'         => floatval(str_replace(".", "", $this->input->post('Amount'))),
      'IsActive'       => $this->input->post('IsActive'),
      'Notes'          => ucfirst($this->input->post('Notes')),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->tanggal->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_date_deleted($id)
  {
    $data_delete    = $this->tanggal->get_by_id($id); //DATA DELETE
    $data           = $this->tanggal->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_ot()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('OvertimeDate') == '') {
      $data['inputerror'][]   = 'OvertimeDate';
      $data['error_string'][] = 'Overtime Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('OvertimeName') == '') {
      $data['inputerror'][]   = 'OvertimeName';
      $data['error_string'][] = 'Overtime Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Amount') == '') {
      $data['inputerror'][]   = 'Amount';
      $data['error_string'][] = 'Amount is required';
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