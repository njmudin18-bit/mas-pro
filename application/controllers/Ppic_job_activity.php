<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ppic_job_activity extends CI_Controller
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
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('barcode_model', 'barcode_sales');
    $this->load->model('grafik_model', 'grafik');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "PPIC";
      $data['nama_halaman']   = "PPIC Job Activity";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG
      $this->load->view('adminx/ppic_job_activity/index', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function data_label_per_bulan()
  {
    $data_grafik = $this->grafik->get_data_label_per_bulan();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Sukses menampilkan data grafik",
        "data"        => $data_grafik
      )
    );
  }

  public function data_job_perbulan()
  {
    $data_grafik = $this->grafik->get_data_job_per_bulan();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Sukses menampilkan data grafik",
        "data"        => $data_grafik
      )
    );
  }

  public function data_job_by_status_perbulan()
  {
    $data_grafik = $this->grafik->get_job_by_status_per_bulan();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Sukses menampilkan data grafik",
        "data"        => $data_grafik
      )
    );
  }

  public function data_ng_perbulan_tahun_jalan()
  {
    $data_grafik = $this->grafik->data_ng_perbulan_tahun_jalan();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Sukses menampilkan data grafik",
        "data"        => $data_grafik
      )
    );
  }

  public function get_data_scan_from_last_month()
  {
    $data_grafik = $this->grafik->set_data_scan_from_last_month();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Sukses menampilkan data grafik",
        "data"        => $data_grafik
      )
    );
  }

  public function job_activity_list()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));

    //GET START AND END DATE
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');

    $year_1     = date('Y', strtotime($start_date));
    $month_1    = date('m', strtotime($start_date));
    $year_2     = date('Y', strtotime($end_date));
    $month_2    = date('m', strtotime($end_date));
    $left_join  = "";
    $text_join  = "";

    $interval   = (($year_2 - $year_1) * 12) + ($month_2 - $month_1) + 1;

    //GET LOOPING FOR LEFT JOIN TABLE TRANSJOB
    if ($month_1 == $month_2) {
      $tbl_trans_job     = "Trans_Job" . $year_2 . $month_2; //Trans_Job202302
      $text_join         = "LEFT JOIN $tbl_trans_job b on b.NoBukti = a.no_job";
    } else {

      for ($i = 0; $i < $interval; $i++) {
        $tempDate       = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' months'));
        $tempTableName  = date('Y', strtotime($tempDate)) . date('m', strtotime($tempDate));

        if ($i < $interval - 1) {
          $left_join     .= " (SELECT NoBukti, Tgl, PartID, UnitID, QtyOrder, Keterangan, WHResult FROM Trans_Job$tempTableName) UNION ALL";
        } else {
          $left_join     .= " (SELECT NoBukti, Tgl, PartID, UnitID, QtyOrder, Keterangan, WHResult FROM Trans_Job$tempTableName)";
        }
      }

      $text_join = " LEFT JOIN (" . $left_join . ") b ON b.NoBukti = a.no_job";
    }

    $sql   = "SELECT 
                a.no_job, 
                a.loc_result, 
                b.Tgl, 
                b.PartID, 
                b.UnitID, 
                b.QtyOrder, 
                b.Keterangan, 
                b.WHResult, 
                c.PartName 
              FROM 
                (
                  SELECT 
                    no_job, 
                    loc_result 
                  FROM 
                    tbl_scanbarcode_job 
                  GROUP BY 
                    no_job, 
                    loc_result
                ) a $text_join 
                LEFT JOIN Ms_Part c ON c.PartID = b.PartID 
              WHERE 
                CAST(b.Tgl AS date) BETWEEN '$start_date' 
                AND '$end_date' 
              ORDER BY 
                b.Tgl DESC";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    $query        = $second_DB->query($sql);
    $result       = $query->result();
    $data         = [];
    $no           = 1;
    $status_job   = "";
    $qty_order    = 0;
    $total_qty    = 0;
    $total_qty_wh = 0;
    $sisa_qty     = 0;

    foreach ($result as $key => $value) {
      $no_job     = $value->no_job;
      $qty_order   = floatval($value->QtyOrder);

      //GET QTY JOB IN WH
      $tgl_job       = explode('-', $value->Tgl);
      $tahun         = $tgl_job[0];
      $bulan         = $tgl_job[1];
      $wh_data       = get_total_qty_jobs_wh_new($no_job, $bulan, $tahun);
      $total_qty_wh  = floatval($wh_data[0]->jlh_qty_wh);
      $sisa_qty      = $qty_order - $total_qty_wh;

      //JOB DETAILS
      $job_details    = $value->no_job . "<br><br>";
      $job_details    .= "<small>";
      $job_details    .= "Part ID: " . $value->PartID . "<br>";
      $job_details    .= "Part Name: " . $value->PartName . "<br>";
      $job_details    .= "Unit ID: " . $value->UnitID . "<br>";
      $job_details    .= "Location: " . $value->WHResult . "<br>";
      $job_details    .= "Notes: " . $value->Keterangan . "<br>";
      $job_details    .= "Job Date: " . substr($value->Tgl, 0, -4) . "<br>";

      $job_details    .= "</small>";

      $data[] = array(
        $no++,
        $job_details,
        number_format($value->QtyOrder),
        '',
        '',
        ''
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
