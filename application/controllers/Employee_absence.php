<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Employee_absence extends CI_Controller
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
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('absence_model', 'absence');
    $this->load->model('typeabsensi_model', 'type');

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Personalia & GA";
      $data['nama_halaman']     = "Daftar Ketidakhadiran";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['type_absensi']     = $this->type->get_all_data();
      $data['department_att'] 	= get_department_att();
      $data['DeptList'] 	      = get_department_att();
      $data['DEPTID']           = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']         = $this->session->userdata('user_dept_name');

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/pga/employee_absence', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function absence_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_absence();

      $jenisID           = $this->input->post('JenisID');
      // Generate nomor request
      $Nomor             = $this->absence->generateAbsensiNumber($jenisID);
      // Ambil data form
      $DeptID            = $this->input->post('DeptID');
      $EmployeeID        = $this->input->post('EmployeeID');
      $StartDate         = $this->input->post('StartDate');
      $EndDate           = $this->input->post('EndDate');
      $Notes             = ucfirst($this->input->post('Notes'));

      $start             = new DateTime($StartDate);
      $end               = new DateTime($EndDate);

      if ($end < $start) {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "End Date tidak boleh lebih kecil dari Start Date",
        ]);

        return;
      }

      // Hitung total hari (inklusi, jadi +1)
      $interval          = $start->diff($end);
      $TotalDays         = $interval->days + 1;

      $Files = null;
      if (!empty($_FILES['Files']['name'])) {
        $config['upload_path']   = './files/uploads/absensi';
        $config['allowed_types'] = 'pdf|png';
        $config['max_size']      = 3072; // 3MB dalam KB
        $ext                     = pathinfo($_FILES['Files']['name'], PATHINFO_EXTENSION);
        $config['file_name']     = $Nomor.'.'.strtolower($ext);
        $config['overwrite']     = false;

        // Pastikan folder upload ada
        if (!is_dir($config['upload_path'])) {
          mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('Files')) {
          echo json_encode(
            array(
              'status_code'  => 500, 
              'status'       => 'error', 
              'message'      => $this->upload->display_errors('', '')
            )
          );
          exit;
        } else {
          $uploadData = $this->upload->data();
          $Files      = $uploadData['file_name'];
        }
      }

      // Data header
      $FirstData = array(
        'Nomor'         => $Nomor,
        'EmployeeID'    => $EmployeeID,
        'AbsenceTypeID' => $jenisID,
        'StartDate'     => $StartDate,
        'EndDate'       => $EndDate,
        'TotalDays'     => $TotalDays,
        'Notes'         => $Notes,
        'Files'         => $Files,
        'isApproved'    => 'P',
        'CreatedDate'   => date('Y-m-d H:i:s'),
        'CreatedBy'     => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Insert = $this->ABSENSI->insert('Trans_EmployeeAbsence', $FirstData);
      if ($Insert) {
        echo json_encode(
          array(
            'status_code'   => 200,
            'status'        => 'success', 
            'message'       => 'Data berhasil disimpan.'
          )
        );
      } else {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error', 
            'message'      => 'Gagal menyimpan data.'
          )
        );
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }

  public function absence_approved()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $AbsenceID      = $this->input->post('Id');
      $isApproved     = $this->input->post('isApproved');
      
      $FirstData = array(
        'isApproved'    => $isApproved,
        'ApprovedDate'  => date('Y-m-d H:i:s'),
        'ApprovedBy'    => $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_EmployeeAbsence', $FirstData, array('AbsenceID' => $AbsenceID));
      if ($Update) {
        echo json_encode(
          array(
            'status_code'   => 200,
            'status'        => 'success', 
            'message'       => 'Data berhasil disimpan.'
          )
        );
      } else {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error', 
            'message'      => 'Gagal menyimpan data.'
          )
        );
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }

  public function absence_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$DeptID 	      = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetEmployeeAbsence @StartDate = ?, @EndDate = ?, @DeptIDs = ?";
    $Query          = $this->ABSENSI->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->isApproved == 'P' ? 'Y' : 'P';
      $ApLbl  = $value->isApproved == 'P' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->AbsenceID."'";
      $Isi2   = "'".$value->AbsenceID."', '".$ApSts."', '".$ApLbl."'";
      $Url    = base_url()."files/uploads/absensi/".$value->Files;

      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="#" onclick="approved('.$Isi2.')">'.$ApLbl.'</a>
                    </div>
                  </div>
                </div>';
      $row[]  = ($value->Files != NULL) ? '<a href="'.$Url.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $row[]  = $value->Nomor;
      //$row[]  = '<button class="btn '.$value->isApprovedClass.' btn-sm btn-block" onclick="approved('.$Isi2.')">'.$ApLbl.'</button>';
      $row[]  = $value->isApproved == 'Y' ? 'APPROVED' : 'PENDING';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->NAME;
      $row[]  = $value->AbsenceName;
      $row[]  = $value->StartDate;
      $row[]  = $value->EndDate;
      $row[]  = $value->TotalDays;
      $row[]  = $value->Notes;
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;
      $row[]  = $value->ApprovedDate;
      $row[]  = $value->ApprovedName;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function absence_edit($id)
  {
    $data = $this->absence->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function absence_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_absence();
        
    // Ambil data form
    $Nomor             = $this->input->post('Nomor');
    $DeptID            = $this->input->post('DeptID');
    $EmployeeID        = $this->input->post('EmployeeID');
    $jenisID           = $this->input->post('JenisID');
    $StartDate         = $this->input->post('StartDate');
    $EndDate           = $this->input->post('EndDate');
    $Notes             = ucfirst($this->input->post('Notes'));

    $start             = new DateTime($StartDate);
    $end               = new DateTime($EndDate);

    if ($end < $start) {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "End Date tidak boleh lebih kecil dari Start Date",
      ]);

      return;
    }

    // Hitung total hari (inklusi, jadi +1)
    $interval          = $start->diff($end);
    $TotalDays         = $interval->days + 1;

    //echo $Nomor; exit;

    // Ambil data lama
    $oldData = $this->ABSENSI->get_where('Trans_EmployeeAbsence', ['Nomor' => $Nomor])->row();
    if (!$oldData) {
      echo json_encode([
        'status_code' => 404,
        'status'      => 'error',
        'message'     => 'Data tidak ditemukan.'
      ]);

      return;
    }

    $Files = $oldData->Files;

    // Hapus file lama kalau file di server tidak ada
    if (!empty($Files) && !file_exists('./files/uploads/absensi/' . $Files)) {
      $Files = null;
    }

    // Upload file baru jika ada
    if (!empty($_FILES['Files']['name'])) {
      $config['upload_path']   = './files/uploads/absensi';
      $config['allowed_types'] = 'pdf|png';
      $config['max_size']      = 3072; // 3MB
      $ext                     = pathinfo($_FILES['Files']['name'], PATHINFO_EXTENSION);
      $config['file_name']     = $Nomor . '.' . strtolower($ext);
      $config['overwrite']     = true;

      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, true);
      }

      $this->load->library('upload', $config);

      if (!$this->upload->do_upload('Files')) {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => $this->upload->display_errors('', '')
        ]);

        return;
      } else {
        // Hapus file lama jika ada
        if (!empty($oldData->Files) && file_exists('./files/uploads/absensi/' . $oldData->Files)) {
          @unlink('./files/uploads/absensi/' . $oldData->Files);
        }

        $uploadData = $this->upload->data();
        $Files      = $uploadData['file_name'];
      }
    }

    // Data header
    $FirstData = array(
      'EmployeeID'    => $EmployeeID,
      'AbsenceTypeID' => $jenisID,
      'StartDate'     => $StartDate,
      'EndDate'       => $EndDate,
      'TotalDays'     => $TotalDays,
      'Notes'         => $Notes,
      'Files'         => $Files, // null jika tidak upload
      'UpdatedDate'   => date('Y-m-d H:i:s'),
      'UpdatedBy'     => $this->session->userdata('user_id')
    );

    //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

    $this->ABSENSI->where('Nomor', $Nomor);
    $Update = $this->ABSENSI->update('Trans_EmployeeAbsence', $FirstData);

    if ($Update) {
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data berhasil diperbarui.'
      ]);
    } else {
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Gagal memperbarui data.'
      ]);
    }
  }

  public function absence_deleted($id)
  {
    $data_delete    = $this->absence->get_by_id($id); //DATA DELETE
    $data           = $this->absence->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_absence()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('EmployeeID') == '') {
      $data['inputerror'][]   = 'EmployeeID';
      $data['error_string'][] = 'Pegawai is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Departemen is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JenisID') == '') {
      $data['inputerror'][]   = 'JenisID';
      $data['error_string'][] = 'Jenis Ketidakhadiran is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('StartDate') == '') {
      $data['inputerror'][]   = 'StartDate';
      $data['error_string'][] = 'Start Date is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EndDate') == '') {
      $data['inputerror'][]   = 'EndDate';
      $data['error_string'][] = 'End Date is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
