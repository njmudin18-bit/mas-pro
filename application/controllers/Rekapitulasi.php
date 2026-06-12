<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekapitulasi extends CI_Controller
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
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']   = "PPIC";
      $data['nama_halaman']   = "Rekap Scan Produksi dan QC";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG
      $this->load->view('adminx/ppic/rekap_scan', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function get_data_belum_scan()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    //$tanggal    = $this->input->post('tanggal');
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');
    $tgl_array  = explode('-', $end_date);
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $trans_job  = "Trans_Job" . $tgl_array[0] . $tgl_array[1]; //Trans_Job202306

    $sql      = "SELECT 
                  a.scan_id AS scan_prod_id, 
                  a.barcode_no AS barcode_no_prod, 
                  a.loc_id AS scan_loc_prod, 
                  a.scan_status AS scan_status_prod, 
                  a.scan_date AS scan_date_prod, 
                  a.scan_by AS scan_by_prod, 
                  a.no_job, 
                  b.scan_id AS scan_qc_id, 
                  b.barcode_no AS barcode_no_qc, 
                  b.loc_id AS scan_loc_qc, 
                  b.scan_status AS scan_status_qc, 
                  b.scan_date AS scan_date_qc, 
                  b.scan_by AS scan_by_qc, 
                  b.no_job,
                  c.PartID,
                  c.UnitID,
                  d.PartName
                FROM 
                  (
                    SELECT 
                      scan_id, 
                      barcode_no, 
                      loc_id, 
                      scan_status, 
                      scan_date, 
                      scan_by, 
                      no_job 
                    FROM 
                      tbl_scanbarcode_job 
                    WHERE 
                      loc_id = 'PR001' 
                    GROUP BY 
                      scan_id, 
                      barcode_no, 
                      loc_id, 
                      scan_status, 
                      scan_date, 
                      scan_by, 
                      no_job
                  ) a 
                  LEFT JOIN (
                    SELECT 
                      scan_id, 
                      barcode_no, 
                      loc_id, 
                      scan_status, 
                      scan_date, 
                      scan_by, 
                      no_job 
                    FROM 
                      tbl_scanbarcode_job 
                    WHERE 
                      loc_id = 'QC001' 
                    GROUP BY 
                      scan_id, 
                      barcode_no, 
                      loc_id, 
                      scan_status, 
                      scan_date, 
                      scan_by, 
                      no_job
                  ) b ON a.barcode_no = b.barcode_no
                LEFT JOIN $trans_job c ON c.NoBukti = a.no_job
                LEFT JOIN Ms_Part d ON d.PartID = c.PartID
                WHERE 
                  CAST(a.scan_date as date) BETWEEN '$start_date' AND '$end_date' 
                  AND b.scan_status IS NULL
                ORDER BY 
                  b.scan_date DESC";

    $query    = $second_DB->query($sql);
    $result   = $query->result();
    $data     = [];
    $no       = 1;

    foreach ($result as $key => $value) {
      $barcode_array  = explode('|', $value->barcode_no_prod);
      $isi            = "'" . $key . "', '" . $value->barcode_no_prod . "'";
      $data[] = array(
        $no++,
        "<span class='badge badge-danger'>" . $value->scan_status_prod . '</span><br><span class="badge badge-dark">' . substr($value->scan_date_prod, 0, -4) . "</span>",
        $value->scan_status_qc == '' ? '-' : "<span class='badge badge-success'>" . $value->scan_status_qc . '</span><br><span class="badge badge-dark">' . substr($value->scan_date_qc, 0, -4) . "</span>",
        '<button type="button" class="btn btn-secondary" data-clipboard-target="#barcode_id_' . $key . '">
          <i class="fa fa-solid fa-copy fa-lg"></i>
        </button>
        <span id="barcode_id_' . $key . '">' . $value->barcode_no_prod . '</span>',
        $value->no_job,
        $value->PartID,
        $value->PartName,
        $value->UnitID,
        number_format($barcode_array[6]),
        number_format($barcode_array[7])
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

  public function get_jumlah_scan()
  {
    //$date       = $this->input->post('_date');
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $sql        = "SELECT * FROM 
                  (SELECT COUNT(scan_id) AS jlh_scan_prod FROM tbl_scanbarcode_job a
                  WHERE CAST(a.scan_date AS date) BETWEEN '$start_date' AND '$end_date' 
                  AND a.loc_id = 'PR001') AS jlh_prod, 
                  (SELECT COUNT(scan_id) AS jlh_scan_qc FROM tbl_scanbarcode_job a
                  WHERE CAST(a.scan_date AS date) BETWEEN '$start_date' AND '$end_date' 
                  AND a.loc_id = 'QC001') AS jlh_qc";
    $query      = $second_DB->query($sql);
    $result     = $query->row();

    $sql2       = "SELECT * FROM 
                  (SELECT top 1 a.scan_date as last_scan_prod, a.loc_id as loc_prod 
                  FROM tbl_scanbarcode_job a
                  WHERE CAST(a.scan_date AS date) BETWEEN '$start_date' AND '$end_date' 
                  AND a.loc_id = 'PR001'
                  ORDER BY a.scan_date DESC) AS jlh_prod, 
                  (SELECT top 1 b.scan_date as last_scan_qc, b.loc_id as loc_qc 
                  FROM tbl_scanbarcode_job b
                  WHERE CAST(b.scan_date AS date) BETWEEN '$start_date' AND '$end_date' 
                  AND b.loc_id = 'QC001'
                  ORDER BY b.scan_date DESC) AS jlh_qc";
    $query2     = $second_DB->query($sql2);
    $result2     = $query2->row();

    echo json_encode(
      array(
        "status_code"   => 200,
        "status"        => "success",
        "message"       => "Sukses menampilkan data scan",
        "data"          => $result,
        "tanggal"       => $result2
      )
    );
  }
}
