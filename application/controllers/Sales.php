<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends CI_Controller
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

			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Monitoring Sales";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/sales/monitoring_sales', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function monitoring_sales_list_new()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql 	 			= "SELECT 
										CASE WHEN SUM(BRC) <> QTYBOX THEN 'PROCESS' ELSE 'COMPLETED' end STATUS, 
										SUM(BRC) QRCODE, 
										a.PARTID, 
										a.PARTNAME, 
										a.QTYORDER, 
										QTYBOX, 
										a.POCUSTOMER, 
										a.NODO, 
										CAST(a.createtime AS date) AS tgl_scan, 
										a.customer 
									FROM 
										(
											SELECT 
												COUNT(a.barcodeid) BRC, 
												a.PARTID, 
												a.PARTNAME, 
												a.QTYORDER, 
												SUBSTRING(a.seqstiker, 5, 3) AS QTYBOX, 
												a.POCUSTOMER, 
												a.NODO, 
												a.createtime, 
												a.customer 
											FROM 
												tbl_printqrcodedo a 
												JOIN tbl_scanbarcode b ON a.barcodeid = b.barcodeid 
											GROUP BY 
												a.PARTID, 
												a.PARTNAME, 
												a.QTYORDER, 
												a.seqstiker, 
												a.POCUSTOMER, 
												a.NODO, 
												a.createtime, 
												a.customer
										) a 
									WHERE 
										CAST(a.createtime as date) BETWEEN '$start_date' 
										AND '$end_date' 
									GROUP BY 
										a.BRC, 
										a.PARTID, 
										a.PARTNAME, 
										a.QTYORDER, 
										QTYBOX, 
										a.POCUSTOMER, 
										a.NODO, 
										CAST(a.createtime AS date), 
										a.customer 
									ORDER BY 
										CAST(a.createtime AS date) DESC";

		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status 		= "";
		$lokasi_1 	= "";
		$lokasi_2 	= "";
		$driver 		= "";

		foreach ($result as $key => $value) {

			if ($value->STATUS == 'COMPLETED') {
				$status = '<span class="badge badge-success" style="font-size: 14px;">COMPLETED</span>
    							 <br>
    							 <span style="font-size: 12px;">' . $value->QRCODE . ' dari ' . $value->QTYBOX . '</span>';
			} else {
				$status = '<span class="badge badge-danger" style="font-size: 14px;">UNCOMPLETED</span>
    							 <br>
    							 <span style="font-size: 12px;">' . $value->QRCODE . ' dari ' . $value->QTYBOX . '</span>';
			}

			$data_lokasi 	= $this->cek_lokasi_scan($value->NODO, $value->POCUSTOMER, $value->PARTID);
			if (count($data_lokasi) > 0) {
				$lokasi_1 = $data_lokasi[0]->lokasi_scan_1 == "" ? "-" : '<span class="badge badge-primary" style="font-size: 14px;">' . $data_lokasi[0]->lokasi_scan_1 . '</span><br><span style="font-size: 11px;"> on ' . $data_lokasi[0]->tgl_masuk_lokasi_1 . '</span>';
				$lokasi_2 = $data_lokasi[0]->lokasi_scan_2 == "" ? "-" : '<span class="badge badge-warning text-white" style="font-size: 14px;">' . $data_lokasi[0]->lokasi_scan_2 . '</span><br><span style="font-size: 11px;"> on ' . $data_lokasi[0]->tgl_masuk_lokasi_2 . '</span>';
				$driver 	= $data_lokasi[0]->nama_driver . " (" . $data_lokasi[0]->no_polisi . ")";
			} else {
				$lokasi_1 = '<span class="badge badge-danger" style="font-size: 14px;">BELUM DI SCAN</span>';
				$lokasi_2 = '<span class="badge badge-danger" style="font-size: 14px;">BELUM DI SCAN</span>';
				$driver 	= '<span class="badge badge-danger" style="font-size: 14px;">BELUM DI INPUT</span>';
			}

			$data[] = array(
				$no++,
				$status,
				'<a class="text-danger" href="' . base_url() . 'sales/monitoring_details/' . base64_encode($value->NODO) . '" target="_blank" style="font-size: 15px;" title="Klik ini untuk melihat detail">' . $value->NODO . '</a>',
				$value->POCUSTOMER,
				number_format($value->QTYORDER, 0),
				$value->customer,
				$value->PARTID,
				$value->PARTNAME,
				$driver,
				$lokasi_1,
				$lokasi_2
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

	public function monitoring_details($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Monitoring Detail Sales";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['no_do'] 					= base64_decode($id);

			$this->load->view('adminx/sales/monitoring_details', $data, FALSE);

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
		} else {
			redirect('errorpage/error403');
		}
	}

	public function monitoring_details_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$no_do 			= $this->input->post('no_do');

		$second_DB  	= $this->load->database('bjsmas01_db', TRUE);

		$sql 					= "SELECT * FROM tbl_printqrcodedo WHERE nodo = '$no_do'";
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {

			$data[] = array(
				$value->seqstiker,
				$value->partid,
				$value->partname,
				number_format($value->qtyorder, 0),
				number_format($value->qtypallet, 0),
				$value->nodo,
				$value->pocustomer,
				$value->statusscan,
				$value->customer == NULL ? '-' : $value->customer,
				$value->keterangan == '' ? '-' : $value->keterangan,
				$value->barcodeid
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

	public function cek_lokasi_scan($nodoc, $po, $part_id)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query("SELECT TOP 1
																			a.barcodeid, 
																			a.nodo, 
																			a.pocustomer, 
																			a.partid, 
																			a.partname, 
																			b.customer, 
																			b.createtime AS tgl_masuk_lokasi_1, 
																			d.Namadivisi AS lokasi_scan_1, 
																			c.create_date AS tgl_masuk_lokasi_2,
																			c.lokasi_scan AS lokasi_scan_2,
																			c.lokasi_id,
																			c.nama_driver,
																			c.no_polisi
																		FROM 
																			tbl_printqrcodedo a 
																			LEFT JOIN tbl_scanbarcode b ON b.barcodeid = a.barcodeid 
																			LEFT JOIN tbl_scanbarcode_approval c ON c.barcode_id = a.barcodeid 
																			LEFT JOIN tbl_msdivisi d ON d.Iddivisi = b.lokasiscan 
																		WHERE 
																			a.nodo = '$nodoc' 
																			AND a.pocustomer = '$po' 
																			AND a.partid = '$part_id'
																			ORDER BY c.create_date DESC");
		$cek 				= $query->num_rows();

		return $query->result();
	}

	public function monitoring_sales_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$where = "DAY(A.createtime) = '" . $tanggal . "'
								AND MONTH(A.createtime) = '" . $bulan . "'
								AND YEAR(A.createtime) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$where = "MONTH(A.createtime) = '" . $bulan . "'
								AND YEAR(A.createtime) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$where = "YEAR(A.createtime) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		$second_DB->select('A.barcodeid, A.nodoc, A.po, A.partno, A.partname, 
    										A.statusscan, A.lokasiscan, A.createtime AS tgl_masuk_gudang, A.qtybox,
												B.barcode_id, B.lokasi_scan, B.create_date AS tgl_pengiriman, C.Namadivisi');
		$second_DB->from('tbl_scanbarcode A');
		$second_DB->join('tbl_scanbarcode_approval B', 'B.barcode_id = A.barcodeid', 'left');
		$second_DB->join('tbl_msdivisi C', 'C.Iddivisi = A.lokasiscan', 'left');
		$second_DB->order_by('A.createtime', 'DESC');

		$query 			= $second_DB->get();
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$temp 			= array_unique(array_column($result, 'nodoc'));
		$unique_arr = array_intersect_key($result, $temp);

		//foreach($query->result() as $value) {
		foreach ($unique_arr as $key => $value) {

			$nodoc 				= $value->nodoc;
			$po 					= $value->po;
			$jlh_box 			= substr($value->qtybox, -1);
			$jlh_box_sub 	= $this->cek_qty($nodoc, $po, $jlh_box);
			$status 			= "";
			if ($jlh_box == $jlh_box_sub) {
				$status = '<span class="badge badge-success" style="font-size: 14px;">COMPLETED</span>
    							 <br>
    							 <span style="font-size: 12px;">' . $jlh_box_sub . ' dari ' . $jlh_box . '</span>';
			} else {
				$status = '<span class="badge badge-danger" style="font-size: 14px;">UNCOMPLETED</span>
    							 <br>
    							 <span style="font-size: 12px;">' . $jlh_box_sub . ' dari ' . $jlh_box . '</span>';
			}

			$data[] = array(
				$no++,
				$value->nodoc,
				$value->po,
				$value->partno,
				$status,
				$value->Namadivisi == "" ? "-" : '<span class="badge badge-primary" style="font-size: 14px;">' . $value->Namadivisi . '</span><br> on <span style="font-size: 12px;">' . $value->tgl_masuk_gudang . '</span>',
				$value->lokasi_scan == "" ? "-" : '<span class="badge badge-warning text-white" style="font-size: 14px;">' . $value->lokasi_scan . '</span><br> on <span style="font-size: 12px;">' . $value->tgl_pengiriman . '</span>'
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

	public function cek_qty($nodoc, $po, $jlh_box)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query("SELECT COUNT(po) AS jlh_box FROM tbl_scanbarcode
											 							 WHERE nodoc = '$nodoc' AND po = '$po'");
		$cek 				= $query->num_rows();
		if ($cek > 0) {
			return 			$query->row()->jlh_box;
		} else {
			return 0;
		}
	}
}
