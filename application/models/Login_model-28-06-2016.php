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
				 
				  $x_api_key=$user_data['x_api_key'];
				 
				  $session_data=array(
				   'token_id'=>$token_id,
				   'x_api_key'=>$x_api_key,
				   'login_name'=>$user_data['login_name'],
				   'role'=>$user_data['role'],
				   'unique_id'=>$user_data['unique_id'],
				   'sub_unique_id'=>$sub_unique_id,
				   'operator_name'=>$user_data['operator_name']
				  );
				  
				  $this->db->insert('db_session',$session_data);
				  //echo '<pre>',print_r($session_data);
				  //die();
				  
				  //$this->session->set_userdata($session_data);
				  
				  $result['successMessage']='Login successfully';
				  $result['Auth-Token']=$token_id;
				  $result['X-Api-Key']=$x_api_key;
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
			$user_data['x_api_key']=$table_data['x_api_key'];
		}
		
	 }
	 
	 return $user_data;
 }
 
 
 	
	
}
?>