<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse_mpr extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    //start tidak usah di ubah
    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if ($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
    $this->contoller_name = $this->router->class;
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END


    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    //end tidak usah di ubah

    $this->load->model('barcode_model', 'barcode_sales');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']   = "WAREHOUSE";
      $data['nama_halaman']   = "WAREHOUSE MPR";
      $data['icon_halaman']   = "icon-airplay";
      $data['perusahaan']     = $this->perusahaan->get_details();

      //ADDING TO LOG
      $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type   = "VIEW";
      $log_data   = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG
      $this->load->view('adminx/warehouse/monitoring_mpr_warehouse', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  //PROSES SIMPAN MPR DETAIL
  public function simpan_mpr_detail()
  {
    $PartID         = $this->input->post('part_ids');
    $PartName       = $this->input->post('part_names');
    $QtyItemArray   = $this->input->post('qty_items');
    $StdPkgArray    = $this->input->post('qty_std_pkgs');
    $NoBuktiArray   = substr($this->input->post('no_buktis'), 0, -1);
    $NoBuktiArray   = explode('|', $NoBuktiArray);

    $idBuktiArray   = substr($this->input->post('id_mpr_details'), 0, -1);
    $idBuktiArray   = explode('|', $idBuktiArray);

    $id_array       = array();
    foreach ($idBuktiArray as $key => $value) {
      array_push($id_array, $value);
    }

    $second_DB      = $this->load->database('bjsmas01_db', TRUE);
    $second_DB->select('*');
    $second_DB->where_in('id', $id_array);
    $query          = $second_DB->get('tbl_monitoring_mpr_detail');
    $data           = $query->result();
    $array_data     = array();

    echo json_encode($data);

    // foreach ($data as $key => $value) {
    //   $array_data[] = array(
    //     'id'              => $value->id,
    //     'NoBukti'         => $value->NoBukti,
    //     'PartID'          => $value->PartID,
    //     'PartName'        => $value->PartName,
    //     'Qty'             => floatval($value->Qty),
    //     'StandartPacking' => floatval($value->StandartPacking),
    //     'StockSimpan'     => floatval($value->StockSimpan),
    //     'QtySisaProduksi' => floatval($value->QtySisaProduksi),
    //     'UnitID'          => $value->UnitID,
    //     'LocationID'      => $value->LocationID,
    //     'Keterangan'      => $value->Keterangan,
    //     'UnitID'          => $value->UnitID,
    //     'StatusPPIC'      => $value->StatusPPIC,
    //     'CreatebyPPIC'    => $value->CreatebyPPIC,
    //     'CreatedatePPIC'  => $value->CreatedatePPIC,
    //     'StatusWH'        => $value->StatusWH,
    //     'CreatebyWH'      => $value->CreatebyWH,
    //     'CreatedateWH'    => $value->CreatedateWH,
    //     'StatusPR'        => $value->StatusPR,
    //     'CreatebyPR'      => $value->CreatebyPR,
    //     'CreatedatePR'    => $value->CreatedatePR
    //   );
    // }
  }

  //PROSES DAN CEK MPR
  public function proses_mpr()
  {

    $item_array = $this->input->post('items');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $second_DB->select('*');
    $second_DB->where_in('NoBukti', $item_array);
    $query  = $second_DB->get('tbl_monitoring_mpr_detail');
    $data   = $query->result();
    //SET SEBAGAI ARRAY DISINI
    $array_data = array();
    foreach ($query->result() as $key => $value) {
      $array_data[] = array(
        'id'              => $value->id,
        'NoBukti'         => $value->NoBukti,
        'PartID'          => $value->PartID,
        'PartName'        => $value->PartName,
        'Qty'             => floatval($value->Qty),
        'StandartPacking' => floatval($value->StandartPacking),
        'StockSimpan'     => floatval($value->StockSimpan),
        'QtySisaProduksi' => floatval($value->QtySisaProduksi),
        'UnitID'          => $value->UnitID,
        'LocationID'      => $value->LocationID,
        'Keterangan'      => $value->Keterangan,
        'UnitID'          => $value->UnitID,
        'StatusPPIC'      => $value->StatusPPIC,
        'CreatebyPPIC'    => $value->CreatebyPPIC,
        'CreatedatePPIC'  => $value->CreatedatePPIC,
        'StatusWH'        => $value->StatusWH,
        'CreatebyWH'      => $value->CreatebyWH,
        'CreatedateWH'    => $value->CreatedateWH,
        'StatusPR'        => $value->StatusPR,
        'CreatebyPR'      => $value->CreatebyPR,
        'CreatedatePR'    => $value->CreatedatePR
      );
    }

    //JUMLAH KAN DISINI
    foreach ($array_data as $row) {
      if (!isset($result[$row['PartID']])) {
        $result[$row['PartID']] = $row;
      } else {
        $result[$row['PartID']]['Qty'] += $row['Qty'];
      }
    }

    $input  = "";
    $html   = "";
    $no     = 1;
    $no2    = 1;
    foreach ($result as $key => $value) {

      $PartID         = $value['PartID'];
      $item_mpr       = "";
      $item_qty       = "";
      $item_std_pkg   = "";
      $item_id        = "";

      $filtered_array = array_filter($array_data, function ($obj) use ($PartID) {
        return $obj['PartID'] == $PartID;
      });

      foreach ($filtered_array as $key => $value) {
        $item_mpr     .= $value['NoBukti'] . '|';
        $item_qty     .= number_format($value['Qty'], 4) . '|';
        $item_std_pkg .= number_format($value['StandartPacking'], 4) . '|';
        $item_id      .= $value['id'] . '|';
      }

      $html .= '
      <tr>
        <td class="text-right">' . $no++ . '</td>
        <td class="text-center">
          <input type="hidden" id="part_id_' . $no2 . '" name="part_id_' . $no2 . '" value="' . $value['PartID'] . '" >
          <input type="hidden" id="part_name_' . $no2 . '" name="part_name_' . $no2 . '" value="' . $value['PartName'] . '" >
          <input type="hidden" id="qty_item_' . $no2 . '" name="qty_item_' . $no2 . '" value="' . $item_qty . '" >
          <input type="hidden" id="qty_std_pkg_' . $no2 . '" name="qty_std_pkg_' . $no2 . '" value="' . $item_std_pkg . '" >
          <input type="hidden" id="no_bukti_' . $no2 . '" name="no_bukti_' . $no2 . '" value="' . $item_mpr . '" >
          <input type="hidden" id="id_' . $no2 . '" name="id_' . $no2 . '" value="' . $item_id . '" >
          <button id="btn_' . $no2 . '" onclick="simpan_multiple_item(' . $no2 . ')" type="button" class="btn btn-primary btn-sm">PROSES</button>
        </td>
        <td>' . $value['PartID'] . '</td>
        <td>' . $value['PartName'] . '</td>
        <td class="text-right">' . number_format($value['Qty'], 4) . '</td>
        <td class="text-right">' . number_format($value['StandartPacking'], 4) . '</td>
        <td class="text-right">' . number_format($value['StockSimpan'], 4) . '</td>
        <td class="text-right">' . number_format($value['QtySisaProduksi'], 4) . '</td>
        <td>' . $value['UnitID'] . '</td>
        <td>' . $value['LocationID'] . '</td>
      </tr>';
      $no2++;
    }

    echo json_encode(
      array(
        "status_code"   => 200,
        "status"        => "success",
        "message"       => "Sukses menampilkan data",
        "array_origin"  => $query->result(),
        "array_filter"  => $filtered_array,
        "array_merge"   => $result,
        "mpr_item"      => $item_array,
        "html"          => $html
      )
    );
  }

  public function proses_mpr_OLD()
  {
    $item_array = $this->input->post('items');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $second_DB->select('*');
    $second_DB->where_in('NoBukti', $item_array);
    $query  = $second_DB->get('tbl_monitoring_mpr_detail');
    $data   = $query->result();

    $array_data = array();
    $isi        = array();
    foreach ($query->result() as $key => $value) {
      $array_data[] = array(
        'id'              => $value->id,
        'NoBukti'         => $value->NoBukti,
        'PartID'          => $value->PartID,
        'PartName'        => $value->PartName,
        'Qty'             => floatval($value->Qty),
        // 'StandartPacking' => $value->StandartPacking,
        // 'StockSimpan'     => $value->StockSimpan,
        // 'QtySisaProduksi' => $value->QtySisaProduksi,
        // 'UnitID'          => $value->UnitID,
        // 'LocationID'      => $value->LocationID,
        // 'Keterangan'      => $value->Keterangan,
        // 'UnitID'          => $value->UnitID,
        // 'StatusPPIC'      => $value->StatusPPIC,
        // 'CreatebyPPIC'    => $value->CreatebyPPIC,
        // 'CreatedatePPIC'  => $value->CreatedatePPIC,
        // 'StatusWH'        => $value->StatusWH,
        // 'CreatebyWH'      => $value->CreatebyWH,
        // 'CreatedateWH'    => $value->CreatedateWH,
        // 'StatusPR'        => $value->StatusPR,
        // 'CreatebyPR'      => $value->CreatebyPR,
        // 'CreatedatePR'    => $value->CreatedatePR
      );
    }

    $new_array = $this->returnUniqueProperty($array_data, 'PartID');

    echo json_encode(
      array(
        'array_old' => $query->result(),
        'array_new' => $new_array
      )
    );
  }

  public function ceklis_update()
  {
    $id       = $this->input->post('id');
    $value     = $this->input->post('value');
    $qtyStd     = $this->input->post('qtyStd');
    $stockSimpan     = $this->input->post('stockSimpan');
    $qtySisa     = $this->input->post('qtySisa');

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $isi_ceklis = $value == 0 ? '1' : '0';
    $data = array(
      'StandartPacking' => floatval($qtyStd),
      'StockSimpan' => floatval($stockSimpan),
      'QtySisaProduksi' => floatval($qtySisa),
      'StatusWH' => $isi_ceklis,
      'CreateByWH' => $this->session->userdata('user_name'),
      'CreateDateWH' => date('Y-m-d H:i:s')
    );

    $second_DB->where('id', $id);
    $second_DB->update('tbl_monitoring_mpr_detail', $data);
    $sql = "SELECT NoBukti
            FROM tbl_monitoring_mpr_detail
            WHERE id = '$id'";

    $query         = $second_DB->query($sql);
    $result       = $query->row();

    echo json_encode(
      array(
        "status_code" => 200,
        "status"     => "success",
        "message"   => "sukses menampilkan data",
        "data"       => $data,
        "nobukti"   => $result->NoBukti
      )
    );
  }

  public function mpr_detail()
  {
    $nobukti     = $this->input->post('nobukti');
    $trans = explode('/', $nobukti);
    $tahunbulan = $trans[2];
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);


    $sql = "SELECT a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking AS QtyStd, a.StockSimpan, SUM(c.Qty) AS Stock, a.UnitID, a.LocationID, a.Keterangan, a.StatusWH  
            FROM tbl_monitoring_mpr_detail a
            LEFT JOIN tbl_standart_packing b ON b.PartID = a.PartID AND b.location = a.LocationID
            LEFT JOIN Buku_Stock$tahunbulan c ON c.PartID = a.PartID AND c.locationID = a.LocationID AND c.nobukti <> a.nobukti
            WHERE a.NoBukti = '$nobukti'
            GROUP BY a.id, a.NoBukti,  a.PartID, a.PartName,  a.Qty, b.standartpacking, a.StockSimpan, a.UnitID, a.LocationID, a.Keterangan, a.StatusWH
            ORDER BY a.LocationID DESC
            ";

    $query         = $second_DB->query($sql);
    $result       = $query->result();
    $no           = 1;

    if (count($result) > 0) {
      $text = '';
      foreach ($result as $key => $value) {
        $coret = $value->StatusWH == 1 ? 'class_coret' : '';
        $pro = '';

        if ($value->StockSimpan == 0) {
          $stock = $value->Stock;
        } else {
          $stock = $value->StockSimpan;
        }

        if ($value->QtyStd == null) {
          $QtyStandart = 0;
        } else {
          $QtyStandart = $value->QtyStd;
        }

        $nilai1 = $value->Qty;
        // $nilai2 = $value->QtyStd;
        $nilai2 = $QtyStandart;
        $nilai3 = $stock;
        $hasil  = 0;
        if ($nilai2 > $nilai3) {
          $hasil = $nilai3 - $nilai1;
        } else {
          if ($nilai2 == null) {
            $hasil = 0;
          } else {
            if ($nilai1 > $nilai2) {
              if ($nilai1 % $nilai2 != 0) {
                $hasil = $nilai2 - ($nilai1 % $nilai2);
              }
            } else {
              $hasil = $nilai2 - $nilai1;
            }
          }
        }

        // $isiStock = "'".$value->id."', '".$value->QtyStd."', '".$stock."', '".$hasil."'";
        $isiStock = "'" . $value->id . "', '" . $QtyStandart . "', '" . $stock . "', '" . $hasil . "'";
        if ($value->StatusWH == 1) {
          $pro = '<input type="checkbox"  name="ceklis" id="ceklis' . $value->id . '" value="' . $value->StatusWH . '" onclick="ceklis(' . $isiStock . ')" checked="checked">';
        } else {
          $pro = '<input type="checkbox"  name="ceklis" id="ceklis' . $value->id . '" value="' . $value->StatusWH . '" onclick="ceklis(' . $isiStock . ')" >';
        }

        if ($hasil < 0) {
          $pro1 = '<td class="text-right text-danger">' . $hasil . '</td>';
        } else {
          $pro1 = '<td class="text-right ">' . $hasil . '</td>';
        }


        $text .= '
        <div class="to-do-label">
          <div class="checkbox-fade fade-in-primary">
            <label class="check-task">' . $pro . '
              <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
              </span>
              <span class="task-title-sp ' . $coret . '">' . $value->PartName . '</span>
            </label>
          </div>
        </div>
        <table class="table table-striped table-responsive">
          <thead>
            <tr>
              <th>No.</th>
              <th>PartID</th>
              <th>Qty</th>
              <th>Standart Packing</th>
              <th>Stock</th>
              <th>Qty Sisa Produksi</th>
              <th>Unit ID</th>
              <th>Location ID</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>' . $no++ . '</td>
              <td>' . $value->PartID . '</td>
              <td class="text-right">' . number_format($value->Qty, 2) . '</td>
              <td class="text-right">' . $QtyStandart . '</td>
              <td class="text-right">' . number_format($stock, 2) . '</td>' . $pro1 . '
              <td>' . $value->UnitID . '</td>
              <td>' . $value->LocationID . '</td>
            </tr>
          </tbody>
        </table>
        <br>
        ';
      }

      echo json_encode(
        array(
          "status_code" => 200,
          "status"     => "success",
          "message"   => "sukses menampilkan data",
          "data"       => $result,
          "html"       => $text
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 409,
          "status"     => "error",
          "message"   => "data blm ada",
          "data"       => array(),
          "html"       => '<h3>Data tidak ada</h3>'
        )
      );
    }
  }

  public function kirim_mpr_wh()
  {
    # code...
    $nobukti   = $this->input->post('nobukti');
    $loc_id   = $this->input->post('loc_id');


    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan 
            FROM tbl_monitoring_mpr 
            WHERE NoBukti = '$nobukti'";

    $query         = $second_DB->query($sql);
    $result       = $query->row();
    $data         = [];

    $NoBukti = $result->NoBukti;
    $JobDate = substr($result->JobDate, 0, -4);
    $PartID = $result->PartID;
    $PartName = $result->PartName;
    $Qty = $result->Qty;
    $Keterangan = $result->Keterangan;

    $data = array(
      'NoBukti' => $NoBukti,
      'JobDate' => $JobDate,
      'PartID' => $PartID,
      'PartName' => $PartName,
      'Qty' => (int)$Qty,
      'Keterangan' => $Keterangan,
      'CreateBy' => $this->session->userdata('user_name'),
      'CreateDate' => date('Y-m-d H:i:s'),
      'loc_id' => $loc_id
    );

    $query_wh1       = $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                         WHERE NoBukti = '$NoBukti' AND loc_id = 'WH001'");
    $cek_wh1        = $query_wh1->num_rows();

    if ($cek_wh1 == 1) {

      $query       = $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                       WHERE NoBukti = '$NoBukti' AND loc_id = 'WH002'");
      $cek         = $query->num_rows();

      if ($cek == 0) {

        $second_DB->trans_start();
        $insert = $second_DB->insert('tbl_monitoring_mpr', $data);
        $second_DB->trans_complete();

        if ($second_DB->trans_status() === FALSE) {
          echo json_encode(
            array(
              "status_code" => 400,
              "status"       => "error",
              "message"     => "MPR gagal dikirim!",
              "data"         => $data
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"       => "success",
              "message"     => "MPR sukses dikirim!",
              "data"         => $data
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 409,
            "status"       => "success",
            "message"     => "MPR sudah dikirim!",
            "data"         => $data
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 405,
          "status"       => "error",
          "message"     => "MPR blm di proses!",
          "data"         => $data
        )
      );
    }
  }

  public function proses_mpr_wh()
  {
    $nobukti   = $this->input->post('nobukti');
    $loc_id   = $this->input->post('loc_id');

    $second_DB  = $this->load->database('bjsmas01_db', TRUE);
    $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan 
            FROM tbl_monitoring_mpr 
            WHERE NoBukti = '$nobukti'";

    $query         = $second_DB->query($sql);
    $result       = $query->row();
    $data         = [];

    $NoBukti = $result->NoBukti;
    $JobDate = substr($result->JobDate, 0, -4);
    $PartID = $result->PartID;
    $PartName = $result->PartName;
    $Qty = $result->Qty;
    $Keterangan = $result->Keterangan;

    $data = array(
      'NoBukti' => $NoBukti,
      'JobDate' => $JobDate,
      'PartID' => $PartID,
      'PartName' => $PartName,
      'Qty' => (int)$Qty,
      'Keterangan' => $Keterangan,
      'CreateBy' => $this->session->userdata('user_name'),
      'CreateDate' => date('Y-m-d H:i:s'),
      'loc_id' => $loc_id
    );

    $query       = $second_DB->query("SELECT * FROM tbl_monitoring_mpr 
                                     WHERE NoBukti = '$NoBukti' AND loc_id = 'WH001'");
    $cek         = $query->num_rows();

    if ($cek == 0) {
      $second_DB->trans_start();
      $insert = $second_DB->insert('tbl_monitoring_mpr', $data);
      $second_DB->trans_complete();
      if ($second_DB->trans_status() === FALSE) {
        echo json_encode(
          array(
            "status_code" => 400,
            "status"       => "error",
            "message"     => "MPR gagal diproses!",
            "data"         => $data
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"       => "success",
            "message"     => "MPR sukses diproses!",
            "data"         => $data
          )
        );
      }
    } else {
      echo json_encode(
        array(
          "status_code" => 409,
          "status"       => "success",
          "message"     => "MPR sudah diproses!",
          "data"         => $data
        )
      );
    }
  }

  public function monitoring_mpr_list_new()
  {

    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $start_date = $this->input->post('start_date');
    $end_date   = $this->input->post('end_date');
    $second_DB  = $this->load->database('bjsmas01_db', TRUE);

    $sql = "SELECT NoBukti, JobDate, PartID, PartName, Qty, Keterangan, CreateBy, CreateDate, loc_id
            FROM tbl_monitoring_mpr
            WHERE CAST(CreateDate AS date) BETWEEN '$start_date' AND '$end_date'  AND loc_id='PPIC001'
            ORDER BY CreateDate DESC";


    $query  = $second_DB->query($sql);
    $result = $query->result();
    $data   = [];
    $no     = 1;


    foreach ($result as $key => $value) {
      $noBuktiMpr = "'" . $value->NoBukti . "'";
      $jumlah_mpr = get_qty_mpr($value->NoBukti);
      $stepnew    = cek_qty($value->NoBukti);
      $status     = '';
      if ($jumlah_mpr->jumlah_mpr == 4) {
        $status   = '<span class="badge badge-success" style="font-size: 14px;">COMPLETE</span>
                    <br>
                    <span style="font-size: 12px;">' . $stepnew . ' dari 4 </span>';
      } else {
        $status = '<span class="badge badge-success" style="font-size: 14px;">OPEN</span>
                    <br>
                    <span style="font-size: 12px;">' . $stepnew . ' dari 4 </span>';
      }

      $data[] = array(
        $no++,
        //'<input type="checkbox" id="item_' . $key . '" name="item" value="' . $value->NoBukti . '">',
        '-',
        '<button class="btn btn-info btn-block text-white btn-sm" onclick="mpr_detail(' . $noBuktiMpr . ')">' . $value->NoBukti . '</button>',
        substr($value->JobDate, 0, -4),
        $value->PartID,
        $value->PartName,
        $value->Qty,
        $value->Keterangan,
        $value->CreateBy,
        substr($value->CreateDate, 0, -4),
        $status,
        '<button class="btn btn-warning btn-block text-white btn-sm" onclick="proses_mpr_wh(' . $noBuktiMpr . ')">PROSES</button>',
        '<button class="btn btn-primary btn-block text-white btn-sm" onclick="kirim_mpr_wh(' . $noBuktiMpr . ')">KIRIM</button>',
        '<button class="btn btn-danger btn-block text-white btn-sm" onclick="lihat_status(' . $noBuktiMpr . ')">DETAILS STATUS</button>'
      );
    }

    $result = array(
      "draw"             => $draw,
      "recordsTotal"     => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
      "data"             => $data
    );

    echo json_encode($result);
    exit();
  }
}
