<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Salary_calculation extends CI_Controller
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
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

		$this->load->helper(array('url', 'form', 'cookie'));
		$this->load->library(array('session', 'cart'));

		$this->load->model('auth_model', 'auth');
		if ($this->auth->isNotLogin());

		//START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('barcode_model', 'barcode_sales');
    $this->load->model('shiftsetting_model', 'shift');

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
    if ($this->Attendance) {
      $this->Attendance->db_debug = TRUE;
    } else {
      echo "Gagal load database!";
    }
	}

	public function index()
	{
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Personalia & GA";
			$data['nama_halaman'] 	= "Hitung Gaji";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		            = base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/pga/hitung_gaji', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function calculate_list()
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

		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('StartDate');
		$EndDate 	      = $this->input->post('EndDate');
		$DeptID 	      = $this->input->post('DeptID');
    $DeptID         = ($DeptID === '' ? NULL : $DeptID);
    $ShiftTolerance = 60;
    $endDateObj     = new DateTime($EndDate);
    $interval       = new DateInterval('P1D');

    //$Sql            = "EXEC dbo.SalaryCalculation131025 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation211125 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation051225 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation231225 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation270126 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";

    $Sql            = "EXEC dbo.SalaryCalculation280526 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    $Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID, $ShiftTolerance]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi          = "'".$value->SSN."', '".$value->UserName."', '".$value->STATE."', 'TUNJANGAN LEMBUR', '".$No."'";

      $row   = [];
      $row[] = $No;
      $row[] = $value->SSN;
      $row[] = $value->UserName;
      $row[] = $value->STATUS;
      $row[] = $value->DEPTNAME;
      $row[] = $value->SUNDAY; //SUNDAY
      $row[] = $value->CYCLE;
      $row[] = $value->HK;
      $row[] = $value->HADIR; //$value->HD;
      $row[] = $value->SAKIT;
      $row[] = $value->IJIN;
      $row[] = $value->ALPA;
      $row[] = $value->TLT_LESS10;
      $row[] = $value->TLT_LESS15;
      $row[] = $value->TLT_MORE15;
      $row[] = $value->HOLIDAY;
      $row[] = $value->HOLIDAY_DATE;
      $row[] = $value->OT_MINGGU; // OT MINGGU
      $row[] = $value->OT_MINGGU_DATE; // OT MINGGU DATE
      $row[] = $value->GAJI_POKOK;
      $row[] = $value->PEMBAGI;
      $row[] = $value->UPAH;
      $row[] = $value->UANG_MKN;
      $row[] = $value->UANG_TUNJ_HADIR;
      $row[] = $value->NOMINAL_SHIFT_2;
      $row[] = $value->UANG_LIBUR_LEMBUR;
      $row[] = $value->JAM_OT;
      //$row[] = $value->GR_TOTAL_UPAH;
      $row[] = $value->TOTAL_TUNJ_MKN;
      $row[] = $value->TOTAL_TUNJ_HADIR;
      $row[] = $value->TOTAL_TUNJ_LEMBUR;
      $row[] = $value->TOTAL_TUNJ_SHIFT;
      $row[] = $value->NILAI_OT;
      $row[] = $value->GR_TOTAL_UPAH; //$value->TOTAL_GAJI;
      $row[] = "";
      $row[] = $value->NILAI_BPJS;
      $row[] = $value->HUTANG;
      $row[] = $value->GAJI_BERSIH;
      $row[] = $value->DEFAULTDEPTID;
      $row[] = $StartDate;
      $row[] = $EndDate;
      $row[] = $value->KET_HUTANG;

      $currentDate = new DateTime($StartDate);
      while ($currentDate <= $endDateObj) {
        $dayName      = date('d', $currentDate->getTimestamp());
        
        $englishMonth = strtoupper(date('F', $currentDate->getTimestamp()));
        $monthName    = $indonesianMonths[$englishMonth];

        $colIn        = $dayName . '_' . $monthName . '_IN';
        $colOut       = $dayName . '_' . $monthName . '_OUT';
        $colTelat     = $dayName . '_' . $monthName . '_TELAT';
        $colGH        = $dayName . '_' . $monthName . '_GH';
        $colIK        = $dayName . '_' . $monthName . '_IK';
        $colTGH       = $dayName . '_' . $monthName . '_TGH';

        $row[]        = isset($value->$colIn) ? $value->$colIn : null;
        $row[]        = isset($value->$colOut) ? $value->$colOut : null;
        $row[]        = isset($value->$colTelat) ? $value->$colTelat : null;
        $row[]        = isset($value->$colGH) ? $value->$colGH : null;
        $row[]        = isset($value->$colIK) ? $value->$colIK : null;
        $row[]        = isset($value->$colTGH) ? $value->$colTGH : null;

        $currentDate->add($interval);
      }
      
      $No++;
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

  public function get_tunjangan_pegawai() 
  {
    $TunjanganID = $this->input->post('Id');

    $Sql = "SELECT 
              a.AllowanceID,
              a.AllowanceName,
              CAST(FORMAT(a.Amount, 'N0', 'id-ID') AS VARCHAR(20)) AS Amount,
              c.HeaderID,
              c.GroupName
            FROM Ms_Allowance a
            LEFT JOIN Trans_TunjanganGroupDT b ON b.AllowanceID = a.AllowanceID
            LEFT JOIN Trans_TunjanganGroupHD c ON c.Nomor = b.Nomor
            WHERE
              c.HeaderID = '$TunjanganID'
              AND a.AllowanceName LIKE '%TUNJANGAN LEMBUR%'
              AND a.IsActive = 'A'
              AND a.AllowanceName <> 'Uang Makan'
            ORDER BY a.AllowanceName";
    $Query  = $this->Attendance->query($Sql);
		if ($Query->num_rows() > 0) {
      // ada data
      $Result = $Query->row();

      echo json_encode(
        array(
          'status_code' => 200,
          'message'     => 'Data ditemukan.',
          'data'        => $Result
        )
      );
    } else {
      echo json_encode(
        array(
          'status_code' => 404,
          'message'     => 'Data tidak ditemukan.',
          'data'        => array()
        )
      );
    }
  }
}