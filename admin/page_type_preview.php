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
$dbu->query("select cms_template.file_name from cms_page_type
			 inner join cms_template on cms_page_type.template_id=cms_template.template_id
			 where page_type_id='".$glob['page_type_id']."'");
$dbu->move_next();
$file_name=$dbu->f('file_name');

$ftm=new FastTemplate("../");
$ftm->define(array('main'=>$file_name));
$dbu->query("select cms_page_type_czone.*, cms_template_czone.*
			 from cms_page_type_czone
			 inner join cms_template_czone on cms_page_type_czone.template_czone_id=cms_template_czone.template_czone_id 
			 where page_type_id='".$glob['page_type_id']."'");
while($dbu->move_next())
{
	$tag = $dbu->f('tag');
	if($dbu->f('default_data'))
	{
		$data = $dbu->f('default_data');
	}
	else
	{
		$data = $dbu->f('name')." - ".$dbu->f('description');
	}
	
	$ftm->assign($tag,$data);
}

$ftm->parse('CONTENT','main');
$ftm->fastprint('CONTENT');

?>