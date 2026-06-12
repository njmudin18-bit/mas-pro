<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Returns extends CI_Controller
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
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "QC";
      $data['nama_halaman']     = "Scan Product Return";
      $data['icon_halaman']     = "icon-airplay";
      $data['perusahaan']       = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/qc/scan_product_return', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function scan_product_return_warehouse()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Warehouse";
      $data['nama_halaman']     = "Scan Product Return";
      $data['icon_halaman']     = "icon-airplay";
      $data['perusahaan']       = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/warehouse/scan_product_return_wh', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function scan_product_return_list()
  {
    $second_DB   = $this->load->database('bjsmas01_db', TRUE);
    $draw        = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length      = intval($this->input->get("length"));

    $tanggal     = $this->input->post('tanggal');
    $bulan       = $this->input->post('bulan');
    $tahun       = $this->input->post('tahun');

    $new_bulan    = 0;
    if (strlen($bulan) == 1) {
      $new_bulan  = "0" . $bulan;
    } else {
      $new_bulan  = $bulan;
    }

    $sql = "SELECT b.PartID, c.PartName, a.* 
            FROM tbl_return_temp a
            LEFT JOIN Trans_Job$tahun$new_bulan b ON b.NoBukti = a.NomorJob
            LEFT JOIN Ms_Part c ON c.PartID = b.PartID 
            WHERE YEAR(a.CreateDate) = '$tahun' 
            AND MONTH(a.CreateDate) = '$new_bulan' 
            AND DAY(a.CreateDate) = '$tanggal'
            ORDER BY a.CreateDate DESC";

    $query       = $second_DB->query($sql);
    $result     = $query->result();
    $data       = [];
    $no         = 1;
    $status     = "";
    $lokasi_1   = "";
    $lokasi_2   = "";

    foreach ($result as $key => $value) {
      $ID     = "'".$value->Id."'";
      $data[] = array(
        $no++,
        '<button id="'.$key.'" class="btn btn-danger" onclick="hapus_data_temp('.$ID.')">
          <i class="fa fa-times"></i>
        </button>',
        number_format($value->QtyBox, 0),
        $value->NomorJob,
        number_format($value->QtyOrder, 0),
        $value->WHLocation,
        $value->PartID,
        $value->PartName,
        $value->BarcodeNo,
        $value->NoUrut,
        substr($value->CreateDate, 0, -4)
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

  public function delete_data() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $second_DB      = $this->load->database('bjsmas01_db', TRUE);
      $ID             = $this->input->post('id');

      $Delete         = $second_DB->delete('tbl_return_temp', array('Id' => $ID));
      if ($Delete) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Barcode sukses dihapus",
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Barcode gagal dihapus",
          )
        );
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function save_barcode_temp() {
    $second_DB        = $this->load->database('bjsmas01_db', TRUE);
    $BarcodeNo        = $this->input->post("barcode_no");

    $cekBarcode       = $second_DB->get_where('tbl_scanbarcode_job', array('barcode_no' => $BarcodeNo, 'loc_id' => 'PR001'))->num_rows();
    if ($cekBarcode > 0) {
      $cekAda         = $second_DB->get_where('tbl_return_temp', array('BarcodeNo' => $BarcodeNo))->num_rows();
      if ($cekAda == 0) {
        $BarcodeArray     = explode('|', $BarcodeNo);
        $JobArray         = explode('-', $BarcodeArray[1]);
        $NoUrutBox        = floatval($BarcodeArray[2]);
        $NoJob            = substr($JobArray[0], 4);
        $WHLocation       = $BarcodeArray[5];
        $QtyOrder         = floatval($BarcodeArray[6]);
        $QtyBox           = floatval($BarcodeArray[7]);

        $DataInsert       = array(
          "NomorJob"      => $NoJob,
          "QtyOrder"      => $QtyOrder,
          "QtyBox"        => $QtyBox,
          "WHLocation"    => $WHLocation,
          "NoUrut"        => $NoUrutBox,
          "BarcodeNo"     => $BarcodeNo,
          "CreateDate"    => date("Y-m-d H:i:s"),
          "CreateBy"      => $this->session->userdata('user_name')
        );

        $Save = $second_DB->insert('tbl_return_temp', $DataInsert);
        if ($Save) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Barcode ".$BarcodeNo." sukses disimpan",
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Barcode ".$BarcodeNo." gagal disimpan",
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Barcode ".$BarcodeNo." sudah terdaftar",
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Barcode ".$BarcodeNo." tidak terdaftar",
        )
      );
    }
  }

  public function save_data() {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $second_DB  = $this->load->database('bjsmas01_db', TRUE);
      $NomorDO    = $this->input->post('nomor_do');
      $QtyReturn  = $this->input->post('qty_return');
      $Keterangan = $this->input->post('keterangan');
      $DataDT     = array();

      $Cek        = $second_DB->get_where('tbl_return_hd', array('NomorDO' => $NomorDO))->num_rows();
      if ($Cek == 0) {
        $DataHD     = array(
          "NomorDO"     => $NomorDO,
          "QtyReturn"   => floatval($QtyReturn),
          "Keterangan"  => $Keterangan,
          "CreateDate"  => date("Y-m-d H:i:s"),
          "CreateBy"    => $this->session->userdata('user_name')
        );
  
        $DataDTArray  = $second_DB->query("SELECT * FROM tbl_return_temp WHERE CAST(CreateDate AS DATE) = CAST(GETDATE() AS DATE)")->result();
        foreach ($DataDTArray as $key => $value) {
          $DataDT[] = array(
            "NomorDO"     => $NomorDO,
            "NomorJob"    => $value->NomorJob,
            "QtyOrder"    => floatval($value->QtyOrder),
            "QtyBox"      => floatval($value->QtyBox),
            "NoUrutBox"   => $value->NoUrut,
            "WHLocation"  => $value->WHLocation,
            "BarcodeNo"   => $value->BarcodeNo
          );
        }
        
        $SaveHD = $second_DB->insert('tbl_return_hd', $DataHD);
        if ($SaveHD) {
          $SaveDT = $second_DB->insert_batch('tbl_return_dt', $DataDT);
          if ($SaveDT) {

            $second_DB->delete('tbl_return_temp', array('id' => $id));
            echo json_encode(
              array(
                "status_code" => 200,
                "status"      => "success",
                "message"     => "DO HD dan DT sukses disimpan",
                "data"        => $DataDT
              )
            );
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "DO DT gagal disimpan",
                "data"        => $DataDT
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "DO HD gagal disimpan",
              "data"        => $DataHD
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "DO sudah terdaftar",
            "data"        => $NomorDO
          )
        );
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }
}
