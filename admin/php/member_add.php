<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/

$dbu=new mysql_db;

$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "members_add.html"));

if(!is_numeric($glob['trainer_id']))
{		
	
	$page_title="Add Member";
	$next_function='member-add';
		
    $ft->assign(array(                      
                        "FIRST_NAME" 		=>      	$glob['first_name'],
                        "SURNAME"           =>  		$glob['surname'],
						"USERNAME"          =>      	$glob['username'],
                        "PASSWORD"          =>	  		$glob['password'],
                        "CONFIRM_PASSWORD"  =>	      	$glob['confirm_password'],           
                        "EMAIL"	            =>	      	$glob['email'],           
                        "CLINIC_NAME"	    =>	      	$glob['clinic_name'],
						"ADDRESS" 			=>		  	$glob['address'],
						"CITY" 				=>		  	$glob['city'],
						"POST_CODE" 		=>			$glob['post_code'],
						"WEBSITE" 			=>			$glob['website'],
						"PHONE" 			=>			$glob['phone'],
						"MOBILE" 			=>			$glob['mobile'],
                        "DISPLAY"			=>		  	'none'
                                            )
                );
}
else
{
    $page_title="Edit Member";
    $next_function='member-update';
    $dbu->query("select t.username, t.password, t.clinic_name, tp.* from trainer as t left join trainer_header_paper as tp on (tp.trainer_id = t.trainer_id) 
    			 where t.trainer_id='".$glob['trainer_id']."'");
    $dbu->move_next();

    $ft->assign(array(                        
                        "FIRST_NAME" 		=>        $dbu->f('first_name'),
                        "SURNAME"           =>        $dbu->f('surname'),
						"USERNAME"          =>        $dbu->f('username'),
                        "PASSWORD"          =>        $dbu->f('password'),
                        "CONFIRM_PASSWORD"  =>        $dbu->f('password'),                   
                        "EMAIL"	            =>        $dbu->f('email'),  
                        "CLINIC_NAME"       =>        $dbu->f('clinic_name'),
						"ADDRESS" 			=>		  $dbu->f('address'),
						"CITY" 				=>		  $dbu->f('city'),
						"POST_CODE" 		=>		  $dbu->f('post_code'),
						"WEBSITE" 			=>		  $dbu->f('website'),
						"PHONE" 			=>		  $dbu->f('phone'),
						"MOBILE" 			=>		  $dbu->f('mobile'),
                        "LOGGEDIN"          =>        $dbu->f('is_login') ? 'checked="checked"' : '',
                        "DISPLAY"			=>		  'none'
                                            )
                );
}
$ft->assign('PAGE_TITLE',$page_title);
$ft->assign('NEXT_FUNCTION',$next_function);
$ft->assign('MEMBER_ID',$glob['trainer_id']);
$ft->assign('MESSAGE',$glob['error']);

$ft->parse('CONTENT','main');
$ft->clear_dynamic('CONTENT','picture');
return $ft->fetch('CONTENT');

?>