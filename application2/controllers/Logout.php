<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Logout extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		//$this->load->model('Common_model');
		$this->load->model('Login_model');
		//$this->load->library('cart');
		
	  /* $authentication=$this->Basic_model->session_exp();
	  
	   if($authentication['error'])
	   {
		  //$login_data=$this->Login_model->logout_user(); 
		  $errortext=$authentication['errortext'];
		  $this->response($errortext);
	   }
	   else
	   {
		   $authentication=$this->Basic_model->authentication('School');
		   if($authentication['error'])
		   {
			  $login_data=$this->Login_model->logout_user(); 
			  $errortext=$authentication['errortext'];
			  $this->response($errortext);
		   }
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
			
			$login_data=$this->Login_model->logout_user();
			
			
			if(!$login_data['error'])
			   {
				   $result=$login_data['result'];
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .=  $login_data['errortext'].'<br>';
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
