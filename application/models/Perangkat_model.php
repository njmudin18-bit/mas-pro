<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat_model extends CI_Model
{
  var $table          = 'Ms_Perangkat';
  var $column_order   = array('Id', 'Nomor', 'Nama', 'JenisID', 'Merk', 'Tipe', 'NoSeri', 'TahunPembelian', 'DeptID', 'UserID', 'Status', 'CreateDate', 'CreateBy', null);
  var $column_search  = array('Id', 'Nomor', 'Nama', 'JenisID', 'Merk', 'Tipe', 'NoSeri', 'TahunPembelian', 'DeptID', 'UserID', 'Status');
  var $order          = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function generatePerangkatNumber()
  {
    // Get current year and month
    $yearMonth  = date('Ym');
    $prefix     = 'PRN' . $yearMonth . '-';

    $this->BJGMAS01->select('Nomor');
    $this->BJGMAS01->like('Nomor', $prefix, 'after');
    $this->BJGMAS01->order_by('Nomor', 'DESC');
    $this->BJGMAS01->limit(1);
    $query = $this->BJGMAS01->get('Ms_Perangkat');

    $lastNumber = '';
    if ($query->num_rows() > 0) {
      $row        = $query->row();
      $lastNumber = $row->Nomor;
    }

    $sequence = 1;
    if (!empty($lastNumber)) {
      $parts = explode('-', $lastNumber);
      if (count($parts) > 1) {
        $lastSequence = (int)$parts[1];
        $sequence     = $lastSequence + 1;
      }
    }

    // Format the sequence with leading zeros (e.g., 1 becomes 001, 12 becomes 012)
    $newSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    return $prefix . $newSequence;
  }

  private function _get_datatables_query()
  {
    $this->BJGMAS01->from($this->table);

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

  public function get_all_bukti($Search, $Periode)
  {
    $Sql    = "";
    $Query  = $this->BJGMAS01->query($Sql);
    $Data   = array();
    foreach ($Query->result() as $Row) {
      $Data[] = array(
        "id"    => $Row->NoBukti,
        "name"  => $Row->PoNo
      );
    }

    echo json_encode($Data);
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
    $this->BJGMAS01->select('Ms_Perangkat.*, Ms_JenisPerangkat.Nama AS NamaJP');
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('Ms_Perangkat.Id', $id);
    $this->BJGMAS01->join('Ms_JenisPerangkat', 'Ms_JenisPerangkat.Id = Ms_Perangkat.JenisID', 'left');
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
