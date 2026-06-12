<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shiftsetting extends CI_Controller
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
    $this->load->model('shiftsetting_model', 'shift');

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Personalia & GA";
      $data['nama_halaman']     = "Jadwal Shift";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['shiftList']        = $this->shift->get_jadwal_shift();
      $data['DeptList'] 	      = get_department_att();
      $data['DEPTID']           = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']         = $this->session->userdata('user_dept_name');
      //echo $data['DEPTID']." - ".$data['DEPTNAME']; exit;

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/pga/shift_setting', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function set_schedule()
  {
    $this->_validation_setting();

    $deptID     = $this->input->post('SelectedDeptID');   // bisa array atau single
    $shiftID    = $this->input->post('ShiftOperation');
    $startDate  = $this->input->post('StartDate');
    $endDate    = $this->input->post('EndDate');
    $members    = $this->input->post('SelectedNip');

    if (empty($members)) {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Tidak ada member terpilih.'
      ]);

      return;
    }

    // Pastikan DeptID berupa array agar bisa looping
    $deptIDs    = is_array($deptID) ? $deptID : [$deptID];
    $insertData = [];

    foreach ($deptIDs as $dept) {
      // Ambil daftar EmployeeID yang sudah ada untuk DeptID ini
      $existingEmployees = $this->Attendance
      ->select('EmployeeID')
      ->from('Trans_ShiftSchedule')
      ->where_in('EmployeeID', $members)
      ->where('DeptID', $dept)
      ->where('ShiftID', $shiftID)
      ->where('PeriodeStart', $startDate)
      ->where('PeriodeEnd', $endDate)
      ->get()
      ->result_array();

      $existingIDs  = array_column($existingEmployees, 'EmployeeID');
      $start        = new DateTime($startDate);
      $end          = new DateTime($endDate);

      foreach ($members as $member) {
        if (in_array($member, $existingIDs)) continue;

        $current = clone $start;
        while ($current <= $end) {
          $insertData[] = [
            'EmployeeID'    => $member,
            'DeptID'        => $dept,
            'ShiftID'       => $shiftID,
            'PeriodeStart'  => $startDate,
            'PeriodeEnd'    => $endDate,
            'ScheduleDate'  => $current->format('Y-m-d'),
            'StartDate'     => $startDate,
            'EndDate'       => $endDate,
            'CreatedBy'     => $this->session->userdata('user_code'),
            'CreatedDate'   => date('Y-m-d H:i:s')
          ];
          $current->modify('+1 day');
        }
      }
    }

    //echo json_encode(array("status" => "error", "data" => $insertData)); exit;

    if (!empty($insertData)) {
        $this->Attendance->trans_start();
        $this->Attendance->insert_batch('Trans_ShiftSchedule', $insertData);
        $this->Attendance->trans_complete();

        if ($this->Attendance->trans_status() === FALSE) {
            echo json_encode([
                'status_code' => 500,
                'status'      => 'error',
                'message'     => 'Data gagal disimpan.'
            ]);
        } else {
            echo json_encode([
                'status_code' => 200,
                'status'      => 'success',
                'message'     => 'Data sukses disimpan.'
            ]);
        }
    } else {
        echo json_encode([
            'status_code' => 500,
            'status'      => 'error',
            'message'     => 'Data sudah tersimpan dengan periode tersebut.'
        ]);
    }
  }


  public function set_schedule_OLD()
  {
    $this->_validation_setting();

    $deptID     = $this->input->post('DeptID');
    $shiftID    = $this->input->post('ShiftOperation');
    $startDate  = $this->input->post('StartDate');
    $endDate    = $this->input->post('EndDate');
    $members    = $this->input->post('SelectedNip');
    $insertData = [];

    if (empty($members)) {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Tidak ada member terpilih.'
      ]);

      return;
    }

    // Ambil daftar EmployeeID yang sudah ada
    $existingEmployees = $this->Attendance
    ->select('EmployeeID')
    ->from('Trans_ShiftSchedule')
    ->where_in('EmployeeID', $members)
    ->where('DeptID', $deptID)
    ->where('ShiftID', $shiftID)
    ->where('PeriodeStart', $startDate)
    ->where('PeriodeEnd', $endDate)
    ->get()
    ->result_array();

    echo json_encode(array("status" => "error", "data" => $existingEmployees)); exit;

    // Ambil EmployeeID yang sudah ada sebagai array
    $existingIDs = array_column($existingEmployees, 'EmployeeID');

    // Konversi tanggal
    $start       = new DateTime($startDate);
    $end         = new DateTime($endDate);

    foreach ($members as $member) {
      // Lewatkan jika data sudah ada
      if (in_array($member, $existingIDs)) continue;

      $current = clone $start;
      while ($current <= $end) {
        $insertData[] = [
          'EmployeeID'    => $member,
          'DeptID'        => $deptID,
          'ShiftID'       => $shiftID,
          'PeriodeStart'  => $startDate,
          'PeriodeEnd'    => $endDate,
          'ScheduleDate'  => $current->format('Y-m-d'),
          'StartDate'     => $startDate,
          'EndDate'       => $endDate,
          'CreatedBy'     => $this->session->userdata('user_code'),
          'CreatedDate'   => date('Y-m-d H:i:s')
        ];
        $current->modify('+1 day');
      }
    }

    if (!empty($insertData)) {
      $this->Attendance->trans_start();
      $this->Attendance->insert_batch('Trans_ShiftSchedule', $insertData);
      $this->Attendance->trans_complete();

      if ($this->Attendance->trans_status() === FALSE) {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data gagal disimpan.'
        ]);
      } else {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data sukses disimpan.'
        ]);
      }
    } else {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Data sudah tersimpan dengan periode tersebut.'
      ]);
    }
  }

  public function update_schedule()
  {
    $ScheduleID = $this->input->post('kodeEdit');
    $DeptID     = $this->input->post('DeptIDEdit');
    $EmployeeID = $this->input->post('NipEdit');
    $ShiftID    = $this->input->post('ShiftOperationEdit');

    $Data = array(
      'ShiftID' => $ShiftID
    );

    //echo json_encode(array("status" => "error", "Data" => $Data)); exit;

    $Update = $this->Attendance->update('Trans_ShiftSchedule', $Data, array('Id' => $ScheduleID));
    if ($Update) {
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data sukses diupdate.'
      ]);
    } else {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Data gagal diupdate.'
      ]);
    }
  }

  public function get_user_by_deptid()
  {
    $DeptID = $this->input->post('DeptID');
    $Data   = $this->shift->get_member_by_deptid($DeptID);

    echo json_encode($Data);
  }

  public function set_schedule_list()
	{
    // Array pemetaan nama bulan Inggris ke Indonesia (dalam huruf kapital)
    $indonesianMonths = [
      'JANUARY'   => 'JANUARI',
      'FEBRUARY'  => 'FEBRUARI',
      'MARCH'     => 'MARET',
      'APRIL'     => 'APRIL',
      'MAY'       => 'MEI',
      'JUNE'      => 'JUNI',
      'JULY'      => 'JULI',
      'AUGUST'    => 'AGUSTUS',
      'SEPTEMBER' => 'SEPTEMBER',
      'OCTOBER'   => 'OKTOBER',
      'NOVEMBER'  => 'NOVEMBER',
      'DECEMBER'  => 'DESEMBER'
    ];

		$Draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$StartDate    = $this->input->post('start_date');
		$EndDate 	    = $this->input->post('end_date');
		$DeptID 	    = $this->input->post('dept_id');
    $startDateObj = new DateTime($StartDate);
    $endDateObj   = new DateTime($EndDate);
    $interval     = new DateInterval('P1D');

		$Sql          = "EXEC dbo.GetShiftSchedules @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query        = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		  = $Query->result();
		$Data 			  = [];
		$No 				  = 1;

		foreach ($Result as $key => $Res) {
      $Row   = [];
      $Row[] = $No++;
      $Row[] = $Res->EmployeeID;
      $Row[] = $Res->NAME;
      $Row[] = $Res->DEPTNAME;

      // Loop tanggal dinamis
      $currentDate    = clone $startDateObj;
      $today          = new DateTime(); // tanggal sekarang, H
      while ($currentDate <= $endDateObj) {
        $day          = date('j', $currentDate->getTimestamp()); 
        $monthEng     = strtoupper(date('F', $currentDate->getTimestamp()));
        $year         = date('Y', $currentDate->getTimestamp());
        $colName      = $day . '_' . $monthEng . '_' . $year;
        $shift        = property_exists($Res, $colName) ? $Res->$colName : '';

        // Pisahkan teks shift dan ID
        $shiftText    = '';
        $scheduleId   = '';
        $shiftId      = '';
        if ($shift) {
          $parts      = explode('|', $shift);
          $shiftText  = $parts[0];                         // "Shift 2 (15:00-23:00)"
          $scheduleId = isset($parts[1]) ? $parts[1] : ''; // "13" ScheduleID
          $shiftId    = isset($parts[2]) ? $parts[2] : ''; // "2" ShiftID
        }

        // Tentukan apakah kolom bisa diedit
        if ($currentDate > $today) {
          // H+1 dan setelahnya → bisa diedit
          $Row[] = '<span data-empid="'.$Res->EmployeeID.'" data-empname="'.$Res->NAME.'" data-deptid="'.$Res->DeptID.'" data-deptname="'.$Res->DEPTNAME.'" data-scheduleid="'.$scheduleId.'" data-shiftid="'.$shiftId.'">'.$shiftText.'</span>';
        } else {
          // H dan sebelumnya → readonly / tidak bisa diedit
          //$Row[] = $shiftText ? $shiftText : '';
          $Row[] = '<span data-empid="'.$Res->EmployeeID.'" data-empname="'.$Res->NAME.'" data-deptid="'.$Res->DeptID.'" data-deptname="'.$Res->DEPTNAME.'" data-scheduleid="'.$scheduleId.'" data-shiftid="'.$shiftId.'">'.$shiftText.'</span>';
        }

        $currentDate->add($interval);
      }
  
      $Data[] = $Row;
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

  public function set_schedule_list_OLD()
  {
    // Mapping bulan Inggris -> Indonesia (untuk header kalau dipakai di view)
    $indonesianMonths = [
        'JANUARY'   => 'JAN',
        'FEBRUARY'  => 'FEB',
        'MARCH'     => 'MAR',
        'APRIL'     => 'APR',
        'MAY'       => 'MEI',
        'JUNE'      => 'JUN',
        'JULY'      => 'JUL',
        'AUGUST'    => 'AGS',
        'SEPTEMBER' => 'SEP',
        'OCTOBER'   => 'OKT',
        'NOVEMBER'  => 'NOV',
        'DECEMBER'  => 'DES'
    ];

    $Draw         = intval($this->input->post("draw"));
    $Start        = intval($this->input->post("start"));
    $Length       = intval($this->input->post("length"));
    $StartDate    = $this->input->post('start_date');
    $EndDate      = $this->input->post('end_date');
    $DeptID       = $this->input->post('dept_id');
    //echo $StartDate." ".$EndDate." ".$DeptID; exit;
    $startDateObj = new DateTime($StartDate);
    $endDateObj   = new DateTime($EndDate);
    $interval     = new DateInterval('P1D');

    // Jalankan stored procedure
    $Sql    = "EXEC dbo.GetShiftSchedules @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query  = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID]);
    $Result = $Query->result();
    //echo $Query; exit;

    $Total  = count($Result);
    $Paged  = array_slice($Result, $Start, $Length);

    $Data   = [];
    $No     = $Start + 1;

    foreach ($Paged as $Res) {
      $Row   = [];
      $Row[] = $No++;
      $Row[] = $Res->EmployeeID;
      $Row[] = $Res->NAME;
      $Row[] = $Res->DEPTNAME;

      // Loop tanggal dinamis
      $currentDate    = clone $startDateObj;
      $today          = new DateTime(); // tanggal sekarang, H
      while ($currentDate <= $endDateObj) {
        $day          = date('j', $currentDate->getTimestamp()); 
        $monthEng     = strtoupper(date('F', $currentDate->getTimestamp()));
        $year         = date('Y', $currentDate->getTimestamp());
        $colName      = $day . '_' . $monthEng . '_' . $year;
        $shift        = property_exists($Res, $colName) ? $Res->$colName : '';

        // Pisahkan teks shift dan ID
        $shiftText    = '';
        $scheduleId   = '';
        if ($shift) {
          $parts      = explode('|', $shift);
          $shiftText  = $parts[0];                         // "Shift 2 (15:00-23:00)"
          $scheduleId = isset($parts[1]) ? $parts[1] : ''; // "13" ScheduleID
          $shiftId    = isset($parts[2]) ? $parts[2] : ''; // "2" ShiftID
        }

        // Tentukan apakah kolom bisa diedit
        if ($currentDate > $today) {
          // H+1 dan setelahnya → bisa diedit
          $Row[] = '<span data-empid="'.$Res->EmployeeID.'" data-empname="'.$Res->NAME.'" data-deptid="'.$Res->DeptID.'" data-scheduleid="'.$scheduleId.'" data-shiftid="'.$shiftId.'">'.$shiftText.'</span>';
        } else {
          // H dan sebelumnya → readonly / tidak bisa diedit
          $Row[] = $shiftText ? $shiftText : '';
        }

        $currentDate->add($interval);
      }

      $Data[] = $Row;
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $Data
    ]);
    exit();
  }

  private function _validation_setting()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('ShiftOperation') == '') {
      $data['inputerror'][]   = 'ShiftOperation';
      $data['error_string'][] = 'Shift Operation is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('StartDate') == '') {
      $data['inputerror'][]   = 'StartDate';
      $data['error_string'][] = 'Start Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EndDate') == '') {
      $data['inputerror'][]   = 'EndDate';
      $data['error_string'][] = 'End Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('SelectedEmployee') == '') {
      $data['inputerror'][]   = 'SelectedEmployee';
      $data['error_string'][] = 'Selected Employee is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}