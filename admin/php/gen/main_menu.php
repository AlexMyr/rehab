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

$ft=new ft(ADMIN_PATH."admin/templates/");
$ft->define(array('main' => "main_menu.html"));
$class1="TopMenuLink";
$class2="TopMenuLink";
$class3="TopMenuLink";
$class4="TopMenuLink";
$class5="TopMenuLink";
$class6="TopMenuLink";
$class7="TopMenuLink";
$class8="TopMenuLink";
$class9="TopMenuLink";
$class10="TopMenuLink";

switch($menu_name)
{

	case 'cms': { $class1="TopMenuLinkSelected"; } break;
	case 'programs': { $class2="TopMenuLinkSelected"; } break;
	case 'faq': { $class3="TopMenuLinkSelected"; } break;	 
	case 'member': { $class4="TopMenuLinkSelected"; } break;
	case 'statistic': { $class5="TopMenuLinkSelected"; } break;

}

$ft->assign( "CLASS1", $class1);
$ft->assign( "CLASS2", $class2);
$ft->assign( "CLASS3", $class3);
$ft->assign( "CLASS4", $class4);
$ft->assign( "CLASS5", $class5);
/*
$ft->assign( "CLASS5", $class5);
$ft->assign( "CLASS6", $class6);
$ft->assign( "CLASS7", $class7);
$ft->assign( "CLASS8", $class8);
$ft->assign( "CLASS9", $class9);
$ft->assign( "CLASS10", $class10);
*/
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>