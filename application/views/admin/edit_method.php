<?php $this->load->view('admin/header');?>

<div class="container">
	<h2>Edit Method</h2> 
    <?php echo validation_errors(); ?>
	<?php echo $this->session->flashdata('message'); 
	if(!empty($method_data))
	{
	$id=$method_data['id'];
	?>
    <form name="add_module" action="<?php echo base_url("admin/update_method/$id");?>" method="post">
      <div class="form-group">
        <label for="exampleInputEmail1">Method Name</label>
        <input type="text" value="<?php echo $method_data['method_name']."_".$method_data['type'];?>" name="method_name" class="form-control" readonly="readonly">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Method Display name</label>
        <input type="text" value="<?php echo $method_data['method_description'];?>" name="display_name" class="form-control" placeholder="Display name">
      </div>
     
      <button type="submit" class="btn btn-default">Update</button>
      <a href="<?php echo base_url('admin/methods');?>"  class="btn btn-default">Cancel</a>
    </form>
	<?php
	}
	?>
</div>
<?php $this->load->view('admin/footer');?>