<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hari_libur extends CI_Controller
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
    $this->load->model('harilibur_model', 'holidays'); // Ganti: Gunakan model harilibur_model, tapi tetap sebagai $this->holidays jika ingin pertahankan nama
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
      $data['nama_halaman']   = "Master Hari Libur";
      $data['icon_halaman']   = "icon-calendar";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/hari_libur', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  // =========================
  // TAMBAH DATA HARI LIBUR
  // =========================
  public function holidays_add()
  {
    $this->_validation_holiday();

    $data = array(
      'HolidayDate' => $this->input->post('tanggal'),
      'HolidayName' => $this->input->post('deskripsi'),
      'HolidayType' => $this->input->post('type'),
      'IsNational'  => ($this->input->post('nationalDays') === 'Yes') ? 1 : 0,
      'Notes'       => $this->input->post('keterangan'),
      'CreatedBy'   => $this->session->userdata('user_code'),
      'CreatedDate' => date('Y-m-d H:i:s')
    );


    $this->holidays->save($data);

    // Log
    log_helper(base_url() . $this->contoller_name . "/holidays_add", "ADD", json_encode($data));

    echo json_encode(array("status" => true, "msg" => "Data berhasil disimpan"));
  }

  // =========================
  // DATATABLES: Ambil data
  // =========================
  public function holidays_list()
  {
    $list = $this->holidays->get_datatables();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $holiday) {
      $no++;
      $row = array(
        "No"          => $no,
        "HolidayID"   => $holiday->HolidayID,
        "HolidayDate" => $holiday->HolidayDate,
        "HolidayName" => $holiday->HolidayName,
        "HolidayType" => $holiday->HolidayType,
        "IsNational"  => $holiday->IsNational,
        "Notes"       => $holiday->Notes,
        "CreatedDate" => $holiday->CreatedDate,
        "CreatedBy"   => $holiday->CreatedBy,
        "Button"      => ' <div class="btn-group" id="Button_' . $holiday->HolidayID . '" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit(' . $holiday->HolidayID . ')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete(' . $holiday->HolidayID . ')">Hapus</a>
                    </div>
                  </div>
                </div>'
      );
      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->holidays->count_all(),
      "recordsFiltered" => $this->holidays->count_filtered(),
      "data"            => $data,
    );

    echo json_encode($output);
  }

  // =========================
  // EDIT: Ambil data by ID
  // =========================
  public function holidays_edit($id)
  {
    $data = $this->holidays->get_by_id($id);

    // Log
    log_helper(base_url() . $this->contoller_name . "/holidays_edit", "EDIT", json_encode($data));

    echo json_encode($data);
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function holidays_update()
  {
    $this->_validation_holiday();

    $id = $this->input->post('HolidayID');
    $data = array(
      'HolidayDate' => $this->input->post('tanggal'),
      'HolidayName' => $this->input->post('deskripsi'),
      'HolidayType' => $this->input->post('type'),
      'IsNational'  => ($this->input->post('nationalDays') === 'Yes') ? 1 : 0,
      'Notes'       => $this->input->post('keterangan'),
      'UpdatedBy'     => $this->session->userdata('user_code'),
      'UpdatedDate'   => date('Y-m-d H:i:s')
    );

    $this->holidays->update($id, $data);

    // Log
    log_helper(base_url() . $this->contoller_name . "/holidays_update", "UPDATE", json_encode($data));

    echo json_encode(array("status" => true, "msg" => "Data berhasil diperbarui"));
  }

  // =========================
  // HAPUS DATA
  // =========================
  public function holidays_deleted($id)
  {
    $data_delete = $this->holidays->get_by_id($id);
    $this->holidays->delete_by_id($id);

    // Log
    log_helper(base_url() . $this->contoller_name . "/holidays_deleted", "DELETE", json_encode($data_delete));

    echo json_encode(array("status" => true, "msg" => "Data berhasil dihapus"));
  }

  // =========================
  // VALIDASI INPUT HARI LIBUR
  // =========================
  private function _validation_holiday()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = true;

    if (!$this->input->post('tanggal')) {
      $data['inputerror'][] = 'tanggal';
      $data['error_string'][] = 'Tanggal wajib diisi';
      $data['status'] = false;
    }

    if (!$this->input->post('deskripsi')) {
      $data['inputerror'][] = 'deskripsi';
      $data['error_string'][] = 'Deskripsi wajib diisi';
      $data['status'] = false;
    }

    // if (!$this->input->post('type')) {
    //   $data['inputerror'][] = 'type';
    //   $data['error_string'][] = 'Type wajib diisi';
    //   $data['status'] = false;
    // }

    if (!$this->input->post('nationalDays')) {
      $data['inputerror'][] = 'nationalDays';
      $data['error_string'][] = 'National Days wajib diisi';
      $data['status'] = false;
    }

    // if (!$this->input->post('keterangan')) {
    //   $data['inputerror'][] = 'keterangan';
    //   $data['error_string'][] = 'Keterangan wajib diisi';
    //   $data['status'] = false;
    // }

    if ($data['status'] === false) {
      echo json_encode($data);
      exit();
    }
  }
}
