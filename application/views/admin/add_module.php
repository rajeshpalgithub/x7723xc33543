<?php $this->load->view('admin/header');?>

<div class="container">
	<h2>Add Modules</h2> 
    <?php echo validation_errors(); ?>
	<?php echo $this->session->flashdata('message'); ?>
    <form name="add_module" action="<?php echo base_url("admin/add_module");?>" method="post">
      <div class="form-group">
        <label for="exampleInputEmail1">Class Name</label>
        <input type="text" name="class_name" class="form-control"  placeholder="Display name">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Class Display name</label>
        <input type="tyxt" name="display_name" class="form-control" placeholder="Display name">
      </div>
     
      <button type="submit" class="btn btn-default">Add</button>
      <a href="<?php echo base_url('admin/modules');?>"  class="btn btn-default">Cancel</a>
    </form>
</div>
<?php $this->load->view('admin/footer');?>