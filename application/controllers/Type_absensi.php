<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Type_absensi extends CI_Controller
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
    $this->load->model('typeabsensi_model', 'type');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Daftar Type Absensi";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/master_data/type_absensi', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function type_add()
  {
    $this->_validation_type();

    $Data = array(
      'AbsenceCode'   => strtoupper($this->input->post('AbsenceCode')),
      'AbsenceName'   => ucwords($this->input->post('AbsenceName')),
      'Description'   => ucfirst($this->input->post('Description')),
      'CreatedDate'   => date('Y-m-d H:i:s'),
      'CreatedBy'     => $this->session->userdata('user_code')
    );
    $insert = $this->type->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "ADD";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function type_list()
  {
    $list = $this->type->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $type) {
      $Isi   = "'".$type->Id."'";
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
      $row[] = $type->AbsenceCode;
      $row[] = $type->AbsenceName;
      $row[] = $type->Description;
      $row[] = $type->CreatedDate;
      $row[] = $type->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->type->count_all(),
      "recordsFiltered" => $this->type->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function type_edit($id)
  {
    $data = $this->type->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function type_update()
  {
    $this->_validation_type();

    $data = array(
      'AbsenceCode'   => strtoupper($this->input->post('AbsenceCode')),
      'AbsenceName'   => ucwords($this->input->post('AbsenceName')),
      'Description'   => ucfirst($this->input->post('Description')),
      'UpdatedDate'   => date('Y-m-d H:i:s'),
      'UpdatedBy'     => $this->session->userdata('user_code')
    );

    $this->type->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function type_deleted($id)
  {
    $data_delete    = $this->type->get_by_id($id); //DATA DELETE
    $data           = $this->type->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_type()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('AbsenceCode') == '') {
      $data['inputerror'][]   = 'AbsenceCode';
      $data['error_string'][] = 'Kode Absensi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AbsenceName') == '') {
      $data['inputerror'][]   = 'AbsenceName';
      $data['error_string'][] = 'Nama Absensi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Description') == '') {
      $data['inputerror'][]   = 'Description';
      $data['error_string'][] = 'Deskripsi is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
