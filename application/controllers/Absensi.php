<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absensi extends CI_Controller
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
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('barcode_model', 'barcode_sales');
    $this->load->model('shiftsetting_model', 'shift');

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Personalia & GA";
			$data['nama_halaman'] 	= "Absensi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/accounting/absensi', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function absensi_list()
	{

		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$now 				= date("Y-m-d");
		$year 			= date("Y");
		$month 			= date("m");

		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');
		$company 	  = $this->input->post('company');
    //exit();
		$absensi_DB_MAS  = $this->load->database('absensi_local_mas', TRUE);
	  $absensi_DB_MAiN = $this->load->database('absensi_local_main', TRUE);

		$sql 		= "SELECT 
                B.SSN + ',' + B.NAME + ',' + convert(varchar, a.CHECKTIME, 103) + ' ' + convert(
                  varchar(5), 
                  a.CHECKTIME, 
                  8
                ) as GREATDAY 
              FROM 
                CHECKINOUT A 
                JOIN USERINFO B ON A.USERID = B.USERID 
                JOIN DEPARTMENTS C ON B.DEFAULTDEPTID = C.DEPTID 
              WHERE 
                CAST(a.CHECKTIME AS DATE) BETWEEN '$start_date' 
                AND '$end_date' 
              ORDER BY 
                CAST(a.CHECKTIME AS DATE) ASC";

		//$query 			= $absensi_DB_MAS->query($sql);
    if ($company == 'MAS') {
      $query 			= $absensi_DB_MAS->query($sql);
    } else {
      $query 			= $absensi_DB_MAiN->query($sql);
    }
    
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;

		foreach ($result as $key => $value) {
			$data[] = array(
				$no++,
				$value->GREATDAY,
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

  public function kehadiran() {
    $user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Accounting";
			$data['nama_halaman'] 	= "Daftar Kehadiran";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/accounting/kehadiran', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function kehadiran_list()
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

		$StartDate  = $this->input->post('StartDate');
		$EndDate 	  = $this->input->post('EndDate');
    $endDateObj = new DateTime($EndDate);
    $interval   = new DateInterval('P1D');

    $Sql        = "EXEC dbo.GetDaftarKehadiran @StartDate = ?, @EndDate = ?";
    $Query      = $this->Attendance->query($Sql, [$StartDate, $EndDate]);
		$Result 		= $Query->result();
		$Data 			= [];
		$No 				= 1;

		foreach ($Result as $key => $value) {
      $row   = [];
      $row[] = $No++;
      $row[] = $value->SSN;
      $row[] = $value->NAME;
      $row[] = $value->HARI_KERJA;
      $row[] = $value->TOTAL_HADIR;
  
      // Reset currentDate untuk setiap baris data
      $currentDate = new DateTime($StartDate);
      
      while ($currentDate <= $endDateObj) {
          $dayName = date('j', $currentDate->getTimestamp());
          
          // Ambil nama bulan Inggris (uppercase) lalu petakan ke nama bulan Indonesia
          $englishMonth = strtoupper(date('F', $currentDate->getTimestamp()));
          $monthName    = $indonesianMonths[$englishMonth]; // <--- Perubahan di sini

          $colIn  = $dayName . '_' . $monthName . '_IN';
          $colOut = $dayName . '_' . $monthName . '_OUT';

          // Pastikan properti ada sebelum diakses
          $row[] = isset($value->$colIn) ? $value->$colIn : null;
          $row[] = isset($value->$colOut) ? $value->$colOut : null;

          $currentDate->add($interval);
      }
  
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

  //PRESENSI
  public function daftar_presensi()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Personalia & GA";
			$data['nama_halaman'] 	= "Daftar Presensi";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
			$data['perusahaan'] 		= $this->perusahaan->get_details();

      //echo $data['DEPTNAME']; exit;

			//ADDING TO LOG
			$log_url 		            = base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/pga/presensi', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function presensi_list()
  {
    // 1. Array pemetaan nama bulan Inggris ke Indonesia
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

    // 2. Ambil Parameter Request
    $Draw           = intval($this->input->get("draw"));
    $start          = intval($this->input->get("start"));
    $length         = intval($this->input->get("length"));

    $StartDate      = $this->input->post('StartDate');
    $EndDate        = $this->input->post('EndDate');
    $DeptID         = $this->input->post('DeptID');
    $DeptID         = ($DeptID === '' ? NULL : $DeptID);
    $ShiftTolerance = 60;
    
    // Setup Date Loop
    $endDateObj     = new DateTime($EndDate);
    $interval       = new DateInterval('P1D');

    // 3. Eksekusi Query Stored Procedure
    // Menggunakan SP terbaru sesuai snippet Anda: SalaryCalculation231225
    //$Sql    = "EXEC dbo.SalaryCalculation231225 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    
    $Sql    = "EXEC dbo.SalaryCalculation280526 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    $Query  = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID, $ShiftTolerance]);
    $Result = $Query->result();
    
    $Data   = [];
    $No     = 1;

    // 4. Looping Data Baris per Baris
    foreach ($Result as $key => $value) {
        $row   = [];
        
        // --- Kolom Tetap (Identitas & Summary) ---
        $row[] = $No++;
        $row[] = $value->SSN;
        $row[] = $value->UserName;
        $row[] = $value->DEPTNAME;
        $row[] = $value->CD;
        $row[] = $value->HADIR;
        $row[] = $value->SAKIT;
        $row[] = $value->IJIN;
        $row[] = $value->DL;
        $row[] = $value->ALPA; // Sesuai snippet terakhir Anda
        $row[] = $value->TLT_LESS10;
        $row[] = $value->TLT_LESS15;
        $row[] = $value->TLT_MORE15;
        $row[] = $value->HOLIDAY;
        $row[] = $value->HOLIDAY_DATE;

        // --- Kolom Dinamis (Tanggal per Tanggal) ---
        $currentDate = new DateTime($StartDate);
        
        while ($currentDate <= $endDateObj) {
            $timestamp    = $currentDate->getTimestamp();
            $dayName      = date('d', $timestamp);
            $englishMonth = strtoupper(date('F', $timestamp));
            // Translate Bulan ke Indonesia agar cocok dengan nama kolom di SP (01_JANUARI_IN)
            $monthName    = isset($indonesianMonths[$englishMonth]) ? $indonesianMonths[$englishMonth] : $englishMonth;

            $colIn        = $dayName . '_' . $monthName . '_IN';
            $colOut       = $dayName . '_' . $monthName . '_OUT';

            // Ambil Value Mentah dari Database
            $inValue      = isset($value->$colIn) ? $value->$colIn : null;
            $outValue     = isset($value->$colOut) ? $value->$colOut : null;

            // ------------------------------------------------------------------
            // LOGIKA UTAMA: Regex untuk Deteksi Format Waktu (Jam:Menit:Detik)
            // ------------------------------------------------------------------
            // Pola ini cocok dengan "06:00", "06:00:00", "17:30"
            // Pola ini TIDAK COCOK dengan "Tahun Baru", "ALPA", "Cuti Bersama"
            $patternTime = '/^\d{1,2}:\d{2}(:\d{2})?$/'; 

            // A. Filter Double Scan (Jika IN & OUT adalah Jam dan berdekatan)
            if (!empty($inValue) && !empty($outValue)) {
                // Pastikan keduanya adalah JAM sebelum menghitung selisih waktu
                if (preg_match($patternTime, $inValue) && preg_match($patternTime, $outValue)) {
                    $t1 = strtotime($inValue);
                    $t2 = strtotime($outValue);

                    // Jika selisih < 10 menit (600 detik), anggap Out double scan
                    if ($t1 !== false && $t2 !== false && abs($t2 - $t1) < 600) {
                        $outValue = null; 
                    }
                }
            }

            // Ambil Nama Shift
            $shiftName = $this->getShiftName($value->SSN, $currentDate->format('Y-m-d'));

            // B. Penentuan Output Kolom IN
            if (!empty($inValue)) {
                if (preg_match($patternTime, $inValue)) {
                    // Jika JAM -> Tambahkan prefix Shift (utk styling di frontend)
                    $row[] = $shiftName . "|" . $inValue;
                } else {
                    // Jika TEKS (Libur/Alpa/Ijin) -> Tampilkan teks murni
                    $row[] = $inValue;
                }
            } else {
                $row[] = $inValue; // Null/Kosong
            }

            // C. Penentuan Output Kolom OUT
            if (!empty($outValue)) {
                if (preg_match($patternTime, $outValue)) {
                    // Jika JAM -> Tambahkan prefix Shift
                    $row[] = $shiftName . "|" . $outValue;
                } else {
                    // Jika TEKS (Libur/Alpa/Ijin) -> Tampilkan teks murni
                    $row[] = $outValue;
                }
            } else {
                $row[] = $outValue; // Null/Kosong
            }

            // Lanjut ke tanggal berikutnya
            $currentDate->add($interval);
        }
    
        $Data[] = $row;
    }

    // 5. Output JSON ke DataTables
    $Output = array(
        "draw"            => $Draw,
        "recordsTotal"    => $Query->num_rows(),
        "recordsFiltered" => $Query->num_rows(),
        "data"            => $Data
    );

    echo json_encode($Output);
    exit();
  }

  public function presensi_list_OLD2()
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

    //$Sql            = "EXEC dbo.GetPresensiReport1209 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation131025 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.GetPresensiReport031125 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    //$Sql            = "EXEC dbo.SalaryCalculation231225 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    
    $Sql            = "EXEC dbo.SalaryCalculation270126 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    $Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID, $ShiftTolerance]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $row   = [];
      $row[] = $No++;
      $row[] = $value->SSN;
      $row[] = $value->UserName;
      $row[] = $value->DEPTNAME;
      $row[] = $value->CD;
      $row[] = $value->HADIR;
      $row[] = $value->SAKIT;
      $row[] = $value->IJIN;
      $row[] = $value->DL;
      //$row[] = $value->ALPA - $value->HOLIDAY;
      $row[] = $value->ALPA;
      $row[] = $value->TLT_LESS10;
      $row[] = $value->TLT_LESS15;
      $row[] = $value->TLT_MORE15;
      $row[] = $value->HOLIDAY;
      $row[] = $value->HOLIDAY_DATE;

      $currentDate = new DateTime($StartDate);
      
      while ($currentDate <= $endDateObj) {
        $timestamp    = $currentDate->getTimestamp();
        $dayName      = date('d', $timestamp);
        $englishMonth = strtoupper(date('F', $timestamp));
        $monthName    = isset($indonesianMonths[$englishMonth]) ? $indonesianMonths[$englishMonth] : $englishMonth;

        $colIn        = $dayName . '_' . $monthName . '_IN';
        $colOut       = $dayName . '_' . $monthName . '_OUT';

        // 1. Ambil Value Mentah dari Database
        $inValue      = isset($value->$colIn) ? $value->$colIn : null;
        $outValue     = isset($value->$colOut) ? $value->$colOut : null;

        // Definisi Status Non-Jam
        $listStatus   = ['ALPA', 'IJIN', 'SAKIT', 'MINGGU', 'CUTI', 'DINAS LUAR'];

        // ---------------------------------------------------------
        // 2. LOGIKA BARU: FILTER DOUBLE SCAN (IN & OUT BERDEKATAN)
        // ---------------------------------------------------------
        if (!empty($inValue) && !empty($outValue)) {
          // Pastikan yang dicek adalah JAM, bukan text status (ALPA, dll)
          if (!in_array($inValue, $listStatus) && !in_array($outValue, $listStatus)) {
              
              $t1 = strtotime($inValue);
              $t2 = strtotime($outValue);

              // Jika konversi waktu sukses DAN selisih < 600 detik
              if ($t1 !== false && $t2 !== false && abs($t2 - $t1) < 600) {
                  $outValue = null; // Paksa OUT jadi kosong
              }
          }
        }
        // ---------------------------------------------------------

        // 3. Ambil ShiftName
        $shiftName    = $this->getShiftName($value->SSN, $currentDate->format('Y-m-d'));

        // 4. Masukkan ke Row (LOGIKA KOLOM IN)
        if (!empty($inValue) && !in_array($inValue, $listStatus)) {
          $row[] = $shiftName . "|" . $inValue;
        } else {
          $row[] = $inValue;
        }

        // 5. Masukkan ke Row (LOGIKA KOLOM OUT)
        // Karena $outValue mungkin sudah di-NULL-kan oleh logika no. 2, maka aman.
        if (!empty($outValue) && !in_array($outValue, $listStatus)) {
          $row[] = $shiftName . "|" . $outValue;
        } else {
          $row[] = $outValue; 
        }

        $currentDate->add($interval);
      }
  
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

  public function presensi_list_OLD()
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

    $Sql            = "EXEC dbo.GetPresensiReport1209 @StartDate = ?, @EndDate = ?, @DeptID = ?, @ShiftTolerance = ?";
    $Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID, $ShiftTolerance]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $row   = [];
      $row[] = $No++;
      $row[] = $value->SSN;
      $row[] = $value->UserName;
      $row[] = $value->DEPTNAME;
      $row[] = $value->CALENDAR_DAYS;
      $row[] = $value->HADIR;
      $row[] = $value->SAKIT;
      $row[] = $value->IJIN;
      $row[] = $value->ALPA - $value->HOLIDAY;
      $row[] = $value->TELAT_LESS15;
      $row[] = $value->TELAT_MORE15;
      $row[] = $value->HOLIDAY;
      $row[] = $value->HOLIDAY_DATE;
  
      // Reset currentDate untuk setiap baris data
      $currentDate = new DateTime($StartDate);
      
      while ($currentDate <= $endDateObj) {
        $dayName      = date('d', $currentDate->getTimestamp());
        
        $englishMonth = strtoupper(date('F', $currentDate->getTimestamp()));
        $monthName    = $indonesianMonths[$englishMonth];

        $colIn        = $dayName . '_' . $monthName . '_IN';
        $colOut       = $dayName . '_' . $monthName . '_OUT';

        $row[]        = isset($value->$colIn) ? $value->$colIn : null;
        $row[]        = isset($value->$colOut) ? $value->$colOut : null;

        $currentDate->add($interval);
      }
  
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

  //SET PEGAWAI NON SHIFT
  public function set_pegawai_nonshift()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Personalia & GA";
			$data['nama_halaman'] 	= "Set Pegawai Non-Shift";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
			$data['perusahaan'] 		= $this->perusahaan->get_details();
      $data['shiftList']      = $this->shift->get_jadwal_shift();

			//ADDING TO LOG
			$log_url 		            = base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/pga/setting_pegawai_nonshift', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function set_pegawai_nonshift_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
		$DeptID 	      = $this->input->post('DeptShow');
    $DeptWhere      = ($DeptID === '' ? NULL : " AND a.DEFAULTDEPTID = '$DeptID' "); 

    $Sql            = "SELECT a.USERID, UPPER(c.ShiftName) AS ShiftName, a.SSN, a.NAME, 
                       UPPER(a.TITLE) AS TITLE,
                       CASE
                          WHEN a.GENDER = 'F' THEN 'PEREMPUAN'
                          WHEN a.GENDER = 'M' THEN 'LAKI-LAKI'
                          ELSE 'UNKNOWN'
                        END AS GENDER,
                       a.DEFAULTDEPTID, b.DEPTNAME, a.OPHONE
                       FROM USERINFO a
                       LEFT JOIN DEPARTMENTS b ON b.DEPTID = a.DEFAULTDEPTID
                       LEFT JOIN ShiftSetting c ON c.ShiftID = a.OPHONE
                       WHERE a.OPHONE <> ''
                       $DeptWhere
                       ORDER BY a.NAME";
    $Query          = $this->Attendance->query($Sql);
    //$Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID, $ShiftTolerance]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->USERID."', '".$value->SSN."', '".$value->NAME."', '".$value->DEFAULTDEPTID."', '".$value->DEPTNAME."', '".$value->OPHONE."'";
      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="openModalEdit('.$Isi.')">Edit</a>
                    </div>
                  </div>
                </div>';
      $row[]  = $value->SSN;
      $row[]  = $value->NAME;
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->GENDER;
      $row[]  = $value->ShiftName;
  
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

  public function update_pegawai_nonshift()
  {
    $this->_validation_non_shift();

    $Nip      = $this->input->post('NipEdit');
    $ShiftID  = $this->input->post('ShiftOperation');

    $Data = array(
      'OPHONE' => $ShiftID == '' ? NULL : $ShiftID
    );

    //echo json_encode(array("status" => "error", "Data" => $Data, "Nip" => $Nip)); exit;

    $Update = $this->Attendance->update('USERINFO', $Data, array('SSN' => $Nip));
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

  public function update_pegawai_nonshift2()
  {
    $this->_validation_non_shift2();

    $Nip      = $this->input->post('NipEdit');
    $ShiftID  = $this->input->post('ShiftOperationEdit');

    $Data = array(
      'OPHONE' => $ShiftID == 'KOSONGKAN' ? NULL : $ShiftID
    );

    //echo json_encode(array("status" => "error", "Data" => $Data, "Nip" => $Nip)); exit;

    $Update = $this->Attendance->update('USERINFO', $Data, array('SSN' => $Nip));
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

  public function get_user_all()
  {
    $Data   = $this->shift->get_user_all();

    echo json_encode($Data);
  }

  public function get_user_bpjs()
  {
    $Data   = $this->shift->get_user_bpjs();

    echo json_encode($Data);
  }

  public function get_user_gapok()
  {
    $Data   = $this->shift->get_user_gapok();

    echo json_encode($Data);
  }

  public function get_user_tunjangan()
  {
    $Data   = $this->shift->get_user_tunjangan();

    echo json_encode($Data);
  }

  private function getShiftName($EmployeeID, $ScheduleDate)
  {
    $Sql  =   "SELECT 
                u.USERID, u.SSN, u.NAME,
                COALESCE(ts.ShiftID, u.OPHONE) AS ShiftID,
                COALESCE(ss.ShiftName, 'Unscheduled') AS ShiftName
              FROM USERINFO u
              LEFT JOIN Trans_ShiftSchedule ts ON ts.EmployeeID = u.SSN AND ts.ScheduleDate = '$ScheduleDate'
              LEFT JOIN ShiftSetting ss ON ss.ShiftID = COALESCE(ts.ShiftID, u.OPHONE)
              WHERE u.SSN = '$EmployeeID'";
    $Query = $this->Attendance->query($Sql);
    $Row   = $Query->row();

    return $Row ? $Row->ShiftName : null;
  }

  private function _validation_non_shift()
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

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_non_shift2()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('ShiftOperationEdit') == '') {
      $data['inputerror'][]   = 'ShiftOperationEdit';
      $data['error_string'][] = 'Shift Operation is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}