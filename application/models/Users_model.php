<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

  var $table = 'table_user';
  var $column_order = array('nip', 'nama_pegawai', 'email_pegawai', 'B.roles_name',
                            'username', 'levels', 'aktivasi', 'last_login', null);
  var $column_search = array('nip', 'nama_pegawai', 'email_pegawai', 'B.roles_name',
                            'username', 'levels', 'aktivasi', 'last_login');
  var $order = array('id' => 'desc');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  private function _get_datatables_query()
  {
    //$this->db->from($this->table);

    $this->db->from('table_user A');
    $this->db->join('roles B', 'B.idroles = A.user_level', 'left');

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

  public function get_alls()
  {
    $this->db->from($this->table);
    $query = $this->db->get();

    return $query->result();
  }

  //SHOW DEPARTMENT FROM DB ATTENDANCE
  public function show_department_att()
  {
    $third_DB   = $this->load->database('attendance', TRUE);

    $ID = array('1', '1190');
    $third_DB->select('*');
    $third_DB->from('DEPARTMENTS');
    $third_DB->where_not_in('DEPTID', $ID);
    $third_DB->order_by('DEPTNAME', 'ASC');
    $query = $third_DB->get();

    return $query->result();
  }

  public function get_karyawan_dept($id)
  {
    $third_DB   = $this->load->database('absensi_local_mas', TRUE);

    $third_DB->select('SSN, UPPER(NAME) AS NAME');
    $third_DB->from('USERINFO');
    $third_DB->where('DEFAULTDEPTID', $id);
    $third_DB->order_by('NAME', 'ASC');
    $query = $third_DB->get();

    return $query->result();
  }

  public function get_qc_users() 
  {
    $Sql    = "SELECT a.id, a.dept_id, a.nip, a.nama_pegawai, a.email_pegawai,
               a.username, a.user_level, b.roles_name, a.aktivasi
               FROM table_user a
               LEFT JOIN roles b ON b.idroles = a.user_level
               WHERE roles_name = 'Staff QC' AND aktivasi = 'Aktif'
               ORDER BY a.username";
    $Query  = $this->db->query($Sql);
    $Result = $Query->result();

    return $Result;
  }
}