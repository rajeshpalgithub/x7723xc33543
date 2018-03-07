<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Role_users_model extends CI_Model
{
	function __construct() {
		$this->load->model('Tables_model');
		
	}
	
	function insert_role_users(
	$parameters=array('role_user_name','role_user_role_id','role_users_report_to','role_user_is_default','role_users_login_id'))
	{
		$error=false;
		$errortext="";
		$result=array();
		
		$table_role_users = $this->Tables_model->role_users;
		
		$column_role_users_name = $this->Tables_model->role_users('role_users_name');
		$column_role_users_report_to = $this->Tables_model->role_users('role_users_report_to');
		$column_role_users_roles_id = $this->Tables_model->role_users('role_users_roles_id');
		$column_role_users_login_id = $this->Tables_model->role_users('role_users_login_id');
		
		
		$role_users_data=array(
			$column_role_users_name=>$parameters['role_user_name'],
			$column_role_users_roles_id=>$parameters['role_user_role_id'],
			$column_role_users_report_to=>$parameters['role_users_report_to'],
			$column_role_users_login_id=>$parameters['role_users_login_id'],
		
		);
		if($this->db->insert($table_role_users, $role_users_data))
		{
			$result['insert_id']=$this->db->insert_id();
			
		}else{
			$error=true;
		}
		
		return array('error'=>$error,'errortext'=>$errortext,'result'=>$result);
	}
}?>