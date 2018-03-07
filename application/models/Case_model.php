<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Case_model extends CI_Model
{
	private $client_id;
	function __construct() {
		$this->load->model('Tables_model');
		$this->load->model('Basic_model');
		
		$this->$client_id = $this->Basic_model->unique_id;
	}
	
	
	private $records_default=20;
	private $page_deafult=0;
	
	function getCases($parameters)
	{
		$cases=array(
			'case_id'=>'id',
			'case_subject'=>'subject',
			'case_details'=>'details',
			'case_status'=>'status',
			'case_priority'=>'priority',
			'case_account_id'=>'account_id',
			'case_account_name'=>'account_name',
			'case_contact_id'=>'contact_id',
			'case_contact_name'=>'contact_name',
			'case_create_date_time'=>'date_time_open',
			
			
		);
		
		$cases_return=array();
		
		$page = $parameters['page']-1;
		$records = $parameters['records'];
		$search_text  = $parameters['search_text'];
		$account_id = $parameters['account_id'];
		if($page=='' || $page <= 0)
		{
			$page = $this->page_deafult;
		}
		if($records=='')
		{
			$records = $this->records_default;
		}
		$column_case_id= $this->Tables_model->cases('case_id');
		$column_case_subject =$this->Tables_model->cases('case_subject');
		$column_open_date_time= $this->Tables_model->cases('case_create_date_time');
		$column_case_priority= $this->Tables_model->cases('case_priority');
		$column_account_id = $this->Tables_model->cases('case_account_id');
		$colum_case_create_date_time = $this->Tables_model->case('case_create_date_time');
		
		
	 	
		
		if($search_text!='' && $account_id!=''){
			$rs=$this->db
			->select($column_case_id)
			->select($column_case_subject)
			->select($column_open_date_time)
			->select($column_case_priority)
			->where($this->Tables_model->cases('case_client_id'),$this->$client_id)
			->where($column_account_id,$account_id)
			->like($column_case_subject,$search_text)
			->order_by($colum_case_create_date_time, 'DESC')
			->get($this->Tables_model->cases);
		
		}else if($search_text=='' && $account_id!='')
		{
			$rs=$this->db
			->select($column_case_id)
			->select($column_case_subject)
			->select($column_open_date_time)
			->select($column_case_priority)
			->where($this->Tables_model->cases('case_client_id'),$this->$client_id)
			->where($column_account_id,$account_id)
			->order_by($colum_case_create_date_time, 'DESC')
			->get($this->Tables_model->cases,$records,$page);
		}
		else if($search_text!='' && $account_id=='')
		{
			$rs=$this->db
			->select($column_case_id)
			->select($column_case_subject)
			->select($column_open_date_time)
			->select($column_case_priority)
			->where($this->Tables_model->cases('case_client_id'),$this->$client_id)
			->like($column_case_subject,$search_text)
			->order_by($colum_case_create_date_time, 'DESC')
			->get($this->Tables_model->cases);
		}
		else{
			$rs=$this->db
			->select($column_case_id)
			->select($column_case_subject)
			->select($column_open_date_time)
			->select($column_case_priority)
			->where($this->Tables_model->cases('case_client_id'),$this->$client_id)
			->order_by($colum_case_create_date_time, 'DESC')
			->get($this->Tables_model->cases,$records,$page);
		}
				 
		if($rs->num_rows()>0)
		 {
			  $cases_arr=$rs->result_array();
			  
			
			  foreach($cases_arr as $item)
			  {
				 
				  $cases_return[]=array(
					 $cases['case_id']=>$item[$column_case_id],
					 $accounts['case_subject']=>$item[$column_case_subject],
					 $accounts['case_create_date_time']=>$item[$column_open_date_time],
					 $accounts['case_priority']=>$item[$column_case_priority],
					
				  );
			  }
		 }
		 return $cases_return;
		
	}
	function total_records($parameters)
	{
		
		return $this->db
		 			->where($this->Tables_model->cases('client_id'),$this->$client_id)
		 			->from($this->Tables_model->cases)
		 			->count_all_results();
	}
	function addCase($parameters)
	{
		$column_case_client_id = $this->Tables_model->cases('case_client_id');
		$parameters[$column_case_client_id]=$this->client_id;
		return $this->db->insert('mytable', $parameters);
		
	}
	
	
}
?>