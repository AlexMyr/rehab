<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "translation_meta_list.html"));
$ft->define_dynamic('template_row','main');

$dbu=new mysql_db;
$i=0;
foreach(array('en', 'us') as $lang){
    $dbu->query("SELECT * FROM meta_translate_".$lang." order by page_name ASC");
    $ft->assign('LANG',strtoupper($lang));
    while($dbu->move_next()){
        $ft->assign('NAME',$dbu->f('page_name'));
    
        if($i%2==1)
        {
            $ft->assign('BG_COLOR',"#F8F9FA");
        }
        else
        {
            $ft->assign('BG_COLOR',"#FFFFFF");
        }
        
        $ft->assign('UPDATE_LINK',"index.php?pag=translation_meta_update&page_id=".$dbu->f('page_id').'&lang='.$lang);
        $ft->parse('template_ROW_OUT','.template_row');
        $i++;
    }
}

if($i==0)
{
    unset($ft);
	return get_error_message("There are no Web Page Templates in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Translated Meta Templates");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','template_row');
return $ft->fetch('CONTENT');

?>
