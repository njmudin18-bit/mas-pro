<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shift extends CI_Controller
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
    $this->load->model('shift_model', 'shift');
  }

  public function index()
  {
    //CHECK FOR ACCESS FOR EACH FUNCTION
    $user_level       = $this->session->userdata('user_level');
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
    if ($check_permission->num_rows() == 1) {
      $data['group_halaman']    = "Master Data";
      $data['nama_halaman']     = "Daftar Shift";
      $data['icon_halaman']     = "icon-layers";
      $data['perusahaan']       = $this->perusahaan->get_details();
      $data['roles']            = $this->roles->get_alls();

      //ADDING TO LOG
      $log_url                  = base_url() . $this->contoller_name . "/" . $this->function_name;
      $log_type                 = "VIEW";
      $log_data                 = "";

      log_helper($log_url, $log_type, $log_data);
      //END LOG

      $this->load->view('adminx/master_data/shift', $data, FALSE);
    } else {
      redirect('errorpage/error403');
    }
  }

  public function shift_add()
  {
    $this->_validation_shift();

    $Data = array(
      'ShiftName'           => $this->input->post('ShiftName'),
      'MondayStartTime'     => $this->input->post('MondayStartTime'),
      'MondayEndTime'       => $this->input->post('MondayEndTime'),
      'TuesdayStartTime'    => $this->input->post('TuesdayStartTime'),
      'TuesdayEndTime'      => $this->input->post('TuesdayEndTime'),
      'WednesdayStartTime'  => $this->input->post('WednesdayStartTime'),
      'WednesdayEndTime'    => $this->input->post('WednesdayEndTime'),
      'ThursdayStartTime'   => $this->input->post('ThursdayStartTime'),
      'ThursdayEndTime'     => $this->input->post('ThursdayEndTime'),
      'FridayStartTime'     => $this->input->post('FridayStartTime'),
      'FridayEndTime'       => $this->input->post('FridayEndTime'),
      'SaturdayStartTime'   => $this->input->post('SaturdayStartTime'),
      'SaturdayEndTime'     => $this->input->post('SaturdayEndTime'),
      'SundayStartTime'     => $this->input->post('SundayStartTime'),
      'SundayEndTime'       => $this->input->post('SundayEndTime'),
      'GracePeriod'         => $this->input->post('GracePeriod'),
      'ShiftAllowance'      => floatval(format_weight($this->input->post('ShiftAllowance'))),
      'Aktivasi'            => $this->input->post('Aktivasi'),
      'MealAllowance'       => $this->input->post('MealAllowance'),
      'CreateDate'          => date('Y-m-d H:i:s'),
      'CreateBy'            => $this->session->userdata('user_code')
    );
    $insert = $this->shift->save($Data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url     = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "ADD";
    $log_data   = json_encode($Data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function shift_list()
  {
    $list = $this->shift->get_datatables();
    $data = array();
    $no   = $_POST['start'];
    foreach ($list as $key => $shift) {
      $Isi   = "'".$shift->ShiftID."'";
      $no++;
      $row   = array();
      $row[] = $no;
      //add html for action
      $row[] = '<div class="btn-group" id="Button_'.$key.'" role="group" aria-label="Button group with nested dropdown">
                  <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="#" onclick="edit('.$Isi.')">Edit</a>
                      <a class="dropdown-item" href="#" onclick="openModalDelete('.$Isi.')">Hapus</a>
                    </div>
                  </div>
                </div>'; //<a class="dropdown-item" href="#" onclick="openModalJamKerja('.$Isi.')">Jam Kerja</a>
      $row[] = $shift->ShiftName;
      $row[] = $shift->Aktivasi == 'Tidak' ? '<label class="label label-danger">'.strtoupper($shift->Aktivasi).'</label>' : '<label class="label label-success">'.strtoupper($shift->Aktivasi).'</label>';
      $row[] = $shift->MondayStartTime;
      $row[] = $shift->MondayEndTime;
      $row[] = $shift->TuesdayStartTime;
      $row[] = $shift->TuesdayEndTime;
      $row[] = $shift->WednesdayStartTime;
      $row[] = $shift->WednesdayEndTime;
      $row[] = $shift->ThursdayStartTime;
      $row[] = $shift->ThursdayEndTime;
      $row[] = $shift->FridayStartTime;
      $row[] = $shift->FridayEndTime;
      $row[] = $shift->SaturdayStartTime;
      $row[] = $shift->SaturdayEndTime;
      $row[] = $shift->SundayStartTime;
      $row[] = $shift->SundayEndTime;
      $row[] = $shift->GracePeriod;
      $row[] = number_format($shift->ShiftAllowance, 0, ',', '.');
      $row[] = $shift->MealAllowance == 'Y' ? 'YA' : 'TIDAK';
      $row[] = $shift->CreateDate;
      $row[] = $shift->CreateBy;

      $data[] = $row;
    }

    $output = array(
      "draw"            => $_POST['draw'],
      "recordsTotal"    => $this->shift->count_all(),
      "recordsFiltered" => $this->shift->count_filtered(),
      "data"            => $data,
    );

    //output to json format
    echo json_encode($output);
  }

  public function shift_edit($id)
  {
    $data = $this->shift->get_by_id($id);
    echo json_encode($data);

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "EDIT";
    $log_data       = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function shift_update()
  {
    $this->_validation_shift();

    $data = array(
      'ShiftName'           => $this->input->post('ShiftName'),
      'MondayStartTime'     => $this->input->post('MondayStartTime'),
      'MondayEndTime'       => $this->input->post('MondayEndTime'),
      'TuesdayStartTime'    => $this->input->post('TuesdayStartTime'),
      'TuesdayEndTime'      => $this->input->post('TuesdayEndTime'),
      'WednesdayStartTime'  => $this->input->post('WednesdayStartTime'),
      'WednesdayEndTime'    => $this->input->post('WednesdayEndTime'),
      'ThursdayStartTime'   => $this->input->post('ThursdayStartTime'),
      'ThursdayEndTime'     => $this->input->post('ThursdayEndTime'),
      'FridayStartTime'     => $this->input->post('FridayStartTime'),
      'FridayEndTime'       => $this->input->post('FridayEndTime'),
      'SaturdayStartTime'   => $this->input->post('SaturdayStartTime'),
      'SaturdayEndTime'     => $this->input->post('SaturdayEndTime'),
      'SundayStartTime'     => $this->input->post('SundayStartTime'),
      'SundayEndTime'       => $this->input->post('SundayEndTime'),
      'GracePeriod'         => $this->input->post('GracePeriod'),
      'ShiftAllowance'      => floatval(format_weight($this->input->post('ShiftAllowance'))),
      'Aktivasi'            => $this->input->post('Aktivasi'),
      'MealAllowance'       => $this->input->post('MealAllowance'),
      'UpdateDate'          => date('Y-m-d H:i:s'),
      'UpdateBy'            => $this->session->userdata('user_code')
    );

    //echo json_encode(array("status" => "error", "data" => $data)); exit;
    $this->shift->update(array('ShiftID' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url    = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type   = "UPDATE";
    $log_data   = json_encode($data);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function shift_deleted($id)
  {
    $data_delete    = $this->shift->get_by_id($id); //DATA DELETE
    $data           = $this->shift->delete_by_id($id);

    echo json_encode(array("status" => "ok"));

    //ADDING TO LOG
    $log_url        = base_url() . $this->contoller_name . "/" . $this->function_name;
    $log_type       = "DELETE";
    $log_data       = json_encode($data_delete);

    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  private function _validation_shift()
  {
    $data                 = array();
    $data['error_string'] = array();
    $data['inputerror']   = array();
    $data['status']       = TRUE;

    if ($this->input->post('ShiftName') == '') {
      $data['inputerror'][]   = 'ShiftName';
      $data['error_string'][] = 'Shift Name is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('MondayStartTime') == '') {
      $data['inputerror'][]   = 'MondayStartTime';
      $data['error_string'][] = 'Monday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('MondayEndTime') == '') {
      $data['inputerror'][]   = 'MondayEndTime';
      $data['error_string'][] = 'Monday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TuesdayStartTime') == '') {
      $data['inputerror'][]   = 'TuesdayStartTime';
      $data['error_string'][] = 'Tuesday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('TuesdayEndTime') == '') {
      $data['inputerror'][]   = 'TuesdayEndTime';
      $data['error_string'][] = 'Tuesday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('WednesdayStartTime') == '') {
      $data['inputerror'][]   = 'WednesdayStartTime';
      $data['error_string'][] = 'Wednesday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('WednesdayEndTime') == '') {
      $data['inputerror'][]   = 'WednesdayEndTime';
      $data['error_string'][] = 'Wednesday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ThursdayStartTime') == '') {
      $data['inputerror'][]   = 'ThursdayStartTime';
      $data['error_string'][] = 'Thursday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ThursdayEndTime') == '') {
      $data['inputerror'][]   = 'ThursdayEndTime';
      $data['error_string'][] = 'Thursday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('FridayStartTime') == '') {
      $data['inputerror'][]   = 'FridayStartTime';
      $data['error_string'][] = 'Friday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('FridayEndTime') == '') {
      $data['inputerror'][]   = 'FridayEndTime';
      $data['error_string'][] = 'Friday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('SaturdayStartTime') == '') {
      $data['inputerror'][]   = 'SaturdayStartTime';
      $data['error_string'][] = 'Saturday Start Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('SaturdayEndTime') == '') {
      $data['inputerror'][]   = 'SaturdayEndTime';
      $data['error_string'][] = 'Saturday End Time is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('GracePeriod') == '') {
      $data['inputerror'][]   = 'GracePeriod';
      $data['error_string'][] = 'Grace Period is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('ShiftAllowance') == '') {
      $data['inputerror'][]   = 'ShiftAllowance';
      $data['error_string'][] = 'Shift Allowance is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('MealAllowance') == '') {
      $data['inputerror'][]   = 'MealAllowance';
      $data['error_string'][] = 'Meal Allowance is required';
      $data['status']         = FALSE;
    }

    if ($this->input->post('Aktivasi') == '') {
      $data['inputerror'][]   = 'Aktivasi';
      $data['error_string'][] = 'Aktivasi is required';
      $data['status']         = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
