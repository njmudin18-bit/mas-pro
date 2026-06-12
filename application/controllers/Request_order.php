<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_order extends CI_Controller
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
    $this->load->model('unitid_model', 'unit');

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Purchasing";
      $data['nama_halaman']     = "Permintaan Pembelian";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['unit']             = $this->unit->get_all_data();
      $data['department_att'] 	= get_department_att();
      $data['DeptList'] 	      = get_department_for_purchasing();
      $data['DEPTID']           = $this->session->userdata('user_dept_id');
      $data['DEPTNAME']         = $this->session->userdata('user_dept_name');

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/purchasing/request_order/index', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function request_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_request();

      // --- 1. AMBIL & OLAH DATA FORM ---
      $DeptID             = $this->input->post('DeptID');
      $DeptName           = $this->input->post('DeptName');
      $EmployeeID         = $this->input->post('EmployeeID');
      $EmployeeName       = $this->input->post('EmployeeName');
      $Prioritas          = $this->input->post('Prioritas');
      $TanggalPermintaan  = $this->input->post('TanggalPermintaan');
      $AlasanPermintaan   = $this->input->post('AlasanPermintaan');
      $ReqNumber          = $this->generateRequestNumber();

      // --- 2. AMBIL DATA DETAIL BARANG ---
      $ArrNamaBarang      = $this->input->post('NamaBarang');
      $ArrQuantity        = $this->input->post('Quantity');
      $ArrUnitID          = $this->input->post('UnitID');
      $ArrHarga           = $this->input->post('Harga');
      $ArrLink            = $this->input->post('Link');

      // Hitung TotalCost dari semua Harga
      $TotalCost      = 0;
      if (is_array($ArrHarga)) {
        foreach ($ArrHarga as $h) {
          $TotalCost += floatval(str_replace(',', '.', str_replace('.', '', $h)));
        }
      }

      // Array Data Header
      $DataHD = array(
        'Nomor'           => $ReqNumber,
        'DeptID'          => $DeptID,
        'DeptName'        => $DeptName,
        'EmployeeID'      => $EmployeeID,
        'EmployeeName'    => $EmployeeName,
        'RequestDate'     => $TanggalPermintaan,
        'Status'          => 'D', //Draft, Pending, Approved, Rejected, Completed, Cancelled
        'Priority'        => $Prioritas,
        'TotalCost'       => $TotalCost,
        'Noted'           => ucfirst($AlasanPermintaan),
        'CreatedDate'     => date('Y-m-d H:i:s'),
        'CreatedBy'       => $this->session->userdata('user_code')
      );

      //echo json_encode(array("status" => "error", "data" => $DataHD));
      //exit();

      // Susun array detail
      $DataDT = array();
      if (is_array($ArrNamaBarang)) {
        foreach ($ArrNamaBarang as $key => $val) {
          $DataDT[] = array(
            'Nomor'    => $ReqNumber,
            'ItemName' => ucwords($val),
            'Quantity' => floatval(str_replace(',', '.', str_replace('.', '', $ArrQuantity[$key] ?? 0))),
            'UnitID'   => $ArrUnitID[$key] ?? null,
            'Prices'   => floatval(str_replace(',', '.', str_replace('.', '', $ArrHarga[$key] ?? 0))),
            'Link'     => $ArrLink[$key] ?? null,
          );
        }
      }

      //echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT));
      //exit();

      // --- 3. MULAI TRANSAKSI DATABASE ---
      $this->BJGMAS01->trans_start();
      // A. SIMPAN HEADER
      $this->BJGMAS01->insert('Trans_RequestOrderHD', $DataHD);
      // B. SIMPAN DETAIL (semua baris barang)
      if (!empty($DataDT)) {
        $this->BJGMAS01->insert_batch('Trans_RequestOrderDT', $DataDT);
      }
      // SELESAI TRANSAKSI
      $this->BJGMAS01->trans_complete();

      // --- 4. RESPONSE AKHIR ---
      if ($this->BJGMAS01->trans_status() === FALSE) {
        echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal menyimpan data."));
      } else {
        echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Sukses menyimpan data."));
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
    exit;
  }
  public function request_approved()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $Nomor      = $this->input->post('Nomor');
      $Status     = $this->input->post('Status');
      
      $FirstData = array(
        'Status'                  => $Status,
        'PurchasingApprovedDate'  => date('Y-m-d H:i:s'),
        'PurchasingApprovedBy'    => $this->session->userdata('user_nip')
      );

      //echo json_encode(array("status" => "error", "data" => $FirstData)); exit;

      // Simpan ke database
      $Update = $this->BJGMAS01->update('Trans_RequestOrderHD', $FirstData, array('Nomor' => $Nomor));
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
  public function request_list()
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

    $Sql            = "EXEC dbo.GetRequestOrder @StartDate = ?, @EndDate = ?, @DeptIDs = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $DeptID]);
		$Result 		    = $Query->result();
		$Data 			    = [];

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Nomor."'";

      $row    = [];
      $row[]  = $value->NoUrut;
      $row[]  = $value->NoUrut != "" ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="#" onclick="updateStatus('.$Isi.')">Approved</a>
                    </div>
                  </div>
                </div>' : '';
      $row[]  = $value->Nomor;
      $row[]  = $value->Status;
      $row[]  = $value->DeptName;
      $row[]  = $value->EmployeeID;
      $row[]  = $value->EmployeeName;
      $row[]  = $value->RequestDate;
      $row[]  = $value->Priority;
      $row[]  = $value->ItemName;
      $row[]  = $value->Quantity;
      $row[]  = $value->UnitID;
      $row[]  = $value->Prices;
      $row[]  = $value->SubTotal;
      $link   = $value->Link;
      if ($link) {
        $short_link = strlen($link) > 10 ? substr($link, 0, 10) . "..." : $link;
        $row[] = '<span title="' . $link . '">' . $short_link . '</span> 
                  <button type="button" class="btn btn-info btn-sm" onclick="copyToClipboard(\'' . $link . '\')" title="Copy Link">
                    <i class="fa fa-copy"></i>
                  </button>';
      } else {
        $row[] = '-';
      }
      $row[]  = $value->Noted;
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;
  
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

  public function request_edit($nomor) // Parameter diubah menjadi $nomor
  {
    // 1. Ambil data Header
    $Header = $this->BJGMAS01->get_where('Trans_RequestOrderHD', ['Nomor' => $nomor])->row();

    if ($Header) {
      // 2. Ambil data Detail berdasarkan Nomor
      //$Detail = $this->BJGMAS01->get_where('Trans_RequestOrderDT', ['Nomor' => $nomor])->result();
      
      $this->BJGMAS01->select("Id, Nomor, ItemName, 
          FORMAT(Quantity, 'N0', 'id-ID') AS Quantity,
          FORMAT(Prices, 'N0', 'id-ID') AS Prices,
          UnitID, Link", FALSE);
      $this->BJGMAS01->from('Trans_RequestOrderDT');
      $this->BJGMAS01->where('Nomor', $nomor);
      $this->BJGMAS01->order_by('Id', 'ASC');
      $Detail = $this->BJGMAS01->get()->result();

      // Gabungkan data header dan detail ke dalam satu variabel
      $Data = [
        'status'      => 'success',
        'status_code' => 200,
        'message'     => 'Data ditemukan.',
        'header'      => $Header,
        'detail'      => $Detail
      ];

      // 3. LOGGING
      $controller_name = get_class($this);
      $function_name   = $this->router->fetch_method();
      
      $log_url  = base_url() . $controller_name . "/" . $function_name;
      $log_type = "EDIT (VIEW DATA)"; // Log bahwa user sedang membuka form edit
      $log_data = json_encode($Data);

      log_helper($log_url, $log_type, $log_data);

      // 4. Output JSON
      echo json_encode($Data);
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    }
  }

  public function request_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $this->_validation_request();

    // --- 1. AMBIL & OLAH DATA FORM ---
    $Nomor              = $this->input->post('Nomor');
    $DeptID             = $this->input->post('DeptID');
    $EmployeeID         = $this->input->post('EmployeeID');
    $Prioritas          = $this->input->post('Prioritas');
    $TanggalPermintaan  = $this->input->post('TanggalPermintaan');
    $AlasanPermintaan   = $this->input->post('AlasanPermintaan');

    // --- 2. AMBIL DATA DETAIL BARANG ---
    $ArrIdDetail        = $this->input->post('IdDetail');
    $ArrNamaBarang      = $this->input->post('NamaBarang');
    $ArrQuantity        = $this->input->post('Quantity');
    $ArrUnitID          = $this->input->post('UnitID');
    $ArrHarga           = $this->input->post('Harga');
    $ArrLink            = $this->input->post('Link');

    // Hitung TotalCost dari semua Harga
    $TotalCost = 0;
    if (is_array($ArrHarga)) {
      foreach ($ArrHarga as $h) {
        $TotalCost += floatval(str_replace(',', '.', str_replace('.', '', $h)));
      }
    }

    // --- 3. SIAPKAN DATA HEADER ---
    $DataHD = array(
      'DeptID'        => $DeptID,
      'EmployeeID'    => $EmployeeID,
      'RequestDate'   => $TanggalPermintaan,
      'Priority'      => $Prioritas,
      'TotalCost'     => $TotalCost,
      'Noted'         => ucfirst($AlasanPermintaan),
      'UpdatedDate'   => date('Y-m-d H:i:s'),
      'UpdatedBy'     => $this->session->userdata('user_code')
    );

    // --- 4. MULAI TRANSAKSI DATABASE ---
    $this->BJGMAS01->trans_begin();

    // A. UPDATE HEADER
    $this->BJGMAS01->where('Nomor', $Nomor);
    $this->BJGMAS01->update('Trans_RequestOrderHD', $DataHD);

    // B. UPDATE DETAIL (per baris berdasarkan Id)
    if (is_array($ArrNamaBarang)) {
      foreach ($ArrNamaBarang as $key => $val) {
        $DetailId = isset($ArrIdDetail[$key]) ? $ArrIdDetail[$key] : null;

        $DataDT = array(
          'ItemName' => ucwords($val),
          'Quantity' => floatval(str_replace(',', '.', str_replace('.', '', $ArrQuantity[$key] ?? 0))),
          'UnitID'   => $ArrUnitID[$key] ?? null,
          'Prices'   => floatval(str_replace(',', '.', str_replace('.', '', $ArrHarga[$key] ?? 0))),
          'Link'     => $ArrLink[$key] ?? null,
        );

        if ($DetailId) {
          // Update baris detail yang sudah ada
          $this->BJGMAS01->where('Id', $DetailId);
          $this->BJGMAS01->update('Trans_RequestOrderDT', $DataDT);
        } else {
          // Insert baris detail baru (jika user menambah baris baru saat edit)
          $DataDT['Nomor'] = $Nomor;
          $this->BJGMAS01->insert('Trans_RequestOrderDT', $DataDT);
        }
      }
    }

    // --- 5. COMMIT ATAU ROLLBACK ---
    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal memperbarui data."));
    } else {
      $this->BJGMAS01->trans_commit();

      // LOGGING
      $controller_name = get_class($this);
      $function_name   = $this->router->fetch_method();

      $log_url  = base_url() . $controller_name . "/" . $function_name;
      $log_type = "UPDATE";
      $log_data = json_encode(array("nomor" => $Nomor, "header" => $DataHD));

      log_helper($log_url, $log_type, $log_data);

      echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Data berhasil diperbarui."));
    }

    exit;
  }

  public function request_deleted($nomor)
  {
    // 1. Ambil data sebelum dihapus untuk keperluan Log
    $data_delete = $this->BJGMAS01->get_where('Trans_RequestOrderHD', ['Nomor' => $nomor])->row();

    if (!$data_delete) {
      echo json_encode(array("status" => "error", "message" => "Data tidak ditemukan"));
      
      return;
    }

    // 2. Mulai Database Transaction
    $this->BJGMAS01->trans_begin();

    // Hapus Detail dulu baru Header
    $this->BJGMAS01->where('Nomor', $nomor);
    $this->BJGMAS01->delete('Trans_RequestOrderDT');

    $this->BJGMAS01->where('Nomor', $nomor);
    $this->BJGMAS01->delete('Trans_RequestOrderHD');

    if ($this->BJGMAS01->trans_status() === FALSE) {
        $this->BJGMAS01->trans_rollback();
        echo json_encode(array("status" => "error", "message" => "Gagal menghapus data"));
    } else {
        $this->BJGMAS01->trans_commit();

        // 3. ADDING TO LOG
        // Gunakan get_class($this) untuk mendapatkan nama controller secara dinamis
        $controller_name = get_class($this); 
        $function_name   = $this->router->fetch_method();
        
        $log_url  = base_url() . $controller_name . "/" . $function_name;
        $log_type = "DELETE";
        $log_data = json_encode(array(
            "nomor"  => $nomor,
            "header" => $data_delete
        ));

        log_helper($log_url, $log_type, $log_data);

        // 4. Echo diletakkan di paling bawah setelah semua proses selesai
        echo json_encode(array("status" => "ok"));
    }
  }

  public function generateRequestNumber()
  {
    // Ambil tahun-bulan sekarang
    $yearMonth = date('Ym');
    // Prefix tetap
    $prefix    = "RQO" . $yearMonth . "-";
    // Ambil nomor terakhir yang sesuai prefix
    $this->BJGMAS01->select('Nomor');
    $this->BJGMAS01->like('Nomor', $prefix, 'after');
    $this->BJGMAS01->order_by('Nomor', 'DESC');
    $this->BJGMAS01->limit(1);
    $query = $this->BJGMAS01->get('Trans_RequestOrderHD');

    $lastNumber = '';
    if ($query->num_rows() > 0) {
      $lastNumber = $query->row()->Nomor;
    }

    $sequence = 1;
    if (!empty($lastNumber)) {
      $parts = explode('-', $lastNumber);
      if (count($parts) > 1) {
        $lastSequence = (int)$parts[1];
        $sequence     = $lastSequence + 1;
      }
    }

    $newSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    return $prefix . $newSequence;
  }

  private function _validation_request()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Departemen wajib dipilih';
      $data['status']         = FALSE;
    }

    if ($this->input->post('EmployeeID') == '') {
      $data['inputerror'][]   = 'EmployeeID';
      $data['error_string'][] = 'Pegawai wajib dipilih';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Prioritas') == '') {
      $data['inputerror'][]   = 'Prioritas';
      $data['error_string'][] = 'Prioritas wajib dipilih';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TanggalPermintaan') == '') {
      $data['inputerror'][]   = 'TanggalPermintaan';
      $data['error_string'][] = 'Tanggal Permintaan wajib diisi';
      $data['status']         = FALSE;
    }

    if ($this->input->post('AlasanPermintaan') == '') {
      $data['inputerror'][]   = 'AlasanPermintaan';
      $data['error_string'][] = 'Alasan Permintaan wajib diisi';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    $namaBarang  = $this->input->post('NamaBarang');
    $quantity    = $this->input->post('Quantity');
    $unitID      = $this->input->post('UnitID');
    $harga       = $this->input->post('Harga');

    if (is_array($namaBarang)) {
      foreach ($namaBarang as $i => $nama) {
        if (empty($nama)) {
          $data['inputerror'][]   = "NamaBarang[$i]";
          $data['error_string'][] = 'Nama Barang is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($quantity)) {
      foreach ($quantity as $i => $qty) {
        //if (empty($qty)) {
        if ($qty === '' || $qty === null) {
          $data['inputerror'][]   = "Quantity[$i]";
          $data['error_string'][] = 'Quantity is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($unitID)) {
      foreach ($unitID as $i => $unit) {
        //if (empty($qty)) {
        if ($unit === '' || $unit === null) {
          $data['inputerror'][]   = "UnitID[$i]";
          $data['error_string'][] = 'UnitID is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($harga)) {
      foreach ($harga as $i => $price) {
        //if (empty($qty)) {
        if ($price === '' || $price === null) {
          $data['inputerror'][]   = "Harga[$i]";
          $data['error_string'][] = 'Harga is required';
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
