<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi_mpr extends CI_Controller {

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
			$data['group_halaman'] 	= "PRODUKSI";
			$data['nama_halaman'] 	= "PRODUKSI MPR";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/monitoring_mpr_produksi', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}


  public function ceklis_update()
  {
    $id 		= $this->input->post('id');
    $value 		= $this->input->post('value');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $isi_ceklis = $value == 0 ? '1' : '0' ;
    $data = array(
      'StatusPR' => $isi_ceklis,
      'CreateByPR' =>$this->session->userdata('user_name'),
      'CreateDatePR' => date('Y-m-d H:i:s')
    );

    $second_DB->where('id', $id);
    $second_DB->update('tbl_monitoring_mpr_detail', $data); 
    $sql = "SELECT NoBukti
            FROM tbl_monitoring_mpr_detail
            WHERE id = '$id'";
            
    $query 				= $second_DB->query($sql);
    $result 			= $query->row(); 

    echo json_encode(
      array(        "status_code" => 200,
        "status" 		=> "success",
        "message" 	=> "sukses menampilkan data",
        "data" 			=> $data,
        "nobukti"   => $result->NoBukti
      )
    );

  }

  // public function mpr_detail()
  // {
  //   $nobukti 		= $this->input->post('nobukti');
  //   $trans = explode('/',$nobukti);
  //   $tahunbulan = $trans[2];
  //   $second_DB  = $this->load->database('bjsmas01_db', TRUE);

            
  //   $sql = "SELECT a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking as QtyStd, a.StockSimpan, SUM(c.Qty) as Stock, a.UnitID, a.LocationID, a.Keterangan, a.StatusPR  
  //           FROM tbl_monitoring_mpr_detail a
  //           LEFT JOIN tbl_standart_packing b on b.PartID = a.PartID and b.location = a.LocationID
  //           LEFT JOIN Buku_Stock$tahunbulan c ON c.PartID = a.PartID
  //           WHERE a.NoBukti = '$nobukti'
  //           GROUP BY a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking, a.StockSimpan, a.UnitID, a.LocationID, a.Keterangan, a.StatusPR";
            
  //   $query 				= $second_DB->query($sql);
  //   $result        = $query->result(); 
  //   $no 					= 1;

  //   if (count($result) > 0) {
	// 	 		 $text = '';
  //     foreach ($result as $key => $value) {
  //       $coret = $value->StatusPR == 1 ? 'class_coret': '';
  //       $pro ='';

  //       if ($value->StockSimpan == 0){
  //         $stock = $value->Stock;
  //       }else{
  //         $stock = $value->StockSimpan;
  //       }

  //       $nilai1 = $value->Qty;
  //       $nilai2 = $value->QtyStd;
  //       $nilai3 = $stock;
  //       $hasil  = 0;
  //       if ($nilai2 > $nilai3){
  //           $hasil = $nilai3- $nilai1;
  //       }else{
  //         if ($nilai2 == null){
  //           $hasil = 0;
  //         }else{
  //           if($nilai1 > $nilai2)
  //           {
  //             if($nilai1 % $nilai2 != 0){
  //               $hasil = $nilai2 - ($nilai1 % $nilai2);
  //             }
  //           }else{
  //             $hasil = $nilai2- $nilai1;
  //           }
  //         }
  //       }

  //       // $isiStock = "'".$value->id."', '".$value->QtyStd."', '".$stock."', '".$hasil."'";
  //       $isiStock = "'".$value->id."'";

  //       if ($value->StatusPR == 1 ){
  //         $pro = '<input type="checkbox"  name="ceklis" id="ceklis'.$value->id.'" value="'.$value->StatusPR.'" onclick="ceklis('.$isiStock.')" checked="checked">';
  //       }else{
  //         $pro = '<input type="checkbox"  name="ceklis" id="ceklis'.$value->id.'" value="'.$value->StatusPR.'" onclick="ceklis('.$isiStock.')" >';
  //       }

  //       if($hasil < 0){
  //         $pro1 = '<td class="text-right text-danger">'.number_format($hasil).'</td>' ;
  //       }else{
  //         $pro1 = '<td class="text-right ">'.number_format($hasil).'</td>' ;
  //       }
        

  //       $text .= '
  //       <div class="to-do-label">
  //         <div class="checkbox-fade fade-in-primary">
  //           <label class="check-task">'.$pro.'
  //             <span class="cr">
  //             <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
  //             </span>
  //             <span class="task-title-sp '.$coret.'">'.$value->PartName.'</span>
  //           </label>
  //         </div>
  //       </div>
  //       <table class="table table-striped table-responsive">
  //         <thead>
  //           <tr>
  //             <th>No.</th>
  //             <th>PartID</th>
  //             <th>Qty</th>
  //             <th>Standart Packing</th>
  //             <th>Stock</th>
  //             <th>Qty Sisa Produksi</th>
  //             <th>Unit ID</th>
  //             <th>Location ID</th>
  //           </tr>
  //         </thead>
  //         <tbody>
  //           <tr>
  //             <td>'.$no++.'</td>
  //             <td>'.$value->PartID.'</td>
  //             <td class="text-right">'.number_format((int)$value->Qty).'</td>
  //             <td class="text-right">'.number_format((int)$value->QtyStd).'</td>
  //             <td class="text-right">'.number_format($stock).'</td>'.$pro1.'
  //             <td>'.$value->UnitID.'</td>
  //             <td>'.$value->LocationID.'</td>
  //           </tr>
  //         </tbody>
  //       </table>
  //       <br>
  //       ';
  //     }

  //     echo json_encode(
  //       array(
  //         "status_code" => 200,
  //         "status" 		=> "success",
  //         "message" 	=> "sukses menampilkan data",
  //         "data" 			=> $result,
  //         "html" 			=> $text
  //       )
  //     );
      
  //   } else{
  //     echo json_encode(
  //       array(
  //         "status_code" => 409,
  //         "status" 		=> "error",
  //         "message" 	=> "data blm ada",
  //         "data" 			=> array(),
  //         "html" 			=> '<h3>Data tidak ada</h3>'
  //        )
  //     );
  //   }

  // }
  public function mpr_detail()
  {
    $nobukti 		= $this->input->post('nobukti');
    $trans = explode('/',$nobukti);
    $tahunbulan = $trans[2];
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

            
    $sql = "SELECT a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking AS QtyStd, a.StockSimpan, SUM(c.Qty) AS Stock, a.UnitID, a.LocationID, a.Keterangan, a.StatusPR  
            FROM tbl_monitoring_mpr_detail a
            LEFT JOIN tbl_standart_packing b ON b.PartID = a.PartID AND b.location = a.LocationID
            LEFT JOIN Buku_Stock$tahunbulan c ON c.PartID = a.PartID AND c.locationID = a.LocationID AND c.nobukti <> a.nobukti
            WHERE a.NoBukti = '$nobukti'
            GROUP BY a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking, a.StockSimpan, a.UnitID, a.LocationID, a.Keterangan, a.StatusPR
            ORDER BY a.LocationID DESC
            ";
            
    $query 				= $second_DB->query($sql);
    $result       = $query->result(); 
    $no 					= 1;

    if (count($result) > 0) {
		 		 $text = '';
      foreach ($result as $key => $value) {
        $coret = $value->StatusPR == 1 ? 'class_coret': '';
        $pro ='';

        if ($value->StockSimpan == 0){
          $stock = $value->Stock;
        }else{
          $stock = $value->StockSimpan;
        }

        if($value->QtyStd == null) {
          $QtyStandart = 0;
        }else{
          $QtyStandart = $value->QtyStd;
        }
        
        $nilai1 = $value->Qty;
        // $nilai2 = $value->QtyStd;
        $nilai2 = $QtyStandart;
        $nilai3 = $stock;
        $hasil  = 0;
        if ($nilai2 > $nilai3){
            $hasil = $nilai3- $nilai1;
        }else{
          if ($nilai2 == null){
            $hasil = 0;
          }else{
            if($nilai1 > $nilai2)
            {
              if($nilai1 % $nilai2 != 0){
                $hasil = $nilai2 - ($nilai1 % $nilai2);
              }
            }else{
              $hasil = $nilai2- $nilai1;
            }
          }
        }

        // $isiStock = "'".$value->id."', '".$value->QtyStd."', '".$stock."', '".$hasil."'";
        $isiStock = "'".$value->id."', '".$QtyStandart."', '".$stock."', '".$hasil."'";
        if ($value->StatusPR == 1 ){
          $pro = '<input type="checkbox"  name="ceklis" id="ceklis'.$value->id.'" value="'.$value->StatusPR.'" onclick="ceklis('.$isiStock.')" checked="checked">';
        }else{
          $pro = '<input type="checkbox"  name="ceklis" id="ceklis'.$value->id.'" value="'.$value->StatusPR.'" onclick="ceklis('.$isiStock.')" >';
        }

        if($hasil < 0){
          $pro1 = '<td class="text-right text-danger">'.$hasil.'</td>' ;
        }else{
          $pro1 = '<td class="text-right ">'.$hasil.'</td>' ;
        }
        

        $text .= '
        <div class="to-do-label">
          <div class="checkbox-fade fade-in-primary">
            <label class="check-task">'.$pro.'
              <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
              </span>
              <span class="task-title-sp '.$coret.'">'.$value->PartName.'</span>
            </label>
          </div>
        </div>
        <table class="table table-striped table-responsive">
          <thead>
            <tr>
              <th>No.</th>
              <th>PartID</th>
              <th>Qty</th>
              <th>Standart Packing</th>
              <th>Stock</th>
              <th>Qty Sisa Produksi</th>
              <th>Unit ID</th>
              <th>Location ID</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>'.$no++.'</td>
              <td>'.$value->PartID.'</td>
              <td class="text-right">'.number_format($value->Qty,2).'</td>
              <td class="text-right">'.$QtyStandart.'</td>
              <td class="text-right">'.number_format($stock,2).'</td>'.$pro1.'
              <td>'.$value->UnitID.'</td>
              <td>'.$value->LocationID.'</td>
            </tr>
          </tbody>
        </table>
        <br>
        ';
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


  public function monitoring_mpr_list_new()
	{

		$draw 			= intval($this->input->get("draw"));
    $start 			= intval($this->input->get("start"));
    $length 		= intval($this->input->get("length"));

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $start_date = $this->input->post('start_date');
  	$end_date 	= $this->input->post('end_date');


    $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan, CreateBy, CreateDate, loc_id
    FROM tbl_monitoring_mpr
    WHERE CAST(CreateDate AS date) BETWEEN '$start_date' AND '$end_date'  AND loc_id='WH002'
    ORDER BY CreateDate DESC";

    $query 				= $second_DB->query($sql);
    $result 			= $query->result();
    $data 				= [];
    $no 					= 1;
    
    foreach ($result as $key => $value) {
      $noBuktiMpr = "'".$value->NoBukti."'";
      $jumlah_mpr = get_qty_mpr($value->NoBukti);
      $stepnew    = cek_qty($value->NoBukti);

      $status   = '';
      if($jumlah_mpr->jumlah_mpr == 4){
        $status = '<span class="badge badge-success" style="font-size: 14px;">COMPLETE</span>
                    <br>
                    <span style="font-size: 12px;">'.$stepnew.' dari 4 </span>';
      }else{
        $status = '<span class="badge badge-success" style="font-size: 14px;">OPEN</span>
                    <br>
                    <span style="font-size: 12px;">'.$stepnew.' dari 4 </span>';
      }
        $data[] = array(
          $no++,
          '<button class="btn btn-info btn-block text-white btn-sm" onclick="mpr_detail('.$noBuktiMpr.')">'.$value->NoBukti.'</button>',
          substr($value->JobDate,0,-4),
          $value->PartID,
          $value->PartName,
          $value->Qty,
          $value->Keterangan,
          $value->CreateBy,
          substr($value->CreateDate,0,-4),
          $status,
          '<button class="btn btn-warning btn-block text-white btn-sm" onclick="terima_mpr_produksi('.$noBuktiMpr.')">TERIMA</button>',
          '<button class="btn btn-danger btn-block text-white btn-sm" onclick="lihat_status('.$noBuktiMpr.')">DETAILS STATUS</button>'      
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

  public function terima_mpr_produksi()
  {
    $nobukti 	= $this->input->post('nobukti');
    // $bulan 		= $this->input->post('bulan');
    $loc_id 	= $this->input->post('loc_id');

    // echo "$nobukti $tahun $bulan $loc_id";
    // $new_bulan    = 0;
	  // if (strlen($bulan) == 1) {
	  //   $new_bulan  = "0".$bulan;
	  // } else {
	  //   $new_bulan  = $bulan;
	  // }

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan 
            FROM tbl_monitoring_mpr 
            WHERE NoBukti = '$nobukti'";
            
    $query 				= $second_DB->query($sql);
    $result 			= $query->row(); //gunakan row untuk 1 data saja
    $data 				= [];

    $NoBukti      = $result->NoBukti; //gunakan row untuk 1 data saja
    $JobDate      = substr($result->JobDate,0,-4);
    $PartID       = $result->PartID;
    $PartName     = $result->PartName;
    $Qty          = $result->Qty;
    $Keterangan   = $result->Keterangan;

    $data = array(
      'NoBukti' => $NoBukti,
      'JobDate' => $JobDate,
      'PartID' => $PartID,
      'PartName' => $PartName,
      'Qty' => (int)$Qty,
      'Keterangan' => $Keterangan,
      'CreateBy' =>$this->session->userdata('user_name'),
      'CreateDate' => date('Y-m-d H:i:s'),
      'loc_id' => $loc_id

    );

    $query_wh1 			= $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                          WHERE NoBukti = '$NoBukti' AND loc_id = 'WH002'");
    $cek_wh1				= $query_wh1->num_rows();

    if($cek_wh1==1){
      $query 			= $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                        WHERE NoBukti = '$NoBukti' AND loc_id = 'PR001'");
      $cek 				= $query->num_rows();

      if ($cek == 0) {
          $second_DB->trans_start();
          $insert = $second_DB->insert('tbl_monitoring_mpr', $data);
          $second_DB->trans_complete();
  
        if ($second_DB->trans_status() === FALSE) {
          echo json_encode(
            array(
              "status_code" => 400,
              "status" 			=> "error",
              "message" 		=> "MPR gagal diterima!",
              "data" 				=> $data
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 200,
              "status" 			=> "success",
              "message" 		=> "MPR sukses diterima!",
              "data" 				=> $data
            )
          );
        }
      }else{
        echo json_encode(
          array(
            "status_code" => 409,
            "status" 			=> "success",
            "message" 		=> "MPR sudah diterima!",
            "data" 				=> $data
          )
        );
      }
    }else{
      echo json_encode(
        array(
          "status_code" => 405,
          "status" 			=> "error",
          "message" 		=> "MPR blm dikirim Warehouse!",
          "data" 				=> $data
        )
      );
    }
    
  }

}


