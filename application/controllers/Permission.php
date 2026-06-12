<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends CI_Controller {

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
    $this->load->model('permission_model', 'permission');
    $this->load->model('permissiongroup_model', 'group');
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Roles & Permission";
			$data['nama_halaman'] 	= "Permissions";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['group'] 					= $this->group->get_alls();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/roles/permission', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function permission_add()
  {
  	//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
	  	$this->_validation_permission();

	  	$data = array(
				'idpermissions_group' 	=> $this->input->post('idpermissions_group'),
				'code_class' 						=> $this->input->post('code_class'),
				'code_method' 					=> $this->input->post('code_method'),
				'code_url' 							=> $this->input->post('code_url'),
				'display_name' 					=> $this->input->post('display_name'),
				'display_icon' 					=> $this->input->post('display_icon'),
				'status' 								=> $this->input->post('status'),
				'type' 									=> $this->input->post('type'),
				'created_date'					=> date('Y-m-d H:i:s')
			);
			$insert = $this->permission->save($data);
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

  public function permission_list()
  {
  	$list 	= $this->permission->get_datatables();
  	//print_r($list);
		$data 	= array();
		$no 		= $_POST['start'];
		$noUrut = 0;
		$type 	= "";
		foreach ($list as $permission) {

			switch ($permission->type) {
				case 'TRUE':
					$type = '<button class="btn waves-effect waves-light btn-primary btn-sm btn-block">SIDEBAR</button>';
					break;

				case 'NAV':
					$type = '<button class="btn waves-effect waves-light btn-warning btn-sm btn-block">NAVBAR</button>';
					break;
				
				default:
					$type = '<button class="btn waves-effect waves-light btn-danger btn-sm btn-block">FUNCTION</button>';
					break;
			}

			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$permission->idpermissions."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>';
			$row[] = $permission->permissions_groupname;
			$row[] = $permission->display_name;
			$row[] = $permission->code_class."/".$permission->code_method;
			$row[] = $type;
			$row[] = $permission->status == 1 ? '<button class="btn waves-effect waves-light btn-success btn-sm">AKTIF</button>' : '<button class="btn waves-effect waves-light btn-inverse btn-sm">NON-AKTIF</button>';
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->permission->count_all(),
			"recordsFiltered" => $this->permission->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function permission_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->permission->get_by_id($id);
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

	public function permission_update()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_permission();

			$data = array(
				'idpermissions_group' 	=> $this->input->post('idpermissions_group'),
				'code_class' 						=> $this->input->post('code_class'),
				'code_method' 					=> $this->input->post('code_method'),
				'code_url' 							=> $this->input->post('code_url'),
				'display_name' 					=> $this->input->post('display_name'),
				'display_icon' 					=> $this->input->post('display_icon'),
				'status' 								=> $this->input->post('status'),
				'type' 									=> $this->input->post('type'),
				'updated_date' 					=> date('Y-m-d H:i:s')
			);
			$this->permission->update(array('idpermissions' => $this->input->post('kode')), $data);
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

	private function _validation_permission(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('idpermissions_group') == '')
		{
			$data['inputerror'][] = 'idpermissions_group';
			$data['error_string'][] = 'Permission Group is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('code_class') == '')
		{
			$data['inputerror'][] = 'code_class';
			$data['error_string'][] = 'Controller is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('code_method') == '')
		{
			$data['inputerror'][] = 'code_method';
			$data['error_string'][] = 'Function is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('code_url') == '')
		{
			$data['inputerror'][] = 'code_url';
			$data['error_string'][] = 'URL is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('display_name') == '')
		{
			$data['inputerror'][] = 'display_name';
			$data['error_string'][] = 'Display Name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('display_icon') == '')
		{
			$data['inputerror'][] = 'display_icon';
			$data['error_string'][] = 'Display Icon is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('type') == '')
		{
			$data['inputerror'][] = 'type';
			$data['error_string'][] = 'Type is required';
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