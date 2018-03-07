
<?php $this->load->view('admin/header');?>

    <div class="container">
    	<div class="title">
            <div class="pull-left"><h2> Imported Methods </h2></div>
            <div class="pull-right"><a class="btn btn-default" href="<?php echo base_url('admin/import_methods');?>">Import Methods</a></div>
        </div>
        <div class="clear"></div>
        <hr />
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#class-1" aria-expanded="true" aria-controls="collapseOne">
                  Display Class Name ( < actuall class name > )
                </a>
              </h4>
            </div>
            <div id="class-1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <div class="panel-body">
               	<table class="table">
                	<tr>
                    	<th>Method name</th>
                        <th>Display name</th>
                        <th>Parrent Name</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                    	<td>....</td>
                        <td>...</td>
                        <td>...</td>
                        <td><a class="btn btn-default btn-small" href="#">Edit</a><a class="btn btn-default btn-small" href="#">Delete</a></td>
                    </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#class-2" aria-expanded="false" aria-controls="collapseTwo">
                  Display Class Name ( < actuall class name > )
                </a>
              </h4>
            </div>
            <div id="class-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body">
                	<table class="table">
                	<tr>
                    	<th>Method name</th>
                        <th>Display name</th>
                        <th>Parrent Name</th>
                        <th>Action</th>
                    </tr>
                     <tr>
                    	<td>....</td>
                        <td>...</td>
                        <td>...</td>
                        <td><a class="btn btn-default btn-small" href="#">Edit</a><a class="btn btn-default btn-small" href="#">Delete</a></td>
                    </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingThree">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#class-3" aria-expanded="false" aria-controls="collapseThree">
                  Display Class Name ( < actuall class name > )
                </a>
              </h4>
            </div>
            <div id="class-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
              <div class="panel-body">
               	<table class="table">
                	<tr>
                    	<th>Method name</th>
                        <th>Display name</th>
                        <th>Parrent Name</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                    	<td>....</td>
                        <td>...</td>
                        <td>...</td>
                        <td><a class="btn btn-default btn-small" href="#">Edit</a><a class="btn btn-default btn-small" href="#">Delete</a></td>
                    </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        
    </div>
<?php $this->load->view('admin/footer');?>