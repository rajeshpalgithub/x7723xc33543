<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Employee extends REST_Controller {
	
function __construct() 
{
	parent::__construct();
	$this->load->model('Rest_model');
	$this->load->model('Basic_model');
	$this->load->model('Common_model');
	$this->load->model('Login_model');
	$this->load->model('School_role_model');
	
	$this->check_permission=array(
	 'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
	 'get_method_list'=>array('GET'=>array("a")),
	 'active'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
	 'report_to'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
	);	
}

public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='employee';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','employee');
		if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
		}
	}

public function index_get()
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
		
		$role_persons=$this->School_role_model->GetRolePersons();
		if(!empty($role_persons))
		   {
			   $result=$role_persons;
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
	
public function tree_get()
{
}
	
public function active_get()
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
	
public function report_to_get()
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
	$response_code = '';
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
		  'password'=>array('required'=>1,'exp'=>''),
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
			$insert_response=$this->School_role_model->InsertRolePerson($post_data);
			if(!$insert_response['error'])
			{
			   $role_persons=$this->School_role_model->GetRolePersons();	
			   $result['successMessage']=$insert_response['result']['successMessage'];
			   $result['role_list']=$role_persons;
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

}
