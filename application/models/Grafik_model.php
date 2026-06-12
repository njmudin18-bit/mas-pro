<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grafik_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//GET DATA UTK DONUTS
	public function get_data()
	{
		$query 	= $this->db->query("SELECT A.nama_kategori AS label, 
																COUNT(B.id_kategori) AS value
																FROM table_kategori A
																LEFT JOIN table_project B ON B.id_kategori = A.id_kategori
																GROUP BY B.id_kategori, A.nama_kategori");
		$result = $query->result();

		return $result;
	}

	//GET DATA UTK PROGRESS
	public function get_data_status()
	{
		$query 	= $this->db->query("SELECT A.nama_project, A.id_pic, 
																A.project_url, A.project_progress, A.project_description, 
																B.nama_status, C.nama_kategori, D.nama
																FROM table_project A
																LEFT JOIN table_status B ON B.id_status = A.id_status
																LEFT JOIN table_kategori C ON C.id_kategori = A.id_kategori
																LEFT JOIN table_institusi D ON D.id_institusi = A.id_institusi
																ORDER BY A.project_progress DESC");
		$result = $query->result();

		return $result;
	}

	public function get_data_by_perusahaan()
	{
		$query 	= $this->db->query("SELECT COUNT(A.id_institusi) AS jlh_apps, B.nama
																FROM table_project A
																LEFT JOIN table_institusi B ON B.id_institusi = A.id_institusi
																GROUP BY A.id_institusi, B.nama 
																ORDER BY B.nama ASC");
		$result = $query->result();

		return $result;
	}

	//GET DATA LABEL PERBULAN
	public function get_data_label_per_bulan()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 	= $second_DB->query("SELECT 
																	YEAR(scan_date) AS Tahun_jalan, 
																	MONTH(scan_date) AS Bulan, 
																	FORMAT(scan_date, 'MMMM', 'en-US') as Nama_bulan, 
																	COUNT(scan_id) AS Jumlah_scan 
																FROM 
																	tbl_scanbarcode_job
                                WHERE YEAR(scan_date) = YEAR(GETDATE())
																GROUP BY 
																	YEAR(scan_date), 
																	MONTH(scan_date), 
																	FORMAT(scan_date, 'MMMM', 'en-US')
																ORDER BY MONTH(scan_date)
															");
		$result = $query->result();

		return $result;
	}

	//GET DATA JOB PERBULAN
	public function get_data_job_per_bulan()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 	= $second_DB->query("SELECT 
																	Nama_bulan as label, 
																	COUNT(no_job) AS value,
																	Bulan,
																	Tahun_jalan
																FROM 
																	(
																		SELECT 
																			no_job, 
																			COUNT(no_job) as label, 
																			YEAR(scan_date) AS Tahun_jalan, 
																			MONTH(scan_date) AS Bulan, 
																			FORMAT(scan_date, 'MMMM', 'en-US') as Nama_bulan 
																		FROM 
																			tbl_scanbarcode_job 
																		GROUP BY 
																			no_job, 
																			YEAR(scan_date), 
																			MONTH(scan_date), 
																			FORMAT(scan_date, 'MMMM', 'en-US')
																	) a
																WHERE a.Tahun_jalan = DATEPART(yyyy, GETDATE())
																GROUP BY 
																	Tahun_jalan, 
																	Bulan, 
																	Nama_bulan
																ORDER BY Bulan");
		$result = $query->result();

		return $result;
	}

	public function get_job_by_status_per_bulan()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 	= $second_DB->query("SELECT 
																	A.no_job, 
																	COUNT(A.no_job) as label, 
																	B.OK, 
																	C.NG 
																FROM 
																	tbl_scanbarcode_job A 
																	JOIN (
																		SELECT 
																			no_job, 
																			COUNT(StatusOK) as OK 
																		FROM 
																			(
																				SELECT 
																					no_job, 
																					CASE WHEN scan_update_date IS NULL THEN 'OK' ELSE 'NG' END StatusOK 
																				FROM 
																					tbl_scanbarcode_job 
																				WHERE 
																					scan_update_date IS NULL
																			) A 
																		GROUP BY 
																			no_job
																	) B ON A.no_job = B.no_job 
																	JOIN (
																		SELECT 
																			no_job, 
																			COUNT(StatusNG) as NG 
																		FROM 
																			(
																				SELECT 
																					no_job, 
																					CASE WHEN scan_update_date IS NOT NULL THEN 'NG' ELSE 'OK' END StatusNG 
																				FROM 
																					tbl_scanbarcode_job 
																				WHERE 
																					scan_update_date IS NOT NULL
																			) A 
																		GROUP BY 
																			no_job
																	) C ON A.no_job = C.no_job 
																WHERE 
																	YEAR(A.scan_date) = '2023' 
																	and MONTH(A.scan_date) = '03' 
																GROUP BY 
																	A.no_job, 
																	B.OK, 
																	C.NG
																");
		$result = $query->result();

		return $result;
	}

	public function data_ng_perbulan_tahun_jalan()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 	= $second_DB->query("SELECT 
																	YEAR(scan_date) AS Tahun_jalan, 
																	MONTH(scan_date) AS Bulan, 
																	FORMAT(scan_date, 'MMMM', 'en-US') as Nama_bulan, 
																	COUNT(scan_update_date) AS Jumlah_NG 
																FROM 
																	tbl_scanbarcode_job
																GROUP BY 
																	YEAR(scan_date), 
																	MONTH(scan_date), 
																	FORMAT(scan_date, 'MMMM', 'en-US')
																ORDER BY Bulan
																");
		$result = $query->result();

		return $result;
	}

	public function set_data_scan_from_last_month()
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 	= $second_DB->query("SELECT 
																	COUNT(scan_date) AS value, 
																	CAST(scan_date AS date) AS date, 
																	FORMAT(scan_date, 'MMMM', 'en-US') AS Nama_bulan 
																FROM 
																	tbl_scanbarcode_job 
																WHERE 
																	CAST(scan_date AS date) between CAST(
																		DATEADD(
																			dd, 
																			-31, 
																			GETDATE()
																		) AS date
																	) 
																	and CAST(
																		GETDATE() AS DATE
																	) 
																GROUP BY 
																	CAST(scan_date AS date), 
																	FORMAT(scan_date, 'MMMM', 'en-US') 
																ORDER BY 
																	date
																");
		$result = $query->result();

		return $result;
	}
}
