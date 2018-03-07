<?php $this->load->view('admin/header');?>
<script>
$( document ).ready(function() 
{
	
	
});
</script> 



    <!---- /title section----->
    <!--- Container ----->
    <div class="container">
            <div class="row">
            
            <div class="container page-title">
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li class="active">Select class</li>
                </ol>
            </div>
       
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">

                    <form id="attandance_report" method="post" action="<?php echo base_url('admin/class_list');?>">

                        <div class="form-group">
                            <label for="exampleInputFile">Select class</label>
                            <select class="form-control" name="class_name" id="class_name">
                                <option value="">Select class</option>
                                <?php 
								  if(!empty($module_name))
								  {
									  foreach($module_name as $item)
									  {
										  $class_display_name=$item['module_name'];
										  $class_name=$item['class_name'];
										  
										  echo "<option value='$class_name'>$class_display_name</option>";
									  }
								  }
								  ?>
                              </select>
                        </div>  

                         
                        
                       
                       <button type="submit" class="btn btn-default">Submit</button>
					</form>
                
                </div>
			</div>
          
  		
	</div>
 	<!--- Container ----->
    <!--- footer ----->
    <!--- /footer ----->
 <?php $this->load->view('admin/footer'); ?>