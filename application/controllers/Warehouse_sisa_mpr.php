<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_sisa_mpr extends CI_Controller {

	public function __construct() {
    parent::__construct();
    
    //start tidak usah di ubah
    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END


    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    //end tidak usah di ubah

    $this->load->model('barcode_model', 'barcode_sales');
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
			$data['group_halaman'] 	= "WAREHOUSE";
			$data['nama_halaman'] 	= "WAREHOUSE SISA MPR";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/warehouse/report_sisa_mpr', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function report_sisa_mpr_list()
	{

		$draw 			= intval($this->input->get("draw"));
    $start 			= intval($this->input->get("start"));
    $length 		= intval($this->input->get("length"));

    

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $start_date = $this->input->post('start_date');
  	$end_date 	= $this->input->post('end_date');

    // $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan, CreateBy, CreateDate, loc_id
    //         FROM tbl_monitoring_mpr
    //         WHERE CAST(CreateDate AS date) BETWEEN '$start_date' AND '$end_date'  AND loc_id='PPIC001'
    //         ORDER BY CreateDate DESC";
    $sql = "SELECT * FROM tbl_monitoring_mpr_detail 
            WHERE CAST(CreatedateWH AS date) BETWEEN '$start_date' AND '$end_date'  AND QtySisaProduksi > 0";


    $query 				= $second_DB->query($sql);
    $result 			= $query->result();
    $data 				= [];
    $no 					= 1;
  
    
    foreach ($result as $key => $value) {

    if ($value->StatusWH == 1){
      $ceklisStatus = "Checked";
    }else{
      $ceklisStatus = "unChecked";
    }
     	$data[] = array(
     		$no++,
        $value->NoBukti,
        $value->PartID,
        $value->PartName,
        number_format($value->Qty,2),
        number_format($value->StandartPacking,2),
        number_format($value->StockSimpan,2),
        number_format($value->QtySisaProduksi,2),
        $value->UnitID,
		    $ceklisStatus,
        $value->CreatebyWH
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

}


