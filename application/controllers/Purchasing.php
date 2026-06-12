<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchasing extends CI_Controller
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
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('barcode_model', 'barcode_sales');
	}

  //PO
	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah PO Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_po', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function get_daftar_po_perbulan() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$year       = $this->input->post('tahun');
		$month 	    = $this->input->post('bulan');
		$table 	    = "Trans_POHD".$year.$month;

		$sql 		    = "SELECT 
                    a.NoBukti AS PoNo, 
                    a.SupplierID, 
                    b.PartnerName AS SupplierName, 
                    a.Status, 
                    a.TGL AS 'Date', 
                    a.Tgl_Needed AS 'DateNeeded', 
                    a.Tgl_JatuhTempo AS 'DueDate', 
                    a.MataUang AS 'Currency', 
                    a.Keterangan AS 'Notes', 
                    a.CreateBy, 
                    a.CreateDate, 
                    a.CompanyCode 
                  FROM 
                    $table a 
                    INNER JOIN (
                      SELECT 
                        * 
                      FROM 
                        Ms_Partner 
                      WHERE 
                        TypePartner in ('S', 'A')
                    ) b ON a.SupplierID = b.PartnerID 
                    and a.CompanyCode = b.CompanyCode 
                  WHERE 
                    a.isWip = 0
                  ORDER BY a.TGL DESC"; //AND MONTH(a.TGL) = '11' AND YEAR(a.TGL) = '2023'
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $PoNo   = "'".$value->PoNo."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$PoNo.')">Tambahkan</button>',
        $value->PoNo,
        $value->SupplierID,
        $value->SupplierName,
        $value->Status,
        date("Y-m-d", strtotime($value->Date)),
        date("Y-m-d", strtotime($value->DateNeeded)),
        date("Y-m-d", strtotime($value->DueDate)),
        $value->Currency,
        
        $value->SupplierName,
        $value->Notes
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_po_data() {
    $po_no      = $this->input->post('po_no');
    $po_no_arr  = explode('/', $po_no);
    $year_month = $po_no_arr[2];
    $table_hd   = "Trans_POHD".$year_month;
    $table_dt   = "Trans_PODT".$year_month;
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql 		    = "SELECT * FROM $table_hd WHERE NoBukti = '$po_no'";
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'NoBukti'           => $value->NoBukti,
      'POParent'          => $value->POParent,
      'TGL'               => $value->TGL,
      'Tgl_Needed'        => $value->Tgl_Needed,
      'ShipmentNotes'     => $value->ShipmentNotes,
      'TGL_JatuhTempo'    => $value->TGL_JatuhTempo,
      'isImport'          => $value->isImport,
      'isAsset'           => $value->isAsset,
      'isBDP'             => $value->isBDP,
      'Status'            => $value->Status,
      'NoContract'        => $value->NoContract,
      'SupplierID'        => $value->SupplierID,
      'ShipmentTo'        => $value->ShipmentTo,
      'Term'              => $value->Term,
      'NilaiTukar'        => $value->NilaiTukar,
      'ConditionID'       => $value->ConditionID,
      'PaymentID'         => $value->PaymentID,
      'ConsigneeID'       => $value->ConsigneeID,
      'PelabuhanID'       => $value->PelabuhanID,
      'TipePPN'           => $value->TipePPN,
      'PPN'               => $value->PPN,
      'MataUang'          => $value->MataUang,
      'Discount'          => $value->Discount,
      'Fee'               => $value->Fee,
      'isWIP'             => $value->isWIP,
      'F_Print'           => $value->F_Print,
      'InvID'             => $value->InvID,
      'JurnalID'          => $value->JurnalID,
      'OnBoardDate'       => $value->OnBoardDate,
      'Keterangan'        => $value->Keterangan,
      'KeteranganJasa'    => $value->KeteranganJasa,
      'ExportWeb'         => $value->ExportWeb,
      'CreateDate'        => $value->CreateDate,
      'CreateBy'          => $value->CreateBy,
      'CompanyCode'       => $value->CompanyCode
    );

    $sql 		    = "SELECT * FROM $table_dt WHERE NoBukti = '$po_no'";
		$query 			= $second_DB->query($sql);
		$value 		  = $query->result();

    $detail[]   = array(
      'Unik'                => $value->Unik,
      'NoBukti'             => $value->NoBukti,
      'ProjectID'           => $value->ProjectID,
      'PartID'              => $value->PartID,
      'WithDetail'          => $value->WithDetail,
      'FormulaID'           => $value->FormulaID,
      'NoRevisi'            => $value->NoRevisi,
      'SubSupplierID'       => $value->SubSupplierID,
      'Qty_Order'           => $value->Qty_Order,
      'Qty_Receive'         => $value->Qty_Receive,
      'NilaiTukar_PO'       => $value->NilaiTukar_PO,
      'UnitID'              => $value->UnitID,
      'HargaJasa'           => $value->HargaJasa,
      'HargaPart'           => $value->HargaPart,
      'CurrencyPO'          => $value->CurrencyPO,
      'HargaPartPO'         => $value->HargaPartPO,
      'Discount'            => $value->Discount,
      'Notes'               => $value->Notes,
      'DetailPart'          => $value->DetailPart,
      'Sequence'            => $value->Sequence,
      'P'                   => $value->P,
      'L'                   => $value->L,
      'T'                   => $value->T,
      'BeratBersihPerPack'  => $value->BeratBersihPerPack,
      'BeratKotorPerPack'   => $value->BeratKotorPerPack,
      'Hs_Code'             => $value->Hs_Code,
      'BMWFE'               => $value->BMWFE,
      'BMWOFE'              => $value->BMWOFE,
      'QtyPerPack'          => $value->QtyPerPack,
      'Grup'                => $value->Grup,
      'isApproved'          => $value->isApproved,
      'isOverPrice'         => $value->isOverPrice,
      'CompanyCode'         => $value->CompanyCode
    );

    echo json_encode($data);
  }

  //PARTNER
  public function partner()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Partner Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_partner', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function get_daftar_partner() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
    
		$sql 		    = "SELECT * FROM Ms_Partner";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $PartnerID = "'".$value->PartnerID."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$PartnerID.')">Tambahkan</button>',
        $value->PartnerID,
        $value->PartnerName,
        $value->Type,
        $value->City,
        $value->Country,
        $value->Contact,
        $value->Phone,
        $value->Address
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_partner_data() {
    $partner_id = $this->input->post('partner_id');

		$sql 		    = "SELECT * FROM Ms_Partner WHERE PartnerID = '$partner_id'";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'PartnerID'             => $value->PartnerID,
      'PartnerName'           => $value->PartnerName,
      'Type'                  => $value->Type,
      'Address'               => $value->Address,
      'City'                  => $value->City,
      'Country'               => $value->Country,
      'Contact'               => $value->Contact,
      'Phone'                 => $value->Phone,
      'Street'                => $value->Street,
      'Block'                 => $value->Block,
      'Number'                => $value->Number,
      'Neighbourhood'         => $value->Neighbourhood,
      'Hamlet'                => $value->Hamlet,
      'District'              => $value->District,
      'AdministrativeVillage' => $value->AdministrativeVillage,
      'Regency'               => $value->Regency,
      'Province'              => $value->Province,
      'Postcode'              => $value->Postcode,
      'Fax'                   => $value->Fax,
      'Email'                 => $value->Email,
      'Website'               => $value->Website,
      'Telex'                 => $value->Telex,
      'NPWP'                  => $value->NPWP,
      'PKPNO'                 => $value->PKPNO,
      'PKPDATE'               => $value->PKPDATE,
      'isImport'              => $value->isImport,
      'BankAccountName'       => $value->BankAccountName,
      'BankAccountNo'         => $value->BankAccountNo,
      'BankName'              => $value->BankName,
      'BankAddress'           => $value->BankAddress,
      'SWIFT'                 => $value->SWIFT,
      'Corresponding'         => $value->Corresponding,
      'TypePartner'           => $value->TypePartner,
      'Currency'              => $value->Currency,
      'CurrencyPO'            => $value->CurrencyPO,
      'PaymentType'           => $value->PaymentType,
      'CreditLimit'           => $value->CreditLimit,
      'Term'                  => $value->Term,
      'ExpiryDay'             => $value->ExpiryDay,
      'TipePPN'               => $value->TipePPN,
      'Notes'                 => $value->Notes,
      'Aktif'                 => $value->Aktif,
      'CreateDate'            => $value->CreateDate,
      'CreateBy'              => $value->CreateBy,
      'CompanyCode'           => $value->CompanyCode
    );

    //SEND DATA TO SERVER HOSTING
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => API_PO.'api/save_partner_to_web',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=977ll1b8gdj4h096q29c69aufcas333b'
      ),
    ));

    $response = curl_exec($curl);
    //print_r($response); exit;
    curl_close($curl);

    echo $response;
  }

  //PART REVISI AMAN
  public function part_revisi() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Part Revisi Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_part_revisi', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function get_daftar_part_revisi() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$sql 		    = "SELECT * FROM Ms_Part_Revisi";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

    // $result[] = (object) array(
    //   'PartID'        => 'AASK-AS001-75-MAS',
    //   'Tgl'           => '2023-01-10 13:12:51.000',
    //   'NoRevisi'      => '1',
    //   'Keterangan'    => '0.38 MM X 4 MM X 260 IN',
    //   'DisetujuiOleh' => 'Mudin',
    //   'Aktif'         => '1'
    // );

		foreach ($result as $key => $value) {
      $PartID = "'".$value->PartID."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$PartID.')">Tambahkan</button>',
        $value->PartID,
        $value->Tgl,
        $value->NoRevisi,
        $value->Keterangan,
        $value->DisetujuiOleh,
        $value->Aktif
			);
		}

		$result = array(
			"draw" 						=> $draw,
			//"recordsTotal" 		=> $query->num_rows(),
			//"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_part_revisi_data()  {
    $part_id    = $this->input->post('part_id');

		$sql 		    = "SELECT * FROM Ms_Part_Revisi WHERE PartID = '$part_id'";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    // $value = (object) array(
    //   'PartID'        => 'AASK-AS001-75-MAS',
    //   'Tgl'           => '2023-01-10 13:12:51.000',
    //   'NoRevisi'      => '1',
    //   'Keterangan'    => '0.38 MM X 4 MM X 260 IN',
    //   'DisetujuiOleh' => 'Mudin',
    //   'Aktif'         => '1'
    // );

    $data = array(
      'PartID'           => $value->PartID,
      'Tgl'              => $value->Tgl,
      'NoRevisi'         => $value->NoRevisi,
      'Keterangan'       => $value->Keterangan,
      'DisetujuiOleh'    => $value->DisetujuiOleh,
      'Aktif'            => $value->Aktif
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => API_PO.'api/save_part_revisi_to_web',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=foeptj9js3j3avlbunmiuhrrfiepra2a'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
  }

  //TYPE INVENTORY AMAN
  public function type_inventory()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Type Inventory Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_type_inventory', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function get_daftar_type_inventory() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
    
		$sql 		    = "SELECT TypeInventoryID, Nama, TypePart, CreateBy, CreateDate  
                   FROM Ms_TypeInventory 
                   WHERE TypePart = 'S'
                   ORDER BY CreateDate DESC";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $TypeInventoryID = "'".$value->TypeInventoryID."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$TypeInventoryID.')">Tambahkan</button>',
        $value->TypeInventoryID,
        $value->Nama,
        $value->TypePart,
        $value->CreateBy,
        substr($value->CreateDate, 0, -4)
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_type_inventory_data() {
    $type_inventory_id = $this->input->post('type_inventory_id');

		$sql 		    = "SELECT * FROM Ms_TypeInventory 
                   WHERE TypeInventoryID = '$type_inventory_id'";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'TypeInventoryID'           => $value->TypeInventoryID,
      'Nama'                      => $value->Nama,
      'Jenis'                     => $value->Jenis,
      'TypePart'                  => $value->TypePart,
      'JenisPenyusutan'           => $value->JenisPenyusutan,
      'Usia'                      => $value->Usia,
      'Residu'                    => $value->Residu,
      'AccountAsset'              => $value->AccountAsset,
      'AccountPenyusutan'         => $value->AccountPenyusutan,
      'AccountAkumPenyusutan'     => $value->AccountAkumPenyusutan,
      'AccountInProgress'         => $value->AccountInProgress,
      'PersenToleransiPO'         => $value->PersenToleransiPO,
      'PersenToleransiReceive'    => $value->PersenToleransiReceive,
      'PersenToleransiPOWIP'      => $value->PersenToleransiPOWIP,
      'PersenToleransiReceiveWIP' => $value->PersenToleransiReceiveWIP,
      'isFinishGoods'             => $value->isFinishGoods,
      'CreateBy'                  => $value->CreateBy,
      'Createdate'                => $value->Createdate
    );

    //SEND DATA TO SERVER HOSTING
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => API_PO.'api/save_type_inventory_to_web',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=977ll1b8gdj4h096q29c69aufcas333b'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
  }

  //TYPE SUPPLIER AMAN
  public function type_supplier()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Type Supplier Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_type_supplier', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function get_daftar_type_supplier() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
    
		$sql 		    = "SELECT * FROM Ms_TypeSupplier";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $type = "'".$value->type."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$type.')">Tambahkan</button>',
        $value->type,
        $value->name,
        $value->CompanyCode
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_type_supplier_data() {
    $type       = $this->input->post('type');

		$sql 		    = "SELECT * FROM Ms_TypeSupplier 
                   WHERE type = '$type'";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'type'           => $value->type,
      'name'           => $value->name,
      'CompanyCode'    => $value->CompanyCode
    );

    //SEND DATA TO SERVER HOSTING
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => API_PO.'api/save_type_supplier_to_web',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=977ll1b8gdj4h096q29c69aufcas333b'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
  }

  //JURNAL AUTO
  public function jurnal_auto()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Jurnal Auto Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_jurnal_auto', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function get_daftar_jurnal_auto() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
    
		$sql 		    = "SELECT * FROM Ms_JurnalAuto order by CreateDate DESC";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $JurnalID = "'".$value->JurnalID."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$JurnalID.')">Tambahkan</button>',
        $value->JurnalID,
        $value->NamaJurnal,
        $value->MsAuto,
        $value->TypeInventoryID,
        $value->IsAsset,
        $value->Keterangan,
        $value->CreateBy,
        substr($value->CreateDate, 0, -4),
        $value->CompanyCode,
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_jurnal_auto_data() {
    $type       = $this->input->post('type');

		$sql 		    = "SELECT * FROM Ms_TypeSupplier 
                   WHERE type = '$type'";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'type'           => $value->type,
      'name'           => $value->name,
      'CompanyCode'    => $value->CompanyCode
    );

    //SEND DATA TO SERVER HOSTING
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => API_PO.'api/save_type_supplier_to_web',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL',
        'Cookie: ci_session=977ll1b8gdj4h096q29c69aufcas333b'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;
  }

  //MASTER PART
  public function master_part() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Purchasing";
			$data['nama_halaman'] 	= "Tambah Master Part Manual";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/purchasing/daftar_master_part', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function master_part_list() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
    $tahun      = $this->input->post('tahun');
    
		$sql 		    = "SELECT * FROM Ms_Part WHERE YEAR(CreateDate) = '$tahun' ORDER BY CreateDate DESC";
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status_qc 	= "";

		foreach ($result as $key => $value) {
      $PartID = "'".$value->PartID."'";
			$data[] = array(
				$no++,
        '<button class="btn btn-info btn-sm" onclick="tambahkan_data('.$PartID.')">Tambahkan</button>',
        $value->PartID,
        $value->PartName,
        $value->OtherID,
        $value->OtherName,
        $value->PartID_Other,
        $value->Material,
        $value->Delivery,
        $value->Keterangan,
        substr($value->CreateDate, 0, -4)
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
  }

  public function send_master_part_data() {
    $part_id    = $this->input->post('part_id');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql 		    = "SELECT * FROM Ms_Part WHERE PartID = '$part_id'";
		$query 			= $second_DB->query($sql);
		$value 		  = $query->row();

    $data       = array(
      'PartID'                    => $value->PartID,
      'PartName'                  => $value->PartName,
      'OtherID'                   => $value->OtherID,
      'OtherName'                 => $value->OtherName,
      'PartID_Other'              => $value->PartID_Other,
      'Material'                  => $value->Material,
      'Delivery'                  => $value->Delivery,
      'Keterangan'                => $value->Keterangan,
      'Keterangan2'               => $value->Keterangan2,
      'FormulaRecomended'         => $value->FormulaRecomended,
      'SectorRecommendedForMPRO'  => $value->SectorRecommendedForMPRO,
      'UnitID_PO'                 => $value->UnitID_PO,
      'UnitID_Stock'              => $value->UnitID_Stock,
      'QtyPallet'                 => $value->QtyPallet,
      'QtySalesPerPack'           => $value->QtySalesPerPack,
      'QtyKanban'                 => $value->QtyKanban,
      'Konversi'                  => $value->Konversi,
      'IsSell'                    => $value->IsSell,
      'SellPrice'                 => $value->SellPrice,
      'SellPriceExport'           => $value->SellPriceExport,
      'StockMin'                  => $value->StockMin,
      'StockMax'                  => $value->StockMax,
      'OrderLeadTime'             => $value->OrderLeadTime,
      'TypeInventoryID'           => $value->TypeInventoryID,
      'RegisterID'                => $value->RegisterID,
      'JenisPart'                 => $value->JenisPart,
      'D_Tinggi'                  => $value->D_Tinggi,
      'D_Lebar'                   => $value->D_Lebar,
      'D_Panjang'                 => $value->D_Panjang,
      'D_Nett'                    => $value->D_Nett,
      'D_Gross'                   => $value->D_Gross,
      'D_Berat'                   => $value->D_Berat,
      'D_BeratBersih'             => $value->D_BeratBersih,
      'D_QtyPerPack'              => $value->D_QtyPerPack,
      'SupplierIDRecomended'      => $value->SupplierIDRecomended,
      'SupplierIDLast'            => $value->SupplierIDLast,
      'OtherIDAktif'              => $value->OtherIDAktif,
      'Aktif'                     => $value->Aktif,
      'NextPartID'                => $value->NextPartID,
      'PartIndex'                 => $value->PartIndex,
      'NamaGambar'                => $value->NamaGambar,
      'Gambar'                    => $value->Gambar,
      'Export'                    => $value->Export,
      'RegisterDateGR'            => $value->RegisterDateGR,
      'RegisterDateJO'            => $value->RegisterDateJO,
      'RegisterDateMPR'           => $value->RegisterDateMPR,
      'CompanyConnection'         => $value->CompanyConnection,
      'VersiBarcode'              => $value->VersiBarcode,
      'CreateDate'                => $value->CreateDate,
      'CreateBy'                  => $value->CreateBy
    );
  }
}