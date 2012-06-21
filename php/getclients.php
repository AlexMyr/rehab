<?php
/************************************************************************
* @Author: 
************************************************************************/
if(isset($glob['pid']))
{
	$glo = array();
	$dbu = new mysql_db();
	$dbu->query("SELECT client_id, first_name, surname, email
				 FROM client
				 WHERE trainer_id = {$_SESSION[U_ID]}
				 ORDER BY surname
				");
	
	while($dbu->move_next())
	{
		$client_list .= '<a href="index.php?pag=program_add_patient&program_id='.$glob['pid'].'&client_id='.$dbu->f('client_id').'">'.$dbu->f('surname').', '.$dbu->f('first_name').'</a><br/>';
	}
	
	return $client_list;
}
?>