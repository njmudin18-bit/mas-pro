<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

	var $_table = 'table_user';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function islogin($data){  
    $query = $this->db->get_where('table_user', array('username' => $data['username']));
    $success      = $query->num_rows();
    $successData  = $query->row();

    //JIKA EMAIL DITEMUKAN
    if ($success > 0) {
      $isPasswordTrue = password_verify($data['password'], $successData->password);
      //echo "a".$isPasswordTrue;
      if ($isPasswordTrue) {
        if ($successData->aktivasi == 'Aktif'){

          $dataLogin =  array(
                        'user_id'         => $successData->id,
                        'user_code'       => $successData->id,
                        'user_dept_id'    => $successData->dept_id,
                        'user_dept_name'  => $successData->dept_name,
                        'user_nip'        => $successData->nip,
                        'user_name'       => $successData->username,
                        'user_realName'   => $successData->nama_pegawai,
                        'user_level_name' => $successData->levels,
                        'user_level'      => $successData->user_level,
                        'user_email'      => $successData->email_pegawai,
                        'user_valid'      => true
                      );
          $this->session->set_userdata($dataLogin);

          //GET TANGGAL SEKARANG
          $now = date('Y-m-d H:i:s');

          //UPDATE TABLE USER AND SET LAST LOGIN
          $update = $this->db->query("UPDATE table_user SET last_login = '$now' 
                                      WHERE id = '".$successData->id."'");

          return 30; //$query->num_rows();
        }else{
          return 20; //USERNAME DI BLOCK
        }
      }else{
        return 10; //JIKA PASSWORD SALAH
      }
    }else{
      return 0; //DATA TIDAK DITEMUKAN
    }
	}

  public function islogin_OLD($data){

    $second_DB    = $this->load->database('bjsmas01_db', TRUE);
    $query        = $second_DB->get_where('Ms_User', array('UserID' => $data['username']));
    $success      = $query->num_rows();
    $successData  = $query->row();

    //JIKA EMAIL DITEMUKAN
    if ($success > 0) {
      $password = strtoupper(md5($data['password']));
      if ($password == $successData->Password) {
        $dataLogin =  array(
          'user_id'         => $successData->UserID,
          'user_code'       => $successData->UserID,
          'user_name'       => $successData->UserName,
          'user_email'      => $successData->UserEmail,
          'user_level'      => $successData->Grup,
          'user_div'        => $successData->Division,
          'user_valid'      => true
        );
        $this->session->set_userdata($dataLogin);

        return 30;
      } else {
        return 10; //JIKA PASSWORD SALAH
      }
    }else{
      return 0; //DATA TIDAK DITEMUKAN
    }
  }

  public function isNotLogin(){
    if ($this->session->userdata('user_valid') == false && $this->session->userdata('user_id') == "") {
      redirect(base_url());
      //redirect('welcome');
    }
  }

}