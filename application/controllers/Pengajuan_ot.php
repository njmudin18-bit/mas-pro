<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan_ot extends CI_Controller
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
    $this->function_name  = $this->router->method;
    $this->load->model('Rolespermissions_model');
    // END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('pengajuanot_model', 'pengajuan');

    $this->load->library(array('session', 'cart', 'email'));

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Personalia & GA";
      $data['nama_halaman']   = "Daftar Pengajuan Lembur";
      $data['icon_halaman']   = "icon-calendar";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();

      // Log akses
      log_helper(base_url() . $this->contoller_name . "/" . $this->function_name, "VIEW", "");

      $this->load->view('adminx/pga/pengajuan_lembur', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function pengajuan_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pengajuan();

      // Ambil data form
      $DeptID            = $this->input->post('DeptID');
      $EmployeeID        = $this->input->post('EmployeeID');
      $StartDate         = $this->input->post('StartTime');
      $EndDate           = $this->input->post('EndTime');
      $OvertimeDate      = substr($StartDate, 0, 10);
      $Notes             = ucfirst($this->input->post('Notes'));
      $start             = new DateTime($StartDate);
      $end               = new DateTime($EndDate);
      $startDate         = DateTime::createFromFormat("Y-m-d\TH:i", $StartDate);
      $endDate           = DateTime::createFromFormat("Y-m-d\TH:i", $EndDate);
      $StartTime         = $startDate->format("Y-m-d H:i:s");
      $EndTime           = $endDate->format("Y-m-d H:i:s");
      $NomorRequest      = $this->pengajuan->generateRequestNumber();
      $DataDT            = array();

      if ($end < $start) {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "End Time tidak boleh lebih kecil dari Start Time",
        ]);

        return;
      }

      //$Sql          = "EXEC dbo.CalculateOvertime @StartDate = ?, @EndDate = ?";
      $Sql          = "EXEC dbo.CalculateOvertime271025 @SSN = ?, @StartDate = ?, @EndDate = ?";
      $Query        = $this->Attendance->query($Sql, [$EmployeeID, $StartTime, $EndTime]);
      $Result 	    = $Query->result();

      //echo json_encode(array("status" => "error", "data" => $Result)); exit;

      // Hitung total jam lembur
      $TotalHours   = !empty($Result) ? $Result[0]->TotalHour : 0;
      //echo json_encode(array("status" => "error", "total_hours" => floatval($TotalHours))); exit;
      //echo json_encode(array("status" => "error", "data" => $Result, "total_hours" => floatval($TotalHours))); exit;
      // $TotalHours   = array_sum(array_map(function($item) {
      //   return floatval($item->JumlahJam);
      // }, $Result));

      // Hitung total nominal lembur
      $TotalAmounts = array_sum(array_map(function($item) {
        return floatval($item->TotalNominalRounded);
      }, $Result));

      $DataHD = array(
        'Nomor'             => $NomorRequest,
        'IsApproved'        => "P",
        'DeptID'            => $DeptID,
        'EmployeeID'        => $EmployeeID,
        'OvertimeDate'      => $OvertimeDate,
        'StartTime'         => $StartTime,
        'EndTime'           => $EndTime,
        'TotalHours'        => floatval($TotalHours),
        'TotalAmount'       => ceil($TotalAmounts),
        'Notes'             => ucfirst($this->input->post('Notes')),
        'CreatedDate'       => date('Y-m-d H:i:s'),
        'CreatedBy'         => $this->session->userdata('user_code')
      );

      //echo json_encode(array("status" => "error", "data_insert" => $DataHD)); exit;

      foreach ($Result as $key => $value) {
        $DataDT[] = array(
          'Nomor'               => $NomorRequest,
          'Status'              => $value->STATUS_PEGAWAI,
          'BaseSalary'          => $value->SALARY,
          'RumusPerhitingan'    => $value->RumusPerhitungan,
          'HourNo'              => $value->JamKe,
          'OvertimeFactor'      => $value->FaktorLembur,
          'HourQty'             => floatval($value->JumlahJam),
          'RatePerHour'         => $value->NominalPerJam,
          'TotalAmount'         => $value->TotalNominal,
          'TotalAmountRounded'  => $value->TotalNominalRounded
        );
      }

      //echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT, "calc" => $Result, "total_hours" => floatval($TotalHours))); exit;

      // mulai transaksi
      $this->Attendance->trans_begin();
      $this->Attendance->insert('Trans_OvertimeHD', $DataHD);
      if (!empty($DataDT)) {
        $this->Attendance->insert_batch('Trans_OvertimeDT', $DataDT);
      }

      if ($this->Attendance->trans_status() === FALSE) {
        $this->Attendance->trans_rollback();

        echo json_encode(["status_code" => 500, "status" => "error", "message" => "Data gagal disimpan."]);
      } else {
        $this->Attendance->trans_commit();

        echo json_encode(["status_code" => 200, "status" => "success", "message" => "Data sukses disimpan.", "Nomor" => $NomorRequest]);
      }

      // logging
      $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type = "INSERT";
      $log_data = json_encode($DataHD);
      log_helper($log_url, $log_type, $log_data);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function pengajuan_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$DeptID         = $this->input->post('DeptShow');
    if (empty($DeptID)) {
      $DeptID = null;
    } else if (is_array($DeptID)) {
      $DeptID = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetOvertimeRequest @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->IsApproved == 'P' ? 'Y' : 'P';
      $ApLbl  = $value->IsApproved == 'P' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->Nomor."'";
      $Isi2   = "'".$value->Nomor."', '".$ApSts."', '".$ApLbl."', '".$value->EmployeeID."'";
      $departmentsWithAccess  = ['IT', 'HRD'];

      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="#" onclick="approved('.$Isi2.')">'.$ApLbl.'</a>
                    </div>
                  </div>
                </div>';
      $row[] = $value->Nomor;
      $row[] = $value->IsApprovedLabel;
      $row[] = $value->DEPTNAME;
      $row[] = $value->EmployeeID;
      $row[] = $value->NAME;
      $row[] = $value->EmployeeStatus;
      $row[] = $value->OvertimeDate;
      $row[] = $value->StartTime;
      $row[] = $value->EndTime;
      if (in_array($this->session->userdata('user_dept_name'), $departmentsWithAccess)) {
        $row[] = $value->TotalHours;
        $row[] = $value->TotalAmount;
      }
      $row[] = $value->Notes;
      $row[] = $value->ApprovedBy;
      $row[] = $value->ApprovedDate;
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

  public function pengajuan_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor  = $this->input->post('NoReq');
      $data   = $this->pengajuan->get_by_id($Nomor);
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

  public function pengajuan_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    // Validasi input
    $this->_validation_pengajuan();

    $Nomor        = $this->input->post('Nomor');
    $DeptID       = $this->input->post('DeptID');
    $EmployeeID   = $this->input->post('EmployeeID');
    $StartDate    = $this->input->post('StartTime');
    $EndDate      = $this->input->post('EndTime');
    $Notes        = ucfirst($this->input->post('Notes'));

    $startDateObj = DateTime::createFromFormat("Y-m-d\TH:i", $StartDate);
    $endDateObj   = DateTime::createFromFormat("Y-m-d\TH:i", $EndDate);

    if (!$startDateObj || !$endDateObj) {
      echo json_encode([
        "status_code" => 400,
        "status"      => "error",
        "message"     => "Format tanggal/waktu tidak valid"
      ]);

      return;
    }

    $StartTime    = $startDateObj->format("Y-m-d H:i:s");
    $EndTime      = $endDateObj->format("Y-m-d H:i:s");
    $OvertimeDate = $startDateObj->format("Y-m-d");

    if ($endDateObj < $startDateObj) {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "End Time tidak boleh lebih kecil dari Start Time"
      ]);

      return;
    }

    // Hitung lembur melalui stored procedure
    //$Sql      = "EXEC dbo.CalculateOvertime271025 @StartDate = ?, @EndDate = ?";
    //$Query    = $this->Attendance->query($Sql, [$StartTime, $EndTime]);
    $Sql      = "EXEC dbo.CalculateOvertime271025 @SSN = ?, @StartDate = ?, @EndDate = ?";
    $Query    = $this->Attendance->query($Sql, [$EmployeeID, $StartTime, $EndTime]);
    $Result   = $Query->result();

    if (empty($Result)) {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Perhitungan lembur gagal atau tidak ada data"
      ]);

      return;
    }

    // Hitung total jam dan nominal
    $TotalHours   = array_sum(array_map(fn($item) => floatval($item->JumlahJam), $Result));
    $TotalAmount  = ceil(array_sum(array_map(fn($item) => floatval($item->TotalNominalRounded), $Result)));

    // Data HD
    $DataHD = [
      'IsApproved'   => "P",
      'DeptID'       => $DeptID,
      'EmployeeID'   => $EmployeeID,
      'OvertimeDate' => $OvertimeDate,
      'StartTime'    => $StartTime,
      'EndTime'      => $EndTime,
      'TotalHours'   => $TotalHours,
      'TotalAmount'  => $TotalAmount,
      'Notes'        => $Notes,
      'UpdatedDate'  => date('Y-m-d H:i:s'),
      'UpdatedBy'    => $this->session->userdata('user_code')
    ];

    // Data DT
    $DataDT = array_map(function($item) use ($Nomor) {
      return [
        'Nomor'              => $Nomor,
        'HourNo'             => $item->JamKe,
        'OvertimeFactor'     => $item->FaktorLembur,
        'HourQty'            => floatval($item->JumlahJam),
        'RatePerHour'        => $item->NominalPerJam,
        'TotalAmount'        => $item->TotalNominal,
        'TotalAmountRounded' => $item->TotalNominalRounded
      ];
    }, $Result);

    echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT, "calc" => $Result, "total_hours" => floatval($TotalHours))); exit; 

    // Jalankan update dalam transaksi
    $this->Attendance->trans_start();
    // Update HD
    $this->Attendance->update('Trans_OvertimeHD', $DataHD, ['Nomor' => $Nomor]);
    // Hapus DT lama
    $this->Attendance->delete('Trans_OvertimeDT', ['Nomor' => $Nomor]);
    // Insert DT baru
    if (!empty($DataDT)) {
      $this->Attendance->insert_batch('Trans_OvertimeDT', $DataDT);
    }

    $this->Attendance->trans_complete();

    if ($this->Attendance->trans_status() === FALSE) {
      echo json_encode(["status_code" => 500, "status" => "error", "message" => "Data gagal diupdate."]);
    } else {
      echo json_encode(["status_code" => 200, "status" => "success", "message" => "Data sukses diupdate."]);
    }
  }

  public function pengajuan_deleted()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor          = $this->input->post('NoReq');
      $data_delete    = $this->pengajuan->get_by_id($Nomor); //DATA DELETE
      $data           = $this->pengajuan->delete_by_id($Nomor);

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

  public function pengajuan_approved() 
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor        = $this->input->post('Id');
      $Status       = $this->input->post('isApproved');
      //echo json_encode($Status); exit;
      $EmployeeID   = $this->input->post('EmployeeID');
      $response     = [
        'success' => [],
        'failed'  => []
      ];

      $FirstData = array(
        'IsApproved'    => $Status,
        'ApprovedDate'  => date('Y-m-d H:i:s'),
        'ApprovedBy'    => $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->Attendance->update('Trans_OvertimeHD', $FirstData, array('Nomor' => $Nomor));
      if ($Update) {
        if ($Status == 'Y') {
          $EmployeeMail      = "";
          $EmployeeResponses = $this->Attendance->get_where('USERINFO_PROPERTIES', array('SSN' => $EmployeeID))->row();
          if ($EmployeeResponses) {
            $EmployeeMail    = $EmployeeResponses->Email;

            $Sql    = "EXEC dbo.SendPengajuanLembur @NomorPengajuan = ?";
            $Query  = $this->Attendance->query($Sql, [$Nomor]);
            $Result = $Query->row();
            //echo json_encode($Result); exit;

            $data['Lembur'] = $Result; // hanya data pegawai ini
            $message        = $this->load->view('slip_lembur', $data, TRUE);

            $this->email->clear(true);
            $this->email->from('notifications@omas-mfg.com', 'PT. MULTI ARTA SEKAWAN');
            $this->email->to($EmployeeMail); // kirim ke masing-masing pegawai
            //$this->email->to('nj.mudin18@gmail.com');
            // $this->email->cc([
            //   'personaliaga.omas@gmail.com'
            // ]);
            $this->email->subject('Persetujuan Lembur Tanggal '.$Result->OvertimeDate);
            $this->email->message($message);

            if ($this->email->send()) {
              $response['success'][] = [
                'name'  => $Result->NAME,
                'email' => $EmployeeMail,
                'status'=> 'Email berhasil dikirim'
              ];

              echo json_encode($response); exit;
            } else {
              $response['failed'][] = [
                'name'  => $Result->NAME,
                'email' => $EmployeeMail,
                'status'=> 'Gagal mengirim email'
              ];

              $email_logs[] = $this->email->print_debugger(['headers']);

              echo json_encode($response); exit;
            }
            
          } else {
            echo json_encode(
              array(
                'status_code'   => 200,
                'status'        => 'success', 
                'message'       => 'Data berhasil disimpan.',
                'email'         => $EmployeeMail
              )
            );
          }
        } else {
          echo json_encode(
            array(
              'status_code'  => 200,
              'status'       => 'success', 
              'message'      => 'Sukses mengupdate data.'
            )
          );
        }
      } else {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error', 
            'message'      => 'Gagal menyimpan data.'
          )
        );
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  private function _validation_pengajuan()
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

    if ($this->input->post('StartTime') == '') {
      $data['inputerror'][]   = 'StartTime';
      $data['error_string'][] = 'Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EndTime') == '') {
      $data['inputerror'][]   = 'EndTime';
      $data['error_string'][] = 'End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Notes') == '') {
      $data['inputerror'][]   = 'Notes';
      $data['error_string'][] = 'Keterangan is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}