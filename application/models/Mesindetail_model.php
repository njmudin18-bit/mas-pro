<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesindetail_model extends CI_Model {

  var $table = 'tbl_MesinDetail';
  var $column_order = array('Kode', 'Nomesin', 'Durasi', 'DurasiOff', 'Mold', 'Qty', 
                            'Total', 'Downtime', 'Detika', 'Detikb', 'Detikc', 'Detikd', 
                            'Detike', 'Detikf', 'Menita', 'Menitb', 'Menitc', 'Menitd', 'Menite', 
                            'Createdate', 'Statusmesin', null);
  var $column_search = array('Kode', 'Nomesin', 'Durasi', 'DurasiOff', 'Mold', 'Qty', 
                             'Total', 'Downtime', 'Detika', 'Detikb', 'Detikc', 'Detikd', 
                             'Detike', 'Detikf', 'Menita', 'Menitb', 'Menitc', 'Menitd', 'Menite', 
                             'Createdate', 'Statusmesin');
  var $order = array('Createdate' => 'DESC');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  private function _get_datatables_query()
  {
    //$this->db->from($this->table);
    $kode_mesin  = $this->input->post('kode_mesin');

    $this->db->select('*');
    $this->db->from('tbl_MesinDetail');
    $this->db->where('Nomesin', $kode_mesin);
    $this->db->order_by('Createdate', "DESC");

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

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $second_DB->limit($_POST['length'], $_POST['start']);
    $query = $second_DB->get();
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $query = $second_DB->get();
    return $query->num_rows();
  }

  public function count_all()
  {
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $second_DB->from($this->table);
    return $second_DB->count_all_results();
  }
}