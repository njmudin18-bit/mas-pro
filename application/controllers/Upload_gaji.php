<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload_gaji extends CI_Controller
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
    $this->load->model('periodegaji_model', 'periodegaji');

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
			$data['nama_halaman'] 	= "Upload Gaji";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$Periode                = $this->periodegaji->get_all_data();
      $data['PeriodeList'] 		= $Periode;
      $data['SelectedPeriode']= $Periode[0];
      //echo json_encode($SelectedPeriode); exit;

			//ADDING TO LOG
			$log_url 		            = base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/pga/upload_gaji', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function gaji_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('StartDate');
		$EndDate 	      = $this->input->post('EndDate');
		$DeptID 	      = $this->input->post('DeptID');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetGaji @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query          = $this->Attendance->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {

      $row   = [];
      $row[] = $No;
      // $row[] = '';
      $row[] = $value->SSN;
      $row[] = $value->NAME;
      $row[] = $value->STATUS;
      $row[] = $value->DEPTNAME;
      $row[] = $value->StartDate;
      $row[] = $value->EndDate;
      $row[] = $value->Sunday;
      $row[] = $value->Cycle;
      $row[] = $value->HK;
      $row[] = $value->HD;
      $row[] = $value->Sakit;
      $row[] = $value->Ijin;
      $row[] = $value->Alpa;
      $row[] = $value->TelatLess10; //TELAT UNDER 10
      $row[] = $value->TelatMore10;
      $row[] = $value->TelatMore15;
      $row[] = $value->OTMinggu; //OT MINGGU
      $row[] = $value->Libur;
      $row[] = $value->GajiPokok;
      $row[] = $value->Pembagi;
      $row[] = $value->Upah;
      $row[] = $value->UangMakan;
      $row[] = $value->UangTunjHadir;
      $row[] = $value->UangShift;
      $row[] = $value->UangLiburLembur;
      $row[] = $value->JamLembur;
      //$row[] = $value->TotalUpah;
      $row[] = $value->TotalTunjMakan;  //aman
      $row[] = $value->TotalTunjHadir;  //
      $row[] = $value->TotalTunjLembur;
      $row[] = $value->TotalTunjShift;
      $row[] = $value->TotalLembur;
      $row[] = $value->PotBPJS;
      $row[] = $value->TotalGaji;
      $row[] = $value->TunjLainnya;
      $row[] = $value->PotHutang;
      $row[] = $value->GajiBersih;
      $row[] = $value->Keterangan;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
      $row[] = $value->KirimEmail;
      $row[] = $value->KirimEmailOn;
      
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

  public function proses_upload_gaji()
  {
    $jsonData = $this->input->raw_input_stream;
    $dataArr  = json_decode($jsonData, true);

    if(!$dataArr || !is_array($dataArr)) {
      echo json_encode([
        'status_code' => 400,
        'status'      => 'error',
        'message'     => 'Data tidak valid'
      ]);

      return;
    }

    $result           = [];
    $deleteConditions = [];

    foreach($dataArr as $item) {
      $EmployeeID = isset($item['NIP']) ? $item['NIP'] : null;
      $DeptID     = isset($item['DEPTID']) ? $item['DEPTID'] : null;
      $StartDate  = isset($item['START DATE']) ? $item['START DATE'] : null;
      $EndDate    = isset($item['END DATE']) ? $item['END DATE'] : null;
      $Cycle      = isset($item['CYCLE']) ? floatval($item['CYCLE']) : null;
      $Sunday     = isset($item['SUNDAY']) ? floatval($item['SUNDAY']) : null;
      $Keterangan = isset($item['KETERANGAN']) ? $item['KETERANGAN'] : null;

      // Simpan kondisi untuk delete dulu
      if ($EmployeeID && $DeptID && $StartDate && $EndDate && $Cycle) {
        $deleteConditions[] = [
          'EmployeeID' => $EmployeeID,
          'DeptID'     => $DeptID,
          'StartDate'  => $StartDate,
          'EndDate'    => $EndDate,
          'Cycle'      => $Cycle
        ];
      }

      // Data yang akan diinsert
      $ArrayData = [
        'EmployeeID'      => $EmployeeID,
        'DeptID'          => $DeptID,
        'StartDate'       => $StartDate,
        'EndDate'         => $EndDate,
        'Sunday'          => $Sunday,
        'Cycle'           => $Cycle,
        'HK'              => isset($item['HK']) ? floatval($item['HK']) : null,
        'HD'              => isset($item['HD']) ? floatval($item['HD']) : null,
        'Sakit'           => isset($item['SAKIT']) ? floatval($item['SAKIT']) : null,
        'Ijin'            => isset($item['IJIN']) ? floatval($item['IJIN']) : null,
        'Alpa'            => isset($item['ALPA']) ? floatval($item['ALPA']) : null,
        'TelatLess10'     => isset($item['TELAT<10']) ? floatval($item['TELAT<10']) : null,
        'TelatMore10'     => isset($item['TELAT>10']) ? floatval($item['TELAT>10']) : null,
        'TelatMore15'     => isset($item['TELAT>15']) ? floatval($item['TELAT>15']) : null,
        'Libur'           => isset($item['HOLIDAY']) ? floatval($item['HOLIDAY']) : null,
        'OTMinggu'        => isset($item['OT MINGGU']) ? floatval($item['OT MINGGU']) : null,
        'GajiPokok'       => isset($item['GAJI POKOK']) ? floatval($item['GAJI POKOK']) : null,
        'Pembagi'         => isset($item['PEMBAGI']) ? floatval($item['PEMBAGI']) : null,
        'Upah'            => isset($item['UPAH']) ? floatval($item['UPAH']) : null,
        'UangMakan'       => isset($item['UANG MAKAN']) ? floatval($item['UANG MAKAN']) : null,
        'UangTunjHadir'   => isset($item['UANG TUNJ. HADIR']) ? floatval($item['UANG TUNJ. HADIR']) : null,
        'UangShift'       => isset($item['UANG SHIFT']) ? floatval($item['UANG SHIFT']) : null,
        'UangLiburLembur' => isset($item['UANG LIBUR LEMBUR']) ? floatval($item['UANG LIBUR LEMBUR']) : null,
        'JamLembur'       => isset($item['JAM LEMBUR']) ? floatval($item['JAM LEMBUR']) : null,
        //'TotalUpah'       => isset($item['TOTAL UPAH']) ? floatval($item['TOTAL UPAH']) : null,
        'TotalTunjMakan'  => isset($item['TOTAL TUNJ. MAKAN']) ? floatval($item['TOTAL TUNJ. MAKAN']) : null,
        'TotalTunjHadir'  => isset($item['TOTAL TUNJ. HADIR']) ? floatval($item['TOTAL TUNJ. HADIR']) : null,
        'TotalTunjLembur' => isset($item['TOTAL TUNJ. LEMBUR']) ? floatval($item['TOTAL TUNJ. LEMBUR']) : null,
        'TotalTunjShift'  => isset($item['TOTAL TUNJ. SHIFT']) ? floatval($item['TOTAL TUNJ. SHIFT']) : null,
        'TotalLembur'     => isset($item['TOTAL LEMBUR']) ? floatval($item['TOTAL LEMBUR']) : null,
        'PotBPJS'         => isset($item['POT. BPJS']) ? floatval($item['POT. BPJS']) : null,
        //'TotalGaji'       => isset($item['TOTAL GAJI']) ? floatval($item['TOTAL GAJI']) : null,
        'TotalGaji'       => isset($item['TOTAL UPAH']) ? floatval($item['TOTAL UPAH']) : null,
        'TunjLainnya'     => isset($item['TUNJ. LAINNYA']) ? floatval($item['TUNJ. LAINNYA']) : null,
        'PotHutang'       => isset($item['POT. HUTANG']) ? floatval($item['POT. HUTANG']) : null,
        'GajiBersih'      => isset($item['GAJI BERSIH']) ? floatval($item['GAJI BERSIH']) : null,
        'Keterangan'      => $Keterangan,
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_id')
      ];

      $result[] = $ArrayData;
    }


    echo json_encode(array('status_code' => 400, 'status' => 'error', "data" => $result)); exit;

    // Jalankan transaksi
    $this->Attendance->trans_start();
    foreach ($deleteConditions as $cond) {
      $this->Attendance->where('EmployeeID', $cond['EmployeeID'])
                        ->where('DeptID', $cond['DeptID'])
                        ->where('StartDate', $cond['StartDate'])
                        ->where('EndDate', $cond['EndDate'])
                        ->where('Cycle', $cond['Cycle'])
                        ->delete('Trans_Gaji');
    }

    if (!empty($result)) {
      $this->Attendance->insert_batch('Trans_Gaji', $result);
    }
    $this->Attendance->trans_complete();

    // ✅ Cek hasil
    if ($this->Attendance->trans_status() === FALSE) {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Gagal menyimpan data'
      ]);
    } else {
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data berhasil diperbarui',
        'deleted'     => count($deleteConditions),
        'inserted'    => count($result)
      ]);
    }
  }
}