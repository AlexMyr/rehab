<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;
$dbu2=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "translation_update.html"));
$ft->define_dynamic('content_row','main');

if(!isset($glob['template_file']) || !isset($glob['lang']))
{
    unset($ft);
	return get_error_message("Invalid parameters.");
}

$page_title="Update Translation";
$next_function='translation-update';
$dbu->query("SELECT * FROM tmpl_translate_".$glob['lang']." WHERE template_file='".$glob['template_file']."'");

while($dbu->move_next())
{
    $ft->assign(array(  "TAG" => $dbu->f('tag'),
                        "TEMPLATE_FILE" => $dbu->f('template_file'),
                        "TAG_ID" => $dbu->f('tag_id'),
                        "TAG_TEXT" => $dbu->f('tag_text'),
                        "SAVE_LINK" => 'index.php?pag=translation_save&template_file='.$glob['template_file'].'&tag='.$dbu->f('tag'),
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