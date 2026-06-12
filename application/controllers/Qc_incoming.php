<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc_incoming extends CI_Controller
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
    $this->load->model('users_model', 'users');
    $this->load->model('whlocation_model', 'whlocation');

    $this->DB_BJGMAS  = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "QC";
      $data['nama_halaman']     = "Scan Barcode Incoming";
      $data['icon_halaman']     = "icon-airplay";
      $data['QCUsers']          = $this->users->get_qc_users();
      $data['perusahaan']       = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/qc/scan_barcode_incoming', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function cek_barcode()
  {
    $BarcodeNumber    = $this->input->post("barcode_no");
    $ExplodeBarcode   = explode('|', $BarcodeNumber);

    $GetData = $this->DB_BJGMAS->get_where('Trans_IncomingScan', array('BarcodeNumber' => $BarcodeNumber));
    $CekData = $GetData->num_rows();
    if ($CekData > 0) {
      echo json_encode(
        array(
          "status_code" => 200, 
          "status"      => "success", 
          "message"     => "Barcode " . $BarcodeNumber . " sudah di scan", 
          "data"        => $GetData->row()
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404, 
          "status"      => "error", 
          "message"     => "Barcode " . $BarcodeNumber . " belum di scan", 
          "data"        => null
        )
      );
    }
  }

  //FUNGSI MENYIMPAN PRODUK
  public function save_barcode()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $BarcodeNumber  = $this->input->post("no_barcode");
      $Status         = $this->input->post("status");
      $Noted          = $this->input->post("penyebab");
      $IsiBarcode     = explode('|', $BarcodeNumber);
      $PoNumber       = str_replace("MT-", "", $IsiBarcode[0]);

      $GetData        = $this->DB_BJGMAS->get_where('Trans_IncomingScan', array('BarcodeNumber' => $BarcodeNumber));
      $CheckData      = $GetData->num_rows();
      if ($CheckData == 0) {
        //SET ARRAY DATA
        $ArrayData = array(
          'BarcodeNumber' => $BarcodeNumber,
          'Sequent'       => $IsiBarcode[3],
          'PONumber'      => $PoNumber,
          'PartID'        => $IsiBarcode[1],
          'Weight'        => floatval(str_replace(",", ".", $IsiBarcode[2])),
          'Status'        => $Status,
          'Noted'         => $Noted,
          'CreateDate'    => date('Y-m-d H:i:s'),
          'CreateBy'      => $this->session->userdata('user_name')
        );

        $Insert = $this->DB_BJGMAS->insert('Trans_IncomingScan', $ArrayData);
        if ($Insert) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Data sukses disimpan"
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data gagal disimpan"
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data sudah tersimpan"
          )
        );
      }

      //echo json_encode(array("Isi barcode" => $IsiBarcode, "Data" => $Data));
      exit;


      exit();
      $barcode_no     = $this->input->post("no_barcode");
      $status         = $this->input->post("status");
      $qty            = $this->input->post("qty");
      $status_old     = $this->input->post("status_old");
      $penyebab       = $this->input->post("penyebab");
      $pic_repair     = ucwords($this->input->post("pic_repair"));

      $barcode_array  = explode('|', $barcode_no);
      $job_array      = explode('-', $barcode_array[1]);
      $no_job         = substr($job_array[0], 4);
      //echo $no_job;
      //exit;

      $second_DB  = $this->load->database('bjsmas01_db', TRUE);

      if ($status_old == 'NG') {

        $data_update = array(
          'scan_status'         => $status,
          'scan_update_date'    => date('Y-m-d H:i:s'),
          'scan_update_by'      => $this->session->userdata('user_name')
        );

        //echo json_encode(array("data det" => $data_update, "ini" => null)); exit;

        $second_DB->trans_start();
        $array = array('barcode_no' => $barcode_no, 'loc_id' => 'QC001', 'scan_status' => 'NG');
        $second_DB->where($array);
        $second_DB->update('tbl_scanbarcode_job', $data_update);
        $second_DB->trans_complete();
        if ($second_DB->trans_status() === FALSE) {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"       => "error",
              "message"     => "Barcode " . $barcode_no . " gagal di update",
              "data"         => $data_update
            )
          );
        } else {
          $data_det = array(
            'scan_id'         => $this->input->post('scan_id_header'),
            'barcode_no'      => $barcode_no,
            'status'          => $status,
            'penyebab'        => $penyebab,
            'qty'             => $qty,
            'pic_repair'      => $pic_repair,
            'created_date'    => date('Y-m-d H:i:s')
          );

          $second_DB->trans_start();
          $insert_det = $second_DB->insert('tbl_scanbarcode_job_details', $data_det);
          $second_DB->trans_complete();

          echo json_encode(
            array(
              "status_code" => 200,
              "status"       => "success",
              "message"     => "Barcode " . $barcode_no . " sukses di update",
              "data"         => $data_update
            )
          );
        }

        //ADDING TO LOG
        $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
        $log_type   = "UPDATE";
        $log_data   = json_encode($data_update);

        log_helper($log_url, $log_type, $log_data);
        //END LOG
      } else {

        //CEK DAHULU APAKAH SUDAH ADA BERDASARKAN STATUS
        if ($status == 'OK') {
          $query = $second_DB->query("SELECT * FROM tbl_scanbarcode_job 
																			WHERE barcode_no = '$barcode_no' 
																			AND loc_id = 'QC001' AND scan_status = '$status'");
        } elseif ($status == 'NG') {
          $query = $second_DB->query("SELECT * FROM tbl_scanbarcode_job 
																			WHERE barcode_no = '$barcode_no' 
																			AND loc_id = 'QC001' AND scan_status = '$status'");
        } else if ($status == 'RA') {
          $query = $second_DB->query("SELECT * FROM tbl_scanbarcode_job 
																			WHERE barcode_no = '$barcode_no' 
																			AND loc_id = 'QC001' AND scan_status = '$status'");
        }

        $cek         = $query->num_rows();
        if ($cek == 0) {
          //CEK BARCODE PPIC ATAU WH
          $CekBarcode   = explode('/', $barcode_no);
          if ($CekBarcode[0] == '|PCG') {
            //EXPLODE ISI BARCODE
            $barcode_isi   = explode('|', $barcode_no);

            //SET ARRAY DATA
            $data = array(
              'barcode_no'     => $barcode_no,
              'no_job'         => $no_job,
              'loc_id'         => "QC001",
              'qty_job'        => $barcode_isi[6],
              'qty_box'        => $barcode_isi[7],
              'loc_result'     => $barcode_isi[5],
              'scan_status'    => $status,
              'scan_date'      => date('Y-m-d H:i:s'),
              'scan_by'        => $this->session->userdata('user_name')
            );

            //INSERT INTO TABLE
            $second_DB->trans_start();
            $insert       = $second_DB->insert('tbl_scanbarcode_job', $data);
            $insert_id    = $second_DB->insert_id();
            $second_DB->trans_complete();

            if ($second_DB->trans_status() === FALSE) {
              echo json_encode(
                array(
                  "status_code" => 400,
                  "status"       => "error",
                  "message"     => "Barcode " . $barcode_no . " gagal discan!",
                  "data"         => $data
                )
              );
            } else {

              //INSERT KE TABLE DETAIL JIKA STATUS NYA NG
              if ($status == 'NG') {

                //SET ARRAY DATA FOR DETAILS
                $data_det = array(
                  'scan_id'       => $insert_id,
                  'barcode_no'     => $barcode_no,
                  'status'         => $status,
                  'penyebab'       => $penyebab,
                  'qty'           => $qty,
                  'pic_repair'     => $pic_repair,
                  'created_date'   => date('Y-m-d H:i:s')
                );

                $second_DB->trans_start();
                $insert_det = $second_DB->insert('tbl_scanbarcode_job_details', $data_det);
                $second_DB->trans_complete();

                if ($second_DB->trans_status() === FALSE) {
                  echo json_encode(
                    array(
                      "status_code" => 400,
                      "status"       => "error",
                      "message"     => "Barcode " . $barcode_no . " gagal disimpan ke table detail",
                      "data"         => $data_det
                    )
                  );
                } else {
                  echo json_encode(
                    array(
                      "status_code" => 200,
                      "status"       => "success",
                      "message"     => "Barcode " . $barcode_no . " sukses discan dan di disimpan",
                      "data"         => $data_det
                    )
                  );
                }
              } else {
                echo json_encode(
                  array(
                    "status_code" => 200,
                    "status"       => "success",
                    "message"     => "Barcode " . $barcode_no . " sukses discan!",
                    "data"         => $data
                  )
                );
              }
            }
          } else {
            $IsiBarcode   = explode('|', $barcode_no);
            $PoNumber     = str_replace("MT-", "", $IsiBarcode[0]);

            //SET ARRAY DATA
            $Data = array(
              'BarcodeNumber' => $barcode_no,
              'Sequent'       => $IsiBarcode[3],
              'PONumber'      => $PoNumber,
              'PartID'        => $IsiBarcode[1],
              'Weight'        => $IsiBarcode[2],
              'Status'        => $status,
              'CreateDate'    => date('Y-m-d H:i:s'),
              'CreateBy'      => $this->session->userdata('user_name')
            );


            echo json_encode(array("Isi barcode" => $IsiBarcode, "Data" => $Data));
            exit;
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 409,
              "status"       => "error",
              "message"     => "Barcode " . $barcode_no . " sudah discan!"
            )
          );
        }
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //FUNGSI MENAMPILKAN DATA
  public function show_barcode_data_list()
  {
    $Draw         = intval($this->input->get("draw"));
    $start        = intval($this->input->get("start"));
    $length       = intval($this->input->get("length"));
    $now          = date("Y-m-d");
    $Tanggal      = $this->input->post('tanggal');
    $Bulan        = $this->input->post('bulan');
    $Tahun        = $this->input->post('tahun');

    $Sql          = "SELECT 
                      a.*,
                      FORMAT(a.Weight, 'N2') AS Weights,
                      CONVERT(VARCHAR, a.CreateDate, 120) AS Tanggal,
                      b.PartName
                     FROM Trans_IncomingScan a
                     LEFT JOIN Ms_Part b ON b.PartID = a.PartID 
                     WHERE DAY(a.CreateDate) = '$Tanggal' AND MONTH(a.CreateDate) = '$Bulan' 
                     AND YEAR(a.CreateDate) = '$Tahun'";
    $Query   = $this->DB_BJGMAS->query($Sql);
    $Result  = $Query->result();
    $Data    = [];
    $No      = 1;
    $attr    = "";
    $isi     = "";

    foreach ($Result as $key => $value) {
      $isi = "'".$value->Id."', '".$value->BarcodeNumber."', '".$value->Status."'";
      $Data[] = array(
        $No++,
        $this->timeAgo($value->Tanggal),
        $value->Status == 'OK' ? '<span class="badge badge-success">' . $value->Status . '</span>' : '<span class="badge badge-danger pointer" onclick="view_details(' . $isi . ')">' . $value->Status . '</span>',
        $value->PONumber,
        $value->Tanggal,
        $value->PartID,
        $value->PartName,
        $value->Weights,
        $value->Sequent,
        $value->BarcodeNumber,
        $value->CreateBy
      );
    }

    $Result = array(
      "draw"             => $Draw,
      "recordsTotal"     => $Query->num_rows(),
      "recordsFiltered"  => $Query->num_rows(),
      "data"             => $Data
    );

    echo json_encode($Result);
    exit();
  }

  //FUNGSI LIHAT DETAIL PRODUK NG
  public function view_product_ng()
  {
    $id = $this->input->post("scan_id");

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $query       = $second_DB->query("SELECT * FROM tbl_scanbarcode_job_details 
																		 WHERE scan_id = '$id' ORDER BY id DESC");
    $cek         = $query->num_rows();
    if ($cek > 0) {

      $header         = $second_DB->query("SELECT * FROM tbl_scanbarcode_job WHERE scan_id = '$id'");
      $result_header   = $header->row();
      $result         = $query->result();

      $text     = "";
      $no       = 1;
      if (count($result) > 0) {

        foreach ($result as $key => $value) {
          $text .= '<tr>
		 									<td class="text-right">' . $no++ . '</td>
		 									<td class="text-left">' . $value->barcode_no . '</td>
		 									<td class="text-center"><span class="badge badge-danger">' . $value->status . '</span></td>
		 									<td class="text-right">' . $value->qty . '</td>
		 									<td class="text-left">' . $value->pic_repair . '</td>
		 									<td class="text-center">' . $value->created_date . '</td>
		 								</tr>
		 								<tr>
		 									<td colspan="6" class="text-left">PENYEBAB: ' . $value->penyebab . '</td>
		 								</tr>';
        }
      } else {
        $text .= '<tr>
	 									<td colspan="6" class="text-center">NO DATA FOUND</td>
	 								</tr>';
      }

      echo json_encode(
        array(
          "status_code" => 200,
          "status"       => "success",
          "message"     => "Barcode " . $id . " ditemukan!",
          "header"       => $result_header,
          "data"         => $result,
          "html"         => $text
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"       => "error",
          "message"     => "Barcode " . $id . " tidak ditemukan!",
          "data"         => array()
        )
      );
    }
  }

  //FUNGSI SIMPAN JIKA ADA NG KEMBALI DI SATU PRODUK
  public function save_more_ng()
  {
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $data_det = array(
      'scan_id'       => $this->input->post('scan_id_view'),
      'barcode_no'     => $this->input->post('no_barcode_view'),
      'status'         => $this->input->post('status_view'),
      'qty'           => $this->input->post('qty_view'),
      'penyebab'       => $this->input->post('penyebab_view'),
      'pic_repair'     => ucwords($this->input->post('pic_repair_view')),
      'created_date'   => date('Y-m-d H:i:s')
    );

    $second_DB->trans_start();
    $insert_det = $second_DB->insert('tbl_scanbarcode_job_details', $data_det);
    $second_DB->trans_complete();

    if ($second_DB->trans_status() === FALSE) {
      echo json_encode(
        array(
          "status_code" => 400,
          "status"       => "error",
          "message"     => "Barcode gagal disimpan ke table detail",
          "data"         => $data_det
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 200,
          "status"       => "success",
          "message"     => "Barcode sukses di disimpan",
          "data"         => $data_det
        )
      );
    }

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data_det);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  function timeAgo($time_ago)
  {
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed;
    $minutes    = round($time_elapsed / 60);
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400);
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640);
    $years      = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
      return "<span class='badge badge-danger'>baru saja</span>";
    }

    //Minutes
    else if ($minutes <= 60) {
      if ($minutes == 1) {
        return "<span class='badge badge-danger text-white'>1 menit lalu</span>";
      } elseif ($minutes <= 15) {
        return "<span class='badge badge-warning text-white'>$minutes menit lalu</span>";
      } elseif ($minutes <= 30) {
        return "<span class='badge badge-success'>$minutes menit lalu</span>";
      } else {
        return "<span class='badge badge-info'>$minutes menit lalu</span>";
      }
    }

    //Hours
    else if ($hours <= 24) {
      if ($hours == 1) {
        return "<span class='badge badge-primary'>1 jam lalu</span<";
      } else {
        return "<span class='badge badge-primary'>$hours jam lalu</span<";
      }
    }

    //Days
    else if ($days <= 7) {
      if ($days == 1) {
        return "<span class='badge badge-light'>kemarin</span>";
      } else {
        return "<span class='badge badge-light'>$days hari lalu</span>";
      }
    }

    //Weeks
    else if ($weeks <= 4.3) {
      if ($weeks == 1) {
        return "<span class='badge badge-secondary'>1 minggu lalu</span>";
      } else {
        return "<span class='badge badge-secondary'>$weeks minggu lalu</span>";
      }
    }

    //Months
    else if ($months <= 12) {
      if ($months == 1) {
        return "<span class='badge badge-dark'>1 bulan lalu</span>";
      } else {
        return "<span class='badge badge-dark'>$months bulan lalu</span>";
      }
    }

    //Years
    else {
      if ($years == 1) {
        return "<span class='badge badge-dark'>setahun lalu</span>";
      } else {
        return "<span class='badge badge-dark'>$years tahun lalu</span>";
      }
    }
  }
}
