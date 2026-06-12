<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_overtime extends CI_Controller
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
    $this->load->model('Ms_overtime_model', 'ot');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Overtime";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/ms_overtime', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function ot_add()
  {
    $this->_validation_ot();

    $Data = array(
      'Periode'           => $this->input->post('Periode'),
      'isActive'          => $this->input->post('isActive'),
      'TipeHari'          => $this->input->post('TipeHari'),
      'UrutanJam'         => ucwords($this->input->post('UrutanJam')),
      'RentangJam'        => strtoupper($this->input->post('RentangJam')),
      'RumusPerhitungan'  => $this->input->post('RumusPerhitungan'),
      'FaktorLembur'      => strtoupper($this->input->post('FaktorLembur')),
      'CreatedDate'       => date('Y-m-d H:i:s'),
      'CreatedBy'         => $this->session->userdata('user_code')
    );
    $insert = $this->ot->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_list()
  {
    $list = $this->ot->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $ot) {
      $Isi   = "'".$ot->OvertimeID."'";
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
      $row[] = $ot->Periode;
      $row[] = $ot->isActive;
      $row[] = $ot->TipeHari;
      $row[] = $ot->UrutanJam;
      $row[] = $ot->RentangJam;
      $row[] = $ot->RumusPerhitungan;
      $row[] = $ot->FaktorLembur;
      $row[] = $ot->CreatedDate;
      $row[] = $ot->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->ot->count_all(),
      "recordsFiltered" => $this->ot->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function ot_edit($id)
  {
    $data = $this->ot->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_update()
  {
    $this->_validation_ot();

    $data = array(
      'Periode'           => $this->input->post('Periode'),
      'isActive'          => $this->input->post('isActive'),
      'TipeHari'          => $this->input->post('TipeHari'),
      'UrutanJam'         => ucwords($this->input->post('UrutanJam')),
      'RentangJam'        => strtoupper($this->input->post('RentangJam')),
      'RumusPerhitungan'  => $this->input->post('RumusPerhitungan'),
      'FaktorLembur'      => strtoupper($this->input->post('FaktorLembur')),
      'UpdatedDate'       => date('Y-m-d H:i:s'),
      'UpdatedBy'         => $this->session->userdata('user_code')
    );

    $this->ot->update(array('OvertimeID' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ot_deleted($id)
  {
    $data_delete    = $this->ot->get_by_id($id); //DATA DELETE
    $data           = $this->ot->delete_by_id($id);

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

    if ($this->input->post('Periode') == '') {
      $data['inputerror'][]   = 'Periode';
      $data['error_string'][] = 'Periode is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TipeHari') == '') {
      $data['inputerror'][]   = 'TipeHari';
      $data['error_string'][] = 'Tipe Lembur is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('UrutanJam') == '') {
      $data['inputerror'][]   = 'UrutanJam';
      $data['error_string'][] = 'Urutan Jam Lembur is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('RentangJam') == '') {
      $data['inputerror'][]   = 'RentangJam';
      $data['error_string'][] = 'Rentang Jam Lembur is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('RumusPerhitungan') == '') {
      $data['inputerror'][]   = 'RumusPerhitungan';
      $data['error_string'][] = 'Formula Lembur is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('FaktorLembur') == '') {
      $data['inputerror'][]   = 'FaktorLembur';
      $data['error_string'][] = 'Faktor Pengali Lembur is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('isActive') == '') {
      $data['inputerror'][]   = 'isActive';
      $data['error_string'][] = 'is Active is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
