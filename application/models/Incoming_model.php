<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incoming_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    
    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function show_fifo_card_selected($PONumberArray, $PartIDArray, $DateArray) 
  {
    if ($DateArray != 'all') {
      $this->BJGMAS01->where_in('CAST(a.CreateDate AS date)', $DateArray);
    }
    
    $this->BJGMAS01->select("a.Id, a.PONumber, a.BarcodeNumber, a.Sequent, a.PartID, 
                              CAST(a.Weight AS DECIMAL(10,2)) AS Weight, a.LotNumber, 
                              a.PartName, a.SupplierID, a.SupplierType, a.SupplierName, a.Month,
                              b.MonthName, b.Colors, b.Shapes, FORMAT(a.CreateDate, 'dd-MMM-yyyy') AS TglCetak, CAST(a.CreateDate AS date) AS TglBuat");
    $this->BJGMAS01->from('Trans_FifoCard a');
    $this->BJGMAS01->join('Ms_ColorShape b', 'b.MonthNumber = a.Month', 'left');
    $this->BJGMAS01->where_in('a.PONumber', $PONumberArray);
    $this->BJGMAS01->where_in('a.PartID', $PartIDArray);
    $this->BJGMAS01->order_by('a.PONumber', 'desc');
    $this->BJGMAS01->order_by('a.Sequent', 'asc');
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_data_rekap($PONumberArray, $PartIDArray) 
  {
    $this->BJGMAS01->select("PONumber, COUNT(*) AS QtyCetak, PartID, PartName, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE) AS TGL_CETAK");
    $this->BJGMAS01->from("Trans_FifoCard");
    $this->BJGMAS01->where_in("PONumber", $PONumberArray);
    $this->BJGMAS01->where_in("PartID", $PartIDArray);
    $this->BJGMAS01->group_by("PONumber, PartID, PartName, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE)");
    $this->BJGMAS01->order_by("PONumber", "desc");

    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_data_for_select($PONumberArray, $PartIDArray) 
  {
    $this->BJGMAS01->select("PONumber, COUNT(*) AS QtyCetak, CAST(CreateDate AS DATE) AS TglBuat, PartID, PartName");
    $this->BJGMAS01->from("Trans_FifoCard a");
    $this->BJGMAS01->where_in("a.PONumber", $PONumberArray);
    $this->BJGMAS01->where_in("a.PartID", $PartIDArray);
    $this->BJGMAS01->group_by("PONumber, PartID, PartName, CAST(CreateDate AS DATE)");
    $this->BJGMAS01->order_by("CAST(CreateDate AS DATE)", "DESC");

    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_by_id($id)
  {
    $this->BJGMAS01->from('Trans_FifoCard');
    $this->BJGMAS01->where('BarcodeNumber', $id);
    $query = $this->BJGMAS01->get();

    return $query->row();
  }

  public function delete_by_id($id)
  {
    $this->BJGMAS01->where('BarcodeNumber', $id);
    $this->BJGMAS01->delete('Trans_FifoCard');
  }

  public function show_fifo_card_by_po($PONumber) 
  {
    $this->BJGMAS01->select("a.Id, a.PONumber, a.BarcodeNumber, a.Sequent, a.PartID,
                              CAST(a.Weight AS DECIMAL(10, 2)) AS Weight, a.LotNumber, 
                              a.PartName, a.SupplierID, a.SupplierType, a.SupplierName, a.Month,
                              b.MonthName, b.Colors, b.Shapes, FORMAT(a.CreateDate, 'dd-MMM-yyyy') AS TglCetak, CAST(a.CreateDate AS date) AS TglBuat");
    $this->BJGMAS01->from('Trans_FifoCard a');
    $this->BJGMAS01->join('Ms_ColorShape b', 'b.MonthNumber = a.Month', 'left');
    $this->BJGMAS01->where_in('a.PONumber', $PONumber);
    $this->BJGMAS01->order_by('a.PONumber', 'desc');
    $this->BJGMAS01->order_by('a.Sequent', 'asc');
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_data_rekap_by_po($PONumber) 
  {
    $this->BJGMAS01->select("PONumber, COUNT(*) AS QtyCetak, PartID, PartName, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE) AS TGL_CETAK");
    $this->BJGMAS01->from("Trans_FifoCard");
    $this->BJGMAS01->where_in("PONumber", $PONumber);
    $this->BJGMAS01->group_by("PONumber, PartID, PartName, SupplierID, SupplierType, SupplierName, CAST(CreateDate AS DATE)");
    $this->BJGMAS01->order_by("PONumber", "desc");

    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function get_data_for_select_by_po($PONumber) 
  {
    $this->BJGMAS01->select("PONumber, COUNT(*) AS QtyCetak, CAST(CreateDate AS DATE) AS TglBuat, PartID, PartName");
    $this->BJGMAS01->from("Trans_FifoCard a");
    $this->BJGMAS01->where_in("a.PONumber", $PONumber);
    $this->BJGMAS01->group_by("PONumber, PartID, PartName, CAST(CreateDate AS DATE)");
    $this->BJGMAS01->order_by("CAST(CreateDate AS DATE)", "DESC");

    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  public function delete_all_barcode($id, $PartID)
  {
    $this->BJGMAS01->where('PONumber', $id);
    $this->BJGMAS01->where('PartID', $PartID);
    $this->BJGMAS01->delete('Trans_FifoCard');
  }

  public function get_all_id($id, $PartID)
  {
    $this->BJGMAS01->from('Trans_FifoCard');
    $this->BJGMAS01->where('PONumber', $id);
    $this->BJGMAS01->where('PartID', $PartID);
    $query = $this->BJGMAS01->get();

    return $query->result();
  }

  // public function _get_datatables_query($start_date, $end_date)
  // {
  //   $this->DB_MASTER->select("a.NoBukti, a.SupplierID, b.PartID, c.PartName, d.Type, d.PartnerName, a.CreateDate"); //e.QtyCetak,
  //   $this->DB_MASTER->from("Trans_POHD a");
  //   $this->DB_MASTER->join("Trans_PODT1 b", "a.NoBukti = b.NoBukti", "left");
  //   $this->DB_MASTER->join("Ms_Part c", "b.PartID = c.PartID", "left");
  //   $this->DB_MASTER->join("Ms_Partner d", "a.SupplierID = d.PartnerID", "left");
  //   $this->DB_MASTER->where("a.SupplierID IN ('TMS', 'SE008', 'IN022', 'RI001', 'SU029', 'AS002', 'TE002')");
  //   $this->DB_MASTER->where("CAST(a.CreateDate AS DATE) BETWEEN '$start_date' AND '$end_date'");
  //   $this->DB_MASTER->group_by(array("a.NoBukti", "a.SupplierID", "b.PartID", "c.PartName", "d.Type", "d.PartnerName", "a.CreateDate"));
  //   $this->DB_MASTER->order_by("a.CreateDate", "desc");

  //   $i = 0;
  //   foreach ($this->column_search as $item) // loop column 
  //   {
  //     if($_POST['search']['value']) // if datatable send POST for search
  //     {
        
  //       if($i===0) // first loop
  //       {
  //         $this->DB_MASTER->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
  //         $this->DB_MASTER->like($item, $_POST['search']['value']);
  //       }
  //       else
  //       {
  //         $this->DB_MASTER->or_like($item, $_POST['search']['value']);
  //       }

  //       if(count($this->column_search) - 1 == $i) //last loop
  //         $this->DB_MASTER->group_end(); //close bracket
  //     }
  //     $i++;
  //   }
    
  //   if(isset($_POST['order'])) // here order processing
  //   {
  //     $this->DB_MASTER->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
  //   } 
  //   else if(isset($this->order))
  //   {
  //     $order = $this->order;
  //     $this->DB_MASTER->order_by(key($order), $order[key($order)]);
  //   }
  // }

  // public function get_datatables($start_date, $end_date)
  // {
  //   $this->_get_datatables_query($start_date, $end_date);
  //   if($_POST['length'] != -1)
  //   $this->DB_MASTER->limit($_POST['length'], $_POST['start']);
  //   $query = $this->DB_MASTER->get();

  //   return $query->result();
  // }

  // public function count_filtered($start_date, $end_date)
  // {
  //   $this->_get_datatables_query($start_date, $end_date);
  //   $query = $this->DB_MASTER->get();

  //   return $query->num_rows();
  // }

  // public function count_all()
  // {
  //   $this->DB_MASTER->from($this->table);

  //   return $this->DB_MASTER->count_all_results();
  // }
  

  // public function get_selected_po($PONumberArray) {
  //   $this->DB_MASTER->select("a.NoBukti, a.SupplierID, b.PartID, c.PartName, d.Type, d.PartnerName, a.CreateDate");
  //   $this->DB_MASTER->from("Trans_POHD a");
  //   $this->DB_MASTER->join("Trans_PODT1 b", "a.NoBukti = b.NoBukti", "left");
  //   $this->DB_MASTER->join("Ms_Part c", "b.PartID = c.PartID", "left");
  //   $this->DB_MASTER->join("Ms_Partner d", "a.SupplierID = d.PartnerID", "left");
  //   $this->DB_MASTER->where_in("a.NoBukti", $PONumberArray);
  //   $this->DB_MASTER->group_by(array("a.NoBukti", "a.SupplierID", "b.PartID", "c.PartName", "d.Type", "d.PartnerName", "a.CreateDate"));
  //   $this->DB_MASTER->order_by("a.CreateDate", "desc");
  //   $query = $this->DB_MASTER->get();

  //   return $query->result();
  // }

  // public function get_selected_po_edit($PONumber, $PartID) {
  //   $this->DB_MASTER->select("PONumber, PartID, PartName, Sequent, SupplierID, SupplierType, SupplierName, Month");
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
  //   $this->DB_MASTER->group_by('PONumber, PartID, PartName, Sequent, SupplierID, SupplierType, SupplierName, Month');
  //   $query = $this->DB_MASTER->get('Trans_FifoCard');

  //   return $query->result();
  // }

  // public function get_selected_po_group($PONumber, $PartID) {
  //   $this->DB_MASTER->select("*");
  //   $this->DB_MASTER->where_in('PONumber', $PONumber);
  //   $this->DB_MASTER->where_in('PartID', $PartID);
  //   $this->DB_MASTER->group_by('PONumber, PartID');
  //   $query = $this->DB_MASTER->get('Trans_FifoCard');

  //   return $query->result();
  // }

  // public function get_max_barcode_by_po_only($PONumber) {
  //   $this->DB_MASTER->select_max('Sequent');
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $query = $this->DB_MASTER->get('Trans_FifoCard');

  //   return $query->row();
  // }

  // public function get_max_barcode_by_po($PONumber, $PartID) {
  //   $this->DB_MASTER->select_max('Sequent');
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
  //   $query = $this->DB_MASTER->get('Trans_FifoCard');

  //   return $query->row();
  // }

  // public function get_max_barcode_by_po_group($PONumber, $PartID) {
  //   $this->DB_MASTER->select_max('Sequent');
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
  //   $query = $this->DB_MASTER->get('Trans_FifoCard');

  //   return $query->row();
  // }

  // public function get_count_barcode_by_po_part($PONumber, $PartID) {
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
  //   $this->DB_MASTER->from('Trans_FifoCard');
  //   $query = $this->DB_MASTER->count_all_results();

  //   return $query;
  // }

  // public function get_last_fifo_by_po_and_item($PONumber, $PartID) {
  //   $this->DB_MASTER->select_max('Sequent');
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
  //   $this->DB_MASTER->from('Trans_FifoCard');
  //   $query = $this->DB_MASTER->count_all_results();

  //   return $query;
  // }

  // public function save_batch($ArrayData)
  // {
  //   $this->DB_MASTER->insert_batch("Trans_FifoCard", $ArrayData);

  //   return $this->DB_MASTER->insert_id();
  // }

  //GET FIFO CARD BY PONUMBER
  // public function get_fifo_by_po($PONumber) {
  //   $this->DB_MASTER->select("COUNT(*) AS QtyLabel, PONumber, PartID, PartName, SupplierID, 
  //                             SupplierType, SupplierName, CAST(CreateDate AS DATE) AS TanggalKedatangan");
  //   $this->DB_MASTER->from("Trans_FifoCard");
  //   $this->DB_MASTER->where("PONumber", $PONumber);
  //   $this->DB_MASTER->group_by("PONumber, PartID, PartName, SupplierType, SupplierID, 
  //                               SupplierType, SupplierName, CAST(CreateDate AS DATE)");
  //   $this->DB_MASTER->order_by("PartID");
  //   $this->DB_MASTER->order_by("TanggalKedatangan", "DESC");

  //   $query = $this->DB_MASTER->get();

  //   return $query->result();
  // }

  //GET FIFO CARD BY PONUMBER AND DATE
  // public function get_fifo_by_po_and_date($PONumber, $ArrivalDate, $PartID) {
  //   $this->DB_MASTER->select("*, CAST(CreateDate AS DATE) AS TanggalKedatangan");
  //   $this->DB_MASTER->from("Trans_FifoCard");
  //   $this->DB_MASTER->where("PONumber", $PONumber);
  //   $this->DB_MASTER->where("PartID", $PartID);
  //   $this->DB_MASTER->where("DATE(CreateDate)", $ArrivalDate);
  //   $this->DB_MASTER->order_by("CreateDate", "DESC");

  //   $query = $this->DB_MASTER->get();

  //   return $query->result();
  // }

  // public function get_qty_label($PONumber, $PartID) {
  //   $this->DB_MASTER->where('PONumber', $PONumber);
  //   $this->DB_MASTER->where('PartID', $PartID);
    
  //   return $this->DB_MASTER->count_all_results('Trans_FifoCard');
  // }
}