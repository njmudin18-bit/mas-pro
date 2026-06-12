<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

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
    $this->load->model('project_model', 'project');
    $this->load->model('kategori_model', 'kategori');
    $this->load->model('status_model', 'status');
    $this->load->model('institusi_model', 'institusi');
  }

  //PROJECT
	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) { 
			$data['group_halaman'] 		= "Project";
			$data['nama_halaman'] 		= "Daftar Project";
			$data['icon_halaman'] 		= "icon-layers";

			$data['karyawan'] 					= get_karyawan_by_dept();
			$data['department'] 				= get_department_att();
			$data['perusahaan'] 				= $this->perusahaan->get_details();
			$data['status_project'] 		= $this->status->get_alls();
			$data['kategori_project'] 	= $this->kategori->get_alls();
			$data['institusi_project'] 	= $this->institusi->get_alls();
      $data['no_document']        = "MAS/FO/IC/024 REV 01";

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/projex', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function project_details($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 		= "Project";
			//$data['nama_halaman'] 	= "Project Details";
			$data['icon_halaman'] 		= "icon-airplay";

			$new_id 									= base64_decode($id);
			$data['status_project'] 	= $this->status->get_alls();
			$data['perusahaan'] 			= $this->perusahaan->get_details();
			$data['project_details'] 	= $this->project->project_details($new_id);
			$project_det 							= $this->project->project_details($new_id);
			$data['nama_halaman'] 		= $project_det->nama_project;

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/projex_detailx', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function project_list()
  {
    $Draw            = intval($this->input->get("draw"));
    $Start           = intval($this->input->get("start"));
    $Length          = intval($this->input->get("length"));
    $StatusProjek    = $this->input->post('StatusProjek'); 
    $TanggalProjek   = $this->input->post('TanggalProjek'); 
    $Kategori        = $this->input->post('KategoriProjek');

    // Base SQL
    // $Sql = "SELECT 
    //             a.id_project, a.id_dept, a.nama_project, a.id_pic, a.project_url,
    //             a.project_description, a.project_progress, a.start_date, a.end_date,
    //             CONVERT(VARCHAR(19), a.create_date, 120) AS create_date,
    //             b.nama_status, 
    //             c.nama_kategori, 
    //             d.nama
    //         FROM table_project AS a
    //         LEFT JOIN table_status AS b 
    //             ON b.id_status = a.id_status
    //         LEFT JOIN table_kategori AS c 
    //             ON c.id_kategori = a.id_kategori
    //         LEFT JOIN table_institusi AS d 
    //             ON d.id_institusi = a.id_institusi
    //         WHERE 1=1";

    // // Kondisi tambahan jika bukan "All"
    // if ($StatusProjek !== "ALL") {
    //   $Sql .= " AND a.id_status = " . $this->db->escape($StatusProjek);
    // }
    // if ($TanggalProjek !== "ALL") {
    //   $Sql .= " AND CONVERT(char(7), a.create_date, 120) = " . $this->db->escape($TanggalProjek);
    // }
    // if ($Kategori !== "ALL") {
    //   $Sql .= " AND a.id_kategori = " . $this->db->escape($Kategori);
    // }

    // // Urutan
    // $Sql .= " ORDER BY a.create_date DESC";

    $Sql = "SELECT 
                a.id_project, a.id_dept, a.nama_project, a.id_pic, a.project_url,
                a.project_description, a.project_progress, a.start_date, a.end_date,
                CONVERT(VARCHAR(19), a.create_date, 120) AS create_date,
                b.nama_status, 
                c.nama_kategori, 
                d.nama
            FROM table_project AS a
            LEFT JOIN table_status AS b 
                ON b.id_status = a.id_status
            LEFT JOIN table_kategori AS c 
                ON c.id_kategori = a.id_kategori
            LEFT JOIN table_institusi AS d 
                ON d.id_institusi = a.id_institusi
            WHERE 1=1";

    // Kondisi tambahan jika bukan "All"
    if ($StatusProjek !== "ALL") {
        $Sql .= " AND a.id_status = " . $this->db->escape($StatusProjek);
    }
    if ($TanggalProjek !== "ALL") {
        // pakai start_date & end_date, bukan create_date
        $Sql .= " AND a.start_date <= EOMONTH(CAST(" . $this->db->escape($TanggalProjek . '-01') . " AS DATE))";
        $Sql .= " AND a.end_date >= CAST(" . $this->db->escape($TanggalProjek . '-01') . " AS DATE)";
    }
    if ($Kategori !== "ALL") {
        $Sql .= " AND a.id_kategori = " . $this->db->escape($Kategori);
    }

    // Urutan
    $Sql .= " ORDER BY a.create_date DESC";
    //echo $Sql; exit;
    // Eksekusi
    $Query  = $this->db->query($Sql);
    $Result = $Query->result();
    $Data 	              = [];
    $No                   = 1;
    foreach ($Result as $key => $Res) {
      $nama_pic = get_karyawan_details($Res->id_pic);
      $Isi      = "'".$Res->id_project."'";

      $Row    = array();
      $Row[]  = $No++;

      $Row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" target="_blank" href="'.base_url().'project/project_details/'.base64_encode($Res->id_project).'">Lihat Detail</a>
                    </div>
                  </div>
                </div>';
      $Row[]   = $Res->nama_project;
			if ($Res->project_progress == 100) {
				$Row[] = "<h5><span class='badge badge-success'>".$Res->project_progress."% </h5></span>";
			} elseif ($Res->project_progress >= 70 && $Res->project_progress < 100) {
				$Row[] = "<h5><span class='badge badge-info'>".$Res->project_progress."% </h5></span>";
			} elseif ($Res->project_progress >= 50 && $Res->project_progress < 70) {
				$Row[] = "<h5><span class='badge badge-primary'>".$Res->project_progress."% </h5></span>";
			} elseif ($Res->project_progress >= 30 && $Res->project_progress < 50) {
				$Row[] = "<h5><span class='badge badge-warning'>".$Res->project_progress."% </h5></span>";
			} elseif ($Res->project_progress > 0 && $Res->project_progress < 30) {
				$Row[] = "<h5><span class='badge badge-secondary'>".$Res->project_progress."% </h5></span>";
			} elseif ($Res->project_progress == 0) {
				$Row[] = "<h5><span class='badge badge-danger'>".$Res->project_progress."% </h5></span>";
			}

      $Row[] = $Res->nama_status;
			$Row[] = $Res->nama_kategori;
			$Row[] = $nama_pic->NAME;
			$Row[] = $Res->nama;
			$Row[] = $Res->project_url;
			$Row[] = $Res->start_date;
			$Row[] = $Res->end_date;
			$Row[] = $Res->create_date;

      $Data[] = $Row;
    }

    $Output = [
      "draw" 				    => $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 				    => $Data
    ];

    echo json_encode($Output);
  }

  public function project_list_OLD()
	{
		$list   = $this->project->get_datatables();
		$data   = array();
		$no 	  = $_POST['start'];
		$noUrut = 0;
		foreach ($list as $project) {
			$nama_pic = get_karyawan_details($project->id_pic);
			$no++;
			$noUrut++;
			$row   = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$project->id_project."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$project->id_project."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>
                <a href="'.base_url().'project/project_details/'.base64_encode($project->id_project).'"
                	class="btn waves-effect waves-light btn-info btn-outline-info btn-sm" target="_blank">
                	<i class="fa fa-eye"></i>
                </a>';
			//$row[] = $project->id_project;
			$row[] = $project->nama_project;
			if ($project->project_progress == 100) {
				$row[] = "<h5><span class='badge badge-success'>".$project->project_progress."% </h5></span>";
			} elseif ($project->project_progress >= 70 && $project->project_progress < 100) {
				$row[] = "<h5><span class='badge badge-info'>".$project->project_progress."% </h5></span>";
			} elseif ($project->project_progress >= 50 && $project->project_progress < 70) {
				$row[] = "<h5><span class='badge badge-primary'>".$project->project_progress."% </h5></span>";
			} elseif ($project->project_progress >= 30 && $project->project_progress < 50) {
				$row[] = "<h5><span class='badge badge-warning'>".$project->project_progress."% </h5></span>";
			} elseif ($project->project_progress > 0 && $project->project_progress < 30) {
				$row[] = "<h5><span class='badge badge-secondary'>".$project->project_progress."% </h5></span>";
			} elseif ($project->project_progress == 0) {
				$row[] = "<h5><span class='badge badge-danger'>".$project->project_progress."% </h5></span>";
			}
			//$row[] = $project->project_progress." %";
			$row[] = $project->nama_status;
			$row[] = $project->nama_kategori;
			$row[] = $nama_pic->NAME;
			$row[] = $project->nama;
			$row[] = $project->project_url;
			$row[] = $project->start_date;
			$row[] = $project->end_date;
			$row[] = $project->create_date;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->project->count_all(),
			"recordsFiltered" => $this->project->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function project_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){ 
			$this->_validation_project();

			$data = array(
				'id_dept' 						=> $this->input->post('kode_dept'),
				'nama_project' 				=> $this->input->post('nama_project'),
				'id_status' 					=> $this->input->post('status'),
				'id_kategori' 				=> $this->input->post('kategori'),
				'id_pic' 							=> $this->input->post('nama_pic'),
				'id_institusi' 				=> $this->input->post('institusi'),
				'project_url' 				=> $this->input->post('project_url'),
				'project_description' => $this->input->post('project_desc'),
				'project_progress' 		=> $this->input->post('progress'),
				'start_date' 					=> $this->input->post('start_date'),
				'end_date' 						=> $this->input->post('end_date'),
				'create_date'					=> date('Y-m-d H:i:s'),
				'create_by' 					=> $this->session->userdata('user_code')
			);
			$insert = $this->project->save($data);
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

	public function project_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1){
			$data = $this->project->get_by_id($id);
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

	public function project_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
			$this->_validation_project();
			$data = array(
				'id_dept' 						=> $this->input->post('kode_dept'),
				'nama_project' 				=> $this->input->post('nama_project'),
				'id_status' 					=> $this->input->post('status'),
				'id_kategori' 				=> $this->input->post('kategori'),
				'id_pic' 							=> $this->input->post('nama_pic'),
				'id_institusi' 				=> $this->input->post('institusi'),
				'project_url' 				=> $this->input->post('project_url'),
				'project_description' => $this->input->post('project_desc'),
				'project_progress' 		=> $this->input->post('progress'),
				'start_date' 					=> $this->input->post('start_date'),
				'end_date' 						=> $this->input->post('end_date'),
				'update_date'					=> date('Y-m-d H:i:s'),
				'update_by' 					=> $this->session->userdata('user_code')
			);
			$this->project->update(array('id_project' => $this->input->post('kode')), $data);
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

	public function project_progress_update()
	{
		$data = array(
			'project_progress' 		=> $this->input->post('progress'),
			'update_date'					=> date('Y-m-d H:i:s'),
			'update_by' 					=> $this->session->userdata('user_code')
		);
		$this->project->update(array('id_project' => $this->input->post('kode')), $data);
		echo json_encode(array("status" => TRUE));

		//ADDING TO LOG
		$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
		$log_type 	= "UPDATE";
		$log_data 	= json_encode($data);
		
		log_helper($log_url, $log_type, $log_data);
		//END LOG
	}

	public function project_hapus($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1){
			$data_delete 	= $this->project->get_by_id($id); //DATA DELETE
			$data 				= $this->project->delete_by_id($id);
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

	private function _validation_project(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('nama_project') == '')
		{
			$data['inputerror'][] = 'nama_project';
			$data['error_string'][] = 'Nama Project is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama_pic') == '')
		{
			$data['inputerror'][] = 'nama_pic';
			$data['error_string'][] = 'Nama PIC is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('project_url') == '')
		{
			$data['inputerror'][] = 'project_url';
			$data['error_string'][] = 'Project URL is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('project_desc') == '')
		{
			$data['inputerror'][] = 'project_desc';
			$data['error_string'][] = 'Project Description is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('start_date') == '')
		{
			$data['inputerror'][] = 'start_date';
			$data['error_string'][] = 'Tanggal Mulai is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('end_date') == '')
		{
			$data['inputerror'][] = 'end_date';
			$data['error_string'][] = 'Tanggal Selesai is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	//PROJECT TIME LINE
	public function project_time_line()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 		= "Project";
			$data['nama_halaman'] 		= "Project Time Line";
			$data['icon_halaman'] 		= "icon-layers";
			$data['perusahaan'] 			= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/projex_time_line', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function project_time_line_list()
	{
		echo json_encode(
											array(
												"status_code" => http_response_code(),
												"status" => "success",
												"message" => "Sukses menampilkan data",
												"data" => $this->project->get_data_time_line()
											)
										);
	}

	//STATUS
	public function project_status()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Project";
			$data['nama_halaman'] 	= "Daftar Project Status";
			$data['icon_halaman'] 	= "icon-bookmark";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/status', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function project_status_list()
	{
		$list = $this->status->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $status) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$status->id_status."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$status->id_status."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $status->id_status;
			$row[] = $status->nama_status;
			$row[] = $status->create_date;
			$row[] = $status->create_by;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->kategori->count_all(),
			"recordsFiltered" => $this->kategori->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function project_status_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_status();

			$data = array(
				'nama_status' 		=> $this->input->post('nama_status'),
				'create_date'			=> date('Y-m-d H:i:s'),
				'create_by' 			=> $this->session->userdata('user_code')
			);
			$insert = $this->status->save($data);
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

	public function project_status_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {

			$data = $this->status->get_by_id($id);
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

	public function project_status_update()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_status();
			$data = array(
				'nama_status'		=> $this->input->post('nama_status'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->status->update(array('id_status' => $this->input->post('kode')), $data);
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

	public function project_status_hapus($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 	= $this->status->get_by_id($id); //DATA DELETE
			$data 				= $this->status->delete_by_id($id);
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

	private function _validation_status(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('nama_status') == '')
		{
			$data['inputerror'][] = 'nama_status';
			$data['error_string'][] = 'Nama Status is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	//KATEGORI
	public function project_kategori()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Project";
			$data['nama_halaman'] 	= "Daftar Project Kategori";
			$data['icon_halaman'] 	= "icon-layers";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/kategorix', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function project_kategori_list()
	{
		$list = $this->kategori->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $kategori) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$kategori->id_kategori."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$kategori->id_kategori."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $kategori->id_kategori;
			$row[] = $kategori->nama_kategori;
			$row[] = $kategori->create_date;
			$row[] = $kategori->create_by;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->kategori->count_all(),
			"recordsFiltered" => $this->kategori->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function project_kategori_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_kategori();

			$data = array(
				'nama_kategori' 	=> $this->input->post('nama_kategori'),
				'create_date'			=> date('Y-m-d H:i:s'),
				'create_by' 			=> $this->session->userdata('user_code')
			);
			$insert = $this->kategori->save($data);
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

	public function project_kategori_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {

			$data = $this->kategori->get_by_id($id);
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

	public function project_kategori_update()
	{
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_kategori();
			$data = array(
				'nama_kategori' => $this->input->post('nama_kategori'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->kategori->update(array('id_kategori' => $this->input->post('kode')), $data);
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

	public function project_kategori_hapus($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 	= $this->kategori->get_by_id($id); //DATA DELETE
			$data 				= $this->kategori->delete_by_id($id);
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

	private function _validation_kategori(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('nama_kategori') == '')
		{
			$data['inputerror'][] = 'nama_kategori';
			$data['error_string'][] = 'Nama Kategori is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	//INSTITUSI
	public function project_institusi()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Project";
			$data['nama_halaman'] 	= "Daftar Institusi";
			$data['icon_halaman'] 	= "icon-layers";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/project/institusix', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function project_institusi_list()
	{
		$list = $this->institusi->get_datatables();
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $institusi) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$institusi->id_institusi."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$institusi->id_institusi."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $institusi->id_institusi;
			$row[] = $institusi->nama;
			$row[] = $institusi->alamat;
			$row[] = $institusi->create_date;
			$row[] = $institusi->create_by;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->institusi->count_all(),
			"recordsFiltered" => $this->institusi->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function project_institusi_add()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) { 
			$this->_validation_institusi();

			$data = array(
				'nama' 						=> $this->input->post('nama_institusi'),
				'alamat' 					=> $this->input->post('alamat_institusi'),
				'create_date'			=> date('Y-m-d H:i:s'),
				'create_by' 			=> $this->session->userdata('user_code')
			);
			$insert = $this->institusi->save($data);
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

	public function project_institusi_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data = $this->institusi->get_by_id($id);
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

	public function project_institusi_update()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$this->_validation_institusi();
			$data = array(
				'nama' 					=> $this->input->post('nama_institusi'),
				'alamat' 				=> $this->input->post('alamat_institusi'),
				'update_date'		=> date('Y-m-d H:i:s'),
				'update_by' 		=> $this->session->userdata('user_code')
			);
			$this->institusi->update(array('id_institusi' => $this->input->post('kode')), $data);
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

	public function project_institusi_hapus($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data_delete 	= $this->institusi->get_by_id($id); //DATA DELETE
			$data 				= $this->institusi->delete_by_id($id);
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

	private function _validation_institusi(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('nama_institusi') == '')
		{
			$data['inputerror'][] = 'nama_institusi';
			$data['error_string'][] = 'Nama Institusi is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('alamat_institusi') == '')
		{
			$data['inputerror'][] = 'alamat_institusi';
			$data['error_string'][] = 'Alamat Institusi is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}