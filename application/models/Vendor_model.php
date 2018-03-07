<?php
class Vendor_model extends CI_Model
{
	
	
 public function SchoolListCommon()
 {
	$vendor_id=$this->Basic_model->unique_id;
	$school_search='';
	 if($this->input->post('search_text'))
	 {
		 $sc_name=$this->input->post('search_text');
		 $school_search=" And name LIKE '%$sc_name%'"; 
	 }
	 
	 $sql="Select * from school Where vendor_id='$vendor_id' $school_search"; 
	 
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
	
 /*public function GetSchoolList()
 {
	 $school_list=array();
	 $sql=$this->SchoolListCommon();
	 $rs=$this->db->query($sql);
	 //echo $this->db->last_query();
	 //die();
	 if($rs->num_rows()>0)
	 {
		 $school_list=$rs->result_array();
	 }
	 
	 return $school_list;
 }*/
 
 
 
 
  public function GetStatelist($id)
  {
	$state_list=array();  
	$rs2=$this->db->select('*')->where('region_id',$id)->get('subregions'); 
	if($rs2->num_rows()>0)
	{
		$state_list=$rs2->result_array();
	}
	return $state_list;
  }
 
  public function GetCountryList()
  {
	  $country_list=array();
	  $rs=$this->db->select('*')->get('regions');
	  if($rs->num_rows()>0)
	   {
		  $country_list=$rs->result_array();
	   }
	   
	   return $country_list;
	  
  }
 
  public function GetSchoolDetailsOnId($id)
  {
	  $student_data=array();
	  $rs=$this->db->select('*')->where('id',$id)->get('school');
	  if($rs->num_rows()>0)
	   {
		  $student_data=$rs->row_array();
	   }
	   
	   return $student_data;
	   
  }
 
 
  public function GetVendorDetails()
  {
	  $vendor_data=array();
	  $rs=$this->db->select('*')->where('Is_active',1)->get('vendor');
	  if($rs->num_rows()>0)
	   {
		   $vendor_data=$rs->result_array();
	   }
	   
	   return $vendor_data;   
  }
 
 
 public function UpdateSchoolData($id,$post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	  
	   $rs=$this->db->select('*')->where('id!=',$id)->where('short_name',$post_data['short_name'])->where('vendor_id',$this->Basic_model->unique_id)->get('school');
	   if($rs->num_rows()>0)
	   {
		   $error=true;
		   $errortext='School shortname already exist';
	   } 
	   
	  $school_validation=$this->Common_model->CheckUniqueItem_edit($post_data,$id);
	  if($school_validation['error'])
	  {
		$error=true;
		$errortext=$school_validation['errortext'];
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
			 'short_name'=>$post_data['short_name'],
			 'product_type'=>$post_data['product_type'],
			 'address'=>$post_data['address1'],
			 'city_village'=>$post_data['city_village'],
			 'country_id'=>$post_data['country'],
			 'state'=>$post_data['state'],
			 'pin_code'=>$post_data['pin'],
			 'update_date_time'=>date("Y/m/d h:i:s A"),
			 'is_active'=> $is_active,
		   );
		   
		   //echo '<pre>',print_r($update_arr);
		   //die();
		   
		   $role=3;
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
		   
		   
		   $success=$this->db->where('id',$id)->update('school',$update_arr);
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
  
  public function randomString($length = 9) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
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
			 'vendor_id'=>$this->Basic_model->unique_id,
			 'name'=>$name,
			 'short_name'=>$post_data['short_name'],
			 'product_type'=>$post_data['product_type'],
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
		        
				$insert_arr2=array
			    (
				 'client_id'=>$unique_id,
				 'key'=>$this->randomString(),
				 'level'=>0,
				 'ip_addresses'=>$this->input->ip_address(),
			    );
				
		        $success=$this->db->insert('keys',$insert_arr2);
		        
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
 
 
}
?>