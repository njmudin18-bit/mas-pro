<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengeluaran_mobil extends CI_Controller
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

    $this->load->helper(array('url', 'form', 'cookie', 'bbm', 'mobil'));
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
    $this->load->model('supir_model', 'supir');

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Warehouse";
      $data['nama_halaman']     = "Rincian Pengeluaran Mobil";
      $data['icon_halaman']     = "icon-layers";
      //$data['HargaSolar']       = get_harga_bbm_pertamina('Prov. Banten', 'bio-solar');
      $data['MobilList'] 		    = get_daftar_mobil();
      $data['SupirList'] 		    = $this->supir->get_nama_supir();
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/driver/rincian_pengeluaran_mobil', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function pengeluaran_mobil_add()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_pengeluaran_mobil();

      // --- 1. AMBIL & OLAH DATA FORM ---
      $Supir             = $this->input->post('Supir');
      $NamaSupir         = $this->input->post('NamaSupir');
      $ReqNumber         = $this->generateRequestNumber();
      
      // Memecah Mobil ID dan Kilometer (Format: "PLATNOMOR|KM")
      $RawMobil          = $this->input->post('Mobil');
      $MobilArr          = explode('|', $RawMobil);
      $Mobil             = isset($MobilArr[0]) ? $MobilArr[0] : '';
      $Kilometer         = isset($MobilArr[1]) ? floatval($MobilArr[1]) : 0;
      $TanggalAwalKirim  = $this->input->post('TanggalAwalKirim');
      $TanggalAkhirKirim = $this->input->post('TanggalAkhirKirim');
      $Noted             = ucfirst($this->input->post('Noted'));
      // Parsing angka (hapus titik ribuan)
      $HargaSolar        = floatval(str_replace('.', '', $this->input->post('HargaSolar')));
      $KMAwal            = floatval(str_replace('.', '', $this->input->post('KMAwal')));
      $KMAkhir           = floatval(str_replace('.', '', $this->input->post('KMAkhir')));
      $IsiSolar          = floatval(str_replace('.', '', $this->input->post('IsiSolar')));
      // Mencegah Division By Zero
      if ($HargaSolar > 0) {
        $TotalLiter    = round(($IsiSolar / $HargaSolar), 2);
      } else {
        $TotalLiter    = 0;
      }
      // Hitung estimasi (Jika $Kilometer dari mobil tidak ada, estimasi 0)
      $EstimasiPerLiter   = round(($TotalLiter * $Kilometer), 2);
      $EstimasiKMAkhir    = round(($EstimasiPerLiter + $KMAwal), 2);
      $ListPilihan        = $this->input->post('PilihCustomer'); // Untuk DT1
      $NamaFileEtoll      = ''; 
      $NamaFileBbm        = ''; 

      // Load library upload satu kali
      $this->load->library('upload');
      // --- 2. PROSES UPLOAD FILE ---
      // Upload Struk E-Toll
      if (!empty($_FILES['FilesEtoll']['name'])) {
        $configEtoll['upload_path']   = './files/uploads/struk_solar/'; 
        $configEtoll['allowed_types'] = 'jpg|jpeg|png|pdf';   
        $configEtoll['max_size']      = 2048; 
        $configEtoll['file_name']     = 'ETOLL-' . date('Ymd') . '-' . md5(uniqid(rand(), true));

        $this->upload->initialize($configEtoll);

        if ($this->upload->do_upload('FilesEtoll')) {
          $upload_data   = $this->upload->data();
          $NamaFileEtoll = $upload_data['file_name'];
        } else {
          $data['inputerror'][]   = 'FilesEtoll';
          $data['error_string'][] = 'Struk E-Toll: ' . $this->upload->display_errors('', '');
          $data['status']         = FALSE;
          echo json_encode($data); exit();
        }
      }

      // Upload Struk BBM
      if (!empty($_FILES['FilesBbm']['name'])) {
        $configBbm['upload_path']   = './files/uploads/struk_solar/'; 
        $configBbm['allowed_types'] = 'jpg|jpeg|png|pdf';   
        $configBbm['max_size']      = 2048; 
        $configBbm['file_name']     = 'BBM-' . date('Ymd') . '-' . md5(uniqid(rand(), true));

        $this->upload->initialize($configBbm);

        if ($this->upload->do_upload('FilesBbm')) {
          $upload_data   = $this->upload->data();
          $NamaFileBbm   = $upload_data['file_name'];
        } else {
          $data['inputerror'][]   = 'FilesBbm';
          $data['error_string'][] = 'Struk BBM: ' . $this->upload->display_errors('', '');
          $data['status']         = FALSE;
          echo json_encode($data); exit();
        }
      }

      // Array Data Header
      $DataHD = array(
        'Nomor'                  => $ReqNumber,
        'Status'                 => 'C',
        'EmployeeID'             => $Supir,
        'EmployeeName'           => $NamaSupir,
        'MobilID'                => $Mobil,
        'TanggalAwalKirim'       => $TanggalAwalKirim,
        'TanggalAkhirKirim'      => $TanggalAkhirKirim,
        'StrukToll'              => $NamaFileEtoll,
        'StrukBbm'               => $NamaFileBbm,
        'KilometerAwal'          => $KMAwal,
        'KilometerAkhir'         => $KMAkhir,
        'HargaSolar'             => $HargaSolar,
        'TotalIsiSolar'          => $IsiSolar,
        'TotalLiter'             => $TotalLiter,
        'EstimasiPerLiter'       => $EstimasiPerLiter,
        'EstimasiKilometerAkhir' => $EstimasiKMAkhir,
        'Noted'                  => $Noted,
        'CreatedDate'            => date('Y-m-d H:i:s'),
        'CreatedBy'              => $this->session->userdata('user_code')
      );

      // echo json_encode(array("status" => "error", "data" => $DataHD));
      // exit();

      // --- 3. MULAI TRANSAKSI DATABASE ---
      $this->BJGMAS01->trans_start();

      // A. SIMPAN HEADER (Wajib untuk dapat ID)
      $this->BJGMAS01->insert('Trans_PengeluaranMobilHD', $DataHD);

      // B. SIMPAN DETAIL 1 (Trans_PengeluaranMobilDT)
      if (!empty($ListPilihan)) {
        $ArrNamaPenerima  = $this->input->post('NamaPenerima');
        $ArrBerapaXKirim  = $this->input->post('BerapaXKirim');
        $ArrListNoBukti   = $this->input->post('ListNoBukti');
        $ArrTotalDO       = $this->input->post('TotalDO');
        $ArrShipmentID    = $this->input->post('ShipmentID');
        $ArrTglKirim      = $this->input->post('TanggalKirimCustomer');
        $DataDT           = array();

        foreach ($ArrNamaPenerima as $key => $val) {
          // Filter hanya yang dicentang user
          if (in_array($val, $ListPilihan)) {
            $DataDT[] = array(
              'Nomor'        => $ReqNumber,
              'ShipmentID'   => $ArrShipmentID[$key],
              'CustomerName' => $ArrNamaPenerima[$key],
              'ListNoBukti'  => $ArrListNoBukti[$key],
              'TotalDO'      => floatval($ArrTotalDO[$key]),
              'BerapaXKirim' => floatval($ArrBerapaXKirim[$key]),
              'TanggalKirim' => $ArrTglKirim[$key],
              'CreatedDate'  => date('Y-m-d H:i:s'),
              'CreatedBy'    => $this->session->userdata('user_code')
            );
          }
        }

        // echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT));
        // exit();

        if (!empty($DataDT)) {
          $this->BJGMAS01->insert_batch('Trans_PengeluaranMobilDT', $DataDT);
        }
      }

      // C. SIMPAN DETAIL 2 (Trans_PengeluaranMobilDT2) - Field Tambahan
      $ArrNamaPenerimaMain = $this->input->post('NamaPenerimaMain');
      $ArrBerapaXKirimMain = $this->input->post('BerapaXKirimMain');
      $ArrTanggalKirimMain = $this->input->post('TanggalKirimMain');
      $DataDT2             = array();

      if (!empty($ArrNamaPenerimaMain)) {
        foreach ($ArrNamaPenerimaMain as $key => $val) {
          // Validasi sederhana: simpan jika nama penerima tidak kosong
          if (!empty($val)) {
            $DataDT2[] = array(
              'Nomor'        => $ReqNumber,
              'CustomerName' => strtoupper($val),
              'BerapaXKirim' => isset($ArrBerapaXKirimMain[$key]) ? floatval($ArrBerapaXKirimMain[$key]) : 0,
              'TanggalKirim' => $ArrTanggalKirimMain[$key],
              'CreatedDate'  => date('Y-m-d H:i:s'),
              'CreatedBy'    => $this->session->userdata('user_code')
            );
          }
        }

        // echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT, "DT2" => $DataDT2));
        // exit();

        if (!empty($DataDT2)) {
          $this->BJGMAS01->insert_batch('Trans_PengeluaranMobilDT2', $DataDT2);
        }
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
  }

  public function pengeluaran_mobil_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
		$Supir 	        = $this->input->post('supir');

    $Sql            = "EXEC dbo.GetPengeluaranMobil @StartDate = ?, @EndDate = ?, @EmployeeID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $Supir]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi        = "'".$value->Nomor."'";
      $Selected   = ($value->GroupID != NULL) ? 'checked' : '';
      $GroupID    = ($value->GroupID != NULL) ? 'Y' : 'N';
      $IsiCB      = "'".$value->Nomor."', '".$GroupID."'";
      $EToll      = base_url()."files/uploads/struk_solar/".$value->StrukToll;
      $BBM        = base_url()."files/uploads/struk_solar/".$value->StrukBbm;
      $StatusWH   = $value->Status == 'DIBUAT' ? '<a class="dropdown-item" href="#" onclick="approved_by_wh_head('.$Isi.')">Approved By WH Head</a>' : '';
      $StatusFN   = $value->Status == 'DIKIRIM' ? '<a class="dropdown-item" href="#" onclick="approved_by_finance('.$Isi.')">Approved By Finance</a>' : '';
      $row        = [];
      //$row[]  = ($value->NomorUrut != NULL) ? '<input type="checkbox" name="GroupID[]" id="GroupID_'.$value->NomorUrut.'" onclick="SetGroupID('.$IsiCB.')" '.$Selected.'>' : '';
      //$row[]  = ($value->NomorUrut != NULL && $value->Status == 'DIBUAT') ? '<input type="checkbox" value="'.$value->Nomor.'" name="GroupID[]" id="GroupID_'.$value->NomorUrut.'">' : '';
      $row[]  = $value->NomorUrut;
      $row[]  = ($value->NomorUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapus('.$Isi.')">Hapus</a>
                      '.$StatusWH.'
                      '.$StatusFN.'
                    </div>
                  </div>
                </div>' : '';
      $row[] = $value->Status;
      $row[] = $value->Nomor;
      //$row[] = $value->GroupID;
      $row[] = $value->EmployeeName;
      $row[] = $value->MobilID;
      $row[] = $value->TanggalAwalKirim;
      $row[] = $value->TanggalAkhirKirim;
      $row[] = $value->TanggalKirim;
      $row[]  = ($value->StrukToll != NULL) ? '<a href="'.$EToll.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen E-Toll">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $row[]  = ($value->StrukBbm != NULL) ? '<a href="'.$BBM.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen BBM">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $row[] = $value->CustomerName;
      $row[] = $value->BerapaXKirim;
      $row[] = $value->KilometerAwal;
      $row[] = $value->KilometerAkhir;
      $row[] = $value->HargaSolar;
      $row[] = $value->TotalIsiSolar;
      $row[] = $value->TotalLiter;
      $row[] = $value->EstimasiPerLiter;
      $row[] = $value->EstimasiKilometerAkhir;
      $row[] = $value->ApprovedWHBy;
      $row[] = $value->ApprovedWHDate;
      $row[] = $value->ApprovedFinanceBy;
      $row[] = $value->ApprovedFinanceDate;
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
  
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

  public function pengeluaran_mobil_kirim_group()
  {
    // 1. Cek Permission
    $user_level = $this->session->userdata('user_level');
    if ($this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level)->num_rows() != 1) {
        echo json_encode(["status" => "forbidden"]);
        return;
    }

    // 2. Ambil Input
    // ReqNumber adalah array (karena dari input name="ReqNumber[]")
    $ReqNumbers = $this->input->post('ReqNumber'); 
    $GroupList  = $this->input->post('GroupList');

    // Validasi: Pastikan ada item yang dipilih
    if (empty($ReqNumbers)) {
      echo json_encode(["status_code" => 400, "message" => "Tidak ada data yang dipilih."]);

      return;
    }

    // 3. LOGIKA PENENTUAN GROUP ID
    $GroupID = '';

    if (empty($GroupList)) {
      $FirstItem = $ReqNumbers[0]; 
      $GroupID   = str_replace('WHDRV', 'GRPID', $FirstItem);
    } else {
      $GroupID = $GroupList;
    }

    $this->BJGMAS01->trans_start();
    $this->BJGMAS01->where_in('Nomor', $ReqNumbers);
    $this->BJGMAS01->update('Trans_PengeluaranMobilHD', [
      'Status'        => 'S',
      'GroupID'       => $GroupID,
      'TglKirimKeACC' => date('Y-m-d H:i:s'),
      'UpdatedBy'     => $this->session->userdata('user_code'),
      'UpdatedDate'   => date('Y-m-d H:i:s')
    ]);
    $this->BJGMAS01->trans_complete();

    if ($this->BJGMAS01->trans_status() === FALSE) {
      echo json_encode(["status_code" => 500, "message" => "Gagal update database."]);
    } else {
      echo json_encode([
        "status_code" => 200, 
        "message"     => "Data berhasil disimpan.",
        "GroupID"     => $GroupID
      ]);
    }
  }

  public function pengeluaran_mobil_deleted() 
  {
    // 1. CEK PERMISSION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
        $Nomor = $this->input->post('Nomor');

        if (empty($Nomor)) {
          echo json_encode(array("status" => "error", "message" => "Nomor kosong"));

          return;
        }

        // Ambil Header (Penting: Kita butuh nama file di kolom StrukToll & StrukBbm)
        $Header = $this->BJGMAS01->get_where('Trans_PengeluaranMobilHD', ['Nomor' => $Nomor])->row_array();
        if (!$Header) {
          echo json_encode(array("status" => "error", "message" => "Data tidak ditemukan"));

          return;
        }

        // Ambil Detail 1 & 2 untuk kelengkapan Log
        $Detail1 = $this->BJGMAS01->get_where('Trans_PengeluaranMobilDT', ['Nomor' => $Nomor])->result_array();
        $Detail2 = $this->BJGMAS01->get_where('Trans_PengeluaranMobilDT2', ['Nomor' => $Nomor])->result_array();

        // Siapkan array data log
        $LogData = array(
          'Header'  => $Header,
          'Detail1' => $Detail1,
          'Detail2' => $Detail2
        );

        $this->BJGMAS01->trans_begin(); 
        try {
            // Hapus Detail 2
            $this->BJGMAS01->delete('Trans_PengeluaranMobilDT2', ['Nomor' => $Nomor]);
            // Hapus Detail 1
            $this->BJGMAS01->delete('Trans_PengeluaranMobilDT', ['Nomor' => $Nomor]);
            // Hapus Header
            $this->BJGMAS01->delete('Trans_PengeluaranMobilHD', ['Nomor' => $Nomor]);
            
            if ($this->BJGMAS01->trans_status() === FALSE) {
                // GAGAL: Rollback DB
                $this->BJGMAS01->trans_rollback();
                echo json_encode(array("status" => "error", "message" => "Gagal menghapus database."));
            } else {
                // SUKSES: Commit DB
                $this->BJGMAS01->trans_commit();

                // Tentukan path folder (FCPATH adalah root folder CI)
                $UploadPath = FCPATH . 'files/uploads/struk_solar/';
                // 1. Hapus File Struk Toll
                if (!empty($Header['StrukToll'])) {
                  $FileToll = $UploadPath.$Header['StrukToll'];
                  if (file_exists($FileToll)) {
                    unlink($FileToll); // Hapus file
                  }
                }

                // 2. Hapus File Struk BBM
                if (!empty($Header['StrukBbm'])) {
                  $FileBbm = $UploadPath.$Header['StrukBbm'];
                  if (file_exists($FileBbm)) {
                    unlink($FileBbm); // Hapus file
                  }
                }
                // -------------------------------

                // --- SIMPAN LOG ---
                $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
                $log_type = "DELETE";
                $log_data = json_encode($LogData);

                if (function_exists('log_helper')) {
                  log_helper($log_url, $log_type, $log_data);
                }

                echo json_encode(array("status" => "ok"));
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array("status" => "error", "message" => "Terjadi kesalahan sistem."));
        }

    } else {
        echo json_encode(array("status" => "forbidden"));
    }
  }

  public function pengeluaran_mobil_edit()
  {
    // 1. CEK PERMISSION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() != 1) {
      echo json_encode(array("status" => "forbidden"));

      return;
    }

    // 2. VALIDASI INPUT
    $Nomor = $this->input->post('Nomor');
    if (empty($Nomor)) {
      $Nomor = $this->uri->segment(3);
    }

    if (empty($Nomor)) {
      echo json_encode(array("status" => "error", "message" => "Nomor tidak boleh kosong"));

      return;
    }

    // 3. AMBIL DATA DARI 3 TABEL
    // A. Ambil Header
    $Header = $this->BJGMAS01->get_where('Trans_PengeluaranMobilHD', ['Nomor' => $Nomor])->row_array();
    if (!$Header) {
      echo json_encode(array("status" => "error", "message" => "Data tidak ditemukan"));

      return;
    }

    $Header['MobilID_Value'] = get_mobil_value($Header['MobilID']);
    
    // B. Link Gambar
    $path_struk = base_url('files/uploads/struk_solar/');
    $Header['Link_StrukToll'] = !empty($Header['StrukToll']) ? $path_struk . $Header['StrukToll'] : null;
    $Header['Link_StrukBbm']  = !empty($Header['StrukBbm'])  ? $path_struk . $Header['StrukBbm']  : null;

    // C. Ambil Detail
    $Detail1 = $this->BJGMAS01->get_where('Trans_PengeluaranMobilDT', ['Nomor' => $Nomor])->result_array();
    $Detail2 = $this->BJGMAS01->get_where('Trans_PengeluaranMobilDT2', ['Nomor' => $Nomor])->result_array();

    // 4. KIRIM RESPONSE
    $Output = array(
      "status"  => "success",
      "header"  => $Header,
      "detail1" => $Detail1,
      "detail2" => $Detail2
    );

    echo json_encode($Output); 
  }

  public function hapus_customer_main() 
  {
    // 1. CEK PERMISSION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      // 2. TANGKAP INPUT
      $Id    = $this->input->post('IdDetail');
      $Nomor = $this->input->post('Nomor');

      if (empty($Id) || empty($Nomor)) {
        echo json_encode(array("status" => "error", "message" => "Parameter ID atau Nomor kosong"));

        return;
      }

      // 3. AMBIL DATA LAMA (UNTUK LOG)
      // Kita perlu data ini sebelum dihapus agar bisa dicatat di log apa yang dihapus
      $DataDeleted = $this->BJGMAS01->get_where('Trans_PengeluaranMobilDT2', [
        'Id'    => $Id, 
        'Nomor' => $Nomor
      ])->row_array();

      if (!$DataDeleted) {
        echo json_encode(array("status" => "error", "message" => "Data tidak ditemukan di database"));

        return;
      }

        // 4. MULAI TRANSAKSI
        $this->BJGMAS01->trans_begin();

        try {
          // Lakukan Penghapusan
          $this->BJGMAS01->where('Id', $Id);
          $this->BJGMAS01->where('Nomor', $Nomor);
          $this->BJGMAS01->delete('Trans_PengeluaranMobilDT2');

          // 5. CEK STATUS TRANSAKSI
          if ($this->BJGMAS01->trans_status() === FALSE) {
            // GAGAL: Rollback
            $this->BJGMAS01->trans_rollback();

            echo json_encode(array("status" => "error", "message" => "Gagal menghapus data."));
          } else {
            // SUKSES: Commit
            $this->BJGMAS01->trans_commit();

            // 6. SIMPAN LOG
            // URL Log
            $log_url  = base_url() . $this->contoller_name . "/" . $this->function_name;
            $log_type = "DELETE_DT2"; // Menandakan hapus detail customer MAI
            $log_data = json_encode($DataDeleted); // Simpan data yang dihapus sbg JSON

            // Panggil Helper (Pastikan helper sudah di-load)
            if (function_exists('log_helper')) {
              log_helper($log_url, $log_type, $log_data);
            }

            echo json_encode(array("status" => "ok"));
          }

        } catch (Exception $e) {
          $this->BJGMAS01->trans_rollback();

          echo json_encode(array("status" => "error", "message" => "Terjadi kesalahan sistem."));
        }

    } else {
        echo json_encode(array("status" => "forbidden"));
    }
  }

  public function pengeluaran_mobil_update()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $this->_validation_pengeluaran_mobil();

      // --- 0. ID UTAMA UNTUK UPDATE ---
      $KodeHeader = $this->input->post('KodeHeader'); 
      
      // Ambil Data Lama (PENTING: Untuk mendapatkan nama file lama)
      $OldData = $this->BJGMAS01->get_where('Trans_PengeluaranMobilHD', ['Id' => $KodeHeader])->row();

      if (!$OldData) {
        echo json_encode(array("status_code" => 404, "status" => "error", "message" => "Data tidak ditemukan."));
        return;
      }

      // --- 1. AMBIL & OLAH DATA FORM ---
      $Nomor             = $OldData->Nomor; 
      $Supir             = $this->input->post('Supir');
      $NamaSupir         = $this->input->post('NamaSupir');
      
      // Memecah Mobil ID dan Kilometer
      $RawMobil          = $this->input->post('Mobil');
      $MobilArr          = explode('|', $RawMobil);
      $Mobil             = isset($MobilArr[0]) ? $MobilArr[0] : '';
      $Kilometer         = isset($MobilArr[1]) ? floatval($MobilArr[1]) : 0;
      $TanggalAwalKirim  = $this->input->post('TanggalAwalKirim');
      $TanggalAkhirKirim = $this->input->post('TanggalAkhirKirim');
      $Noted             = ucfirst($this->input->post('Noted'));
      
      // Parsing angka
      $HargaSolar        = floatval(str_replace('.', '', $this->input->post('HargaSolar')));
      $KMAwal            = floatval(str_replace('.', '', $this->input->post('KMAwal')));
      $KMAkhir           = floatval(str_replace('.', '', $this->input->post('KMAkhir')));
      $IsiSolar          = floatval(str_replace('.', '', $this->input->post('IsiSolar')));
      
      // Hitung Ulang Rumus
      if ($HargaSolar > 0) {
        $TotalLiter    = round(($IsiSolar / $HargaSolar), 2);
      } else {
        $TotalLiter    = 0;
      }
      
      $EstimasiPerLiter   = round(($TotalLiter * $Kilometer), 2);
      $EstimasiKMAkhir    = round(($EstimasiPerLiter + $KMAwal), 2);
      
      $ListPilihan        = $this->input->post('PilihCustomer'); 

      // --- 2. PROSES UPLOAD FILE (DENGAN HAPUS FILE LAMA) ---
      $this->load->library('upload');
      $upload_path_folder = './files/uploads/struk_solar/'; // Variable path biar rapi

      // ================== A. Cek File Struk E-Toll ==================
      if (!empty($_FILES['FilesEtoll']['name'])) {
        $configEtoll['upload_path']   = $upload_path_folder; 
        $configEtoll['allowed_types'] = 'jpg|jpeg|png|pdf';   
        $configEtoll['max_size']      = 2048; 
        $configEtoll['file_name']     = 'ETOLL-' . date('Ymd') . '-' . md5(uniqid(rand(), true));

        $this->upload->initialize($configEtoll);

        if ($this->upload->do_upload('FilesEtoll')) {
          $upload_data   = $this->upload->data();
          $NamaFileEtoll = $upload_data['file_name'];

          // [BARU] LOGIKA HAPUS FILE LAMA
          // Cek apakah di database ada nama file lama DAN filenya ada di server
          if (!empty($OldData->StrukToll)) {
              $path_file_lama = $upload_path_folder . $OldData->StrukToll;
              if (file_exists($path_file_lama)) {
                  unlink($path_file_lama); // Hapus file lama
              }
          }

        } else {
          $data['inputerror'][]   = 'FilesEtoll';
          $data['error_string'][] = 'Struk E-Toll: ' . $this->upload->display_errors('', '');
          $data['status']         = FALSE;
          echo json_encode($data); exit();
        }
      } else {
        // Jika tidak upload, pakai nama file lama
        $NamaFileEtoll = $OldData->StrukToll;
      }

      // ================== B. Cek File Struk BBM ==================
      if (!empty($_FILES['FilesBbm']['name'])) {
        $configBbm['upload_path']   = $upload_path_folder; 
        $configBbm['allowed_types'] = 'jpg|jpeg|png|pdf';   
        $configBbm['max_size']      = 2048; 
        $configBbm['file_name']     = 'BBM-' . date('Ymd') . '-' . md5(uniqid(rand(), true));

        $this->upload->initialize($configBbm);

        if ($this->upload->do_upload('FilesBbm')) {
          $upload_data   = $this->upload->data();
          $NamaFileBbm   = $upload_data['file_name'];

          // [BARU] LOGIKA HAPUS FILE LAMA
          if (!empty($OldData->StrukBbm)) {
              $path_file_lama = $upload_path_folder . $OldData->StrukBbm;
              if (file_exists($path_file_lama)) {
                  unlink($path_file_lama); // Hapus file lama
              }
          }

        } else {
          $data['inputerror'][]   = 'FilesBbm';
          $data['error_string'][] = 'Struk BBM: ' . $this->upload->display_errors('', '');
          $data['status']         = FALSE;
          echo json_encode($data); exit();
        }
      } else {
        // Jika tidak upload, pakai nama file lama
        $NamaFileBbm = $OldData->StrukBbm;
      }

      // Array Data Header (Update)
      $DataHD = array(
        // 'Status'                 => 'C',
        'EmployeeID'             => $Supir,
        'EmployeeName'           => $NamaSupir,
        'MobilID'                => $Mobil,
        'TanggalAwalKirim'       => $TanggalAwalKirim,
        'TanggalAkhirKirim'      => $TanggalAkhirKirim,
        'StrukToll'              => $NamaFileEtoll,
        'StrukBbm'               => $NamaFileBbm,
        'KilometerAwal'          => $KMAwal,
        'KilometerAkhir'         => $KMAkhir,
        'HargaSolar'             => $HargaSolar,
        'TotalIsiSolar'          => $IsiSolar,
        'TotalLiter'             => $TotalLiter,
        'EstimasiPerLiter'       => $EstimasiPerLiter,
        'EstimasiKilometerAkhir' => $EstimasiKMAkhir,
        'Noted'                  => $Noted,
        'UpdatedDate'            => date('Y-m-d H:i:s'), 
        'UpdatedBy'              => $this->session->userdata('user_code')
      );

      // --- 3. MULAI TRANSAKSI DATABASE ---
      $this->BJGMAS01->trans_start();

      // A. UPDATE HEADER
      $this->BJGMAS01->where('Id', $KodeHeader);
      $this->BJGMAS01->update('Trans_PengeluaranMobilHD', $DataHD);

      // B. RESET DETAIL (Hapus Semua Detail Lama Berdasarkan Nomor)
      $this->BJGMAS01->delete('Trans_PengeluaranMobilDT', array('Nomor' => $Nomor));
      $this->BJGMAS01->delete('Trans_PengeluaranMobilDT2', array('Nomor' => $Nomor));

      // C. INSERT ULANG DETAIL 1 (Trans_PengeluaranMobilDT)
      if (!empty($ListPilihan)) {
        $ArrNamaPenerima  = $this->input->post('NamaPenerima');
        $ArrBerapaXKirim  = $this->input->post('BerapaXKirim');
        $ArrListNoBukti   = $this->input->post('ListNoBukti');
        $ArrTotalDO       = $this->input->post('TotalDO');
        $ArrShipmentID    = $this->input->post('ShipmentID');
        $ArrTglKirim      = $this->input->post('TanggalKirimCustomer');
        $DataDT           = array();

        foreach ($ArrNamaPenerima as $key => $val) {
          // Cek berdasarkan ShipmentID
          $CheckID = isset($ArrShipmentID[$key]) ? $ArrShipmentID[$key] : $val;

          // Skip jika BerapaXKirim kosong, NULL, atau = 0
          $BerapaXKirim = isset($ArrBerapaXKirim[$key]) ? $ArrBerapaXKirim[$key] : null;
          if ($BerapaXKirim === null || $BerapaXKirim === '' || floatval($BerapaXKirim) == 0) {
            continue;
          }

          if (in_array($CheckID, $ListPilihan)) {
            $DataDT[] = array(
              'Nomor'        => $Nomor,
              'ShipmentID'   => $ArrShipmentID[$key],
              'CustomerName' => $ArrNamaPenerima[$key],
              'ListNoBukti'  => $ArrListNoBukti[$key],
              'TotalDO'      => floatval($ArrTotalDO[$key]),
              'BerapaXKirim' => floatval($BerapaXKirim),
              'TanggalKirim' => $ArrTglKirim[$key],
              'CreatedDate'  => $OldData->CreatedDate, 
              'CreatedBy'    => $OldData->CreatedBy
            );
          }
        }

        if (!empty($DataDT)) {
          $this->BJGMAS01->insert_batch('Trans_PengeluaranMobilDT', $DataDT);
        }
      }

      // D. INSERT ULANG DETAIL 2 (Trans_PengeluaranMobilDT2)
      $ArrNamaPenerimaMain = $this->input->post('NamaPenerimaMain');
      $ArrBerapaXKirimMain = $this->input->post('BerapaXKirimMain');
      $ArrTanggalKirimMain = $this->input->post('TanggalKirimMain');
      $DataDT2             = array();

      if (!empty($ArrNamaPenerimaMain)) {
        foreach ($ArrNamaPenerimaMain as $key => $val) {
          if (!empty($val)) {
            $DataDT2[] = array(
              'Nomor'        => $Nomor,
              'CustomerName' => strtoupper($val),
              'BerapaXKirim' => isset($ArrBerapaXKirimMain[$key]) ? floatval($ArrBerapaXKirimMain[$key]) : 0,
              'TanggalKirim' => $ArrTanggalKirimMain[$key],
              'CreatedDate'  => $OldData->CreatedDate,
              'CreatedBy'    => $OldData->CreatedBy
            );
          }
        }

        //echo json_encode(array("status" => "error", "HD" => $DataHD, "DT" => $DataDT, "DT2" => $DataDT2)); exit;

        if (!empty($DataDT2)) {
          $this->BJGMAS01->insert_batch('Trans_PengeluaranMobilDT2', $DataDT2);
        }
      }

      // SELESAI TRANSAKSI
      $this->BJGMAS01->trans_complete();

      // --- 4. RESPONSE AKHIR ---
      if ($this->BJGMAS01->trans_status() === FALSE) {
        echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal mengupdate data."));
      } else {
        echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Data berhasil diupdate"));
      }

    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }
  public function approved_by_wh_head() 
  {
    // 1. CEK PERMISSION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() != 1) {
      echo json_encode(array("status" => "forbidden"));

      return;
    }


    $RequestNumber = $this->input->post('RequestNumber');

    $Data = array(
      'Status'          => "S",
      'ApprovedWHDate'  => date('Y-m-d H:i:s'),
      'ApprovedWHBy'    => $this->session->userdata('user_code')
    );

    //echo json_encode(array('status' => 'error','kode'=>$RequestNumber, 'data'=>$Data)); exit;

    $this->BJGMAS01->trans_begin();

    $this->BJGMAS01->where('Nomor', $RequestNumber);
    $this->BJGMAS01->update('Trans_PengeluaranMobilHD', $Data);

    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal melakukan approval."));
    } else {
      $this->BJGMAS01->trans_commit();
      echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Approval berhasil dilakukan."));
    }
  }

  public function approved_by_finance() 
  {
    // 1. CEK PERMISSION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() != 1) {
      echo json_encode(array("status" => "forbidden"));

      return;
    }

    $RequestNumber = $this->input->post('RequestNumber');

    $Data = array(
      'Status'               => "R",
      'ApprovedFinanceDate'  => date('Y-m-d H:i:s'),
      'ApprovedFinanceBy'    => $this->session->userdata('user_code')
    );

    //echo json_encode(array('status' => 'error','kode'=>$RequestNumber, 'data'=>$Data)); exit;

    $this->BJGMAS01->trans_begin();

    $this->BJGMAS01->where('Nomor', $RequestNumber);
    $this->BJGMAS01->update('Trans_PengeluaranMobilHD', $Data);

    if ($this->BJGMAS01->trans_status() === FALSE) {
      $this->BJGMAS01->trans_rollback();
      echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal melakukan approval."));
    } else {
      $this->BJGMAS01->trans_commit();
      echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Approval berhasil dilakukan."));
    }
  }
  public function get_harga_solar() 
  {
    $harga = get_harga_bbm_pertamina('Prov. Banten', 'Gasoil');

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Harga berhasil diambil',
        'harga'       => $harga
      )
    );
  }
  public function get_data_do_by_date() 
  {
    $TanggalAwalKirim   = $this->input->post('TanggalAwalKirim');
    $TanggalAkhirKirim  = $this->input->post('TanggalAkhirKirim');
    $SupirID            = $this->input->post('Supir');

    $ListSupir = [
      "0012024090410" => "BENI", //"BENI PURWANTO"
      "0012025060902" => "ROBI", //"ROBI AHMAD RIFQI"
      "0012023121502" => "ROHMAN" //"ROHMAN"
    ];

    $NamaSupir = $ListSupir[$SupirID];
    //echo $NamaSupir; exit;
    //echo $TanggalKirim; exit;
    $Sql      = "EXEC dbo.GetDOByDate @TanggalAwal = ?, @TanggalAkhir = ?, @Supir = ?";
    $Query    = $this->BJGMAS01->query($Sql, [$TanggalAwalKirim, $TanggalAkhirKirim, $NamaSupir]);
    $Result   = $Query->result();

    if (!empty($Result)) {
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data berhasil diambil",
        "data"        => $Result
      ]);
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data tidak ditemukan",
        "data"        => []
      ]);
    }
  }
  public function generateRequestNumber()
  {
    // Ambil tahun-bulan sekarang
    $yearMonth = date('Ym');
    // Prefix tetap
    $prefix    = "WHDRV" . $yearMonth . "-";
    // Ambil nomor terakhir yang sesuai prefix
    $this->BJGMAS01->select('Nomor');
    $this->BJGMAS01->like('Nomor', $prefix, 'after');
    $this->BJGMAS01->order_by('Nomor', 'DESC');
    $this->BJGMAS01->limit(1);
    $query = $this->BJGMAS01->get('Trans_PengeluaranMobilHD');

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
  public function get_groupid()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->supir->get_groupid($Search);
    
    echo json_encode($Result);
    exit;
  }

  //LAPORAN PENGELUARAN MOBIL
  public function laporan_pengeluaran_mobil()
  {
    $data['group_halaman']    = "Warehouse";
    $data['nama_halaman']     = "Laporan Pengeluaran Mobil";
    $data['icon_halaman']     = "icon-layers";
    $data['MobilList'] 		    = get_daftar_mobil();
    $data['SupirList'] 		    = $this->supir->get_nama_supir();
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['roles']            = $this->roles->get_alls();

    //ADDING TO LOG
    $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type                 = "VIEW";
    $log_data                 = "";

    log_helper($log_url, $log_type, $log_data);
    //END LOG

    $this->load->view('adminx/warehouse/driver/laporan_pengeluaran_mobil', $data);
  }

  public function laporan_pengeluaran_mobil_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('start_date');
		$EndDate 	      = $this->input->post('end_date');
    $Supir 	        = $this->input->post('supir');

    $Sql            = "EXEC dbo.GetLaporanRincianMobil @StartDate = ?, @EndDate = ?, @EmployeeID = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$StartDate, $EndDate, $Supir]);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $EToll      = base_url()."files/uploads/struk_solar/".$value->StrukToll;
      $BBM        = base_url()."files/uploads/struk_solar/".$value->StrukBbm;

      $row    = [];
      $row[]  = $No++;
      $row[]  = '<button type="button" class="btn btn-primary btn-sm" onclick="CheckGroupID(\''.$value->GroupID.'\')"><span class="fa fa-eye"></span></button>';
      $row[]  = $value->GroupID;
      $row[]  = $value->EmployeeName;
      $row[]  = $value->MobilID;
      $row[]  = $value->TanggalPengiriman;
      $row[]  = ($value->StrukToll != NULL) ? '<a href="'.$EToll.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen E-Toll">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $row[]  = ($value->StrukBbm != NULL) ? '<a href="'.$BBM.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen BBM">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      //$row[] = $value->BerapaXKirim;
      $row[] = $value->KilometerAwal;
      $row[] = $value->HargaSolar;
      $row[] = $value->TotalIsiSolar;
      $row[] = $value->TotalLiter;
      $row[] = $value->EstimasiPerLiter;
      $row[] = $value->EstimasiKilometerAkhir;
      $row[] = $value->ApprovedWHDate;
      $row[] = "";
      $row[] = $value->CreatedDate;
      $row[] = $value->CreatedBy;
  
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

  public function pengeluaran_mobil_check_group_id()
  {
    $GroupID = $this->input->post('GroupID');
    $this->BJGMAS01->where('GroupID', $GroupID);
    $query = $this->BJGMAS01->get('Trans_PengeluaranMobilHD');
    $result = $query->result();
    echo json_encode($result);
    exit();
  }
  private function _validation_pengeluaran_mobil()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Supir') == '') {
      $data['inputerror'][]   = 'Supir';
      $data['error_string'][] = 'Supir is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Mobil') == '') {
      $data['inputerror'][]   = 'Mobil';
      $data['error_string'][] = 'Mobil is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TanggalAwalKirim') == '') {
      $data['inputerror'][]   = 'TanggalAwalKirim';
      $data['error_string'][] = 'Tanggal Awal Kirim is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TanggalAkhirKirim') == '') {
      $data['inputerror'][]   = 'TanggalAkhirKirim';
      $data['error_string'][] = 'Tanggal Akhir Kirim is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('KMAwal') == '') {
      $data['inputerror'][]   = 'KMAwal';
      $data['error_string'][] = 'Kilometer Awal is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('KMAkhir') == '') {
      $data['inputerror'][]   = 'KMAkhir';
      $data['error_string'][] = 'Kilometer Akhir is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('IsiSolar') == '') {
      $data['inputerror'][]   = 'IsiSolar';
      $data['error_string'][] = 'Isi Solar Awal is required';
      $data['status']         = FALSE;
    }

    // if (empty($_FILES['Files']['name'])) {
    //   $data['inputerror'][]   = 'Files';
    //   $data['error_string'][] = 'Bukti Struk harus diupload';
    //   $data['status']         = FALSE;
    // }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
