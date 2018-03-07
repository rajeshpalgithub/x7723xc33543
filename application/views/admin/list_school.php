<?php $this->load->view('admin/header'); ?>
<script>
$( document ).ready(function() 
{
$('#myModal').on('hide.bs.modal', function (e) {
  				$("#myModal .modal-body").html('');
});
			
			
			
			$('#myModal').on('show.bs.modal', function(e) {
				var $modal = $(this);
				var $invoker = $(e.relatedTarget);
				
				var target = $invoker.attr("data-url");
				var mTitle= $invoker.attr("data-title");
				//console.log(target);
					/*target = e.relatedTarget.data-url,
					mTitle = e.relatedTarget.data-title;*/
				//$(this).attr("data-url")
				$modal.find('.modal-body').html('<span class="label label-danger" id="loading" >loading...</span>');
		
				$.ajax({
					cache: false,
					type: 'GET',
					url: target,
					success: function(data) {
						$modal.find('.modal-title').html(mTitle);
						$modal.find('.modal-body').html(data);
					}
				});
    		})
			
			
			
			/*jQuery("a[data-target=#myModal]").click(function(ev) {
				
				ev.preventDefault();
				var target = jQuery(this).attr("data-url");
				var mTitle= jQuery(this).attr("data-title");
				
				
				// load the url and show modal on success
				
				
				jQuery("#myModal .modal-body ").append('<div id="loading" >loading...</div>');
				jQuery(".modal-title").html(mTitle);
				
				
				
				var response;
				jQuery.ajax({ type: "GET",   
					 url: target,
					 cache: false,
					 success : function(text)
					 {
						
						 response= text;
					 }
				});
				jQuery('#myModal .modal-body ').html(response);
				
			});*/
 });
</script> 

    <div class="container">
   <div class="container page-title">
    	<h2>Client list</h2>
        <ol class="breadcrumb">
         <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
         <li class="active">Details</li>
        </ol>
    </div>   
    	<form class="form-inline" method="post" action="<?php echo base_url('admin/school_list');?>">
        
              <div class="form-group">
                <label for="exampleInputName2"></label>
                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="School name">
              </div>
              <button type="submit" class="btn btn-default">Search</button>
             
        </form>
        <br>
        <br>
         <?php echo $this->session->flashdata('message'); ?>
          <table class="table table-bordered"> 
          	<thead>
             <tr> 
             	<th>#</th>
                <th>Name</th> 
                <th>API KEY</th> 
                <th>Vendor name</th> 
                <th>@</th> 
                <th>Actions</th> 
            </tr> 
            </thead> 
            <tbody> 
            <?php
			
			if(!empty($school_list))
			{   
				$i=1;
				foreach($school_list as $item)
				{
					$is_active_class='';
					if($item['is_active']==0)
					{
						$is_active_class='active';
					}
					
			$vendor_name='';
			$vendor_name=$this->Common_model->get_single_field_value('vendor','name','id',$item['vendor_id']);
			$students='';
			$students=$this->Admin_model->GetTotalStudents($item['id']);
			
			$api_key='';
			$api_key=$this->Common_model->get_single_field_value('keys','key','client_id',$item['id']);
			$id=$item['id'];
			?>
            <tr class="<?php echo $is_active_class;?>">
            <th scope="row"><?php echo $i; ?></th>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $api_key; ?></td>
            <td><?php echo $vendor_name; ?></td>
            <td>
            <a data-title="Client details" data-toggle="modal" data-target="#myModal" data-url="<?php echo base_url();?>admin/view_school_details/<?php echo $item['id']; ?>">View details</a>
            <!--<a href="<?php echo base_url();?>admin/view_school_details/<?php echo $item['id']; ?>">test</a>-->
			
            </td>
           
            <td>
                <div class="btn-group" role="group" aria-label="...">
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/change_state_school/<?php echo $item['id']; ?>">Change State</a>
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/edit_school/<?php echo $item['id']; ?>">Edit</a>
                  <a class="btn btn-default" onclick="return confirm('Record will be deleted. \nDo you want to continue?')" href="<?php echo base_url();?>admin/delete_school/<?php echo $item['id']; ?>">Delete</a>
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/change_vendor_school/<?php echo $item['id']; ?>">Change vendor</a>
				  <a class="btn btn-info" data-title="Permission list" data-toggle="modal" data-target="#myModal" data-url="<?php echo base_url();?>admin/rest_permission_list/<?php echo $item['id'];?>">Rest Permission</a>
                </div>
            </td> 
            </tr>
            <?php
			$i++;
				}
			}
			else
			{
			?>
            <tr><td colspan=7 align="center">No record found</td></tr>
            <?php } ?>
            
            	
           </tbody> 
         </table>
  	     <?php if(isset($links)){?>
         <div align="right"><?php echo $links;?></div>
         <?php } ?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog ">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Admin</h4>
		  </div>
		  <div class="modal-body">
			<p>&nbsp;</p>
		  </div>
		  <!--<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>-->
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div>







	</div>
 	<!--- Container ----->
  <?php $this->load->view('admin/footer'); ?>
