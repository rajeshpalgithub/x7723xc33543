<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Charges extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'get_method_list'=>array('GET'=>array("a")),
		);	
	
	
		
    }
	
	public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='charges';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','charges');
		if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
		}
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
			  'lable_of_charge'=>array('required'=>1,'exp'=>''),
			  'charges'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['decimal']),
			  'is_percent'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$charges_data=$this->Sop_model->InsertChargesData($post_data);
				if(!$charges_data['error'])
				 {
					 $result['successMessage']=$charges_data['result']['successMessage'];
					 $response_code='200';
				 }
				 else
				 {
					 $error =  true;
				     $errorText .= $charges_data['errortext'].'<br>';
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
