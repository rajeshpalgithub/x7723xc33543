<?php $this->load->view('admin/header'); ?>


    <div class="container">
    	
        <div class="container page-title">
            <h2>Time zone list</h2>
            <ol class="breadcrumb">
             <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
             <li class="active">Details</li>
            </ol>
       </div>
        
        <div align="right">
            <a class="btn btn-default" href="<?php echo base_url();?>admin/add_new_time_zone">Add new</a>
        </div>
        <p>
         <?php echo $this->session->flashdata('message'); ?>
          <table class="table table-bordered"> 
          	<thead>
             <tr> 
             	<th>#</th> 
                <th>School name</th> 
                <th>Time zone</th> 
                <th>Date format</th>
                <th>Action</th>
            </tr> 
            </thead> 
            <tbody> 
            <?php
			
			if(!empty($school_time_zone))
			{
				$i=1;
				foreach($school_time_zone as $item)
				{
				 $school_name='';	
				 $school_name=$this->Common_model->get_single_field_value('school','name','id',$item['school_id']);
					
			?>
            <tr>
            <th scope="row"><?php echo $i; ?></th> 
            <td><?php echo $school_name; ?></td> 
            <td><?php echo $item['timezone']; ?></td> 
            <td><?php echo $item['date_format']; ?></td> 
            <td>
                <div class="btn-group" role="group" aria-label="...">
                  
                  <a class="btn btn-default" href="<?php echo base_url();?>admin/edit_timezone/<?php echo $item['id']; ?>">Edit</a>
                  <a class="btn btn-default" onclick="return confirm('Record will be deleted. \nDo you want to continue?')" href="<?php echo base_url();?>admin/delete_time_zone/<?php echo $item['id']; ?>">Delete</a>
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
            <tr><td colspan=5 align="center">No record found</td></tr>
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
