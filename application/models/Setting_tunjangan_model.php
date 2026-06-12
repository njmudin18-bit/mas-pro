<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_tunjangan_model extends CI_Model
{
  var $table = 'Trans_Tunjangan';

  public function __construct()
  {
    parent::__construct();

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function save($data)
  {
    $this->Attendance->insert($this->table, $data);

    return $this->Attendance->insert_id();
  }

  public function update($where, $data)
  {
    $this->Attendance->update($this->table, $data, $where);

    return $this->Attendance->affected_rows();
  }

  public function get_by_id($Id)
  {
    $this->Attendance->select('TunjanganID, IsActive, DeptID, EmployeeID, Period, AllowanceID, Keterangan');
    $this->Attendance->from('Trans_Tunjangan');
    $this->Attendance->where('TunjanganID', $Id);
    $Query = $this->Attendance->get();

    return $Query->row();
  }

  public function delete_by_id($Id)
  {
    $this->Attendance->where('TunjanganID', $Id);
    $this->Attendance->delete($this->table);
  }

  public function generateGroupNomor()
  {
    $prefix = "TJG";
    $year   = date('Y');

    $query = $this->Attendance->query("
      SELECT TOP 1 Nomor
      FROM Trans_TunjanganGroupHD
      WHERE Nomor LIKE '$prefix-$year%'
      ORDER BY Nomor DESC
    ");

    $lastNumber = 0;

    if ($query->num_rows() > 0) {
      $row        = $query->row();
      $lastNomor  = $row->Nomor;
      $lastNumber = (int) substr($lastNomor, -4);
    }

    $nextNumber    = $lastNumber + 1;
    $nextNumberStr = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $newNomor      = $prefix . '-' . $year . $nextNumberStr;

    return $newNomor;
  }

  public function get_group_by_id($Nomor)
  {
    $this->Attendance->select('*');
    $this->Attendance->from('Trans_TunjanganGroupHD');
    $this->Attendance->where('Nomor', $Nomor);
    $Query = $this->Attendance->get();

    return $Query->row();
  }

  public function get_group_all()
  {
    $this->Attendance->select('HeaderID, Nomor, UPPER(GroupName) AS GroupName, IsActive');
    $this->Attendance->from('Trans_TunjanganGroupHD');
    $this->Attendance->where('IsActive', 'A');
    $this->Attendance->order_by('GroupName', 'ASC');
    $Query = $this->Attendance->get();

    return $Query->result();
  }

  public function delete_group_by_id($Nomor)
  {
    // Mulai transaksi
    $this->Attendance->trans_begin();

    try {
      // Hapus dari tabel DT terlebih dahulu
      $this->Attendance->where('Nomor', $Nomor);
      $this->Attendance->delete('Trans_TunjanganGroupDT');

      // Hapus dari tabel HD
      $this->Attendance->where('Nomor', $Nomor);
      $this->Attendance->delete('Trans_TunjanganGroupHD');

      // Cek status transaksi
      if ($this->Attendance->trans_status() === FALSE) {
        // Jika ada error, rollback
        $this->Attendance->trans_rollback();

        return false;
      } else {
        // Commit jika berhasil
        $this->Attendance->trans_commit();

        return true;
      }
    } catch (Exception $e) {
      // Rollback jika exception
      $this->Attendance->trans_rollback();

      return false;
    }
  }
}
