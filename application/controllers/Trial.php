<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Trial extends CI_Controller
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
      $data['group_halaman'] 	= "Product Development";
			$data['nama_halaman'] 	= "Trial Product";
			$data['icon_halaman'] 	= "icon-bookmark";
			$data['no_form'] 	      = "MAS/FO/PD/002";

			$data['department']     = $this->department->get_all();
			$data['perusahaan']     = $this->perusahaan->get_details();
			$this->load->view('adminx/pd/trial_product/index', $data, FALSE);

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

  public function trial_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_trial();

      // Generate nomor request
      $Nomor             = $this->trial->generateTrialNumber();
      // Ambil data form
      $PartID            = $this->input->post('PartID');
      $FormulaID         = $this->input->post('FormulaList');
      $ProductType       = $this->input->post('ProductType');
      $Proses            = ucwords($this->input->post('Proses'));
      $JenisMaterial     = strtoupper($this->input->post('JenisMaterial'));
      $Mesin             = ucwords($this->input->post('Mesin'));
      $Quantity          = floatval(format_weight($this->input->post('Quantity')));
      $ProsesDate        = $this->input->post('ProsesDate');
      $Shift             = $this->input->post('Shift');
      $Unit              = $this->input->post('UnitList');
      $Keterangan        = $this->input->post('Keterangan');

      // Upload file (opsional)
      $Files = null;
      if (!empty($_FILES['Files']['name'])) {
        $config['upload_path']   = './files/uploads/trial';
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
        'PartID'        => $PartID,
        'FormulaID'     => $FormulaID,
        'Type'          => $ProductType,
        'Proses'        => $Proses,
        'JenisMaterial' => $JenisMaterial,
        'Machine'       => $Mesin,
        'Quantity'      => $Quantity,
        'UnitID'        => $Unit,
        'Files'         => $Files, // null jika tidak upload
        'ProcessDate'   => $ProsesDate,
        'Shift'         => $Shift,
        'Noted'         => $Keterangan,
        'CreateDate'    => date('Y-m-d H:i:s'),
        'CreateBy'      => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Insert = $this->BJGMAS01->insert('Trans_TrialProductHD', $FirstData);
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

	public function trial_list()
	{
		$Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');
    $user_dept   = $this->session->userdata('user_dept_name');

    $Sql         = "EXEC dbo.GetTrialData @StartDate = ?, @EndDate = ?";
    $Query       = $this->BJGMAS01->query($Sql, array($StartDate, $EndDate));
    $Result      = $Query->result();
    $Total       = count($Result);
    $Paged       = array_slice($Result, $Start, $Length);

    $Data        = [];
    $No          = $Start + 1;
    foreach ($Paged as $key => $Res) {
      $Isi          = "'".$Res->Nomor."'";
      $Isi2         = "'".$Res->Nomor."', '".$Res->PartID."', '".$Res->PartName."', '".$Res->FormulaID."'";
      $Url          = base_url()."files/uploads/trial/".$Res->Files;
      $UrlQc        = base_url()."files/uploads/trial_hasil/".$Res->QC_DisetujuiFiles;
      $UrlExtrude   = base_url()."files/uploads/trial_hasil/".$Res->EXTRUDE_DisetujuiFiles;
      $UrlPpic      = base_url()."files/uploads/trial_hasil/".$Res->PPIC_DisetujuiFiles;
      $UrlPd        = base_url()."files/uploads/trial_hasil/".$Res->PD_DisetujuiFiles;
      
      $Row    = array();
      $Row[]  = $No++;
      //$Row[]  = $Res->Nomor;
      $Row[]  = ($Res->Newest != NULL) ? $Res->Nomor.' <span class="badge badge-danger">'.$Res->Newest.'</span>' : $Res->Nomor;
      $Row[]  = ($Res->Files != NULL) ? '<a href="'.$Url.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $Row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="'.base_url().'trial/detail/'.base64_encode($Res->Nomor).'/'.base64_encode($Res->PartID).'" target="_blank">Lihat detail</a>
                    </div>
                  </div>
                </div>';
      $Row[]  = $Res->Type;
      $Row[]  = $Res->PartID;
      $Row[]  = $Res->PartName;
      $Row[]  = $Res->FormulaID;
      $Row[]  = $Res->Proses;
      $Row[]  = $Res->JenisMaterial;
      $Row[]  = $Res->Machine;
      $Row[]  = $Res->Quantity;
      $Row[]  = $Res->UnitName;
      $Row[]  = $Res->Noted;
      $Row[]  = $this->get_diajukan($Res->CreateBy);

      //PELAKSANA
      $Row[]  = ($Res->PD_Status == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->PD_Status).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->PD_Status).'</span>';
      $Row[]  = ($Res->PD_UserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PD\', \'Pelaksana\')" type="button" class="btn btn-info btn-sm" title="Tambahkan pelaksana PD"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PD_UserID);
      $Row[]  = ($Res->PD_OnDate != NULL) ? $Res->PD_OnDate : '';

      $Row[]  = ($Res->PPIC_Status == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->PPIC_Status).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->PPIC_Status).'</span>';
      $Row[]  = ($Res->PPIC_UserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PPIC\', \'Pelaksana\')" type="button" class="btn btn-info btn-sm" title="Tambahkan pelaksana PPIC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PPIC_UserID);
      $Row[]  = ($Res->PPIC_OnDate != NULL) ? $Res->PPIC_OnDate : '';

      $Row[]  = ($Res->EXTRUDE_Status == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->EXTRUDE_Status).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->EXTRUDE_Status).'</span>';
      $Row[]  = ($Res->EXTRUDE_UserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'EXTRUDE\', \'Pelaksana\')" type="button" class="btn btn-info btn-sm" title="Tambahkan pelaksana Extrude"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->EXTRUDE_UserID);
      $Row[]  = ($Res->EXTRUDE_OnDate != NULL) ? $Res->EXTRUDE_OnDate : '';

      $Row[]  = ($Res->QC_Status == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->QC_Status).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->QC_Status).'</span>';
      $Row[]  = ($Res->QC_UserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'QC\', \'Pelaksana\')" type="button" class="btn btn-info btn-sm" title="Tambahkan pelaksana QC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->QC_UserID);
      $Row[]  = ($Res->QC_OnDate != NULL) ? $Res->QC_OnDate : '';

      //PENGAJUAN
      $Row[]  = ($Res->PD_PengajuanStatus == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->PD_PengajuanStatus).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->PD_PengajuanStatus).'</span>';
      $Row[]  = ($Res->PD_PengajuanUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PD\', \'Pengajuan\')" type="button" class="btn btn-primary btn-sm" title="Tambahkan pengajuan PD"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PD_PengajuanUserID);
      $Row[]  = ($Res->PD_PengajuanOnDate != NULL) ? $Res->PD_PengajuanOnDate : '';

      $Row[]  = ($Res->PPIC_PengajuanStatus == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->PPIC_PengajuanStatus).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->PPIC_PengajuanStatus).'</span>';
      $Row[]  = ($Res->PPIC_PengajuanUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PPIC\', \'Pengajuan\')" type="button" class="btn btn-primary btn-sm" title="Tambahkan pengajuan PPIC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PPIC_PengajuanUserID);
      $Row[]  = ($Res->PPIC_PengajuanOnDate != NULL) ? $Res->PPIC_PengajuanOnDate : '';

      $Row[]  = ($Res->EXTRUDE_PengajuanStatus == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->EXTRUDE_PengajuanStatus).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->EXTRUDE_PengajuanStatus).'</span>';
      $Row[]  = ($Res->EXTRUDE_PengajuanUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'EXTRUDE\', \'Pengajuan\')" type="button" class="btn btn-primary btn-sm" title="Tambahkan pengajuan Extrude"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->EXTRUDE_PengajuanUserID);
      $Row[]  = ($Res->EXTRUDE_PengajuanOnDate != NULL) ? $Res->EXTRUDE_PengajuanOnDate : '';

      $Row[]  = ($Res->QC_PengajuanStatus == 'Setuju') ? '<span class="badge badge-pill badge-success">'.strtoupper($Res->QC_PengajuanStatus).'</span>' : '<span class="badge badge-pill badge-danger">'.strtoupper($Res->QC_PengajuanStatus).'</span>';
      $Row[]  = ($Res->QC_PengajuanUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'QC\', \'Pengajuan\')" type="button" class="btn btn-primary btn-sm" title="Tambahkan pengajuan QC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->QC_PengajuanUserID);
      $Row[]  = ($Res->QC_PengajuanOnDate != NULL) ? $Res->QC_PengajuanOnDate : '';
      
      //HASIL
      $Row[]  = '<span onclick="show_hasil_trial(\''.$Res->PD_DisetujuiId.'\', \''.$Res->PD_DisetujuiStatus.'\', \''.$Res->PD_DisetujuiNoted.'\', \''.$Res->PD_DisetujuiFiles.'\', \'PD\', \'Disetujui\', \''.$Res->Nomor.'\')" class="badge badge-pill '.$Res->PD_DisetujuiClass.' pointer">'.$Res->PD_DisetujuiStatus.'</span>';
      $Row[]  = ($Res->PD_DisetujuiUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PD\', \'Disetujui\')" type="button" class="btn btn-danger btn-sm" title="Tambahkan disetujui PD"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PD_DisetujuiUserID);
      $Row[]  = ($Res->PD_DisetujuiOnDate != NULL) ? $Res->PD_DisetujuiOnDate : '';
      $Row[]  = ($Res->PD_DisetujuiFiles != NULL) ? '<a href="'.$UrlPd.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';

      $Row[]  = '<span onclick="show_hasil_trial(\''.$Res->PPIC_DisetujuiId.'\', \''.$Res->PPIC_DisetujuiStatus.'\', \''.$Res->PPIC_DisetujuiNoted.'\', \''.$Res->PPIC_DisetujuiFiles.'\', \'PPIC\', \'Disetujui\', \''.$Res->Nomor.'\')" class="badge badge-pill '.$Res->PPIC_DisetujuiClass.' pointer">'.$Res->PPIC_DisetujuiStatus.'</span>';
      $Row[]  = ($Res->PPIC_DisetujuiUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'PPIC\', \'Disetujui\')" type="button" class="btn btn-danger btn-sm" title="Tambahkan disetujui PPIC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->PPIC_DisetujuiUserID);
      $Row[]  = ($Res->PPIC_DisetujuiOnDate != NULL) ? $Res->PPIC_DisetujuiOnDate : '';
      $Row[]  = ($Res->PPIC_DisetujuiFiles != NULL) ? '<a href="'.$UrlPpic.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';

      $Row[]  = '<span onclick="show_hasil_trial(\''.$Res->EXTRUDE_DisetujuiId.'\', \''.$Res->EXTRUDE_DisetujuiStatus.'\', \''.$Res->EXTRUDE_DisetujuiNoted.'\', \''.$Res->EXTRUDE_DisetujuiFiles.'\', \'EXTRUDE\', \'Disetujui\', \''.$Res->Nomor.'\')" class="badge badge-pill '.$Res->EXTRUDE_DisetujuiClass.' pointer">'.$Res->EXTRUDE_DisetujuiStatus.'</span>';
      $Row[]  = ($Res->EXTRUDE_DisetujuiUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'EXTRUDE\', \'Disetujui\')" type="button" class="btn btn-danger btn-sm" title="Tambahkan disetujui Extrude"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->EXTRUDE_DisetujuiUserID);
      $Row[]  = ($Res->EXTRUDE_DisetujuiOnDate != NULL) ? $Res->EXTRUDE_DisetujuiOnDate : '';
      $Row[]  = ($Res->EXTRUDE_DisetujuiFiles != NULL) ? '<a href="'.$UrlExtrude.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';

      $Row[]  = '<span onclick="show_hasil_trial(\''.$Res->QC_DisetujuiId.'\', \''.$Res->QC_DisetujuiStatus.'\', \''.$Res->QC_DisetujuiNoted.'\', \''.$Res->QC_DisetujuiFiles.'\', \'QC\', \'Disetujui\', \''.$Res->Nomor.'\')" class="badge badge-pill '.$Res->QC_DisetujuiClass.' pointer">'.$Res->QC_DisetujuiStatus.'</span>';
      $Row[]  = ($Res->QC_DisetujuiUserID == NULL) ? '<button onclick="tambah_transaksi('.$Isi2.', \'QC\', \'Disetujui\')" type="button" class="btn btn-danger btn-sm" title="Tambahkan disetujui QC"><i class="fa fa-plus-square" aria-hidden="true"></i></button>' : $this->get_pelaksana($Res->QC_DisetujuiUserID);
      $Row[]  = ($Res->QC_DisetujuiOnDate != NULL) ? $Res->QC_DisetujuiOnDate : '';
      $Row[]  = ($Res->QC_DisetujuiFiles != NULL) ? '<a href="'.$UrlQc.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';

      $Row[]  = $Res->CreateDate;

      $Data[] = $Row;
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $Data
    ]);
    exit();
	}

	public function trial_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Nomor   = $this->input->post('NoTrial');
      $DataHD  = $this->trial->get_hd_by_id($Nomor);

      // Cek apakah data HD atau DT kosong / null
      if (empty($DataHD)) {
        echo json_encode([
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data tidak ditemukan.",
          "first"       => null,
          "second"      => null
        ]);

        return;
      }

      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data ditemukan.",
        "first"       => $DataHD
      ]);
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function trial_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_trial();

    // Ambil data form
    $Nomor         = $this->input->post('Nomor');
    $PartID        = $this->input->post('PartID');
    $FormulaID     = $this->input->post('FormulaList');
    $ProductType   = $this->input->post('ProductType');
    $Proses        = ucwords($this->input->post('Proses'));
    $JenisMaterial = strtoupper($this->input->post('JenisMaterial'));
    $Mesin         = ucwords($this->input->post('Mesin'));
    $Quantity      = floatval(format_weight($this->input->post('Quantity')));
    $ProsesDate    = $this->input->post('ProsesDate');
    $Shift         = $this->input->post('Shift');
    $Unit          = $this->input->post('UnitList');
    $Keterangan    = $this->input->post('Keterangan');

    //echo $Nomor; exit;

    // Ambil data lama
    $oldData = $this->BJGMAS01->get_where('Trans_TrialProductHD', ['Nomor' => $Nomor])->row();
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
    if (!empty($Files) && !file_exists('./files/uploads/trial/' . $Files)) {
      $Files = null;
    }

    // Upload file baru jika ada
    if (!empty($_FILES['Files']['name'])) {
      $config['upload_path']   = './files/uploads/trial';
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
        if (!empty($oldData->Files) && file_exists('./files/uploads/trial/' . $oldData->Files)) {
          @unlink('./files/uploads/trial/' . $oldData->Files);
        }

        $uploadData = $this->upload->data();
        $Files      = $uploadData['file_name'];
      }
    }

    // Data update
    $FirstData = [
      'PartID'        => $PartID,
      'FormulaID'     => $FormulaID,
      'Type'          => $ProductType,
      'Proses'        => $Proses,
      'JenisMaterial' => $JenisMaterial,
      'Machine'       => $Mesin,
      'Quantity'      => $Quantity,
      'UnitID'        => $Unit,
      'Files'         => $Files,
      'ProcessDate'   => $ProsesDate,
      'Shift'         => $Shift,
      'Noted'         => $Keterangan,
      'UpdateDate'    => date('Y-m-d H:i:s'),
      'UpdateBy'      => $this->session->userdata('user_id')
    ];

    $this->BJGMAS01->where('Nomor', $Nomor);
    $Update = $this->BJGMAS01->update('Trans_TrialProductHD', $FirstData);

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

	public function trial_deleted()
	{
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $Nomor = $this->input->post('NoTrial');

      // 1. GET NAMA FILE DARI TABLE HD
      $this->BJGMAS01->where('Nomor', $Nomor);
      $query     = $this->BJGMAS01->get('Trans_TrialProductHD');
      $fileRows  = $query->result();

      foreach ($fileRows as $row) {
        $fileName = basename($row->Files);
        $filePath = FCPATH.'files/uploads/trial/'.$fileName;

        if (file_exists($filePath)) {
          @unlink($filePath);
        }
      }

      // 2. GET NAMA FILE DARI TABLE DT
      $this->BJGMAS01->where('Nomor', $Nomor);
      $queryDT     = $this->BJGMAS01->get('Trans_TrialProductDT');
      $fileRowsDT  = $queryDT->result();

      foreach ($fileRowsDT as $row) {
        $fileName = basename($row->Files);
        $filePath = FCPATH.'files/uploads/trial_hasil/'.$fileName;

        if (file_exists($filePath)) {
          @unlink($filePath);
        }
      }

      // 3. Hapus data dari Trans_TrialProductDT
      $this->BJGMAS01->where('Nomor', $Nomor);
      $this->BJGMAS01->delete('Trans_TrialProductDT');

      // 4. Hapus data dari Trans_TrialProductHD
      $this->BJGMAS01->where('Nomor', $Nomor);
      $Delete = $this->BJGMAS01->delete('Trans_TrialProductHD');

      // 5. Feedback response
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

  //UPDATE HASIL TRIAL
  public function update_hasil_trial()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_hasil_trial();

      $DetailID        = $this->input->post('DetailID');
      $HasilStatus     = $this->input->post('HasilStatus');
      $HasilKeterangan = $this->input->post('HasilKeterangan');
      $DeptName        = $this->input->post('DetailDept');
      $Jenis           = $this->input->post('DetailTrans');
      $Nomor           = $this->input->post('DetailNomor');

      $Data = [
        'Status' => $HasilStatus,
        'Noted'  => $HasilKeterangan
      ];

      // ==== CEK APAKAH ADA FILE YANG DIUPLOAD ====
      if (!empty($_FILES['HasilFiles']['name'])) {
        // ambil dulu data lama untuk hapus file lama
        $Old = $this->BJGMAS01->get_where('Trans_TrialProductDT', ['Id' => $DetailID])->row();
        if ($Old && !empty($Old->Files)) {
          $old_path = './uploads/trial_hasil/'.$Old->Files;
          if (file_exists($old_path)) {
            @unlink($old_path); // hapus file lama
          }
        }

        // ambil ekstensi file
        $ext          = pathinfo($_FILES['HasilFiles']['name'], PATHINFO_EXTENSION);
        $newFileName  = $DeptName.'-'.$Jenis.'-'.$Nomor.'.'.strtolower($ext);

        $config['upload_path']   = './files/uploads/trial_hasil/';
        $config['allowed_types'] = 'pdf|png';
        $config['max_size']      = 5120; // 5MB
        $config['file_name']     = $newFileName;
        $config['overwrite']     = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('HasilFiles')) {
          $uploadData     = $this->upload->data();
          $Data['Files']  = $uploadData['file_name'];
        } else {
          echo json_encode([
            'status_code' => 500,
            'status'      => 'error',
            'message'     => $this->upload->display_errors()
          ]);

          return;
        }
      }
      // ============================================

      $Update = $this->BJGMAS01->update('Trans_TrialProductDT', $Data, ['Id' => $DetailID]);
      if ($Update) {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data sukses diupdate.',
          'file_name'   => isset($Data['Files']) ? $Data['Files'] : null
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data gagal diupdate.',
          'file_name'   => NULL
        ]);
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function hapus_single_row()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $OldFile   = $this->input->post('OldFile');
      $NoRequest = $this->input->post('NoReq');
      $IdDetail  = $this->input->post('IdDt');

      // Tentukan path file
      //$FilePath = $OldFile;
      $FilePath  = FCPATH.'files/uploads/request/'.$OldFile;

      // Coba hapus file dulu
      if (file_exists($FilePath)) {
        if (!unlink($FilePath)) {
          echo json_encode([
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Gagal menghapus file fisik."
          ]);
          exit();
        }
      }

      // Jika file berhasil dihapus (atau tidak ada), lanjut hapus dari DB
      $Delete = $this->BJGMAS01->delete('Trans_RequestSampleDT', array(
        'Id'    => $IdDetail,
        'Nomor'=> $NoRequest
      ));

      if ($Delete) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data dan file sukses dihapus."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Gagal menghapus data dari database."
        ]);
      }

      exit();
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  //SAVE PELAKSANA PD
  public function pelaksana_save_pd()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pelaksana();

      $this->_save_pelaksana();
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //SAVE PELAKSANA PPIC
  public function pelaksana_save_ppic()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pelaksana();

      $this->_save_pelaksana();
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //SAVE PELAKSANA EXTRUDE
  public function pelaksana_save_extrude()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pelaksana();

      $this->_save_pelaksana();
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //SAVE PELAKSANA QC
  public function pelaksana_save_qc()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pelaksana();

      $this->_save_pelaksana();
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function cek_pelaksana()
  {
    $Nomor       = $this->input->post('NoTrial');
    $Departemen  = $this->input->post('Departemen');
    $Jenis       = $this->input->post('Jenis');
    $Cek         = $this->BJGMAS01->get_where('Trans_TrialProductDT', ['Nomor' => $Nomor, 'DeptName' => $Departemen, 'Jenis' => $Jenis]);
    if ($Cek->num_rows() > 0) {
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data ditemukan.',
        'data'        => $Cek->row()
      ]);
    } else {
      echo json_encode([
        'status_code' => 404,
        'status'      => 'error',
        'message'     => 'Data tidak ditemukan.',
        'data'        => []
      ]);
    }
  }

  private function _save_pelaksana()
  {
    $Nomor    = $this->input->post('PelaksanaNoTrial');
    $DeptName = $this->input->post('PelaksanaDept');
    $Jenis    = $this->input->post('PelaksanaJenis');

    $Data = [
      'Nomor'     => $Nomor,
      'DeptName'  => $DeptName,
      'Jenis'     => $Jenis,
      'UserID'    => $this->input->post('PelaksanaID'),
      'Status'    => $this->input->post('PelaksanaStatus'),
      'Noted'     => $this->input->post('PelaksanaKeterangan')
    ];

    // ==== PROSES FILE ====
    if (!empty($_FILES['PelaksanaFiles']['name'])) {
      $DeptName     = $this->input->post('PelaksanaDept');
      $Nomor        = $this->input->post('PelaksanaNoTrial');
      $ext          = pathinfo($_FILES['PelaksanaFiles']['name'], PATHINFO_EXTENSION);
      $newFileName  = $DeptName.'-'.$Jenis.'-'.$Nomor.'.'.strtolower($ext);

      $config['upload_path']   = './files/uploads/trial_hasil/';
      $config['allowed_types'] = 'pdf|png';
      $config['max_size']      = 5120;
      $config['file_name']     = $newFileName;
      $config['overwrite']     = TRUE;

      $this->load->library('upload', $config);

      if ($this->upload->do_upload('PelaksanaFiles')) {
        $uploadData     = $this->upload->data();
        $Data['Files']  = $uploadData['file_name'];
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => $this->upload->display_errors()
        ]);

        return;
      }
    }
    // ======================

    // Cek apakah Nomor sudah ada
    $Cek = $this->BJGMAS01->get_where('Trans_TrialProductDT', [
      'Nomor'    => $Nomor,
      'DeptName' => $DeptName,
      'Jenis'    => $Jenis
    ]);

    if ($Cek->num_rows() > 0) {
      // UPDATE
      $Data['UpdateDate'] = date('Y-m-d H:i:s');
      $Data['UpdateBy']   = $this->session->userdata('user_id');

      $Update = $this->BJGMAS01->update('Trans_TrialProductDT', $Data, ['Nomor' => $Nomor]);
      if ($Update) {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data sukses diupdate.'
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data gagal diupdate.'
        ]);
      }
    } else {
      // INSERT
      $Data['CreateDate'] = date('Y-m-d H:i:s');
      $Data['CreateBy']   = $this->session->userdata('user_id');

      $Insert = $this->BJGMAS01->insert('Trans_TrialProductDT', $Data);
      if ($Insert) {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data sukses disimpan.'
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data gagal disimpan.'
        ]);
      }
    }
  }

  //DETAIL LAPORAN
  public function detail()
  {
    $data['group_halaman'] 	= "Product Development";
    $data['nama_halaman'] 	= "Laporan Hasil Trial";
    $data['icon_halaman'] 	= "icon-bookmark";
    $data['perusahaan'] 		= $this->perusahaan->get_details();
    $data['no_form'] 	      = "MAS/FO/PD/003";
    $data['reg_form'] 	    = "2024/07/004";
    $data['subject_form'] 	= "PPIC, PROD, QC";
    $Nomor                  = base64_decode($this->uri->segment(3));
    $data['Laporan']        = $this->trial->get_data_laporan($Nomor);
    //echo json_encode($data['Laporan']); exit;
    $data['Nomor']          = base64_decode($this->uri->segment(3));
    $data['PartID']         = base64_decode($this->uri->segment(4));

    //echo $Nomor." - ".$PartID; exit;
    $this->load->view('adminx/pd/trial_product/laporan', $data, FALSE);
  }

  public function get_part()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->trial->get_part($Search);
    
    echo json_encode($Result);
    exit;
  }

  public function get_unit()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->trial->get_unit($Search);
    
    echo json_encode($Result);
    exit;
  }

  public function get_formula()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->trial->get_formula($Search);
    
    echo json_encode($Result);
    exit;
  }

  public function get_user_dept()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->trial->get_user_dept($Search);
    
    echo json_encode($Result);
    exit;
  }

  public function get_diajukan($CreateBy)
  {
    $query = $this->db->select('nip, nama_pegawai')->where('id', $CreateBy)->get('table_user');
    if ($query->num_rows() > 0) {
      return $query->row()->nama_pegawai;
    } else {
      return null;
    }
  }

  public function get_pelaksana($PelaksanaID)
  {
    $query = $this->Attendance->select('SSN, NAME')->where('SSN', $PelaksanaID)->get('USERINFO');
    if ($query->num_rows() > 0) {
      return $query->row()->NAME;
    } else {
      return null;
    }
  }

  private function _validation_hasil_trial()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('HasilStatus') == '') {
      $data['inputerror'][]   = 'HasilStatus';
      $data['error_string'][] = 'Hasil is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_pelaksana()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('PelaksanaID') == '') {
      $data['inputerror'][]   = 'PelaksanaID';
      $data['error_string'][] = 'Pelaksana is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('PelaksanaStatus') == '') {
      $data['inputerror'][]   = 'PelaksanaStatus';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_trial()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('ProductType') == '') {
      $data['inputerror'][]   = 'ProductType';
      $data['error_string'][] = 'Product Type is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('PartList') == '') {
      $data['inputerror'][]   = 'PartList';
      $data['error_string'][] = 'Part Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('FormulaList') == '') {
      $data['inputerror'][]   = 'FormulaList';
      $data['error_string'][] = 'Formula ID is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('UnitList') == '') {
      $data['inputerror'][]   = 'UnitList';
      $data['error_string'][] = 'Unit ID is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Proses') == '') {
      $data['inputerror'][]   = 'Proses';
      $data['error_string'][] = 'Proses ID is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JenisMaterial') == '') {
      $data['inputerror'][]   = 'JenisMaterial';
      $data['error_string'][] = 'Jenis Material is required';
      $data['status']         = FALSE;
    }

    
    if ($this->input->post('ProsesDate') == '') {
      $data['inputerror'][]   = 'ProsesDate';
      $data['error_string'][] = 'Tanggal Pengerjaan is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Quantity') == '') {
      $data['inputerror'][]   = 'Quantity';
      $data['error_string'][] = 'Quantity is required';
      $data['status']         = FALSE;
    }

    // if ($this->input->post('UnitList') == '') {
    //   $data['inputerror'][]   = 'UnitList';
    //   $data['error_string'][] = 'Unit is required';
    //   $data['status']         = FALSE;
    // }

    if ($this->input->post('Keterangan') == '') {
      $data['inputerror'][]   = 'Keterangan';
      $data['error_string'][] = 'Keterangan time is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}