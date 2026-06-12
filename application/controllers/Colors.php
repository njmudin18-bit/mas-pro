<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Colors extends CI_Controller
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
    $this->function_name  = $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('users_model', 'users');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
    $this->load->model('colors_model', 'colors');
  }

  public function index()
  {
    $data['group_halaman']    = "Master Data";
    $data['nama_halaman']     = "Daftar Warna FIFO Card";
    $data['icon_halaman']     = "icon-layers";
    $data['perusahaan']       = $this->perusahaan->get_details();
    $data['list']             = $this->db->query("SELECT * FROM table_phone_ext")->result();
    $this->load->view('adminx/master_data/colors', $data, FALSE);
  }

  public function colors_add()
  {
    $this->_validation_colors();
          
    $data = array(
      'MonthNumber'   => $this->input->post('MonthNumber'),
      'MonthName'     => $this->input->post('MonthName'),
      'Colors' 		    => $this->input->post('Colors'),
      'Shapes' 		    => $this->input->post('Shapes'),
      'Aktivasi' 		  => $this->input->post('Aktivasi'),
      'CreateDate'	  => date('Y-m-d H:i:s'),
      'CreateBy'		  => $this->session->userdata('user_code')
    );
    
    $insert = $this->colors->save($data);
    echo json_encode(array("status" => "success"));
    
    //ADDING TO LOG
    $log_url 		    = base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	    = "ADD";
    $log_data 	    = json_encode($data);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
  }

  public function colors_list()
  {
    // Ambil data hasil query dari model
    $list   = $this->colors->get_datatables();
    $data   = array();

    // Amankan nilai $_POST['start'] dan $_POST['draw']
    $start  = isset($_POST['start']) ? (int) $_POST['start'] : 0;
    $draw   = isset($_POST['draw']) ? (int) $_POST['draw'] : 1;

    $no     = $start;
    $noUrut = 0;

    foreach ($list as $key => $group) {
      $no++;
      $noUrut++;

      $row = array();

      // Kolom nomor urut
      $row[] = $no;
      // Kolom aksi dropdown
      $row[] = '<a href="javascript:void(0)" onclick="edit('."'".$group->Id."'".')"
                  class="btn waves-effect waves-light btn-success btn-sm">
                  <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$group->Id."'".')"
                  class="btn waves-effect waves-light btn-danger btn-sm">
                  <i class="fa fa-times"></i>
                </a>';
      // Kolom data warna
      $row[] = $group->MonthNumber;
      $row[] = $group->MonthName;

      // Kotak atau segitiga
      if ($group->Shapes == 'Kotak') {
        $row[] = '<svg width="75" height="75">
                    <rect width="70" height="70" x="2.5" y="2.5" style="fill:'.$group->Colors.';stroke:black;stroke-width:2" />
                  </svg>';
      } else {
        $row[] = '<svg width="75" height="75">
                    <polygon points="35, 0 0, 70 70, 70" style="fill:'.$group->Colors.';stroke:black;stroke-width:2" />
                  </svg>';
      }

      // Kolom status aktivasi
      $row[]  = $group->Aktivasi == 'AKTIF' ?
                '<span class="badge rounded-pill bg-info">'.$group->Aktivasi.'</span>' :
                '<span class="badge rounded-pill bg-dark text-white">'.$group->Aktivasi.'</span>';

      $data[] = $row;
    }

    // Output JSON ke DataTables
    $output = array(
        "draw"            => $draw,
        "recordsTotal"    => $this->colors->count_all(),
        "recordsFiltered" => $this->colors->count_filtered(),
        "data"            => $data,
    );

    echo json_encode($output);
  }

  public function colors_edit($id)
	{
    $data = $this->colors->get_by_id($id);
    echo json_encode($data);
    //ADDING TO LOG
    $log_url 		    = base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	    = "EDIT";
    $log_data 	    = json_encode($data);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
	}

	public function colors_update()
	{
    $this->_validation_colors();

    $data = array(
      'MonthNumber'   => $this->input->post('MonthNumber'),
      'MonthName'     => $this->input->post('MonthName'),
      'Colors' 		    => $this->input->post('Colors'),
      'Shapes' 		    => $this->input->post('Shapes'),
      'Aktivasi' 		  => $this->input->post('Aktivasi'),
      'UpdateDate'	  => date('Y-m-d H:i:s'),
      'UpdateBy'		  => $this->session->userdata('user_code')
    );

    $this->colors->update(array('Id' => $this->input->post('kode')), $data);
    echo json_encode(array("status" => "success"));
    
    //ADDING TO LOG
    $log_url 		    = base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	    = "UPDATE";
    $log_data 	    = json_encode($data);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
	}

  public function colors_deleted()
	{
    $id             = $this->input->post('id_temp');
    $data_delete 	  = $this->colors->get_by_id($id); //DATA DELETE
    $data 			    = $this->colors->delete_by_id($id);
    echo json_encode(array("status" => "ok"));
    
    //ADDING TO LOG
    $log_url 		    = base_url().$this->contoller_name."/".$this->function_name;
    $log_type 	    = "DELETE";
    $log_data 	    = json_encode($data_delete);
    log_helper($log_url, $log_type, $log_data);
    //END LOG
	}

	private function _validation_colors()
	{
		$data 					        = array();
		$data['error_string']   = array();
		$data['inputerror'] 	  = array();
		$data['status'] 		    = TRUE;
		
		if($this->input->post('Colors') == '')
		{
			$data['inputerror'][]   = 'Colors';
			$data['error_string'][] = 'Warna is required';
			$data['status']         = FALSE;
		}
		
		if($this->input->post('Shapes') == '')
		{
			$data['inputerror'][]   = 'Shapes';
			$data['error_string'][] = 'Bentuk is required';
			$data['status']         = FALSE;
		}
		
        if($this->input->post('Aktivasi') == '')
		{
			$data['inputerror'][]   = 'Aktivasi';
			$data['error_string'][] = 'Aktivasi is required';
			$data['status']         = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}
