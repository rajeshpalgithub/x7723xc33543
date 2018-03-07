<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	public function __construct()
	 {
	   parent::__construct();
	   
	   $this->load->model('Admin_model','',TRUE);
	   $this->load->model('Common_model','',TRUE);
	   $this->load->model('Basic_model','',TRUE);
	   $this->load->model('Admin_basic_model','',TRUE);
	   $this->load->model('Vendor_model','',TRUE);
	   
		   $authentication=$this->Admin_basic_model->session_exp();
		   if($authentication['error'])
		   {
			   $this->session->set_flashdata('message','<div class="alert alert-danger">'.$authentication['errortext'].'</div>');
			   redirect('login','refresh');
		   }
		   else
		   {
			   $authentication=$this->Admin_basic_model->authentication('Admin');
			   if($authentication['error'])
			   {
				   $this->session->set_flashdata('message','<div class="alert alert-danger">'.$authentication['errortext'].'</div>');
				   redirect('dashboard/error_page','refresh');
			   }
		   }
	   
	   
	 }
	 
	 public function sys_details()
	 {
		 echo phpinfo();
		 
	 }
	 
	 public function time_zone_list()
	 {
		$data['school_time_zone']=$this->Admin_model->GetTimezoneList(); 
		$this->load->view('admin/school_time_zone_list',$data); 
	 }
	 
	  public function smtp_setting()
	  {
		  $data['smtp_details']=$this->Admin_model->GetSmtpDetails();
		  $this->form_validation->set_rules('smtp_host', 'Smtp host','trim|required');
		  $this->form_validation->set_rules('smtp_user', 'Smtp user','trim|required');
		  $this->form_validation->set_rules('smtp_port', 'Smtp post','trim|required');
		  $this->form_validation->set_rules('smtp_pass', 'Smtp password','trim|required');
		  $this->form_validation->set_rules('store_name','Store name','trim|required');
		  $this->form_validation->set_rules('store_email', 'Store email','trim|required');
		  $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
		  if($this->form_validation->run() == FALSE)
		  {
				$this->load->view('admin/smtp_setting',$data); 
		  }
		  else
		  {
			   $post_data=$this->input->post();
		   	   $smtp_response=$this->Admin_model->UpdateSmtpSetting($post_data);
			   if($smtp_response)
			   {
				   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated</div>');
			   }
			   else
			   {
				   $this->session->set_flashdata('message', '<div class="alert alert-success">Error in updated</div>');
			   }
			   redirect('admin/smtp_setting','refresh');
		  }
		  
		  
	  }
	  
	  
	  
	  public function class_list()
	  {
		  $parames=$this->uri->segment(3);
		  $data['module_name']=$this->Admin_model->GetClassList();
		  $this->form_validation->set_rules('class_name', 'Class name','trim|required');
		  $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
		  if($this->form_validation->run() == FALSE)
		  {
				$this->load->view('admin/class_list',$data); 
		  }
		  else
		  {
			   $post_data=$this->input->post();
			   $class_name=$post_data['class_name'];
			   $this->session->set_flashdata('parames', $parames);
			   redirect($class_name."/get_method_list",'refresh');
		  } 
	  }
	  
	  
	  
	 
	 public function submit_method_list()
	 {
		 $post_data=$this->input->post();
		 $method_action=$this->Admin_model->SubmitMethodList($post_data);
		 if($method_action)
		 {
		   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully submitted</div>');
		   
		 }
		 else
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-alert">Submission error</div>');
		 }
		 redirect('admin/methods/','refresh');

	 }
	 
	 public function update_method_list()
	 {
		 $post_data=$this->input->post();
		 $method_action=$this->Admin_model->UpdateMethodList($post_data);
		 if($method_action)
		 {
		   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully submitted</div>');
		   
		 }
		 else
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-alert">Submission error</div>');
		 }
		 redirect('admin/class_list/2','refresh');

	 }
	 
	
	 
	 public function add_new_time_zone()
	 {
		$data['tz_list']=$this->Common_model->time_zone();
		$data['school_list']=$this->Admin_model->GetSchoolList_1(); 
		$this->form_validation->set_rules('school_id', 'School name','trim|required');
		$this->form_validation->set_rules('time_zone', 'Time','trim|required');
		$this->form_validation->set_rules('date_format', 'Date format','trim|required');
	     $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	     if($this->form_validation->run() == FALSE)
	     {
	   		$this->load->view('admin/add_new_time_zone',$data);
	     }
		 else
		 {
		    $post_data=$this->input->post();
		   	$insert_timezone=$this->Admin_model->AddNewTimeZone($post_data); 
			if(!$insert_timezone['error'])
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">'.$insert_timezone['result']['successMessage'].'</div>');
			    redirect('admin/time_zone_list','refresh');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_timezone['errortext'].'</div>');
			    redirect('admin/add_new_time_zone','refresh');
			}
		    
		 }
	 }
	 
	 
	 public function edit_timezone()
	 {
		 $id=$this->uri->segment(3);
		 $data['timezone_details']=$this->Admin_model->GetTimezoneDetailsOnId($id);
		 $data['tz_list']=$this->Common_model->time_zone();
		 $data['school_list']=$this->Admin_model->GetSchoolList_1();
		 $this->load->view('admin/edit_time_zone',$data);
	 }
	 
	 public function delete_time_zone()
	 {
		 $id=$this->uri->segment(3);
		 $success=$this->db->where('id',$id)->delete('school_timezone');
		 if($success)
		 {
			$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully deleted</div>');
			redirect('admin/time_zone_list','refresh');
		 }
		 else
		 {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
			redirect('admin/time_zone_list','refresh');
		 }
		 
	 }
	 
	 public function apply_sub_menu()
	 {
		 $id='';
		 $data['class_list']=$this->Admin_model->GetRestClassPermissionList($id);
		 if($this->input->post())
		 {
			 $post_data=$this->input->post();
			 $success=$this->Admin_model->SubmitParentChild($post_data);
			 if($success)
			 {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully submitted</div>');
			   
			 }
			 else
			 {
				 $this->session->set_flashdata('message', '<div class="alert alert-alert">Submission error</div>');
			 }
			 redirect('admin/class_list/1','refresh');
		 }
		 else
		 {
		    $this->load->view('admin/get_method_list',$data);
		 }
	 }
	 
	 public function get_method_list()
     {
		$module_id=$this->uri->segment(3);
		$method_list=$this->Admin_model->GetMethodList($module_id);
		$method_response='';
		if(!empty($method_list))
		{
			  $method_response='';			  
			  foreach($method_list as $item)
			  {
				  $method_name=$item['method_name']."_".$item['type'];
				  $method_id=$item['id'];
				 
				  $method_response.="<option value='$method_id'>$method_name</option>";
			  }
		}
		
		echo $method_response;
	
	}
	
	public function get_parent_method_list()
     {
		$db_method_id=$this->uri->segment(3); 
		$module_id=$this->uri->segment(4);
		$method_list=$this->Admin_model->GetParentMethodList($module_id,$db_method_id);
		$find_parent_id=$this->Admin_model->FindParentId($module_id,$db_method_id);
		$method_response='';
		if(!empty($method_list))
		{
			  $method_response='';			  
			  foreach($method_list as $item)
			  {
				  $selected='';
				  $method_name=$item['method_name']."_".$item['type'];
				  $method_id=$item['id'];
				  if($find_parent_id==$method_id)
				  {
					  $selected='selected';
				  }
				 
				  $method_response.="<option value='$method_id' $selected>$method_name</option>";
			  }
		}
		
		echo $method_response;
	
	}

	 
	 public function update_time_zone()
	 {
		$id=$this->uri->segment(3); 
		$post_data=$this->input->post();
		$update_timezone=$this->Admin_model->UpdateTimeZone($post_data,$id); 
		if(!$update_timezone['error'])
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">'.$update_timezone['result']['successMessage'].'</div>');
			redirect('admin/time_zone_list','refresh');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$update_timezone['errortext'].'</div>');
			redirect('admin/edit_time_zone','refresh');
		}
		
	 }
	 
	 public function sms_vendor_list()
	 {
		$data['vendor_list']=$this->Admin_model->SmsVendorList();
		$this->load->view('admin/sms_vendor_list',$data);
	 }
	 
	  public function add_new_sms_vendor()
	  {
		 $this->form_validation->set_rules('vendor_name', 'Vendor name','trim|required');
	     $this->form_validation->set_rules('api_key', 'Api key','trim|required');
		 $this->form_validation->set_rules('end_point', 'End point','trim|required');
	     $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	     if($this->form_validation->run() == FALSE)
	     {
	   		$this->load->view('admin/add_new_sms_vendor');
	     }
		 else
		 {
			 $post_data=$this->input->post();
			 $insert_sms_vendor=$this->Admin_model->InsertSmsVendor($post_data);
			 if($insert_sms_vendor['error'])
			 {
				 $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_sms_vendor['errortext'].'</div>');
			     redirect('admin/add_new_sms_vendor','refresh');
			 }
			 else
			 {
				 $this->session->set_flashdata('message', '<div class="alert alert-success">'.$insert_sms_vendor['result']['successMessage'].'</div>');
			     redirect('admin/sms_vendor_list','refresh');
			 }
			 
		 }
		
		
	  }
	  
	  public function edit_vendorlist_setting()
	  {
		  $id=$this->uri->segment(3);
		  $data['vendor_details']=$this->Admin_model->GetSmsVendorDetailsOnId($id);
		  $this->load->view('admin/edit_sms_vendor',$data);
	  }
	  
	  public function update_sms_vendor()
	  {
		  $id=$this->uri->segment(3);
		  $post_data=$this->input->post();
		  $update_sms_vendor=$this->Admin_model->UpdateSmsVendor($post_data,$id);
		  if($update_sms_vendor['error'])
		  {
			 $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$update_sms_vendor['errortext'].'</div>');
			 redirect('admin/edit_sms_vendor','refresh');
		  }
		  else
		  {
			 $this->session->set_flashdata('message', '<div class="alert alert-success">'.$update_sms_vendor['result']['successMessage'].'</div>');
			 redirect('admin/sms_vendor_list','refresh');
		  }
	  }
	  
	  
	public function change_smsvendor_state()
	{
		$id=$this->uri->segment(3);
		$smsvendor_data=$this->Admin_model->GetSmsVendorDetailsOnId($id);
		if(!empty($smsvendor_data))
		{
			if($smsvendor_data['is_active']==1)
			{
				$new_status=0;
			}
			else
			{
				$new_status=1;
			}
			
			$update_arr=array
	        (
		     'is_active'=>$new_status,
			);
			$success=$this->db->where('id',$id)->update('sms_vendor',$update_arr);
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully changed</div>');
			}
			
		}
		//die();
		redirect('admin/sms_vendor_list','refresh');
	}
	  
	  
	 public function delete_smsvendor_config()
	 {
		$id=$this->uri->segment(3);
		$success=$this->db->where('id',$id)->delete('sms_vendor');
		if($success)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully deleted</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
			   
		}
		
		redirect('admin/sms_vendor_list','refresh');
	 }
	  
	 
	 public function edit_smsconfig_setting()
	 {
		$id=$this->uri->segment(3); 
		$data['school_list']=$this->Admin_model->GetSchoolList_1();
		$data['sms_vendor_list']=$this->Admin_model->GetSmsVendorList();
		$data['sms_config']=$this->Admin_model->GetSmsConfigDetailsOnId($id);
		$this->load->view('admin/edit_new_sms_config',$data);
	 }
	 
	 
	 public function update_sms_config()
	 {
		$id=$this->uri->segment(3); 
		$post_data=$this->input->post();
		$update_sms_config=$this->Admin_model->UpdateSmsConfig($post_data,$id);
		
		
		if($update_sms_config['error'])
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$update_sms_config['errortext'].'</div>');
			 
		 }
		 else
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-success">'.$update_sms_config['result']['successMessage'].'</div>');
			
		 }
		redirect('admin/sms_config','refresh');	 
		
	 }
	 
	 public function add_new_sms_config()
	 {
		 $data['school_list']=$this->Admin_model->GetSchoolList_1();
		 $data['sms_vendor_list']=$this->Admin_model->GetSmsVendorList();
		 $this->form_validation->set_rules('school_list', 'School','trim|required');
	     $this->form_validation->set_rules('vendor_list', 'Vendor','trim|required');
	     $this->form_validation->set_rules('total_sms', 'Total sms','trim|required');
		 $this->form_validation->set_rules('sms_text', 'Sms content','trim|required');
		 $this->form_validation->set_rules('exp_date', 'Expiry date','trim|required');
	     $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	     if($this->form_validation->run() == FALSE)
	     {
	   		$this->load->view('admin/add_new_sms_config',$data);
	     }
		 else
		 {
			 $post_data=$this->input->post();
			 $add_sms_config=$this->Admin_model->AddNewSmsConfig($post_data);
			 
			 //echo '<pre>',print_r($add_sms_config);
			// die();
			 if($add_sms_config['error'])
			 {
				 $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$add_sms_config['errortext'].'</div>');
			     redirect('admin/add_new_sms_config','refresh');
			 }
			 else
			 {
				 $this->session->set_flashdata('message', '<div class="alert alert-success">'.$add_sms_config['result']['successMessage'].'</div>');
			     redirect('admin/sms_config','refresh');
			 }
		 }
		 
		
		 
	 }
	 
	public function view_school_details($id){
	    $id=$this->uri->segment(3);
	 	$data['school_data']=$this->Vendor_model->GetSchoolDetailsOnId($id);
		$data['login_data']=$this->Common_model->GetLoginDetails($id,3);
	 	$this->load->view('admin/viewschool', $data);
	 } 
	 
	 public function permission_list()
	 {
		$id=$this->uri->segment(3); 
		$data['client_id']=$id;
	 	$data['client_permission_data']=$this->Admin_model->GetClassPermissionList($id);
	 	$this->load->view('admin/controller_permission_list', $data);
	 }
	 
	 public function rest_permission_list()
	 {
		$id=$this->uri->segment(3); 
		$data['client_id']=$id;
	 	$data['client_permission_data']=$this->Admin_model->GetRestClassPermissionList($id);
	 	$this->load->view('admin/rest_controller_permission_list', $data);
	 }
	 
	 public function submit_class_permission()
	 {
		$id=$this->uri->segment(3); 
		$post_data=$this->input->post();
	 	$response=$this->Admin_model->SubmitClassPermissionList($id,$post_data);
	 	if(!$response)
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-danger">Insert Error</div>');
			 redirect('admin/school_list','refresh');
		 }
		 else
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully submitted</div>');
			 redirect('admin/school_list','refresh');
		 }
	 }
	 
	 public function submit_rest_class_permission()
	 {
		$id=$this->uri->segment(3); 
		$post_data=$this->input->post();
	 	$response=$this->Admin_model->SubmitRestClassPermissionList($id,$post_data);
	 	if(!$response)
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-danger">Insert Error</div>');
			 redirect('admin/school_list','refresh');
		 }
		 else
		 {
			 $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully submitted</div>');
			 redirect('admin/school_list','refresh');
		 }
	 }
	 
	public function edit_school()
	{
		$id=$this->uri->segment(3);
		$data['country_list']=$this->Vendor_model->GetCountryList();
	 	$data['school_data']=$this->Vendor_model->GetSchoolDetailsOnId($id);
		$data['login_data']=$this->Common_model->GetLoginDetails($id,3);
		$this->load->view('admin/edit_school',$data);
	   
	}
	
	public function update_school()
	{
		$id=$this->uri->segment(3);
		$post_data=$this->input->post();
		
		$update_response=$this->Vendor_model->UpdateSchoolData($id,$post_data);
		if($update_response['error'])
		   {
			  $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$update_response['errortext'].'</div>');
			  redirect('admin/edit_school','refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">'.$update_response['result']['successMessage'].'</div>');
			   redirect('admin/school_list','refresh');
			   //die();
		   }
		
	}
	
	public function delete_school()
	{
		$id=$this->uri->segment(3);
		$check_status=$this->db->select('*')->where('school_id',$id)->get('student');
		if($check_status->num_rows()>0)
		{
			 $this->session->set_flashdata('message', '<div class="alert alert-danger">You can not delete, this school occupied students</div>');
		}
		else
		{
			$success=$this->db->where('unique_id',$id)->where('role',3)->delete('login');
			$success=$this->db->where('id',$id)->delete('school');
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully deleted</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
				   
			}
		}
		redirect('admin/school_list','refresh');
		
	}
	
    public function add_school()
	{
	   $data['country_list']=$this->Vendor_model->GetCountryList();	  
	   $data['vendor_list']=$this->Vendor_model->GetVendorDetails(); 
	   
	   $this->form_validation->set_rules('vendor', 'Vendor name','trim|required');	  
	   $this->form_validation->set_rules('name', 'School name','trim|required');
	   $this->form_validation->set_rules('password', 'Password','trim|required');
	   $this->form_validation->set_rules('phone', 'Phone','trim|required');
	   $this->form_validation->set_rules('address1', 'Address','trim|required');
	   $this->form_validation->set_rules('city_village', 'City / Village','trim|required');
	   $this->form_validation->set_rules('country', 'Country','trim|required');
	   $this->form_validation->set_rules('state', 'State','trim|required');
	   $this->form_validation->set_rules('pin', 'Pin','trim|required');
	   
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		$this->load->view('admin/add_school',$data);
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $insert_response=$this->Admin_model->AddNewSchool($post_data);
		  
		   if($insert_response['error'])
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_response['errortext'].'</div>');
			  redirect('admin/add_school','refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">'.$insert_response['result']['successMessage'].'</div>');
			   redirect('admin/add_school','refresh');
		   }
		  
	   }

	  }
	
	public function getstatelist()
	{ 
		$state_id=$this->uri->segment(3);
		$state_list=$this->Vendor_model->GetStatelist($state_id);
		$section_response='';
		$section_response="<option value=''>Select section name</option>";
		if(!empty($state_list))
		{
			  $section_respons='';			  
			  foreach($state_list as $item)
			  {
				  $state_name=$item['name'];
				  $state_id=$item['id'];
				  $section_respons.="<option value='$state_id'>$state_name</option>";
			  }
		}
		
		echo $section_respons;
	}
	
	public function change_vendor_school()
	{
		$id=$this->uri->segment(3);
	 	$data['vendor_list']=$this->Admin_model->GetVendorList1();
		$data['school_list']=$this->Admin_model->GetSchoolList_1();
		
	 	$this->load->view('admin/change_school_vendor', $data);
	}
	
	public function update_vendor_school()
	{
		$id=$this->uri->segment(3);
		$vendor_id=$this->input->post('vendor');
		
		$arr=array('vendor_id'=>$vendor_id);
		$success=$this->db->where('id',$id)->update('school',$arr);
		if($success)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully updated</div>');
			redirect('admin/school_list','refresh');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in update</div>');
			redirect('admin/change_vendor_school','refresh');   
		}
		
	}
	 
	 public function view_sms_vendor($id){
	    $id=$this->uri->segment(3);
	 	$data['config_data']=$this->Admin_model->GetSmsVendorOnId($id);
	 	$this->load->view('admin/view_sms_vendor', $data);
	 }
	 
	 
	 public function sms_config()
	 {
		$this->load->library('pagination');
		$config['base_url']=base_url() . 'admin/sms_config/';
		$config['total_rows']=$this->Admin_model->getTotalSmsConfig();
		
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['per_page']=20;
		$config['uri_segment']=3;
		
		$config['first_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['first_tag_open'] = '';
		$config['first_tag_close'] = '';
	
		$config['last_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['last_tag_open'] = '';
		$config['last_tag_close'] = '';
		
		$config['next_link'] = '<b class="fa fa-chevron-right">Next</b>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
	
		$config['prev_link'] = '<b class="fa fa-chevron-left">Prev</b>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$data['links']  = $this->pagination->create_links();
	
		$offset=0;
		$uri=$this->uri->segment(3);
		if(isset($uri))
		{
			$offset=$this->uri->segment(3);
		}
		
		$student_section=$this->input->get('section');
		$data['sms_config_list']=$this->Admin_model->GetSmsConfigList($offset,$config['per_page']);
		$this->load->view('admin/sms_config',$data);
	 }
	 
     public function school_list()
	 {
		 
		$this->load->library('pagination');
		$config['base_url']=base_url() . 'admin/school_list/';
		$config['total_rows']=$this->Admin_model->getSchoolRows();
		
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['per_page']=30;
		$config['uri_segment']=3;
		
		$config['first_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['first_tag_open'] = '';
		$config['first_tag_close'] = '';
	
		$config['last_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['last_tag_open'] = '';
		$config['last_tag_close'] = '';
		
		$config['next_link'] = '<b class="fa fa-chevron-right">Next</b>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
	
		$config['prev_link'] = '<b class="fa fa-chevron-left">Prev</b>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$data['links']  = $this->pagination->create_links();
	
		$offset=0;
		$uri=$this->uri->segment(3);
		if(isset($uri))
		{
			$offset=$this->uri->segment(3);
		}
		
		$student_section=$this->input->get('section');
		$data['school_list']=$this->Admin_model->GetSchoolList($offset,$config['per_page']);

		$this->load->view('admin/list_school',$data);
	 }
	 
	public function add_vendor()
	{
	   $this->form_validation->set_rules('name', 'School name','trim|required');
	   $this->form_validation->set_rules('phone', 'Phone','trim|required');
	   $this->form_validation->set_rules('email', 'Email','trim|required');
	   $this->form_validation->set_rules('password', 'Password','trim|required');
	   $this->form_validation->set_rules('address1', 'Address','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		$this->load->view('admin/add_vendor');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $insert_response=$this->Admin_model->AddNewVendor($post_data);
		  
		   if($insert_response['error'])
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_response['errortext'].'</div>');
			  redirect('admin/add_vendor','refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">'.$insert_response['result']['successMessage'].'</div>');
			   redirect('admin/add_vendor','refresh');
		   }
	   }
		
	}
	 
	 
	 
	public function change_smsconfig_state()
	{
		$post_data=array();
		$post_data['school_list']=$this->uri->segment(4);
		$config_checking=$this->Admin_model->SmsConfigChecking($post_data);
	 
	    if(!$config_checking['error'])
	    {
			$id=$this->uri->segment(3);
			$vendor_data=$this->Admin_model->GetSmsConfigDetailsOnId($id);
			if(!empty($vendor_data))
			{
				if($vendor_data['is_active']==1)
				{
					$new_status=0;
				}
				else
				{
					$new_status=1;
				}
				
				$update_arr=array
				(
				 'is_active'=>$new_status,
				);
				$success=$this->db->where('id',$id)->update('sms_config',$update_arr);
				if($success)
				{
					$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully changed</div>');
				}
				
			}
		//die();
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$config_checking['errortext'].'</div>');
		}
		redirect('admin/sms_config','refresh');
	} 
	
	public function change_state()
	{
		$id=$this->uri->segment(3);
		$vendor_data=$this->Admin_model->GetVendorDetailsOnId($id);
		if(!empty($vendor_data))
		{
			if($vendor_data['Is_active']==1)
			{
				$new_status=0;
			}
			else
			{
				$new_status=1;
			}
			
			$update_arr=array
	        (
		     'Is_active'=>$new_status,
			);
			$success=$this->db->where('id',$id)->update('vendor',$update_arr);
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully changed</div>');
			}
			
		}
		//die();
		redirect('admin/vendor_list','refresh');
	}
	
	
	public function change_state_school()
	{
		$id=$this->uri->segment(3);
		$school_data=$this->Vendor_model->GetSchoolDetailsOnId($id);
		if(!empty($school_data))
		{
			if($school_data['Is_active']==1)
			{
				$new_status=0;
			}
			else
			{
				$new_status=1;
			}
			
			$update_arr=array
	        (
		     'Is_active'=>$new_status,
			);
			$success=$this->db->where('id',$id)->update('school',$update_arr);
			
		
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Status successfully changed</div>');
			}
			
		}
		//die();
		redirect('admin/school_list','refresh');
	}
	
	public function view_vendor_details($id){
	    $id=$this->uri->segment(3);
	 	$data['vendor_data']=$this->Admin_model->GetVendorDetailsOnId($id);
		$data['login_data']=$this->Common_model->GetLoginDetails($id,2);
	 	$this->load->view('admin/viewvendor', $data);
	 }
	
	public function delete_sms_config()
	{
		$post_data=array();
		$post_data['school_list']=$this->uri->segment(4);
		$config_checking=$this->Admin_model->SmsConfigChecking($post_data);
	 
	    if(!$config_checking['error'])
	    {
			$id=$this->uri->segment(3);
			$success=$this->db->where('id',$id)->delete('sms_config');
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully deleted</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
				   
			}
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$config_checking['errortext'].'</div>');
		}
		redirect('admin/sms_config','refresh');
	}
	
	public function delete_vendor()
	{
		$id=$this->uri->segment(3);
		
		$check_status=$this->db->select('*')->where('vendor_id',$id)->get('school');
		if($check_status->num_rows()>0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">You can not delete, this vendor occupied school</div>');
		}
		else
		{
			$success=$this->db->where('unique_id',$id)->where('role',2)->delete('login');
			$success=$this->db->where('id',$id)->delete('vendor');
			if($success)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully deleted</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
				   
			}
		}
		redirect('admin/vendor_list','refresh');
		
	}
	
	
	public function edit_vendor()
	{
	  $id=$this->uri->segment(3);
	  $data['vendor_data']=$this->Admin_model->GetVendorDetailsOnId($id);
	  $data['login_data']=$this->Common_model->GetLoginDetails($id,2);
	  $this->load->view('admin/edit_vendor',$data);
	}
	
	public function update_vendor()
	{
		$id=$this->uri->segment(3);
		$post_data=$this->input->post();
		
		$update_response=$this->Admin_model->UpdateVendorData($id,$post_data);
		if($update_response['error'])
		   {
			  $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$update_response['errortext'].'</div>');
			  redirect('admin/edit_vendor','refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">'.$update_response['result']['successMessage'].'</div>');
			   redirect('admin/vendor_list','refresh');
			   //die();
		   }
		
	}
	
	
	
	 public function vendor_list()
	 {
		$this->load->library('pagination');
		$config['base_url']=base_url() . 'admin/vendor_list/';
		$config['total_rows']=$this->Admin_model->getVendorRows();
		
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['per_page']=20;
		$config['uri_segment']=3;
		
		$config['first_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['first_tag_open'] = '';
		$config['first_tag_close'] = '';
	
		$config['last_link'] = '<b class="fa fa-chevron-right"></b>';
		$config['last_tag_open'] = '';
		$config['last_tag_close'] = '';
		
		$config['next_link'] = '<b class="fa fa-chevron-right">Next</b>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
	
		$config['prev_link'] = '<b class="fa fa-chevron-left">Prev</b>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$this->pagination->initialize($config);
		$data['links']  = $this->pagination->create_links();
	
		$offset=0;
		$uri=$this->uri->segment(3);
		if(isset($uri))
		{
			$offset=$this->uri->segment(3);
		}
		
		$student_section=$this->input->get('section');
		$data['vendor_list']=$this->Admin_model->GetVendorList($offset,$config['per_page']);

		 $this->load->view('admin/list_vendor',$data);
	 } 
	 
	 public function modules()
	 {
	   $id='';
	   $data['class_list']=$this->Admin_model->GetRestClassPermissionList($id);
	   $this->load->view('admin/modules',$data);
	 }
	 
	 public function add_module()
	 {
		 
	   $this->form_validation->set_rules('class_name', 'Class name','trim|required');
	   $this->form_validation->set_rules('display_name', 'Display name','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		$this->load->view('admin/add_module');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $insert_response=$this->Admin_model->AddNewModule($post_data);
		  
		   if($insert_response['error'])
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_response['errortext'].'</div>');
			  redirect('admin/add_module','refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully Submitted</div>');
			   redirect('admin/modules','refresh');
		   }
	   }
 
	 }
	 
	 
	 
	 public function edit_module()
	 {
		 $id=$this->uri->segment(3);
		 $data['class_data']=$this->Admin_model->ClassDataOnId($id);
		 $this->load->view('admin/edit_module',$data);
	 }
	 
	  public function update_module()
	  {
	   $id=$this->uri->segment(3); 
	   $this->form_validation->set_rules('class_name', 'Class name','trim|required');
	   $this->form_validation->set_rules('display_name', 'Display name','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		redirect('admin/modules','refresh');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $insert_response=$this->Admin_model->UpdateModule($post_data,$id);
		  
		   if($insert_response['error'])
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_response['errortext'].'</div>');
			  redirect("admin/edit_module/$id",'refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully Submitted</div>');
			   redirect('admin/modules','refresh');
		   }
	   }
		 
		 
		 
		 
	 }
	 
	 
	 public function delete_module()
	 {
		 $id=$this->uri->segment(3);
		 $success=$this->db->where('id',$id)->delete('module');
		 if(!$success)
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
			  redirect("admin/modules",'refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully deleted</div>');
			   redirect('admin/modules','refresh');
		   }
	 }
	 
	 
	 
	  public function imported_methods()
	 {
		  $this->load->view('admin/imported_methods');
	 }
	 public function import_methods()
	 { 
	      $parames=1;
		  $data['module_name']=$this->Admin_model->GetClassList();
		  if($this->input->get())
		  {
			  $post_data=$this->input->get();
			  $class_name=$post_data['class_name'];
			  $this->session->set_flashdata('parames', $parames);
			  redirect($class_name."/get_method_list",'refresh');
		  }
		  else
		  {
		      $this->load->view('admin/method_list_new',$data);
		  }
	 }
	 public function methods()
	 {
		 $data['module_name']=$this->Admin_model->GetClassList();
		 if($this->input->get())
		 {
		   $post_data=$this->input->get();	 
		   $module_id=$post_data['class_name'];
		   $data['method_list']=$this->Admin_model->GetMethodList($module_id);
		   $this->load->view('admin/methods',$data);
		 }
		 else
		 {
		   $this->load->view('admin/methods',$data);
		 }
	 }
	 
	 public function edit_method()
	 {
		 $id=$this->uri->segment(3);
		 $data['method_data']=$this->Admin_model->GetMethodDetails($id);
		 $this->load->view('admin/edit_method',$data);
	 }
	 
	 public function update_method()
	  {
	   $id=$this->uri->segment(3); 
	   $this->form_validation->set_rules('method_name', 'Method name','trim|required');
	   $this->form_validation->set_rules('display_name', 'Display name','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		redirect('admin/methods','refresh');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $insert_response=$this->Admin_model->UpdateMethod($post_data,$id);
		   $module_id=$this->Common_model->get_single_field_value('method','module_id','id',$id);
		   if($insert_response['error'])
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$insert_response['errortext'].'</div>');
			  redirect("admin/edit_method/$id",'refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully Submitted</div>');
			   redirect("admin/methods?class_name=$module_id",'refresh');
		   }
	   }
		 

	 }
	 
	 
	 public function delete_method()
	 {
		 $id=$this->uri->segment(3);
		 $module_id=$this->Common_model->get_single_field_value('method','module_id','id',$id);
		 
		 $success=$this->db->where('id',$id)->delete('method');
		 if(!$success)
		   {
		      $this->session->set_flashdata('message', '<div class="alert alert-danger">Error in delete</div>');
			  redirect("admin/methods?class_name=$module_id",'refresh');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully deleted</div>');
			   redirect("admin/methods?class_name=$module_id",'refresh');
		   }
	 }
	 

	 
	 public function methods_menu()
	 {
		 $data['module_name']=$this->Admin_model->GetClassList();
		 if($this->input->get())
		 {
		   $post_data=$this->input->get();	 
		   $module_id=$post_data['class_name'];
		   $data['method_list']=$this->Admin_model->GetMethodList($module_id);
		   $this->session->set_flashdata('message', '<div class="alert alert-success">Successfully updated</div>');
		   $this->load->view("admin/methods_menu",$data);
		 }
		 else
		 {
		   $this->load->view('admin/methods_menu',$data);
		 }
	 }
	 
	 public function apply_parent_method()
	 {
		$data['module_name']=$this->Admin_model->GetClassList(); 
		$post_data=$this->input->post(); 
		$response=$this->Admin_model->ApplyParentMethod($post_data);
		$this->load->view('admin/methods_menu',$data);
	 }
	 
	
	 
	 
}
