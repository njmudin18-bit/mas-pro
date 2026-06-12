<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Telepon extends CI_Controller
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
    $this->load->model('phone_model', 'phone');
  }

  public function index()
  {
    $data['group_halaman']    = "GA";
    $data['nama_halaman']     = "Daftar Extention";
    $data['icon_halaman']     = "icon-layers";
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['list']             = $this->db->query("SELECT * FROM table_phone_ext")->result();
    $this->load->view('adminx/ga/phone_ext_list', $data, FALSE);
  }

  public function phone_list()
  {
    $list = $this->phone->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    $noUrut = 0;
    foreach ($list as $phone) {
      $no++;
      $noUrut++;
      $row = array();

      $row[] = $no;
      $row[] = "<span class='badge badge-danger' style='font-size:18px;'>".$phone->ext_no."</span>";
      $row[] = $phone->dept_name;
      $row[] = $phone->nama_pegawai;
      $row[] = $phone->aktivasi == 'Tidak' ? '<label class="label label-danger">' . strtoupper($phone->aktivasi) . '</label>' : '<label class="label label-success">' . strtoupper($phone->aktivasi) . '</label>';

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->phone->count_all(),
      "recordsFiltered" => $this->phone->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }
}
