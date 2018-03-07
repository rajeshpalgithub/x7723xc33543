<?php
// this model contain School_class, School_section table relate queries
class School_class_db_model extends CI_Model
{

	function __construct(){

	}

	public function getClass_query(int $clientId)
	{
		$class=array();
		$sql = "SELECT * FROM school_class WHERE client_id=$clientId";
		$rs=$this->db->query($sql);
		if($rs->num_rows()>0)
	 	{
		 	$class=$rs->row_array();
	 	}
	 	return $class;

	}

	public function editClass_query(int $clientId, int $classId )
	{

	}
	public function insertClass_query()
	{

	}
	
}

?>