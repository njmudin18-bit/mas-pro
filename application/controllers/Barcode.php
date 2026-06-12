<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barcode extends CI_Controller
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
			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Barcode Trace";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/ppic/barcode_trace', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function show_barcode_group_by_job() {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');

    $sql = "SELECT a.NoBukti, a.PartID, a.PartName, b.JLH_LABEL, 
              CAST(a.Tgl as date) AS tgl_cetak, a.TypeInventoryID, a.UnitID, a.QtyOrder 
            FROM tbl_qrcodeppic a
              LEFT JOIN (
                SELECT NoBukti, QtyOrder, COUNT(DISTINCT QRCode) AS JLH_LABEL
                FROM tbl_qrcodeppic
                WHERE CAST(QtyPallet AS DECIMAL) > 1
                GROUP BY NoBukti, QtyOrder
              ) b ON b.NoBukti = a.NoBukti
            WHERE CAST(a.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
            GROUP BY a.NoBukti, a.PartID, a.PartName, b.JLH_LABEL, 
              CAST(a.Tgl as date), a.TypeInventoryID, a.UnitID, a.QtyOrder
            ORDER BY CAST(a.Tgl AS date) DESC";

		$second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {
      $link_detail = base_url()."barcode/show_detail/".base64_encode($value->NoBukti)."/".base64_encode(number_format($value->QtyOrder, 0));
			$data[]  = array(
				$no++,
				'<a href="'.$link_detail.'" target="_blank" class="text-danger" 
          style="font-size:15px; cursor:pointer"
          title="Klik untuk detail">'.$value->NoBukti.'</a>',
				number_format($value->JLH_LABEL, 0),
        $value->tgl_cetak,
        $value->PartID,
        $value->PartName,
        $value->TypeInventoryID,
        number_format($value->QtyOrder, 0),
        $value->UnitID
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

  public function show_detail() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "PPIC";
			$data['nama_halaman'] 	= "Barcode Trace";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['job_no']         = base64_decode($this->uri->segment(3));
      $data['qty_order']      = base64_decode($this->uri->segment(4));
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/ppic/barcode_trace_new', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function barcode_trace_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET NO JOB
		$no_job       = $this->input->post('job_no');
		$qty_order    = str_replace(',', '', $this->input->post('qty_order'));
    $now          = date('Ym');
    $start_month  = $now - 2;
    $end_month    = $now;
    $join_hd      = "";
    $join_dt      = "";
    $wh_scan_date = "";
    $wh_scan_by   = "";

    for ($x = $start_month; $x <= $end_month; $x++) {
      $bulan  = $x;
      if ($end_month == $x) {
        $join_hd  .= " SELECT NoBukti, CreateDate, CreateBy, Keterangan FROM [BJGMAS01].[dbo].TRANS_BHPHD$bulan ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID FROM [BJGMAS01].[dbo].Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT NoBukti, CreateDate, CreateBy, Keterangan FROM [BJGMAS01].[dbo].TRANS_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID FROM [BJGMAS01].[dbo].Trans_BHPDT$bulan UNION ALL ";
      }
    }

		$sql 	  = "SELECT 
                a.QRCode, CAST(a.Tgl AS date) AS tgl_cetak, a.PartID, a.PartName, 
                a.QtyOrder, a.NoBukti, a.UnitID,
                b.no_job AS JOB_NO, b.loc_id AS prod_loc, b.scan_date AS prod_scan_date, b.scan_by AS prod_scanby, 
                c.loc_id AS qc_loc, c.scan_date AS qc_scan_date, c.scan_by AS qc_scanby 
              FROM 
              (
                SELECT NoBukti, PartID, PartName, QtyOrder, Tgl, QRCode, UnitID
                FROM tbl_qrcodeppic
                WHERE CAST(QtyPallet AS DECIMAL) > 1
                GROUP BY NoBukti, PartID, PartName, QtyOrder, Tgl, QRCode, UnitID
              ) a
              LEFT JOIN (
                SELECT 
                  scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by, no_job 
                FROM tbl_scanbarcode_job 
                WHERE loc_id = 'PR001' 
                GROUP BY 
                  scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by, no_job
              ) b ON a.QRCode = b.barcode_no 
              LEFT JOIN (
                SELECT 
                  scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by 
                FROM tbl_scanbarcode_job 
                WHERE loc_id = 'QC001' 
                GROUP BY 
                  scan_id, barcode_no, loc_id, scan_status, scan_date, scan_by
              ) c ON a.QRCode = c.barcode_no
              WHERE 
                a.NoBukti = '$no_job' 
              GROUP BY 
                a.QRCode, CAST(a.Tgl AS date), a.PartID, a.PartName, 
                a.QtyOrder, a.NoBukti, a.UnitID,
                b.loc_id, b.scan_date, b.scan_by, b.no_job,
                c.loc_id, c.scan_date, c.scan_by 
              ORDER BY 
                CAST(a.Tgl AS date) DESC";

		$second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
    $qty_pallet   = 0;

		foreach ($result as $key => $value) {
      $array_qr     = explode('|', $value->QRCode);
      $qty_pallet   = $array_qr[7];
      $sql_wh       = $second_DB->query("SELECT  
                        a.NoBukti, a.NoBuktiJob, a.PartID, b.CreateBy, b.CreateDate, c.NAME, 
                        SUBSTRING(b.Keterangan, 11, 100) AS LABEL_NO
                      FROM 
                      (
                        $join_dt
                      ) a
                      LEFT JOIN (
                        $join_hd
                      ) b ON b.NoBukti = a.NoBukti
                      LEFT JOIN [Attendance].[dbo].USERINFO c ON c.SSN = '001' + b.CreateBy
                      WHERE a.NoBuktiJob = '$value->JOB_NO'
                      AND SUBSTRING(b.Keterangan, 11, 100) LIKE '%$array_qr[1]%'");
      $cek          = $sql_wh->num_rows();
      if ($cek > 0) {
        $res          = $sql_wh->row();
        $wh_scan_date = $res->CreateDate;
        $wh_scan_by   = $res->NAME;
      } else {
        $wh_scan_date = "-";
        $wh_scan_by   = "-";
      }
      


			$data[] = array(
				$no++,
				$value->QRCode,
				$value->tgl_cetak,
				$value->prod_loc == '' ? '-' : $value->prod_loc,
				$value->prod_scanby == '' ? '-' : $value->prod_scanby,
				$value->prod_scan_date == '' ? '-' : substr($value->prod_scan_date, 0, -4),
        $value->qc_loc == '' ? '-' : $value->qc_loc,
				$value->qc_scanby == '' ? '-' : $value->qc_scanby,
				$value->qc_scan_date == '' ? '-' : substr($value->qc_scan_date, 0, -4),
        $wh_scan_by,
        $wh_scan_date,
        number_format($qty_pallet, 0),
        $value->UnitID,
        number_format($value->QtyOrder, 0),

        $value->PartID,
        $value->PartName,
        number_format($value->QtyOrder, 0)
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