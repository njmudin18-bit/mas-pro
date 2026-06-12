<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesin extends CI_Controller {

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
    $this->load->model('machine_model', 'machine');
    $this->load->model('mesindetail_model', 'machine_detail');
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Monitoring Mesin Keyence";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
			
			$this->load->view('adminx/mesin/monitoring_keyence', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function show_data_mesin()
	{
		$response = $this->machine->get_all();
		asort($response); //sort array to desc

		$text 		= "";
		
		if (count($response)> 0) {
			foreach ($response as $key => $value) {
				$operator 			= $value->Namaoperator == "" ? "-" : $value->Namaoperator;
				$shift 					= $value->Shift == "" ? "-" : $value->Shift;
				$mesin 					= $value->Namamesin == "" ? "-" : $value->Namamesin;
				$on_off 				= $value->Statusmesin == 1 ? "ON" : "OFF";
				$class 					= $value->Statusmesin == 1 ? "btn-success" : "btn-danger";
				$class_border 	= $value->Statusmesin == 1 ? "border-green" : "border-danger";

				//onclick="open_details('."'".$value->Nomesin."'".')"
				//title="Klik untuk melihat detail #'.$value->Nomesin.'"
				$text .= '<div class="col-xl-3 col-md-12 col-xs-12">
										<div class="card user-card2 '.$class_border.'" style="cursor: pointer;" title="Mesin #'.$mesin.'">
											<div class="card-block text-center">
												<div class="row">
													<div class="col-md-12 col m-b-10">
														<button id="btn_kode_mesin_'.$key.'" class="btn waves-effect waves-light btn-icon '.$class.'">'.$on_off.'
                      			</button>
													</div>
												</div>
												<!-- KODE OPERATOR & QTY -->
												<div class="row b-t-default">
													<div class="col-md-12 col m-t-10">
														<h5 class="text-muted text-center">Mesin #'.$mesin.'</h5>
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Nama Operator</h6>
														<input type="text" id="kode_operator_'.$key.'" class="form-control text-left" readonly="readonly" value="'.$operator.'" title="'.$operator.'">
													</div>
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Quantity</h6>
														<input type="text" id="qty_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->qty.'">
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Shift</h6>
														<input type="text" id="shift_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$shift.'">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi ON</h6>
														<input type="text" id="durasi_on_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->Durasi.'">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi Off</h6>
														<input type="text" id="durasi_off_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->DurasiOff.'">
													</div>
												</div>
											</div>
										</div>
									</div>';
			};
		} else {
			for ($i=1; $i <= 8; $i++) { 
				$text .= '<div class="col-xl-3 col-md-12 col-xs-12">
										<div class="card user-card2" style="cursor: pointer;">
											<div class="card-block text-center">
												<div class="row">
													<div class="col-md-12 col m-b-10">
														<button id="btn_kode_mesin_'.$i.'" class="btn waves-effect waves-light btn-icon btn-danger '.$i.'">OFF
                      			</button>
													</div>
												</div>
												<!-- KODE OPERATOR & QTY -->
												<div class="row b-t-default">
													<div class="col-md-12 col m-t-10">
														<h5 class="text-muted text-center">Mesin #MS000'.$i.'</h5>
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Kode Operator</h6>
														<input type="text" id="kode_operator_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Quantity</h6>
														<input type="text" id="qty_" class="form-control text-center" readonly="readonly" value="0">
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Shift</h6>
														<input type="text" id="shift_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi ON</h6>
														<input type="text" id="durasi_on_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi Off</h6>
														<input type="text" id="durasi_off_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
												</div>
											</div>
										</div>
									</div>';
			}
		}

		echo json_encode(
			array(
				"status" 		=> "success",
				"message" 	=> "sukses menampilkan data",
				"data" 			=> $response,
				"html" 			=> $text
			)
		);
	}

	public function show_data_mesin_OLD()
	{
		$response = $this->machine->get_all();
		asort($response); //sort array to desc

		$text 		= "";
		
		if (count($response)> 0) {
			foreach ($response as $key => $value) {
				$operator 			= $value->Namaoperator == "" ? "-" : $value->Namaoperator;
				$shift 					= $value->Shift == "" ? "-" : $value->Shift;
				$mesin 					= $value->Namamesin == "" ? "-" : $value->Namamesin;
				$on_off 				= $value->Statusmesin == 1 ? "ON" : "OFF";
				$class 					= $value->Statusmesin == 1 ? "btn-success" : "btn-danger";
				$class_border 	= $value->Statusmesin == 1 ? "border-green" : "border-danger";

				//onclick="open_details('."'".$value->Nomesin."'".')"
				//title="Klik untuk melihat detail #'.$value->Nomesin.'"
				$text .= '<div class="col-xl-3 col-md-12 col-xs-12">
										<div class="card user-card2 '.$class_border.'" style="cursor: pointer;" title="Mesin #'.$mesin.'">
											<div class="card-block text-center">
												<div class="row">
													<div class="col-md-12 col m-b-10">
														<button id="btn_kode_mesin_'.$key.'" class="btn waves-effect waves-light btn-icon '.$class.'">'.$on_off.'
                      			</button>
													</div>
												</div>
												<!-- KODE OPERATOR & QTY -->
												<div class="row b-t-default">
													<div class="col-md-12 col m-t-10">
														<h5 class="text-muted text-center">Mesin #'.$mesin.'</h5>
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Nama Operator</h6>
														<input type="text" id="kode_operator_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$operator.'" title="'.$operator.'">
													</div>
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Quantity</h6>
														<input type="text" id="qty_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->qty.'">
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Shift</h6>
														<input type="text" id="shift_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$shift.'">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi ON</h6>
														<input type="text" id="durasi_on_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->Durasi.'">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi Off</h6>
														<input type="text" id="durasi_off_'.$key.'" class="form-control text-center" readonly="readonly" value="'.$value->DurasiOff.'">
													</div>
												</div>
											</div>
										</div>
									</div>';
			};
		} else {
			for ($i=1; $i <= 8; $i++) { 
				$text .= '<div class="col-xl-3 col-md-12 col-xs-12">
										<div class="card user-card2" style="cursor: pointer;">
											<div class="card-block text-center">
												<div class="row">
													<div class="col-md-12 col m-b-10">
														<button id="btn_kode_mesin_'.$i.'" class="btn waves-effect waves-light btn-icon btn-danger '.$i.'">OFF
                      			</button>
													</div>
												</div>
												<!-- KODE OPERATOR & QTY -->
												<div class="row b-t-default">
													<div class="col-md-12 col m-t-10">
														<h5 class="text-muted text-center">Mesin #MS000'.$i.'</h5>
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Kode Operator</h6>
														<input type="text" id="kode_operator_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-6 col m-t-15">
														<h6 class="text-muted text-left">Quantity</h6>
														<input type="text" id="qty_" class="form-control text-center" readonly="readonly" value="0">
													</div>
												</div>
												<div class="row m-t-10 b-t-default">
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Shift</h6>
														<input type="text" id="shift_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi ON</h6>
														<input type="text" id="durasi_on_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
													<div class="col-md-4 col m-t-15">
														<h6 class="text-muted text-left">Durasi Off</h6>
														<input type="text" id="durasi_off_'.$i.'" class="form-control text-center" readonly="readonly" value="0">
													</div>
												</div>
											</div>
										</div>
									</div>';
			}
		}

		echo json_encode(
			array(
				"status" 		=> "success",
				"message" 	=> "sukses menampilkan data",
				"data" 			=> $response,
				"html" 			=> $text
			)
		);
	}

	public function show_data_mesin_detail()
	{
		//CEK APAKAH ADA SEGMENTASI NYA
		if ($this->uri->segment(3) == true) {
			$kode_mesin 						= $this->uri->segment(3);
			$data['kode_mesin']			= $this->uri->segment(3);
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Detail Mesin #".$kode_mesin;
			$data['icon_halaman'] 	= "icon-airplay";

			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$this->load->view('adminx/mesin/monitoring_details', $data, FALSE);
		} else {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Monitoring Mesin Keyence";
			$data['icon_halaman'] 	= "icon-airplay";

			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$this->load->view('adminx/mesin/monitoring_keyence', $data, FALSE);
		}
	}

	public function show_data_mesin_list()
	{
		$list = $this->machine_detail->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $machine_detail) {
			$no++;
			$noUrut++;
			$row 		= array();
			$row[] 	= $no;
			//add html for action
			$row[] 	= $no;
			$row[] 	= $machine_detail->Kode;
			$row[] 	= $machine_detail->Statusmesin;
			$row[] 	= $machine_detail->Nomesin;
			$row[] 	= $machine_detail->Durasi;
			$row[] 	= $machine_detail->DurasiOff;
			$row[] 	= $machine_detail->Mold;
			$row[] 	= $machine_detail->Qty;
			$row[] 	= $machine_detail->Total;
			$row[] 	= $machine_detail->Downtime;
			$row[] 	= $machine_detail->Detika;
			$row[] 	= $machine_detail->Detikb;
			$row[] 	= $machine_detail->Detikc;
			$row[] 	= $machine_detail->Detikd;
			$row[] 	= $machine_detail->Detike;
			$row[] 	= $machine_detail->Detikf;
			$row[] 	= $machine_detail->Menita;
			$row[] 	= $machine_detail->Menitb;
			$row[] 	= $machine_detail->Menitc;
			$row[] 	= $machine_detail->Menitd;
			$row[] 	= $machine_detail->Menite;
			$row[] 	= $machine_detail->Createdate;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			//"recordsTotal" => $this->machine_detail->count_all(),
			//"recordsFiltered" => $this->machine_detail->count_filtered(),
			//"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}
}