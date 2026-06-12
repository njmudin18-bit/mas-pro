<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Typeabsensi_model extends CI_Model
{
  var $table          = 'Ms_AbsenceTypes';
  var $column_order   = array('Id', 'AbsenceCode', 'AbsenceName', 'Description', 'CreatedDate', 'CreatedBy', null);
  var $column_search  = array('Id', 'AbsenceCode', 'AbsenceName', 'Description', 'CreatedDate', 'CreatedBy');
  var $order          = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->ABSENSI->select('Id, AbsenceCode, AbsenceName, Description,
                            CONVERT(VARCHAR(19), CreatedDate, 120) AS CreatedDate, CreatedBy');
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
    $this->ABSENSI->select('Id, AbsenceCode, AbsenceName, Description');
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->where('Id', $id);
    $query = $this->ABSENSI->get();

    return $query->row();
  }

  public function get_all_data()
  {
    $this->ABSENSI->select('Id, AbsenceCode, AbsenceName');
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->order_by('AbsenceName', 'ASC');
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
    $this->ABSENSI->where('Id', $id);
    $this->ABSENSI->delete($this->table);
  }
}
