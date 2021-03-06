<?php
class Basic_model extends CI_Model
{

 public $login_data=''; 
 public $unique_id='';
 public $sub_unique_id='';
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
   
   
   $login_response=$this->get_login_details();
   if(!$login_response['error'])
   {
	   $login_details=$login_response['result']['login_details'];
	   
	   $this->token_id=$login_details['token_id'];
	   $this->login_data=$login_details['login_name'];
	   $this->unique_id=$login_details['unique_id'];
	   $this->sub_unique_id=$login_details['sub_unique_id'];
	   $this->role=$login_details['role'];
	   $this->operator_name=$login_details['operator_name'];  
	   $this->short_name=$this->Common_model->get_single_field_value('school','short_name','id',$this->unique_id);
   }
   else
   {
	   $errortext=$login_response['errortext'];
   }
   
   $this->image_link=base_url();
   if($this->operator_name =='School')
   {
	   $this->timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$this->unique_id);
	   $this->dateformat=$this->Common_model->get_single_field_value('school_timezone','date_format','school_id',$this->unique_id);
	   $this->mysql_dateformat=$this->mysql_date_format($this->dateformat);
	   $this->mysql_input_dateRgxp=$this->mysql_input_format();
	   
   }
   
   $this->regularexp=
	array(
	'name'=>'/^[\pL]*$/',
	'date'=> $this->mysql_input_dateRgxp,
	'alpha_or_numeric'=>'/^[0-9]|([0-9]+[a-zA-Z]+|[a-zA-Z]+[0-9]+)[0-9a-zA-Z]*$/',
	'numeric'=>'/^[1-9]\d*$/',
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
	
	);
 
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
		$check_auth_key=$this->db->select('*')->where('token_id',$user_request_authkey)->order_by('token_id','desc')->get('db_session');
		if($check_auth_key->num_rows()>0)
		{
			$autologin_details=$check_auth_key->row_array();
			$result['login_details']=$autologin_details;
			$result['auth_token']=$user_request_authkey;
			
			$unique_id=$autologin_details['unique_id'];
			
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
		$errortext='Token not found';
	}
	

	
		
   return array('error' => $error, 'errortext' => $errortext, 'result' => $result);		
}
 
 public function getTimeZone($school_id)
 {
	 $timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$school_id);
	 return $timezone;
 }
 
 public function mysql_input_format()
 {
	 $input_date_format='';
	 $dateformat=$this->dateformat;

	 switch($dateformat)
	 {
		 case 'dd/mm/yyyy':
		 $input_date_format='/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
		 break;
		 
		 case 'mm/dd/yyyy':
		 $input_date_format='/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/';
		 break;
		 
		 case 'yyyy/mm/dd':
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
		 case 'dd/mm/yyyy':
		 $date_format='d/m/Y';
		 break;
		 
		 case 'mm/dd/yyyy':
		 $date_format='m/d/Y';
		 break;
		 
		 case 'yyyy/mm/dd':
		 $date_format='Y/m/d';
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
 
	
}
?>