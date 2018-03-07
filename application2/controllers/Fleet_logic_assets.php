<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Fleet_logic_assets extends REST_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		$this->load->model('Login_model');
		
		$this->check_permission=array(
			'get_method_list'=>array('GET'=>array("a")),
			'index'=>array('GET'=>array(3),'POST'=>array(3),'PUT'=>array(3),'DELETE'=>array(3)),
			
		);
	}
	
	public function get_method_list_get()
	{
		
		$data['method_list']=get_class_methods($this);
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','fleet_logic_assets');
		$this->response($data);
		
	}
	public function index_get()
	{
		/* for all assets & can search any asset by ***/
	}
	public function index_put()
	{
		/* edit any asset **/
	}
	public function index_post()
	{
		/* insert new asset ***/
	}
	public function index_delete()
	{
		/* delete any asset ****/
	}
	
	public function asset_location_get()
	{
		/* for getting asset service location / address & geo co-ordinate, input must be asset_id ***/
	}
	public function asset_location_put()
	{
	}
	public function asset_location_post()
	{
	}
	public function asset_location_delete()
	{
	}
	
}
?>