<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_incoming_part_model extends CI_Model {
    var $table          = 'Trans_ScanIncomingPart';
    var $column_order   = array('a.BarcodeNumber', 'a.CreateDate', 'a.CreateBy', 'b.Sequent', 'b.PartID', 'b.PartName', 'b.SupplierID', 'b.SupplierType', 'b.SupplierName', null);
    var $column_search  = array('a.BarcodeNumber', 'a.CreateDate', 'a.CreateBy', 'b.Sequent', 'b.PartID', 'b.PartName', 'b.SupplierID', 'b.SupplierType', 'b.SupplierName');
    var $order          = array('a.CreateDate' => 'desc');

    public function __construct()
    {
      parent::__construct();
      $this->load->database();

      $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
    }

    public function _get_datatables_query($start_date, $end_date)
    {
      $this->BJGMAS01->select("a.BarcodeNumber, FORMAT(a.CreateDate, 'yyyy-MM-dd HH:mm:ss') AS CreateDate, a.CreateBy, b.Sequent, b.PartID, b.PartName, b.SupplierID, b.SupplierType, b.SupplierName");
      $this->BJGMAS01->from("Trans_ScanIncomingPart a");
      $this->BJGMAS01->join("Trans_FifoCard b", "b.BarcodeNumber = a.BarcodeNumber", "left");

      // ✅ Filter tanggal yang benar
      $this->BJGMAS01->where("CAST(a.CreateDate AS DATE) >=", $start_date);
      $this->BJGMAS01->where("CAST(a.CreateDate AS DATE) <=", $end_date);

      // Search filtering
      if (!empty($_POST['search']['value'])) {
          $this->BJGMAS01->group_start();
          foreach ($this->column_search as $key => $item) {
              if ($key === 0) {
                  $this->BJGMAS01->like($item, $_POST['search']['value']);
              } else {
                  $this->BJGMAS01->or_like($item, $_POST['search']['value']);
              }
          }
          $this->BJGMAS01->group_end();
      }

      // Ordering
      if (isset($_POST['order'])) {
          $column_index = $_POST['order']['0']['column'];
          $column_dir = $_POST['order']['0']['dir'];
          $order_column = $this->column_order[$column_index];
          $this->BJGMAS01->order_by($order_column, $column_dir);
      } elseif (isset($this->order)) {
          foreach ($this->order as $key => $val) {
              $this->BJGMAS01->order_by($key, $val);
          }
      } else {
          $this->BJGMAS01->order_by('a.CreateDate', 'DESC');
      }
    }

    public function get_datatables($start_date, $end_date)
    {
        $this->_get_datatables_query($start_date, $end_date);

        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        if ($length != -1) {
            $this->BJGMAS01->limit($length, $start);
        }

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

    public function get_by_id($id)
    {
      $this->BJGMAS01->from($this->table);
      $this->BJGMAS01->where('BarcodeNumber', $id);
      $query = $this->BJGMAS01->get();

      return $query->row();
    }

    public function save($data)
    {
      $this->BJGMAS01->insert($this->table, $data);
      
      return $this->BJGMAS01->insert_id();
    }

    public function delete_by_id($id)
    {
      $this->BJGMAS01->where('BarcodeNumber', $id);
      $this->BJGMAS01->delete($this->table);
    }

    public function check_barcode_fifo($BarcodeNumber)
    {
      if (strpos($BarcodeNumber, 'MT') !== false) {
        return true; // Diterima
      } else {
        return false; // Ditolak
      }
    }

    public function is_barcode_exists($BarcodeNumber)
    {
      $this->BJGMAS01->from('Trans_ScanIncomingPart');
      $this->BJGMAS01->where('BarcodeNumber', $BarcodeNumber);
      $query = $this->BJGMAS01->get();

      return $query->num_rows() > 0; // true jika ada, false jika belum
    }

    public function get_incoming_part_report($start_date, $end_date)
    {
      $this->BJGMAS01->select("PONumber, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE) AS PrintDate");
      $this->BJGMAS01->from("Trans_FifoCard");
      $this->BJGMAS01->where("CAST(CreateDate AS DATE) BETWEEN '$start_date' AND '$end_date'");
      $this->BJGMAS01->group_by("PONumber, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE)");
      $this->BJGMAS01->order_by("CAST(CreateDate AS DATE)", "DESC");

      return $this->BJGMAS01->get();
    }

    public function get_header_po($PONumber, $TblPO)
    {
      $this->BJGMAS01->select("a.NoBukti, a.SupplierID, b.PartnerID, b.PartnerName, b.Type");
      $this->BJGMAS01->from("$TblPO a");
      $this->BJGMAS01->join("Ms_Partner b", "b.PartnerID = a.SupplierID", "left");
      $this->BJGMAS01->where("a.NoBukti", $PONumber);

      return $this->BJGMAS01->get();
    }

    public function get_detail_po($PONumber, $TblPODT)
    {
      $this->BJGMAS01->select("a.NoBukti, a.PartID, b.PartName, COUNT(c.BarcodeNumber) AS JlhLabel");
      $this->BJGMAS01->from("$TblPODT a");
      $this->BJGMAS01->join("Ms_Part b", "b.PartID = a.PartID", "left");
      $this->BJGMAS01->join("Trans_FifoCard c", "c.PONumber = a.NoBukti AND c.PartID = a.PartID", "left");
      $this->BJGMAS01->where("a.NoBukti", $PONumber);
      $this->BJGMAS01->group_by(array("a.NoBukti", "a.PartID", "b.PartName"));

      return $this->BJGMAS01->get();
    }

    public function get_fifocard_by_item_and_po($PONumber, $PartID)
    {
      $this->BJGMAS01->select("a.Id, a.PONumber, a.BarcodeNumber, a.Sequent, a.PartID, 
                               a.PartName, a.SupplierName, a.SupplierType, a.Month, 
                               a.LotNumber, FORMAT(a.CreateDate, 'yyyy-MM-dd HH:mm:ss') AS CreateDate, 
                              b.CreateDate AS TglKedatangan, b.CreateBy AS ScanBy");
      $this->BJGMAS01->from("Trans_FifoCard a");
      $this->BJGMAS01->join("Trans_ScanIncomingPart b", "b.BarcodeNumber = a.BarcodeNumber", "left");
      $this->BJGMAS01->where("a.PONumber", $PONumber);
      $this->BJGMAS01->where("a.PartID", $PartID);
      $this->BJGMAS01->order_by("a.Sequent", "ASC");

      return $this->BJGMAS01->get();
    }
}