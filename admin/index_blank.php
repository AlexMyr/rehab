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


if($_SESSION['mid'])
{
	session_destroy();
}
if($_SESSION[UID])
{
	$user_level=$_SESSION[ACCESS_LEVEL];
    if(!$glob['pag'])
    {
        $glob['pag']='welcome';
    }
    if($glob['pag']=='login')
    {
        $glob['pag']='welcome';
    }
	
}
else
{
$user_level=4;
	if(!$glob['pag'])
	{
		$glob['pag']='login';
	}
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
            if (! $cls_name->$func_name($glob))
            {
            	//$glob['error'].="Failed to execute function $func_name";
            }
            unset($cls_name);
            unset($func_name);
        }
        else
        {
            //$glob['error']= "You are not allowed to run this function !"; 
        }
    }
    else
    {
    	 //echo "Can not find cls_".$cls_name.".php file<BR>";   	
    }
}



include_once("php/gen/page_perm.php");

//$menu=include("php/gen/menu.php");

if($glob['pag'])
{
    if($page_access[$glob['pag']]['perm'] && $page_access[$glob['pag']]['perm'] >= $user_level)
    {
    	$page=include("php/".$glob['pag'].".php");
    	
    }
    else
    {
    	//echo "You are not allowed to view this page";
    	//print_r($page_access);
    	$page="&nbsp;";
    }
}


$ftm=new FastTemplate("templates");
$ftm->define(array('main'=>"index_blank.html"));
$ftm->assign('PAGE',$page);
$ftm->assign('SITE_NAME',$site_name);
$ftm->parse('CONTENT','main');
$ftm->fastprint('CONTENT');
/*
if($debug)
{
   require($script_path."misc/debug.php");
}
*/
?>