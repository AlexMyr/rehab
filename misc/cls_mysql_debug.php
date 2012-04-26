<?php
class mysql_debug{
	var $sql = array();
	
	function save($sql){
		if($sql != ''){
			$this->sql[] = $sql;
			return true;
		}
		return false;
	}
	
	function display(){
		return $this->sql;
	}
}

function &get_debug_instance(){
	static $DEBUGER;
	if(is_object($DEBUGER)){	
		return $DEBUGER;	
	}
	$DEBUGER = new mysql_debug();
	return $DEBUGER;
}
?>