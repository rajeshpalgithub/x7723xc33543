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
    
	
	public function type_get()
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
			
			$product_data=$this->Sop_model->GetUserProductType();
			$result=$product_data;
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
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
			  'product_type'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['product_type']),
			  'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'name'=>array('required'=>0,'exp'=>''),
			  'sku'=>array('required'=>0,'exp'=>''),
			  'cat_id'=>array('required'=>0,'exp'=>''),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'row'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$product_id=$post_data['product_id'];
				$product_type=$post_data['product_type'];
				$page=$post_data['page'];
				$row=$post_data['row'];
				
				if($page < 0)
				{
					$error=true;
					$errorText='Page number must be greater than zero';
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
			
			if($product_type!="")
			{
				$product_data=$this->Sop_model->GetProductOnCatIdAndType($post_data);
				$result['records']=count($product_data);
				$key='product_list';
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
	
	public function price_details($product_type)
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
			
			$product_price_type=array();
			if($product_type=='1')
			{
				$product_price_type=$this->Sop_model->GetProductPriceType();
				foreach($product_price_type as $item)
				{
					$input_array[$item['slab_name']]['required']=1;
					//$input_array['rent_slab_id']=$item['id'];
					$input_array[$item['slab_name']]['exp']=$this->Basic_model->regularexp['decimal'];
				}
			}
			if($product_type=='2')
			{
				$input_array=array(
				  'price'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['decimal']),
				);
			}
		
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$rent_slab_id=array();
				$price_data=$check_input['result']['input_array'];
				
				if(!empty($product_price_type))
				{
					foreach($product_price_type as $item)
					{
						$rent_slab_id['rent_slab_id'][]=$item['id'];
					}
					$post_data=array_combine($rent_slab_id['rent_slab_id'],$price_data);
					$result['post_data']=$post_data;
				}
				else
				{
					$result['post_data']=$price_data['price'];
				}
				
				
			}
			else
			{
				 $error=true;
				 $errorText=$check_input['errortext'];
			}
	
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
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
			  'product_type'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['order_type']),
			  'category_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'product_name'=>array('required'=>1,'exp'=>''),
			  'product_nubmer'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['alpha_or_numeric']),
			  'sku_nubmer'=>array('required'=>1,'exp'=>''),
			  'description'=>array('required'=>0,'exp'=>''),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'image_path'=>array('required'=>0,'exp'=>''),
			);
		
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$product_type=$this->Sop_model->GetProductType();
				if($product_type==3)
				{
					if($post_data['product_type']=="")
					{
					  $error=true;
					  $errorText='Please enter product_type as parameter';
					}
					else
					{
						$input_product_type=$post_data['product_type'];
					}
				}
				else
				{
					$input_product_type=$product_type;
					$post_data['product_type']=$product_type;
				}
				
				if(!$error)
				{
				  $price_details=$this->price_details($input_product_type);
				  
				  if(!$price_details['error'])
				  {
					  $post_data['price_data']=$price_details['result']['post_data'];
				  }
				  else
				  {
					  $error=true;
					  $price_details_error=$price_details['errortext'];
					  $errorText=implode("<br>",$price_details_error);
				  }
				}
				
				
				if(!$error)
				{
					
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
			  'product_type'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['order_type']),
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
				
				$product_type=$this->Sop_model->GetProductType();
				if($product_type==3)
				{
					if($post_data['product_type']=="")
					{
					  $error=true;
					  $errorText='Please enter product_type as parameter';
					}
					else
					{
						$input_product_type=$post_data['product_type'];
					}
				}
				else
				{
					$input_product_type=$product_type;
					$post_data['product_type']=$product_type;
				}
				
				if(!$error)
				{
				  $price_details=$this->price_details($input_product_type);
				  
				  if(!$price_details['error'])
				  {
					  $post_data['price_data']=$price_details['result']['post_data'];
				  }
				  else
				  {
					  $error=true;
					  $price_details_error=$price_details['errortext'];
					  $errorText=implode("<br>",$price_details_error);
				  }
				}
				
				
				if(!$error)
				{
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
	
	
	public function price_slab_get()
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
			
			$product_type=$this->Sop_model->GetProductType();
			if($product_type==1 || $product_type==3)
			{
				$input_array=array(
				  'slab_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					$slab_id=$post_data['slab_id'];
				}
				
				if(!$error){
				$key='';
				if($slab_id=="")
				{
					$slab_data=$this->Sop_model->GetProductRentSlab();
					$key='Rent_slab_list';
				}
				else
				{
					$slab_data=$this->Sop_model->GetProductRentSlabOnId($slab_id);
					$key='Rent_slab_details';
				}
				
				
				if(!empty($slab_data))
				   {
					   $result[$key]=$slab_data;
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= 'No record found'.'<br>';
				   }
				}
			
			}
			else
			{
				$error=true;
				$errorText='Not authorized to access';
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errorText = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function price_slab_post()
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
			
			$product_type=$this->Sop_model->GetProductType();
			if($product_type==1 || $product_type==3)
			{
				$input_array=array(
				  'slab_name'=>array('required'=>1,'exp'=>''),
				  'min_day'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'max_day'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'is_default'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
				  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean'])
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					
					$insert_response=$this->Sop_model->InsertProductRentSlab($post_data);
					if($insert_response['error'])
					 {
						$error=true;
						$errorText=$insert_response['errortext'];
					 }
					 else
					 {
					   $product_data=$this->Sop_model->GetProductRentSlab();
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
			else
			{
				$error=true;
				$errorText='Not authorized to access';
			}
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function price_slab_put()
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
			
			$product_type=$this->Sop_model->GetProductType();
			if($product_type==1 || $product_type==3)
			{
				$input_array=array(
				  'slab_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'slab_name'=>array('required'=>1,'exp'=>''),
				  'min_day'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'max_day'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				  'is_default'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
				  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean'])
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					
					$insert_response=$this->Sop_model->UpdateProductRentSlab($post_data);
					if($insert_response['error'])
					 {
						$error=true;
						$errorText=$insert_response['errortext'];
					 }
					 else
					 {
					   $product_data=$this->Sop_model->GetProductRentSlab();
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
			else
			{
				$error=true;
				$errorText='Not authorized to access';
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
