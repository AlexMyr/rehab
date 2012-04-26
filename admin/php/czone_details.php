<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "czone_details.html"));

if(!is_numeric($glob['template_czone_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
    $dbu->query("select cms_template_czone.name, cms_template_czone.tag, cms_template_czone.description, cms_template_czone.template_id, 
    					cms_template.name as template_name, cms_template.file_name
    			 from cms_template_czone 
    			 inner join cms_template on cms_template.template_id=cms_template_czone.template_id
    			 where template_czone_id='".$glob['template_czone_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TEMPLATE_CZONE_ID" =>        $glob['template_czone_id'],
                        "DESCRIPTION"      =>        get_safe_text($dbu->f('description')),
                        "NAME"             =>        $dbu->f('name'),
                        "TAG"              =>        $dbu->f('tag'),
                        "FILE_NAME"        =>        $dbu->f('file_name'),
                        "TEMPLATE_ID"      =>        $dbu->f('template_id'),
                        "TEMPLATE_NAME"    =>        $dbu->f('template_name')
                     )
                );

}
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>