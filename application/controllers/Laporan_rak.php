<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_rak extends CI_Controller
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
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');

    $this->BJGMAS01   = $this->load->database("bjsmas01_db", true);
    $this->MYSQL      = $this->load->database("mysql", true);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Laporan Data Rak By Lokasi";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['wh']               = $this->BJGMAS01->get_where('Ms_WarehouseStock', array('Aktif' => '1'))->result();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/rak/laporan', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function daftar_laporan_by_wh()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		$StartDate    = $this->input->post('start_date');
		$EndDate 	    = $this->input->post('end_date');
		$WHLokasi	    = $this->input->post('wh_lokasi');
    if ($WHLokasi == 'All') {
      $Where      = " ";
    } else {
      $Where      = " AND WHLokasi = '$WHLokasi' ";
    }

		$Sql 	        = "WITH LatestDates AS (
                      SELECT 
                        Rak, SubRak, QrSubRak, PartID,
                      SoftDelete,
                        MAX(CreateDate) AS LatestCreateDate 
                      FROM 
                        Trans_RakContents 
                      WHERE
                        SoftDelete IS NULL
                        $Where
                      GROUP BY 
                        Rak, SubRak, QrSubRak, SoftDelete, PartID
                    ) 
                    SELECT 
                      a.Id, a.Rak, a.SubRak, b.PartName, a.Stock, 
                      a.Unit, a.PartID, a.WHLokasi, a.Noted, a.QrRak, 
                      a.QrSubRak, a.CreateDate, a.CreateBy 
                    FROM 
                      Trans_RakContents a 
                      JOIN LatestDates ld ON a.Rak = ld.Rak 
                      AND a.SubRak = ld.SubRak 
                      AND a.PartID = ld.PartID 
                      AND a.CreateDate = ld.LatestCreateDate 
                      LEFT JOIN Ms_Part b ON b.PartID = a.PartID 
                    ORDER BY 
                      a.Rak ASC";
		$Query        = $this->BJGMAS01->query($Sql);
		$Result 			= $Query->result();
		$Data 				= [];
		$No 					= 1;

		foreach ($Result as $key => $value) {
      $Isi    = base64_encode($value->PartID."/".$value->PartName);
			$Data[] = array(
				$No++,
        $value->WHLokasi,
				$value->Rak,
				$value->SubRak,
				$value->PartID,
				$value->PartName,
        //'<a href="'.base_url().'laporan_rak/item_detail/'.$Isi.'" target="_blank">'.$value->PartName.'</a>',
				number_format($value->Stock, 2),
				$value->Unit,
				$value->Noted == null ? '-' : $value->Noted,
				substr($value->CreateDate, 0, 19),
				$value->CreateBy
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
	}

  public function data_by_item() 
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Laporan Data Rak By Item";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['item']             = $this->get_wh_item();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/rak/laporan_by_item', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function daftar_laporan_by_item()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		$StartDate    = $this->input->post('start_date');
		$EndDate 	    = $this->input->post('end_date');
		$PartID	      = $this->input->post('items');
    if ($PartID == 'All') {
      $Where      = "";
    } else {
      $Where      = " AND PartID = '$PartID' ";
    }

		$Sql 	        = "WITH LatestDates AS (
                      SELECT 
                        Rak, SubRak, QrSubRak, PartID,
                      SoftDelete,
                        MAX(CreateDate) AS LatestCreateDate 
                      FROM 
                        Trans_RakContents 
                      WHERE
                        SoftDelete IS NULL
                        $Where
                      GROUP BY 
                        Rak, SubRak, QrSubRak, SoftDelete, PartID
                    ) 
                    SELECT 
                      a.Id, a.Rak, a.SubRak, b.PartName, a.Stock, 
                      a.Unit, a.PartID, a.WHLokasi, a.Noted, a.QrRak, 
                      a.QrSubRak, a.CreateDate, a.CreateBy 
                    FROM 
                      Trans_RakContents a 
                      JOIN LatestDates ld ON a.Rak = ld.Rak 
                      AND a.SubRak = ld.SubRak 
                      AND a.PartID = ld.PartID 
                      AND a.CreateDate = ld.LatestCreateDate 
                      LEFT JOIN Ms_Part b ON b.PartID = a.PartID 
                    ORDER BY 
                      a.Rak ASC";
                     //echo $Sql; exit;
		$Query        = $this->BJGMAS01->query($Sql);
		$Result 			= $Query->result();
		$Data 				= [];
		$No 					= 1;

		foreach ($Result as $key => $value) {
      $Isi    = base64_encode($value->PartID."/".$value->PartName);
			$Data[] = array(
				$No++,
        $value->WHLokasi,
				$value->Rak,
				$value->SubRak,
				$value->PartID,
				//'<a href="'.base_url().'laporan_rak/item_detail/'.$Isi.'" target="_blank">'.$value->PartName.'</a>',
				$value->PartName,
				number_format($value->Stock, 2),
				$value->Unit,
				$value->Noted == null ? '-' : $value->Noted,
				substr($value->CreateDate, 0, 19),
				$value->CreateBy
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
	}

  public function data_by_transaksi() 
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Laporan Transaksi Rak By Item";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['item']             = $this->get_wh_item();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/rak/laporan_by_transaksi', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function daftar_laporan_by_transaksi()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		//GET START AND END DATE
		$StartDate    = $this->input->post('start_date');
		$EndDate 	    = $this->input->post('end_date');
		$Items	      = $this->input->post('items');
    if ($Items == 'All') {
      $Where      = " WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate' ";
    } else {
      $Where      = " WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
                      AND a.PartID = '$Items' ";
    }

		$Sql 	        = "SELECT a.Id, a.NoBukti, a.NoMpr, a.PartID, b.PartName, 
                     CAST(a.Quantity AS DECIMAL(10, 2)) AS Quantity, a.Unit, 
                     a.Rak, a.SubRak, a.Destination, a.Noted, a.WHLokasi,
                     FORMAT(a.CreateDate, 'yyyy-MM-dd HH:mm:ss') AS CreateDate, a.CreateBy
                     FROM Trans_RakDesignation a
                     LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                     $Where
                     ORDER BY a.CreateDate DESC";
		$Query        = $this->BJGMAS01->query($Sql);
		$Result 			= $Query->result();
		$Data 				= [];
		$No 					= 1;

		foreach ($Result as $key => $value) {
      $Isi    = base64_encode($value->PartID."/".$value->PartName);
			$Data[] = array(
				$No++,
        $value->NoBukti == null ? '-' : $value->NoBukti,
				$value->NoMpr == null ? '-' : $value->NoMpr,
				$value->PartID,
        '<a href="'.base_url().'laporan_rak/item_detail/'.$Isi.'" target="_blank">'.$value->PartName.'</a>',
				number_format($value->Quantity, 2),
				$value->Unit,
        strtoupper($value->Destination),
				$value->WHLokasi == null ? '-' : $value->WHLokasi,
				$value->Noted == null ? '-' : $value->Noted,
				substr($value->CreateDate, 0, 19),
				$value->CreateBy
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
	}

  public function item_detail($hash)
  {
    $data['group_halaman']    = "Master Data";
    $data['nama_halaman']     = "Form Kontrol Pengeluaran Barang FIFO";
    $data['icon_halaman']     = "icon-layers";
    $Items                    = explode('/', base64_decode($hash));
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['PartID']           = $Items[0];
    $data['PartName']         = $Items[1];

    $this->load->view('adminx/warehouse/rak/form_kontrol', $data, FALSE);
  }

  public function daftar_laporan_item_detail()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$PartID 		= $this->input->post("PartID");

		$Sql 	      = "SELECT 
                    a.Id, a.StockEntryDate,
                    CAST(a.CreateDate AS DATE) AS Tanggal,
                    FORMAT(a.CreateDate, 'MM') AS Month,
                    a.Status, a.NoBukti, a.NoMpr, a.LotNumber,
                    CAST(a.Quantity AS DECIMAL(10, 2)) AS Quantity,
                    CAST(a.OldStock AS DECIMAL(10, 2)) AS OldStock,
                    CAST(a.Stock AS DECIMAL(10, 2)) AS Stock,
                    a.Unit, a.PartID, b.PartName, a.WHLokasi,
                    a.Rak, a.SubRak, a.Destination, a.Noted, a.CreateDate
                  FROM Trans_RakContents a
                  LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                  WHERE a.PartID = '$PartID' AND a.Status = 'OUT'
                  ORDER BY a.CreateDate DESC";
		$Query      = $this->BJGMAS01->query($Sql);
		$Result 		= $Query->result();
		$Data 			= [];
		$No 				= 1;

		foreach ($Result as $key => $value) {
			$Data[] = array(
				$No++,
        $value->Tanggal,
        $value->NoMpr == '' ? '-' : $value->NoMpr,
        $value->PartName,
        number_format($value->Quantity, 2),
        $value->Unit,
        $this->get_fifo_card($value->Month, $value->StockEntryDate),
        $value->WHLokasi,
        $value->Noted
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
	}

  public function daftar_laporan_item_detail_OLD()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$PartID 		= $this->input->post("PartID");

		$Sql 	      = "SELECT 
                    a.Id,
                    CAST(a.CreateDate AS DATE) AS Tanggal,
                    a.NoBukti,
                    CASE 
                        WHEN a.Status = 'KR' THEN 'OUT'
                        WHEN a.Status = 'TM' THEN 'IN'
                        ELSE a.Status
                    END AS StockStatus,
                    CAST(a.Quantity AS DECIMAL(10, 2)) AS Quantity,
                    CAST(a.OldStock AS DECIMAL(10, 2)) AS OldStock,
                    CAST(a.NewStock AS DECIMAL(10, 2)) AS NewStock,
                    a.NoMpr,
                    a.PartID, 
                    b.PartName, 
                    a.Unit, 
                    a.WHLokasi, 
                    a.Status,
                    a.Destination, 
                    a.Noted,
                    a.Rak,
		                a.SubRak,
                    a.CreateDate,
                    FORMAT(a.CreateDate, 'MM') AS Month
                  FROM Trans_RakDesignation a
                  LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                  WHERE a.PartID = '$PartID'
                  ORDER BY a.CreateDate DESC";
		$Query        = $this->BJGMAS01->query($Sql);
		$Result 			= $Query->result();
		$Data 				= [];
		$No 					= 1;

		foreach ($Result as $key => $value) {
			$Data[] = array(
				$No++,
        $value->Tanggal,
        $value->NoBukti,
        $value->StockStatus == 'IN' ? number_format($value->Quantity, 2) : '-',
        $value->StockStatus == 'OUT' ? number_format($value->Quantity, 2) : '-',
        $value->Noted == null ? '-' : $value->Noted,
        number_format($value->NewStock, 2),
        $value->Unit,
        $value->Rak,
        $value->SubRak,
        '',
        $this->get_fifo_card($value->Month),
        ''
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
	}

  public function master_stock_rak()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Mapping Stock Rak";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['rak']              = $this->get_rak();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/rak/master_stock_rak', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function master_stock_rak_list()
  {
    $draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$Rak 		    = $this->input->post("rak");
		$Baris 		  = $this->input->post("baris");
    $Where      = "";
    if ($Rak == 'All' && $Baris == 'All') {
      $Where    = " ";
    } elseif ($Rak != 'All' && $Baris == 'All') {
      $Where    = " QrRak = '$Rak' AND ";
    } elseif ($Rak != 'All' && $Baris != 'All') {
      $Where    = " QrRak = '$Rak' AND QrSubRak = '$Baris' AND ";
    }

    $Sql        = "WITH LatestDates AS (
                    SELECT 
                      Rak, SubRak, QrSubRak, PartID,
                    SoftDelete,
                      MAX(CreateDate) AS LatestCreateDate 
                    FROM 
                      Trans_RakContents 
                    WHERE
                      $Where
                      SoftDelete IS NULL
                    GROUP BY 
                      Rak, SubRak, QrSubRak, SoftDelete, PartID
                  ) 
                  SELECT 
                    a.Id, a.Rak, a.SubRak, b.PartName, a.Stock, 
                    a.Unit, a.PartID, a.WHLokasi, a.Noted, a.QrRak, 
                    a.QrSubRak, a.CreateDate, a.CreateBy 
                  FROM 
                    Trans_RakContents a 
                    JOIN LatestDates ld ON a.Rak = ld.Rak 
                    AND a.SubRak = ld.SubRak 
                    AND a.PartID = ld.PartID 
                    AND a.CreateDate = ld.LatestCreateDate 
                    LEFT JOIN Ms_Part b ON b.PartID = a.PartID 
                  ORDER BY 
                    a.Rak ASC";
                    //echo $Sql; exit;
		$Query      = $this->BJGMAS01->query($Sql);
		$Result 		= $Query->result();
		$Data 			= [];
		$No 				= 1;

		foreach ($Result as $key => $value) {
      $Isi    = base64_encode($value->PartID."/".$value->PartName);
			$Data[] = array(
				$No++,
				'<a href="'.base_url().'laporan_rak/item_detail/'.$Isi.'" target="_blank" class="btn btn-success" title="Form Kontrol Pengeluaran Barang FIFO">
          <i class="fa fa-indent"></i>
        </a>
        <a href="'.base_url().'laporan_rak/kartu_stock/'.$Isi.'" target="_blank" class="btn btn-warning" title="Kartu Stock Material Part">
          <i class="fa fa-list-alt"></i>
        </a>',
        $value->WHLokasi,
        $value->Rak,
        $value->SubRak,
        $value->PartID,
        $value->PartName,
        number_format($value->Stock, 2),
        $value->Unit,
        $value->Noted == null ? '-' : $value->Noted,
        substr($value->CreateDate, 0, 19),
        $value->CreateBy
			);
		}

		$Results = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Results);
		exit();
  }

  public function kartu_stock($hash)
  {
    $data['group_halaman']    = "Master Data";
    $data['nama_halaman']     = "Kartu Stock Material Part";
    $data['icon_halaman']     = "icon-layers";
    $Items                    = explode('/', base64_decode($hash));
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['PartID']           = $Items[0];
    $data['PartName']         = $Items[1];

    $this->load->view('adminx/warehouse/rak/kartu_stock', $data, FALSE);
  }

  public function kartu_stock_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));
		$PartID 		= $this->input->post("PartID");

		$Sql 	      = "SELECT 
                    a.Id, a.StockEntryDate,
                    CAST(a.CreateDate AS DATE) AS Tanggal,
                    FORMAT(a.CreateDate, 'MM') AS Month,
                    a.Status, a.NoBukti, a.NoMpr, a.LotNumber,
                    CAST(a.Quantity AS DECIMAL(10, 2)) AS Quantity,
                    CAST(a.OldStock AS DECIMAL(10, 2)) AS OldStock,
                    CAST(a.Stock AS DECIMAL(10, 2)) AS Stock,
                    a.Unit, a.PartID, b.PartName, a.WHLokasi,
                    a.Rak, a.SubRak, a.Destination, a.Noted, a.CreateDate
                  FROM Trans_RakContents a
                  LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                  WHERE a.PartID = '$PartID'
                  ORDER BY a.CreateDate DESC";
		$Query      = $this->BJGMAS01->query($Sql);
    $Result 	  = $Query->result();
    $Data 		  = [];
    $No 			  = 1;

    foreach ($Result as $key => $value) {
      $Data[] = array(
        $No++,
        $value->Tanggal,
        $value->Status == 'IN' ? number_format($value->Quantity, 2) : '-',
        $value->Status == 'OUT' ? number_format($value->Quantity, 2) : '-',
        $value->Noted == null ? '-' : $value->Noted,
        number_format($value->Stock, 2),
        $value->Unit,
        $value->Rak,
        $value->SubRak,
        $value->LotNumber == null ? '-' : $value->LotNumber,
        $this->get_fifo_card($value->Month, $value->StockEntryDate)
      );
    }

    $Results = array(
      "draw" 						=> $draw,
      "recordsTotal" 		=> $Query->num_rows(),
      "recordsFiltered" => $Query->num_rows(),
      "data" 						=> $Data
    );

    echo json_encode($Results);
    exit();
	}

  public function get_wh_item()
  {
    $Result = $this->BJGMAS01->select('a.PartID, b.PartName')
    ->from('Trans_RakContents a')
    ->join('Ms_Part b', 'b.PartID = a.PartID', 'left')
    ->group_by('a.PartID, b.PartName')
    ->order_by('b.PartName', 'ASC')
    ->get()
    ->result();

    return $Result;
  }

  public function get_rak()
  {
    $Result = $this->BJGMAS01->get_where('Trans_RakHD', array('Status' => 'Aktif'))->result();
    $Result = $this->BJGMAS01->select('Rak, WHLokasi, QRCode')
    ->from('Trans_RakHD')
    ->order_by('Rak', 'ASC')
    ->get()
    ->result();

    return $Result;
  }

  public function get_fifo_card($Month, $StockEntryDate)
  {
    $Data   = $this->MYSQL->get_where('ms_colorshape', array('MonthNumber' => $Month))->row();
    $Isi    = '';
    if ($Data->Shapes == 'Kotak') {
      $Isi  = '<div class="avatar-md" style="margin-left: auto;margin-right: auto;border: 2px solid black;">
                <div style="background-color: '.$Data->Colors.' !important;" class="avatar-title bg-warning-subtle text-black fs-12">
                </div>
              </div><br><small style="font-size: 10px;">'.$StockEntryDate.'</small>';
    } else {
      $Isi  = '<svg width="75" height="75">
                <polygon points="35, 0 0, 70 70, 70" style="fill:'.$Data->Colors.';stroke:black;stroke-width:2" />
              </svg><br><small style="font-size: 10px;">'.$StockEntryDate.'</small>';
    }

    return $Isi;
  }
}
