<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Account extends REST_Controller {
	
	function __construct() {
		
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Sop_model');
		$this->load->model('Login_model');
		$this->load->model('School_role_model','',TRUE);
	    $this->load->library('cart');
		
	    $error=false;
		$response_code='';
		$errortext='';
	
	
		$role_permission_array['sub_unique_id']=$this->Basic_model->sub_unique_id;
		$role_permission_array['unique_id']=$this->Basic_model->unique_id;
		$role_permission_array['role']=$this->Basic_model->role;
		$result=$this->Common_model->get_role_permission($role_permission_array);
		$response=$this->Common_model->permission_url($role_permission_array,$result,$this->uri->uri_string(),$this->input->method(TRUE));
		if(!$response)
		{
			$error=true;
			$response_code='401';
			$errortext='Unauthorised access';
		}
		
		if($error)
		{
			$this->response(array('error'=>$error,'errortext'=>$errortext), $response_code);
		}

  }
	
	
	public function person_get()
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
			
			$person_data=$this->School_role_model->GetSchoolRolePerson();
			
			if(!empty($person_data))
			   {
				   $result['person']=$person_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .=  'No record found'.'<br>';
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
		$response_code = '';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$input_array=array(
			  'customer_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$customer_id=$post_data['customer_id'];
			}
			
			if($customer_id=="")
			{
			    $customer_data=$this->Sop_model->GetCustomerData();
			}
			else
			{
				$customer_data=$this->Sop_model->GetCustomerDetailsOnId($customer_id);
			}
			
			
			if(!empty($customer_data))
			   {
				   $result=$customer_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= 'No record found'.'<br>';
			   }
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}


    public function contact_post()
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
		
			$contact_data_array=array();
			$input_array=array(
			 'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'name'=>array('required'=>1,'exp'=>''),
			 'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			 'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_object=(array)$userdata;
			if(!empty($check_object)){
				foreach($userdata as $item)
				{
					//print_r($item);
					$check_input = $this->Rest_model->Check_parameters($input_array,$item);		
					if($check_input['error'])
					{
						$error =  true;
						$errorText .=  $check_input['errortext'].'<br>';
						$response_code='400';
						break;
					}
					else
					{
						$contact_data_array[] =$check_input['result']['input_array'];
						
					}
				}
			
			}
			else
			{
				$check_input = $this->Rest_model->Check_parameters($input_array,$userdata);		
				if($check_input['error'])
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
					
				}
				
			 }
			 
			 if(!$error)
			 {
				 $contact_response=$this->Sop_model->InsertContactResponse($contact_data_array);
				 if(!$contact_response['error'])
				   {
					   $result['successMessage']=$contact_response['result']['successMessage'];
					   $contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
					   $result['contact_list']=$contact_data;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $contact_response['errortext'].'<br>';
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
	
	
	public function contact_put()
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
			  'contact_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'name'=>array('required'=>1,'exp'=>''),
			  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$contact_data=$this->Sop_model->UpdateContactDetailsOnId($post_data);
			
			   if(!$contact_data['error'])
			   {
				   $result['successMessage']=$contact_data['result']['successMessage'];
				   $contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
				   $result['contact_list']=$contact_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= $contact_data['errortext'].'<br>';
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
	
	
	public function contact_delete()
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
			  'contact_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $contact_data=$this->Sop_model->DeleteContactDetailsOnId($post_data);
			
				  if(!$contact_data['error'])
				   {
					   $result['successMessage']=$contact_data['result']['successMessage'];
					   $contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
				       $result['contact_list']=$contact_data;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $contact_data['errortext'].'<br>';
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
	
	
	public function contact_get()
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
			
			$input_array=array(
			  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
			
			   if(!$contact_data['error'])
			   {
				   $result['contact_list']=$contact_data['result']['contact_list'];
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= $contact_data['errortext'].'<br>';
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
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'name'=>array('required'=>1,'exp'=>''),
			  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			  'role_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'description'=>array('required'=>0,'exp'=>''),
			  'billing_street'=>array('required'=>0,'exp'=>''),
			  'billing_city'=>array('required'=>0,'exp'=>''),
			  'billing_state'=>array('required'=>0,'exp'=>''),
			  'billing_postal_code'=>array('required'=>0,'exp'=>''),
			  'billing_country'=>array('required'=>0,'exp'=>''),
			  'shipping_street'=>array('required'=>0,'exp'=>''),
			  'shipping_city'=>array('required'=>0,'exp'=>''),
			  'shipping_state'=>array('required'=>0,'exp'=>''),
			  'shipping_postal_code'=>array('required'=>0,'exp'=>''),
			  'shipping_country'=>array('required'=>0,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$contact_data=$this->Sop_model->InsertCustomerData($post_data);
			
			   if(!$contact_data['error'])
			   {
				   $result['successMessage']=$contact_data['result']['successMessage'];
				   $customer_data=$this->Sop_model->GetCustomerData();
				   $result['accountList']=$customer_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= $contact_data['errortext'].'<br>';
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
			  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'name'=>array('required'=>1,'exp'=>''),
			  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			  'role_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'description'=>array('required'=>0,'exp'=>''),
			  'billing_street'=>array('required'=>0,'exp'=>''),
			  'billing_city'=>array('required'=>0,'exp'=>''),
			  'billing_state'=>array('required'=>0,'exp'=>''),
			  'billing_postal_code'=>array('required'=>0,'exp'=>''),
			  'billing_country'=>array('required'=>0,'exp'=>''),
			  'shipping_street'=>array('required'=>0,'exp'=>''),
			  'shipping_city'=>array('required'=>0,'exp'=>''),
			  'shipping_state'=>array('required'=>0,'exp'=>''),
			  'shipping_postal_code'=>array('required'=>0,'exp'=>''),
			  'shipping_country'=>array('required'=>0,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$contact_data=$this->Sop_model->UpdateCustomerData($post_data);
			
			   if(!$contact_data['error'])
			   {
				   $result['successMessage']=$contact_data['result']['successMessage'];
				   $customer_data=$this->Sop_model->GetCustomerData();
				   $result['accountList']=$customer_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= $contact_data['errortext'].'<br>';
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
