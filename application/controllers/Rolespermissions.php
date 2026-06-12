<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rolespermissions extends CI_Controller {

	private $contoller_name;
  private $function_name;

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
      
    $this->load->helper(array('url', 'form', 'cookie', 'mydate', 'text'));
    $this->load->library(array('session', 'cart', 'pagination', 'form_validation'));

    $this->load->model('auth_model', 'auth');
    if($this->auth->isNotLogin());

    //START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
    $this->function_name 	= $this->router->method;
    $this->load->model('Rolespermissions_model');
    //END

    $this->load->model('Dashboard_model');
    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('roles_model', 'roles');
  }

  function roles_permissions($idroles){
  	$roles_level 			= $this->uri->segment(3);
    $user_level 			= $this->session->userdata('user_level');    
    $check_permission = $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);    
    if($check_permission->num_rows() == 1){
	    $page_data['page_name'] 								= 'permissions_roles';
	    $page_data['lang_search'] 							= $this->lang->line('search');    

	    $page_data['lang_input_success'] 				= $this->lang->line('input_success');
	    $page_data['lang_success_input_data'] 	= $this->lang->line('success_input_data');
	    $page_data['lang_delete_success'] 			= $this->lang->line('delete_success');
	    $page_data['lang_delete_data'] 					= $this->lang->line('delete_data');
	    $page_data['lang_delete_confirm'] 			= $this->lang->line('delete_confirm');
	    $page_data['lang_success_delete_data'] 	= $this->lang->line('success_delete_data');
	    $page_data['lang_update_success'] 			= $this->lang->line('update_success');
	    $page_data['lang_success_update_data'] 	= $this->lang->line('success_update_data'); 
	    $page_data['lang_cancel_data'] 					= $this->lang->line('cancel_data');
	    $page_data['lang_cancel_confirm'] 			= $this->lang->line('cancel_confirm'); 
	    $page_data['lang_submit'] 							= $this->lang->line('submit');
	    $page_data['lang_close'] 								= $this->lang->line('close');

	    $getpermissions_data 										= $this->Rolespermissions_model->matrix_permissions($idroles);
	    $page_data['getpermissions_data'] 			= $getpermissions_data;
	    $page_data['getpermissions_group_data'] = $this->Rolespermissions_model->get_permissions_group();
	    $page_data['idroles_edit'] 							= $idroles;
	    
	    $page_data['roles_level']								= $roles_level;
	    $page_data['group_halaman'] 						= "Roles & Permission";
			$page_data['nama_halaman'] 							= "Roles Matrix";
			$page_data['icon_halaman'] 							= "icon-airplay";
	    $page_data['perusahaan'] 								= $this->perusahaan->get_details();
	    $page_data['roles_detail'] 							= $this->roles->get_by_id($roles_level);

	    //ADDING TO LOG
			$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";
			
			log_helper($log_url, $log_type, $log_data);
			//END LOG

	    $this->load->view('adminx/roles/permissions_roles', $page_data, FALSE);
    }else{
        redirect('errorpage/error403');
    }
  }

  function insert_roles_permissions(){
    $user_level 			= $this->session->userdata('user_level');    
    $check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name,$this->function_name,$user_level);    
    if($check_permission->num_rows() == 1){
        $idroles_edit 	= $this->input->post('idroles_edit', TRUE); 
        $idpermissions 	= $_POST['permissions'];  
        $data_roles 		= array();
        $count_roles 		= 0;
        foreach($idpermissions as $idrp ){       
            
          if($idrp != ''){
              
              $dt_roles = $this->Rolespermissions_model->cheked_roles_permissions($idroles_edit)->num_rows();
              if($dt_roles > 0){
                  $this->Rolespermissions_model->delete_roles_data($idroles_edit,'roles_permissions'); 
              }
                array_push($data_roles,array(
                  'idroles'=> $idroles_edit,
                  'status'=> '1',
                  'idpermissions'=>$idpermissions[$count_roles],
                  'created_date' => date("Y-m-d H:i:s")
                ));
                $count_roles++; 
          }  
        }
        $this->Rolespermissions_model->insert_roles_data('roles_permissions',$data_roles);
        $this->session->set_flashdata('update_success','message');

        //ADDING TO LOG
				$log_url 		= base_url().$this->contoller_name."/".$this->function_name;
				$log_type 	= "ROLES";
				$log_data 	= json_encode($data_roles);
				
				log_helper($log_url, $log_type, $log_data);
				//END LOG

        redirect($_SERVER['HTTP_REFERER']);
    }else{
      redirect('errorpage/error403');
    }
  } 
}