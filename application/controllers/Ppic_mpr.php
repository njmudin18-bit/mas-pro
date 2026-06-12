<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppic_mpr extends CI_Controller {

	public function __construct() {
    parent::__construct();
    
    //start tidak usah di ubah
    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END


    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    //end tidak usah di ubah

    $this->load->model('barcode_model', 'barcode_sales');
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if($check_permission->num_rows() == 1){
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "PPIC MPR";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/ppic/monitoring_mpr', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function lihat_status()
  {
    $NoBukti 		= $this->input->post('nobukti');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $ppic_mpr_tbl 	= "tbl_monitoring_mpr";
    $sql = "SELECT id, NoBukti, JobDate, PartID, PartName, Qty, Keterangan, CreateBy, CreateDate, loc_id
            FROM $ppic_mpr_tbl 
            WHERE NoBukti = '$NoBukti'
            ORDER BY CreateDate DESC";
            
    $query 				= $second_DB->query($sql);
    $result        = $query->result(); // gunakan result untuk banyak data
    
    if (count($result) > 0) {
		 		 $text = '';
         $status = '';

          foreach ($result as $key => $value) {
            $dept =$value->loc_id;
            $date=substr($value->CreateDate,0,-4);
            $tanggal=substr($date,0,10);
            $jam=substr($date,11,19);

              if ($dept == "PPIC001"){
                $dept = "PPIC";
                $status = "dikirim";
              }elseif($dept == "WH001"){
                $dept = "WAREHOUSE";
                $status = "diproses";
              }elseif($dept == "WH002"){
                $dept = "WAREHOUSE";
                $status = "dikirim";
              }elseif($dept == "PR001"){
                $dept = "PRODUKSI";
                $status = "diterima";
              }

            $text .= '<ul class="timeline" id="timeline" >
                      <li class="event" data-date="'.$tanggal.'">
                      <h3>'.$dept.'</h3>
                      <p>'.$value->NoBukti.' dengan jobdate <span class="font-weight-bold">'.substr($value->JobDate,0,10).'</span>. telah <span class="font-weight-bold">'. $status.'</span> 
                      oleh '.$value->CreateBy.', Barang sudah '. $status.' pada jam : <span class="font-weight-bold">'.$jam.'</span></p>
                      </li>
                      </ul>';
          }
      echo json_encode(
        array(
          "status_code" => 200,
          "status" 		=> "success",
          "message" 	=> "sukses menampilkan data",
          "data" 			=> $result,
          "html" 			=> $text
        )
      );
      
    } else{
      echo json_encode(
        array(
          "status_code" => 409,
          "status" 		=> "error",
          "message" 	=> "data blm ada",
          "data" 			=> array(),
          "html" 			=> '<h3>Data tidak ada</h3>'
        )
      );
    }


  }
  
  function kirim_mpr(){
    $nobukti 		= $this->input->post('nobukti');
    $tahun 		  = $this->input->post('tahun');
    $bulan 		  = $this->input->post('bulan');
    $loc_id 		= $this->input->post('loc_id');
    $pilihan_wh = $this->input->post('pilihan_wh');
    
    $new_bulan    = 0;
    if (strlen($bulan) == 1) {
      $new_bulan  = "0".$bulan;
    } else {
      $new_bulan  = $bulan;
    }

    $second_DB            = $this->load->database('bjsmas01_db', TRUE);
    $ppic_mpr_tbl 	      = "Trans_MPRHD".$tahun.$new_bulan;
    $ppic_mpr_tbl_detail 	= "Trans_MPRDT".$tahun.$new_bulan;
    $sql = "SELECT A.NoBukti, Tgl AS JobDate, A.PartID,B.PartName,A.Qty, A.Keterangan , A.CreateBy, A.CreateDate 
            FROM $ppic_mpr_tbl A 
            LEFT JOIN Ms_Part B
            ON A.PartID = B.PartID
            WHERE A.NoBukti = '$nobukti'";

    $sql_detail = "SELECT A.NoBukti,  A.PartID, B.PartName, A.Qty, A.UnitID , A.LocationID, A.Keterangan
                    FROM $ppic_mpr_tbl_detail A 
                    LEFT JOIN Ms_Part B
                    ON A.PartID = B.PartID
                    WHERE A.NoBukti = '$nobukti'";
            
    $query 				= $second_DB->query($sql);
    $result 			= $query->row(); //gunakan row untuk 1 data saja
    $data 				= [];

    $query_detail 	= $second_DB->query($sql_detail);
    $result_detail 	= $query_detail->result(); //gunakan row untuk 1 data saja
    $data_detail 		= array();

    $StatusDetail = 0;
    foreach ($result_detail as $key => $value) {
      $data_detail[] = array(
        'NoBukti' => $value->NoBukti,
        'PartID' => $value->PartID,
        'PartName' => $value->PartName,
        'Qty' => (double)$value->Qty, 
        'UnitID' => $value->UnitID,
        'LocationID' => $value->LocationID,
        'Keterangan' => $value->Keterangan,
        'StatusPPIC' => $StatusDetail,
        'StatusWH' => $StatusDetail,
        'StatusPR' => $StatusDetail,
        'CreateByPPIC' =>$this->session->userdata('user_name'),
        'CreateDatePPIC' => date('Y-m-d H:i:s')
      );
    }

    $NoBukti = $result->NoBukti; //gunakan row untuk 1 data saja
    $JobDate = substr($result->JobDate,0,-4);
    $PartID = $result->PartID;
    $PartName = $result->PartName;
    $Qty = floatval($result->Qty);
    $Keterangan = $result->Keterangan;

    $data = array(
      'NoBukti' => $NoBukti,
      'JobDate' => $JobDate,
      'PartID' => $PartID,
      'PartName' => $PartName,
      'Qty' => (double)$Qty,
      'Keterangan' => $Keterangan,
      'CreateBy' =>$this->session->userdata('user_name'),
      'CreateDate' => date('Y-m-d H:i:s'),
      'loc_id' => $loc_id,
      'pilih_wh'=>$pilihan_wh 
    );

    $query 			= $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                      WHERE NoBukti = '$NoBukti' AND loc_id = 'PPIC001'");
    $cek 				= $query->num_rows();

    if ($cek == 0) {

          $second_DB->trans_start();
          $insert = $second_DB->insert('tbl_monitoring_mpr', $data);
          $second_DB->trans_complete();

          $second_DB->trans_start();
          $insert = $second_DB->insert_batch('tbl_monitoring_mpr_detail', $data_detail);
          $second_DB->trans_complete();

          if ($second_DB->trans_status() === FALSE) {
            echo json_encode(
              array(
                "status_code" => 400,
                "status" 			=> "error",
                "message" 		=> "MPR gagal dikirim!",
                "data" 				=> $data
              )
            );
          } else {
            echo json_encode(
              array(
                "status_code" => 200,
                "status" 			=> "success",
                "message" 		=> "MPR sukses dikirim!",
                "data" 				=> $data
              )
            );
          }
    }else{
      echo json_encode(
        array(
          "status_code" => 409,
          "status" 			=> "success",
          "message" 		=> "MPR sudah dikirim!",
          "data" 				=> $data
        )
      );
    } 
  }

  //KIRIM MPR MULTIPLE
  public function kirim_mpr_multiple() {
    $no_bukti_array   = json_decode($this->input->post('no_bukti_array'));
    $wh_pilihan 		  = $this->input->post('wh_pilihan');
    $loc_id 		      = $this->input->post('loc_id');
    $second_DB        = $this->load->database('bjsmas01_db', TRUE);
    $data_header      = array();
    $data_detail 		  = array();
    $StatusDetail     = 0;

    foreach ($no_bukti_array as $key => $value) {
      $array_data           = explode("-", $value->value);
      $mpr_no               = $array_data[0];
      $mpr_tahun            = $array_data[1];
      $mpr_bulan            = $array_data[2];
      $ppic_mpr_tbl 	      = "Trans_MPRHD".$mpr_tahun.$mpr_bulan;
      $ppic_mpr_tbl_detail 	= "Trans_MPRDT".$mpr_tahun.$mpr_bulan;

      //CEK SUDAH ADA ATAU BELUM
      $query 			= $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                       WHERE NoBukti = '$mpr_no' AND loc_id = '$loc_id'");
      $cek 				= $query->num_rows();
      if ($cek == 0) { // GANTI DISINI KLO SUDAH SELESAI MENJADI == 0
        //GET DATA HEADER
        $sql_head     = " SELECT A.NoBukti, Tgl AS JobDate, A.PartID, B.PartName, A.Qty, 
                          A.Keterangan, A.CreateBy, A.CreateDate 
                          FROM $ppic_mpr_tbl A 
                          LEFT JOIN Ms_Part B ON A.PartID = B.PartID
                          WHERE A.NoBukti = '$mpr_no'";
        $query_head 	= $second_DB->query($sql_head);
        $result_head	= $query_head->result();
        //LOOP HEAD
        foreach ($result_head as $key => $value) {
          //SAVE TO VAR
          $data_header[] = array(
            'NoBukti'     => $value->NoBukti,
            'JobDate'     => $value->JobDate,
            'PartID'      => $value->PartID,
            'PartName'    => $value->PartName,
            'Qty'         => (double)$value->Qty,
            'Keterangan'  => $value->Keterangan,
            'CreateBy'    => $this->session->userdata('user_name'),
            'CreateDate'  => date('Y-m-d H:i:s'),
            'loc_id'      => $loc_id,
            'pilih_wh'    => $wh_pilihan 
          );
        }

        //GET DATA DETAIL
        $sql_detail   = " SELECT A.NoBukti, A.PartID, B.PartName, A.Qty, A.UnitID, 
                          A.LocationID, A.Keterangan
                          FROM $ppic_mpr_tbl_detail A 
                          LEFT JOIN Ms_Part B
                          ON A.PartID = B.PartID
                          WHERE A.NoBukti = '$mpr_no'";
        $query_detail = $second_DB->query($sql_detail);
        $res_detail 	= $query_detail->result();
        //LOOP DETAIL
        foreach ($res_detail as $key => $value) {
          //SAVE TO VAR
          $data_detail[] = array(
            'NoBukti'         => $value->NoBukti,
            'PartID'          => $value->PartID,
            'PartName'        => $value->PartName,
            'Qty'             => (double)$value->Qty, 
            'UnitID'          => $value->UnitID,
            'LocationID'      => $value->LocationID,
            'Keterangan'      => $value->Keterangan,
            'StatusPPIC'      => $StatusDetail,
            'StatusWH'        => $StatusDetail,
            'StatusPR'        => $StatusDetail,
            'CreateByPPIC'    => $this->session->userdata('user_name'),
            'CreateDatePPIC'  => date('Y-m-d H:i:s')
          );
        }
      }
    }

    //print_r($data_header);
    //echo "<br>";
    //print_r($data_detail);
    $save_head = $second_DB->insert_batch('tbl_monitoring_mpr', $data_header);
    if ($save_head) {
      $save_detail = $second_DB->insert_batch('tbl_monitoring_mpr_detail', $data_detail);
      if ($save_detail) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status" 			=> "success",
            "message" 		=> "MPR sukses disimpan dan dikirim",
            "header"      => $data_header,
            "detail" 			=> $data_detail
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 400,
            "status" 			=> "error",
            "message" 		=> "Gagal menyimpan data detail!",
            "data" 				=> $data_detail
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 400,
          "status" 			=> "error",
          "message" 		=> "Gagal menyimpan data header!",
          "data" 				=> $data_header
        )
      );
    }
  }

  //GANTI WH MPR
  public function ganti_wh_mpr() {  
    $wh_pilihan   = $this->input->post('wh_pilihan');
    $loc_id 		  = $this->input->post('loc_id');
    $part_id      = $this->input->post('part_id');
    $array_data   = explode('-', $this->input->post('no_bukti'));
    $no_bukti     = $array_data[0];

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    $sql          = "UPDATE tbl_monitoring_mpr SET pilih_wh = '$wh_pilihan'
                     WHERE NoBukti = '$no_bukti' AND PartID = '$part_id' AND loc_id = '$loc_id'";
    $update       = $second_DB->query($sql);
    if ($update) {
      echo json_encode(
        array(
          "status_code" => 200,
          "status" 			=> "success",
          "message" 		=> "Sukses mengganti WH MPR No. ".$no_bukti,
          "data"        => $array_data
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 400,
          "status" 			=> "error",
          "message" 		=> "Gagal mengganti WH MPR No. ".$no_bukti,
          "data" 				=> $array_data
        )
      );
    }
  }

  public function monitoring_mpr_list_new()
	{

		$draw 			= intval($this->input->get("draw"));
    $start 			= intval($this->input->get("start"));
    $length 		= intval($this->input->get("length"));

  	$start_date = $this->input->post('start_date');
  	$end_date 	= $this->input->post('end_date');

    $year1  = date('Y', strtotime($start_date));
    $year2  = date('Y', strtotime($end_date));
    $month1 = date('m', strtotime($start_date));
    $month2 = date('m', strtotime($end_date));
    
    $interval   = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
    $sql        = '';

    for($i = 0 ; $i < $interval; $i++){
      $tempDate       = date('Y-m-d', strtotime($start_date. ' + '.$i.' months'));
      $tempTableName  = date('Y', strtotime($tempDate)). date('m',strtotime($tempDate));
    
      if ($i<$interval-1){
        $sql .= "SELECT NoBukti, Tgl AS JobDate, PartID,Qty, Keterangan , CreateBy,  CreateDate 
                    FROM Trans_MPRHD$tempTableName
                    UNION ALL ";
        }else{
        $sql .= "SELECT NoBukti, Tgl AS JobDate, PartID,Qty, Keterangan , CreateBy,  CreateDate 
                    FROM Trans_MPRHD$tempTableName";
        }
    }
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $sql_awal     = 'SELECT A.NoBukti, CAST(JobDate AS date) as JobDate, A.PartID, B.PartName, 
                      A.Qty, A.Keterangan , A.CreateBy, CAST(A.CreateDate AS date) as Tgl, 
                      C.jumlah_mpr, C.pilih_wh
                      FROM (';
    $left_join    = ' )A 
                      LEFT JOIN Ms_Part B ON A.PartID = B.PartID
                      LEFT JOIN (SELECT NoBukti, COUNT(NoBukti) AS jumlah_mpr, pilih_wh FROM tbl_monitoring_mpr GROUP BY pilih_wh, NoBukti ) C ON A.NoBukti = C.NoBukti';
    $where        = " WHERE  CAST(A.CreateDate AS date) BETWEEN '$start_date' AND '$end_date'";
		$sql_new      = $sql_awal.$sql.$left_join.$where;

    //echo $sql_new; exit;

    $query 				= $second_DB->query($sql_new);
    $result 			= $query->result();
    $data 				= [];
    $no 					= 1;

    foreach ($result as $key => $value) {
        $noBuktiMpr = "'".$value->NoBukti."'";
        $jumlah_mpr = $value->jumlah_mpr == null ? 0 : $value->jumlah_mpr;

        $status = '';
        if($jumlah_mpr == 4){
          $status = '<span class="badge badge-success" style="font-size: 14px;">COMPLETE</span>
                      <br>
                      <span style="font-size: 12px;">'.$jumlah_mpr.' dari 4 </span>';
        }else{
          $status = '<span class="badge badge-danger" style="font-size: 14px;">OPEN</span>
                      <br>
                      <span style="font-size: 12px;">'.$jumlah_mpr.' dari 4 </span>';
        }

        $tgl  = explode('-', $value->JobDate);
        $kirimTahun = "'".$tgl[0]."'";
        $kirimBulan = "'".$tgl[1]."'";
        $data[] = array(
          $value->NoBukti."-".$tgl[0]."-".$tgl[1],
          $no++,
          $value->NoBukti,
          $value->JobDate,
          // $tgl[1],
          $value->PartID,
          $value->PartName,
          number_format($value->Qty,0),
          $value->Keterangan,
          $value->pilih_wh == '' ? '-' : $value->pilih_wh,
          $value->CreateBy,
          $value->Tgl,
          $status,
          '<button type="button" class="btn btn-warning btn-block text-white btn-sm" onclick="modalwhmpr('.$noBuktiMpr.','.$kirimTahun.','.$kirimBulan.')">KIRIM</button> ',
          '<button type="button" class="btn btn-danger btn-block text-white btn-sm" onclick="lihat_status('.$noBuktiMpr.')">DETAIL STATUS</button>',
          '<button type="button" class="btn btn-danger btn-block text-white btn-sm" onclick="deleteMpr('.$noBuktiMpr.')">HAPUS</button>',
          $value->pilih_wh
        );
    }

    $result = array(
      "draw" 						=> $draw,
      "recordsTotal" 		=> $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data" 						=> $data
    );

    echo json_encode($result);
	}

  //FUNCTION HAPUS DATA MPR
	public function deleteMpr()
	{
		$nobukti 		= $this->input->post('nobukti');
    // echo $NoBukti;exit;

		// CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $second_DB            = $this->load->database('bjsmas01_db', TRUE);
      $sql = "SELECT id, NoBukti, JobDate, PartID, PartName, Qty, Keterangan, CreateBy, CreateDate,loc_id  
              FROM tbl_monitoring_mpr
              WHERE NoBukti = '$nobukti'";

    $sql_detail = "SELECT id, NoBukti, PartID, PartName, Qty, StandartPacking, StockSimpan, QtySisaProduksi, UnitID, LocationID, Keterangan, StatusPPIC, CreatebyPPIC, CreatedatePPIC, StatusWH,CreatebyWH, CreatedateWH, StatusPR, CreatebyPR, CreatedatePR 
                  FROM tbl_monitoring_mpr_detail
                  WHERE NoBukti = '$nobukti'";
            
    $query 				= $second_DB->query($sql);
    $result 			= $query->result(); 
    $data 				= array();

    $query_detail 	= $second_DB->query($sql_detail);
    $result_detail 	= $query_detail->result(); 
    $data_detail 		= array();

    foreach ($result as $key => $value) {
      $data[] = array(
        'id' => $value->id,
        'NoBukti' => $value->NoBukti,
        'JobDate' => $value->JobDate,
        'PartID' => $value->PartID, 
        'PartName' => $value->PartName,
        'Qty' => $value->Qty,
        'Keterangan' => $value->Keterangan,
        'CreateBy' => $value->CreateBy,
        'CreateDate' => $value->CreateDate,
        'loc_id' => $value->loc_id,
        'deleteBy' =>$this->session->userdata('user_name'),
        'deleteDate' => date('Y-m-d H:i:s')
      );
    }

    foreach ($result_detail as $key => $value) {
      $data_detail[] = array(
        'id' => $value->id,
        'NoBukti' => $value->NoBukti,
        'PartID' => $value->PartID,
        'PartName' => $value->PartName, 
        'Qty' => $value->Qty,
        'StandartPacking' => $value->StandartPacking,
        'StockSimpan' => $value->StockSimpan,  
        'QtySisaProduksi' => $value->QtySisaProduksi,
        'UnitID' => $value->UnitID, 
        'LocationID' => $value->LocationID,
        'Keterangan' => $value->Keterangan,
        'StatusPPIC' => $value->StatusPPIC,
        'CreatebyPPIC' => $value->CreatebyPPIC,
        'CreatedatePPIC' => $value->CreatedatePPIC,
        'StatusWH' => $value->StatusWH,
        'CreatebyWH' => $value->CreatebyWH,
        'CreatedateWH' => $value->CreatedateWH,
        'StatusPR' => $value->StatusPR,
        'CreatebyPR' => $value->CreatebyPR,
        'CreatedatePR' => $value->CreatedatePR,
        'deleteBy' =>$this->session->userdata('user_name'),
        'deleteDate' => date('Y-m-d H:i:s')
      );
    }

    if(empty($data) && empty($data_detail)){
      echo json_encode(
        array(
          "status_code" => 200,
          "status" 			=> "error",
          "message" 		=> "MPR blm dikirim!",
          "nobukti" 		=> $nobukti
        )
      );
    }else{
      
      $dataHapus = array(
        'status' => 'dataMPR',
        'data_header' => $data,
        'data_detail' => $data_detail
      );

      //ADDING TO LOG
        $log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
        $log_type 	= "DELETE";
        $log_data 	= json_encode($dataHapus);

        log_helper($log_url, $log_type, $log_data);
        // END LOG

        $deleteHeader = $second_DB->query("DELETE FROM tbl_monitoring_mpr WHERE nobukti = '$nobukti'");
        if($deleteHeader){
          $deleteDetail = $second_DB->query("DELETE FROM tbl_monitoring_mpr_detail WHERE nobukti = '$nobukti'");
            if($deleteDetail){
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status" 			=> "success",
                  "message" 		=> "STATUS MPR sukses dihapus!",
                  "nobukti" 		=> $nobukti
                )
              );
            } else {
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status" 			=> "error",
                  "message" 		=> "STATUS MPR gagal dihapus!",
                  "nobukti" 		=> $nobukti
                )
              );
            }
        }else{
          echo json_encode(
            array(
              "status_code" => 200,
              "status" 			=> "error",
              "message" 		=> "MPR STATUS gagal dihapus!",
              "nobukti" 		=> $nobukti
            )
          );
        }
      }

		} else {
			echo json_encode(array("status" => "forbidden"));
		}
	}
}