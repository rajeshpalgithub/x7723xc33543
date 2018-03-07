<?php $this->load->view('admin/header');?>
<script>
$( document ).ready(function() 
{
	$('#st_class').change(function() {   
    var selectVal = $('#st_class :selected').val();
	
	var url='<?php echo base_url();?>'+'school/sectionlist/'+selectVal;
	   
	   $.ajax({
        url: url,
        type: 'GET',
        success: function(data) 
		{   
			//alert(data);
		    $('#st_section').empty();
            $('#st_section').append("<option value=''>Select section</option>"+data);
         },
		error: function (xhr, ajaxOptions, thrownError) 
		{	
			error_msg=xhr.status + " "+thrownError+" "+xhr.responseText;
			$('#errclass').html("<span class='alert alert-danger'>"+error_msg+"</span>");
			
		}
       });
    
   });
    
	
});
</script> 



    <!---- /title section----->
    <!--- Container ----->
    <div class="container">
            <div class="row">
            
            <div class="container page-title">
                <h2>Edit sms vendor</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/sms_vendor_list');?>">Vendor list</a></li>
                 <li class="active">Edit</li>
                </ol>
            </div>
            
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">
                
               
                    <?php
					if(!empty($vendor_details))
					{
					$id=$vendor_details['id'];
					?>
                    <form id="attandance_report" method="post" action="<?php echo base_url("admin/update_sms_vendor/$id");?>">

                        <div class="form-group">
                            <label for="exampleInputFile">Vendor name</label>
                            <input type="text" name="vendor_name" value="<?php echo $vendor_details['vendor_name'];?>" class="form-control" placeholder="Vendor name">
                        </div>  

                        <div class="form-group">
                            <label for="exampleInputFile">Api key</label>
                            <input type="text" name="api_key" value="<?php echo $vendor_details['api_key'];?>" class="form-control" placeholder="Api key">
                        </div> 
                        
                         <div class="form-group">
                            <label for="exampleInputFile">End point</label>
                            <input type="text" name="end_point" value="<?php echo $vendor_details['end_point'];?>" class="form-control" placeholder="End point">
                        </div>  
                          
                       <button type="submit" class="btn btn-default">Submit</button>
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