<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
header('Accept-Charset: *');
include_once("module_config.php");
include_once("php/gen/startup.php");
include_once("misc/json.php");
if(!$debug){
	error_reporting(0);
}
session_register(UID);
if(isset($_POST['test']))
{
	$dbu = new mysql_db();
	$dbu->query("select trainer.* from trainer where trainer.trainer_id=".$_SESSION[U_ID]." ");
	$dbu->move_next();
	$profile_id = $dbu->f('trainer_id');
	var_dump($profile_id);exit;
	if(!$profile_id) $profile_id = 'error';
	print_r($profile_id);exit;
}
$user_level = $_SESSION[ACCESS_LEVEL] ? $_SESSION[ACCESS_LEVEL] : 4;
$glob['failure'] = false;
//var_dump($glob);exit;
if($glob['act'] && !$glob['skip_action'])
{
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
            	$glob['failure'] = true;
            }
            unset($cls_name);
            unset($func_name);
        }
        else
        {
        	$glob['error']= "You are not allowed to run this function1 !"; 
            $glob['failure'] = true;
        }
    }else{
    	$glob['error']= "You are not allowed to run this function2 !"; 
    	$glob['failure'] = true;
    }
}

if(!$glob['failure']){
	if($glob['pag']){
	    if($page_access[$glob['pag']]['perm'] && $page_access[$glob['pag']]['perm'] >= $user_level)
	    {
	    	$page = include("php/".$glob['pag'].".php");
	    	$glob['innerHTML'] = $page;
//var_dump($glob);exit;
	    }
	    else
	    {
	    	$glob['innerHTML'] = '';
	    	$glob['failure'] = true;
	    	$glob['error']= "You are not allowed to run this function3 !"; 
	    }
	}elseif(!$glob['act']){
    	$glob['failure'] = true;
    	$glob['error']= "You are not allowed to run this function4 !"; 
	}
}
header('Content-type:text/plain');
$json = new Services_JSON();

echo $json->encode($glob);