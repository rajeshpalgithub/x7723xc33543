<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_login extends CI_Controller {
	
	public function __construct()
	 {
	   parent::__construct();
	   
	   $this->load->model('Basic_model','',TRUE);
	   $this->load->model('Admin_login_model','',TRUE);
	   $this->load->model('Common_model','',TRUE);
	   //$this->load->library('cart');
	 }
	 
	 public function test()
	 {
		 echo phpinfo();
	 }

	public function index()
	{
	   $error=false;
	   
	   if($this->session->userdata('login_name'))
	   {
		 if($this->session->userdata('login_name')!="")
		 {	
		    $error=true;
			$this->load->view('admin/dashboard');
		 }
	  }
	  
	  if(!$error)
	  {
		
	   $this->form_validation->set_rules('unique_text', 'Email / phone / Unique id','trim|required');
	   $this->form_validation->set_rules('password', 'Password','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		$this->load->view('login');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $login_data=$this->Admin_login_model->GetUserLogin($post_data);
		  
		   if(!$login_data['error'])
		   {
			 
			   $page=$login_data['result']['dashboard_page'];
			   $this->load->view('admin/dashboard');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid credentials</div>');
			   redirect('admin_login');
		   }
		   
	   }
	  }
	}
	
	public function forgot_password()
	{
	   $this->form_validation->set_rules('email', 'Email','trim|required');
	   $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><ul><li>', '</li></ul></div>');
	   if($this->form_validation->run() == FALSE)
	   {
	   		$this->load->view('forgot_password');
	   }
	   else
	   {
		   $post_data=$this->input->post();
		   $login_data=$this->Admin_login_model->SendUpdatePassword($post_data);
		   if(!$login_data['error'])
		   {
			   $new_pass=$login_data['result']['new_pass'];
			   $this->session->set_flashdata('message', "<div class='alert alert-success'>Password send successfully to your email : $new_pass</div>");
			   redirect('login');
		   }
		   else
		   {
			   $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$login_data['errortext'].'</div>');
			   redirect('login/forgot_password');
		   }
		   
	   }
		
	}

    public function logout_user()
	{
		$this->session->unset_userdata('login_name');
		$this->session->unset_userdata('unique_id');
		$this->session->unset_userdata('operator_name');
		$this->session->unset_userdata('sub_unique_id');
		$this->session->set_flashdata('message', '<div class="alert alert-success">Logout successfully</div>');
		redirect('admin_login','refresh');
	}


}
