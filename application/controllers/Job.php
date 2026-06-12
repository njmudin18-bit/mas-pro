<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job extends CI_Controller
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

  //KIRIM TRANS JOB SECARA AUTOMATIC
  public function kirim_job_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        //START DATE
        $start_date       = date("Y-m-d"); //$this->input->post("start_date");
        $start_date_array = explode('-', $start_date);
        $start_year       = $start_date_array[0];
        $start_month      = $start_date_array[1];

        //END DATE
        $end_date         = date("Y-m-d"); //$this->input->post("end_date");
        $end_date_array   = explode('-', $end_date);
        $end_year         = $end_date_array[0];
        $end_month        = $end_date_array[1];

        if ($start_month == $end_month) {
          $table      = "Trans_Job".$start_year.$start_month;
          $sql 		    = "SELECT * FROM $table 
                         WHERE CAST(CreateDate as date) BETWEEN '$start_date' AND '$end_date'
                         ORDER BY CreateDate DESC";
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {
            $data[] = array(
              'NoBukti'         => $value->NoBukti,
              'NoPC'            => $value->NoPC,
              'Tgl'             => $value->Tgl,
              'Profit'          => $value->Profit,
              'DateNeed'        => $value->DateNeed,
              'DateBegin'       => $value->DateBegin,
              'DateFinish'      => $value->DateFinish,
              'PartID'          => $value->PartID,
              'Specification'   => $value->Specification,
              'UnitID'          => $value->UnitID,
              'QtyOrder'        => $value->QtyOrder,
              'QtyMPR'          => $value->QtyMPR,
              'QtyGood'         => $value->QtyGood,
              'QtyRepair'       => $value->QtyRepair,
              'QtyReject'       => $value->QtyReject,
              'QtyReturn'       => $value->QtyReturn,
              'FormulaID'       => $value->FormulaID,
              'Keterangan'      => $value->Keterangan,
              'Area'            => $value->Area,
              'Division'        => $value->Division,
              'F_Repair'        => $value->F_Repair,
              'F_Close'         => $value->F_Close,
              'CreateBy'        => $value->CreateBy,
              'CreateDate'      => $value->CreateDate,
              'F_Print'         => $value->F_Print,
              'F_Type'          => $value->F_Type,
              'NoPI'            => $value->NoPI,
              'WHProcess'       => $value->WHProcess,
              'WHResult'        => $value->WHResult,
              'QtyKanban'       => $value->QtyKanban,
              'Proses1'         => $value->Proses1,
              'Proses2'         => $value->Proses2,
              'NomerPo'         => $value->NomerPo,
              'CompanyCode'     => $value->CompanyCode
            );
          }

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'job/save_job_to_web',
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
              "status_code"   => 500,
              "status"        => "error",
              "message"       => "Start date month not same with end date month",
              "data"          => array()
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

  //KIRIM TRANS JOB SECARA MANUAL DENGAN POSTMAN
  public function kirim_job_manual() {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        //START DATE
        $start_date       = $this->input->post("start_date");
        $start_date_array = explode('-', $start_date);
        $start_year       = $start_date_array[0];
        $start_month      = $start_date_array[1];

        //END DATE
        $end_date         = $this->input->post("end_date");
        $end_date_array   = explode('-', $end_date);
        $end_year         = $end_date_array[0];
        $end_month        = $end_date_array[1];

        if ($start_month == $end_month) {
          $table      = "Trans_Job".$start_year.$start_month;
          $sql 		    = "SELECT * FROM $table 
                         WHERE CAST(CreateDate as date) BETWEEN '$start_date' AND '$end_date'
                         ORDER BY CreateDate DESC";
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {
            $data[] = array(
              'NoBukti'         => $value->NoBukti,
              'NoPC'            => $value->NoPC,
              'Tgl'             => $value->Tgl,
              'Profit'          => $value->Profit,
              'DateNeed'        => $value->DateNeed,
              'DateBegin'       => $value->DateBegin,
              'DateFinish'      => $value->DateFinish,
              'PartID'          => $value->PartID,
              'Specification'   => $value->Specification,
              'UnitID'          => $value->UnitID,
              'QtyOrder'        => $value->QtyOrder,
              'QtyMPR'          => $value->QtyMPR,
              'QtyGood'         => $value->QtyGood,
              'QtyRepair'       => $value->QtyRepair,
              'QtyReject'       => $value->QtyReject,
              'QtyReturn'       => $value->QtyReturn,
              'FormulaID'       => $value->FormulaID,
              'Keterangan'      => $value->Keterangan,
              'Area'            => $value->Area,
              'Division'        => $value->Division,
              'F_Repair'        => $value->F_Repair,
              'F_Close'         => $value->F_Close,
              'CreateBy'        => $value->CreateBy,
              'CreateDate'      => $value->CreateDate,
              'F_Print'         => $value->F_Print,
              'F_Type'          => $value->F_Type,
              'NoPI'            => $value->NoPI,
              'WHProcess'       => $value->WHProcess,
              'WHResult'        => $value->WHResult,
              'QtyKanban'       => $value->QtyKanban,
              'Proses1'         => $value->Proses1,
              'Proses2'         => $value->Proses2,
              'NomerPo'         => $value->NomerPo,
              'CompanyCode'     => $value->CompanyCode
            );
          }

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'job/save_job_to_web',
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
              "status_code"   => 500,
              "status"        => "error",
              "message"       => "Start date month not same with end date month",
              "data"          => array()
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
}