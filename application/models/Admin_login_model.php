<?php
class Admin_login_model extends CI_Model
{

 public function logout_user()
 {
	$this->session->unset_userdata('login_name');
	$this->session->unset_userdata('unique_id');
	$this->session->unset_userdata('operator_name');
	$this->session->unset_userdata('sub_unique_id');
	
		
	}
	
 public function GetUserLogin($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 try
	 {
	  $user_data=array();
	  $unique_text=$post_data['unique_text'];
	  $password=md5($post_data['password']);
	 
	   $sql="SELECT * FROM login WHERE (phone_no='$unique_text' OR email='$unique_text' OR unique_id='$unique_text') 
	 	     AND password='$password'";
	    $rs=$this->db->query($sql);
		
	
		 if($rs->num_rows()>0)
		 {
			  $login_data=$rs->row_array();
			  $user_data=$this->get_user_data($login_data);
			  
			 
			  if(!empty($user_data))
			  {
				  $current_time_stamp=$login_data['current_login_timestamp'];
				  $last_ip_address=$login_data['current_ip_address'];
				  
				  $login_info=array(
				   'last_login_timestamp'=>$current_time_stamp,
				   'last_ip_address'=>$current_time_stamp,
				   'current_login_timestamp'=>date("Y/m/d h:i:s A"),
				   'current_ip_address'=>$this->input->ip_address(),
				  );
				  
				  $this->db->where('id',$login_data['id'])->update('login',$login_info);
				  
				  $sub_unique_id=$user_data['sub_unique_id'];
				  if($sub_unique_id!="")
				  {
					  $sub_unique_id=$user_data['sub_unique_id'];
				  }
				  else
				  {
					  $sub_unique_id=0;
				  }
				  
				  $session_data=array(
				   'login_name'=>$user_data['login_name'],
				   'role'=>$user_data['role'],
				   'unique_id'=>$user_data['unique_id'],
				   'sub_unique_id'=>$sub_unique_id,
				   'operator_name'=>$user_data['operator_name']
				  );
				  
				  //echo '<pre>',print_r($session_data);
				  //die();
				  
				  $this->session->set_userdata($session_data);
				  
				  $result['dashboard_page']=$user_data['dashboard_page'];
				  
				  
				  
			  }
			  else
			  {
				$error = true;
                $errortext = 'Not authorised for login';
			  }
			  
		 }
		 else
		 {
			$error = true;
            $errortext = 'Invalid credentials';
		 }
	 }
	 catch(Exception $e)
	 {
		  $error = true;
          $errortext .= $e->getMessage();
	 }
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function randomString($length = 5) 
 {
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
	 return $str;
 }  
 
 public function SendUpdatePassword($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 try
	 {
		 $email=$post_data['email'];
		 $rs=$this->db->select('*')->where('email',$email)->get('login');
		 if($rs->num_rows()>0)
		 {
			 $login_array=$rs->row_array();
			 $client_id=$login_array['unique_id'];
			 
			 $new_password=$this->randomString();
			
			 $login_data=array(
				'password'=>md5($new_password),
			 );
               
			 $success=$this->db->where('email',$email)->update('login',$login_data);
			 if($success)
			 {      
				$client_name=$this->Common_model->get_single_field_value('school','name','id',$client_id);
				$subject="New Password for $client_name";
				$message="<b>Name</b> :".$client_name.'<br/>'.
				"<b>New Password</b>: ".$new_password.'<br/>';
				
				$send_email_array=array(
				  'subject'=>$subject,
				  'message'=>$message,
				  'email'=>$email
				);
				
				$send_email_array=$this->Common_model->send_email($send_email_array);
				if(!$send_email_array['error'])
				{
					$result['new_pass']=$new_password;
					$result['successMessage']='Email send successfully';
				}
				else
				{
					$error = true;
             		$errortext =$send_email_array['errortext'];
				}
					
					
			 }
		 }
		 else
		 {
			 $error = true;
             $errortext = 'Email id not found';
		 }
		 
	 }
	 catch(Exception $e)
	 {
		  $error = true;
          $errortext .= $e->getMessage();
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function get_user_data($login_data)
 {
	 $role=$login_data['role'];
	 $email=$login_data['email'];
	 $phone=$login_data['phone_no'];
	 $Operator_name='';
	 $dashboard_page='';
	 $table_name='';
	 $user_data=array();
	 
	 $error=false;
	 
	 switch($role)
	 {
		  case 1:
		   $dashboard_page='admin/dashboard';
		   $Operator_name='Admin';
		   $table_name='admin';
		  break;
		 
		 case 2 :
		   $dashboard_page='vendor/dashboard';
		   $Operator_name='Vendor';
		   $table_name='vendor';
		 break;
		 case 3 :
		   $dashboard_page='school/dashboard';
		   $Operator_name='School';
		   $table_name='school';
		 break;
		 case 4:
		   $dashboard_page='school/dashboard';
		   $Operator_name='School';
		   $table_name='account';
		   $login_role=4;
		 break;

	 }
	 
	 $rs=$this->db->select('*')->where('phone_no',$phone)->or_where('email',$email)->get('login');
	// echo $this->db->last_query();
	 if($rs->num_rows()>0)
	 {
		$common_data=$rs->row_array(); 
		$login_name='';
		$unique_id=$common_data['unique_id'];
		$sub_unique_id=$common_data['sub_unique_id'];
		
		if($sub_unique_id!="")
		{
		    $rs2=$this->db->select('*')->where('id',$sub_unique_id)->where('is_active',1)->get($table_name);
		}
		else
		{
			$rs2=$this->db->select('*')->where('id',$unique_id)->where('is_active',1)->get($table_name);
		}

		if($rs2->num_rows()>0)
		{
			$table_data=$rs2->row_array();
			$login_name=$table_data['name'];
			
			$user_data['unique_id']=$unique_id;
			$user_data['login_name']=$login_name;
			$user_data['operator_name']=$Operator_name;
			$user_data['dashboard_page']=$dashboard_page;
			$user_data['sub_unique_id']=$sub_unique_id;
			$user_data['role']=$role; 
		}
		
	 }
	 
	 return $user_data;
 }
 
 
 	
	
}
?>