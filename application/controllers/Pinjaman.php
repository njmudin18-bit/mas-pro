<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pinjaman extends CI_Controller
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
    $this->load->model('pinjaman_model', 'pinjaman');

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Personalia & GA";
      $data['nama_halaman']     = "Daftar Pinjaman";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
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

      $this->load->view('adminx/pga/pinjaman', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function pinjaman_add()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pinjaman();
      $Nomor              = $this->pinjaman->generateUniqueNumber();
      $DeptID             = $this->input->post('DeptID');
      $EmployeeID         = $this->input->post('EmployeeID');
      $JumlahPengajuan    = str_replace('.', '', $this->input->post('JumlahPengajuan'));
      $JangkaWaktu        = $this->input->post('JangkaWaktu');
      $Notes              = ucfirst($this->input->post('Notes'));
      $AngsuranKeArr      = $this->input->post('AngsuranKe');
      $NominalAngsuranArr = $this->input->post('NominalAngsuran');
      $SisaPinjamanArr    = $this->input->post('SisaPinjaman');
      $DataDT             = array();

      $DataHD = array(
        'Nomor'           => $Nomor,
        'EmployeeID'      => $EmployeeID,
        'JumlahPengajuan' => floatval($JumlahPengajuan),
        'JangkaWaktu'     => intval($JangkaWaktu),
        'isApproved'      => 'P', // Pending
        'Noted'           => $Notes,
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_id')
      );

      if(!empty($AngsuranKeArr)){
        foreach($AngsuranKeArr as $key => $val){
          $DataDT[] = array(
            'Nomor'           => $Nomor,
            'DeptID'          => $DeptID,
            'EmployeeID'      => $EmployeeID,
            'AngsuranKe'      => intval($val),
            'JumlahPengajuan' => floatval($JumlahPengajuan),
            'NominalAngsuran' => floatval($NominalAngsuranArr[$key]),
            'SisaPinjaman'    => floatval($SisaPinjamanArr[$key]),
            'Status'          => 'UNPAID',
            'CreatedDate'     => date('Y-m-d H:i:s'),
            'CreatedBy'       => $this->session->userdata('user_id')
          );
        }
      }

      //echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT)); exit;

      $this->ABSENSI->trans_start();
      $this->ABSENSI->insert('Trans_PinjamanHD', $DataHD); 
      if(!empty($DataDT)){
        $this->ABSENSI->insert_batch('Trans_PinjamanDT', $DataDT); 
      }
      $this->ABSENSI->trans_complete();

      // 6. Cek Status Transaksi
      if ($this->ABSENSI->trans_status() === FALSE) {
        echo json_encode(array(
          'status_code' => 500,
          'status'      => 'error', 
          'message'     => 'Gagal menyimpan data ke database (Transaction Failed).'
        ));
      } else {
        echo json_encode(array(
          'status_code' => 200,
          'status'      => 'success', 
          'message'     => 'Data berhasil disimpan dengan Nomor: ' . $Nomor
        ));
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
    exit;
  }

  public function pinjaman_approved()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $Nomor          = $this->input->post('Id');
      $isApproved     = $this->input->post('isApproved');

      $FirstData = array(
        'IsApproved'        => $isApproved,
        'TanggalDisetujui'  => ($isApproved == 'P') ? NULL : date('Y-m-d'),
        'ApprovedDate'      => ($isApproved == 'P') ? NULL : date('Y-m-d H:i:s'),
        'ApprovedBy'        => ($isApproved == 'P') ? NULL : $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_PinjamanHD', $FirstData, array('Nomor' => $Nomor));
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

  public function pinjaman_list()
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

    $Sql            = "EXEC dbo.GetDataPinjaman @StartDate = ?, @EndDate = ?, @DeptIDs = ?";
    $Query          = $this->ABSENSI->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();

		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->isApproved == 'PENDING' ? 'Y' : 'P';
      $ApLbl  = $value->isApproved == 'PENDING' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->Nomor."'";
      $Isi2   = "'".$value->Nomor."', '".$ApSts."', '".$ApLbl."'";

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
      $row[]  = $value->Nomor;
      $row[]  = $value->isApproved;
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->NAME;
      $row[]  = $value->JangkaWaktu;
      $row[]  = $value->JumlahPengajuan;
      $row[]  = $value->TanggalDisetujui;
      $row[]  = $value->Noted;
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

  public function pinjaman_edit()
  {
    $Nomor    = $this->input->post('Nomor');
    $DataHD   = $this->pinjaman->get_hd_by_id($Nomor);
    $DataDT   = $this->pinjaman->get_dt_by_id($Nomor);
    $Data     = array(
      "status_code" => 200,
      "status"      => "success",
      "HD"          => $DataHD,
      "DT"          => $DataDT
    );

    echo json_encode($Data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function pinjaman_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
        echo json_encode(["status" => "forbidden"]);
        return;
    }

    $this->_validation_pinjaman();
        
    $Nomor              = $this->input->post('kode');
    $DeptID             = $this->input->post('DeptID');
    $EmployeeID         = $this->input->post('EmployeeID');
    $JumlahPengajuan    = str_replace('.', '', $this->input->post('JumlahPengajuan'));
    $JangkaWaktu        = $this->input->post('JangkaWaktu');
    $Notes              = ucfirst($this->input->post('Notes'));
    $AngsuranKeArr      = $this->input->post('AngsuranKe');
    $NominalAngsuranArr = $this->input->post('NominalAngsuran');
    $SisaPinjamanArr    = $this->input->post('SisaPinjaman');
    
    $DataHD = array(
      'EmployeeID'      => $EmployeeID,
      'JumlahPengajuan' => floatval($JumlahPengajuan),
      'JangkaWaktu'     => intval($JangkaWaktu),
      'isApproved'      => 'P', // Kembali ke Pending jika di-edit
      'Noted'           => $Notes,
      'UpdatedDate'     => date('Y-m-d H:i:s'), // Baiknya ada kolom UpdatedDate
      'UpdatedBy'       => $this->session->userdata('user_id')
    );

    $DataDT = array();
    if(!empty($AngsuranKeArr)){
      foreach($AngsuranKeArr as $key => $val){
        // Bersihkan nilai nominal dari titik jika masih ada
        $nominalClean = str_replace('.', '', $NominalAngsuranArr[$key]);
        $sisaClean    = str_replace('.', '', $SisaPinjamanArr[$key]);

        $DataDT[] = array(
          'Nomor'           => $Nomor,
          'DeptID'          => $DeptID,
          'EmployeeID'      => $EmployeeID,
          'AngsuranKe'      => intval($val),
          'JumlahPengajuan' => floatval($JumlahPengajuan),
          'NominalAngsuran' => floatval($nominalClean),
          'SisaPinjaman'    => floatval($sisaClean),
          'Status'          => 'UNPAID',
          'CreatedDate'     => date('Y-m-d H:i:s'),
          'CreatedBy'       => $this->session->userdata('user_id')
        );
      }
    }

    $this->ABSENSI->trans_start();
    //UPDATE HD
    $this->ABSENSI->where('Nomor', $Nomor);
    $this->ABSENSI->update('Trans_PinjamanHD', $DataHD);
    //DELETE DT BEFORE
    $this->ABSENSI->where('Nomor', $Nomor);
    $this->ABSENSI->delete('Trans_PinjamanDT');
    if(!empty($DataDT)){
      $this->ABSENSI->insert_batch('Trans_PinjamanDT', $DataDT);
    }

    $this->ABSENSI->trans_complete();

    if ($this->ABSENSI->trans_status() === FALSE) {
      echo json_encode(array(
        'status_code' => 500,
        'status'      => 'error', 
        'message'     => 'Gagal mengupdate data ke database (Transaction Failed).'
      ));
    } else {
      echo json_encode(array(
        'status_code' => 200,
        'status'      => 'success', 
        'message'     => 'Data berhasil diupdate dengan Nomor: ' . $Nomor
      ));
    }
    exit;
  }

  public function pinjaman_deleted($Nomor)
  {
    $DataHD         = $this->pinjaman->get_hd_by_id($Nomor);
    $DataDT         = $this->pinjaman->get_dt_by_id($Nomor);
    $Data           = array(
      "status_code" => 200,
      "status"      => "success",
      "HD"          => $DataHD,
      "DT"          => $DataDT
    );
    $data           = $this->pinjaman->delete_by_id($Nomor);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_pinjaman()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;
    $ChangeTo             = $this->input->post('ChangeTo');

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

    if ($this->input->post('JumlahPengajuan') == '') {
      $data['inputerror'][]   = 'JumlahPengajuan';
      $data['error_string'][] = 'Jumlah Pengajuan is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JangkaWaktu') == '') {
      $data['inputerror'][]   = 'JangkaWaktu';
      $data['error_string'][] = 'Jangka Waktu is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Notes') == '') {
      $data['inputerror'][]   = 'Notes';
      $data['error_string'][] = 'Notes is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
