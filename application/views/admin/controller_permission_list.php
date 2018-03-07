
<?php $id=$client_id; ?>
<form id="permission_list" method="post" class="form-horizontal" action="<?php echo base_url("admin/submit_class_permission/$id");?>">

<div class="form-group">
   <div class="form-group">
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Permission list
      </label>
    </div>
    
    <div class="col-lg-5">
       <?php
	    $module_name1="";
		if(!empty($client_permission_data))
		{
			foreach($client_permission_data as $item)
			{
				
				$module_name=$item['module_name'];
				$module_id=$item['id'];
				$checked='';
				$rs=$this->db->select('*')->where('client_id',$id)->where('module_id',$module_id)->get('site_class_permission');
				if($rs->num_rows()>0)
				{
					$checked="checked"."='checked'";
				}
			 ?>
			 
			 
			  <div class="title_left">
				 <ol class="breadcrumb text-left">
				   <div class="form-group">
				   <div class="checkbox">
						<label>
							<input type="checkbox" name="permission[]" <?php echo $checked; ?> value="<?php echo $module_id;?>" /> <?php echo $module_name;?>
						</label>
					</div>
				  
				  </div>
				</ol>
			   </div>
			   <?php
			}
		}
			   ?>
			 
</div>

<div class="form-group">
  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
    <button type="submit" class="btn btn-success">Submit</button>
  </div>
</div>

</form>


