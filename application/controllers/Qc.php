<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc extends CI_Controller
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
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "QC";
      $data['nama_halaman']     = "Scan Barcode";
      $data['icon_halaman']     = "icon-airplay";
      $data['QCUsers']          = $this->users->get_qc_users();
      $data['perusahaan']       = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/qc/scan_barcode', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function cek_barcode()
  {
    $barcode_no       = $this->input->post("barcode_no");
    $CekBarcode       = explode('/', $barcode_no);

    // echo json_encode($CekBarcode)." test ";
    // exit;

    if ($CekBarcode[0] == '|PCG') {

      $barcode_no_array = explode('|', $barcode_no);

      $second_DB    = $this->load->database('bjsmas01_db', TRUE);
      $sql          = "SELECT * FROM tbl_scanbarcode_job 
                      WHERE barcode_no = '$barcode_no' 
                      AND loc_id = 'PR001'";
      $query        = $second_DB->query($sql);
      $cek          = $query->num_rows();
      if ($cek > 0) {

        //$barcode_no_array = explode('|', $barcode_no);

        //CEK APAKAH STATUS NYA OK ATAU NG
        $sql_2      = "SELECT * FROM tbl_scanbarcode_job 
                      WHERE barcode_no = '$barcode_no' AND loc_id = 'QC001'";
        $query_2    = $second_DB->query($sql_2);
        $res        = $query_2->row();

        $sql_3      = "SELECT TOP 1 * FROM tbl_scanbarcode_job_details 
                      WHERE barcode_no ='$barcode_no'
                      ORDER BY created_date DESC";
        $query_3    = $second_DB->query($sql_3);
        $res_3      = $query_3->row();
        echo json_encode(
          array(
            "status_code"   => 200,
            "status"        => "success",
            "message"       => "Barcode " . $barcode_no . " sudah di scan produksi",
            "data"          => $res,
            "data_details"  => $res_3,
            "loc_result"    => $barcode_no_array[5]
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code"   => 404,
            "status"        => "error",
            "message"       => "Barcode " . $barcode_no . " belum di scan produksi",
            "data"          => array(),
            "loc_result"    => $barcode_no_array[5]
          )
        );
      }
    } 
    
    else if ($CekBarcode[0] == 'MT-PO') {
      $second_DB    = $this->load->database('bjsmas01_db', TRUE);
      $sql          = "SELECT * FROM tbl_scanbarcode_job 
                      WHERE barcode_no = '$barcode_no' 
                      AND loc_id = 'INC001'";
      $query        = $second_DB->query($sql);
      $cek          = $query->num_rows();

      if ($cek == 0) {
        echo json_encode(
          array(
            "status_code"   => 200,
            "status"        => "success",
            "message"       => "Barcode " . $barcode_no . " sudah pernah di scan.",
            "data"          => array(),
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code"   => 404,
            "status"        => "error",
            "message"       => "Barcode " . $barcode_no . " sudah pernah di scan.",
            "data"          => array(),
          )
        );
      }
    }

    else {
      echo json_encode(
        array(
          "status_code"   => 404,
          "status"        => "error",
          "message"       => "Barcode tidak ditemukan.",
          "data"          => array(),
          "loc_result"    => ""
        )
      );
    }
  }

  public function cek_barcode_OLD()
  {
    $barcode_no       = $this->input->post("barcode_no");
    $barcode_no_array = explode('|', $barcode_no);

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $sql          = "SELECT * FROM tbl_scanbarcode_job 
									   WHERE barcode_no = '$barcode_no' 
									   AND loc_id = 'PR001'";
    $query        = $second_DB->query($sql);
    $cek          = $query->num_rows();
    if ($cek > 0) {

      //$barcode_no_array = explode('|', $barcode_no);

      //CEK APAKAH STATUS NYA OK ATAU NG
      $sql_2      = "SELECT * FROM tbl_scanbarcode_job 
						 		 	   WHERE barcode_no = '$barcode_no' AND loc_id = 'QC001'";
      $query_2    = $second_DB->query($sql_2);
      $res        = $query_2->row();

      $sql_3      = "SELECT TOP 1 * FROM tbl_scanbarcode_job_details 
                    WHERE barcode_no ='$barcode_no'
                    ORDER BY created_date DESC";
      $query_3    = $second_DB->query($sql_3);
      $res_3      = $query_3->row();
      echo json_encode(
        array(
          "status_code"   => 200,
          "status"        => "success",
          "message"       => "Barcode " . $barcode_no . " sudah di scan produksi",
          "data"          => $res,
          "data_details"  => $res_3,
          "loc_result"    => $barcode_no_array[5]
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code"   => 404,
          "status"        => "error",
          "message"       => "Barcode " . $barcode_no . " belum di scan produksi",
          "data"          => array(),
          "loc_result"    => $barcode_no_array[5]
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
    $draw         = intval($this->input->get("draw"));
    $start        = intval($this->input->get("start"));
    $length       = intval($this->input->get("length"));
    $now          = date("Y-m-d");

    $tanggal      = $this->input->post('tanggal');
    $bulan        = $this->input->post('bulan');
    $tahun        = $this->input->post('tahun');
    $jenis_part   = $this->input->post('jenis_part');
    $inspector    = $this->input->post('inspector');
    $sql          = "";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    $new_bulan    = 0;
    if (strlen($bulan) == 1) {
      $new_bulan  = "0" . $bulan;
    } else {
      $new_bulan  = $bulan;
    }

    $table_name   = 'Trans_Job'.$tahun.$new_bulan; //Trans_Job202301

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = ALL
    if ($tanggal != 'All' && $jenis_part == 'All') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(3); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND DAY(a.scan_date) = '$tanggal' AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = POWER CORD
    if ($tanggal != 'All' && $jenis_part == 'Power Cord') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(1); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND DAY(a.scan_date) = '$tanggal' AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = WIRING
    if ($tanggal != 'All' && $jenis_part == 'Wiring') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(2); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND DAY(a.scan_date) = '$tanggal' AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = ALL
    if ($tanggal == 'All' && $jenis_part == 'All') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(3); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = POWER CORD
    if ($tanggal == 'All' && $jenis_part == 'Power Cord') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(1); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = WIRING
    if ($tanggal == 'All' && $jenis_part == 'Wiring') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(2); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND MONTH(a.scan_date) = '$bulan' AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = ALL
    if ($tanggal == 'All' && $jenis_part == 'All') {
      //GET WH LOCATION
      $WHData         = $this->whlocation->get_by_id(3); //3=ALL, 2=WR, 1=PC 
      $WHLocation     = " AND a.loc_result IN (".$WHData->WhLocation.") ";

      $where   = $WHLocation." AND YEAR(a.scan_date) = '$tahun' ";
    }

    if ($inspector == 'All') {
      $where .= ""; 
    } else {
      $where .= " AND scan_by = '$inspector' ";
    }

    $sql = "SELECT 
							a.*, 
							b.PartID, 
							b.Keterangan,
							b.NoBukti,
							b.WHResult,
							b.QtyOrder,
							b.UnitID,
							b.Tgl,
							c.PartName 
						FROM 
							tbl_scanbarcode_job a 
							LEFT JOIN $table_name b on b.NoBukti = a.no_job 
							LEFT JOIN Ms_Part c ON c.PartID = b.PartID 
						WHERE 
							a.loc_id = 'QC001'
							$where
						ORDER BY 
							scan_id DESC";
    //echo $sql; exit;
    $query   = $second_DB->query($sql);
    $result  = $query->result();
    $data    = [];
    $no      = 1;
    $attr    = "";
    $isi     = "";

    foreach ($result as $key => $value) {
      $isi = "'" . $value->scan_id . "', '" . $value->barcode_no . "', '" . $value->loc_id . "', '" . $value->scan_status . "'";
      if ($value->scan_status == 'NG') {
        $attr   = '<span class="pointer" onclick="view_details(' . $isi . ')">' . $value->barcode_no . '</span>';
      } else {
        $attr   = $value->barcode_no;
      }

      $tgl          = substr($value->scan_date, 0, -4);
      $tgl2         = date("Y-m-d", strtotime($tgl));
      $isi_barcode  = explode('|', $value->barcode_no);
      $split_lagi   = explode('-', $isi_barcode[1]);
      $no_job       = substr($split_lagi[0], 4, 25);

      $no_bukti     = "";
      if ($value->scan_status == 'NG') {
        $no_bukti   = '<a href="#" onclick="view_details(' . $isi . ')" style="font-weight: 700; font-size: 15px; color: #ff5370;">' . $value->NoBukti . '</a>'; /*' . $job_data->NoBukti . '*/
      } else {
        $no_bukti   = $value->NoBukti;
      }

      $data[] = array(
        $no++,
        $this->timeAgo($tgl),
        $value->scan_status == 'OK' ? '<span class="badge badge-success">' . $value->scan_status . '</span>' : '<span class="badge badge-danger pointer" onclick="view_details(' . $isi . ')">' . $value->scan_status . '</span>',
        $no_bukti,
        substr($value->scan_date, 0, -4),
        $value->PartID,
        $value->PartName,
        number_format($value->QtyOrder, 0),
        number_format($isi_barcode[7], 0),
        $value->UnitID,
        substr($value->Tgl, 0, -4),
        '<span class="badge badge-warning text-white">' . $value->loc_id . '</span>',
        $value->Keterangan,
        $value->barcode_no,
        $value->scan_by
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

  public function show_barcode_data_list_OLD()
  {
    $draw         = intval($this->input->get("draw"));
    $start        = intval($this->input->get("start"));
    $length       = intval($this->input->get("length"));
    $now          = date("Y-m-d");

    $tanggal      = $this->input->post('tanggal');
    $bulan        = $this->input->post('bulan');
    $tahun        = $this->input->post('tahun');
    $jenis_part   = $this->input->post('jenis_part');
    $inspector    = $this->input->post('inspector');
    $sql          = "";

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    $new_bulan    = 0;
    if (strlen($bulan) == 1) {
      $new_bulan  = "0" . $bulan;
    } else {
      $new_bulan  = $bulan;
    }

    $table_name   = 'Trans_Job'.$tahun.$new_bulan; //Trans_Job202301

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = ALL
    if ($tanggal != 'All' && $jenis_part == 'All') {
      $where   = " AND a.loc_result IN ('WH-FG', 'WH-GRS00', 'WH-GRS01', 'WH-FG01', 
                   'WH-R01', 'WH-WIP00', 'WH-PR', 'WH-R')
									 AND DAY(a.scan_date) = '$tanggal'
									 AND MONTH(a.scan_date) = '$bulan'
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = POWER CORD
    if ($tanggal != 'All' && $jenis_part == 'Power Cord') {
      $where   = " AND a.loc_result IN ('WH-FG01', 'WH-GRS01', 'WH-PR')
									 AND DAY(a.scan_date) = '$tanggal'
									 AND MONTH(a.scan_date) = '$bulan'
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL TIDAK ALL DAN JENIS = WIRING
    if ($tanggal != 'All' && $jenis_part == 'Wiring') {
      $where   = " AND a.loc_result IN ('WH-FG', 'WH-GRS00', 'WH-R')
									 AND DAY(a.scan_date) = '$tanggal'
									 AND MONTH(a.scan_date) = '$bulan'
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = ALL
    if ($tanggal == 'All' && $jenis_part == 'All') {
      $where   = " AND a.loc_result IN ('WH-FG', 'WH-GRS00', 'WH-GRS01', 'WH-FG01', 'WH-PR', 'WH-R') 
									 AND MONTH(a.scan_date) = '$bulan' 
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = POWER CORD
    if ($tanggal == 'All' && $jenis_part == 'Power Cord') {
      $where   = " AND a.loc_result IN ('WH-FG01', 'WH-GRS01', 'WH-PR')
									 AND MONTH(a.scan_date) = '$bulan' 
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = WIRING
    if ($tanggal == 'All' && $jenis_part == 'Wiring') {
      $where   = " AND a.loc_result IN ('WH-FG', 'WH-GRS00', 'WH-R')
									 AND MONTH(a.scan_date) = '$bulan' 
									 AND YEAR(a.scan_date) = '$tahun' ";
    }

    //JIKA PILIHAN TANGGAL ALL DAN JENIS = ALL
    if ($tanggal == 'All' && $jenis_part == 'All') {
      $where   = " AND a.loc_result IN ('WH-FG', 'WH-GRS00', 'WH-GRS01', 'WH-FG01', 
                   'WH-WIP00', 'WH-PR', 'WH-R')
								 	 AND YEAR(a.scan_date) = '$tahun' ";
    }

    if ($inspector == 'All') {
      $where .= ""; 
    } else {
      $where .= " AND scan_by = '$inspector' ";
    }

    $sql = "SELECT 
							a.*, 
							b.PartID, 
							b.Keterangan,
							b.NoBukti,
							b.WHResult,
							b.QtyOrder,
							b.UnitID,
							b.Tgl,
							c.PartName 
						FROM 
							tbl_scanbarcode_job a 
							LEFT JOIN $table_name b on b.NoBukti = a.no_job 
							LEFT JOIN Ms_Part c ON c.PartID = b.PartID 
						WHERE 
							a.loc_id = 'QC001'
							$where
						ORDER BY 
							scan_id DESC";
    //echo $sql; exit;
    $query   = $second_DB->query($sql);
    $result  = $query->result();
    $data    = [];
    $no      = 1;
    $attr    = "";
    $isi     = "";

    foreach ($result as $key => $value) {
      $isi = "'" . $value->scan_id . "', '" . $value->barcode_no . "', '" . $value->loc_id . "', '" . $value->scan_status . "'";
      if ($value->scan_status == 'NG') {
        $attr   = '<span class="pointer" onclick="view_details(' . $isi . ')">' . $value->barcode_no . '</span>';
      } else {
        $attr   = $value->barcode_no;
      }

      $tgl          = substr($value->scan_date, 0, -4);
      $tgl2         = date("Y-m-d", strtotime($tgl));
      $isi_barcode  = explode('|', $value->barcode_no);
      $split_lagi   = explode('-', $isi_barcode[1]);
      $no_job       = substr($split_lagi[0], 4, 25);

      $no_bukti     = "";
      if ($value->scan_status == 'NG') {
        $no_bukti   = '<a href="#" onclick="view_details(' . $isi . ')" style="font-weight: 700; font-size: 15px; color: #ff5370;">' . $value->NoBukti . '</a>'; /*' . $job_data->NoBukti . '*/
      } else {
        $no_bukti   = $value->NoBukti;
      }

      $data[] = array(
        $no++,
        $this->timeAgo($tgl),
        $value->scan_status == 'OK' ? '<span class="badge badge-success">' . $value->scan_status . '</span>' : '<span class="badge badge-danger pointer" onclick="view_details(' . $isi . ')">' . $value->scan_status . '</span>',
        $no_bukti,
        substr($value->scan_date, 0, -4),
        $value->PartID,
        $value->PartName,
        number_format($value->QtyOrder, 0),
        number_format($isi_barcode[7], 0),
        $value->UnitID,
        substr($value->Tgl, 0, -4),
        '<span class="badge badge-warning text-white">' . $value->loc_id . '</span>',
        $value->Keterangan,
        $value->barcode_no,
        $value->scan_by
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
