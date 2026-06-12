<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Requestsample_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function get_partner($Search)
  {
    $Sql      = "SELECT PartnerID, PartnerName, Type, Address
                 FROM Ms_Partner a
                 WHERE PartnerName LIKE '%$Search%'
                 OR PartnerID LIKE '%$Search%'";
    $Query    = $this->BJGMAS01->query($Sql);
    $Results  = $Query->result();

    $Data     = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'PartnerID'     => $row->PartnerID,
        'PartnerName'   => $row->PartnerName,
        'Type'          => $row->Type,
        'Address'       => $row->Address
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    return $Data;
  }

  public function generateRequestNumber()
  {
      // Get current year and month
      $yearMonth  = date('Ym'); // e.g., 202507
      $prefix     = 'REQ' . $yearMonth . '-';

      // Query to get the last request number for the current month
      $this->BJGMAS01->select('Nomor');
      $this->BJGMAS01->like('Nomor', $prefix, 'after'); // Find numbers starting with our prefix
      $this->BJGMAS01->order_by('Nomor', 'DESC');
      $this->BJGMAS01->limit(1);
      $query = $this->BJGMAS01->get('Trans_RequestSampleHD');

      $lastNumber = '';
      if ($query->num_rows() > 0) {
        $row        = $query->row();
        $lastNumber = $row->Nomor;
      }

      $sequence = 1;
      if (!empty($lastNumber)) {
        // Extract the sequence part (e.g., '001' from 'REQ202507-001')
        $parts = explode('-', $lastNumber);
        if (count($parts) > 1) {
          $lastSequence = (int)$parts[1]; // Convert to integer
          $sequence     = $lastSequence + 1;
        }
      }

      // Format the sequence with leading zeros (e.g., 1 becomes 001, 12 becomes 012)
      $newSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

      return $prefix . $newSequence;
  }

  public function get_hd_by_id($NoReq)
  {
    $this->BJGMAS01->select('a.Id, a.PartnerID, a.CustomerPartID, a.CustomerPartName,
                            a.Status, a.Prices, a.Etd, a.CustomerCheck,
                            a.CustomerName, a.CustomerAddress,
	                          a.Notes, a.Nomor, b.PartnerName, b.Address');
    $this->BJGMAS01->from('Trans_RequestSampleHD a');
    $this->BJGMAS01->join('Ms_Partner b', 'b.PartnerID = a.PartnerID', 'left');
    $this->BJGMAS01->where('Nomor', $NoReq);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function get_dt_by_id($NoReq)
  {
    $this->BJGMAS01->from('Trans_RequestSampleDT');
    $this->BJGMAS01->where('Nomor', $NoReq);
    $query = $this->BJGMAS01->get();

    return $query->result();
  }
}