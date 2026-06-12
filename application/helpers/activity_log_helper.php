<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

function log_helper($log_url, $log_type, $log_data) {
  $ci=& get_instance();

  // paramter
  $data['log_user_id']   = $ci->session->userdata('user_id');
  $data['log_url']       = $log_url;
  $data['log_type']      = $log_type;
  $data['log_time']      = date("Y-m-d H:i:s");
  $data['log_data']      = $log_data;

  //load model log
  $ci->load->model('Log_model');

  //save to database
  $ci->Log_model->save($data);
}    