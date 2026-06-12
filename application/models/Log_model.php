<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function save($data)
  {
    $sql  = $this->db->insert_string('table_logs', $data);
    $ex   = $this->db->query($sql);
    
    return $this->db->affected_rows($sql);
  }
}