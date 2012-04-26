<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array(main => "admin_settings.html"));

    $next_function='settings-admin_update';


    $dbu->query("select * from settings where type=1");
    while($dbu->move_next())
    {
   		$ft->assign( $dbu->f('constant_name'), $dbu->f('value'));
    }

    $dbu->query("select * from settings where type=2");
    while($dbu->move_next())
    {
   		$ft->assign( $dbu->f('constant_name'), $dbu->f('long_value'));
    }
    
    
    $dbu->query("select username, email from user where user_id='".$_SESSION[U_ID]."'");
    $dbu->move_next();

    $ft->assign( "USERNAME", $dbu->f('username'));
    $ft->assign( "EMAIL", $dbu->f('email'));
    

 
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>