<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_model extends CI_Model {

  var $table          = 'table_document';
  var $column_order   = array('is_aktif', 'id_dept', 'nama_document', 'B.nama_type',
                            'nama_file', 'nama_type',
                            'create_date', 'create_by', 'update_date', 'update_by', null);
  var $column_search  = array('is_aktif', 'id_dept', 'nama_document', 'B.nama_type',
                            'nama_file', 'nama_type',
                             'create_date', 'create_by', 'update_date', 'update_by');
  var $order          = array('id' => 'desc');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_document($postData)
  {
    $response = array();

    if(isset($postData) ){

      $this->db->select('A.*, B.nama_type');
      $this->db->from('table_document A');
      $this->db->join('table_doc_type B', 'B.id = A.id_doc_type', 'left');
      $this->db->where("A.nama_document LIKE '%".$postData."%' ");
      $this->db->or_where("B.nama_type LIKE '%".$postData."%' ");
      $this->db->or_where("A.id_dept LIKE '%".$postData."%' ");
      $records = $this->db->get()->result();

      foreach($records as $row ){
        $response[] = array(
                              "value"       => $row->id,
                              "label"       => $row->nama_document,
                              "dept"        => $row->id_dept,
                              "doc_type"    => $row->nama_type,
                              "link_file"   => base_url()."files/uploads/docx/".$row->nama_file,
                              "nomor_doc"   => $row->nomor_document,
                              "tgl_upload"  => $row->create_date,
                              "uploader"    => $row->create_by,
                            );
      }
    }

    return $response;
  }

  private function _get_datatables_query()
  {
    //$this->db->from($this->table);
    $this->db->select('A.*, B.nama_type');
    $this->db->from('table_document A');
    $this->db->join('table_doc_type B', 'B.id = A.id_doc_type', 'left');

    $i = 0;
  
    foreach ($this->column_search as $item) // loop column 
    {
      if($_POST['search']['value']) // if datatable send POST for search
      {
        
        if($i===0) // first loop
        {
          $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->db->like($item, $_POST['search']['value']);
        }
        else
        {
          $this->db->or_like($item, $_POST['search']['value']);
        }

        if(count($this->column_search) - 1 == $i) //last loop
          $this->db->group_end(); //close bracket
      }
      $i++;
    }
    
    if(isset($_POST['order'])) // here order processing
    {
      $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } 
    else if(isset($this->order))
    {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if($_POST['length'] != -1)
    $this->db->limit($_POST['length'], $_POST['start']);
    $query = $this->db->get();
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function count_all()
  {
    $this->db->from($this->table);
    return $this->db->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->db->from($this->table);
    $this->db->where('id', $id);
    $query = $this->db->get();

    return $query->row();
  }

  public function get_by_nomor($id)
  {
    $this->db->from($this->table);
    $this->db->where('nomor_document', $id);
    $query = $this->db->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->db->insert($this->table, $data);
    return $this->db->insert_id();
  }

  public function update($where, $data)
  {
    $this->db->update($this->table, $data, $where);
    return $this->db->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->db->where('id', $id);
    $this->db->delete($this->table);
  }
}