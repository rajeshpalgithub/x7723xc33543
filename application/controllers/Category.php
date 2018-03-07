<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Category extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Sop_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'tree'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'get_method_list'=>array('GET'=>array("a")),
		);		
    }
	
	public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='category';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','category');
		if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
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
			
			$input_array=array(
			  'category_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'search_cat'=>array('required'=>0,'exp'=>''),
			  'search_text'=>array('required'=>0,'exp'=>''),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'row'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$category_id=$post_data['category_id'];
				$page=$post_data['page'];
				$row=$post_data['row'];
				
				if($page < 0)
				{
					$error=true;
					$errorText='Page number must be greater than zero';
				}
				
				if($post_data['search_cat']!="")
				{
					if($post_data['search_text']=="")
					{
						$error=true;
					    $errorText='Search text required';
					}
				}
				
				if($page=="")
				{
					$page=1;
				}
				
				if($row=="")
				{
					$row=20;
				}
				
				$per_page=$row;
                $offset = (($page - 1) * $row);
				
			}
			
			if(!$error){
			
			if($category_id=="")
			{
			    $category_data=$this->Sop_model->GetCategoryData($offset,$per_page);
				$result['records']=count($category_data);
			}
			else
			{
				$category_data=$this->Sop_model->GetProductCategoryById($category_id);
				$result['records']=1;
			}
			
			
			if(!empty($category_data))
			   {
				   $result['page']=$page;
				   $result['category_details']=$category_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= 'No record found'.'<br>';
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
	
	
	public function tree_get()
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
			
		
			
			$category_data=$this->Sop_model->GetProductCategoryTree();
			if(!empty($category_data))
			   {
				   $result['category_list']=$category_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .= 'No record found'.'<br>';
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
			  'category_name'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'parent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertCategoryData($post_data);
			
			   if($insert_response['error'])
			   {
				    $error=true;
					$errorText=$insert_response['errortext'];
			   }
			   else
			   {
				  $result['successMessage']='Data successfully inserted';
				  $response_code='200';
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
			  'cat_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'category_name'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'parent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->UpdateCategoryData($post_data);
			
			   if($insert_response['error'])
			   {
				    $error=true;
					$errorText=$insert_response['errortext'];
			   }
			   else
			   {
				  $result['successMessage']='Data successfully updated';
				  $response_code='200';
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
