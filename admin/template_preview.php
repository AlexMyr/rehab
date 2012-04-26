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

$dbu=new mysql_db;
$dbu->query("select file_name from cms_template where template_id='".$glob['template_id']."'");
$dbu->move_next();
$file_name=$dbu->f('file_name');

$ftm=new FastTemplate("../");
$ftm->define(array('main'=>$file_name));
$dbu->query("select * from cms_template_czone where template_id='".$glob['template_id']."'");
while($dbu->move_next())
{
	$ftm->assign($dbu->f('tag'),$dbu->f('name')." - ".$dbu->f('description'));
}
$ftm->parse('CONTENT','main');
$ftm->fastprint('CONTENT');

?>