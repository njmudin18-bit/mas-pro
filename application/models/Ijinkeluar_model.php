<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ijinkeluar_model extends CI_Model
{
  var $table          = 'Trans_IjinKeluar';
  var $column_order   = array('a.Id', 'a.Nomor', 'a.EmployeeID', 'NAMES', 'c.DEFAULTDEPTID', 'd.DEPTNAME', 'b.AbsenceName', 'b.AbsenceCode', 'a.AbsenceTypeID', 'a.StartDate', 'a.EndDate', 'a.TotalDays', 'a.Notes', 'a.Files', 'a.isApproved', 'a.CreatedDate', 'a.CreatedBy', 'a.ApprovedBy', null);
  var $column_search  = array('a.Id', 'a.Nomor', 'a.EmployeeID', 'NAMES', 'c.DEFAULTDEPTID', 'd.DEPTNAME', 'b.AbsenceName', 'b.AbsenceCode', 'a.AbsenceTypeID', 'a.StartDate', 'a.EndDate', 'a.TotalDays', 'a.Notes', 'a.Files', 'a.isApproved', 'a.CreatedDate', 'a.CreatedBy', 'a.ApprovedBy');
  var $order          = array('a.Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  public function generateIjinNumber()
  {
    // Ambil tahun-bulan sekarang
    $yearMonth = date('Ym');
    // Prefix tetap
    $prefix    = "HRIK" . $yearMonth . "-";
    // Ambil nomor terakhir yang sesuai prefix
    $this->ABSENSI->select('Nomor');
    $this->ABSENSI->like('Nomor', $prefix, 'after');
    $this->ABSENSI->order_by('Nomor', 'DESC');
    $this->ABSENSI->limit(1);
    $query = $this->ABSENSI->get('Trans_IjinKeluar');

    $lastNumber = '';
    if ($query->num_rows() > 0) {
      $lastNumber = $query->row()->Nomor;
    }

    $sequence = 1;
    if (!empty($lastNumber)) {
      $parts = explode('-', $lastNumber);
      if (count($parts) > 1) {
        $lastSequence = (int)$parts[1];
        $sequence     = $lastSequence + 1;
      }
    }

    $newSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    return $prefix . $newSequence;
  }

  function get_datatables($StartDate, $EndDate, $DeptShow)
  {
    $this->_get_datatables_query($StartDate, $EndDate, $DeptShow);
    if ($_POST['length'] != -1)
    $this->ABSENSI->limit($_POST['length'], $_POST['start']);
    $query = $this->ABSENSI->get();
    
    return $query->result();
  }

  function count_filtered($StartDate, $EndDate, $DeptShow)
  {
    $this->_get_datatables_query($StartDate, $EndDate, $DeptShow);
    $query = $this->ABSENSI->get();

    return $query->num_rows();
  }

  public function count_all()
  {
    $this->ABSENSI->from($this->table);

    return $this->ABSENSI->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->ABSENSI->select("a.Id, a.Nomor, a.EmployeeID, UPPER(b.NAME) AS NAME,
                            a.DeptID, c.DEPTNAME,
                            a.Kembali, a.Keperluan,
                            a.Tanggal,
                            CONVERT(TIME(0), a.JamPergi) AS JamPergi,
                            a.Notes");
    $this->ABSENSI->from('Trans_IjinKeluar a');
    $this->ABSENSI->join('USERINFO b', 'b.SSN = a.EmployeeID', 'left');
    $this->ABSENSI->join('DEPARTMENTS c', 'c.DEPTID = b.DEFAULTDEPTID', 'left');
    $this->ABSENSI->where('a.Id', $id);
    $query = $this->ABSENSI->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->ABSENSI->insert($this->table, $data);

    return $this->ABSENSI->insert_id();
  }

  public function update($where, $data)
  {
    $this->ABSENSI->update($this->table, $data, $where);

    return $this->ABSENSI->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->ABSENSI->where('Id', $id);
    $this->ABSENSI->delete($this->table);
  }
}