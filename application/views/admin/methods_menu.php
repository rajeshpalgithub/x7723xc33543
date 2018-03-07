<?php $this->load->view('admin/header');?>
	
	<div class="container">
    	<div class="title">
        <?php echo validation_errors(); ?>
	    <?php echo $this->session->flashdata('message'); ?>
            <div class="pull-left"><h2> Methods Menu </h2></div>
            <div class="pull-right"><a class="btn btn-default" href="<?php echo base_url('admin/import_methods');?>">Import Methods</a></div>
        </div>
        <div class="clear"></div>
        <hr />
    	<form id="attandance_report" method="get" action="<?php echo base_url("admin/methods_menu");?>">
          <div class="form-group">
            <label for="class">Select Class</label>
            <select class="form-control" name="class_name" id="class_name" onchange="this.form.submit()">
			<option value="">Select class</option>
			<?php 
			  $post_class_name="";
			  if($this->input->get('class_name'))
			  {
				  $post_class_name=$this->input->get('class_name');
			  }
			  if(!empty($module_name))
			  {
				  foreach($module_name as $item)
				  {
					  $selected='';
					  $class_display_name=$item['module_name'];
					  $class_name=$item['id'];
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
        
        
          
          
		 <form id="menu_form" method="post" action="<?php echo base_url("admin/apply_parent_method");?>">
          <table class="table">
          <?php
		  if(!empty($method_list))
		  {
			  foreach($method_list as $item)
			  {
				  $method_id=$item['id'];
				  $module_id=$item['module_id'];
				  $parent_id=$item['parent_id'];
				  $method_name=$item['method_name']."_".$item['type'];
		  ?>
          	<tr>
            	<td>
            		<input type="text" class="form-control" value="<?php echo $method_name;?>" readonly="readonly" value="method name"  />
                </td>
                <td>
            		<input type="text" class="form-control" value="<?php echo $item['method_description'];?>"  readonly="readonly" value="Display name" />
                    <input type="hidden" name="method_id[]" class="form-control" value="<?php echo $method_id; ?>"  />
                </td>
                <td>
                  <?php
				  $find_parent_id=$this->Admin_model->FindParentId($module_id,$parent_id);
				  $method_list=$this->Admin_model->GetParentMethodList($module_id,$method_id);
				  ?>
                
            		<select class="form-control" name="parent_id[]"> 
                        <option value="">----Select Parent Method------</option> 
                        <?php 
								  
						  if(!empty($method_list))
						  {
							  
							  foreach($method_list as $item)
							  {
								  $selected='';
								  $method_name=$item['method_name']."_".$item['type'];
								  $method_id=$item['id'];
								  if($find_parent_id==$method_id)
								  {
									  $selected='selected';
								  }
								 
								  echo "<option value='$method_id' $selected>$method_name</option>";
							 }
						  }
								 
						?>
             		</select>
                </td>

                
            </tr>
            <?php
			  }
		  }
		  ?>
          </table>
          <button type="submit" class="btn btn-default">Submit</button>
          </form>
        
    </div>
<?php $this->load->view('admin/footer');?>