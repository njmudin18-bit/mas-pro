<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lhp_fg extends CI_Controller
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

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Rincian Finish Goods";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/lhp_fg', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  //HASIL SCAN WAREHOUSE DATA
  public function hasil_scan_wh_data() {

    $draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		//GET START AND END DATE
		$start_date     = $this->input->post('start_date');
		$end_date       = $this->input->post('end_date');
		$pilihan        = $this->input->post('pilihan');
    $start_array    = explode('-', $start_date);
    $end_array      = explode('-', $end_date);
    $tbl_trans_job  = " LEFT JOIN Trans_Job".$end_array[0].$end_array[1]." c ON c.NoBukti = a.NoBuktiJob ";
    $start_month    = $start_array[0].$start_array[1];
    $end_month      = $end_array[0].$end_array[1];
    $join_hd        = "";
    $join_dt        = "";
    $location_in    = "";
    
    for ($x = $start_month; $x <= $end_month; $x++) {
      $bulan  = $x;
      if ($end_month == $x) {
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$bulan ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan UNION ALL ";
      }
    }

    if ($pilihan == 'all') {
      $location_in = " a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 
                                        'WH-R', 'WH-R01', 'WH-WIP00') ";
    } else if($pilihan == 'pc') {
      $location_in = " a.LocationID IN ('WH-FG01', 'WH-GRS01') ";
    } else {
      $location_in = " a.LocationID IN ('WH-FG', 'WH-GRS00') ";
    }
    
    $sql = "SELECT 
              b.PartName, a.NoBuktiJob, a.PartID, 
              SUM(a.Qty) AS TOTAL_QTY_SCAN_WH, c.QtyOrder, a.LocationID,
              CAST(c.Tgl AS DATE) AS TGL_BUAT_JOB,
              COUNT(a.NoBuktiJob) AS QTY_SCAN_WH
            FROM (
              $join_dt
            ) a
            LEFT JOIN Ms_Part b ON b.PartID = a.PartID
            $tbl_trans_job
            LEFT JOIN (" . $join_hd . ") d ON d.NoBukti = a.NoBukti
            WHERE c.QtyOrder <> a.Qty 
              AND CAST(d.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
              AND $location_in
            GROUP BY a.NoBuktiJob, a.PartID, b.PartName, 
                  c.QtyOrder, a.LocationID, CAST(c.Tgl AS DATE)";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {
      $isi 		 = "'" . $value->NoBuktiJob . "', '" . $value->PartName . "', '".$value->PartID."', '".number_format($value->QtyOrder, 0)."'";
      $btn     = $value->QtyOrder == $value->TOTAL_QTY_SCAN_WH ? '<span class="badge badge-pill badge-success pull-right">COMPLETED</span>' : '<span class="badge badge-pill badge-danger pull-right">OPEN</span>';
			$data[]  = array(
        //$value->NoBuktiJob."|".number_format($value->TOTAL_QTY_SCAN_WH, 0)."|".$value->PartID."|".$value->PartName."|".number_format($value->QtyOrder, 0),
				$no++,
				$value->PartName,
        $value->NoBuktiJob.'<hr>Qty. Job : '.number_format($value->QtyOrder, 0).$btn.'<hr>'.$value->PartID,
        '<a class="text-danger" id="'.$key.'" onclick="cek_detail_transaksi('.$isi.')" style="font-size:.9375rem; cursor:pointer;" 
          title="Klik untuk detail">'.number_format($value->TOTAL_QTY_SCAN_WH, 0).'</a>',
        ''
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

  //CEK DETAIL TRANSAKSI
  public function cek_detail_transaksi() {
    $job_no         = $this->input->post('job_nomor');
    $start_date     = $this->input->post('start_date');
		$end_date       = $this->input->post('end_date');
    $start_array    = explode('-', $start_date);
    $end_array      = explode('-', $end_date);
    $start_month    = $start_array[0].$start_array[1];
    $end_month      = $end_array[0].$end_array[1];
    $join_hd        = "";
    $join_dt        = "";
    
    for ($x = $start_month; $x <= $end_month; $x++) {
      $bulan  = $x;
      if ($end_month == $x) {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH FROM Trans_BHPHD$bulan ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH FROM Trans_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan UNION ALL ";
      }
    }

    $sql  = "SELECT 
              a.NoBuktiJob, a.PartID, SUM(a.Qty) AS TOTAL_SCAN_WH, b.TGL_SCAN_WH 
            FROM (
              $join_dt
            ) a
            LEFT JOIN (
              $join_hd
            ) b ON b.NoBukti = a.NoBukti
            WHERE a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 
                                   'WH-R', 'WH-R01', 'WH-WIP00')
            AND a.NoBuktiJob = '$job_no'
            GROUP BY a.NoBuktiJob, a.PartID, b.TGL_SCAN_WH
            ORDER BY b.TGL_SCAN_WH DESC";
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
    $no           = 1;
    $content      = "";
    $footer       = "";
    $total        = 0;

    foreach ($result as $key => $value) {
      $total  = $total + floatval($value->TOTAL_SCAN_WH);
      $qtys   = floatval($value->TOTAL_SCAN_WH);
      $length = strlen(floatval($value->TOTAL_SCAN_WH));
      $isi    = "'".$value->NoBuktiJob."', '".$key."', '".$qtys."'";
      $content .=   '<tr>
                      <td class="text-right">'.$no++.'</td>
                      <td class="text-center">'.$value->TGL_SCAN_WH.'</td>
                      <td class="text-right">'.number_format($value->TOTAL_SCAN_WH, 0).'</td>
                      <td>
                        <form id="form_'.$key.'">
                          <div class="container">
                            <div class="row">
                              <div class="col-md-3">
                                <select id="shift_'.$key.'" name="shift_'.$key.'" class="form-control">
                                  <option selected disabled>-- Pilih Shift --</option>
                                  <option value="1">1</option>
                                  <option value="2">2</option>
                                  <option value="3">3</option>
                                </select>
                              </div>
                              <div class="col-md-3">
                                <input id="qty_'.$key.'" name="qty_'.$key.'" type="text" max="'.$qtys.'" maxlength="'.$length.'" minlength="0" class="form-control">
                              </div>
                              <div class="col-md-3">
                                <button type="button" onclick="save_data_shift('.$isi.')" id="button_'.$key.'" name="button_'.$key.'" class="btn btn-danger">Simpan</button>
                              </div>
                            </div>
                          </div>
                        </form>
                      </td>
                    </tr>';
      $footer   =   '<tr class="bg-primary">
                      <th class="text-right" colspan="2">TOTAL</th>
                      <th class="text-right">'.number_format($total, 0).'</th>
                      <th></th>
                    </tr>';

    }

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Sukses menampilkan data',
        'html'        => $content,
        'footer'      => $footer,
        'data'        => $result,
        'total'       => $total
      )
    );
  }

  //FUNCTION SIMPAN
  public function save_data_shift() {
    $job_nomor = $this->input->post('job_nomor');
    $qty_scan  = $this->input->post('qty_scaner_wh');
    $shift     = $this->input->post('shift_selected');
    $date      = date("Y-m-d");
    if ($shift == 1) {
      # code...
    } else {
      # code...
    }
    

    echo $job_nomor." - ".$qty_scan." - ".$shift;
  }

  public function save_data_shift_OLD() {
    $shift      = $this->input->post('shift_selected');
    $array_id   = $this->input->post('job_nomor');
    $array_data = array();
    foreach ($array_id as $key => $value) {
      $array_val    = explode('|', $value['value']);
      $qty_scan     = str_replace(',', '', $array_val[1]);
      $qty_order    = str_replace(',', '', $array_val[4]);
      $array_data[] = array(
        'nomor_job'     => $array_val[0],
        'qty_scan'      => floatval($qty_scan),
        'part_id'       => $array_val[2],
        'part_name'     => $array_val[3],
        'qty_order'     => floatval($qty_order),
        'shift'         => floatval($shift),
        'created_date'  => date('Y-m-d H:i:s'),
        'created_by'    => $this->session->userdata('user_code')
      );
    }

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $second_DB->trans_start();
    $insert     = $second_DB->insert_batch('tbl_lhp_fg', $array_data);
    $second_DB->trans_complete();

    if ($second_DB->trans_status() === FALSE) {
      echo json_encode(
        array(
          "status_code"   => 500,
          "status"        => "error",
          "message"       => "Data gagal disimpan!",
          "data"          => $array_data
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code"   => 200,
          "status"        => "success",
          "message"       => "Data sukses disimpan!",
          "data"          => $array_data
        )
      );
    }
  }
}