<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

include_once("module_config.php");
include_once("php/gen/startup.php");
if(!$debug)
{
	error_reporting(0);
}

$user_level=4;
if(!$glob['pag'])
{
	$glob['pag']='tell_friend';
}


if($glob['act'] && !$glob['skip_action'])
{
	include_once("php/gen/func_perm.php");
	list($cls_name,$func_name )=split("-",$glob['act']);
	if(($cls_name)&&($func_name)&&(is_file("classes/cls_".$cls_name.".php"))&&($func_access[$cls_name][$func_name]))
	{
    	if($user_level<=$func_access[$cls_name][$func_name])
        {
        	include_once("classes/cls_".$cls_name.".php");
            $cls_name= new $cls_name;
            if (!$cls_name->$func_name($glob))
            {
            	if($debug)
            	{
            		$glob['error'].="Failed to execute function $func_name";
            	}
            }
            unset($cls_name);
            unset($func_name);
        }
        else
        {
            if($debug)
            {
        		$glob['error']= "You are not allowed to run this function !"; 
            }
            $glob['pag']= "browse"; 
        }
    }
    else
    {
    	 if($debug)
            {
            	 echo "Can not find cls_".$cls_name.".php file<BR>"; 
            }  	
    }
}


include_once("php/gen/page_perm.php");



if($glob['pag'])
{
	
    if($page_access[$glob['pag']]['perm'] && $page_access[$glob['pag']]['perm'] >= $user_level)
    {
    	
    	$page=include("php/".$glob['pag'].".php");
    }
    else
    {
    	$page=include("php/login.php");
    }
}

//$menu=include("php/gen/menu.php");



$ftm=new FastTemplate(ADMIN_PATH.MODULE."templates/");
$ftm->define(array('main'=>"index.html"));
$ftm->assign('PAGE',$page);
$ftm->parse('CONTENT','main');
$ftm->fastprint('CONTENT');

?>