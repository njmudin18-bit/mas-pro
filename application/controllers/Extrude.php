<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Extrude extends CI_Controller
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
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
    $this->DB_BJGMAS = $this->load->database('bjsmas01_db', TRUE);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Produksi";
			$data['nama_halaman'] 	= "Scan Barcode Extrude";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG
			$this->load->view('adminx/produksi/extrude/index', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function save_barcode_extrude() 
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      //MATERIAL  "MT-PO/MAS/202407/00002|MEXX-BU037-XX-MAS|022";
      //JOB       "|PCG/JOB/125/202407/00070-001|001|-|0|WH-B|120000|20000|-|";
      //OPERATOR  "0012022111401";
      //MESIN     "MS0001";
      //BOBIN     "FXTO-BO003-13-MAS|001";

      //GET AND SET PART ID, PO NUMBER
      $MaterialBarcode  = $this->input->post('MaterialBarcode');
      $MaterialExplode  = explode("|", $MaterialBarcode);
      $PONumber         = removeMTPrefix($MaterialExplode[0]);
      $PartID           = $MaterialExplode[1];

      //GET AND SET JOB NUMBER
      $JobBarcode       = $this->input->post('JobBarcode');
      $JobExplode1      = explode("|", $JobBarcode);
      $JobExplode2      = explode("-", $JobExplode1[1]);
      $JobNumber        = removePCGPrefix($JobExplode2[0]);

      $BobinID          = $this->input->post('BobinID');
      $MachineID        = $this->input->post('MachineID');
      $BarcodeNumber    = "EXT-".date("Ymd-His")."-".generateRandomString(5);
      $Cek = $this->DB_BJGMAS->get_where('tbl_barcode_extrude', array('BarcodeNumber' => $BarcodeNumber))->num_rows();
      if ($Cek == 0) {
        $BarcodeNumber    = "EXT-".date("Ymd-His")."-".generateRandomString(5);
      } else {
        $BarcodeNumber    = "EXT-".date("Ymd-His")."-".generateRandomString(5);
      }

      $SaveData = array(
        'PartID'        => $PartID,
        'MachineID'     => $MachineID,
        'BobinID'       => $BobinID,
        'JobNumber'     => removePCGPrefix($JobExplode2[0]),
        'PONumber'      => removeMTPrefix($MaterialExplode[0]),
        'BarcodeNumber' => $BarcodeNumber,
        'PartBarcode'   => $MaterialBarcode,
        'JobBarcode'    => $JobBarcode,
        'CreatedBy'     => $this->session->userdata('user_code'),
        'CreatedDate'   => date('Y-m-d H:i:s')
      );

      $Insert = $this->DB_BJGMAS->insert('tbl_barcode_extrude', $SaveData);
      if ($Insert) {
        // for ($i=0; $2 < ; $i++) { 
        //   # code...
        // }

        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Sukses menyimpan data",
            "data"        => $SaveData
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Gagal menyimpan data",
            "data"        => $SaveData
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 403,
          "status"      => "info",
          "message"     => "Access Denied"
        )
      );
    }
  }

  public function show_extrude_list() 
  {
    $draw        = intval($this->input->get("draw"));
    $start       = intval($this->input->get("start"));
    $length      = intval($this->input->get("length"));
    $now         = date("Y-m-d");

    $tanggal     = $this->input->post('tanggal');
    $bulan       = $this->input->post('bulan');
    $tahun       = $this->input->post('tahun');

    $sql         = "SELECT a.*, b.PartName, c.Namamesin
                    FROM tbl_barcode_extrude a
                    LEFT JOIN Ms_Part b ON b.PartID = a.PartID
                    LEFT JOIN tbl_msmesin c ON c.Idmesin = a.MachineID
                    WHERE DAY(CreatedDate) = '$tanggal'
                    AND MONTH(CreatedDate) = '$bulan'
                    AND YEAR(CreatedDate) = '$tahun'
                    ORDER BY a.CreatedDate DESC";

    $query       = $this->DB_BJGMAS->query($sql);
    $result      = $query->result();
    $data        = [];
    $no          = 1;

    foreach ($result as $key => $value) {
      $Isi    = "'".$value->Id."'";
      $data[] = array(
        $no++,
        '<button onclick="HapusData('.$Isi.')" id="Btn_'.$key.'" class="btn btn-danger btn-sm">HAPUS</button>',
        $value->JobNumber,
        $value->BobinID,
        $value->Namamesin,
        $value->PartID,
        $value->PartName,
        removeMilliseconds($value->CreatedDate)
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

  public function delete_extrude_barcode() 
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id     = $this->input->post('Id');
      $Delete = $this->DB_BJGMAS->delete('tbl_barcode_extrude', array('Id' => $Id));
      if ($Delete) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Sukses menghapus data"
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Gagal menghapus data"
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 403,
          "status"      => "info",
          "message"     => "Access Denied"
        )
      );
    }
  }
}