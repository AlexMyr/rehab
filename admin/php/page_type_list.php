<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "page_type_list.html"));
$ft->define_dynamic('page_type_row','main');

$dbu=new mysql_db;

$dbu->query("select cms_page_type.name, cms_page_type.page_type_id, cms_template.name as template_name, cms_template.file_name
			 from cms_page_type
			 inner join cms_template on cms_page_type.template_id=cms_template.template_id
			 order by cms_page_type.name");

$i=0;

while($dbu->move_next())
{
	$ft->assign('NAME',$dbu->f('name'));
	$ft->assign('TEMPLATE_NAME',$dbu->f('template_name'));
	$ft->assign('FILE_NAME',$dbu->f('file_name'));

	if($i%2==1)
	{
		$ft->assign('BG_COLOR',"#F8F9FA");
	}
	else
	{
		$ft->assign('BG_COLOR',"#FFFFFF");
	}
	
    $ft->assign('EDIT_LINK',"index.php?pag=page_type_add&page_type_id=".$dbu->f('page_type_id'));
    $ft->assign('DELETE_LINK',"index.php?pag=page_type_list&act=page_type-delete&page_type_id=".$dbu->f('page_type_id'));
    $ft->parse('page_type_ROW_OUT','.page_type_row');
    $i++;
}
              
if($i==0)
{
    unset($ft);
	return get_error_message("There are no Page Types in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Page Types");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','page_type_row');
return $ft->fetch('CONTENT');






?>
