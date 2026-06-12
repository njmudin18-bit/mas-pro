<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_incoming_part extends CI_Controller {

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

	public function __construct() {
    parent::__construct();

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('scan_incoming_part_model', 'scan_model');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Scan Incoming Part";
      $data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/incoming/scan_incoming_part', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
	}

  public function scan_incoming_part_list()
  {
    $Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');

    $List        = $this->scan_model->get_datatables($StartDate, $EndDate);
    $Data        = [];
    $No          = $Start + 1;
    foreach ($List as $key => $Row) {
      $BarcodeNumber = "'".$Row->BarcodeNumber."'";
      $Data[] = array(
        $No++,
        '<button onclick="hapus_satu_barcode('.$BarcodeNumber.');" type="button" title="Hapus Barcode '.$Row->BarcodeNumber.'" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></button>',
        $Row->Sequent,
        $Row->BarcodeNumber,
        $Row->PartName,
        $Row->PartID,
        $Row->SupplierType.". ".$Row->SupplierName,
        $Row->CreateDate
      );
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $this->scan_model->count_all(),
      "recordsFiltered" => $this->scan_model->count_filtered($StartDate, $EndDate),
      "data"            => $Data
    ]);
  }

  public function hapus_satu_barcode() 
  {
    $id         = $this->input->post('BarcodeNomor');
    $DataDelete = $this->scan_model->get_by_id($id);
    $data 			= $this->scan_model->delete_by_id($id);
    echo json_encode(array("status" => "success"));
          
    //ADDING TO LOG
    $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	= "DELETE";
    $log_data 	= json_encode($DataDelete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function saving_scanning_part() 
  {
    $BarcodeNumber    = $this->input->post('barcode_no');
    $Cek              = $this->scan_model->check_barcode_fifo($BarcodeNumber);
    if ($Cek) {
      if ($this->scan_model->is_barcode_exists($BarcodeNumber)) {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Nomor QR sudah terdaftar di system."
          )
        );
      } else {
        $data = array(
          'BarcodeNumber' => $BarcodeNumber,
          'CreateDate'    => date('Y-m-d H:i:s'),
          'CreateBy'      => $this->session->userdata('user_id')
        );

        //echo json_encode($data); exit;
        
        $Insert = $this->scan_model->save($data);
        if ($Insert) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Data sukses disimpan"
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data gagal disimpan"
            )
          );
        }
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Nomor QR tidak terdaftar di system."
        )
      );
    }
  }

  public function scan_incoming_part_report()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Scan Incoming Part Report";
      $data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/incoming/scan_incoming_part_report', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
	}

  public function scan_incoming_part_report_list()
  {
    $Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');

    $List        = $this->scan_model->get_incoming_part_report($StartDate, $EndDate);
    $Data        = [];
    $No          = $Start + 1;
    foreach ($List->result() as $key => $Row) {
      $PONumber = "'".$Row->PONumber."'";
      $Data[] = array(
        $No++,
        '<button onclick="lihat_detail_po('.$PONumber.');" type="button" title="Lihat Detail '.$Row->PONumber.'" class="btn btn-success btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button>',
        $Row->PONumber,
        $Row->SupplierID,
        $Row->SupplierType.". ".$Row->SupplierName,
        $Row->PrintDate
      );
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $List->num_rows(),
      "recordsFiltered" => $List->num_rows(),
      "data"            => $Data
    ]);
  }

  public function lihat_detail_po() 
  {
    $PONumber     = $this->input->post('PONumber');
    $PONumberArr  = explode("/", $PONumber);
    $TblPO        = "Trans_POHD".$PONumberArr[2];
    $TblPODT      = "Trans_PODT1".$PONumberArr[2];
    $Data_Header  = $this->scan_model->get_header_po($PONumber, $TblPO);
    $Data_Detail  = $this->scan_model->get_detail_po($PONumber, $TblPODT);
    $Html         = '';
    $No           = 1;
    foreach ($Data_Detail->result() as $key => $value) {
      $Isi   = "'".$value->NoBukti."', '".$value->PartID."', '".$value->PartName."'";
      $Html .= '<tr>
                  <td class="text-right">'.$No++.'</td>
                  <td class="text-center"><button id="lihat_item_po_'.$key.'" onclick="lihat_item_po('.$Isi.');" type="button" title="Lihat Item '.$value->NoBukti.'" class="btn btn-success btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></td>
                  <td class="text-right">'.$value->JlhLabel.'</td>
                  <td class="text-left">'.$value->NoBukti.'</td>
                  <td class="text-left">'.$value->PartID.'</td>
                  <td class="text-left">'.$value->PartName.'</td>
                </tr>';
    }

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Sukses menampilkan data',
        'data_header' => $Data_Header->row(),
        'data_detail' => $Data_Detail->result(),
        'html'        => $Html
      )
    );
  }

  public function lihat_item_po() 
  {
    $PONumber = $this->input->post('PONumber');
    $PartID   = $this->input->post('PartID');
    $Data     = $this->scan_model->get_fifocard_by_item_and_po($PONumber, $PartID);

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Sukses menampilkan data',
        'data'        => $Data->result()
      )
    );
  }
}