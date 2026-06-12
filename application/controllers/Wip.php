<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wip extends CI_Controller
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
    $this->load->model('prosesproduksi_model', 'prosesproduksi');
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
	}

  //CRIMPING
	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Input Crimping PIN";
			$data['icon_halaman'] 	= "icon-airplay";
      $DeptID                 = "1216";
      $data['DeptID']         = $DeptID;
			$data['perusahaan'] 		= $this->perusahaan->get_details();
      $data['DeptList']       = get_department_for_proses_produksi($DeptID);
      $data['LineList']       = get_line_name($DeptID);
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      //echo json_encode($data['DeptList']); exit;

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/ppic/planning/input_crimping_pin', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function wip_crimping_list()
  {
    $Draw           = intval($this->input->get("draw"));
    $start          = intval($this->input->get("start"));
    $length         = intval($this->input->get("length"));

    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    $DeptID         = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetCrimpingPinReport @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $DeptID]);
    $Result         = $Query->result();
    $Data           = [];
    
    // --- VARIABEL BUFFER UNTUK MENYIMPAN HEADER ---
    $currentJobNumber = null;
    $bufPartID        = '';
    $bufJobDate       = '';
    $bufJobQuantity   = '';
    $bufUnitID        = '';
    $bufPartName      = '';

    foreach ($Result as $key => $value) {
      if ($value->JobNumber !== $currentJobNumber) {
        $currentJobNumber = $value->JobNumber;
        
        $bufPartID      = $value->PartID;
        $bufJobDate     = $value->JobDate;
        $bufJobQuantity = $value->JobQuantity;
        $bufUnitID      = $value->UnitID;
        $bufPartName    = $value->PartName;
      }
        
      $UsePartID      = $bufPartID;
      $UseJobDate     = $bufJobDate;
      $UseJobQuantity = $bufJobQuantity;
      $UseUnitID      = $bufUnitID;
      $UsePartName    = addslashes($bufPartName); 
      $UseNoted       = addslashes($value->Noted);
      $Isi            = "'".$value->JobNumber."', '".$UsePartID."', '".$UsePartName."', '".$UseJobDate."', '".$UseJobQuantity."', '".$UseUnitID."',  '".$value->PlanDate."', '".$value->PlanQty."'";
      $IsiEdit        = "'".$value->JobNumber."', '".$UsePartID."', '".$UsePartName."', '".$UseJobDate."', '".$UseJobQuantity."', '".$UseUnitID."',  '".$value->PlanDate."', '".$value->PlanQty."', '".$value->ActualQty."', '".$UseNoted."', '".$value->ActualDate."', '".$value->DownTimeStart."', '".$value->DownTimeEnd."'";
      $IsiHapus       = "'".$value->JobNumber."', '".$value->TransID."'";
      $TambahHtml     = ($value->ActualQty == 0) ? '<a class="dropdown-item" href="#" onclick="tambah('.$IsiEdit.')">Tambah</a>' : "";
      $EditHtml       = ($value->ActualQty > 0) ? '<a class="dropdown-item" href="#" onclick="edit('.$IsiEdit.')">Edit</a>' : "";
      $DeleteHtml     = ($value->ActualQty > 0) ? '<a class="dropdown-item" href="#" onclick="hapus('.$IsiHapus.')">Hapus</a>' : "";
        
      $row    = [];
      $row[]  = $value->NomorUrut;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      '.$TambahHtml.'
                      '.$EditHtml.'
                      '.$DeleteHtml.'
                    </div>
                  </div>
                </div>';
      $row[]  = $value->JobDate;
      $row[]  = $value->JobNumber;
      $row[]  = $value->JobQuantity;
      $row[]  = $value->PartID;
      $row[]  = $value->PartName;
      $row[]  = $value->PlanDate;
      $row[]  = $value->PlanQty; 
      $row[]  = $value->ActualQty;              
      $row[]  = $value->Persentase;
      $row[]  = $value->SisaPlan;              
      $row[]  = $value->Noted;             
      $row[]  = $value->Downtime;             
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;

      $Data[] = $row;
    }

    $Output = array(
      "draw"            => $Draw,
      "recordsTotal"    => $Query->num_rows(),
      "recordsFiltered" => $Query->num_rows(),
      "data"            => $Data
    );

    echo json_encode($Output);
    exit();
  }

  public function wip_crimping_save() 
  {
    $this->_validation_wip_crimping();

    // 1. Tangkap Input Kunci (Primary Keys untuk pengecekan)
    $JobNumber  = $this->input->post('JobNumber');
    $ActualDate = $this->input->post('ActualDate');
    $PartID     = $this->input->post('PartID');
    $PlanDate   = $this->input->post('PlanDate');

    // 2. Tangkap Input Data Nilai
    $JobQuantity = floatval(str_replace('.', '', $this->input->post('JobQuantity')));
    $ActualQty   = floatval(str_replace('.', '', $this->input->post('ActualQty')));
    
    // Hitung Sisa Plan (Logic ini mungkin perlu disesuaikan jika SisaPlan adalah akumulasi, 
    // tapi saya ikuti logika kode asli Anda: JobQty - ActualQty saat ini)
    $SisaPlan    = $JobQuantity - $ActualQty; 

    // Ambil Input Waktu & Keterangan
    $DowntimeStart = $this->input->post('DowntimeStart');
    $DowntimeEnd   = $this->input->post('DowntimeEnd');
    $Keterangan    = $this->input->post('Keterangan');
    $total_menit   = 0;

    // --- VALIDASI TANGGAL & HITUNG MENIT (Sama seperti sebelumnya) ---
    if (!empty($DowntimeStart) && !empty($DowntimeEnd)) {
        $tgl_start = date('Y-m-d', strtotime($DowntimeStart));
        $tgl_end   = date('Y-m-d', strtotime($DowntimeEnd));

        if ($tgl_start !== $tgl_end) {
            echo json_encode(array(
                "status"  => 'error', 
                "message" => 'Error: Tanggal Mulai dan Selesai Downtime harus pada hari yang sama!'
            )); 
            exit;
        }

        $start = strtotime($DowntimeStart);
        $end   = strtotime($DowntimeEnd);
        
        if ($end > $start) {
            $total_menit = ($end - $start) / 60;
        } else {
            echo json_encode(array(
                "status" => 'error', 
                "message" => 'Error: Jam Selesai tidak boleh lebih awal dari Jam Mulai!'
            )); 
            exit;
        }
    }

    $cleanStart = !empty($DowntimeStart) ? str_replace('T', ' ', $DowntimeStart) . ':00' : null;
    $cleanEnd   = !empty($DowntimeEnd)   ? str_replace('T', ' ', $DowntimeEnd) . ':00'   : null;

    // 3. LOGIKA CEK DATA (INSERT vs UPDATE)
    // Sesuai request: Cek JobNumber, PartID, PlanDate, ActualDate
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $this->BJGMAS01->where('PartID', $PartID);
    $this->BJGMAS01->where('PlanDate', $PlanDate);    // Ditambahkan
    $this->BJGMAS01->where('ActualDate', $ActualDate); // Ditambahkan
    $cek_data = $this->BJGMAS01->get('Trans_ProductionPlanDT3');

    $action_status = ''; // Untuk pesan response

    if ($cek_data->num_rows() > 0) {
        // ============================
        // UPDATE MODE
        // ============================
        $UpdateData = array(
            'ActualQty'     => $ActualQty,
            'DownTime'      => $total_menit,
            'DownTimeStart' => $cleanStart,
            'DownTimeEnd'   => $cleanEnd,
            'Noted'         => $Keterangan,
            'UpdatedDate'   => date('Y-m-d H:i:s'), // Update Timestamp
            'UpdatedBy'     => $this->session->userdata('user_id')
        );

        $this->BJGMAS01->where('JobNumber', $JobNumber);
        $this->BJGMAS01->where('PartID', $PartID);
        $this->BJGMAS01->where('PlanDate', $PlanDate);
        $this->BJGMAS01->where('ActualDate', $ActualDate);
        $exec = $this->BJGMAS01->update('Trans_ProductionPlanDT3', $UpdateData);
        
        $action_status = 'diperbarui';

    } else {
        // ============================
        // INSERT MODE
        // ============================
        $InsertData = array(
            'JobNumber'     => $JobNumber,
            'PartID'        => $PartID,
            'PlanDate'      => $PlanDate,
            'ActualDate'    => $ActualDate,
            'ActualQty'     => $ActualQty,
            'DownTime'      => $total_menit,
            'DownTimeStart' => $cleanStart,
            'DownTimeEnd'   => $cleanEnd,
            'Noted'         => $Keterangan,
            'CreatedDate'   => date('Y-m-d H:i:s'),
            'CreatedBy'     => $this->session->userdata('user_id')
        );

        $exec = $this->BJGMAS01->insert('Trans_ProductionPlanDT3', $InsertData);
        
        $action_status = 'disimpan';
    }

    // 4. Update Table Induk (Sisa Plan di Trans_ProductionPlanDT)
    // Dilakukan baik setelah Insert maupun Update agar SisaPlan selalu sinkron
    if ($exec) {
        $updateDataDT = array('SisaPlan' => $SisaPlan);
        
        $this->BJGMAS01->where('JobNumber', $JobNumber);
        $this->BJGMAS01->where('PlanDate', $PlanDate);
        
        // Asumsi: PlanQty di table DT harus cocok dengan JobQuantity yang dikirim
        // (Hati-hati: Jika PlanQty di database decimal 7.000 dan input float 7, bisa miss match di string compare)
        // Sebaiknya gunakan ID unik jika ada, tapi jika harus pakai PlanQty:
        $this->BJGMAS01->where('PlanQty', $JobQuantity); 
        
        $updateParent = $this->BJGMAS01->update('Trans_ProductionPlanDT', $updateDataDT);
        
        if ($updateParent) {
            echo json_encode(array("status" => 'success', 'message' => 'Data berhasil ' . $action_status . ' dan Sisa Plan diperbarui.'));
        } else {
            echo json_encode(array("status" => 'warning', 'message' => 'Data berhasil ' . $action_status . ', tetapi Gagal update Sisa Plan.'));
        }
    } else {
      echo json_encode(array("status" => 'error', 'message' => 'Terjadi kesalahan saat memproses data di database.'));
    }
    
    exit;
  }

  public function wip_crimping_delete()
  {
    // 1. Tangkap Input
    $JobNumber = $this->input->post('JobNumbers');
    $TransID   = $this->input->post('TransIDs');

    // Validasi input
    if (empty($JobNumber) || empty($TransID)) {
      echo json_encode(array("status" => 'error', "message" => 'JobNumber atau TransID tidak valid.'));
      exit;
    }

    // 2. Ambil Data Lama Sebelum Dihapus (PENTING UNTUK UPDATE SISA PLAN)
    $this->BJGMAS01->select('ActualQty, PlanDate');
    $this->BJGMAS01->where('Id', $TransID);
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $oldData = $this->BJGMAS01->get('Trans_ProductionPlanDT3')->row();

    if (!$oldData) {
      echo json_encode(array("status" => 'error', "message" => 'Data tidak ditemukan atau sudah dihapus.'));
      exit;
    }

    $qtyToRestore = $oldData->ActualQty; // Jumlah yang akan dikembalikan ke Sisa Plan
    $planDate     = $oldData->PlanDate;  // Kunci untuk mencari parent row

    // 3. Lakukan Penghapusan
    $this->BJGMAS01->where('Id', $TransID);
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $delete = $this->BJGMAS01->delete('Trans_ProductionPlanDT3');

    if ($delete) {
        // 4. Update Table Induk (Kembalikan Sisa Plan)
        // Logika: SisaPlan bertambah kembali sejumlah ActualQty yang dihapus
        // set('SisaPlan', 'SisaPlan + '.$qty, FALSE) -> FALSE agar tidak di-escape sebagai string
        $this->BJGMAS01->set('SisaPlan', 'SisaPlan + ' . $qtyToRestore, FALSE);
        
        $this->BJGMAS01->where('JobNumber', $JobNumber);
        $this->BJGMAS01->where('PlanDate', $planDate);
        $updateParent = $this->BJGMAS01->update('Trans_ProductionPlanDT');

        if ($updateParent) {
            echo json_encode(array(
                "status"  => 'success', 
                "message" => 'Data berhasil dihapus dan Sisa Plan telah diperbarui.'
            ));
        } else {
            echo json_encode(array(
                "status"  => 'warning', 
                "message" => 'Data berhasil dihapus, tetapi Gagal mengupdate Sisa Plan.'
            ));
        }
    } else {
      echo json_encode(array(
        "status"  => 'error', 
        "message" => 'Gagal menghapus data dari database.'
      ));
    }
    
    exit;
  }

  //CRIMPING WIP LINE
  public function crimping_line()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Input Crimping WIP Line";
			$data['icon_halaman'] 	= "icon-airplay";
      $DeptID                 = "1216";
      $data['DeptID']         = $DeptID;
			$data['perusahaan'] 		= $this->perusahaan->get_details();
      $data['DeptList']       = get_department_for_proses_produksi($DeptID);
      $data['LineList']       = get_line_name($DeptID);
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      //echo json_encode($data['DeptList']); exit;

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/ppic/planning/input_crimping_line', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function crimping_line_list()
  {
    $Draw           = intval($this->input->get("draw"));
    $start          = intval($this->input->get("start"));
    $length         = intval($this->input->get("length"));

    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    $DeptID         = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetCrimpingWipLineReport @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $DeptID]);
    $Result         = $Query->result();
    $Data           = [];
    
    // --- VARIABEL BUFFER UNTUK MENYIMPAN HEADER ---
    $currentJobNumber = null;
    $bufPartID        = '';
    $bufJobDate       = '';
    $bufJobQuantity   = '';
    $bufUnitID        = '';
    $bufPartName      = '';

    foreach ($Result as $key => $value) {
      if ($value->JobNumber !== $currentJobNumber) {
        $currentJobNumber = $value->JobNumber;
        
        $bufPartID      = $value->PartID;
        $bufJobDate     = $value->JobDate;
        $bufJobQuantity = $value->JobQuantity;
        $bufUnitID      = $value->UnitID;
        $bufPartName    = $value->PartName;
      }
        
      $UsePartID      = $bufPartID;
      $UseJobDate     = $bufJobDate;
      $UseJobQuantity = $bufJobQuantity;
      $UseUnitID      = $bufUnitID;
      $UsePartName    = addslashes($bufPartName); 
      $UseNoted       = addslashes($value->Noted);
      $Isi            = "'".$value->JobNumber."', '".$UsePartID."', '".$UsePartName."', '".$UseJobDate."', '".$UseJobQuantity."', '".$UseUnitID."',  '".$value->PlanDate."', '".$value->PlanQty."'";
      $IsiEdit        = "'".$value->JobNumber."', '".$UsePartID."', '".$UsePartName."', '".$UseJobDate."', '".$UseJobQuantity."', '".$UseUnitID."',  '".$value->PlanDate."', '".$value->PlanQty."', '".$value->ActualQty."', '".$UseNoted."', '".$value->ActualDate."', '".$value->DownTimeStart."', '".$value->DownTimeEnd."'";
      $IsiHapus       = "'".$value->JobNumber."', '".$value->TransID."'";
      $TambahHtml     = ($value->ActualQty == 0) ? '<a class="dropdown-item" href="#" onclick="tambah('.$IsiEdit.')">Tambah</a>' : "";
      $EditHtml       = ($value->ActualQty > 0) ? '<a class="dropdown-item" href="#" onclick="edit('.$IsiEdit.')">Edit</a>' : "";
      $DeleteHtml     = ($value->ActualQty > 0) ? '<a class="dropdown-item" href="#" onclick="hapus('.$IsiHapus.')">Hapus</a>' : "";
        
      $row    = [];
      $row[]  = $value->NomorUrut;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      '.$TambahHtml.'
                      '.$EditHtml.'
                      '.$DeleteHtml.'
                    </div>
                  </div>
                </div>';
      $row[]  = $value->JobDate;
      $row[]  = $value->JobNumber;
      $row[]  = $value->JobQuantity;
      $row[]  = $value->PartID;
      $row[]  = $value->PartName;
      $row[]  = $value->LineName;
      $row[]  = $value->PlanDate;
      $row[]  = $value->PlanQty; 
      $row[]  = $value->ActualQty;              
      $row[]  = $value->Persentase;
      $row[]  = $value->SisaPlan;              
      $row[]  = $value->Noted;             
      $row[]  = $value->Downtime;             
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;

      $Data[] = $row;
    }

    $Output = array(
      "draw"            => $Draw,
      "recordsTotal"    => $Query->num_rows(),
      "recordsFiltered" => $Query->num_rows(),
      "data"            => $Data
    );

    echo json_encode($Output);
    exit();
  }

  public function crimping_line_save()
  {
    $this->_validation_wip_crimping();

    // 1. Tangkap Input Kunci (Primary Keys untuk pengecekan)
    $JobNumber  = $this->input->post('JobNumber');
    $ActualDate = $this->input->post('ActualDate');
    $PartID     = $this->input->post('PartID');
    $PlanDate   = $this->input->post('PlanDate');

    // 2. Tangkap Input Data Nilai
    $JobQuantity = floatval(str_replace('.', '', $this->input->post('JobQuantity')));
    $ActualQty   = floatval(str_replace('.', '', $this->input->post('ActualQty')));
    
    // Hitung Sisa Plan (Logic ini mungkin perlu disesuaikan jika SisaPlan adalah akumulasi, 
    // tapi saya ikuti logika kode asli Anda: JobQty - ActualQty saat ini)
    $SisaPlan    = $JobQuantity - $ActualQty; 

    // Ambil Input Waktu & Keterangan
    $DowntimeStart = $this->input->post('DowntimeStart');
    $DowntimeEnd   = $this->input->post('DowntimeEnd');
    $Keterangan    = $this->input->post('Keterangan');
    $total_menit   = 0;

    // --- VALIDASI TANGGAL & HITUNG MENIT (Sama seperti sebelumnya) ---
    if (!empty($DowntimeStart) && !empty($DowntimeEnd)) {
        $tgl_start = date('Y-m-d', strtotime($DowntimeStart));
        $tgl_end   = date('Y-m-d', strtotime($DowntimeEnd));

        if ($tgl_start !== $tgl_end) {
            echo json_encode(array(
                "status"  => 'error', 
                "message" => 'Error: Tanggal Mulai dan Selesai Downtime harus pada hari yang sama!'
            )); 
            exit;
        }

        $start = strtotime($DowntimeStart);
        $end   = strtotime($DowntimeEnd);
        
        if ($end > $start) {
          $total_menit = ($end - $start) / 60;
        } else {
          echo json_encode(array(
            "status" => 'error', 
            "message" => 'Error: Jam Selesai tidak boleh lebih awal dari Jam Mulai!'
          )); 
          exit;
        }
    }

    $cleanStart = !empty($DowntimeStart) ? str_replace('T', ' ', $DowntimeStart) . ':00' : null;
    $cleanEnd   = !empty($DowntimeEnd)   ? str_replace('T', ' ', $DowntimeEnd) . ':00'   : null;

    // 3. LOGIKA CEK DATA (INSERT vs UPDATE)
    // Sesuai request: Cek JobNumber, PartID, PlanDate, ActualDate
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $this->BJGMAS01->where('PartID', $PartID);
    $this->BJGMAS01->where('PlanDate', $PlanDate);    // Ditambahkan
    $this->BJGMAS01->where('ActualDate', $ActualDate); // Ditambahkan
    $cek_data      = $this->BJGMAS01->get('Trans_ProductionPlanDT4');

    $action_status = ''; // Untuk pesan response

    if ($cek_data->num_rows() > 0) {
      // ============================
      // UPDATE MODE
      // ============================
      $UpdateData = array(
        'ActualQty'     => $ActualQty,
        'DownTime'      => $total_menit,
        'DownTimeStart' => $cleanStart,
        'DownTimeEnd'   => $cleanEnd,
        'Noted'         => $Keterangan,
        'UpdatedDate'   => date('Y-m-d H:i:s'), // Update Timestamp
        'UpdatedBy'     => $this->session->userdata('user_id')
      );

      $this->BJGMAS01->where('JobNumber', $JobNumber);
      $this->BJGMAS01->where('PartID', $PartID);
      $this->BJGMAS01->where('PlanDate', $PlanDate);
      $this->BJGMAS01->where('ActualDate', $ActualDate);
      $exec = $this->BJGMAS01->update('Trans_ProductionPlanDT4', $UpdateData);
      
      $action_status = 'diperbarui';

    } else {
      // ============================
      // INSERT MODE
      // ============================
      $InsertData = array(
        'JobNumber'     => $JobNumber,
        'PartID'        => $PartID,
        'PlanDate'      => $PlanDate,
        'ActualDate'    => $ActualDate,
        'ActualQty'     => $ActualQty,
        'DownTime'      => $total_menit,
        'DownTimeStart' => $cleanStart,
        'DownTimeEnd'   => $cleanEnd,
        'Noted'         => $Keterangan,
        'CreatedDate'   => date('Y-m-d H:i:s'),
        'CreatedBy'     => $this->session->userdata('user_id')
      );

      $exec = $this->BJGMAS01->insert('Trans_ProductionPlanDT4', $InsertData);
      
      $action_status = 'disimpan';
    }

    // 4. Update Table Induk (Sisa Plan di Trans_ProductionPlanDT)
    // Dilakukan baik setelah Insert maupun Update agar SisaPlan selalu sinkron
    if ($exec) {
        $updateDataDT = array('SisaPlan' => $SisaPlan);
        
        $this->BJGMAS01->where('JobNumber', $JobNumber);
        $this->BJGMAS01->where('PlanDate', $PlanDate);
        
        // Asumsi: PlanQty di table DT harus cocok dengan JobQuantity yang dikirim
        // (Hati-hati: Jika PlanQty di database decimal 7.000 dan input float 7, bisa miss match di string compare)
        // Sebaiknya gunakan ID unik jika ada, tapi jika harus pakai PlanQty:
        $this->BJGMAS01->where('PlanQty', $JobQuantity); 
        
        $updateParent = $this->BJGMAS01->update('Trans_ProductionPlanDT2', $updateDataDT);
        
        if ($updateParent) {
          echo json_encode(array("status" => 'success', 'message' => 'Data berhasil ' . $action_status . ' dan Sisa Plan diperbarui.'));
        } else {
          echo json_encode(array("status" => 'warning', 'message' => 'Data berhasil ' . $action_status . ', tetapi Gagal update Sisa Plan.'));
        }
    } else {
      echo json_encode(array("status" => 'error', 'message' => 'Terjadi kesalahan saat memproses data di database.'));
    }
    
    exit;
  }
  public function crimping_line_delete()
  {
    // 1. Tangkap Input
    $JobNumber = $this->input->post('JobNumbers');
    $TransID   = $this->input->post('TransIDs');

    // Validasi input
    if (empty($JobNumber) || empty($TransID)) {
      echo json_encode(array("status" => 'error', "message" => 'JobNumber atau TransID tidak valid.'));
      exit;
    }

    // 2. Ambil Data Lama Sebelum Dihapus (PENTING UNTUK UPDATE SISA PLAN)
    $this->BJGMAS01->select('ActualQty, PlanDate');
    $this->BJGMAS01->where('Id', $TransID);
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $oldData = $this->BJGMAS01->get('Trans_ProductionPlanDT4')->row();

    if (!$oldData) {
      echo json_encode(array("status" => 'error', "message" => 'Data tidak ditemukan atau sudah dihapus.'));
      exit;
    }

    $qtyToRestore = $oldData->ActualQty; // Jumlah yang akan dikembalikan ke Sisa Plan
    $planDate     = $oldData->PlanDate;  // Kunci untuk mencari parent row

    // 3. Lakukan Penghapusan
    $this->BJGMAS01->where('Id', $TransID);
    $this->BJGMAS01->where('JobNumber', $JobNumber);
    $delete = $this->BJGMAS01->delete('Trans_ProductionPlanDT4');

    if ($delete) {
        // 4. Update Table Induk (Kembalikan Sisa Plan)
        // Logika: SisaPlan bertambah kembali sejumlah ActualQty yang dihapus
        // set('SisaPlan', 'SisaPlan + '.$qty, FALSE) -> FALSE agar tidak di-escape sebagai string
        $this->BJGMAS01->set('SisaPlan', 'SisaPlan + ' . $qtyToRestore, FALSE);
        
        $this->BJGMAS01->where('JobNumber', $JobNumber);
        $this->BJGMAS01->where('PlanDate', $planDate);
        $updateParent = $this->BJGMAS01->update('Trans_ProductionPlanDT2');

        if ($updateParent) {
          echo json_encode(array(
            "status"  => 'success', 
            "message" => 'Data berhasil dihapus dan Sisa Plan telah diperbarui.'
          ));
        } else {
          echo json_encode(array(
            "status"  => 'warning', 
            "message" => 'Data berhasil dihapus, tetapi Gagal mengupdate Sisa Plan.'
          ));
        }
    } else {
      echo json_encode(array(
        "status"  => 'error', 
        "message" => 'Gagal menghapus data dari database.'
      ));
    }
    
    exit;
  }

  private function _validation_wip_crimping()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('ActualQty') == '') {
      $data['inputerror'][]   = 'ActualQty';
      $data['error_string'][] = 'Actual Qty is required';
      $data['status']         = FALSE;
    }
    
    if ($this->input->post('ActualDate') == '') {
      $data['inputerror'][]   = 'ActualDate';
      $data['error_string'][] = 'Actual Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('DowntimeStart') == '') {
      $data['inputerror'][]   = 'DowntimeStart';
      $data['error_string'][] = 'Downtime Start is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('DowntimeEnd') == '') {
      $data['inputerror'][]   = 'DowntimeEnd';
      $data['error_string'][] = 'Downtime End is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Keterangan') == '') {
      $data['inputerror'][]   = 'Keterangan';
      $data['error_string'][] = 'Keterangan is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}