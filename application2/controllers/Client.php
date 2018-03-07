<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Client extends REST_Controller {
	
	function __construct() {
		
        parent::__construct();
		$this->load->model('Basic_model');
		$this->load->model('Rest_model');
		$this->load->model('Client_model');
		
		$this->check_permission=array(
			'get_method_list'=>array('GET'=>array("a")),
			'index'=>array('POST'=>array(0)),
		);
		
	}
	function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','client');
		$this->response($data);
		
	}
	function index_post()
	{
		$error =  false;
		$errortext = '';
		$result = array();
		$response_code = 200;
		/*$out_json=array(
			'cases'=>'cases',
			'records'=>'records',
			'total_records'=>'total_records',
		);*/
		try{
			
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->post();
			}
			
			$input_array=array(
			  'business_name'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['any']),
			  'business_short_name'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['any']),
			  'user_name'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['any']),
			  'user_email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			  'user_phone'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['phone']),
			  'client_address'=>array('required'=>0,'exp'=>''),
			  'client_city'=>array('required'=>0,'exp'=>''),
			  'client_state'=>array('required'=>0,'exp'=>''),
			  'client_country_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'client_vendor_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'client_time_zone'=>array('required'=>0,'exp'=>''),
			  
			  
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$parameters=$check_input['result']['input_array'];
				$client_add_result=$this->Client_model->add_client($parameters);
				if($client_add_result['error'])
				{
					$error = true;
					$errortext .= $client_add_result['errortext'];
				}else{
					$result = $client_add_result['result'];
				}
				
			}else{
				$error=true;
				$errortext .= $check_input['errortext'].'<br>';
			}
			
		}catch(Exception $e)
		{
			$error = true;
			$errortext .= $e->getMessage();
			$response_code = 400;
		}
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errortext,"<br>")),'result'=>$result),$response_code);
		
	}
	
	
}?>