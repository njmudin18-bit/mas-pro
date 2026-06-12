<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barcode_model extends CI_Model
{

	var $table = 'tbl_scanbarcode';
	var $order = array('createtime' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_by_id($id)
	{

		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->get_where('tbl_scanbarcode', array('barcodeid' => $id));

		return $query->result();
	}

	public function get_data_print_do_new($barcode, $part_id, $nomor_do, $qty_order)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$where = array(
			'barcodeid' 	=> $barcode,
			'partid' 			=> $part_id,
			'nodo' 				=> $nomor_do,
			'qtyorder' 		=> $qty_order
		);
		$second_DB->select('*');
		$second_DB->from('tbl_printqrcodedo');
		$second_DB->where($where);
		$query 	= $second_DB->get();
		$cek 		= $query->num_rows();

		if ($cek > 0) {
			return $query->result();
		} else {
			$where2 = array(
				'barcodeid' 	=> $barcode,
				'partid' 			=> $part_id,
				'nodo' 				=> $nomor_do,
				'qtyorder' 		=> $qty_order
			);
			$second_DB->select('*');
			$second_DB->from('tbl_printqrcodedoulang');
			$second_DB->where($where2);
			$query2 	= $second_DB->get();
			$cek2 		= $query2->num_rows();

			return $query2->result();
		}
	}

  public function get_data_header($barcode)
  {
    $second_DB 	= $this->load->database('bjsmas01_db', TRUE);
    $query      = $second_DB->get_where('tbl_scanbarcode_approval', array('barcode_id' => $barcode));

    return $query->row();
  }

  public function get_data_detail($PartID, $PoNumber, $DoNumber)
  {
    $second_DB 	= $this->load->database('bjsmas01_db', TRUE);

    $second_DB->select('*');
    $second_DB->from('tbl_scanbarcode_approval_dt');
    $second_DB->where('PONumber', $PoNumber);
    $second_DB->where('DONumber', $DoNumber);
    $second_DB->where("LEFT(BarcodeID, CHARINDEX('|', BarcodeID + '|') - 1) = '$PartID'", NULL, FALSE);
    $query = $second_DB->get();

    return $result = $query->result();
  }

  public function get_data_detail_OLD($PartID, $BarcodeID, $PoNumber, $DoNumber)
  {
    $second_DB 	= $this->load->database('bjsmas01_db', TRUE);

    $second_DB->select('*');
    $second_DB->from('tbl_scanbarcode_approval_dt');
    $second_DB->where('PONumber', $PoNumber);
    $second_DB->where('DONumber', $DoNumber);
    $second_DB->where('BarcodeID', $BarcodeID);
    $query = $second_DB->get();

    return $result = $query->result();
  }

  public function get_driver($PoNumber, $DoNumber, $PartID) 
  {
    $second_DB 	= $this->load->database('bjsmas01_db', TRUE);
    $Sql        = "EXEC dbo.GetDriverName @PONumber = ?, @DONumber = ?, @PartID = ?";
    $Query      = $second_DB->query($Sql, array($PoNumber, $DoNumber, $PartID));

    return is_object($Query) ? $Query->result() : array();
  }

	public function get_data_print_do($barcode, $part_id, $nomor_do, $qty_order)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$where = array(
			'barcodeid' 	=> $barcode,
			'partid' 			=> $part_id,
			'nodo' 				=> $nomor_do,
			'qtyorder' 		=> $qty_order
		);
		$second_DB->select('*');
		$second_DB->from('tbl_printqrcodedo');
		$second_DB->where($where);
		$query = $second_DB->get();

		return $query->result();
	}

	public function get_part_terbaru($id)
	{
		$second_DB 	= $this->load->database('bjsmas01_db', TRUE);
		//CEK DULU APAKAH ADA
		$q1 				= $second_DB->query("SELECT TOP 1 * FROM tbl_printqrcodedo
																		 WHERE barcodeid = '$id'
																		 ORDER BY createtime DESC");
		$cek_1 			= $q1->num_rows();
		if ($cek_1 > 0) {
			$res_1 		= $q1->result();

			return $res_1;
		} else {
			//CEK LAGI
			$q2 				= $second_DB->query("SELECT TOP 1 * FROM tbl_printqrcodedoulang 
																			 WHERE barcodeid = '$id'
																			 ORDER BY createtime DESC");
			$cek_2 			= $q2->num_rows();
			if ($cek_2 > 0) {
				$res_2 		= $q2->result();

				return $res_2;
			} else {

				return array();
			}
		}
	}

	public function get_part_terbaru_list($id, $no_do, $no_po)
	{
		$second_DB 	= $this->load->database('bjsmas01_db', TRUE);
		//CEK DULU APAKAH ADA
		$q1 				= $second_DB->query("SELECT TOP 1 * FROM tbl_printqrcodedo
																		 WHERE barcodeid = '$id'
																		 AND nodo = '$no_do' 
																		 AND pocustomer = '$no_po'
																		 ORDER BY createtime DESC");
		$cek_1 			= $q1->num_rows();
		if ($cek_1 > 0) {
			$res_1 		= $q1->result();

			return $res_1;
		} else {
			//CEK LAGI
			$q2 				= $second_DB->query("SELECT TOP 1 * FROM tbl_printqrcodedoulang 
																			 WHERE barcodeid = '$id'
																			 AND nodo = '$no_do' AND pocustomer = '$no_po'
																			 ORDER BY createtime DESC");
			$cek_2 			= $q2->num_rows();
			if ($cek_2 > 0) {
				$res_2 		= $q2->result();

				return $res_2;
			} else {

				return array();
			}
		}
	}

	public function get_newes_part($id)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->limit(1)->order_by('createtime', 'desc')->get_where('tbl_printqrcodedo', array('barcodeid' => $id));

		return $query->result();
	}

	public function get_newes_part_OLD($id)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->limit(1)->order_by('createtime', 'desc')->get_where('tbl_scanbarcode', array('barcodeid' => $id));

		return $query->result();
	}

	public function get_by_id_OLD($id)
	{

		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->get_where('tbl_printqrcodedo', array('barcodeid' => $id));

		return $query->result();
	}

	public function save($data)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$second_DB->insert('tbl_scanbarcode_approval', $data);
		return $second_DB->insert_id();
	}

	public function cek_po_do($no_barcode, $no_do, $no_po)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$where = array(
			'barcode_id' 	=> $no_barcode,
			'no_po' 			=> $no_po,
			'no_do' 			=> $no_do
		);
		$second_DB->select('*');
		$second_DB->from('tbl_scanbarcode_approval');
		$second_DB->where($where);
		$query = $second_DB->get();

		return $query->result();
	}

	public function cek_barcode_po_do($id)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$cek = $this->get_by_id($id);
		//$no_barcode = $cek[0]->barcodeid;
		$no_po = $cek[0]->po;
		$no_do = $cek[0]->nodoc;

		$where = array('no_po' => $no_po, 'no_do' => $no_do);
		$second_DB->select('*');
		$second_DB->from('tbl_scanbarcode_approval');
		$second_DB->where($where);
		$query = $second_DB->get();

		return $query->result();
	}

	public function cek_data_barcode_app($id)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->get_where('tbl_scanbarcode_approval', array('barcode_id' => $id));

		return $query->result();
	}

	public function barang_terkirim_list()
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$query = $second_DB->query("SELECT A.id, A.lokasi_scan, A.create_date, 
                                      A.approved_by, C.Namadivisi, B.* 
                                      FROM tbl_scanbarcode_approval A
                                      LEFT JOIN tbl_scanbarcode B ON A.barcode_id = B.barcodeid
                                      LEFT JOIN tbl_msdivisi C ON A.lokasi_id = C.Iddivisi
                                      ORDER BY A.create_date DESC");
		return $query->result();
	}

	public function get_barcode_by_id($id)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->get_where('tbl_scanbarcode_job', array('scan_id' => $id));

		return $query->result();
	}

	public function cek_exist_dh($barcode_no)
	{
		$second_DB = $this->load->database('bjsmas01_db', TRUE);
		$query = $second_DB->get_where('tbl_scanbarcode_job_dh', array('barcode_no' => $barcode_no));

		return $query->result();
	}
}
