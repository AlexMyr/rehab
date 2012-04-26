<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
if($page_access[$glob['pag']]['menu']) 
{
	$user_level=$_SESSION['user_level'];
	if($user_level==1)
	{
		$menu_name="1_";
	}
	if($user_level==2)
	{
		$menu_name="2_";
	}
	if ($user_level <= $menu_access[$page_access[$glob['pag']]['menu']])
	{
		$menu_name.=$page_access[$glob['pag']]['menu'];
		$menu_simple_name=$page_access[$glob['pag']]['menu'];
	}
	else 
	{
		foreach($menu_access as $menu_name => $access)
		{
			if($user_level <= $access)
			{
				break;
			}
		}
	}
}
if(!$menu_name)
{
	return "&nbsp;";
}
//echo $menu_name."_menu.html";
$dbu=new mysql_db();
$dbu->query("select username, password from user where user_id='".$_SESSION[U_ID]."'");
$dbu->move_next();
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => $menu_name."_menu.html"));

foreach($page_access as $page_name => $page_opts)
{
	if($page_opts['menu']==$menu_simple_name)
	{
		$tag_name=$page_opts['tag'];
		if ($tag_name)
		   $ft->assign('CLASS_'.$tag_name, 'MenuTitle');
		//echo $page_opts['tag'];
	}
}

$tag_name=$page_access[$glob['pag']]['tag'];
if ($tag_name)
{
	$ft->assign('CLASS_'.$tag_name, 'MenuTitleSelected');
}

$ft->assign("RESERVATION_LINK","../index.php?act=auth-login&username=".$dbu->f('username')."&pass=".$dbu->f('password'));
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>