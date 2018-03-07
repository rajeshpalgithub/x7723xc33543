<?php $this->load->view('admin/header');?>
 <script>
      $(function(){
        $("#geocomplete").geocomplete({
          map: ".map_canvas"
        });
        
        $("#search").click(function(){
          $("#geocomplete").geocomplete("find", "NYC");
        });
        
        $("#center").click(function(){
          var map = $("#geocomplete").geocomplete("map"),
            center = new google.maps.LatLng(10, 0);
          
          map.setCenter(center);
          map.setZoom(3);
        });
		
		
  $('#module_id').change(function() {   
    var selectVal = $('#module_id :selected').val();
	
	//alert(selectVal);
	
	  var url='<?php echo base_url();?>'+'admin/get_method_list/'+selectVal;
	   
	   $.ajax({
        url: url,
        type: 'GET',
		beforeSend: function() {
   			 $('#loading_gif').html("<h6><b>Please wait.....<b></h6><img src='<?php echo base_url().'assets/loader/Timer.gif';?>' />");
        },
        success: function(data) 
		{  
		    $('#loading_gif').html("");
		    $('#method_id').empty();
            $('#method_id').append("<option value=''>Select method</option>"+data);
         },
		error: function (xhr, ajaxOptions, thrownError) 
		{	
		    $('#loading_gif').html("");
			error_msg=xhr.status + " "+thrownError+" "+xhr.responseText;
			$('#errclass').html("<span class='alert alert-danger'>"+error_msg+"</span>");
			
		}
       });
    
   });
		
		
  $('#method_id').change(function() {   
    var selectVal = $('#method_id :selected').val();
	var module_id = $('#module_id :selected').val();
	//alert(selectVal);
	
	  var url='<?php echo base_url();?>'+'admin/get_parent_method_list/'+selectVal+'/'+module_id;
	   
	   $.ajax({
        url: url,
        type: 'GET',
		beforeSend: function() {
   			 $('#loading_gif').html("<h6><b>Please wait.....<b></h6><img src='<?php echo base_url().'assets/loader/Timer.gif';?>' />");
        },
        success: function(data) 
		{  
		    $('#loading_gif').html("");
		    $('#parent_method_id').empty();
            $('#parent_method_id').append("<option value=''>Select parent method</option>"+data);
         },
		error: function (xhr, ajaxOptions, thrownError) 
		{	
		    $('#loading_gif').html("");
			error_msg=xhr.status + " "+thrownError+" "+xhr.responseText;
			$('#errclass').html("<span class='alert alert-danger'>"+error_msg+"</span>");
			
		}
       });
    
   });
		
			
		
		
      });
    </script>     	
    
    <!---/header------>
    <!--- Container ----->
    <div class="container">
    <div id='errclass'></div>
            <?php echo validation_errors(); ?>
            <?php echo $this->session->flashdata('message'); ?>
          <div class="row">
          
          <div class="container page-title">
                <h2>Apply parent method</h2>
            <form id="attandance_report" method="post" action="<?php echo base_url('admin/apply_sub_menu');?>">
						<div class="form-group">
                            <label for="exampleInputFile">Select Class</label>
                            <select class="form-control" name="module_id" id="module_id">
								  <option value="">Select calss</option>
								  <?php 
								  
								  if(!empty($class_list))
								  {
									 
									  foreach($class_list as $row)
									  {
										  $class_name=$row['module_name'];
										  $module_id=$row['id'];
										  
										  echo "<option value='$module_id'>$class_name</option>";
									  }
								  }
								 
								  ?>

								</select>
                        </div>  
                        
                        
                        
                        <div class="form-group">
                            <label for="exampleInputFile">Select method</label>
                            <select class="form-control" id="method_id" name="method_id">
								 <option value="">Select method</option>
							  
							</select>
                        </div>  
						*****In case of not selection method become 'root method'
                       <div class="form-group">
					   
                            <label for="exampleInputFile">Select parent method</label>
                            <select class="form-control" id="parent_method_id" name="parent_method_id">
								 <option value="">Select parent method</option>
							  
							</select>
                        </div>
                        
                       
                       <button type="submit" class="btn btn-default">Submit</button>
					</form>
                
            </div>   
          
  	
          </div>
  		
	</div>
<?php $this->load->view('admin/footer'); ?>