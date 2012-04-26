<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "action_box_add.html"));

if(!is_numeric($glob['tag_id']))
{
	$page_title="Add Dynamic Box";
	$next_function='tag-action_box_add';
	
    $ft->assign(array(
    					"DESCRIPTION"       =>        $glob['description'],
                        "FILE_NAME"         =>        $glob['file_name'],
                        "TAG"               =>        $glob['tag'],
                        "NAME"              =>        $glob['name']
                     )
                );
}
else
{
    $page_title="Edit Dynamic Box";
    $next_function='tag-action_box_update';
    $dbu->query("select * from cms_tag_library 
    			 where tag_id='".$glob['tag_id']."'");
    $dbu->move_next();
		
    $ft->assign(array(
                        "TAG_ID" 		   =>        $glob['tag_id'],
                        "DESCRIPTION"      =>        $dbu->gf('comments'),
                        "FILE_NAME"        =>        $dbu->gf('file_name'),
                        "TAG"        	   =>        $dbu->gf('tag'),
                        "NAME"             =>        $dbu->gf('name')
                     )
                );

}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('TAG_ID',$glob['tag_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>