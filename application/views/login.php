 <!DOCTYPE html>
<html lang="en">
	<head>
 		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/formValidation.js"></script>
    <script src="<?php echo base_url(); ?>assets/css/formValidation.css"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/framework/bootstrap.js"></script>
        
<script type="text/javascript">
$(document).ready(function() {

    $('#login_form').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            unique_text: {
					validators: {
						notEmpty: {
							message: 'The Email / phone / Unique id is required and cannot be empty'
						
						}
					}
				},
			password: {
					validators: {
						notEmpty: {
							message: 'The password is required and cannot be empty'
						
						}
					}
				}
        }
    });
});
</script> 
 </head>
 <body>	
  		<h2 class="text-center">Login</h2>
      
      
      <!----body area---->  
    <div class="row">
    
    <?php echo validation_errors(); ?>
    
    
  		<div class="col-md-6 col-md-offset-3">
        <?php echo $this->session->flashdata('message'); ?> 
    	<form id="login_form" method="post" action="<?php echo base_url('admin_login');?>">
        
        <!--<form id="login_form">-->
              <div class="form-group">
                <label for="exampleInputEmail1">Email / phone / Unique id</label>
                <input type="text" class="form-control" name="unique_text" id="unique_text" placeholder="Email / phone / Unique id">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
              </div>
              
              <button type="submit" name='login_submit' class="btn btn-default">Login</button>
		</form>
        <p>
        </div>
        
       
	</div>
 
    <!-- Include all compiled plugins (below), or include individual files as needed -->
 
 </body>
</html>