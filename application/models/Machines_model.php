<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machines_model extends CI_Model
{
  var $table          = 'Ms_ProductionMachines';
  var $column_order   = array('Id', 'DeptID', 'Name', 'IsActive', 'CreatedDate', 'CreatedBy', null);
  var $column_search  = array('Id', 'DeptID', 'Name', 'IsActive', 'CreatedDate', 'CreatedBy');
  var $order          = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->BJGMAS01->select("
                            a.Id,
                            a.DeptID,
                            a.Name,
                            CASE
                              WHEN a.IsActive = 'A' THEN 'AKTIF'
                              ELSE 'TIDAK'
                            END AS IsActive,
                            b.DeptName,
                            CONVERT(VARCHAR(19), a.CreatedDate, 120) AS CreatedDate, 
                            a.CreatedBy");
    $this->BJGMAS01->from('Ms_ProductionMachines a');
    $this->BJGMAS01->join('DEPARTMENTS b', 'b.DeptID = a.DeptID', 'left');

    $i = 0;

    foreach ($this->column_search as $item) // loop column 
    {
      if ($_POST['search']['value']) // if datatable send POST for search
      {

        if ($i === 0) // first loop
        {
          $this->BJGMAS01->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->BJGMAS01->like($item, $_POST['search']['value']);
        } else {
          $this->BJGMAS01->or_like($item, $_POST['search']['value']);
        }

        if (count($this->column_search) - 1 == $i) //last loop
          $this->BJGMAS01->group_end(); //close bracket
      }
      $i++;
    }

    if (isset($_POST['order'])) // here order processing
    {
      $this->BJGMAS01->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->BJGMAS01->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_POST['length'] != -1)
      $this->BJGMAS01->limit($_POST['length'], $_POST['start']);
    $query = $this->BJGMAS01->get();
    
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->BJGMAS01->get();

    return $query->num_rows();
  }

  public function count_all()
  {
    $this->BJGMAS01->from($this->table);

    return $this->BJGMAS01->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('Id', $id);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function get_all_data($DeptID)
  {
    $this->BJGMAS01->select("Id, DeptID, Name, IsActive");
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('IsActive', 'A');
    $this->BJGMAS01->where('DeptID', $DeptID);
    $this->BJGMAS01->order_by('Name', 'ASC');
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function save($data)
  {
    $this->BJGMAS01->insert($this->table, $data);

    return $this->BJGMAS01->insert_id();
  }

  public function update($where, $data)
  {
    $this->BJGMAS01->update($this->table, $data, $where);

    return $this->BJGMAS01->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->BJGMAS01->where('Id', $id);
    $this->BJGMAS01->delete($this->table);
  }
}