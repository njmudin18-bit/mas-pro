<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissiongroup extends CI_Controller {

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
    $this->load->model('permissiongroup_model', 'group');
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Roles & Permission";
			$data['nama_halaman'] 	= "Permission Group";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/roles/permission_group', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function permissiongroup_add()
  {
  	//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
	  	$this->_validation_permissiongroup();

	  	$data = array(
				'permissions_groupname' => $this->input->post('permissions_groupname'),
				'display_icon' 					=> $this->input->post('display_icon'),
				'status' 								=> $this->input->post('status'),
				'created_date'					=> date('Y-m-d H:i:s')
			);
			$insert = $this->group->save($data);
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

  public function permissiongroup_list()
  {
  	$list = $this->group->get_datatables();
  	//print_r($list);
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $group) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$group->idpermissions_group."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>';
			$row[] = $group->permissions_groupname;
			$row[] = '<i class="'.$group->display_icon.'"></i>';
			$row[] = $group->status == 1 ? '<button class="btn waves-effect waves-light btn-success btn-sm">AKTIF</button>' : '<button class="btn waves-effect waves-light btn-inverse btn-sm">NON-AKTIF</button>';
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$group->idpermissions_group."'".')"
									class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
									<i class="feather icon-sliders"></i>
								</a>';
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->group->count_all(),
			"recordsFiltered" => $this->group->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function permissiongroup_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->group->get_by_id($id);
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

	public function permissiongroup_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_permissiongroup();

			$data = array(
				'permissions_groupname' => $this->input->post('permissions_groupname'),
				'display_icon' 					=> $this->input->post('display_icon'),
				'status' 								=> $this->input->post('status'),
				'created_date' 					=> date('Y-m-d H:i:s')
			);
			$this->group->update(array('idpermissions_group' => $this->input->post('kode')), $data);
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

	private function _validation_permissiongroup(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('permissions_groupname') == '')
		{
			$data['inputerror'][] = 'permissions_groupname';
			$data['error_string'][] = 'Permission Group Name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('display_icon') == '')
		{
			$data['inputerror'][] = 'display_icon';
			$data['error_string'][] = 'Display Icon is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('status') == '')
		{
			$data['inputerror'][] = 'status';
			$data['error_string'][] = 'Roles Status is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}