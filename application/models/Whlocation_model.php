<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whlocation_model extends CI_Model
{
  var $table          = 'tbl_wh_location';
  var $column_order   = array('Type', 'WhLocation', 'Status', null);
  var $column_search  = array('Type', 'WhLocation', 'Status');
  var $order          = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS_DB  = $this->load->database('bjsmas01_db', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->BJGMAS_DB->from($this->table);

    $i = 0;
    $search_value = $this->input->post('search')['value'];

    foreach ($this->column_search as $item) // loop column 
    {
      if ($search_value)
      {
        if ($i === 0)
        {
          $this->BJGMAS_DB->group_start();
          $this->BJGMAS_DB->like($item, $search_value);
        } else {
          $this->BJGMAS_DB->or_like($item, $search_value);
        }

        if (count($this->column_search) - 1 == $i)
          $this->BJGMAS_DB->group_end();
      }
      $i++;
    }

    if (isset($_POST['order'])) // here order processing
    {
      $this->BJGMAS_DB->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->BJGMAS_DB->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_POST['length'] != -1)
      $this->BJGMAS_DB->limit($_POST['length'], $_POST['start']);
    $query = $this->BJGMAS_DB->get();
    
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->BJGMAS_DB->get();

    return $query->num_rows();
  }

  public function count_all()
  {
    $this->BJGMAS_DB->from($this->table);

    return $this->BJGMAS_DB->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->BJGMAS_DB->from($this->table);
    $this->BJGMAS_DB->where('Id', $id);
    $query = $this->BJGMAS_DB->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->BJGMAS_DB->insert($this->table, $data);

    return $this->BJGMAS_DB->insert_id();
  }

  public function update($where, $data)
  {
    $this->BJGMAS_DB->update($this->table, $data, $where);

    return $this->BJGMAS_DB->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->BJGMAS_DB->where('Id', $id);

    $this->BJGMAS_DB->delete($this->table);
  }
}
