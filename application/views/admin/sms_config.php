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
            <h2>Sms configuration list</h2>
            <ol class="breadcrumb">
             <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
             <li class="active">Details</li>
            </ol>
       </div>
     
     <form class="form-inline" method="post" action="<?php echo base_url('admin/sms_config');?>">
        
              <div class="form-group">
                <label for="exampleInputName2"></label>
                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="School name">
              </div>
              <button type="submit" class="btn btn-default">Search</button>
             
        </form>
     
        <div align="right">
            <a class="btn btn-default" href="<?php echo base_url();?>admin/add_new_sms_config">Add new</a>
        </div>
        <p>
         <?php echo $this->session->flashdata('message'); ?>
          <table class="table table-bordered"> 
          	<thead>
             <tr> 
             	<th>#</th> 
                <th>School name</th> 
                <th>Sms vendor name</th> 
                <th>Activate date/time</th>
                <th>Expire date/time</th> 
                <th>Total sms</th> 
                <th>Consumed sms</th>
                <th>Content</th>  
                <th>Action</th>
            </tr> 
            </thead> 
            <tbody> 
            <?php
			
			if(!empty($sms_config_list))
			{
				$i=1;
				foreach($sms_config_list as $item)
				{
					$is_active_class='';
					$section='';
					
					if($item['is_active']==0)
					{
						$is_active_class='danger';
					}
				
				$school_name='';	
				$school_name=$this->Common_model->get_single_field_value('school','name','id',$item['school_id']);
				
				$vendor_name='';	
				$vendor_name=$this->Common_model->get_single_field_value('sms_vendor','vendor_name','id',$item['sms_vendor_id']);
					
			?>
            <tr class="<?php echo $is_active_class;?>">
            <th scope="row"><?php echo $i; ?></th> 
            <td><?php echo $school_name; ?></td> 
            <td>
            <a data-title="<?php echo $vendor_name; ?>" data-toggle="modal" data-target="#myModal" data-url="<?php echo base_url();?>admin/view_sms_vendor/<?php echo $item['sms_vendor_id']; ?>">
            <?php echo $vendor_name; ?></a>
            </td> 
            
            <td><?php echo $item['active_date_time']; ?></td> 
            <td><?php echo $item['expire_date_time']; ?></td> 
            <td><?php echo $item['total_sms']; ?></td>
            <td><?php echo $item['used_sms']; ?></td> 
            <td><?php echo $item['sms_text']; ?></td>
            <td>
                <div class="btn-group" role="group" aria-label="...">
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/change_smsconfig_state/<?php echo $item['id']; ?>/<?php echo $item['school_id']; ?>">Change State</a>
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/edit_smsconfig_setting/<?php echo $item['id']; ?>">Edit</a>
                  <a class="btn btn-default" onclick="return confirm('Record will be deleted. \nDo you want to continue?')" href="<?php echo base_url();?>admin/delete_sms_config/<?php echo $item['id']; ?>/<?php echo $item['school_id']; ?>">Delete</a>
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
         <?php if(isset($links)){?>
         <div align="right"><?php echo $links;?></div>
         <?php } ?>
  	
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
