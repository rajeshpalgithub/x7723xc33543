<?php
class Admin_basic_model extends CI_Model
{
	
 public $login_data=''; 
 public $unique_id='';
 public $sub_unique_id='';
 public $role='';
 public $short_name='';
 public $operator_name='';
 //public $product_type='';
 public $timezone='';
 public $dateformat='';
 public $op_role='';
 
 public $start_of_financial_day_month='';
 public $end_of_financial_day_month='';
 
 public $mysql_dateformat='';
 public $mysql_input_dateformat='';
 
 function __construct()
 {
  parent::__construct();
  if($this->session->userdata('login_name'))
  {
	if($this->session->userdata('login_name')!="")
	{
	  $this->login_data=$this->session->userdata('login_name');
	  $this->unique_id=$this->session->userdata('unique_id');
	  $this->sub_unique_id=$this->session->userdata('sub_unique_id');
	  $this->role=$this->session->userdata('role');
	  $this->operator_name=$this->session->userdata('operator_name');  
	  $this->short_name=$this->Common_model->get_single_field_value('school','short_name','id',$this->unique_id);
	  //$this->product_type=$this->Common_model->get_single_field_value('school','product_type','id',$this->unique_id);
	}
   }
   
   if($this->operator_name =='School')
   {
	   $this->timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$this->unique_id);
	   $this->dateformat=$this->Common_model->get_single_field_value('school_timezone','date_format','school_id',$this->unique_id);
	   $this->mysql_dateformat=$this->mysql_date_format($this->dateformat);
   }
   
   $financial_year_data=$this->get_financial_year_data();
   if(!empty($financial_year_data))
   {
	  $start_of_financial_day=$financial_year_data['start_date'];
	  $start_of_financial_month=$financial_year_data['start_month'];
	  $this->start_of_financial_day_month="-$start_of_financial_month-$start_of_financial_day";
	  
	  $end_of_financial_day=$financial_year_data['end_date'];
	  $end_of_financial_month=$financial_year_data['end_month'];
	  $this->end_of_financial_day_month="-$end_of_financial_month-$end_of_financial_day";
   }
   
 
 }	
 
 
 public function get_financial_year_data()
 {
	  $financial_year_data=array(); 
	  $rs=$this->db->select('*')->where('client_id',$this->unique_id)->where('is_active',1)->get('financial_year');
	  if($rs->num_rows()>0)
	  {
		  $financial_year_data=$rs->row_array();
	  }
	  
	  return $financial_year_data;
 }
 
 public function getTimeZone($school_id)
 {
	 $timezone=$this->Common_model->get_single_field_value('school_timezone','timezone','school_id',$school_id);
	 return $timezone;
 }
 
 public function mysql_input_format($input_date)
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
	 

	 if($this->session->userdata('login_name'))
	 {
		 if($this->session->userdata('login_name')=="")
		 {
			$error=true;
			$errortext='Your session is expired'; 
		 }
		 
	 }
	 else
	 {
		 $error=true;
		 $errortext='Your session is expired';
	 }
	 
	 if(!$error)
	 {
		 $check_active=$this->db->select('*')->where('id',$this->unique_id)->where('Is_active',1)->get('school');
		 if(!$check_active->num_rows()>0)
		  {
			$this->load->model('Login_model','',TRUE);
			$this->Login_model->logout_user();  
			$error=true;
			$errortext='Client is inacive';	
		  }
	 }
	 
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 public function authentication($op_name)
 {
	 $error=false;
	 $errortext='';
	 $result='';
	 
	 
     if(!$error)
	 {
		 if($this->operator_name !=$op_name)
		 {
			$error=true;
			$errortext='Invalid user'; 
		 }
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function role_authentication($op_name)
 {
	 $error=false;
	 $errortext='';
	 $result='';

	 $fetch_class=$this->router->fetch_class();
	 $module_id=$this->Common_model->get_single_field_value('site_module','id','class_name',$fetch_class);
	 $check_auth=$this->db->select('*')->where('client_id',$this->unique_id)->where('module_id',$module_id)->get('site_class_permission');
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
	 
	 if(!$error)
	 {
		 if($this->operator_name !=$op_name)
		 {
			$error=true;
			$errortext='Invalid user'; 
		 }
		 else
		 {
			  if($this->sub_unique_id!=0)
			  {
				$role_permission_array['sub_unique_id']= $this->sub_unique_id;
				$role_permission_array['unique_id']=$this->unique_id;
				$role_permission_array['role']=$this->role;
			  
				$result=$this->Common_model->get_role_permission($role_permission_array);
				if(!empty($result))
				{
					//echo $this->uri->uri_string();
					//echo '<pre>',print_r($result);
					//die();
					$fetch_class=$this->router->fetch_class();
					$fetch_method=$this->router->fetch_method();
					
					$request_uri =$fetch_class.'/'.$fetch_method; 
					$permission=$this->Common_model->method_validation($result,$request_uri);
					if(!$permission)
					{
						//$this->load->model('Login_model');
						//$this->Login_model->logout_user();
						$error=true;
						$errortext='Unauthorised access'; 
					}
				}
			  }
		 }
		 
	 }
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
 }
 
 
 public function GetClientPermissionClass()
 {
	 $class_list=array();
	 $check_auth=$this->db->select('*')->where('client_id',$this->unique_id)->get('site_class_permission');
	 if($check_auth->num_rows()>0)
	 {
		$class_list=$check_auth->result_array();
	 }
	 
	 return $class_list;
 }
 
 public function GetClientMethodList($module_id)
 {
	 $method_list=array();
	 $check_auth=$this->db->select('*')->where('module_id',$module_id)->where('type',1)->get('site_method');
	 if($check_auth->num_rows()>0)
	 {
		$method_list=$check_auth->result_array();
	 }
	 
	 return $method_list;
 }
 
}
?>