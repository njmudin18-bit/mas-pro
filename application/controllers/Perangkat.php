<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat extends CI_Controller
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
    $this->load->model('jenisperangkat_model', 'jenis');
    $this->load->model('perangkat_model', 'perangkat');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
    $this->Attendance = $this->load->database('absensi_local_mas', TRUE);
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "IT";
      $data['nama_halaman']     = "Daftar Perangkat";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();
      $data['jenis_perangkat']  = $this->jenis->get_all_data();
      $data['department_att'] 	= get_department_att();
      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";
      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/it/perangkat', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function perangkat_add()
  {
    $this->_validation_perangkat();

    $Data = array(
      'Nomor'             => $this->perangkat->generatePerangkatNumber(),
      'Nama'              => ucwords($this->input->post('Nama')),
      'JenisID'           => $this->input->post('JenisID'),
      'Merk'              => ucfirst($this->input->post('Merk')),
      'Tipe'              => ucfirst($this->input->post('Tipe')),
      'NoSeri'            => $this->input->post('NoSeri'),
      'DeptID'            => $this->input->post('DeptID'),
      'UserID'            => $this->input->post('UserID'),
      'AreaPasang'        => $this->input->post('AreaPemasangan'),
      'Status'            => $this->input->post('Status'),
      'NoBukti'           => $this->input->post('NoBuktiList'),
      'TanggalPembelian'  => $this->input->post('TanggalPembelian'),
      'CreateDate'        => date('Y-m-d H:i:s'),
      'CreateBy'          => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "Data" => $Data)); exit;
    $insert = $this->perangkat->save($Data);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "ADD";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function perangkat_list()
	{
		$draw 			 = intval($this->input->get("draw"));
		$start 			 = intval($this->input->get("start"));
		$length 		 = intval($this->input->get("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');
    $JenisPer    = $this->input->post('jenis_pr');
    $Dept        = $this->input->post('dept');


    //CONVERT(VARCHAR(19), a.CreateDate, 120) AS CreateDate,
    $Sql = "SELECT 
          a.Id, a.Nama AS NamaPerangkat, a.Nomor, a.JenisID, b.Nama AS NamaJP, a.Merk, 
          a.Tipe, a.NoSeri, a.TanggalPembelian, b.Kategori, a.NoBukti,
          a.UserID, a.DeptID, a.Status, a.AreaPasang,
          CASE 
            WHEN UPPER(a.Status) = 'AKTIF' THEN 'badge-success'
            WHEN UPPER(a.Status) = 'TIDAK' THEN 'badge-danger'
            ELSE 'badge-secondary' 
          END AS StatusClass,
          CAST(a.CreateDate as date) AS CreateDate,
          a.CreateBy
        FROM Ms_Perangkat a
        LEFT JOIN Ms_JenisPerangkat b ON b.Id = a.JenisID
        WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'";

// Tambahkan filter Jenis jika bukan 'All'
if ($JenisPer != 'All') {
    $Sql .= " AND a.JenisID = '$JenisPer'";
}

// Tambahkan filter Dept jika bukan 'All'
if ($Dept != 'All') {
    $Sql .= " AND a.DeptID = '$Dept'";
}

$Sql .= " ORDER BY a.CreateDate DESC";

    // $Sql = "SELECT 
    //         a.Id, a.Nama AS NamaPerangkat, a.Nomor, a.JenisID, b.Nama AS NamaJP,
    //         a.Merk, a.Tipe, a.NoSeri, a.TanggalPembelian, b.Kategori, a.NoBukti,
    //         a.UserID, a.DeptID, a.Status, a.AreaPasang,
    //         CASE UPPER(a.Status)
    //             WHEN 'AKTIF' THEN 'badge-success'
    //             WHEN 'TIDAK' THEN 'badge-danger'
    //             ELSE 'badge-secondary'
    //         END AS StatusClass,
    //         CAST(a.CreateDate AS DATE) AS CreateDate,
    //         a.CreateBy
    //         FROM Ms_Perangkat a
    //         LEFT JOIN Ms_JenisPerangkat b ON b.Id = a.JenisID
    //         WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
    //         " . ($JenisPer != 'All' ? " AND a.JenisID = '$JenisPer'" : '') . "
    //         ORDER BY a.CreateDate DESC";
    $Query       = $this->BJGMAS01->query($Sql);
    $Result      = $Query->result();
		$Data        = [];
		$No 		     = 1;

    foreach ($Result as $key => $value) {
      $Isi    = "'".$value->Id."'";
			$Data[] = array(
				$No++,
				'<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
            </div>
          </div>
        </div>',
        '<span class="badge badge-pill '.$value->StatusClass.'">'.$value->Status.'</span>',
        $value->Nomor,
        $value->NamaPerangkat,
        $value->NamaJP,
        $value->Kategori,
        $value->Merk,
        $value->Tipe,
        $value->NoSeri,
        $value->NoBukti,
        $value->TanggalPembelian == '1900-01-01' ? '' : $value->TanggalPembelian,
        $this->get_departemen_name($value->DeptID),
        $this->get_user_name($value->UserID),
        $value->AreaPasang,
        $value->CreateDate,
        $value->CreateBy
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 						=> $Data
		);

		echo json_encode($result);
		exit();
	}

  public function perangkat_edit($id)
  {
    $data = $this->perangkat->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function perangkat_update()
  {
    $this->_validation_perangkat();

    $Data = array(
      'Nama'              => ucwords($this->input->post('Nama')),
      'JenisID'           => $this->input->post('JenisID'),
      'Merk'              => ucfirst($this->input->post('Merk')),
      'Tipe'              => ucfirst($this->input->post('Tipe')),
      'NoSeri'            => $this->input->post('NoSeri'),
      'DeptID'            => $this->input->post('DeptID'),
      'UserID'            => $this->input->post('UserID'),
      'AreaPasang'        => $this->input->post('AreaPemasangan'),
      'Status'            => $this->input->post('Status'),
      'NoBukti'           => $this->input->post('NoBuktiList'),
      'TanggalPembelian'  => $this->input->post('TanggalPembelian'),
      'UpdateDate'        => date('Y-m-d H:i:s'),
      'UpdateBy'          => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "Data" => $Data)); exit;

    $this->perangkat->update(array('Id' => $this->input->post('kode')), $Data);
    echo json_encode(array("status" => "success"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function perangkat_deleted($id)
  {
    $data_delete    = $this->perangkat->get_by_id($id); //DATA DELETE
    $data           = $this->perangkat->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function get_all_bukti()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Periode   = str_replace("-", "", $this->input->post('periode'));
    $Table     = "Trans_POHD".$Periode;
    $this->BJGMAS01->select("NoBukti, CAST(CreateDate AS DATE) AS CreateDate, Keterangan", false);
    $this->BJGMAS01->from($Table);
    $this->BJGMAS01->like("NoBukti", $Search);
    $Query    = $this->BJGMAS01->get();
    $Data     = array();
    foreach ($Query->result() as $Row) {
      $Data[] = array(
        "id"          => $Row->NoBukti,
        "name"        => $Row->NoBukti,
        'tanggal'     => $Row->CreateDate,
        "keterangan"  => $Row->Keterangan
      );
    }

    echo json_encode($Data);
  }

  public function get_departemen_name($DeptID)
  {
    $Query = $this->Attendance->get_where('DEPARTMENTS', array('DEPTID' => $DeptID));
    if ($Query->num_rows() > 0) {
      // Data ada
      $row = $Query->row();

      return $row->DEPTNAME;
    } else {
      // Data tidak ada
      return "";
    }
  }

  public function get_user_name($Nip)
  {
    $Query = $this->Attendance->get_where('USERINFO', array('SSN' => $Nip));
    if ($Query->num_rows() > 0) {
      // Data ada
      $row = $Query->row();

      return $row->NAME;
    } else {
      // Data tidak ada
      return "";
    }
  }

  private function _validation_perangkat()
  {
    $Jenis                = $this->input->post('JenisID');
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Nama') == '') {
      $data['inputerror'][]   = 'Nama';
      $data['error_string'][] = 'Nama Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('JenisID') == '') {
      $data['inputerror'][]   = 'JenisID';
      $data['error_string'][] = 'Jenis Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Status') == '') {
      $data['inputerror'][]   = 'Status';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Merk') == '') {
      $data['inputerror'][]   = 'Merk';
      $data['error_string'][] = 'Merk Perangkat is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Tipe') == '') {
      $data['inputerror'][]   = 'Tipe';
      $data['error_string'][] = 'Tipe is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('NoSeri') == '') {
      $data['inputerror'][]   = 'NoSeri';
      $data['error_string'][] = 'No Seri is required';
      $data['status']         = FALSE;
    }

    if ( ! in_array($Jenis, [8, 9, 10, 19, 20]) ) {
      if ($this->input->post('DeptID') == '') {
        $data['inputerror'][]   = 'DeptID';
        $data['error_string'][] = 'Departemen is required';
        $data['status']         = FALSE;
      }

      if ($this->input->post('UserID') == '') {
        $data['inputerror'][]   = 'UserID';
        $data['error_string'][] = 'User is required';
        $data['status']         = FALSE;
      }
    }

    if ($Jenis == 8 || $Jenis == 9 || $Jenis == 10) {
      if ($this->input->post('AreaPemasangan') == '') {
        $data['inputerror'][]   = 'AreaPemasangan';
        $data['error_string'][] = 'Area Pemasangan is required';
        $data['status']         = FALSE;
      }
    }
    

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}