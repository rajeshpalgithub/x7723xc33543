<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Offrent extends REST_Controller {
	
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
			'offrent_products'=>array('GET'=>array(3,4)),
			'submit_offrent'=>array('POST'=>array(3,4)),
			'update'=>array('POST'=>array(3)),
			'master_status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'offrent_status'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			'temp'=>array('GET'=>array(3,4),'POST'=>array(3,4),'PUT'=>array(3,4),'DELETE'=>array(3,4)),
			'edit_add'=>array('PUT'=>array(3,4)),
			'edit_qty'=>array('PUT'=>array(3,4)),
			'edit_remove'=>array('DELETE'=>array(3,4)),
			'update'=>array('POST'=>array(3,4)),
		);
		
    }
	
	public function get_method_list_get()
	{
		//$parames=$this->session->flashdata('parames');
		//$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		//$data['fetch_class_name']='offrent';
		$data['module_id']=$product_type=$this->Common_model->get_single_field_value('module','id','class_name','offrent');
		$this->response($data);
		/*if($parames==1)
		{
		  $this->load->view("admin/method_list_new",$data);
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
			  'offrent_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),	
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
				if($post_data['offrent_id']!='')
				{
					$offrent_list=$this->Sop_model->GetOffRentDetails($post_data['offrent_id']);
					$result['offrent_details']=$offrent_list;
				}
				else
				{
					$offrent_list=$this->Sop_model->GetOffRentList($offset,$per_page);
					$result['page']=$page;
					$result['records']=count($offrent_list);
					$result['offrent_list']=$offrent_list;
				}
			    if(!empty($offrent_list))
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
				$order_data=$this->Sop_model->GetRecentOffrentList($offset,$per_page);
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
			
			$master_status=$this->Sop_model->GetOffrentMasterStatusList();
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
				 $insert_response=$this->Sop_model->InsertMasterOffrentStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetOffrentMasterStatusList();	
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
				 $update_response=$this->Sop_model->UpdateMasterOffrentStatus($post_data);
			     if(!$update_response['error'])
				 {
				   $status_list=$this->Sop_model->GetOffrentMasterStatusList();	
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
	
	
	public function offrent_status_get()
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
			
			$order_status=$this->Sop_model->GetOffrentStatusList();
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
	
	public function offrent_status_post()
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
				 $insert_response=$this->Sop_model->InsertOffrentStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetOffrentStatusList();	
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
	
	
	public function offrent_status_put()
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
			  'offrent_status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'employee_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			  'status_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->UpdateOffrentStatus($post_data);
			     if(!$insert_response['error'])
				 {
				   $status_list=$this->Sop_model->GetOffrentStatusList();	
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
	
	
	public function offrent_products_get()
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
				$insert_response=$this->Sop_model->OffrentProductList($post_data);
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
				 $insert_response=$this->Sop_model->GetOffrentTempList($post_data);
			     if(!empty($insert_response))
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['offrent_temp_list']=$insert_response;
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
			 'end_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			 'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->InsertDataToTempOffrent($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['offrent_list']=$this->Sop_model->GetOffrentTempList($post_data);
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
	
	public function temp_put()
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
			 'temp_offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'end_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			 'end_date'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['date']),
			 'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->UpdateDataToTempOffrent($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['offrent_list']=$this->Sop_model->GetOffrentTempList($post_data);
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
			 'temp_offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->DeleteDataToTempOffrent($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']=$insert_response['result']['successMessage'];
				   $result['offrent_list']=$this->Sop_model->GetOffrentTempList($post_data);
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
	
	
	public function GetOffrentItem($userdata2)
	{
		$error =  false;
		$errorText = '';
		$result = '';
		$response_code = '';
		try
		{
			
			if(!$error)
			{
				
				if(!isset($userdata2['offrent_arr']))
				{
					$error =  true;
					$errorText .= 'offrent_arr require as parameter'.'<br>';
					$response_code='400';
				}
				
				if(!$error)
				{
					$userdata=$userdata2['offrent_arr'];
				
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
					 $result['offrent_item']=$contact_data_array;
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
	
	
	
	public function submit_offrent_post()
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
			 'status_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_note'=>array('required'=>0,'exp'=>''),
			 'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'note'=>array('required'=>0,'exp'=>''),
			 'offrent_arr'=>array('required'=>0,'exp'=>''),
			);
			$userdata2=array();
			if(isset($userdata->offrent_arr))
			{
			  $userdata2['offrent_arr']=$userdata->offrent_arr;
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
						$offrent_status=$this->Sop_model->GetOffrentEmployeeStatus($sub_unique_id);
						if($offrent_status!=0)
						{
							$post_data['status_id']=$this->Sop_model->GetOffrentEmployeeStatus($sub_unique_id);
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
						 $offrent_item_response=$this->GetOffrentItem($userdata2);
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
						 $insert_response=$this->Sop_model->SubmitOffrent($post_data);
						 if(!$insert_response['error'])
						 {
						   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
						   $result['successMessage']=$insert_response['result']['successMessage'];
						   $page=1;
						   $per_page=20;
						   $offset = (($page - 1) * 20);
						   $result['offrent_list']=$this->Sop_model->GetOffRentList($offset,$per_page);
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
	
	
	public function edit_add_put()
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
			 'offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->InsertDataOffrentDetails($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']="Successfully updated";
				   $offrent_list=$this->Sop_model->GetOffRentDetails($post_data['offrent_id']);
				   $result['offrent_list']=$offrent_list;
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
	
	public function edit_qty_put()
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
			 'offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'qty'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->UpdateDataToOffrent($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']="Successfully updated";
				   $offrent_list=$this->Sop_model->GetOffRentDetails($post_data['offrent_id']);
				   $result['offrent_list']=$offrent_list;
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
			 'offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'product_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			);
			
			$check_input=$this->Rest_model->Check_parameters($input_array,$userdata);
			if(!$check_input['error'])
			{
				 $post_data=$check_input['result']['input_array'];
				 $insert_response=$this->Sop_model->DeleteFromOffrent($post_data);
			     if(!$insert_response['error'])
				 {
				   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
				   $result['successMessage']="Successfully updated";
				   $offrent_list=$this->Sop_model->GetOffRentDetails($post_data['offrent_id']);
				   $result['offrent_list']=$offrent_list;
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
			 'offrent_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'order_id'=>array('required'=>1,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'status_note'=>array('required'=>0,'exp'=>''),
			 'product_id'=>array('required'=>0,'exp'=>$this->Basic_model->regularexp['numeric']),
			 'note'=>array('required'=>0,'exp'=>''),
			 'offrent_arr'=>array('required'=>0,'exp'=>''),
			);
			
			$userdata2=array();
			if(isset($userdata->offrent_arr))
			{
				$userdata2['offrent_arr']=$userdata->offrent_arr;
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
						$offrent_status=$this->Sop_model->GetOffrentEmployeeStatus($sub_unique_id);
						if($offrent_status!=0)
						{
							$post_data['status_id']=$this->Sop_model->GetOffrentEmployeeStatus($sub_unique_id);
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
					 
					 $offrent_item_response=$this->GetOffrentItem($userdata2);
	
					 if($offrent_item_response['error'])
					 {
						 $error=true;
						 $errorText .=implode('<br>',$offrent_item_response['errortext']);
					 }
					 else
					 {
						 $post_data+=$offrent_item_response['result'];
					 }
					
					 if(!$error)
					 {
						 $insert_response=$this->Sop_model->UpdateOffrent($post_data);
						 if(!$insert_response['error'])
						 {
						   //$status_list=$this->Sop_model->GetOrderStatusDetails();	
						   $result['successMessage']=$insert_response['result']['successMessage'];
						   $per_page=20;
						   $page=1;
						   $offset = (($page - 1) * 20);
						   $result['offrent_list']=$this->Sop_model->GetOffRentList($offset,$per_page);
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
