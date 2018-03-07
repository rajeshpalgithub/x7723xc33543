<?php

	function _construct()
	{

	}
	function getAllClass()
	{
		$classes=array();
		$client_id=$this->Basic_model->unique_id;
		$classes_array  = $this->School_class_db_model->getClass_query($client_id);
		if(!empty($classes_array))
		{
			foreach($classes_array  as $class)
			{
				$classes[]=array('id'=>$class['class_id'],'name'=>$class['class_name']);
			}
		}
		return $classes;
	}

?>