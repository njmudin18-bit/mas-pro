<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$second_DB->select('*');
		$second_DB->from('tbl_msdivisi');
		$second_DB->order_by('Namadivisi', 'ASC');
		$query 			= $second_DB->get();
		$result 		= $query->result();

		return $result;
	}

	public function get_nama_divisi($id)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$second_DB->select('*');
		$second_DB->from('tbl_msdivisi');
		$second_DB->where('Iddivisi', $id);
		$query 			= $second_DB->get();
		$result 		= $query->row();
		
		return $result;
	}
}