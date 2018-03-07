<?php
class Rest_model extends CI_Model
{
	public function Check_parameters($input_array,$userdata)
	{
		  $error = false;
		  $errortext = '';
		  $result = '';
		  
					  
		  try
		  {
			  foreach($input_array as $key=>$value)
			  {
				 // print_r($value);
				  
				  if($value['required']==1)
				  {
					
					  if (isset($userdata->$key))
						{
							 
							$input_value=$userdata->$key;
							if($input_value=="")
							{
								$error =  true;
								$errortext .=  "Please enter parameter value of $key".'<br>';
								$response_code='200';
							}
							elseif($value['exp']!=""){
								if(!preg_match($value['exp'],$input_value))
								{
									$error =  true;
									$errortext .=  "Invalid input : $key ".'<br>';
									$response_code='200';
								}
								else
								{
									$input_array[$key]=$input_value;
								}
							}
							else
							{
								$input_array[$key]=$input_value;
							}
						
						}
						else
						{
						   $error =  true;
						   $errortext .=  "Please enter $key as parameter"."<br>";
						   $response_code='200';
							
						}
					  
				  }
				  elseif($value['required']==0)
				  {
					  if (isset($userdata->$key))
						{
							$input_value=$userdata->$key;
							if($value=="")
							{
								$error =  true;
								$errortext .=  "Please enter parameter value of $key".'<br>';
								$response_code='200';
							}
							elseif($value['exp']!=""){
								if(!preg_match($value['exp'],$input_value))
								{
									$error =  true;
									$errortext .=  "Invalid input value : $key ".'<br>';
									$response_code='200';
								}
								else
								{
									$input_array[$key]=$input_value;
								}
								
							}
							else
							{
								$input_array[$key]=$input_value;
							}
						
						}
						else
						{
							$input_array[$key]="";
						}
				  }
				
				
			   
			   }
			  
					 
			  
			  if(!$error){
			  $result['input_array']=array_merge($input_array);
			  }
			  
			
			 
			
		  }
		  catch(Exception $e)
		  {
			 $error = true;
			 $errortext = $e->getMessage();
		  }
		  return array('error'=>$error,'errortext'=>$errortext,'result'=>$result);
		}
}
?>