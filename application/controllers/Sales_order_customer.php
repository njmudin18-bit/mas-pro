<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_Order_Customer extends CI_Controller
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
      $data['nama_halaman']   = "SALES ORDER BY PO CUSTOMER";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);

      $this->load->view('adminx/salesorder/test', $data, FALSE);
      //$this->load->view('adminx/salesorder/sales_order_by_po_customer_view', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function sales_order_by_po()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $start_date = "2023-07-04"; //$this->input->post('start_date');
    $end_date   = "2023-07-18"; //$this->input->post('end_date');

    $year1  = date('Y', strtotime($start_date));
    $year2  = date('Y', strtotime($end_date));
    $month1 = date('m', strtotime($start_date));
    $month2 = date('m', strtotime($end_date));
    $periode_now = $year2 . '' . $month2;

    $sql = "SELECT TOP 10
              * 
            FROM 
              (
                SELECT 
                  a.PartID, 
                  b.PartName, 
                  f.PartnerName, 
                  e.PoCustomer, 
                  a.NoBukti, 
                  cast(e.TGL as date) as tgl, 
                  a.Qty as Qty_Order, 
                  a.Qty_Terkirim + isnull(
                    sum(d.Qty), 
                    0.0
                  ) as Qty_Sent, 
                  (
                    a.Qty - (
                      a.Qty_Terkirim + isnull(
                        sum(d.Qty), 
                        0.0
                      )
                    )
                  ) as Qty_sisa, 
                  a.UnitID 
                From 
                  Trans_SODT$periode_now a 
                  Inner Join Ms_Part b on a.PartId = b.PartID 
                  left join Trans_SJHD$periode_now c on a.NoBukti = c.NoReff 
                  and a.CompanyCode = c.CompanyCode 
                  left join Trans_SJDT$periode_now d on c.NoBukti = d.NoBukti 
                  and c.CompanyCode = d.CompanyCode 
                  and a.PartID = d.PartID 
                  left join Trans_SOHd$periode_now e on a.NoBukti = e.NoBukti 
                  left join Ms_Partner f on f.PartnerID = e.CustomerID 
                Group by 
                  a.PartID, 
                  b.PartName, 
                  f.PartnerName, 
                  e.PoCustomer, 
                  a.NoBukti, 
                  e.TGL, 
                  a.Qty, 
                  a.Qty_Terkirim, 
                  a.UnitID
              ) a 
            order by 
              PartID, 
              tgl desc
            ";


    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $query        = $second_DB->query($sql);
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
        $value->tgl . "T00:00",
        number_format($value->Qty_Order, 0),
        $Qty_Sent,
        number_format($value->Qty_sisa, 0),
        $value->UnitID,
        '<a href="' . base_url() . 'sales_order_customer/sales_order_by_do_customer/' . base64_encode($value->PartID) . '" onclick="simpanNobukti(' . $NoBukti . ')" target="_blank"><button class="btn btn-danger btn-block btn-sm">DETAILS</button></a>'
      );
    }

    $result = array(
      "draw"              => $draw,
      "recordsTotal"      => $query->num_rows(),
      "recordsFiltered"   => $query->num_rows(),
      "data"              => $result
    );

    echo json_encode($result);
  }

  public function sales_order_by_po_OLD()
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
    $periode_now = $year2 . '' . $month2;

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
      order by PartID, tgl desc
      ";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
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
  }

  public function sales_order_by_do_customer($partID)
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']   = "SALES";
      $data['nama_halaman']   = "SALES ORDER BY DO";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['partID']         = base64_decode($partID);

      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);

      $this->load->view('adminx/salesorder/sales_order_by_do_view', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function sales_order_by_do_list()
  {
    $draw       = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $partID     = $this->input->post('partID');
    $noso     = $this->input->post('no_so');
    $start_date   = $this->input->post('periode_awal');
    $end_date   = $this->input->post('periode_now');

    $year1  = date('Y', strtotime($start_date));
    $year2  = date('Y', strtotime($end_date));
    $month1 = date('m', strtotime($start_date));
    $month2 = date('m', strtotime($end_date));

    $interval   = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
    $sql_SJHD = '';
    $sql_SJDT = '';
    for ($i = 0; $i < $interval; $i++) {
      $tempDate       = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' months'));
      $tempTableName  = date('Y', strtotime($tempDate)) . date('m', strtotime($tempDate));

      if ($i < $interval - 1) {
        $sql_SJHD .= "SELECT *
                      FROM Trans_SJHD$tempTableName
                      UNION ";
        $sql_SJDT .= "SELECT *
                      FROM Trans_SJDT$tempTableName
                      UNION ";
      } else {
        $sql_SJHD .= "SELECT *
                      FROM Trans_SJHD$tempTableName";
        $sql_SJDT .= "SELECT *
                      FROM Trans_SJDT$tempTableName";
      }
    }
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $sql_awal     = 'select a.NoBukti, a.NoReff, a.NoPlanning, a.TGL,b.PartID, b.Qty from (';
    $left_join    = ' ) A
                      left join 
                      (';
    $sql_akhir    = ') b ON A.NoBukti = b.NoBukti ';
    $where        = " WHERE  b.PartID='$partID' AND a.NoReff='$noso' order by a.TGL desc";
    $sql_new      = $sql_awal . $sql_SJHD . $left_join . $sql_SJDT . $sql_akhir . $where;

    // echo $sql_new;
    // exit;
    $query        = $second_DB->query($sql_new);
    $result       = $query->result();
    $data         = [];
    $no           = 1;
    foreach ($result as $key => $value) {
      $data[] = array(
        $no++,
        $value->NoReff,
        $value->NoBukti,
        $value->NoPlanning,
        substr($value->TGL, 0, -4),
        $value->PartID,
        number_format($value->Qty, 0)
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
}
