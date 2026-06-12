<?php
defined('BASEPATH') or exit('No direct script access allowed');

class colors_model extends CI_Model
{
  var $table        = 'Ms_ColorShape';
  var $column_order = array(
    'Id', 'MonthNumber', 'MonthName', 'Colors', 'Shapes', 'Aktivasi', 'CreateDate', 'CreateBy', null
  );
  var $column_search = array(
    'Id', 'MonthNumber', 'MonthName', 'Colors', 'Shapes', 'Aktivasi', 'CreateDate', 'CreateBy'
  );
  var $order = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->BJGMAS01->from($this->table);

    $search_value = $this->input->post('search')['value'] ?? null;

    if ($search_value) {
        $this->BJGMAS01->group_start();
        foreach ($this->column_search as $index => $item) {
            if ($index === 0) {
                $this->BJGMAS01->like($item, $search_value);
            } else {
                $this->BJGMAS01->or_like($item, $search_value);
            }
        }
        $this->BJGMAS01->group_end();
    }

    $order_post = $this->input->post('order');
    if (isset($order_post)) {
        $col_index = $order_post[0]['column'];
        $col_dir = $order_post[0]['dir'];
        $this->BJGMAS01->order_by($this->column_order[$col_index], $col_dir);
    } else if (isset($this->order)) {
        $order = $this->order;
        $this->BJGMAS01->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
      // Bangun query awal
      $this->_get_datatables_query();

      // Validasi nilai length dan start dari POST
      $length = isset($_POST['length']) ? (int) $_POST['length'] : 10;
      $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;

      // Pastikan limit hanya dipakai kalau length != -1
      if ($length != -1) {
          $this->BJGMAS01->limit($length, $start);
      }

      // Eksekusi query
      $query = $this->BJGMAS01->get();

      // Return hasil dalam bentuk array of objects
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
