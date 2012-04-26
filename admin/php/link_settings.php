<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array(main => "link_settings.html"));

    $next_function='link_settings-update';

	//Get Link Settings
    $dbu->query("select * from settings where type='1' and module='link'");
    while($dbu->move_next())
    {
   		$ft->assign( $dbu->f('constant_name'), $dbu->f('value'));
    }
    
    $dbu->query("select * from settings where type='2' and module='link'");
    while($dbu->move_next())
    {
   		$ft->assign( $dbu->f('constant_name'),  build_yesno_list($dbu->f('value')));
    }
 
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>