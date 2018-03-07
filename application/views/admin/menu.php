
<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                          <ul class="nav navbar-nav">
                            <li class="active"><a href="#">Dashboard <span class="sr-only">(current)</span></a></li>
                            <li><a href="#">Reports</a></li>
                             <li>
                             	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Vendor<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url('admin/add_vendor');?>">Add Vendor</a></li>
                                <li><a href="<?php echo base_url('admin/vendor_list');?>">Vendor List</a></li>
                               </ul>
                             </li>
                             
                             <li>
                             	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Client<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url('admin/add_school');?>">Add client</a></li>
                                <li><a href="<?php echo base_url('admin/school_list');?>">client List</a></li>
                                <li><a href="<?php echo base_url('admin/sms_config');?>">Sms Configuration</a></li>
                                <li><a href="<?php echo base_url('admin/sms_vendor_list');?>">Sms Vendor</a></li>
                                <li><a href="<?php echo base_url('admin/time_zone_list');?>">Time zone</a></li>
                                <li><a href="<?php echo base_url('admin/smtp_setting');?>">Smtp setting</a></li>
                                <li><a href="<?php echo base_url('admin/class_list/1');?>">Add Class/method </a></li>
                                <li><a href="<?php echo base_url('admin/class_list/2');?>">Edit Class/method </a></li>
								<li><a href="<?php echo base_url('admin/apply_sub_menu');?>">Apply Sub menu </a></li>
                               </ul>
                             </li>
                             <li>
                             	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Modules <span class="caret"></span></a>						
                                <ul class="dropdown-menu">
                                	<li><a href="<?php echo base_url('admin/modules');?>">Modules(class)</a></li>
                                    <li><a href="<?php echo base_url('admin/methods');?>">Methods</a></li>
                                    <li><a href="<?php echo base_url('admin/methods_menu');?>">Methods Menu</a></li>
                                    <li><a href="<?php echo base_url('admin/imported_methods');?>">Imported Methods</a></li>
                                </ul>
                             
                             
                             </li>
                            
                          </ul>
                         
                          <ul class="nav navbar-nav navbar-right">
                          <li><a href="#"><?php echo $this->session->userdata('operator_name'); ?></a></li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->Basic_model->login_data; ?> <span class="caret"></span></a>
                              <ul class="dropdown-menu">
                                <li><a href="#">My Account</a></li>
                                
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url('admin_login/logout_user');?>">Logout</a></li>
                              </ul>
                            </li>
                          </ul>
                        </div>