<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Certificates extends CI_Controller
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
		$this->load->model('trial_model', 'trial');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $data['group_halaman'] 	= "Doc. Control";
			$data['nama_halaman'] 	= "Daftar Sertifikat";
			$data['icon_halaman'] 	= "icon-bookmark";
      $data['DeptList'] 	    = get_department_att();
      $data['DEPTID']         = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']       = $this->session->userdata('user_dept_name');

			$data['department']     = $this->department->get_all();
			$data['perusahaan']     = $this->perusahaan->get_details();
			$this->load->view('adminx/dc/sertifikat/index', $data, FALSE);

			//ADDING TO LOG
			$log_url 		            = base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			redirect('errorpage/error403');
		}
	}

  public function certificates_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $NoExpired          = $this->input->post('NoExpire');
      $ReminderStatus     = $this->input->post('ReminderStatus');
      //echo $NoExpired;
      $this->_validation_certificates($NoExpired, $ReminderStatus);

      // Ambil data form
      $DeptID             = $this->input->post('DeptID');
      $CertificateName    = strtoupper($this->input->post('CertificateName'));
      $CertificateCode    = strtoupper($this->input->post('CertificateCode'));
      $CertificateStatus  = $this->input->post('CertificateStatus');
      $IssueDate          = $this->input->post('IssueDate');
      //$ExpiryDate         = $NoExpired == 'on' ? $this->input->post('ExpiryDate') : '';
      $NoExpire           = $this->input->post('NoExpire'); // on/off
      $ExpiryDate         = ($NoExpire === 'on') ? null : $this->input->post('ExpiryDate');
      $RevokedDate        = $this->input->post('RevokedDate');
      $RenewedDate        = $this->input->post('RenewedDate');
      $NextSurvDue        = $this->input->post('NextSurvDue');
      $Description        = ucfirst($this->input->post('Description'));
      $ReminderIn         = $this->input->post('ReminderIn');
      $Day                = date('Ymd');

      //echo json_encode(array("status" => "error", "data" => $Data)); exit;

      // CEK CertificateCode
      $Cek = $this->BJGMAS01->get_where('Trans_Certificates', array('CertificateCode' => $CertificateCode));
      if ($Cek->num_rows() > 0) {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error',
            'message'      => 'Certificate Number sudah ada.'
          )
        );
        exit;
      }

      // Upload file (opsional)
      $Files = null;
      if (!empty($_FILES['Files']['name'])) {
        $config['upload_path']   = './files/uploads/sertifikat';
        $config['allowed_types'] = 'pdf|png';
        $config['max_size']      = 3072; // 3MB dalam KB
        $ext                     = pathinfo($_FILES['Files']['name'], PATHINFO_EXTENSION);
        $config['file_name']     = $CertificateCode.'_'.$Day.'.'.strtolower($ext);
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

      // Data
      $Data = array(
        'DeptID'          => $DeptID,
        'CertificateName' => $CertificateName,
        'CertificateCode' => $CertificateCode,
        'Description'     => $Description,
        'IssueDate'       => empty($IssueDate) ? null : $IssueDate,
        'ExpiryDate'      => empty($ExpiryDate) ? null : $ExpiryDate,
        'NoExpired'       => $NoExpired,
        'RevokedDate'     => empty($RevokedDate) ? null : $RevokedDate,
        'RenewedDate'     => empty($RenewedDate) ? null : $RenewedDate,
        'NextSurvDue'     => empty($NextSurvDue) ? null : $NextSurvDue,
        'Files'           => $Files, // null jika tidak upload
        'Status'          => $CertificateStatus,
        'ReminderStatus'  => $ReminderStatus,
        'ReminderIn'      => $ReminderIn,
        'CreateDate'      => date('Y-m-d H:i:s'),
        'CreateBy'        => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $Data)); exit;

      // Simpan ke database
      $Insert = $this->BJGMAS01->insert('Trans_Certificates', $Data);
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
  }

  public function certificates_list()
	{
		$draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('StartDate');
    $EndDate        = $this->input->post('EndDate');
    $DeptID         = $this->input->post('DeptID');
    $DeptCondition  = !empty($DeptID) ? " AND DeptID = '$DeptID' " : "";

    //CONVERT(VARCHAR(19), CreateDate, 120) AS CreateDate
    $Sql         = "SELECT 
                        Id, DeptID, CertificateName, CertificateCode, Description, 
                        IssueDate, ExpiryDate, NoExpired, RevokedDate, RenewedDate, NextSurvDue,
                        Files, UPPER(ReminderStatus) AS ReminderStatus, 
                        UPPER(ReminderIn) AS ReminderIn, UPPER(Status) AS Status, CreateBy,
                        CONVERT(VARCHAR(19), CreateDate, 120) AS CreateDate
                    FROM Trans_Certificates
                    WHERE CAST(CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
                      $DeptCondition
                    ORDER BY CreateDate DESC";
    $Query       = $this->BJGMAS01->query($Sql);
    $Result      = $Query->result();
		$Data        = [];
		$No 		     = 1;

    foreach ($Result as $key => $value) {
      $Isi          = "'".$value->Id."'";
      $Url          = base_url()."files/uploads/sertifikat/".$value->Files;

			$Data[] = array(
				$No++,
				'<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
            </div>
          </div>
        </div>',
        ($value->Files != NULL) ? '<a href="'.$Url.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </a>' : '',
				$value->Status,
        $this->get_departemen($value->DeptID),
				$value->CertificateName,
        $value->CertificateCode,
        $value->IssueDate,
        $value->NoExpired,
        $value->ExpiryDate,
        $value->RevokedDate,
        $value->RenewedDate,
        $value->NextSurvDue,
        $value->Description,
        $value->ReminderStatus,
        $value->ReminderIn,
        $value->CreateDate,
        $value->CreateBy
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($result);
		exit();
	}

	public function certificates_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id      = $this->input->post('Kode');
      $Data    = $this->BJGMAS01->get_where('Trans_Certificates', array('Id' => $Id))->row();

      // Cek apakah data HD atau DT kosong / null
      if (!$Data) {
        echo json_encode([
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data tidak ditemukan.",
          "data"        => null
        ]);

        return;
      }

      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data ditemukan.",
        "data"       => $Data
      ]);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function certificates_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $NoExpired          = $this->input->post('NoExpire');
    $ReminderStatus     = $this->input->post('ReminderStatus');
    //echo $NoExpired;
    $this->_validation_certificates($NoExpired, $ReminderStatus);

    // Ambil data form
    $Id                 = $this->input->post('Id');
    $DeptID             = $this->input->post('DeptID');
    $CertificateName    = strtoupper($this->input->post('CertificateName'));
    $CertificateCode    = strtoupper($this->input->post('CertificateCode'));
    $CertificateStatus  = $this->input->post('CertificateStatus');
    $IssueDate          = $this->input->post('IssueDate');
    $ExpiryDate         = $this->input->post('ExpiryDate');
    $RevokedDate        = $this->input->post('RevokedDate');
    $RenewedDate        = $this->input->post('RenewedDate');
    $NextSurvDue        = $this->input->post('NextSurvDue');
    $Description        = ucfirst($this->input->post('Description'));
    $ReminderIn         = $this->input->post('ReminderIn');
    $Day                = date('Ymd');

    // Ambil data lama
    $oldData = $this->BJGMAS01->get_where('Trans_Certificates', ['Id' => $Id])->row();
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
    if (!empty($Files) && !file_exists('./files/uploads/sertifikat/'.$Files)) {
      $Files = null;
    }

    // Upload file baru jika ada
    if (!empty($_FILES['Files']['name'])) {
      $config['upload_path']   = './files/uploads/sertifikat';
      $config['allowed_types'] = 'pdf|png';
      $config['max_size']      = 3072; // 3MB
      $ext                     = pathinfo($_FILES['Files']['name'], PATHINFO_EXTENSION);
      $config['file_name']     = $CertificateCode.'_'.$Day.'.'.strtolower($ext);
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
        if (!empty($oldData->Files) && file_exists('./files/uploads/sertifikat/'.$oldData->Files)) {
          @unlink('./files/uploads/sertifikat/'.$oldData->Files);
        }

        $uploadData = $this->upload->data();
        $Files      = $uploadData['file_name'];
      }
    }

    // Data update
    $Data = [
      'DeptID'          => $DeptID,
      'CertificateName' => $CertificateName,
      'CertificateCode' => $CertificateCode,
      'Description'     => $Description,
      'IssueDate'       => empty($IssueDate) ? null : $IssueDate,
      'ExpiryDate'      => empty($ExpiryDate) ? null : $ExpiryDate,
      'NoExpired'       => $NoExpired,
      'RevokedDate'     => empty($RevokedDate) ? null : $RevokedDate,
      'RenewedDate'     => empty($RenewedDate) ? null : $RenewedDate,
      'NextSurvDue'     => empty($NextSurvDue) ? null : $NextSurvDue,
      'Files'           => $Files, // null jika tidak upload
      'Status'          => $CertificateStatus,
      'ReminderStatus'  => $ReminderStatus,
      'ReminderIn'      => $ReminderIn,
      'UpdateDate'      => date('Y-m-d H:i:s'),
      'UpdateBy'        => $this->session->userdata('user_id')
    ];

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $this->BJGMAS01->where('Id', $Id);
    $Update = $this->BJGMAS01->update('Trans_Certificates', $Data);

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

	public function certificates_deleted()
	{
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $Id = $this->input->post('Kode');

      // 1. GET NAMA FILE DARI TABLE
      $this->BJGMAS01->where('Id', $Id);
      $query     = $this->BJGMAS01->get('Trans_Certificates');
      $fileRows  = $query->result();

      foreach ($fileRows as $row) {
        $fileName = basename($row->Files);
        $filePath = FCPATH.'files/uploads/sertifikat/'.$fileName;

        if (file_exists($filePath)) {
          @unlink($filePath);
        }
      }

      // 2. Hapus data dari Trans_TrialProductHD
      $this->BJGMAS01->where('Id', $Id);
      $Delete = $this->BJGMAS01->delete('Trans_Certificates');

      // 3. Feedback response
      if ($Delete) {
        echo json_encode(array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Semua data dan file berhasil dihapus."
        ));
      } else {
        echo json_encode(array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal dihapus."
        ));
      }
      exit();
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

  public function get_departemen($DeptID)
  {
    $Query = $this->Attendance->select('DEPTID, DEPTNAME')->where('DEPTID', $DeptID)->get('DEPARTMENTS');
    if ($Query->num_rows() > 0) {
      return $Query->row()->DEPTNAME;
    } else {
      return null;
    }
  }

  private function _validation_certificates($NoExpired, $ReminderStatus)
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Departemen is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('CertificateName') == '') {
      $data['inputerror'][]   = 'CertificateName';
      $data['error_string'][] = 'Certificate Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('CertificateCode') == '') {
      $data['inputerror'][]   = 'CertificateCode';
      $data['error_string'][] = 'Certificate Number is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('CertificateStatus') == '') {
      $data['inputerror'][]   = 'CertificateStatus';
      $data['error_string'][] = 'Certificate Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IssueDate') == '') {
      $data['inputerror'][]   = 'IssueDate';
      $data['error_string'][] = 'Issue Date ID is required';
      $data['status']         = FALSE;
    }

    if ($NoExpired !== 'on') {
      if ($this->input->post('ExpiryDate') == '') {
        $data['inputerror'][]   = 'ExpiryDate';
        $data['error_string'][] = 'Expiry Date is required';
        $data['status']         = FALSE;
      }
    }
    
    if ($this->input->post('ReminderStatus') == '') {
      $data['inputerror'][]   = 'ReminderStatus';
      $data['error_string'][] = 'Reminder Status is required';
      $data['status']         = FALSE;
    }

    if ($ReminderStatus == 'Enabled') {
      if ($this->input->post('ReminderIn') == '') {
        $data['inputerror'][]   = 'ReminderIn';
        $data['error_string'][] = 'Reminder In is required';
        $data['status']         = FALSE;
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}