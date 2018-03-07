<?php
require_once APPPATH . '/libraries/REST_Controller.php';
class Check_role 
{
	public function getPermission()
	{
		  $error = false;
		  $errortext = '';
		  $result = '';
		  $response_code='';
		  try
		  {
			  
			    $this->CI =& get_instance();
			    $controller=$this->CI;
				
				if($controller->Basic_model->unique_id!="")
				{
					
					$check_active_client=$controller->Basic_model->check_active_client($controller->Basic_model->unique_id);
					
					if($check_active_client['error'])
					{
						$error=true;
						$errortext=$check_active_client['errortext'];
						$response_code='401';
					}
					
					if(!$error)
					{
						$authentication=$controller->Basic_model->session_exp();
						if($authentication['error'])
						{
						  $login_data=$controller->Login_model->logout_user(); 
						  $error=true;
						  $errortext=$authentication['errortext'];
						  $response_code='401';
						}
					}
					
					if(!$error)
					{
					   $authentication=$controller->Basic_model->authentication('School');
					   if($authentication['error'])
					   {
						  $login_data=$controller->Login_model->logout_user(); 
						  $error=true;
						  $errortext=$authentication['errortext'];
						  $response_code='401';
					   }
					}
				}
				
				
				/*if(!$error)
				{
				   if($controller->Basic_model->sub_unique_id!="")
				   {
					    $role_permission_array['sub_unique_id']=$controller->Basic_model->sub_unique_id;
						$role_permission_array['unique_id']=$controller->Basic_model->unique_id;
						$role_permission_array['role']=$controller->Basic_model->role;
						$result=$controller->Common_model->get_role_permission($role_permission_array);
						$response=$controller->Common_model->permission_url($result,$controller->uri->uri_string(),$controller->input->method(TRUE));
						if(!$response)
						{
							$response_code='401';
							$error=true;
							$errortext='Unauthorised access';
						}
				   }
				}*/
				
				
			
		  }
		  catch(Exception $e)
		  {
			 $error = true;
			 $errortext = $e->getMessage();
		  }
		  
	
		  
    if($error)
	{		  
	  $controller->response(array('error'=>$error,'errortext'=>$errortext), $response_code);
	}	
			 
 }
	  

}
?>