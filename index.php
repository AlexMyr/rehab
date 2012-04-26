<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
session_start();
include_once("module_config.php");
include_once("php/gen/startup.php");

if(!$debug)
{
	error_reporting(0);
}
else
{
	error_reporting(E_ALL & ~E_NOTICE);
}

$user_level=4;
if(!$glob['pag'])
{
	$glob['pag']='cms';
}

/* the session */
if($page_access[$glob['pag']]['session'])
{
	session_register(UID);
}
if($_SESSION[UID])
{
	$user_level=$_SESSION[ACCESS_LEVEL];
}
else
{
	$user_level=4;
}
if($_SESSION[U_ID])
{
//	check_ip($_SERVER['REMOTE_ADDR'],$_SESSION[U_ID]);
	if(!$_SESSION[U_ID]) {
		$glob['pag'] = 'cms';
	}
}


$exercise_session_pages = array("client_add_exercise","client_update_exercise","program_update_exercise");

if(!empty($_SESSION['pids']) && !in_array($glob['pag'],$exercise_session_pages)) unset($_SESSION['pids']);
$glob['success']=false;

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
            else
            {
            	$glob['success']=true;
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
            $glob['pag']= "cms"; 
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


//var_dump($glob['pag']);exit;
include_once("php/gen/page_perm.php");



if($glob['pag'])
{
    if($page_access[$glob['pag']]['perm'] && $page_access[$glob['pag']]['perm'] >= $user_level)
	    {
	    	if($glob['pag'] == 'profile' && !isset($glob['redir']) && !isset($_GET['pag']))
			{
				$glob['redir'] = 'index.php?pag=dashboard&redirect=1';
				include("php/redirect.php");
				unset($glob['redir']);
				exit;
			}
			else
	    		$page=include("php/".$glob['pag'].".php");
	    }
    else
	    {
	    	$page=include("php/login.php");
	    }
}

if($site_module[$page_access[$glob['pag']]['module']])
{
	$template_file=$site_module[$page_access[$glob['pag']]['module']]['template_file'];
	$current_module=$page_access[$glob['pag']]['module'];
	
	$dbu = new mysql_db();
	$dbu->query("select * from ".$current_module."_template_czone");
	while($dbu->move_next())
	{
		$template_tags[$dbu->f('template_czone_id')]=$dbu->f('tag');
		$template_content[$dbu->f('template_czone_id')]=$dbu->f('content');
	}
	
}
else 
{
    $template_file='main_template.html';
}

$ftm=new ft("");
$ftm->define(array('main'=>$template_file));

if($template_tags)
foreach ($template_tags as $template_czone_id => $template_czone_tag)
{
	$tag_content=$template_content[$template_czone_id];
	//get tags from content
	$cms_tag_array=get_cms_tags_from_content($tag_content);
	//****Replacing the CMS tags with objects
	
	if($cms_tag_array)
	foreach ( $cms_tag_array as $key => $cms_tag_params)
	{
		$tag_content=str_replace($cms_tag_params['tag'], get_cms_tag_content($cms_tag_params), $tag_content);
	}
	
	$ftm->assign($template_czone_tag, $tag_content);
}

$ftm->assign('META_TITLE',$site_meta_title);
$ftm->assign('META_KEYWORDS',$site_meta_keywords);
$ftm->assign('META_DESCRIPTION',$site_meta_description);

$ftm->assign('PAGE',$page);
$ftm->assign('BOTTOM_INCLUDES',$bottom_includes);
$ftm->parse('CONTENT','main');
$ftm->ft_print('CONTENT');

if($debug)
{
   require($script_path."misc/debug.php");
}

?>