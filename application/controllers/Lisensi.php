<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lisensi extends CI_Controller
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
    $this->load->model('vendorit_model', 'vendor');
    $this->load->model('lisensi_model', 'lisensi');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "IT";
      $data['nama_halaman']     = "Daftar Lisensi";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['vendors']          = $this->vendor->get_all_vendor();
      $data['department_att'] 	= get_department_att();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/it/lisensi/lisensi', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function lisensi_add()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_lisensi();

      $Data = array(
        'LicenseCode'    => $this->lisensi->generateLicenseNumber(),
        'LicenseName'    => ucfirst($this->input->post('LicenseName')),
        'LicenseType'    => $this->input->post('LicenseType'),
        'LicenseKey'     => $this->input->post('LicenseKey'),
        'VendorID'       => $this->input->post('VendorList'),
        'PurchaseDate'   => $this->input->post('PurchaseDate'),
        'ExpiryDate'     => $this->input->post('ExpiryDate'),
        'SeatsAllowed'   => floatval($this->input->post('SeatsAllowed')),
        'Status'         => $this->input->post('Status'),
        'Notes'          => $this->input->post('Notes'),
        'CreateDate'     => date('Y-m-d H:i:s'),
        'CreateBy'       => $this->session->userdata('user_code')
      );

      //echo json_encode(array("status" => "error", "Data" => $Data)); exit;
      $insert = $this->lisensi->save($Data);
      echo json_encode(array("status" => "success"));

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "ADD";
      $log_data   = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function lisensi_list_OLD()
	{
		$Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');

    //$Sql         = "EXEC dbo.GetTrialData @StartDate = ?, @EndDate = ?";
    //$Query       = $this->BJGMAS01->query($Sql, array($StartDate, $EndDate));
    $Sql         = "SELECT 
                      a.Id,
                      a.VendorID,
                      a.LicenseType,
                      b.VendorName, 
                      a.LicenseCode, 
                      a.LicenseName, 
                      'XXXX-XXXX-XXXXXX-' + RIGHT(a.LicenseKey, 11) AS LicenseKey,
                      a.PurchaseDate, 
                      a.ExpiryDate,
                      ISNULL(a.SeatsAllowed, 0) AS SeatsAllowed,
                      ISNULL(COUNT(c.Id), 0) AS SeatsUsed,
                      (ISNULL(a.SeatsAllowed, 0) - ISNULL(COUNT(c.Id), 0)) AS Sisa,
                      a.Status,
                      CASE 
                        WHEN a.Status = 'Active' THEN 'badge-success'
                        WHEN a.Status = 'Expired' THEN 'badge-danger'
                        WHEN a.Status = 'Terminated' THEN 'badge-dark'
                        ELSE 'badge-secondary'
                      END AS StatusClass,
                      a.Notes,
                      CAST(a.CreateDate AS datetime) AS CreateDate, 
                      a.CreateBy
                    FROM Ms_License a
                    LEFT JOIN Ms_VendorIT b ON b.Id = a.VendorID
                    LEFT JOIN Trans_LicenseAssignment c ON c.LicenseID = a.Id
                    WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
                    GROUP BY 
                      a.Id, a.VendorID, a.LicenseType, 
                      b.VendorName, a.LicenseCode, a.LicenseName, a.LicenseKey, 
                      a.PurchaseDate, a.ExpiryDate, a.SeatsAllowed, 
                      a.Status, a.Notes, a.CreateDate, a.CreateBy
                    ORDER BY a.CreateDate DESC";
    $Query       = $this->BJGMAS01->query($Sql);
    $Result      = $Query->result();
    $Total       = count($Result);
    $Paged       = array_slice($Result, $Start, $Length);

    $Data        = [];
    $No          = $Start + 1;
    foreach ($Paged as $key => $Res) {
      $Isi    = "'".$Res->Id."'";
      $Isi2   = "'".$Res->Id."', '".$Res->LicenseName."', '".$Res->SeatsAllowed."', '".$Res->VendorID."', '".$Res->VendorName."', '".$Res->LicenseType."', '".$Res->Status."'";
      $Trans  = $Res->SeatsAllowed > 1 ? '<a class="dropdown-item" href="#" onclick="openModalTransaksi('.$Isi2.')">Transaksi User</a><a class="dropdown-item" href="#" onclick="openModalDevice('.$Isi2.')">Transaksi Device</a>' : '';
      $Row    = array();
      $Row[]  = $No++;
      $Row[]  = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      '.$Trans.'
                    </div>
                  </div>
                </div>';
      $Row[]  = $Res->LicenseType;
      $Row[]  = '<span class="badge badge-pill '.$Res->StatusClass.'">'.$Res->Status.'</span>';
      $Row[]  = $Res->LicenseName;
      $Row[]  = $Res->LicenseKey;
      $Row[]  = $Res->VendorName;
      $Row[]  = $Res->PurchaseDate;
      $Row[]  = $Res->ExpiryDate;
      $Row[]  = $Res->SeatsAllowed;
      $Row[]  = $Res->SeatsUsed;
      $Row[]  = $Res->Sisa;
      $Row[]  = ucfirst($Res->Notes);
      $Row[]  = $Res->CreateDate;
      $Row[]  = $Res->CreateBy;

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

  public function lisensi_list()
	{
		$draw 			 = intval($this->input->get("draw"));
		$start 			 = intval($this->input->get("start"));
		$length 		 = intval($this->input->get("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');


    $Sql         = "EXEC dbo.GetLisensiDatas @StartDate = ?, @EndDate = ?";
    $Query       = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate]);
    $Result      = $Query->result();
		$Data        = [];
		$No 		     = 1;

    foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Id."'";
      $Isi2   = "'".$value->Id."', '".$value->LicenseName."', '".$value->SeatsAllowed."', '".$value->VendorID."', '".$value->VendorName."', '".$value->LicenseType."', '".$value->Status."'";
      $Trans  = $value->SeatsAllowed >= 1 ? '<a class="dropdown-item" href="#" onclick="openModalTransaksi('.$Isi2.')">Transaksi User</a><a class="dropdown-item" href="#" onclick="openModalDevice('.$Isi2.')">Transaksi Device</a>' : '';
			$Data[] = array(
				$No++,
				'<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
              '.$Trans.'
            </div>
          </div>
        </div>',
        $value->LicenseType,
        '<span class="badge badge-pill '.$value->StatusClass.'">'.$value->Status.'</span>',
				$value->LicenseName,
        $value->LicenseKeyFormat,
        $value->VendorName,
        $value->PurchaseDate,
        $value->ExpiryDate,
        $value->SeatsAllowed,
        $value->SeatsUsed,
        $value->Sisa,
        ucfirst(nl2br($value->DigunakanOleh)),
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

  public function lisensi_edit($id)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data = $this->lisensi->get_by_id($id);
      echo json_encode($data);

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "EDIT";
      $log_data       = json_encode($data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function lisensi_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_lisensi();

      $Data = array(
        'LicenseName'    => ucfirst($this->input->post('LicenseName')),
        'LicenseType'    => $this->input->post('LicenseType'),
        'LicenseKey'     => $this->input->post('LicenseKey'),
        'VendorID'       => $this->input->post('VendorList'),
        'PurchaseDate'   => $this->input->post('PurchaseDate'),
        'ExpiryDate'     => $this->input->post('ExpiryDate'),
        'SeatsAllowed'   => floatval($this->input->post('SeatsAllowed')),
        'Status'         => $this->input->post('Status'),
        'Notes'          => $this->input->post('Notes'),
        'UpdateDate'     => date('Y-m-d H:i:s'),
        'UpdateBy'       => $this->session->userdata('user_code')
      );

      //echo json_encode(array("status" => "error", "Data" => $Data)); exit;

      $this->lisensi->update(array('Id' => $this->input->post('kode')), $Data);
      echo json_encode(array("status" => "success"));

      //ADDING TO LOG
      $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "UPDATE";
      $log_data   = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function lisensi_deleted($id)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data_delete    = $this->lisensi->get_by_id($id); //DATA DELETE
      $data           = $this->lisensi->delete_by_id($id);

      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "DELETE";
      $log_data       = json_encode($data_delete);
      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function get_all_vendor()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->vendor->get_all_vendor($Search);
    
    echo json_encode($Result);
    exit;
  }

  //SAVE TRANSAKSI USER
  function transaksi_user_add() 
  {
    $this->_validation_transaksi_user();

    // Ambil input dari form
    $TransID            = $this->input->post('TransID');
    $TransVendorID      = $this->input->post('TransVendorID');
    $TransLicenseName   = $this->input->post('TransLicenseName');
    $TransLicenseType   = $this->input->post('TransLicenseType');
    $TransSeatsAllowed  = (int) $this->input->post('TransSeatsAllowed');
    $TransStatus        = $this->input->post('TransStatus');
    $TransType          = $this->input->post('TransType');

    // Data array (multi item)
    $TransQty           = $this->input->post('TransQty');
    $kodeTrans          = $this->input->post('kodeTrans');
    $TransDept          = $this->input->post('TransDept');
    $TransDeptText      = $this->input->post('TransDeptText');
    $TransUserID        = $this->input->post('TransUserID');
    $TransUserText      = $this->input->post('TransUserText');
    $TransNotes         = $this->input->post('TransNotes');

    // Hitung total qty
    $totalQty = array_sum($TransQty);
    if ($totalQty > $TransSeatsAllowed) {
        echo json_encode([
            'status_code'  => 500,
            'status'       => 'error',
            'message'      => 'Total Qty transaksi tidak boleh melebihi jumlah akun ('.$TransSeatsAllowed.')'
        ]);
        exit;
    }

    // Pisahkan batch insert & update
    $InsertData = [];
    $UpdateData = [];

    for ($i = 0; $i < count($TransQty); $i++) {
      if (empty($kodeTrans[$i])) {
        // INSERT baru
        $InsertData[] = [
          'LicenseID'        => $TransID,
          'AssignedType'     => $TransType,
          'AssignedDeptID'   => $TransDept[$i],
          'AssignedDeptName' => $TransDeptText[$i],
          'AssignedID'       => $TransUserID[$i],
          'AssignedName'     => $TransUserText[$i],
          'Quantity'         => (int) $TransQty[$i],
          'Notes'            => ucwords($TransNotes[$i]),
          'CreateDate'       => date('Y-m-d H:i:s'),
          'CreateBy'         => $this->session->userdata('user_id')
        ];
      } else {
        // UPDATE data lama
        $UpdateData[] = [
          'Id'               => $kodeTrans[$i], // <--- PK dari tabel Trans_LicenseAssignment
          'AssignedType'     => $TransType,
          'AssignedDeptID'   => $TransDept[$i],
          'AssignedDeptName' => $TransDeptText[$i],
          'AssignedID'       => $TransUserID[$i],
          'AssignedName'     => $TransUserText[$i],
          'Quantity'         => (int) $TransQty[$i],
          'Notes'            => ucwords($TransNotes[$i]),
          'UpdateDate'       => date('Y-m-d H:i:s'),
          'UpdateBy'         => $this->session->userdata('user_id')
        ];
      }
    }

    //echo json_encode(array("status" => "error", "Insert" => $InsertData, "Update" => $UpdateData)); exit;

    $this->BJGMAS01->trans_begin(); // mulai transaksi biar aman
    // Simpan insert batch
    if (!empty($InsertData)) {
      $this->BJGMAS01->insert_batch('Trans_LicenseAssignment', $InsertData);
    }

    // Simpan update batch
    if (!empty($UpdateData)) {
      $this->BJGMAS01->update_batch('Trans_LicenseAssignment', $UpdateData, 'Id');
    }

    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Gagal menyimpan data.'
      ]);
    } else {
      $this->BJGMAS01->trans_commit();
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data berhasil disimpan.'
      ]);
    }
    exit;
  }

  //SAVE TRANSAKSI DEVICE
  function transaksi_device_add() 
  {
    $this->_validation_transaksi_device();

    // Ambil input dari form
    $TransID            = $this->input->post('TransDeviceID');
    $TransVendorID      = $this->input->post('TransDeviceVendorID');
    $TransLicenseName   = $this->input->post('TransDeviceLicenseName');
    $TransLicenseType   = $this->input->post('TransDeviceLicenseType');
    $TransSeatsAllowed  = (int) $this->input->post('TransDeviceSeatsAllowed');
    $TransStatus        = $this->input->post('TransDeviceStatus');
    $TransType          = $this->input->post('TransDeviceType');

    // Data array (multi item)
    $TransQty           = $this->input->post('TransDeviceQty');
    $kodeTrans          = $this->input->post('kodeTrans');
    $TransDept          = $this->input->post('TransDeviceDept');
    $TransDeptText      = $this->input->post('TransDeptText');
    $TransMesin         = $this->input->post('TransDeviceMesin');
    //$TransUserText      = $this->input->post('TransUserText');
    $TransNotes         = $this->input->post('TransDeviceNotes');

    // Hitung total qty
    $totalQty = array_sum($TransQty);
    if ($totalQty > $TransSeatsAllowed) {
      echo json_encode([
        'status_code'  => 500,
        'status'       => 'error',
        'message'      => 'Total Qty transaksi tidak boleh melebihi jumlah akun ('.$TransSeatsAllowed.')'
      ]);
      exit;
    }

    // Pisahkan batch insert & update
    $InsertData = [];
    $UpdateData = [];

    for ($i = 0; $i < count($TransQty); $i++) {
      if (empty($kodeTrans[$i])) {
        // INSERT baru
        $InsertData[] = [
          'LicenseID'        => $TransID,
          'AssignedType'     => $TransType,
          'AssignedDeptID'   => $TransDept[$i],
          'AssignedDeptName' => $TransDeptText[$i],
          //'AssignedID'       => $TransUserID[$i],
          'AssignedName'     => $TransMesin[$i],
          'Quantity'         => (int) $TransQty[$i],
          'Notes'            => ucwords($TransNotes[$i]),
          'CreateDate'       => date('Y-m-d H:i:s'),
          'CreateBy'         => $this->session->userdata('user_id')
        ];
      } else {
        // UPDATE data lama
        $UpdateData[] = [
          'Id'               => $kodeTrans[$i],
          'AssignedType'     => $TransType,
          'AssignedDeptID'   => $TransDept[$i],
          'AssignedDeptName' => $TransDeptText[$i],
          //'AssignedID'       => $TransUserID[$i],
          'AssignedName'     => $TransMesin[$i],
          'Quantity'         => (int) $TransQty[$i],
          'Notes'            => ucwords($TransNotes[$i]),
          'UpdateDate'       => date('Y-m-d H:i:s'),
          'UpdateBy'         => $this->session->userdata('user_id')
        ];
      }
    }

    //echo json_encode(array("status" => "error", "Insert" => $InsertData, "Update" => $UpdateData)); exit;

    $this->BJGMAS01->trans_begin(); // mulai transaksi biar aman
    // Simpan insert batch
    if (!empty($InsertData)) {
      $this->BJGMAS01->insert_batch('Trans_LicenseAssignment', $InsertData);
    }

    // Simpan update batch
    if (!empty($UpdateData)) {
      $this->BJGMAS01->update_batch('Trans_LicenseAssignment', $UpdateData, 'Id');
    }

    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      echo json_encode([
        'status_code' => 500,
        'status'      => 'error',
        'message'     => 'Gagal menyimpan data.'
      ]);
    } else {
      $this->BJGMAS01->trans_commit();
      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data berhasil disimpan.'
      ]);
    }
    exit;
  }

  function transaksi_add_OLD() 
  {
    $this->_validation_transaksi();

    // Ambil input dari form
    $TransID            = $this->input->post('TransID');
    $TransVendorID      = $this->input->post('TransVendorID');
    $TransLicenseName   = $this->input->post('TransLicenseName');
    $TransLicenseType   = $this->input->post('TransLicenseType');
    $TransSeatsAllowed  = (int) $this->input->post('TransSeatsAllowed');
    $TransStatus        = $this->input->post('TransStatus');
    $TransType          = $this->input->post('TransType');

    // Data array (multi item)
    $TransQty           = $this->input->post('TransQty');
    $TransDept          = $this->input->post('TransDept');
    $TransDeptText      = $this->input->post('TransDeptText');
    $TransUserID        = $this->input->post('TransUserID');
    $TransUserText      = $this->input->post('TransUserText');
    $TransNotes         = $this->input->post('TransNotes');

    // Hitung total qty
    $totalQty = array_sum($TransQty);
    if ($totalQty > $TransSeatsAllowed) {
      echo json_encode([
        'status_code'  => 500,
        'status'       => 'error',
        'message'      => 'Total Qty transaksi boleh melebihi jumlah akun ('.$TransSeatsAllowed.')'
      ]);
      exit;
    }

    // Buat batch insert
    $BatchData = [];
    for ($i = 0; $i < count($TransQty); $i++) {
      $BatchData[] = [
        'LicenseID'        => $TransID,
        'AssignedType'     => $TransType,
        'AssignedDeptID'   => $TransDept[$i],
        'AssignedDeptName' => $TransDeptText[$i],
        'AssignedID'       => $TransUserID[$i],
        'AssignedName'     => $TransUserText[$i],
        'Quantity'         => (int) $TransQty[$i],
        'Notes'            => ucwords($TransNotes[$i]),
        'CreateDate'       => date('Y-m-d H:i:s'),
        'CreateBy'         => $this->session->userdata('user_id')
      ];
    }

    //echo json_encode(array("status" => "error", "Data" => $BatchData)); exit;

    $Save = $this->BJGMAS01->insert_batch('Trans_LicenseAssignment', $BatchData);
    if ($Save) {
      echo json_encode([
        'status_code'  => 200,
        'status'       => 'success',
        'message'      => 'Data berhasil disimpan.'
      ]);
    } else {
      echo json_encode([
        'status_code'  => 500,
        'status'       => 'error',
        'message'      => 'Gagal menyimpan data.'
      ]);
    }
    exit;
  }

  //CEK TRANSAKSI
  function transaksi_cek()
  {
    $Id    = $this->input->post('IdLisensi');
    $Type  = $this->input->post('Type');
    $Query = $this->BJGMAS01->order_by('CreateDate', 'ASC')->get_where('Trans_LicenseAssignment', array('LicenseID' => $Id, 'AssignedType' => $Type));
    if ($Query->num_rows() > 0) {
      $Result = $Query->result();
      echo json_encode([
        'status_code'  => 200,
        'status'       => 'success',
        'message'      => 'Data sukses ditampilkan.',
        'data'         => $Result
      ]);
    } else {
      echo json_encode([
        'status_code'  => 404,
        'status'       => 'error',
        'message'      => 'Data gagal ditampilkan.',
        'data'         => array()
      ]);
    }
  }

  //HAPUS SINGLE TRANSAKSI
  function transaksi_delete_row()
  {
    $Id     = $this->input->post('IdDetail');
    $Delete = $this->BJGMAS01->delete('Trans_LicenseAssignment', array('Id' => $Id));
    if ($Delete) {
      echo json_encode([
        'status_code'  => 200,
        'status'       => 'success',
        'message'      => 'Data sukses dihapus.',
      ]);
    } else {
      echo json_encode([
        'status_code'  => 500,
        'status'       => 'error',
        'message'      => 'Data gagal dihapus.',
      ]);
    }
  }

  private function _validation_lisensi()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('LicenseName') == '') {
      $data['inputerror'][]   = 'LicenseName';
      $data['error_string'][] = 'Nama Lisensi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('LicenseType') == '') {
      $data['inputerror'][]   = 'LicenseType';
      $data['error_string'][] = 'Lisensi Type is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('LicenseKey') == '') {
      $data['inputerror'][]   = 'LicenseKey';
      $data['error_string'][] = 'Lisensi Key is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('VendorList') == '') {
      $data['inputerror'][]   = 'VendorList';
      $data['error_string'][] = 'Vendor is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('PurchaseDate') == '') {
      $data['inputerror'][]   = 'PurchaseDate';
      $data['error_string'][] = 'Tanggal Pembelian is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ExpiryDate') == '') {
      $data['inputerror'][]   = 'ExpiryDate';
      $data['error_string'][] = 'Tanggal Expired is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('SeatsAllowed') == '') {
      $data['inputerror'][]   = 'SeatsAllowed';
      $data['error_string'][] = 'Jumlah Akun is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Status') == '') {
      $data['inputerror'][]   = 'Status';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_transaksi_user()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('TransType') == '') {
      $data['inputerror'][]   = 'TransType';
      $data['error_string'][] = 'Type is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    $transQty     = $this->input->post('TransQty');
    $transDept    = $this->input->post('TransDept');
    $transUserID  = $this->input->post('TransUserID');
    $transNotes   = $this->input->post('TransNotes');

    if (is_array($transQty)) {
      foreach ($transQty as $i => $qty) {
        if (empty($qty)) {
          $data['inputerror'][]   = "TransQty[$i]";
          $data['error_string'][] = 'Quantity is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transDept)) {
      foreach ($transDept as $i => $dept) {
        if ($dept === '' || $dept === null) {
          $data['inputerror'][]   = "TransDept[$i]";
          $data['error_string'][] = 'Departemen is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transUserID)) {
      foreach ($transUserID as $i => $user) {
        if ($user === '' || $user === null) {
          $data['inputerror'][]   = "TransUserID[$i]";
          $data['error_string'][] = 'User is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transNotes)) {
      foreach ($transNotes as $i => $note) {
        if ($note === '' || $note === null) {
          $data['inputerror'][]   = "TransNotes[$i]";
          $data['error_string'][] = 'Keterangan is required';
          $data['status']         = FALSE;
        }
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_transaksi_device()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('TransDeviceType') == '') {
      $data['inputerror'][]   = 'TransDeviceType';
      $data['error_string'][] = 'Type is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam deviceContainer
    $transQty     = $this->input->post('TransDeviceQty');
    $transDept    = $this->input->post('TransDeviceDept');
    $transMesin   = $this->input->post('TransDeviceMesin');
    $transNotes   = $this->input->post('TransDeviceNotes');

    if (is_array($transQty)) {
      foreach ($transQty as $i => $qty) {
        if ($qty === '' || $qty === null) {
          $data['inputerror'][]   = "TransDeviceQty[$i]";
          $data['error_string'][] = 'Qty required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transDept)) {
      foreach ($transDept as $i => $dept) {
        if ($dept === '' || $dept === null) {
          $data['inputerror'][]   = "TransDeviceDept[$i]";
          $data['error_string'][] = 'Departemen is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transMesin)) {
      foreach ($transMesin as $i => $mesin) {
        if ($mesin === '' || $mesin === null) {
          $data['inputerror'][]   = "TransDeviceMesin[$i]";
          $data['error_string'][] = 'Mesin is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($transNotes)) {
      foreach ($transNotes as $i => $note) {
        if ($note === '' || $note === null) {
          $data['inputerror'][]   = "TransDeviceNotes[$i]";
          $data['error_string'][] = 'Keterangan is required';
          $data['status']         = FALSE;
        }
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}