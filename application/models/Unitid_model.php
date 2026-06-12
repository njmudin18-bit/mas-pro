<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Unitid_model extends CI_Model
{ 
  var $table   = 'Ms_Unit';
  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function get_all_data()
  {
    $this->BJGMAS01->select("UnitID, UnitName");
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where("UnitName <> ''");
    $this->BJGMAS01->where("UnitName !=", "-");
    $this->BJGMAS01->order_by('UnitName', 'ASC');
    $query = $this->BJGMAS01->get();

    return $query->result();
  }
}