<?php $this->load->view('admin/header');?>




    <!---- /title section----->
    <!--- Container ----->
    <div class="container">
            <div class="row">
            
            <div class="container page-title">
                <h2>Add new time zone</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/time_zone_list');?>">Time zone list</a></li>
                 <li class="active">Add new</li>
                </ol>
            </div>
       
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">
                
                <?php 
				 if(!empty($timezone_details))
				 {
				   
				   $id=$timezone_details['id']; ?>
                    <form id="attandance_report" method="post" action="<?php echo base_url("admin/update_time_zone/$id");?>">
						<div class="form-group">
                            <label for="exampleInputFile">Select school</label>
                            <select class="form-control" name="school_id" id="school_id">
                                <option value="">Select school</option>
                                <?php foreach($school_list as $item) { 
								
								 $selected='';
								 if($item['id']==$timezone_details['school_id'])
								 {
									 $selected='selected';
								 }
								
								?>
                                  <option <?php echo $selected;?> value="<?php echo $item['id']; ?>">
                                    <?php echo $item['name'];?>
                                  </option>
                                <?php } ?>
                              </select>
                        </div>  
                        
                        
                        
                        <div class="form-group">
                            <label for="exampleInputFile">Select timezone</label>
                            <select class="form-control" name="time_zone" id="time_zone">
                                <option value="">Select timezone</option>
                                <?php foreach($tz_list as $t) { 
								   $selected='';
								   if($t['zone']==$timezone_details['timezone'])
								   {
									 $selected='selected';
								   }
								
								?>
                                  <option <?php echo $selected;?> value="<?php echo $t['zone'] ?>">
                                    <?php echo $t['diff_from_GMT'] . ' - ' . $t['zone'] ?>
                                  </option>
                                <?php } ?>
                              </select>
                        </div>  

                        <div class="form-group">
                            <label for="exampleInputFile">Select Date format</label>
                            <select class="form-control" name="date_format" id="date_format">
                            <option value="">Select date format</option>
                            <option <?php if ($timezone_details['date_format'] == 'dd/mm/yy') echo 'selected' ; ?> value="dd/mm/yy">dd/mm/yyyy</option>
                            <option <?php if ($timezone_details['date_format'] == 'mm/dd/yy') echo 'selected' ; ?> value="mm/dd/yy">mm/dd/yyyy</option>
                            <option <?php if ($timezone_details['date_format'] == 'yy/mm/dd') echo 'selected' ; ?> value="yy/mm/dd">yyyy/mm/dd</option>
                            </select>
                        </div> 
                        
                       
                       <button type="submit" class="btn btn-default">Update</button>
                       <a class="btn btn-default" href="<?php echo base_url();?>admin/time_zone_list">Cancel</a>
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