<?php $this->load->view('admin/header');?>
   	
<script>
$( document ).ready(function() 
{
	$('#country').change(function() {   
    var selectVal = $('#country :selected').val();
	$('#loading_gif').html("");
	var url='<?php echo base_url();?>'+'admin/getstatelist/'+selectVal;
	   
	   $.ajax({
        url: url,
        type: 'GET',
		beforeSend: function() {
   			 $('#loading_gif').html("<h6><b>Please wait.....<b></h6><img src='<?php echo base_url().'assets/loader/Timer.gif';?>' />");
        },
        success: function(data) 
		 {  
		    $('#loading_gif').html(""); 
		    $('#state').empty();
            $('#state').append("<option value=''>Select state</option>"+data);
         },
		error: function (xhr, ajaxOptions, thrownError) 
		{	
		    $('#loading_gif').html("");
			error_msg=xhr.status + " "+thrownError+" "+xhr.responseText;
			console.log(error_msg);
			$('#errclass').html("<span class='alert alert-danger'>"+error_msg+"</span>");
			
		}
       });
    
   });
    
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
	
});
</script>     
    <!---/header------>
    <!--- Container ----->
    <div class="container">
    
    <div class="container page-title">
    	<h2>Add new school</h2>
        <ol class="breadcrumb">
         <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
         <li class="active">Add new</li>
        </ol>
    </div>
    
    <div id='errclass'></div>
            <?php echo validation_errors(); ?>
            <?php echo $this->session->flashdata('message'); ?>
          <div class="row">
  			<div class="col-md-6 col-md-offset-3">
            
            	<!--<div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Success!</strong> Student : <b>[name of student] - [ Card No ] - [Class] - [Reg No]</b> Add success.
                </div>
                <div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Error!</strong> Erorr list why not added bcz validation or studet card exist etc....
                </div>-->
            	
                <form id="add_student" method="post" action="<?php echo base_url('admin/add_school');?>">
                  
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
		
							  echo "<option value='$vendor_id'>$vendor_name</option>";
						  }
					  }
					  ?>
            
                    </select>
                  </div>
               
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                  </div>
                  <div class="form-group">
                    <label for="name">Short name</label>
                    <input type="text" class="form-control" maxlength="6" id="short_name" name="short_name" placeholder="Short name">
                  </div>
                  
                  
                  <div class="form-group">
                  	<label for="address">Product type</label>
                     <select class="form-control" name="product_type" id="product_type">
                      <option value="">Select product type</option>
                        <option  value='1'>Rent</option>
                        <option value='2'>Buy</option>
                        <option  value='3'>Both</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                  </div>
                   <div class="form-group">
                    <label for="name">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                  </div>
                   
                   <div class="form-group">
                    <label for="name">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Email">
                  </div>
                 
                  <div class="form-group">
                  	<label for="address">Address</label>
                     <!--<input type="text" class="form-control" id="address1" name="address1" placeholder="Address1">-->
                     <input id="geocomplete" class="form-control" name="address1" type="text" placeholder="Address" >
                    <div class="map_canvas"></div>
                  </div>
                  <div class="form-group">
                  	<label for="address">City / Village</label>
                     <input type="text" class="form-control" id="city_village" name="city_village" placeholder="City / Village">
                  </div>
                  <div class="form-group">
                  	<label for="address">Country</label>
                     <select class="form-control" name="country" id="country">
                      <option value="">Select country</option>
                      <?php 
					  if(!empty($country_list))
					  {
						  foreach($country_list as $item)
						  {
							  $country_name=$item['country'];
							  $country_id=$item['id'];
							  
							  echo "<option value='$country_id'>$country_name</option>";
						  }
					  }
					  ?>
            
                    </select>
                  </div>
                  
                  <div class="form-group">
                  	<label for="address">State</label>
                    <span id="loading_gif"></span>
                     <select class="form-control" name="state" id="state">
                      <option value="">Select state</option>
                    </select>
                  </div>
                    <div class="form-group">
                  	<label for="address">Pin</label>
                     <input type="text" class="form-control" id="pin" name="pin" placeholder="Pin">
                   </div>
             
                 <div class="form-group">
                 	<label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked> Active
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> In Active
                    </label>
                 </div>
                  <button type="submit" name="add_student" class="btn btn-default">Add</button>
                </form>
            </div>
          </div>
  		
	</div>
<?php $this->load->view('admin/footer'); ?>