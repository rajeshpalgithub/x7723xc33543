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
                <h2>Change vendor for school</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/school_list');?>">School list</a></li>
                 <li class="active">Details</li>
                </ol>
            </div>   
    
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
             
  				<div class="col-md-6 col-md-offset-3">
                
                     <?php
					    $id=$this->uri->segment(3);
						$school_name='';
			            $school_name=$this->Common_model->get_single_field_value('school','name','id',$id);
						if($school_name!="")
						{
						
						$vendor_db_id=$this->Common_model->get_single_field_value('school','vendor_id','id',$id);
					 ?>          
                    <form id="attandance_report" method="post" action="<?php echo base_url("admin/update_vendor_school/$id");?>">

                       <div class="form-group">
                        <label for="address">School</label>
                        <input type="text" class="form-control" readonly="readonly" maxlength="6" value="<?php echo $school_name;?>" id="short_name" name="short_name" placeholder="School name">
                        <input type="hidden"  value="<?php echo $id;?>" id="school_id" name="school_id">  
                  </div>
                  
                       <div class="form-group">
                  	<label for="address">Vendor</label>
                     <select class="form-control" name="vendor" id="vendor">
                      <option value="">Select vendor</option>
                      <?php 
					  if(!empty($vendor_list))
					  {
						  
						  foreach($vendor_list as $item)
						  {
							  $vendor_name=$item['name'];
							  $vendor_id=$item['id'];
							  
							  $selected='';
							  if($vendor_db_id==$vendor_id)
							  {
								 $selected='selected';  
							  }
							  
							  echo "<option value='$vendor_id' $selected>$vendor_name</option>";
						  }
					  }
					  ?>
            
                    </select>
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