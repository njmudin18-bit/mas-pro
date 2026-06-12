<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendor_it extends CI_Controller
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
    $this->load->model('vendorit_model', 'vendor');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Daftar Vendor IT";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/master_data/vendor_it', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function vendor_add()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_vendor();

      $Data = array(
        'VendorName'     => $this->input->post('VendorName'),
        'ContactName'    => $this->input->post('ContactName'),
        'Phone'          => $this->input->post('Phone'),
        'Email'          => $this->input->post('Email'),
        'Website'        => $this->input->post('Website'),
        'Address'        => $this->input->post('Address'),
        'CreateDate'     => date('Y-m-d H:i:s'),
        'CreateBy'       => $this->session->userdata('user_code')
      );
      $insert = $this->vendor->save($Data);
      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "ADD";
      $log_data   = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function vendor_list()
  {
    $list = $this->vendor->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $vendor) {
      $Isi   = "'".$vendor->Id."'";
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
      $row[] = $vendor->VendorName;
      $row[] = $vendor->ContactName;
      $row[] = $vendor->Phone;
      $row[] = $vendor->Email;
      $row[] = $vendor->Website;
      $row[] = $vendor->Address;
      $row[] = $vendor->CreateDate;
      $row[] = $vendor->CreateBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->vendor->count_all(),
      "recordsFiltered" => $this->vendor->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function vendor_edit($id)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {

      $data = $this->vendor->get_by_id($id);
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

  public function vendor_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_vendor();

      $Data = array(
        'VendorName'     => ucwords($this->input->post('VendorName')),
        'ContactName'    => ucwords($this->input->post('ContactName')),
        'Phone'          => $this->input->post('Phone'),
        'Email'          => $this->input->post('Email'),
        'Website'        => strtolower($this->input->post('Website')),
        'Address'        => $this->input->post('Address'),
        'UpdateDate'     => date('Y-m-d H:i:s'),
        'UpdateBy'       => $this->session->userdata('user_code')
      );

      $this->vendor->update(array('Id' => $this->input->post('kode')), $Data);
      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "UPDATE";
      $log_data   = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function vendor_deleted($id)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data_delete    = $this->vendor->get_by_id($id); //DATA DELETE
      $data           = $this->vendor->delete_by_id($id);

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

  private function _validation_vendor()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('VendorName') == '') {
      $data['inputerror'][]   = 'VendorName';
      $data['error_string'][] = 'Vendor Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ContactName') == '') {
      $data['inputerror'][]   = 'ContactName';
      $data['error_string'][] = 'Contact Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Phone') == '') {
      $data['inputerror'][]   = 'Phone';
      $data['error_string'][] = 'Phone is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Email') == '') {
      $data['inputerror'][]   = 'Email';
      $data['error_string'][] = 'Email is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Website') == '') {
      $data['inputerror'][]   = 'Website';
      $data['error_string'][] = 'Website is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
