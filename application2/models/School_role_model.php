<?php
class School_role_model extends CI_Model
{

 public function GetSchoolRole()
 {
	 $school_role_array=array();
	 $parent_role_id=$this->get_parent_role();
	 $rs=$this->db->select('*')->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		$school_role_array=$rs->result_array(); 
	 }
	 
	 return $school_role_array;
 }
 
 public function GetSchoolRolePerson()
 {
	 $school_role_array=array();
	 $unique_id=$this->Basic_model->unique_id;
	 $parent_role_id=$this->get_parent_role();
	 
	 $sql="Select *,sub_role_details.id as 'sub_role_details_id' from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id
	 	  Where role_master.parent_role_id=$parent_role_id AND role_master.object_id=$unique_id ORDER BY sub_role_details.name ASC";
	 $rs=$this->db->query($sql);
	 
	 //$rs=$this->db->select('*')->where('parent_role_id',3)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		$school_role_array=$rs->result_array(); 
		$role_array=array();
		if(!empty($school_role_array))
          {
              foreach($school_role_array as $row)
              {
                  $person_name=$row['name'];
				  $role_name=$row['role_name'];
                  $id=$row['sub_role_details_id'];
				  $show_name=$person_name."( ". $role_name." )";
				  
				  $role_array[]=array('id'=>$id,'name'=>$show_name);
              }
          }
	 }
	 
	 return $role_array;
 }
 
 public function GetActiveSchoolRole()
 {
	 $school_role_array=array();
	 $parent_role_id=$this->get_parent_role();
	 
	 $rs=$this->db->select('*')->where('parent_role_id',$parent_role_id)->where('is_active',1)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		$school_role_array=$rs->result_array(); 
	 }
	 
	 return $school_role_array;
 }
 
 
 public function GetSchoolRoleOnId($id)
 {
	 $school_role_array=array();
	 $parent_role_id=$this->get_parent_role();
	 
	 $rs=$this->db->select('*')->where('parent_role_id',$parent_role_id)->where('id',$id)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		$school_role_array=$rs->row_array(); 
	 }
	 
	 return $school_role_array;
 }
 
 
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
	
 public function InsertSchoolRole($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
	$name=$post_data['name'];
	
	$parent_role_id=$this->get_parent_role();
	
	$rs=$this->db->select('*')->where('parent_role_id',$parent_role_id)->where('role_name',$name)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Role already exist';
	 }
	if(!$error)
	{
    	$is_active="";
		if($post_data['is_active']==true)
		{
		   $is_active=1;
		}
		else
		{
		   $is_active=0;
		}
	

	$insert_role_arr=array(
	  'parent_role_id'=>$parent_role_id,
	  'object_id'=>$this->Basic_model->unique_id,
	  'role_name'=>$name,
	  'is_active'=>$is_active,
	  );
	  
	$success=$this->db->insert('role_master',$insert_role_arr);
	if($success)
	{
		 $result['successMessage']="Success! Role : $name - successfully added";
	}
	else
	{
		$error=true;
		$errortext='Error in insert';
	}
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 
 public function UpdateSchoolRole($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
    $id=$post_data['role_id'];
	$name=$post_data['name'];
	$parent_role_id=$this->get_parent_role();
	
	$rs=$this->db->select('*')->where('id',$id)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	if(!$rs->num_rows()>0)
	{
	   $error=true;
	   $errortext='Role not found';
	}
	
	
	
	$rs=$this->db->select('*')->where('id!=',$id)->where('parent_role_id',$parent_role_id)->where('role_name',$name)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Role already exist';
	 }
	
	
	$is_active="";
	if($post_data['is_active']==true)
    {
	   $is_active=1;
    }
    else
    {
	   $is_active=0;
    }
	
	$update_arr=array(
	  'role_name'=>$name,
	  'is_active'=>$is_active,
	  );
	  
	
	$success=$this->db->where('id',$id)->where('parent_role_id',$parent_role_id)->update('role_master',$update_arr);
	if($success)
	{
		 $result['successMessage']="Data successfully updated";
	}
	else
	{
		$error=true;
		$errortext='Error in insert';
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function delete_role_person($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	 
	$id=$post_data['person_id'];
	
	$rs=$this->db->select('*')->where('id',$id)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
	if(!$rs->num_rows()>0)
	{
	   $error=true;
	   $errortext='Role not found';
	}
	
	$rs=$this->db->select('*')->where('role',$id)->get('order_status_master');
	if($rs->num_rows()>0)
	{
	   $error=true;
	   $errortext='You can,t delete this role, this role is already assign';
	}
	
	if(!$error)
	{
		$this->db->where('role',3)->where('sub_unique_id',$id)->where('unique_id',$this->Basic_model->unique_id)->delete('login');
		$success=$this->db->where('id',$id)->delete('sub_role_details');
		if(!$success)
		{
			$error=true;
			$errortext='Error in delete';
		}
		else
		{
			$result['successMessage']='Successfully deleted';
		}
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
}
 
 public function InsertRolePerson($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
    $client_id=$this->Basic_model->unique_id;
	$role_id=$post_data['role_id'];
	
	if($post_data['report_to']!="")
	{
	    $report_to_id=$post_data['report_to'];
	}
	else
	{
		$report_to_id=0;
	}
	
	
	$rs=$this->db->select('*')->where('id',$role_id)->where('object_id',$client_id)->get('role_master');
	if(!$rs->num_rows()>0)
	{
	   $error=true;
	   $errortext='Role not found';
	}
	
	
	if(!$error)
	{
		 if($report_to_id!=0)
		 {
			 $unique_id = $this->Basic_model->unique_id;// client id
			 $parent_role_id = $this->get_parent_role();
			 
			 $sql="Select *,sub_role_details.id as 'sub_role_details_id' from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id
				  Where role_master.parent_role_id=$parent_role_id AND role_master.object_id=$unique_id AND sub_role_details.id=$report_to_id";
			 $rs=$this->db->query($sql);
			 if(!$rs->num_rows()>0)
			 {
			   $error=true;
			   $errortext='Invalid report to person';
			 }
		 }
	}
	
	if(!$error)
	{
		$student_validation=$this->Common_model->CheckUniqueItem($post_data); 
		if(!$student_validation['error'])
		{
			/******** default user checking ******/
			/* if($post_data['is_default']==true)
			 {
			   $is_default=1;
			   $sql="Select * from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id Where
			   role_master.object_id=$client_id And sub_role_details.is_default=$is_default";
			   $rs=$this->db->query($sql);
			   if($rs->num_rows()>0)
			   {
				 $error=true;
				 $errortext='Default employee already set';
			   }
			 }
			 else
			 {
			   $is_default=0;
			 }*/
			/******** /default user checking ******/ 
			/* $is_active="";
			 if($post_data['is_active']==true)
			   {
				   $is_active=1;
			   }
			   else
			   {
				   $is_active=0;
			   }*/
			  
			 //$name=$post_data['name'];  
			 //$role='';
			 //$role=$this->Common_model->get_single_field_value('role_master','role_name','id',$post_data['role_id']);
			 $role_arr=array
			   (
				 'role_master_id'=>$post_data['role_id'],
				 'report_to'=>$post_data['report_to'],
				 'name'=>$post_data['name'],
				 'address'=>$post_data['address'],
				 'city'=>$post_data['city'],
				 'state'=>$post_data['state'],
				 'country'=>$post_data['country'],
				 'postal_code'=>$post_data['postal_code'],
				// 'is_default'=> $is_default,
				 'is_active'=> $post_data['is_active'],
			   );
			   
			  // echo '<pre>',print_r($insert_arr);
			   //die();
			   $success=$this->db->insert('sub_role_details',$role_arr);
			   if($success)
			   {
				   $result['successMessage']="Success";
				   $sub_unique_id=$this->db->insert_id();
				  
				  
				   $parent_role_id=3;
				   $login_data=array(
					 'unique_id'=>$this->Basic_model->unique_id,
					 'password'=>md5($post_data['password']),
					 'email'=>$post_data['email'],
					 'phone_no'=>$post_data['phone'],
					 'role'=>$parent_role_id,
					 'sub_unique_id'=>$sub_unique_id,
				   );
				   $insert_login_data=$this->Common_model->InsertLoginCredentials($login_data);
			   }
		}
		else
		{
			$error=true;
			$errortext='Insert error';
		}
	}
	 
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function GetRolePersons()
 {
	 $role_person_array=array();
	 $school_id=$this->Basic_model->unique_id;
	 
	 $parent_role_id=$this->get_parent_role();
	 $sql="Select * from 
	 		role_master 
		INNER JOIN 
			sub_role_details 
		ON role_master.id=sub_role_details.role_master_id 
	    Where role_master.parent_role_id=$parent_role_id And role_master.object_id=$school_id";
		   
     $rs=$this->db->query($sql);
	// $rs=$this->db->select('*')->where('school_id',$this->Basic_model->unique_id)->get('sub_role_details');
	 if($rs->num_rows()>0)
	 {
		$role_person_array=$rs->result_array(); 
		
	 }
	 
	 return $role_person_array;
 }
 
 
  public function GetRolePersonOnId($id)
  {
	 $role_person_array=array();
	 
	 $school_id=$this->Basic_model->unique_id;
	 
	 $parent_role_id=$this->get_parent_role();
	 $sql="Select * from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id 
	       Where role_master.parent_role_id=$parent_role_id And role_master.object_id=$school_id And sub_role_details.id=$id";
		   
	  $rs=$this->db->query($sql); 	   
	 //$rs=$this->db->select('*')->where('id',$id)->where('school_id',$this->Basic_model->unique_id)->get('role_details');
	 if($rs->num_rows()>0)
	 {
		$role_person_array=$rs->row_array(); 
	 }
	 
	 return $role_person_array;
  }
 
 public function CheckUniqueItem_edit_role($post_data,$id)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 
	 $rs=$this->db->select('*')->where('sub_unique_id!=',$id)->where('unique_id',$this->Basic_model->unique_id)->where('email',$post_data['email'])->get('login');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Email already exist';
	 }
	 
	
   $rs=$this->db->select('*')->where('sub_unique_id!=',$id)->where('unique_id',$this->Basic_model->unique_id)->where('phone_no',$post_data['phone'])->get('login');
   if($rs->num_rows()>0)
   {
	 $error=true;
	 $errortext='Phone already exist'; 
   }
	 
 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 } 
  
 public function UpdateRolePerson($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
	$role_id=$post_data['role_id'];
	$report_to_id=$post_data['report_to'];
	$id=$post_data['person_id'];
	$client_id=$this->Basic_model->unique_id;
	
	$rs=$this->db->select('*')->where('id',$id)->get('sub_role_details');
	if(!$rs->num_rows()>0)
	{
	   $error=true;
	   $errortext='Person not found';
	}
	
	if(!$error)
	{
		$rs=$this->db->select('*')->where('id',$role_id)->where('object_id',$this->Basic_model->unique_id)->get('role_master');
		if(!$rs->num_rows()>0)
		{
		   $error=true;
		   $errortext='Role not found';
		}
	}
	
	if(!$error)
	{
		 $unique_id=$this->Basic_model->unique_id;
		 $parent_role_id=$this->get_parent_role();
		 
		 $sql="Select *,sub_role_details.id as 'sub_role_details_id' from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id
			  Where role_master.parent_role_id=$parent_role_id AND role_master.object_id=$unique_id AND sub_role_details.id=$report_to_id";
		 $rs=$this->db->query($sql);
		 if(!$rs->num_rows()>0)
		 {
		   $error=true;
		   $errortext='Invalid report to person';
		 }
	}
	
	
    if(!$error)
	{
		$student_validation=$this->CheckUniqueItem_edit_role($post_data,$id); 
		if(!$student_validation['error'])
		{
			if($post_data['is_default']==true)
			 {
			   $is_default=1;
			   $sql="Select * from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id Where
			   role_master.object_id=$client_id And sub_role_details.is_default=$is_default";
			   $rs=$this->db->query($sql);
			   if($rs->num_rows()>0)
			   {
				 $error=true;
				 $errortext='Default employee already set';
			   }
			 }
			 else
			 {
			   $is_default=0;
			 }
			 
			
			 $is_active="";
			 if($post_data['is_active']==true)
			   {
				   $is_active=1;
			   }
			   else
			   {
				   $is_active=0;
			   }
			  
			 $name=$post_data['name'];  
			 $role='';
			 $role=$this->Common_model->get_single_field_value('role_master','role_name','id',$post_data['role_id']);
			 
			 $role_arr=array
			   (
				 'role_master_id'=>$post_data['role_id'],
				 'report_to'=>$post_data['report_to'],
				 'name'=>$post_data['name'],
				 'city'=>$post_data['city'],
				 'state'=>$post_data['state'],
				 'country'=>$post_data['country'],
				 'postal_code'=>$post_data['postal_code'],
				 'is_default'=> $is_default,																																																							
				 'is_active'=> $is_active,
			   );
			   
			  // echo '<pre>',print_r($insert_arr);
			   //die();
			   $success=$this->db->where('id',$id)->update('sub_role_details',$role_arr);
			   if($success)
			   {
				   $result['successMessage']="Data successfully updated";
				   
				   $parent_role_id=3;
				   $role=$parent_role_id;
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
				   $update_login_data=$this->db->where('role',$role)->where('sub_unique_id',$id)->where('unique_id',$this->Basic_model->unique_id)->update('login',$login_data);
				   
			   }
		}
		else
		{
			$error=true;
			$errortext=$student_validation['errortext'];
		}
	}
	 
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function GetLoginDetails($id,$role)
  {
	  $login_data=array();
	  $rs=$this->db->select('*')->where('sub_unique_id',$id)->where('role',$role)->where('unique_id',$this->Basic_model->unique_id)->get('login');
	  if($rs->num_rows()>0)
	  {
		  $login_data=$rs->row_array();
	  }
	  
	  return $login_data;
  }
  
  
 public function GetPermissionList()
 {
	 $permission_arr=array();
	 
	 $sql="Select * from module";
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		 $module_array=$rs->result_array();
		 foreach($module_array as $item)
		 {
			 $module_name=$item['module_name'];
			 $module_id=$item['id'];
			 $url_details=array();
			 $sql2="Select *,method.id as 'permission_id' from module INNER JOIN method on module.id=method.module_id Where module.is_active=1
	         And method.is_active=1 and method.module_id=$module_id";
			 $rs=$this->db->query($sql2);
			 if($rs->num_rows()>0)
			 {
				 $module_method_array=$rs->result_array();
				 foreach($module_method_array as $row)
				 {
					 $url="";
					 $url=$row['class_name']."/".$row['method_name'];
					 $url_details[]=array(
					   'method_id'=>$row['permission_id'],
					   'display_name'=>$row['method_description'],
					   'url'=>$url,
					   'type'=>$row['type']
					 );
				 }
				 
			 }
			 
			 $permission_arr[]=array(
			   'module_name'=>$module_name,
			   'module_permission_url'=>$url_details,
			 );
			 
		 }
		 
		 
		 return $permission_arr;
	 }
	 
 
	  return $permission_arr;
 }
 
 
  public function InsertPermission($post_data)
  {
	$error='';
	$errortext='';
	$result='';
	
	$employee_id=$post_data['employee_id'];
	$method_id=$post_data['method_id'];
	
	if($post_data['is_active']==true)
	{
		$is_active=1;
	}
	else
	{
		$is_active=0;
	}
	
	$client_id=$this->Basic_model->unique_id;
	
	$sql="Select * from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id Where
	role_master.object_id=$client_id AND sub_role_details.id=$employee_id";
	$rs=$this->db->query($sql);
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Employee not found';
	}
	
	if(!$error)
	{
	  $rs=$this->db->select('*')->where('id',$method_id)->get('method');
	  if(!$rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='Method not found';
	  }
	  
	  $rs=$this->db->select('*')->where('sub_role_details_id',$employee_id)->where('method_id',$method_id)->get('module_permission');
	  if($rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='This url already exist';
	  }
		
	}
	
	if(!$error)
	{
		$permission_data=array(
		 'sub_role_details_id'=>$employee_id,
		 'method_id'=>$method_id,
		 'is_active'=>$is_active,
	    );
		
	   $success=$this->db->insert('module_permission',$permission_data);
	   if($success)
	   {
		   $result['successMessage']="Data successfully inserted";
	   }
	   else
	   {
		   $error=true;
		   $errortext='Insert error';
	   }
		
		
	}
	
	  
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
  }
  
  
  public function UpdatePermission($post_data)
  {
	$error='';
	$errortext='';
	$result='';
	
	$permission_id=$post_data['permission_id'];
	$employee_id=$post_data['employee_id'];
	$method_id=$post_data['method_id'];
	
	if($post_data['is_active']==true)
	{
		$is_active=1;
	}
	else
	{
		$is_active=0;
	}
	
	$client_id=$this->Basic_model->unique_id;
	
	$sql="Select * from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id Where
	role_master.object_id=$client_id AND sub_role_details.id=$employee_id";
	$rs=$this->db->query($sql);
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Employee not found';
	}
	
	  $rs=$this->db->select('*')->where('id',$permission_id)->get('module_permission');
	  if(!$rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='Permission id not found';
	  }
	
	if(!$error)
	{
	  $rs=$this->db->select('*')->where('id',$method_id)->get('method');
	  if(!$rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='Method not found';
	  }
	  
	  $rs=$this->db->select('*')->where('sub_role_details_id',$employee_id)->where('method_id',$method_id)
	  ->where('id!=',$permission_id)->get('module_permission');
	  if($rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='This url already exist';
	  }
		
	}
	
	if(!$error)
	{
		$permission_data=array(
		 'method_id'=>$method_id,
		 'is_active'=>$is_active,
	    );
		
	   $success=$this->db->where('id',$permission_id)->update('module_permission',$permission_data);
	   if($success)
	   {
		   $result['successMessage']="Data successfully updated";
	   }
	   else
	   {
		   $error=true;
		   $errortext='Insert error';
	   }
		
		
	}
	
	  
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
  }
  
  
  public function GetPermisionList()
  {
	$error='';
	$errortext='';
	$result='';
	
	 $client_id=$this->Basic_model->unique_id;
	 $permission_list=array();
     $rs=$this->db->select('*')->group_by('sub_role_details_id')->order_by('id','asc')->get('module_permission');
	 if($rs->num_rows()>0)
	 {
		 $permission_array=$rs->result_array();
		 foreach($permission_array as $item)
		 {
			 $role_details=array();
			 $url_details=array();
			 
			 $sub_role_details_id=$item['sub_role_details_id'];
			 
			 
			  $sql2="Select *,module_permission.id as 'permission_id' from module INNER JOIN method on module.id=method.module_id INNER JOIN module_permission
			         ON method.id=module_permission.method_id Where module.is_active=1
	                 And method.is_active=1 and module_permission.sub_role_details_id=$sub_role_details_id";
					
			  $rs=$this->db->query($sql2);
			  if($rs->num_rows()>0)
			  {
				 $module_method_array=$rs->result_array();
				 foreach($module_method_array as $row)
				 {
					 $url="";
					 $url=$row['class_name']."/".$row['method_name'];
					 $url_details[]=array(
					   'permission_id'=>$row['permission_id'],
					   'display_name'=>$row['method_description'],
					   'url'=>$url,
					   'type'=>$row['type']
					 );
				 }
				 
			  }
			  
			  $sql="Select *,sub_role_details.id as 'role_id' from role_master INNER JOIN sub_role_details ON role_master.id=sub_role_details.role_master_id Where
			 role_master.object_id=$client_id AND sub_role_details.id=$sub_role_details_id";
			 $rs=$this->db->query($sql);
			 if($rs->num_rows()>0)
			  {
				 $sub_role_details=$rs->row_array();
				 $role_details=array(
				  'role'=>$sub_role_details['role_name'],
				  'employee_id'=>$sub_role_details['role_id'],
				  'employee_name'=>$sub_role_details['name'],
				  'permitted_url'=>$url_details
				 );
				 
			  }
			  
			  if(!empty($role_details))
			  {
				  $permission_list[]=array(
					 'method_list'=>$role_details,
				  );
			  }
			  
			 
		 }
	 }
	  
	 return $permission_list;
  }
  
  
  public function DeletePermission($post_data)
  {
	$error='';
	$errortext='';
	$result='';
	
	  $permission_id=$post_data['permission_id'];
	
	  $rs=$this->db->select('*')->where('id',$permission_id)->get('module_permission');
	  if(!$rs->num_rows()>0)
	  {
		 $error=true;
		 $errortext='Permission id not found';
	  }
	
	if(!$error)
	{
	   $success=$this->db->where('id',$permission_id)->delete('module_permission');
	   if($success)
	   {
		   $result['successMessage']="Data successfully deleted";
	   }
	   else
	   {
		   $error=true;
		   $errortext='delete error';
	   }
		
		
	}
	
	  
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
  }
 
}
?>