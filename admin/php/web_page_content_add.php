<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "web_page_content_add.html"));

if(!is_numeric($glob['web_page_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}
else
{
	$dbu->query("select name from cms_web_page
    			 where web_page_id='".$glob['web_page_id']."'");
	$dbu->move_next();
	$web_page_name=$dbu->f('name');
	
}

if(!is_numeric($glob['template_czone_id']))
{
    unset($ft);
	return get_error_message("Invalid ID.");
}

if(!$glob['umode'])
{
	$ft->assign('UMODE','');
}
else 
{
	$ft->assign('UMODE','1');
}

if(!is_numeric($glob['web_page_content_id']))
{
    $page_title="Add Content for ".$web_page_name;
    $next_function='web_page_content-add';
    if(!is_numeric($glob['mode']))
    {
    	$glob['mode']=1;
    }
    
    $ft->assign(array(
                        "TITLE"            =>        $glob['title'],
                        "SORT_ORDER"       =>        $glob['sort_order'],
                        "CONTENT_TEMPLATE" =>        build_content_templates_list($glob['content_template_id']),
                        "MODE"             =>        build_content_input_mode_list($glob['mode']),
                        "MODE_MESSAGE"     =>        get_content_input_mode_message($glob['mode'])
                     )
                );
                
    $params['cols']=80;
    $params['rows']=16;
    $ft->assign('CONTENT_INPUT_AREA',get_content_input_area($glob['mode'], $glob['content'], 'content',$params));

}
else
{
    $page_title="Edit Content for ".$web_page_name;
    $next_function='web_page_content-update';
    $dbu->query("select * from cms_web_page_content 
    			 where web_page_content_id='".$glob['web_page_content_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TITLE"            =>        $dbu->gf('title'),
                        "SORT_ORDER"       =>        $dbu->gf('sort_order'),
                        "CONTENT_TEMPLATE" =>        build_content_templates_list($dbu->gf('content_template_id')),
                        "MODE"             =>        build_content_input_mode_list($dbu->gf('mode')),
                        "MODE_MESSAGE"     =>        get_content_input_mode_message($dbu->gf('mode')),
                     )
                );
                
    $params['cols']=80;
    $params['rows']=16;
    $ft->assign('CONTENT_INPUT_AREA',get_content_input_area($dbu->gf('mode'), $dbu->f('content'), 'content',$params));
}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('WEB_PAGE_NAME',$web_page_name);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('WEB_PAGE_ID',$glob['web_page_id']);
$ft->assign('TEMPLATE_CZONE_ID',$glob['template_czone_id']);
$ft->assign('WEB_PAGE_CONTENT_ID',$glob['web_page_content_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>