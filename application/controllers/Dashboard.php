<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct()
	 {
	   parent::__construct();
	   
	   $this->load->model('Common_model','',TRUE);
	   $this->load->model('Basic_model','',TRUE);
	   $this->load->model('Attandance_model','',TRUE);
	   $this->load->model('Object_model','',TRUE);
	   $this->load->model('Login_model','',TRUE);
	   $this->load->model('Sop_model','',TRUE);
	   $this->load->library('cart');
	   
	       $authentication=$this->Basic_model->session_exp();
		   if($authentication['error'])
		   {
			   $this->session->set_flashdata('message','<div class="alert alert-danger">'.$authentication['errortext'].'</div>');
			   redirect('login','refresh');
		   }
		   else
		   {
			   $op_name=$this->Basic_model->operator_name;
			   $authentication=$this->Basic_model->authentication($op_name);
			   if($authentication['error'])
			   {
				   $this->session->set_flashdata('message','<div class="alert alert-danger">'.$authentication['errortext'].'</div>');
				   redirect('dashboard/error_page','refresh');
			   }
		   }

	 }
	 
	public function index()
	{
		$result="";
		$page=$this->session->userdata('page');
		if($page=="school/dashboard")
		{
			  $sub_unique_id="";
			  $sub_unique_id=$this->Basic_model->sub_unique_id;
			  $unique_id=$this->Basic_model->unique_id;
			  $role=$this->Basic_model->role;
			  
			  if($sub_unique_id!=0)
			  {
			    $role_permission_array['sub_unique_id']=$sub_unique_id;
			    $role_permission_array['unique_id']=$unique_id;
			    $role_permission_array['role']=$role;
				
			    $result=$this->Common_model->get_role_permission($role_permission_array);
				if(!$result)
				{
					$this->session->set_flashdata('message','<div class="alert alert-danger">You are not authorised for login</div>');
			        $this->Login_model->logout_user();
				    redirect('login','refresh');
				}
				
			  }
			  
			$data['order_todo_list']=$this->Sop_model->GetOrderToDoList();
			$data['offrent_todo_list']=$this->Sop_model->GetOffrentToDoList();
			$data['service_todo_list']=$this->Sop_model->GetServiceEquipmentToDoList();	
			$data['result']=$result; 
		    $data['content']=$page;
	        $this->load->view('school/template/template',$data);
		}
		else
		{
			$this->load->view($page);
		}
	}
	
	public function error_page()
	{
		$this->load->view('error_page');
	}
	
	
}
