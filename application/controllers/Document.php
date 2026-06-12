<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Document extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *    http://example.com/index.php/welcome
	 *  - or -
	 *    http://example.com/index.php/welcome/index
	 *  - or -
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

		$this->load->helper(array('url', 'form', 'cookie', 'file'));
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
		$this->load->model('department_model', 'department');
		$this->load->model('document_type_model', 'document_type');
		$this->load->model('document_model', 'document');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$data['group_halaman'] 	= "Document";
			$data['nama_halaman'] 	= "Cari Document";
			$data['icon_halaman'] 	= "icon-bookmark";

			$data['department'] = $this->department->get_all();
			$data['perusahaan'] = $this->perusahaan->get_details();
			$this->load->view('adminx/document/cari_document', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function document_input()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$data['group_halaman'] 	= "Document";
			$data['nama_halaman'] 	= "Input Document";
			$data['icon_halaman'] 	= "icon-bookmark";

			$data['type_doc'] 			= $this->document_type->get_all();
			$data['department'] 		= get_department_att();
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$this->load->view('adminx/document/document_input', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function document_add()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_document();

			$nama_department = $this->input->post('nama_department');
			$type_doc = $this->input->post('type_doc');
			$aktif = $this->input->post('aktif');
			$no_doc = strtoupper($this->input->post('no_doc'));
			$judul_doc = ucwords($this->input->post('judul_doc'));
			$now = date('Y-m-d H:i:s');
			$max_upload = 1024 * 15;

			//PREPARING CONFIG FILE UPLOAD
			$new_name = $type_doc . "_" . $nama_department . "_" . $_FILES['file']['name'];
			$config['file_name'] = $new_name;
			$config['upload_path'] = './files/uploads/docx';
			$config['allowed_types'] = 'pdf';
			$config['max_size'] = $max_upload;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file')) {
				$status = "error";
				$msg = $this->upload->display_errors();
			} else {

				$dataupload = $this->upload->data();
				$data = array(
					'is_aktif' => $aktif,
					'id_doc_type' => $type_doc,
					'id_dept' => $nama_department,
					'nomor_document' => $no_doc,
					'nama_document' => $judul_doc,
					'nama_file' => $dataupload['file_name'],
					'create_date' => $now,
					'create_by' => $this->session->userdata('user_code')
				);

				$insert = $this->document->save($data);
				echo json_encode(array("status" => "ok"));

				//ADDING TO LOG
				$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
				$log_type 	= "ADD";
				$log_data 	= json_encode($data);

				log_helper($log_url, $log_type, $log_data);
				//END LOG
			}
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	public function document_list()
	{
		$list = $this->document->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$noUrut = 0;
		foreach ($list as $document) {
			$department_name = get_department_name($document->id_dept);
			$no++;
			$noUrut++;
			$row = array();
			//add html for action
			$row[] = $no;
			$row[] = '<input type="checkbox" class="ids" name="id" value="' . $document->id . '">
								<input type="hidden" id="nomor_document" name="nomor_document" value="' . $document->nomor_document . '">';
			$row[] = '<a href="javascript:void(0)" onclick="edit(' . "'" . $document->id . "'" . ')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $document->id . "'" . ')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>
                <a href="' . base_url() . 'files/uploads/docx/' . $document->nama_file . '"
                	class="btn waves-effect waves-light btn-info btn-outline-info btn-sm" target="_blank" title="Lihat files ' . $document->nama_file . '">
                	<i class="fa fa-eye"></i>
                </a>';
			$row[] = $document->is_aktif == 'Ya' ? '<h5><span class="badge badge-primary">' . strtoupper($document->is_aktif) . '</span></h5>' : '<h5><span class="badge badge-secondary">' . strtoupper($document->is_aktif) . '</span></h5>';
			$row[] = $document->nama_type;
			$row[] = $document->nomor_document;
			$row[] = $document->nama_document;
			$row[] = '<h5><span class="badge badge-success">' . $department_name->DEPTNAME . '</span>';
			$row[] = $document->nama_file;
			$row[] = $document->create_date . '<br><span class="badge badge-info">Uploader : ' . $document->create_by . '</span>';

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->document->count_all(),
			"recordsFiltered" => $this->document->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function document_list_result()
	{
		// POST data
		$postData = $this->input->post('search');
		$data = $this->document->get_document($postData);

		echo json_encode($data);
	}

	public function document_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->document->get_by_id($id);
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

	public function document_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_document();

			$id = $this->input->post('kode');
			$nama_department = $this->input->post('nama_department');
			$type_doc = $this->input->post('type_doc');
			$aktif = $this->input->post('aktif');
			$no_doc = strtoupper($this->input->post('no_doc'));
			$judul_doc = ucwords($this->input->post('judul_doc'));
			$now = date('Y-m-d H:i:s');
			$max_upload = 1024 * 15;
			$cek_empty = $_FILES['file']['name'];

			if (!empty($cek_empty)) {

				//HAPUS FILE LAMA
				$cek_file = $this->document->get_by_id($id);
				$files = "./files/uploads/docx/" . $cek_file->nama_file;
				$hapus_file = unlink($files);

				//PREPARING CONFIG FILE UPLOAD
				$new_name = $type_doc . "_" . $nama_department . "_" . $_FILES['file']['name'];
				$config['file_name'] = $new_name;
				$config['upload_path'] = './files/uploads/docx';
				$config['allowed_types'] = 'pdf';
				$config['max_size'] = $max_upload;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('file')) {
					$status = "error";
					$msg = $this->upload->display_errors();
				} else {

					$dataupload = $this->upload->data();
					$data = array(
						'is_aktif' => $aktif,
						'id_doc_type' => $type_doc,
						'id_dept' => $nama_department,
						'nomor_document' => $no_doc,
						'nama_document' => $judul_doc,
						'nama_file' => $dataupload['file_name'],
						'update_date' => $now,
						'update_by' => $this->session->userdata('user_code')
					);
				}
			} else {
				//echo "kosong";
				$data = array(
					'is_aktif' => $aktif,
					'id_doc_type' => $type_doc,
					'id_dept' => $nama_department,
					'nomor_document' => $no_doc,
					'nama_document' => $judul_doc,
					'update_date' => $now,
					'update_by' => $this->session->userdata('user_code')
				);
			}

			$this->document->update(array('id' => $this->input->post('kode')), $data);
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

	public function document_deleted($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 	= $this->document->get_by_id($id); //DATA DELETE
			$cek_file 		= $this->document->get_by_id($id);
			$files 				= "./files/uploads/docx/" . $cek_file->nama_file;
			$hapus_file 	= unlink($files);
			if ($hapus_file) {
				$data = $this->document->delete_by_id($id);
				echo json_encode(array("status" => "ok"));

				//ADDING TO LOG
				$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
				$log_type 	= "DELETE";
				$log_data 	= json_encode($data_delete);

				log_helper($log_url, $log_type, $log_data);
				//END LOG
			}
		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

	private function _validation_document()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('nama_department') == '') {
			$data['inputerror'][] = 'nama_department';
			$data['error_string'][] = 'Nama Department is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('type_doc') == '') {
			$data['inputerror'][] = 'type_doc';
			$data['error_string'][] = 'Type Document is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('aktif') == '') {
			$data['inputerror'][] = 'aktif';
			$data['error_string'][] = 'Aktif is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('no_doc') == '') {
			$data['inputerror'][] = 'no_doc';
			$data['error_string'][] = 'Nomor Document is required';
			$data['status'] = FALSE;
		}

		if ($this->input->post('judul_doc') == '') {
			$data['inputerror'][] = 'judul_doc';
			$data['error_string'][] = 'Judul Document is required';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function get_nama_divisi()
	{
		$id 	= $this->input->post('id');
		$data = get_department_name($id);

		echo json_encode($data);
	}
}
