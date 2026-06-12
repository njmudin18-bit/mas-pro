<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noted extends CI_Controller {

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
    $this->load->model('noted_model', 'noted');
  }

  public function noted_add()
  {
  	$data = array(
			'id_project' 				=> $this->input->post('id_project'),
			'content' 					=> $this->input->post('catatan'),
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$insert = $this->noted->save($data);
		echo json_encode(array("status" => "ok"));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "ADD";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
  }

  public function noted_list_by_project($id)
  {
  	$response = $this->noted->get_by_id($id);
  	$text 		= "";
		if (count($response)> 0) {
			foreach ($response as $key => $value) {
				$text .= '<div class="mail-body">
										<div class="mail-body-content email-read">
											<div class="card">
												<div class="card-header">
													<button id="btn_hapus_'.$value->id.'" class="btn waves-effect waves-light btn-danger btn-square 
														m-r-10 float-right btn-sm" onclick="hapus_catatan('."'".$value->id."'".')">hapus</button>
													<button id="btn_edit_'.$value->id.'" class="btn waves-effect waves-light btn-warning btn-square 
														m-r-10 float-right btn-sm" onclick="edit_catatan('."'".$value->id."'".')">edit</button>&nbsp;
													
												</div>
												<div class="card-block">
													<div class="media m-b-20">
														<div class="media-left photo-table">
															<a href="#">
																<img class="media-object img-radius" src="'.base_url().'files/assets/images/idea.png" alt="'.$value->nama_pegawai.'">
															</a>
														</div>
														<div class="media-body photo-contant">
															<a href="#">
																<h6 class="user-name txt-primary">'.$value->nama_pegawai.'</h6>
															</a>
															<a class="user-mail txt-muted" href="#">
																<p class="user-mail txt-muted">on '.$value->create_date.'</p>
															</a>
															<div>'.$value->content.'</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<hr>';
			};
		} else {
			$text .= '<h5 class="col-form-label">
									Buat catatan tentang project <strong>disini</strong>.
								</h5>
								<hr>';
		}

		echo json_encode(
			array(
				"status" 		=> "success",
				"message" 	=> "sukses menampilkan data",
				"html" 			=> $text
			)
		);
		//echo json_encode($data);
  }

  public function noted_edit($id)
	{
		$data = $this->noted->get_noted_by_id($id);
		echo json_encode($data);

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "EDIT";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

	public function noted_update()
	{
		$data = array(
			'id_project' 				=> $this->input->post('id_project'),
			'content' 					=> $this->input->post('catatan'),
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$this->noted->update(array('id' => $this->input->post('kode')), $data);
		echo json_encode(array("status" => TRUE));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "UPDATE";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

  public function noted_deleted($id)
	{
		$data_delete 	= $this->noted->get_noted_by_id($id); //DATA DELETE
		$data 				= $this->noted->delete_by_id($id);
		echo json_encode(array("status" => "ok"));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "DELETE";
		$log_data 	= json_encode($data_delete);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}
}