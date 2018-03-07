<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Mysession extends REST_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('Rest_model');
		$this->load->model('Basic_model');
		$this->load->model('Common_model');
		
		
		
		$this->check_permission=array(
			'index'=>array('GET'=>array(0,3,4)),
			'get_method_list'=>array('GET'=>array("a")),
		);
	}
	
	public function get_method_list_get()
	{
		
		//$this->load->model('Admin_model','',TRUE);
		$data['method_list']=get_class_methods($this);
		$data['fetch_class_name']='mysession';
		$data['module_id']=$this->Common_model->get_single_field_value('module','id','class_name','mysession');
		$this->response($data);
		/*if($parames==1)
		{
		  $this->load->view('admin/method_list_new',$data);
		}
		if($parames==2)
		{
		  $this->load->view('admin/method_list',$data);
		}*/
	}
	public function index_get()
	{
		$this->response(array('mysession'=>$this->session->session_id));
		
	}
	
}
?>