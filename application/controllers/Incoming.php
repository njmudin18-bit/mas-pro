<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incoming extends CI_Controller {

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

	public function __construct() {
    parent::__construct();

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('incoming_model', 'incoming');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Incoming Part";
      $data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/incoming/search_po', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
	}

  public function incoming_part_list()
  {
    $Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');

    // Eksekusi stored procedure ambil semua data
    $Sql    = "EXEC dbo.GetPOPurchasingDynamic @StartDate = ?, @EndDate = ?";
    $Query  = $this->BJGMAS01->query($Sql, array($StartDate, $EndDate));
    $Result = $Query->result();
    $Total  = count($Result);
    $Paged  = array_slice($Result, $Start, $Length);

    $Data  = [];
    $No    = $Start + 1;
    foreach ($Paged as $key => $Row) {
      $DataCount  = (int)$Row->Cetak;
      $Url          = base_url()."incoming/preview_fifo_card_single/".base64_encode($Row->NoBukti);
      //$HapusJs    = "'".addslashes($Row->NoBukti)."'";
      $HapusJs    = "'".$Row->NoBukti."', '".$Row->PartID."'";

      // Tentukan isi dropdown berdasarkan jumlah Cetak
      if ($DataCount > 0) {
        $Isi = '<div class="dropdown d-inline-block">
                  <button id="btn_'.$key.'" class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-more-fill align-middle"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btn_'.$key.'">
                    <a href="'.$Url.'" target="_blank" class="dropdown-item edit-item-btn">
                      <i class="ri-eye-fill align-bottom me-2"></i> Cek All Barcode
                    </a>
                    <a onclick="openModalDelete('.$HapusJs.')" class="dropdown-item remove-item-btn">
                      <i class="ri-delete-bin-fill align-bottom me-2"></i> Hapus All Barcode
                    </a>
                  </div>
                </div>';
      } else {
        $Isi = '-';
      }

      $Data[] = array(
        $Row->NoBukti,
        $No++,
        $Isi,
        $Row->NoBukti,
        $Row->Cetak,
        $Row->SupplierID,
        $Row->Type,
        $Row->PartnerName,
        $Row->PartID,
        $Row->PartName,
        $Row->CreateDate
      );
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $Data
    ]);
  }

  public function show_qty_cetak() 
  {
    $PONumberArray  = $this->input->post('PONumber');
    $PONumberData   = $this->get_selected_po($PONumberArray);
    //echo $PONumberData; exit;
    //echo json_encode($PONumberData); exit;

    $html           = '';
    $No             = 1;
    $NoBox          = 1;
    if (is_array($PONumberData)) {
      foreach ($PONumberData as $key => $value) {
        $html .= '<tr data-group>
                    <td class="text-right">'.$No++.'</td>
                    <td class="text-left">
                      '.$value->NoBukti.'<hr>
                      '.$value->Type.'. '.$value->PartnerName.'
                    </td>
                    <td>
                      '.$value->PartID.'<hr>'.$value->PartName.'
                    </td>
                    <td>
                      <div data-x-wrapper="Berat">
                        <div data-x-group class="input-group mb-3">
                          <input type="text" id="Lot_'.$key.'" name="LotNumber[]" class="form-control" placeholder="Nomor Lot" type="text" maxlength="12">
                          <input type="text" id="Qty_'.$key.'" name="Berat[]" class="form-control" placeholder="Netto (KG)" type="text" maxlength="8" oninput="AllowDecimalAndComma(this)">
                          <div class="input-group-append">
                            <span class="btn btn-success" id="quantity-plus" data-add-btn title="Tambah kolom">+</span>
                          </div>
                        </div>
                      </div>

                      <input type="hidden" name="NomorPO[]" value="'.$value->NoBukti.'" id="NomorPO_'.$key.'" >
                      <input type="hidden" name="PartID[]" value="'.$value->PartID.'" id="PartID_'.$key.'" >
                      <input type="hidden" name="PartName[]" value="'.$value->PartName.'" id="PartName_'.$key.'" >
                      <input type="hidden" name="SupplierID[]" value="'.$value->SupplierID.'" id="SupplierID_'.$key.'" >
                      <input type="hidden" name="SupplierType[]" value="'.$value->Type.'" id="SupplierType_'.$key.'" >
                      <input type="hidden" name="SupplierName[]" value="'.$value->PartnerName.'" id="SupplierName_'.$key.'" >
                    </td>
                  </tr>';
      }
    } else {
      // tangani jika data kosong atau salah format
      log_message('error', 'PONumberData tidak valid: '.print_r($PONumberData, true));
    }

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => "success",
        'message'     => "Data sukses ditampilkan",
        'html'        => $html,
        'data'        => $PONumberData
      )
    );

    exit;
  }

  public function get_selected_po($PONumberArray) 
  {
    $ByPeriod = [];

    // 1. Siapkan semua NoBukti sebagai string untuk WHERE
    if (!is_array($PONumberArray) || empty($PONumberArray)) {
        $PONumberArray = []; // tetap array agar tidak error
    }

    // 2. Kelompokkan berdasarkan yyyymm dari PONumber
    foreach ($PONumberArray as $po) {
        if (preg_match('/PO\/MAS\/(\d{6})\//', $po, $match)) {
            $period = $match[1];
            $ByPeriod[$period][] = $po;
        }
    }

    // 3. Tambahkan periode default (tanggal hari ini) jika belum ada
    $currentPeriod = date('Ym');
    if (!array_key_exists($currentPeriod, $ByPeriod)) {
        $ByPeriod[$currentPeriod] = []; // bisa kosong, tapi tetap akan dijalankan
    }

    // 4. Escape semua NoBukti
    $allPONumbersEscaped = [];
    foreach ($PONumberArray as $po) {
        $allPONumbersEscaped[] = $this->BJGMAS01->escape($po);
    }
    $whereInClause = empty($allPONumbersEscaped) ? '' : "WHERE a.NoBukti IN (" . implode(",", $allPONumbersEscaped) . ")";

    // 5. Bangun query dinamis per periode
    $sqlParts = [];
    foreach ($ByPeriod as $period => $_) {
        $tableHD = "Trans_POHD{$period}";
        $tableDT = "Trans_PODT1{$period}";

        $sqlParts[] = "
            SELECT 
                a.NoBukti, a.SupplierID, d.Type, d.PartnerName,
                b.PartID, c.PartName, CAST(a.CreateDate AS date) AS CreateDate
            FROM $tableHD a
            LEFT JOIN $tableDT b ON b.NoBukti = a.NoBukti
            LEFT JOIN Ms_Part c ON c.PartID = b.PartID
            LEFT JOIN Ms_Partner d ON d.PartnerID = a.SupplierID
            $whereInClause
        ";
    }

    if (empty($sqlParts)) {
        return [];
    }

    // 6. Gabungkan dan eksekusi
    $finalSql = implode(" UNION ALL ", $sqlParts) . " ORDER BY CreateDate DESC";
    //return $finalSql; exit;

    $query = $this->BJGMAS01->query($finalSql);
    return $query->result();
  }

  public function saving_qty_cetak() 
  {
    $Months    = $this->input->post('Months');
    $Data      = $this->input->post('Data');

    $ArrayData = [];
    $POArray   = [];
    $PIDArray  = [];
    $DateArray = [];

    // Validasi dasar
    if (empty($Months) || !is_array($Data)) {
      echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap atau format salah.']);
      
      return;
    }

    // Kumpulkan semua PO dan PartID terlebih dahulu
    foreach ($Data as $row) {
      $POArray[]  = $row['NomorPO'];
      $PIDArray[] = $row['PartID'];
    }

    // Ambil Sequent terakhir per kombinasi PONumber + PartID
    $lastSequents = [];
    if (!empty($POArray) && !empty($PIDArray)) {
      $this->BJGMAS01->select('PONumber, PartID, MAX(CAST(Sequent AS INT)) AS LastSeq');
      $this->BJGMAS01->from('Trans_FifoCard');
      $this->BJGMAS01->where_in('PONumber', array_unique($POArray));
      $this->BJGMAS01->where_in('PartID', array_unique($PIDArray));
      $this->BJGMAS01->group_by(['PONumber', 'PartID']);

      $result = $this->BJGMAS01->get()->result();

      foreach ($result as $row) {
        $key                = $row->PONumber . '|' . $row->PartID;
        $lastSequents[$key] = intval($row->LastSeq);
      }
    }

    // Susun data
    foreach ($Data as $rowIndex => $row) {
      $NomorPO      = $row['NomorPO'];
      $PartID       = $row['PartID'];
      $PartName     = $row['PartName'];
      $SupplierID   = $row['SupplierID'];
      $SupplierType = $row['SupplierType'];
      $SupplierName = $row['SupplierName'];
      $LotNumbers   = isset($row['LotNumber']) ? $row['LotNumber'] : [];
      $Berats       = $row['Berat'];

      if (!is_array($Berats)) {
        echo json_encode(['status' => 'error', 'message' => "Data Berat tidak valid di baris ke-" . ($rowIndex + 1)]);
        return;
      }

      $key = $NomorPO . '|' . $PartID;
      $lastSeq = isset($lastSequents[$key]) ? $lastSequents[$key] : 0;

      foreach ($Berats as $i => $berat) {
        if (empty($berat)) continue;

        $sequentNumber = $lastSeq + ($i + 1);
        $sequent = str_pad($sequentNumber, 3, "0", STR_PAD_LEFT);
        $weight  = floatval(str_replace(",", ".", $berat));

        $ArrayData[] = [
          'PONumber'      => $NomorPO,
          'BarcodeNumber' => "MT-$NomorPO|$PartID|$weight|$sequent",
          'Sequent'       => $sequent,
          'PartID'        => $PartID,
          'PartName'      => $PartName,
          'SupplierID'    => $SupplierID,
          'SupplierType'  => $SupplierType,
          'SupplierName'  => $SupplierName,
          'Weight'        => $weight,
          'Month'         => $Months,
          'LotNumber'     => isset($LotNumbers[$i]) ? $LotNumbers[$i] : '',
          'CreateDate'    => date('Y-m-d H:i:s'),
          'CreateBy'      => $this->session->userdata('user_id')
        ];

        $DateArray[] = date('Y-m-d');
      }
    }

    //echo json_encode($ArrayData); exit();

    if (count($ArrayData) > 0) {
      $POItems = array_map(function($item) {
        return "'" . $item . "'";
      }, array_unique($POArray));

      $PartIdItems = array_map(function($item) {
        return "'" . $item . "'";
      }, array_unique($PIDArray));

      $POMerged     = '[' . implode(', ', $POItems) . ']';
      $PartIdMerged = '[' . implode(', ', $PartIdItems) . ']';

      $Insert = $this->BJGMAS01->insert_batch('Trans_FifoCard', $ArrayData);
      if ($Insert) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses disimpan",
          "PONumber"    => $POArray,
          "PartID"      => $PIDArray,
          "Date"        => $DateArray,
          "PO"          => $POMerged,
          "PartID"      => $PartIdMerged,
          "Date"        => date('Y-m-d'),
          "Url"         => base_url() . "incoming/preview_fifo_card/" . base64_encode($POMerged) . "/" . base64_encode($PartIdMerged) . "/" . date('Y-m-d')
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan",
          "PONumber"    => [],
          "PartID"      => [],
          "Date"        => []
        ]);
      }
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Berat bersih tidak boleh kosong atau NOL"
      ]);
    }
  }

  public function preview_fifo_card()
	{
    $PONumberArray          = base64_decode($this->uri->segment(3));
    $PartIDArray            = base64_decode($this->uri->segment(4));
    $DateArray              = $this->uri->segment(5);

    //echo $PONumberArray."<br>".$PartIDArray; exit;

    $data['group_halaman'] 	= "Warehouse";
    $data['nama_halaman'] 	= "Preview FIFO Card";
    $data['array_nomor_po'] = $PONumberArray;
    $data['array_part_id'] 	= $PartIDArray;
    $data['array_date'] 	  = $DateArray;
    $data['icon_halaman'] 	= "icon-airplay";
    $data['perusahaan'] 		= $this->perusahaan->get_details();

    //ADDING TO LOG
    $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	= "VIEW";
    $log_data 	= "";
    
    log_helper($log_url, $log_type, $log_data);
    //END LOG

    $this->load->view('adminx/warehouse/incoming/print_preview_fifo_card', $data, FALSE);
	}

  public function get_fifo_card() 
  {
    $PONumberArray  = $this->input->post('PONumber');
    $PartIDArray    = $this->input->post('PartID');
    $DateArray      = $this->input->post('Date');
    $Data           = $this->incoming->show_fifo_card_selected($PONumberArray, $PartIDArray, $DateArray);
    $DataRekap      = $this->incoming->get_data_rekap($PONumberArray, $PartIDArray);
    $DataSelect     = $this->incoming->get_data_for_select($PONumberArray, $PartIDArray);
    $HtmlRekap      = '';
    $No             = 1;
    $TotakCetak     = 0;

    $IsiData        = array();
		$no 	          = 0;
    foreach ($DataRekap as $key => $value) {
      $Isi        = "'".$value->PONumber."', '".$value->PartID."'";

			$no++;
			$row        = array();
			$row[]      = $no;
			//add html for action
      $row[]      = '<button onclick="show_detail_fifo('.$Isi.')" class="btn btn-danger btn-sm" type="button" id="BtnEdit_'.$key.'">Edit</button>';
			$row[]      = $value->PONumber;
			$row[]      = $value->QtyCetak;
			$row[]      = $value->TGL_CETAK;
			$row[]      = $value->SupplierType.'. '.$value->SupplierName;
			$row[]      = $value->PartID;
			$row[]      = $value->PartName;
			
			$IsiData[]  = $row;
		}

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data sukses ditampilkan',
        'data'        => $IsiData,
        'datas'       => $Data,
        'data_rekap'  => $DataRekap,
        'html_rekap'  => $HtmlRekap,
        'data_select' => $DataSelect,
        'total_cetak' => $TotakCetak
      )
    );
  }

  public function hapus_fifo_card()
	{
    $id             = $this->input->post('Barcode');
    $data_delete 		= $this->incoming->get_by_id($id); //DATA DELETE
    $data 					= $this->incoming->delete_by_id($id);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url 		    = base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	    = "DELETE";
    $log_data 	    = json_encode($data_delete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
	}

  public function get_selected_fifo_card() 
  {
    $Pilihan        = $this->input->post('selected');
    $PONumberArray  = [];
    $PartIDArray    = [];
    $FilterDate     = [];
    foreach ($Pilihan as $key => $value) {
      $Isi              = explode('|', $value);
      $FilterDate[]     = $Isi[0];
      $PONumberArray[]  = $Isi[1];
      $PartIDArray[]    = $Isi[2];
    }

    $Data           = $this->incoming->show_fifo_card_selected($PONumberArray, $PartIDArray, $FilterDate);
    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data sukses ditampilkan',
        'data'        => $Data
      )
    );
  }

  public function preview_fifo_card_single()
  {
    $PONumber               = base64_decode($this->uri->segment(3));
    $data['group_halaman'] 	= "Master Data";
    $data['nama_halaman'] 	= "Preview FIFO Card ".$PONumber;
    $data['nomor_po']       = $PONumber;
    $data['icon_halaman'] 	= "icon-airplay";
    $data['perusahaan'] 		= $this->perusahaan->get_details();

    //ADDING TO LOG
    $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	= "VIEW";
    $log_data 	= "";
    
    log_helper($log_url, $log_type, $log_data);
    //END LOG

    $this->load->view('adminx/warehouse/incoming/print_preview_fifo_card_single', $data, FALSE);
  }

  public function get_fifo_card_single() 
  {
    $PONumber       = $this->input->post('PONumber');
    $Data           = $this->incoming->show_fifo_card_by_po($PONumber);
    $DataRekap      = $this->incoming->get_data_rekap_by_po($PONumber);
    $DataSelect     = $this->incoming->get_data_for_select_by_po($PONumber);

    $HtmlRekap      = '';
    $No             = 1;
    $TotakCetak     = 0;

    $IsiData        = array();
		$no 	          = 0;
    foreach ($DataRekap as $key => $value) {
      $Isi        = "'".$value->PONumber."', '".$value->PartID."'";

			$no++;
			$row        = array();
			$row[]      = $no;
			//add html for action
      $row[]      = '<button onclick="show_detail_fifo('.$Isi.')" class="btn btn-danger" type="button" id="BtnEdit_'.$key.'">Edit</button>';
			$row[]      = $value->PONumber;
			$row[]      = $value->QtyCetak;
			$row[]      = $value->TGL_CETAK;
			$row[]      = $value->SupplierType.'. '.$value->SupplierName;
			$row[]      = $value->PartID;
			$row[]      = $value->PartName;
			
			$IsiData[]  = $row;
		}

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data sukses ditampilkan',
        'data'        => $IsiData,
        'datas'       => $Data,
        'data_rekap'  => $DataRekap,
        'html_rekap'  => $HtmlRekap,
        'data_select' => $DataSelect,
        'total_cetak' => $TotakCetak
      )
    );
  }

  public function hapus_semua_fifo_card()
	{
    $id             = $this->input->post('PONumber');
    $PartID         = $this->input->post('PartID');
    $data_delete 		= $this->incoming->get_all_id($id, $PartID);
    $data 					= $this->incoming->delete_all_barcode($id, $PartID);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	= "DELETE";
    $log_data 	= json_encode($data_delete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
	}
}