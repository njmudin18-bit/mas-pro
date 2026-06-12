<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

  var $table = 'table_project';
  var $column_order = array('nama_project', 'A.id_status', 'A.id_kategori', 
  													'id_pic', 'A.id_institusi', 'project_url', 
  													'project_description', 'project_progress', 
  													'start_date', 'end_date',
  													'B.nama_status', 'C.nama_kategori', 'D.nama',
                            'create_date', 'create_by', 'update_date', 'update_by', null);
  var $column_search = array('nama_project', 'A.id_status', 'A.id_kategori', 
  													 'id_pic', 'A.id_institusi', 'project_url', 
  													 'project_description', 'project_progress', 
  													 'start_date', 'end_date',
  													 'B.nama_status', 'C.nama_kategori', 'D.nama',
                             'create_date', 'create_by', 'update_date', 'update_by');
  var $order = array('id_project' => 'desc');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_data_time_line()
  {
    $query = $this->db->query("SELECT A.*, B.nama_status, C.nama_kategori, D.nama
                               FROM table_project A
                               LEFT JOIN table_status B ON B.id_status = A.id_status
                               LEFT JOIN table_kategori C ON C.id_kategori = A.id_kategori
                               LEFT JOIN table_institusi D ON D.id_institusi = A.id_institusi
                               WHERE B.nama_status != 'Finish' 
                               ORDER BY id_project DESC");
    return $query->result();
  }

  public function project_details($id)
  {
  	$query = $this->db->query("SELECT A.*, B.nama_status, C.nama_kategori, D.nama
                              FROM table_project A
                              LEFT JOIN table_status B ON B.id_status = A.id_status
                              LEFT JOIN table_kategori C ON C.id_kategori = A.id_kategori
                              LEFT JOIN table_institusi D ON D.id_institusi = A.id_institusi
                              WHERE A.id_project = '$id'");
  	
  	return $query->row();
  }

  private function _get_datatables_query()
  {
  	$this->db->select('A.*, B.nama_status, C.nama_kategori, D.nama');
    $this->db->from('table_project A');
    $this->db->join('table_status B', 'B.id_status = A.id_status', 'left');
    $this->db->join('table_kategori C', 'C.id_kategori = A.id_kategori', 'left');
    $this->db->join('table_institusi D', 'D.id_institusi = A.id_institusi', 'left');

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
    $this->db->where('id_project', $id);
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
    $this->db->where('id_project', $id);
    $this->db->delete($this->table);
  }
}