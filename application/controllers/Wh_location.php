<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wh_location extends CI_Controller
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
    $this->function_name  = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('whlocation_model', 'whlocation');
    //$this->load->model('phone_model', 'phone');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "PPIC";
      $data['nama_halaman']     = "WH Location";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/wh_location/index', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function wh_location_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_wh_location();

      $data = array(
        'Type'         => $this->input->post('Type'),
        'WhLocation'   => $this->input->post('WhLocation'),
        'Status'       => $this->input->post('Status'),
        'CreatedDate'  => date('Y-m-d H:i:s'),
        'CreatedBy'    => $this->session->userdata('user_code')
      );
      $insert = $this->whlocation->save($data);
      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "ADD";
      $log_data   = json_encode($data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function wh_location_list()
  {
    $list = $this->whlocation->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    $noUrut = 0;
    foreach ($list as $Datas) {
      $no++;
      $noUrut++;
      $row    = array();
      $row[]  = $no;
      //add html for action
      $row[]  = '<a href="javascript:void(0)" onclick="edit('."'" .$Datas->Id."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$Datas->Id."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
      //$row[]  = $Datas->Type == 'PC' ? 'Power Cord' : 'Wiring';
      $row[]  = $Datas->Type == 'PC' ? 'Power Cord' : ($Datas->Type == 'WR' ? 'Wiring' : 'All');
      $row[]  = $Datas->WhLocation;
      $row[]  = $Datas->Status == 'Tidak' ? '<label class="label label-danger">'.strtoupper($Datas->Status).'</label>' : '<label class="label label-success">'.strtoupper($Datas->Status).'</label>';

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->whlocation->count_all(),
      "recordsFiltered" => $this->whlocation->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function wh_location_edit($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data = $this->whlocation->get_by_id($id);
      echo json_encode($data);

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "EDIT";
      $log_data       = json_encode($data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function wh_location_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_wh_location();

      $data = array(
        'Type'         => $this->input->post('Type'),
        'WhLocation'   => $this->input->post('WhLocation'),
        'Status'       => $this->input->post('Status'),
        'UpdatedDate'  => date('Y-m-d H:i:s'),
        'UpdatedBy'    => $this->session->userdata('user_code')
      );
      $this->whlocation->update(array('Id' => $this->input->post('kode')), $data);
      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "UPDATE";
      $log_data   = json_encode($data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function wh_location_deleted($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data_delete    = $this->whlocation->get_by_id($id); //DATA DELETE
      $data           = $this->whlocation->delete_by_id($id);

      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "DELETE";
      $log_data       = json_encode($data_delete);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  private function _validation_wh_location()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Type') == '') {
      $data['inputerror'][]   = 'Type';
      $data['error_string'][] = 'Type is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('WhLocation') == '') {
      $data['inputerror'][]   = 'WhLocation';
      $data['error_string'][] = 'Wh Location is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Status') == '') {
      $data['inputerror'][]   = 'Status';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
