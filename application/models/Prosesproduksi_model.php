<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Prosesproduksi_model extends CI_Model
{
  var $table          = 'Trans_ProductionProcessHD';
  var $order          = array('a.Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function get_by_id($id)
  {
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('Id', $id);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function get_by_dept_id($id)
  {
    $this->BJGMAS01->select('Id, DeptID, UPPER(ProcessName) as ProcessName, Status');
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('DeptID', $id);
    $this->BJGMAS01->order_by('ProcessName', 'ASC');
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_proses_with_line_by_id($id)
  {
    $this->BJGMAS01->where('DeptID', $id);
    $this->BJGMAS01->order_by('LineName', 'ASC'); // Ganti ASC dengan DESC jika ingin urutan terbalik
    $Query = $this->BJGMAS01->get('Trans_ProductionProcessDT');

    return $Query->result();
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
    $this->BJGMAS01->trans_begin();

    try {
      $this->BJGMAS01->where('DeptID', $id);
      $this->BJGMAS01->delete('Trans_ProductionProcessDT'); 

      // $this->BJGMAS01->where('Id', $id);
      // $this->BJGMAS01->delete('Trans_ProductionProcessHD'); 

      if ($this->BJGMAS01->trans_status() === FALSE) {
        $this->BJGMAS01->trans_rollback();

        return false;
      } else {
        $this->BJGMAS01->trans_commit();

        return true;
      }
    } catch (\Exception $e) {
      $this->BJGMAS01->trans_rollback();

      return false;
    }
  }

  public function get_hd_by_id($id)
  {
    $this->BJGMAS01->from("Trans_ProductionProcessDT");
    $this->BJGMAS01->where('Id', $id);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function get_dt_by_id($id)
  {
    $this->BJGMAS01->from('Trans_ProductionProcessDT');
    $this->BJGMAS01->where('DeptID', $id);
    $query = $this->BJGMAS01->get();

    return $query->result();
  }
}