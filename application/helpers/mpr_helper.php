<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

function get_qty_mpr($nobukti)
{
  $ci=& get_instance();

  $second_DB    = $ci->load->database('bjsmas01_db', TRUE);
  $query  = $second_DB->query("SELECT COUNT(NoBukti) AS jumlah_mpr FROM tbl_monitoring_mpr
  WHERE NoBukti = '$nobukti'");

  return $query->row(); 
}

function cek_qty($NoBukti)
{
  $ci=& get_instance();

  $second_DB  = $ci->load->database('bjsmas01_db', TRUE);
  $query 			= $second_DB->query("SELECT COUNT(id) as step  FROM tbl_monitoring_mpr
                                    WHERE NoBukti = '$NoBukti'");
  $cek 				= $query->num_rows();
  if ($cek > 0) {
    return $query->row()->step;
  } else {
    return 0;
  }
}
