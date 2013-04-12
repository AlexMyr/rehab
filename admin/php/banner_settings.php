<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array(main => "banner_settings.html"));

$next_function='settings-update_banner_settings';

$show_banner = $dbu->field("select value from settings where constant_name='SHOW_BANNER'");
$banner_content = $dbu->field("select long_value from settings where constant_name='BANNER_CONTENT'");

$ft->assign( 'SHOW_BANNER', $show_banner ? 'checked' : '');
$ft->assign( 'BANNER_CONTENT', $banner_content);

$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');

?>