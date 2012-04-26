<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;
$dbu2=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "translation_meta_update.html"));
$ft->define_dynamic('content_row','main');

if(!isset($glob['page_id']) || !isset($glob['lang']))
{
    unset($ft);
	return get_error_message("Invalid parameters.");
}

$page_title="Update Meta Translation";
$next_function='translation-update';
$dbu->query("SELECT * FROM meta_translate_".$glob['lang']." WHERE page_id='".$glob['page_id']."'");

while($dbu->move_next())
{
    $ft->assign(array(  "PAGE_ID" => $dbu->f('page_id'),
                        "PAGE_NAME" => $dbu->f('page_name'),
                        "TITLE" => $dbu->f('title'),
                        "KEYWORDS" => $dbu->f('keywords'),
                        "DESCRIPTION" => $dbu->f('description')
                  )
                );
	$ft->parse('CONTENT_ROW','.content_row');
}          
            
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('LANG',$glob['lang']);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('WEB_PAGE_ID',$glob['web_page_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','tag_row');
return $ft->fetch('CONTENT');

?>