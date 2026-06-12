<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends CI_Controller
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
		$this->load->model('barcode_model', 'barcode_sales');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Scan Barcode";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/cari_barcode_sales', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function cari_barcode()
	{
		$barcode				= $this->input->post('code_barcode');
    //echo $barcode; exit;
		$barcode_array	= explode('|', $barcode);
		$part_id 				= $barcode_array[0];
		$nomor_do 			= $barcode_array[1];
		$qty_order 			= $barcode_array[2];

		$data_barcode 	= $this->barcode_sales->get_data_print_do_new($barcode, $part_id, $nomor_do, $qty_order);
    if (count($data_barcode) > 0) {
      $second_DB 	= $this->load->database('bjsmas01_db', TRUE);
      $DT1        = $second_DB->select('id, barcode_id, nama_driver, no_polisi, checker, persiapan_planning, qty_order, part_id, notes, ekspedisi')->get_where('tbl_scanbarcode_approval', array('barcode_id' => $barcode))->row();
      $DT2        = $second_DB->select('Id, BarcodeID, Total, DONumber, PONumber')->get_where('tbl_scanbarcode_approval_dt', array('BarcodeID' => $barcode))->result();
      echo json_encode(
				array(
					"status_code" => 200,
					"status" 			=> "success",
					"message" 		=> "Sukses menampilkan barcode " . $barcode,
					"data" 				=> $data_barcode,
          "detail1"     => $DT1,
          "detail2"     => $DT2,
				)
			);
		} else {
			echo json_encode(
				array(
					"status_code" => 404,
					"status" 			=> "error",
					"message" 		=> "Barcode " . $barcode . " tidak ditemukan!",
					"data" 				=> array()
				)
			);
		}
	}

  public function hapus_single_row()
  {
    $Barcode    = $this->input->post('Barcode');
    $DetailID   = $this->input->post('Id');
    $second_DB 	= $this->load->database('bjsmas01_db', TRUE);

    $second_DB->where('Id', $DetailID);
    $Delete = $second_DB->delete('tbl_scanbarcode_approval_dt');
    if ($Delete) {
      echo json_encode(array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data sukses dihapus."
      ));
    } else {
      echo json_encode(array(
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal dihapus."
      ));
    }
    exit();
  }

	private function get_customer_name($no_do)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$do_array 	= explode('/', $no_do);
		$thn_bln_do = $do_array[2];
		$table_name	= "trans_SJHD" . $thn_bln_do;
		$query 			= $second_DB->query("SELECT a.*, b.PartnerName FROM $table_name a
																		 LEFT JOIN Ms_Partner b ON b.PartnerID = a.ReceiverID
																		 WHERE NoBukti = '$no_do'
																		 ORDER BY a.TGL DESC");

		return $query->row();
	}

  public function approved_status()
  {
    //echo "OK"; exit;
    $this->_validation_wh();

    $no_do        = $this->input->post('no_do');
    $no_po        = $this->input->post('no_po');
    $nm_customer  = strtoupper($this->input->post('nm_customer'));
    $no_barcode   = $this->input->post('no_barcode');
    $part_id      = $this->input->post('part_no');
    $qty_order    = $this->input->post('qty_order');
    $Checker2     = $this->input->post('Checker2');
    $PerPlanning  = $this->input->post('PersiapanPlanning');
    $nama_driver  = ucwords($this->input->post('nama_driver'));
    $no_polisi    = strtoupper($this->input->post('no_polisi'));
    $kodeDetail   = $this->input->post('kodeDetail'); // array
    $totalColly   = $this->input->post('TotalColly'); // array
    $Notes        = $this->input->post('Notes'); // NOTES
    $ekspedisi    = $this->input->post('ekspedisi'); // Y atau N
    //echo $Notes." aaaaa "; exit;
    //echo json_encode(array('status' => 'error', 'data' => $Notes)); exit;
    $createBy     = $this->session->userdata('user_code');
    $createDate   = date('Y-m-d H:i:s');
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    // Pastikan kodeDetail array
    $kodeDetail   = is_array($kodeDetail) ? $kodeDetail : [];

    // Cek apakah semua kodeDetail kosong (berarti insert baru)
    $is_insert = true;
    foreach ($kodeDetail as $kd) {
      if (!empty($kd)) {
        $is_insert = false;

        break;
      }
    }

    if ($is_insert) {
      // Cek apakah header (barcode) sudah ada
      $where = [
        'barcode_id' => $no_barcode,
        'no_po'      => $no_po,
        'no_do'      => $no_do,
        'part_id'    => $part_id,
        'qty_order'  => $qty_order
      ];

      $cek = $second_DB->get_where('tbl_scanbarcode_approval', $where)->num_rows();

      if ($cek == 0) {
        // === Validasi kombinasi DO + PO + Part + Qty (tanpa barcode) ===
        $where2 = [
          'no_po'     => $no_po,
          'no_do'     => $no_do,
          'part_id'   => $part_id,
          'qty_order' => $qty_order
        ];

        $cek2 = $second_DB->get_where('tbl_scanbarcode_approval', $where2)->num_rows();
        if ($cek2 > 0) {
          return $this->_json_response(500, "Barcode $no_barcode dengan DO, PO dan Part ID di atas sudah terdaftar.");
        }

        // === Insert Header ===
        $data = [
          'barcode_id'          => $no_barcode,
          'no_po'               => $no_po,
          'no_do'               => $no_do,
          'part_id'             => $part_id,
          'qty_order'           => $qty_order,
          'nama_customer'       => $nm_customer,
          'nama_driver'         => $nama_driver,
          'no_polisi'           => $no_polisi,
          'checker'             => $Checker2,
          'persiapan_planning'  => $PerPlanning,
          'lokasi_id'           => "WH001",
          'lokasi_scan'         => "DELIVERY",
          'notes'               => $Notes,
          'ekspedisi'           => $ekspedisi,
          'approved_by'         => $createBy,
          'create_date'         => $createDate
        ];

        //echo json_encode(array('status' => 'error', 'data' => $data)); exit;
        //echo json_encode($data);

        $insert = $this->barcode_sales->save($data);

        if (!$insert) {
          return $this->_json_response(500, "Gagal menyimpan header barcode $no_barcode");
        }
      }

      // === Lanjutkan Insert Detail Colly jika TotalColly[] punya data ===
      $colly_data = [];

      if (is_array($totalColly) && !empty($totalColly)) {
        foreach ($totalColly as $total) {
          if (!empty($total)) {
            $colly_data[] = [
              'PONumber'    => $no_po,
              'DONumber'    => $no_do,
              //'Total'       => floatval($total),
              'Total'       => 0,
              'BarcodeID'   => $no_barcode,
              'CreateDate'  => $createDate,
              'CreateBy'    => $createBy
            ];
          }
        }

        if (!empty($colly_data)) {
          $second_DB->insert_batch('tbl_scanbarcode_approval_dt', $colly_data);
        }
      }

      return $this->_json_response(200, "Barcode $no_barcode sukses disimpan (header &/ detail)");
    } else {
      // === Update Detail berdasarkan kodeDetail[] ===
      $updateSuccess = true;

      if (is_array($totalColly)) {
          foreach ($totalColly as $index => $total) {
              $total = floatval($total);
              $kode  = isset($kodeDetail[$index]) ? $kodeDetail[$index] : '';

              if (!empty($kode)) {
                  // UPDATE
                  $update = $second_DB->update(
                      'tbl_scanbarcode_approval_dt',
                      ['Total' => $total],
                      ['Id' => $kode]
                  );

                  if (!$update) {
                      $updateSuccess = false;
                      break;
                  }
              } else {
                  // INSERT jika kodeDetail kosong
                  if (!empty($total)) {
                      $second_DB->insert('tbl_scanbarcode_approval_dt', [
                          'PONumber'    => $no_po,
                          'DONumber'    => $no_do,
                          //'Total'       => $total,
                          'Total'       => 0,
                          'BarcodeID'   => $no_barcode,
                          'CreateDate'  => $createDate,
                          'CreateBy'    => $createBy
                      ]);
                  }
              }
          }
      }

      if ($updateSuccess) {
        return $this->_json_response(200, "Data berhasil diupdate");
      } else {
        return $this->_json_response(500, "Gagal mengupdate data");
      }
    }
  }

  public function approved_status_OLD()
  {
    //echo "OK"; exit;
    $this->_validation_wh();

    $no_do        = $this->input->post('no_do');
    $no_po        = $this->input->post('no_po');
    $nm_customer  = strtoupper($this->input->post('nm_customer'));
    $no_barcode   = $this->input->post('no_barcode');
    $part_id      = $this->input->post('part_no');
    $qty_order    = $this->input->post('qty_order');
    $Checker2     = $this->input->post('Checker2');
    $PerPlanning  = $this->input->post('PersiapanPlanning');
    $nama_driver  = ucwords($this->input->post('nama_driver'));
    $no_polisi    = strtoupper($this->input->post('no_polisi'));
    $kodeDetail   = $this->input->post('kodeDetail'); // array
    $totalColly   = $this->input->post('TotalColly'); // array
    $Notes        = $this->input->post('Notes'); // NOTES
    //echo $Notes." aaaaa "; exit;
    //echo json_encode(array('status' => 'error', 'data' => $Notes)); exit;
    $createBy     = $this->session->userdata('user_code');
    $createDate   = date('Y-m-d H:i:s');
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    // Pastikan kodeDetail array
    $kodeDetail   = is_array($kodeDetail) ? $kodeDetail : [];

    // Cek apakah semua kodeDetail kosong (berarti insert baru)
    $is_insert = true;
    foreach ($kodeDetail as $kd) {
      if (!empty($kd)) {
        $is_insert = false;

        break;
      }
    }

    if ($is_insert) {
      // Cek apakah header (barcode) sudah ada
      $where = [
        'barcode_id' => $no_barcode,
        'no_po'      => $no_po,
        'no_do'      => $no_do,
        'part_id'    => $part_id,
        'qty_order'  => $qty_order
      ];

      $cek = $second_DB->get_where('tbl_scanbarcode_approval', $where)->num_rows();

      if ($cek == 0) {
        // === Validasi kombinasi DO + PO + Part + Qty (tanpa barcode) ===
        $where2 = [
          'no_po'     => $no_po,
          'no_do'     => $no_do,
          'part_id'   => $part_id,
          'qty_order' => $qty_order
        ];

        $cek2 = $second_DB->get_where('tbl_scanbarcode_approval', $where2)->num_rows();
        if ($cek2 > 0) {
          return $this->_json_response(500, "Barcode $no_barcode dengan DO, PO dan Part ID di atas sudah terdaftar.");
        }

        // === Insert Header ===
        $data = [
          'barcode_id'          => $no_barcode,
          'no_po'               => $no_po,
          'no_do'               => $no_do,
          'part_id'             => $part_id,
          'qty_order'           => $qty_order,
          'nama_customer'       => $nm_customer,
          'nama_driver'         => $nama_driver,
          'no_polisi'           => $no_polisi,
          'checker'             => $Checker2,
          'persiapan_planning'  => $PerPlanning,
          'lokasi_id'           => "WH001",
          'lokasi_scan'         => "DELIVERY",
          'notes'               => $Notes,
          'approved_by'         => $createBy,
          'create_date'         => $createDate
        ];

        echo json_encode(array('status' => 'error', 'data' => $data)); exit;
        //echo json_encode($data);

        $insert = $this->barcode_sales->save($data);

        if (!$insert) {
          return $this->_json_response(500, "Gagal menyimpan header barcode $no_barcode");
        }
      }

      // === Lanjutkan Insert Detail Colly jika TotalColly[] punya data ===
      $colly_data = [];

      if (is_array($totalColly) && !empty($totalColly)) {
        foreach ($totalColly as $total) {
          if (!empty($total)) {
            $colly_data[] = [
              'PONumber'    => $no_po,
              'DONumber'    => $no_do,
              //'Total'       => floatval($total),
              'Total'       => 0,
              'BarcodeID'   => $no_barcode,
              'CreateDate'  => $createDate,
              'CreateBy'    => $createBy
            ];
          }
        }

        if (!empty($colly_data)) {
          $second_DB->insert_batch('tbl_scanbarcode_approval_dt', $colly_data);
        }
      }

      return $this->_json_response(200, "Barcode $no_barcode sukses disimpan (header &/ detail)");
    } else {
      // === Update Detail berdasarkan kodeDetail[] ===
      $updateSuccess = true;

      if (is_array($totalColly)) {
          foreach ($totalColly as $index => $total) {
              $total = floatval($total);
              $kode  = isset($kodeDetail[$index]) ? $kodeDetail[$index] : '';

              if (!empty($kode)) {
                  // UPDATE
                  $update = $second_DB->update(
                      'tbl_scanbarcode_approval_dt',
                      ['Total' => $total],
                      ['Id' => $kode]
                  );

                  if (!$update) {
                      $updateSuccess = false;
                      break;
                  }
              } else {
                  // INSERT jika kodeDetail kosong
                  if (!empty($total)) {
                      $second_DB->insert('tbl_scanbarcode_approval_dt', [
                          'PONumber'    => $no_po,
                          'DONumber'    => $no_do,
                          //'Total'       => $total,
                          'Total'       => 0,
                          'BarcodeID'   => $no_barcode,
                          'CreateDate'  => $createDate,
                          'CreateBy'    => $createBy
                      ]);
                  }
              }
          }
      }

      if ($updateSuccess) {
        return $this->_json_response(200, "Data berhasil diupdate");
      } else {
        return $this->_json_response(500, "Gagal mengupdate data");
      }
    }
  }

	public function produk_terkirim()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Barang Terkirim";
			$data['icon_halaman'] 	= "icon-package";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/produk_terkirim', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function produk_terkirim_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$where = " DAY(A.create_date) = '" . $tanggal . "'
								AND MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$where = " MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$where = " YEAR(A.create_date) = '" . $tahun . "'";
		}

		// $sql 		= "EXEC dbo.GetDataBarangTerkirim @Day = ?, @Month = ?, @Year = ?";
		// $query 	= $second_DB->query($sql);

    $sql    = "EXEC dbo.GetDataBarangTerkirim @Day = ?, @Month = ?, @Year = ?";
    $query  = $second_DB->query($sql, array($tanggal, $bulan, $tahun));
		$data 	= [];
		$no 		= 1;

		foreach ($query->result() as $value) {
			//$data_box 		= explode('/', $value->seqstiker);
      if (!empty($value->seqstiker)) {
        $data_box = explode('/', $value->seqstiker);
        $badge_text = isset($data_box[1]) ? $data_box[1] : '-'; // fallback kalau index ke-1 gak ada
      } else {
        $badge_text = '-'; // default kalau NULL
      }
      $PerPlanning  = $value->persiapan_planning == '' ? 'KOSONG' : $value->persiapan_planning;

			$data[] = array(
				$no++,
        '<a class="text-danger" href="scan_details/'.base64_encode($value->no_do).'/'.base64_encode($value->no_po).'/'.$value->part_id.'/'.$value->qty_order.'/'.date('Y-m-d', strtotime($value->createtime)).'/'.base64_encode($PerPlanning).'/'.$value->nama_driver.'/'.base64_encode($value->no_polisi).'/'.base64_encode($value->barcode_id).'" title="Klik more" target="_blank">'. $value->no_do.'</a>',
        $value->no_po,
				$value->barcode_id,
				'<span class="badge badge-success" style="font-size: 14px;">'.$value->lokasi_scan.'</span>',
				get_created_by($value->approved_by),
				$value->Namadivisi,
				substr($value->create_date, 0, -4),
				'<span class="badge badge-danger" style="font-size: 17px;">'.$badge_text.'</span>',
				number_format($value->qtypallet),
				number_format($value->qty_order),
				$value->part_id,
				$value->PartName,
				$value->nama_customer,
				$value->nama_driver . " (" . $value->no_polisi . ")"
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function produk_terkirim_list_range_OLD()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		// $sql 		= "WITH RankedData AS (
    //             SELECT 
    //                 A.id, 
    //                 A.lokasi_scan, 
    //                 A.create_date, 
    //                 A.approved_by, 
    //                 A.no_po, 
    //                 A.no_do, 
    //                 A.part_id, 
    //                 A.qty_order, 
    //                 A.nama_customer, 
    //                 A.nama_driver, 
    //                 A.no_polisi, 
    //                 A.barcode_id, 
    //                 A.checker, 
    //                 A.persiapan_planning, 
    //                 B.seqstiker, 
    //                 B.qtypallet,
    //                 B.createtime, 
    //                 C.Namadivisi, 
    //                 D.PartName,
    //                 ROW_NUMBER() OVER (
    //                     PARTITION BY A.no_po, A.no_do, A.part_id, A.qty_order 
    //                     ORDER BY A.create_date DESC, A.id DESC -- create_date terbaru atau id tertinggi jika create_date sama
    //                 ) as rn
    //             FROM 
    //                 tbl_scanbarcode_approval A 
    //             LEFT JOIN 
    //                 Ms_Part D ON D.PartID = A.part_id 
    //             LEFT JOIN 
    //                 tbl_msdivisi C ON A.lokasi_id = C.Iddivisi 
    //             LEFT JOIN 
    //                 ( ( SELECT * FROM tbl_printqrcodedo ) UNION ALL ( SELECT * FROM tbl_printqrcodedoulang ) ) B 
    //                 ON B.barcodeid = A.barcode_id 
    //             WHERE 
    //                 CAST(A.create_date as date) BETWEEN '$start_date' AND '$end_date'
    //         )
    //         SELECT 
    //             id, 
    //             lokasi_scan, 
    //             create_date, 
    //             approved_by, 
    //             no_po, 
    //             no_do, 
    //             part_id, 
    //             qty_order, 
    //             nama_customer, 
    //             nama_driver, 
    //             no_polisi, 
    //             barcode_id, 
    //             checker, 
    //             persiapan_planning, 
    //             seqstiker, 
    //             qtypallet,
    //             createtime, 
    //             Namadivisi, 
    //             PartName
    //         FROM 
    //             RankedData
    //         WHERE 
    //             rn = 1
    //         ORDER BY 
    //             create_date DESC";
    //           //echo $sql; exit;
		// $query 	= $second_DB->query($sql);

    //$sql    = "EXEC dbo.GetDataBarangTerkirimByDateRange @StartDate = ?, @EndDate = ?";
    //$query  = $second_DB->query($sql, array($start_date, $end_date));

    $sql    = "EXEC dbo.GetBarangTerkirimNew @StartDate = ?, @EndDate = ?";
    $query  = $second_DB->query($sql, array($start_date, $end_date));
		$data 	= [];
		$No 		= 1;

		foreach ($query->result() as $value) {
			//$data_box     = explode('/', $value->seqstiker);
      $PerPlanning  = $value->persiapan_planning == '' ? 'KOSONG' : $value->persiapan_planning;
      $no           = (!empty($value->no_po)) ? $No : '';
			$data[] = array(
				$value->NomorUrut,
				'<a class="text-danger" href="scan_details/'.base64_encode($value->no_do).'/'.base64_encode($value->no_po).'/'.$value->part_id.'/'.$value->QtyOrder.'/'.date('Y-m-d', strtotime($value->createtime)).'/'.base64_encode($PerPlanning).'/'.base64_encode($value->nama_driver).'/'.base64_encode($value->no_polisi).'/'.base64_encode($value->barcode_id).'" title="Klik more" target="_blank">'. $value->no_do.'</a>',
				$value->no_po,
				$value->barcode_id,
				'<span class="badge badge-success" style="font-size: 14px;">' . $value->lokasi_scan . '</span>',
				get_created_by($value->approved_by),
				$value->Namadivisi,
				$value->create_date,
				'<span class="badge badge-danger" style="font-size: 17px;">'.$value->TotalBox.'</span>',
				$value->QtyPerBox,
				$value->QtyOrder,
				$value->TotalBoxKirim,
				$value->part_id,
				$value->PartName,
				$value->nama_customer,
				$value->nama_driver . " (" . $value->no_polisi . ")"
			);

      $no++;
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

  public function produk_terkirim_list_range_OLD2()
  {
    $StartDate = $this->input->post('start_date');
    $EndDate   = $this->input->post('end_date');
    $second_DB = $this->load->database('bjsmas01_db', TRUE);


    $sql    = "EXEC dbo.GetDataBarangTerkirim @StartDate = ?, @EndDate = ?";
    $query  = $second_DB->query($sql, array($StartDate, $EndDate));
    $result = $query->result();
    $Total  = count($result);
    $data   = array();
    $no     = $_POST['start'];
    foreach ($result as $key => $value) {
      $no++;
      $PerPlanning  = $value->persiapan_planning == '' ? 'KOSONG' : $value->persiapan_planning;
      $row   = array();

      $row[] =	$value->NomorUrut;
      $row[] =	$value->create_date;
			$row[] =	'<a class="text-danger" href="scan_details/'.base64_encode($value->no_do).'/'.base64_encode($value->no_po).'/'.$value->part_id.'/'.$value->QtyOrder.'/'.date('Y-m-d', strtotime($value->createtime)).'/'.base64_encode($PerPlanning).'/'.base64_encode($value->nama_driver).'/'.base64_encode($value->no_polisi).'/'.base64_encode($value->barcode_id).'" title="Klik more" target="_blank">'. $value->no_do.'</a>';
			$row[] =	$value->no_po;
      $row[] =	$value->part_id;
			$row[] =	$value->PartName;
      $row[] =	$value->QtyOrder;
      $row[] =	$value->QtyPerBox;
			$row[] =	$value->TotalBox;
			$row[] =	$value->TotalBoxKirim;
      $row[] =	get_created_by($value->approved_by);
			$row[] =	$value->barcode_id;
			$row[] =	$value->lokasi_scan;
			$row[] =	$value->Namadivisi;
			$row[] =	$value->nama_customer;
			$row[] =	$value->nama_driver." (" . $value->no_polisi . ")";

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function produk_terkirim_list_rangeXXXX()
  {
    $StartDate = $this->input->post('start_date');
    $EndDate   = $this->input->post('end_date');
    $second_DB = $this->load->database('bjsmas01_db', TRUE);


    // $sql    = "EXEC dbo.GetDataBarangTerkirim @StartDate = ?, @EndDate = ?";
    // $query  = $second_DB->query($sql, array($StartDate, $EndDate));

    $sql    = "EXEC dbo.GetDataBarangTerkirimByDateRange @StartDate = ?, @EndDate = ?";
    $query  = $second_DB->query($sql, array($StartDate, $EndDate));
    $result = $query->result();
    $Total  = count($result);
    $data   = array();
    $no     = $_POST['start'];
    $Nomor  = 1;
    foreach ($result as $key => $value) {
      // if (!empty($value->seqstiker)) {
      //   $data_box = explode('/', $value->seqstiker);
      //   $TotalBox = isset($data_box[1]) ? $data_box[1] : '-'; // fallback kalau index ke-1 gak ada
      // } else {
      //   $TotalBox = '-'; // default kalau NULL
      // }
      $PerPlanning  = $value->persiapan_planning == '' ? 'KOSONG' : $value->persiapan_planning;
      $row   = array();

      $row[] =	$Nomor++; //$value->NomorUrut;
      $row[] =	$value->create_date;
			$row[] =	'<a class="text-danger" href="scan_details/'.base64_encode($value->no_do).'/'.base64_encode($value->no_po).'/'.$value->part_id.'/'.$value->qty_order.'/'.date('Y-m-d', strtotime($value->createtime)).'/'.base64_encode($PerPlanning).'/'.base64_encode($value->nama_driver).'/'.base64_encode($value->no_polisi).'/'.base64_encode($value->barcode_id).'" title="Klik more" target="_blank">'. $value->no_do.'</a>';
			$row[] =	$value->no_po;
      $row[] =	$value->part_id;
			$row[] =	$value->PartName;
      $row[] =	$value->qty_order;
      $row[] =	$value->qtypallet;
			$row[] =	$value->TotalBox;
			//$row[] =	"";//$value->TotalBoxKirim;
      $row[] =	get_created_by($value->approved_by);
			$row[] =	$value->barcode_id;
			$row[] =	$value->lokasi_scan;
			$row[] =	$value->Namadivisi;
			$row[] =	$value->nama_customer;
			$row[] =	$value->nama_driver." (" . $value->no_polisi . ")";

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function produk_terkirim_list_range()
	{
		$draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $second_DB      = $this->load->database('bjsmas01_db', TRUE);

    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    $user_dept      = $this->session->userdata('user_dept_name');

    $Sql            = "EXEC dbo.GetDataBarangTerkirimByDateRange @StartDate = ?, @EndDate = ?";
    $Query          = $second_DB->query($Sql, array($StartDate, $EndDate));
    $Result         = $Query->result();
		$Data           = [];
		$Nomor 		      = 1;

    foreach ($Result as $key => $value) {
      $Isi          = "'".$value->id."', '".$value->barcode_id."'";
      $PerPlanning  = $value->persiapan_planning == '' ? 'KOSONG' : $value->persiapan_planning;
			$Data[] = array(
        $Nomor++,
        '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
            </div>
          </div>
        </div>',
        $value->create_date,
        '<a class="text-danger" href="scan_details/'.base64_encode($value->no_do).'/'.base64_encode($value->no_po).'/'.$value->part_id.'/'.$value->qty_order.'/'.date('Y-m-d', strtotime($value->createtime)).'/'.base64_encode($PerPlanning).'/'.base64_encode($value->nama_driver).'/'.base64_encode($value->no_polisi).'/'.base64_encode($value->barcode_id).'" title="Klik more" target="_blank">'. $value->no_do.'</a>',
        $value->no_po,
        $value->part_id,
        $value->PartName,
        $value->qty_order,
        $value->qtypallet,
        $value->TotalBox,
        get_created_by($value->approved_by),
        $value->barcode_id,
        $value->lokasi_scan,
        $value->Namadivisi,
        $value->nama_customer,
        $value->nama_driver." (" . $value->no_polisi . ")",
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($result);
		exit();
	}

  public function produk_terkirim_hapus_OLD()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $BarcodeId  = $this->input->post('BarcodeId');
      $Id         = $this->input->post('Id');
      $second_DB 	= $this->load->database('bjsmas01_db', TRUE);

      //echo json_encode(array("Id" => $Id, "Barcode" => $BarcodeId)); exit;

      $second_DB->where('id', $Id);
      $second_DB->where('barcode_id', $BarcodeId);
      $Delete = $second_DB->delete('tbl_scanbarcode_approval');
      if ($Delete) {
        $second_DB->where('BarcodeID', $BarcodeId);
        $Delete = $second_DB->delete('tbl_scanbarcode_approval_dt');

        echo json_encode(array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses dihapus."
        ));
      } else {
        echo json_encode(array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal dihapus."
        ));
      }
      exit();
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function produk_terkirim_hapus()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {

      $BarcodeId = $this->input->post('BarcodeId');
      $Id        = $this->input->post('Id');
      $second_DB = $this->load->database('bjsmas01_db', TRUE);

      // Mulai transaksi
      $second_DB->trans_begin();

      try {
        // Hapus data dari tabel utama
        $second_DB->where('id', $Id);
        $second_DB->where('barcode_id', $BarcodeId);
        $second_DB->delete('tbl_scanbarcode_approval');

        // Hapus data dari tabel detail
        $second_DB->where('BarcodeID', $BarcodeId);
        $second_DB->delete('tbl_scanbarcode_approval_dt');

        // Cek apakah ada error selama proses delete
        if ($second_DB->trans_status() === FALSE) {
          $second_DB->trans_rollback();
          echo json_encode([
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Terjadi kesalahan saat menghapus data. Transaksi dibatalkan."
          ]);
        } else {
          // Commit jika semua berhasil
          $second_DB->trans_commit();
          echo json_encode([
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses dihapus."
          ]);
        }

      } catch (Exception $e) {
        // Rollback jika terjadi exception
        $second_DB->trans_rollback();
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Terjadi kesalahan: " . $e->getMessage()
        ]);
      }

      exit();

    } else {
      echo json_encode(["status" => "forbidden"]);
    }
  }
  
  public function produk_terkirim_edit()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id         = $this->input->post('Id');
      $BarcodeId  = $this->input->post('BarcodeId');
      $second_DB  = $this->load->database('bjsmas01_db', TRUE);
      $SqlHD      = "SELECT 
                      a.id, a.barcode_id, a.no_po, a.no_do, a.part_id,
                      a.persiapan_planning, a.checker, a.no_polisi, 
                      a.nama_driver, a.nama_customer, a.notes, a.ekspedisi
                    FROM tbl_scanbarcode_approval a 
                    WHERE barcode_id = ?";
      $QueryHD    = $second_DB->query($SqlHD, array($BarcodeId));
      $ResultHD   = $QueryHD->row();

      echo json_encode(array("status_code" => 200, "DataHD" => $ResultHD));
      
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function produk_terkirim_update()
  {
    // CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $Id                 = $this->input->post('Id');
      $second_DB          = $this->load->database('bjsmas01_db', TRUE);

      $Ekspedisi          = $this->input->post('ekspedisi'); // Y atau N

      $Data = array(
        'nama_driver'     => $this->input->post('nama_driver'),
        'no_polisi'       => $this->input->post('no_polisi'),
        'checker'         => $this->input->post('Checker2'),
        'notes'           => ucfirst($this->input->post('Notes')),
        'ekspedisi'       => $Ekspedisi,
      );

      //echo json_encode(array("status" => "error", "Data" => $Data)); exit;

      $Update = $second_DB->update('tbl_scanbarcode_approval', $Data, array('id' => $Id));
      if ($Update) {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Sukses mengupdate data.'
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Gagal mengupdate data.'
        ]);
      }
      
      
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

	public function scan_details()
	{
		$data['group_halaman'] 	= "Warehouse";
		$data['nama_halaman'] 	= "Scan Details";
		$data['icon_halaman'] 	= "icon-package";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$data['no_do'] 					= base64_decode($this->uri->segment(3));
		$data['no_po'] 					= base64_decode($this->uri->segment(4));
		$DoNumber 					    = base64_decode($this->uri->segment(3));
		$PoNumber 					    = base64_decode($this->uri->segment(4));
		$data['part_id'] 				= $this->uri->segment(5);
		$PartID 				        = $this->uri->segment(5);
		$data['qty_order'] 			= $this->uri->segment(6);
		$data['po_date'] 				= $this->uri->segment(7);
		$data['pers_planning']  = strtoupper(base64_decode($this->uri->segment(8)));
    //echo $data['pers_planning']; exit;
		$data['driver'] 		    = strtoupper(urldecode($this->uri->segment(9)));
    //echo json_encode($data['driver']); exit;
		$data['nopol'] 		      = base64_decode($this->uri->segment(10));
		//$data['checker2'] 		  = base64_decode($this->uri->segment(11));
		$barcode 		            = base64_decode($this->uri->segment(11));
    //echo $barcode; exit;
    $data['header']         = $this->barcode_sales->get_data_header($barcode);
    //echo json_encode($data['header']); exit;
    $data['detail']         = $this->barcode_sales->get_data_detail($PartID, $PoNumber, $DoNumber);
    //$BarcodeID              = $barcode;
    //$data['detail']         = $this->barcode_sales->get_data_detail($PartID, $BarcodeID, $PoNumber, $DoNumber);
    //echo json_encode($data['detail']); exit;
    //echo $PoNumber."-".$DoNumber."-".$PartID."<br>";
    $data['driverList']     = $this->barcode_sales->get_driver($PoNumber, $DoNumber, $PartID);
    //echo json_encode($data['driverList']);exit;

		//ADDING TO LOG
		$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type 	= "VIEW";
		$log_data 	= "";

		log_helper($log_url, $log_type, $log_data);
		//END LOG

		$this->load->view('adminx/warehouse/scan_details', $data, FALSE);
	}

  public function scan_details_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$no_do 			= $this->input->post('no_do');
		$no_po 			= $this->input->post('no_po');
		$part_id 		= $this->input->post('part_id');
		$qty_order 	= $this->input->post('qty_order');
		$po_date 		= $this->input->post('po_date');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql = "SELECT * FROM tbl_printqrcodedo 
						WHERE CAST(createtime AS date) = '$po_date' 
						AND nodo = '$no_do' 
						AND pocustomer = '$no_po'
						AND partid = '$part_id'
						ORDER BY barcodeid";
		$query	= $second_DB->query($sql);
    $Cek    = $query->num_rows(); 
		$data 	= [];

    if ($Cek > 0) {
      foreach ($query->result() as $value) {

        $data[] = array(
          $value->seqstiker,
          $value->barcodeid,
          $value->partid,
          $value->partname,
          number_format($value->qtyorder),
          number_format($value->qtypallet),
          $value->nodo,
          $value->pocustomer,
          $value->keterangan == '' ? '-' : $value->keterangan,
          $value->customer,
          $value->createtime
        );
      }

      $result = array(
        "draw" 						=> $draw,
        "recordsTotal" 		=> $query->num_rows(),
        "recordsFiltered" => $query->num_rows(),
        "data" 						=> $data
      );

      echo json_encode($result);
      exit();
    } else {
      $sql2 = "SELECT * FROM tbl_printqrcodedoulang 
               WHERE CAST(createtime AS date) = '$po_date' 
               AND nodo = '$no_do' 
               AND pocustomer = '$no_po'
               AND partid = '$part_id'
               ORDER BY barcodeid";
      $query2	= $second_DB->query($sql2);
      $data2 	= [];

      foreach ($query2->result() as $value) {

        $data2[] = array(
          $value->seqstiker,
          $value->barcodeid,
          $value->partid,
          $value->partname,
          number_format($value->qtyorder),
          number_format($value->qtypallet),
          $value->nodo,
          $value->pocustomer,
          $value->keterangan == '' ? '-' : $value->keterangan,
          $value->customer,
          $value->createtime
        );
      }

      $result2 = array(
        "draw" 						=> $draw,
        "recordsTotal" 		=> $query2->num_rows(),
        "recordsFiltered" => $query2->num_rows(),
        "data" 						=> $data2
      );

      echo json_encode($result2);
      exit();
    }
	}

	public function summary_barang_delivery()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Ringkasan Barang Delivery";
			$data['icon_halaman'] 	= "icon-package";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/barang_delivery', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function summary_barang_delivery_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$WHERE = "WHERE DAY(a.createtime) = " . $tanggal . " AND MONTH(a.createtime) = " . $bulan . " AND YEAR(a.createtime) = " . $tahun;
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$WHERE = "WHERE MONTH(a.createtime) = " . $bulan . " AND YEAR(a.createtime) = " . $tahun;
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$WHERE = "WHERE YEAR(a.createtime) = " . $tahun;
		}

		$sql = "SELECT CASE WHEN SUM(BRC) <> QTYBOX THEN 'PROCESS' ELSE 'COMPLETED' end STATUS,
						SUM(BRC) QRCODE, a.PARTID, a.PARTNAME, SUM(a.QTYORDER) QTYORDER, a.QTYPALLET, 
						QTYBOX, a.POCUSTOMER, a.NODO, CAST(a.createtime AS date) AS tgl_scan, a.customer
						FROM
						(SELECT COUNT(a.barcodeid) BRC, a.PARTID, a.PARTNAME, a.QTYORDER, a.QTYPALLET, 
						SUBSTRING(a.seqstiker,3,1)AS QTYBOX,  a.POCUSTOMER, a.NODO, a.createtime, a.customer
						FROM tbl_printqrcodedo a
						LEFT JOIN tbl_scanbarcode b ON a.barcodeid = b.barcodeid
						GROUP BY a.PARTID, a.PARTNAME, a.QTYORDER, a.QTYPALLET, a.seqstiker, 
						a.POCUSTOMER, a.NODO, a.createtime, a.customer ) a
						$WHERE
						GROUP BY a.BRC, a.PARTID, a.PARTNAME, a.QTYPALLET, QTYBOX, a.POCUSTOMER, 
						a.NODO, CAST(a.createtime AS date), a.customer
						ORDER BY CAST(a.createtime AS date) DESC";

		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status 		= "";
		$lokasi_1 	= "";
		$lokasi_2 	= "";

		foreach ($result as $key => $value) {

			$data[] = array(
				$no++,
				$value->NODO,
				$value->POCUSTOMER,
				$value->PARTID,
				$value->PARTNAME,
				number_format($value->QTYORDER, 0),
				$value->customer
			);
		}


		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function cek_lokasi_scan($nodoc, $po)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query("SELECT TOP 1 A.barcodeid, A.nodoc, A.po, 
																		A.createtime AS tgl_masuk_lokasi_1, 
																		B.Namadivisi AS lokasi_1, C.lokasi_id, C.lokasi_scan AS lokasi_2, 
																		C.create_date AS tgl_pengiriman
																		FROM tbl_scanbarcode A
																		LEFT JOIN tbl_msdivisi B ON B.Iddivisi = A.lokasiscan
																		LEFT JOIN tbl_scanbarcode_approval C ON C.barcode_id = A.barcodeid
																		WHERE A.nodoc = '$nodoc' AND A.po = '$po' AND C.lokasi_scan IS NOT NULL");
		$cek 				= $query->num_rows();

		return $query->result();
	}

  private function _json_response($code, $message, $data = [])
  {
    echo json_encode([
      'status_code' => $code,
      'status'      => $code == 200 ? 'success' : 'error',
      'message'     => $message,
      'data'        => $data
    ]);
    exit;
  }

  private function _validation_wh()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('nama_driver') == '') {
      $data['inputerror'][]   = 'nama_driver';
      $data['error_string'][] = 'Nama Driver is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('no_polisi') == '') {
      $data['inputerror'][]   = 'no_polisi';
      $data['error_string'][] = 'No Polisi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Checker2') == '') {
      $data['inputerror'][]   = 'Checker2';
      $data['error_string'][] = 'Checker 2 is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    //$totalColly  = $this->input->post('TotalColly');
    //$notes     = $this->input->post('Notes');

    // if (is_array($totalColly)) {
    //   foreach ($totalColly as $i => $total) {
    //     if (empty($total)) {
    //       $data['inputerror'][]   = "TotalColly[$i]";
    //       $data['error_string'][] = 'Total Colly is required';
    //       $data['status']         = FALSE;
    //     }
    //   }
    // }

    // if (is_array($notes)) {
    //   foreach ($notes as $i => $not) {
    //     if ($not === '' || $not === null) {
    //       $data['inputerror'][]   = "Notes[$i]";
    //       $data['error_string'][] = 'Notes is required';
    //       $data['status']         = FALSE;
    //     }
    //   }
    // }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
