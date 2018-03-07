<?php $this->load->view('admin/header');?>

    <div class="container">
    	<form>
          <div class="form-group">
            <label for="class">Select Class</label>
            <select class="form-control" name="class"> 
            	<option value="">---- Select Class-----</option> 
             </select>
          </div>
		</form>
        <hr />
        <h5>Methods</h5>
        <form>
        	<table class="table ">
            	<tr>
                	<td><input type="text" class="form-control" readonly="readonly" value="<method name form class>"></td>
                    <td><input type="text" class="form-control" placeholder="Type Display Name"></td>
                </tr>
            </table>
            <button type="submit" class="btn btn-default">Import To Data Base</button>
            <a href="<?php echo base_url('admin/methods');?>" class="btn btn-default">Cancel</a>
        </form>
    </div>
<?php $this->load->view('admin/footer');?>