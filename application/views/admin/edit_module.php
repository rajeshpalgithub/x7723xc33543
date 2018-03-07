<?php $this->load->view('admin/header');?>

<div class="container">
	<h2>Edit Modules</h2> 
    <?php echo validation_errors(); ?>
	<?php echo $this->session->flashdata('message'); 
	if(!empty($class_data))
	{
	$id=$class_data['id'];
	?>
    <form name="add_module" action="<?php echo base_url("admin/update_module/$id");?>" method="post">
      <div class="form-group">
        <label for="exampleInputEmail1">Class Name</label>
        <input type="text" value="<?php echo $class_data['class_name'];?>" name="class_name" class="form-control"  placeholder="Display name">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Class Display name</label>
        <input type="text" value="<?php echo $class_data['module_name'];?>" name="display_name" class="form-control" placeholder="Display name">
      </div>
     
      <button type="submit" class="btn btn-default">Update</button>
      <a href="<?php echo base_url('admin/modules');?>"  class="btn btn-default">Cancel</a>
    </form>
	<?php
	}
	?>
</div>
<?php $this->load->view('admin/footer');?>