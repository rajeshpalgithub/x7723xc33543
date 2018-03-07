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
                <h2>Smtp setting</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li><a href="<?php echo base_url('admin/sms_vendor_list');?>">Vendor list</a></li>
                 <li class="active">Add new</li>
                </ol>
            </div>
       
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">
                
                <?php
				 $smtp_host='';
				 $smtp_id='';
				 $smtp_user='';
				 $smtp_pass='';
				 $smtp_port='';
				 $store_name='';
				 $store_email='';
				 
				 if(!empty($smtp_details))
				 {
					 $smtp_host=$smtp_details['smtp_host'];
					 $smtp_id=$smtp_details['id'];
					 $smtp_user=$smtp_details['smtp_user'];
					 $smtp_pass=$smtp_details['smtp_pass'];
					 $smtp_port=$smtp_details['smtp_port'];
					 $store_name=$smtp_details['store_name'];
					 $store_email=$smtp_details['store_email'];
				 }
				
				?>
                  
                
                
                    <form id="smtp_setting" method="post" action="<?php echo base_url('admin/smtp_setting');?>">

                        <div class="form-group">
                            <label for="exampleInputFile">Host</label>
                            <input type="text" name="smtp_host" value="<?php echo $smtp_host;?>" class="form-control" placeholder="Host">
                            <input type="hidden" name="smtp_id" value="<?php echo $smtp_id;?>">
                        </div>  

                        <div class="form-group">
                            <label for="exampleInputFile">User</label>
                            <input type="text" name="smtp_user" value="<?php echo $smtp_user;?>" class="form-control" placeholder="User">
                        </div> 
                        
                        <div class="form-group">
                            <label for="exampleInputFile">Password</label>
                            <input type="text" name="smtp_pass" value="<?php echo $smtp_pass;?>" class="form-control" placeholder="Password">
                        </div>  
                        
                         <div class="form-group">
                            <label for="exampleInputFile">Port</label>
                            <input type="text" name="smtp_port" value="<?php echo $smtp_port;?>" class="form-control" placeholder="Port">
                        </div> 
                         <div class="form-group">
                            <label for="exampleInputFile">Store name</label>
                            <input type="text" name="store_name" value="<?php echo $store_name;?>" class="form-control" placeholder="Store name">
                        </div> 
                        <div class="form-group">
                            <label for="exampleInputFile">Store email</label>
                            <input type="text" name="store_email" value="<?php echo $store_email;?>" class="form-control" placeholder="Store email">
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