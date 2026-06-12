<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengiriman_model extends CI_Model {

  var $table          = 'Trans_JadwalKirimHD';
  var $column_order   = array('a.Id', 'a.NoKirim', 'a.PartID', 'b.PartName', 'a.Location', 'a.CreateDate', 'a.CreateBy', null);
  var $column_search  = array('a.Id', 'a.NoKirim', 'a.PartID', 'b.PartName', 'a.Location', 'a.CreateDate', 'a.CreateBy');
  var $order          = array('a.CreateDate' => 'desc');

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function _get_datatables_query($start_date, $end_date)
  {
    $this->BJGMAS01->select("a.Id, a.NoKirim, a.PartID, b.PartName, a.Location, a.CreateDate, a.CreateBy");
    $this->BJGMAS01->from("Trans_JadwalKirimHD a");
    $this->BJGMAS01->join("Ms_Part b", "b.PartID = a.PartID", "left");
    $this->BJGMAS01->where("CAST(a.CreateDate AS DATE) BETWEEN '$start_date' AND '$end_date'");

    // Default order by CreateDate (only if no other order is applied)
    $default_order_column     = "a.CreateDate";
    $default_order_direction  = "desc";
    $this->BJGMAS01->order_by($default_order_column, $default_order_direction);

    // Searching logic
    $i = 0;
    foreach ($this->column_search as $item)
    {
        if($_POST['search']['value'])
        {
            if($i === 0) // first loop
            {
                $this->BJGMAS01->group_start();
                $this->BJGMAS01->like($item, $_POST['search']['value']);
            }
            else
            {
                $this->BJGMAS01->or_like($item, $_POST['search']['value']);
            }

            if(count($this->column_search) - 1 == $i)
                $this->BJGMAS01->group_end();
        }
        $i++;
    }

    // Check if order exists from DataTables
    if(isset($_POST['order']))
    {
        $column_index = $_POST['order']['0']['column']; // Column index from DataTables
        $column_name = $this->column_order[$column_index]; // Get column name from mapping
        $order_dir = $_POST['order']['0']['dir']; // Order direction (asc/desc)

        // Prevent duplicate ORDER BY clause for "a.CreateDate"
        if ($column_name !== $default_order_column)
        {
            $this->BJGMAS01->order_by($column_name, $order_dir);
        }
    }
    else if(isset($this->order))
    {
        $order = $this->order;
        $order_column = key($order);
        $order_direction = $order[$order_column];

        // Prevent duplicate ORDER BY clause
        if ($order_column !== $default_order_column)
        {
            $this->BJGMAS01->order_by($order_column, $order_direction);
        }
    }
  }

  public function get_datatables($start_date, $end_date)
  {
    $this->_get_datatables_query($start_date, $end_date);
    if($_POST['length'] != -1)
    $this->BJGMAS01->limit($_POST['length'], $_POST['start']);
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function count_filtered($start_date, $end_date)
  {
    $this->_get_datatables_query($start_date, $end_date);
    $query = $this->BJGMAS01->get();

    return $query->num_rows();
  }

  public function count_all()
  {
    $this->BJGMAS01->from($this->table);

    return $this->BJGMAS01->count_all_results();
  }

  public function get_part_id($Search = '')
  {
    $this->BJGMAS01->select('PartID AS id, PartName AS text');
    $this->BJGMAS01->from('Ms_Part');

    if (!empty($search)) {
      $this->BJGMAS01->like('PartName', $Search);
    }

    $this->BJGMAS01->limit(10);
    $query = $this->BJGMAS01->get();

    return $query->result_array();
  }

  public function get_by_id($id)
  {
    $this->BJGMAS01->from($this->table);
    $this->BJGMAS01->where('NoKirim', $id);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function save($data)
  {
    $this->BJGMAS01->insert($this->table, $data);

    return $this->BJGMAS01->insert_id();
  }

  public function update($where, $data)
  {
    $this->BJGMAS01->update($this->table, $data, $where);

    return $this->BJGMAS01->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->BJGMAS01->where('NoKirim', $id);
    $this->BJGMAS01->delete($this->table);
  }

  public function get_jadwal_data($Location = 'KG', $Month = null, $Year = null)
  {
    $this->BJGMAS01->select("
      a.Location, 
      a.Type, 
      a.PartID, 
      ISNULL(b.PartName, 'NULL') AS PartName,
      ISNULL(c.PONumber, 'NULL') AS PONumber,
      CAST(ISNULL(c.QuantityPO, 0) AS INT) AS QuantityPO,
      c.TanggalKirim, 
      ISNULL(c.PlanQuantity, 0) AS PlanQuantity,
      ISNULL(c.ActualQuantity, 0) AS ActualQuantity
    ");
    $this->BJGMAS01->from('Trans_JadwalKirimHD a');
    $this->BJGMAS01->join('Ms_Part b', 'b.PartID = a.PartID', 'left');
    $this->BJGMAS01->join('Trans_JadwalKirimDT2 c', 'c.NoKirim = a.NoKirim', 'left');
    $this->BJGMAS01->where('a.Location', $Location);

    if ($Month && $Year) {
      $this->BJGMAS01->where("MONTH(c.TanggalKirim)", $Month);
      $this->BJGMAS01->where("YEAR(c.TanggalKirim)", $Year);
    }

    $this->BJGMAS01->order_by('b.PartName', 'ASC');
    $this->BJGMAS01->order_by('a.CreateDate', 'DESC');

    $query = $this->BJGMAS01->get();
    
    return $query->result_array();
  }

  public function get_single_data_timeline($NoKirim)
  {
    $this->BJGMAS01->select("TOP 1 
      a.NoKirim, 
      DATENAME(MONTH, a.TanggalKirim) AS Bulan, 
      YEAR(a.TanggalKirim) AS Tahun, 
      b.Location, 
      a.PartID, 
      c.PartName", false);
    $this->BJGMAS01->from('Trans_JadwalKirimDT2 a');
    $this->BJGMAS01->join('Trans_JadwalKirimHD b', 'b.NoKirim = a.NoKirim', 'left');
    $this->BJGMAS01->join('Ms_Part c', 'c.PartID = a.PartID', 'left');
    $this->BJGMAS01->where('a.NoKirim', $NoKirim);

    $Query  = $this->BJGMAS01->get();
    $Result = $Query->row();

    return $Result;
    // Optional: use $result->Bulan, $result->Tahun, etc.
  }

  public function get_jadwal_data_old($Location) //$Location, $Month, $Year 
  {
    $this->BJGMAS01->select("
      a.Location, 
      a.Type, 
      a.PartID, 
      ISNULL(b.PartName, 'NULL') AS PartName,
      ISNULL(c.PONumber, 'NULL') AS PONumber,
      CAST(ISNULL(c.QuantityPO, 0) AS INT) AS QuantityPO,
      c.TanggalKirim, 
      ISNULL(c.PlanQuantity, 0) AS PlanQuantity,
      ISNULL(c.ActualQuantity, 0) AS ActualQuantity,
      c.TanggalKirim, 
      a.NoKirim, 
      a.CreateDate
    ");
    $this->BJGMAS01->from('Trans_JadwalKirimHD a');
    $this->BJGMAS01->join('Ms_Part b', 'b.PartID = a.PartID', 'left');
    $this->BJGMAS01->join('Trans_JadwalKirimDT2 c', 'c.NoKirim = a.NoKirim', 'left');
    $this->BJGMAS01->where('a.Location', 'KG');
    $this->BJGMAS01->where('MONTH(a.TanggalKirim)', '03');
    $this->BJGMAS01->where('YEAR(a.TanggalKirim)', '2025');
    $this->BJGMAS01->order_by('b.PartName', 'ASC');
    $this->BJGMAS01->order_by('a.CreateDate', 'DESC');

    $query = $this->BJGMAS01->get();

    return $query->result_array();
  }

  public function pengiriman_harian_data($Tanggal, $TblSOHD)
  {
    $Sql = "SELECT
              a.Id,
              b.PartName, 
              a.TanggalKirim,
              FORMAT(CAST(a.ActualQuantity AS INT), 'N0') AS KirimActual,
              FORMAT(CAST(b.QtyPallet AS INT), 'N0') AS StdPacking,
              CAST(a.ActualQuantity AS INT) % CAST(b.QtyPallet AS INT) AS QtyPecahan,
              CASE 
                    WHEN a.ActualQuantity = 0 THEN 0 
                    ELSE CAST(a.ActualQuantity AS FLOAT) / CAST(b.QtyPallet AS FLOAT) 
                END AS StandardColly,
              CASE 
                    WHEN a.ActualQuantity = 0 THEN 0 
                    ELSE CAST(a.ActualQuantity AS INT) / CAST(b.QtyPallet AS INT) 
                END AS StdColly,
              CASE 
                    WHEN CAST(a.ActualQuantity AS INT) % CAST(b.QtyPallet AS INT) > 0 THEN 1 
                    ELSE 0 
                END AS PecahanColly,
              FORMAT(
                CAST(a.ActualQuantity AS INT) / CAST(b.QtyPallet AS INT) 
                + 
                CASE 
                  WHEN CAST(a.ActualQuantity AS INT) % CAST(b.QtyPallet AS INT) > 0 THEN 1 
                  ELSE 0 
                END,
                'N0'
              ) AS TotalColly,
              FORMAT(
                ((CAST(a.ActualQuantity AS INT) / CAST(b.QtyPallet AS INT)) * CAST(b.QtyPallet AS INT)) 
                + (CAST(a.ActualQuantity AS INT) % CAST(b.QtyPallet AS INT)),
                'N0'
              ) AS PlanningKirim,
              d.CustomerIDAlamat, 
              d.NamaPenerima,
              d.Alamat,
              a.PartID,
              a.PONumber,  
              CAST(a.QuantityPO AS INT) AS QuantityPO,
              CAST(a.PlanQuantity AS INT) AS PlanQuantity, 
              a.Terkirim,
              CASE 
                WHEN a.Terkirim IS NULL OR LTRIM(RTRIM(a.Terkirim)) = '' THEN 'BELUM' 
                ELSE 'YA' 
              END AS StatusTerkirim,
              a.CreateDate
            FROM 
              Trans_JadwalKirimDT2 a
            LEFT JOIN 
              Ms_Part b ON b.PartID = a.PartID
            LEFT JOIN 
              $TblSOHD c ON c.PoCustomer = a.PONumber
            LEFT JOIN 
              Ms_CustomerAlamatKirim d ON d.CustomerIDAlamat = c.ShipmentID
            WHERE 
              a.TanggalKirim = '$Tanggal'
              AND a.ActualQuantity > 0";
    $Query  = $this->BJGMAS01->query($Sql);
    $Result = $Query->result();

    return $Result;
  }

  public function update_status_kirim($IdKirim, $Status)
  {
    $Update = $this->BJGMAS01->update('Trans_JadwalKirimDT2', ['Terkirim' => $Status], ['Id' => $IdKirim]);
    if ($Update) {
      return json_encode(['status_code' => 200, 'status' => 'success', 'message' => 'Status kirim berhasil diperbarui']);
    } else {
      return json_encode(['status_code' => 500, 'status' => 'error', 'message' => 'Status kirim gagal diperbarui']);
    }
  }
}