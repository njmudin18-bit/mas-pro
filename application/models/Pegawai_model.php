<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
  var $table          = 'USERINFO';
  var $column_order   = array('SSN', 'DEPTNAME', 'NAME', 'JOB_TITLE', 'GENDER', 'PEGAWAI_SHIFT', 'SHIFTNAME', 'BOD', 'HIREDDAY', 'STREET', null);
  var $column_search  = array('SSN', 'DEPTNAME', 'NAME', 'JOB_TITLE', 'GENDER', 'PEGAWAI_SHIFT', 'SHIFTNAME', 'BOD', 'HIREDDAY', 'STREET');
  var $order          = array('DEPTNAME' => 'asc', 'NAME' => 'asc');


  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  private function _get_datatables_query($DeptShow = null)
  {
    $this->ABSENSI->select("
        a.USERID, 
        a.SSN, 
        b.DEPTNAME, 
        UPPER(a.NAME) AS NAME,
        CASE
            WHEN a.GENDER = 'M' THEN 'PRIA'
            WHEN a.GENDER = 'F' THEN 'WANITA'
            ELSE 'UNKNOWN'
        END AS GENDER,
        CASE 
            WHEN a.OPHONE IS NULL OR LTRIM(RTRIM(a.OPHONE)) = '' THEN 'SHIFT'
            ELSE 'NON-SHIFT'
        END AS PEGAWAI_SHIFT,
        UPPER(c.ShiftName) AS SHIFTNAME,
        'XXXX-' + RIGHT(CONVERT(CHAR(10), a.BIRTHDAY, 120), 5) AS BOD,
        CAST(a.HIREDDAY AS DATE) AS HIREDDAY,
        a.OPHONE,
        a.FPHONE,
        CASE
          WHEN a.ZIP = 'Y' THEN 'TERDAFTAR'
          ELSE 'NO'
        END AS BPJS,
        CASE
          WHEN a.ZIP = 'Y' THEN 'checked'
          ELSE ''
        END AS BPJS_CHECKED,
        UPPER(d.JobTitle) AS JOB_TITLE,
        FORMAT(d.BasicSalary, 'N2', 'id-ID') AS SALARY,
        a.ZIP,
        a.DEFAULTDEPTID,
        UPPER(a.STREET) AS STREET
    ");
    $this->ABSENSI->from('USERINFO a');
    $this->ABSENSI->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->ABSENSI->join('ShiftSetting c', 'c.ShiftID = a.OPHONE', 'left');
    $this->ABSENSI->join('Ms_BasicSalary d', 'd.BasicSalaryID = a.FPHONE', 'left');
    // filter DeptShow (multi select)
    if (!empty($DeptShow) && is_array($DeptShow)) {
      $this->ABSENSI->where_in('b.DEPTID', $DeptShow);
    }
    // WHERE condition
    $this->ABSENSI->where("(a.SSN LIKE '001%' OR a.SSN = '0022012030019')", NULL, FALSE);
    $this->ABSENSI->where("b.DEPTNAME NOT LIKE '%PT MAIN%'", NULL, FALSE);
    $this->ABSENSI->where("b.DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'", NULL, FALSE);

    // Kolom yang bisa dicari (gunakan ekspresi asli, bukan alias!)
    $column_search = array(
      'a.SSN',
      'b.DEPTNAME',
      'a.NAME',
      "CASE WHEN a.GENDER = 'M' THEN 'PRIA' WHEN a.GENDER = 'F' THEN 'WANITA' ELSE 'UNKNOWN' END",
      "CASE WHEN a.OPHONE IS NULL OR LTRIM(RTRIM(a.OPHONE)) = '' THEN 'SHIFT' ELSE 'NON-SHIFT' END",
      'c.ShiftName',
      'd.JobTitle',
      "RIGHT(CONVERT(CHAR(10), a.BIRTHDAY, 120), 5)", // hanya MM-DD yang dicari
      'a.HIREDDAY',
      'a.STREET'
    );

    $i = 0;
    foreach ($column_search as $item) // loop column
    {
        if (!empty($_POST['search']['value'])) 
        {
            if ($i === 0) 
            {
                $this->ABSENSI->group_start();
                $this->ABSENSI->where("$item LIKE '%".$_POST['search']['value']."%'", NULL, FALSE);
            } 
            else 
            {
                $this->ABSENSI->or_where("$item LIKE '%".$_POST['search']['value']."%'", NULL, FALSE);
            }

            if (count($column_search) - 1 == $i)
                $this->ABSENSI->group_end();
        }
        $i++;
    }

    // ORDER
    if (isset($_POST['order'])) 
    {
        $order_column = $_POST['order']['0']['column'];
        $order_dir    = $_POST['order']['0']['dir'];

        // mapping kolom sesuai index DataTables
        $column_order = array(
            'a.SSN',
            'b.DEPTNAME',
            'a.NAME',
            'GENDER',
            'PEGAWAI_SHIFT',
            'SHIFTNAME',
            'BOD',
            'a.HIREDDAY',
            'STREET'
        );

        if (isset($column_order[$order_column])) {
            $this->ABSENSI->order_by($column_order[$order_column], $order_dir);
        }
    } 
    else 
    {
        // default order
        $this->ABSENSI->order_by('b.DEPTNAME', 'ASC');
        $this->ABSENSI->order_by('a.NAME', 'ASC');
    }
  }

  function get_datatables($DeptShow = null)
  {
    $this->_get_datatables_query($DeptShow);
    if ($_POST['length'] != -1)
      $this->ABSENSI->limit($_POST['length'], $_POST['start']);
    $query = $this->ABSENSI->get();
    
    return $query->result();
  }

  function count_filtered($DeptShow = null)
  {
    $this->_get_datatables_query($DeptShow);
    $query = $this->ABSENSI->get();

    return $query->num_rows();
  }

  public function count_all($DeptShow = null)
  {
    $this->ABSENSI->from($this->table);
    if (!empty($DeptShow) && is_array($DeptShow)) {
      $this->ABSENSI->where_in('DEFAULTDEPTID', $DeptShow);
    }

    return $this->ABSENSI->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->where('AllowanceID', $id);
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
    $this->ABSENSI->where('AllowanceID', $id);
    $this->ABSENSI->delete($this->table);
  }
}
