<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Stock extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('Sop_model');
	
	
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
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$stock_data=$this->Sop_model->GetStockData();
			if(!empty($stock_data))
			   {
				   $result=$stock_data;
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
			  'sku'=>array('required'=>1,'exp'=>''),
			  'avl_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'alert_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertStockData($post_data);
			    if(!$insert_response['error'])
				{
				   $stock_data=$this->Sop_model->GetStockData();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['stock_list']=$stock_data;
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
			  'stock_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'avl_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'alert_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$update_response=$this->Sop_model->UpdateStockData($post_data);
			    if(!$update_response['error'])
				{
				   $stock_data=$this->Sop_model->GetStockData();	
				   $result['successMessage']=$update_response['result']['successMessage'];
				   $result['stock_list']=$stock_data;
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
	
	
	
	
}
