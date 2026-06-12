<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supir_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  public function get_nama_supir()
  {
    $this->ABSENSI->select('a.USERID, a.SSN, UPPER(a.NAME) AS NAME, a.DEFAULTDEPTID, b.DEPTNAME, c.IsActive');
    $this->ABSENSI->from('USERINFO a');
    $this->ABSENSI->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->ABSENSI->join('USERINFO_PROPERTIES c', 'c.SSN = a.SSN', 'left');

    $this->ABSENSI->group_start();
    $this->ABSENSI->where_in('a.DEFAULTDEPTID', ['1221', '1177']);
    $this->ABSENSI->or_where('a.SSN', '0022026270495');
    $this->ABSENSI->group_end();

    $this->ABSENSI->where("ISNULL(c.IsActive, '') <> 'O'", NULL, FALSE);
    $this->ABSENSI->where("a.USERID <>", "24054");
    $this->ABSENSI->order_by('a.NAME', 'ASC');

    $Query = $this->ABSENSI->get();

    return $Query->result();
  }

  public function get_nama_supir_OLD()
  {
    $this->ABSENSI->select('a.USERID, a.SSN, UPPER(a.NAME) AS NAME, a.DEFAULTDEPTID, b.DEPTNAME, c.IsActive');
    $this->ABSENSI->from('USERINFO a');
    $this->ABSENSI->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->ABSENSI->join('USERINFO_PROPERTIES c', 'c.SSN = a.SSN', 'left');
    $this->ABSENSI->where_in('a.DEFAULTDEPTID', ['1221', '1177']);
    $this->ABSENSI->where("ISNULL(c.IsActive, '') <> 'O'", NULL, FALSE);
    $this->ABSENSI->where("a.USERID <>", "24054");
    $this->ABSENSI->order_by('a.NAME', 'ASC');
    $Query = $this->ABSENSI->get();

    return $Query->result();
  }

  public function get_groupid($Search)
  {
    $Sql      = "SELECT GroupID
                 FROM Trans_PengeluaranMobilHD
                 WHERE GroupID LIKE '%$Search%'
                 GROUP BY GroupID";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'GroupID'   => $row->GroupID
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }
}