<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengiriman extends CI_Controller {

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

	public function __construct() {
    parent::__construct();

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('pengiriman_model', 'pengiriman');
    $this->load->model('supir_model', 'supir');

    //$this->DB_MASTER  = $this->load->database('mysql', TRUE);
    $this->BJGMAS01   = $this->load->database('bjsmas01_db', TRUE);
  }

  public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Jadwal Pengiriman";
			$data['perusahaan'] 		= $this->perusahaan->get_company_details();

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('pages/sales/pengiriman', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
	}

  public function get_po_number()
  {
    $Search     = $this->input->post('search');
    $StartDate  = $this->input->post('StartDate');
    $EndDate    = $this->input->post('EndDate');
    
    $Sql = "SELECT 
                a.NoBukti AS PoNo, 
                a.SupplierID, 
                a.TGL AS Date
            FROM Trans_POHD a 
            INNER JOIN (
                SELECT * FROM Ms_Partner WHERE TypePartner IN ('S', 'A')
            ) b ON a.SupplierID = b.PartnerID 
            AND a.CompanyCode = b.CompanyCode 
            WHERE CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate'
            AND a.isWip = 0
            AND a.SupplierID IN ('IN022', 'TE002', 'SE008')"; //AND a.SupplierID IN ('TMS', 'SE008', 'IN022', 'RI001', 'SU029', 'AS002', 'TE002')";

    if (!empty($Search)) {
      $Sql .= " AND a.NoBukti LIKE '%$Search%'";
    }

    $Sql .= " ORDER BY a.CreateDate DESC";

    $Query = $this->DB_MASTER->query($Sql);
    $Data   = array();
    foreach ($Query->result() as $Row) {
      $Data[] = array(
        "id"    => $Row->PoNo,
        "name"  => $Row->PoNo
      );
    }

    echo json_encode($Data);
  }

  public function get_fifo_card($Month)
  {
    $Data   = $this->DB_MASTER->get_where('ms_colorshape', array('MonthNumber' => $Month))->row();
    $Isi    = '';
    if ($Data->Shapes == 'Kotak') {
      $Isi  = '<div class="avatar-md" style="margin-left: auto;margin-right: auto;border: 2px solid black;">
                <div style="background-color: '.$Data->Colors.' !important;" class="avatar-title bg-warning-subtle text-black fs-12">
                </div>
              </div>';
    } else {
      $Isi  = '<svg width="75" height="75">
                <polygon points="35, 0 0, 70 70, 70" style="fill:'.$Data->Colors.';stroke:black;stroke-width:2" />
              </svg>';
    }

    return $Isi;
  }

  public function get_part_id() 
  {
    if ($this->input->server('REQUEST_METHOD') != 'POST') {
        // Handle non-POST requests (e.g., return an error)
        $response = array('error' => 'Invalid request method.');
        header('Content-Type: application/json');
        echo json_encode($response);
        
        return;
    }

    $Search     = strtoupper(trim($this->input->post('search')));
    // Use exact match first
    $this->BJGMAS01->group_start();
    $this->BJGMAS01->where('PartID', $Search);
    $this->BJGMAS01->or_where('PartName', $Search);
    // Use LIKE for partial matching
    $this->BJGMAS01->or_like("LTRIM(RTRIM(PartID))", $Search, 'both', FALSE); 
    $this->BJGMAS01->or_like("LTRIM(RTRIM(PartName))", $Search, 'both', FALSE);
    $this->BJGMAS01->group_end();

    $Query    = $this->BJGMAS01->get('Ms_Part');
    $Results  = $Query->result();

    // Prepare the JSON response
    $Data = array();
    foreach ($Results as $row) {
      $Data[] = array(
        'id'    => $row->PartID,
        'name'  => $row->PartName
      );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($Data);
  }

  //SAVE HEADER
  public function pengiriman_add()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
      $this->_validation_pengiriman();

      $PartID     = $this->input->post('PartID'); 
      $PartName   = $this->input->post('PartName');
      $PONumber   = $this->input->post('PONumber');
      $QuantityPO = $this->input->post('QuantityPO');
      $Lokasi     = $this->input->post('Lokasi');
      $Type       = $this->input->post('Type');
      $JadwalData = $this->input->post('jadwalForm');
      $TotalPO    = 0;
      $TotalKirim = 0;
      $SecondData = array();
      $ThirdData  = array();

      $FirstData  = array(
        'NoKirim'     => date('Ym-').$PartID,
        'PartID'      => $PartID,
        'Location'    => $Lokasi,
        'Type'        => $Type,
        'CreateDate'  => date('Y-m-d H:i:s'),
        'CreateBy'    => $this->session->userdata('user_id')
      );

      if (!empty($PONumber)) {
        foreach ($PONumber as $key => $PO) {
          $QtyPO        = floatval(str_replace(',', '', isset($QuantityPO[$key]) ? $QuantityPO[$key] : 0));
          $TotalPO     += floatval($QtyPO);

          $SecondData[]  = array(
            'NoKirim'    => date('Ym-').$PartID,
            'PONumber'   => strtoupper($PO),
            'QuantityPO' => $QtyPO,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateBy'   => $this->session->userdata('user_id')
          );
        }
      }

      //echo json_encode(array("HD data" => $FirstData, "DT data" => $SecondData)); exit;
      //exit;

      if ($TotalKirim == $TotalPO || $TotalKirim < $TotalPO) {
        $InsertHD      = $this->BJGMAS01->insert('Trans_JadwalKirimHD', $FirstData);
        if ($InsertHD) {
          $InsertDT    = $this->BJGMAS01->insert_batch('Trans_JadwalKirimDT1', $SecondData);
          if ($InsertDT) {
            echo json_encode(
              array(
                "status_code" => 200,
                "status"      => "success",
                "message"     => "Data sukses disimpan"
              )
            );
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Data detail gagal disimpan"
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data header gagal disimpan"
            )
          );
        }

        exit;
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Total kirim ".number_format($TotalKirim)." lebih besar dari Total PO ".number_format($TotalPO)
          )
        );
      }
      
      exit;
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //SAVE MORE HEADER
  public function pengiriman_update()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {

      $PartID     = $this->input->post('PartIDEdit'); 
      $PartName   = $this->input->post('PartNameEdit');
      $PONumber   = $this->input->post('PONumber');
      $QuantityPO = $this->input->post('QuantityPO');
      $Lokasi     = $this->input->post('LokasiEdit');
      $Type       = $this->input->post('JenisEdit');
      $ArrayData  = array();

      if (!empty($PONumber)) {
        foreach ($PONumber as $key => $PO) {
          $QtyPO        = floatval(str_replace(',', '', isset($QuantityPO[$key]) ? $QuantityPO[$key] : 0));

          $ArrayData[]  = array(
            'NoKirim'    => date('Ym-').$PartID,
            'PONumber'   => strtoupper($PO),
            'QuantityPO' => $QtyPO,
            'CreateDate' => date('Y-m-d H:i:s'),
            'CreateBy'   => $this->session->userdata('user_id')
          );
        }
      }

      //echo json_encode(array("Second" => $ArrayData)); exit;

      $Insert    = $this->BJGMAS01->insert_batch('Trans_JadwalKirimDT1', $ArrayData);
      if ($Insert) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses disimpan"
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data detail gagal disimpan"
          )
        );
      }
      
      exit;
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //EDIT HEADER
  public function pengiriman_edit()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {

      $PartID    = $this->input->post('PartIDs'); 
      $NoKirim   = $this->input->post('NoKirims');

      $Sql       = "SELECT a.Id, a.NoKirim, a.PONumber, CAST(a.QuantityPO AS INT) AS QuantityPO, 
                    b.PartID, c.PartName, b.Location, b.Type
                    FROM Trans_JadwalKirimDT1 a
                    LEFT JOIN Trans_JadwalKirimHD b ON b.NoKirim = a.NoKirim
                    LEFT JOIN Ms_Part c ON c.PartID = b.PartID
                    WHERE a.NoKirim = '$NoKirim' 
                    AND b.PartID = '$PartID'";
      $Cek    = $this->BJGMAS01->query($Sql);
      //echo $Cek->num_rows(); exit;
      if ($Cek->num_rows() > 0) {
        $Data = $Cek->result();
        $Html = "";
        foreach ($Data as $key => $value) {
          $key    = $value->Id;
          $IsiX   = "'".$value->PartID."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->Id."'";
          $Isi    = "'".$value->PartID."', '".$value->PartName."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->QuantityPO."', '".$value->Id."', '".$value->Location."', '".$value->Type."'";
          $Html .= '<div class="form-group row mb-2">
                      <label class="col-sm-2 col-form-label">Nomor PO</label>
                      <div class="col-md-4">
                        <input value="'.$value->Id.'" type="hidden" name="IdPOUpdate[]" id="IdPOUpdate_'.$key.'" >
                        <input value="'.$value->PONumber.'" type="text" name="PONumberUpdate[]" id="PONumber_'.$key.'" class="form-control text-uppercase" required="required" placeholder="Masukan nomor PO" maxlength="35" autocomplete="off">
                      </div>
                      <label class="col-sm-2 col-form-label">Quantity PO</label>
                      <div class="col-md-2 mb-1">
                        <input value="'.number_format($value->QuantityPO, 0).'" type="text" name="QuantityPOUpdate[]" id="QuantityPO_'.$key.'" maxlength="7" onkeypress="return CheckNumeric();" onkeyup="FormatCurrency(this);" class="form-control" required="required" placeholder="Masukan quantity" autocomplete="off">
                      </div>
                      <div class="button-group col-md-2">
                        <a type="button" onclick="update_header('.$Isi.')" href="javascript:void(0)" class="btn btn-info" id="BtnPOUpdate_'.$key.'" title="Update PO"><i class="ri-save-3-line"></i></a>
                        <a type="button" onclick="hapus_header('.$Isi.')" href="javascript:void(0)" class="btn btn-danger" id="BtnPOHapus_'.$key.'" title="Hapus PO"><i class="ri-delete-bin-fill"></i></a>
                      </div>
                    </div>';
        }

        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data ditemukan",
            "data"        => $Cek->result(),
            "html"        => $Html 
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 404,
            "status"      => "error",
            "message"     => "Data tidak ditemukan",
            "data"        => null,
            "html"        => null
          )
        );
      }
      exit;
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //DELETE HEADER
  public function pengiriman_po_delete()
  {
    //echo "disini";
    $IdDetail     = $this->input->post('Ids');
    $PONumber     = $this->input->post('PONumbers');
    $QuantityPO   = $this->input->post('QuantityPOs');

    //echo $IdDetail."-".$PONumber."-".$QuantityPO;
    //exit;

    $Delete = $this->BJGMAS01->delete('Trans_JadwalKirimDT1', array('Id' => $IdDetail, 'PONumber' => $PONumber, 'QuantityPO' => $QuantityPO));
    if ($Delete) {
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses dihapus."
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data gagal dihapus."
        )
      );
    }
  }

  //UPDATE PO KIRIM
  public function pengiriman_po_update()
  {
    $IdUpdate     = $this->input->post('IdUpdate');
    $PONumbers    = $this->input->post('PONumber');
    $QuantityPOs  = $this->input->post('QuantityPO');

    if (!empty($IdUpdate) && is_array($IdUpdate)) {
      $UpdateData = [];

      foreach ($IdUpdate as $key => $id) {
        $PONumber   = $PONumbers[$key] ?? null;
        $QuantityPO = floatval(str_replace(',', '', $QuantityPOs[$key] ?? 0));

        $UpdateData[] = [
          'Id'          => $id,
          'PONumber'    => $PONumber,
          'QuantityPO'  => $QuantityPO,
          'UpdateDate'  => date('Y-m-d H:i:s'),
          'UpdateBy'    => $this->session->userdata('user_id')
        ];
      }

      //echo json_encode(array("Array" => $UpdateData)); exit;

      $Update = $this->BJGMAS01->update_batch('Trans_JadwalKirimDT1', $UpdateData, 'Id');
      if ($Update) {
        echo json_encode(
          array(
            "status_code" => 200,
            "status"      => "success",
            "message"     => "Data sukses diupdate"
          )
        );
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Data gagal diupdate"
          )
        );
      }

      exit;
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "No data received."
        )
      );
    }
  }

  //HAPUS ALL
  public function pengiriman_hapus()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
      $IdDetail    = $this->input->post('IdDetail');
      $PONumber    = $this->input->post('PONumbers');
      $POQuantity  = floatval(str_replace(',', '', $this->input->post('POQuantitys')));
      $NoKirim     = $this->input->post('NoKirims');
      $PartID      = $this->input->post('PartIDs');
      $Cek         = $this->BJGMAS01->query("SELECT * FROM Trans_JadwalKirimDT1 WHERE NoKirim = '$NoKirim'")->num_rows();
      //echo json_encode(array("Cek" => $Cek, "Id" => $IdDetail, "PO" => $PONumber, "Qty" => $POQuantity, "PartID" => $PartID)); exit;

      //CEK DULU APAKAH DATA LEBIH DARI SATU
      if ($Cek > 1) {
        $DeleteDT = $this->BJGMAS01->where('PONumber', $PONumber)
                    ->where('CAST(QuantityPO AS INT) =', $POQuantity, false)
                    ->delete('Trans_JadwalKirimDT2');
        if ($DeleteDT) {
          $DeleteHD = $this->BJGMAS01->delete('Trans_JadwalKirimDT1', array('Id' => $IdDetail));
          if ($DeleteHD) {
            echo json_encode(
              array(
                "status_code" => 200,
                "status"      => "success",
                "message"     => "Data sukses dihapus."
              )
            );
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Data header gagal dihapus."
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data detail gagal dihapus."
            )
          );
        }
      } else {
        $DeleteDT2 = $this->BJGMAS01->where('PONumber', $PONumber)
                    ->where('CAST(QuantityPO AS INT) =', $POQuantity, false)
                    ->delete('Trans_JadwalKirimDT2');
        if ($DeleteDT2) {
          $DeleteDT1 = $this->BJGMAS01->delete('Trans_JadwalKirimDT1', array('Id' => $IdDetail));
          if ($DeleteDT1) {
            $DeletedHD = $this->BJGMAS01->where('NoKirim', $NoKirim)
                        ->where('PartID', $PartID)
                        ->delete('Trans_JadwalKirimHD');
            if ($DeletedHD) {
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status"      => "success",
                  "message"     => "Data sukses dihapus."
                )
              );
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Data header gagal dihapus."
                )
              );
            }
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Data detail 2 gagal dihapus."
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data detail 2 gagal dihapus."
            )
          );
        }
      }
      exit;
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //SHOW ALL FIRST TIME
  public function pengiriman_list()
  {
    $Draw 			    = intval($this->input->get("draw"));
    $Start 			    = intval($this->input->get("start"));
    $Length 		    = intval($this->input->get("length"));
    $StartDate      = $this->input->post('start_date');
    $EndDate        = $this->input->post('end_date');
    $Location       = $this->input->post('location');
    $Loc            = "";
    if ($Location != 'All') {
      $Loc = " AND b.Location = '$Location' ";
    }

    $Sql            = "SELECT
                        a.Id, b.PartID, c.PartName, a.PONumber,
                        FORMAT(
                          MAX(CAST(a.QuantityPO AS INT)), 
                          'N0'
                        ) AS QuantityPO, 
                        MAX(b.Location) AS Location,
                        MAX(b.Type) AS Type,
                        MAX(a.NoKirim) AS NoKirim,
                        FORMAT(
                          MAX(a.CreateDate), 'yyyy-MM-dd HH:mm:ss'
                        ) AS CreateDate, 
                        MAX(a.CreateBy) AS CreateBy
                      FROM 
                        Trans_JadwalKirimDT1 a 
                        LEFT JOIN Trans_JadwalKirimHD b ON b.NoKirim = a.NoKirim 
                        LEFT JOIN Ms_Part c ON c.PartID = b.PartID 
                      WHERE 
                        CAST(a.CreateDate AS DATE) BETWEEN '$StartDate' AND '$EndDate' 
                        $Loc
                      GROUP BY
                        a.Id, b.PartID, c.PartName, a.PONumber
                      HAVING 
                        NOT (a.PONumber = '' AND MAX(CAST(a.QuantityPO AS INT)) = 0)
                      ORDER BY
                        MAX(a.CreateDate) DESC,
                        c.PartName ASC";
                      //echo $Sql; exit;
		$Query 	        = $this->BJGMAS01->query($Sql);
		$Result         = $Query->result();
		$Data 	        = [];
		$No 		        = 1;
        
		foreach ($Result as $key => $value) {
      $Id     = "'".$value->Id."'";
      $Hapus  = "'".$value->Id."', '".$value->PONumber."', '".$value->QuantityPO."', '".$value->NoKirim."', '".$value->PartID."'";
      $Isi    = "'".$value->PartID."', '".$value->PartName."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->QuantityPO."', '".$value->Id."', '".$value->Location."', '".$value->Type."'";
      $Kirim  = "'".$value->PartID."', '".$value->PartName."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->QuantityPO."', '".$value->Location."', '".$value->Type."'";
			$Link   = base_url('pengiriman/timeline_jadwal_kirim/'.$value->NoKirim.'/'.$value->Location.'/'.base64_encode($value->PONumber));
			//$Link   = base_url('pengiriman/timeline_jadwal_kirim/'.$value->PartID.'/'.$value->PartName.'/'.$value->Location.'/'.base64_encode($value->PONumber).'/'.$value->NoKirim);
      $Data[] = array(
				$No++,
        '<div class="dropdown d-inline-block">
          <button id="btn_'.$key.'" class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ri-more-fill align-middle"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a onclick="buat_jadwal('.$Kirim.')" target="_blank" class="dropdown-item edit-item-btn">
                <i class="ri-calendar-fill align-bottom me-2"></i> Buat jadwal kirim
              </a>
            </li>
            <li>
              <a href="'.$Link.'" target="_blank" class="dropdown-item edit-item-btn">
                <i class="ri-eye-fill align-bottom me-2"></i> Lihat data
              </a>
            </li>
            <li>
              <a onclick="edit_header('.$Isi.')" class="dropdown-item edit-item-btn">
                <i class="ri-pencil-fill align-bottom me-2"></i> Edit data
              </a>
            </li>
            <li>
              <a onclick="openModalDelete('.$Hapus.')" class="dropdown-item remove-item-btn">
                <i class="ri-delete-bin-fill align-bottom me-2"></i> Hapus data
              </a>
            </li>
          </ul>
        </div>',
        $value->Location,
        $value->PartName,
        $value->PartID,
        $value->PONumber,
        $value->QuantityPO,
        $value->CreateDate
			);
		}
		
		$Result = array(
			"draw" 				    => $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 				    => $Data
		);
		
		echo json_encode($Result);
		exit();
  }

  //JADWAL KIRIM
  public function buat_jadwal()
  {
    $PartID     = $this->input->post('PartIDs');
    $PartName   = $this->input->post('PartNames');
    $PONumber   = $this->input->post('PONumbers');
    $NoKirim    = $this->input->post('NoKirims');
    $QuantityPO = $this->input->post('QuantityPOs');

    $Sql    = "SELECT a.*, CAST(a.QuantityPO AS INT) AS QuantityPO, c.PartName
                FROM Trans_JadwalKirimDT2 a
                LEFT JOIN Ms_Part c ON c.PartID = a.PartID
                WHERE a.NoKirim = '$NoKirim' 
                AND A.PONumber = '$PONumber'
                ORDER BY TanggalKirim ASC";
    $Cek    = $this->BJGMAS01->query($Sql);
    //echo $Cek->num_rows(); exit;
    if ($Cek->num_rows() > 0) {
      $Data = $Cek->result();
      $Html = "";
      foreach ($Data as $key => $value) {
        $key    = $value->Id;
        $IsiX   = "'".$value->PartID."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->Id."'";
        $Isi    = "'".$value->PartID."', '".$value->PartName."', '".$value->PONumber."', '".$value->NoKirim."', '".$value->QuantityPO."', '".$value->Id."'";
        $Html .= '<div id="CurrentList_'.$key.'" class="form-group row mb-2">
                    <label class="col-sm-2 col-form-label">Tanggal Kirim</label>
                    <div class="col-md-2">
                      <input type="hidden" value="'.$value->Id.'" name="IdUpdate[]">
                      <input type="date" value="'.$value->TanggalKirim.'" name="TanggalKirimUpdate[]" id="TanggalKirim_'.$key.'" placeholder="Masukan tanggal kirim" class="form-control">
                    </div>
                    <label class="col-sm-1 col-form-label">Plan Qty</label>
                    <div class="col-md-2 mb-1">
                      <input type="text" value="'.number_format($value->PlanQuantity, 0).'" name="QuantityKirimRencanaUpdate[]" id="QuantityKirimRencana_'.$key.'" maxlength="7" onkeypress="return CheckNumeric();" onkeyup="FormatCurrency(this);" class="form-control" required="required" placeholder="Masukan quantity" autocomplete="off">
                    </div>
                    <label class="col-sm-1 col-form-label">Actual Qty</label>
                    <div class="col-md-2 mb-1">
                      <input type="text" value="'.number_format($value->ActualQuantity, 0).'" name="QuantityKirimAktualUpdate[]" id="QuantityKirimAktual_'.$key.'" maxlength="7" onkeypress="return CheckNumeric();" onkeyup="FormatCurrency(this);" class="form-control" required="required" placeholder="Masukan quantity" autocomplete="off">
                    </div>
                    <div class="button-group btn-container col-md-2">
                      <a type="button" onclick="update_detail('.$Isi.')" href="javascript:void(0)" class="btn btn-info" id="BtnUpdate_'.$key.'" title="Update jadwal"><i class="ri-save-3-line"></i></a>
                      <a type="button" onclick="hapus_detail('.$Isi.')" href="javascript:void(0)" class="btn btn-danger" id="BtnHapus_'.$key.'" title="Hapus jadwal"><i class="ri-delete-bin-fill"></i></a>
                    </div>
                  </div>';
      }

      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data ditemukan",
          "data"        => $Cek->result(),
          "html"        => $Html 
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 404,
          "status"      => "error",
          "message"     => "Data tidak ditemukan",
          "data"        => null,
          "html"        => null
        )
      );
    }
  }

  //SAVE DETAIL
  public function buat_jadwal_kirim()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {

      $NoKirim      = $this->input->post('KodeJadwalKirim');
      $PartID       = $this->input->post('JadwalPartID');
      $PONumber     = $this->input->post('JadwalPONumber');
      $QuantityPO   = floatval(str_replace(',', '', $this->input->post('JadwalQuantityPO'))); //$this->input->post('JadwalQuantityPO');
      $JadwalData   = $this->input->post('jadwalForm');
      $TanggalKirim = $this->input->post('TanggalKirim');
      $QtyPlan      = $this->input->post('QuantityKirimRencana');
      $QtyActual    = $this->input->post('QuantityKirimAktual');
      $TotalPO      = $QuantityPO;
      $TotalKirim   = 0;
      $ThirdData    = array();

      if (!empty($TanggalKirim)) {
        foreach ($TanggalKirim as $key => $jadwal) {
          $GetTotalKirimRow = $this->BJGMAS01->query("SELECT CAST(SUM(ActualQuantity) AS INT) AS QuantityKirimAktual FROM Trans_JadwalKirimDT2 WHERE NoKirim = '$NoKirim' AND PONumber = '$PONumber'")->row();
          $GetTotalKirim    = $GetTotalKirimRow->QuantityKirimAktual ?? 0; 
          $TanggalKirim     = isset($jadwal) ? trim($jadwal) : '';
          $QuantityAktual   = !empty($QtyActual[$key]) ? floatval(str_replace(',', '', $QtyActual[$key])) : 0;

          // Stop processing if TanggalKirim is empty
          if (empty($TanggalKirim)) {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Tanggal kirim harus diisi."
              )
            );
            exit();
          }

          if ($QuantityAktual >= 0) {
            $QuantityPlan    = floatval(str_replace(',', '', $QtyPlan[$key]));
            $TotalKirim     += floatval($QuantityAktual);
            $GrandTotalKirim = $GetTotalKirim + $TotalKirim;

            $ThirdData[]  = array(
              'NoKirim'         => date('Ym-').$PartID,
              'PartID'          => $PartID,
              'PONumber'        => $PONumber,
              'QuantityPO'      => $QuantityPO,
              'TanggalKirim'    => $TanggalKirim,
              'PlanQuantity'    => $QuantityPlan,
              'ActualQuantity'  => $QuantityAktual,
              'CreateDate'      => date('Y-m-d H:i:s'),
              'CreateBy'        => $this->session->userdata('user_id')
            );
          }
        }
      }
      
      // echo json_encode(array('Get Total Kirim' => $GetTotalKirim, 'Total Kirim' => $TotalKirim, 'Grand Total' => $GrandTotalKirim, 'Data 3' => $ThirdData, 'Jumlah Data' => count($ThirdData), 'Total PO' => $TotalPO, 'Total Kirim' => $TotalKirim));
      // exit;

      if (count($ThirdData) > 0) {
        if ($TotalKirim == $TotalPO || $TotalKirim < $TotalPO) {
          if ($GrandTotalKirim == $TotalPO || $GrandTotalKirim < $TotalPO) {
            $Insert       = $this->BJGMAS01->insert_batch('Trans_JadwalKirimDT2', $ThirdData);
            if ($Insert) {
              echo json_encode(
                array(
                  "status_code" => 200,
                  "status"      => "success",
                  "message"     => "Data sukses disimpan"
                )
              );
            } else {
              echo json_encode(
                array(
                  "status_code" => 500,
                  "status"      => "error",
                  "message"     => "Data header gagal disimpan"
                )
              );
            }
          } else {
            echo json_encode(
              array(
                "status_code" => 500,
                "status"      => "error",
                "message"     => "Grand Total kirim ".number_format($GrandTotalKirim)." lebih besar dari Total PO ".number_format($TotalPO)
              )
            );
          }
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Total kirim ".number_format($TotalKirim)." lebih besar dari Total PO ".number_format($TotalPO)
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Anda belum mengisi quantity kirim "
          )
        );
      }
      
      exit;
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  //HAPUS DETAIL
  public function hapus_single_jadwal()
  {
    $Id      = $this->input->post('IdDetails');
    $Delete  = $this->BJGMAS01->delete('Trans_JadwalKirimDT2', array('Id' => $Id));
    if ($Delete) {
      echo json_encode(
        array(
          "status_code" => 200,
          "status"      => "success",
          "message"     => "Data sukses dihapus"
        )
      );
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "Data detail 2 gagal dihapus"
        )
      );
    }
  }

  //UPDATE DETAIL
  public function update_single_jadwal()
  {
    $IdUpdate   = $this->input->post('IdUpdate'); 
    $PONumber   = $this->input->post('PONumbers'); 
    $QuantityPO = floatval($this->input->post('QuantityPOs'));
    $TotalPO    = $QuantityPO;
    $NoKirim    = $this->input->post('NoKirims');
    $TglKirims  = $this->input->post('TglKirim');
    $QtyPlans   = $this->input->post('QtyPlan');
    $QtyActuals = $this->input->post('QtyActual');

    if (!empty($IdUpdate) && is_array($IdUpdate)) {
      $UpdateData = [];
      $TotalKirim = 0;

      foreach ($IdUpdate as $key => $id) {
        $QtyActual = floatval(str_replace(',', '', $QtyActuals[$key] ?? 0));
        $TotalKirim += $QtyActual;

        $UpdateData[] = [
          'Id'             => $id,
          'TanggalKirim'   => $TglKirims[$key] ?? null,
          'PlanQuantity'   => floatval(str_replace(',', '', $QtyPlans[$key] ?? 0)),
          'ActualQuantity' => $QtyActual,
          'UpdateDate'     => date('Y-m-d H:i:s'),
          'UpdateBy'       => $this->session->userdata('user_id')
        ];
      }

      //echo json_encode(array("Array" =>$UpdateData, "Total Kirim" => $TotalKirim, "Total PO" => $QuantityPO)); exit;
      if ($TotalKirim == $TotalPO || $TotalKirim < $TotalPO) {
        $Update       = $this->BJGMAS01->update_batch('Trans_JadwalKirimDT2', $UpdateData, 'Id');
        if ($Update) {
          echo json_encode(
            array(
              "status_code" => 200,
              "status"      => "success",
              "message"     => "Data sukses diupdate"
            )
          );
        } else {
          echo json_encode(
            array(
              "status_code" => 500,
              "status"      => "error",
              "message"     => "Data gagal diupdate"
            )
          );
        }
      } else {
        echo json_encode(
          array(
            "status_code" => 500,
            "status"      => "error",
            "message"     => "Total kirim ".number_format($TotalKirim)." lebih besar dari Total PO ".number_format($TotalPO)
          )
        );
      }
      exit;
    } else {
      echo json_encode(
        array(
          "status_code" => 500,
          "status"      => "error",
          "message"     => "No data received."
        )
      );
    }
  }

  //TIMELINE JADWAL KIRIM
  public function timeline_jadwal_kirim()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Planning Kirim Bulanan";
      $data['data']           = $this->pengiriman->get_jadwal_data();
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['NoKirim']        = $this->uri->segment(3) ?? '';
      $data['Location']       = $this->uri->segment(4) ?? '';
      $data['PONumber']       = base64_decode($this->uri->segment(5) ?? '');
			$data['icon_halaman'] 	= "icon-airplay";

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/sales/jadwal', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
  }

  public function timeline_jadwal_kirim_data()
  {
    $Draw                 = intval($this->input->get("draw"));
    $Start                = intval($this->input->get("start"));
    $Length               = intval($this->input->get("length"));
    $Month                = $this->input->post('Months');
    $Year                 = $this->input->post('Years');
    $Location             = $this->input->post('Location');
    
    $Sql                  = "EXEC dbo.GetMonthlyDeliveryReport @Year = $Year, @Month = $Month, @Location = '$Location'";
    $Query                = $this->BJGMAS01->query($Sql, FALSE);
    $Result               = $Query->result();
    $Data 	              = [];
    $No                   = 1;
    foreach ($Result as $key => $Res) {
      $Row    = array();
      $Row[]  = $Res->NomorUrut;
      $Row[]  = $Res->Location;
      $Row[]  = $Res->PartName;
      $Row[]  = $Res->PartID;
      $Row[]  = $Res->PONumber;
      $Row[]  = $Res->QuantityPO;
      $Row[]  = $Res->POLebih;
      for ($i = 1; $i <= 31; $i++) {
        $planKey  = $i . '_PLAN';
        $actKey   = $i . '_ACT';

        $Row[] = (isset($Res->$planKey) && $Res->$planKey != 0) ? $Res->$planKey : '';
        $Row[] = (isset($Res->$actKey) && $Res->$actKey != 0) ? $Res->$actKey : '';
      }
      $Row[]  = $Res->TotalPlan;
      $Row[]  = $Res->TotalKirim;
      $Row[]  = $Res->Percentage;
      $Row[]  = "";
      $Row[]  = "";
      $Row[]  = "";
      $Row[]  = $Res->Selisih;

      $Data[] = $Row;
    }

    $Output = [
      "draw" 				    => $Draw,
			"recordsTotal" 		=> $Query->num_rows(),
			"recordsFiltered" => $Query->num_rows(),
			"data" 				    => $Data
    ];

    echo json_encode($Output);
  }

  public function timeline_jadwal_kirim_data_OLD()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $Location   = $this->input->post('Location');
    $Month      = $this->input->post('Months');
    $Year       = $this->input->post('Years');
    $data       = $this->pengiriman->get_jadwal_data($Location, $Month, $Year);

    $groupedData = [];
    $serialNumber = 1;

    // Group data by ITEM (PartName)
    foreach ($data as $row) {
      $key = $row['PartName'];
      if (!isset($groupedData[$key])) {
        $groupedData[$key] = [
          'NO'            => $serialNumber++,
          'PartID'        => $row['PartID'],
          'ITEM'          => $row['PartName'],
          'No PO'         => [],
          'QTY PO'        => [],
          'PO LEBIH'      => 0,
          'Plan'          => array_fill(1, 31, 0),
          'Actual'        => array_fill(1, 31, 0),
          'Total Plan'    => 0,
          'Stok'          => $row['QuantityPO'],
          'Total Kirim'   => 0
        ];
      }

      $groupedData[$key]['No PO'][]   = $row['PONumber'];
      //$groupedData[$key]['QTY PO'][]  = number_format($row['QuantityPO'], 0);
      $groupedData[$key]['QTY PO'][]  = (int)$row['QuantityPO'];

      // Check if TanggalKirim is a valid date string
      if ($row['TanggalKirim'] && strtotime($row['TanggalKirim']) !== false) {
        $day = date('d', strtotime($row['TanggalKirim']));

        // Ensure PlanQuantity and ActualQuantity are treated as numeric
        if (is_numeric($row['PlanQuantity'])) {
          $groupedData[$key]['Plan'][(int)$day] += (float)$row['PlanQuantity']; // Tambahkan nilai
          $groupedData[$key]['Total Plan']      += (float)$row['PlanQuantity'];
        }

        if (is_numeric($row['ActualQuantity'])) {
          $groupedData[$key]['Actual'][(int)$day] += (float)$row['ActualQuantity']; // Tambahkan nilai
          $groupedData[$key]['Total Kirim']       += (float)$row['ActualQuantity'];
        }
      } else {
        log_message('error', 'Invalid date string: ' . $row['TanggalKirim']);
        continue;
      }
    }

    $data = [];
    $No = 1;
    foreach ($groupedData as $group) {
      $TtlPO = $this->get_total_po($group['No PO'], $group['PartID']);

      $row   = array();
      $row[] = $group['NO'];
      $row[] = $group['ITEM'];
      $row[] = $group['PartID'];
      $row[] = $this->format_po_numbers($group['No PO']);
      $row[] = $this->format_po_real($group['No PO'], $group['PartID']);
      //$row[] = $group['Total Kirim'] - $TtlPO." XX"; //number_format($TtlPO - $group['Total Plan'], 0); // PO LEBIH
      $row[] = number_format(($group['Total Plan'] - $group['Total Kirim']), 0); // PO LEBIH
      for ($day = 1; $day <= 31; $day++) {
          $planValue        = (float)$group['Plan'][$day];
          $formattedPlan    = ($planValue == 0) ? '' : number_format($planValue, 0);
          $row[]            = $formattedPlan;

          $actualValue      = (float)$group['Actual'][$day];
          $formattedActual  = ($actualValue == 0) ? '' : number_format($actualValue, 0);
          $row[]            = $formattedActual;
      }

      $row[] = number_format($group['Total Plan'], 0);
      $row[] = number_format($group['Total Kirim'], 0);
      if ($group['Stok'] != 0 && $group['Total Kirim'] != 0) { // Tambahkan kondisi untuk Total Kirim
        $row[] = number_format(($group['Total Kirim'] / $group['Total Plan']) * 100, 2) . " %";
      } else {
        $row[] = '0 %';
      }
      $row[] = "";
      $row[] = "";
      $row[] = "";
      $row[] = number_format(($group['Total Kirim'] - $TtlPO), 0); //PO KURANG

      $data[] = $row;
    }

    $output = [
      "draw"              => $draw,
      "recordsTotal"      => count($data),
      "recordsFiltered"   => count($data),
      "data"              => $data
    ];

    echo json_encode($output);
  }
  public function get_po_qty($PONumber, $PartID)
  {
    //$Data = $this->BJGMAS01->get_where('Trans_JadwalKirimDT2', array('PONumber' => $PONumber, 'PartID' => $PartID));
    $Data = $this->BJGMAS01
    ->order_by('CreateDate', 'DESC') // Replace 'YourColumnName' with the column you want to sort by
    ->limit(1)
    ->get_where('Trans_JadwalKirimDT2', array('PONumber' => $PONumber, 'PartID' => $PartID));

    if ($Data->num_rows() > 0) {
      return $Data->row()->QuantityPO;
    } else {
      return $PONumber;
    }
  }

  public function get_total_po($poNumbers, $PartID)
  {
    $uniquePOs = array_unique($poNumbers);
    $TotalPO   = 0;
    if (count($uniquePOs) > 1) {
      foreach ($uniquePOs as $po) {
        $qty = $this->get_po_qty(htmlspecialchars($po), $PartID);
        // Konversi ke integer dengan penanganan kesalahan
        if (is_numeric($qty)) {
          $TotalPO += intval($qty);
        } else {
          // Log kesalahan atau tangani kasus non-numerik
          log_message('error', 'Non-numeric value from get_po_qty(): ' . $qty);
          // Misalnya, atur $TotalPO += 0; atau lakukan tindakan lain
        }
      }
    } else {
      $TotalPO = intval($this->get_po_qty(htmlspecialchars(reset($uniquePOs)), $PartID));
    }

    return $TotalPO;
  }
  private function format_po_real($poNumbers, $PartID) 
  {
    $uniquePOs    = array_unique($poNumbers);
    $numberedList = '';
    $count        = 1;
    if (count($uniquePOs) > 1) {
      foreach ($uniquePOs as $po) {
        // Periksa apakah $po adalah NULL
        if ($po === null) {
          $po = '';
        }
        $numberedList .= $count.'. '.number_format($this->get_po_qty(htmlspecialchars($po), $PartID), 0).';<br>';
        $count++;
      }
    } else {
      $numberedList = number_format($this->get_po_qty(htmlspecialchars(reset($uniquePOs)), $PartID), 0);
    }

    return $numberedList;
  }

  // Fungsi untuk memformat No PO
  private function format_po_numbers($poNumbers) 
  {
    $uniquePOs    = array_unique($poNumbers);
    $numberedList = '';
    $count        = 1;
    if (count($uniquePOs) > 1) { // Periksa apakah ada lebih dari satu nilai
      foreach ($uniquePOs as $po) {
          // Periksa apakah $po adalah NULL
          if ($po === null) {
              $po = '';
          }
          $numberedList .= $count . '. ' . htmlspecialchars($po) . ';<br>';
          $count++;
      }
    } else {
        $numberedList = htmlspecialchars(reset($uniquePOs));
    }
    return $numberedList;
  }

  // Fungsi untuk memformat Qty PO
  function format_qty_po($qtyPOs) {
    $uniqueQtyPOs = array_unique($qtyPOs);
    if (count($uniqueQtyPOs) > 1) {
      $numberedList = '';
      $count        = 1;
      foreach ($uniqueQtyPOs as $qty) {
        $numberedList .= $count . '. ' . $qty . ';<br>';
        $count++;
      }

      return $numberedList;
    } else {
      return reset($uniqueQtyPOs);
    }
  }

  // JADWAL PENGIRIMAN HARIAN
  public function pengiriman_harian()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Planning Kirim Harian";
			$data['perusahaan'] 		= $this->perusahaan->get_details();
			$data['SupirList'] 		  = $this->supir->get_nama_supir();
      $data['icon_halaman'] 	= "icon-airplay";

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/sales/pengiriman_harian_new', $data, FALSE);
			//$this->load->view('adminx/sales/pengiriman_harian', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
	}

  public function pengiriman_harian_data()
  {
    $Draw           = intval($this->input->get("draw"));
    $start          = intval($this->input->get("start"));
    $length         = intval($this->input->get("length"));
    $Tanggal        = $this->input->post('tanggal');
    $PilihanDO      = $this->input->post('PilihanDO');

    // $Sql            = "EXEC dbo.GetPlanningKirimHarian @Tanggal = '$Tanggal', @DO = '$PilihanDO'";
    // $Query          = $this->BJGMAS01->query($Sql);
    //echo $Sql; exit;

    $Sql            = "EXEC dbo.GetPlanningKirimHarian @Tanggal = ?, @DO = ?";
    $Query          = $this->BJGMAS01->query($Sql, [$Tanggal, $PilihanDO]);
    $Result         = $Query->result();
    $Data           = [];

    $AllowDepts     = ['IT', 'SALES'];
    $Companies      = ['PT. KENCANA GEMILANG', 'PT. KENCANA SUKSES GEMILANG', 'PT. HARTONO ISTANA TEKNOLOGI'];
    $UserDepts      = $this->session->userdata('user_dept_name');
    $CurrentCompany = '';
    $GroupId        = 0; 

    foreach ($Result as $key => $value) {
      $CBHtml      = '';
      $CBPersiapan = '';
      $CBJamKirim  = '';

      if (!empty($value->NamaPenerima)) {
        $CurrentCompany = $value->NamaPenerima;
      }
      
      $IsHeaderGroup = !empty($value->NamaPenerima) || ($value->WaktuFaktur == 'WAKTU');

      if ($IsHeaderGroup) {
        $GroupId++;

        $CBJamKirim = '<input type="checkbox" id="master_grp_'.$GroupId.'" onchange="toggleGroup('.$GroupId.')">';
      }

      elseif (!empty($value->TanggalDO) && $value->UnitID != 'SUB TOTAL BOX' && $value->WaktuFaktur != 'WAKTU') {
        $Checked = ($value->Pengiriman == 'Y') ? 'checked' : '';
        $Isi    = "'".$CurrentCompany."', '".$value->TanggalDO."', '".$value->NoDO."', '".$value->PoCustomer."', '".$value->PartID."', '".$value->PartName."'";
        
        // Tambahkan onchange="checkChild('.$GroupId.')"
        $CBJamKirim = '<input type="checkbox" class="child_grp_'.$GroupId.'" name="JamKirim[]" value="'.$Isi.'" onchange="checkChild('.$GroupId.')">';
      }

      if (empty($value->NamaPenerima) && in_array($CurrentCompany, $Companies) && !empty($value->TanggalDO) && $value->WaktuFaktur != 'WAKTU') {
        $Isi    = "'".$CurrentCompany."', '".$value->TanggalDO."', '".$value->NoDO."', '".$value->PoCustomer."', '".$value->PartID."', '".$value->PartName."'";
        
        $CBHtml = '<input type="checkbox" name="Plant[]" id="Plant_'.$key.'" value="'.$Isi.'">';
      }

      if (empty($value->NamaPenerima) && !empty($value->TanggalDO) && $value->UnitID != 'SUB TOTAL BOX' && $value->WaktuFaktur != 'WAKTU') {
        $AtributChecked = ($value->Persiapan == 'F') ? 'checked' : '';
        $ValueCB        = ($value->Persiapan == 'P') ? 'F' : 'P';
        $Isi2           = "'".$CurrentCompany."', '".$value->TanggalDO."', '".$value->NoDO."', '".$value->PoCustomer."', '".$value->PartID."', '".$value->PartName."', '".$ValueCB."'";  
        $CBPersiapan    = '<input type="checkbox" name="Persiapan[]" id="Persiapan_'.$key.'" onclick="save_persiapan('.$Isi2.')" value="'.$ValueCB.'" '.$AtributChecked.'>';
      }

      $row    = [];
      $row[]  = $value->No;
      $row[]  = $CBHtml;
      $row[]  = $CBPersiapan;
      $row[]  = $value->TanggalDO;
      $row[]  = $value->WaktuFaktur;
      $row[]  = $value->NoDO;
      $row[]  = $value->PoCustomer;
      $row[]  = $value->PartID;
      $row[]  = $value->PartName;
      $row[]  = $value->NamaPenerima;
      $row[]  = $CBJamKirim;          
      $row[]  = $value->Ekspedisi;                 
      $row[]  = $value->Jam;                 
      $row[]  = $value->Driver;                   
      $row[]  = $value->NoPolisi;                   
      $row[]  = $value->Tanggal;                   
      $row[]  = $value->Qty;
      $row[]  = $value->UnitID;
      $row[]  = $value->JmlBox;
      $row[]  = $value->Plant;
      $row[]  = $value->Noted;

      $Data[] = $row;
    }

    $Output = array(
      "draw"            => $Draw,
      "recordsTotal"    => $Query->num_rows(),
      "recordsFiltered" => $Query->num_rows(),
      "data"            => $Data
    );

    echo json_encode($Output);
    exit();
  }

  public function save_plant() 
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      // 1. Ambil data Array kolom dari AJAX POST
      // Nama variabel PHP disesuaikan menjadi PascalCase
      $PlantTujuan  = $this->input->post('plant_tujuan'); 
      $Noted        = $this->input->post('noted');        
      $TanggalDO    = $this->input->post('tanggal_do');   
      $NoDO         = $this->input->post('no_do');        
      $PoCust       = $this->input->post('po_cust');      
      $PartID       = $this->input->post('part_id');      
      
      // 2. Validasi: Pastikan ada data utama (No DO)
      if (empty($NoDO)) {
          echo json_encode(['status_code' => 400, 'status' => 'error', 'message' => 'Tidak ada data yang dipilih.']);
          return;
      }

      $DataInsert   = array();
      $CurrentTime  = date('Y-m-d H:i:s');
      $UserID       = $this->session->userdata('user_id') ?? 'SYSTEM';
      
      // Mulai Transaksi Database
      $this->BJGMAS01->trans_start();

      // 3. Looping data berdasarkan jumlah No DO
      $count        = count($NoDO);
      $TotalUpdate  = 0;

      for ($i = 0; $i < $count; $i++) {
          
          // Ambil nilai per baris berdasarkan index $i
          $ValNoDO    = $NoDO[$i];
          $ValPoCust  = $PoCust[$i];
          $ValPartID  = $PartID[$i];
          
          // Handling tanggal (convert ke Y-m-d)
          $RawTgl     = $TanggalDO[$i];
          $ValTglDO   = (!empty($RawTgl)) ? date('Y-m-d', strtotime($RawTgl)) : null;

          // Ambil Plant & Noted
          // Gunakan variabel $PlantTujuan di sini
          $ValPlant   = (trim($PlantTujuan[$i]) == '-') ? '' : trim($PlantTujuan[$i]);
          $ValNoted   = trim($Noted[$i]);

          // KUNCI PENCARIAN DATA (Primary Key Logic)
          $WhereCondition = array(
            'DONumber' => $ValNoDO,
            'PONumber' => $ValPoCust,
            'PartID'   => $ValPartID
          );

          // Cek apakah data sudah ada?
          $CheckData = $this->BJGMAS01->get_where('Trans_PlanningKirimNoted', $WhereCondition);

          if ($CheckData->num_rows() > 0) {
            // === KONDISI UPDATE ===
            $DataUpdate = array(
              'DODate'      => $ValTglDO,
              'Plant'       => $ValPlant,
              'Noted'       => ucfirst($ValNoted),
              'UpdatedDate' => $CurrentTime,
              'UpdatedBy'   => $UserID
            );

            $this->BJGMAS01->where($WhereCondition);
            $this->BJGMAS01->update('Trans_PlanningKirimNoted', $DataUpdate);
            $TotalUpdate++;
          } else {
            // === KONDISI INSERT ===
            $DataInsert[] = array(
              'DODate'      => $ValTglDO,
              'DONumber'    => $ValNoDO,
              'PONumber'    => $ValPoCust,
              'PartID'      => $ValPartID,
              'Plant'       => $ValPlant, 
              'Noted'       => ucfirst($ValNoted),
              'CreatedDate' => $CurrentTime, 
              'CreatedBy'   => $UserID
            );
          }
      }

      //echo json_encode(array('status_code' => 500, 'status' => 'error', 'data' => $DataInsert, 'message' => 'error msg')); exit;

      // 4. Eksekusi Insert Batch (Hanya jika ada data baru)
      if (!empty($DataInsert)) {
        $this->BJGMAS01->insert_batch('Trans_PlanningKirimNoted', $DataInsert);
      }

      // Selesaikan Transaksi
      $this->BJGMAS01->trans_complete();

      // 5. Cek status transaksi dan kirim respon JSON
      if ($this->BJGMAS01->trans_status() === FALSE) {
        echo json_encode(['status_code' => 500, 'status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
      } else {
        $TotalInsert = count($DataInsert);
        
        echo json_encode([
          'status_code' => 200, 
          'status'      => 'success', 
          'message'     => "Proses selesai. ($TotalInsert Data Baru, $TotalUpdate Data Diupdate)"
        ]);
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function save_persiapan() 
  {
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);

    if ($check_permission->num_rows() == 1) {
      $DODate       = date('Y-m-d', strtotime($this->input->post('do_date')));   
      $DONumber     = $this->input->post('do_number');        
      $PONumber     = $this->input->post('po_number');      
      $PartID       = $this->input->post('part_id');
      $StatusRaw    = $this->input->post('persiapan'); 
      $Status       = ($StatusRaw == '' || $StatusRaw == null) ? 'P' : $StatusRaw;
      $CurrentTime  = date('Y-m-d H:i:s');
      $UserID       = $this->session->userdata('user_id') ?? 'SYSTEM';
      
      $Where = array(
        'DODate'   => $DODate,
        'DONumber' => $DONumber,
        'PONumber' => $PONumber,
        'PartID'   => $PartID
      );

      $Cek = $this->BJGMAS01->get_where('Trans_PlanningKirimPersiapan', $Where);

      $this->BJGMAS01->trans_start();

      if ($Cek->num_rows() > 0) {
        $DataUpdate = array(
          'Status'      => $Status,
          'UpdatedDate' => $CurrentTime,
          'UpdatedBy'   => $UserID
        );
        
        $this->BJGMAS01->where($Where);
        $this->BJGMAS01->update('Trans_PlanningKirimPersiapan', $DataUpdate);
      } else {
        $DataInsert = array(
          'DONumber'    => $DONumber,
          'DODate'      => $DODate,
          'PONumber'    => $PONumber,
          'PartID'      => $PartID,
          'Status'      => $Status,
          'CreatedDate' => $CurrentTime,
          'CreatedBy'   => $UserID
        );

        $this->BJGMAS01->insert('Trans_PlanningKirimPersiapan', $DataInsert);
      }

      $this->BJGMAS01->trans_complete();
      if ($this->BJGMAS01->trans_status() === FALSE) {
        echo json_encode(['status_code' => 500, 'status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
      } else {
        echo json_encode(['status_code' => 200, 'status' => 'success', 'message' => 'Sukses menyimpan data ke database.']);
      }
    } else {
      echo json_encode(array("status" => "forbidden"));
    }
  }

  public function save_jam_kirim_OLD()
  {
    // 1. CHECK PERMISSION
    $UserLevel       = $this->session->userdata('user_level');
    $CheckPermission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $UserLevel);

    if ($CheckPermission->num_rows() == 1) {
      
      // 2. VALIDATION
      $this->_validation_jam_kirim();

      // 3. AMBIL DATA FORM (HEADER) - [DISESUAIKAN]
      $Expedition   = $this->input->post('Ekspedisi');
      $TanggalKirim = $this->input->post('TanggalKirim');
      $JamKirim     = $this->input->post('JamKirim');
      
      $InputSupir   = $this->input->post('Supir'); 
      $InputMobil   = $this->input->post('Mobil');

      if ($Expedition == 'Y') {
          // EKSPEDISI: SupirID diset statis/dummy, Nama dari Input
          $SupirID   = NULL; // Atau sesuaikan dengan aturan kolom DB (misal: NULL)
          $SupirName = trim($InputSupir); 
          $Mobil     = trim($InputMobil);
      } else {
          // INTERNAL: SupirID dari Input, Nama dari post tambahan (jika ada)
          $SupirID   = $InputSupir; 
          $PostNama  = $this->input->post('SupirNama');
          $SupirName = !empty($PostNama) ? trim($PostNama) : ''; 
          $Mobil     = $InputMobil; 
      }

      // 4. AMBIL DATA ARRAY
      $ArrTanggalDO = $this->input->post('tanggal_do');
      $ArrNoDO      = $this->input->post('no_do');
      $ArrPOCust    = $this->input->post('po_cust');
      $ArrPartID    = $this->input->post('part_id');
      $ArrPartName  = $this->input->post('part_name');
      
      $UserID       = $this->session->userdata('user_id');
      $DateNow      = date('Y-m-d H:i:s');
      
      // Array Penampung Kunci Inputan (Untuk perbandingan logic Cleanup)
      $InputKeysMap = [];

      // Susun Kunci dari Data yang DI-SUBMIT
      if (!empty($ArrNoDO)) {
        foreach ($ArrNoDO as $i => $NoDO) {
            $CleanNoDO   = trim($NoDO);
            $CleanNoPO   = trim($ArrPOCust[$i]);
            $CleanPartID = trim($ArrPartID[$i]);
            
            $Key = $CleanNoDO . '|' . $CleanNoPO . '|' . $CleanPartID;
            $InputKeysMap[] = $Key;
        }
      }

      $this->BJGMAS01->trans_start();

      // ========================================================================
      // PHASE 0: CLEANUP (HAPUS YANG DI-UNCHECK)
      // ========================================================================
      $this->BJGMAS01->select('Id, DONumber, PONumber, PartID');
      
      // LOGIKA DELETE: 
      // Karena untuk Ekspedisi SupirID-nya 'EKSPEDISI', kita harus hati-hati saat cleanup.
      // Sebaiknya cleanup berdasarkan Tanggal Kirim saja untuk item yang tidak ada di list
      // Tapi agar aman, kita skip filter DriverSSN jika Ekspedisi = 'Y' atau gunakan logika khusus.
      
      if ($Expedition == 'N') {
          $this->BJGMAS01->where('DriverSSN', $SupirID);
      } else {
          // Jika Ekspedisi, kita tidak bisa filter by SSN karena SSN-nya dummy ('EKSPEDISI')
          // yang mungkin dipakai banyak orang. 
          // SOLUSI: Filter berdasarkan DriverName ATAU lewati filter driver (hanya Date & DO).
          $this->BJGMAS01->where('DriverName', $SupirName);
      }
      
      $this->BJGMAS01->where('DeliveryDate', $TanggalKirim);
      $ExistingTripData = $this->BJGMAS01->get('Trans_PlanningKirimDetails')->result();

      $IdsToCleanup = [];

      foreach ($ExistingTripData as $RowDB) {
          $DbKey = trim($RowDB->DONumber) . '|' . trim($RowDB->PONumber) . '|' . trim($RowDB->PartID);
          if (!in_array($DbKey, $InputKeysMap)) {
              $IdsToCleanup[] = $RowDB->Id;
          }
      }

      if (!empty($IdsToCleanup)) {
        $this->BJGMAS01->where_in('Id', $IdsToCleanup);
        $this->BJGMAS01->delete('Trans_PlanningKirimDetails');
      }

      // ========================================================================
      // PHASE 1: INSERT / REPLACE DATA YANG DICEKLIS
      // ========================================================================
      
      $BatchInsert = [];
      $LogData     = [];

      if (!empty($ArrNoDO)) {
        foreach ($ArrNoDO as $i => $NoDO) {
          $CleanNoDO   = trim($ArrNoDO[$i]);
          $CleanNoPO   = trim($ArrPOCust[$i]);
          $CleanPartID = trim($ArrPartID[$i]);
          $CleanTglDO  = date('Y-m-d', strtotime($ArrTanggalDO[$i])); 

          // Hapus data eksisting
          $this->BJGMAS01->where('DONumber', $CleanNoDO);
          $this->BJGMAS01->where('PONumber', $CleanNoPO);
          $this->BJGMAS01->where('PartID', $CleanPartID);
          $this->BJGMAS01->where('DODate', $CleanTglDO);
          $this->BJGMAS01->delete('Trans_PlanningKirimDetails');

          // Siapkan Insert Baru
          $DataRowInsert = [
            'DONumber'     => $CleanNoDO,
            'DODate'       => $CleanTglDO,
            'PONumber'     => $CleanNoPO,
            'PartID'       => $CleanPartID,
            'Expedition'   => $Expedition, 
            'DeliveryDate' => $TanggalKirim,
            'DeliveryTime' => $JamKirim,
            'DriverSSN'    => $SupirID,   // Sudah disesuaikan di atas
            'DriverName'   => strtoupper($SupirName), // Sudah disesuaikan di atas
            'VehicleNo'    => strtoupper($Mobil),     // Sudah disesuaikan di atas
            'Status'       => "Y",
            'CreatedBy'    => $UserID,
            'CreatedDate'  => $DateNow
          ];

          $BatchInsert[] = $DataRowInsert;
          $LogData[]     = array_merge($DataRowInsert, ['Action' => 'REPLACE (DELETE-INSERT)']);
        }

        if (!empty($BatchInsert)) {
          $this->BJGMAS01->insert_batch('Trans_PlanningKirimDetails', $BatchInsert);
        }
      }

      $this->BJGMAS01->trans_complete();

      // 7. RESPONSE
      if ($this->BJGMAS01->trans_status() === FALSE) {
        $DBError = $this->BJGMAS01->error();
        echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal DB: " . $DBError['message']));
      } else {
        $LogUrl  = base_url() . $this->contoller_name . "/" . $this->function_name;
        log_helper($LogUrl, "SAVE_BATCH", json_encode($LogData));

        echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Data berhasil disimpan."));
      }
    } else {
      echo json_encode(array("status" => "forbidden", "message" => "Anda tidak memiliki akses."));
    }
  }

  public function save_jam_kirim()
  {
    // 1. CHECK PERMISSION
    $UserLevel       = $this->session->userdata('user_level');
    $CheckPermission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $UserLevel);

    if ($CheckPermission->num_rows() == 1) {

        // 2. VALIDATION
        $this->_validation_jam_kirim();

        // 3. AMBIL DATA FORM (HEADER)
        // Logika Ekspedisi dipertahankan sesuai permintaan
        $Expedition   = $this->input->post('Ekspedisi');
        $TanggalKirim = $this->input->post('TanggalKirim'); // Parameter 5 (DeliveryDate)
        $JamKirim     = $this->input->post('JamKirim');

        $InputSupir   = $this->input->post('Supir');
        $InputMobil   = $this->input->post('Mobil');

        if ($Expedition == 'Y') {
            // EKSPEDISI: SupirID NULL/Dummy, Nama dari Input
            $SupirID   = NULL; 
            $SupirName = trim($InputSupir);
            $Mobil     = trim($InputMobil);
        } else {
            // INTERNAL: SupirID dari Input, Nama dari post tambahan
            $SupirID   = $InputSupir;
            $PostNama  = $this->input->post('SupirNama');
            $SupirName = !empty($PostNama) ? trim($PostNama) : '';
            $Mobil     = $InputMobil;
        }

        // 4. AMBIL DATA ARRAY
        $ArrTanggalDO = $this->input->post('tanggal_do');
        $ArrNoDO      = $this->input->post('no_do');
        $ArrPOCust    = $this->input->post('po_cust');
        $ArrPartID    = $this->input->post('part_id');
        // $ArrPartName = $this->input->post('part_name'); 

        $UserID       = $this->session->userdata('user_id');
        $DateNow      = date('Y-m-d H:i:s');

        $this->BJGMAS01->trans_start();

        // ========================================================================
        // CORE LOGIC: CHECK (DELETE) THEN INSERT
        // ========================================================================
        
        $BatchInsert = [];
        $LogData     = [];

        if (!empty($ArrNoDO)) {
            foreach ($ArrNoDO as $i => $NoDO) {
                // Bersihkan Input
                $CleanNoDO   = trim($ArrNoDO[$i]);          // Parameter 1
                $CleanTglDO  = date('Y-m-d', strtotime($ArrTanggalDO[$i])); // Parameter 2
                $CleanNoPO   = trim($ArrPOCust[$i]);        // Parameter 3
                $CleanPartID = trim($ArrPartID[$i]);        // Parameter 4
                // Parameter 5 adalah $TanggalKirim (DeliveryDate)

                // -----------------------------------------------------------
                // LANGKAH A: HAPUS DAHULU (JIKA ADA DATA DENGAN 5 PARAMETER INI)
                // -----------------------------------------------------------
                $this->BJGMAS01->where('DONumber', $CleanNoDO);
                $this->BJGMAS01->where('DODate', $CleanTglDO);
                $this->BJGMAS01->where('PONumber', $CleanNoPO);
                $this->BJGMAS01->where('PartID', $CleanPartID);
                $this->BJGMAS01->where('DeliveryDate', $TanggalKirim); // <--- Kunci agar bisa parsial beda hari
                $this->BJGMAS01->delete('Trans_PlanningKirimDetails');

                // -----------------------------------------------------------
                // LANGKAH B: SIAPKAN INSERT BARU
                // -----------------------------------------------------------
                $DataRowInsert = [
                  'DONumber'     => $CleanNoDO,
                  'DODate'       => $CleanTglDO,
                  'PONumber'     => $CleanNoPO,
                  'PartID'       => $CleanPartID,
                  'Expedition'   => $Expedition,
                  'DeliveryDate' => $TanggalKirim, 
                  'DeliveryTime' => $JamKirim,
                  'DriverSSN'    => $SupirID,
                  'DriverName'   => strtoupper($SupirName),
                  'VehicleNo'    => strtoupper($Mobil),
                  'Status'       => "Y",
                  'CreatedBy'    => $UserID,
                  'CreatedDate'  => $DateNow
                ];

                $BatchInsert[] = $DataRowInsert;
                $LogData[]     = array_merge($DataRowInsert, ['Action' => 'REPLACE (5 PARAMS)']);
            }

            // Eksekusi Insert Sekaligus (Lebih cepat daripada insert di dalam loop)
            if (!empty($BatchInsert)) {
                $this->BJGMAS01->insert_batch('Trans_PlanningKirimDetails', $BatchInsert);
            }
        }

        $this->BJGMAS01->trans_complete();

        // 7. RESPONSE
        if ($this->BJGMAS01->trans_status() === FALSE) {
            $DBError = $this->BJGMAS01->error();
            echo json_encode(array("status_code" => 500, "status" => "error", "message" => "Gagal DB: " . $DBError['message']));
        } else {
            $LogUrl  = base_url() . $this->contoller_name . "/" . $this->function_name;
            log_helper($LogUrl, "SAVE_PLANNING", json_encode($LogData));

            echo json_encode(array("status_code" => 200, "status" => "success", "message" => "Data berhasil disimpan."));
        }
    } else {
      echo json_encode(array("status" => "forbidden", "message" => "Anda tidak memiliki akses."));
    }
  }

  //REKAP MONTHLY REPORT
  public function recap_monthly_report()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Sales";
			$data['nama_halaman'] 	= "Recap of Monthly Delivery Schedule";
			$data['perusahaan'] 		= $this->perusahaan->get_company_details();

			//ADDING TO LOG
			$log_url 		            = base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	            = "VIEW";
			$log_data 	            = "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('pages/sales/recap_monthly_report', $data, FALSE);
		} else {
			redirect('errors/error403');
		}
  }

  public function recap_monthly_report_data()
  {
    $draw       = intval($this->input->get("draw"));
    $start      = intval($this->input->get("start"));
    $length     = intval($this->input->get("length"));
    $Location   = $this->input->post('Location');
    $Month      = $this->input->post('Months');
    $Year       = $this->input->post('Years');
    $data       = $this->pengiriman->get_jadwal_data($Location, $Month, $Year);

    $groupedData = [];
    $serialNumber = 1;

    // Group data by ITEM (PartName)
    foreach ($data as $row) {
      $key = $row['PartName'];
      if (!isset($groupedData[$key])) {
        $groupedData[$key] = [
          'NO'            => $serialNumber++,
          'PartID'        => $row['PartID'],
          'ITEM'          => $row['PartName'],
          'No PO'         => [],
          'QTY PO'        => [],
          'PO LEBIH'      => 0,
          'Plan'          => array_fill(1, 31, 0),
          'Actual'        => array_fill(1, 31, 0),
          'Total Plan'    => 0,
          'Stok'          => $row['QuantityPO'],
          'Total Kirim'   => 0
        ];
      }

      $groupedData[$key]['No PO'][]   = $row['PONumber'];
      //$groupedData[$key]['QTY PO'][]  = number_format($row['QuantityPO'], 0);
      $groupedData[$key]['QTY PO'][]  = (int)$row['QuantityPO'];

      // Check if TanggalKirim is a valid date string
      if ($row['TanggalKirim'] && strtotime($row['TanggalKirim']) !== false) {
        $day = date('d', strtotime($row['TanggalKirim']));

        // Ensure PlanQuantity and ActualQuantity are treated as numeric
        if (is_numeric($row['PlanQuantity'])) {
          $groupedData[$key]['Plan'][(int)$day] += (float)$row['PlanQuantity']; // Tambahkan nilai
          $groupedData[$key]['Total Plan']      += (float)$row['PlanQuantity'];
        }

        if (is_numeric($row['ActualQuantity'])) {
          $groupedData[$key]['Actual'][(int)$day] += (float)$row['ActualQuantity']; // Tambahkan nilai
          $groupedData[$key]['Total Kirim']       += (float)$row['ActualQuantity'];
        }
      } else {
        log_message('error', 'Invalid date string: ' . $row['TanggalKirim']);
        continue;
      }
    }

    $data = [];
    $No = 1;
    foreach ($groupedData as $group) {
      $TtlPO = $this->get_total_po($group['No PO'], $group['PartID']);

      $row   = array();
      $row[] = $group['NO'];
      $row[] = $group['ITEM'];
      $row[] = $group['PartID'];
      $row[] = number_format($group['Total Kirim'], 0);
      $row[] = number_format($group['Total Plan'], 0);
      if ($group['Stok'] != 0 && $group['Total Kirim'] != 0) {
        $row[]  = number_format(($group['Total Kirim'] / $group['Total Plan']) * 100, 2) . " %";
      } else {
        $row[]  = '0 %';
      }
      $row[]    = number_format(($group['Total Plan'] - $group['Total Kirim']), 0);

      $data[]   = $row;
    }

    $output = [
      "draw"              => $draw,
      "recordsTotal"      => count($data),
      "recordsFiltered"   => count($data),
      "data"              => $data
    ];

    echo json_encode($output);
  }

  //FUNCTION UPDATE STATUS KIRIM
  public function update_status_kirim()
  {
    $IdKirim      = $this->input->post('IdKirim');
    $StatusKirim  = $this->input->post('StatusKirim');
    $Status       = "";
    if ($StatusKirim == "BELUM") {
      $Status     = "YA";
    } else {
      $Status     = NULL;
    }

    //echo json_encode(array("Id" => $IdKirim, "StatusKirim" => $StatusKirim, "Status" => $Status)); exit;

    $Responses = $this->pengiriman->update_status_kirim($IdKirim, $Status);
    
    echo $Responses;
  }

  private function _validation_pengiriman()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('PartID') == '') {
      $data['inputerror'][]   = 'PartID';
      $data['error_string'][] = 'Part ID is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('Lokasi') == '') {
      $data['inputerror'][]   = 'Lokasi';
      $data['error_string'][] = 'Lokasi is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('Type') == '') {
      $data['inputerror'][]   = 'Type';
      $data['error_string'][] = 'Type is required';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
  private function _validation_jam_kirim()
  {
      $data             = array();
      $data['error_string'] = array();
      $data['inputerror']   = array();
      $data['status']       = TRUE;

      // 1. Validasi Pilihan Ekspedisi
      if ($this->input->post('Ekspedisi') == '') {
          $data['inputerror'][]   = 'Ekspedisi';
          $data['error_string'][] = 'Pilih jenis pengiriman (Internal/Ekspedisi)';
          $data['status'] = FALSE;
      }

      // 2. Validasi Tanggal
      if ($this->input->post('TanggalKirim') == '') {
          $data['inputerror'][]   = 'TanggalKirim';
          $data['error_string'][] = 'Tanggal Kirim harus diisi';
          $data['status'] = FALSE;
      }

      // 3. Validasi Jam Kirim
      // LOGIKA: Jika Ekspedisi = 'Y', maka Jam Kirim TIDAK wajib (boleh kosong).
      // Jika Ekspedisi = 'N' atau kosong, maka Jam Kirim WAJIB.
      $is_ekspedisi = ($this->input->post('Ekspedisi') == 'Y');

      if (!$is_ekspedisi) {
          if ($this->input->post('JamKirim') == '') {
              $data['inputerror'][]   = 'JamKirim';
              $data['error_string'][] = 'Jam Kirim harus diisi untuk pengiriman Internal';
              $data['status'] = FALSE;
          }
      }

      // 4. Validasi Supir (Wajib untuk kedua kondisi)
      // Asumsi: name input di HTML adalah "Supir" (baik dropdown maupun textbox)
      // Jika Anda menggunakan name="SupirNama" saat ekspedisi, ganti baris post di bawah.
      if ($this->input->post('Supir') == '') {
          $data['inputerror'][]   = 'Supir';
          
          // Pesan error dinamis
          if ($is_ekspedisi) {
              $data['error_string'][] = 'Nama Supir Ekspedisi harus diketik';
          } else {
              $data['error_string'][] = 'Silakan pilih Supir Internal';
          }
          $data['status'] = FALSE;
      }

      // 5. Validasi Mobil (Wajib untuk kedua kondisi)
      if ($this->input->post('Mobil') == '') {
          $data['inputerror'][]   = 'Mobil';
          
          // Pesan error dinamis
          if ($is_ekspedisi) {
              $data['error_string'][] = 'Nopol / Jenis Kendaraan harus diketik';
          } else {
              $data['error_string'][] = 'Silakan pilih Mobil Internal';
          }
          $data['status'] = FALSE;
      }

      if ($data['status'] === FALSE) {
          echo json_encode($data);
          exit();
      }
  }

  private function _validation_jam_kirim_OLD()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('Ekspedisi') == '') {
      $data['inputerror'][]   = 'Ekspedisi';
      $data['error_string'][] = 'Ekspedisi is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('TanggalKirim') == '') {
      $data['inputerror'][]   = 'TanggalKirim';
      $data['error_string'][] = 'Tanggal Kirim is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('Ekspedisi') == '' || $this->input->post('Ekspedisi') == 'N') {
      if ($this->input->post('JamKirim') == '') {
        $data['inputerror'][]   = 'JamKirim';
        $data['error_string'][] = 'Jam Kirim is required';
        $data['status'] = FALSE;
      }
    }

    if ($this->input->post('Supir') == '') {
      $data['inputerror'][]   = 'Supir';
      $data['error_string'][] = 'Supir is required';
      $data['status'] = FALSE;
    }

    if ($this->input->post('Mobil') == '') {
      $data['inputerror'][]   = 'Mobil';
      $data['error_string'][] = 'Mobil is required';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}