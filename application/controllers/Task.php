<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller {

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
    $this->load->model('task_model', 'task');
  }

  public function task_add()
  {
  	$data = array(
			'id_project' 				=> $this->input->post('id_project'),
			'nama_task' 				=> $this->input->post('nama_task'),
			'status_task' 			=> 'undone',
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$insert = $this->task->save($data);
		echo json_encode(array("status" => "ok"));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "ADD";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
  }

  public function task_list()
  {
  	$list = $this->task->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $task) {
			$no++;
			$noUrut++;
			$row = array();
			//add html for action

			$coret 	 = $task->status_task == 'done' ? "coret" : "";

			if ($task->status_task == 'done') {
				$row[] = '<input type="checkbox" value="undone" id="task_'.$task->id.'" onclick="update_task('."'".$task->id."'".')" checked="checked">';
			} else {
				$row[] = '<input type="checkbox" value="done" id="task_'.$task->id.'" onclick="update_task('."'".$task->id."'".')">';
			}

			$row[] 	= "<span class=".$coret.">".$task->nama_task."<span>";
			$row[] 	= $task->create_date;
			$row[] 	= '<button id="btn_deleted_task_'.$task->id.'" type="button"
									onclick="deleted_task('."'".$task->id."'".')" 
									class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon">
									<i class="icofont icofont-ui-delete delete_todo"></i>
								</button>';
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->task->count_all(),
			"recordsFiltered" => $this->task->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function task_edit($id)
	{
		$data = $this->task->get_noted_by_id($id);
		echo json_encode($data);

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "EDIT";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

	public function task_update()
	{
		$data = array(
			'status_task' 			=> $this->input->post('status_task'),
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$this->task->update(array('id' => $this->input->post('kode')), $data);
		echo json_encode(array("status" => TRUE));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "UPDATE";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

  public function task_deleted($id)
	{
		$data_delete 	= $this->task->get_by_id($id); //DATA DELETE
		$data 				= $this->task->delete_by_id($id);
		echo json_encode(array("status" => "ok"));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "DELETE";
		$log_data 	= json_encode($data_delete);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}
}
