<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qr extends CI_Controller
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
		$this->load->library(array('session', 'cart', 'ciqrcode'));

		$this->load->model('auth_model', 'auth');
		if ($this->auth->isNotLogin());

		//START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('document_model', 'document');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "QC";
			$data['nama_halaman'] 	= "Scan Barcode";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/qc/scan_barcode', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function generate_qr_sop()
	{
		$ip 	= APPS_IP_STATIC;
		$id 	= $this->uri->segment(3);

		header("Content-Type: image/png");
		$params['data'] = APPS_IP_STATIC . "omas-monitoring-projek/qr/preview_sop/" . $id;
		$this->ciqrcode->generate($params);
	}

	public function generate_qr()
	{
		/*$id 											= $this->uri->segment(3);
		$doc 											= $this->document->get_by_id($id);
		$dept 										= get_department_name($doc->id_dept);
		$data['department_name'] 	= $dept->DEPTNAME;
		$data['nomor_document'] 	= $doc->nomor_document;
		$data['nama_document'] 		= $doc->nama_document;
		$data['content_qr'] 			= base_url()."qr/preview_sop/".$doc->nomor_document;*/

		$data['group_halaman'] 		= "SOP";
		$data['nama_halaman'] 		= "SOP Preview";
		$data['icon_halaman'] 		= "icon-home";
		$data['perusahaan'] 			= $this->perusahaan->get_details();
		$this->load->view('adminx/qr/preview_qr', $data, FALSE);
	}

	public function generate_qr_banyak()
	{
		$array_id 	= $this->input->post('id_doc');
		$result 		= array();
		foreach ($array_id as $key => $value) {
			$doc 		= $this->document->get_by_id($value);
			$output = array(
				"id" 							=> $doc->id,
				"is_aktif" 				=> $doc->is_aktif,
				"id_doc_type" 		=> $doc->id_doc_type,
				"id_dept" 				=> $doc->id_dept,
				"nomor_document" 	=> $doc->nomor_document,
				"nama_document" 	=> $doc->nama_document,
				"content_qr" 			=> base_url() . "qr/preview_sop/" . $doc->nomor_document
			);

			$result[] = $output;
		}

		echo json_encode(
			array(
				"status_code" => 200,
				"status"			=> "success",
				"message"			=> "Data berhasil ditampilkan",
				"data"				=> $result
			)
		);
	}

	public function preview_sop()
	{
		$id 										= $this->uri->segment(3);
		$doc 										= $this->document->get_by_nomor($id);
		$data['nomor_document'] = $doc->nomor_document;
		$data['nama_document'] 	= $doc->nama_document;
		$data['file'] 					= base_url() . 'files/uploads/docx/' . $doc->nama_file;

		$data['group_halaman'] 	= "SOP";
		$data['nama_halaman'] 	= "SOP Preview";
		$data['icon_halaman'] 	= "icon-home";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$this->load->view('adminx/qr/preview_sop_new2', $data, FALSE);
	}
}
