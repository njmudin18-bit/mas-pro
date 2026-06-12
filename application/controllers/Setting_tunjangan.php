<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_tunjangan extends CI_Controller
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
    $this->load->model('setting_tunjangan_model', 'setting');
    $this->load->model('allowance_model', 'allowance');

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Personalia & GA";
      $data['nama_halaman']   = "Setting Tunjangan";
      $data['icon_halaman']   = "icon-calendar";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      $data['TunjanganList']  = $this->allowance->get_all_data();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/pga/setting_tunjangan', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function tunjangan_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_tunjangan();

      $Data = array(
        'IsActive'          => $this->input->post('IsActive'),
        'DeptID'            => $this->input->post('DeptID'),
        'EmployeeID'        => $this->input->post('EmployeeID'),
        'Period'            => $this->input->post('Period'),
        'AllowanceID'       => $this->input->post('AllowanceID'),
        'Keterangan'        => ucfirst($this->input->post('Keterangan')),
        'CreatedDate'       => date('Y-m-d H:i:s'),
        'CreatedBy'         => $this->session->userdata('user_code')
      );

      //echo json_encode(array("status" => "error", "data" => $Data)); exit;

      $Save = $this->setting->save($Data);
      if ($Save) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses ditambahkan."
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data gagal ditambahkan."
          )
        );
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "INSERT";
      $log_data = json_encode($Data);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function tunjangan_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

    $DeptID = $this->input->post('DeptShow');
    // Jika array, ubah menjadi string dipisah koma
    if (is_array($DeptID)) {
      if (in_array('ALL', $DeptID)) {
        $DeptIDParam = 'ALL';
      } else {
        $DeptIDParam = implode(',', $DeptID);
      }
    } else {
      $DeptIDParam = $DeptID ?: 'ALL';
    }

    $Sql            = "EXEC dbo.GetBeneficiary @DeptID = ?";
    $Query          = $this->Attendance->query($Sql, [$DeptIDParam]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->TunjanganID."'";
      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>';
      $row[] = $value->IsActive;
      $row[] = $value->DEPTNAME;
      $row[] = $value->NAME;
      $row[] = $value->EmployeeID;
      $row[] = $value->Period;
      $row[] = $value->AllowanceName;
      $row[] = $value->Amount;
      $row[] = $value->Keterangan;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function tunjangan_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id  = $this->input->post('TunjanganID');
      $data   = $this->setting->get_by_id($Id);
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

  public function tunjangan_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    // Validasi input
    $this->_validation_tunjangan();

    $Data = array(
      'IsActive'          => $this->input->post('IsActive'),
      'DeptID'            => $this->input->post('DeptID'),
      'EmployeeID'        => $this->input->post('EmployeeID'),
      'Period'            => $this->input->post('Period'),
      'AllowanceID'       => $this->input->post('AllowanceID'),
      'Keterangan'        => ucfirst($this->input->post('Keterangan')),
      'UpdatedDate'       => date('Y-m-d H:i:s'),
      'UpdatedBy'         => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $Update = $this->setting->update(array('TunjanganID' => $this->input->post('Kode')), $Data);
    if ($Update) {
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses diupdate."
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal diupdate."
        )
      );
    }
  }

  public function tunjangan_deleted()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id             = $this->input->post('TunjanganID');
      $data_delete    = $this->setting->get_by_id($Id); //DATA DELETE
      $Delete         = $this->setting->delete_by_id($Id);
      if ($Delete) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses dihapus."
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data gagal dihapus."
          )
        );
      }

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

  private function _validation_tunjangan()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Departemen is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EmployeeID') == '') {
      $data['inputerror'][]   = 'EmployeeID';
      $data['error_string'][] = 'Pegawai is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Period') == '') {
      $data['inputerror'][]   = 'Period';
      $data['error_string'][] = 'Periode is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AllowanceID') == '') {
      $data['inputerror'][]   = 'AllowanceID';
      $data['error_string'][] = 'Jenis Tunjangan is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IsActive') == '') {
      $data['inputerror'][]   = 'IsActive';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    // if ($this->input->post('Keterangan') == '') {
    //   $data['inputerror'][]   = 'Keterangan';
    //   $data['error_string'][] = 'Keterangan is required';
    //   $data['status']         = FALSE;
    // }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  // TUNJANGAN GROUP
  public function tunjangan_group()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Personalia & GA";
      $data['nama_halaman']   = "Setting Tunjangan Group";
      $data['icon_halaman']   = "icon-calendar";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      $data['TunjanganList']  = $this->allowance->get_all_data();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/pga/setting_tunjangan_group', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function tunjangan_group_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

    $Sql            = "EXEC dbo.GetGroupAllowance";
    $Query          = $this->Attendance->query($Sql);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi   = "'".$value->Nomor."'";
      $row   = [];
      $row[] = $value->NomorUrut;
      $row[] = ($value->NomorUrut != NULL) ? 
                '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>' : '';
      $row[] = $value->IsActive;
      $row[] = $value->Nomor;
      $row[] = $value->GroupName;
      $row[] = $value->Period;
      $row[] = $value->AllowanceName;
      $row[] = $value->Amount;
      $row[] = $value->Keterangan;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function tunjangan_group_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_tunjangan_group();

      $this->Attendance->trans_begin();
      $Nomor        = $this->setting->generateGroupNomor();
      $AllowanceIDs = $this->input->post('AllowanceID');

      $DataHD = array(
        'GroupName'    => ucwords($this->input->post('GroupName')),
        'Nomor'        => $Nomor,
        'Period'       => $this->input->post('Period'),
        'IsActive'     => $this->input->post('IsActive'),
        'Keterangan'   => ucfirst($this->input->post('Keterangan')),
        'CreatedDate'  => date('Y-m-d H:i:s'),
        'CreatedBy'    => $this->session->userdata('user_code')
      );

      $this->Attendance->insert('Trans_TunjanganGroupHD', $DataHD);

      if (!empty($AllowanceIDs) && is_array($AllowanceIDs)) {
        $DataDT = [];
        foreach ($AllowanceIDs as $AllowanceID) {
          $DataDT[] = array(
            'Nomor'        => $Nomor,
            'AllowanceID'  => $AllowanceID,
            'CreatedDate'  => date('Y-m-d H:i:s'),
            'CreatedBy'    => $this->session->userdata('user_code')
          );
        }

        if (!empty($DataDT)) {
          $this->Attendance->insert_batch('Trans_TunjanganGroupDT', $DataDT);
        }
      }

      //echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT)); exit;

      if ($this->Attendance->trans_status() === FALSE) {
        $this->Attendance->trans_rollback();
        echo json_encode(
          array(
            "status_code"   => 500,
            "status"        => "error", 
            "message"       => "Data gagal disimpan."
          )
        );
      } else {
        $this->Attendance->trans_commit();
        echo json_encode(
          array(
            "status_code"   => 200,
            "status"        => "success",
            "message"       => "Data sukses disimpan."
          )
        );
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "INSERT";
      $log_data = json_encode(array(
        'header' => $DataHD,
        'detail' => $DataDT
      ));
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function tunjangan_group_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor    = $this->input->post('Nomor');
      $Data     = $this->setting->get_group_by_id($Nomor);
      // Kalau kosong/null, set ke NULL
      if ($Nomor === '' || $Nomor === null) {
        $Nomor  = null;
      }

      $Sql      = "EXEC dbo.GetAllowanceList @Nomor = ?";
      $Query    = $this->Attendance->query($Sql, [$Nomor]);
      $Result   = $Query->result();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data ditemukan.",
          "HD"          => $Data,
          "DT"          => $Result
        )
      );
      //echo json_encode($data);

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "EDIT";
      $log_data       = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function tunjangan_group_update()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() != 1) {
      echo json_encode(array("status" => "forbidden"));

      return;
    }

    $this->_validation_tunjangan_group();

    $this->Attendance->trans_begin(); // mulai transaksi

    $Nomor        = $this->input->post('Nomor');
    $AllowanceIDs = $this->input->post('AllowanceID');

    // Update HD
    $DataHD = array(
      'GroupName'    => ucwords($this->input->post('GroupName')),
      'Period'       => $this->input->post('Period'),
      'IsActive'     => $this->input->post('IsActive'),
      'Keterangan'   => ucfirst($this->input->post('Keterangan')),
      'UpdatedDate'  => date('Y-m-d H:i:s'),
      'UpdatedBy'    => $this->session->userdata('user_code')
    );

    $this->Attendance->where('Nomor', $Nomor);
    $this->Attendance->update('Trans_TunjanganGroupHD', $DataHD);

    // Hapus DT lama
    $this->Attendance->where('Nomor', $Nomor);
    $this->Attendance->delete('Trans_TunjanganGroupDT');

    // Siapkan data DT baru
    $DataDT = [];
    if (!empty($AllowanceIDs) && is_array($AllowanceIDs)) {
      foreach ($AllowanceIDs as $AllowanceID) {
        $DataDT[] = array(
          'Nomor'       => $Nomor,
          'AllowanceID' => $AllowanceID,
          'CreatedDate' => date('Y-m-d H:i:s'),
          'CreatedBy'   => $this->session->userdata('user_code')
        );
      }

      // Insert batch DT baru
      if (!empty($DataDT)) {
        $this->Attendance->insert_batch('Trans_TunjanganGroupDT', $DataDT);
      }
    }

    // Cek status transaksi
    if ($this->Attendance->trans_status() === FALSE) {
      $this->Attendance->trans_rollback();

      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal diupdate."
      ]);
    } else {
      $this->Attendance->trans_commit();

      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data sukses diupdate."
      ]);
    }

    // logging
    $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type = "UPDATE";
    $log_data = json_encode([
      'header' => $DataHD,
      'detail' => $DataDT
    ]);
    log_helper($log_url, $log_type, $log_data);
  }

  public function tunjangan_group_deleted()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor          = $this->input->post('Nomor');
      $data_delete    = $this->setting->get_group_by_id($Nomor); //DATA DELETE
      $Delete         = $this->setting->delete_group_by_id($Nomor);
      if ($Delete) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses dihapus."
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data gagal dihapus."
          )
        );
      }

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

  public function get_tunjangan_list() 
  {
    $Nomor     = $this->input->post('Nomor');
    // Kalau kosong/null, set ke NULL
    if ($Nomor === '' || $Nomor === null) {
      $Nomor   = null;
    }

    $Sql            = "EXEC dbo.GetAllowanceList @Nomor = ?";
    $Query          = $this->Attendance->query($Sql, [$Nomor]);
		$Result 		    = $Query->result();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data ditemukan.",
        "data"        => $Result
      )
    );
  }

  private function _validation_tunjangan_group()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('GroupName') == '') {
      $data['inputerror'][]   = 'GroupName';
      $data['error_string'][] = 'Group Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Period') == '') {
      $data['inputerror'][]   = 'Period';
      $data['error_string'][] = 'Periode is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IsActive') == '') {
      $data['inputerror'][]   = 'IsActive';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam tunjanganContainer
    $allowanceID  = $this->input->post('AllowanceID');
    if (is_array($allowanceID)) {
      foreach ($allowanceID as $i => $allowance) {
        if (empty($allowance)) {
          $data['inputerror'][]   = "AllowanceID[$i]";
          $data['error_string'][] = 'Tunjangan is required';
          $data['status']         = FALSE;
        }
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}