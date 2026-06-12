<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Return_product extends CI_Controller
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

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    //END
  }

  //KIRIM DATA SCAN BARCODE PPIC AUTO
  public function kirim_scan_barcode_ppic_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        
        $tanggal    = date("Y-m-d");
        
        $sql 		    = "SELECT * FROM tbl_scanbarcode_job 
                       WHERE CAST(scan_date as DATE) = '$tanggal' ORDER BY scan_date DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'scan_id'           => $value->scan_id,
            'barcode_no'        => $value->barcode_no,
            'no_job'            => $value->no_job,
            'loc_id'            => $value->loc_id,
            'qty_job'           => $value->qty_job,
            'qty_box'           => $value->qty_box,
            'loc_result'        => $value->loc_result,
            'scan_status'       => $value->scan_status,
            'scan_date'         => $value->scan_date,
            'scan_by'           => $value->scan_by,
            'scan_update_date'  => $value->scan_update_date,
            'scan_update_by'    => $value->scan_update_by
          );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_barcode_ppic_to_web',
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

  //KIRIM DATA SCAN BARCODE PPIC MANUAL DENGAN POSTMAN
  public function kirim_scan_barcode_ppic_manual() {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        //VALIDATION
        $this->_validation_();

        $tanggal    = $this->input->post("tanggal");
        
        $sql 		    = "SELECT * FROM tbl_scanbarcode_job 
                       WHERE CAST(scan_date as DATE) = '$tanggal' ORDER BY scan_date DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'scan_id'           => $value->scan_id,
            'barcode_no'        => $value->barcode_no,
            'no_job'            => $value->no_job,
            'loc_id'            => $value->loc_id,
            'qty_job'           => $value->qty_job,
            'qty_box'           => $value->qty_box,
            'loc_result'        => $value->loc_result,
            'scan_status'       => $value->scan_status,
            'scan_date'         => $value->scan_date,
            'scan_by'           => $value->scan_by,
            'scan_update_date'  => $value->scan_update_date,
            'scan_update_by'    => $value->scan_update_by
          );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_barcode_ppic_to_web',
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

  //KIRIM DATA SCAN BARCODE PPIC MANUAL DENGAN POSTMAN
  public function kirim_scan_barcode_sales_manual() {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        //VALIDATION
        $this->_validation_();

        $tanggal    = $this->input->post("tanggal");
        
        $sql 		    = "SELECT * FROM tbl_scanbarcode_approval 
                       WHERE CAST(create_date as DATE) = '$tanggal' ORDER BY create_date DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'id'              => $value->id,
            'barcode_id'      => $value->barcode_id,
            'no_po'           => $value->no_po,
            'no_do'           => $value->no_do,
            'part_id'         => $value->part_id,
            'qty_order'       => $value->qty_order,
            'nama_customer'   => $value->nama_customer,
            'nama_driver'     => $value->nama_driver,
            'no_polisi'       => $value->no_polisi,
            'lokasi_id'       => $value->lokasi_id,
            'lokasi_scan'     => $value->lokasi_scan,
            'approved_by'     => $value->approved_by,
            'create_date'     => $value->create_date
          );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_barcode_sales_to_web',
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
          "message"       => "Method Not AllowedXX",
          "data"          => array()
        )
      );
    }
  }

  //KIRIM DATA SCAN BARCODE PPIC AUTOMATIC DENGAN POSTMAN
  public function kirim_scan_barcode_sales_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        $tanggal    = date("Y-m-d");
        
        $sql 		    = "SELECT * FROM tbl_scanbarcode_approval 
                       WHERE CAST(create_date as DATE) = '$tanggal' ORDER BY create_date DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'id'              => $value->id,
            'barcode_id'      => $value->barcode_id,
            'no_po'           => $value->no_po,
            'no_do'           => $value->no_do,
            'part_id'         => $value->part_id,
            'qty_order'       => $value->qty_order,
            'nama_customer'   => $value->nama_customer,
            'nama_driver'     => $value->nama_driver,
            'no_polisi'       => $value->no_polisi,
            'lokasi_id'       => $value->lokasi_id,
            'lokasi_scan'     => $value->lokasi_scan,
            'approved_by'     => $value->approved_by,
            'create_date'     => $value->create_date
          );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_barcode_sales_to_web',
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

  //KIRIM DATA DO HEADER DAN DETAIL MANUAL
  public function kirim_data_do_manual() {
    $method = $this->input->method(TRUE);
    if ($method == 'POST') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {
        //VALIDATION
        $this->_validation_month_year();
        $month        = $this->input->post('month'); //date("Y-m-d");
        $year         = $this->input->post('year'); //date("Y-m-d");

        if ($month != $year) {
          $table_hd   = "Trans_SJHD".$year.$month;
          $table_dt   = "Trans_SJDT".$year.$month;

          $sql 		    = "SELECT * FROM $table_hd ORDER BY CreateDate DESC";
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {
            $sql_det = "SELECT * FROM $table_dt WHERE NoBukti = '$value->NoBukti'";

            $data[] = array(
              'NoBukti'           => $value->NoBukti,
              'NoReff'            => $value->NoReff,
              'NoPlanning'        => $value->NoPlanning,
              'NoSeri'            => $value->NoSeri,
              'TglFaktur'         => $value->TglFaktur,
              'TGL'               => $value->TGL,
              'ReceiverID'        => $value->ReceiverID,
              'ShipmentID'        => $value->ShipmentID,
              'TipeBC'            => $value->TipeBC,
              'NoBC'              => $value->NoBC,
              'Tgl_BC'            => $value->Tgl_BC,
              'Tgl_BPB'           => $value->Tgl_BPB,
              'MataUang'          => $value->MataUang,
              'NilaiTukar'        => $value->NilaiTukar,
              'NilaiTukarPajak'   => $value->NilaiTukarPajak,
              'PPN'               => $value->PPN,
              'Term'              => $value->Term,
              'NoPlatMobil'       => $value->NoPlatMobil,
              'NamaSupir'         => $value->NamaSupir,
              'JurnalID'          => $value->JurnalID,
              'Keterangan'        => $value->Keterangan,
              'TypeTrans'         => $value->TypeTrans,
              'Discount'          => $value->Discount,
              'NoStuffing'        => $value->NoStuffing,
              'ContainerNo'       => $value->ContainerNo,
              'ContainerSeal'     => $value->ContainerSeal,
              'ExpNo'             => $value->ExpNo,
              'ExpName'           => $value->ExpName,
              'F_Print'           => $value->F_Print,
              'IsScan'            => $value->IsScan,
              'DoCustomer'        => $value->DoCustomer,
              'CreateBy'          => $value->CreateBy,
              'CreateDate'        => $value->CreateDate,
              'CompanyCode'       => $value->CompanyCode,
              'MonthYear'         => $month."-".$year,
              'Detail'            => $second_DB->query($sql_det)->result()
            );
          };

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'apis/save_do_to_web',
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
          $log_type 	= "API DO MAS";
          $log_data 	= json_encode($data);
          
          log_helper($log_url, $log_type, $log_data);
          //END LOG
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Anda memasukan nilai yang sama.",
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

  //KIRIM DATA DO HEADER DAN DETAIL AUTOMATIC
  public function kirim_data_do_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {

        $month        = "12"; //date('m');
        $year         = "2023"; //date('Y');

        if ($month != $year) {
          $table_hd   = "Trans_SJHD".$year.$month;
          $table_dt   = "Trans_SJDT".$year.$month;

          $sql 		    = "SELECT * FROM $table_hd ORDER BY CreateDate DESC";
          $second_DB  = $this->load->database('bjsmas01_db', TRUE);
          $query 			= $second_DB->query($sql);
          $result 		= $query->result();
          $data       = array();

          foreach ($result as $key => $value) {
            $sql_det = "SELECT * FROM $table_dt WHERE NoBukti = '$value->NoBukti'";

            $data[] = array(
              'NoBukti'           => $value->NoBukti,
              'NoReff'            => $value->NoReff,
              'NoPlanning'        => $value->NoPlanning,
              'NoSeri'            => $value->NoSeri,
              'TglFaktur'         => $value->TglFaktur,
              'TGL'               => $value->TGL,
              'ReceiverID'        => $value->ReceiverID,
              'ShipmentID'        => $value->ShipmentID,
              'TipeBC'            => $value->TipeBC,
              'NoBC'              => $value->NoBC,
              'Tgl_BC'            => $value->Tgl_BC,
              'Tgl_BPB'           => $value->Tgl_BPB,
              'MataUang'          => $value->MataUang,
              'NilaiTukar'        => $value->NilaiTukar,
              'NilaiTukarPajak'   => $value->NilaiTukarPajak,
              'PPN'               => $value->PPN,
              'Term'              => $value->Term,
              'NoPlatMobil'       => $value->NoPlatMobil,
              'NamaSupir'         => $value->NamaSupir,
              'JurnalID'          => $value->JurnalID,
              'Keterangan'        => $value->Keterangan,
              'TypeTrans'         => $value->TypeTrans,
              'Discount'          => $value->Discount,
              'NoStuffing'        => $value->NoStuffing,
              'ContainerNo'       => $value->ContainerNo,
              'ContainerSeal'     => $value->ContainerSeal,
              'ExpNo'             => $value->ExpNo,
              'ExpName'           => $value->ExpName,
              'F_Print'           => $value->F_Print,
              'IsScan'            => $value->IsScan,
              'DoCustomer'        => $value->DoCustomer,
              'CreateBy'          => $value->CreateBy,
              'CreateDate'        => $value->CreateDate,
              'CompanyCode'       => $value->CompanyCode,
              'MonthYear'         => $month."-".$year,
              'Detail'            => $second_DB->query($sql_det)->result()
            );
          };

          //echo json_encode(array('data' => $data)); exit;

          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => API_PO.'apis/save_do_to_web',
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
          $log_type 	= "API DO MAS";
          $log_data 	= json_encode($data);
          
          log_helper($log_url, $log_type, $log_data);
          //END LOG
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Anda memasukan nilai yang sama.",
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

  //KIRIM DATA MASTER PARTNER AUTOMATIC
  public function kirim_master_partner_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {

        $sql 		    = "SELECT * FROM Ms_Partner ORDER BY CreateDate DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'PartnerID'               => $value->PartnerID,
            'PartnerName'             => $value->PartnerName,
            'Type'                    => $value->Type,
            'Address'                 => $value->Address,
            'City'                    => $value->City,
            'Country'                 => $value->Country,
            'Contact'                 => $value->Contact,
            'Phone'                   => $value->Phone,
            'Street'                  => $value->Street,
            'Block'                   => $value->Block,
            'Number'                  => $value->Number,
            'Neighbourhood'           => $value->Neighbourhood,
            'Hamlet'                  => $value->Hamlet,
            'District'                => $value->District,
            'AdministrativeVillage'   => $value->AdministrativeVillage,
            'Regency'                 => $value->Regency,
            'Province'                => $value->Province,
            'Postcode'                => $value->Postcode,
            'Fax'                     => $value->Fax,
            'Email'                   => $value->Email,
            'Website'                 => $value->Website,
            'Telex'                   => $value->Telex,
            'NPWP'                    => $value->NPWP,
            'PKPNO'                   => $value->PKPNO,
            'PKPDATE'                 => $value->PKPDATE,
            'isImport'                => $value->isImport,
            'BankAccountName'         => $value->BankAccountName,
            'BankAccountNo'           => $value->BankAccountNo,
            'BankName'                => $value->BankName,
            'BankAddress'             => $value->BankAddress,
            'SWIFT'                   => $value->SWIFT,
            'Corresponding'           => $value->Corresponding,
            'TypePartner'             => $value->TypePartner,
            'Currency'                => $value->Currency,
            'CurrencyPO'              => $value->CurrencyPO,
            'PaymentType'             => $value->PaymentType,
            'CreditLimit'             => $value->CreditLimit,
            'Term'                    => $value->Term,
            'ExpiryDay'               => $value->ExpiryDay,
            'TipePPN'                 => $value->TipePPN,
            'Notes'                   => $value->Notes,
            'Aktif'                   => $value->Aktif,
            'CreateDate'              => $value->CreateDate,
            'CreateBy'                => $value->CreateBy,
            'CompanyCode'             => $value->CompanyCode
          );
        };

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_master_partner_to_web',
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

        // //ADDING TO LOG
        $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
        $log_type 	= "API PARTNER MAS";
        $log_data 	= json_encode($data);
        
        log_helper($log_url, $log_type, $log_data);
        //END LOG
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

  //KIRIM DATA MASTER CUSTOMER ALAMAT KIRIM
  public function kirim_master_customer_alamat_kirim_automatic() {
    $method = $this->input->method(TRUE);
    if ($method == 'GET') {
      //GET AND SET USER & PASS FROM CURL
      $username = $this->input->server('PHP_AUTH_USER');
      $password = $this->input->server('PHP_AUTH_PW');

      if ($username == 'njmudin@omas-mfg.com' && $password == '$2y$10$PUqxZ.VazFVo7yiSnS6PQOpDrgaAkNb7Sd5VRtS2qCiINDnMRJXRK') {

        $sql 		    = "SELECT * FROM Ms_CustomerAlamatKirim ORDER BY CreateDate DESC";
        $second_DB  = $this->load->database('bjsmas01_db', TRUE);
        $query 			= $second_DB->query($sql);
        $result 		= $query->result();
        $data       = array();

        foreach ($result as $key => $value) {
          $data[] = array(
            'CustomerID'        => $value->CustomerID,
            'NamaPenerima'      => $value->NamaPenerima,
            'CustomerIDAlamat'  => $value->CustomerIDAlamat,
            'Alamat'            => $value->Alamat,
            'City'              => $value->City,
            'Contact'           => $value->Contact,
            'Phone'             => $value->Phone,
            'Keterangan'        => $value->Keterangan,
            'Pajak'             => $value->Pajak,
            'Aktif'             => $value->Aktif,
            'Koneksi'           => $value->Koneksi,
            'CreateDate'        => $value->CreateDate,
            'CreateBy'          => $value->CreateBy,
            'KdLokasi'          => $value->KdLokasi,
            'kdWilayah'         => $value->kdWilayah,
            'KdCabang'          => $value->KdCabang,
            'KodeKirim'         => $value->KodeKirim,
            'AlamatPrint'       => $value->AlamatPrint,
            'WithCompanyCode'   => $value->WithCompanyCode,
            'CompanyCode'       => $value->CompanyCode
          );
        };

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => API_PO.'apis/save_master_customer_alamat_kirim_to_web',
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

        // //ADDING TO LOG
        $log_url 		= base_url().$this->contoller_name."/".$this->function_name;
        $log_type 	= "API CUSTOMER ALAMAT KIRIM MAS";
        $log_data 	= json_encode($data);
        
        log_helper($log_url, $log_type, $log_data);
        //END LOG
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

  private function _validation_(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('tanggal') == '')
		{
			$data['inputerror'][] = 'tanggal';
			$data['error_string'][] = 'Tangal transaksi is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

  private function _validation_month_year(){
		$data 								= array();
		$data['error_string'] = array();
		$data['inputerror'] 	= array();
		$data['status'] 			= TRUE;

		if($this->input->post('month') == '')
		{
			$data['inputerror'][]   = 'month';
			$data['error_string'][] = 'Bulan is required';
			$data['status']         = FALSE;
		}

    if($this->input->post('year') == '')
		{
			$data['inputerror'][]   = 'year';
			$data['error_string'][] = 'Tahun is required';
			$data['status']         = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}