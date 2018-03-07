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
                <h2>Add new vendor</h2>
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li class="active">Add new</li>
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
            	
                <form id="add_student" method="post" action="<?php echo base_url('admin/add_vendor');?>">
   
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                  </div>
                  <div class="form-group">
                  	<label for="address">Address</label>
                     <!--<input type="text" class="form-control" id="geocomplete" name="address1" placeholder="Address1">-->
                    <input id="geocomplete" class="form-control" name="address1" type="text" placeholder="Type in an address" >
                    <div class="map_canvas"></div>
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
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
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