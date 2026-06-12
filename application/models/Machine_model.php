<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machine_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_all()
  {
    $now          = date("Ymd");
    $second_DB    = $this->load->database('bjsmas01_db', TRUE);

    // $sql   = "SELECT a.kode, e.Namaoperator, c.Namamesin, d.Namamold, a.Job, a.Shift, 
    //           d.Kapasitasmold, a.created_at as Tglinput, 
    //           isnull(b.Durasi, '') DurasiON,
    //           isnull(b.DurasiOff, '') DurasiOff, 
    //           isnull(b.Mold, '') qty, 
    //           isnull(b.Statusmesin,'') Statusmesin     
    //           from tbl_operatormesin a
    //           left join tbl_Datakeyence b ON a.kode = b.Kode
    //           left join tbl_msmesin c ON a.Idmesin = c.Idmesin
    //           left join tbl_msmold d ON a.Idmold = d.Idmold
    //           left Join tbl_msoperator e ON a.Nik = e.Idoperator 
    //           where convert(varchar(8),a.created_at,112) IN ('20230530')";
    $sql        = "SELECT 
                    a.Kode, 
                    a.Nomesin, 
                    a.Mold AS qty, 
                    a.Statusmesin, 
                    ISNULL(e.Namaoperator, '') Namaoperator, 
                    c.Namamesin, 
                    ISNULL(d.Namamold, '') Namamold, 
                    ISNULL(b.Job, '') Job, 
                    ISNULL(b.Shift, '') Shift, 
                    ISNULL(d.Kapasitasmold, '') Kapasitasmold 
                  FROM 
                    tbl_Datakeyence a 
                    left join tbl_operatormesin b ON a.Nomesin = b.Idmesin 
                    and a.Kode = b.kode 
                    left join tbl_msmesin c ON a.Nomesin = c.Idmesin 
                    left join tbl_msmold d ON b.Idmold = d.Idmold 
                    left Join tbl_msoperator e ON b.Nik = e.Idoperator 
                  WHERE 
                    CONVERT(
                      VARCHAR(8), 
                      a.Createdate, 
                      112
                    ) IN ('20230531')";

    $query      = $second_DB->query($sql);
    $result     = $query->result();

    return $result;
  }

  public function get_all_old2()
  {
    $now         = date("Ymd");
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    /*$sql 				= " SELECT TOP 8 C.Namaoperator, A.Nomesin, D.Namamesin, A.qty, 
										A.Statusmesin, A.Durasi, A.DurasiOff, B.Shift
										FROM tbl_MesinDetail A
										LEFT JOIN tbl_operatormesin B ON a.Kode = B.kode
										LEFT JOIN tbl_msoperator C ON B.Nik = C.Idoperator
										LEFT JOIN tbl_msmesin D ON D.Idmesin = A.Nomesin
										WHERE CONVERT(VARCHAR(8), A.Createdate, 112) LIKE '$now'
										ORDER BY A.Createdate DESC";*/
    $sql   = " SELECT a.Namamesin as Namamesin_old, 
							isnull(b.Nik,'') Operator, 
							isnull(c.Namamesin,'') Namamesin, 
							isnull(d.Namamold,'') Namamold, 
						  isnull(b.Job,'') Job, 
						  isnull(b.Shift,'') Shift, 
						  isnull(d.Kapasitasmold,'') Kapasitasmold, 
						  isnull(e.qty,0) qty,
						  isnull(e.Durasi,0) Durasi,
							isnull(e.DurasiOff,0) DurasiOff,
						  isnull(e.Statusmesin,'') Statusmesin,
						  isnull(f.Namaoperator,'') Namaoperator
							FROM tbl_msmesin a
							LEFT JOIN (SELECT nik, Idmesin, Idmold, Job, Shift 
													FROM tbl_operatormesin WHERE convert(VARCHAR(8),created_at,112) like '$now' AND status = 'Proses'
												) b ON a.Idmesin = b.Idmesin
							LEFT JOIN tbl_msmesin c ON b.Idmesin = c.Idmesin 
							LEFT JOIN tbl_msmold d ON b.Idmold = d.Idmold 
							LEFT JOIN tbl_msoperator f ON f.Idoperator = b.Nik
							LEFT JOIN (SELECT Kode, Nomesin, Durasi, DurasiOff, Mold, Qty, Statusmesin 
												FROM tbl_MesinDetail ) e ON a.Idmesin = e.Nomesin
							GROUP BY a.Namamesin, b.Nik, c.Namamesin, d.Namamold, 
							b.Job, b.Shift, d.Kapasitasmold, e.qty, e.Statusmesin, 
							e.Durasi, e.DurasiOff, f.Namaoperator";
    $query      = $second_DB->query($sql);
    $result     = $query->result();

    return $result;
  }

  public function get_all_old()
  {
    $now         = date("Ymd");
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $query      = $second_DB->query("SELECT TOP 8 * FROM tbl_MesinDetail 
																		 WHERE convert(char(8),Createdate,112)  = '$now'
																		 ORDER BY Createdate DESC");
    $result = $query->result();

    return $result;
  }
}
