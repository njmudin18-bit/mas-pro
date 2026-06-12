<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuanot_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function generateRequestNumber()
  {
    // Get current year and month
    $yearMonth  = date('Ym');
    $prefix     = 'REQOT' . $yearMonth . '-';

    $this->Attendance->select('Nomor');
    $this->Attendance->like('Nomor', $prefix, 'after');
    $this->Attendance->order_by('Nomor', 'DESC');
    $this->Attendance->limit(1);
    $query = $this->Attendance->get('Trans_OvertimeHD');

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

  public function get_by_id($Nomor)
  {
    $this->Attendance->select('HeaderID, Nomor, DeptID, EmployeeID, StartTime, EndTime, Notes');
    $this->Attendance->from('Trans_OvertimeHD');
    $this->Attendance->where('Nomor', $Nomor);
    $Query = $this->Attendance->get();

    return $Query->row();
  }

  public function delete_by_id($Nomor)
  {
    $this->Attendance->trans_start();
    $this->Attendance->delete('Trans_OvertimeDT', ['Nomor' => $Nomor]);
    $this->Attendance->delete('Trans_OvertimeHD', ['Nomor' => $Nomor]);
    $this->Attendance->trans_complete();

    // Kembalikan status transaksi langsung
    return $this->Attendance->trans_status();
  }
}
