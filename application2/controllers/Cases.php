<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Cases extends REST_Controller {
	function __construct() {
		
        parent::__construct();
		
		$this->load->model('Basic_model');
		$this->load->model('Rest_model');
		$this->load->model('Case_model');
		
		
		$this->check_permission=array(
			'get_method_list'=>array('GET'=>array("a")),
			'index'=>array('GET'=>array(3),'POST'=>array(3,4),'PUT'=>array(3),'DELETE'=>array(3)),
		);
	}
	function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','cases');
		$this->response($data);
		
	}
	function index_get()
	{
		/* 	@method : {index_get}
			@details : {this method return all cases of a client, it takes parameters for shorting}
			@Input:{ 
				@account_id:{for listing all case againest a customer(account_id). #required:no}
				@page:{for pagination #required:no}
				@records:{for nuber of record #required:no}
			}
			
		*/
		$error =  false;
		$errortext = '';
		$result = array();
		$response_code = 200;
		$out_json=array(
			'cases'=>'cases',
			'records'=>'records',
			'total_records'=>'total_records',
		);
		try{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$input_array=array(
			  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'case_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'search_text'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['any']),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['not_zero']),
			  'records'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$query=$check_input['result']['input_array'];
				
			}else{
				$error = true;
				$errortext .= $check_input['errortext'];
			}
			if(!$error )
			{
				if(empty($query['case_id'])){
					//all case records
					$cases = $this->Case_model->getCases($query);
					$result[$out_json['cases']]=$cases;
					$result[$out_json['records']]=count($cases);
					$result[$out_json['total_records']]=100;
				}else{
					// get case details
					$result=$this->Case_model->caseDetails($query);
				}
				
			}
			
		}catch(Exception $e)
		{
			$error = true;
			$errortext .= $e->getMessage();
			$response_code = 400;
		}
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errortext,"<br>")),'result'=>$result),$response_code);
			
			
	}
	
	function index_post()
	{
		/* insert new case */
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = 200;
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText .=$authentication['errortext'].'<br>';
			  $response_code=401;
			}
			if(!$error)
			{
			  	$userdata=json_decode(file_get_contents('php://input'));
				if(!is_object($userdata))
				{
					$userdata =(object)$this->post();
				}
				$input_array=array(
				  'subject'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['varchar250']),
				  'parent_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'details'=>array('required'=>0,'exp'=>''),
				  'status'=>array('required'=>0,'exp'=>''),// 'New', 'Working', 'Escalated'
				  'priority'=>array('required'=>0,'exp'=>''),//'High', 'Medium', 'Low'
				  'account_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'contact_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'category_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'reason_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'internal_comments'=>array('required'=>0,'exp'=>''),
				  'asset_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'assignment_rule_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'is_send_notification_email'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				  
				);
			
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					// this portion is similar to controller account : index_post()
					
				}else{
					$error=true;
			  		$errorText .= $check_input['errortext'].'<br>';
				}
				
			}
			
		}catch(Exception $e){
			$error =  true;
			$errorText  .= $e->getMessage().'<br>';
			$response_code = 400;
			
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
}
?>