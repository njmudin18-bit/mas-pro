<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Koreksi_model extends CI_Model
{
  var $table          = 'Trans_KoreksiAbsensi';
  var $column_order   = array('a.AbsenceID', 'a.Nomor', 'a.EmployeeID', 'NAMES', 'c.DEFAULTDEPTID', 'd.DEPTNAME', 'b.AbsenceName', 'b.AbsenceCode', 'a.AbsenceTypeID', 'a.StartDate', 'a.EndDate', 'a.TotalDays', 'a.Notes', 'a.Files', 'a.isApproved', 'a.CreatedDate', 'a.CreatedBy', 'a.ApprovedBy', null);
  var $column_search  = array('a.AbsenceID', 'a.Nomor', 'a.EmployeeID', 'NAMES', 'c.DEFAULTDEPTID', 'd.DEPTNAME', 'b.AbsenceName', 'b.AbsenceCode', 'a.AbsenceTypeID', 'a.StartDate', 'a.EndDate', 'a.TotalDays', 'a.Notes', 'a.Files', 'a.isApproved', 'a.CreatedDate', 'a.CreatedBy', 'a.ApprovedBy');
  var $order          = array('a.AbsenceID' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  public function generateAbsensiNumber()
  {
    // Ambil tahun-bulan sekarang
    $yearMonth = date('Ym'); // contoh: 202509

    // Prefix tetap
    $prefix    = "HRKA" . $yearMonth . "-";

    // Ambil nomor terakhir yang sesuai prefix
    $this->ABSENSI->select('Nomor');
    $this->ABSENSI->like('Nomor', $prefix, 'after');
    $this->ABSENSI->order_by('Nomor', 'DESC');
    $this->ABSENSI->limit(1);
    $query = $this->ABSENSI->get('Trans_KoreksiAbsensi');

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

    return $prefix . $newSequence; // contoh hasil: HRKA202509-001
  }

  private function _get_datatables_query($StartDate, $EndDate, $DeptShow)
  {
    // $this->ABSENSI->select("a.AbsenceID, a.EmployeeID, UPPER(c.NAME) AS NAMES, 
    //                         c.DEFAULTDEPTID, d.DEPTNAME,
    //                         b.AbsenceName, b.AbsenceCode, a.AbsenceTypeID, 
    //                         a.StartDate, a.EndDate, a.TotalDays, a.Nomor,
    //                         a.Notes, a.Files, a.isApproved,
    //                         CASE 
    //                           WHEN a.isApproved = 'Y' THEN 'btn-danger'
    //                           WHEN a.isApproved = 'N' THEN 'btn-warning'
    //                           ELSE 'btn-secondary'
    //                         END AS isApprovedClass,
    //                         CONVERT(VARCHAR(19), a.CreatedDate, 120) AS CreatedDate, a.CreatedBy,
    //                         CONVERT(VARCHAR(19), a.ApprovedDate, 120) AS ApprovedDate,
    //                         a.ApprovedBy,
    //                         ISNULL(UPPER(e.NAME), '-') AS ApprovedName");
    // $this->ABSENSI->from('Trans_EmployeeAbsence a');
    // $this->ABSENSI->join('Ms_AbsenceTypes b', 'b.Id = a.AbsenceTypeID', 'left');
    // $this->ABSENSI->join('USERINFO c', 'c.SSN = a.EmployeeID', 'left');
    // $this->ABSENSI->join('DEPARTMENTS d', 'd.DEPTID = c.DEFAULTDEPTID', 'left');
    // $this->ABSENSI->join('USERINFO e', 'e.SSN = a.ApprovedBy', 'left');
    // $this->ABSENSI->where("CAST(a.CreatedDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'");
    // // filter dept_id kalau ada isinya
    // if (!empty($DeptShow)) {
    //   $this->ABSENSI->where('c.DEFAULTDEPTID', $DeptShow);
    // }

    $this->ABSENSI->from($this->table);

    $i = 0;

    foreach ($this->column_search as $item) // loop column 
    {
      if ($_POST['search']['value']) // if datatable send POST for search
      {

        if ($i === 0) // first loop
        {
          $this->ABSENSI->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->ABSENSI->like($item, $_POST['search']['value']);
        } else {
          $this->ABSENSI->or_like($item, $_POST['search']['value']);
        }

        if (count($this->column_search) - 1 == $i) //last loop
          $this->ABSENSI->group_end(); //close bracket
      }
      $i++;
    }

    if (isset($_POST['order'])) // here order processing
    {
      $this->ABSENSI->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->ABSENSI->order_by(key($order), $order[key($order)]);
    }
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
    $this->ABSENSI->select("a.KoreksiID, a.Nomor, a.EmployeeID, UPPER(b.NAME) AS NAME,
                            b.DEFAULTDEPTID, c.DEPTNAME, a.Tanggal, a.Notes,
                            CASE 
                              WHEN CONVERT(DATE, a.CheckInAsli) = '1970-01-01' THEN NULL 
                              ELSE CONVERT(VARCHAR(19), a.CheckInAsli, 120) 
                            END AS CheckInAsli,
                            CASE 
                              WHEN CONVERT(DATE, a.CheckOutAsli) = '1970-01-01' THEN NULL 
                              ELSE CONVERT(VARCHAR(19), a.CheckOutAsli, 120) 
                            END AS CheckOutAsli,
                            a.ChangeTo AS ColumnChange,
                            CONVERT(VARCHAR(19), a.CheckInKoreksi, 120) AS CheckInKoreksi,
                            CONVERT(VARCHAR(19), a.CheckOutKoreksi, 120) AS CheckOutKoreksi");
    $this->ABSENSI->from('Trans_KoreksiAbsensi a');
    $this->ABSENSI->join('USERINFO b', 'b.SSN = a.EmployeeID', 'left');
    $this->ABSENSI->join('DEPARTMENTS c', 'c.DEPTID = b.DEFAULTDEPTID', 'left');
    $this->ABSENSI->where('a.KoreksiID', $id);
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
    $this->ABSENSI->where('KoreksiID', $id);
    $this->ABSENSI->delete($this->table);
  }
}