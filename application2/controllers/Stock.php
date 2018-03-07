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
	
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'get_method_list'=>array('GET'=>array("a")),
		);
		
    }
	
	public function get_method_list_get()
	{
		//$parames=$this->session->flashdata('parames');
		//$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		//$data['fetch_class_name']='stock';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','stock');
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
