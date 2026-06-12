<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perusahaan_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_details()
	{
		$query 	= $this->db->query("SELECT TOP 1 * FROM table_perusahaan
																WHERE status = 'Up'
															  ORDER BY id");
		$result = $query->row();

		return $result;
	}

	public function get_details_old()
	{
		$query 	= $this->db->query("SELECT * FROM table_perusahaan
																WHERE status = 'Up'
															  ORDER BY id DESC LIMIT 1");
		$result = $query->row();

		return $result;
	}
}