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
			'get_method_list'=>array('GET'=>array("a")),
			'type'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'price_slab'=>array('GET'=>array(0,3,4),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
		);
	}
    
	public function get_method_list_get()
	{
		$parames=$this->session->flashdata('parames');
		$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='products';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','products');
		if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
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
