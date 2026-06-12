<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_Order_Do extends CI_Controller
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
      $data['nama_halaman']   = "SALES ORDER BY HISTORY DO";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);

      $this->load->view('adminx/salesorder/sales_order_by_do_so_view', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }


  public function sales_order_by_do()
  {
    $draw       = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $start_date   = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');
    // echo $start_date . $end_date;
    // exit;
    $year1  = date('Y', strtotime($start_date));
    $year2  = date('Y', strtotime($end_date));
    $month1 = date('m', strtotime($start_date));
    $month2 = date('m', strtotime($end_date));

    $interval   = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
    $sql_SJHD = '';
    $sql_SJDT = '';
    $sql_SOHD = '';
    for ($i = 0; $i < $interval; $i++) {
      $tempDate       = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' months'));
      $tempTableName  = date('Y', strtotime($tempDate)) . date('m', strtotime($tempDate));

      if ($i < $interval - 1) {
        $sql_SJHD .= "SELECT *
                      FROM Trans_SJHD$tempTableName
                      UNION ";
        $sql_SOHD .= "SELECT *
                       FROM Trans_SOHD$tempTableName
                       UNION ";
        $sql_SJDT .= "SELECT *
                      FROM Trans_SJDT$tempTableName
                      UNION ";
      } else {
        $sql_SJHD .= "SELECT *
                      FROM Trans_SJHD$tempTableName";
        $sql_SOHD .= "SELECT *
                         FROM Trans_SOHD$tempTableName";
        $sql_SJDT .= "SELECT *
                      FROM Trans_SJDT$tempTableName";
      }
    }
    // echo "qq";
    // exit;
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $sql_awal     = 'select a.NoBukti, d.PartnerName,c.PoCustomer, a.NoReff, a.NoPlanning, a.TGL,b.PartID, b.Qty from (';
    $left_join    = ' ) A
                      left join 
                      (';
    $left_join2    = ') b ON A.NoBukti = b.NoBukti
    left join 
    ( ';
    $sql_akhir    = ')
    c on a.NoReff = c.NoBukti
   left join Ms_Partner d on c.CustomerID = d.PartnerID';
    $where        = " WHERE  CAST(a.TGL AS date) BETWEEN '$start_date' AND '$end_date' order by a.TGL desc";
    $sql_new      = $sql_awal . $sql_SJHD . $left_join . $sql_SJDT . $left_join2 . $sql_SOHD  . $sql_akhir . $where;
    $query        = $second_DB->query($sql_new);
    $result       = $query->result();
    $data         = [];
    $no           = 1;
    // echo $sql_new;
    // exit;
    foreach ($result as $key => $value) {
      $data[] = array(
        $no++,
        $value->NoReff,
        $value->PartnerName,
        $value->PoCustomer,
        $value->NoBukti,
        // $value->NoPlanning,
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

  // public function sales_order_by_do_customer($partID)
  // {
  //   $user_level       = $this->session->userdata('user_level');
  //   $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
  //   if ($check_permission->num_rows() == 1) {
  //     $data['group_halaman']   = "SALES";
  //     $data['nama_halaman']   = "SALES ORDER BY DO";
  //     $data['icon_halaman']   = "icon-airplay";
  //     $data['perusahaan']     = $this->perusahaan->get_details();
  //     $data['partID']         = base64_decode($partID);

  //     $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
  //     $log_type   = "VIEW";
  //     $log_data   = "";

  //     log_helper($log_url, $log_type, $log_data);

  //     $this->load->view('adminx/salesorder/sales_order_by_do_view', $data, FALSE);
  //   } else {
  //     redirect('errorpage/error403');
  //   }
  // }

  // public function sales_order_by_do_list()
  // {
  //   $draw       = intval($this->input->get("draw"));
  //   $start       = intval($this->input->get("start"));
  //   $length     = intval($this->input->get("length"));
  //   $partID     = $this->input->post('partID');
  //   $noso     = $this->input->post('no_so');
  //   $start_date   = $this->input->post('periode_awal');
  //   $end_date   = $this->input->post('periode_now');

  //   $year1  = date('Y', strtotime($start_date));
  //   $year2  = date('Y', strtotime($end_date));
  //   $month1 = date('m', strtotime($start_date));
  //   $month2 = date('m', strtotime($end_date));

  //   $interval   = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
  //   $sql_SJHD = '';
  //   $sql_SJDT = '';
  //   for ($i = 0; $i < $interval; $i++) {
  //     $tempDate       = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' months'));
  //     $tempTableName  = date('Y', strtotime($tempDate)) . date('m', strtotime($tempDate));

  //     if ($i < $interval - 1) {
  //       $sql_SJHD .= "SELECT *
  //                     FROM Trans_SJHD$tempTableName
  //                     UNION ";
  //       $sql_SJDT .= "SELECT *
  //                     FROM Trans_SJDT$tempTableName
  //                     UNION ";
  //     } else {
  //       $sql_SJHD .= "SELECT *
  //                     FROM Trans_SJHD$tempTableName";
  //       $sql_SJDT .= "SELECT *
  //                     FROM Trans_SJDT$tempTableName";
  //     }
  //   }
  //   $second_DB    = $this->load->database('bjsmas01_db', TRUE);
  //   $sql_awal     = 'select a.NoBukti, a.NoReff, a.NoPlanning, a.TGL,b.PartID, b.Qty from (';
  //   $left_join    = ' ) A
  //                     left join 
  //                     (';
  //   $sql_akhir    = ') b ON A.NoBukti = b.NoBukti';
  //   $where        = " WHERE  b.PartID='$partID' AND a.NoReff='$noso'";
  //   $sql_new      = $sql_awal . $sql_SJHD . $left_join . $sql_SJDT . $sql_akhir . $where;
  //   $query        = $second_DB->query($sql_new);
  //   $result       = $query->result();
  //   $data         = [];
  //   $no           = 1;
  //   foreach ($result as $key => $value) {
  //     $data[] = array(
  //       $no++,
  //       $value->NoReff,
  //       $value->NoBukti,
  //       $value->NoPlanning,
  //       substr($value->TGL, 0, -4),
  //       $value->PartID,
  //       number_format($value->Qty, 0)

  //     );
  //   }


  //   $result = array(
  //     "draw"             => $draw,
  //     "recordsTotal"     => $query->num_rows(),
  //     "recordsFiltered" => $query->num_rows(),
  //     "data"             => $data
  //   );

  //   echo json_encode($result);
  // }
}
