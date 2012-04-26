<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/

if(!$_SESSION[UID])
{
$ft=new ft(ADMIN_PATH.MODULE."templates/");
    $ft->define( array(main => "login.html"));

    if($glob["error"]) 
    	$ft->assign("ERROR",$glob["error"]);
    else 
    	$ft->assign("ERROR","");
    $ft->parse('mainContent', 'main');
    return $ft->fetch('mainContent');
}

?>
