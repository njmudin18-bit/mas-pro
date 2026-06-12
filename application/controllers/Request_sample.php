<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_sample extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *    http://example.com/index.php/welcome
	 *  - or -
	 *    http://example.com/index.php/welcome/index
	 *  - or -
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

		$this->load->helper(array('url', 'form', 'cookie', 'file'));
		$this->load->library(array('session', 'cart'));

		$this->load->model('auth_model', 'auth');
		if ($this->auth->isNotLogin());

		//START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('department_model', 'department');
		$this->load->model('document_type_model', 'document_type');
		$this->load->model('document_model', 'document');
		$this->load->model('requestsample_model', 'request');

    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Request for Product Sample";
			$data['icon_halaman'] 	= "icon-bookmark";

			$data['department'] = $this->department->get_all();
			$data['perusahaan'] = $this->perusahaan->get_details();
			$this->load->view('adminx/sales/request_sample/input_sample', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

  public function sample_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $CustomerCheck     = $this->input->post('CustomerNewCheck');
      $this->_validation_sample($CustomerCheck);

      // Generate nomor request
      $Nomor             = $this->request->generateRequestNumber();
      // Ambil data form
      $PartnerID         = $this->input->post('PartnerID');
      $CustomerPartID    = strtoupper($this->input->post('CustomerPartID'));
      $CustomerPartName  = strtoupper($this->input->post('CustomerPartName'));

      //JIKA CUSTOMER BARU
      $CustomerNew       = strtoupper($this->input->post('CustomerNew'));
      $CustomerNewAddr   = ucfirst($this->input->post('CustomerNewAddress'));

      $Keterangan        = $this->input->post('Keterangan');
      $notes             = $this->input->post('Notes');
      $Quantity          = $this->input->post('Quantity');

      $StatusRequest     = $this->input->post('StatusRequest');
      $Harga             = floatval(format_weight($this->input->post('Harga')));
      $Etd               = $this->input->post('Etd');

      // Data header
      $FirstData = array(
        'Nomor'             => $Nomor,
        'PartnerID'         => $PartnerID,
        'CustomerCheck'     => $CustomerCheck,
        'CustomerName'      => $CustomerCheck === 'on' ? $CustomerNew : null,
        'CustomerAddress'   => $CustomerCheck === 'on' ? $CustomerNewAddr : null,
        'CustomerPartID'    => $CustomerPartID,
        'CustomerPartName'  => $CustomerPartName,
        'Status'            => $StatusRequest,
        'Prices'            => $Harga,
        'Etd'               => $Etd,
        'Notes'             => $Keterangan,
        'CreateDate'        => date('Y-m-d H:i:s'),
        'CreateBy'          => $this->session->userdata('user_id')
      );

      //echo json_encode(array('status' => 'error', 'HD' => $FirstData)); exit;

      // Proses upload Files[] dan Notes[]
      $files          = $_FILES['Files'];
      $SecondData     = [];
      $upload_errors  = [];

      for ($i = 0; $i < count($files['name']); $i++) {
        // Ambil ekstensi file
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));

        // Validasi ekstensi hanya pdf atau png
        if (!in_array($ext, ['pdf', 'png'])) {
          $upload_errors[] = "File ke-" . ($i + 1) . " bukan PDF atau PNG (." . $ext . ")";

          continue;
        }

        // Siapkan $_FILES untuk upload CI
        $_FILES['file']['name']     = $files['name'][$i];
        $_FILES['file']['type']     = $files['type'][$i];
        $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
        $_FILES['file']['error']    = $files['error'][$i];
        $_FILES['file']['size']     = $files['size'][$i];

        $config['upload_path']      = './files/uploads/request';
        $config['allowed_types']    = 'pdf|png';
        $config['max_size']         = 5000; // 5 MB
        $config['encrypt_name']     = FALSE;
        $config['file_name']        = $Nomor . '-' . ($i + 1);

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('file')) {
          $data = $this->upload->data();

          $SecondData[] = array(
            'Nomor'      => $Nomor,
            'Files'      => $data['file_name'],
            'Types'      => strtolower($data['file_ext']),
            'Quantity'   => $Quantity[$i],
            'Notes'      => $notes[$i],
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateBy'   => $this->session->userdata('user_id')
          );
        } else {
          $upload_errors[] = "File ke-" . ($i + 1) . ": " . strip_tags($this->upload->display_errors('', '')) . "<br>";
        }
      }

      // Jika ada error upload, kembalikan respon error
      if (!empty($upload_errors)) {
        echo json_encode([
          'status_code'   => 500,
          'status'        => 'error',
          'message'       => 'Beberapa file gagal diupload.<br>' . implode('<br>', $upload_errors),
          'errors'        => $upload_errors
        ]);
        return;
      }

      // Mulai transaksi di koneksi BJGMAS01
      $this->BJGMAS01->trans_begin();
      // Simpan Header
      if (!$this->BJGMAS01->insert('Trans_RequestSampleHD', $FirstData)) {
        $this->BJGMAS01->trans_rollback();
        $this->responseJSON(500, 'error', 'Gagal menyimpan data header.', ['step' => 'Header Insert']);
      }

      // Simpan Detail 1
      if (!$this->BJGMAS01->insert_batch('Trans_RequestSampleDT', $SecondData)) {
        $this->BJGMAS01->trans_rollback();
        $this->responseJSON(500, 'error', 'Gagal menyimpan data detail ', ['step' => 'Detail Insert']);
      }

      // Commit transaksi
      if ($this->BJGMAS01->trans_status() === FALSE) {
        $this->BJGMAS01->trans_rollback();
        $this->responseJSON(500, 'error', 'Gagal menyimpan data (transaksi gagal).');
      } else {
        $this->BJGMAS01->trans_commit();
        $this->responseJSON(200, 'success', 'Sukses menyimpan data.');
      }

      exit;
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

	public function sample_list_OLD()
	{
		$Draw        = intval($this->input->post("draw"));
    $Start       = intval($this->input->post("start"));
    $Length      = intval($this->input->post("length"));
    $StartDate   = $this->input->post('start_date');
    $EndDate     = $this->input->post('end_date');
    $user_dept   = $this->session->userdata('user_dept_name');

    $Sql         = "EXEC dbo.GetProductSample @StartDate = ?, @EndDate = ?";
    $Query       = $this->BJGMAS01->query($Sql, array($StartDate, $EndDate));
    $Result      = $Query->result();
    $Total       = count($Result);
    $Paged       = array_slice($Result, $Start, $Length);

    $Data        = [];
    $No          = $Start + 1;
    foreach ($Paged as $key => $Res) {
      $Class  = $this->get_status_badge_class($Res->StatusPD);
      $Isi    = "'".$Res->Nomor."'";
      $Isi2   = "'".$Res->Nomor."', '".$Res->PartnerName."', '".$Res->PartnerID."'";
      $Isi3   = "'".$Res->Nomor."', '".$Res->PartnerName."', '".$Res->PartnerID."', '".$Res->Quantity."', '".$Res->Etd."'";
      $Url    = base_url()."files/uploads/request/".$Res->Files;
      $Url2   = base_url()."files/uploads/drawing/".$Res->FileDrawing;
      $BtnCek = ($Res->CheckPD != NULL) ? '<a class="dropdown-item" href="#" onclick="tambah_keterangan('.$Isi2.')">PD Edit Keterangan</a>' : '<a class="dropdown-item" href="#" onclick="tambah_keterangan('.$Isi2.')">PD Tambah Keterangan</a>';
      $BtnQC  = ($Res->CheckQC != NULL) ? '<a class="dropdown-item" href="#" onclick="cek_qc('.$Isi3.')">QC Edit Keterangan</a>' : '<a class="dropdown-item" href="#" onclick="cek_qc('.$Isi3.')">QC Tambah Keterangan</a>';
      
      $Row    = array();
      $Row[]  = $Res->NomorUrut;
      $Row[]  = $Res->Nomor;
      $Row[]  = '<a href="'.$Url.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>';
      $Row[]  = ($Res->FileDrawing != NULL) ? '<a href="'.$Url2.'" target="_blank" class="btn btn-danger btn-sm" title="Klik untuk lihat drawing">
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>' : '';
      $Row[]  = ($Res->NomorUrut != NULL) ? '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="hapusAll('.$Isi.')">Hapus</a>
                      <a class="dropdown-item" href="#" onclick="cek_drawing('.$Isi2.')">Upload Drawing</a>
                      '.$BtnCek.'
                      '.$BtnQC.'
                    </div>
                  </div>
                </div>' : '';
      $Row[]  = $Res->PartnerID == '' ? '-' : $Res->PartnerID;
      $Row[]  = $Res->PartnerName;
      $Row[]  = ($Res->NomorUrut != NULL) ? '<span class="badge badge-pill badge-primary">DIAJUKAN</span>' : '';
      $Row[]  = ($Res->CheckPD != NULL) ? '<span class="badge badge-pill '.$Class.' pointer" title="'.$Res->NotedPD.'">'.$Res->StatusPD.'</span>' : '';
      $Row[]  = $Res->ProcessDatePD;
      $Row[]  = ($Res->CheckQC != NULL) ? '<span class="badge badge-pill '.$Class.' pointer" title="'.$Res->NotedQC.'">'.$Res->StatusQC.'</span>' : '';
      $Row[]  = $Res->ProcessDateQC;
      $Row[]  = $Res->NotedQC;
      $Row[]  = $Res->Quantity;
      $Row[]  = $Res->CustomerPartName;
      $Row[]  = $Res->CustomerPartID;
      $Row[]  = $Res->Etd;
      $Row[]  = ($user_dept == 'IT' || $user_dept == 'PD') ? ((float)$Res->Prices == 0 ? '' : number_format((float)$Res->Prices, 0, ',', '.')) : '';
      $Row[]  = $Res->StatusHD;
      $Row[]  = $Res->Notes;
      $Row[]  = $Res->CreateDate;
      $Row[]  = $Res->CreateBy;

      $Data[] = $Row;
    }

    echo json_encode([
      "draw"            => $Draw,
      "recordsTotal"    => $Total,
      "recordsFiltered" => $Total,
      "data"            => $Data
    ]);
    exit();
	}

  public function sample_list()
	{
		$draw 			    = intval($this->input->get("draw"));
		$start 			    = intval($this->input->get("start"));
		$length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    $user_dept      = $this->session->userdata('user_dept_name');

    $Sql            = "EXEC dbo.GetProductSample @StartDate = ?, @EndDate = ?";
    $Query          = $this->BJGMAS01->query($Sql, array($StartDate, $EndDate));
    $Result         = $Query->result();
		$Data           = [];
		$No 		        = 1;

    foreach ($Result as $key => $Res) {
      $Class  = $this->get_status_badge_class($Res->StatusPD);
      $Isi    = "'".$Res->Nomor."'";
      $Isi2   = "'".$Res->Nomor."', '".$Res->PartnerName."', '".$Res->PartnerID."'";
      $Isi3   = "'".$Res->Nomor."', '".$Res->PartnerName."', '".$Res->PartnerID."', '".$Res->Quantity."', '".$Res->Etd."'";
      $Url    = base_url()."files/uploads/request/".$Res->Files;
      $Url2   = base_url()."files/uploads/drawing/".$Res->FileDrawing;
      $BtnCek = ($Res->CheckPD != NULL) ? '<a class="dropdown-item" href="#" onclick="tambah_keterangan('.$Isi2.')">PD Edit Keterangan</a>' : '<a class="dropdown-item" href="#" onclick="tambah_keterangan('.$Isi2.')">PD Tambah Keterangan</a>';
      $BtnQC  = ($Res->CheckQC != NULL) ? '<a class="dropdown-item" href="#" onclick="cek_qc('.$Isi3.')">QC Edit Keterangan</a>' : '<a class="dropdown-item" href="#" onclick="cek_qc('.$Isi3.')">QC Tambah Keterangan</a>';

			$Data[] = array(
				$Res->NomorUrut,
        $Res->Nomor,
        '<a href="'.$Url.'" target="_blank" class="btn btn-warning btn-sm" title="Klik untuk lihat dokumen detail">
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </a>',
        ($Res->FileDrawing != NULL) ? '<a href="'.$Url2.'" target="_blank" class="btn btn-danger btn-sm" title="Klik untuk lihat drawing">
            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </a>' : '',
				'<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
              <a class="dropdown-item" href="#" onclick="hapusAll('.$Isi.')">Hapus</a>
              <a class="dropdown-item" href="#" onclick="cek_drawing('.$Isi2.')">Upload Drawing</a>
              '.$BtnCek.'
              '.$BtnQC.'
            </div>
          </div>
        </div>',
				$Res->PartnerID == '' ? '-' : $Res->PartnerID,
        $Res->PartnerName,
        ($Res->NomorUrut != NULL) ? '<span class="badge badge-pill badge-primary">DIAJUKAN</span>' : '',
        ($Res->CheckPD != NULL) ? '<span class="badge badge-pill '.$Class.' pointer" title="'.$Res->NotedPD.'">'.$Res->StatusPD.'</span>' : '',
        $Res->ProcessDatePD,
        ($Res->CheckQC != NULL) ? '<span class="badge badge-pill '.$Class.' pointer" title="'.$Res->NotedQC.'">'.$Res->StatusQC.'</span>' : '',
        $Res->ProcessDateQC,
        $Res->NotedQC,
        $Res->Quantity,
        $Res->CustomerPartName,
        $Res->CustomerPartID,
        $Res->Etd,
        ($user_dept == 'IT' || $user_dept == 'PD') ? ((float)$Res->Prices == 0 ? '' : number_format((float)$Res->Prices, 0, ',', '.')) : '',
        $Res->StatusHD,
        $Res->Notes,
        $Res->CreateDate,
        $Res->CreateBy
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

	public function sample_edit()
  {
    $NoReq   = $this->input->post('NoRequest');
    $DataHD  = $this->request->get_hd_by_id($NoReq);
    $DataDT  = $this->request->get_dt_by_id($NoReq);

    // Cek apakah data HD atau DT kosong / null
    if (empty($DataHD) || empty($DataDT)) {
      echo json_encode([
        "status_code" => 404,
        "status"      => "error",
        "message"     => "Data tidak ditemukan.",
        "first"       => null,
        "second"      => null
      ]);

      return;
    }

    echo json_encode([
      "status_code" => 200,
      "status"      => "success",
      "message"     => "Data ditemukan.",
      "first"       => $DataHD,
      "second"      => $DataDT
    ]);
  }

  public function sample_update()
  {
    // Cek akses
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() !== 1) {
      echo json_encode(array("status" => "forbidden")); return;
    }

    $CustomerCheck     = $this->input->post('CustomerNewCheck');
    $this->_validation_sample($CustomerCheck);

    $Nomor            = $this->input->post('NoRequest');
    $PartnerID        = $this->input->post('PartnerID');
    $CustomerPartID   = strtoupper($this->input->post('CustomerPartID'));
    $CustomerPartName = strtoupper($this->input->post('CustomerPartName'));

    //JIKA CUSTOMER BARU
    $CustomerNew       = strtoupper($this->input->post('CustomerNew'));
    $CustomerNewAddr   = ucfirst($this->input->post('CustomerNewAddress'));

    $Keterangan       = $this->input->post('Keterangan');
    $StatusRequest    = $this->input->post('StatusRequest');
    $Harga            = floatval(format_weight($this->input->post('Harga')));
    $Etd              = $this->input->post('Etd');

    // Array inputs
    $kodeSecond       = $this->input->post('kodeSecond');
    $Quantity         = $this->input->post('Quantity');
    $Notes            = $this->input->post('Notes');
    $OldFiles         = $this->input->post('OldFiles');

    // Update Header
    $FirstData = array(
      'PartnerID'        => $PartnerID,
      'CustomerCheck'    => $CustomerCheck,
      'CustomerName'     => $CustomerCheck === 'off' ? $CustomerNew : null,
      'CustomerAddress'  => $CustomerCheck === 'off' ? $CustomerNewAddr : null,
      'CustomerPartID'   => $CustomerPartID,
      'CustomerPartName' => $CustomerPartName,
      'Status'           => $StatusRequest,
      'Prices'           => $Harga,
      'Etd'              => $Etd,
      'Notes'            => $Keterangan,
      'UpdateDate'       => date('Y-m-d H:i:s'),
      'UpdateBy'         => $this->session->userdata('user_id')
    );

    //echo json_encode(array("status" => "error", "Data" => $FirstData)); exit;

    $this->BJGMAS01->where('Nomor', $Nomor);
    $this->BJGMAS01->update('Trans_RequestSampleHD', $FirstData);

    // Folder upload
    $upload_path = FCPATH.'files/uploads/request/';

    // Loop detail rows
    foreach ($Quantity as $i => $qty) {
        $IdDetail   = $kodeSecond[$i];
        $Catatan    = $Notes[$i];
        $FileName   = $OldFiles[$i] ?? null; // Default: pakai file lama

        // Handle upload file baru (jika ada)
        if (!empty($_FILES['Files']['name'][$i])) {
            $originalName = $_FILES['Files']['name'][$i];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // Cek tipe file
            if (!in_array($ext, ['pdf', 'png'])) {
                echo json_encode([
                    "status_code" => 400,
                    "status"      => "error",
                    "message"     => "File harus berformat PDF atau PNG di baris ke-" . ($i + 1)
                ]);
                return;
            }

            // Set ulang $_FILES agar bisa diproses Upload CI
            $_FILES['file']['name']     = $_FILES['Files']['name'][$i];
            $_FILES['file']['type']     = $_FILES['Files']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['Files']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['Files']['error'][$i];
            $_FILES['file']['size']     = $_FILES['Files']['size'][$i];

            // Konfigurasi upload
            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'pdf|png';
            $config['file_name']     = $Nomor . '-' . ($i + 1) . '-' . time();
            $config['overwrite']     = true;

            $this->load->library('upload', $config);

            // Proses upload
            if (!$this->upload->do_upload('file')) {
                echo json_encode([
                    "status_code" => 400,
                    "status"      => "error",
                    "message"     => "Upload gagal di baris ke-" . ($i + 1) . ": " . strip_tags($this->upload->display_errors('', ''))
                ]);
                return;
            }

            // Hapus file lama
            if ($FileName && file_exists($upload_path . $FileName)) {
                @unlink($upload_path . $FileName);
            }

            $uploadData = $this->upload->data();
            $FileName   = $uploadData['file_name'];
        }

        // Data detail
        $DataDT = array(
          'Nomor'     => $Nomor,
          'Files'     => $FileName,
          'Types'     => pathinfo($FileName, PATHINFO_EXTENSION),
          'Quantity'  => $qty,
          'Notes'     => $Catatan,
          'CreateDate'=> date('Y-m-d H:i:s'),
          'CreateBy'  => $this->session->userdata('user_id')
        );

        if (empty($IdDetail)) {
          // INSERT
          $this->BJGMAS01->insert('Trans_RequestSampleDT', $DataDT);
        } else {
          // UPDATE
          unset($DataDT['CreateDate'], $DataDT['CreateBy']);
          $this->BJGMAS01->where('Id', $IdDetail);
          $this->BJGMAS01->update('Trans_RequestSampleDT', $DataDT);
        }
    }

    echo json_encode(array(
      "status_code" => 200,
      "status"      => "success",
      "message"     => "Data berhasil disimpan."
    ));
  }

	public function sample_deleted_all()
	{
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $NoRequest = $this->input->post('NoRequest');

      //GET NAMA FILE
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $query    = $this->BJGMAS01->get('Trans_RequestSampleDT');
      $fileRows = $query->result();

      // 2. Hapus file-file fisik
      foreach ($fileRows as $row) {
        $fileName = basename($row->Files);
        $filePath = FCPATH.'files/uploads/request/'.$fileName;

        if (file_exists($filePath)) {
          @unlink($filePath); // gunakan @ untuk mencegah warning jika gagal
        }
      }

      // 3. GET NAMA FILE DRAWING
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $query2     = $this->BJGMAS01->get('Trans_RequestSampleDT3');
      $fileRows2  = $query2->result();

      foreach ($fileRows2 as $row) {
        $fileName = basename($row->Files);
        $filePath = FCPATH.'files/uploads/drawing/'.$fileName;

        if (file_exists($filePath)) {
          @unlink($filePath); // gunakan @ untuk mencegah warning jika gagal
        }
      }

      // 4. Hapus data dari Trans_RequestSampleDT3
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $this->BJGMAS01->delete('Trans_RequestSampleDT3');

      // 5. Hapus data dari Trans_RequestSampleDT2
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $this->BJGMAS01->delete('Trans_RequestSampleDT2');

      // 6. Hapus data dari Trans_RequestSampleDT
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $this->BJGMAS01->delete('Trans_RequestSampleDT');

      // 7. Hapus data dari Trans_RequestSampleHD
      $this->BJGMAS01->where('Nomor', $NoRequest);
      $Delete = $this->BJGMAS01->delete('Trans_RequestSampleHD');

      // 8. Feedback response
      if ($Delete) {
        echo json_encode(array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Semua data dan file berhasil dihapus."
        ));
      } else {
        echo json_encode(array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal dihapus."
        ));
      }
      exit();
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
	}

  public function hapus_single_row()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $OldFile   = $this->input->post('OldFile');
      $NoRequest = $this->input->post('NoReq');
      $IdDetail  = $this->input->post('IdDt');

      // Tentukan path file
      //$FilePath = $OldFile;
      $FilePath  = FCPATH.'files/uploads/request/'.$OldFile;

      // Coba hapus file dulu
      if (file_exists($FilePath)) {
        if (!unlink($FilePath)) {
          echo json_encode([
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Gagal menghapus file fisik."
          ]);
          exit();
        }
      }

      // Jika file berhasil dihapus (atau tidak ada), lanjut hapus dari DB
      $Delete = $this->BJGMAS01->delete('Trans_RequestSampleDT', array(
        'Id'    => $IdDetail,
        'Nomor'=> $NoRequest
      ));

      if ($Delete) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data dan file sukses dihapus."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Gagal menghapus data dari database."
        ]);
      }

      exit();
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  public function get_customer()
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
      // Handle non-POST requests (e.g., return an error)
      $response = array('error' => 'Invalid request method.');
      header('Content-Type: application/json');
      echo json_encode($response);
      
      return;
    }

    $Search    = strtoupper(trim($this->input->post('search')));
    $Result    = $this->request->get_partner($Search);
    
    echo json_encode($Result);
    exit;
  }

  //KETERANGAN OLEH PD
  public function sample_keterangan_cek()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

      $NomorReq      = $this->input->post('NoRequest');
      $PartnerName   = $this->input->post('PartnerNames');
      $PartnerID     = $this->input->post('PartnerIDs');

      // Jalankan query pencarian
      $this->BJGMAS01->from('Trans_RequestSampleDT2');
      $this->BJGMAS01->where('Nomor', $NomorReq);
      $this->BJGMAS01->order_by('CreateDate', 'DESC');
      $this->BJGMAS01->limit(1);
      $Query = $this->BJGMAS01->get();

      // Cek apakah data ditemukan
      if ($Query->num_rows() > 0) {
        $Result = $Query->row();
        $Res    = $this->BJGMAS01->order_by('CreateDate', 'DESC')->get_where('Trans_RequestSampleDT2', ['Nomor' => $NomorReq])->result();
        $Prices = $this->BJGMAS01->select('Prices')->order_by('CreateDate', 'DESC')->get_where('Trans_RequestSampleHD', ['Nomor' => $NomorReq])->row();

        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data ditemukan.',
          'data'        => $Result,
          'history'     => $Res,
          'prices'      => $Prices
        ]);
      } else {
        echo json_encode([
          'status_code' => 404,
          'status'      => 'error',
          'message'     => 'Data tidak ditemukan.',
          'data'        => array()
        ]);
      }
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  public function sample_keterangan_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_keterangan();

      // Ambil data form
      $NomorReq      = $this->input->post('NomorReqs');
      $PartnerName   = $this->input->post('PartnerNames');
      $PartnerID     = $this->input->post('PartnerIDs');
      $Status        = $this->input->post('StatusList');
      $ProcessDate   = $this->input->post('ProcessDate');
      $Harga         = floatval(format_weight($this->input->post('Hargas')));
      $Keterangan    = ucfirst($this->input->post('Keterangans'));
      $Arrays        = array(
        'Nomor'             => $NomorReq,
        'PartnerID'         => $PartnerID,
        'PartnerName'       => $PartnerName,
      );

      // Data header
      $FirstData = array(
        'Nomor'             => $NomorReq,
        'Status'            => $Status,
        'Froms'             => "PD", //$this->session->userdata('user_dept_name'),
        'ProcessDate'       => $ProcessDate,
        'Noted'             => $Keterangan,
        'CreateDate'        => date('Y-m-d H:i:s'),
        'CreateBy'          => $this->session->userdata('user_id')
      );

      //echo json_encode(array('status' => 'error', 'HD' => $FirstData)); exit;

      if ($this->BJGMAS01->insert('Trans_RequestSampleDT2', $FirstData)) {

        $SecondData = array(
          'Prices' => $Harga
        );
        $this->BJGMAS01->where('Nomor', $NomorReq);
        $this->BJGMAS01->update('Trans_RequestSampleHD', $SecondData);

        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data sukses disimpan.',
          'data'        => $Arrays,
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data gagal disimpan.',
          'data'        => $Arrays,
        ]);
      }

      exit;
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  private function responseJSON($status_code, $status, $message, $errors = null) 
  {
    $response = [
      'status_code' => $status_code,
      'status'      => $status,
      'message'     => $message
    ];

    if ($errors !== null) {
        $response['errors'] = $errors;
    }

    echo json_encode($response);
    exit;
  }

  //KETERANGAN OLEH QC
  public function sample_qc_cek()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
      $NomorReq      = $this->input->post('NoRequest');
      $PartnerName   = $this->input->post('PartnerNames');
      $PartnerID     = $this->input->post('PartnerIDs');

      // Jalankan query pencarian
      $this->BJGMAS01->from('Trans_RequestSampleDT2');
      $this->BJGMAS01->where('Nomor', $NomorReq);
      $this->BJGMAS01->where('Froms', 'QC');
      $this->BJGMAS01->order_by('CreateDate', 'DESC');
      $this->BJGMAS01->limit(1);
      $Query = $this->BJGMAS01->get();

      // Cek apakah data ditemukan
      if ($Query->num_rows() > 0) {
        $Result = $Query->row();
        $Res    = $this->BJGMAS01->order_by('CreateDate', 'DESC')->get_where('Trans_RequestSampleDT2', ['Nomor' => $NomorReq, 'Froms' => 'QC'])->result();

        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data ditemukan.',
          'data'        => $Result,
          'history'     => $Res
        ]);
      } else {
        echo json_encode([
          'status_code' => 404,
          'status'      => 'error',
          'message'     => 'Data tidak ditemukan.',
          'data'        => array()
        ]);
      }

      exit;
    } else {
			echo json_encode(array("status" => "forbidden"));
		}
  }

  public function sample_qc_cek_add()
  {
    $user_level = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $this->_validation_qc();

      // Data utama
      $NomorReq       = $this->input->post('QcNomorReqs');
      $PartnerName    = $this->input->post('QcPartnerName');
      $PartnerID      = $this->input->post('QcPartnerID');
      $QtySample      = (float) $this->input->post('QcSampleQuantity');

      // Array input
      $QcStatus       = $this->input->post('QcStatus');
      $QcQuantity     = $this->input->post('QcQuantity');
      $QcTanggal      = $this->input->post('QcTanggal');
      $QcKeterangan   = $this->input->post('QcKeterangan');
      $kodeThird      = $this->input->post('kodeThird');

      $insertBatch    = [];
      $totalQtyCheck  = 0;

      if (is_array($QcStatus)) {
        foreach ($QcStatus as $i => $status) {
          $qty        = floatval($QcQuantity[$i] ?? 0);
          $keterangan = $QcKeterangan[$i] ?? null;
          $tanggal    = $QcTanggal[$i] ?? null;
          $idDetail   = $kodeThird[$i] ?? null;

          $totalQtyCheck += $qty;

          $data = [
            'Nomor'       => $NomorReq,
            'Status'      => $status,
            'ProcessDate' => $tanggal,
            'Froms'       => 'QC',
            'Quantity'    => $qty,
            'Noted'       => ucfirst($keterangan)
          ];

          if (!empty($idDetail)) {
            // Update
            $data['UpdateDate'] = date('Y-m-d H:i:s');
            $data['UpdateBy']   = $this->session->userdata('user_id');

            $this->BJGMAS01->update('Trans_RequestSampleDT2', $data, ['Id' => $idDetail]);
          } else {
            // Insert
            $data['CreateDate'] = date('Y-m-d H:i:s');
            $data['CreateBy']   = $this->session->userdata('user_id');
            $insertBatch[]      = $data;
          }
        }

        if ($totalQtyCheck > $QtySample) {
          echo json_encode([
            'status_code' => 500,
            'status'      => 'error',
            'message'     => 'Jumlah pengecekan (' . $totalQtyCheck . ') melebihi jumlah sampel (' . $QtySample . ').'
          ]);
          return;
        }

        // Insert batch jika ada data baru
        if (!empty($insertBatch)) {
          $this->BJGMAS01->insert_batch('Trans_RequestSampleDT2', $insertBatch);
        }

        echo json_encode([
          'status_code' => 200,
          'status'      => 'success',
          'message'     => 'Data berhasil disimpan.',
          'data'        => [
            'Nomor'       => $NomorReq,
            'PartnerID'   => $PartnerID,
            'PartnerName' => $PartnerName,
          ]
        ]);
      } else {
        echo json_encode([
          'status_code' => 500,
          'status'      => 'error',
          'message'     => 'Data status QC tidak valid.',
        ]);
      }
    } else {
      echo json_encode([
        'status_code' => 403,
        'status'      => 'forbidden'
      ]);
    }
  }

  public function sample_qc_delete_row()
  {
    $NoRequest = $this->input->post('NoReq');
    $IdDetail  = $this->input->post('IdDt');

    //echo $NoRequest." - ".$IdDetail; exit;

    // Jika file berhasil dihapus (atau tidak ada), lanjut hapus dari DB
    $Delete = $this->BJGMAS01->delete('Trans_RequestSampleDT2', array(
      'Id'    => $IdDetail,
      'Nomor' => $NoRequest
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

  //DRAWING
  public function cek_drawing()
  {
    $NomorReq = $this->input->post('NoRequest');

    // Jalankan query pencarian
    $this->BJGMAS01->from('Trans_RequestSampleDT3');
    $this->BJGMAS01->where('Nomor', $NomorReq);
    $this->BJGMAS01->order_by('CreateDate', 'DESC');
    $this->BJGMAS01->limit(1);
    $Query = $this->BJGMAS01->get();

    // Cek apakah data ditemukan
    if ($Query->num_rows() > 0) {
      $Result = $Query->row();
      $Res    = $this->BJGMAS01->order_by('CreateDate', 'DESC')->get_where('Trans_RequestSampleDT3', ['Nomor' => $NomorReq])->result();

      echo json_encode([
        'status_code' => 200,
        'status'      => 'success',
        'message'     => 'Data ditemukan.',
        'data'        => $Result,
        'history'     => $Res
      ]);
    } else {
      echo json_encode([
        'status_code' => 404,
        'status'      => 'error',
        'message'     => 'Data tidak ditemukan.',
        'data'        => array()
      ]);
    }
  }

  public function save_drawing()
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() != 1) {
      echo json_encode(["status" => "forbidden"]);

      return;
    }

    $KodeDetail    = $this->input->post('UploadKodeDetail');

    if (empty($KodeDetail)){
      $this->_validation_drawing();
    }

    $NomorReqs     = $this->input->post('UploadNomorReqs');
    $PartnerName   = $this->input->post('UploadPartnerName');
    $PartnerID     = $this->input->post('UploadPartnerID');
    $Notes         = $this->input->post('UploadNoted');
    

    $file_name     = null;
    $file_type     = null;

    // Handle file upload
    if (!empty($_FILES['Files']['name'])) {
      $ext                     = pathinfo($_FILES['Files']['name'], PATHINFO_EXTENSION);
      $new_file_name           = $NomorReqs . '.' . $ext;

      $config['upload_path']   = './files/uploads/drawing/';
      $config['allowed_types'] = 'pdf|png';
      $config['max_size']      = 3072; // 3MB
      $config['file_name']     = $NomorReqs;
      $config['overwrite']     = TRUE;

      $this->load->library('upload', $config);

      // Cek dan hapus file lama
      if (!empty($KodeDetail) && !empty($_FILES['Files']['name'])) {
        $this->BJGMAS01->where('Id', $KodeDetail);
        $oldData = $this->BJGMAS01->get('Trans_RequestSampleDT3')->row();

        if ($oldData && !empty($oldData->Files)) {
          $oldFilePath = './files/uploads/drawing/' . $oldData->Files;
          if (file_exists($oldFilePath)) {
            @unlink($oldFilePath);
          }
        }
      }

      if (!$this->upload->do_upload('Files')) {
        echo json_encode([
          "status_code" => 500,
          "status"      => 'error',
          "message"     => $this->upload->display_errors('', '')
        ]);

        return;
      }

      $upload_data  = $this->upload->data();
      $file_name    = $upload_data['file_name'];
      $file_type    = $upload_data['file_ext'];
    } else {
      $file_name    = null;
      $file_type    = null;
    }

    $Data = [
      'Nomor'       => $NomorReqs,
      'Notes'       => $Notes,
      'CreateDate'  => date('Y-m-d H:i:s'),
      'CreateBy'    => $this->session->userdata('user_id')
    ];

    if ($file_name !== null) {
      $Data['Files'] = $file_name;
      $Data['Types'] = ltrim($file_type, '.');
    }

    //echo json_encode($data); exit;

    if (empty($KodeDetail)) {
      // INSERT
      $Insert = $this->BJGMAS01->insert('Trans_RequestSampleDT3', $Data);
      if ($Insert) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data berhasil disimpan."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal disimpan."
        ]);
      }
    } else {
      // UPDATE
      $this->BJGMAS01->where('Id', $KodeDetail);
      $this->BJGMAS01->where('Nomor', $NomorReqs);
      $Update = $this->BJGMAS01->update('Trans_RequestSampleDT3', $Data);
      if ($Update) {
        echo json_encode([
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data berhasil diupdate."
        ]);
      } else {
        echo json_encode([
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal diupdate."
        ]);
      }
    }
  }

  private function get_status_badge_class($status)
  {
    switch (strtoupper($status)) {
        case 'FINISH':
            return 'badge-success';
        case 'PROSES':
            return 'badge-info';
        case 'HOLD':
            return 'badge-danger';
        case 'REVIEW':
            return 'badge-warning';
        default:
            return 'badge-secondary'; // fallback/default
    }
  }

  private function _validation_keterangan()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('StatusList') == '') {
      $data['inputerror'][]   = 'StatusList';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ProcessDate') == '') {
      $data['inputerror'][]   = 'ProcessDate';
      $data['error_string'][] = 'Tanggal proses is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Keterangans') == '') {
      $data['inputerror'][]   = 'Keterangans';
      $data['error_string'][] = 'Keterangan is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_qc()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    // validasi per kolom dalam jumlahContainer
    $qcStatus     = $this->input->post('QcStatus');
    $qcQuantity   = $this->input->post('QcQuantity');
    $qcKeterangan = $this->input->post('QcKeterangan');

    if (is_array($qcStatus)) {
      foreach ($qcStatus as $i => $sts) {
        //if (empty($qc)) {
        if ($sts === '' || $sts === null) {
          $data['inputerror'][]   = "QcStatus[$i]";
          $data['error_string'][] = 'Status is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($qcQuantity)) {
      foreach ($qcQuantity as $i => $qty) {
        //if ($not === '' || $not === null) {
        if (empty($qty)) {
          $data['inputerror'][]   = "QcQuantity[$i]";
          $data['error_string'][] = 'Quantity Pengecekan is required';
          $data['status']         = FALSE;
        }
      }
    }

    if (is_array($qcKeterangan)) {
      foreach ($qcKeterangan as $i => $ket) {
        //if ($not === '' || $not === null) {
        if (empty($ket)) {
          $data['inputerror'][]   = "QcKeterangan[$i]";
          $data['error_string'][] = 'Keterangan is required';
          $data['status']         = FALSE;
        }
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_drawing()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    // Check apakah file tidak diupload
    if (empty($_FILES['Files']['name'])) {
      $data['inputerror'][]   = 'Files';
      $data['error_string'][] = 'File is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  private function _validation_sample($CustomerCheck)
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($CustomerCheck == 'on') {
      if ($this->input->post('CustomerNew') == '') {
        $data['inputerror'][]   = 'CustomerNew';
        $data['error_string'][] = 'Customer New is required';
        $data['status']         = FALSE;
      }

      if ($this->input->post('CustomerNewAddress') == '') {
        $data['inputerror'][]   = 'CustomerNewAddress';
        $data['error_string'][] = 'Customer New Address is required';
        $data['status']         = FALSE;
      }
    }

    if ($CustomerCheck == 'off') {
      if ($this->input->post('Alamat') == '') {
        $data['inputerror'][]   = 'Alamat';
        $data['error_string'][] = 'Alamat ID is required';
        $data['status']         = FALSE;
      }

      if ($this->input->post('PartnerID') == '') {
        $data['inputerror'][]   = 'PartnerID';
        $data['error_string'][] = 'Partner ID is required';
        $data['status']         = FALSE;
      }
    }

    if ($this->input->post('CustomerPartName') == '') {
      $data['inputerror'][]   = 'CustomerPartName';
      $data['error_string'][] = 'Customer Part Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('StatusRequest') == '') {
      $data['inputerror'][]   = 'StatusRequest';
      $data['error_string'][] = 'Status is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Harga') == '') {
      $data['inputerror'][]   = 'Harga';
      $data['error_string'][] = 'Harga is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Etd') == '') {
      $data['inputerror'][]   = 'Etd';
      $data['error_string'][] = 'Estimasi time is required';
      $data['status']         = FALSE;
    }

    // validasi per kolom dalam jumlahContainer
    $quantity  = $this->input->post('Quantity');
    //$notes     = $this->input->post('Notes');

    if (is_array($quantity)) {
      foreach ($quantity as $i => $qty) {
        if (empty($qty)) {
          $data['inputerror'][]   = "Quantity[$i]";
          $data['error_string'][] = 'Quantity Sample is required';
          $data['status']         = FALSE;
        }
      }
    }

    // if (is_array($notes)) {
    //   foreach ($notes as $i => $not) {
    //     if ($not === '' || $not === null) {
    //       $data['inputerror'][]   = "Notes[$i]";
    //       $data['error_string'][] = 'Notes is required';
    //       $data['status']         = FALSE;
    //     }
    //   }
    // }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}