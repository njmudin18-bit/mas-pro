<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Periodegaji_model extends CI_Model
{
  var $table          = 'Trans_PeriodeGaji';
  var $column_order   = array('StartDate', 'EndDate', 'Payday', 'Cycle', 'CreatedDate', 'CreatedBy', null);
  var $column_search  = array('StartDate', 'EndDate', 'Payday', 'Cycle', 'CreatedDate', 'CreatedBy');
  var $order          = array('Id' => 'desc');

  public function __construct()
  {
    parent::__construct();

    $this->ABSENSI = $this->load->database('absensi_local_mas', TRUE);
  }

  private function _get_datatables_query_OLD($StartDate, $EndDate)
  {
      $this->ABSENSI->select("Id, Cycle,
                              FORMAT(StartDate, 'dd MMM yyyy', 'id-ID') AS StartDate, 
                              FORMAT(EndDate, 'dd MMM yyyy', 'id-ID') AS EndDate,
                              FORMAT(Payday, 'dd MMMM yyyy', 'id-ID') AS Payday,
                              CONVERT(VARCHAR(19), CreatedDate, 120) AS CreatedDate, CreatedBy");
      $this->ABSENSI->from($this->table);
      $this->ABSENSI->where('CAST(Payday AS DATE) >=', $StartDate);
      $this->ABSENSI->where('CAST(Payday AS DATE) <=', $EndDate);

      $searchValue = $this->input->post('search')['value'];

      if ($searchValue) 
      {
          $i = 0; // Reset counter untuk loop pencarian

          // Buka kurung kurawal untuk klausa OR LIKE
          $this->ABSENSI->group_start();

          // 1. Selalu tambahkan pencarian Payday yang diformat sebagai LIKE yang pertama
          // Ini memungkinkan pencarian seperti "Februari"
          $this->ABSENSI->like("FORMAT(Payday, 'dd MMMM yyyy', 'id-ID')", $searchValue);
          $i++;

          // 2. Loop melalui kolom pencarian standar lainnya menggunakan OR LIKE
          foreach ($this->column_search as $item) 
          {
              // Lewati 'Payday' karena sudah ditangani di atas
              // CATATAN: Karena Payday di select sudah diformat, ini penting
              if ($item == 'Payday') continue; 

              // Tambahkan OR LIKE untuk kolom lainnya. 
              // Untuk kolom tanggal/datetime, Anda harus CAST ke VARCHAR/string agar bisa dicari.
              if ($item == 'StartDate' || $item == 'EndDate' || $item == 'CreatedDate') {
                  $this->ABSENSI->or_like('CAST(' . $item . ' AS VARCHAR)', $searchValue);
              } else {
                  $this->ABSENSI->or_like($item, $searchValue);
              }
              $i++;
          }
          
          // Tutup kurung kurawal
          $this->ABSENSI->group_end();
      }
      
      // ... (Bagian ordering tetap sama) ...
      if (isset($_POST['order']))
      {
        $this->ABSENSI->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
      } else if (isset($this->order)) {
        $order = $this->order;
        $this->ABSENSI->order_by(key($order), $order[key($order)]);
      }
  }

  private function _get_datatables_query($StartDate, $EndDate)
  {
      // ... (Bagian SELECT dan WHERE filter tanggal tetap sama) ...
      $this->ABSENSI->select("Id, Cycle,
                              FORMAT(StartDate, 'dd MMM yyyy', 'id-ID') AS StartDate, 
                              FORMAT(EndDate, 'dd MMM yyyy', 'id-ID') AS EndDate,
                              FORMAT(Payday, 'dd MMMM yyyy', 'id-ID') AS Payday,
                              CONVERT(VARCHAR(19), CreatedDate, 120) AS CreatedDate, CreatedBy");
      $this->ABSENSI->from($this->table);
      $this->ABSENSI->where('CAST(Payday AS DATE) >=', $StartDate);
      $this->ABSENSI->where('CAST(Payday AS DATE) <=', $EndDate);

      $searchValue = $this->input->post('search')['value'];

      if ($searchValue) 
      {
          $this->ABSENSI->group_start();

          // 1. Tambahkan pencarian untuk Payday (nama bulan penuh: 'dd MMMM yyyy')
          $this->ABSENSI->like("FORMAT(Payday, 'dd MMMM yyyy', 'id-ID')", $searchValue);
          
          // 2. Tambahkan pencarian untuk StartDate (singkatan bulan: 'dd MMM yyyy')
          $this->ABSENSI->or_like("FORMAT(StartDate, 'dd MMM yyyy', 'id-ID')", $searchValue);
          
          // 3. Tambahkan pencarian untuk EndDate (singkatan bulan: 'dd MMM yyyy')
          $this->ABSENSI->or_like("FORMAT(EndDate, 'dd MMM yyyy', 'id-ID')", $searchValue);

          // 4. Tambahkan pencarian untuk CreatedDate (datetime format)
          $this->ABSENSI->or_like("CONVERT(VARCHAR(19), CreatedDate, 120)", $searchValue);
          
          // 5. Loop melalui kolom pencarian standar lainnya
          // Kita hanya perlu mencari kolom non-tanggal (Cycle, CreatedBy, dll.)
          foreach ($this->column_search as $item) 
          {
              // Lewati semua kolom tanggal karena sudah ditangani di atas
              if ($item == 'StartDate' || $item == 'EndDate' || $item == 'Payday' || $item == 'CreatedDate') continue;
              
              // Kolom sisanya (misalnya Cycle, CreatedBy)
              $this->ABSENSI->or_like($item, $searchValue);
          }
          
          $this->ABSENSI->group_end();
      }
      
      // ... (Bagian ordering tetap sama) ...
      if (isset($_POST['order']))
      {
        $this->ABSENSI->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
      } else if (isset($this->order)) {
        $order = $this->order;
        $this->ABSENSI->order_by(key($order), $order[key($order)]);
      }
  }

  function get_datatables($StartDate, $EndDate)
  {
    $this->_get_datatables_query($StartDate, $EndDate);
    if ($_POST['length'] != -1)
      $this->ABSENSI->limit($_POST['length'], $_POST['start']);
    $query = $this->ABSENSI->get();
    
    return $query->result();
  }

  function count_filtered($StartDate, $EndDate)
  {
    $this->_get_datatables_query($StartDate, $EndDate);
    $query = $this->ABSENSI->get();

    return $query->num_rows();
  }

  public function count_all($StartDate, $EndDate)
  {
    $this->ABSENSI->from($this->table);
    // Terapkan filter tanggal yang sama seperti di _get_datatables_query
    $this->ABSENSI->where('CAST(Payday AS DATE) >=', $StartDate);
    $this->ABSENSI->where('CAST(Payday AS DATE) <=', $EndDate);

    return $this->ABSENSI->count_all_results();
  }

  public function get_by_id($id)
  {
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->where('Id', $id);
    $query = $this->ABSENSI->get();

    return $query->row();
  }

  public function get_all_data()
  {
    $this->ABSENSI->select("StartDate, EndDate, Payday, Cycle, CONVERT(VARCHAR(10), StartDate, 120) + ' - ' + CONVERT(VARCHAR(10), EndDate, 120) + ' (Gaji ke-' + CAST(Cycle AS VARCHAR(10)) + ')' AS Periode");
    $this->ABSENSI->from($this->table);
    $this->ABSENSI->order_by('Payday', 'DESC');
    $query = $this->ABSENSI->get();

    return $query->result();
  }

  public function save($data)
  {
    $this->ABSENSI->insert($this->table, $data);

    return $this->ABSENSI->insert_id();
  }

  public function update($where, $data)
  {
    $this->ABSENSI->update($this->table, $data, $where);

    return $this->ABSENSI->affected_rows();
  }

  public function delete_by_id($id)
  {
    $this->ABSENSI->where('Id', $id);
    $this->ABSENSI->delete($this->table);
  }
}
