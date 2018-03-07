<?php
class Admin_model extends CI_Model
{
 
 public function GetTimezoneList()
 {
	 $timezone_arr=array();
	 $rs=$this->db->select('*')->get('school_timezone');
	 if($rs->num_rows()>0)
	 {
		 $timezone_arr=$rs->result_array();
	 }
	 
	 return  $timezone_arr;
 }
 
 public function GetClassList()
 {
	 $class_arr=array();
	 $rs=$this->db->select('*')->get('module');
	 if($rs->num_rows()>0)
	 {
		 $class_arr=$rs->result_array();
	 }
	 
	 return  $class_arr;
 }
 
 public function get_display_name($method_name,$moudule_id)
 {
	 $filter_array=explode('_',$method_name);
	 
	  $method_type='';
	  if(in_array('get', $filter_array))
	  {
		  $method_type='GET';
		  $method_name_arr=explode('_get',$method_name);
		  $method_name=$method_name_arr[0];
	  }
	  if(in_array('post', $filter_array))
	  {
		  $method_type='POST';
		  $method_name_arr=explode('_post',$method_name);
		  $method_name=$method_name_arr[0];
	  }
	  if(in_array('put', $filter_array))
	  {
		  $method_type='PUT';
		  $method_name_arr=explode('_put',$method_name);
		  $method_name=$method_name_arr[0];
	  }
	  if(in_array('delete', $filter_array))
	  {
		  $method_type='DELETE';
		  $method_name_arr=explode('_delete',$method_name);
		  $method_name=$method_name_arr[0];
	  }
	 
	 $module_array='';
	 $rs=$this->db->select('*')->where('module_id',$moudule_id)->where('method_name',$method_name)->where('type',$method_type)->get('method');
	 //echo $this->db->last_query();
	 //die();
	 if($rs->num_rows()>0)
	 {
		 $module_array=$rs->row_array();
		 $dispalay_name=$module_array['method_description'];
	 }
	 return $module_array;
 }
 
 public function SubmitMethodList($post_data)
 {
	//echo '<pre>',print_r($post_data); 
	//die();
	$module_id=$post_data['module_id'];
	$method_name_array=$post_data['name'];
	$method_display_name_array=$post_data['display_name']; 
	//$method_type_array=$post_data['method_type'];
	$method_request_array=$post_data['request_type'];
	$success=0;
	if(!empty($method_display_name_array))
	{
		for($i=0;$i<=count($method_display_name_array)-1;$i++)
		{
			$method_description='';
			$method_name='';
			$method_type='';
			
			$method_name=$method_name_array[$i];
			if(isset($method_display_name_array[$i]))
			{
				$method_description=$method_display_name_array[$i];
			}
			
			$method_name=$method_name_array[$i];
			if(isset($method_display_name_array[$i]))
			{
				$method_type=$method_request_array[$i];
			}
			
			if($method_description!="")
			{
				$insert_array=array(
				 'module_id'=>$module_id,
				 'method_description'=>$method_description,
				 'method_name'=>$method_name,
				 'type'=>$method_type,
				);
				
			   //echo '<pre>',print_r($insert_array);
				
			  $success=$this->db->insert('method',$insert_array);
			}

	   }
	}
	
	 return $success;
 }
 
 
 public function UpdateMethodList($post_data)
 {
	//echo '<pre>',print_r($post_data); 
	//die();
	$module_id=$post_data['module_id'];
	$method_name_array=$post_data['name'];
	$method_display_name_array=$post_data['display_name']; 
	$method_request_array=$post_data['request_type'];
		
	for($i=0;$i<=count($method_display_name_array)-1;$i++)
	{
		$method_description='';
		$method_name='';
		$method_type='';
		
		$method_name=$method_name_array[$i];
		if(isset($method_display_name_array[$i]))
		{
			$method_description=$method_display_name_array[$i];
		}
		
		$method_name=$method_name_array[$i];
		if(isset($method_display_name_array[$i]))
		{
			$method_type=$method_request_array[$i];
		}
		
		if($method_description!="")
		{
			$insert_array=array(
			 'method_description'=>$method_description,
			);
			
			//echo '<pre>',print_r($insert_array);
			
		   $success=$this->db->where('module_id',$module_id)->where('type',$method_type)->where('method_name',$method_name)
		   ->update('method',$insert_array);
		  
		}
		
	}
	
	 return $success;
 }
 
 
 
 public function GetSmtpDetails()
 {
	 $smtp_arr=array();
	 $rs=$this->db->select('*')->get('smtp_setting');
	 if($rs->num_rows()>0)
	 {
		 $smtp_arr=$rs->row_array();
	 }
	 
	 return  $smtp_arr;
 }
 
 public function UpdateSmtpSetting($post_data)
 {
	 $smtp_id=$post_data['smtp_id'];
	 
	   $smtp_setting_array=array(
		  'smtp_host'=>$post_data['smtp_host'],
		  'smtp_user'=>$post_data['smtp_user'],
		  'smtp_port'=>$post_data['smtp_port'],
		  'smtp_pass'=>$post_data['smtp_pass'],
		  'store_name'=>$post_data['store_name'],
		  'store_email'=>$post_data['store_email']
		 );
	 
	 if($smtp_id=="")
	 {
		 $success=$this->db->insert('smtp_setting',$smtp_setting_array);
	 }
	 else
	 {
		 $success=$this->db->where('id',$smtp_id)->update('smtp_setting',$smtp_setting_array);
	 }
	 
	 return $success;
 }
 
 public function AddNewTimeZone($post_data)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 $school_id=$post_data['school_id'];
	 $rs=$this->db->select('*')->where('school_id',$school_id)->get('school_timezone');
	 if($rs->num_rows()>0)
	 {
		 $error=true;
		 $errortext='Timezone already exist for this school';
	 }
	 else
	 {
		 $time_zone_array=array(
		  'school_id'=>$school_id,
		  'timezone'=>$post_data['time_zone'],
		  'date_format'=>$post_data['date_format']
		 );
		 
		 
		 $success=$this->db->insert('school_timezone',$time_zone_array);
		  if($success)
		  {
			$result['successMessage']='Time zone successfully added';
		  }
		  else
		  {
			$error=true;
			$errortext='Insert error';
		  }
		  
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
  public function UpdateTimeZone($post_data,$id)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 $school_id=$post_data['school_id'];
	 $rs=$this->db->select('*')->where('id!=',$id)->where('school_id',$school_id)->get('school_timezone');
	 if($rs->num_rows()>0)
	 {
		 $error=true;
		 $errortext='Timezone already exist for this school';
	 }
	 else
	 {
		 $time_zone_array=array(
		  'school_id'=>$school_id,
		  'timezone'=>$post_data['time_zone'],
		  'date_format'=>$post_data['date_format']
		 );
		 
		 
		 $success=$this->db->where('id',$id)->update('school_timezone',$time_zone_array);
		  if($success)
		  {
			$result['successMessage']='Time zone successfully updated';
		  }
		  else
		  {
			$error=true;
			$errortext='Insert error';
		  }
		  
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function GetTimezoneDetailsOnId($id)
 {
	$time_zone_arr=array();
	$rs=$this->db->select('*')->where('id',$id)->get('school_timezone');
	 if($rs->num_rows()>0)
	 {
		 $time_zone_arr=$rs->row_array();
	 } 
	 
	 return $time_zone_arr;
 }
 
 public function SmsVendorList()
 {
	 $sms_vendor_arr=array();
	 $rs=$this->db->select('*')->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		 $sms_vendor_arr=$rs->result_array();
	 }
	 
	 return  $sms_vendor_arr;
 }
 
 public function InsertSmsVendor($post_data)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 $vendor_name=$post_data['vendor_name'];
	 $api_key=$post_data['api_key'];
	 $end_point=$post_data['end_point'];
	 
	 $rs=$this->db->select('*')->where('vendor_name',$vendor_name)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Vendor name already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('api_key',$api_key)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Api key already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('end_point',$end_point)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Api endpoint already exist';
	 }
	 
	 if(!$error)
	 {
		   $vendor_arr=array(
		     'vendor_name'=>$vendor_name,
		     'api_key'=>$api_key,
		     'end_point'=>$end_point,
			 'is_active'=>1,
		    );
		  
		  $success=$this->db->insert('sms_vendor',$vendor_arr);
		  if($success)
		  {
			$result['successMessage']='Sms vendor successfully added';
		  }
		  else
		  {
			$error=true;
			$errortext='Insert error';
		  }
		  
		  
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
  public function UpdateSmsVendor($post_data,$id)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 $vendor_name=$post_data['vendor_name'];
	 $api_key=$post_data['api_key'];
	 $end_point=$post_data['end_point'];
	 
	 $rs=$this->db->select('*')->where('id!=',$id)->where('vendor_name',$vendor_name)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Vendor name already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('id!=',$id)->where('api_key',$api_key)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Api key already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('id!=',$id)->where('end_point',$end_point)->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Sms Api endpoint already exist';
	 }
	 
	 if(!$error)
	 {
		   $vendor_arr=array(
		     'vendor_name'=>$vendor_name,
		     'api_key'=>$api_key,
		     'end_point'=>$end_point,
			 'is_active'=>1,
		    );
		  
		  $success=$this->db->where('id',$id)->update('sms_vendor',$vendor_arr);
		  if($success)
		  {
			$result['successMessage']='Sms vendor successfully updated';
		  }
		  else
		  {
			 $error=true;
			 $errortext='Insert error';
		  }
		  
		  
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 
 
	
 public function SmsConfigChecking($post_data)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 $startdatetime='';
	 $enddatetime='';
	 
	// echo '<pre>',print_r($post_data);
	 //die();
	 
	 $school_id=$post_data['school_list'];
	 $rs=$this->db->select('*')->where('school_id',$school_id)->where('is_active',1)->get('sms_config');
	 if($rs->num_rows()>0)
	 {
		 $sms_config_arr=$rs->row_array();
		 $startdate=date("Y-m-d", strtotime($sms_config_arr['active_date_time']));
		 $enddate=date("Y-m-d", strtotime($sms_config_arr['expire_date_time']));
			
		 $startdatetime=$startdate." 00:00:00.000";
		 $enddatetime=$enddate." 23:59:59.997";
	 }
	 
	     $sql="SELECT * FROM sms_config WHERE CURDATE() between '$startdatetime' and '$enddatetime' And 
		       school_id='$school_id' AND is_active=1";
		 $rs2=$this->db->query($sql);
		 if($rs2->num_rows()>0)
		 {
			 $error=true;
			 $errortext='you can not allow this operation';
		 }
		 else
		 {
			$result['status']=1;
		 }
		 
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);	 
 }


 public function AddNewSmsConfig($post_data)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	
	 $config_checking=$this->SmsConfigChecking($post_data);
	 
	 if(!$config_checking['error'])
	 {
		 
	   $school_id=$post_data['school_list'];	 
	   $sms_config_array=array(
	   'is_active'=>1,
	   'used_sms'=>0,
	   'school_id'=>$school_id,
	   'total_sms'=>$post_data['total_sms'],
	   'sms_text'=>$post_data['sms_text'],
	   'active_date_time'=>date("Y/m/d"),
	   'expire_date_time'=>$post_data['exp_date'],
	   'sms_vendor_id'=>$post_data['vendor_list'],
		);
		
		$success=$this->db->insert('sms_config',$sms_config_array);
		if($success)
		{
			$result['successMessage']='Configuration successfully added';
		}
		else
		{
			$error=true;
			$errortext='Insert error';
		}
	 }
	 else
	 {
		 $error=true;
		 $errortext=$config_checking['errortext'];
	 }
	 
	  return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	 
 }

  public function UpdateSmsConfig($post_data,$id)
 {
	 $error=false;
	 $errortext='';
	 $result='';
		 
	   $sms_config_array=array(
	   'is_active'=>1,
	   'total_sms'=>$post_data['total_sms'],
	   'sms_text'=>$post_data['sms_text'],
	   'expire_date_time'=>$post_data['exp_date'],
	   'sms_vendor_id'=>$post_data['vendor_list'],
		);
		
		$success=$this->db->where('id',$id)->update('sms_config',$sms_config_array);
		if($success)
		{
			$result['successMessage']='Configuration successfully updated';
		}
		else
		{
			$error=true;
			$errortext='Insert error';
		}
	 
	
	  return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	 
 }
	
 public function AddNewVendor($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 
     $school_validation=$this->Common_model->CheckUniqueItem($post_data);	 
	 
	 if(!$school_validation['error'])
	 {
		 
	   $is_active="";
	   if($post_data['inlineRadioOptions']=='option1')
	   {
		   $is_active=1;
	   }
	   else
	   {
		   $is_active=0;
	   }
	   
	 
	   $name=$post_data['name'];
	   
	   $insert_arr=array
	   (
		 'name'=>$name,
		 'address'=>$post_data['address1'],
		 'date_time'=>date("Y/m/d h:i:s A"),
		 'Is_active'=> $is_active,
	   );
	   //echo '<pre>',print_r($insert_arr);
	   //die();

	   $success=$this->db->insert('vendor',$insert_arr);
	   if($success)
	   {	
	       $unique_id=$this->db->insert_id();
		   $result['successMessage']="Success! Vendor : $name";
		   
		   $login_data=array(
		   'unique_id'=>$unique_id,
		   'password'=>md5($post_data['password']),
		   'email'=>$post_data['email'],
		   'phone_no'=>$post_data['phone'],
		   'role'=>2,
		   );
		   $insert_login_data=$this->Common_model->InsertLoginCredentials($login_data);
	   }
	   else
	   {
		 $error=true;
		 $errortext='Insert error';
	   }
				   
	 
	 }
	 else
	 {
		 $error=true;
		 $errortext=$school_validation['errortext'];
	 }
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}	

 public function GetVendorCommon()
 {
	 $vendor_list=array();
	 $vendor_search='';
	 if($this->input->post('search_text'))
	 {
		 $sc_name=$this->input->post('search_text');
		 $vendor_search=" Where name LIKE '%$sc_name%'"; 
	 }
	 $sql="Select * from vendor $vendor_search";
	 
	 return $sql;
 }
 
 
  public function getVendorRows()
  {
	 $vendor_list_count=0;
	 $sql=$this->GetVendorCommon();
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		$vendor_list_count=$rs->num_rows();
	 }
	 
	 return $vendor_list_count;
  }

	
 public function GetVendorList($offset,$perpage)
 {
	 $sql=$this->GetVendorCommon();
	 $limit_query=" LIMIT $offset, $perpage";
	 $sql2=$sql.$limit_query;
	 $rs=$this->db->query($sql2);
	 //echo $this->db->last_query();
	 //die();
	 if($rs->num_rows()>0)
	 {
		 $vendor_list=$rs->result_array();
	 }
	 
	 return $vendor_list;
 }	
 
 
  public function GetVendorList1()
 {
	 $sql=$this->GetVendorCommon();
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		 $vendor_list=$rs->result_array();
	 }
	 
	 return $vendor_list;
 }
 
 
 public function SchoolListCommon()
 {
	
	$school_search='';
	 if($this->input->post('search_text'))
	 {
		 $sc_name=$this->input->post('search_text');
		 $school_search=" Where name LIKE '%$sc_name%'"; 
	 }
	 
	 $sql="Select * from school $school_search"; 
	 
	 return $sql;
 }
 
  public function getSchoolRows()
  {
	 $school_list_count=0;
	 $sql=$this->SchoolListCommon();
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		$school_list_count=$rs->num_rows();
	 }
	 
	 return $school_list_count;
  }
  
  public function smsConfigCommon($search_text)
  {
	  $school_search='';
	  
	  if($search_text!="")
	  {
		  $school_search=" Where school.name LIKE '%$search_text%'";
	  }
	  
	  $sql="Select * From sms_config INNER JOIN school ON sms_config.school_id=school.id $school_search";
	  
	  return $sql;
  }
  
  public function getTotalSmsConfig()
  {
	 $sms_config_total=0;
	  
	 $search_text="";
	 if($this->input->post('search_text'))
	  {
		  $search_text=$this->input->post('search_text');
	  }
	 
	 $sql=$this->smsConfigCommon($search_text);
	 
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		 $sms_config_total=$rs->num_rows();
	 }
	 
	 return $sms_config_total;
  }
  
  
 
 public function GetSmsConfigList($offset,$perpage)
 {
	 $sms_config_arr=array();
	 //$rs=$this->db->select('*')->limit($perpage,$offset)->get('sms_config');
	 
	 $search_text="";
	 if($this->input->post('search_text'))
	  {
		  $search_text=$this->input->post('search_text');
	  }
	 
	 $sql=$this->smsConfigCommon($search_text);
	 $limit_query=" LIMIT $offset, $perpage";
	 $sql2=$sql.$limit_query;
	 
	 $rs=$this->db->query($sql2);
	 if($rs->num_rows()>0)
	 {
		 $sms_config_arr=$rs->result_array();
	 }
	 
	 return $sms_config_arr;
 }
 

 public function GetSmsConfigDetailsOnId($id)
 {
	 
	 $sms_config_arr=array();
	 $rs=$this->db->select('*')->where('id',$id)->get('sms_config'); 
	 if($rs->num_rows()>0)
	 {
		 $sms_config_arr=$rs->row_array();
	 }
	 
	 return $sms_config_arr;
	 
 }
 
 public function GetSmsVendorOnId($id)
 {
	 
	 $sms_config_arr=array();
	 $rs=$this->db->select('*')->where('id',$id)->get('sms_vendor'); 
	 if($rs->num_rows()>0)
	 {
		 $sms_config_arr=$rs->row_array();
	 }
	 
	 return $sms_config_arr;
	 
 }
 
 
 public function AddNewSchool($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 
     $school_validation=$this->Common_model->CheckUniqueItem($post_data);	 
	 
	 if(!$school_validation['error'])
	 {
		 
		 $rs=$this->db->select('*')->where('short_name',$post_data['short_name'])->where('vendor_id',$this->Basic_model->unique_id)->get('school');
		 if($rs->num_rows()>0)
		 {
			   $error=true;
			   $errortext='School shortname already exist';
		 } 
	   	 
		 if(!$error)
		 {
		   $is_active="";
		   if($post_data['inlineRadioOptions']=='option1')
		   {
			   $is_active=1;
		   }
		   else
		   {
			   $is_active=0;
		   }
		   
		 
		   $name=$post_data['name'];
		   
		   $insert_arr=array
		   (
			 'vendor_id'=>$post_data['vendor'],
			 'name'=>$name,
			 'short_name'=>$post_data['short_name'],
			 'address'=>$post_data['address1'],
			 'city_village'=>$post_data['city_village'],
			 'country_id'=>$post_data['country'],
			 'state'=>$post_data['state'],
			 'pin_code'=>$post_data['pin'],
			 'date_time'=>date("Y/m/d h:i:s A"),
			 'is_active'=> $is_active,
		   );
		   //echo '<pre>',print_r($insert_arr);
		   //die();
		   
		   
		   $success=$this->db->insert('school',$insert_arr);
		   if($success)
		   {   $unique_id=$this->db->insert_id();
			   $result['successMessage']="Success! School : $name";
			   
			   $login_data=array(
			   'unique_id'=>$unique_id,
			   'password'=>md5($post_data['password']),
			   'email'=>$post_data['email'],
			   'phone_no'=>$post_data['phone'],
			   'role'=>3,
			   );
			   $insert_login_data=$this->Common_model->InsertLoginCredentials($login_data);
			   
			   $master_role_data=array(
				   'parent_role_id'=>0,
				   'object_id'=>$unique_id,
				   'role_name'=>'Admin',
				   'is_active'=>1,
			     );
			  $success=$this->db->insert('role_master',$master_role_data);	
		   }
		   else
		   {
			 $error=true;
			 $errortext='Insert error';
		   }
		 }
	 
	 }
	 else
	 {
		 $error=true;
		 $errortext=$school_validation['errortext'];
	 }
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}
 
 
 
 
 
 public function GetSchoolList_1()
 {
	 $sql2="";
	 $school_list=array();
	 $sql=$this->SchoolListCommon();
	 $rs=$this->db->query($sql);
	
	 if($rs->num_rows()>0)
	 {
		 $school_list=$rs->result_array();
	 }
	 
	 return $school_list;
 }
 
 
 public function GetSmsVendorList()
 {
	 $sql2="";
	 $vendor_list=array();
	 $rs=$this->db->get('sms_vendor');
	 if($rs->num_rows()>0)
	 {
		 $vendor_list=$rs->result_array();
	 }
	 
	 return $vendor_list;
 }
 
 public function GetSchoolList($offset,$perpage)
 {
	 $sql2="";
	 $school_list=array();
	 $sql=$this->SchoolListCommon();
	 
	 $limit_query=" LIMIT $offset, $perpage";
	 $sql2=$sql.$limit_query;
	 
	 $rs=$this->db->query($sql2);
	
	 if($rs->num_rows()>0)
	 {
		 $school_list=$rs->result_array();
	 }
	 
	 return $school_list;
 }
 
 
  public function GetVendorDetailsOnId($id)
  {
	  $vendor_data=array();
	  $rs=$this->db->select('*')->where('id',$id)->get('vendor');
	  if($rs->num_rows()>0)
	   {
		   $vendor_data=$rs->row_array();
	   }
	   
	   return $vendor_data;   
  }
  
  
  public function GetSmsVendorDetailsOnId($id)
  {
	  $vendor_data=array();
	  $rs=$this->db->select('*')->where('id',$id)->get('sms_vendor');
	  if($rs->num_rows()>0)
	   {
		   $vendor_data=$rs->row_array();
	   }
	   
	   return $vendor_data;   
  }
 
 public function UpdateVendorData($id,$post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	
	  $vendor_validation=$this->Common_model->CheckUniqueItem_edit($post_data,$id);
	  if($vendor_validation['error'])
	  {
		$error=true;
		$errortext=$vendor_validation['errortext'];
	  }
	  
	  if(!$error)
	  {
	     $is_active="";
	   if($post_data['inlineRadioOptions']=='option1')
	   {
		   $is_active=1;
	   }
	   else
	   {
		   $is_active=0;
	   }
	   
	   $name=$post_data['name'];
	   
	   //echo '<pre>',print_r($post_data);
	   
	   $update_arr=array
	   (
		 'name'=>$name,
		 'address'=>$post_data['address1'],
		 'update_date_time'=>date("Y/m/d h:i:s A"),
		 'is_active'=> $is_active,
	   );
	   
	   //echo '<pre>',print_r($update_arr);
	   //die();
	   $role=2;
	   if($post_data['password']=="")
	   {
		   $login_data=array(
			'email'=>$post_data['email'],
			'phone_no'=>$post_data['phone'],
		   );
	   }
	   else
	   {
		   $login_data=array(
			'password'=>md5($post_data['password']),
			'email'=>$post_data['email'],
			'phone_no'=>$post_data['phone'],
		   );
		   
	   }
	   $insert_login_data=$this->Common_model->UpdateLoginCredentials($login_data,$role,$id);
	   
	   
	   $success=$this->db->where('id',$id)->update('vendor',$update_arr);
	   if($success)
	   {
		   $result['successMessage']="Data successfully Updated";
	   }
	   else
	   {
		 $error=true;
		 $errortext='update error';
	   }	   
	  }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}
	
 
 
 public function GetTotalStudents($id)
 {
	 $total_students=0;
	 $rs=$this->db->select('*')->where('school_id',$id)->get('student');
	  if($rs->num_rows()>0)
	   {
		  $total_students=$rs->num_rows();
	   }
	   
	  return $total_students; 
 }
 
 public function GetClassPermissionList($id)
 {
	 $controller_list=array();
	 $rs=$this->db->select('*')->get('module');
	  if($rs->num_rows()>0)
	   {
		  $controller_list=$rs->result_array();
	   }
	   
	  return $controller_list; 
 }
 
 public function GetRestClassPermissionList($id)
 {
	 $controller_list=array();
	 $rs=$this->db->select('*')->get('module');
	  if($rs->num_rows()>0)
	   {
		  $controller_list=$rs->result_array();
	   }
	   
	  return $controller_list; 
 }
 
 public function ClassDataOnId($id)
 {
	 $controller_list=array();
	 $rs=$this->db->select('*')->where('id',$id)->get('module');
	  if($rs->num_rows()>0)
	   {
		  $controller_list=$rs->row_array();
	   }
	   
	  return $controller_list; 
 }
 
 public function SubmitClassPermissionList($id,$post_data)
 {
	
	if(!empty($post_data))
	{
		$permission_count=count($post_data['permission']);
		if($permission_count>0)
		{
			$rs=$this->db->where('client_id',$id)->delete('site_class_permission');
			for($i=0;$i<=$permission_count-1;$i++)
			{
				$insert_array=array('client_id'=>$id,'module_id'=>$post_data['permission'][$i]);
				$success=$this->db->insert('site_class_permission',$insert_array);
			}
		}
	}
	
	return $success;
		
 }

 
 public function SubmitRestClassPermissionList($id,$post_data)
 {
	$success=$this->db->where('client_id',$id)->delete('rest_class_permission');
	if(!empty($post_data))
	{
		$permission_count=count($post_data['permission']);
		if($permission_count>0)
		{
			for($i=0;$i<=$permission_count-1;$i++)
			{
				$insert_array=array('client_id'=>$id,'module_id'=>$post_data['permission'][$i]);
				$success=$this->db->insert('rest_class_permission',$insert_array);
			}
		}
	}
	
	return $success;
		
 }
 
 
public function GetMethodList($module_id)
{
	$method_array=array();
	$rs=$this->db->select('*')->where('module_id',$module_id)->get('method');
	if($rs->num_rows()>0)
	   {
		  $method_array=$rs->result_array();
	   }
	   
	return $method_array;   
	
}

public function GetMethodDetails($id)
{
	$method_array=array();
	$rs=$this->db->select('*')->where('id',$id)->get('method');
	if($rs->num_rows()>0)
	   {
		  $method_array=$rs->row_array();
	   }
	   
	return $method_array;   
	
}

public function GetParentMethodList($module_id,$method_id)
{
	$method_array=array();
	$rs=$this->db->select('*')->where('id!=',$method_id)->where('module_id',$module_id)->get('method');
	if($rs->num_rows()>0)
	   {
		  $method_array=$rs->result_array();
	   }
	   
	return $method_array;   
	
}


public function FindParentId($module_id,$method_id)
{
	$method_array=array();
	$parent_id='';
	$rs=$this->db->select('*')->where('id',$method_id)->where('module_id',$module_id)->get('method');
	if($rs->num_rows()>0)
	   {
		  $method_array=$rs->row_array();
		  $parent_id=$method_array['id'];
	   }
	   
	return $parent_id;   
	
}

public function SubmitParentChild($post_data)
{
	$module_id=$post_data['module_id'];
	$method_id=$post_data['method_id'];
	
	if(isset($post_data['parent_method_id']))
	{
	   $parent_method_id=$post_data['parent_method_id'];
	}
	else
	{
		$parent_method_id=0;
	}
	
   $success=$this->db->where('module_id',$module_id)->where('id',$method_id)->update('method',array('parent_id'=>$parent_method_id));
   return $success;
}


public function AddNewModule($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 $class_name=trim($post_data['class_name']);
	 $display_name=trim($post_data['display_name']);
	 
	 if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $class_name))
	  {
			$class_name=strtolower($class_name);
			$rs=$this->db->select('*')->where('class_name',$class_name)->get('module');
			if(!$rs->num_rows()>0)
			{
				
				  $insert_array=array(
					 'class_name'=>$class_name,
					 'module_name'=>$display_name,
					
					);
				
				  $success=$this->db->insert('module',$insert_array);
				
				
			}
			else
			{
			  $error=true;
		      $errortext='Class name already exist';
			}
	  }
	  else
	  {
		 $error=true;
		 $errortext='No special characters not allowed';
	  }
	  
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
	
public function UpdateModule($post_data,$id)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 $class_name=trim($post_data['class_name']);
	 $display_name=trim($post_data['display_name']);
	 
	 if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $class_name))
	  {
			$class_name=strtolower($class_name);
			$rs=$this->db->select('*')->where('id!=',$id)->where('class_name',$class_name)->get('module');
			if(!$rs->num_rows()>0)
			{
				
				  $update_array=array(
					 'class_name'=>$class_name,
					 'module_name'=>$display_name,
					
					);
				
				  $success=$this->db->where('id',$id)->update('module',$update_array);
				
				
			}
			else
			{
			  $error=true;
		      $errortext='Class name already exist';
			}
	  }
	  else
	  {
		 $error=true;
		 $errortext='No special characters not allowed';
	  }
	  
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
	
public function UpdateMethod($post_data,$id)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 $display_name=trim($post_data['display_name']);
	 
	 $update_array=array(
 	  'method_description'=>$display_name,
	
	);
				
   $success=$this->db->where('id',$id)->update('method',$update_array);
	  
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function ApplyParentMethod($post_data)
 {
	 $method_id=$post_data['method_id'];
	 $parent_id=$post_data['parent_id'];
	 $method_id_count=count($method_id);
	 for($i=0;$i<=$method_id_count-1;$i++)
	 {
		 if(isset($parent_id[$i]))
		 {
			 $parent_method_id=$parent_id[$i];
			 
			 $module_id=$this->Common_model->get_single_field_value('method','module_id','id',$method_id[$i]);
			 $success=$this->db->where('module_id',$module_id)->where('id',$method_id[$i])->update('method',array('parent_id'=>$parent_method_id));
		 }
	 }
	 
	 return $success;
	 
 }
	

 
}
?>