<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

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
      
    $this->load->helper(array('url', 'form', 'cookie', 'file'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('upload_model', 'upload_model');
  }

  public function upload_list_by_project()
  {
  	$list = $this->upload_model->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $upload_model) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="open_modal_files_delete('."'".$upload_model->id."'".')"
                	class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon">
                	<i class="fa fa-times"></i>
                </a>
                <a href="#" onclick="open_modal_files('.$upload_model->id.')"
                	class="btn waves-effect waves-light btn-info btn-outline-info btn-icon">
                	<i class="fa fa-eye"></i>
                </a>';
			$row[] = $upload_model->title;
			$row[] = $upload_model->nama_file;
			$row[] = pathinfo($upload_model->nama_file, PATHINFO_EXTENSION);
			$row[] = $upload_model->create_date;
		
			$data[] = $row;
		}

		$output = array(
			"draw" 						=> $_POST['draw'],
			"recordsTotal" 		=> $this->upload_model->count_all(),
			"recordsFiltered" => $this->upload_model->count_filtered(),
			"data" 						=> $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function do_upload() 
  {
  	$kode_project = $this->input->post('kode_project');
  	$judul_file 	= $this->input->post('judul_file');

  	//PREPARING CONFIG FILE UPLOAD
  	$new_name                 = $kode_project."_".$_FILES['file']['name'];
    $config['file_name']      = $new_name;
    $config['upload_path'] 		= './files/uploads/files';
    $config['allowed_types'] 	= 'pdf|jpg|png';
    $config['max_size']  			= '8192';

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('file')) {
      $status = "error";
      $msg 		= $this->upload->display_errors();
    } else {

      $dataupload = $this->upload->data();
	    $data = array(
				'id_project'		=> $kode_project,
				'title'					=> $judul_file,
				'nama_file'			=> $dataupload['file_name'],
				'create_date'		=> date('Y-m-d H:i:s'),
				'create_by' 		=> $this->session->userdata('user_code')
			);
       
      $insert = $this->upload_model->save($data);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "ADD";
			$log_data 	= json_encode($data);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
    }
  }

  public function show_only_files($id)
	{
		$data = $this->upload_model->get_files_by_id($id);
		echo json_encode($data);

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "VIEW";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

	public function files_edit($id)
	{
		$data = $this->upload_model->get_by_id($id);
		echo json_encode($data);

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "EDIT";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

	public function files_hapus($id)
	{
		$cek_file 	= $this->upload_model->get_by_id($id);
		$files 			= "./files/uploads/files/".$cek_file->nama_file;
		$hapus_file = unlink($files);
		if ($hapus_file) {
			$data_delete 	= $this->upload_model->get_by_id($id); //DATA DELETE
			$data 				= $this->upload_model->delete_by_id($id);
			echo json_encode(array("status" => "ok"));

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "DELETE";
			$log_data 	= json_encode($data_delete);
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
		}
		/**/
	}
}