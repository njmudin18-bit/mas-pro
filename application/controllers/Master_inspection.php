<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_inspection extends CI_Controller
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
		$this->load->model('variable_inspection_model', 'variable_inspection');
		$this->load->model('function_inspection_model', 'function_inspection');
		$this->load->model('attribute_inspection_model', 'attribute_inspection');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "QC";
			$data['nama_halaman'] 	= "Master Variable Inspeksi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/master_inspeksi/variable', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function variable_inspection_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$this->_validation_variable();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'standard' 			=> $this->input->post('standard'),
				'toleransi' 		=> $this->input->post('toleransi'),
				'tools' 				=> $this->input->post('tools'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'insert_date'		=> date('Y-m-d H:i:s'),
				'insert_by' 		=> $this->session->userdata('user_code')
			);
			$insert = $this->variable_inspection->save($data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "ADD";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function variable_inspection_list()
	{
		$list = $this->variable_inspection->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $variable_inspection) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit(' . "'" . $variable_inspection->id . "'" . ')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $variable_inspection->id . "'" . ')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $variable_inspection->aktivasi == 'Aktif' ? '<span class="badge badge-pill badge-success">' . strtoupper($variable_inspection->aktivasi) . '</span>' : '<span class="badge badge-pill badge-warning text-white">' . strtoupper($variable_inspection->aktivasi) . '</span>';
			$row[] = $variable_inspection->item_name;
			$row[] = $variable_inspection->standard;
			$row[] = $variable_inspection->toleransi == '' ? '-' : $variable_inspection->toleransi;
			$row[] = $variable_inspection->tools;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->variable_inspection->count_all(),
			"recordsFiltered" => $this->variable_inspection->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function variable_inspection_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->variable_inspection->get_by_id($id);
			echo json_encode($data);

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "EDIT";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function variable_inspection_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_variable();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'standard' 			=> $this->input->post('standard'),
				'toleransi' 		=> $this->input->post('toleransi'),
				'tools' 				=> $this->input->post('tools'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->variable_inspection->update(array('id' => $this->input->post('kode')), $data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "UPDATE";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function variable_inspection_deleted($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 		= $this->variable_inspection->get_by_id($id); //DATA DELETE
			$data 					= $this->variable_inspection->delete_by_id($id);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "DELETE";
			$log_data 	= json_encode($data_delete);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	private function _validation_variable()
	{
		$data                 = array();
		$data['error_string'] = array();
		$data['inputerror']   = array();
		$data['status']       = TRUE;

		if ($this->input->post('item_name') == '') {
			$data['inputerror'][] = 'item_name';
			$data['error_string'][] = 'Item Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('standard') == '') {
			$data['inputerror'][] = 'standard';
			$data['error_string'][] = 'Standard Inspeksi is required';
			$data['status'] = FALSE;
		}

		// if ($this->input->post('toleransi') == '') {
		// 	$data['inputerror'][] = 'toleransi';
		// 	$data['error_string'][] = 'Toleransi Inspeksi is required';
		// 	$data['status'] = FALSE;
		// }

		if ($this->input->post('tools') == '') {
			$data['inputerror'][] = 'tools';
			$data['error_string'][] = 'Tools Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('aktivasi') == '') {
			$data['inputerror'][] = 'aktivasi';
			$data['error_string'][] = 'Aktivasi is required';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	//FUNCTION INSPEKSI
	public function function()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "QC";
			$data['nama_halaman'] 	= "Master Function Inspeksi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/master_inspeksi/function', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function function_inspection_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$this->_validation_function();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'standard' 			=> $this->input->post('standard'),
				'toleransi' 		=> $this->input->post('toleransi'),
				'tools' 				=> $this->input->post('tools'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'insert_date'		=> date('Y-m-d H:i:s'),
				'insert_by' 		=> $this->session->userdata('user_code')
			);
			$insert = $this->function_inspection->save($data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "ADD";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function function_inspection_list()
	{
		$list = $this->function_inspection->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $function_inspection) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit(' . "'" . $function_inspection->id . "'" . ')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $function_inspection->id . "'" . ')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $function_inspection->aktivasi == 'Aktif' ? '<span class="badge badge-pill badge-success">' . strtoupper($function_inspection->aktivasi) . '</span>' : '<span class="badge badge-pill badge-warning text-white">' . strtoupper($function_inspection->aktivasi) . '</span>';
			$row[] = $function_inspection->item_name;
			$row[] = $function_inspection->standard;
			$row[] = $function_inspection->toleransi == '' ? '-' : $function_inspection->toleransi;
			$row[] = $function_inspection->tools;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->function_inspection->count_all(),
			"recordsFiltered" => $this->function_inspection->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function function_inspection_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->function_inspection->get_by_id($id);
			echo json_encode($data);

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "EDIT";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function function_inspection_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_function();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'standard' 			=> $this->input->post('standard'),
				'toleransi' 		=> $this->input->post('toleransi'),
				'tools' 				=> $this->input->post('tools'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->function_inspection->update(array('id' => $this->input->post('kode')), $data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "UPDATE";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function function_inspection_deleted($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 		= $this->function_inspection->get_by_id($id); //DATA DELETE
			$data 					= $this->function_inspection->delete_by_id($id);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "DELETE";
			$log_data 	= json_encode($data_delete);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	private function _validation_function()
	{
		$data                 = array();
		$data['error_string'] = array();
		$data['inputerror']   = array();
		$data['status']       = TRUE;

		if ($this->input->post('item_name') == '') {
			$data['inputerror'][] = 'item_name';
			$data['error_string'][] = 'Item Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('standard') == '') {
			$data['inputerror'][] = 'standard';
			$data['error_string'][] = 'Standard Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('tools') == '') {
			$data['inputerror'][] = 'tools';
			$data['error_string'][] = 'Tools Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('aktivasi') == '') {
			$data['inputerror'][] = 'aktivasi';
			$data['error_string'][] = 'Aktivasi is required';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	//ATTRIBUTE INSPEKSI
	public function attribute()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "QC";
			$data['nama_halaman'] 	= "Master Attribute Inspeksi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/master_inspeksi/attribute', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function attribute_inspection_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$this->_validation_attribute();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'insert_date'		=> date('Y-m-d H:i:s'),
				'insert_by' 		=> $this->session->userdata('user_code')
			);
			$insert = $this->attribute_inspection->save($data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "ADD";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function attribute_inspection_list()
	{
		$list = $this->attribute_inspection->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $attribute_inspection) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit(' . "'" . $attribute_inspection->id . "'" . ')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $attribute_inspection->id . "'" . ')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $attribute_inspection->aktivasi == 'Aktif' ? '<span class="badge badge-pill badge-success">' . strtoupper($attribute_inspection->aktivasi) . '</span>' : '<span class="badge badge-pill badge-warning text-white">' . strtoupper($attribute_inspection->aktivasi) . '</span>';
			$row[] = $attribute_inspection->item_name;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->attribute_inspection->count_all(),
			"recordsFiltered" => $this->attribute_inspection->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function attribute_inspection_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->attribute_inspection->get_by_id($id);
			echo json_encode($data);

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "EDIT";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function attribute_inspection_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_attribute();

			$data = array(
				'item_name' 		=> $this->input->post('item_name'),
				'aktivasi' 			=> $this->input->post('aktivasi'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->attribute_inspection->update(array('id' => $this->input->post('kode')), $data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "UPDATE";
			$log_data 	= json_encode($data);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function attribute_inspection_deleted($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 		= $this->attribute_inspection->get_by_id($id); //DATA DELETE
			$data 					= $this->attribute_inspection->delete_by_id($id);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "DELETE";
			$log_data 	= json_encode($data_delete);

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	private function _validation_attribute()
	{
		$data                 = array();
		$data['error_string'] = array();
		$data['inputerror']   = array();
		$data['status']       = TRUE;

		if ($this->input->post('item_name') == '') {
			$data['inputerror'][] = 'item_name';
			$data['error_string'][] = 'Item Inspeksi is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('aktivasi') == '') {
			$data['inputerror'][] = 'aktivasi';
			$data['error_string'][] = 'Aktivasi is required';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
