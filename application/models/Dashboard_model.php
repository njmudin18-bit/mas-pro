<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function getDatalevel($user_le)
  {
    $this->db->select('u.user_level, r.roles_name');
    $this->db->from('table_user as u');
    $this->db->join('roles as r', 'u.user_level=r.idroles', 'left');
    $this->db->where('r.idroles', $user_le);
    $query = $this->db->get()->row();

    $this->db->save_queries = false;
    return $query;
  }

  public function getpermissions_groupname()
  {
    $this->db->select('idpermissions_group, permissions_groupname, status');
    $this->db->from('permissions_group');
    $this->db->where('status', '1');

    return $this->db->get()->result();
  }


  public function getpermissions($idpermissions_group, $user_le)
  {
    $this->db->select('pr.idpermissions, pr.idpermissions_group, pr.code_class, pr.code_method, pr.display_name, pr.code_url, pr.display_icon, pr.status');
    $this->db->from('permissions as pr');
    $this->db->join('roles_permissions as rp', 'pr.idpermissions = rp.idpermissions', 'left');
    $this->db->where("pr.idpermissions_group =  '$idpermissions_group' and rp.idroles = '$user_le' and pr.status = '1' and pr.type = 'TRUE' and rp.status='1'");
    $this->db->order_by('pr.display_name', 'ASC');


    return $this->db->get();
  }

  public function getmethod_permission($idpermissions_group, $code_method, $code_class)
  {
    $this->db->select('idpermissions, idpermissions_group, code_class, code_method, display_name, status');
    $this->db->from('permissions');
    $this->db->where("idpermissions_group = '$idpermissions_group' and code_method = '$code_method' and  code_class = '$code_class' and status = '1'");

    return $this->db->get()->row();
  }

  public function getroles_permissions($roles)
  {
    $sql    = " SELECT rp.idroles, pg.display_icon, pg.idpermissions_group, pg.permissions_groupname
                FROM roles_permissions as rp
                LEFT JOIN permissions as pr ON rp.idpermissions = pr.idpermissions
                LEFT JOIN permissions_group as pg ON pg.idpermissions_group = pr.idpermissions_group
                WHERE rp.idroles = '1' and rp.status = '1' and pg.status = '1' AND pg.apps IS NULL
                GROUP BY rp.idroles, pg.display_icon, pg.idpermissions_group, pg.permissions_groupname
                ORDER BY 
                  CASE
                    WHEN pg.permissions_groupname = 'Dashboard' THEN 2
                    ELSE 0
                  END
                DESC, permissions_groupname ASC";
    $query  = $this->db->query($sql);

    return $query->result();
  }

  public function getroles_permissions_old($roles)
  {
    $this->db->select('rp.idroles, pg.display_icon, pg.idpermissions_group, pg.permissions_groupname');
    $this->db->from('roles_permissions as rp');
    $this->db->join('permissions as pr', 'rp.idpermissions = pr.idpermissions', 'left');
    $this->db->join('permissions_group as pg', 'pg.idpermissions_group = pr.idpermissions_group', 'left');
    //$this->db->where("rp.idroles = '$roles' and rp.status = '1' and pg.status = '1'");
    $this->db->where("rp.idroles = '$roles' and rp.status = '1' and pg.status = '1' AND pg.apps IS NULL");
    $this->db->group_by('rp.idroles, pg.display_icon, pg.idpermissions_group, pg.permissions_groupname');
    $this->db->order_by('pg.permissions_groupname', 'ASC');

    return $this->db->get()->result();
  }
}
