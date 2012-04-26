<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
if($page_access[$glob['pag']]['menu']) 
{ 
	if ($user_level <= $menu_access[$page_access[$glob['pag']]['menu']])
	{
		$menu_name=$page_access[$glob['pag']]['menu'];
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

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => $menu_name."_menu.html"));


$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>