<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trial_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
    $this->ABSENSI  = $this->load->database('absensi_local_mas', TRUE);
  }

  public function get_data_laporan($Nomor)
  {
    $Sql      = "EXEC dbo.GetTrialDataByID @Nomor = '$Nomor'";
    $Query    = $this->BJGMAS01->query($Sql);

    return $Query->row();
  }

  public function get_formula($Search)
  {
    $Sql      = "SELECT FormulaID, PartID, Keterangan
                 FROM Ms_FormulaHd
                 WHERE FormulaID LIKE '%$Search%'
                 OR PartID LIKE '%$Search%'";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'FormulaID'     => $row->FormulaID,
        'PartID'        => $row->PartID,
        'Keterangan'    => $row->Keterangan
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }

  public function get_unit($Search)
  {
    $Sql      = "SELECT UnitID, UnitName
                 FROM Ms_Unit
                 WHERE UnitName LIKE '%$Search%'
                 OR UnitID LIKE '%$Search%'";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'UnitID'     => $row->UnitID,
        'UnitName'   => $row->UnitName,
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }

  public function get_part($Search)
  {
    $Sql      = "SELECT PartID, PartName
                 FROM Ms_Part a
                 WHERE PartName LIKE '%$Search%'
                 OR PartID LIKE '%$Search%'";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'PartID'     => $row->PartID,
        'PartName'   => $row->PartName,
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }

  public function get_user_dept($Search)
  {
    $Sql        = "SELECT 
                    a.USERID, SSN, UPPER(a.NAME) AS NAME, DEFAULTDEPTID, b.DEPTNAME
                   FROM USERINFO a
                   LEFT JOIN DEPARTMENTS b ON b.DEPTID = a.DEFAULTDEPTID
                   WHERE b.DEPTNAME = '$Search'
                   ORDER BY a.NAME";
    $Query    = $this->ABSENSI->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'USERID'     => $row->USERID,
        'NAME'       => $row->NAME,
        'SSN'        => $row->SSN
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }

  public function generateTrialNumber()
  {
    // Get current year and month
    $yearMonth  = date('Ym');
    $prefix     = 'TRL' . $yearMonth . '-';

    $this->BJGMAS01->select('Nomor');
    $this->BJGMAS01->like('Nomor', $prefix, 'after');
    $this->BJGMAS01->order_by('Nomor', 'DESC');
    $this->BJGMAS01->limit(1);
    $query = $this->BJGMAS01->get('Trans_TrialProductHD');

    $lastNumber = '';
    if ($query->num_rows() > 0) {
      $row        = $query->row();
      $lastNumber = $row->Nomor;
    }

    $sequence = 1;
    if (!empty($lastNumber)) {
      $parts = explode('-', $lastNumber);
      if (count($parts) > 1) {
        $lastSequence = (int)$parts[1];
        $sequence     = $lastSequence + 1;
      }
    }

    // Format the sequence with leading zeros (e.g., 1 becomes 001, 12 becomes 012)
    $newSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    return $prefix . $newSequence;
  }

  public function get_hd_by_id($Nomor)
  {
    $this->BJGMAS01->select('a.Id, a.Nomor, a.PartID, b.PartName, a.FormulaID,
                             a.Type, a.Proses, a.JenisMaterial, a.Machine,
                             CAST(a.Quantity AS INT) AS Quantity, 
                             a.UnitID, d.UnitName,
                             a.Files, c.PartID AS PartIDFormula, 
                             c.Keterangan AS KeteranganFormula,
                             a.ProcessDate, a.Shift, a.Noted');
    $this->BJGMAS01->from('Trans_TrialProductHD a');
    $this->BJGMAS01->join('Ms_Part b', 'b.PartID = a.PartID', 'left');
    $this->BJGMAS01->join('Ms_FormulaHd c', 'c.FormulaID = a.FormulaID', 'left');
    $this->BJGMAS01->join('Ms_Unit d', 'd.UnitID = a.UnitID', 'left');
    $this->BJGMAS01->where('Nomor', $Nomor);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }
}