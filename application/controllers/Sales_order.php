<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_Order extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));
    $this->load->model('auth_model', 'auth');
    if ($this->auth->isNotLogin());
    $this->contoller_name = $this->router->class;
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('barcode_model', 'barcode_sales');
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']   = "SALES";
      $data['nama_halaman']   = "SALES ORDER BY PARTID";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";
      log_helper($log_url, $log_type, $log_data);
      $this->load->view('adminx/salesorder/sales_order_view', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }


  public function sales_order_by_po()
  {
    $draw       = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');

    $year1  = date('Y', strtotime($start_date));
    $year2  = date('Y', strtotime($end_date));
    $month1 = date('m', strtotime($start_date));
    $month2 = date('m', strtotime($end_date));
    $periode_awal = $start_date;
    $periode_now = $year2 . '' . $month2;

    $sql_awal = "
    SELECT A.PartID,A.PartName,A.Qty_Order,A.Qty_Sent,A.Qty_sisa,isnull(B.STOK,0.0) AS Stok FROM (
      select PartID, PartName,SUM(Qty_Order) as Qty_Order,SUM(Qty_Sent) as Qty_Sent,SUM(qty_sisa) as Qty_sisa from (
      Select a.PartID, b.PartName, f.PartnerName,e.PoCustomer, a.NoBukti,cast(e.TGL as date) as tgl, a.Qty as Qty_Order, 
      a.Qty_Terkirim + isnull(sum(d.Qty),0.0) as Qty_Sent,(a.Qty - (a.Qty_Terkirim + isnull(sum(d.Qty),0.0))) as Qty_sisa, a.UnitID
      From Trans_SODt$periode_now a Inner Join Ms_Part b on a.PartId = b.PartID 
      left join Trans_SJHD$periode_now c on a.NoBukti = c.NoReff and a.CompanyCode = c.CompanyCode 
      left join Trans_SJDT$periode_now d on c.NoBukti = d.NoBukti and c.CompanyCode = d.CompanyCode and a.PartID = d.PartID 
      left join Trans_SOHd$periode_now e on a.NoBukti = e.NoBukti
      left join Ms_Partner f on f.PartnerID = e.CustomerID
      Group by a.PartID, b.PartName, f.PartnerName,e.PoCustomer,a.NoBukti,e.TGL, a.Qty, a.Qty_Terkirim,  a.UnitID
      ) a
      GROUP BY PartID, PartName
      ) A
      LEFT JOIN (
      SELECT PartID, SUM(QTY) AS STOK FROM (
        SELECT PartId, UnitId, LocationId, Qty, Tgl, TypeTrans, NoBukti, PartName, OtherID, Material, NoBuktiReff, Spesifikasi, InventoryID, TglAwal, HPPAkhir, StockMin, StockMax, Selisih, Title, NomerPo 
        FROM (
          SELECT a.PartId, a.UnitID, a.LocationID, a.Qty, a.Tgl, a.TypeTrans, a.NoBukti, c.PartName, c.OtherID, c.Material, a.NoBuktiReff, c.Keterangan AS Spesifikasi, '(' + c.TypeInventoryID + ') ' + d.Nama AS InventoryID, '6/1/2023' AS TglAwal, e.HPPAkhir, c.StockMin, c.StockMax, (a.Qty - c.StockMin) AS Selisih, 'REPORT CLOSING BALANCE OF STOCK' AS Title, f.NomerPo  
          FROM Buku_Stock$periode_now a 
          INNER JOIN Ms_CategoryPart b ON LEFT(a.PartID, LEN(b.CategoryID)) = b.CategoryID 
          INNER JOIN Ms_Part c ON a.PartID = c.PartID 
          INNER JOIN Ms_Part_HPP$periode_now e ON e.PartID = c.PartID 
          INNER JOIN Ms_TypeInventory d ON c.TypeInventoryID = d.TypeInventoryID 
          LEFT JOIN Trans_Job$periode_now f ON a.PartID = f.PartID AND a.NoBuktiReff = f.NoBukti 
          WHERE TypeTrans <> 'AWAL'
          UNION ALL
          SELECT a.PartId, a.UnitID, a.LocationID, SUM(a.Qty) AS Qty, a.Tgl, a.TypeTrans, a.NoBukti, c.PartName, c.OtherID, c.Material, a.NoBuktiReff, c.Keterangan AS Spesifikasi, '(' + c.TypeInventoryID + ') ' + d.Nama AS InventoryID, '6/1/2023' AS TglAwal, e.HPPAkhir, c.StockMin, c.StockMax, (SUM(a.Qty) - c.StockMin) AS Selisih, 'REPORT CLOSING BALANCE OF STOCK' AS Title, f.NomerPo 
          FROM Buku_Stock$periode_now a 
          INNER JOIN Ms_CategoryPart b ON LEFT(a.PartID, LEN(b.CategoryID)) = b.CategoryID 
          INNER JOIN Ms_Part c ON a.PartID = c.PartID 
          INNER JOIN Ms_Part_HPP$periode_now e ON e.PartID = c.PartID 
          INNER JOIN Ms_TypeInventory d ON c.TypeInventoryID = d.TypeInventoryID 
          LEFT JOIN Trans_Job$periode_now f ON a.PartID = f.PartID AND a.NoBuktiReff = f.NoBukti 
          WHERE TypeTrans = 'AWAL'
          GROUP BY a.PartId, a.UnitID, a.LocationID, a.Tgl, a.TypeTrans, a.NoBukti, c.PartName, c.OtherID, c.Material, a.NoBuktiReff, c.Keterangan, d.Nama, e.HPPAkhir, c.TypeInventoryID, c.StockMin, c.StockMax, f.NomerPo 
        ) a
      ) STOCK
      WHERE  Qty <> 0 and LocationID in ('','WH-FG','WH-FG01','WH-GRS00','WH-GRS01', 'WH-R','WH-R01')
      GROUP BY PARTID
      ) B ON A.PartID = B.PartID";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    $query         = $second_DB->query($sql_awal);
    $result       = $query->result();
    $data         = [];
    $no           = 1;

    foreach ($result as $key => $value) {

      $wip = 0;
      $partid = "'" . $value->PartID . "'";
      if ($value->Qty_sisa == 0) {
        $hasil = ($value->Stok + $wip);
      } else {
        $hasil = ($value->Stok + $wip) - $value->Qty_sisa;
      }

      if ($hasil < 0) {
        $total = '<div class="p-2 mb-2 text-right bg-danger text-white">' . number_format($hasil, 0) . '</div>';
      } else {
        $total = '<div class="p-2 mb-2 text-right bg-success text-white ">' . number_format($hasil, 0) . '</div>';
      }

      $data[] = array(
        $no++,
        $value->PartID,
        $value->PartName,
        number_format($value->Qty_Order, 0),
        number_format($value->Stok, 0),
        $wip,
        number_format($value->Qty_Sent, 0),
        number_format($value->Qty_sisa, 0),
        $total,
        '<a href="' . base_url() . 'sales_order/sales_order_by_poid/' . base64_encode($value->PartID) . '"  target="_blank"><button class="btn btn-danger btn-block btn-sm">DETAILS</button></a>'
      );
    }

    $result = array(
      "draw"             => $draw,
      "recordsTotal"     => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data"             => $data
    );

    echo json_encode($result);
  }

  public function sales_order_by_poid($partID)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']   = "SALES";
      $data['nama_halaman']   = "SALES ORDER BY PO";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['partID']         = base64_decode($partID);

      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);

      $this->load->view('adminx/salesorder/sales_order_by_po_view', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function sales_order_by_po_list()
  {
    $draw       = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $partID     = $this->input->post('partID');
    $end_date   = $this->input->post('periode_now');

    $year2  = date('Y', strtotime($end_date));
    $month2 = date('m', strtotime($end_date));
    $periode_now = $year2 . '' . $month2;

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $sql     = "select * from (
      Select a.PartID, b.PartName, f.PartnerName,e.PoCustomer, a.NoBukti,cast(e.TGL as date) as tgl, a.Qty as Qty_Order, 
      a.Qty_Terkirim + isnull(sum(d.Qty),0.0) as Qty_Sent,(a.Qty - (a.Qty_Terkirim + isnull(sum(d.Qty),0.0))) as Qty_sisa, a.UnitID
      From Trans_SODT$periode_now  a Inner Join Ms_Part b on a.PartId = b.PartID 
      left join Trans_SJHD$periode_now  c on a.NoBukti = c.NoReff and a.CompanyCode = c.CompanyCode 
      left join Trans_SJDT$periode_now d on c.NoBukti = d.NoBukti and c.CompanyCode = d.CompanyCode and a.PartID = d.PartID 
      left join Trans_SOHd$periode_now  e on a.NoBukti = e.NoBukti
      left join Ms_Partner f on f.PartnerID = e.CustomerID
      Group by a.PartID, b.PartName, f.PartnerName,e.PoCustomer,a.NoBukti,e.TGL, a.Qty, a.Qty_Terkirim,  a.UnitID
      ) a
      where  PartID='$partID' order by PartID, tgl desc";

    $query         = $second_DB->query($sql);
    $result       = $query->result();
    $data         = [];
    $no           = 1;

    foreach ($result as $key => $value) {

      $query         = $second_DB->query($sql);
      $result       = $query->result();
      $data         = [];
      $no           = 1;

      foreach ($result as $key => $value) {
        $NoBukti = "'" . $value->NoBukti . "'";
        if ($value->Qty_Order == $value->Qty_Sent) {
          $Qty_Sent = '<div class="p-2 mb-2 text-right bg-success text-white">' . number_format($value->Qty_Sent, 0) . '</div>';
        } else {
          $Qty_Sent = '<div class="p-2 mb-2 text-right bg-danger text-white ">' . number_format($value->Qty_Sent, 0) . '</div>';
        }
        $data[] = array(
          $no++,
          $value->PartID,
          $value->PartName,
          $value->PartnerName,
          $value->PoCustomer,
          $value->NoBukti,
          $value->tgl,
          number_format($value->Qty_Order, 0),
          $Qty_Sent,
          number_format($value->Qty_sisa, 0),
          $value->UnitID,
          '<a href="' . base_url() . 'sales_order_customer/sales_order_by_do_customer/' . base64_encode($value->PartID) . '" onclick="simpanNobukti(' . $NoBukti . ')" target="_blank"><button class="btn btn-danger btn-block btn-sm">DETAILS</button></a>'
        );
      }

      $result = array(
        "draw"             => $draw,
        "recordsTotal"     => $query->num_rows(),
        "recordsFiltered" => $query->num_rows(),
        "data"             => $data
      );

      echo json_encode($result);
      exit();
    }
  }
}
