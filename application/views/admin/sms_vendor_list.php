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
            <h2>Sms vendor list</h2>
            <ol class="breadcrumb">
             <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
             <li class="active">Details</li>
            </ol>
       </div>
        
        <div align="right">
            <a class="btn btn-default" href="<?php echo base_url();?>admin/add_new_sms_vendor">Add new</a>
        </div>
        <p>
         <?php echo $this->session->flashdata('message'); ?>
          <table class="table table-bordered"> 
          	<thead>
             <tr> 
             	<th>#</th> 
                <th>Sms vendor name</th> 
                <th>Api key</th> 
                <th>End point</th>
                <th>Action</th>
            </tr> 
            </thead> 
            <tbody> 
            <?php
			
			if(!empty($vendor_list))
			{
				$i=1;
				foreach($vendor_list as $item)
				{
					$is_active_class='';
					$section='';
					
					if($item['is_active']==0)
					{
						$is_active_class='danger';
					}
				
				
					
			?>
            <tr class="<?php echo $is_active_class;?>">
            <th scope="row"><?php echo $i; ?></th> 
            <td><?php echo $item['vendor_name']; ?></td> 
            <td><?php echo $item['api_key']; ?></td> 
            <td><?php echo $item['end_point']; ?></td> 
            <td>
                <div class="btn-group" role="group" aria-label="...">
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/change_smsvendor_state/<?php echo $item['id']; ?>">Change State</a>
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/edit_vendorlist_setting/<?php echo $item['id']; ?>">Edit</a>
                  <a class="btn btn-default" onclick="return confirm('Record will be deleted. \nDo you want to continue?')" href="<?php echo base_url();?>admin/delete_smsvendor_config/<?php echo $item['id']; ?>">Delete</a>
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
            <tr><td colspan=8 align="center">No record found</td></tr>
            <?php } ?>
            
            	
           </tbody> 
         </table>
  	
<!--<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Student details</h4>
      </div>
      <div class="modal-body">
        <?php echo $this->load->view('school/viewstudent'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>-->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog ">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Noble Iron</h4>
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
