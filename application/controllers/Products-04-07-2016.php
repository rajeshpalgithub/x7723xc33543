<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Products extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('Sop_model');
		
		
		$error=false;
		$response_code='';
		$errortext='';
		
		
		$role_permission_array['sub_unique_id']=$this->Basic_model->sub_unique_id;
		$role_permission_array['unique_id']=$this->Basic_model->unique_id;
		$role_permission_array['role']=$this->Basic_model->role;
		$result=$this->Common_model->get_role_permission($role_permission_array);
		$response=$this->Common_model->permission_url($role_permission_array,$result,$this->uri->uri_string(),$this->input->method(TRUE));
		if(!$response)
		{
			$error=true;
			$response_code='401';
			$errortext='Unauthorised access';
		}
		
		if($error)
		{
			$this->response(array('error'=>$error,'errortext'=>$errortext), $response_code);
		}

	}

	public function search_get()
	{
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
			  'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'search_cat'=>array('required'=>0,'exp'=>''),
			  'search_text'=>array('required'=>0,'exp'=>''),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'row'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$product_id=$post_data['product_id'];
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
			$key='';
			if($product_id=="")
			{
			    $product_data=$this->Sop_model->GetProductData($offset,$per_page);
				$result['records']=count($product_data);
				$key='product_list';
			}
			else
			{
				$product_data=$this->Sop_model->GetProductDetailsOnId($product_id);
				$result['records']=1;
				$key='product_details';
			}
			
			
			if(!empty($product_data))
			   {
				   $result['page']=$page;
				   $result[$key]=$product_data;
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
			  'category_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'product_name'=>array('required'=>1,'exp'=>''),
			  'product_nubmer'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['alpha_or_numeric']),
			  'sku_nubmer'=>array('required'=>1,'exp'=>''),
			  'price'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['decimal']),
			  'description'=>array('required'=>0,'exp'=>''),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'image_path'=>array('required'=>0,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertProductData($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $product_data=$this->Sop_model->GetProductData(20,0);
				   $result['successMessage']='Data successfully inserted';
				   $result['product_data']=$product_data;
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
			  'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'category_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'product_name'=>array('required'=>1,'exp'=>''),
			  'product_nubmer'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['alpha_or_numeric']),
			  'sku_nubmer'=>array('required'=>1,'exp'=>''),
			  'description'=>array('required'=>0,'exp'=>''),
			  'price'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['decimal']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'image_path'=>array('required'=>0,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->UpdateProductData($product_id,$post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $product_data=$this->Sop_model->GetProductData(20,0);
				   $result['successMessage']='Data successfully updated';
				   $result['product_data']=$product_data;
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
