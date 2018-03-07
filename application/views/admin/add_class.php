<?php $this->load->view('admin/header');?>

<div class="container">
	<h2>Modules</h2> 
    <form>
      <div class="form-group">
        <label for="exampleInputEmail1">Class Name</label>
        <input type="text" class="form-control" readonly="readonly" value="<any class name>">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Display name</label>
        <input type="tyxt" class="form-control" placeholder="Display name">
      </div>
     
      <button type="submit" class="btn btn-default">Add</button>
      <a href="#"  class="btn btn-default">Cancel</a>
    </form>
</div>
<?php $this->load->view('admin/footer');?>