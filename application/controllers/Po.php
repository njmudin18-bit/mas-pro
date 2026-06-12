<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Po extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *        http://example.com/index.php/welcome
   *    - or -
   *        http://example.com/index.php/welcome/index
   *    - or -
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

    //$this->load->helper(array('url', 'form', 'cookie', ''));
    $this->load->library(array('session', 'cart'));

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    //END
  }

  //KIRIM PO AUTO
  function kirim_po_automatic()
  {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        //$start_date       = "2023-12-05";
        //$end_date         = "2023-12-05";

        $start_date       = date("Y-m-d");
        $end_date         = date("Y-m-d");

        $array_start_date = explode('-', $start_date);
        $array_end_date   = explode('-', $end_date);

        if ($array_start_date[1] == $array_end_date[1]) {
          $table_hd   = "Trans_POHD".$array_start_date[0].$array_start_date[1];
          $table_dt   = "Trans_PODT1".$array_start_date[0].$array_start_date[1];

          $sql 		    = "SELECT * FROM $table_hd 
                         WHERE CAST(CreateDate AS DATE) between '$start_date' AND '$end_date'
                         ORDER BY CreateDate DESC";
          //echo $sql; exit;
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {

            $sql_det = "SELECT * FROM $table_dt WHERE NoBukti = '$value->NoBukti'";

            $data[] = array(
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
              'CompanyCode'       => $value->CompanyCode,
              'Detail'            => $second_DB->query($sql_det)->result()
            );
          }

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'apis/save_po_to_web',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data, false),
            CURLOPT_HTTPHEADER => array(
              'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL'
            ),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          echo $response;

          //ADDING TO LOG
          $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
          $log_type 	= "API PO MAS";
          $log_data 	= json_encode($data);
          
          log_helper($log_url, $log_type, $log_data);
          //END LOG
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Tanggal yang anda masukan berbeda bulan. Harus ada dibulan yang sama.",
              "data"        => array()
            )
          );
        }

      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
  }

  //KIRIM PO MANUAL SECARA MANUAL DENGAN POSTMAN
  function kirim_po_manual()
  {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        $start_date       = $this->input->post('start_date');
        $end_date         = $this->input->post('end_date');

        $array_start_date = explode('-', $start_date);
        $array_end_date   = explode('-', $end_date);

        if ($array_start_date[1] == $array_end_date[1]) {
          $table_hd   = "Trans_POHD".$array_start_date[0].$array_start_date[1];
          $table_dt   = "Trans_PODT1".$array_start_date[0].$array_start_date[1];

          $sql 		    = "SELECT * FROM $table_hd 
                         WHERE CAST(CreateDate AS DATE) between '$start_date' AND '$end_date'
                         ORDER BY CreateDate DESC";
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {

            $sql_det = "SELECT * FROM $table_dt WHERE NoBukti = '$value->NoBukti'";

            $data[] = array(
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
              'CompanyCode'       => $value->CompanyCode,
              'Detail'            => $second_DB->query($sql_det)->result()
            );
          }

          //SEND PO DATA TO WEB
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'apis/save_po_to_web',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data, false),
            CURLOPT_HTTPHEADER => array(
              'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL'
            ),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          echo $response;

        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Tanggal yang anda masukan berbeda bulan. Harus ada dibulan yang sama.",
              "data"        => array()
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
  }

  //KIRIM MASTER PART AUTO
  public function kirim_master_part_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        $tanggal  = date("Y-m-d");
        $bulan    = date("m");
        $tahun    = date("Y");
        
        $sql 		    = "SELECT * FROM Ms_Part 
                       WHERE CAST(CreateDate as date) = '$tanggal'
                       AND MONTH(CreateDate) = '$bulan' 
                       AND YEAR(CreateDate) = '$tahun'
                       ORDER BY CreateDate DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
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

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_master_part_to_web',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data, false),
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
  }

  //KIRIM MASTER PART SECARA MANUAL DENGAN POSTMAN
  public function kirim_master_part_manual() {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        $tanggal    = $this->input->post("tanggal");
        $bulan      = $this->input->post("bulan");
        $tahun      = $this->input->post("tahun");
        
        $sql 		    = "SELECT * FROM Ms_Part 
                       WHERE CAST(CreateDate as date) = '$tanggal'
                       AND MONTH(CreateDate) = '$bulan' 
                       AND YEAR(CreateDate) = '$tahun'
                       ORDER BY CreateDate DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
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

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_master_part_to_web',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data, false),
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic bmptdWRpbkBvbWFzLW1mZy5jb206JDJ5JDEwJFBVcXhaLlZhekZWbzd5aVNuUzZQUU9wRHJnYUFrTmI3U2Q1VlJ0UzJxQ2lJTkRuTVJKWFJL'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

      } else {
        echo json_encode(
          array(
            "status_code"   => 401,
            "status"        => "Unauthorized",
            "message"       => "Restricted data",
            "data"          => array()
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"   => 405,
          "status"        => "Error",
          "message"       => "Method Not Allowed",
          "data"          => array()
        )
      );
    }
  }
}