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

                    <form id="attandance_report" method="post" action="<?php echo base_url('admin/add_new_time_zone');?>">
						<div class="form-group">
                            <label for="exampleInputFile">Select school</label>
                            <select class="form-control" name="school_id" id="school_id">
                                <option value="">Select school</option>
                                <?php foreach($school_list as $item) { ?>
                                  <option value="<?php echo $item['id'] ?>">
                                    <?php echo $item['name'];?>
                                  </option>
                                <?php } ?>
                              </select>
                        </div>  
                        
                        
                        
                        <div class="form-group">
                            <label for="exampleInputFile">Select timezone</label>
                            <select class="form-control" name="time_zone" id="time_zone">
                                <option value="">Select timezone</option>
                                <?php foreach($tz_list as $t) { ?>
                                  <option value="<?php echo $t['zone'] ?>">
                                    <?php echo $t['diff_from_GMT'] . ' - ' . $t['zone'] ?>
                                  </option>
                                <?php } ?>
                              </select>
                        </div>  

                        <div class="form-group">
                            <label for="exampleInputFile">Select Date format</label>
                            <select class="form-control" name="date_format" id="date_format">
                            <option value="">Select date format</option>
                            <option value="dd/mm/yy">dd/mm/yyyy</option>
                            <option value="mm/dd/yy">mm/dd/yyyy</option>
                            <option value="yy/mm/dd">yyyy/mm/dd</option>
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