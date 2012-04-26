<?php
/************************************************************************
* @Author: Tinu Coman                                                   *
************************************************************************/
$ft = new ft(ADMIN_PATH.MODULE."templates/");
$ft->define( array(main => "translation_list.html"));
$ft->define_dynamic('template_row','main');

$dbu=new mysql_db;
$i=0;
$dbu->query("SELECT DISTINCT template_file FROM tmpl_translate_en order by template_file ASC");
$ft->assign('LANG',strtoupper($lang));
while($dbu->move_next()){
    $ft->assign('NAME',$dbu->f('template_file'));

    if($i%2==1)
    {
        $ft->assign('BG_COLOR',"#F8F9FA");
    }
    else
    {
        $ft->assign('BG_COLOR',"#FFFFFF");
    }
    
    $ft->assign('UPDATE_LINK_EN',"index.php?pag=translation_update&template_file=".$dbu->f('template_file').'&lang=en');
    $ft->assign('UPDATE_LINK_US',"index.php?pag=translation_update&template_file=".$dbu->f('template_file').'&lang=us');
    $ft->parse('template_ROW_OUT','.template_row');
    $i++;
}

if($i==0)
{
    unset($ft);
	return get_error_message("There are no Web Page Templates in the database.");
}

$ft->assign('PAGE_TITLE',"List Of Translated Templates");
$ft->assign('MESSAGE',$glob['error']);
$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','template_row');
return $ft->fetch('CONTENT');

?>
