<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Account_model extends CI_Model
{
	
	private $page_default =0;
	private $records_default =20;
	
	function __construct() {
		$this->load->model('Tables_model');
		$this->load->model('Login_model');
	}
	
	function getAccounts($parameters)
	{
		$accounts= array(
			'account_id'=>'id',
			'account_name'=>'name',
			'account_email'=>'email',
			'account_phone_no'=>'phone_no',
			'account_is_active'=>'is_active',

		);

		 $customer_list=array();
		 $client_id= $this->Basic_model->unique_id;
		 $page = $parameters['page']-1;
		 $records= $parameters['records'];
		 $account_name = $parameters['account_name'];
		 if($page=='' || $page <= 0 )
		 {
			 	$page = $this->page_default;
		 }
		 if($records=='' || $records > 20)
		 {
			 $records=$this->records_default;
		 }
		 
		 $column_account_id = $this->Tables_model->account('account_id');
		 $column_account_name = $this->Tables_model->account('account_name');
		 $column_account_is_active  = $this->Tables_model->account('account_is_active');
		 $column_account_phone = $this->Tables_model->account('account_phone');
		 $column_account_email = $this->Tables_model->account('account_email');
		 $column_client_id = $this->Tables_model->account('client_id');
		 
		 if($account_name!='')
		 {
		 	

		 	$rs=$this->db->select($this->Tables_model->select('account','*'))
		 					->where( $column_client_id,$client_id)
							->like($column_account_name, $account_name)
							->or_like($column_account_phone, $account_name)
							->or_like($column_account_email, $account_name)
							->order_by($column_account_id, 'DESC')
							->get($this->Tables_model->account);
		 }else{
			
			
			$rs=$this->db->select($this->Tables_model->account('*'))
			 			->where($this->Tables_model->account('client_id'),$client_id)
			 			->order_by($column_account_id, 'DESC')
						->get($this->Tables_model->account,$records,$page);
		 }
		
		 
		 if($rs->num_rows()>0)
		 {
			  $customer_arr=$rs->result_array();
			  foreach($customer_arr as $item)
			  {
				  $id=$item['id'];
				  $email=$this->Login_model->get_employee_login_data('email',$id);
				  $phone_no=$this->Login_model->get_employee_login_data('phone_no',$id);
				  $customer_list[]=array(
					 $accounts['account_id']=>$item[$column_account_id],
					 $accounts['account_name']=>$item[$column_account_name],
					 $accounts['account_email']=>$email,
					 $accounts['account_phone_no']=>$phone_no,
					 $accounts['account_is_active']=>$item[$column_account_is_active]
				  );
			  }
		 }
		 
		 return $customer_list;
	}
	function getTotalRecords($parameters)
	{
		
		 $client_id = $this->Basic_model->unique_id;
		 $account_name = $parameters['account_name'];
		 if($account_name!='')
		 {
		 	return $this->db
		 			->where($this->Tables_model->account('client_id'),$client_id)
		 			->like($this->Tables_model->account('account_name'), $account_name)
					->or_like($this->Tables_model->account('account_phone'), $account_name)
					->or_like($this->Tables_model->account('account_email'), $account_name)
		 			->from($this->Tables_model->account)
		 			->count_all_results();
		 }else{
		 	return $this->db
		 			->where($this->Tables_model->account('client_id'),$client_id)
		 			->from($this->Tables_model->account)
		 			->count_all_results();
		 }
	}
}
?>