<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Menu extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		//$this->load->model('Login_model');
		//$this->load->model('School_role_model');
		
	   /* $error=false;
		$response_code='';
		$errortext='';
	
	
		$sub_unique_id=$this->Basic_model->sub_unique_id;
		$unique_id=$this->Basic_model->unique_id;
		$role=$this->Basic_model->role;
		
		if($unique_id=="")
		{
			$error=true;
			$response_code='401';
			$errortext='Unauthorised access';
		}
		
		if($error)
		{
			$this->response(array('error'=>$error,'errortext'=>$errortext), $response_code);
		}*/

		$this->check_permission=array(
			'get_method_list'=>array('GET'=>array("a")),
			'index'=>array('GET'=>array(3,4)),
		);

    }

	function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','menu');
		$this->response($data);
		
	}

	public function index_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		$parent_id = 0;
		$module_name='';
		$menu_type='';
		try
		{
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			$input_array=array(
			  'parent_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),	// parent id
			  'module_name'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['name']),	// module name
			  'menu_type'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['name']), // menu type (e.g:sub , main.... etc)
			 
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				
			if(!$check_input['error'])
			{
				
				
				$post_data=$check_input['result']['input_array'];
				$parent_id=$post_data['parent_id'];
				$module_name=$post_data['module_name'];
				$menu_type=$post_data['menu_type'];
				
			}else{
				$error=true;
				$errorText=$check_input['errortext'];
				
			}
			
			if(!$error)
			{
				if($this->Basic_model->sub_unique_id!=0)
				{
					//it is for client role(e.g: users & customer ) APIs
					$permission_list=$this->Common_model->get_role_permission($parent_id,$module_name); 
					
					if(!$permission_list['error'])
					   {
						   $result['menu_link']=$permission_list['result']['menu_link'];
						   $response_code='200';
					   }
					   else
					   {
						   $error =  true;
						   $errorText .= $permission_list['errortext'].'<br>';
					   }
				}
				else
				{
					$permission_list=$this->Basic_model->GetClientPermissionClass($parent_id,$module_name,$menu_type); // client admin APIs
					if(!empty($permission_list))
				    {
					   $result['menu_link']=$permission_list;
					   $response_code='200';
				    }
				    else
				    {
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
				    }
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
	
	
}
