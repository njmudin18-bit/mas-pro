<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Koreksi_absensi extends CI_Controller
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
    $this->load->model('koreksi_model', 'koreksi');
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
      $data['nama_halaman']     = "Daftar Koreksi Absensi";
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

      $this->load->view('adminx/pga/koreksi_absensi', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function koreksi_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_koreksi();

      // Generate nomor request
      $Nomor             = $this->koreksi->generateAbsensiNumber();
      // Ambil data form
      $DeptID            = $this->input->post('DeptID');
      $EmployeeID        = $this->input->post('EmployeeID');
      $Tanggal           = $this->input->post('Tanggal');
      $CheckInAsli       = date('Y-m-d H:i:s', strtotime($this->input->post('CheckInAsli')));
      $CheckOutAsli      = date('Y-m-d H:i:s', strtotime($this->input->post('CheckOutAsli')));
      $ChangeTo          = $this->input->post('ChangeTo');
      $CheckInKoreksi    = ($ChangeTo === 'OUT') ? null : str_replace('T', ' ', date('Y-m-d H:i:s', strtotime($this->input->post('CheckInKoreksi'))));
      $CheckOutKoreksi   = ($ChangeTo === 'IN') ? null : str_replace('T', ' ', date('Y-m-d H:i:s', strtotime($this->input->post('CheckOutKoreksi'))));
      $Notes             = ucfirst($this->input->post('Notes'));

      // Data header
      $FirstData = array(
        'Nomor'           => $Nomor,
        'EmployeeID'      => $EmployeeID,
        'Tanggal'         => $Tanggal,
        'CheckInAsli'     => $CheckInAsli,
        'CheckOutAsli'    => $CheckOutAsli,
        'ChangeTo'        => $ChangeTo,
        'CheckInKoreksi'  => $CheckInKoreksi,
        'CheckOutKoreksi' => $CheckOutKoreksi,
        'Notes'           => $Notes,
        'isApproved'      => 'P',
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Insert = $this->ABSENSI->insert('Trans_KoreksiAbsensi', $FirstData);
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

  public function koreksi_approved_OLD()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $KoreksiID  = $this->input->post('Id');
      $isApproved = $this->input->post('isApproved');

      //$Query      = $this->ABSENSI->get_where('Trans_KoreksiAbsensi', array('KoreksiID' => $KoreksiID))->row();
      $this->ABSENSI
          ->select('a.*, b.USERID')
          ->from('Trans_KoreksiAbsensi a')
          ->join('USERINFO b', 'a.EmployeeID = b.SSN', 'left') // Lakukan LEFT JOIN
          ->where('a.KoreksiID', $KoreksiID);

      $Query = $this->ABSENSI->get()->row();
      
      if (!$Query) {
        echo json_encode(array("status_code" => 404, "status" => "error", "message" => "Data tidak ditemukan."));
        
        exit;
      }
      
      $Tanggal    = $Query->Tanggal;
      $UserID     = $Query->USERID;
      $TanggalIn  = $Query->CheckInKoreksi;
      $TanggalOut = $Query->CheckOutKoreksi;
      $Kolom      = $Query->ChangeTo;
      
      $FirstData = array(
        'isApproved'    => $isApproved,
        'ApprovedDate'  => date('Y-m-d H:i:s'),
        'ApprovedBy'    => $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData, "row" => $Query, "Tgl" => $Tanggal, "Kolom" => $Kolom)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_KoreksiAbsensi', $FirstData, array('KoreksiID' => $KoreksiID));
      if ($Update) {

        //INSERT KE TABLE CHECKINOUT
        if ($Kolom == 'IN') {
          $DataInsert = array(
            'USERID'     => $UserID,
            'CHECKTIME'  => $TanggalIn,
            'CHECKTYPE'  => 'I',
            'VERIFYCODE' => 0,
            'SENSORID'   => 5,
            'Memoinfo'   => NULL,
            'WorkCode'   => 0,
            'sn'         => '6530143500103',
            'UserExtFmt' => 1,
            'MachineId'  => NULL
          );
        } else {
          $DataInsert = array(
            'USERID'     => $UserID,
            'CHECKTIME'  => $TanggalOut,
            'CHECKTYPE'  => 'I',
            'VERIFYCODE' => 0,
            'SENSORID'   => 5,
            'Memoinfo'   => NULL,
            'WorkCode'   => 0,
            'sn'         => '6530143500103',
            'UserExtFmt' => 1,
            'MachineId'  => NULL
          );
        }

        $this->ABSENSI->insert('CHECKINOUT', $DataInsert);
        // Cek apakah query berhasil
        if ($this->ABSENSI->affected_rows() > 0) {
          echo json_encode(
            array(
              'status_code'   => 200,
              'status'        => 'success', 
              'message'       => 'Data sukses disimpan.'
            )
          );
        } else {
          echo json_encode(
            array(
              'status_code'   => 500,
              'status'        => 'error', 
              'message'       => 'Data gagal disimpan.'
            )
          );
        }
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

  public function koreksi_approved()
  {
      //CHECK FOR ACCESS FOR EACH FUNCTION
      $user_level       = $this->session->userdata('user_level');
      $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

      if ($check_permission->num_rows() == 1) {

          $KoreksiID  = $this->input->post('Id');
          $isApproved = $this->input->post('isApproved');

          // Mengambil Data Koreksi
          $this->ABSENSI
              ->select('a.*, b.USERID')
              ->from('Trans_KoreksiAbsensi a')
              ->join('USERINFO b', 'a.EmployeeID = b.SSN', 'left')
              ->where('a.KoreksiID', $KoreksiID);

          $Query = $this->ABSENSI->get()->row();

          if (!$Query) {
              echo json_encode(array("status_code" => 404, "status" => "error", "message" => "Data tidak ditemukan."));
              exit;
          }

          $UserID     = $Query->USERID;
          $TanggalIn  = $Query->CheckInKoreksi;
          $TanggalOut = $Query->CheckOutKoreksi;
          $Kolom      = $Query->ChangeTo;

          // Data update untuk tabel Trans_KoreksiAbsensi
          $FirstData = array(
              'isApproved'   => $isApproved,
              'ApprovedDate' => date('Y-m-d H:i:s'),
              'ApprovedBy'   => $this->session->userdata('user_nip')
          );

          // 1. UPDATE status di Trans_KoreksiAbsensi
          $Update = $this->ABSENSI->update('Trans_KoreksiAbsensi', $FirstData, array('KoreksiID' => $KoreksiID));

          if ($Update) {

              // Menyiapkan Data Insert untuk CHECKINOUT
              // Tentukan waktu berdasarkan kolom yang dikoreksi (IN atau OUT)
              $CheckTimeTarget = ($Kolom == 'IN') ? $TanggalIn : $TanggalOut;

              // 2. CEK DULU: Apakah data ini sudah ada di CHECKINOUT?
              $this->ABSENSI->where('USERID', $UserID);
              $this->ABSENSI->where('CHECKTIME', $CheckTimeTarget);
              $CekDuplikat = $this->ABSENSI->get('CHECKINOUT');

              // Jika data BELUM ada (num_rows 0), baru kita Insert
              if ($CekDuplikat->num_rows() == 0) {
                  
                  $DataInsert = array(
                      'USERID'     => $UserID,
                      'CHECKTIME'  => $CheckTimeTarget,
                      'CHECKTYPE'  => 'I',
                      'VERIFYCODE' => 0,
                      'SENSORID'   => 5,
                      'Memoinfo'   => NULL,
                      'WorkCode'   => 0,
                      'sn'         => '6530143500103',
                      'UserExtFmt' => 1,
                      'MachineId'  => NULL
                  );

                  $this->ABSENSI->insert('CHECKINOUT', $DataInsert);
              } 
              // Jika data SUDAH ada, kita biarkan saja (Skip Insert), 
              // tapi tetap anggap proses ini sukses.

              // Kirim response sukses
              echo json_encode(
                  array(
                      'status_code' => 200,
                      'status'      => 'success',
                      'message'     => 'Data sukses disimpan.'
                  )
              );

          } else {
              // Gagal Update Trans_KoreksiAbsensi
              echo json_encode(
                  array(
                      'status_code' => 500,
                      'status'      => 'error',
                      'message'     => 'Gagal menyimpan data (Update Error).'
                  )
              );
          }
      } else {
          echo json_encode(array("status" => "forbidden"));
      }
      exit;
  }

  public function koreksi_list()
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

    $Sql            = "EXEC dbo.GetDataKoreksiAbsensi @StartDate = ?, @EndDate = ?, @DeptIDs = ?";
    $Query          = $this->ABSENSI->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();

		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->isApproved == 'P' ? 'Y' : 'P';
      $ApLbl  = $value->isApproved == 'P' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->KoreksiID."'";
      $Isi2   = "'".$value->KoreksiID."', '".$ApSts."', '".$ApLbl."'";

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
      //$row[]  = '<button class="btn '.$value->isApprovedClass.' btn-sm btn-block" onclick="approved('.$Isi2.')">'.$ApLbl.'</button>';
      $row[]  = $value->isApproved == 'Y' ? 'APPROVED' : 'PENDING';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->NAME;
      $row[]  = $value->Tanggal;
      $row[]  = $value->CheckInAsli;
      $row[]  = $value->CheckOutAsli;
      $row[]  = $value->ColumnChange;
      $row[]  = $value->CheckInKoreksi;
      $row[]  = $value->CheckOutKoreksi;
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

  public function koreksi_edit($id)
  {
    $data = $this->koreksi->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function koreksi_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_koreksi();
        
    // Ambil data form
    $Nomor             = $this->input->post('Nomor');
    $DeptID            = $this->input->post('DeptID');
    $EmployeeID        = $this->input->post('EmployeeID');
    $Tanggal           = $this->input->post('Tanggal');
    $CheckInAsli       = date('Y-m-d H:i:s', strtotime($this->input->post('CheckInAsli')));
    $CheckOutAsli      = date('Y-m-d H:i:s', strtotime($this->input->post('CheckOutAsli')));
    $ChangeTo          = $this->input->post('ChangeTo');
    $CheckInKoreksi    = ($ChangeTo === 'OUT') ? null : str_replace('T', ' ', date('Y-m-d H:i:s', strtotime($this->input->post('CheckInKoreksi'))));
    $CheckOutKoreksi   = ($ChangeTo === 'IN') ? null : str_replace('T', ' ', date('Y-m-d H:i:s', strtotime($this->input->post('CheckOutKoreksi'))));
    $Notes             = ucfirst($this->input->post('Notes'));

    // Data header
    $FirstData = array(
      'EmployeeID'      => $EmployeeID,
      'Tanggal'         => $Tanggal,
      'CheckInAsli'     => $CheckInAsli,
      'CheckOutAsli'    => $CheckOutAsli,
      'ChangeTo'        => $ChangeTo,
      'CheckInKoreksi'  => $CheckInKoreksi,
      'CheckOutKoreksi' => $CheckOutKoreksi,
      'Notes'           => $Notes,
      'UpdatedDate'     => date('Y-m-d H:i:s'),
      'UpdatedBy'       => $this->session->userdata('user_id')
    );

    //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

    $this->ABSENSI->where('Nomor', $Nomor);
    $Update = $this->ABSENSI->update('Trans_KoreksiAbsensi', $FirstData);

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

  public function koreksi_deleted($id)
  {
    $data_delete    = $this->koreksi->get_by_id($id); //DATA DELETE
    $data           = $this->koreksi->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function koreksi_periksa_absensi()
  {
    $this->_validation_periksa();

    $EmployeeID = $this->input->post('EmployeeID');
    $Tanggal    = $this->input->post('Tanggal');
    $this->ABSENSI
        ->select("a.USERID, CONVERT(VARCHAR(19), a.CHECKTIME, 120) AS CHECKTIME", false)
        ->from("CHECKINOUT a")
        ->join("USERINFO b", "b.USERID = a.USERID", "left")
        ->where("CAST(a.CHECKTIME AS DATE) =", $Tanggal)
        ->where("b.SSN", $EmployeeID)
        ->order_by("a.CHECKTIME", "DESC");

    $query = $this->ABSENSI->get();

    if ($query->num_rows() > 0) {
      echo json_encode(
        array(
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data ditemukan',
          'data'        => $query->result()
        )
      );
    } else {
      echo json_encode(
        array(
          'status_code' => 404,
          'status'      => 'error',
          'message'     => 'Data tidak ditemukan',
          'data'        => array()
        )
      );
    }
    exit;
  }

  private function _validation_periksa()
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

    if ($this->input->post('Tanggal') == '') {
      $data['inputerror'][]   = 'Tanggal';
      $data['error_string'][] = 'Tanggal is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_koreksi()
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

    if ($this->input->post('Tanggal') == '') {
      $data['inputerror'][]   = 'Tanggal';
      $data['error_string'][] = 'Tanggal is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Notes') == '') {
      $data['inputerror'][]   = 'Notes';
      $data['error_string'][] = 'Notes is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ChangeTo') == '') {
      $data['inputerror'][]   = 'ChangeTo';
      $data['error_string'][] = 'Perubahan pada is required';
      $data['status']         = FALSE;
    }

    if ($ChangeTo == 'IN') {
      if ($this->input->post('CheckInKoreksi') == '') {
        $data['inputerror'][]   = 'CheckInKoreksi';
        $data['error_string'][] = 'Check In is required';
        $data['status']         = FALSE;
      }
    }

    if ($ChangeTo == 'OUT') {
      if ($this->input->post('CheckOutKoreksi') == '') {
        $data['inputerror'][]   = 'CheckOutKoreksi';
        $data['error_string'][] = 'Check Out is required';
        $data['status']         = FALSE;
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
