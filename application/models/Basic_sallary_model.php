<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Basic_sallary_model extends CI_Model
{
  var $table          = 'Ms_BasicSalary';
  var $column_order   = array('JobCode', 'JobTitle', 'Grade', 'JobLevel', 'BasicSalary', 'SalaryDivider', 'Period', 'EffectiveDate', 'IsActive', 'CreatedDate', 'CreatedBy', null);
  var $column_search  = array('JobCode', 'JobTitle', 'Grade', 'JobLevel', 'BasicSalary', 'SalaryDivider', 'Period', 'EffectiveDate', 'IsActive', 'CreatedDate', 'CreatedBy');
  var $order          = array('BasicSalaryID' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->ABSENSI->select("BasicSalaryID, JobCode, JobTitle, Grade,
                            JobLevel, Period, EffectiveDate, SalaryDivider,
                            FORMAT(BasicSalary, 'N2', 'id-ID') AS BasicSalary,
                            FORMAT(DailySalary, 'N2', 'id-ID') AS DailySalary,
                            CASE 
                              WHEN IsActive = 'A' THEN 'AKTIF'
                              ELSE 'NON AKTIF'
                            END AS IsActive,
                            CONVERT(VARCHAR(19), CreatedDate, 120) AS CreatedDate, CreatedBy");
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

  function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_POST['length'] != -1)
      $this->ABSENSI->limit($_POST['length'], $_POST['start']);
    $query = $this->ABSENSI->get();
    
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
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
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->where('BasicSalaryID', $id);
    $query = $this->ABSENSI->get();

    return $query->row();
  }

  public function get_all_data()
  {
    $this->ABSENSI->select("BasicSalaryID, UPPER(JobTitle) AS JobTitle, Period, IsActive,
	                          FORMAT(BasicSalary, 'N0', 'id-ID') AS BasicSalary");
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->where('IsActive', 'A');
    $this->ABSENSI->order_by('JobTitle', 'ASC');
    $query = $this->ABSENSI->get();

    return $query->result();
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
    $this->ABSENSI->where('BasicSalaryID', $id);
    $this->ABSENSI->delete($this->table);
  }
}
