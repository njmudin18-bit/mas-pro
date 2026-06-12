<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tembaga extends CI_Controller
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

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    $data['group_halaman']    = "Warehouse";
    $data['nama_halaman']     = "Control Tembaga";
    $data['icon_halaman']     = "icon-layers";
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['list']             = $this->db->query("SELECT * FROM table_phone_ext")->result();

    $this->load->view('adminx/warehouse/control_tembaga', $data, FALSE);
  }

  public function get_po_number()
  {
    $Supplier   = $this->input->post('Supplier');
    $Search     = $this->input->post('search');
    $StartDate  = $this->input->post('StartDate');
    $EndDate    = $this->input->post('EndDate');

    $Sql    = "EXEC dbo.GetPOCustomerDynamicByDate @startDate = '$StartDate', @endDate = '$EndDate', @SupplierID = '$Supplier'";
    $Query  = $this->BJGMAS01->query($Sql);
    $Data   = array();
    foreach ($Query->result() as $Row) {
      $Data[] = array(
        "id"    => $Row->PoNo,
        "name"  => $Row->PoNo
      );
    }

    echo json_encode($Data);
  }

  public function get_qc_scanned($BarcodeNumber) 
  {
    $Query = $this->BJGMAS01->get_where('Trans_IncomingScan', array('BarcodeNumber' => $BarcodeNumber));

    if ($Query->num_rows() > 0) {
      return $Query->row();
    } else {
      return null;
    }
  }

  public function get_fifo_card($Month)
  {
    $Data   = $this->BJGMAS01->get_where('Ms_ColorShape', array('MonthNumber' => $Month))->row();
    $Isi    = '';
    if ($Data->Shapes == 'Kotak') {
      $Isi  = '<div class="avatar-md" style="margin-left: auto;margin-right: auto;border: 2px solid black; background-color: '.$Data->Colors.' !important;"></div>';
    } else {
      $Isi  = '<svg width="75" height="75">
                <polygon points="35, 0 0, 70 70, 70" style="fill:'.$Data->Colors.';stroke:black;stroke-width:2" />
              </svg>';
    }

    return $Isi;
  }

  public function tembaga_report_list()
  {
    $Draw           = intval($this->input->get("draw"));
    $Start          = intval($this->input->get("start"));
    $Length         = intval($this->input->get("length"));
    $Supplier       = $this->input->post('supplier');
    $PONumber       = $this->input->post('po_number');
    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    //$Where          = " WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate' AND SupplierID IN ('TE002', 'IN022', 'SE008') ";

    $Where          = "";
    $Wheres         = "";
    if ($Supplier == 'All') {
      $Wheres       = " AND a.SupplierID IN ('IN022', 'TE002', 'SE008') ";
    } else if ($Supplier == 'TE002') {
      $Wheres       = " AND a.SupplierID IN ('TE002') ";
    } else if ($Supplier == 'SE008') {
      $Wheres       = " AND a.SupplierID IN ('SE008') ";
    } else {
      $Wheres       = " AND a.SupplierID IN ('IN022') ";
    }

    if (!empty($PONumber) && $PONumber != 'all') {
      $Where .= " AND a.PONumber = '$PONumber' ";
    }

    // $Sql            = "SELECT
    //                     a.PartID, b.PartName, CAST(a.CreateDate AS DATE) AS DateIn, a.PONumber,
    //                     a.SupplierID, a.SupplierType, a.SupplierName, a.BarcodeNumber,
    //                     ROUND(COALESCE(a.Weight, 0), 2) AS Weight,
    //                     LPAD(MONTH(a.CreateDate), 2, '0') AS MonthNumber
    //                   FROM trans_fifocard a
    //                   LEFT JOIN ms_part b ON b.PartID = a.PartID
    //                   WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
    //                   $Wheres
    //                   $Where
    //                   ORDER BY a.CreateDate DESC";
    $Sql = "SELECT 
                a.PartID,
                b.PartName,
                CAST(a.CreateDate AS DATE) AS DateIn,
                a.PONumber,
                a.SupplierID,
                a.SupplierType,
                a.SupplierName,
                a.BarcodeNumber,
                ROUND(ISNULL(a.Weight, 0), 2) AS Weight,
                RIGHT('0' + CAST(MONTH(a.CreateDate) AS VARCHAR(2)), 2) AS MonthNumber
            FROM Trans_FifoCard a
            LEFT JOIN Ms_Part b ON b.PartID = a.PartID
            WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
            $Wheres
            $Where
            ORDER BY a.CreateDate DESC";
            //echo $Sql; exit;
    $Query      = $this->BJGMAS01->query($Sql);
    $Result     = $Query->result();
    $Data       = [];
    $No         = 1;
    $Inspector  = '';
    $Noted      = '';
        
    foreach ($Result as $key => $value) {
      $QcData = $this->get_qc_scanned($value->BarcodeNumber);
      if ($QcData !== null) {
        $Inspector  = !empty($QcData->CreateBy) ? $QcData->CreateBy : '-';
        $Noted      = !empty($QcData->Noted) ? $QcData->Noted : '-';
      } else {
        $Inspector  = '-';
        $Noted      = '-';
      }

      $Data[] = array(
        $No++,
        $value->PartName."<hr>| ".$value->PartID,
        $value->DateIn,
        $this->get_fifo_card($value->MonthNumber),
        $value->PONumber,
        $value->SupplierType.". ".$value->SupplierName,
        $value->Weight,
        $value->BarcodeNumber,
        $Inspector,
        $Noted
      );
    }
    
    $Result = array(
      "draw"            => $Draw,
      "recordsTotal"    => $Query->num_rows(),
      "recordsFiltered" => $Query->num_rows(),
      "data"            => $Data
    );
    
    echo json_encode($Result);
    exit();
  }
}