<?php
defined('BASEPATH') or exit('No direct script access allowed');

function removePCGPrefix($string) {
  return substr($string, 4);
}

function removeMTPrefix($string) {
  return substr($string, 3);
}

function removeMilliseconds($datetimeString) {
  return substr($datetimeString, 0, 19);
}

function generateRandomString($length = 5) {
  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  
  return $randomString;
}

function get_job_details($no_job, $bulan, $tahun)
{
  $ci = &get_instance();

  $new_bulan    = 0;
  if (strlen($bulan) == 1) {
    $new_bulan  = "0" . $bulan;
  } else {
    $new_bulan  = $bulan;
  }

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $table_name   = 'Trans_Job' . $tahun . $new_bulan; //Trans_Job202301

  $query  = $second_DB->query("SELECT A.*, B.PartName FROM $table_name A 
                               LEFT JOIN Ms_Part B ON B.PartID = A.PartID
                               WHERE A.NoBukti = '$no_job'");

  return $query->row();
}

function get_job_history($no_job, $bulan, $tahun)
{
  $ci = &get_instance();

  $new_bulan    = 0;
  if (strlen($bulan) == 1) {
    $new_bulan  = "0" . $bulan;
  } else {
    $new_bulan  = $bulan;
  }

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $table_name   = 'Trans_Job' . $tahun . $new_bulan; //Trans_Job202301

  $query  = $second_DB->query("SELECT a.CompanyCode + '/' + a.NoBukti AS NoBukti, b.PartID, 
                              b.PartName, b.OtherID,
                              b.OtherName, b.TypeInventoryID, a.UnitID, b.QtyPallet, 
                              a.Keterangan, a.QtyOrder, a.Tgl,
                              a.DateNeed, a.DateBegin, a.DateFinish, a.Proses1, 
                              a.Proses2, a.WHResult, a.NomerPo AS PO
                              FROM $table_name a, Ms_Part b WHERE a.PartID=b.PartID 
                              AND NoBukti = '$no_job'");

  return $query->row();
}

function get_wh_history($no_job, $no_kartu, $bulan, $tahun)
{
  $ci = &get_instance();

  $new_bulan    = 0;
  if (strlen($bulan) == 1) {
    $new_bulan  = "0" . $bulan;
  } else {
    $new_bulan  = $bulan;
  }

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $table_name   = 'Trans_BHPHD' . $tahun . $new_bulan; //Trans_BHPHD202301
  $table_name2  = 'Trans_BHPDT' . $tahun . $new_bulan; //Trans_BHPDT202301

  /*$query  = $second_DB->query("SELECT A.*, B.PartID, B.LocationID, C.PartName, C.UnitID_Stock 
                               FROM $table_name A
                               JOIN $table_name2 B ON A.NoBukti = B.NoBukti
                               LEFT JOIN Ms_Part C ON C.PartID = B.PartID
                               WHERE B.NoBuktiJob = '$no_job'");*/
  /*$query  = $second_DB->query("SELECT A.*, B.NoKartu, B.LocationID, 
                                B.PartID, B.NoBuktiJob, C.PartName, C.UnitID_Stock   
                                FROM Trans_BHPHD202301 A
                                JOIN Trans_BHPDT202301 B ON A.NoBukti = B.NoBukti
                                LEFT JOIN Ms_Part C ON C.PartID = B.PartID
                                WHERE B.NoBuktiJob = '$no_job' 
                                AND B.NoKartu = '$no_kartu'
                                ORDER BY NoKartu");*/

  $query  = $second_DB->query("SELECT A.*, B.NoKartu, B.LocationID, 
                                B.PartID, B.NoBuktiJob, C.PartName, C.UnitID_Stock   
                                FROM $table_name A
                                JOIN $table_name2 B ON A.NoBukti = B.NoBukti
                                LEFT JOIN Ms_Part C ON C.PartID = B.PartID
                                WHERE B.NoBuktiJob = '$no_job' 
                                AND B.NoKartu = '$no_kartu'
                                ORDER BY NoKartu");
  $cek    = $query->num_rows();

  return $query->result();
}

function get_total_qty_jobs_wh($no_job, $bulan, $tahun)
{
  $ci = &get_instance();

  $new_bulan    = 0;
  if (strlen($bulan) == 1) {
    $new_bulan  = "0" . $bulan;
  } else {
    $new_bulan  = $bulan;
  }

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $table_name   = 'Trans_BHPHD' . $tahun . $new_bulan; //Trans_BHPHD202301
  $table_name2  = 'Trans_BHPDT' . $tahun . $new_bulan; //Trans_BHPDT202301

  $query  = $second_DB->query("SELECT SUM(B.Qty) AS jlh_qty_wh  
                               FROM $table_name A
                               JOIN $table_name2 B ON A.NoBukti = B.NoBukti
                               WHERE B.NoBuktiJob = '$no_job'");

  return $query->result();
}

function get_total_qty_jobs_wh_new($no_job, $bulan, $tahun)
{
  $ci = &get_instance();

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $table_name   = 'Trans_BHPHD' . $tahun . $bulan; //Trans_BHPHD202301
  $table_name2  = 'Trans_BHPDT' . $tahun . $bulan; //Trans_BHPDT202301

  $query  = $second_DB->query("SELECT SUM(B.Qty) AS jlh_qty_wh  
                               FROM $table_name A
                               JOIN $table_name2 B ON A.NoBukti = B.NoBukti
                               WHERE B.NoBuktiJob = '$no_job'");

  return $query->result();
}

function get_created_by($id)
{
  $ci = &get_instance();

  $sql  = "SELECT * FROM table_user WHERE id = '$id'";
  $data = $ci->db->query($sql);
  $cek  = $data->num_rows();
  if ($cek > 0) {
    $res  = $data->row();

    return $res->username;
  } else {
    return "sa";
  }
}

function get_user_united($id)
{
  $ci = &get_instance();

  $third_DB   = $ci->load->database('attendance', TRUE);

  $third_DB->select('*');
  $third_DB->from('USERINFO');
  $third_DB->where('ssn', $id);
  $query = $third_DB->get();

  return $query->result();
}

function cek_pernah_reject($no_job)
{
  $ci = &get_instance();
  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);

  $query  = $second_DB->query("SELECT * FROM tbl_scanbarcode_job 
                               WHERE barcode_no LIKE '%$no_job%'
                               AND loc_id = 'QC001'
                               AND scan_update_date IS NOT NULL");
  $cek    = $query->num_rows();
  return $cek;
}
