<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tracking extends CI_Controller
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
    $this->load->model('roles_model', 'roles');
    $this->load->model('phone_model', 'phone');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Tracking";
      $data['nama_halaman']     = "Tracking Barcode PPIC & Sales";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['department_att']   = get_department_att();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/it/tracking_barcode', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function tracking_list()
  {
    $draw           = intval($this->input->get("draw"));
    $start          = intval($this->input->get("start"));
    $length         = intval($this->input->get("length"));

    //SET VARIABLE DATA
    $pilihan        = $this->input->post('pilihan');
    $barcode_ppic   = $this->input->post('barcode_ppic');
    $barcode_sales  = $this->input->post('barcode_sales');
    $second_DB      = $this->load->database('bjsmas01_db', TRUE);

    //QUERY PPIC
    $sql_ppic       = "SELECT 
                        a.scan_id AS scan_prod_id, 
                        a.barcode_no AS barcode_no_prod,
                        a.no_job AS no_job,
                        a.loc_id AS scan_loc_prod, 
                        a.scan_status AS scan_status_prod, 
                        a.scan_date AS scan_date_prod, 
                        a.scan_by AS scan_by_prod, 
                        b.scan_id AS scan_qc_id, 
                        b.barcode_no AS barcode_no_qc, 
                        b.loc_id AS scan_loc_qc, 
                        b.scan_status AS scan_status_qc, 
                        b.scan_date AS scan_date_qc, 
                        b.scan_by AS scan_by_qc 
                      FROM 
                        (
                          SELECT 
                            scan_id, 
                            barcode_no,
                          no_job,
                            loc_id, 
                            scan_status, 
                            scan_date, 
                            scan_by 
                          FROM 
                            tbl_scanbarcode_job 
                          WHERE 
                            loc_id = 'PR001' 
                          GROUP BY 
                            scan_id, 
                            barcode_no,
                          no_job,
                            loc_id, 
                            scan_status, 
                            scan_date, 
                            scan_by
                        ) a 
                        LEFT JOIN (
                          SELECT 
                            scan_id, 
                            barcode_no,
                          no_job,
                            loc_id, 
                            scan_status, 
                            scan_date, 
                            scan_by 
                          FROM 
                            tbl_scanbarcode_job 
                          WHERE 
                            loc_id = 'QC001' 
                          GROUP BY 
                            scan_id, 
                            barcode_no,
                          no_job,
                            loc_id, 
                            scan_status, 
                            scan_date, 
                            scan_by
                        ) b ON a.barcode_no = b.barcode_no 
                      WHERE 
                        a.barcode_no = '$barcode_ppic' 
                      ORDER BY 
                        a.barcode_no";
    $query_ppic     = $second_DB->query($sql_ppic);
    $result         = $query_ppic->result();
    $data           = [];
    $no             = 1;

    foreach ($result as $key => $value) {

      $sql_sales      = "SELECT a.*, b.PartID, b.PartName FROM tbl_scanbarcode_approval a
                         LEFT JOIN Ms_Part b ON b.PartID = a.part_id
                         WHERE barcode_id = '$barcode_sales'";
      $query_sales    = $second_DB->query($sql_sales);
      $data_sales     = $query_sales->row();

      $array_barcode  = explode('|', $value->barcode_no_prod);
      $ket_barcode    = $array_barcode[1];
      $job            = explode('/', $value->no_job); //get tahun dan bulan dari no job
      $tbl_wh         = "Trans_BHPHD" . $job[2]; //Trans_BHPHD202304
      $sql_wh         = "SELECT * FROM $tbl_wh WHERE Keterangan LIKE '%$ket_barcode%'";
      $query_wh       = $second_DB->query($sql_wh);
      $data_wh        = $query_wh->row();
      $nip            = $data_wh == NULL ? "-" : "001" . $data_wh->CreateBy;

      $data[] = array(
        $no++,
        $value->barcode_no_prod,
        $value->no_job,
        $value->scan_by_prod,
        substr($value->scan_date_prod, 0, -4),
        $value->scan_by_qc,
        substr($value->scan_date_qc, 0, -4),
        $data_wh == NULL ? "-" : get_karyawan_($nip),
        $data_wh == NULL ? "-" : substr($data_wh->CreateDate, 0, -4),
        $data_sales == NULL ? "-" : $data_sales->barcode_id, //barcode sales
        $data_sales == NULL ? "-" : $this->get_username_wh($data_sales->approved_by),
        $data_sales == NULL ? "-" : substr($data_sales->create_date, 0, -4),
        $data_sales == NULL ? "-" : $data_sales->nama_driver . " (" . $data_sales->no_polisi . ")",
        $data_sales == NULL ? "-" : $data_sales->no_po,
        $data_sales == NULL ? "-" : $data_sales->no_do,
        $data_sales == NULL ? "-" : $data_sales->part_id,
        $data_sales == NULL ? "-" : $data_sales->PartName
      );
    }

    $result = array(
      "draw"              => $draw,
      "recordsTotal"      => $query_ppic->num_rows(),
      "recordsFiltered"   => $query_ppic->num_rows(),
      "data"              => $data
    );

    echo json_encode($result);
    exit();
  }

  public function get_username_wh($id)
  {
    $user = $this->db->query("SELECT * FROM table_user WHERE id = '$id'")->row();

    return $user->username;
  }
}
