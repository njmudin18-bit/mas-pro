<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type_document extends CI_Controller {

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
    $this->load->model('document_type_model', 'doc_type');
  }

  public function index()
  {
  	//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
	  	$data['group_halaman'] 		= "Document";
			$data['nama_halaman'] 		= "Type Document";
			$data['icon_halaman'] 		= "icon-bookmark";
	  	$data['perusahaan'] 			= $this->perusahaan->get_details();

	  	//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/document/type_document', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function doc_type_add()
  {
  	//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){ 
	  	$this->_validation_type_doc();
	  	$data = array(
				'nama_type' 	=> strtoupper($this->input->post('type_doc')),
				'create_date'	=> date('Y-m-d H:i:s'),
				'create_by' 	=> $this->session->userdata('user_code')
			);
			$insert = $this->doc_type->save($data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "ADD";
			$log_data 	= json_encode($data);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  public function doc_type_list()
  {
  	$list = $this->doc_type->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $doc_type) {
			$no++;
			$noUrut++;
			$row 	 = array();
			
			$row[] = $no;
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$doc_type->id."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$doc_type->id."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $doc_type->id;
			$row[] = $doc_type->nama_type;
			$row[] = $doc_type->create_date;
			$row[] = $doc_type->create_by;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->doc_type->count_all(),
			"recordsFiltered" => $this->doc_type->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function doc_type_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1){
			$data = $this->doc_type->get_by_id($id);
			echo json_encode($data);

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "EDIT";
			$log_data 	= json_encode($data);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}			
	}

	public function doc_type_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
			$data = array(
				'nama_type' 		=> strtoupper($this->input->post('type_doc')),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->doc_type->update(array('id' => $this->input->post('kode')), $data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "UPDATE";
			$log_data 	= json_encode($data);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

  public function doc_type_deleted($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1){
			$data_delete 	= $this->doc_type->get_by_id($id); //DATA DELETE
			$data 				= $this->doc_type->delete_by_id($id);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "DELETE";
			$log_data 	= json_encode($data_delete);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
		  echo json_encode(array("status" => "forbidden"));
		}
	}

	private function _validation_type_doc(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('type_doc') == '')
		{
			$data['inputerror'][] = 'type_doc';
			$data['error_string'][] = 'Type Document is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}