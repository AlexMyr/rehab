<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "sys_message_add.html"));

if(!is_numeric($glob['sys_message_id']))
{
	$page_title='Add New Sistem Message';
	$next_function='sys_message-add';
    $ft->assign(array(
                        "TITLE"            	=>        $glob['title'],
                        "SUBJECT"          	=>        $glob['subject'],
                        "FROM_EMAIL" 	    =>        $glob['from_email'],
                        "FROM_NAME"         =>        $glob['from_name'],
                        "TEXT"              =>        $glob['text'],   
                        'DESCRIPTION'       =>        $glob['description'],
                        'S_EDIT_MODE'       =>        '<!--',
                        'E_EDIT_MODE'       =>        '-->',  
                        'S_ADD_MODE'        =>        '',
                        'E_ADD_MODE'        =>        ''  
                     )
                );
}
else 
{	
	$page_title='Edit Sistem Message';
	$next_function='sys_message-update';
	$dbu->query("select * from sys_message 
    			 where sys_message_id ='".$glob['sys_message_id']."'");
    $dbu->move_next();
    $ft->assign(array(
                        "SYS_MESSAGE_ID"    =>        $glob['sys_message_id'],
                        "TITLE"            	=>        $dbu->gf('title'),
                        "SUBJECT"          	=>        $dbu->gf('subject'),
                        "FROM_EMAIL" 	    =>        $dbu->gf('from_email'),
                        "FROM_NAME"         =>        $dbu->gf('from_name'),
                        "TEXT"              =>        $dbu->gf('text'),   
                        'DESCRIPTION_TXT'   =>        get_safe_text($dbu->f('description')),
                        'S_EDIT_MODE'       =>        '',
                        'E_EDIT_MODE'       =>        '',  
                        'S_ADD_MODE'        =>        '<!--',
                        'E_ADD_MODE'        =>        '-->'  
                     )
                );


}

$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('SYS_MESSAGE_ID',$glob['sys_message_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
return $ft->fetch('CONTENT');

?>