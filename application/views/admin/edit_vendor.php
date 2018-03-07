<?php $this->load->view('admin/header');?>
   	
    
    <!---/header------>
    <!--- Container ----->
    <div class="container">
    <div id='errclass'></div>
            <?php echo validation_errors(); ?>
            <?php echo $this->session->flashdata('message'); ?>
          <div class="row">
              
              <div class="container page-title">
                <h2>Edit vendor</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/vendor_list');?>">Vendor</a></li>
                 <li class="active">Edit</li>
                </ol>
            </div>   
             
  			<div class="col-md-6 col-md-offset-3">
            	<!--<div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Success!</strong> Student : <b>[name of student] - [ Card No ] - [Class] - [Reg No]</b> Add success.
                </div>
                <div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Error!</strong> Erorr list why not added bcz validation or studet card exist etc....
                </div>-->
            	 
                 <?php 
				 if(!empty($vendor_data) && !empty($login_data))
				 {
				 
				 $id=$vendor_data['id'] ?>
                <form id="add_student" method="post" action="<?php echo base_url("admin/update_vendor/$id");?>">
                *** (Leave blank for same password)
               
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" value="<?php echo $vendor_data['name'];?>" name="name" placeholder="Name">
                  </div>
                  <div class="form-group">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" value="<?php echo $login_data['phone_no'];?>" id="phone" name="phone" placeholder="Phone">
                  </div>
                   <div class="form-group">
                    <label for="name">Email</label>
                    <input type="text" class="form-control" value="<?php echo $login_data['email'];?>" id="email" name="email" placeholder="Email">
                  </div>
                  <div class="form-group">
                    <label for="name">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                  </div>
                 
                  <div class="form-group">
                  	<label for="address">Address</label>
                     <input type="text" class="form-control" value="<?php echo $vendor_data['address'];?>" id="address1" name="address1" placeholder="Address1">
                  </div>
                 <div class="form-group">
                   
                    <?php
					  $is_active='';
					  $is_not_active='';
					  if($vendor_data['Is_active']==1)
				      {
						$is_active='checked';
					  }
					  else
					  {
						$is_not_active='checked';
					  }
					?>
                    
                    
                 	<label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" <?php echo $is_active; ?>> Active
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" <?php echo $is_not_active; ?>> In Active
                    </label>
                 </div>
                  <button type="submit" name="add_student" class="btn btn-default">Update</button>
                   <a class="btn btn-default" href="<?php echo base_url();?>admin/vendor_list">Cancel</a>
                </form>
                <?php
					}
					else
					{
						echo '<div class="alert alert-danger">No data found</div>';
					}
					?>
            </div>
          </div>
  		
	</div>
<?php $this->load->view('admin/footer'); ?>