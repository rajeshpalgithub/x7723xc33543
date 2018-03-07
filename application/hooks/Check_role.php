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
				
						if(isset($controller->check_permission))
						{
							/// write block of code or any method that will check if incoming method name exist in this class if exit then what is permission
							$skip_parameter=false;
							$skip_parameter=$controller->Basic_model->CheckSkipMethod($controller->check_permission);

							

							if(!$skip_parameter)
							{
								
								if(!$controller->Basic_model->error)
								{
									$class_permission=$controller->Basic_model->CheckClassPermission();
									
									if($class_permission['error'])
									{
										$error=true;
										$errortext=$class_permission['errortext'];
										$response_code='401';
									}
								}else{
									$error=true;
									$errortext= $controller->Basic_model->errortext;
									$response_code=401;
								}
								
								if(!$error)
								{
									foreach($check_permission as $key_value=>$method_item)
									{
										$request_method_type=$controller->input->server('REQUEST_METHOD');
										$request_method_name=$controller->router->fetch_method();
										
										if($key_value==$request_method_name)
										{
											foreach($method_item as $key=>$row)
											{
												if($request_method_type==$key)
												{
													$skip_cat=array("a");
													$cat_1=array(0,3,4);
													$cat_2=array(0,3);
													$cat_3=array(3,4);
													$cat_4=array(3);
													$cat_5=array(4);
													
													
													if($row!=$skip_cat)
													{
														if($row==$cat_1 || $row==$cat_2)
														{
															$check_flag=0;
														}
														elseif($row==$cat_3)
														{
															if($controller->Basic_model->token_id!="" || ($controller->Basic_model->role==4 || $controller->Basic_model->role==3))
															{
																$check_flag=0;
															}
															else
															{
															  $check_flag=2;
															}
														}
														else
														{
															$check_flag=1;
														}
														
													
													
														if($check_flag==1 || $check_flag==2)
														{
															$authentication=$controller->Basic_model->check_role_permission();
															if($authentication['error'])
															{
															  //$login_data=$controller->Login_model->logout_user(); 
															  $error=true;
															  $errortext=$authentication['errortext'];
															  $response_code='401';
															}
														}
														break;
													}
												}
											}
										 break;
										}
									}
								
								}
								
								
							}
						}
					
			
				
			
		  }
		  catch(Exception $e)
		  {
			 $error = true;
			 $errortext = $e->getMessage();
			 $response_code = 400;
		  }
		  
	
		  
    if($error)
	{		  
	  $controller->response(array('error'=>$error,'errortext'=>$errortext), $response_code);
	}	
			 
 }
	  

}
?>