<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc_transaction extends CI_Controller
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
      $data['nama_halaman']   = "QC Check Sheet";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/qc/check_sheet', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function search_part()
  {
    $part_name  = $this->input->post('term');

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $second_DB->select('*');
    $second_DB->from('Ms_Part');
    $second_DB->like('PartName', $part_name);
    $second_DB->or_like('PartID', $part_name);
    $query  = $second_DB->get();

    $result = $query->result_array();
    // Initialize Array with fetched data
    $data = array();
    foreach ($result as $res) {

      $data[] = array(
        "id"    => $res['PartID'],
        "text"  => $res['PartID'] . " - " . $res['PartName']
      );
    }

    echo json_encode($data);
  }

  public function save_part()
  {
    $part_name_array  = $this->input->post('part_name');
    $data_insert      = array();

    foreach ($part_name_array as $key => $value) {

      $sql_cek  = $this->db->query("SELECT * FROM tbl_part_check_list WHERE part_id = '$value'");
      $cek      = $sql_cek->num_rows();
      if ($cek == 0) {
        $data_insert[]  = array(
          'part_id'       => $value,
          'id_module'     => '',
          'aktivasi'      => 'AKTIF',
          'insert_date'   => date('Y-m-d H:i:s'),
          'insert_by'     => $this->session->userdata('user_name')
        );
      }
    }

    //echo json_encode($data_insert);
    //INSERT INTO TABLE
    if (count($data_insert) > 0) {
      $this->db->trans_start();
      $insert = $this->db->insert_batch('tbl_part_check_list', $data_insert);
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        echo json_encode(
          array(
            "status_code"   => 400,
            "status"        => "error",
            "message"       => "Gagal menyimpan data",
            "status_insert" => $insert,
            "data"          => $data_insert
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code"   => 200,
            "status"        => "success",
            "message"       => "Success menyimpan data",
            "status_insert" => $insert,
            "data"          => $data_insert
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code"     => 409,
          "status"          => "duplicated",
          "message"         => "Data sudah tersimpan",
          "data"            => array()
        )
      );
    }
  }

  //FUNGSI MENAMPILKAN DATA
  public function show_data_list()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));

    $sql        = "SELECT * FROM tbl_part_check_list ORDER BY id DESC";
    $query      = $this->db->query($sql);
    $result     = $query->result();
    $data       = [];
    $no         = 1;

    foreach ($result as $key => $value) {

      $data_part = $this->get_part_name($value->part_id);

      $data[] = array(
        $no++,
        '<div class="btn-group" role="group">
          <button id="btn_group_pilihan_' . $key . '" type="button" class="btn btn-danger dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="feather icon-settings"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
            <a class="dropdown-item" href="' . base_url() . 'check_sheet/index/' . $value->id . '" target="_blank">Setting</a>
            <a class="dropdown-item" href="#" onclick="hapus_part_name(' . $value->id . ')">Hapus</a>
          </div>
        </div>',
        $value->aktivasi == 'AKTIF' ? '<span class="badge badge-pill badge-success">' . strtoupper($value->aktivasi) . '</span>' : '<span class="badge badge-pill badge-warning text-white">' . strtoupper($value->aktivasi) . '</span>',
        $value->part_id,
        $data_part->PartName,
        $value->id_module,
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

  //FUNGSI MENAMPILKAN DETAIL PART
  public function get_part_name($part_id)
  {
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $query       = $second_DB->query("SELECT * FROM Ms_Part WHERE PartID = '$part_id'");
    $cek        = $query->num_rows();
    if ($cek > 0) {
      $result   = $query->row();

      return $result;
    } else {
      return '-';
    }
  }

  public function part_deleted()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $id             = $this->input->post('part_id');
      $data_delete    = $this->qc_transaction->get_by_id($id); //DATA DELETE
      $data           = $this->qc_transaction->delete_by_id($id);

      echo json_encode(array("status" => "ok"));

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "DELETE";
      $log_data       = json_encode($data_delete);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }
}
