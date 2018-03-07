<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Cart extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('Sop_model');
		$this->load->library('cart');
		
		$this->check_permission=array(
		'index'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
		'get_method_list'=>array('GET'=>array("a")),
		'qty'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
		'date'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
		'status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
		'submit'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
		);

		/*$fetch_method=$this->router->fetch_method();
		if($fetch_method!="get_method_list")
		{ 
			if($this->Basic_model->token_id=="")
			{
				$error =  false;
				$errorText = '';
				$response_code='';
			
				$headers=getallheaders();
				if(!isset($headers['Session']))
				{
				   $error=true;
				   $errorText='Session id required';
				   $response_code='401';
				   $this->response( array('error'=>$error,'errortext'=>$errorText),$response_code);
				}
			}
		 }*/
		

    }
	
	public function get_method_list_get()
	{
		//$parames=$this->session->flashdata('parames');
		//$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		//$data['fetch_class_name']='cart';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','cart');
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
	
	/* public function test_get()
	{
		$cart_data=$this->Sop_model->send_order_email(3,5);
		print_r($cart_data);
		die();
	} */
	
	public function index_get()
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
			
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
				$cart_data=$this->Sop_model->GetCartDetails();
				
				if(!empty($cart_data))
				   {
					   $result['cart_data']=$cart_data;
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
			  'product_sku'=>array('required'=>1,'exp'=>''),
			  'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['alpha_or_numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertDataToCart($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully inserted';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function qty_put()
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
			  'product_sku'=>array('required'=>1,'exp'=>''),
			  'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->update_cart_qty($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully updated';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function date_put()
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
			  'start_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			  'end_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			);
			
		
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->update_cart_price_by_date($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully updated';
				   $result['cart_data']=$cart_data;
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
			  'product_sku'=>array('required'=>1,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->delete_cart_item($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully deleted';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function status_get()
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
				$order_status=$this->Sop_model->GetOrderStatus();
				
				if(!empty($order_status))
				   {
					   $result['order_status']=$order_status;
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
	
	public function submit_post()
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
			  'customer_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'order_status_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'note'=>array('required'=>0,'exp'=>''),
			  'purchase_order_number'=>array('required'=>0,'exp'=>''),
			  'billing_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'shipping_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				
				if($this->Basic_model->role==4)
				{
					$sub_unique_id=$this->Basic_model->sub_unique_id;
					$post_data['order_status_id']=$this->Sop_model->GetEmployeeStatus($sub_unique_id);
					$post_data['customer_id']=$sub_unique_id;
				}
				else
				{
					if($post_data['order_status_id']=="")
					{
						$error=true;
						$errorText='Please enter order_status_id as parameter';
					}
					
					if($post_data['customer_id']=="")
					{
						$error=true;
						$errorText='Please enter customer_id as parameter';
					}
				}
				
				if(!$error)
				{
					$insert_response=$this->Sop_model->SubmitProductOrder($post_data);
					if($insert_response['error'])
					 {
						$error=true;
						$errorText=$insert_response['errortext'];
					 }
					 else
					 {
					   $invoice_id=$insert_response['result']['invoice_id'];
					   $cart_data=$this->Sop_model->GetProductOrderDetailsOnId($invoice_id);
					   $result['successMessage']='Cart successfully submitted';
					   $result['cart_data']=$cart_data;
					   $response_code='200';
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
	
}
