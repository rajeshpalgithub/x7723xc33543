<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
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
			  'unique_text'=>array('required'=>1,'exp'=>''),
			  'password'=>array('required'=>1,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$login_data=$this->Login_model->GetUserLogin($post_data);
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
	
	public function forgot_password_post()
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
			  'email'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['email']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$login_data=$this->Login_model->SendUpdatePassword($post_data);
				if(!$login_data['error'])
				   {
					   $result['successMessage']=$login_data['result']['successMessage'];
					   $result['new_pass']=$login_data['result']['new_pass'];
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .=  $login_data['errortext'].'<br>';
					   $result['new_pass']=$login_data['result']['new_pass'];
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
