<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('School_role_model');
		$this->load->model('User_model');
		
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'employee'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'employee_permission'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'employee_permission_methods'=>array('GET'=>array(3)),
			'employee_active'=>array('GET'=>array(3)),
			'employee_report_to'=>array('GET'=>array(3)),
			'activate_post'=>array('POST'=>array(3)),
			'change_password'=>array('PUT'=>array(3,4)),
			'get_method_list'=>array('GET'=>array("a")),
		);	

		
    }
    
	public function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','user');
		$this->response($data);
		
	}

    public function role_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$role_array=$this->School_role_model->GetSchoolRole();
			if(!empty($role_array))
			   {
				   $result=$role_array;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .=  'No data found'.'<br>';
			   }
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}


	public function role_post()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->post();
			}
			
			$input_array=array(
			  'name'=>array('required'=>1,'exp'=>''),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->School_role_model->InsertSchoolRole($post_data);
			    if(!$insert_response['error'])
				{
				   $role_data=$this->School_role_model->GetSchoolRole();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['role_list']=$role_data;
				   $response_code='200';
				}
				else
				{
				   $error =  true;
				   $errorText .= $insert_response['errortext'].'<br>';
				}
				
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function role_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->put();
			}
			
			$input_array=array(
			  'role_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'name'=>array('required'=>1,'exp'=>''),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$update_response=$this->School_role_model->UpdateSchoolRole($post_data);
			    if(!$update_response['error'])
				{
				   $role_data=$this->School_role_model->GetSchoolRole();	
				   $result['successMessage']=$update_response['result']['successMessage'];
				   $result['role_list']=$role_data;
				   $response_code='200';
				}
				else
				{
				   $error =  true;
				   $errorText .= $update_response['errortext'].'<br>';
				}
				
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	
public function index_get()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = 200;
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->get();
		}
		$input_array=array(
			  'user_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'search_text'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['any']),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['not_zero']),
			  'records'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
		);
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$input_data=$check_input['result']['input_array'];
			$user_id=$input_data['user_id'];
			if($user_id ==''){
				
				
					
					if(!$error){
						$getUsers = $this->User_model->getUsers($input_data);
						$users['total_records'] =$this->User_model->getTotal_users($input_data);
						$users['records']=count($getUsers);
						$users['users']= $getUsers;
						
					}
			}else{
				// get user details
			}
			
			if(!empty($users))
			{
					   $result=$users;
			} else
			{
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
			}
			 
			
		}else{
			$error=true;
			$errorText .= $check_input['errortext'];
		}
		//$role_persons=$this->School_role_model->GetRolePersons();
		
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errorText .= $e->getMessage();
		$response_code=400;
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}
	
	
public function user_active_get()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = '';
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->get();
		}
		
		$role_array=$this->School_role_model->GetActiveSchoolRole();
		if(!empty($role_array))
		   {
			   $result=$role_array;
			   $response_code='200';
		   }
		   else
		   {
			   $error =  true;
			   $errorText .=  'No data found'.'<br>';
		   }
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errortext = $e->getMessage();
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}
	
public function user_report_to_get()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = '';
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->get();
		}
		
		$role_array=$this->School_role_model->GetSchoolRolePerson();
		if(!empty($role_array))
		   {
			   $result=$role_array;
			   $response_code='200';
		   }
		   else
		   {
			   $error =  true;
			   $errorText .=  'No data found'.'<br>';
		   }
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errortext = $e->getMessage();
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}
	
public function index_post()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = 200;
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->post();
		}
		
		$input_array=array(
		  'role_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'name'=>array('required'=>1,'exp'=>''),
		  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
		  //'password'=>array('required'=>0,'exp'=>''),
		  'report_to'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
		  //'address'=>array('required'=>0,'exp'=>''),
		 // 'city'=>array('required'=>0,'exp'=>''),
		 // 'state'=>array('required'=>0,'exp'=>''),
		 // 'country'=>array('required'=>0,'exp'=>''),
		 // 'postal_code'=>array('required'=>0,'exp'=>''),
		 // 'is_active'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['boolean']),
		  //'is_default'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['boolean']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];
			//$insert_response=$this->School_role_model->InsertRolePerson($post_data);
			$insert_response=$this->User_model->insertUser($post_data);
			if(!$insert_response['error'])
			{
			   	
			   $result['insertMessage']=$insert_response['result']['successMessage'];
			  
			}
			else
			{
			   $error =  true;
			   $errorText .= $insert_response['errortext'].'<br>';
			}
			
			
		}
		else
		{
			$error =  true;
			$errorText .=  $check_input['errortext'].'<br>';
			$response_code=400;
		}
		
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errortext = $e->getMessage();
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}
	
public function index_put()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = '';
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->put();
		}
		
		$input_array=array(
		  'person_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'role_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'name'=>array('required'=>1,'exp'=>''),
		  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
		  'password'=>array('required'=>0,'exp'=>''),
		  'report_to'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'city'=>array('required'=>1,'exp'=>''),
		  'state'=>array('required'=>1,'exp'=>''),
		  'country'=>array('required'=>1,'exp'=>''),
		  'postal_code'=>array('required'=>1,'exp'=>''),
		  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
		  'is_default'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];
			$update_response=$this->School_role_model->UpdateRolePerson($post_data);
			if(!$update_response['error'])
			{
			   $role_persons=$this->School_role_model->GetRolePersons();	
			   $result['successMessage']=$update_response['result']['successMessage'];
			   $result['role_list']=$role_persons;
			   $response_code='200';
			}
			else
			{
			   $error =  true;
			   $errorText .= $update_response['errortext'].'<br>';
			}
				
		}
		else
		{
			$error =  true;
			$errorText .=  $check_input['errortext'].'<br>';
			$response_code='400';
		}
		
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errortext = $e->getMessage();
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}

public function index_delete()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = '';
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->delete();
		}
		
		$input_array=array(
		  'person_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];
			$delete_response=$this->School_role_model->delete_role_person($post_data);
			if(!$delete_response['error'])
			{
			   $role_persons=$this->School_role_model->GetRolePersons();	
			   $result['successMessage']=$delete_response['result']['successMessage'];
			   $result['role_list']=$role_persons;
			   $response_code='200';
			}
			else
			{
			   $error =  true;
			   $errorText .= $delete_response['errortext'].'<br>';
			}
			
			
		}
		else
		{
			$error =  true;
			$errorText .=  $check_input['errortext'].'<br>';
			$response_code='400';
		}
		
		
	}
	catch(Exception $e)
	{
		$error = true;
		$errortext = $e->getMessage();
	}
	
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}


public function user_permission_methods_get()
{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			
			if(!$error)
			{
				$permission_list=$this->School_role_model->GetPermissionList();
				
				  if(!empty($permission_list))
				   {
					   $result['url_list']=$permission_list;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
				   }
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	

	public function user_permission_post()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->post();
			}
			
			$input_array=array(
			  'employee_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'method_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->School_role_model->InsertPermission($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   
				   $result['successMessage']='Data successfully inserted';
				   $permission_list=$this->School_role_model->GetPermisionList();
				   $result['permission_list']=$permission_list;
				   $response_code='200';
			     }
				
				
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function user_employee_hold_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			
			if(!$error)
			{
				$permission_list=$this->School_role_model->GetPermisionList();
				
				if(!empty($permission_list))
				   {
					   $result['permission_list']=$permission_list;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
				   }
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function user_permission_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->put();
			}
			
			$input_array=array(
			  'permission_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'employee_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'method_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->School_role_model->UpdatePermission($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $result['successMessage']='Data successfully updated';
				   $permission_list=$this->School_role_model->GetPermisionList();
				   $result['permission_list']=$permission_list;
				   $response_code='200';
			     }
				
				
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function user_permission_delete()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->delete();
			}
			
			$input_array=array(
			  'permission_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->School_role_model->DeletePermission($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $result['successMessage']='Data successfully deleted';
				   $permission_list=$this->School_role_model->GetPermisionList();
				   $result['permission_list']=$permission_list;
				   $response_code='200';
			     }
				
				
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}

	
	
}

function activate_post() // activate user
{
	
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = 200;
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->post();
		}
		
		$input_array=array(
		  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
		  'auth_code'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];

			$activation_result=$this->User_model->activate_user($post_data);
			if(!$activation_result['error'])
			{
				$result = $activation_result['result'];
			}else{
				$error = true;
				$errorText .=$activation_result['errortext'];
			}

		}else{
			$error =  true;
			$errorText .=  $check_input['errortext'].'<br>';
			
		}
	}catch(Exception $e)
	{
		$error =  true;
		$errorText .=  $e->getMessage();
	}
	$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
}

function change_password_put()
{
	$error =  false;
	$errorText = '';
	$result = '';
	$response_code = 200;
	try
	{
		$userdata=json_decode(file_get_contents('php://input'));
		if(!is_object($userdata))
		{
			$userdata =(object)$this->put();
		}
		
		$input_array=array(
		  'new_password'=>array('required'=>1,'exp'=>''),
		  'old_password'=>array('required'=>1,'exp'=>''),
		  
		);
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);

		if(!$check_input['error'])
		{
			$input_data=$check_input['result']['input_array'];

			//$activation_result=$this->User_model->activate_user($input_data);
			if(!$activation_result['error'])
			{
				//$result = $activation_result['result'];
			}else{
				$error = true;
				$errorText .=$activation_result['errortext'];
			}

		}else{
			$error =  true;
			$errorText .=  $check_input['errortext'].'<br>';
			
		}


	}catch(Exception $e)
	{
		$error =  true;
		$errortext .=$e->getMessage();
	}
}


