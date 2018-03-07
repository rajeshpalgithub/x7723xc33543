<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Client_model extends CI_Model
{
	
	function __construct() {
		$this->load->model('Tables_model');
		$this->load->model('User_model');
		
	}
	function add_client($parameters=array('user_name','user_email','user_phone','business_name','business_short_name'=>'','client_address','client_city','client_state','client_country_id','client_vendor_id'=>0,'client_time_zone'))
	{
		$error= false;
		$errortext='';
		$result=array();
		
		$table_client = $this->Tables_model->client;
		$table_login  = $this->Tables_model->login;
		$table_roles = $this->Tables_model->roles;
		$table_role_users = $this->Tables_model->role_users;
		
		
		$column_client_id = $this->Tables_model->client('client_id');
		$column_client_name = $this->Tables_model->client('client_name');
		$column_client_short_name = $this->Tables_model->client('client_short_name');
		$column_client_address = $this->Tables_model->client('client_address');
		$column_client_city = $this->Tables_model->client('client_city');
		$column_client_state = $this->Tables_model->client('client_state');
		$column_client_pin_code = $this->Tables_model->client('client_pin_code');
		$column_client_country_id = $this->Tables_model->client('client_country_id');
		$column_client_vendor_id = $this->Tables_model->client('client_vendor_id');
		$column_client_time_zone = $this->Tables_model->client('client_time_zone');
		$column_client_login_id =  $this->Tables_model->client('client_login_id');
		
		$column_login_email = $this->Tables_model->login('login_email');
		$column_login_password = $this->Tables_model->login('login_password');
		$column_login_phone = $this->Tables_model->login('login_phone_no');
		$column_login_role = $this->Tables_model->login('login_role');
		$column_login_client_id = $this->Tables_model->login('login_client_id');
		$column_login_activation_code = $this->Tables_model->login('login_activation_code');
		
		/*$column_roles_name = $this->Table_model->roles('roles_name');
		$column_roles_parent_id = $this->Table_model->roles('roles_parent_id');
		$column_roles_client_id = $this->Table_model->roles('roles_client_id');
		$column_roles_created_by = $this->Table_model->roles('roles_created_by');*/
		
		
		$client_email = $parameters['user_email'];
		$client_phone = $parameters['user_phone'];
		$password = rand();
		$auth_code = rand(999,9999);
		
			$login_data = array(
				$column_login_email=>$client_email,
				$column_login_password=> md5($password),
				$column_login_phone=>$client_phone,
				$column_login_role=>3,
				$column_login_activation_code => $auth_code
			
			);
		// check if email id exist or not
		if($this->User_model->check_user_email_is_exist($client_email))
		{
			$error = true;
			$errortext .="Email Id already exist in our system.<br>";
		}else{
			$this->db->trans_start();
			if(!$error && $this->db->insert($table_login, $login_data))
			{
				$login_id = $this->db->insert_id();
				$client_data = array(
					$column_client_name => $parameters['business_name'],
					$column_client_short_name=> $parameters['business_short_name'],
					$column_client_address => $parameters['client_address'],
					$column_client_city => $parameters['client_city'],
					$column_client_state => $parameters['client_state'],
					$column_client_country_id => $parameters['client_country_id'],
					$column_client_vendor_id => $parameters['client_country_id'],
					$column_client_time_zone=>$parameters['client_time_zone'],
					$column_client_login_id =>$login_id,
				);
				
				if($this->db->insert($table_client, $client_data))
				{
						$this->load->model('Roles_model');
						
						$client_id = $this->db->insert_id();
						$role_data = array(
							'roles_name' =>'Admin',
							'roles_client_id' =>$client_id,
							'roles_parent_id'=>0,
							'roles_created_by'=>'',
							'roles_is_active'=>1
						);
						// insert into role table 
						$role_insert_result = $this->Roles_model->insert_roles($role_data);
						if(!$role_insert_result['error'])
						{
							$role_id = $role_insert_result['result']['insert_id'];
							// insert into role users table
							$this->load->model('Role_users_model');
							$role_users_data = array(
								'role_user_name'=>$parameters['user_name'],
								'role_user_role_id'=>$role_id,
								'role_users_report_to'=>0,
								'role_user_is_default'=>1,
								'role_users_login_id'=>$login_id,
							);
							$user_insert_result = $this->Role_users_model->insert_role_users($role_users_data);
							if(!$user_insert_result['error'])
							{
								$result['successMessage']="Your registration is successfull.<br>";
							}else{
								$this->db->trans_rollback();
							}
						}else{
							$this->db->trans_rollback();
						}
				}else{
					$this->db->trans_rollback();
				}
				
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
			{
				$error = true;
				$errortext .="Can't able to register now, please try again later.<br>";
					// generate an error... or use the log_message() function to log your error
			}else{
				$this->load->model('Common_model');
					$email = array(
						'email'=>$client_email,
						'message'=>"
							<html>
								<body>
									<h1 align=\"center\" >Thanks for signing up with Urcib Crm!</h1>
									<p aling=\"center\">Verify your account</p>
									<p>
										Your Login email id: $client_email,<br>
										Login password: $password, <br>
										Auth Code: $auth_code
									</p>
								</body>
							
							</html>
						
						",
						'subject'=>"Welcome to Urcib Crm:Verify your account",
					);
					$email_response = $this->Common_model->send_email($email);
					$result['successMessage'] = "User registration successfull";
			}
		}
		return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}
}

?>