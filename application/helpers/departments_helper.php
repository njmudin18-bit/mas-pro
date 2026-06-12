<?php
defined('BASEPATH') or exit('No direct script access allowed');

function get_department_att()
{
  $ci = &get_instance();

  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB               = $ci->load->database('absensi_local_mas', TRUE);
  $level                  = $ci->session->userdata('user_level');
  $dept_id                = $ci->session->userdata('user_dept_id');
  $deptName               = $ci->session->userdata('user_dept_name');
  $ID                     = array('1', '1190', '115');
  $departmentsWithAccess  = ['IT', 'HRD', 'ACCOUNTING'];
  $deptNameProduction     = ['ADMIN PRODUKSI'];
  $deptIDProduction       = ['1215', '1216', '1217', '1218', '1219', '1220'];
  $deptNameDriver         = ['DRIVER WAREHOUSE'];
  $deptIDDriver           = ['1177', '1221', '1234', '21'];
  $deptNameExtrude        = ['EXTRUDE'];
  $deptIDExtrude          = ['1217', '1216'];
  $departmentsEHS         = ['EHS'];
  $departmentsEHSAccess   = ['1222', '140', '1224'];
  //echo $deptName; exit;
  //if ($level == 'sa' || $level == 'admin') {
  //if ($level == '1') {
  if (in_array($deptName, $departmentsWithAccess)) {
    $third_DB->where_not_in('DEPTID', $ID);
    $third_DB->where_not_in('SUPDEPTID', '115');
  } else if (in_array($deptName, $deptNameProduction)) {
    $third_DB->where_in('DEPTID', $deptIDProduction);
  } else if (in_array($deptName, $deptNameDriver)) {
    $third_DB->where_in('DEPTID', $deptIDDriver);
  } elseif (in_array($deptName, $deptNameExtrude)) {
    $third_DB->where_in('DEPTID', $deptIDExtrude);
  } elseif (in_array($deptName, $departmentsEHS)) {
    $third_DB->where_in('DEPTID', $departmentsEHSAccess);
  } else {
    $third_DB->where('DEPTID', $dept_id);
  }

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  //$third_DB->where("DEPTNAME NOT LIKE '%PT MAIN%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA INDUSTRI%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'");
  $third_DB->order_by('DEPTNAME', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_department_for_purchasing()
{
  $ci = &get_instance();

  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB               = $ci->load->database('absensi_local_mas', TRUE);
  $level                  = $ci->session->userdata('user_level');
  $dept_id                = $ci->session->userdata('user_dept_id');
  $deptName               = $ci->session->userdata('user_dept_name');
  $ID                     = array('1', '1190', '115');
  $departmentsWithAccess  = ['IT', 'PURCHASING'];
  $departmentsEHS         = ['EHS'];
  $departmentsEHSAccess   = ['1222', '140', '1224'];
  $departmentsWH          = ['DRIVER WAREHOUSE'];
  $departmentsWHAccess    = ['1177', '21'];

  if (in_array($deptName, $departmentsWithAccess)) {
    $third_DB->where_not_in('DEPTID', $ID);
    $third_DB->where_not_in('SUPDEPTID', '115');
  } elseif (in_array($deptName, $departmentsEHS)) {
    $third_DB->where_in('DEPTID', $departmentsEHSAccess);
  } elseif (in_array($deptName, $departmentsWH)) {
    $third_DB->where_in('DEPTID', $departmentsWHAccess);
  } else {
    $third_DB->where('DEPTID', $dept_id);
  }

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  //$third_DB->where("DEPTNAME NOT LIKE '%PT MAIN%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA INDUSTRI%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'");
  $third_DB->order_by('DEPTNAME', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_department_for_security()
{
  $ci = &get_instance();

  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB               = $ci->load->database('absensi_local_mas', TRUE);
  $level                  = $ci->session->userdata('user_level');
  $dept_id                = $ci->session->userdata('user_dept_id');
  $deptName               = $ci->session->userdata('user_dept_name');
  $ID                     = array('1', '1190', '115');
  $departmentsWithAccess  = ['IT', 'HRD', 'ACCOUNTING', 'SECURITY'];
  $deptNameProduction     = ['ADMIN PRODUKSI'];
  $deptIDProduction       = ['1215', '1216', '1217', '1218', '1219', '1220'];
  $deptNameDriver         = ['DRIVER WAREHOUSE'];
  $deptIDDriver           = ['1177', '1221', '1234', '21'];
  //echo $deptName; exit;
  //if ($level == 'sa' || $level == 'admin') {
  //if ($level == '1') {
  if (in_array($deptName, $departmentsWithAccess)) {
    $third_DB->where_not_in('DEPTID', $ID);
    $third_DB->where_not_in('SUPDEPTID', '115');
  } else if (in_array($deptName, $deptNameProduction)) {
    $third_DB->where_in('DEPTID', $deptIDProduction);
  } else if (in_array($deptName, $deptNameDriver)) {
    $third_DB->where_in('DEPTID', $deptIDDriver);
  } else {
    $third_DB->where('DEPTID', $dept_id);
  }

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  //$third_DB->where("DEPTNAME NOT LIKE '%PT MAIN%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA INDUSTRI%'");
  $third_DB->where("DEPTNAME NOT LIKE '%PT. MULTI ARTA SEKAWAN%'");
  $third_DB->order_by('DEPTNAME', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_department_for_proses_produksi($DeptID)
{
  $ci = &get_instance();

  $DeptIDArr  = ['1216', '1217', '1218', '1219'];
  $third_DB   = $ci->load->database('bjsmas01_db', TRUE);

  $third_DB->select('DEPTID, DEPTNAME');
  $third_DB->from('DEPARTMENTS');
  //$third_DB->where_in('DEPTID', $DeptIDArr);
  $third_DB->where_in('DEPTID', $DeptID);
  $third_DB->order_by('DEPTNAME', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_line_name($DeptID)
{
  $ci = &get_instance();

  $third_DB   = $ci->load->database('bjsmas01_db', TRUE);

  $third_DB->select('Id, DeptID, LineName');
  $third_DB->from('Trans_ProductionProcessDT');
  //$third_DB->where_in('DEPTID', $DeptIDArr);
  $third_DB->where_in('DeptID', $DeptID);
  $third_DB->order_by('LineName', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_periode_penggajian()
{
    $ci = &get_instance();
    
    // Ambil nama departemen dari sesi
    $deptName = $ci->session->userdata('user_dept_name');
    $deptNameUpper = strtoupper($deptName);
    
    // Inisialisasi array untuk menampung periode yang diperbolehkan
    $periode_penggajian = [];

    /**
     * Logika Penentuan Periode Penggajian Berdasarkan deptName
     * Setiap item kini memiliki kunci 'selected'
     */
    switch ($deptNameUpper) {
        case 'IT':
            $periode_penggajian = [
                // IT tidak memiliki default selected yang spesifik
                ['value' => 'MINGGUAN', 'label' => 'PEGAWAI MINGGUAN', 'selected' => FALSE],
                ['value' => 'BULANAN', 'label' => 'PEGAWAI BULANAN', 'selected' => FALSE],
                ['value' => 'MAGANG', 'label' => 'PEGAWAI MAGANG', 'selected' => FALSE]
            ];
            break;
            
        case 'HRD':
            $periode_penggajian = [
                // HRD default selected MINGGUAN
                ['value' => 'MINGGUAN', 'label' => 'PEGAWAI MINGGUAN', 'selected' => TRUE],
                ['value' => 'MAGANG', 'label' => 'PEGAWAI MAGANG', 'selected' => FALSE],
            ];
            break;
            
        case 'ACCOUNTING':
            $periode_penggajian = [
                // ACCOUNTING default selected BULANAN
                ['value' => 'BULANAN', 'label' => 'PEGAWAI BULANAN', 'selected' => TRUE]
            ];
            break;
            
        default:
            // Default: BULANAN (dan default selected TRUE)
             $periode_penggajian = [
                 ['value' => 'BULANAN', 'label' => 'PEGAWAI BULANAN', 'selected' => TRUE]
             ];
            break;
    }

    return $periode_penggajian;
}

function get_department_name($id)
{
  $ci = &get_instance();
  
  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB   = $ci->load->database('absensi_local_mas', TRUE);

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  $third_DB->where('DEPTID', $id);
  $query = $third_DB->get();

  return $query->row();
}

function get_department_name_byid($id)
{
  $ci         = &get_instance();
  $third_DB   = $ci->load->database('absensi_local_mas', TRUE);

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  $third_DB->where('DEPTID', $id);
  $query = $third_DB->get();

  return $query->row()->DEPTNAME;
}

function get_karyawan_by_dept()
{
  $ci = &get_instance();
  
  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB   = $ci->load->database('absensi_local_mas', TRUE);

  $dept_id    = $ci->session->userdata('user_dept_id');

  $third_DB->select('*');
  $third_DB->from('USERINFO');
  //$third_DB->where('DEFAULTDEPTID', $dept_id);
  $third_DB->where('DEFAULTDEPTID', '1225');
  $third_DB->order_by('USERID', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_karyawan_($nip)
{
  $ci = &get_instance();

  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB   = $ci->load->database('absensi_local_mas', TRUE);

  $third_DB->select('*');
  $third_DB->from('USERINFO');
  $third_DB->where('SSN', $nip);
  $third_DB->order_by('USERID', 'ASC');
  $query  = $third_DB->get();
  $data   = $query->row();

  return $data->NAME;
}

function get_karyawan_details($id)
{
  $ci = &get_instance();

  //$third_DB   = $ci->load->database('attendance', TRUE);
  $third_DB   = $ci->load->database('absensi_local_mas', TRUE);

  $third_DB->select('A.*, B.DEPTNAME');
  $third_DB->from('USERINFO A');
  $third_DB->join('DEPARTMENTS B', 'B.DEPTID = A.DEFAULTDEPTID', 'LEFT');
  $third_DB->where('SSN', $id);
  $query  = $third_DB->get();
  $cek    = $query->num_rows();
  if ($cek > 0) {
    return $query->row();
  } else {
    return "-";
  }
}
