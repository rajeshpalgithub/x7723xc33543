<?php $this->load->view('admin/header');?>
	
	<div class="container">
    	<div class="title">
        <?php echo validation_errors(); ?>
	    <?php echo $this->session->flashdata('message'); ?>
            <div class="pull-left"><h2> Methods </h2></div>
            <div class="pull-right"><a class="btn btn-default" href="<?php echo base_url('admin/import_methods');?>">Import Methods</a></div>
        </div>
        <div class="clear"></div>
        <hr />
    	<form id="attandance_report" method="get" action="<?php echo base_url("admin/methods");?>">
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
          <table class="table">
          <?php
		  if(!empty($method_list))
		  {
			  foreach($method_list as $item)
			  {
				  $method_id=$item['id'];
				  $method_name=$item['method_name']."_".$item['type'];
		  ?>
          	<tr>
            	<td>
            		<input type="text" class="form-control" value="<?php echo $method_name;?>" readonly="readonly" value="method name"  />
                </td>
                <td>
            		<input type="text" class="form-control" value="<?php echo $item['method_description'];?>"  readonly="readonly" value="Display name" />
                </td>
                <td>
            		<a class="btn btn-default" href="<?php echo base_url("admin/edit_method/$method_id");?>">Edit</a>
                    <a class="btn btn-default" onclick="return confirm('Record will be deleted. \nDo you want to continue?')" 
                    href="<?php echo base_url("admin/delete_method/$method_id");?>">Delete</a>
                </td>
                
            </tr>
            <?php
			  }
		  }
		  ?>
          </table>
          
		</form>
        
    </div>
<?php $this->load->view('admin/footer');?>