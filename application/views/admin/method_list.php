<?php $this->load->view('admin/header');?>
<script>
$( document ).ready(function() 
{
	
	
});
</script> 



    <!---- /title section----->
    <!--- Container ----->
    <div class="container">
            <div class="row">
            
            <div class="container page-title">
                <ol class="breadcrumb">
                 <li><a href="<?php echo base_url('dashboard');?>">Dashboard</a></li>
                 <li class="active">Select class</li>
                </ol>
            </div>
       
             <?php echo validation_errors(); ?>
             <?php echo $this->session->flashdata('message'); ?>
  				<div class="col-md-6 col-md-offset-3">

                    <table class="table table-bordered"> 
                    <form id="attandance_report" method="post" action="<?php echo base_url('admin/update_method_list');?>">
                    <input type="hidden" class="form-control" value="<?php echo $module_id; ?>" id="module_id" name="module_id">
                    <tbody> 
                    <?php

					if(!empty($method_list))
					{
						for($i=2;$i<=count($method_list)-2;$i++)
						{
							$dispalay_name='';
							$method_type='';
							
							$method_name=$method_list[$i];
							$module_array=$this->Admin_model->get_display_name($method_name,$module_id);
							
							if(!empty($module_array))
							{
								
							 $filter_array=explode('_',$method_list[$i]);

							  if(($method_list[$i][0]!="_") &&
							     (in_array('get', $filter_array) || in_array('post', $filter_array) || 
							     in_array('put', $filter_array) || in_array('delete', $filter_array))
								 )
							  {	
							  
							    if ((strpos($method_list[$i], '_get') !== false) || (strpos($method_list[$i], '_post') !== false) 
									|| (strpos($method_list[$i], '_put') !== false) || (strpos($method_list[$i], '_delete') !== false))
								  {
								
								
							  $dispalay_name=$module_array['method_description'];
							  $method_type=$module_array['type'];
							  
							  $method_type='';
							  $method_name='';
							  $method_name_arr=array();
							  if(in_array('get', $filter_array))
							  {
								  $method_type='GET';
								  $method_name_arr=explode('_get',$method_list[$i]);
								  $method_name=$method_name_arr[0];
							  }
							  if(in_array('post', $filter_array))
							  {
								  $method_type='POST';
								  $method_name_arr=explode('_post',$method_list[$i]);
								  $method_name=$method_name_arr[0];
							  }
							  if(in_array('put', $filter_array))
							  {
								  $method_type='PUT';
								  $method_name_arr=explode('_put',$method_list[$i]);
								  $method_name=$method_name_arr[0];
							  }
							  if(in_array('delete', $filter_array))
							  {
								  $method_type='DELETE';
								  $method_name_arr=explode('_delete',$method_list[$i]);
								  $method_name=$method_name_arr[0];
							  }
							  
					?>	
                    <tr>
                    <td><?php echo $method_list[$i]; ?></td>
                    <td>
					<input type="hidden" class="form-control" value="<?php echo $method_type; ?>"  id="request_type[]" name="request_type[]">
                    <input type="hidden" class="form-control" value="<?php echo $method_name; ?>" id="name[]" name="name[]">
                    <input type="text" class="form-control" value="<?php echo $dispalay_name;?>" id="display_name[]" name="display_name[]" placeholder="Display name">
                    </td>
                    <td>
                    
                    
                    </td>
                    </tr>
                    <?php
								  }
							  }
							}
						}
					}
					?>
                    <tr>
                    <td><button type="submit" class="btn btn-default">Submit</button></td>
                    <td colspan=2><a class="btn btn-primary" href="<?php echo base_url();?>admin/class_list/2">Cancel</a></td>
                    </tr>
                    </tbody>  
                    </form>
                    </table>

                </div>
			</div>
          
  		
	</div>
 	<!--- Container ----->
    <!--- footer ----->
    <!--- /footer ----->
 <?php $this->load->view('admin/footer'); ?>