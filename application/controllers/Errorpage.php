<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errorpage extends CI_Controller {

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

    $this->load->helper('url');
		$this->load->model('auth_model', 'auth');

		$this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
  }

  public function error404()
  {
  	$data['perusahaan'] = $this->perusahaan->get_details();
  	$this->load->view('not_found', $data, FALSE);
  }

  public function error403_old()
  {
  	$data['perusahaan'] = $this->perusahaan->get_details();
  	$this->load->view('error403', $data, FALSE);
  }

  public function error403()
  {
  	$data['group_halaman'] 	= "FORBIDDEN";
		$data['nama_halaman'] 	= "Access Denied";
		$data['icon_halaman'] 	= "icon-home";

  	$data['perusahaan'] = $this->perusahaan->get_details();
  	$this->load->view('error403_new', $data, FALSE);
  }
}