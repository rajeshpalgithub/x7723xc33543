<?php
class Sop_model extends CI_Model
{
	
 public function InsertCustomerData($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';

	 $email=$post_data['email'];
	 $phone=$post_data['phone'];
	 
	 $rs=$this->db->select('*')->where('email',$email)->get('account');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Email id already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('phone',$phone)->get('account');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Phone no already exist';
	 }
	 
	 if(!$error)
	 {
		 $insert_arr=array
		   (
			 'client_id'=>$this->Basic_model->unique_id,
			 'name'=>$post_data['name'],
			 'email'=>$post_data['email'],
			 'phone'=>$post_data['phone'],
			 'role_id'=>$post_data['role_id'],
			 'description'=>$post_data['description'],
			 
			 'billing_street'=>$post_data['billing_street'],
			 'billing_city'=>$post_data['billing_city'],
			 'billing_state'=>$post_data['billing_state'],
			 'billing_postal_code'=>$post_data['billing_postal_code'],
			 'billing_country'=>$post_data['billing_country'],
			 
			 'shipping_street'=>$post_data['shipping_street'],
			 'shipping_city'=>$post_data['shipping_city'],
			 'shipping_state'=>$post_data['shipping_state'],
			 'shipping_postal_code'=>$post_data['shipping_postal_code'],
			 'shipping_country'=>$post_data['shipping_country'],
			 
			 'is_active'=> $post_data['inlineRadioOptions'],
		   );
		   
		  //echo '<pre>',print_r($insert_arr);
		  //die();
		   $success=$this->db->insert('account',$insert_arr);
		   if($success)
		   {
			   if(!empty($post_data['account_name']))
			   {
			     $account_id=$this->db->insert_id();
				 $contract_count=count($post_data['account_name']);
				 for($i=0;$i<=$contract_count-1;$i++)
				 {
					 $contract_arr=array(
					   'account_id'=>$account_id,
					   'name'=>$post_data['account_name'][$i],
					   'email'=>$post_data['account_email'][$i],
					   'phone'=>$post_data['account_phone'][$i],
					 );
					 
					 $success=$this->db->insert('account_contract',$contract_arr); 
				 }
			   }
			   
			 $result['successMessage']="Data successfully inserted";
		   }
		  else
		   {
			 $error=true;
			 $errortext='insert error';
			}
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}
	
	
 public function UpdateCustomerData($post_data,$cust_id)
 {
	 $error='';
	 $errortext='';
	 $result='';

	 $email=$post_data['email'];
	 $phone=$post_data['phone'];
	 
	 $rs=$this->db->select('*')->where('id!=',$cust_id)->where('email',$email)->get('account');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Email id already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('id!=',$cust_id)->where('phone',$phone)->get('account');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Phone no already exist';
	 }
	 
	 if(!$error)
	 {
		 $insert_arr=array
		   (
			 'name'=>$post_data['name'],
			 'email'=>$post_data['email'],
			 'phone'=>$post_data['phone'],
			 'role_id'=>$post_data['role_id'],
			 'description'=>$post_data['description'],
			 
			 'billing_street'=>$post_data['billing_street'],
			 'billing_city'=>$post_data['billing_city'],
			 'billing_state'=>$post_data['billing_state'],
			 'billing_postal_code'=>$post_data['billing_postal_code'],
			 'billing_country'=>$post_data['billing_country'],
			 
			 'shipping_street'=>$post_data['shipping_street'],
			 'shipping_city'=>$post_data['shipping_city'],
			 'shipping_state'=>$post_data['shipping_state'],
			 'shipping_postal_code'=>$post_data['shipping_postal_code'],
			 'shipping_country'=>$post_data['shipping_country'],
			 
			 'is_active'=> $post_data['inlineRadioOptions'],
		   );
		   
		  //echo '<pre>',print_r($insert_arr);
		  //die();
		   $success=$this->db->where('id',$cust_id)->update('account',$insert_arr);
		   if($success)
		   {
			   if(!empty($post_data['account_name']))
			   {
			     $success=$this->db->where('account_id',$cust_id)->delete('account_contract');
				 $contract_count=count($post_data['account_name']);
				 for($i=0;$i<=$contract_count-1;$i++)
				 {
					 $contract_arr=array(
					   'account_id'=>$cust_id,
					   'name'=>$post_data['account_name'][$i],
					   'email'=>$post_data['account_email'][$i],
					   'phone'=>$post_data['account_phone'][$i],
					 );
					 
					$success=$this->db->insert('account_contract',$contract_arr);
				 }
			   }
			   
			 $result['successMessage']="Data successfully updated";
		   }
		  else
		   {
			 $error=true;
			 $errortext='update error';
			}
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	}	
	
 
 public function GetContractDetails($cust_id)
 {
	 $contract_arr=array();
	 $rs=$this->db->select('*')->where('account_id',$cust_id)->get('account_contract');
	 if($rs->num_rows()>0)
	 {
		  $contract_arr=$rs->result_array();
	 }
	 
	 return $contract_arr;
 }
 
 
 
 public function GetCustomerData()
 {
	 $customer_arr=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('account');
	 if($rs->num_rows()>0)
	 {
		  $customer_arr=$rs->result_array();
	 }
	 
	 return $customer_arr;
 }

 public function GetCustomerDetailsOnId($cust_id)
 {
	  $customer_data=array();
	  $rs=$this->db->select('*')->where('id',$cust_id)->get('account');
	  if($rs->num_rows()>0)
	   {
		  $customer_data=$rs->row_array();
	   }
	   
	   return $customer_data;  
  }


 public function GetProductAttribute($cat_id)
 {
	 $attribute_arr=array();
	 $rs=$this->db->select('*')->where('cat_id',$cat_id)->order_by("attribute_name", "asc")->get('attribute');
	 if($rs->num_rows()>0)
	 {
		  $attribute_arr=$rs->result_array();
	 }
	 
	 return $attribute_arr;
 }
 
 
 public function InsertCategoryData($post_data)
 {
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 $category_name=$post_data['category_name'];
	 $client_id=$this->Basic_model->unique_id;
	 
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('category_name',$category_name)->get('category');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Category already exist';
	 }
	 
	 if(!$error)
	 {
		 
		  if($post_data['is_active']==true)
			 {
				 $is_active=1;
			 }
			 else
			 {
				 $is_active=0;
			 }
		 
		 
		 
		 $insert_arr=array
		   (
			 'client_id'=>$this->Basic_model->unique_id,
			 'category_name'=>$category_name,
			 'is_active'=> $is_active,
		   );
		   
		   //echo '<pre>',print_r($insert_arr);
		
		 $product_id="";	
		 $success=$this->db->insert('category',$insert_arr);
		 if($success)
		 {
			 $result['successMessage']="Data successfully inserted";
		 }
		 else
		 {
			 $error=true;
			 $errortext='insert error';
		 }
		 
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
	 
 }
 
 
 public function GetProductCategoryById($cat_id)
 {
	 $cat_arr=array();
	 $client_id=$this->Basic_model->unique_id;
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('is_active',1)->where('cat_id',$cat_id)->get('category');
	 if($rs->num_rows()>0)
	 {
		  $cat_arr=$rs->result_array();
	 }
	 
	 return $cat_arr;
 }
 
 public function GetProductCategory()
 {
	 $cat_arr=array();
	 $client_id=$this->Basic_model->unique_id;
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('is_active',1)->order_by("category_name", "asc")->get('category');
	 if($rs->num_rows()>0)
	 {
		  $cat_arr=$rs->result_array();
	 }
	 
	 return $cat_arr;
 }
 
 public function GetProductAttributeValue($att_id)
 {
	 $attribute_value=array();
	 $rs=$this->db->select('*')->where('attribute_id',$att_id)->order_by("attribute_value_name", "asc")->get('attribute_value');
	 if($rs->num_rows()>0)
	 {
		  $attribute_value=$rs->result_array();
	 }
	 
	 return $attribute_value;
 }
 
 
  public function UpdateProductData($product_id,$post_data)
  {
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 //echo '<pre>',print_r($post_data);
	 //die();
	 
	 
	 $product_nubmer=$post_data['product_nubmer'];
	 $sku_nubmer=$post_data['sku_nubmer'];
	 
	 $client_id=$this->Basic_model->unique_id;
	 
	 $rs=$this->db->select('*')->where('id!=',$product_id)->where('client_id',$client_id)->where('product_number',$product_nubmer)->get('products');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Product no already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('id!=',$product_id)->where('client_id',$client_id)->where('sku',$sku_nubmer)->get('products');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='SKU  already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('cat_id',$cat_id)->get('category');
	 if(!$rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Category not found';
	 }
	 
	 if(!$error)
	 {
		 $new_image_name=$post_data['image_path'];
		 
		 if($post_data['is_active']==true)
		 {
			 $is_active=1;
		 }
		 else
		 {
			 $is_active=0;
		 }
		 
		 $update_arr=array
		   (
			 'cat_id'=>$post_data['category_name'],
			 'sku'=>$post_data['sku_nubmer'],
			 'product_name'=>$post_data['product_name'],
			 'product_number'=>$post_data['product_nubmer'],
			 'product_price'=>$post_data['price'],
			 'product_description'=>$post_data['description'],
			 'product_image'=>$new_image_name,
			 'is_active'=>$is_active,
		   );
		   
		 //echo '<pre>',print_r($update_arr);	
		 $success=$this->db->where('id',$product_id)->update('products',$update_arr);
		 $success=$this->db->where('product_id',$product_id)->delete('product_attribute');	
		 
		 if(isset($post_data['attribute_value_name']))
		 {
		    for($i=0;$i<=count($post_data['attribute_value_name'])-1;$i++)
			{
				
				if($post_data['attribute_value_name'][$i]!="")
				{
				   $insert_arr2=array
				   (
					 'product_id'=>$product_id,
					 'attribute_id'=>$post_data['attribute_id'][$i],
					 'attribute_value_id'=>$post_data['attribute_value_name'][$i],
				   );
				   
				    //echo '<pre>',print_r($insert_arr2);
					
					  $success=$this->db->insert('product_attribute',$insert_arr2);
					   if($success)
					   {
						 $result['successMessage']="Data successfully inserted";
					   }
					   else
					   {
						 $error=true;
						 $errortext='insert error';
						}
				}

			}
		 }
		 //$success=1;	
		 if($success)
		   {
			 $result['successMessage']="Data successfully updated";
		   }
		   else
		   {
			 $error=true;
			 $errortext='update error';
		   }	
			
	 }
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	 
  }
 
 
 public function InsertProductData($post_data)
 {
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 $product_nubmer=$post_data['product_nubmer'];
	 $sku_nubmer=$post_data['sku_nubmer'];
	 $cat_id=$post_data['category_id'];
	 
	 $client_id=$this->Basic_model->unique_id;
	 
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('product_number',$product_nubmer)->get('products');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Product no already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('sku',$sku_nubmer)->get('products');
	 if($rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='SKU already exist';
	 }
	 
	 $rs=$this->db->select('*')->where('client_id',$client_id)->where('cat_id',$cat_id)->get('category');
	 if(!$rs->num_rows()>0)
	 {
		   $error=true;
		   $errortext='Category not found';
	 }
	 
	 if(!$error)
	 {
		 
		  $imgname=$post_data['image_path'];
		  
		  if($post_data['is_active']==true)
			 {
				 $is_active=1;
			 }
			 else
			 {
				 $is_active=0;
			 }
		 
		  $insert_arr=array
		   (
			 'client_id'=>$this->Basic_model->unique_id,
			 'cat_id'=>$post_data['category_id'],
			 'sku'=>$post_data['sku_nubmer'],
			 'product_name'=>$post_data['product_name'],
			 'product_number'=>$post_data['product_nubmer'],
			 'product_price'=>$post_data['price'],
			 'product_description'=>$post_data['description'],
			 'product_image'=>$imgname,
			 'is_active'=> $is_active,
		   );
		   
		   //echo '<pre>',print_r($insert_arr);
		
		 $product_id="";	
		 $success=$this->db->insert('products',$insert_arr);
		 $product_id=$this->db->insert_id();
		 
		 
		/* if(isset($post_data['attribute_value_name']))
		 {
		    for($i=0;$i<=count($post_data['attribute_value_name'])-1;$i++)
			{
				
				if($post_data['attribute_value_name'][$i]!="")
				{
				   $insert_arr2=array
				   (
					 'product_id'=>$product_id,
					 'attribute_id'=>$post_data['attribute_id'][$i],
					 'attribute_value_id'=>$post_data['attribute_value_name'][$i],
				   );
				   
				    //echo '<pre>',print_r($insert_arr2);
					
					  $success=$this->db->insert('product_attribute',$insert_arr2);
					   if($success)
					   {
						 $result['successMessage']="Data successfully inserted";
					   }
					   else
					   {
						 $error=true;
						 $errortext='insert error';
						}
				}

			}
		 }*/
		 
		 //die();
		
	 }
	
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 }
 
  public function GetCategoryCommon()
 {
	 $client_id=$this->Basic_model->unique_id;
	 $search_condition='';
	 
	 if($this->input->get('search_cat'))
	 {
		 $search_cat=$this->input->get('search_cat');
		 $search_text=$this->input->get('search_text');
		 $search_condition=" And $search_cat LIKE '%$search_text%'"; 
	 }
	
	 $sql="Select * from category Where client_id=$client_id $search_condition";
	 
	 return $sql;
 }
 
 public function getTotalCategoryCount()
 {
	 $category_count=0;
	 $sql=$this->GetCategoryCommon();
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		 $category_count=$rs->num_rows;
	 }
	 
	 return $category_count;
 }
 
 public function GetCategoryData($offset,$perpage)
 {
	 $product_arr=array();
	 $sql=$this->GetCategoryCommon();
	 $limit_query=" LIMIT $offset, $perpage";
	 $sql2=$sql.$limit_query;
	 $rs=$this->db->query($sql2);
	 //$rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('products');
	 if($rs->num_rows()>0)
	 {
		  $product_arr=$rs->result_array();
	 }
	 
	 return $product_arr;
	 
 }
 
 public function GetProductCommon()
 {
	 $client_id=$this->Basic_model->unique_id;
	 $search_condition='';
	 
	 if($this->input->get('search_cat'))
	 {
		 $search_cat=$this->input->get('search_cat');
		 $search_text=$this->input->get('search_text');
		 $search_condition=" And $search_cat LIKE '%$search_text%'"; 
	 }
	
	 $sql="Select * from products Where client_id=$client_id $search_condition";
	 
	 return $sql;
 }
 
 public function getTotalProductCount()
 {
	 $product_count=0;
	 $sql=$this->GetProductCommon();
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		 $product_count=$rs->num_rows;
	 }
	 
	 return $product_count;
 }
 
 public function GetProductData($offset,$perpage)
 {
	 $product_arr=array();
	 $sql=$this->GetProductCommon();
	 $limit_query=" LIMIT $offset, $perpage";
	 $sql2=$sql.$limit_query;
	 $rs=$this->db->query($sql2);
	 //$rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('products');
	 if($rs->num_rows()>0)
	 {
		  $product_arr=$rs->result_array();
	 }
	 
	 return $product_arr;
	 
 }
 
 
 public function GetProductDetailsOnId($product_id)
 {
	  $product_data=array();
	  $rs=$this->db->select('*')->where('id',$product_id)->get('products');
	  if($rs->num_rows()>0)
	   {
		  $product_data=$rs->row_array();
	   }
	   
	   return $product_data;  
  }
  
  
  public function GetProductOnCatId($cat_id)
 {
	  $product_data=array();
	  $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->where('cat_id',$cat_id)->get('products');
	  if($rs->num_rows()>0)
	   {
		  $product_data=$rs->result_array();
	   }
	   
	   return $product_data;  
  }
  
  
 public function GetAttributeDetailsOnProductId($product_id)
 {
	  $attribute_data=array();
	  $rs=$this->db->select('*')->where('product_id',$product_id)->get('product_attribute');
	  if($rs->num_rows()>0)
	   {
		  $attribute_data=$rs->result_array();
	   }
	   
	   return $attribute_data;  
  }
  
  
 public function GetWarehouseDetails()
 {
	 
	 $warehouse_arr=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('warehouse');
	 if($rs->num_rows()>0)
	 {
		  $warehouse_arr=$rs->result_array();
	 }
	 
	 return $warehouse_arr;
	 
 }
 
 
 public function InsertStockData($post_data)
 {
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 $sku_name=$post_data['sku'];
	 //$sku_name=$this->Common_model->get_single_field_value('products','sku','id',$product_id);
	 
	 $rs3=$this->db->select('*')->where('sku',$sku_name)->where('client_id',$this->Basic_model->unique_id)->get('products');
	 if(!$rs3->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Product Not available";
	 }
	 
	 
	 $rs3=$this->db->select('*')->where('sku',$sku_name)->where('client_id',$this->Basic_model->unique_id)->get('master_stock');
	 if($rs3->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Product already exist";
	 }
	 
	 
	 if(!$error)
	 {
		 $insert_arr=array
		   (
			 'client_id'=>$this->Basic_model->unique_id,
			 'sku'=>$sku_name,
			 'avl_qty'=>$post_data['avl_qty'],
			 'alert_qty'=>$post_data['alert_qty'],
		   );
			   
		//echo '<pre>',print_r($insert_arr);
		//die();	
		 $success=$this->db->insert('master_stock',$insert_arr);
		 if($success)
		   {
			 $result['successMessage']="Data successfully inserted";
		   }
		   else
		   {
			 $error=true;
			 $errortext='insert error';
			}
	 }
	 
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 }
 
 public function UpdateStockData($post_data)
 {
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 
	  $stock_id=$post_data['stock_id'];
	 //$sku_name=$this->Common_model->get_single_field_value('products','sku','id',$product_id);
	 
	 $rs3=$this->db->select('*')->where('id',$stock_id)->get('master_stock');
	 if(!$rs3->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Stock id not found";
	 }
	 
	 if(!$error)
	 {
		 $update_arr=array
		   (
			 'avl_qty'=>$post_data['avl_qty'],
			 'alert_qty'=>$post_data['alert_qty'],
		   );
			   
		//echo '<pre>',print_r($update_arr);
		//die();	
		 $success=$this->db->where('id',$stock_id)->update('master_stock',$update_arr);
		 if($success)
		   {
			 $result['successMessage']="Data successfully updated";
		   }
		   else
		   {
			 $error=true;
			 $errortext='insert error';
			}
	 }
	 
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	 
 }
  
 public function GetStockData()
 {
	 $stock_arr=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('master_stock');
	 if($rs->num_rows()>0)
	 {
		  $stock_arr=$rs->result_array();
	 }
	 
	 return $stock_arr;
 }
 
 public function GetStockDetailsOnId($stock_id)
 {
	 $stock_arr=array();
	 $rs=$this->db->select('*')->where('id',$stock_id)->where('client_id',$this->Basic_model->unique_id)->get('master_stock');
	 if($rs->num_rows()>0)
	 {
		  $stock_arr=$rs->row_array();
	 }
	 
	 return $stock_arr;
 }
 
 
 public function GetChargesData()
 {
	 $charges_arr=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('additional_charges');
	 if($rs->num_rows()>0)
	 {
		  $charges_arr=$rs->result_array();
	 }
	 
	 return $charges_arr;
 }
 
 
 public function GetChargesDataOnId($charge_id)
 {
	 $charges_arr=array();
	 $rs=$this->db->select('*')->where('id',$charge_id)->where('client_id',$this->Basic_model->unique_id)->get('additional_charges');
	 if($rs->num_rows()>0)
	 {
		  $charges_arr=$rs->row_array();
	 }
	 
	 return $charges_arr;
 }
 
 
 public function InsertChargesData($post_data)
 {
	 
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 $lbl_of_charges=trim($post_data['label_of_charges']);
	 	 
	 $rs3=$this->db->select('*')->where('lable_of_charge',$lbl_of_charges)->where('client_id',$this->Basic_model->unique_id)->get('additional_charges');
	 if($rs3->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Label of charges already exist";
	 }
	 else
	 {
		 $insert_arr=array
		   (
			 'client_id'=>$this->Basic_model->unique_id,
			 'lable_of_charge'=>$lbl_of_charges,
			 'charge'=>$post_data['charges'],
			 'is_percent'=>$post_data['charges_type'],
			 'is_active'=>$post_data['inlineRadioOptions'],
		   );
			   
		//echo '<pre>',print_r($insert_arr);
		//die();	
		 $success=$this->db->insert('additional_charges',$insert_arr);
		 if($success)
		   {
			 $result['successMessage']="Data successfully inserted";
		   }
		   else
		   {
			 $error=true;
			 $errortext='insert error';
			}
	 }
	 
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 
 }
 
 
 public function UpdateAddtionalCharges($charge_id,$post_data)
 {
	
	 
	 $error='';
	 $errortext='';
	 $result=''; 
	 
	 $lbl_of_charges=trim($post_data['label_of_charges']);
	 	 
	 $rs3=$this->db->select('*')->where('id!=',$charge_id)->where('lable_of_charge',$lbl_of_charges)
	 		->where('client_id',$this->Basic_model->unique_id)->get('additional_charges');
	 if($rs3->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Label of charges already exist";
	 }
	 else
	 {
		 $insert_arr=array
		   (
			 'lable_of_charge'=>$lbl_of_charges,
			 'charge'=>$post_data['charges'],
			 'is_percent'=>$post_data['charges_type'],
			 'is_active'=>$post_data['inlineRadioOptions'],
		   );
			   
		//echo '<pre>',print_r($insert_arr);
		//die();	
		 $success=$this->db->where('id',$charge_id)->update('additional_charges',$insert_arr);
		 if($success)
		   {
			 $result['successMessage']="Data successfully updated";
		   }
		   else
		   {
			 $error=true;
			 $errortext='insert error';
			}
	 }
	 
	 
	 return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 
  
 }
 
 public function InsertDataToCart($post_data)
 {
	 $error='';
	 $errortext='';
	 $result='';
	 
	 //$customer_name=$post_data['customer_name'];
	 //$category_name=$post_data['category_name'];
	 $sku_name=$post_data['product_sku'];
	 $product_qty=$post_data['qty'];
	 
	 $rs=$this->db->select('*')->where('sku',$sku_name)->where('client_id',$this->Basic_model->unique_id)
	 ->get('products');
	 if(!$rs->num_rows()>0)
	 {
		 $error=true;
		 $errortext="Product is not available";
	 }
	 
	 if(!$error)
	 {
		 $product_id=$this->Common_model->get_single_field_value('products','id','sku',$sku_name);
		 $product_name=$this->Common_model->get_single_field_value('products','product_name','sku',$sku_name);
		 $product_image=$this->Common_model->get_single_field_value('products','product_image','id',$product_id);
		 $product_price=$this->Common_model->get_single_field_value('products','product_price','id',$product_id);
		 //$sku_name=$this->Common_model->get_single_field_value('products','sku','id',$product_id);
		 $avl_qty=$this->Common_model->get_single_field_value('master_stock','avl_qty','sku',$sku_name);
		 $alert_qty=$this->Common_model->get_single_field_value('master_stock','alert_qty','sku',$sku_name);
	 }
	 
	 //$this->cart->destroy();
	 //die();
	 
	 $rs=$this->db->select('*')->where('sku',$sku_name)->where('client_id',$this->Basic_model->unique_id)
	 ->get('master_stock');
	 if($rs->num_rows()>0)
	 {
		 $stock_arr=$rs->row_array();
		 if($product_qty >$stock_arr['avl_qty'])
		 {
			 $error=true;
			 $errortext="Stock is not available";
		 }		 
	 }
	 else
	 {
		 $error=true;
		 $errortext="Stock details not found";
	 }
	 
	 
	$rs=$this->db->select('*')->where('sku_name',$sku_name)->where('client_id',$this->Basic_model->unique_id)
	->where('sub_unique_id',$this->Basic_model->sub_unique_id)->get('temp_cart');
	 if($rs->num_rows()>0)
	 {
		 $error=true;
		 $errortext="This product is already selected";
	 }
	 
	 /*$check_cart=$this->cart->contents();
	 if(!empty($check_cart))
	 {
		 foreach($check_cart as $item)
		 {
			 $cart_sku=$item['id'];
			 if($sku_name==$cart_sku)
			 {
				 $error=true;
				 $errortext="This product is already selected";
				 break;
			 }
		 }
	 }*/
	 
	 
	 if(!$error)
	 {
		 
		 $temp_cart=array(
		  'token_id'=>$this->Basic_model->token_id,
		  'sku_name'=>$sku_name,
		  'product_id'=>$product_id,
		  'product_qty'=>$product_qty,
		  'product_price'=>$product_price,
		  'avl_qty'=>$avl_qty,
		  'client_id'=>$this->Basic_model->unique_id,
		  'sub_unique_id'=>$this->Basic_model->sub_unique_id,
		 );
		$success=$this->db->insert('temp_cart',$temp_cart);
		 
	   /*$product_name=htmlspecialchars($product_name);	 
	   $data = array(
        'id'      => $sku_name,
        'qty'     => $product_qty,
        'price'   => $product_price,
        'name'    => $sku_name,
        'options' => array(
					'product_id' => $product_id, 
					'avl_qty' => $avl_qty,
					'alert_qty' => $alert_qty,
					'product_image' => $product_image
						   )
		);
		
		$this->cart->insert($data);
		*/
		
		
	  //echo '<pre>',print_r($data);
	  //die();
	  	
	  
	
	  $result['successMessage']='Product successfully inserted';
	 
	 }
	
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 }


 public function SubmitProductOrder($post_data)
 {
	//echo '<pre>',print_r($post_data);
	//die();
	
	$error=false;
	$errortext='';
	$result='';
	
	$order_id="";
	$invoice_id="";
	
	$customer_id=$post_data['customer_id'];
	$order_status=$post_data['order_status_id'];
	$note=$post_data['note'];
	
	$token_id=$this->Basic_model->token_id;
	$client_id=$this->Basic_model->unique_id;
	$sub_unique_id=$this->Basic_model->sub_unique_id;
	
	$rs=$this->db->where('token_id',$token_id)->where('client_id',$client_id)->where('sub_unique_id',$sub_unique_id)
	->get('temp_cart');
	if(!$rs->num_rows()>0)
	{
		$error=true;
	    $errortext='Cart is empty';
	}
	
	if(!$error)
	{
		$rs=$this->db->select('*')->where('id',$customer_id)->where('client_id',$this->Basic_model->unique_id)->get('account');
	    if(!$rs->num_rows()>0)
	    {
		   $error=true;
		   $errortext='Customer not found';
	    }
		
		$client_id=$this->Basic_model->unique_id;
		$sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
	      INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role INNER JOIN status_master ON order_status_master.status_text=status_master.id 
		  Where role_master.object_id=$client_id And status_master.is_active=1 And sub_role_details.is_active=1 
		  and role_master.is_active=1 And status_master.client_id=$client_id And sub_role_details.id=$order_status";
		  $rs=$this->db->query($sql);
		  if(!$rs->num_rows()>0)
	      {
		    $error=true;
		    $errortext='Invalid status id';
	      }
		
	}
	
	if(!$error)
	{
	    
	   $cart_data=$this->GetCartDetails();
	   
	   //print_r($cart_data);
	  // die();
	   
	   $product_list=$cart_data['product_list'];
	   $charges=$cart_data['charges'];
	   
	   $subtotal=$cart_data['sub_total'];
	   $charges_total=$cart_data['charges_total'];
	   $grand_total=$cart_data['grand_total'];
	//insert order
		$order_array=array(
		  'customer_id'=>$customer_id,
		  'order_total'=>$subtotal,
		  'order_status'=>$order_status,
		  'note'=>$note
		);
	
	
	$this->db->insert('product_order',$order_array);
	$order_id=$this->db->insert_id();
	
	
	$order_status_log_array=array(
	  'client_id'=>$this->Basic_model->unique_id,
	  'order_id'=>$order_id,
	  'role_id'=>$this->Basic_model->sub_unique_id,
	  'order_status_id'=>$order_status,
	);
	
	//echo '<pre>',print_r($order_array);
	$this->db->insert('order_status_log',$order_status_log_array);
	
	//$product_count=count($product_id);
	foreach($product_list as $item)
	{
		 $new_qty=0;
		 $sku_no="";
		 $order_details_array=array(
		  'order_id'=>$order_id,
		  'product_id'=>$item['product_id'],
		  'qty'=>$item['qty'],
		  'rate'=>$item['price'],
		  'total_rate'=>$item['amount'],
		 );
		 
		 //echo '<pre>',print_r($order_details_array);
	    $this->db->insert('order_details',$order_details_array);
		
		$sku_no=$item['sku'];
		$new_qty=$item['avl_qty']-$item['qty'];
		
		$update_arr=array(
		 'avl_qty'=>$new_qty
		);
		
		$success=$this->db->where('client_id',$this->Basic_model->unique_id)->where('sku',$sku_no)
		->update('master_stock',$update_arr);
		
	}
	
	$invoice_array=array(
	  'client_id'=>$this->Basic_model->unique_id,
	  'customer_id'=>$customer_id,
	  'order_id'=>$order_id,
	  'sub_total'=>$subtotal,
	  'charges_total'=>$charges_total,
	  'grand_total'=>$grand_total,
	 );
	 
	 //echo '<pre>',print_r($invoice_array);
	 $this->db->insert('invoice',$invoice_array);
	 $invoice_id=$this->db->insert_id();
	 
	 if(!empty($charges))
	 {
       
		foreach($charges as $key=>$value)
		{
			 $charges_array=array(
			  'order_id'=>$order_id,
			  'charges'=>$key,
			 );
			 
			 //echo '<pre>',print_r(charges);
			$this->db->insert('charges',$charges_array);
			$charges_id=$this->db->insert_id();
			
			$charges_array=array(
			  'invoice_id'=>$invoice_id,
			  'charges_id'=>$charges_id,
			  'amount'=>$value,
			 );
			 
			 //echo '<pre>',print_r(charges);
			$this->db->insert('invoice_charges',$charges_array);
			
			
		}
	 }
	
	$token_id=$this->Basic_model->token_id;
	$rs=$this->db->where('token_id',$token_id)->where('client_id',$client_id)->where('sub_unique_id',$sub_unique_id)
	->delete('temp_cart');
	
    $result['successMessage']="Data successfully inserted";
	$result['invoice_id']=$invoice_id;
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 
 }
 
 
 public function GetCartDetails()
 {
	 $cart_details=array();
	 $cart_content=array();
	 
	 $token_id=$this->Basic_model->token_id;
	 $client_id=$this->Basic_model->unique_id;
	 $sub_unique_id=$this->Basic_model->sub_unique_id;
	
	 $rs=$this->db->where('token_id',$token_id)->where('client_id',$client_id)->where('sub_unique_id',$sub_unique_id)->get('temp_cart');
	 if($rs->num_rows()>0)
	 {
		  $cart_content=$rs->result_array();
	 }
	 
	 $data['cart_content']=$cart_content;
	 $data['order_status']=$this->Sop_model->GetOrderStatus();
	 $data['additonal_charges']=$this->Sop_model->GetChargesData();
	
	 
	 if(!empty($data))
	 {
		$cart_details=$this->ViewFormattedCart($data);
	 }
	
	 return $cart_details;
 }

 public function ViewFormattedCart($data)
 {
	 $cart_content=$data['cart_content'];
	 $additonal_charges=$data['additonal_charges'];
	 $subtotal=0;
	 $total_additional_charges=0;
	 $grand_total=0;
	 $product_details=array();
	 $product_charges=array();
	 $formatted_order_details='Cart is empty';
	 
	 //print_r($cart_content);
	 //die();
	 
	 if(!empty($cart_content))
	 {
		 foreach($cart_content as $item)
		 {
			$item_image="";
			$item_name="";
			$sku="";
			$show_item="";
			$qty="";
			$price="";
			$amount="";
			$row_id="";
			$cust_id="";
			$avl_qty="";
			
			$product_id=$item['product_id'];
			$item_image=$this->Common_model->get_single_field_value('products','product_image','id',$product_id);
			if($item_image=="")
			{
				$item_image="Image-not-found.gif";
			}
			
			$avl_qty=$item['avl_qty'];
			$sku=$item['sku_name'];
			
			
			$item_name=$this->Common_model->get_single_field_value('products','product_name','id',$product_id);
			$show_item=$item_name." ( SKU: ".$sku.")";
			
			$qty=$item['product_qty'];
			$price=$item['product_price'];
			$amount=($qty*$price);
			$subtotal=$subtotal+$amount;
			
			$product_details[]=array(
			 'product_id'=>$product_id,
			 'product_name'=>$item_name,
			 'sku'=>$sku,
			 'avl_qty'=>$avl_qty,
			 'product_sku'=>$show_item,
			 'qty'=>$qty,
			 'price'=>$price,
			 'amount'=>$amount,
			);
			
		}
		
		if(!empty($additonal_charges))
		{
		   foreach($additonal_charges as $row)
		   {
			  $charge_amount="";
			  $charge_id="";
			  
			  $charge_id=$row['id'];
			  if($row['is_percent']==1)
			  {
				  $charge_amount=floatval($subtotal*$row['charge']/100);
			  }
			  else
			  {
				  $charge_amount=$row['charge'];
			  }
			  
			  $total_additional_charges=$total_additional_charges+$charge_amount;
			  
			  $product_charges[$row['lable_of_charge']]=$charge_amount;
			   
		   }
		}
		
		$grand_total=$subtotal+$total_additional_charges;
	 
		 $formatted_order_details=array
		 (
		  'product_list'=>$product_details,
		  'sub_total'=>$subtotal, 
		  'charges'=>$product_charges,
		  'charges_total'=>$total_additional_charges, 
		  'grand_total'=>$grand_total,
		);
		
	 }

	return $formatted_order_details;
	 
 }



 public function delete_cart_item($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
	$sku_name=$post_data['sku_name'];
	
	$check_row_id=$this->CheckRowId($sku_name);
	if($check_row_id==0)
	{
		$error=true;
		$errortext='Invalid sku'; 
	}
	
	if(!$error)
	{
		$token_id=$this->Basic_model->token_id;
		$client_id=$this->Basic_model->unique_id;
	  	$sub_unique_id=$this->Basic_model->sub_unique_id;
		
	  	$success=$this->db->where('token_id',$token_id)->where('client_id',$client_id)
		->where('sub_unique_id',$sub_unique_id)
		->where('sku_name',$sku_name)->delete('temp_cart'); 
		
		if($success)
		{
		  $message='Item successfully removed';
		  $result['successMessage']=$message;
		}
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 		
		
 }
  
  public function CheckRowId($sku_name)
  {
	  $flag=0;
	  
	  $client_id=$this->Basic_model->unique_id;
	  $sub_unique_id=$this->Basic_model->sub_unique_id;
	  $token_id=$this->Basic_model->token_id;
	   
	  $rs=$this->db->where('token_id',$token_id)->where('client_id',$client_id)
	  ->where('sub_unique_id',$sub_unique_id)->where('sku_name',$sku_name)->get('temp_cart');
	  
	  if($rs->num_rows()>0)
	  {
		  $flag=1;
	  }
	 
	  return $flag;
  }
  
  
  public function update_cart_qty($post_data)
  {
		$error='';
		$errortext='';
		$result='';
		
		$data='';
		$qty=$post_data['qty'];
		$sku_name=$post_data['sku_name'];
		
		$check_row_id=$this->CheckRowId($sku_name);
		if($check_row_id==0)
		{
			$error=true;
			$errortext='Invalid sku'; 
		}
		
		$rs=$this->db->select('*')->where('sku',$sku_name)->where('client_id',$this->Basic_model->unique_id)
		->get('master_stock');
		if(!$rs->num_rows()>0)
		{
			$error=true;
			$errortext='Stock is not available'; 
		}
		
		if($qty <= 0)
		{
			$error=true;
			$errortext='Quantity must be greater than or equal zero'; 
		}
		
		if(!$error)
		{
			 $stock_arr=$rs->row_array();
			 if($qty >$stock_arr['avl_qty'])
			 {
				 $message="Stock is not available";
				 $error=true;
			     $errortext=$message;
			 }
			 else
			 {
				$update_data = array(
				  'sku_name'=>$sku_name,
				  'product_qty'=>$qty
				);
				
				
				$token_id=$this->Basic_model->token_id;
				$client_id=$this->Basic_model->unique_id;
	  			$sub_unique_id=$this->Basic_model->sub_unique_id;
				
	  			$success=$this->db->where('token_id',$token_id)->where('client_id',$client_id)
				->where('sub_unique_id',$sub_unique_id)->where('sku_name',$sku_name)
				->update('temp_cart',$update_data); 
				
				if($success)
				{
					$message="Item successfully updated";
					$result['successMessage']=$message;
				}
				else
				{
					 $message='Error in update';
					 $error=true;
					 $errortext=$message;
				}
			 }
		}
		
	  return array('error' => $error, 'errortext' => $errortext, 'result' => $result); 	
	}


 public function GetProductOrderDetailsOnId($inv_id)
 {
	$order_details=array();
	 
	$data['charges_details']=$this->GetChargesDetailsOnId($inv_id);
	
	$data['inv_details']=$this->GetInvoiceDetailsOnId($inv_id);
	$order_id=$this->Common_model->get_single_field_value('invoice','order_id','id',$inv_id);
	$data['orders']=$this->GetOrderOnId($order_id);
	$data['order_details']=$this->GetOrderDetailsOnId($order_id);
	$data['order_status']=$this->GetOrderStatus();
	
	$data['category_details']=$this->GetProductCategory();
	//$data['cart_content']=$this->cart->contents();
	//$data['order_status']=$this->GetOrderStatus();
	$data['additonal_charges']=$this->GetChargesData();
	
	if(!empty($data))
	{
		$order_details=$this->FormattedOrderDetails($data);
	}
	
	return $order_details;
 }

 public function FormattedOrderDetails($data)
 {
	 
	 $formatted_order_details=array();
	
	 $order_details=$data['order_details'];
	 $inv_details=$data['inv_details'];
	 $charges_details=$data['charges_details'];
	 $orders=$data['orders'];
	 
	 $product_details=array();
	 $product_charges=array();
	 
	 $invoice_date=date_format(date_create($inv_details['date_time']),"d/m/Y");
	 $invoice_id=$inv_details['id'];
	 $order_id=$inv_details['order_id'];
	 $customer_name=$this->Common_model->get_single_field_value('account','name','id',$inv_details['customer_id']);
	 
	 $sub_total=$inv_details['sub_total'];
	 $charges_total=$inv_details['charges_total'];
	 $grand_total=$inv_details['grand_total'];
	 $note=$orders['note'];
	 $db_order_status_id=$orders['order_status'];
	 
	 $order_status_text=$this->Common_model->get_single_field_value('status_master','order_status_text','id',$db_order_status_id);
	 
	 if(!empty($order_details))
	 {  
			foreach($order_details as $item)
			{
				$item_image="";
				$item_name="";
				$sku="";
				$show_item="";
				$qty="";
				$price="";
				$amount="";
				$row_id="";
				$cust_id="";
				$avl_qty="";

				$product_id=$item['product_id'];
				$sku=$this->Common_model->get_single_field_value('products','sku','id',$product_id);
				$item_image=$this->Common_model->get_single_field_value('products','product_image','id',$product_id);
				if($item_image=="")
				{
					$item_image="Image-not-found.gif";
				}
				
				$item_name=$this->Common_model->get_single_field_value('products','product_name','id',$product_id);
				$show_item=$item_name." ( SKU: ".$sku.")";
				
				$qty=$item['qty'];
				$price=$item['rate'];
				$amount=$item['total_rate'];
				
				$product_details[]=array(
				 'product_id'=>$product_id,
				 'product_name'=>$item_name,
				 'product_sku'=>$show_item,
				 'qty'=>$qty,
				 'price'=>$price,
				 'amount'=>$amount,
				);
				
			}
			
			
		}
	
	

	if(!empty($charges_details))
	{
	   foreach($charges_details as $row)
	   {
		   $charge_label_id="";
		   $charge_label="";
		   $charge_label_id=$row['charges_id'];
		   $charge_label=$this->Common_model->get_single_field_value('charges','charges','id',$charge_label_id);
		
		   $product_charges[$charge_label]=$row['amount'];
		   
	   }
	}
	
	
	$formatted_order_details=array
	(
	  'invoice_date'=>$invoice_date,
	  'invoice_id'=>$invoice_id,
	  'order_id'=>$order_id,
	  'customer_name'=>$customer_name,
	  'product_list'=>$product_details,
	  'sub_total'=>$sub_total, 
	  'charges'=>$product_charges,
	  'charges_total'=>$charges_total, 
	  'grand_total'=>$grand_total,
	  'note'=>$note,
	  'status'=>$order_status_text,
	);
	
	
	return $formatted_order_details;
		
 }

 public function GetProductOrderDetails()
 {
	 $order_details=array();
	 $client_id=$this->Basic_model->unique_id;
	 $search_condition="";
	 $search_cat="";
	 $search_text="";
	 if($this->input->get('search_cat'))
	 {
		 $search_cat=$this->input->get('search_cat');
		 $search_text=$this->input->get('search_text');
		 $search_condition=" And $search_cat LIKE '%$search_text%'"; 
	 }
	 
	 if($this->input->get('start_date'))
	 {
		 
		 $start_date=date("Y-m-d", strtotime($this->input->get('start_date')));
		 $end_date=date("Y-m-d", strtotime($this->input->get('end_date')));
		
		 $start_date_time=$start_date." 00:00:00.000";
		 $end_date_time=$end_date." 23:59:59.997";
			
		$search_condition=" AND date_time between '$start_date_time' and '$end_date_time'";
		 
	 }
	 else
	 {
		 $current_year=date("Y");
	     $start_of_current_financial_year=$current_year."-04-01";
		 $end_of_current_financial_year=($current_year+1)."-03-31";
		 
		 $start_date_time=$start_of_current_financial_year." 00:00:00.000";
		 $end_date_time=$end_of_current_financial_year." 23:59:59.997";
			
		 $search_condition=" AND date_time between '$start_date_time' and '$end_date_time'";
	 }
	 
	 if($search_cat=='customer_id')
	 {
		 $sql="select * from invoice INNER JOIN account on invoice.customer_id=account.id where invoice.client_id=$client_id
		      and account.name LIKE '%$search_text%' order by invoice.date_time desc";
	 }
	 else
	 {
	   $sql="Select * from invoice Where client_id=$client_id $search_condition order by date_time desc";
	 }
	 
	 
	 if($this->input->get('status_id'))
	 {
		 $status_id=$this->input->get('status_id');
		 
		 $sql="Select * from invoice INNER JOIN product_order ON invoice.order_id=product_order.id INNER JOIN order_status_master
		  ON product_order.order_status=order_status_master.id INNER JOIN status_master ON order_status_master.status_text=status_master.id
		  Where invoice.client_id=$client_id And status_master.id=$status_id order by invoice.date_time desc";
	 }
	 
	 
	 $rs=$this->db->query($sql);
	 
	// echo $this->db->last_query();
	// die();

	//$rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->order_by("date_time", "desc")->get('invoice');
	 if($rs->num_rows()>0)
	 {
		  $order_details_raw=$rs->result_array();
		  $order_details=$this->GetFormatProductOrderDetails($order_details_raw);
	 }
	 
	 return $order_details; 
 }
 
 
 public function GetFormatProductOrderDetails($order_details)
 {
	 $formatted_details=array();
	 if(!empty($order_details))
		{
			foreach($order_details as $item)
			{
				$customer_name='';
				$customer_name=$this->Common_model->get_single_field_value('account','name','id',$item['customer_id']);
				$status_text="";
				$order_status=$this->Common_model->get_single_field_value('product_order','order_status','id',$item['order_id']);;
				$status_id=$this->Common_model->get_single_field_value('order_status_master','status_text','id',$order_status);
				$status_text=$this->Common_model->get_single_field_value('status_master','order_status_text','id',$status_id);
				
				$formatted_details[]=array(
				  'date'=>date_format(date_create($item['date_time']),"d/m/Y"),
				  'invoice_id'=>$item['id'],
				  'order_id'=>$item['order_id'],
				  'customer_name'=>$customer_name,
				  'order_status'=>$status_text,
				);
			}
		}
		
		return $formatted_details;
 }
 
 
  public function GetInvoiceDetailsOnId($inv_id)
  {
	 $invoice_details=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->where('id',$inv_id)->get('invoice');
	 
	 if($rs->num_rows()>0)
	 {
		  $invoice_details=$rs->row_array();
	 }
	 
	 return $invoice_details;
  }
  
  
  public function GetChargesDetailsOnId($inv_id)
  {
	 $charges_details=array();
	 $rs=$this->db->select('*')->where('invoice_id',$inv_id)->get('invoice_charges');
	 
	 if($rs->num_rows()>0)
	 {
		  $charges_details=$rs->result_array();
	 }
	 
	 return $charges_details;
  }
  
  
  public function GetOrderDetailsOnId($order_id)
  {
	 $order_details=array();
	 $rs=$this->db->select('*')->where('order_id',$order_id)->get('order_details');
	 
	 if($rs->num_rows()>0)
	 {
		  $order_details=$rs->result_array();
	 }
	 
	 return $order_details;
  }
  
  
  public function  GetOrderOnId($order_id)
  {
	 $order_details=array();
	 $rs=$this->db->select('*')->where('id',$order_id)->get('product_order');
	 
	 if($rs->num_rows()>0)
	 {
		  $order_details=$rs->row_array();
	 }
	 
	 return $order_details;
  }
  
  
  function roleParentChildTree2($parent,$tree = '',$category_tree_array = '')
  {
	  $branch=array();
	  $client_id=$this->Basic_model->unique_id;
	  if (!is_array($category_tree_array))
	  {
		$category_tree_array = array();
	  }
	   $sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
	   INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role INNER JOIN status_master ON order_status_master.status_text=status_master.id 
	   Where role_master.object_id=$client_id and sub_role_details.is_active=1 and status_master.is_active=1
	   and role_master.is_active=1 and sub_role_details.report_to=$parent order by sub_role_details.id asc";
			
		//$sql="Select * from sub_role_details Where report_to=$parent order by id asc";	
			
	  $rs=$this->db->query($sql);
	 
	  if($rs->num_rows()>0)
	  { 
	  //$tree .= '<li>';
		 
		  $tree .= '<ul>';
		  $role_array=$rs->result_array();
		  
		  foreach($role_array as $item)
		  {

				$category_tree_array[]= array(
				   "id" => $item['role'] 
				   
				);
			 	$tree .= '<li><span class="folder">'.$item['name'].'</span>';		   
			  $tree = $this->roleParentChildTree2($item['role'],$tree,$category_tree_array);
		  }
		 $tree .= '</ul>';
		$tree .= '</li>';
	  }
	  
	  
	  return $tree;
  }
  
  
  
  function roleParentChildTree($parent, $category_tree_array = '')
  {
	  $branch=array();
	  $client_id=$this->Basic_model->unique_id;
	  if (!is_array($category_tree_array))
	  {
		$category_tree_array = array();
	  }
	   $sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
	   INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role INNER JOIN status_master ON order_status_master.status_text=status_master.id 
	   Where role_master.object_id=$client_id and sub_role_details.is_active=1 and status_master.is_active=1
	   and role_master.is_active=1 and sub_role_details.report_to=$parent order by sub_role_details.id asc";
			
		//$sql="Select * from sub_role_details Where report_to=$parent order by id asc";	
			
	  $rs=$this->db->query($sql);
	  if($rs->num_rows()>0)
	  {
		  $role_array=$rs->result_array();
		  foreach($role_array as $item)
		  {
			  $category_tree_array[]= array(
			                   "id" => $item['role'], 
							   "name" => $item['name'],
							   "role_name" => $item['role_name'],
							   "order_status_id" => $item['order_status_id'],
							   "status_text" => $item['order_status_text'],
							   );
			 			   
			  $category_tree_array = $this->roleParentChildTree($item['role'],$category_tree_array);
		  }
	  }
	  
	  return $category_tree_array;
  }
  
 
  
  public function GetOrderStatus()
  {
	  $status_arr=array();
	  $client_id=$this->Basic_model->unique_id;
	  $role_access="";
	  
	  //echo $this->Basic_model->sub_unique_id;
	  //die();
	 
	  if($this->Basic_model->sub_unique_id==0)
	  {
		  //admin
		  $sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
	      INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role INNER JOIN status_master ON order_status_master.status_text=status_master.id 
		  Where role_master.object_id=$client_id And status_master.is_active=1 And
	      sub_role_details.is_active=1 and role_master.is_active=1 And status_master.client_id=$client_id order by sub_role_details.id asc";
		
		  $rs=$this->db->query($sql);
				  
				    
		 //echo $this->db->last_query();
		 // die();
		  
				  if($rs->num_rows()>0)
				  {
					  $role_array=$rs->result_array();
					 
					  foreach($role_array as $item)
					  {
						  $status_arr[]= array(
										   "id" => $item['role'], 
										   "name" => $item['name'],
										   "role_name" => $item['role_name'],
										   "order_status_id" => $item['order_status_id'],
										   "status_text" => $item['order_status_text'],
										   );
									   
						 
					  }
				  }
		    
	  }
	  else
	  {
		  $role_id=$this->Basic_model->sub_unique_id;
		  //$role_id=1;
		  $sql="Select *,order_status_master.id as 'order_status_id' from sub_role_details INNER JOIN role_master ON sub_role_details.role_master_id=role_master.id 
		  INNER JOIN order_status_master ON sub_role_details.id=order_status_master.role INNER JOIN status_master ON order_status_master.status_text=status_master.id 
		  Where role_master.object_id=$client_id AND status_master.is_active=1 And
		  sub_role_details.is_active=1 and role_master.is_active=1 And status_master.client_id=$client_id and sub_role_details.id=$role_id";
		   
		   //$sql="Select * from sub_role_details order by id asc";
		  
		  $rs=$this->db->query($sql);
		  $self_array=$rs->row_array();
	
		  $status_arr2=array(
						   "id" => $self_array['role'], 
						   "name" => $self_array['name'],
						   "role_name" => "Self",
						   "order_status_id" => $self_array['order_status_id'],
						   "status_text" => $self_array['order_status_text'],
						   );
		  
		  $status_arr=$this->roleParentChildTree($role_id);
		  
		  array_unshift($status_arr,$status_arr2);
	  }
	
	  return $status_arr;
  }
 public function GetMasterOrderStatus()
 {
	 $order_status_details=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->where('is_active',1)->get('status_master');
	 if($rs->num_rows()>0)
	 {
		  $order_status_details=$rs->result_array();
	 }
	 
	 return $order_status_details;
 }
 
 public function GetMasterStatusDetails()
 {
	 $order_status_details=array();
	 $rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('status_master');
	 if($rs->num_rows()>0)
	 {
		  $order_status_details=$rs->result_array();
	 }
	 
	 return $order_status_details;
 }
  
 public function GetOrderStatusDetails()
 {
	 $order_status_details=array();
	 $client_id=$this->Basic_model->unique_id;
	 $status_details=array();
	 
	 $sql="Select * from status_master INNER JOIN  order_status_master ON status_master.id=order_status_master.status_text  
	       Where status_master.client_id=$client_id";
	 
	 //$rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->get('order_status_master');
	 $rs=$this->db->query($sql);
	 if($rs->num_rows()>0)
	 {
		  
		  $order_status_details=$rs->result_array();
		  if(!empty($order_status_details))
		  {
			   foreach($order_status_details as $item)
			   {
				   $role_name="";
				   $role_name=$this->Common_model->get_single_field_value('sub_role_details','name','id',$item['role']);
				   
				   $status_details[]=array(
				     'id'=>$item['id'],
				     'role_name'=>$role_name,
				     'status'=>$item['order_status_text'],
					 'is_changeable'=>$item['is_changeable'],
					 'is_active'=>$item['is_active'],
				   );
			   }
		  }
		  
	 }
	 
	 return $status_details;
 }
 
 
  public function GetOrderStatusDetailsOnId($id)
 {
	 $order_status_details=array();
	 $rs=$this->db->select('*')->where('id',$id)->get('order_status_master');
	 
	 if($rs->num_rows()>0)
	 {
		  $order_status_details=$rs->row_array();
	 }
	 
	 return $order_status_details;
 }
 
 public function GetMasterOrderStatusDetailsOnId($status_id)
 {
	 $master_status_details=array();
	 $rs=$this->db->select('*')->where('id',$status_id)->get('status_master');
	 if($rs->num_rows()>0)
	 {
		  $master_status_details=$rs->row_array();
	 }
	 
	 return $master_status_details;
 }
  
 public function InsertMasterOrderStatus($post_data)
 {
	$error='';
	$errortext='';
	$result='';

	$status=$post_data['status'];
	$client_id=$this->Basic_model->unique_id;
	$rs=$this->db->select('*')->where('client_id',$client_id)->where('order_status_text',$status)->get('status_master');
	if($rs->num_rows()>0)
	{
		$error=true;
		$errortext='Status already exist';
	}
	
	if(!$error)
	{
		 if($post_data['is_changeable']==true)
		 {
			 $is_changeable=1;
		 }
		 else
		 {
			 $is_changeable=0;
		 }
		 
		 
		 if($post_data['is_active']==true)
		 {
			 $is_active=1;
		 }
		 else
		 {
			 $is_active=0;
		 }
		
		
		  $insert_arr=array
		   (
			 'client_id'=>$client_id,
			 'order_status_text'=>$status,
			 'is_changeable'=>$is_changeable,
			 'is_active'=>$is_active,
		   );
		   
		   $success=$this->db->insert('status_master',$insert_arr);
		   if($success)
		   {
		    $result['successMessage']="Data successfully inserted";
		   }
		   else
		   {
			   $error=true;
	           $errortext='Insert error';
		   }
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	
 }
 
 
 
 public function InsertOrderStatus($post_data)
 {
	$error='';
	$errortext='';
	$result='';
	
	$role=$post_data['employee_id'];
	$status=$post_data['status_id'];
	$client_id=$this->Basic_model->unique_id;
	
	$rs=$this->db->select('*')->where('id',$status)->get('status_master');
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Status not found';
	}
	
	$rs=$this->db->select('*')->where('id',$role)->get('sub_role_details');
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Role not found';
	}
	
	
	if(!$error)
	{ 
		$sql="Select * from status_master INNER JOIN order_status_master ON status_master.id=order_status_master.status_text  
		Where status_master.client_id=$client_id And order_status_master.role=$role";
		
		$rs=$this->db->query($sql);
		if($rs->num_rows()>0)
		 {
			$error=true;
			$errortext='Status already exist';
		 }
	}
	 
	 
	 if(!$error)
	 {
	   $insert_arr=array
	   (
		 'role'=>$role,
		 'status_text'=>$status,
	   );
	   
	   $success=$this->db->insert('order_status_master',$insert_arr);
	   if($success)
	   {
		$result['successMessage']="Data successfully inserted";
	   }
	   else
	   {
		   $error=true;
		   $errortext='Insert error';
	   }
	 }
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);   
 }
  
  
 public function UpdateMasterOrderStatus($post_data)
 {
	$error='';
	$errortext='';
	$result='';

	$status=$post_data['status'];
	$status_id=$post_data['status_id'];
	$client_id=$this->Basic_model->unique_id;
	
	$rs=$this->db->select('*')->where('client_id',$client_id)->where('id',$status_id)->get('status_master');
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Invalid status id';
	}
	
	if(!$error)
	{
		$rs=$this->db->select('*')->where('id!=',$status_id)->where('client_id',$client_id)->where('order_status_text',$status)->get('status_master');
		if($rs->num_rows()>0)
		{
			$error=true;
			$errortext='Status already exist';
		}
	}
	
	if(!$error)
	{
		
		if($post_data['is_changeable']==true)
		 {
			 $is_changeable=1;
		 }
		 else
		 {
			 $is_changeable=0;
		 }
		 
		 
		 if($post_data['is_active']==true)
		 {
			 $is_active=1;
		 }
		 else
		 {
			 $is_active=0;
		 }
		
		
		  $insert_arr=array
		   (
			 'order_status_text'=>$status,
			 'is_changeable'=>$is_changeable,
			 'is_active'=>$is_active,
		   );
		   
		   $success=$this->db->where('id',$status_id)->update('status_master',$insert_arr);
		   if($success)
		   {
		    $result['successMessage']="Data successfully updated";
		   }
		   else
		   {
			   $error=true;
	           $errortext='Insert error';
		   }
	}
	
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);
	
 }
  
  public function UpdateOrderStatus($post_data)
  {
	
	$error='';
	$errortext='';
	$result='';
	
	$role=$post_data['employee_id'];
	$status=$post_data['status_id'];
	$status_id=$post_data['order_status_id']; 
	$client_id=$this->Basic_model->unique_id;
	
	$rs=$this->db->select('*')->where('id',$status_id)->get('order_status_master');
	if(!$rs->num_rows()>0)
	{
		$error=true;
		$errortext='Order status not found';
	}
	
	
	if(!$error)
	{
		$rs=$this->db->select('*')->where('id',$status)->get('status_master');
		if(!$rs->num_rows()>0)
		{
			$error=true;
			$errortext='Status not found';
		}
		
		$rs=$this->db->select('*')->where('id',$role)->get('sub_role_details');
		if(!$rs->num_rows()>0)
		{
			$error=true;
			$errortext='Role not found';
		}
	}
	
	if(!$error)
	{
		$sql="Select * from status_master INNER JOIN order_status_master ON status_master.id=order_status_master.status_text  
		Where status_master.client_id=$client_id And order_status_master.role=$role And order_status_master.id!=$status_id";
				
		$rs=$this->db->query($sql);
		if($rs->num_rows()>0)
		 {
			$error=true;
			$errortext='Status already exist';
		 }
	}
	 
	 
	 if(!$error)
	 {
	   $insert_arr=array
	   (
		 'role'=>$role,
		 'status_text'=>$status,
	   );
	   
	   $success=$this->db->where('id',$status_id)->update('order_status_master',$insert_arr);
	   if($success)
	   {
		$result['successMessage']="Data successfully updated";
	   }
	   else
	   {
		   $error=true;
		   $errortext='Insert error';
	   }
		   
	 }
	  
	return array('error' => $error, 'errortext' => $errortext, 'result' => $result);   
   
  }
  
  
  public function GetSubmitOrderStatus($order_id)
  {
	  $order_status=array();
	  $client_id=$this->Basic_model->unique_id;
	  $sql="Select * from product_order INNER JOIN order_status_master ON product_order.order_status=order_status_master.id
	  		INNER JOIN status_master ON order_status_master.status_text=status_master.id Where product_order.id=$order_id And status_master.client_id=$client_id";
			
	  $rs=$this->db->query($sql);
	  if($rs->num_rows()>0)
	  {
		  $order_status=$rs->row_array();
	  }
	  
	  return $order_status;  		
  }
  
  
  public function UpdateProductOrderStatus($post_data,$order_id)
  {
	  
	$customer_id=$post_data['customer_id'];
	$subtotal=$post_data['subtotal'];
	$product_id=$post_data['product_id'];
	$product_rate=$post_data['product_rate'];
	$product_qty=$post_data['qty'];
	$product_amount=$post_data['product_amount'];
	$charges_total=$post_data['total_additional_charges'];
	$grand_total=$post_data['grand_total'];
	
	$charges_id=array();
	if(!empty($post_data['charges_id']))
	{
	 $charges_id=$post_data['charges_id'];
	}
	
	$charges_amount=array();
	if(!empty($post_data['charges_id']))
	{
	 $charges_amount=$post_data['charges_amount'];
	}
	
	    $note=$post_data['note'];
		$order_status=$post_data['status_id'];
		$sku_arr=$post_data['sku'];
		$avl_qty=$post_data['avl_qty'];

	   $success=0;
	   $order_status=$post_data['status_id'];
		$order_array=array(
		  'order_total'=>$subtotal,
		  'order_status'=>$order_status,
		  'note'=>$note
		);
	
	   $this->db->where('id',$order_id)->update('product_order',$order_array);
	   
	   $order_status_log_array=array(
		  'client_id'=>$this->Basic_model->unique_id,
		  'order_id'=>$order_id,
		  'role_id'=>$this->Basic_model->sub_unique_id,
		  'order_status_id'=>$order_status,
		);
		
		//echo '<pre>',print_r($order_array);
		$this->db->insert('order_status_log',$order_status_log_array);
		
		
		$rs=$this->db->select('*')->where('order_id',$order_id)->get('order_details');
	    if($rs->num_rows()>0)
		{
			$order_details=$rs->result_array();
			foreach($order_details as $item)
			{
				$avl_qty="";
				$sku_no=$this->Common_model->get_single_field_value('products','sku','id',$item['product_id']);
				
				$rs=$this->db->select('*')->where('client_id',$this->Basic_model->unique_id)->where('sku',$sku_no)->get('master_stock');
				if($rs->num_rows()>0)
				{
					$stock_arr=$rs->row_array();
					$avl_qty=$stock_arr['avl_qty'];
				}
				
				$new_qty=$avl_qty+$item['qty'];
				
		
				$update_arr=array(
				 'avl_qty'=>$new_qty
				);
				
				$success=$this->db->where('client_id',$this->Basic_model->unique_id)->where('sku',$sku_no)
				->update('master_stock',$update_arr);
			}
			
		}
		
		$this->db->select('*')->where('order_id',$order_id)->delete('order_details');
		
		$product_count=count($product_id);
		for($i=0;$i<=$product_count-1;$i++)
		{
			 $new_qty=0;
			 $sku_no="";
			 $avl_qty="";
			 $order_details_array=array(
			  'order_id'=>$order_id,
			  'product_id'=>$product_id[$i],
			  'qty'=>$product_qty[$i],
			  'rate'=>$product_rate[$i],
			  'total_rate'=>$product_amount[$i],
			 );
			 
			 //echo '<pre>',print_r($order_details_array);
			$this->db->insert('order_details',$order_details_array);
			
		    $sku_no=$sku_arr[$i];
			$client_id=$this->Basic_model->unique_id;
			$sql="SELECT * FROM master_stock WHERE client_id = $client_id AND sku = '$sku_no'";
			$rs=$this->db->query($sql);
			
			//echo $this->db->last_query();
			//die();
			if($rs->num_rows()>0)
			{
				$stock_arr=$rs->row_array();
				$avl_qty=$stock_arr['avl_qty'];
			}
			
			$new_qty=$avl_qty-$product_qty[$i];
			
			$update_arr=array(
			 'avl_qty'=>$new_qty
			);
			
			$success=$this->db->where('client_id',$this->Basic_model->unique_id)->where('sku',$sku_no)
					->update('master_stock',$update_arr);
			
		}
		
		$invoice_id=$this->Common_model->get_single_field_value('invoice','id','order_id',$order_id);
		
		$invoice_array=array(
		  'sub_total'=>$subtotal,
		  'charges_total'=>$charges_total,
		  'grand_total'=>$grand_total,
		 );
		 
		 //echo '<pre>',print_r($invoice_array);
	    $this->db->where('id',$invoice_id)->update('invoice',$invoice_array);
		
		$this->db->select('*')->where('invoice_id',$invoice_id)->delete('invoice_charges');
		
		if(!empty($charges_id))
		 {
		   $charges_count=count($charges_id);
			for($i=0;$i<=$charges_count-1;$i++)
			{
				 $charges_array=array(
				  'invoice_id'=>$invoice_id,
				  'charges_id'=>$charges_id[$i],
				  'amount'=>$charges_amount[$i],
				 );
				 
				 //echo '<pre>',print_r($charges_array);
				$this->db->insert('invoice_charges',$charges_array);
			}
		 }
		
		$success=$this->cart->destroy();
		
		if($success)
		{
			$success=1;
		}
		
		return $success;
  }
  
  
  public function GetMonthlyReport($input_array)
  {
	  $client_id=$this->Basic_model->unique_id;
	  $report_details=array();
	  $account_id=$input_array['account_id'];
	  $start_date=date("Y-m-d",strtotime($input_array['start_date']));
	  $end_date=date("Y-m-d", strtotime($input_array['end_date']));
	 
	  $start_date_time=$start_date." 00:00:00.000";
	  $end_date_time=$end_date." 23:59:59.997";
	  
	  $sql="Select MONTH(invoice.date_time) As 'current_month',YEAR(invoice.date_time) As 'current_year' FROM invoice INNER JOIN
	  		account ON invoice.customer_id=account.id WHERE (invoice.date_time BETWEEN '$start_date_time' AND '$end_date_time')
			AND invoice.customer_id=$account_id AND invoice.client_id=$client_id GROUP BY MONTH(invoice.date_time)
			ORDER BY MONTH(invoice.date_time) desc ";
			
	  $rs=$this->db->query($sql);	
	  $result_month=$rs->result_array();
	 
	  $total_value=0;
	  foreach($result_month as $item)
	  {
		  $result_start_date='';
		  $result_end_date='';
		  $result_start_date=$item['current_year'].'-'.$item['current_month'].'-01';
		  $result_end_date=date('Y-m-t', strtotime($result_start_date));

		  $invoice_start_date=date("Y-m-d",strtotime($result_start_date));
	 	  $invoice_end_date=$result_end_date;
		 
		  $invoice_start_time=$invoice_start_date." 00:00:00.000";
		  $invoice_end_date_time=$invoice_end_date." 23:59:59.997";
		  
		 $sql="Select products.product_name,products.sku,products.product_price,SUM(order_details.qty) as 'total_qty',
		      SUM(order_details.total_rate) as 'total_price' 
		  from invoice INNER JOIN order_details ON invoice.order_id=order_details.order_id INNER JOIN products ON 
		  order_details.product_id=products.id WHERE (invoice.date_time BETWEEN '$invoice_start_time' AND 
		  '$invoice_end_date_time') AND invoice.client_id=$client_id AND invoice.customer_id=$account_id GROUP BY products.product_name ORDER BY 
		  products.product_name asc";
		  
		 /* $sql="Select *
		  from invoice INNER JOIN order_details ON invoice.order_id=order_details.order_id INNER JOIN products ON 
		  order_details.product_id=products.id WHERE (invoice.date_time BETWEEN '$invoice_start_time' AND 
		  '$invoice_end_date_time') AND invoice.client_id=$client_id AND invoice.customer_id=$account_id";*/

		  $rs2=$this->db->query($sql);	
	      $order_details=$rs2->result_array();
		  
		  // echo $this->db->last_query();
		  
		  
		  //echo '<pre>',print_r($order_details);
	      //die();
		  
		  $total_product=0;
		  foreach($order_details as $row)
		  {
			   $total_product=$total_product+$row['total_price'];
		  }
		  $total_value=$total_value+$total_product;
		  
		  $report_details[]=array(
		   'month'=>date("F", mktime(0, 0, 0, $item['current_month'], 10)),
		   'product_details'=>$order_details
		  );
		
	  }
	  
	 $report_details=array('total_value'=>$total_value,'report_details'=>$report_details);
	 
	 return $report_details;
	  
  }
  
   public function GetLastFinancialYearReportData($account_id)
  {
	  $start_date='';
	  $end_date='';
	  $current_date=date("Y-m-d");
	  $current_year=date("Y");
	  
	  $start_of_last_financial_year=($current_year-1)."-04-01";
	  $end_of_last_financial_year=($current_year)."-03-31";
	  
	  $input_array['account_id']=$account_id;
	  $input_array['start_date']=$start_of_last_financial_year;
	  $input_array['end_date']=$end_of_last_financial_year;
	  
	  $current_year_report=$this->GetMonthlyReport($input_array);
	  
	  
	  //echo '<pre>',print_r($current_year_report);
	  //die();
	  
	  return $current_year_report;
	 
	  
  }
  
  public function GetCurrentFinancialYearReportData($account_id)
  {
	  $start_date='';
	  $end_date='';
	  $current_date=date("Y-m-d");
	  $current_year=date("Y");
	  $start_of_current_financial_year=$current_year."-04-01";
	  
	  if($this->input->get('start_date'))
	  {
		  $start_date=$this->input->get('start_date'); 
	  }
	  else
	  {
		  $start_date=$start_of_current_financial_year;
	  }
	  
	  
	  if($this->input->get('end_date'))
	  {
		  $end_date=$this->input->get('end_date'); 
	  }
	  else
	  {
		  $end_date=$current_date;
	  }
	  
	  $start_of_last_financial_year=($current_year-1)."-04-01";
	  $end_of_last_financial_year=($current_year)."-03-31";
	  
	  $input_array['account_id']=$account_id;
	  $input_array['start_date']=$start_date;
	  $input_array['end_date']=$end_date;
	  
	  $current_year_report=$this->GetMonthlyReport($input_array);
	  
	  $current_year_report['start_date']=$start_date;
	  $current_year_report['end_date']=$end_date;
	  //echo '<pre>',print_r($current_year_report);
	  //die();
	  
	  return $current_year_report;
	 
	  
  }

}
?>