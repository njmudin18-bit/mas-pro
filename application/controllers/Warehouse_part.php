<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse_part extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('url', 'form', 'cookie'));
		$this->load->library(array('session', 'cart'));

		$this->load->model('auth_model', 'auth');
		if ($this->auth->isNotLogin());

		//START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name 	= $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('barcode_model', 'barcode_sales');
		$this->BJGMAS01  = $this->load->database("bjsmas01_db", true);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {


			$role_names 		= ['Supervisor Warehouse', 'Superadmin'];
			$role_placeholders 	= "'" . implode("','", $role_names) . "'";

			$sql 				= "SELECT roles_name, idroles FROM roles WHERE roles_name IN ($role_placeholders)";
			$query 				= $this->db->query($sql);
			$roles 				= $query->result();

			$supervisor_level 	= null;
			$superadmin_level 	= null;
			$current_level 		= $this->session->userdata('user_level');
			$current_user 		= $this->session->userdata('user_name');


			foreach ($roles as $role) {
				if ($role->roles_name === 'Supervisor Warehouse') {
					$supervisor_level = (int)$role->idroles;
				} elseif ($role->roles_name === 'Superadmin') {
					$superadmin_level = (int)$role->idroles;
				}
			}



			if ($current_level == $supervisor_level || $current_level == $superadmin_level ||  stripos($current_user, 'novi') !== false) {

				$data['wh_lokasi']      = $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();
			} else {

				$data['wh_lokasi']      = $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack WHERE pic='$current_user' GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();
			}


			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Warehouse Part";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 	= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 	= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/wh_part/index', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}


	// WAREHOUSE PART #################################

	public function transfer_data()
	{
		// Ambil role untuk validasi
		$role_names         = ['Supervisor Warehouse', 'Superadmin'];
		$role_placeholders  = "'" . implode("','", $role_names) . "'";
		$supervisor_level   = null;
		$superadmin_level   = null;
		$current_level      = (int)$this->session->userdata('user_level');

		$sql = "SELECT roles_name, idroles FROM roles WHERE roles_name IN ($role_placeholders)";
		$query = $this->db->query($sql);
		$roles = $query->result();

		foreach ($roles as $role) {
			if ($role->roles_name === 'Supervisor Warehouse') {
				$supervisor_level = (int)$role->idroles;
			} elseif ($role->roles_name === 'Superadmin') {
				$superadmin_level = (int)$role->idroles;
			}
		}

		// Ambil input
		$partid             = $this->input->post('partid_tf');
		$rack_awal          = $this->input->post('id_rack_awal_tf');
		$kolom_awal         = $this->input->post('id_kolom_awal_tf');
		$wh_lokasi_awal     = $this->input->post('wh_lokasi_awal_tf');


		$rack_tujuan        = $this->input->post('id_rack_tujuan_tf');
		$kolom_tujuan       = $this->input->post('id_kolom_tujuan_tf');
		$wh_lokasi_tujuan   = $this->input->post('wh_lokasi_tujuan_tf');
		$qty_transfer       = (float)$this->input->post('qty_tf');


		$racks = $this->BJGMAS01
			->select('id_rack, nama_rack')
			->where_in('id_rack', [$rack_awal, $rack_tujuan])
			->get('ms_rack')
			->result();

		// Mapping hasil ke variabel
		$nama_rack_awal = '';
		$nama_rack_tujuan = '';

		foreach ($racks as $r) {
			if ($r->id_rack == $rack_awal) {
				$nama_rack_awal = $r->nama_rack;
			}
			if ($r->id_rack == $rack_tujuan) {
				$nama_rack_tujuan = $r->nama_rack;
			}
		}

		// Validasi PIC lokasi awal
		$this->BJGMAS01->select('pic', false);
		$this->BJGMAS01->where('wh_lokasi', $wh_lokasi_awal);
		$this->BJGMAS01->where('id_rack', $rack_awal);
		$query = $this->BJGMAS01->get('ms_rack');

		if ($query->num_rows() === 0) {
			echo json_encode(['status' => 'error', 'message' => 'Lokasi tidak ditemukan.']);
			return;
		}

		$row = $query->row_array();
		$pic_lokasi = $row['pic'];
		$current_user = $this->session->userdata('user_name');

		if ($current_level != $supervisor_level && $current_level != $superadmin_level) {
			if (strtolower($pic_lokasi) !== strtolower($current_user)) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Anda bukan PIC untuk lokasi ini.'
				]);
				return;
			}
		}

		// Cek stok di lokasi awal (pakai qty_remaining)
		$stok_query = $this->BJGMAS01
			->select('SUM(qty_remaining) as total_stok')
			->where('partid', $partid)
			->where('id_rack', $rack_awal)
			->where('id_kolom', $kolom_awal)
			->where('wh_lokasi', $wh_lokasi_awal)
			->where('type_trans', 'IN')
			->where('qty_remaining >', 0)
			->get('tbl_trans_rack')
			->row();

		$total_stok = (float)($stok_query->total_stok ?? 0);

		if ($total_stok < $qty_transfer) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Stok tidak mencukupi untuk transfer.'
			]);
			return;
		}

		// Ambil partname dan units dari transaksi terakhir
		$last_trans = $this->BJGMAS01
			->select('partname, units')
			->where('partid', $partid)
			->order_by('created_date', 'DESC')
			->limit(1)
			->get('tbl_trans_rack')
			->row();

		if (!$last_trans) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Data part tidak ditemukan.'
			]);
			return;
		}

		$partname = $last_trans->partname;
		$units = $last_trans->units;
		$created_by = $this->session->userdata('user_code');
		$current_time = date('Y-m-d H:i:s');
		$transfer_group_id = 'TF-' . date('YmdHis') . '-' . uniqid();

		// --- MULAI TRANSAKSI ---
		$this->BJGMAS01->trans_start();

		// 1. Buat OUT dari lokasi awal
		$data_out = [
			'id_rack'           => $rack_awal,
			'id_kolom'          => $kolom_awal,
			'wh_lokasi'         => $wh_lokasi_awal,
			'partid'            => $partid,
			'partname'          => $partname,
			'qty'               => -$qty_transfer,
			'noted'             => "Transfer ke $nama_rack_tujuan ($wh_lokasi_tujuan)",
			// 'fifo'              => $pilih_fifo,
			// 'tgl_fifo'          => $tgl_fifo,
			'transfer_group_id' => $transfer_group_id,
			'tgl_status_part'   => $current_time,
			'units'             => $units,
			'type_trans'        => 'OUT',
			'created_by'        => $created_by,
			'created_date'      => $current_time,
			'qty_remaining'     => NULL
		];



		$this->BJGMAS01->insert('tbl_trans_rack', $data_out, true);
		$out_id =  $this->BJGMAS01->insert_id();

		if (!$out_id) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Gagal simpan OUT.']);
			return;
		}

		// 2. Proses FIFO khusus transfer
		$tgl_fifo_sumber = $this->proses_fifo_transfer($out_id, $partid, $rack_awal, $kolom_awal, $qty_transfer, $transfer_group_id);

		if (!$tgl_fifo_sumber) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Gagal proses FIFO untuk transfer.']);
			return;
		}


		// 🔁 2.5 Ambil tgl_fifo dari IN sumber (FIFO pertama)
		$source_fifo = $this->BJGMAS01
			->select('tr.tgl_fifo')
			->from('tbl_detail_fifo df')
			->join('tbl_trans_rack tr', 'tr.id_trans_rack = df.in_id')
			->where('df.out_id', $out_id)
			->order_by('tr.tgl_fifo, tr.created_date') // ambil yang paling awal
			->limit(1)
			->get()
			->row();

		$tgl_fifo_sumber = !empty($source_fifo) ? $source_fifo->tgl_fifo : $current_time;
		// var_dump($tgl_fifo_sumber);
		// exit;
		// Konversi ke nama bulan Indonesia
		$bulan = date('m', strtotime($tgl_fifo_sumber));
		$nama_bulan = [
			'01' => 'januari',
			'02' => 'februari',
			'03' => 'maret',
			'04' => 'april',
			'05' => 'mei',
			'06' => 'juni',
			'07' => 'juli',
			'08' => 'agustus',
			'09' => 'september',
			'10' => 'oktober',
			'11' => 'november',
			'12' => 'desember'
		];
		$fifo_sumber = $nama_bulan[$bulan];




		// 3. Buat IN ke lokasi tujuan
		$data_in = [
			'id_rack'           => $rack_tujuan,
			'id_kolom'          => $kolom_tujuan,
			'wh_lokasi'         => $wh_lokasi_tujuan,
			'partid'            => $partid,
			'partname'          => $partname,
			'qty'               => $qty_transfer,
			'noted'             => "Transfer dari $nama_rack_awal ($wh_lokasi_awal)",
			'fifo'              => $fifo_sumber,
			'tgl_fifo'          => $tgl_fifo_sumber,
			'transfer_group_id' => $transfer_group_id,
			'tgl_status_part'   => $current_time,
			'units'             => $units,
			'type_trans'        => 'IN',
			'created_by'        => $created_by,
			'created_date'      => $current_time,
			'qty_remaining'     => $qty_transfer  // stok baru, full tersedia
		];

		// var_dump($data_in);
		// exit;

		$in_id = $this->BJGMAS01->insert('tbl_trans_rack', $data_in, true);

		if (!$in_id) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Gagal simpan IN.']);
			return;
		}


		// Selesai
		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			echo json_encode(['status' => 'error', 'message' => 'Transaksi transfer gagal.']);
		} else {
			echo json_encode(['status' => 'success', 'message' => 'Transfer berhasil.']);
		}
	}

	/**
	 * Proses FIFO khusus untuk transfer
	 * - Update tgl_fifo dan fifo di OUT
	 * - Simpan transfer_group_id di tbl_detail_fifo
	 * - Kembalikan tgl_fifo_sumber untuk IN tujuan
	 */
	private function proses_fifo_transfer($out_id, $partid, $id_rack, $id_kolom, $qty_out, $transfer_group_id = null)
	{
		$remaining = (float)$qty_out;
		$tgl_fifo_sumber = null; // tgl_fifo dari IN pertama

		$this->BJGMAS01->trans_start();

		// Ambil semua IN yang tersedia (FIFO)
		$in_records = $this->BJGMAS01->query("
        SELECT tr.id_trans_rack, tr.qty_remaining, tr.tgl_fifo
        FROM tbl_trans_rack tr
        WHERE tr.partid = ?
          AND tr.id_rack = ?
          AND tr.id_kolom = ?
          AND tr.type_trans = 'IN'
          AND tr.qty_remaining > 0
        ORDER BY tr.tgl_fifo ASC, tr.created_date ASC
    	", [$partid, $id_rack, $id_kolom])->result_array();

		foreach ($in_records as $in) {
			if ($remaining <= 0) break;

			$available = (float)$in['qty_remaining'];
			$used = min($available, $remaining);

			// Simpan ke detail FIFO + transfer_group_id
			$this->BJGMAS01->insert('tbl_detail_fifo', [
				'out_id'             => $out_id,
				'in_id'              => $in['id_trans_rack'],
				'qty_used'           => $used,
				'tgl_fifo_in'        => $in['tgl_fifo'],
				'transfer_group_id'  => $transfer_group_id, // ⬅️ Tambahkan
				'created_at'         => date('Y-m-d H:i:s')
			]);

			// Catat tgl_fifo dari IN pertama
			if ($tgl_fifo_sumber === null) {
				$tgl_fifo_sumber = $in['tgl_fifo'];
			}

			// Kurangi qty_remaining di IN
			$new_remaining = $available - $used;
			$this->BJGMAS01->update('tbl_trans_rack', [
				'qty_remaining' => $new_remaining
			], [
				'id_trans_rack' => $in['id_trans_rack']
			]);

			$remaining -= $used;
		}

		// Jika stok tidak cukup
		if ($remaining > 0) {
			$this->BJGMAS01->trans_rollback();
			log_message('error', "Stok tidak cukup untuk transfer OUT ID: $out_id");
			return false;
		}

		// --- UPDATE tgl_fifo dan fifo di transaksi OUT ---
		if ($tgl_fifo_sumber !== null) {
			$bulan_angka = date('m', strtotime($tgl_fifo_sumber));

			$nama_bulan = [
				'01' => 'januari',
				'02' => 'februari',
				'03' => 'maret',
				'04' => 'april',
				'05' => 'mei',
				'06' => 'juni',
				'07' => 'juli',
				'08' => 'agustus',
				'09' => 'september',
				'10' => 'oktober',
				'11' => 'november',
				'12' => 'desember'
			];

			$fifo = $nama_bulan[$bulan_angka];

			// Update OUT dengan tgl_fifo dan fifo dari sumber
			$this->BJGMAS01->update('tbl_trans_rack', [
				'tgl_fifo' => $tgl_fifo_sumber,
				'fifo'     => $fifo
			], [
				'id_trans_rack' => $out_id
			]);
		}

		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			log_message('error', "Transaksi FIFO transfer gagal untuk OUT ID: $out_id");
			return false;
		}

		// ✅ Kembalikan tgl_fifo_sumber untuk dipakai di IN tujuan
		return $tgl_fifo_sumber;
	}

	public function get_list_transaksi()
	{


		$sql = "
				SELECT 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					SUM(a.qty) AS total_qty,
					 MAX(a.tgl_status_part) AS latest_created_date,
					a.units
				FROM tbl_trans_rack a
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom
				WHERE a.wh_lokasi = ?
				GROUP BY 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					a.units
				ORDER BY 
					LEFT(b.nama_rack, PATINDEX('%[0-9]%', b.nama_rack + '0') - 1), 
						TRY_CAST(SUBSTRING(b.nama_rack, PATINDEX('%[0-9]%', b.nama_rack + '0'), 10) AS INT),
					LEFT(c.nama_kolom, PATINDEX('%[0-9]%', c.nama_kolom + '0') - 1), 
						TRY_CAST(SUBSTRING(c.nama_kolom, PATINDEX('%[0-9]%', c.nama_kolom + '0'), 10) AS INT)
			";
		$wh_lokasi 	= $this->input->post('wh_lokasi');
		$query 		= $this->BJGMAS01->query($sql, [$wh_lokasi]);
		$result 	= $query->result();

		// Jika untuk DataTables, bisa tambahkan nomor urut, dll
		$data 		= [];
		$no 		= 1;
		foreach ($result as $row) {
			$lihat 	=  $row->id_rack . '|' . $row->id_kolom . '|' . $row->partid . '|' . $row->wh_lokasi;
			$part 	= "'" . $row->id_rack . '|' . $row->id_kolom . '|' . $row->partid . '|' . $row->wh_lokasi . "'";

			$data[] = [
				'no'         	=> $no++,
				'id_rack'    	=> $row->id_rack,
				'nama_rack'  	=> $row->nama_rack,
				'id_kolom'   	=> $row->id_kolom,
				'nama_kolom' 	=> $row->nama_kolom,
				'wh_lokasi'  	=> $row->wh_lokasi,
				'partid'     	=> $row->partid,
				'partname'   	=> $row->partname,
				'qty'        	=> number_format($row->total_qty, 2, ",", "."),
				'units'      	=> $row->units,
				'status_part' 	=> $this->getStatusWarnaPart($row->latest_created_date),
				'lihat' 	 	=> '<a href="' . base_url() . 'warehouse_part/warehouse_mapping/' . base64_encode($lihat) . '" ><button class="btn btn-danger btn-block btn-sm">Lihat</button></a>',
				'pindah' 		=>  '<button type="button" class="btn btn-danger btn-block text-white btn-sm" onclick="pindah(' . $part . ')">Pindah</button>',
				'transfer' 		=>  '<button type="button" class="btn btn-success btn-block text-white btn-sm" onclick="transfer(' . $part . ')">Transfer</button>',
				'hapus' 		=>  '<button type="button" class="btn btn-danger btn-block text-white btn-sm" onclick="hapus(' . $part . ')">Hapus</button>'

			];
		}


		echo json_encode(['data' => $data]);
	}

	public function getStatusWarnaPart($tanggal)
	{
		// Jika tidak ada tanggal, kembalikan 'red' sebagai default
		if (!$tanggal) {
			return '<div style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;"></div>'; // 3 bulan atau lebih
		}

		try {
			$tanggalPart = new DateTime($tanggal);
		} catch (Exception $e) {
			// Jika format tanggal tidak valid, kembalikan 'red'
			return '<div style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;"></div>'; // 3 bulan atau lebih
		}

		$tanggalSekarang = new DateTime();

		// Jika tanggal part lebih baru dari sekarang (masa depan), dianggap tidak ada selisih
		if ($tanggalPart > $tanggalSekarang) {
			return '<div style="background-color:#28a745; width:54px; height:24px; border-radius:4px;"></div>';
		}

		// Hitung selisih antara tanggal sekarang dan tanggal part
		$selisih 	= $tanggalSekarang->diff($tanggalPart);
		$totalBulan = ($selisih->y * 12) + $selisih->m;

		// Tentukan status berdasarkan selisih bulan
		if ($totalBulan < 1) {
			return '<div style="background-color:#28a745; width:54px; height:24px; border-radius:4px;"></div>'; // kurang dari 1 bulan
		} elseif ($totalBulan < 2) {
			return '<div style="background-color:#28a745; width:54px; height:24px; border-radius:4px;"></div>'; // antara 1 - <2 bulan
		} elseif ($totalBulan < 3) {
			return '<div style="background-color:#ffc107; width:54px; height:24px; border-radius:4px;"></div>'; // antara 2 - <3 bulan
		} else {
			return '<div style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;"></div>'; // 3 bulan atau lebih
		}
	}

	public function hapus_data()
	{
		// Ambil parameter dari POST
		$param = $this->input->post('id');

		if (!$param) {
			echo 'Parameter tidak ditemukan.';
			return;
		}

		// Pisahkan parameter dengan |
		$parts = explode('|', $param);

		if (count($parts) !== 4) {
			echo 'Format parameter salah.';
			return;
		}

		list($id_rack, $id_kolom, $partid, $wh_lokasi) = $parts;

		// Ambil PIC dari lokasi untuk SQL Server
		$this->BJGMAS01->select('TOP 1 pic', false); // false = agar tidak di-escape
		$this->BJGMAS01->where('wh_lokasi', $wh_lokasi);
		$this->BJGMAS01->where('id_rack', $id_rack);
		$query = $this->BJGMAS01->get('ms_rack');


		if ($query->num_rows() === 0) {
			echo json_encode(['status' => 'error', 'message' => 'Lokasi tidak ditemukan.']);
			return;
		}

		$row 			= $query->row_array();
		$pic_lokasi 	= $row['pic'];

		$sql 			= "SELECT idroles FROM roles WHERE roles_name='Supervisor Warehouse'";
		$query 			= $this->db->query($sql);
		$spv_level 		= $query->row();

		$current_level 	= $this->session->userdata('user_level');
		$current_user 	= $this->session->userdata('user_name');

		// Bandingkan sepervisor wh bukan
		if ((int)$current_level !== (int)$spv_level->idroles) {
			// Bandingkan PIC dengan session user
			if (strtolower($pic_lokasi) !== strtolower($current_user)) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Anda bukan PIC untuk lokasi ini. Tidak dapat menghapus data.'
				]);
				return;
			}
		}

		// Ambil data dari trans_rack
		$this->BJGMAS01->where([
			'id_rack'    => $id_rack,
			'id_kolom'   => $id_kolom,
			'partid'     => $partid,
			'wh_lokasi'  => $wh_lokasi

		]);
		$query = $this->BJGMAS01->get('tbl_trans_rack');

		if ($query->num_rows() === 0) {
			echo 'Data tidak ditemukan di database.';
			return;
		}

		$rows = $query->result_array();


		// Kumpulkan semua id_trans_rack
		$id_list = array_column($rows, 'id_trans_rack');

		// Cek apakah id tersebut ada sebagai id_in
		$this->BJGMAS01->where_in('in_id', $id_list);
		$check_in = $this->BJGMAS01->get('tbl_detail_fifo');

		// Cek apakah id tersebut ada sebagai id_out
		$this->BJGMAS01->where_in('out_id', $id_list);
		$check_out = $this->BJGMAS01->get('tbl_detail_fifo');

		if ($check_in->num_rows() > 0 || $check_out->num_rows() > 0) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Maaf, tidak bisa dihapus karena ada transaksi yang saling berhubungan.'
			]);
			return;
		}

		$user = $this->session->userdata('user_code');
		$now  = date('Y-m-d H:i:s');

		// Mulai transaksi
		$this->BJGMAS01->trans_start();

		foreach ($rows as $row) {
			$row['created_by']   = $user;
			$row['created_date'] = $now;


			$this->BJGMAS01->insert('tbl_trans_rack_hapus', $row);
		}

		// Hapus dari trans_rack jika insert selesai
		$this->BJGMAS01->where([
			'id_rack'    => $id_rack,
			'id_kolom'   => $id_kolom,
			'partid'     => $partid,
			'wh_lokasi'  => $wh_lokasi
		]);
		$this->BJGMAS01->delete('tbl_trans_rack');

		// Selesaikan transaksi
		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Terjadi kesalahan saat menghapus data. Transaksi dibatalkan.'
			]);
		} else {
			echo json_encode([
				'status' => 'success',
				'message' => 'Data berhasil dihapus.'
			]);
		}
	}

	public function pindah_data()
	{
		$partid           = $this->input->post('partid');
		$id_rack_awal     = $this->input->post('id_rack_awal');
		$id_kolom_awal    = $this->input->post('id_kolom_awal');
		$wh_lokasi_awal   = $this->input->post('wh_lokasi_awal');
		$id_rack_tujuan   = $this->input->post('id_rack_tujuan');
		$id_kolom_tujuan  = $this->input->post('id_kolom_tujuan');
		$wh_lokasi_tujuan = $this->input->post('wh_lokasi_tujuan');

		$this->BJGMAS01->trans_begin();

		// Cari baris yang akan diupdate
		$this->BJGMAS01->where([
			'partid'        => $partid,
			'id_rack'   	=> $id_rack_awal,
			'id_kolom'  	=> $id_kolom_awal,
			'wh_lokasi' 	=> $wh_lokasi_awal
		]);

		$query = $this->BJGMAS01->get('tbl_trans_rack');



		if ($query->num_rows() == 0) {
			echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
			return;
		}

		// Update data ke lokasi tujuan
		$this->BJGMAS01->where([
			'partid'        => $partid,
			'id_rack'   	=> $id_rack_awal,
			'id_kolom'  	=> $id_kolom_awal,
			'wh_lokasi' 	=> $wh_lokasi_awal
		]);

		$update = $this->BJGMAS01->update('tbl_trans_rack', [
			'id_rack'   	=> $id_rack_tujuan,
			'id_kolom'  	=> $id_kolom_tujuan,
			'wh_lokasi' 	=> $wh_lokasi_tujuan,
			'updated_date'  => date('Y-m-d H:i:s'), // opsional: timestamp update
			'updated_by'    => $this->session->userdata('user_code') // jika pakai login
		]);

		if (!$update || $this->BJGMAS01->trans_status() === FALSE) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan part.']);
		} else {
			$this->BJGMAS01->trans_commit();
			echo json_encode(['status' => 'success', 'message' => 'Part berhasil dipindahkan.']);
		}
	}


	public function get_rack_kolom()
	{

		$racks  = $this->BJGMAS01->get('ms_rack')->result();

		echo json_encode([
			'rack' => $racks,
		]);
	}

	// WAREHOUSE MAPPING #################################

	public function warehouse_mapping($data_params)
	{
		$data_array = explode("|", base64_decode($data_params));

		$id_rack 	= $data_array[0];
		$id_kolom 	= $data_array[1];
		$partid 	= $data_array[2];

		$sql = "
				SELECT 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					a.units
					
				FROM tbl_trans_rack a
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom
				WHERE 
					partid='$partid'
					AND a.id_rack='$id_rack'
					AND a.id_kolom='$id_kolom'
				order by a.created_date asc ";

		$query = $this->BJGMAS01->query($sql);


		$data['group_halaman'] 		= "Warehouse";
		$data['nama_halaman'] 		= "Warehouse Mapping";
		$data['icon_halaman'] 		= "icon-airplay";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$data['wh'] 				= $query->row();

		$this->load->view('adminx/warehouse/wh_mapping/index', $data, FALSE);
	}

	public function delete_transaksi_mapping()
	{
		$id_trans_rack = $this->input->post('id');

		if (empty($id_trans_rack)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'ID tidak ditemukan.'
			]);
			return;
		}

		$this->BJGMAS01->trans_start();

		// Ambil data utama
		$this->BJGMAS01->select('type_trans, transfer_group_id, relasi_sisa_mpr');
		$this->BJGMAS01->where('id_trans_rack', $id_trans_rack);
		$query = $this->BJGMAS01->get('tbl_trans_rack');

		if ($query->num_rows() === 0) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode([
				'status' => 'error',
				'message' => 'Data tidak ditemukan di database.'
			]);
			return;
		}

		$trans = $query->row_array();
		$transfer_group_id = $trans['transfer_group_id'];

		try {
			$ids_to_delete = [$id_trans_rack];

			// Jika ini transfer, cari pasangannya
			if (!empty($transfer_group_id)) {
				$this->BJGMAS01->select('id_trans_rack, relasi_sisa_mpr');
				$this->BJGMAS01->where('transfer_group_id', $transfer_group_id);
				$this->BJGMAS01->where('id_trans_rack !=', $id_trans_rack);
				$pair_query = $this->BJGMAS01->get('tbl_trans_rack');

				foreach ($pair_query->result_array() as $pair) {
					$ids_to_delete[] = $pair['id_trans_rack'];

					// 🔎 Jika pasangannya ada relasi_sisa_mpr, update tbl_sisa_mpr
					if (!empty($pair['relasi_sisa_mpr'])) {
						$this->BJGMAS01->where('relasi_sisa_mpr', $pair['relasi_sisa_mpr']);
						$this->BJGMAS01->update('tbl_sisa_mpr', [
							'is_deleted'    => 1,
							'updated_by'   => $this->session->userdata('user_code'),
							'updated_date' => date('Y-m-d H:i:s')
						]);
					}
				}
			}

			// Proses setiap ID
			foreach ($ids_to_delete as $id) {
				$this->BJGMAS01->where('id_trans_rack', $id);
				$data = $this->BJGMAS01->get('tbl_trans_rack')->row_array();
				if (!$data) continue;

				$type_trans = $data['type_trans'];

				// --- FIFO ---
				if ($type_trans === 'OUT') {
					$detail_count = $this->BJGMAS01
						->where('out_id', $id)
						->count_all_results('tbl_detail_fifo');

					if ($detail_count > 0) {
						$this->reverse_fifo_out($id);
					}
					$this->BJGMAS01->where('out_id', $id);
					$this->BJGMAS01->delete('tbl_detail_fifo');
				} elseif ($type_trans === 'IN') {
					$used_count = $this->BJGMAS01
						->where('in_id', $id)
						->count_all_results('tbl_detail_fifo');

					if ($used_count > 0) {
						$this->BJGMAS01->trans_rollback();
						echo json_encode([
							'status' => 'error',
							'message' => "Tidak bisa hapus IN (ID: $id) yang sudah digunakan di FIFO."
						]);
						return;
					}
				}

				// 🔎 Kalau ada relasi_sisa_mpr, update tbl_sisa_mpr jadi is_deleted=1
				if (!empty($data['relasi_sisa_mpr'])) {
					$this->BJGMAS01->where('relasi_sisa_mpr', $data['relasi_sisa_mpr']);
					$this->BJGMAS01->update('tbl_sisa_mpr', [
						'is_deleted'    => 1,
						'updated_by'   => $this->session->userdata('user_code'),
						'updated_date' => date('Y-m-d H:i:s')
					]);
				}

				// Backup sebelum hapus
				$data['created_by'] = $this->session->userdata('user_code');
				$data['created_date'] = date('Y-m-d H:i:s');
				$this->BJGMAS01->insert('tbl_trans_rack_hapus', $data);

				// Hapus dari utama
				$this->BJGMAS01->where('id_trans_rack', $id);
				$this->BJGMAS01->delete('tbl_trans_rack');
			}

			$this->BJGMAS01->trans_complete();

			if ($this->BJGMAS01->trans_status() === FALSE) {
				throw new Exception("Transaksi gagal.");
			}

			echo json_encode([
				'status' => 'success',
				'message' => 'Transaksi berhasil dibatalkan, termasuk update Sisa MPR.'
			]);
		} catch (Exception $e) {
			$this->BJGMAS01->trans_rollback();
			log_message('error', 'Gagal hapus transfer: ' . $e->getMessage());
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal membatalkan transfer.'
			]);
		}
	}

	private function reverse_fifo_out($out_id)
	{
		$details = $this->BJGMAS01
			->select('in_id, qty_used')
			->where('out_id', $out_id)
			->get('tbl_detail_fifo')
			->result_array();

		foreach ($details as $detail) {
			// Kembalikan qty_remaining di IN
			$this->BJGMAS01->query("
            UPDATE tbl_trans_rack 
            SET qty_remaining = qty_remaining + ?
            WHERE id_trans_rack = ?
        ", [$detail['qty_used'], $detail['in_id']]);
		}
	}

	public function warehouse_mapping_detail()
	{
		$partid 	= $this->input->post('partid');
		$id_rack 	= $this->input->post('id_rack');
		$id_kolom 	= $this->input->post('id_kolom');
		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');


		$sql = "
			WITH StokAwal AS (
				SELECT 
					SUM(
					CASE 
						WHEN a.type_trans = 'IN' THEN a.qty 
						WHEN a.type_trans = 'OUT' THEN a.qty 
						ELSE 0 
					END
					) AS total_stok_awal
				FROM 
					tbl_trans_rack a
				WHERE 
					partid='$partid'
					AND a.id_rack='$id_rack'
					AND a.id_kolom='$id_kolom'
					AND a.created_date < '$start_date'
				)

				SELECT 	
				a.id_trans_rack,

				CONVERT(VARCHAR, a.created_date, 120) AS tanggal,
				CASE 
					WHEN a.type_trans = 'IN' THEN a.qty 
					ELSE NULL 
				END AS qty_in,
				CASE 
					WHEN a.type_trans = 'OUT' THEN a.qty 
					ELSE NULL 
				END AS qty_out,

				
    			(SELECT total_stok_awal FROM StokAwal) + 
				SUM(
					CASE 
					WHEN a.type_trans = 'IN' THEN a.qty 
					WHEN a.type_trans = 'OUT' THEN a.qty 
					ELSE 0 
					END
				) OVER (
					PARTITION BY a.partid, a.id_rack, a.id_kolom 
					ORDER BY a.created_date ASC
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) AS balance,
				a.tgl_fifo,
				a.noted,
				a.fifo

				FROM 
				tbl_trans_rack a 
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack 
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom 
				
				WHERE 
				partid='$partid'
				AND a.id_rack='$id_rack'
				AND a.id_kolom='$id_kolom'
				AND CAST(a.created_date AS DATE) BETWEEN '$start_date' AND '$end_date' 

				UNION ALL

				SELECT 
    			NULL AS id_trans_rack,
				'$start_date 00:00:00' AS tanggal,
				(SELECT total_stok_awal FROM StokAwal) AS qty_in,
				NULL AS qty_out,
				(SELECT total_stok_awal FROM StokAwal) AS balance, 
				NULL AS tgl_fifo,
				'Stok Awal' AS noted,
				NULL AS fifo

				ORDER BY 
				tanggal ASC";



		$query 		  	= $this->BJGMAS01->query($sql);
		$data       	= $query->result();

		// foreach ($data as $key => $value) {
		// 	$data[$key]->tanggal 	= date('d-m-Y', strtotime($value->tanggal));
		// 	$data[$key]->fifo 		= $this->get_fifo($value->fifo, date('d-m-Y', strtotime($value->tgl_fifo)));
		// }

		// Ambil role level untuk Supervisor dan Superadmin
		$role_names = ['Supervisor Warehouse', 'Superadmin'];
		$role_placeholders = "'" . implode("','", $role_names) . "'";

		$sql = "SELECT roles_name, idroles FROM roles WHERE roles_name IN ($role_placeholders)";
		$query = $this->db->query($sql);
		$roles = $query->result();

		$supervisor_level = null;
		$superadmin_level = null;

		foreach ($roles as $role) {
			if ($role->roles_name === 'Supervisor Warehouse') {
				$supervisor_level = (int)$role->idroles;
			} elseif ($role->roles_name === 'Superadmin') {
				$superadmin_level = (int)$role->idroles;
			}
		}

		// Ambil level user dari session
		$current_level = (int) $this->session->userdata('user_level');

		// Iterasi dan proses data
		foreach ($data as $key => $value) {
			// Konversi tanggal ke format dd-mm-yyyy
			$parts = explode(' ', $value->tanggal);


			$formatted_date = date(
				'd-m-Y',
				strtotime($parts[0])
			);

			// Format tombol delete jika id_trans_rack tersedia
			$btn_delete = '';
			if (!empty($value->id_trans_rack)) {
				$btn_delete = '<button class="btn btn-danger btn-sm delete-trans" data-id="' . htmlspecialchars($value->id_trans_rack, ENT_QUOTES) . '" title="Hapus Data">
									<i class="fa-solid fa-trash"></i>
								</button>';
			}

			// Hanya tampilkan tombol hapus untuk Supervisor atau Superadmin
			if ($current_level === $supervisor_level || $current_level === $superadmin_level) {
				$data[$key]->tanggal = $formatted_date . ' ' . $btn_delete;
			} else {
				$data[$key]->tanggal = $formatted_date;
			}

			// Proses FIFO jika data tersedia
			$tgl_fifo = !empty($value->tgl_fifo) ? date('d-m-Y', strtotime($value->tgl_fifo)) : null;
			$data[$key]->fifo = "
								<a href='#' 
								class='show-fifo-btn' 
								title='FIFO Part' 
								data-id='{$value->id_trans_rack}'>
									{$this->get_fifo($value->fifo,$tgl_fifo)}
								</a>";
		}



		$result = array(
			"recordsTotal"     	=> $query->num_rows(),
			"recordsFiltered" 	=> $query->num_rows(),
			"data"             	=> $data
		);

		echo json_encode($result);
	}

	public function get_fifo_detail()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo "<p class='text-danger'>ID tidak valid.</p>";
			return;
		}

		$trans = $this->BJGMAS01
			->select('id_trans_rack, partid, partname, type_trans, qty, tgl_fifo, fifo')
			->where('id_trans_rack', $id)
			->get('tbl_trans_rack')
			->row();

		if (!$trans) {
			echo "<p class='text-danger'>Data tidak ditemukan.</p>";
			return;
		}

		$output = '';

		if ($trans->type_trans === 'OUT') {
			$details = $this->BJGMAS01
				->select('df.qty_used, tr.id_trans_rack,tr.created_date, tr.tgl_fifo, tr.qty, tr.qty_remaining')
				->from('tbl_detail_fifo df')
				->join('tbl_trans_rack tr', 'tr.id_trans_rack = df.in_id')
				->where('df.out_id', $id)
				->get()
				->result();

			$output .= "<h6><strong>{$trans->partid}</strong> - {$trans->partname} (OUT)</h6>";
			$output .= "<p><strong>Qty:</strong> " . abs($trans->qty) . "</p>";
			$output .= "<h6>Sumber Stok (IN):</h6>";

			if ($details) {
				$output .= '<table class="table table-bordered table-sm">';
				$output .= '<thead><tr><th>Tanggal Transaksi</th><th>Tgl FIFO</th><th>Qty Digunakan</th><th>Sisa Stok IN</th></tr></thead><tbody>';
				foreach ($details as $d) {
					$tanggal = (new DateTime($d->created_date))->format('Y-m-d H:i:s');

					$output .= "<tr>
                    <td>{$tanggal}</td>
                    <td>" . date('d-m-Y', strtotime($d->tgl_fifo)) . "</td>
                    <td>{$d->qty_used}</td>
                    <td>{$d->qty_remaining}</td>
                </tr>";
				}
				$output .= '</tbody></table>';
			} else {
				$output .= '<p><em>Belum ada alokasi FIFO.</em></p>';
			}
		} elseif ($trans->type_trans === 'IN') {
			$details = $this->BJGMAS01
				->select('df.qty_used, tr.id_trans_rack,tr.created_date, tr.tgl_fifo, tr.noted,tr.qty')
				->from('tbl_detail_fifo df')
				->join('tbl_trans_rack tr', 'tr.id_trans_rack = df.out_id')
				->where('df.in_id', $id)
				->get()
				->result();

			$output .= "<h6><strong>{$trans->partid}</strong> - {$trans->partname} (IN)</h6>";
			$output .= "<p><strong>Qty:</strong> " . $trans->qty . "</p>";
			$output .= "<h6>Digunakan di (OUT):</h6>";
			$output .= "<div class='table-responsive'>";

			if ($details) {
				$output .= '<table class="table table-bordered table-sm">';
				$output .= '<thead><tr><th>Tanggal Transaksi</th><th>Tgl FIFO</th><th>Qty Digunakan</th><th>Noted</th></tr></thead><tbody>';
				foreach ($details as $d) {
					$tanggal = (new DateTime($d->created_date))->format('Y-m-d H:i:s');

					$output .= "<tr>
                    <td>{$tanggal}</td>
                    <td>" . date('d-m-Y', strtotime($d->tgl_fifo)) . "</td>
                    <td>{$d->qty_used}</td>
                    <td>{$d->noted}</td>
                </tr>";
				}
				$output .= '</tbody></table>';
			} else {
				$output .= '<p><em>Stok belum digunakan.</em></p>';
			}
			$output .= "</div>";
		}

		echo $output;
	}

	function get_ms_fifo($bulan = null)
	{
		if ($bulan) {
			$this->BJGMAS01->where('bulan', $bulan);
		}
		$fifoData = $this->BJGMAS01->get('ms_fifo')->result();

		echo json_encode($fifoData);;
	}

	// Fungsi untuk membuat opsi berdasarkan bulan, bentuk, dan warna
	private function get_fifo($bulan, $tgl_fifo)
	{
		// Ambil data dari tabel ms_fifo berdasarkan bulan
		$query 	= $this->BJGMAS01->get_where('ms_fifo', ['bulan' => $bulan]);
		$row 	= $query->row();

		// Jika tidak ditemukan data, kembalikan bulan saja
		if (!$row) {
			return '<span>' . $bulan . '</span>';
		}

		$bentuk = $row->bentuk;
		$warna 	= $row->warna;


		$iconMap = [
			'kotak' 	=> 'fa-solid fa-square',
			'segitiga' 	=> 'fa-solid fa-play fa-rotate-270'
		];

		// Tentukan kelas ikon berdasarkan bentuk
		$iconClass = isset($iconMap[$bentuk]) ? $iconMap[$bentuk] : 'fa-circle';

		// Cek jika warna adalah putih, beri background biru
		// if ($warna === 'white') {
		// 	$warna = 'white'; // Warna ikon tetap putih
		// 	return '<span><i class="' . $iconClass . '" style="color:' . $warna . '; background-color: blue; padding: 2px 5px; border-radius: 50%; margin-right: 8px;"></i> </span>';
		// }

		// Jika bukan warna putih, tampilkan warna sesuai parameter
		return '<span><i class="' . $iconClass . '" style="color:' . $warna . '; margin-right: 8px;"></i>' . $tgl_fifo . '</span>';
	}

	// WAREHOUSE INSERT #################################


	public function page_insert_data()
	{

		$data['group_halaman']  = "Warehouse";
		$data['nama_halaman']   = "Input data part";
		$data['icon_halaman']   = "icon-airplay";
		$data['perusahaan']     = $this->perusahaan->get_details();

		$current_user 			= $this->session->userdata('user_name');
		$user_fg 			   	= 'slamet';
		if (strtolower($current_user) == strtolower($user_fg)) {
			$data['rack']         	= $this->BJGMAS01->query("SELECT id_rack, nama_rack FROM ms_rack WHERE pic='$current_user'")->result();
		} else {
			$data['rack']         	= $this->BJGMAS01->query("SELECT id_rack, nama_rack FROM ms_rack ")->result();
		}


		$this->load->view('adminx/warehouse/insert_data/index', $data, FALSE);
	}

	public function cari_partname()
	{
		$SearchTerm = $this->input->get('term');
		$TypeInvID  = array('RM01', 'MP01', 'CM01');

		// $this->BJGMAS01->select("(PartID + '|' + PartName) as id, PartName as text, UnitID_PO as unit");
		$this->BJGMAS01->select("(PartID + '|' + PartName) as id, PartName as text, UnitID_Stock as unit");
		$this->BJGMAS01->where_in('TypeInventoryID', $TypeInvID);
		$this->BJGMAS01->like('PartName', $SearchTerm);
		$this->BJGMAS01->or_like('PartID', $SearchTerm);

		$Data = $this->BJGMAS01->get('Ms_Part');

		echo json_encode($Data->result());
	}

	public function cari_job()
	{

		$SearchTerm = $this->input->get('term');

		// Ambil bulan & tahun sekarang
		$bulan = date('m');
		$tahun = date('Y');
		$table = 'Trans_MPRHD' . $tahun . $bulan;

		// Cek apakah tabel ada
		$cekTable = $this->BJGMAS01->query("
										SELECT COUNT(*) AS ada 
										FROM INFORMATION_SCHEMA.TABLES 
										WHERE TABLE_NAME = '$table'
									")->row()->ada;

		if ($cekTable == 0) {
			// Kalau tidak ada, ambil bulan & tahun sebelumnya
			$prevMonth = date('m', strtotime('-1 month'));
			$prevYear  = date('Y', strtotime('-1 month'));
			$table = 'Trans_MPRHD' . $prevYear . $prevMonth;
		}

		// Query pencarian
		$this->BJGMAS01->select("NoBukti as id, NoBukti as text");
		$this->BJGMAS01->like('NoBukti', $SearchTerm);
		$this->BJGMAS01->or_like('NoBuktiJob', $SearchTerm);

		$Data = $this->BJGMAS01->get($table);

		echo json_encode($Data->result());
	}

	public function cari_job_old()
	{

		$SearchTerm = $this->input->get('term');

		// Ambil bulan & tahun sekarang
		$bulan = date('m');
		$tahun = date('Y');
		$table = 'Trans_MPRHD' . $tahun . $bulan;

		// Cek apakah tabel ada
		$cekTable = $this->BJGMAS01->query("
										SELECT COUNT(*) AS ada 
										FROM INFORMATION_SCHEMA.TABLES 
										WHERE TABLE_NAME = '$table'
									")->row()->ada;

		if ($cekTable == 0) {
			// Kalau tidak ada, ambil bulan & tahun sebelumnya
			$prevMonth = date('m', strtotime('-1 month'));
			$prevYear  = date('Y', strtotime('-1 month'));
			$table = 'Trans_MPRHD' . $prevYear . $prevMonth;
		}

		// Query pencarian
		$this->BJGMAS01->select("NoBuktiJob as id, NoBuktiJob  as text");
		$this->BJGMAS01->like('NoBuktiJob', $SearchTerm);

		$Data = $this->BJGMAS01->get($table);

		echo json_encode($Data->result());
	}


	public function get_rack_detail($id_rack)
	{
		$rack = $this->BJGMAS01->get_where('ms_rack', ['id_rack' => $id_rack])->row();
		$kolom = $this->BJGMAS01->get_where('ms_kolom_rack', ['id_rack' => $id_rack])->result();
		echo json_encode([
			'wh_lokasi' => $rack ? $rack->wh_lokasi : '',
			'kolom' => $kolom
		]);
	}


	/**
	 * Cek apakah stok mencukupi untuk transaksi OUT
	 * Menggunakan qty_remaining dari transaksi IN
	 */
	private function cek_stok($partid, $id_rack, $id_kolom, $qty_needed)
	{
		$qty_needed = (float)$qty_needed;
		if ($qty_needed <= 0) return TRUE;

		$query = $this->BJGMAS01->query("
        SELECT ISNULL(SUM(qty_remaining), 0) AS total_stok
        FROM tbl_trans_rack
        WHERE partid = ?
          AND id_rack = ?
          AND id_kolom = ?
          AND type_trans = 'IN'
          AND qty_remaining > 0
    ", [$partid, $id_rack, $id_kolom]);

		$result = $query->row();
		$total_stok = (float)$result->total_stok;

		return $total_stok >= $qty_needed;
	}

	public function insert_transaksi()
	{
		// Validasi sederhana
		$this->_validate_transaksi_form();

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => validation_errors('<div>', '</div>')
			]);
			return;
		}

		// Ambil dan parse input
		$part           = $this->input->post('PartID');
		$part           = explode('|', $part);
		$PartID         = $part[0];
		$PartName       = $part[1];
		$type_trans     = $this->input->post('type_trans');
		$id_rack        = $this->input->post('nama_rack');
		$id_kolom       = $this->input->post('nama_kolom');
		$qty            = (float) $this->input->post('qty');
		$wh_lokasi      = $this->input->post('wh_lokasi');
		$noted          = $this->input->post('noted');
		$units          = $this->input->post('units');
		$tgl_fifo_input = $this->input->post('tgl_fifo');
		$tgl_fifo       = DateTime::createFromFormat('d-m-Y', $tgl_fifo_input)->format('Y-m-d');
		$tgl_status_part = $tgl_fifo;
		$pilih_fifo     = $this->input->post('fifo');

		// Simpan qty sesuai tipe transaksi
		$qty_db = ($type_trans === 'OUT') ? -$qty : $qty;

		// Validasi stok hanya jika OUT
		if ($type_trans == 'OUT') {
			$tgl_fifo       = "";
			$pilih_fifo     = "";
			if (!$this->cek_stok($PartID, $id_rack, $id_kolom, $qty)) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Stok Tidak Cukup.'
				]);
				exit;
			}
		}

		// --- SIAPKAN DATA UNTUK DISIMPAN ---
		$data = [
			'id_rack'           => $id_rack,
			'id_kolom'          => $id_kolom,
			'wh_lokasi'         => $wh_lokasi,
			'partid'            => $PartID,
			'partname'          => $PartName,
			'qty'               => $qty_db,
			'noted'             => $noted,
			'fifo'              => $pilih_fifo,
			'tgl_fifo'          => $tgl_fifo,
			'tgl_status_part'   => $tgl_status_part,
			'units'             => $units,
			'type_trans'        => $type_trans,
			'created_by'        => $this->session->userdata('user_code'),
			'created_date'      => date('Y-m-d H:i:s')
		];

		// Atur qty_remaining:
		// - Untuk IN: sama dengan qty (positif)
		// - Untuk OUT: NULL
		$data['qty_remaining'] = ($type_trans === 'IN') ? $qty : NULL;

		$this->BJGMAS01->trans_start();

		$this->BJGMAS01->insert('tbl_trans_rack', $data, true);

		$insert_id = $this->BJGMAS01->insert_id();

		if (!$insert_id) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal menyimpan transaksi.'
			]);
			return;
		}
		// ✅ Jika OUT, proses FIFO otomatis
		$fifo_date_from_in = false;
		if ($type_trans === 'OUT' && $qty > 0) {
			$fifo_date_from_in = $this->proses_fifo_automatis($insert_id, $PartID, $id_rack, $id_kolom, $qty);
			if (!$fifo_date_from_in) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Gagal memproses alokasi FIFO .'
				]);
				return;
			}
		}

		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Transaksi database gagal.'
			]);
			return;
		}

		// --- RESPON SUKSES ---
		echo json_encode([
			'status' => 'success',
			'message' => 'Transaksi berhasil disimpan.',
			'data' => [
				'id' => $insert_id,
				'tgl_fifo' => $fifo_date_from_in ? $fifo_date_from_in : $tgl_fifo
			]
		]);
	}

	public function insert_transaksi_modal()
	{
		$partid   		= $this->input->post('partid');
		$partname   	= $this->input->post('partname');
		$type_trans 	= $this->input->post('type_trans');
		$id_rack  		= $this->input->post('id_rack');
		$id_kolom 		= $this->input->post('id_kolom');
		$qty      		= (float) $this->input->post('jumlah');
		$wh_lokasi     	= $this->input->post('wh_lokasi');
		$units      	= $this->input->post('units');
		$noted      	= $this->input->post('noted');
		$tgl_fifo_input = $this->input->post('tgl_fifo');
		$pilih_fifo     = $this->input->post('pilih_fifo');
		$sisa_mpr	    = $this->input->post('jumlah_sisa_mpr');
		$job      		= $this->input->post('pilih_job');
		$relasi_sisa_mpr 	= null;
		$qty_db 			= ($type_trans === 'OUT') ? -$qty : $qty;




		if ($type_trans == 'OUT') {
			$tgl_fifo = "";
			$pilih_fifo = "";
			if (!$this->cek_stok($partid, $id_rack, $id_kolom, $qty)) {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Stok Tidak Cukup.'
				]);
				exit;
			}

			if ($qty <= 0 && empty($sisa_mpr) && empty($job)) {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Data tidak boleh kosong.'
				]);
				exit;
			}
		} else {
			$tgl_fifo	 		= DateTime::createFromFormat('d-m-Y', $tgl_fifo_input)->format('Y-m-d');
			if (empty($qty) || $qty <= 0) {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Qty tidak boleh kosong.'
				]);
				exit;
			}
			if (empty($tgl_fifo_input)) {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Tanggal fifo tidak boleh kosong.'
				]);
				exit;
			}
		}

		$this->BJGMAS01->trans_start();

		if (!empty($qty) && $qty > 0) {

			// Simpan ke tbl_trans_rack -UTAMA
			$data = [
				'id_rack'     		=> $id_rack,
				'id_kolom'    		=> $id_kolom,
				'wh_lokasi'  		=> $wh_lokasi,
				'partid'      		=> $partid,
				'partname'    		=> $partname,
				'qty'         		=> $qty_db,
				'noted'       		=> $noted,
				'fifo'        		=> $pilih_fifo,
				'tgl_fifo'    		=> $tgl_fifo,
				'units'       		=> $units,
				'type_trans'  		=> $type_trans,
				'created_by'  		=> $this->session->userdata('user_code'),
				'tgl_status_part'   => date('Y-m-d H:i:s'),
				'created_date' 		=> date('Y-m-d H:i:s'),
				'qty_remaining'     => ($type_trans === 'IN') ? $qty : NULL
			];

			$this->BJGMAS01->insert('tbl_trans_rack', $data, true);

			$insert_id = $this->BJGMAS01->insert_id();
			if (!$insert_id) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode([
					'status' => 'error',
					'message' => 'Gagal menyimpan transaksi.'
				]);
				return;
			}

			// Proses FIFO otomatis untuk OUT
			$fifo_date_from_in = false;
			if ($type_trans === 'OUT' && $qty > 0) {
				$fifo_date_from_in = $this->proses_fifo_automatis($insert_id, $partid, $id_rack, $id_kolom, $qty);
				if (!$fifo_date_from_in) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Gagal memproses alokasi FIFO di modal.'
					]);
					return;
				}
			}


			if ($this->BJGMAS01->trans_status() === FALSE) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Transaksi database gagal.'
				]);
				return;
			}
		}

		// Simpan ke tbl_sisa_mpr (jika ada)
		if (!empty($sisa_mpr) && !empty($job)) {
			$relasi_sisa_mpr = 'SM-' . date('YmdHis') . '-' . uniqid();

			$data_sisa_mpr = [
				'job'           	=> $job,
				'partid'        	=> $partid,
				'partname'      	=> $partname,
				'sisa_mpr'      	=> $sisa_mpr,
				'units'         	=> $units,
				'wh_lokasi'     	=> $wh_lokasi,
				'relasi_sisa_mpr' 	=> $relasi_sisa_mpr,
				'created_by'    	=> $this->session->userdata('user_code'),
				'created_date'  	=> date('Y-m-d H:i:s')
			];


			$this->BJGMAS01->insert('tbl_sisa_mpr', $data_sisa_mpr);

			// Simpan ke tbl_trans_rack
			$data = [
				'id_rack'     		=> $id_rack,
				'id_kolom'    		=> $id_kolom,
				'wh_lokasi'  		=> $wh_lokasi,
				'partid'      		=> $partid,
				'partname'    		=> $partname,
				'qty'         		=> -$sisa_mpr,
				'noted'       		=> "Sisa MPR dari $job",
				'fifo'        		=> $pilih_fifo,
				'tgl_fifo'    		=> $tgl_fifo,
				'units'       		=> $units,
				'type_trans'  		=> $type_trans,
				'relasi_sisa_mpr'	=> $relasi_sisa_mpr, // hanya ada jika insert ke tbl_sisa_mpr
				'created_by'  		=> $this->session->userdata('user_code'),
				'tgl_status_part'   => date('Y-m-d H:i:s'),
				'created_date' 		=> date('Y-m-d H:i:s'),
				'qty_remaining'     => ($type_trans === 'IN') ? $qty : NULL
			];

			$this->BJGMAS01->insert('tbl_trans_rack', $data, true);

			$insert_id = $this->BJGMAS01->insert_id();
			if (!$insert_id) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode([
					'status' => 'error',
					'message' => 'Gagal menyimpan transaksi.'
				]);
				return;
			}

			// Proses FIFO otomatis untuk OUT
			$fifo_date_from_in = false;
			if ($type_trans === 'OUT' && $sisa_mpr > 0) {
				$fifo_date_from_in = $this->proses_fifo_automatis($insert_id, $partid, $id_rack, $id_kolom, $sisa_mpr);
				if (!$fifo_date_from_in) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Gagal memproses alokasi FIFO di modal.'
					]);
					return;
				}
			}

			if ($this->BJGMAS01->trans_status() === FALSE) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Transaksi database gagal.'
				]);
				return;
			}
		} elseif (!empty($sisa_mpr) || !empty($job)) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Silahkan isi Pilih Job dan Sisa MPR.'
			]);
			return;
		}


		$this->BJGMAS01->trans_complete();


		echo json_encode([
			'status' => 'success',
			'message' => 'Transaksi berhasil disimpan.',
			// 'data' => [
			// 	'id' => $insert_id,
			// 	'tgl_fifo' => $fifo_date_from_in ? $fifo_date_from_in : $tgl_fifo,
			// 	'relasi_sisa_mpr' => $relasi_sisa_mpr
			// ]
		]);
	}


	public function insert_transaksi_modal_test()
	{


		//cek dulu disini untuk type trans out. untuk partid,id_rack,id_kolom, ada stoknya ga? artinaya qty yang type transout < dari stok partid,id_rack,id_kolom
		$partid   		= $this->input->post('partid');
		$partname   	= $this->input->post('partname');
		$type_trans 	=  $this->input->post('type_trans');
		$id_rack  		= $this->input->post('id_rack');
		$id_kolom 		= $this->input->post('id_kolom');
		$qty      		= (float) $this->input->post('jumlah');
		$wh_lokasi     	= $this->input->post('wh_lokasi');
		$units      	= $this->input->post('units');
		$noted      	= $this->input->post('noted');
		$tgl_fifo_input = $this->input->post('tgl_fifo');
		$pilih_fifo     = $this->input->post('pilih_fifo');
		$sisa_mpr	    = $this->input->post('jumlah_sisa_mpr');
		$job      		= $this->input->post('pilih_job');
		// Ubah ke format YYYY-MM-DD
		// Simpan qty sesuai tipe transaksi
		$qty_db = ($type_trans === 'OUT') ? -$qty : $qty;

		if ($type_trans == 'OUT') {
			$tgl_fifo       = "";
			$pilih_fifo     = "";
			if (!$this->cek_stok($partid, $id_rack, $id_kolom, $qty)) {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Stok Tidak Cukup.'
				]);
				exit;
			}
		} else {
			if ($tgl_fifo_input == null || $tgl_fifo_input == '') {
				echo json_encode([
					'status' 	=> 'error',
					'message' 	=> 'Tanggal fifo tidak boleh kosong.'
				]);
				exit;
			}

			$tgl_fifo 		= DateTime::createFromFormat('d-m-Y', $tgl_fifo_input)->format('Y-m-d');
		}


		$relasi_sisa_mpr = 'SM-' . date('YmdHis') . '-' . uniqid();


		// Simpan ke database (contoh)
		$data = [
			'id_rack'     		=> $id_rack,
			'id_kolom'    		=> $id_kolom,
			'wh_lokasi'  		=> $wh_lokasi,
			'partid'      		=> $partid,
			'partname'    		=> $partname,
			'qty'         		=> $qty_db,
			'noted'       		=> $noted,
			'fifo'        		=> $pilih_fifo,
			'tgl_fifo'    		=> $tgl_fifo,
			'units'       		=> $units,
			'type_trans'  		=> $type_trans,
			'created_by'  		=> $this->session->userdata('user_code'),
			'tgl_status_part'   => date('Y-m-d H:i:s'),
			'created_date' 		=> date('Y-m-d H:i:s')
		];

		// Atur qty_remaining:
		// - Untuk IN: sama dengan qty (positif)
		// - Untuk OUT: NULL
		$data['qty_remaining'] = ($type_trans === 'IN') ? $qty : NULL;

		$this->BJGMAS01->trans_start();

		$this->BJGMAS01->insert('tbl_trans_rack', $data, true);

		// Simpan ke tbl_sisa_mpr
		if (!empty($sisa_mpr) && !empty($job)) {
			$data_sisa_mpr = [
				'job'           => $job,
				'partid'        => $partid,
				'partname'      => $partname,
				'sisa_mpr'      => $sisa_mpr,
				'units'         => $units,
				'wh_lokasi'     => $wh_lokasi,
				'created_by'    => $this->session->userdata('user_code'),
				'created_date'  => date('Y-m-d H:i:s')
			];

			// Cek apakah data sisa_mpr sudah ada
			$this->BJGMAS01->where('partid', $partid);
			$this->BJGMAS01->where('job', $job);
			$existing = $this->BJGMAS01->get('tbl_sisa_mpr')->row();

			if ($existing) {
				// Update jika sudah ada
				$new_sisa = $existing->sisa_mpr + $sisa_mpr;
				$this->BJGMAS01->where('id', $existing->id);
				$this->BJGMAS01->update('tbl_sisa_mpr', [
					'sisa_mpr'     => $new_sisa,
					'updated_by'   => $this->session->userdata('user_code'),
					'updated_date' => date('Y-m-d H:i:s')
				]);
			} else {
				// Insert baru
				$this->BJGMAS01->insert('tbl_sisa_mpr', $data_sisa_mpr);
			}
		} elseif (!empty($sisa_mpr) || !empty($job)) {
			// Salah satu kosong → return error
			echo json_encode([
				'status'  => 'error',
				'message' => 'Silahkan isi Pilih Job dan Sisa MPR.'
			]);

			return;
		}

		$insert_id = $this->BJGMAS01->insert_id();

		if (!$insert_id) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal menyimpan transaksi.'
			]);
			return;
		}

		// ✅ Jika OUT, proses FIFO otomatis
		$fifo_date_from_in = false;
		if ($type_trans === 'OUT' && $qty > 0) {
			$fifo_date_from_in = $this->proses_fifo_automatis($insert_id, $partid, $id_rack, $id_kolom, $qty);
			if (!$fifo_date_from_in) {
				echo json_encode([
					'status' => 'error',
					'message' => 'Gagal memproses alokasi FIFO di modal.'
				]);
				return;
			}
		}

		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Transaksi database gagal.'
			]);
			return;
		}

		// --- RESPON SUKSES ---
		echo json_encode([
			'status' => 'success',
			'message' => 'Transaksi berhasil disimpan.',
			'data' => [
				'id' => $insert_id,
				'tgl_fifo' => $fifo_date_from_in ? $fifo_date_from_in : $tgl_fifo
			]
		]);
	}

	private function proses_fifo_automatis($out_id, $partid, $id_rack, $id_kolom, $qty_out)
	{
		// 🔹 Pastikan qty awal dibulatkan 2 desimal
		$remaining = round((float)$qty_out, 2);
		$first_fifo_date = null;

		$this->BJGMAS01->trans_start();

		// 🔹 Ambil stok IN tertua yang masih tersedia
		$in_records = $this->BJGMAS01->query("
												SELECT tr.id_trans_rack, tr.qty_remaining, tr.tgl_fifo
												FROM tbl_trans_rack tr
												WHERE tr.partid = ?
												AND tr.id_rack = ?
												AND tr.id_kolom = ?
												AND tr.type_trans = 'IN'
												AND tr.qty_remaining > 0
												ORDER BY tr.tgl_fifo ASC, tr.created_date ASC
											", [$partid, $id_rack, $id_kolom])->result_array();

		foreach ($in_records as $in) {
			if ($remaining <= 0.00) break;

			// 🔹 Pastikan pembulatan qty IN
			$available = round((float)$in['qty_remaining'], 2);
			$used      = round(min($available, $remaining), 2);

			// Simpan detail FIFO
			$this->BJGMAS01->insert('tbl_detail_fifo', [
				'out_id'       => $out_id,
				'in_id'        => $in['id_trans_rack'],
				'qty_used'     => $used,
				'tgl_fifo_in'  => $in['tgl_fifo'],
				'created_at'   => date('Y-m-d H:i:s')
			]);

			// Catat tgl_fifo pertama yang digunakan
			if ($first_fifo_date === null) {
				$first_fifo_date = $in['tgl_fifo'];
			}

			// Kurangi stok IN
			$new_remaining = round($available - $used, 2);
			$this->BJGMAS01->update('tbl_trans_rack', [
				'qty_remaining' => $new_remaining
			], [
				'id_trans_rack' => $in['id_trans_rack']
			]);

			// 🔹 Update sisa kebutuhan
			$remaining = round($remaining - $used, 2);
		}

		// 🔹 Simpan tgl_fifo pertama & nama bulan ke transaksi OUT
		if ($remaining <= 0.005 && $first_fifo_date !== null) {
			$bulan_angka = date('m', strtotime($first_fifo_date));

			$nama_bulan = [
				'01' => 'januari',
				'02' => 'februari',
				'03' => 'maret',
				'04' => 'april',
				'05' => 'mei',
				'06' => 'juni',
				'07' => 'juli',
				'08' => 'agustus',
				'09' => 'september',
				'10' => 'oktober',
				'11' => 'november',
				'12' => 'desember'
			];

			$fifo = $nama_bulan[$bulan_angka] ?? '';

			$this->BJGMAS01->update('tbl_trans_rack', [
				'tgl_fifo' => $first_fifo_date,
				'fifo'     => $fifo
			], [
				'id_trans_rack' => $out_id
			]);

			// Pastikan sisa dianggap nol
			$remaining = 0.00;
		}

		// 🔹 Jika stok tidak cukup (lebih dari toleransi), rollback
		if ($remaining > 0.005) {
			$this->BJGMAS01->trans_rollback();
			log_message('error', "Stok tidak cukup untuk OUT ID: $out_id, sisa: $remaining");
			return false;
		}

		$this->BJGMAS01->trans_complete();

		if ($this->BJGMAS01->trans_status() === FALSE) {
			log_message('error', "Transaksi FIFO gagal untuk OUT ID: $out_id");
			return false;
		}

		return $first_fifo_date;
	}


	private function _validate_transaksi_form()
	{
		$this->form_validation->set_rules('nama_rack', 'Nama Rack', 'required');
		$this->form_validation->set_rules('nama_kolom', 'Nama Kolom', 'required');
		$this->form_validation->set_rules('wh_lokasi', 'WH Lokasi', 'required');
		$this->form_validation->set_rules('PartID', 'Part Name', 'required');
		$this->form_validation->set_rules('qty', 'Jumlah', 'required|numeric');
		$this->form_validation->set_rules('units', 'Units', 'required');
		$this->form_validation->set_rules('tgl_fifo', 'Tanggal Fifo', 'required');
		$this->form_validation->set_rules('fifo', 'Fifo', 'required');
		$this->form_validation->set_rules('type_trans', 'Type Trans', 'required');
	}

	// WAREHOUSE RACK #################################

	public function rack_add()
	{
		// //CHECK FOR ACCESS FOR EACH FUNCTION
		// $user_level       = $this->session->userdata('user_level');
		// $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		// if ($check_permission->num_rows() == 1) {
		$this->_validation_rak();

		$Rak      = trim(strtoupper($this->input->post('Rak')));
		$DataRak  = $this->BJGMAS01->get_where('ms_rack', array('nama_rack' => $Rak));
		$CekRak   = $DataRak->num_rows();
		if ($CekRak == 0) {

			$master_rack = array(
				'nama_rack'    => $Rak,
				'wh_lokasi'    => $this->input->post('WHLokasi'),
				'pic'       => $this->input->post('user'),
				'created_by'   => $this->session->userdata('user_code'),
				'created_date' => date('Y-m-d H:i:s')
			);

			$looping_kolom = intval($this->input->post('Isi'));

			$this->BJGMAS01->trans_begin(); // Mulai transaksi

			$this->BJGMAS01->insert('ms_rack', $master_rack);
			$id_rack = $this->BJGMAS01->insert_id();

			$data_kolom = [];
			for ($i = 1; $i <= $looping_kolom; $i++) {
				$data_kolom[] = array(
					'id_kolom'     => $id_rack . '-' . $i,
					'nama_kolom'   => $Rak . '-' . $i,
					'id_rack'      => $id_rack,
					'created_by'   => $this->session->userdata('user_code'),
					'created_date' => date('Y-m-d H:i:s')
				);
			}

			if (!empty($data_kolom)) {
				$this->BJGMAS01->insert_batch('ms_kolom_rack', $data_kolom);
			}

			if ($this->BJGMAS01->trans_status() === FALSE) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode([
					"status_code" => 500,
					"status"      => "error",
					"message"     => "Gagal simpan data rack atau kolom."
				]);
			} else {
				$this->BJGMAS01->trans_commit();
				echo json_encode([
					"status_code" => 200,
					"status"      => "success",
					"message"     => "Data Rak dan Kolom sukses disimpan."
				]);
			}
		} else {
			echo json_encode(
				array(
					"status_code" => 500,
					"status"      => "error",
					"message"     => "Data Rak " . $Rak . " sudah tersedia."
				)
			);
		}
		// } else {
		//   echo json_encode(array("status" => "forbidden"));
		// }
	}

	public function rack_list()
	{
		$Draw    = intval($this->input->get("draw"));
		$Start   = intval($this->input->get("start"));
		$Length  = intval($this->input->get("length"));

		$Sql     = "SELECT 
                    a.id_rack, 
                    a.nama_rack, 
                    a.wh_lokasi, 
                  	a.pic,
                    COUNT(b.id_kolom) AS jumlah_kolom,
                    a.created_by, 
                    a.created_date
                FROM 
                    ms_rack a 
                LEFT JOIN 
                    ms_kolom_rack b ON a.id_rack = b.id_rack
                GROUP BY 
                    a.id_rack, a.nama_rack, a.wh_lokasi, a.pic, a.created_by, a.created_date
                ORDER BY 
				CAST(a.id_rack AS INT) ASC";

		$Query   = $this->BJGMAS01->query($Sql);

		$Result  = $Query->result();
		$Data    = [];
		$No      = 1;

		foreach ($Result as $key => $value) {
			$tanggal_asli = $value->created_date;
			// Buat objek DateTime dari string
			$tanggal = new DateTime($tanggal_asli);


			$Data[] = array(

				$No++,
				'<a href="javascript:void(0)" onclick="edit(' . "'" . $value->id_rack . "'" . ')"
						class="btn waves-effect waves-light btn-success btn-sm">
							<i class="fa fa-edit"></i>
						</a>
						<a href="javascript:void(0)" onclick="openModalDelete(' . "'" . $value->id_rack . "'" . ')"
						class="btn waves-effect waves-light btn-danger btn-sm">
							<i class="fa fa-times"></i>
				</a>',
				strtoupper($value->pic),
				$value->nama_rack,
				$value->wh_lokasi,
				$value->jumlah_kolom,
				$value->created_by,
				$tanggal->format('d-m-Y H:i:s')
			);
		}

		$Results = array(
			"draw"             => $Draw,
			"recordsTotal"     => $Query->num_rows(),
			"recordsFiltered"  => $Query->num_rows(),
			"data"             => $Data
		);

		echo json_encode($Results);
		exit();
	}

	public function master_rack()
	{
		$data['group_halaman']    = "Master Data";
		$data['nama_halaman']     = "Master Rack";
		$data['icon_halaman']     = "icon-layers";
		$data['perusahaan']       = $this->perusahaan->get_details();
		$kg 					  = ['WH-FG', 'WH-FG01'];
		$nonkg 					  = ['WH-GRS00', 'WH-GRS01', 'WH-KND', 'WH-KUDUS', 'WH-SAYUNG'];

		$this->BJGMAS01->select('LocationID');
		$this->BJGMAS01->from('Ms_WarehouseStock');
		$this->BJGMAS01->where('Aktif', '1');
		$this->BJGMAS01->where_not_in('LocationID', array_merge($kg, $nonkg));

		$data['wh'] 				= $this->BJGMAS01->get()->result();

		// Tambahkan dua lokasi manual
		array_push(
			$data['wh'],
			(object)['LocationID' => 'WH-FG-KG'],
			(object)['LocationID' => 'WH-FG-NONKG']
		);


		//ADDING TO LOG
		$log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type                 = "VIEW";
		$log_data                 = "";

		log_helper($log_url, $log_type, $log_data);
		//END LOG



		$this->load->view('adminx/warehouse/rack/index', $data, FALSE);
	}

	public function rack_edit($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		// $user_level       = $this->session->userdata('user_level');
		// $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		// if ($check_permission->num_rows() == 1) {
		//$data = $this->BJGMAS01->get_where('Trans_RakHD', array('Id' => $id))->row();
		$Sql     = "SELECT 
                    a.id_rack, 
                    a.nama_rack, 
                    a.wh_lokasi, 
                    a.pic, 
                    COUNT(b.id_kolom) AS jumlah_kolom,
                    a.created_by, 
                    a.created_date
                FROM 
                    ms_rack a 
                LEFT JOIN 
                    ms_kolom_rack b ON a.id_rack = b.id_rack
                WHERE 
                    a.id_rack = '$id'
                GROUP BY 
                    a.id_rack, a.nama_rack, a.wh_lokasi, a.pic, a.created_by, a.created_date
                ORDER BY 
                    a.id_rack ASC, CAST(a.created_date AS DATE) DESC";
		$Query    = $this->BJGMAS01->query($Sql);
		$Result   = $Query->row();

		$SqlDT    = "SELECT id_kolom, nama_kolom, id_rack FROM ms_kolom_rack 
                WHERE id_rack = '$id'
                ORDER BY 
                TRY_CAST(SUBSTRING(nama_kolom, CHARINDEX('-', nama_kolom) + 1, LEN(nama_kolom)) AS INT)";

		$QueryDT  = $this->BJGMAS01->query($SqlDT);
		$ResultDT = $QueryDT->result();
		$HtmlDT   = "";
		foreach ($ResultDT as $key => $value) {
			$HtmlDT   .= "<li class='list-group-item'>" . trim($value->nama_kolom) . "</li>";
		}

		echo json_encode(
			array(
				"data_header" => $Result,
				"data_detail" => $ResultDT,
				"html_detail" => $HtmlDT
			)
		);

		//   //ADDING TO LOG
		//   $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
		//   $log_type       = "EDIT";
		//   $log_data       = json_encode($Result);

		//   log_helper($log_url, $log_type, $log_data);
		//   //END LOG
		// } else {
		//   echo json_encode(array("status" => "forbidden"));
		// }
	}


	public function rack_update()
	{
		// $user_level       = $this->session->userdata('user_level');
		// $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		// if ($check_permission->num_rows() == 1) {
		$this->_validation_rak();

		$Id     	= $this->input->post('kode');
		$Rak    	= trim(strtoupper($this->input->post('Rak')));

		// Cek apakah nama rack sudah ada (kecuali untuk id yang sedang diedit)
		$DataRak  	= $this->BJGMAS01->get_where('ms_rack', array('nama_rack' => $Rak, 'id_rack !=' => $Id));
		$CekRak   	= $DataRak->num_rows();
		if ($CekRak == 0) {

			$master_rack = array(
				'nama_rack'    	=> $Rak,
				'wh_lokasi'    	=> $this->input->post('WHLokasi'),
				'pic'       	=> $this->input->post('user'),
				'created_by'   	=> $this->session->userdata('user_code'),
				'created_date' 	=> date('Y-m-d H:i:s')
			);

			$looping_kolom = intval($this->input->post('Isi'));

			$this->BJGMAS01->trans_begin(); // Mulai transaksi

			// Update ms_rack
			$this->BJGMAS01->update('ms_rack', $master_rack, ['id_rack' => $Id]);

			// Hapus kolom lama
			$this->BJGMAS01->delete('ms_kolom_rack', ['id_rack' => $Id]);

			// Insert ulang kolom baru
			$data_kolom = [];
			for ($i = 1; $i <= $looping_kolom; $i++) {
				$data_kolom[] = array(
					'id_kolom'     => $Id . '-' . $i,
					'nama_kolom'   => $Rak . '-' . $i,
					'id_rack'      => $Id,
					'created_by'   => $this->session->userdata('user_code'),
					'created_date' => date('Y-m-d H:i:s')
				);
			}
			if (!empty($data_kolom)) {
				$this->BJGMAS01->insert_batch('ms_kolom_rack', $data_kolom);
			}

			if ($this->BJGMAS01->trans_status() === FALSE) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode([
					"status_code" => 500,
					"status"      => "error",
					"message"     => "Gagal update data rack atau kolom."
				]);
			} else {
				$this->BJGMAS01->trans_commit();
				echo json_encode([
					"status_code" => 200,
					"status"      => "success",
					"message"     => "Data Rak dan Kolom sukses diupdate."
				]);
			}
		} else {
			echo json_encode([
				"status_code" => 500,
				"status"      => "error",
				"message"     => "Data Rak " . $Rak . " sudah tersedia."
			]);
		}
		// } else {
		//   echo json_encode(array("status" => "forbidden"));
		// }
	}

	public function rack_hapus($id)
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		// $user_level       = $this->session->userdata('user_level');
		// $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		// if ($check_permission->num_rows() == 1) {
		//harusnya di cek dulu ada transaksi di dalam rak ini
		$this->BJGMAS01->trans_begin(); // Mulai transaksi

		$this->BJGMAS01->delete("ms_kolom_rack", ['id_rack' => $id]);
		$this->BJGMAS01->delete("ms_rack", ['id_rack' => $id]);

		if ($this->BJGMAS01->trans_status() === FALSE) {
			$this->BJGMAS01->trans_rollback();
			echo json_encode(
				array(
					"status_code" => 500,
					"status"      => "error",
					"message"     => "Gagal menghapus data rak atau kolom."
				)
			);
		} else {
			$this->BJGMAS01->trans_commit();
			echo json_encode(
				array(
					"status_code" => 200,
					"status"      => "success",
					"message"     => "Data berhasil dihapus"
				)
			);
		}
		// } else {
		//   echo json_encode(array("status" => "forbidden"));
		// }
	}

	public function get_rack_user()
	{
		$wh_lokasi 	= $this->input->post('wh_lokasi');
		$sql 		= "SELECT pic FROM ms_rack WHERE wh_lokasi = ?";
		$query 		= $this->BJGMAS01->query($sql, [$wh_lokasi]);
		$result 	= $query->row();

		echo json_encode($result);
	}

	private function _validation_rak()
	{
		$data                 = array();
		$data['error_string'] = array();
		$data['inputerror']   = array();
		$data['status']       = TRUE;

		if ($this->input->post('Rak') == '') {
			$data['inputerror'][]   = 'Rak';
			$data['error_string'][] = 'Rak is required';
			$data['status']         = FALSE;
		}

		if ($this->input->post('WHLokasi') == '') {
			$data['inputerror'][]   = 'WHLokasi';
			$data['error_string'][] = 'WH Lokasi is required';
			$data['status']         = FALSE;
		}

		if ($this->input->post('Isi') == '') {
			$data['inputerror'][]   = 'Isi';
			$data['error_string'][] = 'Isi is required';
			$data['status']         = FALSE;
		}

		if ($this->input->post('user') == '') {
			$data['inputerror'][]   = 'user';
			$data['error_string'][] = 'User is required';
			$data['status']         = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	// WAREHOUSE PART  DELETE #################################
	public function delete()
	{

		$data['group_halaman'] 	= "Warehouse";
		$data['nama_halaman'] 	= "Part Delete";
		$data['icon_halaman'] 	= "icon-airplay";
		$data['perusahaan'] 	= $this->perusahaan->get_details();
		$data['wh_lokasi']      = $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();

		//ADDING TO LOG
		$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type 		= "VIEW";
		$log_data 		= "";

		log_helper($log_url, $log_type, $log_data);
		//END LOG

		$this->load->view('adminx/warehouse/wh_hapus/index', $data, FALSE);
	}

	public function clear_table()
	{
		$user_level = $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

		if ($check_permission->num_rows() == 1) {
			// Hapus data
			$this->BJGMAS01->query("TRUNCATE TABLE tbl_trans_rack_hapus");

			// Respon sukses
			echo json_encode(['status' => 'success', 'message' => 'Tabel berhasil dikosongkan']);
		} else {
			// Jangan redirect, kirim JSON error
			http_response_code(403);
			echo json_encode(['status' => 'error', 'message' => 'Anda tidak punya otorisasi untuk melakukan ini']);
		}
	}

	public function get_list_transaksi_hapus()
	{
		$sql = "
				SELECT 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					SUM(a.qty) AS total_qty,
					 MAX(a.tgl_status_part) AS latest_created_date,
					a.units
				FROM tbl_trans_rack_hapus a
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom
				WHERE a.wh_lokasi = ?
				GROUP BY 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					a.units
				ORDER BY 
					LEFT(b.nama_rack, PATINDEX('%[0-9]%', b.nama_rack + '0') - 1), 
						TRY_CAST(SUBSTRING(b.nama_rack, PATINDEX('%[0-9]%', b.nama_rack + '0'), 10) AS INT),
					LEFT(c.nama_kolom, PATINDEX('%[0-9]%', c.nama_kolom + '0') - 1), 
						TRY_CAST(SUBSTRING(c.nama_kolom, PATINDEX('%[0-9]%', c.nama_kolom + '0'), 10) AS INT)
			";
		$wh_lokasi 	= $this->input->post('wh_lokasi');
		$query 		= $this->BJGMAS01->query($sql, [$wh_lokasi]);
		$result 	= $query->result();

		// Jika untuk DataTables, bisa tambahkan nomor urut, dll
		$data 		= [];
		$no 		= 1;
		foreach ($result as $row) {
			$lihat 	=  $row->id_rack . '|' . $row->id_kolom . '|' . $row->partid . '|' . $row->wh_lokasi;

			$data[] = [
				'no'         	=> $no++,
				'id_rack'    	=> $row->id_rack,
				'nama_rack'  	=> $row->nama_rack,
				'id_kolom'   	=> $row->id_kolom,
				'nama_kolom' 	=> $row->nama_kolom,
				'wh_lokasi'  	=> $row->wh_lokasi,
				'partid'     	=> $row->partid,
				'partname'   	=> $row->partname,
				'qty'        	=> number_format($row->total_qty, 2, ",", "."),
				'units'      	=> $row->units,
				'status_part' 	=> $this->getStatusWarnaPart($row->latest_created_date),
				'lihat' 	 	=> '<a href="' . base_url() . 'warehouse_part/warehouse_mapping_hapus/' . base64_encode($lihat) . '" ><button class="btn btn-danger btn-block btn-sm">Lihat</button></a>',

			];
		}


		echo json_encode(['data' => $data]);
	}

	public function warehouse_mapping_hapus($data_params)
	{
		$data_array = explode("|", base64_decode($data_params));
		$id_rack 	= $data_array[0];
		$id_kolom 	= $data_array[1];
		$partid 	= $data_array[2];

		$sql = "
				SELECT 
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					a.units
					
				FROM tbl_trans_rack_hapus a
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom
				WHERE 
					partid='$partid'
					AND a.id_rack='$id_rack'
					AND a.id_kolom='$id_kolom'
				order by a.created_date asc ";

		$query = $this->BJGMAS01->query($sql);


		$data['group_halaman'] 		= "Warehouse";
		$data['nama_halaman'] 		= "Warehouse Mapping Hapus";
		$data['icon_halaman'] 		= "icon-airplay";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$data['wh'] 				= $query->row();

		$this->load->view('adminx/warehouse/wh_mapping_hapus/index', $data, FALSE);
	}


	public function warehouse_mapping_detail_hapus()
	{
		$partid 	= $this->input->post('partid');
		$id_rack 	= $this->input->post('id_rack');
		$id_kolom 	= $this->input->post('id_kolom');
		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');


		$sql = "
			WITH StokAwal AS (
				SELECT 
					SUM(
					CASE 
						WHEN a.type_trans = 'IN' THEN a.qty 
						WHEN a.type_trans = 'OUT' THEN -a.qty 
						ELSE 0 
					END
					) AS total_stok_awal
				FROM 
					tbl_trans_rack_hapus a
				WHERE 
					partid='$partid'
					AND a.id_rack='$id_rack'
					AND a.id_kolom='$id_kolom'
					AND a.created_date < '$start_date'
				)

				SELECT 
				CONVERT(VARCHAR, a.created_date, 120) AS tanggal,
				CASE 
					WHEN a.type_trans = 'IN' THEN a.qty 
					ELSE NULL 
				END AS qty_in,
				CASE 
					WHEN a.type_trans = 'OUT' THEN a.qty 
					ELSE NULL 
				END AS qty_out,

				
    			(SELECT total_stok_awal FROM StokAwal) + 
				SUM(
					CASE 
					WHEN a.type_trans = 'IN' THEN a.qty 
					WHEN a.type_trans = 'OUT' THEN a.qty 
					ELSE 0 
					END
				) OVER (
					PARTITION BY a.partid, a.id_rack, a.id_kolom 
					ORDER BY a.created_date ASC
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) AS balance,
				a.tgl_fifo,
				a.noted,
				a.fifo

				FROM 
				tbl_trans_rack_hapus a 
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack 
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom 
				
				WHERE 
				partid='$partid'
				AND a.id_rack='$id_rack'
				AND a.id_kolom='$id_kolom'
				AND CAST(a.created_date AS DATE) BETWEEN '$start_date' AND '$end_date' 

				UNION ALL

				SELECT 
				'$start_date 00:00:00' AS tanggal,
				(SELECT total_stok_awal FROM StokAwal) AS qty_in,
				NULL AS qty_out,
				(SELECT total_stok_awal FROM StokAwal) AS balance, 
				NULL AS tgl_fifo,
				'Stok Awal' AS noted,
				NULL AS fifo

				ORDER BY 
				tanggal ASC;";



		$query 		  	= $this->BJGMAS01->query($sql);
		$data      		= $query->result();

		foreach ($data as $key => $value) {
			$data[$key]->tanggal 	= date('d-m-Y', strtotime($value->tanggal));
			$data[$key]->fifo 		= $this->get_fifo($value->fifo, date('d-m-Y', strtotime($value->tgl_fifo)));
		}


		$result = array(
			"recordsTotal"     	=> $query->num_rows(),
			"recordsFiltered" 	=> $query->num_rows(),
			"data"             	=> $data
		);

		echo json_encode($result);
	}

	// WAREHOUSE PART FIFO #################################
	public function get_part_id()
	{
		$term = $this->input->get('search');
		$query = $this->BJGMAS01->select('partid AS id, partname AS text')
			->like('partid', $term)
			->or_like('partname', $term)
			->group_by(['partid', 'partname'])
			->get('tbl_trans_rack');

		echo json_encode($query->result());
	}

	public function get_lokasi()
	{
		$term = $this->input->get('search');
		$query = $this->BJGMAS01->select('wh_lokasi AS id, wh_lokasi AS text')
			->like('wh_lokasi', $term)
			->group_by('wh_lokasi')
			->get('ms_rack');

		echo json_encode($query->result());
	}

	public function get_rack()
	{
		$term = $this->input->get('search');
		$query = $this->BJGMAS01->select('id_rack AS id, nama_rack AS text')
			->like('nama_rack', $term)
			->group_by(['id_rack', 'nama_rack'])
			->get('ms_rack');

		echo json_encode($query->result());
	}

	public function part_fifo()
	{

		$role_names 		= ['Supervisor Warehouse', 'Superadmin'];
		$role_placeholders 	= "'" . implode("','", $role_names) . "'";

		$sql 				= "SELECT roles_name, idroles FROM roles WHERE roles_name IN ($role_placeholders)";
		$query 				= $this->db->query($sql);
		$roles 				= $query->result();

		$supervisor_level 	= null;
		$superadmin_level 	= null;
		$current_level 		= $this->session->userdata('user_level');
		$current_user 		= $this->session->userdata('user_name');


		foreach ($roles as $role) {
			if ($role->roles_name === 'Supervisor Warehouse') {
				$supervisor_level = (int)$role->idroles;
			} elseif ($role->roles_name === 'Superadmin') {
				$superadmin_level = (int)$role->idroles;
			}
		}



		if ($current_level == $supervisor_level || $current_level == $superadmin_level ||  stripos($current_user, 'novi') !== false) {

			$data['wh_lokasi']      = $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();
		} else {

			$data['wh_lokasi']      = $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack WHERE pic='$current_user' GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();
		}


		$data['group_halaman'] 	= "Warehouse";
		$data['nama_halaman'] 	= "Warehouse Part FIFO";
		$data['icon_halaman'] 	= "icon-airplay";
		$data['perusahaan'] 	= $this->perusahaan->get_details();

		//ADDING TO LOG
		$log_url 	= base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type 	= "VIEW";
		$log_data 	= "";

		log_helper($log_url, $log_type, $log_data);
		//END LOG

		$this->load->view('adminx/warehouse/wh_part_fifo/index', $data, FALSE);
	}


	public function list_data_fifo()
	{

		$part_id   = $this->input->post('part_id');
		$lokasi_id = $this->input->post('lokasi_id');
		$rack_id   = $this->input->post('rack_id');

		$start     = $this->input->post('start');
		$length    = $this->input->post('length');

		// Jika input kosong, kirimkan data kosong
		if (empty($part_id) || empty($lokasi_id) || empty($rack_id)) {
			echo json_encode([
				"draw"            => intval($this->input->post('draw')),
				"recordsTotal"    => 0,
				"recordsFiltered" => 0,
				"data"            => []
			]);
			return;
		}

		$this->BJGMAS01->select("
				tbl_trans_rack.partid,
				tbl_trans_rack.partname,
				tbl_trans_rack.id_rack,
				tbl_trans_rack.id_kolom,
				tbl_trans_rack.tgl_fifo, 
				tbl_trans_rack.fifo, 
				tbl_trans_rack.qty AS qty_in, 
				tbl_trans_rack.qty_remaining, 
				tbl_trans_rack.units, 
				tbl_trans_rack.noted, 
				ms_rack.nama_rack, 
				ms_rack.wh_lokasi
			");
		$this->BJGMAS01->from("tbl_trans_rack");
		$this->BJGMAS01->join("ms_rack", "ms_rack.id_rack = tbl_trans_rack.id_rack", "left");
		$this->BJGMAS01->where("type_trans", 'IN');
		$this->BJGMAS01->order_by("tbl_trans_rack.partname", "ASC"); // <-- Urutkan berdasarkan partname A-Z
		if ($part_id !== 'all') {
			$this->BJGMAS01->where("partid", $part_id);
		}

		if ($lokasi_id !== 'all') {
			$this->BJGMAS01->where("tbl_trans_rack.wh_lokasi", $lokasi_id);
		}

		if ($rack_id !== 'all') {
			$this->BJGMAS01->where("tbl_trans_rack.id_rack", $rack_id);
		}

		$this->BJGMAS01->order_by("tgl_fifo", "ASC");

		// Hitung total record sebelum limit
		$clone = clone $this->BJGMAS01;
		$recordsTotal = $clone->count_all_results();

		$this->BJGMAS01->limit($length, $start);
		$query  = $this->BJGMAS01->get();
		$result = $query->result();

		$data = [];
		$no = $start;
		foreach ($result as $row) {
			$lihat 	=  $row->id_rack . '|' . $row->id_kolom . '|' . $row->partid . '|' . $row->wh_lokasi;
			$no++;
			$tgl_fifo = !empty($row->tgl_fifo) ? date('d-m-Y', strtotime($row->tgl_fifo)) : null;

			$data[] = [
				'partname'  => 'PartID : ' . $row->partid . '<br>' . 'PartName : ' . $row->partname ?? '', // Assuming partname is not selected in the query
				'fifo'      => $this->get_fifo($row->fifo, $tgl_fifo),
				'pa'      	=> $row->partname,
				'in'        => $row->qty_in,
				'units'     => $row->units,
				'remaining' => $row->qty_remaining,
				// 'rack'      =>  $row->nama_rack,
				'rack'      => '<a href="' . base_url() . 'warehouse_part/warehouse_mapping/' . base64_encode($lihat) . '" target="_blank" >' . $row->nama_rack . '</a>',
				'noted'      => $row->noted,
				'lokasi'    => $row->wh_lokasi
			];
		}


		$output = [
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsTotal,
			"data"            => $data
		];

		echo json_encode($output);
	}

	// WAREHOUSE SISA MPR #################################

	public function sisa_mpr()
	{


		$data['group_halaman'] 		= "Warehouse";
		$data['nama_halaman'] 		= "Warehouse Sisa MPR";
		$data['icon_halaman'] 		= "icon-airplay";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$data['wh_lokasi']      	= $this->BJGMAS01->query("SELECT wh_lokasi FROM ms_rack GROUP BY wh_lokasi ORDER BY wh_lokasi ASC")->result();

		$this->load->view('adminx/warehouse/sisa_mpr/index', $data, FALSE);
	}

	public function lihat_sisa_mpr()
	{

		// ambil data dari POST
		$periode = $this->input->post('pilih_periode');  // contoh: "2025-08-13 to 2025-08-20"
		$user    = $this->input->post('user');           // contoh: "nurdimas"
		$lokasi  = $this->input->post('wh_lokasi');      // contoh: "WH-SCRAP00"

		// validasi minimal periode harus ada
		if (empty($periode)) {
			echo json_encode(['data' => []]);
			return;
		}

		if ($user === "ALL") {
			// User ALL → hanya cek periode
			if (empty($periode)) {
				echo json_encode([
					'status'  => 'error',
					'message' => 'Periode harus diisi!'
				]);
				return;
			}
		} else {
			// User biasa → cek semua field
			if (empty($periode) || empty($user) || empty($lokasi)) {
				echo json_encode([
					'status'  => 'error',
					'message' => 'Semua field harus diisi!'
				]);
				return;
			}
		}

		// pecah periode menjadi start_date dan end_date
		$start_date = null;
		$end_date   = null;

		if (!empty($periode)) {
			$dates = explode(" to ", $periode);
			$start_date = isset($dates[0]) ? trim($dates[0]) : null;
			$end_date   = isset($dates[1]) ? trim($dates[1]) : null;
			// kalau end_date kosong, samakan dengan start_date
			if (empty($end_date)) {
				$end_date = $start_date;
			}

			// konversi format d-m-Y ke Y-m-d agar sesuai SQL Server
			$start_date = DateTime::createFromFormat('d-m-Y', $start_date)->format('Y-m-d');
			$end_date   = DateTime::createFromFormat('d-m-Y', $end_date)->format('Y-m-d');
		}

		// base query
		$sql = "
				SELECT 
				 	s.id_sisa_mpr,
					a.id_rack,
					b.nama_rack,
					a.id_kolom,
					c.nama_kolom,
					a.wh_lokasi,
					a.partid,
					a.partname,
					s.sisa_mpr,
					s.job,
					a.units,
					s.no_transaksi_united,
					s.created_by,
					s.relasi_sisa_mpr,
					s.terima_produksi,
					CONVERT(VARCHAR(19), s.created_date, 120) AS created_date
				FROM tbl_trans_rack a
				JOIN tbl_sisa_mpr s ON a.relasi_sisa_mpr = s.relasi_sisa_mpr
				LEFT JOIN ms_rack b ON a.id_rack = b.id_rack
				LEFT JOIN ms_kolom_rack c ON a.id_kolom = c.id_kolom
				WHERE CAST(s.created_date AS DATE) BETWEEN ? AND ?
			";

		$params = [$start_date, $end_date];

		// kalau user bukan ALL → filter by lokasi + user
		if ($user !== "ALL") {
			$sql .= " AND a.wh_lokasi = ? ";
			$params[] = $lokasi;
		}

		$sql .= "
					ORDER BY 
						s.job ASC;
				";

		$query  	= $this->BJGMAS01->query($sql, $params);
		$result 	= $query->result();

		// Jika untuk DataTables, bisa tambahkan nomor urut, dll
		$data 		= [];
		$no 		= 1;
		foreach ($result as $row) {
			$lihat 	=  $row->id_rack . '|' . $row->id_kolom . '|' . $row->partid . '|' . $row->wh_lokasi;

			// cari periode dari job/mpr (format ada YYYYMM)
			$periode_mpr = null;
			if (preg_match('/\d{6}/', $row->job, $matches)) {
				$periode_mpr = $matches[0]; // contoh 202509
			}

			$partid_mpr   = $row->partid;
			$partname_mpr = $row->partname;

			if ($periode_mpr) {
				$table_mpr = "Trans_MPRHD" . $periode_mpr;

				// cek tabel ada?
				$cekTable = $this->BJGMAS01->query("
                SELECT COUNT(*) AS ada
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_NAME = '$table_mpr'
            ")->row()->ada;

				if ($cekTable > 0) {
					// ambil partid dari Trans_MPRHD + join Ms_Part buat partname
					$mprData = $this->BJGMAS01->query("
                    SELECT TOP 1 h.partid, p.partname
                    FROM $table_mpr h
                    JOIN Ms_Part p ON h.partid = p.partid
                    WHERE h.NoBukti = ? or h.NoBuktiJob = ?
                ", [$row->job, $row->job])->row();

					if ($mprData) {
						$partid_mpr   = $mprData->partid;
						$partname_mpr = $mprData->partname;
					}
				}
			}

			$data[] = [
				// hasil lookup dari MPR
				'partid_mpr'       => $partid_mpr,
				'partname_mpr'     => $partname_mpr,
				'group_label'       => $row->job . ' - (' . $partname_mpr . ')',

				'id_sisa_mpr'    => $row->id_sisa_mpr,
				'no'         	=> $no++,
				'id_rack'    	=> $row->id_rack,
				'nama_rack'  	=> '<a href="' . base_url() . 'warehouse_part/warehouse_mapping/' . base64_encode($lihat) . '" target="_blank" >' . $row->nama_rack . '</a>',
				'id_kolom'   	=> $row->id_kolom,
				'nama_kolom' 	=> '<a href="' . base_url() . 'warehouse_part/warehouse_mapping/' . base64_encode($lihat) . '" target="_blank" >' . $row->nama_kolom . '</a>',
				'partid'     	=> $row->partid,
				'partname'   	=> $row->partname,
				'qty' 			=> '<a href="javascript:void(0)" style="font-size:14px; font-weight:bold;" class="edit-qty" data-relasi="' . $row->relasi_sisa_mpr . '" data-qty="' . $row->sisa_mpr . '">'	. number_format($row->sisa_mpr, 2, ",", ".") . '</a>',
				'units'      	=> $row->units,
        'job'   		=> $row->job,
				'status'	 	=> $this->getStatusWarnaSisampr($row->no_transaksi_united),
				'terima_produksi'	 	=> $this->getStatusWarnaSisampr($row->terima_produksi),
				'no_transaksi' 	=> $row->no_transaksi_united,
				'created_by' 	=> $row->created_by,
				'created_date' 	=> $row->created_date,
			];
		}


		echo json_encode(['data' => $data]);
	}

	public function getStatusWarnaSisampr($no_transaksi)
	{
		if ($no_transaksi == null || $no_transaksi == '') {
			return '<div style="background-color:#dc3545; width:54px; height:24px; border-radius:4px;"></div>';
		} else {
			return '<div style="background-color:#28a745; width:54px; height:24px; border-radius:4px;"></div>';
		}
	}

	public function input_transaksi_united()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$str       		= $this->input->post('job_no');
			$no_transaksi 	= $this->input->post('no_transaksi');

			// pecah pakai delimiter " - "
			$parts = explode(" - ", $str);

			// ambil elemen pertama (array index 0)
			$job_no = $parts[0];

			if (!$job_no || !$no_transaksi) {
				echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
				return;
			}

			// Update ke tabel
			$this->BJGMAS01->where('job', $job_no);
			$this->BJGMAS01->update('tbl_sisa_mpr', [
				'no_transaksi_united' 	=> $no_transaksi,
				'updated_date'    		=> date('Y-m-d H:i:s'),
				'updated_by'      		=> $this->session->userdata('user_code')
			]);

			if ($this->BJGMAS01->affected_rows() > 0) {
				echo json_encode(['status' => 'success', 'message' => 'Update berhasil']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diupdate']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Tidak ada hak akses']);
		}
	}

	public function update_qty_mpr()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$relasi_sisa_mpr = $this->input->post('relasi_id') ?? null;
			$qty_new         = $this->input->post('qty') ?? null;
			$qty_old         = $this->input->post('oldQty') ?? null;

			if (!$relasi_sisa_mpr || !$qty_new || !$qty_old) {
				echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
				return;
			}

			if ($qty_new <= 0) {
				echo json_encode(['status' => 'error', 'message' => 'Qty harus lebih dari 0']);
				return;
			}

			if ($qty_new == $qty_old) {
				echo json_encode(['status' => 'error', 'message' => 'Tidak ada perubahan pada qty']);
				return;
			}

			$this->BJGMAS01->trans_begin();

			// 1. Cari transaksi OUT berdasarkan relasi_sisa_mpr
			$trans_out = $this->BJGMAS01->get_where('tbl_trans_rack', ['relasi_sisa_mpr' => $relasi_sisa_mpr])->row();
			if (!$trans_out) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode(['status' => 'error', 'message' => 'Data transaksi tidak ditemukan']);
				return;
			}

			$id_out = $trans_out->id_trans_rack;


			// 2. Ambil log FIFO lama
			$detail_lama = $this->BJGMAS01->get_where('tbl_detail_fifo', ['out_id' => $id_out])->result();

			// 3. Kembalikan qty_remaining ke IN terkait
			foreach ($detail_lama as $row) {
				$this->BJGMAS01->query("
											UPDATE tbl_trans_rack
											SET qty_remaining = qty_remaining + ?
											WHERE id_trans_rack = ?
										", [$row->qty_used, $row->in_id]);
			}

			// 4. Hapus log FIFO lama
			$this->BJGMAS01->delete('tbl_detail_fifo', ['out_id' => $id_out]);

			// 5. Update qty baru di tbl_trans_rack & tbl_sisa_mpr
			$this->BJGMAS01->update('tbl_trans_rack', ['qty' => -$qty_new], ['relasi_sisa_mpr' => $relasi_sisa_mpr]);
			$this->BJGMAS01->update('tbl_sisa_mpr', ['sisa_mpr' => $qty_new], ['relasi_sisa_mpr' => $relasi_sisa_mpr]);

			// 6. Alokasi ulang FIFO otomatis
			$fifo_ok = $this->proses_fifo_automatis($id_out, $trans_out->partid, $trans_out->id_rack, $trans_out->id_kolom, $qty_new);
			if (!$fifo_ok) {
				$this->BJGMAS01->trans_rollback();
				echo json_encode(['status' => 'error', 'message' => 'Stok tidak cukup untuk qty baru']);
				return;
			}

			// 7. Commit transaksi
			$this->BJGMAS01->trans_commit();

			echo json_encode(['status' => 'success', 'message' => 'Qty berhasil diperbarui']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Tidak ada hak akses']);
		}
	}


	public function update_nobuktiunited()
	{
		// CHECK FOR ACCESS FOR EACH FUNCTION

		$user_level 		= $this->session->userdata('user_level');
		$check_permission 	= $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

		if ($check_permission->num_rows() != 1) {
			echo json_encode(['status' => 'error', 'message' => 'Anda tidak punya hak akses']);
			return;
		}
		// END CHECK

		$log_queries 		= []; // untuk simpan query yang dijalankan
		$ids 				= $this->input->post('ids'); // array dari AJAX
		$no_transaksi 		= $this->input->post('no_transaksi');

		if (!empty($ids) && is_array($ids)) {

			// update semua id yang dipilih
			$this->BJGMAS01->where_in('id_sisa_mpr', $ids);
			$update = $this->BJGMAS01->update('tbl_sisa_mpr', [
				'no_transaksi_united' 	=> $no_transaksi,
				'updated_date'    		=> date('Y-m-d H:i:s'),
				'updated_by'      		=> $this->session->userdata('user_code')
			]);
			$log_queries[] = $this->BJGMAS01->last_query(); // simpan query UPDATE


			if ($update) {
				echo json_encode(['status' => 'success', 'message' => 'Update berhasil']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diupdate']);
			}
		} else {
			echo json_encode([
				"status" 	=> "error",
				"message" 	=> "Tidak ada data yang dipilih"
			]);
		}

		// ADDING TO LOG
		$log_url   = base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type  = "UPDATE";
		$log_data  = implode(";\n", $log_queries); // gabungkan query2 jadi string

		log_helper($log_url, $log_type, $log_data);
		// END ADDING TO LOG

	}
}
