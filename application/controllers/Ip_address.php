<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ip_address extends CI_Controller
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
    $this->load->model('ip_address_model', 'ip_address');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "GA";
      $data['nama_halaman']     = "Daftar IP Address";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      //$data['department_att']   = get_department_att();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/it/ip', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function ip_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {

      $this->_validation_ip();

      $data = array(
        'dept_id'         => $this->input->post('dept_id'),
        'dept_name'       => $this->input->post('dept_name'),
        'nip'             => $this->input->post('nip'),
        'nama_pegawai'    => $this->input->post('nama_pegawai'),
        'ip_address'      => $this->input->post('ip_address'),
        'computer_name'   => $this->input->post('computer_name'),
        'remote_id'       => $this->input->post('remote_id'),
        'aktivasi'        => $this->input->post('aktivasi'),
        'insert_date'     => date('Y-m-d H:i:s'),
        'insert_by'       => $this->session->userdata('user_code')
      );
      $insert = $this->ip_address->save($data);
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

  public function ip_list()
  {
    $list = $this->ip_address->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    $noUrut = 0;
    foreach ($list as $ip_address) {
      $no++;
      $noUrut++;
      $row = array();
      $row[] = $no;
      //add html for action
      $row[] = '<a href="javascript:void(0)" onclick="edit(' . "'" . $ip_address->id . "'" . ')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $ip_address->id . "'" . ')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
      $row[] = $ip_address->dept_name . " - " . $ip_address->dept_id;
      $row[] = $ip_address->aktivasi == 'Tidak' ? '<label class="label label-danger">' . strtoupper($ip_address->aktivasi) . '</label>' : '<label class="label label-success">' . strtoupper($ip_address->aktivasi) . '</label>';
      $row[] = $ip_address->nama_pegawai;
      $row[] = $ip_address->ip_address;
      $row[] = $ip_address->computer_name;
      $row[] = $ip_address->remote_id;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->ip_address->count_all(),
      "recordsFiltered" => $this->ip_address->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function ip_edit($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data = $this->ip_address->get_by_id($id);
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

  public function ip_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {

      $this->_validation_ip();

      $data = array(
        'dept_id'         => $this->input->post('dept_id'),
        'dept_name'       => $this->input->post('dept_name'),
        'nip'             => $this->input->post('nip'),
        'nama_pegawai'    => $this->input->post('nama_pegawai'),
        'ip_address'      => $this->input->post('ip_address'),
        'computer_name'   => $this->input->post('computer_name'),
        'remote_id'       => $this->input->post('remote_id'),
        'aktivasi'        => $this->input->post('aktivasi'),
        'update_date'     => date('Y-m-d H:i:s'),
        'update_by'       => $this->session->userdata('user_code')
      );
      $this->ip_address->update(array('id' => $this->input->post('kode')), $data);
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

  public function ip_deleted($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data_delete    = $this->ip_address->get_by_id($id); //DATA DELETE
      $data           = $this->ip_address->delete_by_id($id);

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

  private function _validation_ip()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('custom') == 'M') {
      if ($this->input->post('dept_id') == '') {
        $data['inputerror'][] = 'dept_id';
        $data['error_string'][] = 'Department is required';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('ip_address') == '') {
      $data['inputerror'][] = 'ip_address';
      $data['error_string'][] = 'IP Address is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('computer_name') == '') {
      $data['inputerror'][] = 'computer_name';
      $data['error_string'][] = 'Computer Name is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('aktivasi') == '') {
      $data['inputerror'][] = 'aktivasi';
      $data['error_string'][] = 'Aktivasi is required';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
