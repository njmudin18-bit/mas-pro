<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ijin_keluar extends CI_Controller
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
    $this->load->library(array('session', 'cart', 'telegram'));

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
    $this->load->model('ijinkeluar_model', 'ijin');
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
      $data['nama_halaman']     = "Daftar Ijin Keluar";
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

      $this->load->view('adminx/pga/ijin_keluar', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function ijin_keluar_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_ijin_keluar();

      // Generate nomor request
      $ChatID            = '-1003419846788';
      $Nomor             = $this->ijin->generateIjinNumber();
      // Ambil data form
      $DeptID            = $this->input->post('DeptID');
      $EmployeeID        = $this->input->post('EmployeeID');
      $Keperluan         = $this->input->post('Keperluan');
      $Kembali           = $this->input->post('Kembali');
      $Tanggal           = $this->input->post('Tanggal');
      $JamPergi          = $this->input->post('JamPergi');
      $Notes             = ucfirst($this->input->post('Notes'));
      $TanggalOut        = $Tanggal." ".$JamPergi;

      // Data header
      $FirstData = array(
        'Nomor'           => $Nomor,
        'IsApproved'      => 'P',
        'EmployeeID'      => $EmployeeID,
        'DeptID'          => $DeptID,
        'Keperluan'       => $Keperluan,
        'Kembali'         => $Kembali,
        'Tanggal'         => $Tanggal,
        'JamPergi'        => $JamPergi,
        'Notes'           => $Notes,
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_id')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Insert = $this->ABSENSI->insert('Trans_IjinKeluar', $FirstData);
      if ($Insert) {

        $GetUserID  = $this->ABSENSI->get_where('USERINFO', array('SSN' => $EmployeeID))->row();
        $DeptName   = $this->ABSENSI->get_where('DEPARTMENTS', array('DEPTID' => $DeptID))->row();

        $text       = "<b>== INFO PEGAWAI IJIN KELUAR ==</b> \n\n";
        $text      .= "Halo team security, ada pegawai ijin dengan informasi : \n";
        $text      .= "Nomor: <b>".$Nomor."</b> \n";
        $text      .= "NIP: <b>".$EmployeeID."</b> \n";
        $text      .= "Nama: <b>".$GetUserID->NAME."</b> \n";
        $text      .= "Departemen: <b>".$DeptName->DEPTNAME."</b> \n\n";
        $text      .= "Keluar perusahaan ditanggal dan jam : \n";
        $text      .= "Tanggal: <b>".date("d M Y", strtotime($Tanggal))."</b>\n";
        $text      .= "Jam Keluar: <b>".$JamPergi."</b>\n";
        $text      .= "Kembali: <b>".$Kembali."</b>\n";
        $text      .= "Keperluan: <b>".$Keperluan."</b>\n";
        $text      .= "Keterangan: <b>".$Notes."</b>\n\n";
        $text      .= "Sekian dan terima kasih.\n";
        $text      .= "<i>Dikirim dari system <a href=\"http://10.11.9.22:8080/omas-monitoring-projek\">MASPRO</a>.</i>\n";
        
        $result = $this->telegram->sendMessage($ChatID, $text);

        //exit;
        if ($Keperluan == 'Pribadi' && $Kembali == 'TIDAK') {
          $DataInsert = array(
            'USERID'     => $GetUserID->USERID,
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
              'status_code'     => 200,
              'status'          => 'success', 
              'message'         => 'Data berhasil disimpan.'
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

  public function ijin_keluar_approved()
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
      $Update = $this->ABSENSI->update('Trans_IjinKeluar', $FirstData, array('Id' => $AbsenceID));
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
      $GoDate         = date('Y-m-d H:i:s', strtotime($this->input->post('ReturnDate')));
      $TanggalNow     = date('Y-m-d', strtotime($this->input->post('ReturnDate')));
      $Times          = date('H:i:s', strtotime($this->input->post('ReturnDate')));
      $Now            = $GoDate;

      //echo json_encode(array('SID' => $SecurityID, 'TGL' => $GoDate)); exit;

      $Cek = $this->ABSENSI->get_where('Trans_IjinKeluar', array('IsApproved' => 'Y', 'Id' => $Id))->num_rows();
      if ($Cek > 0) {
        $DataAwal        = $this->ijin->get_by_id($Id);
        $TanggalPergi    = $DataAwal->Tanggal;
        $Now             = $Times; //date('H:i:s');
        $Time            = "";
        if ($DataAwal->Keperluan == 'Pribadi' && $DataAwal->Kembali == 'TIDAK') {
          $Time          = $DataAwal->JamPergi;
        } else {
          $Time          = $Times; //date('H:i:s');
        }
        
        $Nip             = $DataAwal->EmployeeID;
        $SqlKalkulasi    = "EXEC dbo.KalkulasiIjinKeluar131125 @EmployeeID_Input = ?, @Tanggal_Input = ?, @JamDatang_Input = ?";
        $QueryKalkulasi  = $this->ABSENSI->query($SqlKalkulasi, [$Nip, $TanggalPergi, $Time]);
        $ResKalkulasi    = $QueryKalkulasi->row();

        $UpdateData = array(
          'JamPergi'                 => $ResKalkulasi->JamPergi,
          'JamKembali'               => $ResKalkulasi->JamKembali,
          'DurasiMenit'              => floatval($ResKalkulasi->DurasiIzinMenit),
          'GajiHarian'               => floatval($ResKalkulasi->DailySalary),
          'Potongan'                 => floatval($ResKalkulasi->Potongan),
          'GajiHarianNett'           => floatval($ResKalkulasi->GajiHarianNett),
          'AdditionalNotes'          => $ResKalkulasi->Keterangan,
          'SecurityCheckedGoDate'    => $GoDate, //date('Y-m-d H:i:s'),
          'SecurityCheckedGoBy'      => $SecurityID
        );

        //echo json_encode(array("status" => "error", "kalkulasi" => $ResKalkulasi, "data" => $UpdateData, "awal" => $DataAwal)); exit;

        // Simpan ke database
        $Update = $this->ABSENSI->update('Trans_IjinKeluar', $UpdateData, array('Id' => $Id));
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
        echo json_encode(
          array(
            'status_code'  => 404,
            'status'       => 'error', 
            'message'      => 'Data belum di setujui oleh HRD.'
          )
        );
        exit;
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

      $Id             = $this->input->post('Id');
      $DataAwal       = $this->ijin->get_by_id($Id);
      $TanggalPergi   = $DataAwal->Tanggal;
      $Nip            = $this->input->post('Nip');
      $SecurityID     = $this->input->post('SecurityID');
      $ReturnDate     = date('Y-m-d H:i:s', strtotime($this->input->post('ReturnDate')));

      //echo json_encode(array('SID' => $SecurityID, 'TGL' => $ReturnDate)); exit;
      $TanggalNow     = date('Y-m-d', strtotime($this->input->post('ReturnDate'))); //date('Y-m-d'); //'2025-11-05';
      $Time           = date('H:i:s', strtotime($this->input->post('ReturnDate'))); //date('H:i:s'); //'17:00:00';
      $Now            = $ReturnDate;

      $Cek = $this->ABSENSI->get_where('Trans_IjinKeluar', array('IsApproved' => 'Y', 'Id' => $Id))->num_rows();
      if ($Cek > 0) {
        $CekLagi = $this->ABSENSI->where('SecurityCheckedGoBy IS NOT NULL')->where('SecurityCheckedGoDate IS NOT NULL')->where('IsApproved', 'Y')->where('Id', $Id)->get('Trans_IjinKeluar')->num_rows();
        if ($CekLagi > 0) {
          if ($TanggalPergi == $TanggalNow) {
            $SqlKalkulasi    = "EXEC dbo.KalkulasiIjinKeluar @EmployeeID_Input = ?, @Tanggal_Input = ?, @JamDatang_Input = ?";
            $QueryKalkulasi  = $this->ABSENSI->query($SqlKalkulasi, [$Nip, $TanggalNow, $Time]);
            $ResKalkulasi    = $QueryKalkulasi->row();

            $UpdateData = array(
              'JamPergi'                 => $ResKalkulasi->JamPergi,
              'JamKembali'               => $ResKalkulasi->JamKembali,
              'DurasiMenit'              => floatval($ResKalkulasi->DurasiIzinMenit),
              'GajiHarian'               => floatval($ResKalkulasi->DailySalary),
              'Potongan'                 => floatval($ResKalkulasi->Potongan),
              'GajiHarianNett'           => floatval($ResKalkulasi->GajiHarianNett),
              'AdditionalNotes'          => $ResKalkulasi->Keterangan,
              'SecurityCheckedBackDate'  => $Now,
              'SecurityCheckedBackBy'    => $SecurityID
            );

            //echo json_encode(array("status" => "error", "message" => "debug", "data" => $UpdateData, "kalkulasi" => $ResKalkulasi)); exit;

            // Update ke database
            $Update = $this->ABSENSI->update('Trans_IjinKeluar', $UpdateData, array('Id' => $Id));
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
            echo json_encode(
              array(
                'status_code'   => 500,
                'status'        => 'error', 
                'message'       => 'Transaksi berbeda tanggal.'
              )
            );
            exit;
          }
        } else {
          echo json_encode(
            array(
              'status_code'  => 404,
              'status'       => 'error', 
              'message'      => 'Pegawai masih belum keluar perusahaan.'
            )
          );
          exit;
        }
      } else {
        echo json_encode(
          array(
            'status_code'  => 404,
            'status'       => 'error', 
            'message'      => 'Data belum di setujui oleh HRD.'
          )
        );
        exit;
      }
      //echo json_encode(array("status" => "error", "tgl" => $Tanggal, 'jam' => $Time)); exit;  
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }

  public function ijin_keluar_list()
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

    $Sql            = "EXEC dbo.GetDataIjinKeluar @StartDate = ?, @EndDate = ?, @DeptIDs = ?, @Keperluan = ?";
    $Query          = $this->ABSENSI->query($Sql, [$StartDate, $EndDate, $DeptID, $Keperluan]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $ApSts  = $value->IsApproved == 'P' ? 'Y' : 'P';
      $ApLbl  = $value->IsApproved == 'P' ? 'APPROVED' : 'PENDING';
      $Isi    = "'".$value->Id."'";
      $Isi2   = "'".$value->Id."', '".$ApSts."', '".$ApLbl."'";
      $Isi3   = "'".$value->Id."', '".$value->NAME."', '".$value->EmployeeID."'";
      $Html3  = '';
      if ($value->Kembali == 'YA') {
        $Html3  = '<a class="dropdown-item" href="#" onclick="securityCheckGo('.$Isi3.')">Security Check Pergi</a>
                   <a class="dropdown-item" href="#" onclick="securityCheckBack('.$Isi3.')">Security Check Kembali</a>';
      } else {
        $Html3  = '<a class="dropdown-item" href="#" onclick="securityCheckGo('.$Isi3.')">Security Check Pergi</a>';
      }
      

      $row    = [];
      $row[]  = $No++;
      $row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="#" onclick="approved('.$Isi2.')">'.$ApLbl.'</a>
                      '.$Html3.'
                    </div>
                  </div>
                </div>';
      $row[]  = $value->Nomor;
      $row[]  = $value->IsApproved == 'Y' ? 'APPROVED' : 'PENDING';
      $row[]  = $value->DEPTNAME;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->NAME;
      $row[]  = $value->Keperluan;
      $row[]  = $value->Kembali;
      $row[]  = $value->Tanggal;
      $row[]  = $value->JamPergi;
      $row[]  = $value->JamKembali;
      //$row[]  = $value->DurasiMenit;
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
      $row[]  = $value->SecurityCheckedGoDate;
      $row[]  = $value->SecurityCheckedGoBy;
      $row[]  = $value->SecurityCheckedBackDate;
      $row[]  = $value->SecurityCheckedBackBy;
  
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

  public function ijin_keluar_edit($id)
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

  public function ijin_keluar_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_ijin_keluar();
        
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

  public function ijin_keluar_deleted($id)
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

  private function _validation_ijin_keluar()
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

    if ($this->input->post('Kembali') == '') {
      $data['inputerror'][]   = 'Kembali';
      $data['error_string'][] = 'Kembali is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Tanggal') == '') {
      $data['inputerror'][]   = 'Tanggal';
      $data['error_string'][] = 'Tanggal is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JamPergi') == '') {
      $data['inputerror'][]   = 'JamPergi';
      $data['error_string'][] = 'Jam Pergi is required';
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
