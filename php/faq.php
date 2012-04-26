<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
$site_meta_title=$meta_title;
$site_meta_keywords=$meta_keywords;
$site_meta_description=$meta_description;

$dbu=new mysql_db;

if(!is_numeric($glob['id']))
{
	$file='faq_main_categories';
}
else 
{
	$dbu->query("select faq_category_id from faq_category_subcategory where parent_id='".$glob['id']."' and faq_category_id != '".$glob['id']."'");
	if($dbu->move_next())
	{
		$file='faq_subcategories';
	}
	else 
	{
		$file='faqs';
	}
}
$page=include("php/".$file.".php");
return $page;
?>