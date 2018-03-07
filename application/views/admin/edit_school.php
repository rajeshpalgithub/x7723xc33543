<?php $this->load->view('admin/header');?>
   	
<script>
$( document ).ready(function() 
{
	$('#country').change(function() {   
    var selectVal = $('#country :selected').val();
	$('#loading_gif').html("");
	var url='<?php echo base_url();?>'+'admin/getstatelist/'+selectVal;
	//alert(url);  
	   $.ajax({
        url: url,
        type: 'GET',
		beforeSend: function() {
   			 $('#loading_gif').html("<h6><b>Please wait.....<b></h6><img src='<?php echo base_url().'assets/loader/Timer.gif';?>' />");
        },
        success: function(data) 
		 {  
		    //alert(data);
			$('#loading_gif').html("");
		    $('#state').empty();
            $('#state').append("<option value=''>Select state</option>"+data);
         },
		error: function (xhr, ajaxOptions, thrownError) 
		{	
		    $('#loading_gif').html("");
			error_msg=xhr.status + " "+thrownError+" "+xhr.responseText;
		    //alert(error_msg);
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
    <div id='errclass'></div>
            <?php echo validation_errors(); ?>
            <?php echo $this->session->flashdata('message'); ?>
          <div class="row">
          
          <div class="container page-title">
            <h2>Edit school</h2>
            <ol class="breadcrumb">
             <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
             <li><a href="<?php echo base_url('admin/school_list');?>">School</a></li>
             <li class="active">Edit</li>
            </ol>
        </div>
          
  			<div class="col-md-6 col-md-offset-3">
            	<!--<div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Success!</strong> Student : <b>[name of student] - [ Card No ] - [Class] - [Reg No]</b> Add success.
                </div>
                <div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Error!</strong> Erorr list why not added bcz validation or studet card exist etc....
                </div>-->
            	
               
                 <?php 
				 if(!empty($school_data) && !empty($login_data))
				 {
				   $id=$school_data['id'] ?>
                   *** (Leave blank for same password)
                <form id="add_student" method="post" action="<?php echo base_url("admin/update_school/$id");?>">
                
               
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" value="<?php echo $school_data['name'];?>" name="name" placeholder="Name">
                  </div>
                  <div class="form-group">
                    <label for="name">Short name</label>
                    <input type="text" class="form-control" maxlength="6" value="<?php echo $school_data['short_name'];?>" id="short_name" name="short_name" placeholder="Short name">
                  </div>
                  <?php
				    $product_type=$school_data['product_type'];
				  ?>
                  <div class="form-group">
                  	<label for="address">Product type</label>
                     <select class="form-control" name="product_type" id="product_type">
                      <option value="">Select product type</option>
                        <option <?php if($product_type=='1'){echo 'selected';} ?> value='1'>Rent</option>
                        <option <?php if($product_type=='2'){echo 'selected';} ?> value='2'>Buy</option>
                        <option <?php if($product_type=='3'){echo 'selected';} ?> value='3'>Both</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" id="phone" value="<?php echo $login_data['phone_no'];?>" name="phone" placeholder="Phone">
                  </div>
                  <div class="form-group">
                    <label for="name">Email</label>
                    <input type="text" class="form-control" id="email" value="<?php echo $login_data['email'];?>" name="email" placeholder="Email">
                  </div>
                  <div class="form-group">
                    <label for="name">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                  </div>
                 
                  <div class="form-group">
                  	<label for="address">Address</label>
                     <!--<input type="text" class="form-control" id="address1" value="<?php echo $school_data['address'];?>" name="address1" placeholder="Address1">-->
                     <input id="geocomplete" class="form-control" value="<?php echo $school_data['address'];?>" name="address1" type="text" placeholder="Address" >
                    <div class="map_canvas"></div>
                  
                  </div>
                  <div class="form-group">
                  	<label for="address">City / Village</label>
                     <input type="text" class="form-control" id="city_village" value="<?php echo $school_data['city_village'];?>" name="city_village" placeholder="City / Village">
                  </div>
                  <div class="form-group">
                  	<label for="address">Country</label>
                     <select class="form-control" name="country" id="country">
                      <option value="">Select country</option>
                      <?php 
					  if(!empty($country_list))
					  {
						  $country_db_id=$school_data['country_id'];
						  foreach($country_list as $item)
						  {
							  $country_name=$item['country'];
							  $country_id=$item['id'];
							  
							  $selected='';
							  if($country_db_id==$country_id)
							  {
								 $selected='selected';  
							  }
							  
							  echo "<option value='$country_id' $selected>$country_name</option>";
						  }
					  }
					  ?>
            
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <?php 
					  //$state_name='';
					  //$state_name=$this->Common_model->get_single_field_value('subregions','name','id',$school_data['state']);
					  $school_data['state'];
					  $region_id=$this->Common_model->get_single_field_value('subregions','region_id','id',$school_data['state']);
					  $state_list=$this->Vendor_model->GetStatelist($region_id);
					 
					?>
                  	<label for="address">State</label>
                    <span id="loading_gif"></span>
                     <select class="form-control" name="state" id="state">
                      <?php 
					  if(!empty($state_list))
					  {
						  $saved_state=$school_data['state'];
						  foreach($state_list as $item)
						  {
							  $selected='';
							  $state_name=$item['name'];
							  $state_id=$item['id'];
							  
							  if($saved_state==$state_id)
							  {
								 $selected='selected';  
							  }
							  
							  echo "<option value='$state_id' $selected >$state_name</option>";
						  }
					  }
					  ?>
                    </select>
                  </div>
                    <div class="form-group">
                  	<label for="address">Pin</label>
                     <input type="text" class="form-control" id="pin" value="<?php echo $school_data['pin_code'];?>" name="pin" placeholder="Pin">
                   </div>
             
                 <div class="form-group">
                    <?php
					  $is_active='';
					  $is_not_active='';
					  if($school_data['is_active']==1)
				      {
						$is_active='checked';
					  }
					  else
					  {
						$is_not_active='checked';
					  }
					?>
                 	<label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" <?php echo $is_active; ?>> Active
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" <?php echo $is_not_active; ?>> In Active
                    </label>
                 </div>
                  <button type="submit" name="add_student" class="btn btn-default">Update</button>
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/school_list">Cancel</a>
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
<?php $this->load->view('admin/footer'); ?>