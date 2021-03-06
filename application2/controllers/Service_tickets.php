<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Service_tickets extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('Sop_model');
	
	
	    $this->check_permission=array(
			'index'=>array('GET'=>array(3,4),'POST'=>array(3)),
			'recent'=>array('GET'=>array(3,4)),
			'get_method_list'=>array('GET'=>array("a")),
			'equipment_image'=>array('GET'=>array(3,4),'POST'=>array(3,4),'DELETE'=>array(3,4)),
			'service_request_products'=>array('GET'=>array(3,4)),
			'submit_service_request'=>array('POST'=>array(3,4)),
			'update'=>array('POST'=>array(3,4)),
			'upload'=>array('POST'=>array(3,4)),
			'master_status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'service_request_status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'temp'=>array('GET'=>array(3,4),'POST'=>array(3,4),'DELETE'=>array(3,4)),
			'edit_add'=>array('POST'=>array(3,4)),
			'edit_remove'=>array('DELETE'=>array(3,4)),
		);
		
    }
	
	public function get_method_list_get()
	{
		//$parames=$this->session->flashdata('parames');
		//$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','service_tickets');
		$this->response($data);
		/*if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
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
			$invoice_id='';
			$userdata=json_decode(file_get_contents('php://input'));
			if(!is_object($userdata))
			{
				$userdata =(object)$this->get();
			}
			
			$input_array=array(
			  'service_request_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),	
			  'order_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'start_date'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['date']),
			  'end_date'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['date']),
			  'search_cat'=>array('required'=>0,'exp'=>''),
			  'search_text'=>array('required'=>0,'exp'=>''),
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'row'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$order_id=$post_data['order_id'];
				
				$start_date=$post_data['start_date'];
				$end_date=$post_data['end_date'];
				
				if($this->input->get('start_date') && isset($start_date))
				{
					if($end_date=="")
					{
						$error =  true;
				        $errorText .= 'End date required'.'<br>';
					}
				}
				
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

			if(!$error)
			{
				if($post_data['service_request_id']!='')
				{
					$service_request_list=$this->Sop_model->GetServiceRequestDetails($post_data['service_request_id']);
					$result['service_request_details']=$service_request_list;
				}
				else
				{
					$service_request_list=$this->Sop_model->GetServiceRequestList($offset,$per_page);
					$result['page']=$page;
					$result['records']=count($service_request_list);
					$result['service_request_list']=$service_request_list;
				}
			    if(!empty($service_request_list))
			     {
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
	
	
	public function recent_get()
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
			
			$input_array=array(
			  'page'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'row'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				
				
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
				$order_data=$this->Sop_model->GetRecentServiceEquipmentList($offset,$per_page);
				$result['page']=$page;
				$result['records']=count($order_data);
				$result['order_list']=$order_data;
				
				
				if(!empty($order_data))
				   {
					   
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
	
	
	
	
	public function master_status_get()
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
			
			$master_status=$this->Sop_model->GetServiceRequesttMasterStatusList();
			if(!empty($master_status))
			{
			   $result=$master_status;
			   $response_code='200';
			}
			else
			{
			   $error =  true;
			   $errorText .='No record found'.'<br>';
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function master_status_post()
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
			  'status'=>array('required'=>1,'exp'=>''),
			  'is_changeable'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->InsertMasterServiceRequestStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetServiceRequesttMasterStatusList();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['status_list']=$status_list;
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
	
	
	public function master_status_put()
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
			  'status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'status'=>array('required'=>1,'exp'=>''),
			  'is_changeable'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			  'is_active'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['boolean']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $update_response=$this->Sop_model->UpdateMasterServiceRequestStatus($post_data);
			     if(!$update_response['error'])
				 {
				   $status_list=$this->Sop_model->GetServiceRequesttMasterStatusList();	
				   $result['successMessage']=$update_response['result']['successMessage'];
				   $result['status_list']=$status_list;
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
	
	
	public function service_request_status_get()
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
			
			$order_status=$this->Sop_model->GetServiceRequestStatusList();
			if(!empty($order_status))
			{
			   $result=$order_status;
			   $response_code='200';
			}
			else
			{
			   $error =  true;
			   $errorText .='No record found'.'<br>';
			}
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function service_request_status_post()
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
			  'employee_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->InsertServiceStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetServiceRequestStatusList();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['status_list']=$status_list;
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
	
	public function service_request_products_get()
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
			
			$input_array=array(
			  'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				$post_data=$check_input['result']['input_array'];
				$insert_response=$this->Sop_model->ServiceProductList($post_data);
			    if(!$insert_response['error'])
				 {	
				   $result['product_list']=$insert_response['result']['products'];
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
	
	
	public function service_request_status_put()
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
			  'service_status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'employee_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->UpdateServiceRequestStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetServiceRequestStatusList();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['status_list']=$status_list;
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
	
	public function temp_get()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->GetServiceTempList($post_data);
			     if(!empty($insert_response))
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['service_temp_list']=$insert_response;
				   //$result['status_list']=$status_list;
				   $response_code='200';
				 }
				else
				{
				   $error =  true;
				   $errorText .= 'No record found'.'<br>';
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
	
	
	public function GetServiceMultipleImage($userdata2)
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
				$image_response=$this->GetServiceMultipleImage($userdata2);
				
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data=$image_response['result'];
				 }
				
				
				$image_data=$this->Sop_model->UploadServiceImage($post_data);
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
	
	
	public function temp_post()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'image_name'=>array('required'=>0,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->image_name))
			{
			  $userdata2['image_name']=$userdata->image_name;
			}
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 
				 $image_response=$this->MultipleImageName($userdata2);
				
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data+=$image_response['result'];
				 }
				 
				 if(!$error)
				 {
					 $insert_response=$this->Sop_model->InsertDataToTempServiceRequest($post_data);
					 if(!$insert_response['error'])
					 {
					   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
					   $result['successMessage']=$insert_response['result']['successMessage'];
					   $result['service_request_list']=$this->Sop_model->GetServiceTempList($post_data);
					   $response_code='200';
					 }
					else
					 {
					   $error =  true;
					   $errorText .= $insert_response['errortext'].'<br>';
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
	
	
	public function temp_delete()
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
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->DeleteDataToServiceRequest($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['service_request_list']=$this->Sop_model->GetServiceTempList($post_data);
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
	
	
	public function GetServiceRequestItem($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				
				if(!isset($userdata2['service_arr']))
				{
					$error =  true;
					$errorText .= 'service_arr as element'.'<br>';
					$response_code='400';
				}
				
				if(!$error)
				{
					$userdata=$userdata2['service_arr'];
					$input_array=array(
					 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
					 'note'=>array('required'=>1,'exp'=>''),
					);
				
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
					 $result['service_request_item']=$contact_data_array;
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
	
	
	
	public function submit_service_request_post()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_note'=>array('required'=>1,'exp'=>''),
			 'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'service_arr'=>array('required'=>0,'exp'=>''),
			 'note'=>array('required'=>0,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->service_arr))
			{
			  $userdata2['service_arr']=$userdata->service_arr;
			}
			if(!$error)
			{
				$check_input=$this->Rest_model->Check_parameters($input_array,(object)$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 if($this->Basic_model->role==4)
					 {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						
						$service_request_status_id=$this->Sop_model->GetServiceRequestEmployeeStatus($sub_unique_id);
						if($service_request_status_id!=0)
						{
							$post_data['status_id']=$this->Sop_model->GetServiceRequestEmployeeStatus($sub_unique_id);
						}
						else
						{
							$error=true;
							$errorText='Default employee is not set';
						}

					 }
					 else
					 {
						if($post_data['status_id']=="")
						{
							$error=true;
							$errorText='Please enter status_id as parameter';
						}
					 }
					 
					 if(!$error)
					 {
						 $offrent_item_response=$this->GetServiceRequestItem($userdata2);
						 if($offrent_item_response['error'])
						 {
							 $error=true;
							 $errorText .=implode('<br>',$offrent_item_response['errortext']);
						 }
						 else
						 {
							 $post_data+=$offrent_item_response['result'];
						 }
					 }
					 if(!$error)
					 {
						 $insert_response=$this->Sop_model->SubmitServiceRequest($post_data);
						 if(!$insert_response['error'])
						 {
						   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
						   $result['successMessage']=$insert_response['result']['successMessage'];
						   $per_page=20;
						   $page=1;
						   $offset = (($page - 1) * 20);
						   $result['service_request_list']=$this->Sop_model->GetServiceRequestList($offset,$per_page);
						   $response_code='200';
						 }
						 else
						 {
						   $error =  true;
						   $errorText .= $insert_response['errortext'].'<br>';
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
			
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
	public function equipment_image_get()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->GetEquipmentImageList($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();
				   $result['equipment_image_count']=count($insert_response['result']['image_list']);	
				   $result['equipment_image_list']=$insert_response['result']['image_list'];
				   //$result['status_list']=$status_list;
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
	
	public function equipment_image_delete()
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
			 'equipment_image_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $delete_response=$this->Sop_model->DeleteEquipmentImage($post_data);
			     if(!$delete_response['error'])
				 {
				   $post_data2=$delete_response['result']['image_list_data'];	 
				   $insert_response=$this->Sop_model->GetEquipmentImageList($post_data2);
				   
				   $result['successMessage']=$delete_response['result']['successMessage'];
				   $result['equipment_image_count']=count($insert_response['result']['image_list']);	
				   $result['equipment_image_list']=$insert_response['result']['image_list'];
				   //$result['status_list']=$status_list;
				   $response_code='200';
				 }
				else
				{
				   $error =  true;
				   $errorText .= $delete_response['errortext'].'<br>';
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
	
	public function equipment_image_post()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'image_name'=>array('required'=>0,'exp'=>''),
			);
			$userdata2=array();
			if(isset($userdata->image_name))
			{
			   $userdata2['image_name']=$userdata->image_name;
			}
			
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $image_response=$this->MultipleImageName($userdata2);
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data+=$image_response['result'];
				 }
					
				 if(!$error)
				 {					 
					 $insert_response=$this->Sop_model->InsertEquipmentImage($post_data);
					 if(!$insert_response['error'])
					 { 
					   $response=$this->Sop_model->GetEquipmentImageList($post_data);
					   $result['successMessage']=$insert_response['result']['successMessage'];
					   $result['equipment_image_count']=count($response['result']['image_list']);	
					   $result['equipment_image_list']=$response['result']['image_list'];

					   $response_code='200';
					 }
					else
					 {
					   $error =  true;
					   $errorText .= $insert_response['errortext'].'<br>';
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
	
	public function edit_add_post()
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
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'image'=>array('required'=>0,'exp'=>''),
			);
			$userdata2=array();
			if(isset($userdata->image))
			{
			  $userdata2['image']=$userdata->image;
			}
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $image_response=$this->GetServiceMultipleImage($userdata2);
				 if($image_response['error'])
				 {
					 $error=true;
					 $errorText .=implode('<br>',$image_response['errortext']);
				 }
				 else
				 {
					 $post_data+=$image_response['result'];
				 }

				 $insert_response=$this->Sop_model->InsertEquipmentToServiceRequest($post_data);
			     if(!$insert_response['error'])
				 { 
				   
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $service_request_list=$this->Sop_model->GetServiceRequestDetails($post_data['service_request_id']);
				   $result['service_request_details']=$service_request_list;

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

	public function edit_remove_delete()
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
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->DeleteFromServiceRequest($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']="Successfully updated";
				   $service_request_list=$this->Sop_model->GetServiceRequestDetails($post_data['service_request_id']);
				   $result['service_request_details']=$service_request_list;
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
	
	public function update_post()
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
			 'service_request_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_note'=>array('required'=>0,'exp'=>''),
			 'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'note'=>array('required'=>0,'exp'=>''),
			 'service_arr'=>array('required'=>0,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->service_arr))
			{
			  $userdata2['service_arr']=$userdata->service_arr;
			}
			
			if(!$error)
			{
				$check_input=$this->Rest_model->Check_parameters($input_array,(object)$userdata);
				if(!$check_input['error'])
				{
					 $post_data=$check_input['result']['input_array'];
					 
					 if($this->Basic_model->role==4)
					 {
						$sub_unique_id=$this->Basic_model->sub_unique_id;
						$service_request_status_id=$this->Sop_model->GetServiceRequestEmployeeStatus($sub_unique_id);
						if($service_request_status_id!=0)
						{
							$post_data['status_id']=$this->Sop_model->GetServiceRequestEmployeeStatus($sub_unique_id);
						}
						else
						{
							$error=true;
							$errorText='Default employee is not set';
						}

					 }
					 else
					 {
						if($post_data['status_id']=="")
						{
							$error=true;
							$errorText='Please enter status_id as parameter';
						}
					 }
					 
					 if(!$error)
					 {
						 $offrent_item_response=$this->GetServiceRequestItem($userdata2);
						 if($offrent_item_response['error'])
						 {
							 $error=true;
							 $errorText .=implode('<br>',$offrent_item_response['errortext']);
						 }
						 else
						 {
							 $post_data+=$offrent_item_response['result'];
						 }
					 }
					
					 if(!$error)
					 {
						 $insert_response=$this->Sop_model->UpdateServiceRequest($post_data);
						 if(!$insert_response['error'])
						 {
						   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
						   $result['successMessage']=$insert_response['result']['successMessage'];
						   $per_page=20;
						   $page=1;
						   $offset = (($page - 1) * 20);
						   $result['service_request_list']=$this->Sop_model->GetServiceRequestList($offset,$per_page);
						   $response_code='200';
						 }
						 else
						 {
						   $error =  true;
						   $errorText .= $insert_response['errortext'].'<br>';
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
			
		}
		catch(Exception $e)
		{
			$error = true;
			$errortext = $e->getMessage();
		}
		
		$this->response( array('error'=>$error,'errortext'=>explode("<br>",rtrim($errorText,"<br>")),'result'=>$result),$response_code);
	}
	
}
