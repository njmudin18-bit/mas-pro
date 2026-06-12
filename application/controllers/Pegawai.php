<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
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
    $this->load->model('pegawai_model', 'pegawai');
    $this->load->model('shiftsetting_model', 'shift');
    $this->load->model('basic_sallary_model', 'gapok');
    $this->load->model('setting_tunjangan_model', 'tunjangan');

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Personalia & GA";
      $data['nama_halaman']   = "Daftar Pegawai";
      $data['icon_halaman']   = "icon-calendar";
      $data['DeptList']       = get_department_att();
      $data['GapokList']      = $this->gapok->get_all_data();
      $data['shiftList']      = $this->shift->get_jadwal_shift();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      $data['TunjanganList']  = $this->tunjangan->get_group_all();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/pga/ms_pegawai', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function pegawai_list()
  {
    $Draw               = intval($this->input->get("draw"));
    $start              = intval($this->input->get("start"));
    $length             = intval($this->input->get("length"));

    $PeriodePenggajian  = $this->input->post('PeriodePenggajian');
    $StatusPekerja      = $this->input->post('StatusPekerja');
    $Status             = $this->input->post('Status');
    $DeptShow           = $this->input->post('DeptShow');
    
    // Inisialisasi parameter SQL
    $DeptIDs            = NULL;
    $PeriodeGaji        = NULL;
    $StatusKerja        = NULL;
    $StatusParam        = empty($Status) ? NULL : $Status;

    if (!empty($DeptShow) && is_array($DeptShow) && !in_array('ALL', array_map('strtoupper', $DeptShow))) {
      $DeptIDs = implode(",", array_map('trim', $DeptShow));
    }

    if (!empty($StatusPekerja) && is_array($StatusPekerja) && !in_array('ALL', array_map('strtoupper', $StatusPekerja))) {
      $StatusKerja = implode(",", array_map('trim', $StatusPekerja));
    }

    if (!empty($PeriodePenggajian) && is_array($PeriodePenggajian) && !in_array('ALL', array_map('strtoupper', $PeriodePenggajian))) {
      $PeriodeGaji = implode(",", array_map('trim', $PeriodePenggajian));
    }
    
    // Pastikan NULL dikirim jika string kosong
    $DeptIDs      = empty($DeptIDs) ? NULL : $DeptIDs;
    $PeriodeGaji  = empty($PeriodeGaji) ? NULL : $PeriodeGaji;
    $StatusKerja  = empty($StatusKerja) ? NULL : $StatusKerja;

    $Sql          = "EXEC dbo.GetEmployeeList @DeptIDs = ?, @Status = ?, @PeriodePenggajian = ?, @StatusKerja = ?";
    $Query        = $this->Attendance->query($Sql, [$DeptIDs, $StatusParam, $PeriodeGaji, $StatusKerja]);

    //echo json_encode(array('sql' => $Sql)); exit;
    $Result       = $Query->result();
    $Data         = [];
    $No           = 1;

    foreach ($Result as $key => $value) {
      $Isi    = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "', '" . $value->OPHONE . "'";
      $Isi2   = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "', '" . $value->FPHONE . "'";
      $Isi3   = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "', '" . $value->BPJS . "'";
      $Isi4   = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "', '" . $value->STATE . "'";
      $Isi5   = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "', '" . $value->STATUS_PEGAWAI . "'";
      $Isi6   = "'" . $value->USERID . "', '" . $value->SSN . "', '" . $value->NAME . "', '" . $value->DEFAULTDEPTID . "', '" . $value->DEPTNAME . "'";

      // Perbaikan kecil: Gunakan empty() untuk cek yang lebih baik
      //$Link2  = (empty($value->FPHONE)) ? '' : '<a class="dropdown-item" href="#" onclick="modalGajiPokok(' . $Isi2 . ')">Edit Gaji Pokok</a>';
      $Link2  = '<a class="dropdown-item" href="#" onclick="modalGajiPokok(' . $Isi2 . ')">Edit Gaji Pokok</a>';
      $Link3  = (empty($value->BPJS))   ? '' : '<a class="dropdown-item" href="#" onclick="modalBPJS(' . $Isi3 . ')">Edit BPJS</a>';
      $Link4  = (empty($value->STATE))  ? '' : '<a class="dropdown-item" href="#" onclick="modalEditTunjangan(' . $Isi4 . ')">Edit Tunjangan</a>';

      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                      <div class="btn-group" role="group">
                          <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                          <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                              <a class="dropdown-item" href="#" onclick="openModalEdit(' . $Isi . ')">Edit Non Shift</a>
                              ' . $Link2 . '
                              ' . $Link3 . '
                              ' . $Link4 . '
                              <a class="dropdown-item" href="#" onclick="openModalStatusPegawai(' . $Isi5 . ')">Edit Status Pegawai</a>
                              <a class="dropdown-item" href="#" onclick="openModalAktivasiPegawai(' . $Isi6 . ')">Set Off Pegawai</a>
                              <a class="dropdown-item" href="#" onclick="openModalUbahDepartemen(' . $Isi6 . ')">Edit Departemen</a>
                          </div>
                      </div>
                  </div>';
      $row[] = $value->SSN;
      $row[] = $value->DEPTNAME;
      $row[] = $value->NAME;
      $row[] = $value->STATUS_PEGAWAI;
      $row[] = $value->SALARY;
      $row[] = $value->PEGAWAI_AKTIF;
      $row[] = $value->JOB_TITLE;
      $row[] = $value->BPJS;
      $row[] = $value->GroupName;
      $row[] = $value->PEGAWAI_SHIFT;
      $row[] = $value->GENDER;
      $row[] = $value->EMAIL;
      $row[] = $value->BOD;
      $row[] = $value->HIREDDAY;
      $row[] = $value->STREET;
  
      $Data[] = $row;
    }

    $Output = array(
      "draw"              => $Draw,
      "recordsTotal"      => $Query->num_rows(),
      "recordsFiltered"   => $Query->num_rows(),
      "data"              => $Data
    );

    echo json_encode($Output);
    exit();
  }

  public function daftar_bpjs()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_daftar_bpjs();

      // ambil data post
      $employees  = $this->input->post('Employees');
      $daftarBpjs = $this->input->post('DaftarBpjs');

      if (empty($employees)) {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Tidak ada data pegawai yang dikirim."
        ]);
        return;
      }

      // siapkan array untuk insert_batch
      $updateData = [];
      foreach ($employees as $emp) {
        $updateData[] = [
          'SSN'       => $emp['Nip'],
          'ZIP'       => $daftarBpjs
        ];
      }

      //echo json_encode($updateData); exit;
      $Update = $this->Attendance->update_batch('USERINFO', $updateData, 'SSN');
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function save_tunjangan()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_daftar_tunjangan();

      // ambil data post
      $employees        = $this->input->post('Employees');
      $daftarTunjangan  = $this->input->post('DaftarTunjangan');

      if (empty($employees)) {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Tidak ada data pegawai yang dikirim."
        ]);
        return;
      }

      // siapkan array untuk insert_batch
      $updateData = [];
      foreach ($employees as $emp) {
        $updateData[] = [
          'SSN'       => $emp['Nip'],
          'STATE'     => $daftarTunjangan
        ];
      }

      //echo json_encode($updateData); exit;
      $Update = $this->Attendance->update_batch('USERINFO', $updateData, 'SSN');
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function setting_gapok()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_daftar_gapok();

      // ambil data post
      $employees    = $this->input->post('Employees');
      $daftarGapok  = $this->input->post('DaftarGapok');

      if (empty($employees)) {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Tidak ada data pegawai yang dikirim."
        ]);
        return;
      }

      // siapkan array untuk insert_batch
      $updateData = [];
      foreach ($employees as $emp) {
        $updateData[] = [
          'SSN'       => $emp['Nip'],
          'FPHONE'    => $daftarGapok
        ];
      }

      //echo json_encode($updateData); exit;
      $Update = $this->Attendance->update_batch('USERINFO', $updateData, 'SSN');
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_departemen_pegawai()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      //$this->_validation_edit_gapok();

      // ambil data post
      $Nip    = $this->input->post('NipUbahDepartemen');
      $DeptID = $this->input->post('DeptList');

      $updateData = array(
        'DEFAULTDEPTID' => $DeptID
      );

      //echo json_encode(array("status" => "error", "Data" => $updateData)); exit;
      $Update = $this->Attendance->update('USERINFO', $updateData, array('SSN' => $Nip));
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_gaji_pokok()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_edit_gapok();

      // ambil data post
      $NipGapok    = $this->input->post('NipGapok');
      $NameGapok   = $this->input->post('NameGapok');
      $IDGapok     = $this->input->post('DaftarGapokEdit');

      $updateData = array(
        'FPHONE' => $IDGapok,
        //"SSN"    => $NipGapok
      );

      //echo json_encode(array("status" => "error", "Data" => $updateData)); exit;
      $Update = $this->Attendance->update('USERINFO', $updateData, array('SSN' => $NipGapok));
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_bpjs_pegawai()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_edit_bpjs();

      // ambil data post
      $Nip          = $this->input->post('EmployeeID');
      $Name         = $this->input->post('EmployeeName');
      $daftarBpjs   = $this->input->post('DaftarEditBpjs');

      $updateData = array(
        'ZIP' => $daftarBpjs
      );

      //echo json_encode(array("status" => "error", "Data" => $updateData)); exit;
      $Update = $this->Attendance->update('USERINFO', $updateData, array('SSN' => $Nip));
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_status_pegawai()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_status_pegawai();

      // ambil data post
      $Nip        = $this->input->post('NipSP');
      $Name       = $this->input->post('NameSP');
      $Status     = $this->input->post('StatusSP');

      $updateData = array(
        'CITY' => $Status
      );

      //echo json_encode(array("status" => "error", "Data" => $updateData)); exit;
      $Update = $this->Attendance->update('USERINFO', $updateData, array('SSN' => $Nip));
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_tunjangan_pegawai()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_update_tunjangan();

      // ambil data post
      $Nip          = $this->input->post('EmployeeID');
      $Name         = $this->input->post('EmployeeName');
      $TunjanganID  = $this->input->post('EditTunjangan');

      $updateData = array(
        'STATE' => $TunjanganID
      );

      //echo json_encode(array("status" => "error", "Data" => $updateData)); exit;
      $Update = $this->Attendance->update('USERINFO', $updateData, array('SSN' => $Nip));
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "UPDATE";
      $log_data = json_encode($updateData);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function update_aktivasi_pegawai()
  {
    // Lakukan validasi
    $this->_validation_aktivasi_pegawai();

    // Ambil data post
    $Nip              = $this->input->post('NipAktivasi');
    $Status           = $this->input->post('StatusAktivasi');
    $userCode         = $this->session->userdata('user_code');
    $currentDateTime  = date('Y-m-d H:i:s');
    
    $Cek = $this->Attendance->get_where('USERINFO_PROPERTIES', array('SSN' => $Nip));

    //if (!empty($Cek) && (is_array($Cek) || is_object($Cek))) {
    if ($Cek->num_rows() > 0) {
      // Data yang akan di-update
      $updateData = array(
        'IsActive'     => $Status,
        'UpdatedDate'  => $currentDateTime,
        'UpdatedBy'    => $userCode
      );

      // Kondisi WHERE untuk update
      $where = array('SSN' => $Nip);
      
      // Lakukan update data
      $Result             = $this->Attendance->update('USERINFO_PROPERTIES', $updateData, $where);
      $actionMessage      = "Data sukses diperbarui.";
    } else {
      // Data yang akan di-insert
      $insertData = array(
        'SSN'           => $Nip,
        'IsActive'      => $Status,
        'CreatedDate'   => $currentDateTime,
        'CreatedBy'     => $userCode
      );
      
      // Lakukan insert data
      $Result           = $this->Attendance->insert('USERINFO_PROPERTIES', $insertData);
      $actionMessage    = "Data sukses disimpan (baru).";
    }

    // 3. Respon Akhir
    if ($Result) {
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => $actionMessage
      ]);
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal diproses. Silakan coba lagi."
      ]);
    }
  }

  private function _validation_aktivasi_pegawai()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('StatusAktivasi') == '') {
      $data['inputerror'][]   = 'StatusAktivasi';
      $data['error_string'][] = 'Status Aktivasi is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_daftar_bpjs()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DaftarBpjs') == '') {
      $data['inputerror'][]   = 'DaftarBpjs';
      $data['error_string'][] = 'Daftar Bpjs is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_daftar_gapok()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DaftarGapok') == '') {
      $data['inputerror'][]   = 'DaftarGapok';
      $data['error_string'][] = 'Daftar Gapok is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_edit_gapok()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DaftarGapokEdit') == '') {
      $data['inputerror'][]   = 'DaftarGapokEdit';
      $data['error_string'][] = 'Daftar Gapok is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
  private function _validation_edit_bpjs()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DaftarEditBpjs') == '') {
      $data['inputerror'][]   = 'DaftarEditBpjs';
      $data['error_string'][] = 'BPJS is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_update_tunjangan()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('EditTunjangan') == '') {
      $data['inputerror'][]   = 'EditTunjangan';
      $data['error_string'][] = 'Tunjangan is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_daftar_tunjangan()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DaftarTunjangan') == '') {
      $data['inputerror'][]   = 'DaftarTunjangan';
      $data['error_string'][] = 'Tunjangan is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
  private function _validation_status_pegawai()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('StatusSP') == '') {
      $data['inputerror'][]   = 'StatusSP';
      $data['error_string'][] = 'Status Pegawai is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}