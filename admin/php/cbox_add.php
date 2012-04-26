<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "cbox_add.html"));

if(!is_numeric($glob['content_box_id']))
{
    $page_title="Add Content Box";
    $next_function='cbox-add';
    if(!is_numeric($glob['mode']))
    {
    	$glob['mode']=1;
    }
    
    $ft->assign(array(
                        "TITLE"            =>        $glob['title'],
                        "TAG"              =>        $glob['tag'],
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
    $page_title="Edit Content Box";
    $next_function='cbox-update';
    $dbu->query("select * from cms_content_box 
    			 where content_box_id='".$glob['content_box_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TITLE"            =>        $dbu->gf('title'),
                        "TAG"              =>        $dbu->gf('tag'),
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
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('CONTENT_BOX_ID',$glob['content_box_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>