<?php
class Login_model extends CI_Model
{

 public function logout_user()
 {
	 $error='';
	 $errortext='';
	 $result='';
	 try
	 {
		 
		$login_response=$this->Basic_model->get_login_details();
        if($login_response['error'])
	    {
		 $error=true;
		 $errortext=$login_response['errortext'];
	    }
		else
		{
			
			 $token_id=$this->Basic_model->token_id;
			 $client_id=$this->Basic_model->unique_id;
			 $sub_unique_id=$this->Basic_model->sub_unique_id;
			
			 $rs=$this->db->where('token_id',$token_id)->where('client_id',$client_id)->where('sub_unique_id',$sub_unique_id)
	         ->delete('temp_cart');
			 
			 $login_details=$login_response['result']['login_details'];
	         $token_id=$login_details['token_id'];
			 $success=$this->db->where('token_id',$token_id)->delete('db_session');	
			 if($success)
			 {
				 $result['successMessage']='Logout successfully';
			 }
			 
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
				    $storeEmail = 'tanmoy@urcib.com';
					$storeName = 'Urcib technologies';
					$to=$email;
					
					$message="<b>Name</b> :".$client_name.'<br/>'.
							 "<b>New Password</b>: ".$new_password.'<br/>';
					
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
						$result['new_pass']=$new_password;
						
					}
					else
					{
						$error = true;
             			$errortext = 'Email  not send';
						$result['new_pass']=$new_password;
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
	  
	  if($this->Basic_model->login_data)
	   {
		 if($this->Basic_model->login_data!="")
		 {	
		    $error=true;
			$errortext .= 'You are already login';
		 }
	  }
	 
	 if(!$error){
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
				  
				  $hash_algorithm = 'sha256';
				  $token_id = hash($hash_algorithm, uniqid(rand()));
				 
				  $session_data=array(
				   'token_id'=>$token_id,
				   'login_name'=>$user_data['login_name'],
				   'role'=>$user_data['role'],
				   'unique_id'=>$user_data['unique_id'],
				   'sub_unique_id'=>$sub_unique_id,
				   'operator_name'=>$user_data['operator_name']
				  );
				  
				  $this->db->insert('db_session',$session_data);
				  $role='';
				  
				  if($sub_unique_id!=0)
				  {
					  $role_master_id=$this->Common_model->get_single_field_value('sub_role_details','role_master_id','id',$sub_unique_id);
					  $rs=$this->db->where('object_id',$user_data['unique_id'])->where('id',$role_master_id)
					  ->get('role_master');
					  if($rs->num_rows()>0)
					  {
						  $role_array=$rs->row_array();
						  $role=$role_array['role_name'];
					  }
				  }
				  else
				  {
					  $rs=$this->db->where('object_id',$user_data['unique_id'])->where('parent_role_id',0)
					  ->get('role_master');
					  if($rs->num_rows()>0)
					  {
						  $role_array=$rs->row_array();
						  $role=$role_array['role_name'];
					  }
				  }
				  $result['successMessage']='Login successfully';
				  $result['Auth-Token']=$token_id;
				  $result['role']=$role;
				  $result['user_name']=$user_data['login_name'];
				   
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
		
		$rs2=$this->db->select('*')->where('id',$unique_id)->where('Is_active',1)->get($table_name);

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