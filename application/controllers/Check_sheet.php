<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Check_sheet extends CI_Controller
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
    $this->load->model('qc_transaction_model', 'qc_transaction');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {


      $data['group_halaman']  = "QC";
      $data['nama_halaman']   = "Setting Check Sheet";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      $id                     = $this->uri->segment(3);
      $parts                  = $this->qc_transaction->get_by_id($id);
      $part_id                = $parts->part_id;
      $data['part_details']   = $this->qc_transaction->get_part_detail($part_id);

      //ADDING TO LOG
      $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/qc/setting_check_sheet', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function data_variable_list()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));

    $this->db->select('*');
    $this->db->from('tbl_variable_inspection');
    $this->db->order_by('item_name', 'ASC');
    $query = $this->db->get();

    $data   = [];
    $nomor   = 1;

    foreach ($query->result() as $value) {

      $data[] = array(
        $value->id,
        $nomor++,
        $value->item_name,
        $value->standard,
        $value->toleransi,
        $value->tools,
      );
    }

    $result = array(
      "draw"             => $draw,
      "recordsTotal"     => $query->num_rows(),
      "recordsFiltered"  => $query->num_rows(),
      "data"             => $data
    );

    echo json_encode($result);
    exit();
  }

  public function save_transaksi_variable()
  {
    echo "aaaa";
  }
}
