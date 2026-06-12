<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rak extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   * 		http://example.com/index.php/welcome
   *	- or -
   * 		http://example.com/index.php/welcome/index
   *	- or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();

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
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');

    $this->BJGMAS01  = $this->load->database("bjsmas01_db", true);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Daftar Rak WH";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['wh']               = $this->BJGMAS01->get_where('Ms_WarehouseStock', array('Aktif' => '1'))->result();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/master_data/rak_wh', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function rak_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_rak();
      
      $Rak      = trim(strtoupper($this->input->post('Rak')));
      $DataRak  = $this->BJGMAS01->get_where('Trans_RakHD', array('Rak' => $Rak));
      $CekRak   = $DataRak->num_rows();
      if ($CekRak == 0) {
        $DataHD = array(
          'Rak'         => $Rak,
          'WHLokasi'    => $this->input->post('WHLokasi'),
          'Status'      => trim($this->input->post('Aktivasi')),
          'QRCode'      => "1RAK-".$Rak,
          'Noted'       => ucfirst($this->input->post('Noted')),
          'CreateDate'  => date('Y-m-d H:i:s'),
          'CreateBy'    => $this->session->userdata('user_code')
        );

        $InsertHD = $this->BJGMAS01->insert('Trans_RakHD', $DataHD);
        if ($InsertHD) {
          $LastIdHD   = $this->BJGMAS01->insert_id();
          $DataDT     = array();
          $StartLoop  = 1;
          $EndLoop    = floatval($this->input->post('Isi'));
          for ($i= $StartLoop; $i <= $EndLoop ; $i++) {
            $DataDT[] = array(
              'IdHeader'        => $LastIdHD,
              'Sequent'         => $Rak.$i,
              'QRCode'          => "2RAK-".$Rak.$i
              //'QRCode'          => "2RAK-".$Rak.$i."-".$LastIdHD
            );
          }

          //echo json_encode(array('HD' => $DataHD, 'DT' => $DataDT)); exit;

          $InsertDT = $this->BJGMAS01->insert_batch('Trans_RakDT', $DataDT);
          if ($InsertDT) {
            echo json_encode(
              array(
                "status_code" => 200,
                "status"      => "success",
                "message"     => "Data Rak sukses disimpan."
              )
            );
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Data Rak DT gagal disimpan."
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data Rak HD gagal disimpan."
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data Rak ".$Rak." sudah tersedia."
          )
        );
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function rak_list()
  {
    $Draw    = intval($this->input->get("draw"));
    $Start   = intval($this->input->get("start"));
    $Length  = intval($this->input->get("length"));

    $Sql     = "SELECT 
                  a.Id, a.Rak, a.WHLokasi, a.Status, a.QRCode, 
                  CAST(a.Noted AS NVARCHAR(MAX)) AS Noted, 
                  CAST(a.CreateDate AS DATE) AS CreateDate, 
                  a.CreateBy,
                  COUNT(b.Id) AS Isi 
                FROM Trans_RakHD a
                JOIN Trans_RakDT b ON b.IdHeader = a.Id
                GROUP BY 
                    a.Id, a.Rak, a.WHLokasi, a.Status, a.QRCode, 
                    CAST(a.Noted AS NVARCHAR(MAX)), 
                    CAST(a.CreateDate AS DATE), 
                    a.CreateBy
                ORDER BY a.Rak ASC, CAST(a.CreateDate AS DATE) DESC";
    $Query   = $this->BJGMAS01->query($Sql);
    $Result  = $Query->result();
    $Data    = [];
    $No      = 1;

    foreach ($Result as $key => $value) {
      $Data[] = array(
        $value->Id,
        $No++,
        '<a href="javascript:void(0)" onclick="edit('."'".$value->Id."'".')"
          class="btn waves-effect waves-light btn-success btn-sm">
          <i class="fa fa-edit"></i>
        </a>
        <a href="javascript:void(0)" onclick="openModalDelete('."'".$value->Id."'".')"
          class="btn waves-effect waves-light btn-danger btn-sm">
          <i class="fa fa-times"></i>
        </a>',
        strtoupper($value->Status),
        $value->Rak,
        $value->WHLokasi,
        $value->Isi,
        $value->CreateDate,
        $value->CreateBy
      );
    }

    $Results = array(
      "draw"             => $Draw,
      "recordsTotal"     => $Query->num_rows(),
      "recordsFiltered"  => $Query->num_rows(),
      "data"             => $Data
    );

    echo json_encode($Results);
    exit();
  }

  public function rak_edit($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      //$data = $this->BJGMAS01->get_where('Trans_RakHD', array('Id' => $id))->row();
      $Sql     = "SELECT 
                  a.Id, a.Rak, a.WHLokasi, a.Status, a.QRCode, 
                  CAST(a.Noted AS NVARCHAR(MAX)) AS Noted, 
                  CAST(a.CreateDate AS DATE) AS CreateDate, 
                  a.CreateBy,
                  COUNT(b.Id) AS Isi 
                FROM Trans_RakHD a
                JOIN Trans_RakDT b ON b.IdHeader = a.Id
                WHERE a.Id = '$id'
                GROUP BY 
                    a.Id, a.Rak, a.WHLokasi, a.Status, a.QRCode, 
                    CAST(a.Noted AS NVARCHAR(MAX)), 
                    CAST(a.CreateDate AS DATE), 
                    a.CreateBy
                ORDER BY a.Rak ASC, CAST(a.CreateDate AS DATE) DESC";
      $Query    = $this->BJGMAS01->query($Sql);
      $Result   = $Query->row();

      $SqlDT    = "SELECT * FROM Trans_RakDT WHERE IdHeader = '$id'";
      $QueryDT  = $this->BJGMAS01->query($SqlDT);
      $ResultDT = $QueryDT->result();
      $HtmlDT   = "";
      foreach ($ResultDT as $key => $value) {
        $HtmlDT   .= "<li class='list-group-item'>".trim($value->Sequent)."</li>";
      }

      echo json_encode(
        array(
          "data_header" => $Result,
          "data_detail" => $ResultDT,
          "html_detail" => $HtmlDT
        )
      );

      //ADDING TO LOG
      $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type       = "EDIT";
      $log_data       = json_encode($Result);

      log_helper($log_url, $log_type, $log_data);
      //END LOG
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function rak_update()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->_validation_rak();

      $Id     = $this->input->post('kode');
      $Rak    = trim(strtoupper($this->input->post('Rak')));
      $DataHD = array(
        'Rak'            => $Rak,
        'WHLokasi'       => $this->input->post('WHLokasi'),
        'Status'         => trim($this->input->post('Aktivasi')),
        'QRCode'         => "1RAK-".$Rak,
        'Noted'          => ucfirst($this->input->post('Noted')),
        'CreateDate'     => date('Y-m-d H:i:s'),
        'CreateBy'       => $this->session->userdata('user_code')
      );

      $UpdateHD = $this->BJGMAS01->update('Trans_RakHD', $DataHD, array('id' => $Id));
      if ($UpdateHD) {
        $LastHD     = $this->BJGMAS01->get_where('Trans_RakHD', array('Id' => $Id))->row();
        $LastIdHD   = $LastHD->Id;
        $DataDT     = array();
        $StartLoop  = 1;
        $EndLoop    = floatval($this->input->post('Isi'));
        for ($i= $StartLoop; $i <= $EndLoop ; $i++) {
          $this->BJGMAS01->delete("Trans_RakDT", ['IdHeader' => $Id]);
          $DataDT[] = array(
            'IdHeader'        => $LastIdHD,
            'Sequent'         => $Rak.$i,
            'QRCode'          => "2RAK-".$Rak.$i."-".$LastIdHD
          );
        }

        $InsertDT = $this->BJGMAS01->insert_batch('Trans_RakDT', $DataDT);
        if ($InsertDT) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Data Rak sukses disimpan."
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data Rak DT gagal disimpan."
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data Rak HD gagal disimpan."
          )
        );
      }

    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function rak_hapus($id)
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $this->BJGMAS01->delete("Trans_RakHD", ['Id' => $id]);
      $this->BJGMAS01->delete("Trans_RakDT", ['IdHeader' => $id]);

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data berhasil dihapus"
        )
      );
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function preview_rak($id)
  {
    $data['RakIdArray']       = base64_decode($id);
    //echo base64_decode($id); exit;
    $data['group_halaman']    = "Master Data";
    $data['nama_halaman']     = "Preview Rak WH";
    $data['icon_halaman']     = "icon-layers";
    $data['perusahaan']       = $this->perusahaan->get_details();

    $this->load->view('adminx/master_data/preview_rak', $data, FALSE);
  }

  public function pilih_rak()
  {
    $RakIDArray   = $this->input->post('RakID');
    $RakItems     = array_map(function($item) {
      return $item;
    }, $RakIDArray);

    $RakMerged = implode('-', $RakItems);

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data Rak ditemukan",
        "Rak"         => $RakMerged,
        "Url"         => base_url()."rak/preview_rak/".base64_encode($RakMerged)
      )
    );
  }

  public function tampilkan_pilihan_rak()
  {
    $RakID  = explode('-', $this->input->post('RakID'));
    foreach ($RakID as $key => $value) {
      $Sql     = "SELECT * FROM Trans_RakHD WHERE Id = '$value' ORDER BY Rak ASC";
      $Query   = $this->BJGMAS01->query($Sql);
      $Result  = $Query->result();

      foreach ($Result as $key => $Response) {
        $Data[] = array(
          "Id"      => $Response->Id,
          "Rak"     => $Response->Rak,
          "QRCode"  => $Response->QRCode,
          "Details" => $this->BJGMAS01->get_where('Trans_RakDT', array('IdHeader' => $Response->Id))->result()
        );
      }
    }

    echo json_encode(
      array(
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data Rak ditemukan",
        "data"        => $Data
      )
    );
  }

  private function _validation_rak()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Rak') == '') {
      $data['inputerror'][]   = 'Rak';
      $data['error_string'][] = 'Rak is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('WHLokasi') == '') {
      $data['inputerror'][]   = 'WHLokasi';
      $data['error_string'][] = 'WH Lokasi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Isi') == '') {
      $data['inputerror'][]   = 'Isi';
      $data['error_string'][] = 'Isi is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Aktivasi') == '') {
      $data['inputerror'][]   = 'Aktivasi';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
