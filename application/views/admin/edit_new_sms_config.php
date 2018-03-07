<?php $this->load->view('admin/header');?>
<script>
$(document).ready(function() {
  $("#school_list").attr("disabled", true);
});
</script> 



    <!---- /title section----->
    <!--- Container ----->
    <div class="container">
            <div class="row">
            
            <div class="container page-title">
                <h2>Edit config</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/sms_config');?>">Sms configuration list</a></li>
                 <li class="active">Edit</li>
                </ol>
           </div>
            
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
             
           
  				<div class="col-md-6 col-md-offset-3">
                
                <?php
				 if(!empty($sms_config))
				 {
					 $school_name=$this->Common_model->get_single_field_value('school','name','id',$sms_config['school_id']);
				     $id=$sms_config['id'];
				?>
                    <form id="attandance_report" method="post" action="<?php echo base_url("admin/update_sms_config/$id");?>">
                    
                        <div class="form-group">
                        <label for="name">School</label>
                        <input type="text" name="school_name" readonly="readonly" value="<?php echo $school_name; ?>" class="form-control" placeholder="School name">
                        <input type="hidden" name="school_list" value="<?php echo $sms_config['school_id']; ?>" class="form-control" placeholder="Total sms">
                       </div>
                       
                        <div class="form-group">
                        <label for="name">Vendor</label>
                        <select class="form-control" name="vendor_list" id="vendor_list">
                          <option value="">Select vendor</option>
                          <?php 
                          if(!empty($sms_vendor_list)){
                             
							  $vendor_id_db=$sms_config['sms_vendor_id'];
                              foreach($sms_vendor_list as $item)
                              {
                                  $vendor_name=$item['vendor_name'];
                                  $vendor_id=$item['id'];
								  $selected='';
								  if($vendor_id_db==$vendor_id)
								  {
									 $selected='selected';  
								  }
								  
                                  echo "<option value='$vendor_id' $selected>$vendor_name</option>";
                              }
                          }
                          ?>
                
                        </select>
                       </div>
                        <div class="form-group">
                            <label for="exampleInputFile">Total sms</label>
                            <input type="text" name="total_sms" value="<?php echo $sms_config['total_sms']; ?>" class="form-control" placeholder="Total sms">
                        </div>  
                        
                         <div class="form-group">
                            <label for="exampleInputFile">Sms content</label>
                            <input type="text" name="sms_text" value="<?php echo $sms_config['sms_text']; ?>" class="form-control" placeholder="Sms content">
                        </div> 

                      <div class="form-group">
                        <label for="exampleInputFile">Expiry date</label>
                        <input type="date" name="exp_date" value="<?php echo $sms_config['expire_date_time']; ?>" class="form-control" placeholder="Expiry date">
                        
                      </div>
                          
                       <button type="submit" class="btn btn-default">Update</button>
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
 	<!--- Container ----->
    <!--- footer ----->
    <!--- /footer ----->
 <?php $this->load->view('admin/footer'); ?>