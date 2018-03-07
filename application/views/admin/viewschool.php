<div class="row">
	<label class="col-md-12 control-label">Date/time: <?php echo $school_data['date_time'];?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">Name: <?php echo $school_data['name'];?></label>
</div>

<div class="row">
<?php $api_key=$this->Common_model->get_single_field_value('keys','key','client_id',$school_data['id']); ?>
	<label class="col-md-12 control-label">Api key: <?php echo $api_key;?></label>
</div>

<div class="row">
	<label class="col-md-12 control-label">Short name: <?php echo $school_data['short_name'];?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">Email: <?php echo $login_data['email'];?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">Phone: <?php echo $login_data['phone_no'];?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">Address: <?php echo $school_data['address'];?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">City / village: <?php echo $school_data['city_village'];?></label>
</div>
<div class="row">
<?php $state=$this->Common_model->get_single_field_value('subregions','name','id',$school_data['state']); ?>
	<label class="col-md-12 control-label">State: <?php echo $state;?></label>
</div>
<div class="row">
	<label class="col-md-12 control-label">Pin: <?php echo $school_data['pin_code'];?></label>
</div>



