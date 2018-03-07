<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Tables_model extends CI_Model
{
	
	
	private $tables = array(
			'account'=>array(
				'table_name'=>'account',
				'columns'=>array(
									'account_id'=>array('column_name'=>'id',
										'meta_data'=>array('data_type'=>'int(11)','is_read_only'=>TRUE,'required'=>TRUE)
									),
									'account_name'=>array('column_name'=>'name',
										'meta_data'=>array('data_type'=>'varchar(250)','is_read_only'=>false,'required'=>TRUE)
									),
									'client_id'=>array('column_name'=>'client_id',
										'meta_data'=>array('data_type'=>'int(11)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_type_id'=>array('column_name'=>'type_id',
										'meta_data'=>array('data_type'=>'int(11)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_email'=>array('column_name'=>'email',
										'meta_data'=>array('data_type'=>'varchar(250)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_phone'=>array('column_name'=>'phone',
										'meta_data'=>array('data_type'=>'varchar(11)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_description'=>array('column_name'=>'description',
										'meta_data'=>array('data_type'=>'text(450)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_is_active'=>array('column_name'=>'is_active',
										'meta_data'=>array('data_type'=>'int(1)','is_read_only'=>false,'required'=>TRUE)
									),
									'account_add_date_time'=>array('column_name'=>'add_date_time',
										'meta_data'=>array('data_type'=>'int(1)','is_read_only'=>true,'required'=>TRUE)
									),
									'account_auth_code'=>array('column_name'=>'auth_code',
										'meta_data'=>array('data_type'=>'varchar(250)','is_read_only'=>true,'required'=>TRUE)
									),
								)

			),
		'cases'=>array(
				'table_name'=>'case',
				'columns'=>array(
						'case_id'=>array('column_name'=>'case_id','meta_data'=>array('data_type'=>'int(11)')),
						'case_parent_id'=>array('column_name'=>'case_parent_id','meta_data'=>array()),
						'case_client_id'=>array('column_name'=>'case_client_id','meta_data'=>array()),
						'case_subject'=>array('column_name'=>'case_subject','meta_data'=>array()),
						'case_details'=>array('column_name'=>'case_details','meta_data'=>array()),
						'case_status'=>array('column_name'=>'case_status','meta_data'=>array()),
						'case_priority'=>array('column_name'=>'case_priority','meta_data'=>array()),
						'case_account_id'=>array('column_name'=>'case_account_id','meta_data'=>array()),
						'case_contact_id'=>array('column_name'=>'case_contact_id','meta_data'=>array()),
						'case_category_id'=>array('column_name'=>'case_category_id','meta_data'=>array()),
						'case_reason_id'=>array('column_name'=>'case_reason','meta_data'=>array()),
						'case_internal_comments'=>array('column_name'=>'case_internal_comments','meta_data'=>array()),
						'case_asset_id'=>array('column_name'=>'case_asset_id','meta_data'=>array()),
						'case_product_id'=>array('column_name'=>'case_product_id'),
						'case_assigment_rule_id'=>array('column_name'=>'case_assigment_rule_id'),
						'case_is_send_notification_email'=>array('column_name'=>'case_is_send_notification_email'),
						'case_created_by'=>array('column_name'=>'case_created_by',),
						'case_create_date_time'=>array('column_name'=>'case_create_date_time',),
						'case_owner'=>array('column_name'=>'case_owner',),

					)
			),
		'case_category'=>array(
				'table_name'=>'case_category',
				'columns'=>array(
					'case_category_id'=>array('column_name'=>'case_category_id','meta_data'=>array()),
					'case_category_name'=>array('column_name'=>'case_category_name','meta_data'=>array()),
					'case_category_id'=>array('column_name'=>'case_category_id','meta_data'=>array()),
					'parent_case_category_id'=>array('column_name'=>'parent_case_category_id','meta_data'=>array()),
					'case_category_client_id'=>array('column_name'=>'case_category_client_id','meta_data'=>array()),
					'case_category_created_by_user_id'=>array('column_name'=>'case_category_created_by_user_id','meta_data'=>array()),
					'case_category_is_active'=>array('column_name'=>'case_category_is_active','meta_data'=>array()),
					'case_category_created_date_time'=>array('column_name'=>'case_category_created_date_time','meta_data'=>array()),
					'case_category_employee_group_id'=>array('column_name'=>'case_category_employee_group_id','meta_data'=>array()),
					'case_category_is_active'=>array('column_name'=>'case_category_is_active','meta_data'=>array()),

					),
			),
		'case_log'=>array(
						'table_name'=>'case_log',
						'columns'=>array(
							'case_log_id'=>array('column_name'=>'case_log_id','meta_data'=>array()),
							'case_log_action_performed'=>array('column_name'=>'case_log_action_performed','meta_data'=>array()),
							'case_log_case_id'=>array('column_name'=>'case_log_case_id','meta_data'=>array()),
							'case_log_date_added'=>array('column_name'=>'case_log_date_added','meta_data'=>array()),
							'case_log_employee_id'=>array('column_name'=>'case_log_employee_id','meta_data'=>array()),
							),
					),
		'roles'=>array(
				'table_name'=>'role_master',
				'columns'=>array(
						'roles_id'=>array('column_name'=>'id','meta_data'=>array()),
						'roles_client_id'=>array('column_name'=>'object_id','meta_data'=>array()),
						'roles_parent_id'=>array('column_name'=>'parent_role_id','meta_data'=>array()),
						'roles_name'=>array('column_name'=>'role_name','meta_data'=>array()),
						'roles_created_by'=>array('column_name'=>'role_master_created_by','meta_data'=>array()),
						'roles_created_date'=>array('column_name'=>'role_date','meta_data'=>array()),
						'roles_is_active'=>array('column_name'=>'is_active','meta_data'=>array()),
						
					),
			),
		'role_users'=>array(
				'table_name'=>'sub_role_details',
				'columns'=>array(
						'role_users_id'=>array('column_name'=>'id','meta_data'=>array()),
						'role_users_roles_id'=>array('column_name'=>'role_master_id','meta_data'=>array()),
						'role_users_name'=>array('column_name'=>'name','meta_data'=>array()),
						'role_users_report_to'=>array('column_name'=>'report_to','meta_data'=>array()),
						'role_users_address'=>array('column_name'=>'address','meta_data'=>array()),
						'role_users_city'=>array('column_name'=>'city','meta_data'=>array()),
						'role_user_state'=>array('column_name'=>'state','meta_data'=>array()),
						'role_users_country'=>array('column_name'=>'country','meta_data'=>array()),
						'role_users_postal_code'=>array('column_name'=>'postal_code','meta_data'=>array()),
						'role_users_is_default'=>array('column_name'=>'is_default','meta_data'=>array()),
						'role_users_is_active'=>array('column_name'=>'is_active','meta_data'=>array()),
						'role_users_created_date'=>array('column_name'=>'date','meta_data'=>array()),
						'role_users_record_owner_id'=>array('column_name'=>'sub_role_details_record_owner_id','meta_data'=>array()),
						'role_users_login_id'=>array('column_name'=>'sub_role_details_login_id','meta_data'=>array()),
						
				),
			),
		'login'=>array(
				'table_name'=>'login',
				'columns'=>array(
					'login_id'=>array('column_name'=>'id','meta_data'=>array()),
					'login_email'=>array('column_name'=>'email','meta_data'=>array()),
					'login_phone_no'=>array('column_name'=>'phone_no','meta_data'=>array()),
					'login_client_id'=>array('column_name'=>'unique_id','meta_data'=>array()),
					'login_password'=>array('column_name'=>'password','meta_data'=>array()),
					'login_role'=>array('column_name'=>'role','meta_data'=>array()),
					'login_user_id'=>array('column_name'=>'sub_unique_id','meta_data'=>array()),
					'login_last_login_timestamp'=>array('column_name'=>'last_login_timestamp','meta_data'=>array()),
					'login_current_login_timestamp'=>array('column_name'=>'current_login_timestamp','meta_data'=>array()),
					'login_last_ip_address'=>array('column_name'=>'last_ip_address','meta_data'=>array()),
					'login_current_ip_address'=>array('column_name'=>'current_ip_address','meta_data'=>array()),
					'login_activation_code'=>array('column_name'=>'activation_code','meta_data'=>array()),
					'login_record_add_date_time'=>array('column_name'=>'record_Add_Date_Time','meta_data'=>array()),
					'login_is_active'=>array('column_name'=>'login_is_active','meta_data'=>array()),
				),
			),
		'client'=>array(
			'table_name'=>'school',
			'columns'=>array(
				'client_id'=>array('column_name'=>'id',''=>array()),
				'client_logo'=>array('column_name'=>'school_logo',''=>array()),
				'client_name'=>array('column_name'=>'name',''=>array()),
				'client_short_name'=>array('column_name'=>'short_name',''=>array()),
				'client_address'=>array('column_name'=>'address',''=>array()),
				'client_country_id'=>array('column_name'=>'country_id',''=>array()),
				'client_city'=>array('column_name'=>'city_village',''=>array()),
				'client_state'=>array('column_name'=>'state',''=>array()),
				'client_pin_code'=>array('column_name'=>'pin_code',''=>array()),
				'client_vendor_id'=>array('column_name'=>'vendor_id',''=>array()),
				'client_time_zone'=>array('column_name'=>'timezone_id',''=>array()),
				'client_is_active'=>array('column_name'=>'is_active',''=>array()),
				'client_login_id'=>array('column_name'=>'school_login_id',''=>array()),
			),
		),
		

	);
	
	function __get($table)
	{
		
		return $this->tables[$table]['table_name'];
	}

	public function __call($table,$argument)
	{
		$columns = '';
		
		$table_columns = $this->tables[$table]['columns'];
		switch($argument[0])
		{
			case '*' : 
				
				foreach($table_columns as $key => $value)
				{
					
					$columns .=  $value['column_name'].',';
				}
				
			break;
			default :
				
				foreach($table_columns as $key => $value)
				{
					
					if(in_array($key,$argument)){
						$columns = $value['column_name'];
					}
				}
				

		}
		return rtrim($columns,',');
	}

	public function select($table,$argument)
	{
		$columns = array();
		
		$table_columns = $this->tables[$table]['columns'];
		
		
		
		switch($argument[0])
		{
			case '*' : 
				
				foreach($table_columns as $key=>$value)
				{
					$columns[$key] = $value['column_name'];
					
				}
				
				break;
				default :
				foreach($table_columns as $key => $value)
				{
					
					$pieces = explode(" ", $argument);
					
					
					if(in_array($pieces[0],$table_columns)){
						
						$pieces_count = count($pieces);
						if($pieces_count > 1)
						{
							$columns[$key] = $value['column_name'].' as '.$pieces[$pieces_count-1];
						}else{
							$columns[$key] = $value['column_name'];
						}
						
					}
				}
				
		}
		return $columns;
	}
	
}
?>