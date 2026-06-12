<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Planning extends CI_Controller
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
    $this->load->model('machines_model', 'machines');
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
			$data['nama_halaman'] 	= "Planning Produksi Crimping";
			$data['icon_halaman'] 	= "icon-airplay";
      $DeptID                 = "1216";
      $data['DeptID']         = $DeptID;
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['MesinList'] 		  = $this->machines->get_all_data($DeptID);
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
			$this->load->view('adminx/ppic/planning/index', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function planning_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$DeptID 	      = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetProductionPlan @StartDate = ?, @EndDate = ?, @DeptID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Id."', '".$value->JobNumber."'";
      $Isi2   = "'".$value->Id."'";

      $row    = [];
      $row[]  = $value->NomorUrut;
      $row[]  = ($value->NomorUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapusAll('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>' : '';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->JobDate;
      $row[]  = $value->JobNumber;
      $row[]  = $value->JobQuantity;
      $row[]  = $value->PartID;
      $row[]  = $value->PartName;
      $row[]  = $value->PlanDate;
      $row[]  = $value->PlanQty; 
      $row[]  = $value->ActualQty;               //10
      $row[]  = $value->Persentase;
      $row[]  = $value->SisaPlan;
      $row[]  = ""; //KOSONGKAN
      $row[]  = $value->LineName;
      $row[]  = $value->PlanDate2;
      $row[]  = $value->PlanQty2;
      $row[]  = $value->ActualQty2;
      $row[]  = $value->Persentase2;
      $row[]  = $value->SisaPlan2;
      $row[]  = $value->Noted;
      $row[]  = $value->CreatedDate;
  
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

  public function planning_edit()
  {
    $IdHeader   = $this->input->post('IdHeader');
    $JobNumber  = $this->input->post('JobNumber');
    $this->BJGMAS01->select("
      a.Id, a.JobNumber, a.JobDate, a.UnitID, a.PartID, b.PartName,
      FORMAT(a.JobQuantity, 'N0', 'id-ID') AS JobQuantity,
      FORMAT(a.JobDate, 'yyyy-MM') AS JobPeriode,
      a.LineID, d.LineName, a.MachineID,
      a.DeptID, c.DEPTNAME, a.Noted
    ");
    $this->BJGMAS01->from('Trans_ProductionPlanHD a');
    $this->BJGMAS01->join('Ms_Part b', 'b.PartID = a.PartID', 'left');
    $this->BJGMAS01->join('DEPARTMENTS c', 'c.DEPTID = a.DeptID', 'left');
    $this->BJGMAS01->join('Trans_ProductionProcessDT d', 'd.Id = a.LineID', 'left');
    $this->BJGMAS01->where('a.Id', $IdHeader);
    $this->BJGMAS01->where('a.JobNumber', $JobNumber);

    $Query = $this->BJGMAS01->get();

    if ($Query->num_rows() > 0) {
      $this->BJGMAS01->select("
        Id,
        JobNumber,
        PlanDate,
        FORMAT(PlanQty, 'N0') AS PlanQty,
        FORMAT(Uph, 'N0') AS Uph,
        FORMAT(Hours, 'N2') AS Hours,
        FORMAT(SisaPlan, 'N0') AS SisaPlan
      ", false);
      $this->BJGMAS01->from("Trans_ProductionPlanDT");
      $this->BJGMAS01->where("JobNumber", $JobNumber);
      $this->BJGMAS01->order_by("PlanDate", 'ASC');
      $QuerySec   = $this->BJGMAS01->get();

      $this->BJGMAS01->select("
        Id,
        JobNumber,
        PlanDate,
        FORMAT(PlanQty, 'N0') AS PlanQty,
        FORMAT(Uph, 'N0') AS Uph,
        FORMAT(Hours, 'N2') AS Hours,
        FORMAT(SisaPlan, 'N0') AS SisaPlan
      ", false);
      $this->BJGMAS01->from("Trans_ProductionPlanDT2");
      $this->BJGMAS01->where("JobNumber", $JobNumber);
      $this->BJGMAS01->order_by("PlanDate", 'ASC');
      $QueryTrd   = $this->BJGMAS01->get();

      $FirstData  = $Query->row();
      $SecondData = $QuerySec->result();
      $ThirdData  = $QueryTrd->result();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data ditemukan.",
          "first"       => $FirstData,
          "second"      => $SecondData,
          "third"       => $ThirdData
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data tidak ditemukan.",
          "first"       => array(),
          "second"      => array(),
          "third"       => array(),
        )
      );
    }
  }

  public function get_job_number()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search     = strtoupper(trim($this->input->post('search')));
    $Periode    = $this->input->post('Periode');
    $TableName  = 'Trans_Job'.$Periode;
    //echo $TableName; exit;

    $Sql      = "SELECT a.NoBukti, CAST(a.Tgl AS DATE) AS Tgl, a.PartID, a.Keterangan,
                 FORMAT(a.QtyOrder, 'N0', 'id-ID') AS QtyOrder, b.PartName, a.UnitID
                 FROM $TableName a
                 LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                 WHERE a.NoBukti LIKE '%$Search%'";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'id'          => $row->NoBukti,
        'name'        => $row->NoBukti,
        'PartID'      => $row->PartID,
        'PartName'    => $row->PartName,
        'Tgl'         => $row->Tgl,
        'QtyOrder'    => $row->QtyOrder,
        'Keterangan'  => $row->Keterangan,
        'UnitID'      => $row->UnitID
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($Data);
  }

  public function planning_save() 
  {
    $this->_validation_planning();

    // --- AMBIL INPUT DATA ---
    $JobQuantityStr = $this->input->post('JobQuantity');
    $JobQuantity    = floatval(str_replace('.', '', $JobQuantityStr));
    
    // Data untuk Detail 1
    $PlanQtyArray   = $this->input->post('PlanQty');
    $PlanDates      = $this->input->post('PlanDate');
    $UphArray       = $this->input->post('Uph');
    $HoursArray     = $this->input->post('Jam');
    $SisaPlanArray  = $this->input->post('SisaPlan');

    // Data untuk Detail 2
    $PlanQty2Array  = $this->input->post('PlanQty2');
    $PlanDates2     = $this->input->post('PlanDate2');
    $UphArray2      = $this->input->post('Uph2');
    $HoursArray2    = $this->input->post('Jam2');
    $SisaPlanArray2 = $this->input->post('SisaPlan2');
    
    $JobNumber      = $this->input->post('JobList');
    $PartID         = $this->input->post('PartID');
    $JobDate        = $this->input->post('JobDate');
    $UnitID         = $this->input->post('UnitID');
    $Noted          = ucfirst($this->input->post('Noted'));
    $DeptID         = $this->input->post('DeptID');
    $LineName       = $this->input->post('LineName'); 
    $MachineID      = $this->input->post('Mesin'); 

    // [LOGIC 1] CEK HEADER: Jika JobNumber sudah ada, STOP
    $cek_header = $this->BJGMAS01->get_where('Trans_ProductionPlanHD', ['JobNumber' => $JobNumber]);
    if ($cek_header->num_rows() > 0) {

      $this->responseJSON(400, 'error', 'Gagal: Job Number ' . $JobNumber . ' sudah terdaftar.');
      return; 
    }

    // --- MENYUSUN DATA HEADER ---
    $DataHeader = array(
      'JobNumber'   => $JobNumber,
      'JobDate'     => $JobDate,
      'JobQuantity' => $JobQuantity,
      'UnitID'      => $UnitID,
      'PartID'      => $PartID,
      'DeptID'      => $DeptID, 
      'LineID'      => $LineName, 
      'MachineID'   => $MachineID, 
      'Noted'       => $Noted,
      'CreatedDate' => date('Y-m-d H:i:s'),
      'CreatedBy'   => $this->session->userdata('user_id')
    );

    $this->BJGMAS01->trans_begin();
    try {
      // 1. Simpan Header
      $this->BJGMAS01->insert('Trans_ProductionPlanHD', $DataHeader); 
      
      // 2. Proses Detail 1 (Trans_ProductionPlanDT)
      $DataDetail1 = array();
      if (is_array($PlanDates)) {
        foreach ($PlanDates as $key => $date) {
          if (!empty($date) && isset($PlanQtyArray[$key]) && $PlanQtyArray[$key] !== '') {
            $qty            = floatval(str_replace('.', '', $PlanQtyArray[$key]));
            $uph            = floatval(str_replace('.', '', $UphArray[$key]));
            $jam            = floatval(str_replace(',', '.', $HoursArray[$key]));
            $sisa           = floatval(str_replace('.', '', $SisaPlanArray[$key]));
            $DataDetail1[]  = array(
              'JobNumber'   => $JobNumber,
              'PlanDate'    => $date,
              'PlanQty'     => $qty,
              'Uph'         => $uph,
              'Hours'       => $jam,
              'SisaPlan'    => $sisa,
              'CreatedDate' => date('Y-m-d H:i:s'),
              'CreatedBy'   => $this->session->userdata('user_id')
            );
          }
        }
      }

      if (!empty($DataDetail1)) {
        $this->BJGMAS01->insert_batch('Trans_ProductionPlanDT', $DataDetail1);
      }

      // 3. Proses Detail 2 (Trans_ProductionPlanDT2)
      $DataDetail2 = array();
      if (is_array($PlanDates2)) {
        foreach ($PlanDates2 as $key => $date2) {
          if (!empty($date2) && isset($PlanQty2Array[$key]) && $PlanQty2Array[$key] !== '') {
            $qty2   = floatval(str_replace('.', '', $PlanQty2Array[$key]));
            $uph2   = floatval(str_replace('.', '', $UphArray2[$key]));
            $jam2   = floatval(str_replace(',', '.', $HoursArray2[$key]));
            $sisa2  = floatval(str_replace('.', '', $SisaPlanArray2[$key]));
            $DataDetail2[]  = array(
              'JobNumber'   => $JobNumber,
              'PlanDate'    => $date2,
              'PlanQty'     => $qty2,
              'Uph'         => $uph2,
              'Hours'       => $jam2,
              'SisaPlan'    => $sisa2,
              'CreatedDate' => date('Y-m-d H:i:s'),
              'CreatedBy'   => $this->session->userdata('user_id')
            );
          }
        }
      }

      //echo json_encode(array("status" => "error", "HD" => $DataHeader, "DT1" => $DataDetail1, "DT2" => $DataDetail2)); exit;

      if (!empty($DataDetail2)) {
        $this->BJGMAS01->insert_batch('Trans_ProductionPlanDT2', $DataDetail2);
      }

      // Final Check Transaction
      if ($this->BJGMAS01->trans_status() === FALSE) {
        $this->BJGMAS01->trans_rollback();
        $this->responseJSON(500, 'error', 'Gagal menyimpan data ke database.');
      } else {
        $this->BJGMAS01->trans_commit();
        $this->responseJSON(200, 'success', 'Data Planning (Header & 2 Detail) berhasil disimpan.');
      }
    } catch (Exception $e) {
      $this->BJGMAS01->trans_rollback();
      $this->responseJSON(500, 'error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
  }

  public function planning_update() 
  {
    $this->_validation_planning();

    $SecondDataInsert  = array();
    $SecondDataUpdate  = array();
    $ThirdDataInsert   = array();
    $ThirdDataUpdate   = array();
    $JobQuantityStr    = $this->input->post('JobQuantity');
    $JobQuantity       = floatval(str_replace('.', '', $JobQuantityStr));
    $PlanQtyArray      = $this->input->post('PlanQty');
    
    $IdHeader          = $this->input->post('kodeFirst');
    $IdDetail          = $this->input->post('kodeSecond');
    $IdDetail2         = $this->input->post('kodeThird');
    $JobNumber         = $this->input->post('JobList');
    $PartID            = $this->input->post('PartID');
    $JobDate           = $this->input->post('JobDate');
    $UnitID            = $this->input->post('UnitID');
    $Noted             = ucfirst($this->input->post('Noted'));
    $DeptID            = $this->input->post('DeptID');
    $LineName          = $this->input->post('LineName');
    $MachineID         = $this->input->post('Mesin'); 

    $PlanDate          = $this->input->post('PlanDate');
    $PlanQty           = $this->input->post('PlanQty');
    $UphArray          = $this->input->post('Uph');
    $HoursArray        = $this->input->post('Jam');
    $SisaPlanArray     = $this->input->post('SisaPlan');

    $PlanDate2         = $this->input->post('PlanDate2');
    $PlanQty2          = $this->input->post('PlanQty2');
    $UphArray2         = $this->input->post('Uph2');
    $HoursArray2       = $this->input->post('Jam2');
    $SisaPlanArray2    = $this->input->post('SisaPlan2');

    $TotalQty          = 0;

    $DataHeader = array(
      'JobNumber'   => $JobNumber,
      'JobDate'     => $JobDate,
      'JobQuantity' => $JobQuantity,
      'UnitID'      => $UnitID,
      'PartID'      => $PartID,
      'DeptID'      => $DeptID, 
      'LineID'      => $LineName,
      'MachineID'   => $MachineID,
      'Noted'       => $Noted,
      'CreatedDate' => date('Y-m-d H:i:s'),
      'CreatedBy'   => $this->session->userdata('user_id')
    );

    if (is_array($PlanDate)) {
      foreach ($PlanDate as $i => $tanggal) {
        $secondId       = isset($IdDetail[$i]) ? trim($IdDetail[$i]) : "";

        $Tgl            = $tanggal;
        $planQty        = floatval(format_weight($PlanQty[$i] ?? '0'));
        $uph            = floatval(str_replace('.', '', $UphArray[$i] ?? '0'));
        $sisa           = floatval(str_replace('.', '', $SisaPlanArray[$i] ?? '0'));
        $jam            = floatval(str_replace(',', '.', $HoursArray[$i] ?? '0'));
        $TotalQty       += $planQty;

        if ($secondId !== "") {
          $SecondDataUpdate[]  = array(
            'Id'            => $secondId,
            'JobNumber'     => $JobNumber,
            'PlanDate'      => $Tgl,
            'PlanQty'       => $planQty,
            'Uph'           => $uph,
            'Hours'         => $jam,
            'SisaPlan'      => $sisa,
            'UpdatedDate'   => date('Y-m-d H:i:s'),
            'UpdatedBy'     => $this->session->userdata('user_id')
          );
        } else {
          $SecondDataInsert[]  = array(
            'JobNumber'     => $JobNumber,
            'PlanDate'      => $Tgl,
            'PlanQty'       => $planQty,
            'Uph'           => $uph,
            'Hours'         => $jam,
            'SisaPlan'      => $sisa,
            'CreatedDate'   => date('Y-m-d H:i:s'),
            'CreatedBy'     => $this->session->userdata('user_id')
          );
        }
      }
    }

    if (is_array($PlanDate2)) {
      foreach ($PlanDate2 as $i => $tanggal) {
        $thirdId        = isset($IdDetail2[$i]) ? trim($IdDetail2[$i]) : "";

        $Tgl            = $tanggal;
        $planQty        = floatval(format_weight($PlanQty2[$i] ?? '0'));
        $uph2           = floatval(str_replace('.', '', $UphArray2[$i] ?? '0'));
        $sisa2          = floatval(str_replace('.', '', $SisaPlanArray2[$i] ?? '0'));
        $jam2           = floatval(str_replace(',', '.', $HoursArray2[$i] ?? '0'));
        $TotalQty       += $planQty;

        if ($thirdId !== "") {
          $ThirdDataUpdate[]  = array(
            'Id'            => $thirdId,
            'JobNumber'     => $JobNumber,
            'PlanDate'      => $Tgl,
            'PlanQty'       => $planQty,
            'Uph'           => $uph2,
            'Hours'         => $jam2,
            'SisaPlan'      => $sisa2,
            'UpdatedDate'   => date('Y-m-d H:i:s'),
            'UpdatedBy'     => $this->session->userdata('user_id')
          );
        } else {
          $ThirdDataInsert[]  = array(
            'JobNumber'     => $JobNumber,
            'PlanDate'      => $Tgl,
            'PlanQty'       => $planQty,
            'Uph'           => $uph2,
            'Hours'         => $jam2,
            'SisaPlan'      => $sisa2,
            'CreatedDate'   => date('Y-m-d H:i:s'),
            'CreatedBy'     => $this->session->userdata('user_id')
          );
        }
      }
    }

    //echo json_encode(array('status' => 'error', 'message' => 'Data berhasil disimpan.', 'HD' => $DataHeader)); exit();
    // echo json_encode(
    //   array(
    //     'status'            => 'error', 
    //     'message'           => 'Data berhasil disimpan.',
    //     'HD'                => $DataHeader,
    //     'SecondDataInsert'  => $SecondDataInsert,
    //     'SecondDataUpdate'  => $SecondDataUpdate,
    //     'ThirdDataInsert'   => $ThirdDataInsert,
    //     'ThirdDataUpdate'   => $ThirdDataUpdate
    //   )
    // ); exit();
    
    // 1. Mulai Transaksi
    $this->BJGMAS01->trans_begin();
    try {
      // 2. Jalankan Update Header
      $updateHeader = $this->BJGMAS01->update('Trans_ProductionPlanHD', $DataHeader, ['Id' => $IdHeader]);

      if (!$updateHeader) {
        throw new Exception("Gagal mengupdate data header.");
      }

      // 3. Jalankan Operasi Tabel Kedua (DT1)
      if (!empty($SecondDataInsert)) {
        $this->BJGMAS01->insert_batch('Trans_ProductionPlanDT', $SecondDataInsert);
      }
      if (!empty($SecondDataUpdate)) {
        $this->BJGMAS01->update_batch('Trans_ProductionPlanDT', $SecondDataUpdate, 'Id');
      }

      // 4. Jalankan Operasi Tabel Ketiga (DT2)
      if (!empty($ThirdDataInsert)) {
        $this->BJGMAS01->insert_batch('Trans_ProductionPlanDT2', $ThirdDataInsert);
      }
      if (!empty($ThirdDataUpdate)) {
        $this->BJGMAS01->update_batch('Trans_ProductionPlanDT2', $ThirdDataUpdate, 'Id');
      }

      // 5. Cek status transaksi secara keseluruhan
      if ($this->BJGMAS01->trans_status() === FALSE) {
        // Jika ada query yang gagal di tengah jalan
        $this->BJGMAS01->trans_rollback();
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Terjadi kesalahan saat memproses data detail."
        ]);
      } else {
        // Jika semua sukses
        $this->BJGMAS01->trans_commit();
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data berhasil disimpan."
        ]);
      }
    } catch (Exception $e) {
      // 6. Rollback jika terjadi exception
      $this->BJGMAS01->trans_rollback();
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => $e->getMessage()
      ]);
    }    

    exit(); 
  }

  public function hapus_row_jumlah()
  {
    $JobNumber  = $this->input->post('JobNumber');
    $KodeSecond = $this->input->post('KodeSecond');

    //echo json_encode(array('Job' => $JobNumber, 'ID' => $KodeSecond)); exit;

    $Delete = $this->BJGMAS01->delete('Trans_ProductionPlanDT', array('Id' => $KodeSecond, 'JobNumber' => $JobNumber));
    if ($Delete) {
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data sukses dihapus."
      ]);
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal dihapus."
      ]);
    }
    exit();
  }

  public function hapus_row_proses()
  {
    $JobNumber  = $this->input->post('JobNumber');
    $KodeThird  = $this->input->post('KodeThird');

    //echo json_encode(array('Job' => $JobNumber, 'ID' => $KodeThird)); exit;

    $Delete = $this->BJGMAS01->delete('Trans_ProductionPlanDT2', array('Id' => $KodeThird, 'JobNumber' => $JobNumber));
    if ($Delete) {
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data sukses dihapus."
      ]);
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal dihapus."
      ]);
    }
    exit();
  }

  public function hapus_all()
  {
    $jobNumber = $this->input->post('JobNumber');

    $this->BJGMAS01->trans_start();
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_ProductionPlanHD');
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_ProductionPlanDT');
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_ProductionPlanDT2');
    $this->BJGMAS01->trans_complete();

    if ($this->BJGMAS01->trans_status() === TRUE) {
    echo json_encode([
      'status_code' => 200,
      'status'      => 'success',
      'message'     => 'Data berhasil dihapus.'
    ]);
    } else {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Terjadi kesalahan saat menghapus data.'
      ]);
    }
  }

  public function get_proses_produksi_with_line()
	{
		$id 	= $this->input->post('id');
    $data = $this->prosesproduksi->get_proses_with_line_by_id($id);

    echo json_encode($data);
	}

  //INJECTION
  public function injection()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Planning Produksi Injection";
			$data['icon_halaman'] 	= "icon-airplay";
      $DeptID                 = "1218";
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
			$this->load->view('adminx/ppic/planning/injection', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function planning_injection_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$DeptID 	      = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetProductionPlan @StartDate = ?, @EndDate = ?, @DeptIDs = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Id."', '".$value->JobNumber."'";
      $Isi2   = "'".$value->Id."'";

      $row    = [];
      $row[]  = $value->NomorUrut;
      $row[]  = ($value->NomorUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapusAll('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>' : '';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->JobDate;
      $row[]  = $value->JobNumber;
      $row[]  = $value->JobQuantity;
      $row[]  = $value->PartID;
      $row[]  = $value->PartName;
      $row[]  = $value->LineName;
      $row[]  = $value->PlanDate;
      $row[]  = $value->PlanQty; 
      $row[]  = "";               //10
      $row[]  = "";
      $row[]  = "";
      $row[]  = $value->Noted;
      $row[]  = $value->CreatedDate;
  
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

  //PLANNING HARIAN CRIMPING PIN
  public function planning_harian()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Planning Harian";
			$data['icon_halaman'] 	= "icon-airplay";
      $DeptID                 = "1216";
      $data['DeptID']         = $DeptID;
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['MesinList'] 		  = $this->machines->get_all_data($DeptID);
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
			$this->load->view('adminx/ppic/planning/planning_harian', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function planning_harian_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$Tanggal        = $this->input->post('tanggal');
		$MesinID 	      = $this->input->post('mesin_id');
    if (empty($MesinID)) {
      $MesinID       = null;
    } else if (is_array($MesinID)) {
      $MesinID       = implode(',', $MesinID);
    }

    $Sql            = "EXEC dbo.GetReportPlanningHarianByDate @Tanggal = ?, @MachineID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$Tanggal, $MesinID]);
		$Result 		    = $Query->result();
		$Data 			    = [];

		foreach ($Result as $key => $value) {
      $row    = [];
      $row[]  = $value->NomorUrut;
      $row[]  = $value->PartName;
      $row[]  = $value->PartID;
      $row[]  = $value->PlanQty;
      $row[]  = $value->ActualQty;
      $row[]  = $value->Downtime;
      $row[]  = $value->JobNumber;
      $row[]  = $value->JobQuantity;
      $row[]  = $value->SisaPlan;
      $row[]  = $value->DownTimeStart.' s/d '.$value->DownTimeEnd;
      $row[]  = $value->Noted;
  
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

  private function responseJSON($status_code, $status, $message, $errors = null) 
  {
    $response = [
      'status_code' => $status_code,
      'status'      => $status,
      'message'     => $message
    ];

    if ($errors !== null) {
        $response['errors'] = $errors;
    }

    echo json_encode($response);
    exit;
  }

  private function _validation_planning()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('PartID') == '') {
      $data['inputerror'][]   = 'PartID';
      $data['error_string'][] = 'Part ID is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Mesin') == '') {
      $data['inputerror'][]   = 'Mesin';
      $data['error_string'][] = 'Mesin is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('PartName') == '') {
      $data['inputerror'][]   = 'PartName';
      $data['error_string'][] = 'Part Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JobDate') == '') {
      $data['inputerror'][]   = 'JobDate';
      $data['error_string'][] = 'Job Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JobQuantity') == '') {
      $data['inputerror'][]   = 'JobQuantity';
      $data['error_string'][] = 'Job Quantity is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('UnitID') == '') {
      $data['inputerror'][]   = 'UnitID';
      $data['error_string'][] = 'Unit ID is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('LineName') == '') {
      $data['inputerror'][]   = 'LineName';
      $data['error_string'][] = 'Line Name is required';
      $data['status']         = FALSE;
    }
    
    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Dept ID is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    $planDate   = $this->input->post('PlanDate');
    $planQty    = $this->input->post('PlanQty');
    $uph        = $this->input->post('Uph');
    $sisaPlan   = $this->input->post('SisaPlan');

    if (is_array($planDate)) {
      foreach ($planDate as $i => $tgl) {
        if (empty($tgl)) {
          $data['inputerror'][]   = "PlanDate[$i]";
          $data['error_string'][] = 'Tanggal is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($planQty)) {
      foreach ($planQty as $i => $qty) {
        //if (empty($qty)) {
        if ($qty === '' || $qty === null) {
          $data['inputerror'][]   = "PlanQty[$i]";
          $data['error_string'][] = 'Plan Quantity Qty is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($uph)) {
      foreach ($uph as $i => $uphs) {
        //if (empty($qty)) {
        if ($uphs === '' || $uphs === null) {
          $data['inputerror'][]   = "Uph[$i]";
          $data['error_string'][] = 'UPH is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($sisaPlan)) {
      foreach ($sisaPlan as $i => $sisaPlans) {
        //if (empty($qty)) {
        if ($sisaPlans === '' || $sisaPlans === null) {
          $data['inputerror'][]   = "SisaPlan[$i]";
          $data['error_string'][] = 'Sisa Plan is required';
          $data['status']         = FALSE;
        }
      }
    }

    // if ($this->input->post('DeptID') == '1216') {
    //   $planDate2  = $this->input->post('PlanDate2');
    //   $planQty2   = $this->input->post('PlanQty2');

    //   if (is_array($planDate2)) {
    //     foreach ($planDate2 as $i => $tgl) {
    //       if (empty($tgl)) {
    //         $data['inputerror'][]   = "PlanDate2[$i]";
    //         $data['error_string'][] = 'Tanggal is required';
    //         $data['status']         = FALSE;
    //       }
    //     }
    //   }

    //   if (is_array($planQty2)) {
    //     foreach ($planQty2 as $i => $qty) {
    //       //if (empty($qty)) {
    //       if ($qty === '' || $qty === null) {
    //         $data['inputerror'][]   = "PlanQty2[$i]";
    //         $data['error_string'][] = 'Plan Quantity Qty is required';
    //         $data['status']         = FALSE;
    //       }
    //     }
    //   }
    // }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}