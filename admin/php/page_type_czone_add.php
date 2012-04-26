<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "page_type_czone_add.html"));

if(!is_numeric($glob['page_type_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
	$dbu->query("select name from cms_page_type
    			 where page_type_id='".$glob['page_type_id']."'");
	$dbu->move_next();
	$page_type_name=$dbu->f('name');
}

if(!is_numeric($glob['page_type_czone_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
	$active_checked='';
    $page_title="Set Default Data for Content Zone";
    $next_function='page_type-czone_update';
    $dbu->query("select * from cms_page_type_czone 
    			 where page_type_czone_id='".$glob['page_type_czone_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "MODE"             =>        build_content_input_mode_list($dbu->gf('mode')),
                        "MODE_MESSAGE"     =>        get_content_input_mode_message($dbu->gf('mode')),
                        "PAGE_TYPE_CZONE_ID" =>        $glob['page_type_czone_id'],
                        "PAGE_TYPE_ID"     =>        $glob['page_type_id']
                     )
                );
                
    $params['cols']=80;
    $params['rows']=16;
    $ft->assign('CONTENT_INPUT_AREA',get_content_input_area($dbu->gf('mode'), $dbu->f('default_data'), 'default_data',$params));
}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('PAGE_TYPE_NAME',$page_type_name);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('PAGE_TYPE_ID',$glob['page_type_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>