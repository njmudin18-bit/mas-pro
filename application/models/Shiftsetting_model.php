<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shiftsetting_model extends CI_Model
{
  // var $table = 'ShiftSetting';
  // var $column_order = array('ShiftName', 'StartTime', 'EndTime', 'GracePeriod', 'Aktivasi', null);
  // var $column_search = array('ShiftName', 'StartTime', 'EndTime', 'GracePeriod', 'Aktivasi');
  // var $order = array('ShiftID' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function get_member_by_deptid($DeptID)
  {
    $deptIDExtrude      = ['1216', '1217'];
    $deptIDProduction   = ['1216', '1218', '1219'];
    $deptIDDriver       = ['1177', '1221', '1234', '21'];
    $NipIgnoreDriver    = ['0022016041300', '0022012100017', '0022022101004', '0022021060201', '00220151005'];
    $DeptName           = $this->session->userdata('user_dept_name');
    if ($DeptName == 'ADMIN PRODUKSI') {
      $this->Attendance->where_in('a.DEFAULTDEPTID', $deptIDProduction);
    } else if ($DeptName == 'DRIVER WAREHOUSE') {
      $this->Attendance->where_in('a.DEFAULTDEPTID', $deptIDDriver);
      $this->Attendance->where_not_in('a.SSN', $NipIgnoreDriver);
    } else if ($DeptName == 'EXTRUDE') {
      $this->Attendance->where_in('a.DEFAULTDEPTID', $deptIDExtrude);
    } else {
      $this->Attendance->where('a.DEFAULTDEPTID', $DeptID);
    }

    $this->Attendance->select('a.USERID, UPPER(a.NAME) AS NAME, a.SSN, b.DEPTID, b.DEPTNAME');
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->join('USERINFO_PROPERTIES c', 'c.SSN = a.SSN', 'left');
    $this->Attendance->where("(c.IsActive IS NULL OR c.IsActive != 'O')");
    $this->Attendance->order_by('b.DEPTNAME', 'ASC');
    $this->Attendance->order_by('NAME', 'ASC');
    $Query  = $this->Attendance->get();

    return $Query->result();
  }

  public function get_user_all()
  {
    $this->Attendance->select('a.USERID, UPPER(a.NAME) AS NAME, a.SSN, b.DEPTNAME');
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->where("(a.SSN LIKE '001%' OR a.SSN = '0022012030019')", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT MAIN%'", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%MANAJEMEN%'", NULL, FALSE);
    $this->Attendance->order_by('b.DEPTNAME', 'ASC');
    $this->Attendance->order_by('a.NAME', 'ASC');
    // $this->Attendance->order_by('a.NAME', 'ASC');
    $Query  = $this->Attendance->get();

    return $Query->result();
  }

  public function get_user_bpjs()
  {
    $this->Attendance->select("
      a.USERID, a.SSN, b.DEPTNAME, UPPER(a.NAME) AS NAME,
      a.ZIP,
      CASE
        WHEN a.ZIP = 'Y' THEN 'YES'
        ELSE 'NO'
      END AS BPJS
    ");
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->join('ShiftSetting c', 'c.ShiftID = a.OPHONE', 'left');
    $this->Attendance->where("(a.SSN LIKE '001%' OR a.SSN = '0022012030019')", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT MAIN%'", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'", NULL, FALSE);
    $this->Attendance->where("(a.ZIP IS NULL OR a.ZIP <> 'Y')", NULL, FALSE);
    $this->Attendance->order_by('b.DEPTNAME', 'ASC');
    $this->Attendance->order_by('a.NAME', 'ASC');

    $Query  = $this->Attendance->get();

    return $Query->result();
  }

  public function get_user_gapok()
  {
    $this->Attendance->select("
      a.USERID, a.SSN, b.DEPTNAME, UPPER(a.NAME) AS NAME, a.FPHONE
    ");
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->join('ShiftSetting c', 'c.ShiftID = a.OPHONE', 'left');
    $this->Attendance->where("(a.SSN LIKE '001%' OR a.SSN = '0022012030019')", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT MAIN%'", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'", NULL, FALSE);
    $this->Attendance->where("(a.FPHONE IS NULL OR a.FPHONE = '')", NULL, FALSE);
    $this->Attendance->order_by('b.DEPTNAME', 'ASC');
    $this->Attendance->order_by('a.NAME', 'ASC');

    $Query  = $this->Attendance->get();

    return $Query->result();
  }

  public function get_user_tunjangan()
  {
    $this->Attendance->select("
      a.USERID, a.SSN, b.DEPTNAME, UPPER(a.NAME) AS NAME, a.STATE
    ");
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->join('ShiftSetting c', 'c.ShiftID = a.OPHONE', 'left');
    $this->Attendance->where("(a.SSN LIKE '001%' OR a.SSN = '0022012030019')", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT MAIN%'", NULL, FALSE);
    $this->Attendance->where("b.DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'", NULL, FALSE);
    $this->Attendance->where("(a.STATE IS NULL OR a.STATE = '')", NULL, FALSE);
    $this->Attendance->order_by('b.DEPTNAME', 'ASC');
    $this->Attendance->order_by('a.NAME', 'ASC');

    $Query  = $this->Attendance->get();

    return $Query->result();
  }

  public function get_spv_by_deptid($DeptID)
  {
    $this->Attendance->select('a.USERID, a.SSN, UPPER(a.NAME) AS NAME, a.GENDER, UPPER(a.TITLE) AS TITLE, a.DEFAULTDEPTID, b.DEPTNAME');
    $this->Attendance->from('USERINFO a');
    $this->Attendance->join('DEPARTMENTS b', 'b.DEPTID = a.DEFAULTDEPTID', 'left');
    $this->Attendance->where('a.DEFAULTDEPTID', $DeptID);
    $this->Attendance->where('a.TITLE', 'SUPERVISOR');
    $Query = $this->Attendance->get();

    return $Query->row();
  }

  public function get_jadwal_shift()
  {
    $this->Attendance->select("ShiftID, ShiftName, 
                               LEFT(CONVERT(VARCHAR(8), MondayStartTime, 108), 5) AS JamIn, 
	                             LEFT(CONVERT(VARCHAR(8), MondayEndTime, 108), 5)   AS JamOut", false)
    ->from('ShiftSetting')
    ->where('Aktivasi', 'Aktif')
    ->order_by('ShiftName');

    $Query = $this->Attendance->get();

    return $Query->result();
  }
}
