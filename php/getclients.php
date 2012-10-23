<?php
/************************************************************************
* @Author: 
************************************************************************/
if(isset($glob['pid']))
{
	$glo = array();
	$dbu = new mysql_db();
	$client = $dbu->query("SELECT client_id, first_name, surname, email
				 FROM client
				 WHERE trainer_id = {$_SESSION[U_ID]}
				 ORDER BY surname
				");
	
	while($client->next())
	{
        //$custom_plan = $dbu->field('SELECT exercise_program_plan_id
        //                            FROM exercise_program_plan 
        //                            WHERE trainer_id = '.$_SESSION[U_ID].'
        //                                AND client_id = '.$client->f('client_id'));
        
        $pid = $custom_plan ? $custom_plan : $glob['pid'];
		$client_list .= '<a href="index.php?pag=program_add_patient&program_id='.$pid.'&client_id='.$client->f('client_id').'">'.$client->f('surname').', '.$client->f('first_name').'</a><br/>';
	}
	
	return $client_list;
}
?>