<?php defined('BASEPATH') OR exit('No direct script access allowed');


class User_model extends CI_Model
{
	
	private $page_default =0;
	private $records_default =20;
	private $client_id;
	function __construct() {
		$this->load->model('Tables_model');
		$this->load->model('Login_model');
		$this->client_id = $this->Basic_model->unique_id;
	}
	
	function getUsers($parameters)
	{
		$user_list = array();
		$users = array(
			'role_id'=>'role_id',
			'role_name'=>'role_name',
			'role_users_id'=>'user_id',
			'role_parent_id'=>'parent_role_id',
			'role_users_name'=>'name',
			'role_users_is_default'=>'is_default',
		
		);
		
		
		 $page = $parameters['page']-1;
		 $records= $parameters['records'];
		 $search_text = $parameters['search_text'];
		 
		 if($page=='' || $page <= 0 )
		 {
			 	$page = $this->page_default;
		 }
		 if($records=='' || $records > 20)
		 {
			 $records=$this->records_default;
		 }
		 
		 $table_roles = $this->Tables_model->roles;
		 $table_role_users = $this->Tables_model->role_users;
		 
		// $column_roles_client_id = $this->Tables_model->roles('roles_client_id');
		 
		 $column_roles_id = $this->Tables_model->roles('roles_id');
		 $column_roles_client_id= $this->Tables_model->roles('roles_client_id');
		 $column_roles_parent_id= $this->Tables_model->roles('roles_parent_id');
		 $column_roles_name= $this->Tables_model->roles('roles_name');
		 $column_roles_created_by= $this->Tables_model->roles('roles_created_by');
		 $column_roles_created_date= $this->Tables_model->roles('roles_created_date');
		 $column_roles_is_active= $this->Tables_model->roles('roles_roles_is_active');
		 
		 $column_role_users_id= $this->Tables_model->role_users('role_users_id');
		 $column_role_users_roles_id= $this->Tables_model->role_users('role_users_roles_id');
		 $column_role_users_name= $this->Tables_model->role_users('role_users_name');
		 $column_role_users_report_to= $this->Tables_model->role_users('role_users_report_to');
		 $column_role_users_address= $this->Tables_model->role_users('role_users_address');
		 $column_role_users_city= $this->Tables_model->role_users('role_users_city');
		 $column_role_user_state= $this->Tables_model->role_users('role_user_state');
		 $column_role_users_country= $this->Tables_model->role_users('role_users_country');
		 $column_role_users_postal_code= $this->Tables_model->role_users('role_users_postal_code');
		 $column_role_users_is_default= $this->Tables_model->role_users('role_users_is_default');
		 $column_role_users_is_active= $this->Tables_model->role_users('role_users_is_active');
		 $column_role_users_created_date= $this->Tables_model->role_users('role_users_created_date');
		 
		 
		$client_id= $this->client_id;
		 
		 if($search_text!='')
		 {
		 	
			
		 	$sql="SELECT  $table_roles.$column_roles_id as role_id, 
			 				$table_roles.$column_roles_name as role_name, 
							$table_role_users.$column_role_users_id as role_users_id,
			 				$table_role_users.$column_role_users_name as role_user_name,
							$table_role_users.$column_role_users_is_default as role_users_is_default
			  FROM $table_roles
	 		
			INNER JOIN 
			$table_role_users
			ON $table_roles.$column_roles_id=$table_role_users.$column_role_users_roles_id
			WHERE
			$table_roles.$column_roles_client_id=$client_id
			AND
			$table_role_users.$column_role_users_name LIKE '%$search_text%'
			OR
			$table_roles.$column_roles_name LIKE '%$search_text%' 
			ORDER BY
			$table_role_users.$column_role_users_created_date";
							
		 }else{
			
			 $sql="SELECT  $table_roles.$column_roles_id as role_id, 
			 				$table_roles.$column_roles_name as role_name, 
							$table_role_users.$column_role_users_id as role_users_id,
			 				$table_role_users.$column_role_users_name as role_user_name,
							$table_role_users.$column_role_users_is_default as role_users_is_default
			  FROM $table_roles
	 		
			INNER JOIN 
			$table_role_users
			ON $table_roles.$column_roles_id=$table_role_users.$column_role_users_roles_id
			WHERE
			$table_roles.$column_roles_client_id=$client_id
			ORDER BY
			$table_role_users.$column_role_users_created_date DESC  LIMIT $page,$records";
			
		
		 				
			
		 }
		
		 
		 $rs=$this->db->query($sql);
		 
		 if($rs->num_rows()>0)
		 {
			  $users_arr=$rs->result_array();
			  foreach($users_arr as $item)
			  {
				  
				  $user_list[]=array(
					 $users['role_users_id']=>$item['role_users_id'],
					 $users['role_users_name']=>$item['role_user_name'],
					 $users['role_name']=>$item['role_name'],
					 $users['role_id']=>$item['role_id'],
					 $users['role_users_is_default']=>$item['role_users_is_default'],
					
				  );
			  }
		 }
		 
		 return $user_list;
		 
	}
	function getTotal_users($parameters)
	{
		$client_id = $this->client_id;
		
		$search_text = $parameters['search_text'];
		
		$table_roles = $this->Tables_model->roles;
		$table_role_users = $this->Tables_model->role_users;
		
		 $column_roles_client_id = $this->Tables_model->roles('roles_client_id');
		 
		 $column_roles_id = $this->Tables_model->roles('roles_id');
		 
		 $column_role_users_roles_id= $this->Tables_model->role_users('role_users_roles_id');
		 $column_role_users_name= $this->Tables_model->role_users('role_users_name');
		 $column_roles_name= $this->Tables_model->roles('roles_name');
		
		if( $search_text != '')
		{
			$sql="SELECT  count(*) as total_rows
			
			 FROM $table_roles
	 		
			INNER JOIN 
			$table_role_users
			ON $table_roles.$column_roles_id=$table_role_users.$column_role_users_roles_id
			WHERE
			$table_roles.$column_roles_client_id=$client_id
			AND
			$table_role_users.$column_role_users_name LIKE '%$search_text%'
			OR
			$table_roles.$column_roles_name LIKE '%$search_text%' ";
		
		 }else{
		 	$sql="SELECT  count(*) as total_rows
			  FROM $table_roles
	 		
			INNER JOIN 
			$table_role_users
			ON $table_roles.$column_roles_id=$table_role_users.$column_role_users_roles_id
			WHERE
			$table_roles.$column_roles_client_id=$client_id";
		}
		
		 $rs=$this->db->query($sql);
		 $ret = $rs->row();
		return $ret->total_rows;
	}
	
	function insertUser($post_data)
	{
	 
	$error='';
	$errortext='';
	$result='';
	
    $client_id=$this->client_id;
	/******** tables *******/
		$table_role = $this->Tables_model->roles;
		$table_role_users = $this->Tables_model->role_users;
		$table_login = $this->Tables_model->login;
		
		$column_role_id= $this->Tables_model->roles('role_id');
		$column_role_client_id = $this->Tables_model->roles('client_id');
		
		 $column_role_users_id= $this->Tables_model->role_users('role_users_id');	
		 $column_role_users_roles_id= $this->Tables_model->role_users('role_users_roles_id');
		 $column_role_users_name= $this->Tables_model->role_users('role_users_name');
		 $column_role_users_report_to= $this->Tables_model->role_users('role_users_report_to');
		 $column_role_users_address= $this->Tables_model->role_users('role_users_address');
		 $column_role_users_city= $this->Tables_model->role_users('role_users_city');
		 $column_role_user_state= $this->Tables_model->role_users('role_user_state');
		 $column_role_users_country= $this->Tables_model->role_users('role_users_country');
		 $column_role_users_postal_code= $this->Tables_model->role_users('role_users_postal_code');
		 $column_role_users_is_active= $this->Tables_model->role_users('role_users_is_active');
		 $column_role_users_login_id= $this->Tables_model->role_users('role_users_login_id');
		 
		 
		 $column_login_email = $this->Tables_model->login('login_email');
		 $column_login_phone_no = $this->Tables_model->login('login_phone_no');
		 $column_login_password = $this->Tables_model->login('login_password');
		 $column_login_client_id = $this->Tables_model->login('login_client_id');
		 $column_login_role = $this->Tables_model->login('login_role');
		 $column_login_user_id = $this->Tables_model->login('login_user_id');
		 $column_login_activation_code = $this->Tables_model->login('login_activation_code');
		 
		 
		 
		 
	/**********************/
	
	
	$role_id=$post_data['role_id'];
	$report_to_id=$post_data['report_to'];
	$user_name = $post_data['name'];
	$user_phone = $post_data['phone'];
	$user_email = $post_data['email'];
	
	$roles = $this->getRole($role_id,$client_id);
	if(empty($roles))
	{
		$error = true;
	   	$errortext .="Incorrect Role Selected<br>";
	}
	
	
	if($report_to_id=="")
	{
	    $report_to_id=0;
	}
	if(!$error)
	{
		 if($report_to_id!=0)
		 {
			 
			// $parent_role_id = $this->get_parent_role();
			
			$sql = "SELECT $table_role_users.* 
							FROM $table_role_users 
							WHERE $table_role_users.$column_role_users_roles_id = $table_role.$column_role_id 
							AND
							$table_role.$column_role_client_id=$client_id
							AND 
							$table_role_users.$column_role_users_id=$report_to_id";
			
			 $rs=$this->db->query($sql);
			 if(!$rs->num_rows()>0)
			 {
			   $error=true;
			   $errortext='Invalid report to user';
			 }
		 }
	}
	
	if(!$error)
	{
		 
		$email_exist = $this->check_user_email_is_exist($user_email);
		if($email_exist)
		{
			$error = true;
			$errortext .="Email Id already exist in our system.<br>";
		}
		if(!$error)
		{
			
					$password = rand();
					$auth_code = rand(999,9999);
					$login_array=array(
						$column_login_email =>$user_email,
						$column_login_phone_no =>$user_phone,
						$column_login_password =>md5($password),
						$column_login_role =>3,
						$column_login_client_id => $client_id,
						$column_login_activation_code=>$auth_code,
						//insert record owner id
					);
			
			
			  
			$this->db->trans_start();
			
			if($this->db->insert($table_login,$login_array))
			{
				$login_id = $this->db->insert_id();
				$user_arr=array
				(
						 $column_role_users_roles_id =>$role_id,
						 $column_role_users_report_to=>$report_to_id,
						 $column_role_users_name=>$user_name,
						 $column_role_users_login_id=>$login_id,
				);
				
				$this->db->insert($table_role_users,$user_arr);
					
					
					// need to setup trigger in mysql before insert trigger and check if email id is exist or not
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === TRUE)
			{
					// email password and authentication code.
				$this->load->model('Common_model');
				$email = array(
					'email'=>$user_email,
					'message'=>"
						<html>
							<body>
								<h1>Welcome to Urcib Crm</h1>
								<p  text-aling=\"center\">Verify your account</p>
								<p>
									Your Login email id: $user_email,<br>
									Login password: $password, <br>
									Auth Code: $auth_code
								</p>
							</body>
						
						</html>
					
					",
					'subject'=>"Welcome to Urcib Crm",
				);
				$email_response = $this->Common_model->send_email($email);
				$result['successMessage'] = "User registration successfull";
				
				
			}else{
				$errortext .="Can't able to register now please try again <br>";
			}
						
					
				
			
			  
		}
		
	}
	 
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 
 	}
	
	function getRole($role_id,$client_id)
	{
		$table_roles = $this->Tables_model->roles;
		$column_roles_id = $this->Tables_model->roles('roles_id');
		$column_roles_client_id = $this->Tables_model->roles('roles_client_id');
		 $column_roles_parent_id= $this->Tables_model->roles('roles_parent_id');
		 $column_roles_name= $this->Tables_model->roles('roles_name');
		 $column_roles_created_by= $this->Tables_model->roles('roles_created_by');
		 $column_roles_created_date= $this->Tables_model->roles('roles_created_date');
		 $column_roles_is_active= $this->Tables_model->roles('roles_is_active');
		
		$return_result=array();
		
		$sql = "SELECT * FROM $table_roles WHERE $table_roles.$column_roles_id = $role_id AND $table_roles.$column_roles_client_id=$client_id";
		$query = $this->db->query($sql);
		$row = $query->row();
		if (isset($row))
		{
				$return_result['role_id'] = $row->$column_roles_id;
				$return_result['role_parent_id'] = $row->$column_roles_parent_id;
				$return_result['role_name'] = $row->$column_roles_name;
				$return_result['role_created_by'] = $row->$column_roles_created_by;
				$return_result['role_is_active'] = $row->$column_roles_is_active;
				$return_result['role_create_date'] = $row->$column_roles_created_date;
		}
		return $return_result;
		
	}
	
	function check_user_email_is_exist($email)
	{
		$table_login = $this->Tables_model->login;
		$column_login_email = $this->Tables_model->login('login_email');
		$column_login_role = $this->Tables_model->login('login_role');
		
		$return_result = false;
		
		$sql = "SELECT * FROM $table_login WHERE $column_login_email = '$email' AND $column_login_role=3";
		$rs=$this->db->query($sql);
		
		 if($rs->num_rows()>0)
		 {
		   $return_result = true;
		 }
		 
		 return $return_result;
	}
	function delete_user($parameters=array('user_id'))
	{
		$return_is_success = false;
		$user_id = $parameters['user_id'];
		
		
		$table_role_users = $this->Table_modle->role_users;
		$column_role_users_id=$this->Tables_model->role_users('role_users_id');
		
		if(!empty($user_id))
		{
			$return_is_success = $this->db->delete($table_role_users, array($column_role_users_id => $user_id));
		}
		return $return_is_success;
	}

	function activate_user($parameters=array('email','auth_code'))
	{
		$error =  false;
		$errortext="";
		$result=array();

		$email = $parameters['email'];
		$auth_code = $parameters['auth_code'];

		$login_update_array=array(
			'login_is_active'=>1,
			
		);

		$this->db->trans_start();
		$this->db->where('activation_code',$auth_code)
		->where('email',$email)
		->where('role',3)
		->update('login',$login_update_array);
		$this->db->trans_complete();


	}
	
}

?>