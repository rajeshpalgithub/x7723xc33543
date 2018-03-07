<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class School_classes extends REST_Controller {

	function __construct() {
		
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		//$this->load->model('Sop_model');
		//$this->load->model('Login_model');
		$this->load->model('School_role_model','',TRUE);

		// school modle
		$this->load->model('School_class_model');

		$this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)), // 3= client role and Admin acn access it
			'get_method_list'=>array('GET'=>array("a")), // a= skip 
			
		);
	}

	/////////////////////// ////////////////////////////////////////////////////////////
	public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='account';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','account');
		if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
		}
	}
	//////////////////////////////////////////////////////////////////////////

	public function index_get() 
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '200';
		try{
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			$class_data=$this->School_class_model->getAllClass();
			
			if(!empty($class_data))
			{
				   $result['classes']=$class_data;
				   
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
	public function index_put()
	{

	}
	public function index_delete()
	{

	}
	public function index_post()
	{

	}

}
?>