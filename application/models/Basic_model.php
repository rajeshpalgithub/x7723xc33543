<?php
class Basic_model extends CI_Model
{

 public $login_data=''; 
 public $unique_id='';
 public $sub_unique_id=0;
 public $role='';
 public $short_name='';
 public $operator_name='';
 public $token_id='';
 public $timezone='';
 public $dateformat='';
 public $op_role='';
 public $x_api_key='';
 public $auth_token='';
 public $mysql_dateformat='';
 public $mysql_input_dateformat='';
 public $mysql_input_dateRgxp='';
 public $regularexp=array();
 
 public $image_link='';
 
 function __construct()
 {
  parent::__construct();
  $this->load->model('Common_model');
  
  /*if($this->session->userdata('login_name'))
  {
	if($this->session->userdata('login_name')!="")
	{
	  $this->login_data=$this->session->userdata('login_name');
	  $this->unique_id=$this->session->userdata('unique_id');
	  $this->sub_unique_id=$this->session->userdata('sub_unique_id');
	  $this->role=$this->session->userdata('role');
	  $this->operator_name=$this->session->userdata('operator_name');  
	  $this->short_name=$this->Common_model->get_single_field_value('school','short_name','id',$this->unique_id);
	}
   }*/
   
    $db_api_response=$this->get_api_key();
	
	
	
	if($this->token_id=="")
	{
		if(!$db_api_response['error'])
		{
		   $api_response=$db_api_response['result']['key_array'];
		   $this->unique_id=$api_response['client_id'];
		   $this->x_api_key=$api_response['key'];
		}
		else
		{
			  
			 $this->x_api_key=$db_api_response['result']['x_api_key'];
		}
		
	}
   
  $login_response=$this->get_login_details();
  $this->image_link=base_url();
  
   if(!$login_response['error'])
   {
	   $login_details=$login_response['result']['login_details'];
	   $this->token_id=$login_details['token_id'];
	   $this->login_data=$login_details['user_name'];
	   $this->unique_id=$login_details['client_id'];
	   $this->sub_unique_id=$login_details['user_id'];
	   $this->role=$login_details['login_role'];
	   $this->operator_name=($login_details['login_role']==3 ?'Client':'Customer');  
	   $this->short_name=$login_details['business_short_name'];
   }
   else
   {
	   $error=true;
	   $response_code='401';
	   $errortext=$login_response['errortext'];
   }
   
   
   /*if($this->operator_name =='Client')
   {
	   $this->timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$this->unique_id);
	   $this->dateformat=$this->Common_model->get_single_field_value('school_timezone','date_format','school_id',$this->unique_id);
	   $this->mysql_dateformat=$this->mysql_date_format($this->dateformat);
	   $this->mysql_input_dateRgxp=$this->mysql_input_format();
	   
   }*/
   
   $this->regularexp=
	array(
	'any'=>'/^[0-9a-zA-Z ]+$/',
	'name'=>'/^[\pL]*$/',
	'date'=> $this->mysql_input_dateRgxp,
	'alpha_or_numeric'=>'/^[0-9]|([0-9]+[a-zA-Z]+|[a-zA-Z]+[0-9]+)[0-9a-zA-Z]*$/',
	'numeric'=>'/^[0-9]\d*$/',
	'not_zero'=>'/^[1-9]\d*$/',
	'boolean'=>'/^(?i)(true|false|1|0)$/',
	'decimal'=>'/^\d+(\.(\d{2}))?$/',
	'order_type'=>'/^\b(1|2)\b$/',
	'product_type'=>'/^\b(1|2)\b$/',
	'payment_type'=>'/^\b(C|ACH)\b$/',
	'time'=>'/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',
	'email'=>'/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',
	'credit_card'=>'/^([0-9]{4})[-|s]*([0-9]{4})[-|s]*([0-9]{4})[-|s]*([0-9]{2,4})$/',
	'cvv'=>'/^[0-9]{3,4}$/',
	'2digit'=>'(0[1-9]|1[0-2])\/[0-9]{2}',
	'varchar250'=>'/^.{250,}$/',
	'phone'=>'/^\d{10}$/',
	
	);
 
 }	


public function check_role_permission()
{
	$error=false;
	$errortext='';
	$result='';
	
	$role_permission_array['sub_unique_id']=$this->sub_unique_id;
	$role_permission_array['unique_id']=$this->unique_id;
	$role_permission_array['role']=$this->role;
	$result=$this->Common_model->get_role_permission();
	$response=$this->Common_model->permission_url($role_permission_array,$result,$this->uri->uri_string(),$this->input->method(TRUE));
	
	if($this->role=="")
	{
		$error=true;
		$response_code='401';
		$errortext='Unauthorised access';
		$result='';
	}
	
	if(!$error)
	{
		if(!$response)
		{
			$error=true;
			$response_code='401';
			$errortext='Unauthorised access';
			$result='';
		}
	}
	
    return array('error' => $error, 'errortext' => $errortext, 'result' => $result);		
}


 
public function check_active_client($unique_id)
{
	$error=false;
	$errortext='';
	$result='';
	
	$check_active=$this->db->select('*')->where('id',$unique_id)->where('Is_active',1)->get('school');
	//echo $this->db->last_query();
	//die();
	if(!$check_active->num_rows()>0)
	{
		$error=true;
		$errortext='Client is inacive';	
	}
	
    return array('error' => $error, 'errortext' => $errortext, 'result' => $result);		
}
 
public function CheckGuestUserHeader()
{
	$error=false;
	$errortext='';
	$result='';
	
	if($this->token_id=="")
	{
		$headers=getallheaders();
		if(!isset($headers['Session']))
		{
		   $error=true;
		   $errortext='Session required';
		}
		
		if(!isset($headers['Api-Key']))
		{
		   $error=true;
		   $errortext='Api-Key required';
		}
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	
}
 
public function get_api_key()
{
	
	$error=false;
	$errortext='';
	$result='';
	
	$key_array='';
	$auth_key=0;
	$headers=getallheaders();
	
	if(isset($headers['Api-Key']))
	{
	   $x_api_key =$headers['Api-Key'];
	}
	else
	{
		$x_api_key="";
	}
	
	if($x_api_key!="")
	{
		$rs=$this->db->select('*')->where('key',$x_api_key)->get('keys');
		//echo $this->db->last_query();
		//die();
		if($rs->num_rows()>0)
		{
			$key_array=$rs->row_array();
			$result['key_array']=$key_array;
		}
		else
		{
			$error=true;
		    $errortext='Invalid Api-Key';
			$result['x_api_key']=$x_api_key;
		}	
	}
	else
	{
		$error=true;
		$errortext='Unauthorised access BCZ api-key';
		$result['x_api_key']=$x_api_key;
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	
}
 
public function get_login_details()
{
	$error=false;
	$errortext='';
	$result='';
	
	
	$auth_key=0;
	$headers=getallheaders();
	
	//print_r($headers);
	//die();
	
	if(isset($headers['Auth-Token']))
	{
	   $user_request_authkey =$headers['Auth-Token'];
	}
	else
	{
		$user_request_authkey="";
	}
	
	if(isset($headers['Api-Key']))
	{
	   $x_api_key =$headers['Api-Key'];
	}
	else
	{
		$x_api_key="";
	}
	

	if($user_request_authkey!="")
	{
		//$check_auth_key=$this->db->select('*')->where('token_id',$user_request_authkey)->order_by('token_id','desc')->get('db_session');
		
		
		$rs=$this->db->select('login.id as login_id')
			->select('login.role as login_role')
			->select('login.login_is_active')
			->select('school.id as client_id')
		
		 ->select('sub_role_details.id as user_id')
		 ->select('sub_role_details.name as user_name')
		 ->select('school.name as business_name')
		 ->select('school.short_name as business_short_name')
		 ->select('role_master.role_name as role_name')
		 ->from('login')
		 ->join('school','school.school_login_id = login.id')
		 ->join('sub_role_details','sub_role_details.sub_role_details_login_id = login.id')
		 ->join('role_master','role_master.id = sub_role_details.role_master_id')
		 ->join('db_session','db_session.db_session_login_id = login.id')
		 ->where('db_session.token_id',$user_request_authkey)
		 ->get();
		
		
		if($rs->num_rows()>0)
		{
			$autologin_details=$rs->row_array();
			
			
			
			
			if($autologin_details['login_is_active'])
			{
				
				/********************************************/
				
				$result['login_details']=$autologin_details;
				$result['auth_token']=$user_request_authkey;
				$unique_id=$autologin_details['client_id'];
				
				
				
				if($x_api_key!="")
				{
					$rs=$this->db->select('*')->where('client_id',$unique_id)->where('key',$x_api_key)->get('keys');
					//echo $this->db->last_query();
					//die();
					if(!$rs->num_rows()>0)
					{
						$error=true;
						$errortext='Invalid API-KEY';
					}
					else
					{
						$this->x_api_key=$x_api_key;
					}
				
				}
				
				$result['x_api_key']=$x_api_key;
				
				
				/******************************************/
			}else{
				$error = true;
				$errortext .= 'User is inactive';
			}
			
			
		}
		else
		{
			$error=true;
			$errortext='Session expired';
			
		}
	}
	else
	{
		$error=true;
		$errortext='Auth-Token not found';
	}
	

	
		
   return array('error' => $error, 'errortext' => $errortext, 'result' => $result);		
}
 
 public function getTimeZone($school_id)
 {
	 $timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$school_id);
	 return $timezone;
 }
 
 
 public function mysql_db_format($input_date)
 {
	 $input_date_format='';
	 $dateformat=$this->dateformat;
	 switch($dateformat)
	 {
		 case 'dd/mm/yy':
		 $input_date_format=date('Y-m-d', strtotime(str_replace('/', '-', $input_date)));
		 break;
		 
		 case 'mm/dd/yy':
		 $input_date_format=date('Y-m-d', strtotime(str_replace('-', '/', $input_date)));
		 break;
		 
		 case 'yy/mm/dd':
		 $input_date_format=date('Y-m-d', strtotime(str_replace('/', '-', $input_date)));
		 break;
	 }
	 
	 return $input_date_format;
 }
 
 public function mysql_input_format()
 {
	 $input_date_format='';
	 $dateformat=$this->dateformat;

	 switch($dateformat)
	 {
		 case 'dd/mm/yy':
		 $input_date_format='/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
		 break;
		 
		 case 'mm/dd/yy':
		 $input_date_format='/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/';
		 break;
		 
		 case 'yy/mm/dd':
		 $input_date_format='/^\d{4}-((0\d)|(1[012]))-(([012]\d)|3[01])$/';
		 break;
	 }
	 
	 return $input_date_format;
 }
 
 public function mysql_date_format($dateformat)
 {
	 $date_format='';
	 switch($dateformat)
	 {
		 case 'dd/mm/yy':
		 $date_format='d-m-Y';
		 break;
		 
		 case 'mm/dd/yy':
		 $date_format='m-d-Y';
		 break;
		 
		 case 'yy/mm/dd':
		 $date_format='Y-m-d';
		 break;
	 }
	 
	 return $date_format;
 }
 
 
 public function session_exp()
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
      $login_response=$this->get_login_details();
	  
	 
	  
      if($login_response['error'])
	  {
		 $error=true;
		 $errortext=$login_response['errortext'];
	  }
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function authentication($op_name)
 {
	 $error='';
	 $errortext='';
	 $result='';
	
	 if($this->operator_name !=$op_name)
	 {
		$error=true;
		$errortext='Invalid user'; 
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
function buildTree(array $elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
			// loop for all sub or sub-sub menu
            $children = $this->buildTree($elements, $element['id']);
            if ($children) {
                $element['sub_menu'] = $children;
            }
            $branch[] = $element;
        }
    }

    return $branch;
}

 
 public function GetClientPermissionClass($parent_id='',$module_name='',$menu_type='')
 {
	 
	 $class_list=array();
	 if(($parent_id!=''  || $menu_type!='') && $module_name!=''){
		
		$has_child = 0; 
		
		$module_display_name='';
		$module_class_name = '';
		$sql_menu_type='';
		$sql_parent_id = '' ;
		$sql_module_name = '';
		if($menu_type!='')
		{
			$sql_menu_type=" AND method.menu_type='$menu_type'";
		}
		if($parent_id!='')
		{
			$sql_parent_id=" AND method.parent_id=$parent_id";
		}
		if($module_name!='')
		{
			$sql_module_name=" AND module.class_name='$module_name'";
		}
		
		$sql="SELECT 
			method.id as id,
			method.parent_id as parent_id,
			method.module_id as module_id,
			method.method_description as method_description,
			method.method_name as method_name,
			method.type as type,
			method.is_active as is_active,
			method.menu_type as menu_type,
			method.info as info,
			module.module_name as module_name,
			module.class_name as class_name,
			module.is_active as module_is_active,
			module.details as module_details
			 
			FROM method,module
			 
			WHERE  
			module.id=method.module_id".$sql_menu_type.$sql_parent_id.$sql_module_name;
			
		
		 $query = $this->db->query($sql);
		 if($query->num_rows()>0)
		 {
			
					
			 foreach($query->result_array() as $item){
				 
					$module_display_name = $item['module_name'];
					$module_class_name = $item['class_name'];
					$has_child = $this->Common_model->get_single_field_value('method','count(*)','parent_id',$item['id']);
					$module_details = $item['module_details'];
				 
				 $menu_arr[]=array(
					"id" => $item['id'],
					"parent_id" => $item['parent_id'],
					"display_name" => $item['method_description'],
					"api" => $item['method_name'],
					"type" => $item['type'],
					"menu_type"=>$item['menu_type'],
					"has_child" => $has_child,
					"info"=>$item['info'],
					);
				
				
			 }
			
			 $class_list[]=array
				(
				  'display_name'=>$module_display_name,
				  'module_name'=>$module_class_name,
				  'module_details'=>$module_details,
				  'method_list'=>$menu_arr
				);
		 }
	 }
	 else{
		 
		 
	 	$check_auth=$this->db->select('*')->where('client_id',$this->unique_id)->get('rest_class_permission');
		 if($check_auth->num_rows()>0)
		 {
			$class_list_array=$check_auth->result_array();
			foreach($class_list_array as $item)
			{
				$module_name=$this->Common_model->get_single_field_value('module','module_name','id',$item['module_id']);
				$class_name=$this->Common_model->get_single_field_value('module','class_name','id',$item['module_id']);
				$module_details = $this->Common_model->get_single_field_value('module','details','id',$item['module_id']);
				
				$menu_arr=array();
				$method_list=$this->GetClientMethodList($item['module_id'],$menu_type,$parent_id);
				foreach($method_list as $item)
				{
					$has_child = $this->Common_model->get_single_field_value('method','count(*)','parent_id',$item['id']);
					
					
					$menu_arr[]=array(
					"id" => $item['id'],
					"parent_id" => $item['parent_id'],
					"display_name" => $item['method_description'],
					"api" => $item['method_name'],
					"type" => $item['type'],
					"menu_type"=>$item['menu_type'],
					"has_child" => $has_child,
					"info"=>$item['info'],
					);
				}
				
				
				
				$class_list[]=array
				(
				  'display_name'=>$module_name,
				  'module_name'=>$class_name,
				  'module_details'=>$module_details,
				  //'method_list'=>$this->buildTree($menu_arr)
				  'method_list'=>$menu_arr
				);
			}
		 }
	 
	 }
	
	 return $class_list;
 }
 
 public function GetClientMethodList($module_id,$menu_type='',$parent_id='')
 {
	 $method_list=array();
	 
	 $sql_menu_type ='';
	 $sql_parent_id = '';
	 if($menu_type!='')
	 {
		 $sql_menu_type = " AND menu_type='$menu_type'";
	 	
	 }
	 if($parent_id!='')
	 {
		 $sql_parent_id = " AND parent_id = $parent_id";
	 }
	 $sql = "SELECT * FROM method WHERE module_id=$module_id".$sql_menu_type.$sql_parent_id;
	 $query = $this->db->query($sql);
	 
	 if($query->num_rows()>0)
	 {
		$method_list=$query->result_array();
	 }
	 
	 return $method_list;
 }
 
 public function CheckSkipMethod($check_permission)
 {
	$skip_parameter=false; 
	$request_method_type=$this->input->server('REQUEST_METHOD');
	$request_method_name=$this->router->fetch_method();
	foreach($check_permission as $key_value=>$method_item)
	{
		if($key_value==$request_method_name)
		{
			foreach($method_item as $row)
			{
				$skip_cat=array("a");
				if($row==$skip_cat)
				{
					$skip_parameter=true;
					break;
				}
			}
		}
	}
	
	return $skip_parameter;
	 
 }
 
 public function CheckClassPermission()
 {
	 $error=false;
	 $errortext='';
	 $result='';

	 $fetch_class=$this->router->fetch_class();
	 $module_id=$this->Common_model->get_single_field_value('module','id','class_name',$fetch_class);
	 
	 
	 $check_auth=$this->db->select('*')->where('client_id',$this->unique_id)->where('module_id',$module_id)->get('rest_class_permission');
	 
	 
	 if(!$check_auth->num_rows()>0)
	 {
		$error=true;
		$errortext='Unauthorised access';
	 }
	 
	 if(!$error)
	 {
		if($fetch_class=='offrent') 
		{
			$client_product_type=$this->Common_model->get_single_field_value('school','product_type','id',$this->unique_id);
			if($client_product_type==2)
			{
				$error=true;
				$errortext='Unauthorised access';
			}
		}
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
	
}
?>