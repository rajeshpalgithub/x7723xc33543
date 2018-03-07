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
			  'customer_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'order_status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'note'=>array('required'=>1,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
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
