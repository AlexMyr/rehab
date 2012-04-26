<?php
/************************************************************************
* @Author: MedeeaWeb Works                                              *
************************************************************************/ 
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "client_add.html"));
//$ft->assign('MESSAGE', get_error($glob['error']));


//$page_title='Login Member';
//$next_function ='auth-login';

$dbu = new mysql_db();

//$dbu->query("select name from cms_menu where menu_id=".$glob['menu_id']);

$select = "select client.* from client where 1=1 ";

if(!empty($glob['client_id']) && is_numeric($glob['client_id'])) 
	{
		$select .= "AND client.client_id=".$glob['client_id']." ";
		
		$ft->assign('CLIENT_ID', $glob['client_id']);
		
		$dbu->query($select);
		
		$i = 0;
		
		while($dbu->move_next())
			{
				$ft->assign(array(
					'FIRST_NAME'=>$dbu->gf('first_name'),
					'SURNAME'=>$dbu->gf('surname'),
					'CLIENT_NAME'=>$dbu->gf('first_name')." ".$dbu->gf('surname'),
					'EMAIL'=>$dbu->gf('email'),
					'IMAGE_TYPE'=>build_print_image_type_list($dbu->gf('print_image_type')),
					'CLIENT_NOTE'=>$dbu->gf('client_note'),
				));
				$i++;
			}
	
	}
$ft->assign('HIDE_NAV','displayNone');

$ft->assign('CSS_PAGE', $glob['pag']);

$site_meta_title=$meta_title." - Client Record";
$site_meta_keywords=$meta_keywords.", Client Record";
$site_meta_description=$meta_description." Client Record";

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>