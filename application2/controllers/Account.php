<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Account extends REST_Controller {
	
	function __construct() {
		
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Account_model');
		$this->load->model('Sop_model');
		$this->load->model('Login_model');
		$this->load->model('School_role_model','',TRUE);
	    $this->load->library('cart');
		
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(0,3),'PUT'=>array(3,4),'DELETE'=>array(3)),
			'type'=>array('GET'=>array(3),'POST'=>array(3),'DELETE'=>array(3)),
			'get_method_list'=>array('GET'=>array("a")),
			'profile'=>array('GET'=>array(3,4)),
			'contact'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
			'billing_address'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
			'shipping_address'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
		);

  }
  
  
    public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		///$data['fetch_class_name']='account';
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','account');
		$this->response($data);
		/*if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
		}*/
	}
	
	public function activate_post()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
				$userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->post();
				}
			
				$contact_data_array=array();
				$input_array=array(
				 'email_phone'=>array('required'=>1,'exp'=>''),
				 'auth_code'=>array('required'=>1,'exp'=>''),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					if(!$error)
					{
						$contact_data=$this->Sop_model->Activate_user($post_data);
						if(!$contact_data['error'])
						{
						   $result['successMessage']=$contact_data['result']['successMessage'];
						   $response_code='200';
						}
						else
						{
						   $error =  true;
						   $errorText .= $contact_data['errortext'].'<br>';
						}
					}
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	public function type_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
			    $userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->get();
				}
			
			$person_data=$this->Sop_model->get_account_type();
			
			if(!empty($person_data))
			   {
				   $result['account_type_list']=$person_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .=  'No record found'.'<br>';
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
	
	
  public function type_post()
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
		  'is_default'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];
			$insert_response=$this->Sop_model->InsertAccountType($post_data);
			if(!$insert_response['error'])
			{	
			   $result['successMessage']=$insert_response['result']['successMessage'];
			   $result['account_type_list']=$this->Sop_model->get_account_type();
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

	
  public function type_put()
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
		  'type_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
		  'name'=>array('required'=>1,'exp'=>''),
		  'is_default'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
		);
		
		$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
		if(!$check_input['error'])
		{
			$post_data=$check_input['result']['input_array'];
			$insert_response=$this->Sop_model->UpdateAccountType($post_data);
			if(!$insert_response['error'])
			{	
			   $result['successMessage']=$insert_response['result']['successMessage'];
			   $result['account_type_list']=$this->Sop_model->get_account_type();
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

	

	
	public function index_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		$customer_id='';
		try
		{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$input_array=array(
			  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'account_name'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['any']),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['not_zero']),
			  'records'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$customer_id=$post_data['account_id'];
				$account_name = $post_data['account_name'];
				
				if($customer_id=="")
				{
					
					
					
					
					//$customer_data=$this->Sop_model->GetCustomerData($account_name);
						$getAccounts = $this->Account_model->getAccounts($post_data);
						$customer_data['total_records'] = $this->Account_model->getTotalRecords($post_data);;
						$customer_data['records']=count($getAccounts);
						$customer_data['accounts'] = $getAccounts;
						
					
					
				}
				else
				{
					
					$customer_data=$this->Sop_model->GetCustomerDetailsOnId($customer_id);
				}
			
			
				if(!empty($customer_data))
				   {
					   $result=$customer_data;
				   }
			   else
				{
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
				}
				
			}else{
				$error=true;
				$errorText = $check_input['errortext'];
			}
			
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$response_code='400';
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
    
	
	public function profile_get()
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
				
				if($this->Basic_model->role==4)
				{
				  $sub_unique_id=$this->Basic_model->sub_unique_id;
				  $post_data['customer_id']=$sub_unique_id;
				}
				else
				{
					if($post_data['customer_id']=="")
					{
						$error =  true;
				        $errorText .= 'Please enter customer_id as parameter '.'<br>';
					}
				}
			}
			
			if(!$error)
			   {
				   $customer_id=$post_data['customer_id'];
				   $customer_data=$this->Sop_model->GetCustomerDetailsOnId($customer_id);
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
	
	
	
	public function ContactItem($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				
				if(!isset($userdata2['contact_arr']))
				{
					$error =  true;
					$errorText .= 'contact_arr as element'.'<br>';
					$response_code='400';
				}
				
				if(!$error)
				{
					$userdata=$userdata2['contact_arr'];
					$input_array=array(
					 'name'=>array('required'=>1,'exp'=>''),
					 'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
					 'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
					);
				
					$check_object=(array)$userdata;
					if(!empty($check_object)){
					
						foreach($userdata as $item)
						{
							
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
				}
				 
				 if(!$error)
				 {
					 $result['contact_item']=$contact_data_array;
				 }
			}
		 
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return  array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
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
				$userdata =(object)$this->post();
			}
			
			$input_array=array(
			 'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'contact_arr'=>array('required'=>1,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->contact_arr))
			{
			  $userdata2['contact_arr']=$userdata->contact_arr;
			}
			if(!$error)
			{
				$check_input=$this->Rest_model->Check_parameters($input_array,(object)$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					 {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					 }
					 else
					 {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					 }
					 
					 if(!$error)
					 {
						 $item_response=$this->ContactItem($userdata2);
						 if($item_response['error'])
						 {
							 $error=true;
							 $errorText .=implode('<br>',$item_response['errortext']);
						 }
						 else
						 {
							 $post_data+=$item_response['result'];
						 }
					 }
					 if(!$error)
					 {
						$contact_response=$this->Sop_model->InsertContactResponse($post_data);
					    if(!$contact_response['error'])
						   {
							   $result['successMessage']=$contact_response['result']['successMessage'];
							   $data['account_id']=$contact_response['result']['account_id'];
							   $contact_data=$this->Sop_model->ContactDetailsOnId($data);
							   $result['contact_list']=$contact_data['result']['contact_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_response['errortext'].'<br>';
						   }
					 }
					
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
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
					if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
					  }
					if(!$error)
					{
						$contact_data=$this->Sop_model->UpdateContactDetailsOnId($post_data);
						if(!$contact_data['error'])
						{
						   $result['successMessage']=$contact_data['result']['successMessage'];
						   $contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
						   $result['contact_list']=$contact_data['result']['contact_list'];
						   $response_code='200';
						}
						else
						{
						   $error =  true;
						   $errorText .= $contact_data['errortext'].'<br>';
						}
					}
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	
	public function contact_delete()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
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
					 if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					  }
					  
					 if(!$error)
					 {
						  $contact_data=$this->Sop_model->DeleteContactDetailsOnId($post_data);
						  if(!$contact_data['error'])
						   {
							   $result['successMessage']=$contact_data['result']['successMessage'];
							   $contact_data=$this->Sop_model->ContactDetailsOnId($post_data);
							   $result['contact_list']=$contact_data['result']['contact_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_data['errortext'].'<br>';
						   }
					 }
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	
	public function contact_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
			  		$userdata=json_decode(file_get_contents('php://input'));
					if(!is_object($userdata))
					{
						$userdata =(object)$this->get();
					}
					
					$input_array=array(
					  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
					);
					
					$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
					if(!$check_input['error'])
					{
						$post_data=$check_input['result']['input_array'];
						if($this->Basic_model->role==4)
						{
							$sub_unique_id=$this->Basic_model->sub_unique_id;
							$post_data['account_id']=$sub_unique_id;
						}
						else
						{
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
						}
						
						
						if(!$error)
						{
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
					}
					else
					{
						$error =  true;
						$errorText .=  $check_input['errortext'].'<br>';
						$response_code='400';
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
	

	public function index_post()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
			  $userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->post();
				}
				
				$input_array=array(
				  'is_active'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['boolean']),
				  'name'=>array('required'=>1,'exp'=>''),
				  'password'=>array('required'=>0,'exp'=>''),
				  'type_id'=>array('required'=>0,'exp'=>''),// customer priority like gold,silver..etc
				  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
				  'role_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'description'=>array('required'=>0,'exp'=>''),
				);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$token_id=$this->Basic_model->token_id;
				if($token_id!="" && $this->Basic_model->role!=4)
				{
					if($post_data['is_active']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter is_active as parameter'.'<br>';
					}
					
					if($post_data['type_id']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter type_id as parameter'.'<br>';
					}
				}
				else
				{
					$post_data['is_active']=false;
					if($post_data['password']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter password as parameter'.'<br>';
					}
				}
			
			  if(!$error)
			  {
				   $contact_data=$this->Sop_model->InsertCustomerData($post_data);
				   if(!$contact_data['error'])
				   {
					   $result['successMessage']=$contact_data['result']['successMessage'];
					   $customer_id=$contact_data['result']['account_id'];
					   $customer_data=$this->Sop_model->GetCustomerDetailsOnId($customer_id);
					   $result['customer_data']=$customer_data;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $contact_data['errortext'].'<br>';
				   }
			  }
				
			}
			else
			{
				$error =  true;
				$errorText .=  $check_input['errortext'].'<br>';
				$response_code='400';
			}
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext .= $e->getMessage().'<br>';
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
			  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'name'=>array('required'=>1,'exp'=>''),
			  'type_id'=>array('required'=>0,'exp'=>''),
			  'password'=>array('required'=>0,'exp'=>''),
			  'phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			  'role_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				
				$token_id=$this->Basic_model->token_id;
				if($token_id!="" && $this->Basic_model->role!=4)
				{
					if($post_data['account_id']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter account_id as parameter'.'<br>';
					}
					if($post_data['is_active']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter is_active as parameter'.'<br>';
					}
					
					if($post_data['type_id']=="")
					{
						$error =  true;
				   		$errorText .= 'Please enter type_id as parameter'.'<br>';
					}
				}
				else
				{
					$post_data['is_active']=true;
					$post_data['account_id']=$this->Basic_model->sub_unique_id;
				}
				
			   if(!$error)	
				{
				   $contact_data=$this->Sop_model->UpdateCustomerData($post_data);
				   if(!$contact_data['error'])
				   {
					   $result['successMessage']=$contact_data['result']['successMessage'];
					   $customer_id=$post_data['account_id'];
					   $customer_data=$this->Sop_model->GetCustomerDetailsOnId($customer_id);
					   $result['accountList']=$customer_data;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $contact_data['errortext'].'<br>';
				   }
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
	
	
	public function billing_address_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
			  		$userdata=json_decode(file_get_contents('php://input'));
					if(!is_object($userdata))
					{
						$userdata =(object)$this->get();
					}
					
					$input_array=array(
					  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
					);
					
					$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
					if(!$check_input['error'])
					{
						$post_data=$check_input['result']['input_array'];
						if($this->Basic_model->role==4)
						{
							$sub_unique_id=$this->Basic_model->sub_unique_id;
							$post_data['account_id']=$sub_unique_id;
						}
						else
						{
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
						}
						
						
						if(!$error)
						{
							$contact_data=$this->Sop_model->BillingAddressListOnId($post_data);
							if(!$contact_data['error'])
							{
							   $result['billing_address_list']=$contact_data['result']['billing_address_list'];
							   $response_code='200';
							}
							else
							{
							   $error =  true;
							   $errorText .= $contact_data['errortext'].'<br>';
							}
						}
					}
					else
					{
						$error =  true;
						$errorText .=  $check_input['errortext'].'<br>';
						$response_code='400';
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
	
	
	
	public function BillingAddressItem($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				
				if(!isset($userdata2['billing_address_arr']))
				{
					$error =  true;
					$errorText .= 'billing_address_arr as element'.'<br>';
					$response_code='400';
				}
				
				if(!$error)
				{
					$userdata=$userdata2['billing_address_arr'];
					$input_array=array(
					 'address1'=>array('required'=>1,'exp'=>''),
					 'address2'=>array('required'=>1,'exp'=>''),
					 'city'=>array('required'=>1,'exp'=>''),
					 'state'=>array('required'=>1,'exp'=>''),
					 'pin'=>array('required'=>1,'exp'=>''),
					 'phone'=>array('required'=>0,'exp'=>''),
					 'fax'=>array('required'=>0,'exp'=>''),
					);
				
					$check_object=(array)$userdata;
					if(!empty($check_object)){
					
						foreach($userdata as $item)
						{
							
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
				}
				 
				 if(!$error)
				 {
					 $result['billing_address_item']=$contact_data_array;
				 }
			}
		 
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return  array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
	}
	
	
	
	public function billing_address_post()
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
			 'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'billing_address_arr'=>array('required'=>1,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->billing_address_arr))
			{
			  $userdata2['billing_address_arr']=$userdata->billing_address_arr;
			}
			if(!$error)
			{
				$check_input=$this->Rest_model->Check_parameters($input_array,(object)$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					 {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					 }
					 else
					 {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					 }
					 
					 if(!$error)
					 {
						 $item_response=$this->BillingAddressItem($userdata2);
						 if($item_response['error'])
						 {
							 $error=true;
							 $errorText .=implode('<br>',$item_response['errortext']);
						 }
						 else
						 {
							 $post_data+=$item_response['result'];
						 }
					 }
					 if(!$error)
					 {
						 $contact_response=$this->Sop_model->InsertBillingAddress($post_data);
						 if(!$contact_response['error'])
						   {
							   $result['successMessage']=$contact_response['result']['successMessage'];
							   $data['account_id']=$contact_response['result']['account_id'];
							   $billing_address_list=$this->Sop_model->BillingAddressListOnId($data);
							   $result['billing_address_list']=$billing_address_list['result']['billing_address_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_response['errortext'].'<br>';
						   }
					 }
					
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	

	
	public function billing_address_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
		    	$userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->put();
				}
				
				$input_array=array(
				  'billing_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'address1'=>array('required'=>1,'exp'=>''),
				  'address2'=>array('required'=>1,'exp'=>''),
				  'city'=>array('required'=>1,'exp'=>''),
				  'state'=>array('required'=>1,'exp'=>''),
				  'pin'=>array('required'=>1,'exp'=>''),
				  'phone'=>array('required'=>0,'exp'=>''),
				  'fax'=>array('required'=>0,'exp'=>''),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
					  }
					if(!$error)
					{
						$contact_data=$this->Sop_model->UpdateBillingAddressDetailsOnId($post_data);
						if(!$contact_data['error'])
						{
						   $result['successMessage']=$contact_data['result']['successMessage'];
						   $billing_address_data=$this->Sop_model->BillingAddressListOnId($post_data);
						   $result['billing_address_list']=$billing_address_data['result']['billing_address_list'];
						   $response_code='200';
						}
						else
						{
						   $error =  true;
						   $errorText .= $contact_data['errortext'].'<br>';
						}
					}
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	
	public function billing_address_delete()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
			    $userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->delete();
				}
				
				$input_array=array(
				  'billing_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					  }
					  
					 if(!$error)
					 {
						  $contact_data=$this->Sop_model->DeleteBillingAddressDetailsOnId($post_data);
						  if(!$contact_data['error'])
						   {
							   $result['successMessage']=$contact_data['result']['successMessage'];
							   $billing_address_data=$this->Sop_model->BillingAddressListOnId($post_data);
						  	   $result['billing_address_list']=$billing_address_data['result']['billing_address_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_data['errortext'].'<br>';
						   }
					 }
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	
	public function shipping_address_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
			  		$userdata=json_decode(file_get_contents('php://input'));
					if(!is_object($userdata))
					{
						$userdata =(object)$this->get();
					}
					
					$input_array=array(
					  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
					);
					
					$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
					if(!$check_input['error'])
					{
						$post_data=$check_input['result']['input_array'];
						if($this->Basic_model->role==4)
						{
							$sub_unique_id=$this->Basic_model->sub_unique_id;
							$post_data['account_id']=$sub_unique_id;
						}
						else
						{
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
						}
						
						
						if(!$error)
						{
							$contact_data=$this->Sop_model->ShippingAddressListOnId($post_data);
							if(!$contact_data['error'])
							{
							   $result['shipping_address_list']=$contact_data['result']['shipping_address_list'];
							   $response_code='200';
							}
							else
							{
							   $error =  true;
							   $errorText .= $contact_data['errortext'].'<br>';
							}
						}
					}
					else
					{
						$error =  true;
						$errorText .=  $check_input['errortext'].'<br>';
						$response_code='400';
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
	
	
	public function ShippingAddressItem($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				
				if(!isset($userdata2['shipping_address_arr']))
				{
					$error =  true;
					$errorText .= 'shipping_address_arr as element'.'<br>';
					$response_code='400';
				}
				
				if(!$error)
				{
					$userdata=$userdata2['shipping_address_arr'];
					$input_array=array(
					 'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
					 'address_name'=>array('required'=>0,'exp'=>''),
					 'address1'=>array('required'=>1,'exp'=>''),
					 'address2'=>array('required'=>1,'exp'=>''),
					 'city'=>array('required'=>1,'exp'=>''),
					 'state'=>array('required'=>1,'exp'=>''),
					 'pin'=>array('required'=>1,'exp'=>''),
					 'phone'=>array('required'=>0,'exp'=>''),
					 'fax'=>array('required'=>0,'exp'=>''),
					);
				
					$check_object=(array)$userdata;
					if(!empty($check_object)){
					
						foreach($userdata as $item)
						{
							
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
				}
				 
				 if(!$error)
				 {
					 $result['shipping_address_item']=$contact_data_array;
				 }
			}
		 
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return  array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
	}
	
	
	
	
	public function shipping_address_post()
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
			 'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'shipping_address_arr'=>array('required'=>1,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->shipping_address_arr))
			{
			  $userdata2['shipping_address_arr']=$userdata->shipping_address_arr;
			}
			if(!$error)
			{
				$check_input=$this->Rest_model->Check_parameters($input_array,(object)$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					 {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					 }
					 else
					 {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					 }
					 
					 if(!$error)
					 {
						 $item_response=$this->ShippingAddressItem($userdata2);
						 if($item_response['error'])
						 {
							 $error=true;
							 $errorText .=implode('<br>',$item_response['errortext']);
						 }
						 else
						 {
							 $post_data+=$item_response['result'];
						 }
					 }
					 if(!$error)
					 {
						 $contact_response=$this->Sop_model->InsertShippingAddress($post_data);
						 if(!$contact_response['error'])
						   {
							   $result['successMessage']=$contact_response['result']['successMessage'];
							   $data['account_id']=$contact_response['result']['account_id'];
							   $billing_address_list=$this->Sop_model->ShippingAddressListOnId($data);
							   $result['shipping_address_list']=$billing_address_list['result']['shipping_address_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_response['errortext'].'<br>';
						   }
					 }
					
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	
	public function shipping_address_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
		    	$userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->put();
				}
				
				$input_array=array(
				  'shipping_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'address_name'=>array('required'=>0,'exp'=>''),
				  'address1'=>array('required'=>1,'exp'=>''),
				  'address2'=>array('required'=>1,'exp'=>''),
				  'city'=>array('required'=>1,'exp'=>''),
				  'state'=>array('required'=>1,'exp'=>''),
				  'pin'=>array('required'=>1,'exp'=>''),
				  'phone'=>array('required'=>0,'exp'=>''),
				  'fax'=>array('required'=>0,'exp'=>''),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
							if($post_data['account_id']=="")
							{
								$error=true;
								$errorText='Please enter account_id as parameter';
							}
					  }
					if(!$error)
					{
						$contact_data=$this->Sop_model->UpdateShippingAddressDetailsOnId($post_data);
						if(!$contact_data['error'])
						{
						   $result['successMessage']=$contact_data['result']['successMessage'];
						   $billing_address_data=$this->Sop_model->ShippingAddressListOnId($post_data);
						   $result['shipping_address_list']=$billing_address_data['result']['shipping_address_list'];
						   $response_code='200';
						}
						else
						{
						   $error =  true;
						   $errorText .= $contact_data['errortext'].'<br>';
						}
					}
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
	
	public function shipping_address_delete()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
			    $userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->delete();
				}
				
				$input_array=array(
				  'shipping_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'account_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					  {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$post_data['account_id']=$sub_unique_id;
					  }
					  else
					   {
						if($post_data['account_id']=="")
						{
							$error=true;
							$errorText='Please enter account_id as parameter';
						}
					  }
					  
					 if(!$error)
					 {
						  $contact_data=$this->Sop_model->DeleteBillingAddressDetailsOnId($post_data);
						  if(!$contact_data['error'])
						   {
							   $result['successMessage']=$contact_data['result']['successMessage'];
							   $billing_address_data=$this->Sop_model->ShippingAddressListOnId($post_data);
						  	   $result['shipping_address_list']=$billing_address_data['result']['shipping_address_list'];
							   $response_code='200';
						   }
						   else
						   {
							   $error =  true;
							   $errorText .= $contact_data['errortext'].'<br>';
						   }
					 }
				
				}
				else
				{
					$error =  true;
					$errorText .=  $check_input['errortext'].'<br>';
					$response_code='400';
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
}
