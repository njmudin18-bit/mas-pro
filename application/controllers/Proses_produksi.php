<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Proses_produksi extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if ($this->auth->isNotLogin()) redirect('auth/login');

    // START ROLE MANAGEMENT
    $this->contoller_name = $this->router->class;
    $this->function_name   = $this->router->method;
    $this->load->model('Rolespermissions_model');
    // END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('prosesproduksi_model', 'prosesproduksi');

    $this->BJGMAS01 = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']  = "Master Data";
      $data['nama_halaman']   = "Master Proses Produksi";
      $data['icon_halaman']   = "icon-calendar";
      $DeptID                 = "1216";
      $data['DeptID']         = $DeptID;
      $data['DeptList']       = get_department_for_proses_produksi($DeptID);
      $data['perusahaan']     = $this->perusahaan->get_details();
      $data['roles']          = $this->roles->get_alls();
      // Log akses
      log_helper(base_url().$this->contoller_name."/".$this->function_name, "VIEW", "");

      $this->load->view('adminx/master_data/proses_produksi', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function proses_add()
  {
    $this->_validation_proses();

    $Data = array(
      'DeptID'         => $this->input->post('DeptID'),
      'ProcessName'    => strtoupper($this->input->post('ProcessName')),
      'Status'         => $this->input->post('isActive'),
      'CreatedDate'    => date('Y-m-d H:i:s'),
      'CreatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $Data)); exit;

    $insert = $this->prosesproduksi->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type    = "ADD";
    $log_data    = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function proses_list()
	{
		$Draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));

    $Sql            = "EXEC dbo.GetProsesProduksi";
    $Query          = $this->BJGMAS01->query($Sql);
		$Result 		    = $Query->result();
		$Data 			    = [];
		$No 				    = 1;

		foreach ($Result as $key => $value) {
      $Isi    = "'".$value->DeptID."'";
      $HtmlE  = ($value->NoUrut != NULL) ? '<a class="dropdown-item" href="#" onclick="editLine('.$Isi.')">Edit Line</a>' : '';

      $row    = [];
      $row[]  = $value->NoUrut;
      $row[]  = ($value->NoUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                      '.$HtmlE.'
                    </div>
                  </div>
                </div>' : ''; //<a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
      //$row[]  = $value->StatusLabel;
      //$row[]  = $value->DeptID;
      $row[]  = $value->DEPTNAME;
      //$row[]  = $value->ProcessName;
      $row[]  = $value->LineName;
      $row[]  = $value->CreatedDate;
      $row[]  = $value->CreatedBy;
  
      $Data[] = $row;
    }

		$Output = array(
			"draw" 						=> $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($Output);
		exit();
	}

  public function proses_edit($id)
  {
    $data = $this->prosesproduksi->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function proses_update()
  {
    $this->_validation_proses();

    $data = array(
      'DeptID'         => $this->input->post('DeptID'),
      'ProcessName'    => strtoupper($this->input->post('ProcessName')),
      'Status'         => $this->input->post('isActive'),
      'UpdatedDate'    => date('Y-m-d H:i:s'),
      'UpdatedBy'      => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;

    $this->prosesproduksi->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function proses_deleted($id)
  {
    $data_delete    = $this->prosesproduksi->get_by_id($id); //DATA DELETE
    $data           = $this->prosesproduksi->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function get_proses_produksi()
	{
		$id 	= $this->input->post('id');
    $data = $this->prosesproduksi->get_by_dept_id($id);

    echo json_encode($data);
	}

  //LINE
  public function line_add()
  {
    $this->_validation_line();

    $DeptID     = $this->input->post('DeptIDLine', TRUE);
    //$ProsessID  = $this->input->post('ProcessNameLine', TRUE);
    $Lines      = $this->input->post('LineName', TRUE);
    $DataInsert = [];
    
    if (!empty($Lines) && is_array($Lines)) {
      foreach ($Lines as $LineName) {
        if (trim($LineName) == '') continue;

        $DataInsert[] = [
          'DeptID'      => $DeptID,
          //'ProcessID'   => $ProsessID,
          'LineName'    => ucfirst($LineName),
          'CreatedDate' => date('Y-m-d H:i:s'),
          'CreatedBy'   => $this->session->userdata('user_id')
        ];
      }
    }

    //echo json_encode(array("status" => "error", "data" => $DataInsert)); exit;

    if (!empty($DataInsert)) {
      // Mulai Transaksi (untuk keamanan data)
      $this->BJGMAS01->trans_start();

      $this->BJGMAS01->where('DeptID', $DeptID);
      //$this->BJGMAS01->where('ProcessID', $ProsessID);
      $this->BJGMAS01->delete('Trans_ProductionProcessDT');

      $this->BJGMAS01->insert_batch('Trans_ProductionProcessDT', $DataInsert);         
      $this->BJGMAS01->trans_complete();
      if ($this->BJGMAS01->trans_status() === FALSE) {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Gagal menyimpan data'
        ]);
      } else {
        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Sukses menyimpan data'
        ]);
      }
    } else {
      echo json_encode(['status' => false, 'msg' => 'Tidak ada data Line yang diproses.']);
    }
  }

  public function line_edit()
  {
    $id      = $this->input->post('Kode');
    //$DataHD  = $this->prosesproduksi->get_hd_by_id($id);
    $DataDT  = $this->prosesproduksi->get_dt_by_id($id);

    // Cek apakah data HD atau DT kosong / null
    //if (empty($DataHD) || empty($DataDT)) {
    if (empty($DataDT)) {
      echo json_encode([
        "status_code" => 404,
        "status"      => "error",
        "message"     => "Data tidak ditemukan.",
        //"first"       => null,
        "second"      => null
      ]);

      return;
    }

    echo json_encode([
      "status_code" => 200,
      "status"      => "success",
      "message"     => "Data ditemukan.",
      //"first"       => $DataHD,
      "second"      => $DataDT
    ]);
  }

  public function hapus_single_row()
  {
    $IdHeader  = $this->input->post('IdHD');
    $IdDetail  = $this->input->post('IdDt');

    //echo json_encode(array('HD' => $IdHeader, 'DT' => $IdDetail)); exit;

    $Delete = $this->BJGMAS01->delete('Trans_ProductionProcessDT', array(
      'Id'        => $IdDetail
      //'ProcessID' => $IdHeader
    ));

    if ($Delete) {
      echo json_encode([
        "status_code" => 200,
        "status"      => "success",
        "message"     => "Data sukses dihapus."
      ]);
    } else {
      echo json_encode([
        "status_code" => 500,
        "status"      => "error",
        "message"     => "Data gagal dihapus."
      ]);
    }

    exit();
  }

  private function _validation_proses()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('DeptID') == '') {
      $data['inputerror'][]   = 'DeptID';
      $data['error_string'][] = 'Departemen is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('isActive') == '') {
      $data['inputerror'][]   = 'isActive';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ProcessName') == '') {
      $data['inputerror'][]   = 'ProcessName';
      $data['error_string'][] = 'Process Name is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_line()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;
    $line                 = $this->input->post('LineName');

    if ($this->input->post('DeptIDLine') == '') {
      $data['inputerror'][]   = 'DeptIDLine';
      $data['error_string'][] = 'Departemen is required';
      $data['status']         = FALSE;
    }

    // if ($this->input->post('ProcessNameLine') == '') {
    //   $data['inputerror'][]   = 'ProcessNameLine';
    //   $data['error_string'][] = 'Process Name is required';
    //   $data['status']         = FALSE;
    // }

    if (is_array($line)) {
      foreach ($line as $i => $li) {
        if (empty($li)) {
          $data['inputerror'][]   = "LineName[$i]";
          $data['error_string'][] = 'Line Name is required';
          $data['status']         = FALSE;
        }
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}