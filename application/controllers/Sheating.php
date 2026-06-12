<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sheating extends CI_Controller
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
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Control Job Sheating Extrude";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/sheating/job', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function list_data()
  {
    $Draw                 = intval($this->input->get("draw"));
    $Start                = intval($this->input->get("start"));
    $Length               = intval($this->input->get("length"));
    $Month                = $this->input->post('Months');
    $Year                 = $this->input->post('Years');
    $Sql                  = "EXEC dbo.GetJobSheatingData @Year = '$Year', @Month = '$Month'";
    $Query                = $this->BJGMAS01->query($Sql);
    $Result               = $Query->result();
    $Data 	              = [];
    $No                   = 1;
    $PersentaseTembaga    = 0;
    $PersentaseInsulating = 0;
    $PersentasePVC        = 0;
    $Selisih              = 0;
    foreach ($Result as $key => $Res) {
      $JobNumber = "'".$Res->JobNumber."'";

      $Row    = array();
      $Row[]  = $Res->NomorUrut;
      $Row[] = ($Res->NomorUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$JobNumber.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapusAll('.$JobNumber.')">Hapus</a>
                    </div>
                  </div>
                </div>' : '';
      $Row[]  = $Res->JobDate;
      $Row[]  = $Res->PartName;
      $Row[]  = $Res->PartID;
      $Row[]  = $Res->JobNumber; 
      $Row[]  = $Res->JobQuantity;
      $Row[]  = $Res->PvcWeight;
      $Row[]  = $Res->ProcessDateDT1;
      $Row[]  = $Res->PlanQty;
      $Row[]  = $Res->ActualQty;
      $Row[]  = $Res->TotalProduksi;
      $Row[]  = $Res->JumlahPercentage;
      $Row[]  = $Res->ProcessDateDT2;
      $Row[]  = $Res->BeratKabel;
      $Row[]  = $Res->BeratKabelPercentage;
      $Row[]  = $Res->PanjangKabel;
      $Row[]  = $Res->PanjangKabelPercentage;               
      $Row[]  = $Res->BeratPvc;
      $Row[]  = $Res->BeratPvcPercentage;
      $Row[]  = $Res->Selisih;
      $Row[]  = $Res->FinishedGoods;
      $Row[]  = $Res->Notes;
      $Row[]  = $Res->CreateDate;
      $Row[]  = $Res->UnitID;

      $Data[] = $Row;
    }

    $Output = [
      "draw" 				    => $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 				    => $Data
    ];

    echo json_encode($Output);
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
                 FORMAT(a.QtyOrder, 'N0') AS QtyOrder, b.PartName, a.UnitID
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

  public function save_data()
  {
    $this->_validation_job();

    //DATA HEADER
    $SecondData           = array();
    $ThirdData            = array();
    $Periode              = $this->input->post('Periode'); 
    $JobNumber            = $this->input->post('JobList');
    $PartID               = $this->input->post('PartID');
    $JobDate              = $this->input->post('JobDate');
    $JobQuantity          = floatval(str_replace(',', '', $this->input->post('JobQuantity')));
    $UnitID               = $this->input->post('UnitID');
    $Remark               = strtoupper($this->input->post('Remark'));
    $PvcWeight            = floatval(format_weight($this->input->post('PvcWeight')));
    
    //DATA DT1
    $TanggalProses        = $this->input->post('TanggalProses');
    $PlanQty              = $this->input->post('PlanQty');
    $ActualQty            = $this->input->post('ActualQty');
    $TotalActualQty       = 0;

    //DATA DT2
    $TanggalNG            = $this->input->post('TanggalNG');
    $BeratKabelNG         = $this->input->post('BeratKabelNG');
    $PanjangKabelNG       = $this->input->post('PanjangKabelNG');
    $BeratPvcNG           = $this->input->post('BeratPvcNG');

    $FirstData            = array(
      'JobNumber'       => $JobNumber,
      'JobDate'         => $JobDate,
      'JobQuantity'     => $JobQuantity,
      'UnitID'          => $UnitID,
      'PartID'          => $PartID,
      'PvcWeight'       => $PvcWeight,
      'Notes'           => $Remark,
      'CreateDate'      => date('Y-m-d H:i:s'),
      'CreateBy'        => $this->session->userdata('user_id')
    );

    if (is_array($TanggalProses)) {
      foreach ($TanggalProses as $i => $tanggal) {
        $Tgl            = $tanggal;
        $planQty        = floatval(format_weight($PlanQty[$i] ?? '0'));
        $actualQty      = floatval(format_weight($ActualQty[$i] ?? '0'));
        $TotalActualQty += $actualQty;

        $SecondData[]   = array(
          'JobNumber'     => $JobNumber,
          'ProcessDate'   => $Tgl,
          'PlanQty'       => $planQty,
          'ActualQty'     => $actualQty,
          'CreateDate'    => date('Y-m-d H:i:s'),
          'CreateBy'      => $this->session->userdata('user_id')
        );
      }
    }

    if (is_array($TanggalNG)) {
      foreach ($TanggalNG as $i => $tanggal) {
        $Tgl                = $tanggal;
        $beratKabelNG       = floatval(format_weight($BeratKabelNG[$i] ?? '0'));
        $panjangKabelNG     = floatval(format_weight($PanjangKabelNG[$i] ?? '0'));
        $beratPvcNG         = floatval(format_weight($BeratPvcNG[$i] ?? '0'));

        $ThirdData[]  = array(
          'JobNumber'     => $JobNumber,
          'ProcessDate'   => $Tgl,
          'BeratKabel'    => $beratKabelNG,
          'PanjangKabel'  => $panjangKabelNG,
          'BeratPvc'      => $beratPvcNG,
          'CreateDate'    => date('Y-m-d H:i:s'),
          'CreateBy'      => $this->session->userdata('user_id')
        );
      }
    }

    //echo json_encode(array("status" => "success", "Data Job" => $FirstData, "Data Jumlah" => $SecondData, "Data NG" => $ThirdData, "Total Actual Qty" => $TotalActualQty, "Total Qty Job" => $JobQuantity)); exit();
    
    $JobQuantity = floatval($FirstData['JobQuantity'] ?? 0);
    if ($TotalActualQty > $JobQuantity) {
      echo json_encode([
        "status_code" => 400,
        "status"      => "error",
        "message"     => "Total Actual Qty. (" . number_format($TotalActualQty) . ") melebihi Job Qty. (" . number_format($JobQuantity) . ")."
      ]);

      return;
    }

    // Cek jika data sudah ada
    $checkExist = $this->BJGMAS01->get_where('Trans_JobSheatingHD', ['JobNumber' => $JobNumber]);
    if ($checkExist->num_rows() > 0) {
      $this->responseJSON(409, 'error', "Nomor job #$JobNumber sudah terdaftar.");
    }

    // Mulai transaksi di koneksi BJGMAS01
    $this->BJGMAS01->trans_begin();
    // Simpan Header
    if (!$this->BJGMAS01->insert('Trans_JobSheatingHD', $FirstData)) {
      $this->BJGMAS01->trans_rollback();
      $this->responseJSON(500, 'error', 'Gagal menyimpan data header.', ['step' => 'Header Insert']);
    }

    // Simpan Detail 1
    if (!$this->BJGMAS01->insert_batch('Trans_JobSheatingDT1', $SecondData)) {
      $this->BJGMAS01->trans_rollback();
      $this->responseJSON(500, 'error', 'Gagal menyimpan data detail 1.', ['step' => 'Detail 1 Insert']);
    }

    // Simpan Detail 2
    if (!$this->BJGMAS01->insert_batch('Trans_JobSheatingDT2', $ThirdData)) {
      $this->BJGMAS01->trans_rollback();
      $this->responseJSON(500, 'error', 'Gagal menyimpan data detail 2.', ['step' => 'Detail 2 Insert']);
    }

    // Commit transaksi
    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      $this->responseJSON(500, 'error', 'Gagal menyimpan data (transaksi gagal).');
    } else {
      $this->BJGMAS01->trans_commit();
      $this->responseJSON(200, 'success', 'Sukses menyimpan data.');
    }

    exit;
  }

  public function edit_data()
  {
    $JobNumber  = $this->input->post('JobNumber');
    $this->BJGMAS01->select("
      a.Id, a.JobNumber, a.JobDate, a.PartID, b.PartName, a.Notes, a.UnitID,
      REPLACE(FORMAT(a.JobQuantity, 'N0'), ',', '.') AS JobQuantity,
      REPLACE(CONVERT(varchar(20), CAST(a.PvcWeight AS decimal(18,4))), '.', ',') AS PvcWeight,
      FORMAT(a.JobDate, 'yyyy-MM') AS JobPeriode
    ");
    $this->BJGMAS01->from('Trans_JobSheatingHD a');
    $this->BJGMAS01->join('Ms_Part b', 'b.PartID = a.PartID', 'left');
    $this->BJGMAS01->where('a.JobNumber', $JobNumber);

    $Query = $this->BJGMAS01->get();

    if ($Query->num_rows() > 0) {
      $this->BJGMAS01->select("
        Id,
        JobNumber,
        ProcessDate,
        FORMAT(PlanQty, 'N0') AS PlanQty,
        FORMAT(ActualQty, 'N0') AS ActualQty
      ", false);
      $this->BJGMAS01->from("Trans_JobSheatingDT1");
      $this->BJGMAS01->where("JobNumber", $JobNumber);
      $this->BJGMAS01->order_by("ProcessDate", 'ASC');
      $QuerySec   = $this->BJGMAS01->get();

      $this->BJGMAS01->select("
        Id,
        JobNumber,
        ProcessDate,
        CONVERT(varchar(20), CAST(BeratKabel AS decimal(18,2))) AS BeratKabel,
        CONVERT(varchar(20), CAST(PanjangKabel AS decimal(18,2))) AS PanjangKabel,
        CONVERT(varchar(20), CAST(BeratPvc AS decimal(18,2))) AS BeratPvc
      ", false);
      $this->BJGMAS01->from("Trans_JobSheatingDT2");
      $this->BJGMAS01->where("JobNumber", $JobNumber);
      $QueryTrd   = $this->BJGMAS01->get();

      $FirstData  = $Query->row();
      $SecondData = $QuerySec->result();
      $ThirdData  = $QueryTrd->result();

      //$ThirdData  = $this->BJGMAS01->get_where('Trans_JobInsulatingDT2', array('JobNumber' => $JobNumber))->result();
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data ditemukan.",
          "first"       => $FirstData,
          "second"      => $SecondData,
          "third"       => $ThirdData,
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

  public function update_data()
  {
    $this->_validation_job();

    //DATA HEADER
    $SecondDataInsert     = array();
    $SecondDataUpdate     = array();
    $ThirdDataInsert      = array();
    $ThirdDataUpdate      = array();
    $FirstId              = $this->input->post('kodeFirst');
    $SecondId             = $this->input->post('kodeSecond');
    $ThirdId              = $this->input->post('kodeThird');
    $Periode              = $this->input->post('Periode'); 
    $JobNumber            = $this->input->post('JobList');
    $PartID               = $this->input->post('PartID');
    $JobDate              = $this->input->post('JobDate');
    $JobQuantity          = floatval(str_replace('.', '', $this->input->post('JobQuantity')));
    $Remark               = strtoupper($this->input->post('Remark'));
    $UnitID               = $this->input->post('UnitID');
    $PvcWeight            = floatval(format_weight($this->input->post('PvcWeight')));

    //DATA DT1
    $TanggalProses        = $this->input->post('TanggalProses');
    $PlanQty              = $this->input->post('PlanQty');
    $ActualQty            = $this->input->post('ActualQty');
    $TotalActualQty       = 0;

    //DATA DT2
    $TanggalNG            = $this->input->post('TanggalNG');
    $BeratKabelNG         = $this->input->post('BeratKabelNG');
    $PanjangKabelNG       = $this->input->post('PanjangKabelNG');
    $BeratPvcNG           = $this->input->post('BeratPvcNG');

    $FirstData            = array(
      'JobNumber'       => $JobNumber,
      'JobDate'         => $JobDate,
      'JobQuantity'     => $JobQuantity,
      'UnitID'          => $UnitID,
      'PartID'          => $PartID,
      'PvcWeight'       => $PvcWeight,
      'Notes'           => $Remark,
      'UpdateDate'      => date('Y-m-d H:i:s'),
      'UpdateBy'        => $this->session->userdata('user_id')
    );

    if (is_array($TanggalProses)) {
      foreach ($TanggalProses as $i => $tanggal) {
        $secondId       = isset($SecondId[$i]) ? trim($SecondId[$i]) : "";

        $Tgl            = $tanggal;
        $planQty        = floatval(format_weight($PlanQty[$i] ?? '0'));
        $actualQty      = floatval(format_weight($ActualQty[$i] ?? '0'));
        $TotalActualQty += $actualQty;

        if ($secondId !== "") {
          $SecondDataUpdate[]  = array(
            'Id'            => $secondId,
            'JobNumber'     => $JobNumber,
            'ProcessDate'   => $Tgl,
            'PlanQty'       => $planQty,
            'ActualQty'     => $actualQty,
            'UpdateDate'    => date('Y-m-d H:i:s'),
            'UpdateBy'      => $this->session->userdata('user_id')
          );
        } else {
          $SecondDataInsert[]  = array(
            'JobNumber'     => $JobNumber,
            'ProcessDate'   => $Tgl,
            'PlanQty'       => $planQty,
            'ActualQty'     => $actualQty,
            'CreateDate'    => date('Y-m-d H:i:s'),
            'CreateBy'      => $this->session->userdata('user_id')
          );
        }
      }
    }

    if (is_array($TanggalNG)) {
      foreach ($TanggalNG as $i => $tanggal) {
        $thirdId = isset($ThirdId[$i]) ? trim($ThirdId[$i]) : "";
    
        $beratKabelNG     = floatval(format_weight($BeratKabelNG[$i] ?? '0'));
        $panjangKabelNG   = floatval(format_weight($PanjangKabelNG[$i] ?? '0'));
        $beratPvcNG       = floatval(format_weight($BeratPvcNG[$i] ?? '0'));
    
        if ($thirdId !== "") {
          // Data untuk UPDATE
          $ThirdDataUpdate[] = array(
            'Id'            => $thirdId,
            'JobNumber'     => $JobNumber,
            'ProcessDate'   => $tanggal,
            'BeratKabel'    => $beratKabelNG,
            'PanjangKabel'  => $panjangKabelNG,
            'BeratPvc'      => $beratPvcNG,
            'UpdateDate'    => date('Y-m-d H:i:s'),
            'UpdateBy'      => $this->session->userdata('user_id')
          );
        } else {
          // Data untuk INSERT
          $ThirdDataInsert[] = array(
            'JobNumber'     => $JobNumber,
            'ProcessDate'   => $tanggal,
            'BeratKabel'    => $beratKabelNG,
            'PanjangKabel'  => $panjangKabelNG,
            'BeratPvc'      => $beratPvcNG,
            'CreateDate'    => date('Y-m-d H:i:s'),
            'CreateBy'      => $this->session->userdata('user_id')
          );
        }
      }
    }

    //echo json_encode(array('status_code' => 200, 'status' => 'success', 'message' => 'Data berhasil disimpan.', 'FirstData' => $FirstData, 'SecondDataInsert' => $SecondDataInsert, 'SecondDataUpdate' => $SecondDataUpdate, 'ThirdDataInsert' => $ThirdDataInsert, 'ThirdDataUpdate' => $ThirdDataUpdate, "Total Actual Qty." => $TotalActualQty, "Total Qty Job" => $JobQuantity)); exit(); 

    $JobQuantity = floatval($FirstData['JobQuantity'] ?? 0);
    if ($TotalActualQty > $JobQuantity) {
      echo json_encode([
        "status_code" => 400,
        "status"      => "error",
        "message"     => "Total Actual Qty. (" . number_format($TotalActualQty) . ") melebihi Job Qty. (" . number_format($JobQuantity) . ")."
      ]);

      return;
    }

    if ($this->BJGMAS01->update('Trans_JobSheatingHD', $FirstData, ['Id' => $FirstId])) {
      !empty($SecondDataInsert) && $this->BJGMAS01->insert_batch('Trans_JobSheatingDT1', $SecondDataInsert);
      !empty($SecondDataUpdate) && $this->BJGMAS01->update_batch('Trans_JobSheatingDT1', $SecondDataUpdate, 'Id');
      !empty($ThirdDataInsert)  && $this->BJGMAS01->insert_batch('Trans_JobSheatingDT2', $ThirdDataInsert);
      !empty($ThirdDataUpdate)  && $this->BJGMAS01->update_batch('Trans_JobSheatingDT2', $ThirdDataUpdate, 'Id');
    
      // RESPONSE SUCCESS
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data berhasil disimpan."
      ]);
    } else {
      // RESPONSE ERROR
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Gagal mengupdate data header."
      ]);
    }        

    exit();
  }

  public function hapus_row_jumlah()
  {
    $JobNumber  = $this->input->post('JobNumber');
    $KodeSecond = $this->input->post('KodeSecond');

    $Delete = $this->BJGMAS01->delete('Trans_JobSheatingDT1', array('Id' => $KodeSecond, 'JobNumber' => $JobNumber));
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

  public function hapus_row_ng()
  {
    $JobNumber  = $this->input->post('JobNumber');
    $KodeThird  = $this->input->post('KodeThird');

    $Delete = $this->BJGMAS01->delete('Trans_JobSheatingDT2', array('Id' => $KodeThird, 'JobNumber' => $JobNumber));
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

    $this->BJGMAS01->trans_start(); // Mulai transaksi
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_JobSheatingDT2');
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_JobSheatingDT1');
    $this->BJGMAS01->where('JobNumber', $jobNumber)->delete('Trans_JobSheatingHD');
    $this->BJGMAS01->trans_complete(); // Selesai transaksi

    if ($this->BJGMAS01->trans_status() === TRUE) {
      // Jika semua berhasil
      echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data berhasil dihapus.'
      ]);
    } else {
      // Jika terjadi error
      echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Terjadi kesalahan saat menghapus data.'
      ]);
    }
  }

  private function responseJSON($status_code, $status, $message, $errors = null) {
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

  private function _validation_job()
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

    if ($this->input->post('PvcWeight') == '') {
      $data['inputerror'][]   = 'PvcWeight';
      $data['error_string'][] = 'PVC Weight is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    $tanggalProses  = $this->input->post('TanggalProses');
    $planQty        = $this->input->post('PlanQty');
    $actualQty      = $this->input->post('ActualQty');

    if (is_array($tanggalProses)) {
      foreach ($tanggalProses as $i => $tgl) {
        if (empty($tgl)) {
          $data['inputerror'][]   = "TanggalProses[$i]";
          $data['error_string'][] = 'Tanggal Proses is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($planQty)) {
      foreach ($planQty as $i => $qty) {
        //if (empty($qty)) {
          if ($qty === '' || $qty === null) {
          $data['inputerror'][]   = "PlanQty[$i]";
          $data['error_string'][] = 'Plan Qty is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($actualQty)) {
      foreach ($actualQty as $i => $qty) {
        //if (empty($qty)) {
        if ($qty === '' || $qty === null) {
          $data['inputerror'][]   = "ActualQty[$i]";
          $data['error_string'][] = 'Actual Qty is required';
          $data['status']         = FALSE;
        }
      }
    }

    // validasi per kolom dalam ngContainer
    $tanggalNG          = $this->input->post('TanggalNG');
    $beratKabelNG       = $this->input->post('BeratKabelNG');
    $panjangKabelNG     = $this->input->post('PanjangKabelNG');
    $beratPvcNG         = $this->input->post('BeratPvcNG');

    if (is_array($tanggalNG)) {
      foreach ($tanggalNG as $i => $tglNG) {
         if ($tglNG === '' || $tglNG === null) {
          $data['inputerror'][]   = "TanggalNG[$i]";
          $data['error_string'][] = 'Tanggal NG is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($beratKabelNG)) {
      foreach ($beratKabelNG as $i => $beratKabel) {
         if ($beratKabel === '' || $beratKabel === null) {
          $data['inputerror'][]   = "BeratKabelNG[$i]";
          $data['error_string'][] = 'Berat Kabel NG is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($panjangKabelNG)) {
      foreach ($panjangKabelNG as $i => $panjangKabel) {
         if ($panjangKabel === '' || $panjangKabel === null) {
          $data['inputerror'][]   = "PanjangKabelNG[$i]";
          $data['error_string'][] = 'Panjang Kabel NG is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($beratPvcNG)) {
      foreach ($beratPvcNG as $i => $pvc) {
         if ($pvc === '' || $pvc === null) {
          $data['inputerror'][]   = "BeratPvcNG[$i]";
          $data['error_string'][] = 'Berat PVC NG is required';
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