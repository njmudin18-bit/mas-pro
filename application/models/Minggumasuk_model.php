<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Minggumasuk_model extends CI_Model
{
  var $table = 'Ms_MingguMasuk';
  var $column_order = array('Tanggal', 'IsActive', 'Noted', 'CreatedDate', 'CreatedBy', null);
  var $column_search = array('Tanggal', 'IsActive', 'Noted', 'CreatedDate', 'CreatedBy');
  var $order = array('Tanggal' => 'DESC');

  public function __construct()
  {
    parent::__construct();
    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  private function _get_datatables_query()
  {
    $this->ABSENSI->select('Id, Tanggal, IsActive, Noted, CreatedDate, CreatedBy');
    $this->ABSENSI->from($this->table);

    $i = 0;
    foreach ($this->column_search as $item) {
      if ($_POST['search']['value']) {
        if ($i === 0) {
          $this->ABSENSI->group_start();
          $this->ABSENSI->like($item, $_POST['search']['value']);
        } else {
          $this->ABSENSI->or_like($item, $_POST['search']['value']);
        }
        if (count($this->column_search) - 1 == $i) {
          $this->ABSENSI->group_end();
        }
      }
      $i++;
    }

    if (isset($_POST['order'])) {
      $this->ABSENSI->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->ABSENSI->order_by(key($order), $order[key($order)]);
    }
  }

  public function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_POST['length'] != -1) {
      $this->ABSENSI->limit($_POST['length'], $_POST['start']);
    }
    $query = $this->ABSENSI->get();
    return $query->result();
  }

  public function count_filtered()
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
    $this->ABSENSI->where('Id', $id);
    $query = $this->ABSENSI->get($this->table);
    return $query->row();
  }

  public function save($data)
  {
    $this->ABSENSI->insert($this->table, $data);
    return $this->ABSENSI->insert_id();
  }

  public function update($id, $data)
  {
    $this->ABSENSI->where('Id', $id);
    $this->ABSENSI->update($this->table, $data);
    return $this->ABSENSI->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->ABSENSI->where('Id', $id);
    $this->ABSENSI->delete($this->table);
  }
}
