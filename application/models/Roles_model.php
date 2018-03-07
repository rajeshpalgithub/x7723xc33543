<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Roles_model extends CI_Model
{
	function __construct() {
		$this->load->model('Tables_model');
		
	}
	
	function insert_roles($parameters=array('roles_name','roles_client_id','roles_parent_id','roles_created_by','roles_is_active'))
	{
		$error=false;
		$result=array();
		$errortext = '';
		
		$table_roles = $this->Tables_model->roles;
		 $column_roles_client_id= $this->Tables_model->roles('roles_client_id');
		 $column_roles_parent_id= $this->Tables_model->roles('roles_parent_id');
		 $column_roles_name= $this->Tables_model->roles('roles_name');
		 $column_roles_created_by= $this->Tables_model->roles('roles_created_by');
		 $column_roles_is_active= $this->Tables_model->roles('roles_is_active');
		 
		 $role_data = array(
						$column_roles_name =>$parameters['roles_name'],
						$column_roles_client_id =>$parameters['roles_client_id'],
						$column_roles_parent_id =>$parameters['roles_parent_id'],
						$column_roles_created_by =>$parameters['roles_created_by'],
						$column_roles_is_active =>$parameters['roles_is_active'],
						
					
		);
		if($this->db->insert($table_roles, $role_data)){
			$result['insert_id']=$this->db->insert_id();
		}else{
			$error=true;
		}
		 
		 return array('error'=>$error,'errortext'=>$errortext,'result'=>$result);
		
	}
}?>