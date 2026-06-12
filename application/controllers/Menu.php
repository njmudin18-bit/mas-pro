<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

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
      
    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('menu_model', 'menu');
  }

  public function index()
	{
		$data['group_halaman'] 	= "Managment Menu";
		$data['nama_halaman'] 	= "Master Menu";
		$data['icon_halaman'] 	= "icon-airplay";

		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$this->load->view('adminx/menu/menu_master', $data, FALSE);
	}

	public function menu_add()
  {
  	$data = array(
			'menu_id' 					=> $this->input->post('id_project'),
			'title' 						=> $this->input->post('nama_task'),
			'url' 							=> $this->input->post('nama_task'),
			'icon' 							=> $this->input->post('nama_task'),
			'is_active' 				=> $this->input->post('nama_task'),
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$insert = $this->menu->save($data);
		echo json_encode(array("status" => "ok"));
  }

  public function menu_list()
  {
  	$list = $this->menu->get_datatables();
  	//print_r($list);
		$data = array();
		$no 	= $_POST['start'];
		$noUrut = 0;
		foreach ($list as $menu) {
			$no++;
			$noUrut++;
			$row = array();
			$row[] = $no;
			//add html for action
			$row[] = '<a href="javascript:void(0)" onclick="edit('."'".$menu->id."'".')"
									class="btn waves-effect waves-light btn-success btn-outline-success btn-sm">
									<i class="fa fa-edit"></i>
								</a>
                <a href="javascript:void(0)" onclick="openModalDelete('."'".$menu->id."'".')"
                	class="btn waves-effect waves-light btn-danger btn-outline-danger btn-sm">
                	<i class="fa fa-times"></i>
                </a>';
			$row[] = $menu->menu_id;
			$row[] = $menu->title;
			$row[] = $menu->url;
			$row[] = $menu->icon;
			$row[] = $menu->is_active;
		
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->menu->count_all(),
			"recordsFiltered" => $this->menu->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
  }

  public function menu_edit($id)
	{
		$data = $this->menu->get_by_id($id);
		echo json_encode($data);
	}

	public function menu_update()
	{
		$data = array(
			'status_task' 			=> $this->input->post('status_task'),
			'create_date'				=> date('Y-m-d H:i:s'),
			'create_by' 				=> $this->session->userdata('user_code')
		);
		$this->menu->update(array('id' => $this->input->post('kode')), $data);
		echo json_encode(array("status" => TRUE));
	}

  public function menu_deleted($id)
	{
		$data = $this->menu->delete_by_id($id);
		echo json_encode(array("status" => "ok"));
	}
}