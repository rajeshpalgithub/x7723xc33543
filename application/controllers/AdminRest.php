<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
class AdminRest extends REST_Controller {
	
	function __construct() {
		
        parent::__construct();
		
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		/////////////////////////////
		$this->load->model('AdminRest_model','',true);
		
		$this->check_permission=array(
			'classList'=>array('GET'=>array(1)), // 1= admin
			
		);
	}
	function classList_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '200';
		try
		{
			/*$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}*/
			
			$class_list_data=$this->AdminRest_model->class_list();
			
			if(!empty($class_list_data))
			   {
				   $result['class']=$class_list_data;
				   
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
			$response_code = '500';
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
}
?>