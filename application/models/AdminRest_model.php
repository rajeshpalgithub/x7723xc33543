<?php
class AdminRest_model extends CI_Model
{
	function class_list()
	{
		$classArray=array();
		$sql="SELECT * FROM module";
	 	$rs=$this->db->query($sql);
		 if($rs->num_rows()>0)
		 {
			 $classArray=$rs->row_array();
			 
		 }
	 
	 	return $classArray;
	}
}

?>