<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ijin_telat extends CI_Controller
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
    $this->load->model('ijintelat_model', 'ijin');
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
      $data['nama_halaman']     = "Daftar Ijin Datang Telat";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['type_absensi']     = $this->type->get_all_data();
      $data['department_att'] 	= get_department_att();
      $data['DeptList'] 	      = get_department_for_security();
      $data['DEPTID']           = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']         = $this->session->userdata('user_dept_name');

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/pga/ijin_telat', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function ijin_telat_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_ijin_telat();

      // Generate nomor request
      $Nomor             = $this->ijin->generateIjinTelatNumber();
      // Ambil data form
      $DeptID            = $this->input->post('DeptID');
      $EmployeeID        = $this->input->post('EmployeeID');
      $Keperluan         = $this->input->post('Keperluan');
      $Tanggal           = $this->input->post('Tanggal');
      $JamDatang         = $this->input->post('JamDatang');
      $Notes             = ucfirst($this->input->post('Notes'));
      $TanggalIn         = $Tanggal." ".$JamDatang;

      //KALKULASI TELAT
      $Sql               = "EXEC dbo.KalkulasiIjinDatangTelat @SSN_Input = ?, @Tanggal_Input = ?, @JamDatang_Input = ?";
      $Query             = $this->ABSENSI->query($Sql, [$EmployeeID, $Tanggal, $JamDatang]);
      $CalcResult 		   = $Query->row();

      // Data header
      $FirstData = array(
        'Nomor'           => $Nomor,
        'IsApproved'      => 'P',
        'EmployeeID'      => $EmployeeID,
        'DeptID'          => $DeptID,
        'Keperluan'       => $Keperluan,
        'Tanggal'         => $Tanggal,
        'JamDatang'       => $JamDatang,
        'DurasiMenit'     => $CalcResult->DurasiTelatFinal,
        'GajiHarian'      => floatval($CalcResult->DailySalary),
        'Potongan'        => floatval($CalcResult->Potongan),
        'GajiHarianNett'  => floatval($CalcResult->DailySalaryNett),
        'Notes'           => $Notes,
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData, "kalkulasi" => $CalcResult)); exit;

      // Simpan ke database
      $Insert = $this->ABSENSI->insert('Trans_IjinDatangTelat', $FirstData);
      if ($Insert) {

        $GetUserID = $this->ABSENSI->get_where('USERINFO', array('SSN' => $EmployeeID))->row();

        if ($Keperluan == 'Pribadi') {
          $DataInsert = array(
            'USERID'     => $GetUserID->USERID,
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

          //echo json_encode(array("status" => "error", "data" => $DataInsert)); exit;

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
              'status_code'   => 200,
              'status'        => 'success', 
              'message'       => 'Data berhasil disimpan.'
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

  public function ijin_telat_approved()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $AbsenceID      = $this->input->post('Id');
      $isApproved     = $this->input->post('isApproved');

      $FirstData = array(
        'IsApproved'   => $isApproved,
        'ApprovedDate' => ($isApproved == 'P') ? NULL : date('Y-m-d H:i:s'),
        'ApprovedBy'   => ($isApproved == 'P') ? NULL : $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_IjinDatangTelat', $FirstData, array('Id' => $AbsenceID));
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

  public function security_check_go()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $Id             = $this->input->post('Id');
      $SecurityID     = $this->input->post('SecurityID');
      
      $FirstData = array(
        'SecurityCheckedGoDate'  => date('Y-m-d H:i:s'),
        'SecurityCheckedGoBy'    => $SecurityID
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_IjinKeluar', $FirstData, array('Id' => $Id));
      if ($Update) {
        echo json_encode(
          array(
            'status_code'   => 200,
            'status'        => 'success', 
            'message'       => 'Data sukses diupdate.'
          )
        );
      } else {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error', 
            'message'      => 'Gagal gagal diupdate.'
          )
        );
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }
  
  public function security_check_back()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $Id               = $this->input->post('Id');
      $SecurityID       = $this->input->post('SecurityID');
      $Data             = $this->ijin->get_by_id($Id);
      $Tanggal          = $Data->Tanggal;
      $JamPergiString   = $Tanggal." ".$Data->JamPergi;
      $JamKembaliString = date('Y-m-d H:i:s');
      $datetime1        = new DateTime($JamPergiString);
      $datetime2        = new DateTime($JamKembaliString);
      $interval         = $datetime1->diff($datetime2);
      $DurasiMenit      = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

      //echo json_encode(array("status" => "error", "data" => $Data, "JamPergi" => $JamPergi, "JamKembali" => $JamKembali)); exit;
      
      $FirstData = array(
        'DurasiMenit'              => $DurasiMenit,
        'JamKembali'               => $JamKembaliString,
        'SecurityCheckedBackDate'  => $JamKembaliString,
        'SecurityCheckedBackBy'    => $SecurityID
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->ABSENSI->update('Trans_IjinKeluar', $FirstData, array('Id' => $Id));
      if ($Update) {
        echo json_encode(
          array(
            'status_code'   => 200,
            'status'        => 'success', 
            'message'       => 'Data sukses diupdate.'
          )
        );
      } else {
        echo json_encode(
          array(
            'status_code'  => 500,
            'status'       => 'error', 
            'message'      => 'Gagal gagal diupdate.'
          )
        );
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }

  public function ijin_telat_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		$StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$Keperluan      = $this->input->post('keperluan');
		$DeptID 	      = $this->input->post('dept_id');
    if (empty($DeptID)) {
      $DeptID       = null;
    } else if (is_array($DeptID)) {
      $DeptID       = implode(',', $DeptID);
    }

    $Sql            = "EXEC dbo.GetDataIjinTelat @StartDate = ?, @EndDate = ?, @DeptIDs = ?, @Keperluan = ?";
    $Query          = $this->ABSENSI->query($Sql, [$StartDate, $EndDate, $DeptID, $Keperluan]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->IsApproved == 'P' ? 'Y' : 'P';
      $ApLbl  = $value->IsApproved == 'P' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->Id."'";
      $Isi2   = "'".$value->Id."', '".$ApSts."', '".$ApLbl."'";
      $Isi3   = "'".$value->Id."', '".$value->NAME."'";

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
      $row[]  = $value->IsApproved == 'Y' ? 'APPROVED' : 'PENDING';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->NAME;
      $row[]  = $value->Keperluan;
      $row[]  = $value->Tanggal;
      $row[]  = $value->JamDatang;
      $row[]  = $value->DurasiMenit;
      if ($this->session->userdata('user_dept_name') == 'IT' || $this->session->userdata('user_dept_name') == 'HRD' || $this->session->userdata('user_dept_name') == 'ACCOUNTING') {
      $row[]  = $value->GajiHarian;
      $row[]  = $value->Potongan;
      $row[]  = $value->GajiHarianNett;
      }
      $row[]  = $value->Notes;
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;
      $row[]  = $value->HRDApprovedDate;
      $row[]  = $value->HRDApprovedName;
      // $row[]  = $value->SecurityCheckedDate;
      // $row[]  = $value->SecurityCheckedBy;
  
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

  public function ijin_telat_edit($id)
  {
    $data = $this->ijin->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function ijin_telat_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_ijin_telat();
        
    // Ambil data form
    $Id             = $this->input->post('kode');
    $DeptID         = $this->input->post('DeptID');
    $EmployeeID     = $this->input->post('EmployeeID');
    $Keperluan      = $this->input->post('Keperluan');
    $Kembali        = $this->input->post('Kembali');
    $Tanggal        = $this->input->post('Tanggal');
    $JamPergi       = $this->input->post('JamPergi');
    $Notes          = ucfirst($this->input->post('Notes'));

    // Data header
    $FirstData = array(
      'EmployeeID'      => $EmployeeID,
      'DeptID'          => $DeptID,
      'Keperluan'       => $Keperluan,
      'Kembali'         => $Kembali,
      'Tanggal'         => $Tanggal,
      'JamPergi'        => $JamPergi,
      'Notes'           => $Notes,
      'UpdatedDate'     => date('Y-m-d H:i:s'),
      'UpdatedBy'       => $this->session->userdata('user_id')
    );

    //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

    $this->ABSENSI->where('Id', $Id);
    $Update = $this->ABSENSI->update('Trans_IjinKeluar', $FirstData);

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

  public function ijin_telat_deleted($id)
  {
    $data_delete    = $this->ijin->get_by_id($id); //DATA DELETE
    $data           = $this->ijin->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_ijin_telat()
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

    if ($this->input->post('Keperluan') == '') {
      $data['inputerror'][]   = 'Keperluan';
      $data['error_string'][] = 'Keperluan is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Tanggal') == '') {
      $data['inputerror'][]   = 'Tanggal';
      $data['error_string'][] = 'Tanggal is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JamDatang') == '') {
      $data['inputerror'][]   = 'JamDatang';
      $data['error_string'][] = 'Jam Datang is required';
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
