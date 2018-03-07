<?php $this->load->view('admin/header');?>

    <div class="container">
	<form id="attandance_report" method="get" action="<?php echo base_url("admin/import_methods");?>">
          <div class="form-group">
            <label for="class">Select Class</label>
            <select class="form-control" name="class_name" id="class_name" onchange="this.form.submit()">
			<option value="">Select class</option>
			<?php 
			  $post_class_name="";
			  if(isset($fetch_class_name))
			  {
				  $post_class_name=$fetch_class_name;
			  }
			
			  $module_name=$this->Admin_model->GetClassList();
			  if(!empty($module_name))
			  {
				  foreach($module_name as $item)
				  {
					  $selected='';
					  $class_display_name=$item['module_name'];
					  $class_name=$item['class_name'];
					  if($post_class_name==$class_name)
					  {
						   $selected='selected';
					  }
					  
					  echo "<option value='$class_name' $selected>$class_display_name</option>";
				  }
			  }
			  ?>
		  </select>
          </div>
		</form>
        <hr />
        <h5>Methods</h5>
        <form id="attandance_report" method="post" action="<?php echo base_url('admin/submit_method_list');?>">
        <?php
		if(!isset($module_id))
		{
			$module_id='';
		}
		?>
         <input type="hidden" class="form-control" value="<?php echo $module_id; ?>" id="module_id" name="module_id">
        	<table class="table ">
            <?php
				if(!empty($method_list))
				{
					for($i=2;$i<=count($method_list)-2;$i++)
					{
							$dispalay_name='';
							$method_type='';
							
							$method_name=$method_list[$i];
							$module_array=$this->Admin_model->get_display_name($method_name,$module_id);
							if(empty($module_array))
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
                	<td><input type="text" class="form-control" readonly="readonly" value="<?php echo $method_list[$i]; ?>"></td>
                    <td>
                    <input type="hidden" class="form-control" value="<?php echo $method_type; ?>"  id="request_type[]" name="request_type[]">
                    <input type="hidden" class="form-control" value="<?php echo $method_name; ?>"  id="name[]" name="name[]">
                    <input type="text" class="form-control"  id="display_name[]" name="display_name[]" placeholder="Display name">
                </tr>
                <?php
								  }
							  }
							}
						}
				
				}
				?>
            </table>
            <button type="submit" class="btn btn-default">Import To Data Base</button>
            <a href="<?php echo base_url('admin/methods');?>" class="btn btn-default">Cancel</a>
        </form>
    </div>
<?php $this->load->view('admin/footer');?>