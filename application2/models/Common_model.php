<?php
class Common_model extends CI_Model
{
	
	
   public function get_parent_role()
	 {
		$parent_role_id=''; 
		$rs2=$this->db->select('*')->where('parent_role_id',0)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
		if($rs2->num_rows()>0)
		{
			$role_array=$rs2->row_array();
			$parent_role_id=$role_array['id'];
		}
		
		return $parent_role_id;
	 }	
	 
	 
	 
  function roleParentChildTree($parent, $category_tree_array = '')
  {
	  $branch=array();
	  $client_id=$this->Basic_model->unique_id;
	  if (!is_array($category_tree_array))
	  {
		$category_tree_array = array();
	  }
	   $sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
	   INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role Where role_master.object_id=$client_id and sub_role_details.is_active=1
	   and role_master.is_active=1 and sub_role_details.report_to=$parent order by sub_role_details.id asc";
			
		//$sql="Select * from sub_role_details Where report_to=$parent order by id asc";	
			
	  $rs=$this->db->query($sql);
	  if($rs->num_rows()>0)
	  {
		  $role_array=$rs->result_array();
		  foreach($role_array as $item)
		  {
			  $category_tree_array[]= array(
			                   "id" => $item['role'], 
							   "name" => $item['name'],
							   "role_name" => $item['role_name'],
							   "order_status_id" => $item['order_status_id'],
							   "status_text" => $item['status_text'],
							   );
			 			   
			  $category_tree_array = $this->roleParentChildTree($item['role'],$category_tree_array);
		  }
	  }
	  
	  return $category_tree_array;
  }
	 
	 
	 
	 
	 
	 

  public function get_role_permission($parent_id=0,$module='')
  {
	 $error='';
	 $errortext='';
	 $result='';
	  
	  $sub_unique_id=$this->Basic_model->sub_unique_id;
	  //$sub_unique_id=1;
	  $unique_id=$this->Basic_model->unique_id;
	  $role=$this->Basic_model->role;
	  $get_parent_role=$this->get_parent_role();
	  
	  $result=array();
	  
	  $module_query='';
	  if($module!='')
	  {
		  $module_query ="AND method.module_id=(SELECT id FROM module WHERE class_name='$module')";
	  }
	  
	  $check_active=$this->db->select('*')->where('id',$unique_id)->where('Is_active',1)->get('school');
	  if($check_active->num_rows()>0)
	  {	
 	      $sql="Select *,module_permission.id as 'permission_id' from role_master 
		  INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id
		  INNER JOIN module_permission on sub_role_details.id=module_permission.sub_role_details_id
		  INNER JOIN method ON module_permission.method_id=method.id 
		  INNER JOIN module ON method.module_id=module.id
		  WHERE module.is_active=1 
		  $module_query 
		  AND method.is_active=1 
		  AND method.parent_id=$parent_id 
		  AND sub_role_details.id=$sub_unique_id 
		  AND role_master.parent_role_id=$get_parent_role  
		  AND sub_role_details.is_active=1 
		  AND role_master.is_active=1 
		  AND role_master.object_id=$unique_id";		  
	
		 $rs=$this->db->query($sql);
	
		 $url_details=array();
		 if($rs->num_rows()>0)
		 {
			  $module_method_array=$rs->result_array();
			  foreach($module_method_array as $row)
			  {
				 $url="";
				 $url= $row['method_name'];
				 $url_details[]=array(
				   'display_name'=>$row['method_description'],
				   'api'=>$url,
				   'type'=>$row['type'],
				   'menu_type'=>$row['menu_type'],
				 );
			  }
			  
			  $result['menu_link']=$url_details;
		 }
		 else
		 {
			 $error=true;
			 $errortext='No permission is set';
		 }
	  }
	  else
	  {
		  $error=true;
		  $errortext='User is inactive';
	  }
	  
	
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	
		
  }
  
   public function permission_url($role_permission_array,$result,$request_uri,$type)
  {
	  $permission="";
	  $url="#";
	  $raw_request_uri="";
	
	  //echo $request_uri='school/st_class_list'.'----->';
	  $unique_id=$role_permission_array['unique_id'];
	  $sub_unique_id=$role_permission_array['sub_unique_id'];
	  $role=$this->Basic_model->role;
	  
	  if($unique_id!="")
	  {
		  if($sub_unique_id!=0)
		  {
			  if(!empty($result) && $role!=4)
			  {
				$raw_request_array=explode('/',$request_uri); 
				$method_name='';
				if(isset($raw_request_array[1]))
				{
					$method_name=$raw_request_array[1];
				}
				else
				{
					$method_name='index';
				}
				
				
				$raw_request_uri=$raw_request_array[0].'/' .$method_name;
				
				//echo '<pre>',print_r($result);
				//die();
				if(!$result['error'])
				{
					$menu_link=$result['result']['menu_link'];
					if(!empty($menu_link))
					{
						foreach($menu_link as $item)
						{
							
						  $permission_uri="";
						  $permission_uri=$item['url'];
						  if($raw_request_uri==$permission_uri)
						  {
							  if($item['type']==$type)
							  {
								$permission=true;
								 break;
							  }
							 
						  }
						 
						}
					}
				}
			  }
			  else
			  {
				  $permission=false;
			  }
		  }
		  else
		  {
			  $permission=true;
		  }
	  }
	  else
	  {
		  $permission=false;
	  }
	  
	  
	  
	  return $permission;
	  //return $url;
	  
  }
  
  public function Create_url($result,$request_uri)
  {
	  $permission="";
	  $url="#";
	  $raw_request_uri="";
	  
	  //echo $request_uri='school/st_class_list'.'----->';
	  if(!empty($result))
	  {
		$raw_request_array=explode('/',$request_uri); 
		$raw_request_uri=$raw_request_array[0].'/' .$raw_request_array[1];
		foreach($result as $item)
		{
		  	
		  $permission_uri="";
		  $permission_uri=$item['class_name']."/".$item['method_name'];
		  if($raw_request_uri==$permission_uri)
		  {
			  $permission=true;
			  $url=base_url($request_uri);
			  break;
		  }
		 
		}
	  }
	  else
	  {
		  $url=base_url($request_uri);
	  }
	  
	  return $url;
	  //return $url;
	  
  }
  
  public function send_sms($mobile_no,$final_message)
  {
	 $error=false;
	 $errortext='';
	 $result='';
		 
	 $this->load->model('Sms_model');
	 $authentication=$this->Sms_model->send_sms($mobile_no,$final_message);
	 if($authentication['error'])
	 {
	  $error=true;
	  $errorText=$authentication['errortext'];
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
  }
  
  
   public function send_email($send_email_array)
   {
		 $error='';
		 $errortext='';
		 $result='';
		 
		 $email=$send_email_array['email'];
		 $message=$send_email_array['message'];
		 $subject=$send_email_array['subject'];
		 
		 $client_id=$this->Basic_model->unique_id;
		 $token_id=$this->Basic_model->token_id;
		 
		 // need to verify if email sending to customer or client roles
		 
		 $storeEmail = $this->Common_model->get_single_field_value('smtp_setting','store_email','id',1);
		 $storeName = $this->Common_model->get_single_field_value('smtp_setting','store_name','id',1);
		 
		/* if($token_id=="")
		 {
		    
			 
			 $storeEmail = $this->Common_model->get_single_field_value('smtp_setting','store_email','id',1);
		     $storeName = $this->Common_model->get_single_field_value('smtp_setting','store_name','id',1);
		 }
		 else
		 {
			 $storeEmail = $this->Common_model->get_single_field_value('client_info','store_email','client_id',$client_id);
		     $storeName = $this->Common_model->get_single_field_value('client_info','store_name','client_id',$client_id);
		 }*/
		 $to=$email;
		
		 $smtp_host=$this->Common_model->get_single_field_value('smtp_setting','smtp_host','id',1);
		 $smtp_user=$this->Common_model->get_single_field_value('smtp_setting','smtp_user','id',1);
		 $smtp_port=$this->Common_model->get_single_field_value('smtp_setting','smtp_port','id',1);
		 $smtp_pass=$this->Common_model->get_single_field_value('smtp_setting','smtp_pass','id',1);
		
		 $config = array(
			'protocol' => 'smtp',
			'smtp_host' => $smtp_host,
			'smtp_user' => $smtp_user,
			'smtp_port' => $smtp_port,
			'smtp_pass' => $smtp_pass,
			'mailtype'  => 'html', 
			'charset'   => 'iso-8859-1'
		 );
		
		$this->load->library('encrypt');
		 $this->load->library('email', $config);
		 $this->load->library('parser');
		 $this->email->clear();
		 $config['mailtype'] = "html";
		 $this->email->initialize($config);
		 $this->email->set_newline("\r\n");
		 $this->email->from($storeEmail,$storeName);
		 $this->email->to($to);
		 $this->email->reply_to($storeEmail,$storeName);
		 $this->email->subject($subject);
		 $this->email->message($message);
		
				
		if($this->email->send())
		{
			$result['successMessage']='Send successfully';
		}
		else
		{
			//print_r($this->email->print_debugger());
			//die();
			$error = true;
			$errortext = 'Email not send';
		}
		 
		 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	 }
  
  public function method_validation($result,$request_uri)
  {
	  $permission=false;
	 
	  //echo $request_uri='school/st_class_list'.'----->';
	  if(!empty($result))
	  {
		foreach($result as $item)
		{
		  $permission_uri="";
		  $permission_uri=$item['class_name']."/".$item['method_name'];
		  if($request_uri==$permission_uri)
		  {
			  $permission=true;
			  break;
		  }
		 
		}
	  }
	  
	  return $permission;
	  
  }

  public function time_zone()
  {  
	  $zones_array = array();
	  $timestamp = time();
	  foreach(timezone_identifiers_list() as $key => $zone) {
		date_default_timezone_set($zone);
		$zones_array[$key]['zone'] = $zone;
		$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
	  }
	  return $zones_array;
  }


	
 public function get_single_field_value($tablename, $fieldname, $condition, $conditionvalue)
 {
		$this->db->select($fieldname);
		$this->db->where($condition, $conditionvalue);
		$query = $this->db->get($tablename);
		
		//echo $this->db->last_query();
		//die();
		
		if($query->num_rows()>0){
			$data = $query->row_array();
			return $data[$fieldname];
		}
		else
		{
			return false;
		}
		
}

    
	
 public function get_field_value($tablename, $fieldnameArray, $condition, $conditionvalue)
 {
	
		$fieldname = implode(",", $fieldnameArray);
		$this->db->select($fieldname);
		$this->db->where($condition, $conditionvalue);
		$query = $this->db->get($tablename);
		
		if($query->num_rows()>0){
			$data = $query->row_array();
			return $data;
		}
		else
		{
			return false;
		}		
 }
 
 
  
 
  public function InsertLoginCredentials($login_data)
  {
	  $success=$this->db->insert('login',$login_data);
	  $last_insert_id=$this->db->insert_id();
	  
	  return $last_insert_id;
  }
  
  public function UpdateLoginCredentials($login_data,$role,$id)
  {
	  $success=$this->db->where('role',$role)->where('unique_id',$id)->where('sub_unique_id',"")->update('login',$login_data);
	 	  
	  return $success;
  }
  
  public function GetLoginDetails($id,$role)
  {
	  $login_data=array();
	  $rs=$this->db->select('*')->where('role',$role)->where('unique_id',$id)->get('login');
	  if($rs->num_rows()>0)
	  {
		  $login_data=$rs->row_array();
	  }
	  
	  return $login_data;
  }
 
 public function CheckUniqueItem($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 
	 $rs=$this->db->select('*')->where('email',$post_data['email'])->get('login');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Email already exist';
	 }
	 
	 if(!$error)
	 {
		   $rs=$this->db->select('*')->where('phone_no',$post_data['phone'])->get('login');
		   if($rs->num_rows()>0)
		   {
			 $error=true;
			 $errortext='Phone already exist'; 
		   }
	 }
 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function CheckUniqueItem_edit($post_data,$id)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 
	 $rs=$this->db->select('*')->where('unique_id!=',$id)->where('email',$post_data['email'])->get('login');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Email already exist';
	 }
	 
	
   $rs=$this->db->select('*')->where('unique_id!=',$id)->where('phone_no',$post_data['phone'])->get('login');
   if($rs->num_rows()>0)
   {
	 $error=true;
	 $errortext='Phone already exist'; 
   }
	 
 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }	
	
}
?>