<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Minggu_masuk extends CI_Controller
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
    $this->load->model('Minggumasuk_model', 'minggumasuk'); // Ganti: Gunakan model harilibur_model, tapi tetap sebagai $this->holidays jika ingin pertahankan nama
  }

  // =========================
  // HALAMAN UTAMA: Daftar Hari Libur
  // =========================
  public function index()
  {
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Minggu Masuk";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/minggu_masuk', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  // =========================
  // TAMBAH DATA HARI LIBUR
  // =========================
  public function minggu_masuk_add()
  {
    $this->_minggu_masuk_validation();

    $data = array(
      'Tanggal'     => $this->input->post('Tanggal'),
      'IsActive'    => $this->input->post('IsActive'),
      'Noted'       => $this->input->post('Noted'),
      'CreatedBy'   => $this->session->userdata('user_code'),
      'CreatedDate' => date('Y-m-d H:i:s')
    );


    $this->minggumasuk->save($data);

    // Log
    log_helper(base_url() . $this->contoller_name . "/minggu_masuk_add", "ADD", json_encode($data));

    echo json_encode(array("status" => "ok", "message" => "Data berhasil disimpan"));
  }

  // =========================
  // DATATABLES: Ambil data
  // =========================
  public function minggu_masuk_list()
  {
    $list = $this->minggumasuk->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $basic) {
      $Isi   = "'".$basic->Id."'";
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
      $row[] = $basic->Tanggal;
      $row[] = $basic->IsActive == 'A' ? 'AKTIF' : 'NON AKTIF';
      $row[] = $basic->Noted;
      $row[] = $basic->CreatedDate;
      $row[] = $basic->CreatedBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->minggumasuk->count_all(),
      "recordsFiltered" => $this->minggumasuk->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  // =========================
  // EDIT: Ambil data by ID
  // =========================
  public function minggu_masuk_edit($id)
  {
    $data = $this->minggumasuk->get_by_id($id);

    // Log
    log_helper(base_url() . $this->contoller_name . "/minggu_masuk_edit", "EDIT", json_encode($data));

    echo json_encode($data);
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function minggu_masuk_update()
  {
    $this->_minggu_masuk_validation();

    $id = $this->input->post('kode');
    $data = array(
      'Tanggal'     => $this->input->post('Tanggal'),
      'IsActive'    => $this->input->post('IsActive'),
      'Noted'       => $this->input->post('Noted'),
      'UpdatedBy'   => $this->session->userdata('user_code'),
      'UpdatedDate' => date('Y-m-d H:i:s')
    );
    $this->minggumasuk->update($id, $data);

    // Log
    log_helper(base_url() . $this->contoller_name . "/minggu_masuk_update", "UPDATE", json_encode($data));

    echo json_encode(array("status" => "ok", "msg" => "Data berhasil diperbarui"));
  }

  // =========================
  // HAPUS DATA
  // =========================
  public function minggu_masuk_deleted($id)
  {
    $data_delete = $this->minggumasuk->get_by_id($id);
    $this->minggumasuk->delete_by_id($id);

    // Log
    log_helper(base_url() . $this->contoller_name . "/minggu_masuk_deleted", "DELETE", json_encode($data_delete));

    echo json_encode(array("status" =>true, "msg" => "Data berhasil dihapus"));
  }

  // =========================
  // VALIDASI INPUT HARI LIBUR
  // =========================
  private function _minggu_masuk_validation()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = true;

    if (!$this->input->post('Tanggal')) {
      $data['inputerror'][] = 'Tanggal';
      $data['error_string'][] = 'Tanggal wajib diisi';
      $data['status'] = false;
    }

    if (!$this->input->post('Noted')) {
      $data['inputerror'][] = 'Noted';
      $data['error_string'][] = 'Noted wajib diisi';
      $data['status'] = false;
    }

    if (!$this->input->post('IsActive')) {
      $data['inputerror'][] = 'IsActive';
      $data['error_string'][] = 'Status wajib diisi';
      $data['status'] = false;
    }

    if ($data['status'] === false) {
      echo json_encode($data);
      exit();
    }
  }
}
