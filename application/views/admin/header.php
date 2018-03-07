<!DOCTYPE html>
<html lang="en">
 <head>
 	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.0.min.js"></script>
    
    
    <script src="<?php echo base_url(); ?>assets/js/jquery.geocomplete.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/logger.js"></script>
    
 	
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    
    
    <!--<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>-->
    <!--<script src="<?php echo base_url(); ?>assets/js/formValidation.js"></script>-->
	<style>
		.clear{clear:both;}
	</style>
 </head>
 <body>
 	<!---header------>
 	<nav class="navbar navbar-default">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">URCIB CRM</a>
            </div>
        
            <!-- Collect the nav links, forms, and other content for toggling -->
            <?php $this->load->view('admin/menu'); ?><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>