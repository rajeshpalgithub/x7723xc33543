<?php $this->load->view('admin/header');?>

<div class="container">
	<h2>Modules</h2> <a class="btn btn-default" href="<?php echo base_url('admin/add_module');?>">Add Module(class)</a>
	<?php echo validation_errors(); ?>
	<?php echo $this->session->flashdata('message'); ?>
	<table class="table">
    	<thead>
            <tr>
                <th>Name</th>
                <th>Display Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
		<?php
		if(!empty($class_list))
		{
			foreach($class_list as $item)
			{
				$module_id=$item['id']
			?>
				<tr>
					<th><?php echo $item['class_name'];?></th>
					<th><?php echo $item['module_name'];?></th>
					<th><a class="btn btn-default" href="<?php echo base_url("admin/edit_module/$module_id");?>">Edit</a><a class="btn btn-default" 
					onclick="return confirm('Record will be deleted. \nDo you want to continue?')" href="<?php echo base_url("admin/delete_module/$module_id");?>">Delete</a></th>
				</tr>
				<?php
			}
		}
			?>
        </tbody>
	</table>
</div>
<?php $this->load->view('admin/footer');?>