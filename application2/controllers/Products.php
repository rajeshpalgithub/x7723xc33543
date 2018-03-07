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
		
		
		$this->check_permission=array(
			'index'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'cart'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
			'category'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'stock'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'category_tree'=>array('GET'=>array(3)),
			'get_method_list'=>array('GET'=>array("a")),
			'type'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'price_slab'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'cart_qty'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
			'cart_date'=>array('GET'=>array(0,3,4),'POST'=>array(0,3,4),'PUT'=>array(0,3,4),'DELETE'=>array(0,3,4)),
			'cart_status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'cart_submit'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
		);
	}
    
	public function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','products');
		$this->response($data);
		
	}
	
	
	public function category_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			///////////////////////////////////////
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			////////////////////////
			if(!$error){
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function category_tree_get()
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

	public function category_post()
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
			  'category_name'=>array('required'=>1,'exp'=>''),
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
	
	public function category_put()
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
			  'category_name'=>array('required'=>1,'exp'=>''),
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
	
	
	public function MultipleImage($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				if(isset($userdata2['image']))
				{
					$userdata=$userdata2['image'];
				
					$input_array=array(
					 'encode_image'=>array('required'=>1,'exp'=>''),
					);
				
					if(!$error)
					{
						$check_object=(array)$userdata;
						if(!empty($check_object)){
						
							foreach($userdata as $item)
							{
								
								$check_input = $this->Rest_model->Check_parameters($input_array,$item);		
								if($check_input['error'])
								{
									$error =  true;
									$errorText .=  $check_input['errortext'].'<br>';
									$response_code='400';
									break;
								}
								else
								{
									$contact_data_array[] =$check_input['result']['input_array'];
									
								}
							}
						
						}
						else
						{
							$check_input = $this->Rest_model->Check_parameters($input_array,$userdata);		
							if($check_input['error'])
							{
								$error =  true;
								$errorText .=  $check_input['errortext'].'<br>';
								$response_code='400';
								
							}
							
						 }
					}
					 
					 if(!$error)
					 {
						 $result['image']=$contact_data_array;
					 }
				 
				}
				else
				{
					$error =  true;
					$errorText .=  'Please enter image as element'.'<br>';
					$response_code='400';

				}
			}
		 
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return  array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
	}
	
	
	public function MultipleImageName($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				if(isset($userdata2['image_name']))
				{
					$userdata=$userdata2['image_name'];
				
					$input_array=array(
					 'image_name'=>array('required'=>1,'exp'=>''),
					);
				
					if(!$error)
					{
						$check_object=(array)$userdata;
						if(!empty($check_object)){
						
							foreach($userdata as $item)
							{
								
								$check_input = $this->Rest_model->Check_parameters($input_array,$item);		
								if($check_input['error'])
								{
									$error =  true;
									$errorText .=  $check_input['errortext'].'<br>';
									$response_code='400';
									break;
								}
								else
								{
									$contact_data_array[] =$check_input['result']['input_array'];
									
								}
							}
						
						}
						else
						{
							$check_input = $this->Rest_model->Check_parameters($input_array,$userdata);		
							if($check_input['error'])
							{
								$error =  true;
								$errorText .=  $check_input['errortext'].'<br>';
								$response_code='400';
								
							}
							
						 }
					}
					 
					 if(!$error)
					 {
						 $result['image_name']=$contact_data_array;
					 }
				 
				}
				else
				{
					$error =  true;
					$errorText .=  'Please enter image_name as element'.'<br>';
					$response_code='400';

				}
			}
		 
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		return  array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result);
	}
	
	public function image_get()
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
			  'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$image_data=$this->Sop_model->GetProductImageName($post_data);
				
				if(!$image_data['error'])
				   {
					   $result['successMessage']=$image_data['result']['image_list'];
					   //$result['upload_image']=$image_data['result']['upload_image'];
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $image_data['errortext'].'<br>';
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
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function image_delete()
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
				$userdata =(object)$this->delete();
			}
			
			$input_array=array(
			  'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$image_data=$this->Sop_model->DeleteProductImageName($post_data);
				
				if(!$image_data['error'])
				   {
					   $result['successMessage']=$image_data['result']['image_list'];
					   //$result['upload_image']=$image_data['result']['upload_image'];
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $image_data['errortext'].'<br>';
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
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	
	public function image_post()
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
			  'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'image_name'=>array('required'=>0,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->image))
			{
			  $userdata2['image_name']=$userdata->image;
			}
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data2=$check_input['result']['input_array'];
				$image_response=$this->MultipleImageName($userdata2);
				
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data=$image_response['result'];
				 }
				 $post_data['product_id']=$post_data2['product_id'];
				
				$image_data=$this->Sop_model->UploadProductImageName($post_data);
				if(!$image_data['error'])
				   {
					   $result['successMessage']=$image_data['result']['successMessage'];
					   //$result['upload_image']=$image_data['result']['upload_image'];
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $image_data['errortext'].'<br>';
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
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function upload_post()
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
			  'image'=>array('required'=>1,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->image))
			{
			  $userdata2['image']=$userdata->image;
			}
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$image_response=$this->MultipleImage($userdata2);
				
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data=$image_response['result'];
				 }
				
				
				$image_data=$this->Sop_model->UploadProductImage($post_data);
				if(!$image_data['error'])
				   {
					   $result['successMessage']=$image_data['result']['successMessage'];
					   $result['upload_image']=$image_data['result']['upload_image'];
					   $response_code='200';
				   }
				   else
				   {
					   $error =  true;
					   $errorText .= $image_data['errortext'].'<br>';
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
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
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
			
			if(!$error)
			{
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
					
					/*if($product_type!="")
					{
						$product_data=$this->Sop_model->GetProductOnCatIdAndType($post_data);
						$result['records']=count($product_data);
						$key='product_list';
					}*/
					
					
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
					   $page=1;
					   $per_page=20;
					   $offset = (($page - 1) * 20);
					   
					   $product_data=$this->Sop_model->GetProductData($offset,$per_page);
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
	
	
	public function price_slab_delete()
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
				$userdata =(object)$this->delete();
			}
			
			$product_type=$this->Sop_model->GetProductType();
			if($product_type==1 || $product_type==3)
			{
				$input_array=array(
				  'slab_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
				);
				
				$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
				if(!$check_input['error'])
				{
					$post_data=$check_input['result']['input_array'];
					
					$insert_response=$this->Sop_model->DeleteProductRentSlab($post_data);
					if($insert_response['error'])
					 {
						$error=true;
						$errorText=$insert_response['errortext'];
					 }
					 else
					 {
					   $product_data=$this->Sop_model->GetProductRentSlab();
					   $result['successMessage']='Data successfully deleted';
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
	
   
   public function stock_get()
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
			
			$stock_data=$this->Sop_model->GetStockData();
			if(!empty($stock_data))
			   {
				   $result=$stock_data;
				   $response_code='200';
			   }
			   else
			   {
				   $error =  true;
				   $errorText .=  'No data found'.'<br>';
			   }
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	

	public function stock_post()
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
			  'sku'=>array('required'=>1,'exp'=>''),
			  'avl_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'alert_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertStockData($post_data);
			    if(!$insert_response['error'])
				{
				   $stock_data=$this->Sop_model->GetStockData();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['stock_list']=$stock_data;
				   $response_code='200';
				}
				else
				{
				   $error =  true;
				   $errorText .= $insert_response['errortext'].'<br>';
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
	
	
	
	public function stock_put()
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
			  'stock_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'avl_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'alert_qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$update_response=$this->Sop_model->UpdateStockData($post_data);
			    if(!$update_response['error'])
				{
				   $stock_data=$this->Sop_model->GetStockData();	
				   $result['successMessage']=$update_response['result']['successMessage'];
				   $result['stock_list']=$stock_data;
				   $response_code='200';
				}
				else
				{
				   $error =  true;
				   $errorText .= $update_response['errortext'].'<br>';
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
	
	
	
	public function cart_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
				$cart_data=$this->Sop_model->GetCartDetails();
				
				if(!empty($cart_data))
				   {
					   $result['cart_data']=$cart_data;
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

	public function cart_post()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
			   $userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->post();
			}
			
			$input_array=array(
			  'product_sku'=>array('required'=>1,'exp'=>''),
			  'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['alpha_or_numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->InsertDataToCart($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully inserted';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function cart_qty_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
			   $userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->put();
			}
			
			$input_array=array(
			  'product_sku'=>array('required'=>1,'exp'=>''),
			  'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->update_cart_qty($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully updated';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function cart_date_put()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			if(!$error)
			{
		    	$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->put();
			}
			
			$input_array=array(
			  'start_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			  'end_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			);
			
		
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->update_cart_price_by_date($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully updated';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function cart_delete()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$authentication=$this->Basic_model->CheckGuestUserHeader();
			if($authentication['error'])
			{
			  $error=true;
			  $errorText=$authentication['errortext'];
			  $response_code='401';
			}
			
			if(!$error)
			{
		    	$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->delete();
			}
			
			$input_array=array(
			  'product_sku'=>array('required'=>1,'exp'=>''),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->delete_cart_item($post_data);
   			    if($insert_response['error'])
			     {
				    $error=true;
					$errorText=$insert_response['errortext'];
			     }
			     else
			     {
				   $cart_data=$this->Sop_model->GetCartDetails();
				   $result['successMessage']='Data successfully deleted';
				   $result['cart_data']=$cart_data;
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	
	public function cart_status_get()
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			
			if(!$error)
			{
				$order_status=$this->Sop_model->GetOrderStatus();
				
				if(!empty($order_status))
				   {
					   $result['order_status']=$order_status;
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
	
	public function cart_submit_post()
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
			  'customer_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'order_status_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'note'=>array('required'=>0,'exp'=>''),
			  'purchase_order_number'=>array('required'=>0,'exp'=>''),
			  'billing_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'shipping_address_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				
				if($this->Basic_model->role==4)
				{
					$sub_unique_id=$this->Basic_model->sub_unique_id;
					
					$order_status_id=$this->Sop_model->GetEmployeeStatus($sub_unique_id);
					if($order_status_id!=0)
					{
					    $post_data['order_status_id']=$this->Sop_model->GetEmployeeStatus($sub_unique_id);
					    $post_data['customer_id']=$sub_unique_id;
					}
					else
					{
						$error=true;
						$errorText='Default employee is not set';
					}
				}
				else
				{
					if($post_data['order_status_id']=="")
					{
						$error=true;
						$errorText='Please enter order_status_id as parameter';
					}
					
					if($post_data['customer_id']=="")
					{
						$error=true;
						$errorText='Please enter customer_id as parameter';
					}
				}
				
				if(!$error)
				{
					$insert_response=$this->Sop_model->SubmitProductOrder($post_data);
					if($insert_response['error'])
					 {
						$error=true;
						$errorText=$insert_response['errortext'];
					 }
					 else
					 {
					   $invoice_id=$insert_response['result']['invoice_id'];
					   $cart_data=$this->Sop_model->GetProductOrderDetailsOnId($invoice_id);
					   $result['successMessage']='Cart successfully submitted';
					   $result['cart_data']=$cart_data;
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
	
	
	
}
