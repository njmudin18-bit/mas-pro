<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Allowance extends CI_Controller
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
    $this->load->model('allowance_model', 'basic');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Tunjangan";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/ms_tunjangan', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function allowance_add()
  {
    $this->_validation_allowance();

    $allowanceType = $this->input->post('AllowanceType');

    $Data = array(
      'AllowanceName'  => ucwords($this->input->post('AllowanceName')),
      'AllowanceType'  => $allowanceType,
      'Amount'         => ($allowanceType === 'FIXED' ? floatval(str_replace(".", "", $this->input->post('Amount'))) : null),
      'Percentage'     => ($allowanceType === 'PERCENTAGE' ? $this->input->post('Percentage') : null),
      'Period'         => $this->input->post('Periode'),
      'EffectiveDate'  => $this->input->post('EffectiveDate'),
      'ApplyTo'        => $this->input->post('ApplyTo'),
      'IsActive'       => $this->input->post('IsActive'),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );
    $insert = $this->basic->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function allowance_list()
  {
    $list = $this->basic->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $basic) {
      $Isi   = "'".$basic->AllowanceID."'";
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
      $row[] = $basic->AllowanceName;
      $row[] = $basic->AllowanceType;
      $row[] = $basic->Amount;
      $row[] = $basic->Percentage;
      $row[] = $basic->ApplyTo;
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

  public function allowance_edit($id)
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

  public function allowance_update()
  {
    $this->_validation_allowance();

    $allowanceType = $this->input->post('AllowanceType');

    $data = array(
      'AllowanceName'  => ucwords($this->input->post('AllowanceName')),
      'AllowanceType'  => $allowanceType,
      'Amount'         => ($allowanceType === 'FIXED' ? floatval(str_replace(".", "", $this->input->post('Amount'))) : null),
      'Percentage'     => ($allowanceType === 'PERCENTAGE' ? $this->input->post('Percentage') : null),
      'Period'         => $this->input->post('Periode'),
      'EffectiveDate'  => $this->input->post('EffectiveDate'),
      'ApplyTo'        => $this->input->post('ApplyTo'),
      'IsActive'       => $this->input->post('IsActive'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    $this->basic->update(array('AllowanceID' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function allowance_deleted($id)
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

  private function _validation_allowance()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('AllowanceName') == '') {
      $data['inputerror'][]   = 'AllowanceName';
      $data['error_string'][] = 'Allowance Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AllowanceType') == '') {
      $data['inputerror'][]   = 'AllowanceType';
      $data['error_string'][] = 'Allowance Type is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AllowanceType') == 'FIXED') {
      if ($this->input->post('Amount') == '') {
        $data['inputerror'][]   = 'Amount';
        $data['error_string'][] = 'Amount is required';
        $data['status']         = FALSE;
      }
    }

    if ($this->input->post('AllowanceType') == 'PERCENTAGE') {
      if ($this->input->post('Percentage') == '') {
        $data['inputerror'][]   = 'Percentage';
        $data['error_string'][] = 'Percentage is required';
        $data['status']         = FALSE;
      }
    }

    if ($this->input->post('Periode') == '') {
      $data['inputerror'][]   = 'Periode';
      $data['error_string'][] = 'Period is required';
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
