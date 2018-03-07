<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Login_model extends CI_Model
{
	private $page_default =0;
	private $records_default =20;
	
	function __construct() {
		$this->load->model('Tables_model');
	}

 public function logout_user()
 {
	 $error='';
	 $errortext='';
	 $result='';
	 try
	 {
		 
		$login_response=$this->Basic_model->get_login_details();
		//print_r($login_response);
		
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
		 $auth_code = $post_data['auth_code'];
		
		
			
			 $new_password=$this->randomString();
			
			 $login_data=array(
				'password'=>md5($new_password),
			 );
               
			 $this->db->trans_start();
			 $this->db->where('email',$email)->where('activation_code',$auth_code)
			 ->where('role',3)->update('login',$login_data);
			 
			 if($this->db->affected_rows())
			 {      
				$this->db->where('email',$email)->where('role',3)->update('login',array('activation_code'=>''));
				$subject="New Password";
				$message="
					<html>
						<body>
							<p>Hi, You have requested new password here is new password bellow</p>
							<p>New Password: $new_password</p>
						</body>
					</html>
				
				";
				
				$send_email_array=array(
				  'subject'=>$subject,
				  'message'=>$message,
				  'email'=>$email
				);
				
				$send_email_array=$this->Common_model->send_email($send_email_array);
				if(!$send_email_array['error'])
				{
					
					$result['successMessage']='New password has been sent to your email id.';
				}
				else
				{
					$error = true;
             		$errortext =$send_email_array['errortext'];
				}
					
			 }else{
				 $error = true;
				 $errortext ="Incorrect authentication code.";
			 }
			 $this->db->trans_complete();
			 if ($this->db->trans_status() === FALSE)
			 {
				$error = true;
             	$errortext ="Can't perform opetation now, Please try again.";
			 }
		 
	 }
	 catch(Exception $e)
	 {
		  $error = true;
          $errortext .= $e->getMessage();
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }



 public function get_session_id()
{
	$my_session_id='';
	if($this->Basic_model->token_id=="")
	{
		$headers=getallheaders();
		if(isset($headers['Session']))
		{
		   $my_session_id=$headers['Session'];
		}
	}
	
	return $my_session_id;

}

 
 public function GetCustUserLogin($post_data)
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
	 
	 if(!$error)
	 {
		$client_id=$this->Basic_model->unique_id; 
	    $sql="SELECT * FROM login WHERE (phone_no='$unique_text' OR email='$unique_text') AND password='$password' AND unique_id=$client_id AND role=4";
	    $rs=$this->db->query($sql);
		
		 if($rs->num_rows()>0)
		 {
			  $login_data=$rs->row_array();
			  $sub_unique_id=$login_data['sub_unique_id'];
			  $role=$login_data['role'];
			  
			  $user_name=$this->Common_model->get_single_field_value('account','name','id',$sub_unique_id);
			  $client_name=$this->Common_model->get_single_field_value('school','name','id',$client_id);
			  
			  $rs2=$this->db->select('*')->where('id',$sub_unique_id)->where('is_active',1)->get('account');
			  if($rs2->num_rows()>0)
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
				  
				  
				  
				  $hash_algorithm = 'sha256';
				  $token_id = hash($hash_algorithm, uniqid(rand()));
				 
				  $session_data=array(
				   'token_id'=>$token_id,
				   'login_name'=>$user_name,
				   'role'=>4,
				   'unique_id'=>$client_id,
				   'sub_unique_id'=>$sub_unique_id,
				   'operator_name'=>'Customer'
				  );
				  
				  $this->db->insert('db_session',$session_data);
				  $role='';
				  
				  $operating_name='role';
				  
				  if($sub_unique_id!=0)
				  {
					  if($role==4)
					  {
						   $role=$this->Common_model->get_single_field_value('account','name','id',$sub_unique_id);
						   $operating_name='customer';
						   $cart_data['my_session_id']=$this->get_session_id();
						   $cart_data['customer_id']=$sub_unique_id;
						   $cart_data['token_id']=$token_id;
						   $this->update_cart_data($cart_data);
						   
					  }
					  
				  }
				  
				  $result['successMessage']='Login successfully';
				  $result['AuthToken']=$token_id;
				  $result['user_name']=$user_name;
				  $result['client_name']=$client_name;
				   
			  }
			  else
			  {
				$error = true;
                $errortext = 'Not authorised for login or user is inactive';
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
	  
	  /*if($this->Basic_model->login_data)
	   {
		 if($this->Basic_model->login_data!="")
		 {	
		    $error=true;
			$errortext .= 'You are already login';
		 }
	   }*/
	 
	
		//$client_id=$this->Basic_model->unique_id; 
	    /*$sql="SELECT * FROM login WHERE email='$unique_text' AND password='$password'";
	    $rs=$this->db->query($sql);*/
		
		
		$rs=$this->db->select('login.id as login_id')
			->select('login.role as login_role')
			->select('login.login_is_active')
			->select('school.id as client_id')
		
		 ->select('sub_role_details.id as user_id')
		 ->select('sub_role_details.name as user_name')
		 ->select('school.name as business_name')
		 ->select('school.short_name as business_short_name')
		 ->select('school.school_logo as business_logo')
		 ->select('role_master.role_name as role_name')
		 
		 ->from('login')
		 ->join('school','school.school_login_id = login.id')
		 ->join('sub_role_details','sub_role_details.sub_role_details_login_id = login.id')
		 ->join('role_master','role_master.id = sub_role_details.role_master_id')
		 ->where('login.email',$unique_text)->where('login.password',$password)->get();
		
		
		
		 if($rs->num_rows()>0)
		 {
			  $user_data=$rs->row_array();
			  
				  $login_id=$user_data['login_id'];
				  $client_id = $user_data['client_id'];
				  $role= $user_data['role_name'];
				  $user_name = $user_data['user_name'];
				  $business_name = $user_data['business_name'];
				  $business_short_name = $user_data['business_short_name'];
				  $business_logo = $user_data['business_logo'];
				  $user_is_active = $user_data['login_is_active'];
				  
				  $hash_algorithm = 'sha256';
				  $token_id = hash($hash_algorithm, uniqid(rand()));
				 
				  $session_data=array(
				   'token_id'=>$token_id,
				   'db_session_login_id'=>$login_id
				  );
				  $this->db->trans_start();
				  $this->db->insert('db_session',$session_data);
				  $db_session_id = $this->db->insert_id();
				  
				  $login_log=array(
				   'login_log_login_id'=>$login_id,
				   'login_log_ip_address'=>$this->input->ip_address(),
				   'login_log_token_id' => $db_session_id,
				  );
				  $this->db->insert('login_log',$login_log);
				  $this->db->trans_complete();
				  
				 
				  
				
					  if($user_data['login_role']==4)
					  {
						   $role='Customer';
						   $cart_data['my_session_id']=$this->get_session_id();
						   $cart_data['customer_id']=$client_id;
						   $cart_data['token_id']=$token_id;
						   $this->update_cart_data($cart_data);
						   
					  }
					 
				 
				  
				  $result['successMessage']='Login successfully';
				  $result['AuthToken']= $token_id;
				  $result['role']= $role;
				  $result['user_name']= $user_name;
				  $result['business_name']= $business_name;
				  $result['business_short_name']= $business_short_name;
				  $result['user_is_active']= $user_is_active;
				  $result['user_image']= '';
				  $result['business_logo'] = '';
				   
			  
			 
			  
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
 
 
 public function update_cart_data($cart_data)
 {
	 $my_session_id=$cart_data['my_session_id'];
	 $customer_id=$cart_data['customer_id'];
	 $token_id=$cart_data['token_id'];
	 
	 $update_arr=array(
	   'token_id'=>$token_id,
	   'sub_unique_id'=>$customer_id
	  );
	  
	 $this->db->where('token_id',$my_session_id)->update('temp_cart',$update_arr);
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
	 $login_role='';
	 $error=false;
	 
	 switch($role)
	 {
		  /*case 1:
		   $dashboard_page='admin/dashboard';
		   $Operator_name='Admin';
		   $table_name='admin';
		   $login_role=1;
		  break;*/
		 
		 /*case 2 :
		   $dashboard_page='vendor/dashboard';
		   $Operator_name='Vendor';
		   $table_name='vendor';
		   $login_role=2;
		 break;*/
		 case 3 :
		  // $dashboard_page='school/dashboard';
		   $Operator_name='School';
		   $table_name='school';
		   $login_role=3;
		 break;
		 case 4:
		   $error=true;
		 break;

	 }
	 
	 if(!$error)
	 {
		 $rs=$this->db->select('school.id as client_id')
		 ->select('sub_role_details.id as user_id')
		 ->select('sub_role_details.name as user_name')
		 ->select('school.name as business_name')
		 ->select('school.short_name as business_short_name')
		 ->from('login')
		 ->join('school','school.school_login_id = login.id')
		 ->join('sub_role_details','sub_role_details.sub_role_details_login_id = login.id')
		 ->join('role_master','role_master.id = sub_role_details.role_msater_id')
		 ->where('login.email',$email)->where('login.password',$login_role)->get();
		 
		 //$sql = "SELECT school.id as client_id, sub_role_details.id as user_id FROM login ";
		 
		// echo $this->db->last_query();
		  if($rs->num_rows()>0)
		  {
			$common_data=$rs->row_array(); 
			$login_name='';
			$unique_id=$common_data['unique_id'];
			$sub_unique_id=$common_data['sub_unique_id'];
			
			//print_r($common_data);
			//die();
			
			/*if($sub_unique_id!="")
			{
				$rs2=$this->db->select('*')->where('id',$sub_unique_id)->where('is_active',1)->get('sub_role_details');
			}
			else
			{
				$rs2=$this->db->select('*')->where('id',$unique_id)->where('is_active',1)->get($table_name);
			}*/
			
			//echo $this->db->last_query();
			//die();

			if($rs2->num_rows()>0)
			{
				$table_data=$rs2->row_array();
				$login_name=$table_data['name'];
				
				$user_data['unique_id']=$unique_id;
				$user_data['login_name']=$login_name;
				//$user_data['operator_name']=$Operator_name;
				//$user_data['dashboard_page']=$dashboard_page;
				$user_data['sub_unique_id']=$sub_unique_id;
				$user_data['role']=$login_role; 
			}
			
		 }
		 
	 }
	 return $user_data;
 }
 
 public function get_employee_login_data($field_name,$id)
 {
	 
	 $field_data='';
	 $rs=$this->db->select('*')
	 ->where('role',4)
	 ->where('sub_unique_id',$id)
	 ->where('unique_id',$this->Basic_model->unique_id)
	 ->get('login');

	 if($rs->num_rows()>0)
	 {
		  $login_array=$rs->row_array();
		  $field_data=$login_array[$field_name];
	 }
	 return $field_data;
 }

 function sendAuthCode($parameters=array('email'))
 {
	$error= false;
	$errortext = "";
	$result = array();

	$email = $parameters['email'];
	// generate auth code
	$auth_code = rand(999,9999);
	$update_data = array(
        'activation_code' => $auth_code,
	);
	$this->db->trans_start();
	$this->db->where('email', $email)->where('role',3)->update('login', $update_data);
	
	if($this->db->affected_rows())
	{
		
		$this->load->model('Common_model');
				$mail_content = array(
					'email'=>$email,
					'message'=>"
						<html>
							<body>
								
								<p  aling=\"center\">Your forgot password authentication code bellow</p>
								<p>
									Auth Code: $auth_code
								</p>
							</body>
						
						</html>
					
					",
					'subject'=>"Forgot password auth code",
				);
		$email_response = $this->Common_model->send_email($mail_content);
		$result['successMessage'] = "Authentication code sent to your register email id.";
		$result['email'] = $email;
	}else{
		$error = true;
		$errortext .= "Incorrect email id";
	}
	$this->db->trans_complete();
	if ($this->db->trans_status() === FALSE)
	{
		$error = true;
		$errortext .= "Can't perform operation now, Please try again.";
	}
	return array('error'=>$error,'errortext'=>$errortext,'result'=>$result);
 }
	
}
?>