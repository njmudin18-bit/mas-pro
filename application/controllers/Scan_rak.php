<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Scan_rak extends CI_Controller
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
		$this->load->model('roles_model', 'roles');

    $this->BJGMAS01  = $this->load->database("bjsmas01_db", true);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Scan Rak";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/rak/scan', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function cek_rak()
  {
    $QR       = trim($this->input->post('code_barcode'));
    $QRArray  = explode("-", $QR);
    if ($QRArray[0] == '1RAK') {
      $Data = $this->BJGMAS01->get_where('Trans_RakHD', array('QRCode' => $QR))->row();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "sukses",
          "message"     => "Data Rak ditemukan",
          "data"        => $Data,
          "detail"      => $this->BJGMAS01->get_where('Trans_RakDT', array('IdHeader' => $Data->Id))->result()
        )
      );
    } elseif ($QRArray[0] == '2RAK') {
      $Sql2   = "SELECT a.Id, A.IdHeader, a.Sequent, a.QRCode, b.Rak, b.WHLokasi, b.Noted
                  FROM Trans_RakDT a
                  LEFT JOIN Trans_RakHD b ON b.Id = a.IdHeader
                  WHERE a.QRCode = '$QR'";
      $Query2 = $this->BJGMAS01->query($Sql2);
      $Data   = $Query2->row();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "sukses",
          "message"     => "Data Rak ditemukan",
          "data"        => $Data,
          "detail"      => $this->BJGMAS01->get_where('Trans_RakDT', array('QRCode' => $QR))->result()
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data Rak tidak ditemukan",
          "data"        => array(),
          "detail"      => array()
        )
      );
    }
  }

  public function cek_rak_except()
  {
    $QR       = trim($this->input->post('code_barcode'));
    $QRArray  = explode("-", $QR);
    if ($QRArray[0] == '1RAK') {
      $this->BJGMAS01->where_not_in('QRCode', $QR);
      $Data = $this->BJGMAS01->get('Trans_RakHD')->result();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "sukses",
          "message"     => "Data Rak ditemukan",
          "data"        => $Data
        )
      );
    } elseif ($QRArray[0] == '2RAK') {
      $Sql2   = "SELECT a.Id, A.IdHeader, a.Sequent, a.QRCode, b.Rak, b.WHLokasi, b.Noted
                  FROM Trans_RakDT a
                  LEFT JOIN Trans_RakHD b ON b.Id = a.IdHeader
                  WHERE a.QRCode = '$QR'";
      $Query2 = $this->BJGMAS01->query($Sql2);
      $Data   = $Query2->row();

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "sukses",
          "message"     => "Data Rak ditemukan",
          "data"        => $Data,
          "detail"      => $this->BJGMAS01->get_where('Trans_RakDT', array('QRCode' => $QR))->result()
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data Rak tidak ditemukan",
          "data"        => array(),
          "detail"      => array()
        )
      );
    }
  }

  public function get_baris()
  {
    $QrRak  = $this->input->post('QrRak');
    $Rak    = $CekRak = $this->BJGMAS01->get_where('Trans_RakHD', ['QRCode' => $QrRak])->row();
    $IdRak  = $Rak->Id;
    $Data   = $this->BJGMAS01->get_where('Trans_RakDT', array('IdHeader' => $IdRak))->result();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data baris ditemukan.",
        "data"        => $Data
      )
    );
  }

  public function cari_partname()
  {
    $SearchTerm = $this->input->get('term');
    $TypeInvID  = array('RM01', 'MP01');

    $this->BJGMAS01->select('PartID as id, PartName as text, UnitID_PO as unit');
    $this->BJGMAS01->where_in('TypeInventoryID', $TypeInvID);
    $this->BJGMAS01->like('PartName', $SearchTerm);
    $this->BJGMAS01->or_like('PartID', $SearchTerm);

    $Data = $this->BJGMAS01->get('Ms_Part');

    echo json_encode($Data->result());
  }

  public function cari_nomor_job()
  {
    $SearchTerm   = $this->input->get('term');
    $YearMonth    = date('Ym');
    $TableJob     = "Trans_Job".$YearMonth;
    $TableMpr     = "Trans_MPRHD".$YearMonth;
    $Response     = array();

    $this->BJGMAS01->select('NoBukti as id, NoBukti as text');
    $this->BJGMAS01->like('NoBukti', $SearchTerm);
    $JobData = $this->BJGMAS01->get($TableJob)->result();
    foreach ($JobData as $key => $value) {
      $Response[] = array(
        'id'   => $value->id,
        'text' => $value->text,
        'Mpr'  => $this->BJGMAS01->get_where($TableMpr, array('NoBuktiJob' => $value->id))->result()
      );
    }

    echo json_encode($Response);
  }

  //DONE DENGAN FORMAT BARU
  public function tambah_item()
  {
    $this->_validation_item();

    $Baris      = $this->input->post('Baris');
    $PartID     = $this->input->post('PartID');
    $Quantity   = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('Quantity'))));
    $Unit       = $this->input->post('Unit');
    $Rak        = $this->input->post('Rak');
    $WHLokasi   = $this->input->post('WHLokasi');
    $Noted      = $this->input->post('Noted');
    $Qr         = $this->input->post('QR');
    $LotNumber  = $this->input->post('LotNumber');
    $QrArray    = explode("-", $Qr);
    $QrRak      = "";
    $QrSubRak   = "";

    if ($QrArray[0] == '1RAK') {
      $this->BJGMAS01->select('a.Id, a.Rak, b.Sequent, a.QRCode AS QrRak, b.QRCode AS QrSubRak, a.WHLokasi')
                     ->from('Trans_RakHD a')
                     ->join('Trans_RakDT b', 'b.IdHeader = a.Id', 'left')
                     ->where('a.QRCode', $Qr)
                     ->where('b.Sequent', $Baris);
      $DataRak    = $this->BJGMAS01->get()->row();
      $QrRak      = $DataRak->QrRak;
      $QrSubRak   = $DataRak->QrSubRak;
    }

    if ($QrArray[0] == '2RAK') {
      $this->BJGMAS01->select('a.Id, b.Rak, a.Sequent, b.QRCode AS QrRak, a.QRCode AS QrSubRak, b.WHLokasi')
                     ->from('Trans_RakDT a')
                     ->join('Trans_RakHD b', 'b.Id = a.IdHeader', 'left')
                     ->where('a.QRCode', $Qr);
      $DataRak    = $this->BJGMAS01->get()->row();
      $QrRak      = $DataRak->QrRak;
      $QrSubRak   = $DataRak->QrSubRak;
    }

    //$Items      = $this->BJGMAS01->get_where('Trans_RakContents', array('Rak' => $Rak, 'SubRak' => $Baris, 'PartID' => $PartID, 'Status' => 'IN'));
    $Items      = $this->BJGMAS01
                  ->where(array(
                    'Rak' => $Rak,
                    'SubRak' => $Baris,
                    'PartID' => $PartID,
                    'Status' => 'IN'
                  ))
                  ->where('SoftDelete IS NULL')
                  ->get('Trans_RakContents');
    $CekItems   = $Items->num_rows();
    if ($CekItems == 0) {

      $Data = array(
        'PartID'          => $PartID,
        'Status'          => 'IN',
        'Quantity'        => $Quantity,
        'Stock'           => $Quantity,
        'Unit'            => $Unit,
        'WHLokasi'        => $WHLokasi,
        'LotNumber'       => $LotNumber,
        'Noted'           => $Noted,
        'Rak'             => $Rak,
        'QrRak'           => $QrRak,
        'SubRak'          => $Baris,
        'QrSubRak'        => $QrSubRak,
        //'StockEntryDate'  => date('Y-m-d'),
        'CreateDate'      => date('Y-m-d H:i:s'),
        'CreateBy'        => $this->session->userdata('user_code')
      );

      //echo json_encode(array('Data' => $Data, 'Status' => 'New')); exit;

      $Save = $this->BJGMAS01->insert('Trans_RakContents', $Data);
      if ($Save) {
        $this->log_history($Data, 'IN');
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses disimpan."
          )
        );
        exit();
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data Rak DT gagal disimpan."
          )
        );
        exit();
      }
    } else {
      $DataItem   = $Items->row();
      $IdItem     = $DataItem->Id; 
      $StockLama  = floatval($DataItem->Stock);
      $Request    = floatval(str_replace(', ', '.', str_replace('.', '', $this->input->post('Quantity'))));
      $Noted      = $this->input->post('Noted');
      $StockBaru  = $StockLama + $Request;

      $Data       = array(
        'PartID'          => $PartID,
        'Status'          => 'IN',
        'Quantity'        => $Request,
        'OldStock'        => $StockLama,
        'Stock'           => $StockBaru,  
        'Unit'            => $Unit,
        'WHLokasi'        => $WHLokasi,
        'LotNumber'       => $LotNumber,
        'Noted'           => $Noted,
        'Rak'             => $Rak,
        'QrRak'           => $QrRak,
        'SubRak'          => $Baris,
        'QrSubRak'        => $QrSubRak,
        //'StockEntryDate'  => date('Y-m-d'),
        'CreateDate'      => date('Y-m-d H:i:s'),
        'CreateBy'        => $this->session->userdata('user_code')
      );

      $Log        = array(
        'DataLama' => $DataItem,
        'DataBaru' => $Data
      );

      //echo json_encode(array('DataBaru' => $Data, 'DataLama' => $DataItem, 'Log' => $Log)); exit;
      
      $Save = $this->BJGMAS01->insert('Trans_RakContents', $Data);
      if ($Save) {
        $this->log_history($Log, 'IN');
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data stock sukses diupdate."
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data stock gagal diupdate."
          )
        );
      }
    }
  }

  //DONE SEBAGIAN UNTUK STOK MASUK, 
  //DONE STOK KELUAR STATUS BON, 
  //DONE STOK KELUAR STATUS JOB
  public function update_item()
  {
    $IdItem       = $this->input->post('KodeEdit');
    $Rak          = $this->input->post('RakEdit');
    $QrRak        = $this->input->post('QrRakEdit');
    $Baris        = $this->input->post('BarisEdit');
    $QrBaris      = $this->input->post('QrBarisEdit');
    $PartID       = $this->input->post('PartIdEdit');
    $StockLama    = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('QuantityEdit'))));
    $Request      = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('QuantityBaru'))));
    $Status       = $this->input->post('Status');
    $Peruntukan   = $this->input->post('Peruntukan');
    $Noted        = $this->input->post('NotedEdit');
    $Unit         = $this->input->post('UnitEdit');
    $WHLokasi     = $this->input->post('WHLokasiEdit');
    $NoBukti      = $this->input->post('JobNomor');
    $MprArray     = $this->input->post('MprItems');
    $DataItem     = $this->BJGMAS01->get_where('Trans_RakContents', array('Id' => $IdItem))->row();
    if ($Request > 0) {
      if ($Status == 'IN') {
        $StockBaru  = $StockLama + $Request;
        $Data  = array(
          'PartID'          => $PartID,
          'Status'          => $Status,
          'Quantity'        => $Request,
          'OldStock'        => $StockLama,
          'Stock'           => $StockBaru,  
          'Unit'            => $Unit,
          'WHLokasi'        => $WHLokasi,
          'Noted'           => $Noted,
          'Rak'             => $Rak,
          'QrRak'           => $QrRak,
          'SubRak'          => $Baris,
          'QrSubRak'        => $QrBaris,
          //'StockEntryDate'  => date('Y-m-d'),
          'CreateDate'      => date('Y-m-d H:i:s'),
          'CreateBy'        => $this->session->userdata('user_code')
        );

        $Log  = array(
          'DataLama' => $DataItem,
          'DataBaru' => $Data
        );

        //echo json_encode(array('Data' => $Data, 'Log' => $Log)); exit;
        
        $Save = $this->BJGMAS01->insert('Trans_RakContents', $Data);
        if ($Save) {
          $this->log_history($Log, 'UP');
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Data stock sukses ditambah."
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data stock gagal ditambah."
            )
          );
        }
      } else {
        if ($Request > $StockLama) {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Stok tidak mencukupi. Stok tersedia: ".number_format($StockLama, 0)
            )
          );
          exit();
        } else {
          $StockBaru        = $StockLama - $Request;
          $StockEntryDate   = null;
          if (empty($DataItem->StockEntryDate)) {
            $StockEntryDate = date('Y-m-d', strtotime($DataItem->CreateDate));
          } else {
            $StockEntryDate = $DataItem->StockEntryDate;
          }
          
          if ($Peruntukan == 'Bon') {
            $Data = array(
              'PartID'          => $PartID,
              'Status'          => $Status,
              'Quantity'        => $Request,
              'OldStock'        => $StockLama,
              'Stock'           => $StockBaru,  
              'Unit'            => $Unit,
              'WHLokasi'        => $WHLokasi,
              'Destination'     => $Peruntukan,
              'Noted'           => $Noted,
              'Rak'             => $Rak,
              'QrRak'           => $QrRak,
              'SubRak'          => $Baris,
              'QrSubRak'        => $QrBaris,
              'StockEntryDate'  => $StockEntryDate,
              'CreateDate'      => date('Y-m-d H:i:s'),
              'CreateBy'        => $this->session->userdata('user_code')
            );

            $Log  = array(
              'DataLama'        => $DataItem,
              'DataBaru'        => $Data
            );

            //echo json_encode(array('Data' => $Data, 'Log' => $Log)); exit;
            $Save = $this->BJGMAS01->insert('Trans_RakContents', $Data);
            if ($Save) {
              $this->log_history($Log, 'UP');
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status"      => "success",
                  "message"     => "Data stock sukses dikurangi."
                )
              );
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Data stock gagal dikurangi."
                )
              );
            }
          } else {
            if (!empty($MprArray)) {
              foreach ($MprArray as $key => $value) {
                $Data[] = array(
                  'NoBukti'         => $NoBukti,
                  'NoMpr'           => $value,
                  'PartID'          => $PartID,
                  'Status'          => $Status,
                  'Quantity'        => $Request,
                  'OldStock'        => $StockLama,
                  'Stock'           => $StockBaru,  
                  'Unit'            => $Unit,
                  'WHLokasi'        => $WHLokasi,
                  'Status'          => $Status,
                  'Destination'     => $Peruntukan,
                  'Noted'           => $Noted,
                  'Rak'             => $Rak,
                  'QrRak'           => $QrRak,  
                  'SubRak'          => $Baris,
                  'QrSubRak'        => $QrBaris,
                  'StockEntryDate'  => $StockEntryDate,
                  'CreateDate'      => date('Y-m-d H:i:s'),
                  'CreateBy'        => $this->session->userdata('user_code')
                );
              }

              $Log  = array(
                'DataLama'      => $DataItem,
                'DataBaru'      => $Data
              );

              //echo json_encode(array('Data' => $Data, 'Log' => $Log)); exit;
              $Save = $this->BJGMAS01->insert_batch('Trans_RakContents', $Data);
              if ($Save) {
                $this->log_history($Log, 'UP');
                echo json_encode(
                  array(
                    "status_code" => 200,
                    "status"      => "success",
                    "message"     => "Data stock sukses dikurangi."
                  )
                );
              } else {
                echo json_encode(
                  array(
                    "status_code" => 500,
                    "status"      => "error",
                    "message"     => "Data stock gagal dikurangi."
                  )
                );
              }
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Silahkan pilih salah satu No. MPR dahulu."
                )
              );
            }
          }
        }
      }
    } else {
      $Sts = $Status == 'IN' ? 'Penambah' : 'Pengurang';
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Silahkan isi Quantity ".$Sts." lebih besar dari 0"
        )
      );
    }
  }

  public function hapus_item()
  {
    $IdItem = $this->input->post('IdItem');
    $PartID = $this->input->post('PartID');
    $Rak    = $this->input->post('Rak');
    $Baris  = $this->input->post('Baris');
    // echo json_encode(array('PartID' => $PartID, 'Rak' => $Rak, 'Baris' => $Baris));
    // exit;
    $Log    = $this->BJGMAS01->get_where('Trans_RakContents', array('Id' => $IdItem))->row();
    $Data   = array(
      'SoftDelete'  => 'Y',
      'DeleteDate'  => date('Y-m-d H:i:s'),
      'DeleteBy'    => $this->session->userdata('user_code')
    );

    $Delete = $this->BJGMAS01->update('Trans_RakContents', $Data, array('PartID' => $PartID, 'Rak' => $Rak, 'SubRak' => $Baris));
    if ($Delete) {
      $this->log_history($Log, 'DL');
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses dihapus."
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal dihapus."
        )
      );
    }
  }

  //DONE
  public function daftar_items()
  {
    $Draw     = intval($this->input->get("draw"));
    $Start    = intval($this->input->get("start"));
    $Length   = intval($this->input->get("length"));
    $Baris    = $this->input->post("baris");
    $Where    = "";

    $Qr       = trim($this->input->post('qr'));
    $QrArray  = explode("-", $Qr);
    if ($QrArray[0] == '1RAK') {
      if ($Baris == null || $Baris == '') {
        $Where = " WHERE QrRak = '$Qr' ";
      } elseif ($Baris == 'All') {
        $Where = " WHERE QrRak = '$Qr' ";
      } else {
        $Where = " WHERE QrRak = '$Qr' AND SubRak = '$Baris' ";
      }
    } else {
      $Where = " WHERE QrSubRak = '$Qr' ";
    }

    //echo $Where; exit;

    $Sql      = "WITH LatestDates AS (
                  SELECT 
                    Rak, SubRak, QrSubRak, PartID, MAX(CreateDate) AS LatestCreateDate
                  FROM Trans_RakContents
                  $Where 
                  AND SoftDelete IS NULL
                  GROUP BY Rak, SubRak, QrSubRak, PartID
                )
                SELECT 
                  a.Id, a.Rak, a.SubRak, b.PartName, a.Stock, a.Unit, 
                  a.PartID, a.WHLokasi, a.Noted, a.QrRak, a.QrSubRak, a.CreateDate, a.CreateBy
                FROM Trans_RakContents a
                JOIN LatestDates ld ON a.Rak = ld.Rak 
                  AND a.SubRak = ld.SubRak
                  AND a.PartID = ld.PartID
                  AND a.CreateDate = ld.LatestCreateDate
                LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                ORDER BY a.CreateDate DESC";
                //echo $Sql; exit;
    $Query    = $this->BJGMAS01->query($Sql);
    $Result   = $Query->result();
    $Data     = [];
    $No       = 1;

    foreach ($Result as $key => $value) {
      $Edit     = "'".$value->Id."', '".$value->Rak."', '".$value->SubRak."', '".$value->PartID."', '".$value->PartName."', '".formatNumber($value->Stock)."', '".$value->Unit."', '".$value->WHLokasi."', '".$value->QrRak."', '".$value->QrSubRak."'";
      $Hapus    = "'".$value->Id."', '".$value->Rak."', '".$value->SubRak."', '".$value->PartID."', '".$value->PartName."'";
      $Transfer = "'".$value->Id."', '".$value->Rak."', '".$value->SubRak."', '".$value->PartID."', '".$value->PartName."', '".formatNumber($value->Stock)."', '".$value->Unit."', '".$value->QrRak."', '".$value->WHLokasi."'";
      $Data[] = array(
        $No++,
        '<a href="javascript:void(0)" onclick="edit_item('.$Edit.')"
          class="btn waves-effect waves-light btn-success btn-sm" title="Edit Item">
          <i class="fa fa-edit"></i>
        </a>
        <a href="javascript:void(0)" onclick="openModalDelete('.$Hapus.')"
          class="btn waves-effect waves-light btn-danger btn-sm" title="Hapus Item">
          <i class="fa fa-times"></i>
        </a>
        <a href="javascript:void(0)" onclick="transfer_item('.$Transfer.')"
          class="btn waves-effect waves-light btn-warning btn-sm" title="Transfer Item">
          <i class="fa fa-exchange"></i>
        </a>',
        $value->Rak,
        $value->SubRak,
        $value->PartName,
        number_format($value->Stock, 2),
        $value->Unit,
        $value->PartID,
        $value->WHLokasi,
        $value->Noted == null ? '-' : $value->Noted,
        substr($value->CreateDate, 0, 19),
        $value->CreateBy
      );
    }

    $Results = array(
      "draw"             => $Draw,
      "recordsTotal"     => $Query->num_rows(),
      "recordsFiltered"  => $Query->num_rows(),
      "data"             => $Data
    );

    echo json_encode($Results);
    exit();
  }

  //DONE
  public function transfer_item()
  {
    $this->_validation_item_transfer();

    $KodeItem     = $this->input->post('KodeItemTransfer');
    $Rak          = $this->input->post('RakTransfer');
    $QrRak        = $this->input->post('RakTransfer');
    $RakLabel     = $this->input->post('RakTransferLabel');
    $Baris        = $this->input->post('BarisTransferLabel');
    $QrSubRak     = $this->input->post('BarisTransfer');
    $PartID       = $this->input->post('PartIdTransfer');
    $PartName     = $this->input->post('PartNameTransfer');
    $Quantity     = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('QuantityStockTransfer'))));
    $Request      = floatval(str_replace(',', '.', str_replace('.', '', $this->input->post('QuantityTransfer'))));
    $Status       = $this->input->post('StatusTransfer');
    $Noted        = $this->input->post('NotedTransfer');
    $Unit         = $this->input->post('UnitTransfer');
    $WHLokasi     = $this->input->post('WHLokasiTransfer');
    $DataRakLama  = $this->BJGMAS01->get_where('Trans_RakContents', array('Id' => $KodeItem))->row();
    $RakLama      = $DataRakLama->Rak;
    $BarisLama    = $DataRakLama->SubRak;
    //echo json_encode(array('Quantity' => $Quantity, 'Request' => $Request)); exit;
    if ($Request > 0) {
      if ($Request > $Quantity) {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Stok tidak mencukupi. Stok tersedia: ".number_format($Quantity, 0)
          )
        );
        exit();
      } else {
        $Items      = $this->BJGMAS01->get_where('Trans_RakContents', array('Rak' => $RakLabel, 'SubRak' => $Baris, 'PartID' => $PartID));
        $CekItems   = $Items->num_rows();
        if ($CekItems == 0) {
          //$CekStock   = $Items->row();
          $QtyStock         = 0;
          $StockBaru        = $QtyStock + $Request;
          $StockLama        = $Quantity - $Request;
          $UpdateStockLama  = $Quantity - $Request;
          $StockEntryDate   = null;
          if (empty($DataRakLama->StockEntryDate)) {
            $StockEntryDate = date('Y-m-d', strtotime($DataRakLama->CreateDate));
          } else {
            $StockEntryDate = $DataRakLama->StockEntryDate;
          }
          //$StockBaru = $Quantity - $Request;
          $Log = array(
            'StockRakBaru'        => $QtyStock,
            'StockTransferan'     => $Request,
            'StockUpdateRakBaru'  => $StockBaru, 
            'StockRakLama'        => $Quantity,
            'StockUpdateRakLama'  => $StockLama,
            'PartID'              => $PartID,
            'PartName'            => $PartName,
            'RakBaru'             => $RakLabel,
            'BarisBaru'           => $Baris,
            'RakLama'             => $RakLama,
            'BarisLama'           => $BarisLama,
            'Status'              => "TRF"
          );

          $DataStockLama = array(
            'PartID'          => $PartID,
            'Status'          => 'OUT',
            'Quantity'        => $Request,
            'OldStock'        => $Quantity,
            'Stock'           => $StockLama,
            'Unit'            => $Unit,
            'WHLokasi'        => $WHLokasi,
            'LotNumber'       => $DataRakLama->LotNumber,
            'Noted'           => "Dipindahkan ke Rak ".$RakLabel." Baris ".$Baris." Sebanyak ".$Request." ".$Unit." di Tgl. ".date('Y-m-d'),
            'Rak'             => $DataRakLama->Rak,
            'QrRak'           => $DataRakLama->QrRak,
            'SubRak'          => $DataRakLama->SubRak,
            'QrSubRak'        => $DataRakLama->QrSubRak,
            'StockEntryDate'  => $StockEntryDate,
            'CreateDate'      => date('Y-m-d H:i:s'),
            'CreateBy'        => $this->session->userdata('user_code')
          );

          $DataTransfer = array(
            'PartID'          => $PartID,
            'Status'          => 'IN',
            'Quantity'        => $Request,
            'OldStock'        => $QtyStock,
            'Stock'           => $StockBaru,
            'Unit'            => $Unit,
            'WHLokasi'        => $WHLokasi,
            'LotNumber'       => $DataRakLama->LotNumber,
            'Noted'           => $Noted,
            'Rak'             => $RakLabel,
            'QrRak'           => $QrRak,
            'SubRak'          => $Baris,
            'QrSubRak'        => $QrSubRak,
            'StockEntryDate'  => $StockEntryDate,
            'CreateDate'      => date('Y-m-d H:i:s'),
            'CreateBy'        => $this->session->userdata('user_code')
          );

          //echo json_encode(array('StockLama' => $DataStockLama, 'StockTransfer' => $DataTransfer, 'Log' => $Log)); exit;

          //$Save = $this->BJGMAS01->insert('Trans_RakContents', $DataTransfer);
          $Save = $this->BJGMAS01->insert('Trans_RakContents', $DataStockLama);
          if ($Save) {
            //$Update = $this->BJGMAS01->insert('Trans_RakContents', $DataStockLama);
            $Update = $this->BJGMAS01->insert('Trans_RakContents', $DataTransfer);
            if ($Update) {
              $this->log_history($Log, 'TF');
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status"      => "success",
                  "message"     => "Data stock transfer sukses diupdate."
                )
              );
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Data stock transfer gagal ditransfer."
                )
              );
            }
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Data stock gagal ditransfer."
              )
            );
            exit();
          }
        } else {
          if ($Quantity == 0) {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Stok tersedia: ".number_format($Quantity, 0)." silahkan cek kembali."
              )
            );
            exit();
          } else {
            $CekStock   = $this->BJGMAS01->get_where('Trans_RakItems', array('Rak' => $RakLabel, 'SubRak' => $Baris, 'PartID' => $PartID))->row();
            $QtyStock   = floatval($CekStock->Quantity);
            $StockBaru  = $QtyStock + $Request;
            $StockLama  = $Quantity - $Request;
            $Log = array(
              'StockRakBaru'        => $QtyStock,
              'StockTransferan'     => $Request,
              'StockUpdateRakBaru'  => $StockBaru, 
              'StockRakLama'        => $Quantity,
              'StockUpdateRakLama'  => $StockLama,
              'PartID'              => $PartID,
              'PartName'            => $PartName,
              'RakBaru'             => $RakLabel,
              'BarisBaru'           => $Baris,
              'RakLama'             => $RakLama,
              'BarisLama'           => $BarisLama,
              'Status'              => "TF"
            );

            $DataStockLama = array(
              'Quantity'    => $StockLama,
              'Noted'       => "Dipindahkan ke Rak ".$RakLabel." Baris ".$Baris." Sebanyak ".$Request." ".$Unit." di Tgl. ".date('Y-m-d'),
              'UpdateDate'  => date('Y-m-d H:i:s'),
              'UpdateBy'    => $this->session->userdata('user_code')
            );

            $DataTransfer = array(
              'Rak'         => $RakLabel,
              'SubRak'      => $Baris,
              'PartID'      => $PartID,
              'Quantity'    => $Quantity,
              'Unit'        => $Unit,
              'WHLokasi'    => $WHLokasi,
              'Status'      => 'TF',
              'Noted'       => $Noted,
              'QrRak'       => $QrRak,
              'QrSubRak'    => $QrSubRak,
              'CreateDate'  => date('Y-m-d H:i:s'),
              'CreateBy'    => $this->session->userdata('user_code')
            );

            $UpdateDataTransfer = array(
              'Quantity'    => $StockBaru,
              'Noted'       => $Noted." Sebanyak: ".$Request,
              'UpdateDate'  => date('Y-m-d H:i:s'),
              'UpdateBy'    => $this->session->userdata('user_code')
            );

            $UpdateStockTransfer = $this->BJGMAS01->update('Trans_RakItems', $UpdateDataTransfer, array('Rak' => $RakLabel, 'SubRak' => $Baris, 'PartID' => $PartID));
            if ($UpdateStockTransfer) {
              $UpdateStockLama   = $this->BJGMAS01->update('Trans_RakItems', $DataStockLama, array('Id' => $KodeItem));
              if ($UpdateStockLama) {
                $this->log_history($Log, 'TF');
                echo json_encode(
                  array(
                    "status_code" => 200,
                    "status"      => "success",
                    "message"     => "Data stock transfer sukses diupdate."
                  )
                );
              } else {
                echo json_encode(
                  array(
                    "status_code" => 500,
                    "status"      => "error",
                    "message"     => "Data stock baru gagal ditransfer."
                  )
                );
              }
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Data stock baru gagal ditransfer."
                )
              );
              exit();
            }
          }
        }
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Silahkan isi quantity transfer lebih besar dari 0."
        )
      );
    }
  }

  //LOG HISTORY RAK
  public function log_history($JsonData, $Status)
  {
    $Data = array(
      'Data'        => json_encode($JsonData),
      'Status'      => $Status,
      'CreateDate'  => date('Y-m-d H:i:s'),
      'CreateBy'    => $this->session->userdata('user_code')
    );

    $this->BJGMAS01->insert('Trans_RakLog', $Data);
  }

  private function _validation_item()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Baris') == '') {
      $data['inputerror'][]   = 'Baris';
      $data['error_string'][] = 'Baris is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Quantity') == '') {
      $data['inputerror'][]   = 'Quantity';
      $data['error_string'][] = 'Quantity is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Rak') == '') {
      $data['inputerror'][]   = 'Rak';
      $data['error_string'][] = 'Rak is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('WHLokasi') == '') {
      $data['inputerror'][]   = 'WHLokasi';
      $data['error_string'][] = 'WH Lokasi is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_item_transfer()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('RakTransfer') == '') {
      $data['inputerror'][]   = 'RakTransfer';
      $data['error_string'][] = 'Rak Baru is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('BarisTransfer') == '') {
      $data['inputerror'][]   = 'BarisTransfer';
      $data['error_string'][] = 'Baris Baru is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('QuantityTransfer') == '') {
      $data['inputerror'][]   = 'QuantityTransfer';
      $data['error_string'][] = 'Quantity Transfer is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
