<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc_report extends CI_Controller
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
			$data['group_halaman'] 	= "QC";
			$data['nama_halaman'] 	= "Laporan Hasil Inspeksi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/qc/laporan_qc', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function monitoring_job_list_new()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		//$jenis_part = $this->input->post('jenis_part');
		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');

		$year_1 		= date('Y', strtotime($start_date));
		$month_1 		= date('m', strtotime($start_date));
		$year_2 		= date('Y', strtotime($end_date));
		$month_2 		= date('m', strtotime($end_date));
		$left_join 	= "";
		$text_join 	= "";

		$interval   = (($year_2 - $year_1) * 12) + ($month_2 - $month_1) + 1;

		//GET LOOPING FOR LEFT JOIN TABLE TRANSJOB
		if ($month_1 == $month_2) {
			$tbl_trans_job 		= "Trans_Job" . $year_2 . $month_2; //Trans_Job202302
			$text_join 				= "LEFT JOIN $tbl_trans_job b on b.NoBukti = a.no_job";
		} else {

			for ($i = 0; $i < $interval; $i++) {
				$tempDate       = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' months'));
				$tempTableName  = date('Y', strtotime($tempDate)) . date('m', strtotime($tempDate));

				if ($i < $interval - 1) {
					$left_join 		.= " (SELECT NoBukti, Tgl, PartID, UnitID, QtyOrder, Keterangan, WHResult FROM Trans_Job$tempTableName) UNION ALL";
				} else {
					$left_join 		.= " (SELECT NoBukti, Tgl, PartID, UnitID, QtyOrder, Keterangan, WHResult FROM Trans_Job$tempTableName)";
				}
			}

			$text_join = " LEFT JOIN (" . $left_join . ") b ON b.NoBukti = a.no_job";
		}

		$sql 	= " SELECT a.no_job, a.loc_result, b.Tgl, b.PartID, b.UnitID, b.QtyOrder, 
							b.Keterangan, b.WHResult, c.PartName FROM
							(SELECT no_job, loc_result
							FROM tbl_scanbarcode_job 
							GROUP BY no_job, loc_result) a 
							$text_join
							LEFT JOIN Ms_Part c ON c.PartID = b.PartID
							WHERE CAST(b.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
							ORDER BY b.Tgl DESC";

		// $sql 	= " SELECT a.no_job, a.loc_result, b.Tgl, b.PartID, b.UnitID, b.QtyOrder, 
		// 					b.Keterangan, b.WHResult, c.PartName FROM
		// 					(SELECT no_job, loc_result
		// 					FROM tbl_scanbarcode_job 
		// 					GROUP BY no_job, loc_result) a 
		// 					$left_join
		// 					LEFT JOIN Ms_Part c ON c.PartID = b.PartID
		// 					WHERE CAST(b.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
		// 					ORDER BY b.Tgl DESC";
		// echo $sql;
		// exit;

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
		$status_job 	= "";
		$qty_order 		= 0;
		$total_qty 		= 0;
		$total_qty_wh = 0;
		$sisa_qty 		= 0;
		$bulan 				= 0;
		$tahun				= 0;

		foreach ($result as $key => $value) {
			$no_job 		= $value->no_job;
			$qty_order 	= floatval($value->QtyOrder);

			//GET QTY JOB IN WH
			$tgl_job 			= explode('-', $value->Tgl);
			$tahun 				= $tgl_job[0];
			$bulan 				= $tgl_job[1];
			$wh_data 			= get_total_qty_jobs_wh_new($no_job, $bulan, $tahun);
			$total_qty_wh = floatval($wh_data[0]->jlh_qty_wh);
			$sisa_qty 		= $qty_order - $total_qty_wh;

			//CEK PERNAH REJECT KAH?
			$data_reject 	= cek_pernah_reject($no_job);
			//print_r($data_reject); echo "<br>";
			if ($data_reject > 0) {
				$text_reject = '<h5><span class="badge badge-warning text-white">YA</span></h5>';
			} else {
				$text_reject = '<h5><span class="badge badge-info">TIDAK</span></h5>';
			}

			$data[] = array(
				$no++,
				$value->no_job,
				substr($value->Tgl, 0, -4),
				$value->PartID,
				$value->PartName,
				$value->UnitID,
				number_format($qty_order, 0),
				number_format($sisa_qty, 0),
				$value->loc_result, //$value->WHResult,
				$value->Keterangan == '' ? '-' : $value->Keterangan,
				$text_reject,
				$sisa_qty == 0 ? '<button class="btn btn-success btn-block btn-sm">COMPLETED</button>' : '<button class="btn btn-warning btn-block text-white btn-sm">OPEN</button>',
				'<a href="' . base_url() . 'qc_report/monitoring_job_details/' . base64_encode($no_job) . '/' . $bulan . '/' . $tahun . '" target="_blank"><button class="btn btn-danger btn-block btn-sm">DETAILS</button></a>'
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

	public function monitoring_job_list_new_OLD()
	{

		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$now 				= date("Y-m-d");

		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');
		$jenis_part = $this->input->post('jenis_part');

		$new_bulan    = 0;
		if (strlen($bulan) == 1) {
			$new_bulan  = "0" . $bulan;
		} else {
			$new_bulan  = $bulan;
		}

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$WH_table 	= "Trans_BHPDT" . $tahun . $new_bulan; //Trans_BHPDT202212
		$PPIC_table = "Trans_Job" . $tahun . $new_bulan; //Trans_Job202301

		if ($jenis_part == 'All') {
			$WHERE = "WHERE MONTH(b.Tgl) = " . $bulan . " AND YEAR(b.Tgl) = " . $tahun;
		} elseif ($jenis_part == 'Power Cord') {
			$WHERE = "WHERE MONTH(b.Tgl) = " . $bulan . " AND YEAR(b.Tgl) = " . $tahun . " AND WHResult IN ('WH-FG01', 'WH-GRS01')";
		} else {
			$WHERE = "WHERE MONTH(b.Tgl) = " . $bulan . " AND YEAR(b.Tgl) = " . $tahun . " AND WHResult IN ('WH-FG', 'WH-GRS00')";
		}

		$sql 				=  "SELECT a.no_job, b.Tgl, b.PartID, b.UnitID, b.QtyOrder, 
										b.Keterangan, b.WHResult, c.PartName FROM 
										(SELECT SUBSTRING(barcode_no, 6, 19) AS no_job
										FROM tbl_scanbarcode_job 
										GROUP BY SUBSTRING(barcode_no, 6, 19)) a
										LEFT JOIN $PPIC_table b on b.NoBukti = a.no_job
										LEFT JOIN Ms_Part c ON c.PartID = b.PartID
										$WHERE
										ORDER BY no_job DESC";

		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
		$status_job 	= "";
		$qty_order 		= 0;
		$total_qty 		= 0;
		$total_qty_wh = 0;
		$sisa_qty 		= 0;
		$text_reject 	= "";

		foreach ($result as $key => $value) {
			$no_job 		= $value->no_job;
			$qty_order 	= floatval($value->QtyOrder);

			//GET QTY JOB IN WH
			$wh_data 			= get_total_qty_jobs_wh($no_job, $bulan, $tahun);
			$total_qty_wh = floatval($wh_data[0]->jlh_qty_wh);
			$sisa_qty 		= $qty_order - $total_qty_wh;

			//CEK PERNAH REJECT KAH?
			$data_reject 	= cek_pernah_reject($no_job);
			//print_r($data_reject); echo "<br>";
			if ($data_reject > 0) {
				$text_reject = '<h5><span class="badge badge-warning text-white">YA</span></h5>';
			} else {
				$text_reject = '<h5><span class="badge badge-info">TIDAK</span></h5>';
			}

			$data[] = array(
				$no++,
				$value->no_job,
				substr($value->Tgl, 0, -4),
				$value->PartID,
				$value->PartName,
				$value->UnitID,
				number_format($qty_order, 0),
				number_format($sisa_qty, 0), //$qty_order." - ".$total_qty_wh." = ",
				$value->WHResult,
				$value->Keterangan == '' ? '-' : $value->Keterangan,
				$text_reject,
				$sisa_qty == 0 ? '<button class="btn btn-success btn-block btn-sm">COMPLETED</button>' : '<button class="btn btn-warning btn-block text-white btn-sm">OPEN</button>',
				'<a href="' . base_url() . 'qc_report/monitoring_job_details/' . base64_encode($no_job) . '/' . $bulan . '/' . $tahun . '" target="_blank"><button class="btn btn-danger btn-block btn-sm">DETAILS</button></a>'
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

	//FUNGSI MONITORING JOB DETAILS
	public function monitoring_job_details($id, $bulan, $tahun)
	{
		//echo $bulan.'-'.$tahun.'-';
		//echo base64_decode($id); exit();
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Laporan Hasil Inspeksi Details";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['no_job'] 				= base64_decode($id);
			$data['bulan'] 					= $bulan;
			$data['tahun'] 					= $tahun;
			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/qc/laporan_qc_details', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function monitoring_job_details_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$now 				= date("Y-m-d");
		$year 			= date("Y");
		$month 			= date("m");

		$no_job 		= $this->input->post('no_job');
		//echo $no_job . "<br>";
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql 		= " SELECT a.scan_id AS scan_prod_id, a.barcode_no AS barcode_no_prod, 
								a.loc_id AS scan_loc_prod, 
								a.scan_status AS scan_status_prod, a.scan_date AS scan_date_prod, 
								a.scan_by AS scan_by_prod, 
								b.scan_id AS scan_qc_id, b.barcode_no AS barcode_no_qc, b.loc_id AS scan_loc_qc, 
								b.scan_status AS scan_status_qc, b.scan_date AS scan_date_qc, 
								b.scan_by AS scan_by_qc, b.scan_update_date AS scan_date_after_reject 
								FROM 
								(SELECT scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by 
								FROM tbl_scanbarcode_job 
								WHERE loc_id = 'PR001' 
								GROUP BY scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by) a 
								LEFT JOIN 
								(SELECT scan_id, barcode_no, loc_id, scan_status, scan_date, 
								scan_by, scan_update_date 
								FROM tbl_scanbarcode_job 
								WHERE loc_id = 'QC001'
								GROUP BY scan_id, barcode_no, loc_id, scan_status, scan_date, 
								scan_by, scan_update_date) b
								ON a.barcode_no = b.barcode_no
								WHERE a.barcode_no LIKE '%$no_job%'
								ORDER BY a.barcode_no"; //ORDER BY a.scan_id DESC, b.scan_date DESC

		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
		$status_qc 		= "";
		$wh_scan_loc 	= "";
		$wh_time_scan = "";
		$wh_scan_by 	= "";
		$no_kartu 		= "";
		$barcode_qty 	= 0;
		$user_united 	= "";

		foreach ($result as $key => $value) {

			//SET NO KARTU
			// $isi_kartu_array 	= explode('|', $value->barcode_no_prod);
			// $no_job_array 		= explode('-', $isi_kartu_array[1]);
			// print_r($no_job_array[0]);
			// echo " baru<br>";
			$no_kartu 		= substr($value->barcode_no_prod, 1, 27);
			//echo $no_kartu;
			//exit;
			//exit;
			$job_data 		= get_job_history($no_job, $bulan, $tahun); //get part name dll
			$job_wh_data 	= get_wh_history($no_job, $no_kartu, $bulan, $tahun); //get WH data like loc id, time dll
			//echo count($job_wh_data); exit;

			if (count($job_wh_data) > 0) {
				$wh_scan_loc 	= $job_wh_data[0]->LocationID;
				$wh_time_scan = substr($job_wh_data[0]->CreateDate, 0, -4);
				$wh_scan_by 	= $job_wh_data[0]->CreateBy;
			} else {
				$wh_scan_loc 	= "-";
				$wh_time_scan = "-";
				$wh_scan_by 	= "-";
			}

			if ($value->scan_status_qc == 'OK') {
				$status_qc = '<h5><span class="badge badge-success">' . $value->scan_status_qc . '</span></h5>';
			} elseif ($value->scan_status_qc == 'NG') {
				$status_qc = '<h5><span class="badge badge-danger">' . $value->scan_status_qc . '</span></h5>';
			} else {
				$status_qc = '-';
			}

			//get qty bacode
			$barcode_qty = explode("|", $value->barcode_no_prod);
			//get user united
			/*$user_united_data = get_user_united($wh_scan_by);
    	//print_r($user_united_data); exit();
    	if (count($user_united_data) > 0) {
    		$user_united = $user_united_data[0]->NAME;
    	} else {
    		$user_united = "-";
    	}*/

			$data[] = array(
				$no++,
				$job_data->PartID,
				$job_data->PartName,
				$job_data->UnitID,
				number_format($barcode_qty[7]),
				'<h5><span class="badge badge-info">' . $value->scan_loc_prod . '</span></h5>',
				substr($value->scan_date_prod, 0, -4),
				$value->scan_by_prod,
				$value->scan_loc_qc == NULL ? '-' : '<h5><span class="badge badge-warning text-white">' . $value->scan_loc_qc . '</span></h5>',
				$value->scan_date_qc == NULL ? '-' : substr($value->scan_date_qc, 0, -4),
				$value->scan_by_qc == NULL ? '-' : $value->scan_by_qc,
				$status_qc,
				$wh_scan_loc,
				$wh_time_scan,
				$wh_scan_by, //$user_united, //$wh_scan_by,
				$value->barcode_no_prod,
				$value->scan_date_after_reject == '' ? '-' : '<button class="btn btn-danger btn-block btn-sm" onclick="open_details_reject(' . $value->scan_qc_id . ')">DETAIL NG</button>'
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
}
