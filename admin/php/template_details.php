<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "template_details.html"));

if(!is_numeric($glob['template_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
    $dbu->query("select * from cms_template
    			 where template_id='".$glob['template_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TEMPLATE_ID"      =>        $glob['template_id'],
                        "DESCRIPTION"      =>        get_safe_text($dbu->f('description')),
                        "NAME"             =>        $dbu->f('name'),
                        "FILE_NAME"        =>        $dbu->f('file_name')
                     )
                );

}
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>