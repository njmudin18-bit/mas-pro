<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lhp extends CI_Controller
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
    $this->load->model('whlocation_model', 'whlocation');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Tracking No Barcode Produksi";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/lhp', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  //DATA AWAL
  public function show_no_barcode_production() 
  {
    $draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

		//GET START AND END DATE
		$start_date     = $this->input->post('start_date');
		$end_date       = $this->input->post('end_date');

    $year1          = date('Y', strtotime($start_date));
    $year2          = date('Y', strtotime($end_date));
    $month1         = date('m', strtotime($start_date));
    $month2         = date('m', strtotime($end_date));
    $interval       = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

    //GET WH LOCATION
    $WHData         = $this->whlocation->get_by_id(3);
    $WHLocation     = " AND a.LocationID IN (".$WHData->WhLocation.") ";

    $start_array    = explode('-', $start_date);
    $end_array      = explode('-', $end_date);
    $tbl_trans_job  = " LEFT JOIN Trans_Job".$end_array[0].$end_array[1]." c ON c.NoBukti = a.NoBuktiJob ";
    $start_month    = $start_array[0].$start_array[1];
    $end_month      = $end_array[0].$end_array[1];
    $join_hd        = "";
    $join_dt        = "";

    for ($i = 0; $i < $interval; $i++) {
      $tempDate       = date('Y-m-d', strtotime($start_date. ' + '.$i.' months'));
      $tempTableName  = date('Y', strtotime($tempDate)). date('m',strtotime($tempDate));
    
      if ($i < $interval -1) {
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$tempTableName UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$tempTableName UNION ALL ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$tempTableName ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$tempTableName ";
      }
    }

    $sql = "SELECT 
              b.PartName, a.NoBuktiJob, a.PartID, 
              SUM(a.Qty) AS TOTAL_QTY_SCAN_WH, c.QtyOrder, a.LocationID,
              CAST(c.Tgl AS DATE) AS TGL_BUAT_JOB,
              COUNT(a.NoBuktiJob) AS QTY_SCAN_WH,
              c.Keterangan
            FROM (
              $join_dt
            ) a
            LEFT JOIN Ms_Part b ON b.PartID = a.PartID
            $tbl_trans_job
            LEFT JOIN (".$join_hd.") d ON d.NoBukti = a.NoBukti
            WHERE CAST(d.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
            $WHLocation
            GROUP BY a.NoBuktiJob, a.PartID, b.PartName, c.QtyOrder, a.LocationID, CAST(c.Tgl AS DATE), c.Keterangan
            ORDER BY CAST(c.Tgl AS DATE) DESC";
            //AND a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 'WH-R', 'WH-R01', 'WH-WIP00', 'WH-SAYUNG')

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {
      $max_qr = "";
      $sql2   = " SELECT TOP 1 NoBukti, PartID, QRCode 
                  FROM tbl_qrcodeppic 
                  WHERE NoBukti = 'CKP/' + '$value->NoBuktiJob' ORDER BY QRCode DESC";
      $query2 = $second_DB->query($sql2);
      $cek    = $query2->num_rows();
      if ($cek > 0) {
        $res  = $query2->row();
        $array_qr = explode('|', $res->QRCode);
        $max_qr   = "01 - ".$array_qr[2];
      } else {
        $max_qr   = "-";
      }

      $TOTAL_SCAN_LABEL_WH  = strlen($value->QTY_SCAN_WH) == 1 ? "01 - 0".$value->QTY_SCAN_WH : "01 - 0".$value->QTY_SCAN_WH;
      $isi 		              = "'" . $value->NoBuktiJob . "', '" . $value->PartName . "'";
			$data[]  = array(
				$no++,
				$value->PartName,
        $value->NoBuktiJob.'<hr>'.$value->PartID,
        $value->TGL_BUAT_JOB,
				$max_qr.'<hr><a target="_blank" href="'.base_url().'lhp/list_barcode_produksi/'.base64_encode($value->NoBuktiJob).'/'.base64_encode($value->PartID).'/'.base64_encode($value->PartName).'" class="btn btn-secondary btn-sm btn-block">DETAIL</a>',
        '<a target="_blank" href="'.base_url().'lhp/list_barcode_belum_scan/'.base64_encode($value->NoBuktiJob).'/'.base64_encode($value->PartID).'/'.base64_encode($value->PartName).'/'.base64_encode(number_format($value->QtyOrder, 0)).'/'.$start_date.'/'.$end_date.'" class="btn btn-warning text-white btn-sm btn-block">DETAIL</a>',
        $TOTAL_SCAN_LABEL_WH.'<hr><a target="_blank" href="'.base_url().'lhp/list_barcode_wh/'.base64_encode($value->NoBuktiJob).'/'.base64_encode($value->PartID).'/'.base64_encode($value->PartName).'/'.base64_encode($start_date).'/'.base64_encode($end_date).'" class="btn btn-info btn-sm btn-block">DETAIL</a>',
        '<a onclick="cek_detail_transaksi('.$isi.')" style="font-size:.9375rem; color:#ff5370; cursor:pointer;" 
          title="Klik untuk detail">'.number_format($value->TOTAL_QTY_SCAN_WH, 0).'</a>',
        number_format($value->QtyOrder, 0),
        number_format(($value->QtyOrder - $value->TOTAL_QTY_SCAN_WH), 0),
        $value->QtyOrder == $value->TOTAL_QTY_SCAN_WH ? '<button class="btn btn-success btn-sm btn-block">COMPLETED</button>' : '<button class="btn btn-danger btn-sm btn-block">OPEN</button>',
        $value->Keterangan
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
    $job_no_array   = explode('/', $this->input->post('job_nomor'));
    $start_month    = $job_no_array[2];
		$end_month      = date('Ym');

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

            //echo $sql; exit;
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
    $no           = 1;
    $content      = "";
    $footer       = "";
    $total        = 0;

    foreach ($result as $key => $value) {
      $Isi   = "'".$value->NoBuktiJob."', '".$value->PartID."', '".$value->TGL_SCAN_WH."'";
      $total = $total + floatval($value->TOTAL_SCAN_WH);
      $content .= '<tr>
                      <td class="text-right">'.$no++.'</td>
                      <td class="text-left">'.$value->NoBuktiJob.'</td>
                      <td class="text-left">'.$value->PartID.'</td>
                      <td class="text-right">'.number_format($value->TOTAL_SCAN_WH, 0).'</td>
                      <td class="text-center">
                        '.$value->TGL_SCAN_WH.'
                        <hr>
                        <button class="btn btn-danger btn-sm" onclick="cek_waktu('.$Isi.')">CEK WAKTU</button>
                      </td>
                   </tr>';
      $footer   = '<tr class="bg-info">
                      <th class="text-right" colspan="3">TOTAL</th>
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

  //CEK TANGGAL TRANSAKSI
  public function cek_tanggal_transaksi() 
  {
    $JobNumber        = $this->input->post('NomorJob');
    $PartID           = $this->input->post('IDPart');
    $ScanDate         = $this->input->post('TanggalScan');

    $job_no         = $this->input->post('NomorJob');
    $job_no_array   = explode('/', $this->input->post('NomorJob'));
    $start_month    = $job_no_array[2];
		$end_month      = date('Ym');

    $join_hd        = "";
    $join_dt        = "";
    
    for ($x = $start_month; $x <= $end_month; $x++) {
      $bulan  = $x;
      if ($end_month == $x) {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH, CONVERT(TIME(0), Tgl) AS JAM_SCAN_WH FROM Trans_BHPHD$bulan ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH, CONVERT(TIME(0), Tgl) AS JAM_SCAN_WH FROM Trans_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$bulan UNION ALL ";
      }
    }

    $sql  = "SELECT 
              a.NoBuktiJob, a.PartID, SUM(a.Qty) AS TOTAL_SCAN_WH, 
              b.TGL_SCAN_WH, b.JAM_SCAN_WH, c.PartName
            FROM (
              $join_dt
            ) a
            LEFT JOIN (
              $join_hd
            ) b ON b.NoBukti = a.NoBukti
            LEFT JOIN Ms_Part c ON c.PartID = a.PartID
            WHERE a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 
                                   'WH-R', 'WH-R01', 'WH-WIP00')
            AND a.NoBuktiJob = '$job_no' AND a.PartID = '$PartID' AND b.TGL_SCAN_WH = '$ScanDate'
            GROUP BY a.NoBuktiJob, a.PartID, b.TGL_SCAN_WH, b.JAM_SCAN_WH, c.PartName
            ORDER BY b.JAM_SCAN_WH DESC";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
    $no           = 1;
    $content      = "";
    $footer       = "";
    $total        = 0;

    foreach ($result as $key => $value) {
      $Isi   = "'".$value->NoBuktiJob."', '".$value->PartID."', '".$value->TGL_SCAN_WH."'";
      $total = $total + floatval($value->TOTAL_SCAN_WH);
      $content .= '<tr>
                      <td class="text-right">'.$no++.'</td>
                      <td class="text-left">'.$value->PartID.'</td>
                      <td class="text-left">'.$value->PartName.'</td>
                      <td class="text-right">'.number_format($value->TOTAL_SCAN_WH, 0).'</td>
                      <td class="text-center">'.$value->TGL_SCAN_WH.'</td>
                      <td class="text-center">'.$value->JAM_SCAN_WH.'</td>
                   </tr>';
      $footer   = '<tr class="bg-info">
                      <th class="text-right" colspan="3">TOTAL</th>
                      <th class="text-right">'.number_format($total, 0).'</th>
                      <th colspan="2"></th>
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

  //DAFTAR NO BARCODE PRODUKSI
  public function list_barcode_produksi() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "No Barcode PPIC";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['no_job'] 		    = base64_decode($this->uri->segment(3));
			$data['part_id'] 		    = base64_decode($this->uri->segment(4));
			$data['part_name'] 		  = base64_decode($this->uri->segment(5));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/list_barcode_produksi', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  //DAFTAR NO BARCODE PRODUKSI DATA
  public function list_barcode_produksi_data() {

    $draw 			  = intval($this->input->get("draw"));
		$start 			  = intval($this->input->get("start"));
		$length 		  = intval($this->input->get("length"));

		//GET START AND END DATE
		$no_job       = $this->input->post('no_job');
		$part_id      = $this->input->post('part_id');

		$sql 	        = "SELECT NoBukti, QRCode, QtyOrder, CAST(Tgl AS DATE) AS TGL_CETAK
                     FROM tbl_qrcodeppic
                     WHERE NoBukti = 'CKP/' + '$no_job' AND PartID = '$part_id'
                     AND CAST(QtyPallet AS decimal) > 1
                     GROUP BY NoBukti, QRCode, QtyOrder, CAST(Tgl AS DATE)";

		$second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
    $qtyOrder     = "";

		foreach ($result as $key => $value) {
      $qtyOrder = number_format($value->QtyOrder, 0);
      $array_qr = explode('|', $value->QRCode);
			$data[]  = array(
				$no++,
				$value->QRCode,
        $array_qr[7],
        $value->TGL_CETAK,
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data,
      "qty_order"       => $qtyOrder
		);

		echo json_encode($result);
		exit();
  }

  //DAFTAR NO BARCODE WH
  public function list_barcode_wh() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "No Barcode WH";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['no_job'] 		    = base64_decode($this->uri->segment(3));
			$data['part_id'] 		    = base64_decode($this->uri->segment(4));
			$data['part_name'] 		  = base64_decode($this->uri->segment(5));
			$data['start_date'] 		= base64_decode($this->uri->segment(6));
			$data['end_date'] 		  = base64_decode($this->uri->segment(7));

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/list_barcode_wh', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  //DAFTAR NO BARCODE PRODUKSI DATA
  public function list_barcode_wh_data() {

    $draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

    //GET START AND END DATE
    $no_job         = $this->input->post('no_job');
    $job_array      = explode('/', $no_job);
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
        $join_hd  .= " SELECT * FROM [BJGMAS01].[dbo].Trans_BHPHD$bulan ";
        $join_dt  .= " SELECT * FROM [BJGMAS01].[dbo].Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT * FROM [BJGMAS01].[dbo].Trans_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT * FROM [BJGMAS01].[dbo].Trans_BHPDT$bulan UNION ALL ";
      }
    }

    //, d.NAME
    $sql = "SELECT a.*, b.*, c.QtyOrder 
            FROM 
              (
                $join_dt
              ) a 
              LEFT JOIN (
                $join_hd
              ) b ON b.NoBukti = a.NoBukti 
              LEFT JOIN [BJGMAS01].[dbo].Trans_Job$job_array[2] c ON c.NoBukti = a.NoBuktiJob 
            WHERE
              CAST(b.Tgl AS DATE) BETWEEN '$start_date' AND '$end_date' AND
              a.NoBuktiJob = '$no_job' 
            ORDER BY 
              b.CreateDate DESC"; //LEFT JOIN [Attendance].[dbo].USERINFO d ON d.SSN = '001' + b.CreateBy

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $query 				= $second_DB->query($sql);
    $result 			= $query->result();
    $data 				= [];
    $no 					= 1;
    $qtyOrder     = "";

    foreach ($result as $key => $value) {
      $qtyOrder = number_format($value->QtyOrder, 0);
      $data[]  = array(
        $no++,
        $value->NoKartu,
        number_format($value->Qty, 0),
        substr($value->CreateDate, 0, -4),
        "-" //$value->NAME
      );
    }

    $result = array(
      "draw" 						=> $draw,
      "recordsTotal" 		=> $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data" 						=> $data,
      "qty_order"       => $qtyOrder
    );

    echo json_encode($result);
    exit();
  }

  //DAFTAR NO BARCODE BELUM SCAN 
  public function list_barcode_belum_scan() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "No Barcode Belum Scan";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['no_job'] 		    = base64_decode($this->uri->segment(3));
			$data['part_id'] 		    = base64_decode($this->uri->segment(4));
			$data['part_name'] 		  = base64_decode($this->uri->segment(5));
			$data['qty_order'] 		  = base64_decode($this->uri->segment(6));
      $data['start_date']     = $this->uri->segment(7);
      $data['end_date']       = $this->uri->segment(8);

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/list_barcode_belum_scan', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  public function list_barcode_belum_scan_data() {
    $draw 			  = intval($this->input->get("draw"));
		$start 			  = intval($this->input->get("start"));
		$length 		  = intval($this->input->get("length"));

    $start_date   = $this->input->post('start_date');
  	$end_date 	  = $this->input->post('end_date');
    $year1        = date('Y', strtotime($start_date));
    $year2        = date('Y', strtotime($end_date));
    $month1       = date('m', strtotime($start_date));
    $month2       = date('m', strtotime($end_date));
    $interval     = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

		//GET START AND END DATE
		$no_job       = $this->input->post('no_job');
    $exp 	        = explode('/', $no_job);
    $start 	      = $exp[2];
    $end 	        = date('Ym');
		$qty_order    = str_replace(',', '', $this->input->post('qty_order'));
    $sql_sub      = "";

    for($i = 0; $i < $interval; $i++){
      $tempDate       = date('Y-m-d', strtotime($start_date. ' + '.$i.' months'));
      $tempTableName  = date('Y', strtotime($tempDate)). date('m', strtotime($tempDate));
    
      if ($i < $interval -1) {
        $sql_sub .= "SELECT NoBukti, NoBuktiJob, NoKartu, PartID, Qty 
                     FROM Trans_BHPDT$tempTableName
                     UNION ALL ";
      } else {
        $sql_sub .= "SELECT NoBukti, NoBuktiJob, NoKartu, PartID, Qty FROM Trans_BHPDT$tempTableName";
      }
    }

    //SQL UTAMA
    $sql = "SELECT QRCode, NoBukti, PartID, PartName, QtyOrder
            FROM tbl_qrcodeppic 
            WHERE NoBukti = 'CKP/' + '$no_job' 
            AND CAST(QtyOrder AS DECIMAL) = '$qty_order'
            GROUP BY QRCode, NoBukti, PartID, PartName, QtyOrder";

		$second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
    $qtyOrder     = "";

		foreach ($result as $key => $value) {
      $barcode_array = explode('|', $value->QRCode);
      $sql2 = " SELECT 
                  a.NoKartu, a.NoBukti, a.NoBuktiJob, a.PartID, a.Qty
                FROM (
                    $sql_sub
                ) a
                WHERE a.NoBuktiJob = '$no_job' 
                AND NoKartu = '$barcode_array[1]'
                ORDER BY NoKartu";
      $q2   = $second_DB->query($sql2);
      $cek  = $q2->num_rows();
      if ($cek == 0) {
        $data[]  = array(
        	$no++,
        	$value->QRCode,
          $barcode_array[2],
          number_format($barcode_array[7], 0)
        );
      }
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data,
      "qty_order"       => number_format($qty_order)
		);

		echo json_encode($result);
		exit();
  }

  //HASIL SCAN WAREHOUSE
  public function hasil_scan_wh() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Hasil Scan Warehouse";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/hasil_scan_warehouse', $data, FALSE);
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
    $year1          = date('Y', strtotime($start_date));
    $year2          = date('Y', strtotime($end_date));
    $month1         = date('m', strtotime($start_date));
    $month2         = date('m', strtotime($end_date));
    $interval       = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

		$pilihan        = $this->input->post('pilihan');
    $start_array    = explode('-', $start_date);
    $end_array      = explode('-', $end_date);
    $tbl_trans_job  = " LEFT JOIN Trans_Job".$end_array[0].$end_array[1]." c ON c.NoBukti = a.NoBuktiJob ";
    $start_month    = $start_array[0].$start_array[1];
    $end_month      = $end_array[0].$end_array[1];
    $join_hd        = "";
    $join_dt        = "";
    $location_in    = "";

    for($i = 0 ; $i < $interval; $i++){
      $tempDate       = date('Y-m-d', strtotime($start_date. ' + '.$i.' months'));
      $tempTableName  = date('Y', strtotime($tempDate)). date('m', strtotime($tempDate));
    
      if ($i < $interval -1){
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$tempTableName UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$tempTableName UNION ALL ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti FROM Trans_BHPHD$tempTableName ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, Qty FROM Trans_BHPDT$tempTableName ";
      }
    }

    if ($pilihan == 'all') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(3); //3=ALL, 2=WR, 1=PC
      $location_in    = " a.LocationID IN (".$WHData->WhLocation.") ";

      //$location_in = " a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 'WH-R', 'WH-R01', 'WH-WIP00') ";
    } else if($pilihan == 'pc') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(1); //3=ALL, 2=WR, 1=PC
      $location_in    = " a.LocationID IN (".$WHData->WhLocation.") ";

      //$location_in = " a.LocationID IN ('WH-FG01', 'WH-GRS01') ";
    } else {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(2); //3=ALL, 2=WR, 1=PC
      $location_in    = " a.LocationID IN (".$WHData->WhLocation.") ";

      //$location_in = " a.LocationID IN ('WH-FG', 'WH-GRS00') ";
    }
    
    $sql = "SELECT 
              b.PartName, a.NoBuktiJob, a.PartID, 
              SUM(a.Qty) AS TOTAL_QTY_SCAN_WH, c.QtyOrder, 
              a.LocationID, b.TypeInventoryID,
              CAST(c.Tgl AS DATE) AS TGL_BUAT_JOB,
              COUNT(a.NoBuktiJob) AS QTY_SCAN_WH
            FROM (
              $join_dt
            ) a
            LEFT JOIN Ms_Part b ON b.PartID = a.PartID
            $tbl_trans_job
            LEFT JOIN (" . $join_hd . ") d ON d.NoBukti = a.NoBukti
            WHERE CAST(d.Tgl AS date) BETWEEN '$start_date' AND '$end_date'
              AND $location_in
              AND b.TypeInventoryID NOT IN ('MP01')
            GROUP BY 
              a.NoBuktiJob, a.PartID, b.PartName, 
              c.QtyOrder, a.LocationID, b.TypeInventoryID,
              CAST(c.Tgl AS DATE)"; //c.QtyOrder <> a.Qty AND
    //echo $sql; exit;
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {
      if ($value->QtyOrder == $value->TOTAL_QTY_SCAN_WH) {
        $sts_button         = '<button class="btn btn-success btn-sm btn-block">COMPLETED</button>';
      } else {
        $sts_button         = '<button class="btn btn-danger btn-sm btn-block">OPEN</button>';
      }

      $TOTAL_SCAN_LABEL_WH  = strlen($value->QTY_SCAN_WH) == 1 ? "01 - 0".$value->QTY_SCAN_WH : "01 - ".$value->QTY_SCAN_WH;
      $isi 		              = "'" . $value->NoBuktiJob . "', '" . $value->PartName . "'";
      $isi2                 = "'" . $value->NoBuktiJob . "', '" . $value->PartName . "', '".$value->PartID."', '".number_format($value->QtyOrder, 0)."'";
			$data[]  = array(
				$no++,
				$value->PartName.'<hr>
        <button class="btn btn-danger btn-sm" onclick="set_data_shift('.$isi2.')">INPUT DATA SHIFT</button>',
        $value->NoBuktiJob.'<hr>'.$value->PartID,
        $value->TGL_BUAT_JOB,
        $TOTAL_SCAN_LABEL_WH.'<hr><a target="_blank" href="'.base_url().'lhp/list_barcode_wh/'.base64_encode($value->NoBuktiJob).'/'.base64_encode($value->PartID).'/'.base64_encode($value->PartName).'/'.base64_encode($start_date).'/'.base64_encode($end_date).'" class="btn btn-info btn-sm btn-block">DETAIL</a>',
        '<a id="'.$key.'" onclick="cek_detail_transaksi('.$isi.')" style="font-size:.9375rem; color:#ff5370; cursor:pointer;" 
          title="Klik untuk detail">'.number_format($value->TOTAL_QTY_SCAN_WH, 0).'</a>',
        //number_format($value->TOTAL_QTY_SCAN_WH, 0),
        number_format($value->QtyOrder, 0),
        $value->QtyOrder == $value->TOTAL_QTY_SCAN_WH ? '<button class="btn btn-success btn-sm btn-block">COMPLETED</button>' : '<button class="btn btn-danger btn-sm btn-block">OPEN</button>'
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
  
  //CEK DETAIL
  public function cek_detail_transaksi_wh() {
    $job_no         = $this->input->post('job_nomor');
    $job_no_array   = explode('/', $this->input->post('job_nomor'));
    $start_month    = $job_no_array[2];
		$end_month      = date('Ym');
    $join_hd        = "";
    $join_dt        = "";
    
    for ($x = $start_month; $x <= $end_month; $x++) {
      $bulan  = $x;
      if ($end_month == $x) {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH FROM Trans_BHPHD$bulan ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, CAST(Qty AS DECIMAL) AS Qty FROM Trans_BHPDT$bulan ";
      } else {
        $join_hd  .= " SELECT Tgl, NoBukti, CAST(Tgl AS date) AS TGL_SCAN_WH FROM Trans_BHPHD$bulan UNION ALL ";
        $join_dt  .= " SELECT NoBukti, NoBuktiJob, PartID, LocationID, CAST(Qty AS DECIMAL) AS Qty FROM Trans_BHPDT$bulan UNION ALL ";
      }
    }

    $sql  = "SELECT 
              a.NoBuktiJob, a.PartID, SUM(a.Qty) AS TOTAL_SCAN_WH, 
              b.TGL_SCAN_WH, c.PartName
            FROM (
              $join_dt
            ) a
            LEFT JOIN (
              $join_hd
            ) b ON b.NoBukti = a.NoBukti
            LEFT JOIN Ms_Part c ON a.PartID = c.PartID
            WHERE a.LocationID IN ('WH-FG', 'WH-FG01', 'WH-GRS00', 'WH-GRS01', 
                                   'WH-R', 'WH-R01', 'WH-WIP00')
            AND a.NoBuktiJob = '$job_no'
            GROUP BY a.NoBuktiJob, a.PartID, b.TGL_SCAN_WH, c.PartName
            ORDER BY b.TGL_SCAN_WH DESC";
    //echo $sql; exit;
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
    $no           = 1;
    $content      = "";
    $footer       = "";
    $total        = 0;
    $sts_button   = '';
    $qty_shift    = 0;

    foreach ($result as $key => $value) {
      //CEK STATUS SUDAH KOMPLETE ATAU BELUM
      $sql_sts    = "SELECT
                      nomor_job,
                      tanggal_produksi,
                      SUM(
                        CASE
                          WHEN shift_1_qty IS NULL
                          THEN 0
                          ELSE CAST(shift_1_qty AS DECIMAL)
                        END
                        +
                        CASE
                          WHEN shift_2_qty IS NULL
                          THEN 0
                          ELSE CAST(shift_2_qty AS DECIMAL)
                        END
                        +
                        CASE
                          WHEN shift_3_qty IS NULL
                          THEN 0
                          ELSE CAST(shift_3_qty AS DECIMAL)
                        END
                      ) AS subtotal
                    FROM tbl_data_pershift_dt a
                    WHERE nomor_job = '$value->NoBuktiJob' 
                    AND tanggal_produksi = '$value->TGL_SCAN_WH'
                    GROUP BY nomor_job, tanggal_produksi";
      $q_sts      = $second_DB->query($sql_sts);
      $cek_sts    = $q_sts->num_rows();
      if ($cek_sts > 0) {
        $res_sts    = $q_sts->row();
        $qty_shift  = $res_sts->subtotal;
        if ($res_sts->subtotal == $value->TOTAL_SCAN_WH) {
          $sts_button   = '<button class="btn btn-success btn-block btn-sm">COMPLETED</button>';
        } else {
          $sts_button   = '<button class="btn btn-danger btn-block btn-sm">OPEN</button>';
        }
      } else {
        $sts_button     = '<button class="btn btn-secondary btn-block btn-sm">NOT IN</button>';
      }
      

      $total      = $total + floatval($value->TOTAL_SCAN_WH);
      $isi        = "'" . $value->NoBuktiJob . "', '" . $value->PartName . "', '".$value->PartID."', '".number_format($value->TOTAL_SCAN_WH, 0)."', '".$value->TGL_SCAN_WH."'";
      $content .= '<tr>
                      <td class="text-right">'.$no++.'</td>
                      <td class="text-right">
                        <button onclick="modal_input_shift('.$isi.')" class="btn btn-warning btn-sm">INPUT HASIL</button>
                      </td>
                      <td class="text-center">'.$value->TGL_SCAN_WH.'</td>
                      <td class="text-right">'.number_format($value->TOTAL_SCAN_WH, 0).'</td>
                      <td class="text-right">'.number_format($qty_shift, 0).'</td>
                      <td class="text-center">'.$sts_button.'</td>
                  </tr>';
      $footer   = '<tr class="bg-info">
                      <th class="text-right" colspan="3">TOTAL</th>
                      <th class="text-right">'.number_format($total, 0).'</th>
                      <th colspan="2"></th>
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

  //SIMPAN DATA PER SHIFT
  public function simpan_data_per_shift() {
    $total_qty_input  = 0;
    $kode_detail      = $this->input->post('kode_detail');
    $job_nomor        = $this->input->post('job_nomor');
    $job_qty          = floatval($this->input->post('job_qty'));
    $part_id          = $this->input->post('part_id_');
    $qty_shift_1      = floatval(str_replace(',', '', $this->input->post('qty_shift_1')));
    $qty_shift_2      = floatval(str_replace(',', '', $this->input->post('qty_shift_2')));
    $qty_shift_3      = floatval(str_replace(',', '', $this->input->post('qty_shift_3')));
    $label_shift_1    = $this->input->post('label_shift_1');
    $label_shift_2    = $this->input->post('label_shift_2');
    $label_shift_3    = $this->input->post('label_shift_3');
    $qty_produksi     = floatval($this->input->post('qty_produksi'));
    $tanggal_produksi = $this->input->post('tanggal_produksi');
    $second_DB        = $this->load->database('bjsmas01_db', TRUE);

    //DATA FOR TABLE HEADER
    $data_header = array(
      'nomor_job'    => $job_nomor,
      'quantity_job' => $job_qty,
      'part_id'      => $part_id,
      'created_date' => date('Y-m-d H:i:s'),
      'created_by'   => $this->session->userdata('user_code')
    );

    //DATA FOR TABLE DETAIL
    $data_detail = array(
      'nomor_job'             => $job_nomor,
      'tanggal_produksi'      => $tanggal_produksi,
      'total_qty_shift'       => $qty_produksi,
      'shift_1_qty'           => $qty_shift_1,
      'shift_1_created_date'  => $qty_shift_1 == 0 ? NULL : date('Y-m-d H:i:s'),
      'shift_1_created_by'    => $qty_shift_1 == 0 ? NULL : $this->session->userdata('user_code'),
      'shift_2_qty'           => $qty_shift_2,
      'shift_2_created_date'  => $qty_shift_2 == 0 ? NULL : date('Y-m-d H:i:s'),
      'shift_2_created_by'    => $qty_shift_2 == 0 ? NULL : $this->session->userdata('user_code'),
      'shift_3_qty'           => $qty_shift_3,
      'shift_3_created_date'  => $qty_shift_3 == 0 ? NULL : date('Y-m-d H:i:s'),
      'shift_3_created_by'    => $qty_shift_3 == 0 ? NULL : $this->session->userdata('user_code')
    );

    //CEK DAHULU DI TABLE HADER
    $query_cek  = $second_DB->query("SELECT * FROM tbl_data_pershift_hd WHERE nomor_job = '$job_nomor'");
    $cek        = $query_cek->num_rows();
    if ($cek == 0) {
      $insert_hd  = $second_DB->insert('tbl_data_pershift_hd', $data_header);
      if ($insert_hd) {
        $insert_dt  = $second_DB->insert('tbl_data_pershift_dt', $data_detail);
        if ($insert_dt) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Sukses menyimpan data ke table header dan detail",
              "header"      => $data_header,
              "detail"      => $data_detail
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Gagal menyimpan data ke table detail",
              "detail"      => $data_detail
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Gagal menyimpan data ke table header",
            "header"      => $data_header
          )
        );
      }
    } else {
      $total_qty_input = $qty_shift_1 + $qty_shift_2 + $qty_shift_3;
      $sql  = " SELECT SUM(
                          CASE
                            WHEN shift_1_qty IS NULL
                            THEN 0
                            ELSE shift_1_qty
                          END
                          +
                          CASE
                            WHEN shift_2_qty IS NULL
                            THEN 0
                            ELSE shift_2_qty
                          END
                          +
                          CASE
                            WHEN shift_3_qty IS NULL
                            THEN 0
                            ELSE shift_3_qty
                          END
                        ) as subtotal,
                nomor_job, tanggal_produksi, CAST(total_qty_shift AS decimal) AS total_qty
                FROM tbl_data_pershift_dt
                WHERE nomor_job = '$job_nomor' 
                AND tanggal_produksi = '$tanggal_produksi'
                AND CAST(total_qty_shift AS decimal) = '$qty_produksi'
                GROUP BY nomor_job, tanggal_produksi, total_qty_shift";
      $qu   = $second_DB->query($sql);
      $cek2 = $qu->num_rows();
      if ($cek2 == 0) {
        $insert_dt  = $second_DB->insert('tbl_data_pershift_dt', $data_detail);
        if ($insert_dt) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Sukses menyimpan data ke table header dan detail",
              "header"      => $data_header,
              "detail"      => $data_detail
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Gagal menyimpan data ke table detail",
              "detail"      => $data_detail
            )
          );
        }
      } else {
        
        $data_detail_update = array(
          'nomor_job'             => $job_nomor,
          'tanggal_produksi'      => $tanggal_produksi,
          'total_qty_shift'       => $qty_produksi,
          'shift_1_qty'           => $qty_shift_1,
          'shift_1_created_date'  => $qty_shift_1 == 0 ? NULL : date('Y-m-d H:i:s'),
          'shift_1_created_by'    => $qty_shift_1 == 0 ? NULL : $this->session->userdata('user_code'),
          'shift_2_qty'           => $qty_shift_2,
          'shift_2_created_date'  => $qty_shift_2 == 0 ? NULL : date('Y-m-d H:i:s'),
          'shift_2_created_by'    => $qty_shift_2 == 0 ? NULL : $this->session->userdata('user_code'),
          'shift_3_qty'           => $qty_shift_3,
          'shift_3_created_date'  => $qty_shift_3 == 0 ? NULL : date('Y-m-d H:i:s'),
          'shift_3_created_by'    => $qty_shift_3 == 0 ? NULL : $this->session->userdata('user_code')
        );

        $update_dt = $second_DB->update('tbl_data_pershift_dt', $data_detail_update, array('id' => $kode_detail));
        if ($update_dt) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Sukses mengupdate data",
              "data"        => $data_detail_update
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Gagal mengupdate data",
              "data"        => $data_detail_update
            )
          );
        }
      }
    }
  }

  //CHECK APAKAH DATA SUDAH ADA
  public function check_data_shift() {
    $second_DB        = $this->load->database('bjsmas01_db', TRUE);
    $job_no           = $this->input->post('job_no');
    $production_date  = $this->input->post('production_date');
    $production_qty   = $this->input->post('production_qty');

    $sql = "SELECT id, nomor_job, tanggal_produksi, 
            CAST(total_qty_shift AS DECIMAL) AS total_qty,
            CAST(shift_1_qty AS DECIMAL) AS qty_shift_1,
            CAST(shift_2_qty AS DECIMAL) AS qty_shift_2,
            CAST(shift_3_qty AS DECIMAL) AS qty_shift_3
            FROM tbl_data_pershift_dt 
            WHERE nomor_job = '$job_no' 
            AND tanggal_produksi = '$production_date'
            AND CAST(total_qty_shift AS decimal) = '$production_qty'";
    $qu  = $second_DB->query($sql);
    $cek = $qu->num_rows();
    if ($cek > 0) {
      $data = $qu->row();
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Sukses menampilkan data",
          "data"        => $data
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data tidak ditemukan",
          "data"        => array()
        )
      );
    }
  }

  //LAPORAN VIEW SHIFT
  public function laporan_job_per_shift() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Laporan Job Per Shift";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/laporan_shift', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
  }

  //LAPORAN VIEW SHIFT LIST
  public function laporan_job_per_shift_list() {
    $draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $start_date     = $this->input->post('start_date');
		$end_date       = $this->input->post('end_date');

    $sql = "SELECT a.nomor_job, a.part_id, b.PartName,
            CAST(quantity_job AS DECIMAL) AS qty_job, a.created_date
            FROM tbl_data_pershift_hd a
            LEFT JOIN Ms_Part b ON b.PartID =  a.part_id
            WHERE CAST(a.created_date AS DATE) BETWEEN '$start_date' AND '$end_date'
            ORDER BY a.created_date DESC";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;

		foreach ($result as $key => $value) {
      $isi = "'".$value->nomor_job."', '".$value->part_id."', '".$value->PartName."', '".number_format($value->qty_job)."'"; 
			$data[]  = array(
				$no++,
        '<button class="btn btn-success btn-sm" onclick="cek_detail_transaksi('.$isi.')"><i class="fa fa-eye"></i></button>',
				$value->nomor_job,
				$value->part_id,
				$value->PartName,
				number_format($value->qty_job),
				substr($value->created_date, 0, -4)
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

  public function laporan_job_detail_list() {
    $job_no   = $this->input->post("job_no");

    $sql = "SELECT
            a.nomor_job,
            a.tanggal_produksi,
            CAST(a.total_qty_shift AS DECIMAL) AS total_qty_wh,
            CAST(a.shift_1_qty AS DECIMAL) AS qty_shift_1,
            CAST(a.shift_2_qty AS DECIMAL) AS qty_shift_2,
            CAST(a.shift_3_qty AS DECIMAL) AS qty_shift_3,
            SUM(
              CASE
                WHEN a.shift_1_qty IS NULL
                THEN 0
                ELSE CAST(a.shift_1_qty AS DECIMAL)
              END
              +
              CASE
                WHEN a.shift_2_qty IS NULL
                THEN 0
                ELSE CAST(a.shift_2_qty AS DECIMAL)
              END
              +
              CASE
                WHEN a.shift_3_qty IS NULL
                THEN 0
                ELSE CAST(a.shift_3_qty AS DECIMAL)
              END
            ) AS subtotal
          FROM tbl_data_pershift_dt a
          WHERE a.nomor_job = '$job_no'
          GROUP BY a.nomor_job, a.tanggal_produksi, a.total_qty_shift, 
            a.shift_1_qty, a.shift_2_qty, a.shift_3_qty
          ORDER BY tanggal_produksi DESC";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
		$query 				= $second_DB->query($sql);
		$result 			= $query->result();
		$data 				= [];
		$no 					= 1;
    $content      = "";
    $sts          = "";

    foreach ($result as $key => $value) {

      $sql_date = "SELECT
                    id,
                    nomor_job,
                    tanggal_produksi,
                    shift_1_created_date AS tgl_shift_1,
                    shift_2_created_date AS tgl_shift_2,
                    shift_3_created_date AS tgl_shift_3
                  FROM tbl_data_pershift_dt
                  WHERE nomor_job = '$value->nomor_job' AND tanggal_produksi = '$value->tanggal_produksi'";
      $q_date   = $second_DB->query($sql_date);
      $res_date = $q_date->row();

      if ($value->total_qty_wh == $value->subtotal) {
        $sts = '<button class="btn btn-success btn-block btn-sm">COMPLETED</button>';
      } else {
        $sts = '<button class="btn btn-danger btn-block btn-sm">OPEN</button>';
      }
      
      $row   = array();
			$row[] = $no++;
      $row[] = $sts;
      $row[] = $value->tanggal_produksi;
			$row[] = number_format($value->total_qty_wh);
      $row[] = number_format($value->subtotal);
			$row[] = number_format($value->qty_shift_1);
			$row[] = $res_date->tgl_shift_1 == NULL ? '-' : substr($res_date->tgl_shift_1, 0, -4);
      $row[] = number_format($value->qty_shift_2);
			$row[] = $res_date->tgl_shift_2 == NULL ? '-' : substr($res_date->tgl_shift_2, 0, -4);
      $row[] = number_format($value->qty_shift_3);
			$row[] = $res_date->tgl_shift_3 == NULL ? '-' : substr($res_date->tgl_shift_3, 0, -4);
		
			$data[] = $row;
    }

    echo json_encode(
      array(
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Sukses menampilkan data',
        'html'        => $content,
        'data'        => $data
      )
    );
  }
}