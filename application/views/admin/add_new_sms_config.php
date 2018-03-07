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
                <h2>Add new config</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/sms_config');?>">Sms configuration list</a></li>
                 <li class="active">Add new</li>
                </ol>
           </div>
            
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">
                
                
                    <form id="attandance_report" method="post" action="<?php echo base_url('admin/add_new_sms_config');?>">
                    
                        <div class="form-group">
                        <label for="name">School</label>
                        <select class="form-control" name="school_list" id="school_list">
                          <option value="">Select school</option>
                          <?php 
                          if(!empty($school_list)){
                             
                              foreach($school_list as $item)
                              {
                                  $school_name=$item['name'];
                                  $school_id=$item['id'];
                                  echo "<option value='$school_id'>$school_name</option>";
                              }
                          }
                          ?>
                
                        </select>
                       </div>
                       
                        <div class="form-group">
                        <label for="name">Vendor</label>
                        <select class="form-control" name="vendor_list" id="vendor_list">
                          <option value="">Select vendor</option>
                          <?php 
                          if(!empty($sms_vendor_list)){
                             
                              foreach($sms_vendor_list as $item)
                              {
                                  $vendor_name=$item['vendor_name'];
                                  $vendor_id=$item['id'];
                                  echo "<option value='$vendor_id'>$vendor_name</option>";
                              }
                          }
                          ?>
                
                        </select>
                       </div>
                        <div class="form-group">
                            <label for="exampleInputFile">Total sms</label>
                            <input type="text" name="total_sms" class="form-control" placeholder="Total sms">
                        </div> 
                        
                         <div class="form-group">
                            <label for="exampleInputFile">Sms content</label>
                            <input type="text" name="sms_text"  class="form-control" placeholder="Sms content">
                        </div>  

                      <div class="form-group">
                        <label for="exampleInputFile">Expiry date</label>
                        <input type="date" name="exp_date" class="form-control" placeholder="Expiry date">
                        
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